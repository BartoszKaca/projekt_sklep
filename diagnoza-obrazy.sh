#!/bin/bash

# 🔍 Skrypt diagnostyczny - działa zarówno z Docker jak i lokalnie

echo "============================================"
echo "🔍 DIAGNOZA PROBLEMÓW Z OBRAZAMI"
echo "============================================"
echo ""

# Sprawdź czy działa Docker
USE_DOCKER=false
if docker-compose ps 2>/dev/null | grep -q "laravel_app"; then
    USE_DOCKER=true
    echo "🐳 Wykryto działające kontenery Docker"
    EXEC_PREFIX="docker-compose exec -T app"
else
    echo "💻 Tryb lokalny (bez Docker)"
    EXEC_PREFIX=""
fi
echo ""

# 1. Sprawdź link symboliczny
echo "1️⃣ Sprawdzam link symboliczny public/storage..."
if [ -L "public/storage" ]; then
    target=$(readlink public/storage)
    echo "   ✅ Link symboliczny istnieje"
    echo "   📂 Wskazuje na: $target"
else
    echo "   ❌ Link symboliczny NIE istnieje!"
    echo "   💡 Rozwiązanie:"
    if [ "$USE_DOCKER" = true ]; then
        echo "      docker-compose exec app php artisan storage:link"
    else
        echo "      php artisan storage:link"
    fi
fi
echo ""

# 2. Sprawdź uprawnienia
echo "2️⃣ Sprawdzam uprawnienia katalogów..."
echo "   storage/: $(ls -ld storage 2>/dev/null | awk '{print $1}' || echo 'nie można sprawdzić')"
echo "   storage/app/: $(ls -ld storage/app 2>/dev/null | awk '{print $1}' || echo 'nie można sprawdzić')"
if [ -d "storage/app/public" ]; then
    echo "   storage/app/public/: $(ls -ld storage/app/public | awk '{print $1}')"
else
    echo "   ❌ storage/app/public/ nie istnieje!"
fi
echo ""

# 3. Sprawdź katalog products
echo "3️⃣ Sprawdzam katalog products..."
if [ -d "storage/app/public/products" ]; then
    echo "   ✅ Katalog storage/app/public/products istnieje"
    file_count=$(find storage/app/public/products -type f 2>/dev/null | wc -l | tr -d ' ')
    echo "   📁 Liczba plików: $file_count"
    
    if [ "$file_count" -gt 0 ]; then
        echo "   📄 Przykładowe pliki:"
        ls -lh storage/app/public/products 2>/dev/null | head -5 | tail -4
    else
        echo "   ⚠️  Katalog jest pusty - brak obrazów!"
    fi
else
    echo "   ❌ Katalog storage/app/public/products NIE istnieje!"
    echo "   💡 Rozwiązanie:"
    echo "      mkdir -p storage/app/public/products"
    echo "      chmod -R 775 storage/app/public/products"
fi
echo ""

# 4. Sprawdź konfigurację .env
echo "4️⃣ Sprawdzam konfigurację .env..."
if [ -f ".env" ]; then
    db_host=$(grep "^DB_HOST=" .env | cut -d '=' -f2)
    app_url=$(grep "^APP_URL=" .env | cut -d '=' -f2)
    fs_disk=$(grep "^FILESYSTEM_DISK=" .env | cut -d '=' -f2)
    
    echo "   DB_HOST=$db_host"
    echo "   APP_URL=$app_url"
    echo "   FILESYSTEM_DISK=$fs_disk"
    
    if [ "$USE_DOCKER" = true ] && [ "$db_host" != "db" ]; then
        echo "   ⚠️  Używasz Docker ale DB_HOST != db"
    fi
    
    if [ "$USE_DOCKER" = false ] && [ "$db_host" = "db" ]; then
        echo "   ⚠️  DB_HOST=db ale Docker nie działa!"
        echo "   💡 Zmień na DB_HOST=127.0.0.1"
    fi
else
    echo "   ❌ Plik .env nie istnieje!"
fi
echo ""

# 5. Sprawdź połączenie z bazą
echo "5️⃣ Sprawdzam połączenie z bazą danych..."
if [ "$USE_DOCKER" = true ]; then
    result=$(docker-compose exec -T app php artisan tinker --execute="
    try {
        \$conn = \DB::connection()->getPdo();
        echo 'OK';
    } catch (\Exception \$e) {
        echo 'ERROR: ' . \$e->getMessage();
    }
    " 2>&1 | grep -E "OK|ERROR" | head -1)
else
    result=$($EXEC_PREFIX php artisan tinker --execute="
    try {
        \$conn = \DB::connection()->getPdo();
        echo 'OK';
    } catch (\Exception \$e) {
        echo 'ERROR: ' . \$e->getMessage();
    }
    " 2>&1 | grep -E "OK|ERROR" | head -1)
fi

if echo "$result" | grep -q "OK"; then
    echo "   ✅ Połączono z bazą danych!"
    
    # Sprawdź produkty
    if [ "$USE_DOCKER" = true ]; then
        docker-compose exec -T app php artisan tinker --execute="
        \$products = \App\Models\Product::count();
        \$images = \App\Models\ProductImage::count();
        echo '   📦 Produktów: ' . \$products . PHP_EOL;
        echo '   🖼️  Obrazów: ' . \$images . PHP_EOL;
        if (\$images > 0) {
            echo '   
📄 Przykładowe ścieżki:' . PHP_EOL;
            \$samples = \App\Models\ProductImage::take(3)->get(['id', 'path']);
            foreach(\$samples as \$img) {
                echo '      ID: ' . \$img->id . ' → ' . \$img->path . PHP_EOL;
            }
        }
        " 2>/dev/null
    else
        php artisan tinker --execute="
        \$products = \App\Models\Product::count();
        \$images = \App\Models\ProductImage::count();
        echo '   📦 Produktów: ' . \$products . PHP_EOL;
        echo '   🖼️  Obrazów: ' . \$images . PHP_EOL;
        if (\$images > 0) {
            echo '   📄 Przykładowe ścieżki:' . PHP_EOL;
            \$samples = \App\Models\ProductImage::take(3)->get(['id', 'path']);
            foreach(\$samples as \$img) {
                echo '      ID: ' . \$img->id . ' → ' . \$img->path . PHP_EOL;
            }
        }
        " 2>/dev/null
    fi
else
    echo "   ❌ Brak połączenia z bazą!"
    echo "   $result"
    echo ""
    echo "   💡 Rozwiązanie:"
    if [ "$USE_DOCKER" = true ]; then
        echo "      docker-compose up -d"
        echo "      docker-compose logs db"
    else
        echo "      1. Sprawdź czy MySQL działa"
        echo "      2. Sprawdź dane w .env (DB_HOST, DB_USERNAME, DB_PASSWORD)"
        echo "      3. Upewnij się że baza 'sklep_laravel' istnieje"
    fi
fi
echo ""

# 6. Sprawdź czy serwer działa
echo "6️⃣ Sprawdzam serwer..."
if [ "$USE_DOCKER" = true ]; then
    if docker-compose ps | grep -q "laravel_app.*Up"; then
        echo "   ✅ Kontenery Docker działają"
        echo "   📍 http://localhost:8000"
    else
        echo "   ❌ Kontenery nie działają!"
        echo "   💡 Uruchom: docker-compose up -d"
    fi
else
    if curl -s http://localhost:8000 > /dev/null 2>&1; then
        echo "   ✅ Serwer działa na http://localhost:8000"
    else
        echo "   ⚠️  Serwer nie odpowiada"
        echo "   💡 Uruchom: php artisan serve"
    fi
fi
echo ""

# PODSUMOWANIE
echo "============================================"
echo "📋 PODSUMOWANIE"
echo "============================================"
echo ""

problems=0

# Problem 1: Link symboliczny
if [ ! -L "public/storage" ]; then
    problems=$((problems+1))
    echo "❌ Problem #$problems: Brak linku symbolicznego"
    if [ "$USE_DOCKER" = true ]; then
        echo "   Rozwiązanie: docker-compose exec app php artisan storage:link"
    else
        echo "   Rozwiązanie: php artisan storage:link"
    fi
    echo ""
fi

# Problem 2: Brak katalogu products
if [ ! -d "storage/app/public/products" ]; then
    problems=$((problems+1))
    echo "❌ Problem #$problems: Brak katalogu products"
    echo "   Rozwiązanie: mkdir -p storage/app/public/products && chmod -R 775 storage"
    echo ""
fi

# Problem 3: Baza danych
if ! echo "$result" | grep -q "OK"; then
    problems=$((problems+1))
    echo "❌ Problem #$problems: Brak połączenia z bazą danych"
    if [ "$USE_DOCKER" = true ]; then
        echo "   Rozwiązanie: ./start-docker.sh"
    else
        echo "   Rozwiązanie: Sprawdź .env i upewnij się że MySQL działa"
    fi
    echo ""
fi

# Brak problemów
if [ $problems -eq 0 ]; then
    echo "✅ Nie wykryto krytycznych problemów!"
    echo ""
    echo "Jeśli obrazy nadal się nie wyświetlają:"
    echo "1. Sprawdź czy w bazie są produkty z obrazami"
    echo "2. Otwórz Console w przeglądarce (F12) i sprawdź zakładkę Network"
    echo "3. Sprawdź URL: http://localhost:8000/storage/products/test.jpg"
    echo "4. Upewnij się że ścieżki w bazie są poprawne (bez 'storage/' na początku)"
else
    echo "⚠️  Znaleziono $problems problem(y)"
    echo ""
    echo "📚 Zobacz pełną dokumentację: ROZWIAZANIE_OBRAZY.md"
    echo ""
    if [ "$USE_DOCKER" = true ]; then
        echo "🚀 Szybkie rozwiązanie: ./start-docker.sh"
    else
        echo "🚀 Szybkie rozwiązanie: ./start-local.sh"
    fi
fi

echo ""
echo "============================================"
