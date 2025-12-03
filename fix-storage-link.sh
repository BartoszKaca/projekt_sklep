#!/bin/bash

# Skrypt naprawiający wyświetlanie obrazów w Laravel
# Problem: brak linku symbolicznego między public/storage a storage/app/public

echo "🔧 Naprawiam wyświetlanie obrazów..."

# Usuń stary link jeśli istnieje
if [ -L "public/storage" ]; then
    echo "Usuwam stary link symboliczny..."
    rm public/storage
fi

# Utwórz nowy link symboliczny
echo "Tworzę link symboliczny: public/storage -> storage/app/public"
php artisan storage:link

# Sprawdź uprawnienia do katalogów
echo "Sprawdzam uprawnienia..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Upewnij się, że katalog products istnieje w storage/app/public
if [ ! -d "storage/app/public/products" ]; then
    echo "Tworzę katalog storage/app/public/products"
    mkdir -p storage/app/public/products
    chmod -R 775 storage/app/public/products
fi

echo "✅ Gotowe! Teraz obrazy powinny się wyświetlać."
echo ""
echo "Jeśli obrazy nadal się nie wyświetlają, sprawdź:"
echo "1. Czy obrazy są zapisane w storage/app/public/products/"
echo "2. Czy w bazie danych ścieżki zaczynają się od 'products/' (bez 'storage/')"
echo "3. Czy w widoku używasz: asset('storage/' . \$image->path)"
