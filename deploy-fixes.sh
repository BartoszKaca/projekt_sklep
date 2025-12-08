#!/bin/bash
# 🚀 ONE-CLICK DEPLOYMENT - Wszystko w jednej komendzie
# Użycie: ./deploy-fixes.sh

set -e

echo "🚀 Starting one-click deployment..."

# Kolory
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

SERVER="root@bartoszkaca.online"
REMOTE_PATH="/root/projekt_sklep/sklep"

echo -e "${YELLOW}→${NC} Uploading files..."

# Upload wszystkich plików
scp -q \
    app/Observers/OrderObserver.php \
    app/Http/Controllers/Admin/ReportController.php \
    docker/nginx/conf.d/default.conf \
    quick-fix.sh \
    "$SERVER:/tmp/"

echo -e "${GREEN}✓${NC} Files uploaded"
echo -e "${YELLOW}→${NC} Deploying on server..."

# Wykonaj deployment na serwerze
ssh "$SERVER" << 'ENDSSH'
set -e
cd /root/projekt_sklep/sklep

# Backup timestamp
BACKUP_DATE=$(date +%Y%m%d_%H%M%S)

# Backup bazy
echo "Creating backup..."
docker exec sklep_db mysqldump -u root -proot sklep_laravel > "backup_${BACKUP_DATE}.sql" 2>/dev/null || echo "DB backup skipped"

# Przenieś pliki na właściwe miejsca
echo "Moving files..."
mv /tmp/OrderObserver.php app/Observers/
mv /tmp/ReportController.php app/Http/Controllers/Admin/
mv /tmp/default.conf docker/nginx/conf.d/
mv /tmp/quick-fix.sh .
chmod +x quick-fix.sh

# Restart kontenerów
echo "Restarting containers..."
docker-compose -f docker-compose.prod.yml restart app nginx

# Czekaj na uruchomienie
sleep 5

# Wyczyść cache
echo "Clearing cache..."
docker exec sklep_app php artisan config:clear
docker exec sklep_app php artisan cache:clear
docker exec sklep_app php artisan route:clear
docker exec sklep_app php artisan view:clear

echo ""
echo "✅ Deployment completed!"
echo "Backup: backup_${BACKUP_DATE}.sql"
ENDSSH

echo ""
echo -e "${GREEN}✅ ONE-CLICK DEPLOYMENT COMPLETED!${NC}"
echo ""
echo "📝 Next steps:"
echo "  1. Test phpMyAdmin: http://bartoszkaca.online/pma"
echo "  2. Test inventory: http://bartoszkaca.online/admin/reports/inventory"
echo "  3. Test product stock: http://bartoszkaca.online/admin/products/1/stock"
echo ""
echo "🔍 Monitor logs:"
echo "  ssh $SERVER 'docker logs -f sklep_app'"
echo ""
