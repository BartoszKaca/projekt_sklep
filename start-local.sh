#!/bin/bash

# 🔧 Skrypt dla lokalnego środowiska (bez Dockera)

echo "============================================"
echo "🔧 KONFIGURACJA LOKALNEGO ŚRODOWISKA"
echo "============================================"
echo ""

# 1. Sprawdź PHP
echo "1️⃣ Sprawdzam PHP..."
if ! command -v php &> /dev/null; then
    echo "   ❌ PHP nie jest zainstalowane!"
    exit 1
fi
php_version=$(php -v | head -n 1)
echo "   ✅ $php_version"
echo ""

# 2. Sprawdź Composer
echo "2️⃣ Sprawdzam Composer..."
if ! command -v composer &> /dev/null; then
    echo "   ❌ Composer nie jest zainstalowany!"
    exit 1
fi
echo "   ✅ Composer zainstalowany"
echo ""

# 3. Sprawdź MySQL
echo "3️⃣ Sprawdzam MySQL..."
if command -v mysql &> /dev/null; then
    echo "   ✅ MySQL zainstalowany"
else
    echo "   ⚠️  MySQL nie znaleziony - upewnij się że jest zainstalowany"
fi
echo ""

# 4. Instaluj zależności
echo "4️⃣ Instaluję zależności..."
if [ ! -d "vendor" ]; then
    composer install
else
    echo "   ✅ Zależności już zainstalowane"
fi
echo ""

# 5. Konfiguracja .env
echo "5️⃣ Sprawdzam konfigurację .env..."
if grep -q "DB_HOST=db" .env; then
    echo "   ⚠️  Wykryto konfigurację Docker w .env"
    echo "   💡 Zmień DB_HOST=db na DB_HOST=127.0.0.1"
    echo ""
    read -p "   Czy chcesz użyć .env.local? (t/n): " answer
    if [ "$answer" = "t" ] || [ "$answer" = "T" ]; then
        cp .env.local .env
        echo "   ✅ Skopiowano .env.local do .env"
        echo "   ⚠️  PAMIĘTAJ: Ustaw swoje dane MySQL w .env (DB_USERNAME, DB_PASSWORD)"
    fi
fi
echo ""

# 6. Utwórz bazę danych
echo "6️⃣ Tworzenie bazy danych..."
echo "   💡 Pamiętaj aby utworzyć bazę danych:"
echo "   mysql -u root -p"
echo "   CREATE DATABASE sklep_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
echo ""
read -p "   Czy baza danych już istnieje? (t/n): " db_exists

if [ "$db_exists" != "t" ] && [ "$db_exists" != "T" ]; then
    echo "   Utwórz bazę przed kontynuowaniem!"
    exit 1
fi

# 7. Uruchom migracje
echo ""
echo "7️⃣ Uruchamiam migracje..."
php artisan migrate

# 8. Utwórz link symboliczny
echo ""
echo "8️⃣ Tworzę link symboliczny dla obrazów..."
php artisan storage:link

# 9. Napraw uprawnienia
echo ""
echo "9️⃣ Naprawiam uprawnienia..."
chmod -R 775 storage bootstrap/cache
mkdir -p storage/app/public/products
chmod -R 775 storage/app/public/products

# 10. Sprawdź połączenie
echo ""
echo "🔟 Testuję połączenie z bazą..."
php artisan tinker --execute="
try {
    echo 'Testowanie połączenia...' . PHP_EOL;
    \$connection = \DB::connection()->getPdo();
    echo '✅ Połączono z bazą danych!' . PHP_EOL;
    \$count = \DB::table('products')->count();
    echo '📦 Liczba produktów: ' . \$count . PHP_EOL;
} catch (\Exception \$e) {
    echo '❌ Błąd: ' . \$e->getMessage() . PHP_EOL;
    echo '💡 Sprawdź konfigurację DB_* w pliku .env' . PHP_EOL;
}
"

echo ""
echo "============================================"
echo "✅ GOTOWE!"
echo "============================================"
echo ""
echo "🚀 Uruchom serwer:"
echo "   php artisan serve"
echo ""
echo "📍 Aplikacja: http://localhost:8000"
echo ""
echo "💡 Opcjonalnie uruchom Vite (jeśli używasz):"
echo "   npm install"
echo "   npm run dev"
echo ""
