# AI Agent Instructions for projekt_sklep

## Architektura projektu
Ten projekt to sklep internetowy zbudowany w Laravel, skupiający się na sprzedaży albumów muzycznych i gadżetów. Oto kluczowe aspekty architektury:

### Główne komponenty
- **Models** (`app/Models/`): Implementują logikę biznesową i relacje między encjami
- **Controllers** (`app/Http/Controllers/`): Obsługują żądania HTTP i koordynują operacje
- **Views** (`resources/views/`): Blade templates dla interfejsu użytkownika
- **Migrations** (`database/migrations/`): Definicje struktur bazy danych

### Kluczowe wzorce i konwencje
1. **Zarządzanie produktami**:
   - Produkty (`Product.php`) mają wiele wariantów (`ProductVariant.php`)
   - Automatyczne śledzenie stanu magazynowego przez `StockMovement.php`
   - Przykład: `$product->decreaseStock(2)` automatycznie tworzy wpis w historii zmian

2. **Obsługa zamówień**:
   - Zamówienia (`Order.php`) zawierają pozycje (`OrderItem.php`) i informacje o wysyłce (`OrderShipping.php`)
   - Stan magazynowy jest aktualizowany automatycznie przy finalizacji zamówienia

3. **Zarządzanie obrazami**:
   - Produkty mogą mieć wiele obrazów z określoną kolejnością
   - Jeden obraz jest oznaczony jako główny (`is_primary`)

## Środowisko deweloperskie

### Docker Setup
```bash
docker-compose up -d  # Uruchamia środowisko (MySQL + PHP + phpMyAdmin)
docker-compose exec app php artisan migrate  # Migracje bazy danych
```

Porty:
- Aplikacja: `localhost:8000`
- phpMyAdmin: `localhost:8080`
- MySQL: `localhost:3307`

### Baza danych
- Host: `db`
- Database: `sklep_laravel`
- User: `laravel`
- Password: `laravel`

## Wskazówki dla AI
1. Używaj Soft Deletes dla modeli związanych z produktami i zamówieniami
2. Implementuj scope'y dla często używanych filtrów (np. `active()`, `featured()`)
3. Wykorzystuj relacje Eloquent zamiast ręcznych joinów
4. Zawsze używaj transakcji dla operacji zmieniających stan magazynowy

## Przydatne komendy
```bash
php artisan make:model NazwaModelu -mcr  # Tworzy model, migrację i kontroler
php artisan storage:link  # Linkuje storage dla plików publicznych
php artisan cache:clear  # Czyści cache aplikacji
```