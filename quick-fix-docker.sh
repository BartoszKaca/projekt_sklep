#!/bin/bash

# 🔥 SZYBKI FIX - dla już działającego projektu Docker

echo "🔥 SZYBKA NAPRAWA OBRAZÓW (Docker)"
echo "===================================="
echo ""

# Sprawdź czy Docker działa
if ! docker-compose ps 2>/dev/null | grep -q "laravel_app"; then
    echo "❌ Kontenery Docker nie działają!"
    echo "💡 Uruchom: docker-compose up -d"
    exit 1
fi

echo "✅ Docker działa"
echo ""

# 1. Storage link
echo "1️⃣ Tworzę link symboliczny..."
docker-compose exec -T app php artisan storage:link
echo ""

# 2. Uprawnienia
echo "2️⃣ Naprawiam uprawnienia..."
docker-compose exec -T app chmod -R 775 storage bootstrap/cache
docker-compose exec -T app mkdir -p storage/app/public/products
docker-compose exec -T app chmod -R 775 storage/app/public/products
echo ""

# 3. Cache
echo "3️⃣ Czyszczę cache..."
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan view:clear
echo ""

# 4. Test
echo "4️⃣ Testuję połączenie..."
docker-compose exec -T app php artisan tinker --execute="
try {
    \$products = \App\Models\Product::count();
    \$images = \App\Models\ProductImage::count();
    echo '✅ Połączono!' . PHP_EOL;
    echo '📦 Produktów: ' . \$products . PHP_EOL;
    echo '🖼️  Obrazów: ' . \$images . PHP_EOL;
} catch (\Exception \$e) {
    echo '❌ Błąd: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "✅ GOTOWE!"
echo ""
echo "Sprawdź: http://localhost:8000"
echo ""
