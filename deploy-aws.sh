#!/bin/bash

# ===========================================================================
# AWS EC2 Production Deployment Script
# ===========================================================================
# Skrypt automatyzujący wdrożenie aplikacji Laravel na AWS EC2
# Użycie: ./deploy-aws.sh

set -e

# Kolory dla output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Konfiguracja
DOMAIN="${1:-rapshop.pl}"
EMAIL="${2:-your-email@example.com}"
ENV_FILE=".env"

# Funkcje
log_info() { echo -e "${BLUE}ℹ️  $1${NC}"; }
log_success() { echo -e "${GREEN}✅ $1${NC}"; }
log_warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }
log_error() { echo -e "${RED}❌ $1${NC}"; exit 1; }

# ===========================================================================
# FAZA 1: Sprawdzenia wstępne
# ===========================================================================
phase_1_checks() {
    log_info "=== FAZA 1: Sprawdzenia wstępne ==="

    if ! command -v docker &> /dev/null; then
        log_error "Docker nie zainstalowany!"
    fi
    log_success "Docker zainstalowany"

    if ! command -v docker-compose &> /dev/null; then
        log_error "Docker Compose nie zainstalowany!"
    fi
    log_success "Docker Compose zainstalowany"

    if ! command -v git &> /dev/null; then
        log_error "Git nie zainstalowany!"
    fi
    log_success "Git zainstalowany"

    # Sprawdź czy kod jest w repozytorium
    if [ ! -d ".git" ]; then
        log_error "Nie znaleziono .git - uruchom z root projektu"
    fi
    log_success "Projekt jest repozytorium Git"

    echo ""
}

# ===========================================================================
# FAZA 2: Przygotowanie plików
# ===========================================================================
phase_2_prepare_files() {
    log_info "=== FAZA 2: Przygotowanie plików ==="

    # Sprawdź czy plik .env istnieje
    if [ ! -f "$ENV_FILE" ]; then
        log_warning ".env nie znaleziony, kopiuję z .env.docker.prod"
        cp .env.docker.prod $ENV_FILE
        log_warning "EDYTUJ .env przed kontynuowaniem!"
        return 1
    fi
    log_success ".env istnieje"

    # Sprawdź czy APP_KEY jest ustawiony
    if grep -q "APP_KEY=$" $ENV_FILE; then
        log_info "Generuję klucz aplikacji..."
    fi

    echo ""
    log_success "Faza 2 ukończona"
    echo ""
}

# ===========================================================================
# FAZA 3: Build Docker images
# ===========================================================================
phase_3_docker_build() {
    log_info "=== FAZA 3: Build Docker images ==="

    log_info "Building Laravel app image..."
    docker-compose -f docker-compose.prod.yml build --no-cache app

    log_success "Docker images zbudowane"
    echo ""
}

# ===========================================================================
# FAZA 4: Start containers
# ===========================================================================
phase_4_start_containers() {
    log_info "=== FAZA 4: Start kontenerów ==="

    log_info "Uruchamianie kontenerów..."
    docker-compose -f docker-compose.prod.yml up -d

    # Czekaj aż bazy danych będzie gotowa
    log_info "Czekam na dostępność bazy danych..."
    sleep 10

    # Sprawdzenie statusu
    log_info "Statusy kontenerów:"
    docker-compose -f docker-compose.prod.yml ps

    echo ""
    log_success "Faza 4 ukończona"
    echo ""
}

# ===========================================================================
# FAZA 5: Inicjalizacja bazy danych
# ===========================================================================
phase_5_database_setup() {
    log_info "=== FAZA 5: Inicjalizacja bazy danych ==="

    log_info "Instalowanie Composer dependencies..."
    docker-compose -f docker-compose.prod.yml exec -T app composer install --no-dev --optimize-autoloader

    log_info "Generowanie APP_KEY..."
    docker-compose -f docker-compose.prod.yml exec -T app php artisan key:generate

    log_info "Uruchamianie migracji..."
    docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate --force --seed

    log_info "Tworzenie symbolic link do storage..."
    docker-compose -f docker-compose.prod.yml exec -T app php artisan storage:link

    log_info "Cache optimization..."
    docker-compose -f docker-compose.prod.yml exec -T app php artisan config:cache
    docker-compose -f docker-compose.prod.yml exec -T app php artisan route:cache
    docker-compose -f docker-compose.prod.yml exec -T app php artisan view:cache

    echo ""
    log_success "Faza 5 ukończona"
    echo ""
}

# ===========================================================================
# FAZA 6: Konfiguracja Nginx (RĘCZNIE)
# ===========================================================================
phase_6_nginx_setup() {
    log_info "=== FAZA 6: Konfiguracja Nginx ==="

    log_warning "Nginx musi być zainstalowany na hoście!"
    log_info "Kroki manualne:"
    echo ""
    echo "1. Skopiuj konfigurację:"
    echo "   sudo cp nginx.conf /etc/nginx/conf.d/rapshop.conf"
    echo ""
    echo "2. Edytuj YOUR_HOME_IP w konfiguracji:"
    echo "   sudo nano /etc/nginx/conf.d/rapshop.conf"
    echo ""
    echo "3. Wygeneruj SSL certyfikat:"
    echo "   sudo certbot certonly --standalone -d $DOMAIN -d www.$DOMAIN -m $EMAIL"
    echo ""
    echo "4. Test Nginx:"
    echo "   sudo nginx -t"
    echo ""
    echo "5. Reload Nginx:"
    echo "   sudo systemctl reload nginx"
    echo ""
    log_warning "Wróć po ukończeniu tych kroków"
    echo ""
}

# ===========================================================================
# FAZA 7: Testy
# ===========================================================================
phase_7_tests() {
    log_info "=== FAZA 7: Testy ==="

    log_info "Test połączenia z bazą danych..."
    docker-compose -f docker-compose.prod.yml exec -T db mysqladmin ping -h localhost -u root -proot

    log_info "Test PHPMyAdmin..."
    curl -s http://localhost:8080 | grep -q "phpMyAdmin" && log_success "PHPMyAdmin dostępny" || log_warning "PHPMyAdmin niedostępny (normalnie dla produkcji)"

    log_info "Test aplikacji Laravel..."
    docker-compose -f docker-compose.prod.yml exec -T app php artisan tinker --execute="echo 'Laravel OK';"

    echo ""
    log_success "Faza 7 ukończona"
    echo ""
}

# ===========================================================================
# FAZA 8: Backup setup
# ===========================================================================
phase_8_backup() {
    log_info "=== FAZA 8: Backup setup ==="

    log_info "Tworzę katalog na backupy..."
    mkdir -p ~/backups

    log_info "Tworzymy skrypt backupu..."
    cat > ~/backup-database.sh << 'EOF'
#!/bin/bash
BACKUP_DIR=~/backups
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
docker-compose -f /path/to/docker-compose.prod.yml exec -T db mysqldump -u root -proot sklep_laravel > $BACKUP_DIR/db_$TIMESTAMP.sql
echo "Backup: $BACKUP_DIR/db_$TIMESTAMP.sql"
EOF
    chmod +x ~/backup-database.sh

    log_info "Dodaj do crontab:"
    echo "0 2 * * * ~/backup-database.sh"

    echo ""
    log_success "Faza 8 ukończona"
    echo ""
}

# ===========================================================================
# SUMMARY
# ===========================================================================
show_summary() {
    echo ""
    echo "=========================================="
    echo "     🚀 DEPLOYMENT SUMMARY"
    echo "=========================================="
    echo ""
    log_success "Aplikacja jest uruchomiona!"
    echo ""
    echo "Dostępy:"
    echo "  📱 Aplikacja: http://localhost"
    echo "  🗄️  PHPMyAdmin: http://localhost:8080"
    echo "  📧 Mail: sprawdź Mailtrap.io"
    echo ""
    echo "Logi:"
    echo "  docker-compose -f docker-compose.prod.yml logs -f app"
    echo "  docker-compose -f docker-compose.prod.yml logs -f db"
    echo ""
    echo "Następne kroki:"
    echo "  1. Uruchom FAZĘ 6 (Nginx setup)"
    echo "  2. Wygeneruj SSL certyfikat"
    echo "  3. Skonfiguruj DNS"
    echo "  4. Test aplikacji na domenie"
    echo ""
    echo "=========================================="
    echo ""
}

# ===========================================================================
# MAIN EXECUTION
# ===========================================================================
main() {
    log_info "=== AWS EC2 PRODUCTION DEPLOYMENT ==="
    log_info "Domain: $DOMAIN"
    log_info "Email: $EMAIL"
    echo ""

    phase_1_checks
    phase_2_prepare_files || return 1
    phase_3_docker_build
    phase_4_start_containers
    phase_5_database_setup
    phase_6_nginx_setup
    phase_7_tests
    phase_8_backup
    show_summary
}

# Run main
main "$@"
