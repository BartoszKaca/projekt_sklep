#!/bin/bash

# 🚀 Skrypt startowy dla projektu Laravel z Dockerem
# Rozwiązuje problemy z bazą danych i obrazami

echo "============================================"
echo "🚀 URUCHAMIANIE PROJEKTU RAP SHOP"
echo "============================================"
echo ""

# 1. Sprawdź czy Docker działa
echo "1️⃣ Sprawdzam Docker..."
if ! command -v docker &> /dev/null; then
    echo "   ❌ Docker nie jest zainstalowany!"
    echo "   📥 Zainstaluj Docker Desktop z: https://www.docker.com/products/docker-desktop"
    exit 1
fi

if ! docker info &> /dev/null; then
    echo "   ❌ Docker nie jest uruchomiony!"
    echo "   💡 Uruchom Docker Desktop i spróbuj ponownie"
    exit 1
fi

echo "   ✅ Docker działa"
echo ""

# 2. Uruchom kontenery
echo "2️⃣ Uruchamiam kontenery Docker..."
docker-compose up -d

echo "   ⏳ Czekam na uruchomienie bazy danych (15 sekund)..."
sleep 15

# 3. Sprawdź czy kontenery działają
echo ""
echo "3️⃣ Status kontenerów:"
docker-compose ps

# 4. Utwórz link symboliczny dla storage
echo ""
echo "4️⃣ Tworzę link symboliczny dla obrazów..."
docker-compose exec -T app php artisan storage:link

# 5. Uruchom migracje
echo ""
echo "5️⃣ Uruchamiam migracje bazy danych..."
docker-compose exec -T app php artisan migrate --force

# 6. Napraw uprawnienia
echo ""
echo "6️⃣ Naprawiam uprawnienia katalogów..."
docker-compose exec -T app chmod -R 775 storage bootstrap/cache
docker-compose exec -T app mkdir -p storage/app/public/products
docker-compose exec -T app chmod -R 775 storage/app/public/products

# 7. Sprawdź połączenie z bazą
echo ""
echo "7️⃣ Testuję połączenie z bazą danych..."
docker-compose exec -T app php artisan tinker --execute="
try {
    \$count = \DB::table('products')->count();
    echo '✅ Połączono z bazą! Liczba produktów: ' . \$count . PHP_EOL;
} catch (\Exception \$e) {
    echo '❌ Błąd połączenia: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "============================================"
echo "✅ GOTOWE!"
echo "============================================"
echo ""
echo "📍 Aplikacja: http://localhost:8000"
echo "📍 phpMyAdmin: http://localhost:8080"
echo "   └─ Login: root / root"
echo ""
echo "🔧 Przydatne komendy:"
echo "   docker-compose logs -f app      # Zobacz logi"
echo "   docker-compose exec app bash    # Wejdź do kontenera"
echo "   docker-compose down             # Zatrzymaj kontenery"
echo "   docker-compose up -d            # Uruchom ponownie"
echo ""
echo "⚠️  Jeśli obrazy nadal się nie wyświetlają:"
echo "   1. Sprawdź czy są produkty z obrazami w bazie"
echo "   2. Otwórz http://localhost:8000/storage/products/nazwa-obrazu.jpg"
echo "   3. Zobacz console w przeglądarce (F12)"
echo ""
