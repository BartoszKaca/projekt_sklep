# 🖼️ Naprawa wyświetlania obrazów w sklepie

## Problem
Obrazy produktów nie wyświetlają się, ponieważ brakuje linku symbolicznego między `public/storage` a `storage/app/public`.

## Rozwiązanie - wykonaj następujące kroki:

### 1. Utwórz link symboliczny (NAJWAŻNIEJSZE!)

W terminalu, w katalogu głównym projektu wykonaj:

```bash
php artisan storage:link
```

To utworzy symboliczny link: `public/storage` → `storage/app/public`

### 2. Upewnij się, że katalogi mają odpowiednie uprawnienia

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 3. Sprawdź czy katalog products istnieje

```bash
mkdir -p storage/app/public/products
chmod -R 775 storage/app/public/products
```

### 4. Możesz też użyć gotowego skryptu

```bash
chmod +x fix-storage-link.sh
./fix-storage-link.sh
```

---

## Jak to działa?

### Struktura katalogów w Laravel:

```
storage/app/public/products/    ← Tu są zapisywane obrazy
         ↓ (link symboliczny)
public/storage/products/        ← Tu aplikacja ich szuka
```

### W kodzie:
- **Upload**: `$image->store('products', 'public')` zapisuje do `storage/app/public/products/`
- **Wyświetlanie**: `asset('storage/' . $path)` generuje URL: `http://twoja-domena.pl/storage/products/image.jpg`
- **Link symboliczny** sprawia, że `public/storage/` wskazuje na `storage/app/public/`

---

## Weryfikacja

Po wykonaniu powyższych kroków sprawdź:

1. **Czy link istnieje:**
```bash
ls -la public/ | grep storage
```
Powinno pokazać: `storage -> ../storage/app/public`

2. **Czy są tam obrazy:**
```bash
ls -la storage/app/public/products/
```

3. **Czy URL działa** - wejdź w przeglądarce na:
```
http://localhost:8000/storage/products/nazwa-obrazu.jpg
```

---

## Możliwe dodatkowe problemy

### Problem: "Permission denied" podczas tworzenia linku
**Rozwiązanie:**
```bash
sudo php artisan storage:link
```

### Problem: Link już istnieje ale jest zepsuty
**Rozwiązanie:**
```bash
rm public/storage
php artisan storage:link
```

### Problem: Obrazy są ale ścieżki w bazie są złe
Sprawdź w bazie danych tabelę `product_images`, kolumna `path` powinna zawierać:
- ✅ Poprawnie: `products/abc123.jpg`
- ❌ Źle: `storage/products/abc123.jpg`
- ❌ Źle: `/storage/app/public/products/abc123.jpg`

---

## W przypadku problemów na produkcji (serwer www)

Jeśli używasz shared hostingu lub serwera gdzie nie masz dostępu do SSH, możesz:

1. **Skopiować pliki fizycznie** z `storage/app/public/` do `public/storage/`
2. **Użyć .htaccess** do przekierowania (mniej eleganckie rozwiązanie)
3. **Zmienić ścieżki w kodzie** aby używać bezpośredniego dostępu do storage (niezalecane)

---

## Podsumowanie

Główny problem: **Brak linku symbolicznego**

Główne rozwiązanie: **`php artisan storage:link`**

To powinno rozwiązać problem! 🎉
