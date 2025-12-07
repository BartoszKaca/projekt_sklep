# 🔧 Naprawa błędów 500 i konfiguracja phpMyAdmin

## Problem 1: Błąd 500 na `/admin/products/{id}/stock`

### Przyczyna:
- Brak obsługi błędów w metodzie `stock()`
- Potencjalne problemy z relacjami w widoku
- Brak walidacji danych

### Rozwiązanie:
1. ✅ Dodano try-catch w metodzie `stock()`
2. ✅ Dodano logowanie błędów
3. ✅ Poprawiono widok - dodano sprawdzanie null dla variants

## Problem 2: Błąd 500 na raporcie inwentarza

### Przyczyna:
- Błąd przy dostępie do `->value` gdy `first()` zwraca `null`
- Brak obsługi wyjątków

### Rozwiązanie:
1. ✅ Poprawiono dostęp do wartości w `inventory()`
2. ✅ Dodano sprawdzanie czy wynik nie jest null
3. ✅ Dodano try-catch z logowaniem

## Problem 3: phpMyAdmin przez subdomenę nie działa

### Przyczyna:
- Konfiguracja przez subdomenę wymaga konfiguracji DNS
- Użytkownik wolał prostszą ścieżkę `/pma`

### Rozwiązanie:
1. ✅ Zmieniono konfigurację Nginx na ścieżkę `/pma`
2. ✅ Usunięto osobny plik `phpmyadmin.conf`
3. ✅ Dodano proxy w głównym konfigu Nginx

## Co zostało zmienione:

### 1. `app/Http/Controllers/Admin/ProductController.php`
```php
public function stock(Product $product)
{
    try {
        $product->load(['variants', 'category']);
        $movements = $product->stockMovements()
            ->with(['user', 'variant'])
            ->latest()
            ->paginate(20);
        
        return view('admin.products.stock', compact('product', 'movements'));
    } catch (\Exception $e) {
        \Log::error('Stock page error: ' . $e->getMessage(), [
            'product_id' => $product->id,
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->route('admin.products.index')
            ->with('error', 'Wystąpił błąd podczas ładowania strony stanu magazynowego.');
    }
}
```

### 2. `app/Http/Controllers/Admin/ReportController.php`
```php
// Poprawiono dostęp do wartości:
$productsValueResult = Product::whereDoesntHave('variants')
    ->selectRaw('SUM(stock_quantity * price) as value')
    ->first();
$productsValue = $productsValueResult && $productsValueResult->value ? $productsValueResult->value : 0;
```

### 3. `docker/nginx/conf.d/default.conf`
```nginx
# phpMyAdmin - dostęp przez /pma
location /pma {
    rewrite ^/pma$ /pma/ permanent;
}

location /pma/ {
    proxy_pass http://phpmyadmin/;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_set_header X-Forwarded-Host $host;
    proxy_set_header X-Forwarded-Port $server_port;
    
    proxy_connect_timeout 60s;
    proxy_send_timeout 300s;
    proxy_read_timeout 300s;
    proxy_buffering off;
    proxy_request_buffering off;
    
    proxy_redirect http://phpmyadmin/ /pma/;
    proxy_redirect / /pma/;
    proxy_cookie_path / /pma;
}
```

### 4. `resources/views/admin/products/stock.blade.php`
- Poprawiono JavaScript - dodano sprawdzanie null dla variants

## Wdrożenie na serwerze:

```bash
cd /var/www/sklep
git pull origin main

# Przeładuj konfigurację Nginx
docker compose -f docker-compose.prod.yml restart nginx

# Sprawdź logi
docker logs sklep_app --tail 50
docker logs sklep_nginx --tail 50
```

## Dostęp do phpMyAdmin:

**Przed:** `http://pma.domenaserwera.pl` (wymagało konfiguracji DNS)

**Teraz:** `http://www.domenaserwera.pl/pma` (prostsze, bez dodatkowej konfiguracji DNS)

## Testowanie:

1. **Strona stanu magazynowego:**
   - Wejdź na `/admin/products/{id}/stock`
   - Powinno załadować się bez błędu 500

2. **Raport inwentarza:**
   - Wejdź na `/admin/reports/inventory`
   - Powinno wygenerować raport bez błędu

3. **phpMyAdmin:**
   - Wejdź na `http://www.bartoszkaca.online/pma`
   - Powinno przekierować na phpMyAdmin

## Troubleshooting:

**Jeśli nadal błąd 500:**
```bash
# Sprawdź logi Laravel
docker exec sklep_app tail -f storage/logs/laravel.log

# Sprawdź logi Nginx
docker logs sklep_nginx --tail 100

# Sprawdź błędy PHP
docker exec sklep_app php artisan config:clear
docker exec sklep_app php artisan cache:clear
```

**Jeśli phpMyAdmin nie działa:**
```bash
# Sprawdź czy kontener działa
docker ps | grep phpmyadmin

# Sprawdź logi
docker logs sklep_phpmyadmin

# Przetestuj połączenie
docker exec sklep_nginx wget -O- http://phpmyadmin/
```

---

**Po wdrożeniu:**
1. ✅ Sprawdź dostęp do `/admin/products/{id}/stock`
2. ✅ Sprawdź raport inwentarza
3. ✅ Sprawdź dostęp do phpMyAdmin przez `/pma`
