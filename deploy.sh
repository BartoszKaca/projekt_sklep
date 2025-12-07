#!/bin/bash
set -e

# =============================================================================
# Skrypt Deploy dla Sklepu Laravel na AWS EC2 z Docker
# =============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# Kolory
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

log_info() { echo -e "${BLUE}[INFO]${NC} $1"; }
log_success() { echo -e "${GREEN}[OK]${NC} $1"; }
log_warn() { echo -e "${YELLOW}[WARN]${NC} $1"; }
log_error() { echo -e "${RED}[ERROR]${NC} $1"; }

# =============================================================================
# FUNKCJE
# =============================================================================

check_requirements() {
    log_info "Sprawdzanie wymagań..."
    
    if ! command -v docker &> /dev/null; then
        log_error "Docker nie jest zainstalowany!"
        exit 1
    fi
    
    if ! command -v docker-compose &> /dev/null && ! docker compose version &> /dev/null; then
        log_error "Docker Compose nie jest zainstalowany!"
        exit 1
    fi
    
    if [ ! -f docker-compose.prod.yml ]; then
        log_error "Brak pliku docker-compose.prod.yml!"
        exit 1
    fi
    
    log_success "Wymagania spełnione"
}

setup_env() {
    log_info "Konfiguracja środowiska..."
    
    if [ ! -f .env ]; then
        if [ -f .env.production ]; then
            cp .env.production .env
            log_warn "Skopiowano .env.production do .env - SPRAWDŹ KONFIGURACJĘ!"
        else
            log_error "Brak pliku .env! Utwórz go z .env.production.example"
            exit 1
        fi
    fi
    
    log_success "Środowisko skonfigurowane"
}

pull_latest() {
    log_info "Pobieranie najnowszych zmian z repozytorium..."
    
    if [ -d .git ]; then
        git fetch origin
        git pull origin main
        log_success "Kod zaktualizowany"
    else
        log_warn "Brak repozytorium git, pomijam pull"
    fi
}

build_containers() {
    log_info "Budowanie kontenerów Docker..."
    
    if ! docker compose -f docker-compose.prod.yml build --no-cache 2>&1; then
        log_error "Błąd podczas budowania kontenerów!"
        log_info "Sprawdź logi powyżej aby zobaczyć szczegóły błędu"
        exit 1
    fi
    
    log_success "Kontenery zbudowane"
}

start_containers() {
    log_info "Uruchamianie kontenerów..."
    
    if ! docker compose -f docker-compose.prod.yml up -d 2>&1; then
        log_error "Błąd podczas uruchamiania kontenerów!"
        log_info "Sprawdź logi powyżej aby zobaczyć szczegóły błędu"
        log_info "Spróbuj: docker compose -f docker-compose.prod.yml logs"
        exit 1
    fi
    
    # Poczekaj chwilę i sprawdź status
    sleep 2
    local failed_containers=$(docker compose -f docker-compose.prod.yml ps --format json 2>/dev/null | grep -c '"State":"exited"' || echo "0")
    
    if [ "$failed_containers" -gt "0" ]; then
        log_warn "Niektóre kontenery zakończyły się błędem. Sprawdź status:"
        docker compose -f docker-compose.prod.yml ps
        log_info "Sprawdź logi: ./deploy.sh logs"
    fi
    
    log_success "Kontenery uruchomione"
}

wait_for_db() {
    log_info "Oczekiwanie na bazę danych..."
    
    local max_attempts=30
    local attempt=1
    
    while [ $attempt -le $max_attempts ]; do
        if docker compose -f docker-compose.prod.yml exec -T db mysqladmin ping -h localhost -u root -p"${DB_ROOT_PASSWORD:-rootsecret}" --silent 2>/dev/null; then
            log_success "Baza danych gotowa"
            return 0
        fi
        
        log_info "Próba $attempt/$max_attempts - czekam na MySQL..."
        sleep 2
        ((attempt++))
    done
    
    log_error "Timeout - baza danych nie odpowiada!"
    exit 1
}

run_migrations() {
    log_info "Uruchamianie migracji Laravel..."
    
    docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force
    
    log_success "Migracje wykonane"
}

optimize_laravel() {
    log_info "Optymalizacja Laravel..."
    
    docker compose -f docker-compose.prod.yml exec -T app php artisan config:cache
    docker compose -f docker-compose.prod.yml exec -T app php artisan route:cache
    docker compose -f docker-compose.prod.yml exec -T app php artisan view:cache
    docker compose -f docker-compose.prod.yml exec -T app php artisan event:cache
    
    log_success "Laravel zoptymalizowany"
}

setup_storage() {
    log_info "Konfiguracja storage..."
    
    docker compose -f docker-compose.prod.yml exec -T app php artisan storage:link 2>/dev/null || true
    
    # Uprawnienia
    docker compose -f docker-compose.prod.yml exec -T app chmod -R 775 storage bootstrap/cache
    
    log_success "Storage skonfigurowany"
}

restart_queue() {
    log_info "Restart queue workera..."
    
    # Sprawdź czy kontener queue istnieje
    if docker compose -f docker-compose.prod.yml ps queue &>/dev/null; then
        docker compose -f docker-compose.prod.yml restart queue || log_warn "Nie udało się zrestartować queue (może nie być uruchomiony)"
    else
        log_warn "Kontener queue nie istnieje - pomijam restart"
    fi
    
    log_success "Queue worker zrestartowany"
}

show_status() {
    echo ""
    echo "=============================================="
    echo -e "${GREEN}🚀 DEPLOY ZAKOŃCZONY POMYŚLNIE${NC}"
    echo "=============================================="
    echo ""
    docker compose -f docker-compose.prod.yml ps
    echo ""
    
    # Sprawdź czy wszystkie kontenery działają
    local containers=$(docker compose -f docker-compose.prod.yml ps --format json | jq -r '.[] | select(.State != "running") | .Name' 2>/dev/null || echo "")
    if [ -n "$containers" ]; then
        log_warn "Niektore kontenery nie działają:"
        docker compose -f docker-compose.prod.yml ps --format "table {{.Name}}\t{{.Status}}"
        log_info "Sprawdź logi: ./deploy.sh logs"
    else
        log_success "Wszystkie kontenery działają poprawnie"
    fi
    
    echo ""
    log_info "Logi: docker compose -f docker-compose.prod.yml logs -f"
    log_info "Stop: docker compose -f docker-compose.prod.yml down"
}

# =============================================================================
# KOMENDY
# =============================================================================

case "${1:-deploy}" in
    deploy)
        log_info "🚀 Rozpoczynam pełny deploy..."
        check_requirements
        setup_env
        pull_latest
        
        log_info "Zatrzymywanie istniejących kontenerów..."
        docker compose -f docker-compose.prod.yml down --remove-orphans || true
        
        build_containers
        start_containers
        wait_for_db
        run_migrations
        optimize_laravel
        setup_storage
        restart_queue
        show_status
        ;;
    
    update)
        log_info "🔄 Szybka aktualizacja (bez przebudowy)..."
        pull_latest
        docker compose -f docker-compose.prod.yml up -d
        wait_for_db
        run_migrations
        optimize_laravel
        restart_queue
        show_status
        ;;
    
    rebuild)
        log_info "🔨 Przebudowa kontenerów..."
        log_info "Zatrzymywanie i usuwanie istniejących kontenerów..."
        docker compose -f docker-compose.prod.yml down --remove-orphans || true
        
        # Usuń stare kontenery, które mogą mieć konflikt nazw
        log_info "Usuwanie starych kontenerów z konfliktami nazw..."
        docker ps -a --filter "name=sklep_" --format "{{.Names}}" | xargs -r docker rm -f || true
        
        sleep 1
        
        build_containers
        
        start_containers
        
        log_info "Oczekiwanie na inicjalizację kontenerów..."
        sleep 3
        
        wait_for_db
        
        run_migrations
        optimize_laravel
        setup_storage
        restart_queue
        
        show_status
        ;;
    
    start)
        log_info "▶️ Uruchamianie..."
        
        # Sprawdź czy są stare kontenery z konfliktami
        local conflict_containers=$(docker ps -a --filter "name=sklep_" --format "{{.Names}}" 2>/dev/null | grep -v "$(docker compose -f docker-compose.prod.yml ps --format '{{.Name}}' 2>/dev/null | tr '\n' '|')" || echo "")
        
        if [ -n "$conflict_containers" ]; then
            log_warn "Znaleziono stare kontenery z konfliktami, usuwam..."
            echo "$conflict_containers" | xargs -r docker rm -f || true
        fi
        
        docker compose -f docker-compose.prod.yml up -d
        show_status
        ;;
    
    stop)
        log_info "⏹️ Zatrzymywanie..."
        docker compose -f docker-compose.prod.yml down --remove-orphans
        log_success "Zatrzymano"
        ;;
    
    cleanup)
        log_info "🧹 Czyszczenie starych kontenerów i obrazów..."
        docker compose -f docker-compose.prod.yml down --remove-orphans --volumes --rmi all || true
        
        # Usuń wszystkie kontenery z nazwą sklep_
        docker ps -a --filter "name=sklep_" --format "{{.Names}}" | xargs -r docker rm -f || true
        
        log_success "Czyszczenie zakończone"
        ;;
    
    logs)
        docker compose -f docker-compose.prod.yml logs -f "${2:-}"
        ;;
    
    status)
        docker compose -f docker-compose.prod.yml ps
        ;;
    
    shell)
        docker compose -f docker-compose.prod.yml exec app bash
        ;;
    
    artisan)
        shift
        docker compose -f docker-compose.prod.yml exec -T app php artisan "$@"
        ;;
    
    mysql)
        docker compose -f docker-compose.prod.yml exec db mysql -u root -p"${DB_ROOT_PASSWORD:-rootsecret}" sklep_laravel
        ;;
    
    backup)
        log_info "📦 Tworzenie backupu bazy danych..."
        BACKUP_FILE="backup_$(date +%Y%m%d_%H%M%S).sql"
        docker compose -f docker-compose.prod.yml exec -T db mysqldump -u root -p"${DB_ROOT_PASSWORD:-rootsecret}" sklep_laravel > "$BACKUP_FILE"
        gzip "$BACKUP_FILE"
        log_success "Backup utworzony: ${BACKUP_FILE}.gz"
        ;;
    
    *)
        echo "Użycie: $0 {deploy|update|rebuild|start|stop|logs|status|shell|artisan|mysql|backup|cleanup}"
        echo ""
        echo "Komendy:"
        echo "  deploy   - Pełny deploy (build + migracje + optymalizacja)"
        echo "  update   - Szybka aktualizacja bez przebudowy"
        echo "  rebuild  - Przebudowa wszystkich kontenerów"
        echo "  start    - Uruchom kontenery"
        echo "  stop     - Zatrzymaj kontenery"
        echo "  cleanup  - Usuń stare kontenery i obrazy (UWAGA: usuwa dane!)"
        echo "  logs     - Pokaż logi (opcjonalnie: logs nginx)"
        echo "  status   - Status kontenerów"
        echo "  shell    - Bash w kontenerze app"
        echo "  artisan  - Uruchom artisan (np: ./deploy.sh artisan migrate)"
        echo "  mysql    - Konsola MySQL"
        echo "  backup   - Backup bazy danych"
        exit 1
        ;;
esac

