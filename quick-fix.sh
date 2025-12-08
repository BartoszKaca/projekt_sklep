#!/bin/bash

# 🚀 Quick Fix Deployment Script
# Data: 08.12.2025

set -e

echo "🔧 Starting deployment of fixes..."

# Kolory dla outputu
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Funkcja do wyświetlania komunikatów
print_status() {
    echo -e "${GREEN}✓${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

# Sprawdź czy jesteśmy w katalogu projektu
if [ ! -f "docker-compose.prod.yml" ]; then
    print_error "Error: docker-compose.prod.yml not found!"
    echo "Please run this script from the project root directory."
    exit 1
fi

print_status "Project directory verified"

# 1. Backup przed zmianami
echo ""
echo "📦 Creating backup..."
BACKUP_DATE=$(date +%Y%m%d_%H%M%S)

# Backup bazy danych
print_status "Backing up database..."
docker exec sklep_db mysqldump -u root -p${DB_ROOT_PASSWORD:-root} sklep_laravel > "backup_db_${BACKUP_DATE}.sql" 2>/dev/null || {
    print_warning "Database backup skipped (container might not be running)"
}

print_status "Backups created: backup_db_${BACKUP_DATE}.sql"

# 2. Zatrzymanie aplikacji (opcjonalne - można komentować dla zero-downtime)
echo ""
echo "🛑 Stopping containers..."
docker-compose -f docker-compose.prod.yml down
print_status "Containers stopped"

# 3. Restart kontenerów
echo ""
echo "🚀 Starting containers..."
docker-compose -f docker-compose.prod.yml up -d
print_status "Containers started"

# Czekaj aż kontenery będą gotowe
echo ""
echo "⏳ Waiting for containers to be ready..."
sleep 10

# 4. Czyszczenie cache
echo ""
echo "🧹 Clearing Laravel cache..."
docker exec sklep_app php artisan config:clear
docker exec sklep_app php artisan cache:clear
docker exec sklep_app php artisan route:clear
docker exec sklep_app php artisan view:clear
print_status "Cache cleared"

# 5. Restart Nginx
echo ""
echo "🔄 Restarting Nginx..."
docker-compose -f docker-compose.prod.yml restart nginx
print_status "Nginx restarted"

# 6. Sprawdzenie statusu
echo ""
echo "📊 Checking container status..."
docker-compose -f docker-compose.prod.yml ps

# 7. Test połączeń
echo ""
echo "🔍 Testing services..."

# Test aplikacji
if curl -s -o /dev/null -w "%{http_code}" http://localhost | grep -q "200\|301\|302"; then
    print_status "Application is responding"
else
    print_warning "Application might not be responding correctly"
fi

# Test phpMyAdmin
if curl -s -o /dev/null -w "%{http_code}" http://localhost/pma | grep -q "200\|301\|302"; then
    print_status "phpMyAdmin is responding"
else
    print_warning "phpMyAdmin might not be responding correctly"
fi

# 8. Podsumowanie
echo ""
echo "=========================================="
echo "✅ Deployment completed!"
echo "=========================================="
echo ""
echo "📝 Next steps:"
echo "1. Test phpMyAdmin: http://bartoszkaca.online/pma"
echo "2. Test inventory report: http://bartoszkaca.online/admin/reports/inventory"
echo "3. Test product stock: http://bartoszkaca.online/admin/products/[ID]/stock"
echo ""
echo "📊 Monitor logs:"
echo "   docker logs sklep_app -f --tail=50"
echo "   docker logs sklep_nginx -f --tail=50"
echo ""
echo "🔧 If issues occur:"
echo "   docker-compose -f docker-compose.prod.yml logs -f"
echo ""
echo "📦 Backup location:"
echo "   Database: backup_db_${BACKUP_DATE}.sql"
echo ""
print_warning "Remember to delete old backups after successful testing!"
echo ""

# Pokaż ostatnie logi
echo "📋 Last 20 lines from Laravel log:"
docker exec sklep_app tail -n 20 /var/www/html/storage/logs/laravel.log 2>/dev/null || {
    print_warning "Could not read Laravel logs"
}

echo ""
echo "🎉 All done!"
