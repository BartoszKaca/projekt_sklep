#!/bin/bash
set -e

# =============================================================================
# Skrypt Migracji Bazy Danych - Lokalnie → Produkcja
# Rozwiązuje problem lower_case_table_names między macOS a Linux
# =============================================================================

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() { echo -e "${BLUE}[INFO]${NC} $1"; }
log_success() { echo -e "${GREEN}[OK]${NC} $1"; }
log_warn() { echo -e "${YELLOW}[WARN]${NC} $1"; }
log_error() { echo -e "${RED}[ERROR]${NC} $1"; }

BACKUP_DIR="./database/backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

mkdir -p "$BACKUP_DIR"

# =============================================================================
# KROK 1: Export z lokalnego Docker MySQL
# =============================================================================

export_local_db() {
    log_info "Eksportuję bazę danych z lokalnego Dockera..."
    
    # Sprawdź czy lokalny kontener działa
    if ! docker ps | grep -q mysql_db; then
        log_error "Lokalny kontener mysql_db nie działa!"
        log_info "Uruchom: docker-compose up -d db"
        exit 1
    fi
    
    BACKUP_FILE="${BACKUP_DIR}/sklep_migration_${TIMESTAMP}.sql"
    
    # Export z opcjami kompatybilności
    docker exec mysql_db mysqldump \
        -u root \
        -proot \
        --single-transaction \
        --routines \
        --triggers \
        --set-gtid-purged=OFF \
        --column-statistics=0 \
        sklep_laravel > "$BACKUP_FILE"
    
    log_success "Backup zapisany: $BACKUP_FILE"
    
    # Kompresja
    gzip -f "$BACKUP_FILE"
    BACKUP_FILE="${BACKUP_FILE}.gz"
    
    log_success "Skompresowano: $BACKUP_FILE"
    echo "$BACKUP_FILE"
}

# =============================================================================
# KROK 2: Przygotowanie produkcyjnej bazy (WAŻNE dla lower_case_table_names)
# =============================================================================

prepare_production_db() {
    log_info "Przygotowuję produkcyjną bazę danych..."
    
    log_warn "========================================================"
    log_warn "WAŻNE: Aby uniknąć błędu lower_case_table_names:"
    log_warn "1. USUŃ stary volume MySQL: docker volume rm sklep_mysql_data"
    log_warn "2. Uruchom ponownie: docker-compose -f docker-compose.prod.yml up -d db"
    log_warn "3. MySQL zostanie zainicjowany z lower_case_table_names=2"
    log_warn "========================================================"
    
    read -p "Czy kontynuować? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
}

# =============================================================================
# KROK 3: Transfer na serwer produkcyjny
# =============================================================================

transfer_to_production() {
    local BACKUP_FILE=$1
    
    log_info "Transfer backupu na serwer produkcyjny..."
    
    read -p "Podaj adres IP serwera EC2: " SERVER_IP
    read -p "Podaj ścieżkę do klucza SSH (.pem): " SSH_KEY
    
    if [ ! -f "$SSH_KEY" ]; then
        log_error "Plik klucza SSH nie istnieje: $SSH_KEY"
        exit 1
    fi
    
    # Transfer
    scp -i "$SSH_KEY" "$BACKUP_FILE" "ubuntu@${SERVER_IP}:~/sklep_backup.sql.gz"
    
    log_success "Backup przesłany na serwer"
}

# =============================================================================
# KROK 4: Import na produkcji
# =============================================================================

import_on_production() {
    log_info "Importuję bazę na produkcji..."
    
    read -p "Podaj adres IP serwera EC2: " SERVER_IP
    read -p "Podaj ścieżkę do klucza SSH (.pem): " SSH_KEY
    
    ssh -i "$SSH_KEY" "ubuntu@${SERVER_IP}" << 'REMOTE_SCRIPT'
        set -e
        cd /var/www/sklep
        
        echo "Dekompresja backupu..."
        gunzip -k ~/sklep_backup.sql.gz 2>/dev/null || true
        
        echo "Import do MySQL..."
        docker compose -f docker-compose.prod.yml exec -T db mysql \
            -u root \
            -p"${DB_ROOT_PASSWORD:-rootsecret}" \
            sklep_laravel < ~/sklep_backup.sql
        
        echo "Czyszczenie..."
        rm -f ~/sklep_backup.sql ~/sklep_backup.sql.gz
        
        echo "Uruchomienie migracji Laravel..."
        docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force
        
        echo "✅ Import zakończony!"
REMOTE_SCRIPT
    
    log_success "Baza danych zaimportowana na produkcji"
}

# =============================================================================
# MENU
# =============================================================================

show_menu() {
    echo ""
    echo "=============================================="
    echo "  MIGRACJA BAZY DANYCH - Sklep Laravel"
    echo "=============================================="
    echo ""
    echo "1) Export lokalnej bazy (docker mysql_db)"
    echo "2) Przygotuj produkcję (instrukcje lower_case_table_names)"
    echo "3) Transfer backup na serwer EC2"
    echo "4) Import na produkcji"
    echo "5) Pełna migracja (wszystkie kroki)"
    echo "6) Wyjście"
    echo ""
    read -p "Wybierz opcję [1-6]: " choice
    
    case $choice in
        1)
            export_local_db
            ;;
        2)
            prepare_production_db
            ;;
        3)
            # Znajdź najnowszy backup
            LATEST_BACKUP=$(ls -t ${BACKUP_DIR}/*.sql.gz 2>/dev/null | head -1)
            if [ -z "$LATEST_BACKUP" ]; then
                log_error "Brak backupów! Najpierw wykonaj export (opcja 1)"
                exit 1
            fi
            log_info "Używam: $LATEST_BACKUP"
            transfer_to_production "$LATEST_BACKUP"
            ;;
        4)
            import_on_production
            ;;
        5)
            log_info "🚀 Rozpoczynam pełną migrację..."
            BACKUP_FILE=$(export_local_db)
            prepare_production_db
            transfer_to_production "$BACKUP_FILE"
            import_on_production
            log_success "🎉 Pełna migracja zakończona!"
            ;;
        6)
            exit 0
            ;;
        *)
            log_error "Nieprawidłowa opcja"
            exit 1
            ;;
    esac
}

# Uruchom menu lub komendę z argumentu
if [ -n "$1" ]; then
    case $1 in
        export) export_local_db ;;
        prepare) prepare_production_db ;;
        transfer) transfer_to_production "$2" ;;
        import) import_on_production ;;
        full) 
            BACKUP_FILE=$(export_local_db)
            prepare_production_db
            transfer_to_production "$BACKUP_FILE"
            import_on_production
            ;;
        *) show_menu ;;
    esac
else
    show_menu
fi

