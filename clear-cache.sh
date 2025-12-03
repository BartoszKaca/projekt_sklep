#!/bin/bash

echo "🧹 Czyszczenie cache Laravel..."
echo ""

php artisan config:clear
echo "✓ Config cache wyczyszczony"

php artisan route:clear
echo "✓ Route cache wyczyszczony"

php artisan view:clear
echo "✓ View cache wyczyszczony"

php artisan cache:clear
echo "✓ Application cache wyczyszczony"

php artisan optimize:clear
echo "✓ Wszystkie cache wyczyszczone"

echo ""
echo "✅ Gotowe! Teraz przetestuj aplikację."
