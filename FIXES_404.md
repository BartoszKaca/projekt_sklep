# 🔧 Naprawa błędu 404 przy dodawaniu do koszyka

## Problem
Po kliknięciu "Dodaj do koszyka" pojawiał się błąd 404.

## Co zostało naprawione:

### 1. **Poprawiona obsługa routy w JavaScript**
   - Zastosowano `@json(route('cart.add'))` zamiast `{{ route('cart.add') }}`
   - Dodano fallback URL `/cart/add`
   - Dodano sprawdzanie czy route istnieje przed wywołaniem

### 2. **Ulepszona obsługa błędów**
   - Sprawdzanie statusu 404
   - Obsługa błędów parsowania JSON
   - Szczegółowe logowanie błędów w konsoli

### 3. **Poprawiona obsługa produktów z wariantami**
   - Przekierowanie na stronę produktu gdy wymagany jest wariant
   - Dodano atrybuty `data-product-id` i `data-slug` do kart produktów
   - Wszystkie widoki aktualizowane (home, products/index, products/show, wishlist)

### 4. **Poprawiona obsługa błędów w kontrolerze**
   - Try-catch w metodzie `add()`
   - Lepsze logowanie błędów
   - Zwracanie `product_slug` i `redirect_url` w odpowiedzi

### 5. **Zaktualizowane wszystkie funkcje addToCart**
   - `resources/views/products/index.blade.php`
   - `resources/views/home.blade.php`
   - `resources/views/products/show.blade.php`
   - `resources/views/account/wishlist.blade.php`
   - `resources/views/cart/index.blade.php` (update, remove)

## Co sprawdzić na serwerze:

### 1. Wyczyść cache routów:
```bash
cd /var/www/sklep
./deploy.sh artisan route:clear
./deploy.sh artisan config:clear
./deploy.sh artisan cache:clear
```

### 2. Sprawdź czy route działa:
```bash
./deploy.sh artisan route:list | grep cart.add
```

Powinno pokazać:
```
POST   cart/add  .................................... cart.add › CartController@add
```

### 3. Sprawdź logi:
```bash
./deploy.sh logs app | grep -i "cart\|404"
```

### 4. Sprawdź w przeglądarce:
- Otwórz konsolę deweloperską (F12)
- Sprawdź Network tab
- Kliknij "Dodaj do koszyka"
- Zobacz jaki URL jest wywoływany i jaki status zwraca

## Jeśli nadal występuje 404:

1. **Sprawdź czy route jest zarejestrowany:**
   ```bash
   ./deploy.sh artisan route:list | grep cart
   ```

2. **Sprawdź czy plik routes/web.php jest poprawny:**
   ```bash
   cat routes/web.php | grep -A 2 "cart.add"
   ```

3. **Sprawdź cache:**
   ```bash
   ./deploy.sh artisan route:cache
   ./deploy.sh artisan config:cache
   ```

4. **Restart aplikacji:**
   ```bash
   ./deploy.sh restart
   ```

## Zmienione pliki:

- ✅ `app/Http/Controllers/CartController.php` - lepsza obsługa błędów
- ✅ `resources/views/products/index.blade.php` - poprawiony JavaScript
- ✅ `resources/views/home.blade.php` - dodano obsługę wariantów
- ✅ `resources/views/products/show.blade.php` - poprawiona obsługa błędów
- ✅ `resources/views/cart/index.blade.php` - poprawione funkcje update/remove
- ✅ `resources/views/account/wishlist.blade.php` - poprawiony JavaScript
- ✅ `resources/views/layouts/shop.blade.php` - poprawiony toggleWishlist
- ✅ `resources/views/layouts/app.blade.php` - poprawiony toggleWishlist
- ✅ `app/Http/Controllers/HomeController.php` - załadowanie wariantów

---

**Po wdrożeniu zmian na serwerze, wyczyść cache routów!**
