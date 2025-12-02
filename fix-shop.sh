#!/bin/bash

echo "=========================================="
echo "RAP SHOP - Skrypt naprawczy"
echo "=========================================="
echo ""

# Sprawdź czy jesteśmy w katalogu projektu
if [ ! -f "artisan" ]; then
    echo "❌ Błąd: Nie znaleziono pliku artisan."
    echo "Upewnij się, że uruchamiasz skrypt z głównego katalogu projektu."
    exit 1
fi

echo "✓ Znaleziono katalog projektu Laravel"
echo ""

# 1. Zmiana nazwy pliku odred-detail.blade.php
echo "1. Naprawa nazwy pliku order-detail..."
if [ -f "resources/views/account/odred-detail.blade.php" ]; then
    mv resources/views/account/odred-detail.blade.php resources/views/account/order-detail.blade.php
    echo "✓ Zmieniono nazwę pliku odred-detail.blade.php -> order-detail.blade.php"
else
    echo "⚠ Plik odred-detail.blade.php nie istnieje lub został już zmieniony"
fi
echo ""

# 2. Usunięcie zbędnych plików dokumentacji mailingu
echo "2. Usuwanie zbędnych plików dokumentacji mailingu..."

files_to_remove=(
    "MAILING_CHANGES.md"
    "MAILING_QUICK_START.md"
    "MAILING_README.md"
    "MAILING_SYSTEM.md"
    "TESTING_EMAILS.md"
    "TESTING_MAILING_SYSTEM.md"
    "test-mailing.sh"
)

removed_count=0
for file in "${files_to_remove[@]}"; do
    if [ -f "$file" ]; then
        rm "$file"
        echo "✓ Usunięto: $file"
        removed_count=$((removed_count + 1))
    fi
done

if [ $removed_count -eq 0 ]; then
    echo "⚠ Nie znaleziono plików do usunięcia (mogły być już usunięte)"
else
    echo "✓ Usunięto $removed_count plików"
fi
echo ""

# 3. Czyszczenie cache
echo "3. Czyszczenie cache Laravel..."
php artisan cache:clear > /dev/null 2>&1
echo "✓ Cache wyczyszczony"

php artisan config:clear > /dev/null 2>&1
echo "✓ Config cache wyczyszczony"

php artisan route:clear > /dev/null 2>&1
echo "✓ Route cache wyczyszczony"

php artisan view:clear > /dev/null 2>&1
echo "✓ View cache wyczyszczony"
echo ""

# 4. Podsumowanie
echo "=========================================="
echo "✅ Naprawa zakończona!"
echo "=========================================="
echo ""
echo "Wykonane operacje:"
echo "  ✓ Zmieniono nazwę pliku order-detail"
echo "  ✓ Usunięto zbędne pliki dokumentacji"
echo "  ✓ Wyczyszczono cache Laravel"
echo ""
echo "Następne kroki:"
echo "  1. Uruchom serwer: php artisan serve"
echo "  2. Zaloguj się i przetestuj wishlist"
echo "  3. Sprawdź szczegóły w pliku FIXES.md"
echo ""
echo "=========================================="
