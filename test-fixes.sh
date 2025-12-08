#!/bin/bash

# 🧪 Test Script - Weryfikacja działania napraw
# Data: 08.12.2025

set -e

# Kolory
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Domain/IP do testowania
DOMAIN="${1:-bartoszkaca.online}"

echo ""
echo "🧪 Testing fixes on: $DOMAIN"
echo "========================================"
echo ""

# Test 1: Główna strona
echo -n "1. Testing main page... "
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://$DOMAIN)
if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "301" ] || [ "$HTTP_CODE" = "302" ]; then
    echo -e "${GREEN}✓ OK${NC} (HTTP $HTTP_CODE)"
else
    echo -e "${RED}✗ FAILED${NC} (HTTP $HTTP_CODE)"
fi

# Test 2: phpMyAdmin
echo -n "2. Testing phpMyAdmin (/pma)... "
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://$DOMAIN/pma/)
if [ "$HTTP_CODE" = "200" ]; then
    echo -e "${GREEN}✓ OK${NC} (HTTP $HTTP_CODE)"
    
    # Sprawdź czy ładują się style
    echo -n "   - Checking CSS loading... "
    RESPONSE=$(curl -s http://$DOMAIN/pma/ | grep -c "themes/pmahomme/css" || echo "0")
    if [ "$RESPONSE" -gt "0" ]; then
        echo -e "${GREEN}✓ OK${NC}"
    else
        echo -e "${YELLOW}⚠ WARNING${NC} (CSS might not load)"
    fi
else
    echo -e "${RED}✗ FAILED${NC} (HTTP $HTTP_CODE)"
fi

# Test 3: Admin panel
echo -n "3. Testing admin panel... "
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://$DOMAIN/admin)
if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "301" ] || [ "$HTTP_CODE" = "302" ]; then
    echo -e "${GREEN}✓ OK${NC} (HTTP $HTTP_CODE)"
else
    echo -e "${YELLOW}⚠ WARNING${NC} (HTTP $HTTP_CODE) - might need login"
fi

# Test 4: Sprawdź kontenery
echo ""
echo "4. Checking Docker containers..."
if command -v docker &> /dev/null; then
    docker ps --format "table {{.Names}}\t{{.Status}}" | grep sklep || {
        echo -e "${YELLOW}⚠ WARNING${NC} Docker containers not found (might be on remote server)"
    }
else
    echo -e "${YELLOW}⚠ WARNING${NC} Docker not available locally"
fi

# Test 5: Sprawdź czy są błędy w logach (jeśli lokalnie)
echo ""
echo "5. Checking Laravel logs..."
if docker ps | grep -q sklep_app; then
    echo "Recent errors in Laravel log:"
    docker exec sklep_app tail -n 50 /var/www/html/storage/logs/laravel.log 2>/dev/null | grep -i "error\|exception\|failed" | tail -n 5 || {
        echo -e "${GREEN}✓ No recent errors found${NC}"
    }
else
    echo -e "${YELLOW}⚠ Skipped${NC} (app container not accessible locally)"
fi

# Test 6: Test konkretnych endpointów z logowaniem
echo ""
echo "6. Testing specific endpoints (requires login)..."
echo -e "${BLUE}ℹ${NC} Manual test required:"
echo "   - Login to: http://$DOMAIN/login"
echo "   - Test: http://$DOMAIN/admin/reports/inventory"
echo "   - Test: http://$DOMAIN/admin/products/1/stock"

# Podsumowanie
echo ""
echo "========================================"
echo "📊 Test Summary"
echo "========================================"
echo ""
echo "✅ Automated tests completed"
echo ""
echo "📝 Manual tests required:"
echo "   1. Login as admin"
echo "   2. Go to: Reports → Inventory"
echo "   3. Go to: Products → [Any Product] → Manage Stock"
echo "   4. Change order status (check for _oldStatus error)"
echo "   5. Access phpMyAdmin at: http://$DOMAIN/pma"
echo ""
echo "🔍 Check logs if any issues:"
echo "   ssh root@$DOMAIN"
echo "   docker logs sklep_app --tail=100"
echo "   docker exec sklep_app tail /var/www/html/storage/logs/laravel.log"
echo ""

# Test dostępności API (jeśli istnieje)
echo "7. Additional checks..."
echo -n "   - Checking Redis... "
if docker ps | grep -q sklep_redis; then
    echo -e "${GREEN}✓ Running${NC}"
else
    echo -e "${YELLOW}⚠ Not running locally${NC}"
fi

echo -n "   - Checking MySQL... "
if docker ps | grep -q sklep_db; then
    echo -e "${GREEN}✓ Running${NC}"
else
    echo -e "${YELLOW}⚠ Not running locally${NC}"
fi

echo -n "   - Checking Nginx... "
if docker ps | grep -q sklep_nginx; then
    echo -e "${GREEN}✓ Running${NC}"
else
    echo -e "${YELLOW}⚠ Not running locally${NC}"
fi

echo ""
echo "🎉 Testing completed!"
echo ""
