# 🚨 ROZWIĄZANIE PROBLEMU Z OBRAZAMI

## Problem
Obrazy produktów nie wyświetlają się z **DWÓCH powodów**:

1. ❌ **Brak linku symbolicznego** między `public/storage` a `storage/app/public`
2. ❌ **Baza danych nie działa** - błąd połączenia z MySQL

---

## 🎯 Szybkie rozwiązanie

### Wybierz swoją metodę pracy:

## OPCJA A: Docker (zalecane) 🐳

Jeśli masz Docker Desktop zainstalowany:

```bash
# Nadaj uprawnienia
chmod +x start-docker.sh

# Uruchom wszystko
./start-docker.sh
```

To uruchomi:
- ✅ Kontenery Docker (app, MySQL, phpMyAdmin)
- ✅ Migracje bazy danych
- ✅ Link symboliczny dla obrazów
- ✅ Poprawne uprawnienia

**Aplikacja:** http://localhost:8000
**phpMyAdmin:** http://localhost:8080 (login: root/root)

---

## OPCJA B: Lokalne środowisko 💻

Jeśli chcesz używać lokalnego PHP i MySQL:

### 1. Zainstaluj wymagania:
- PHP 8.2+
- MySQL 8.0+
- Composer

### 2. Zmień konfigurację:

```bash
# Skopiuj lokalną konfigurację
cp .env.local .env

# LUB edytuj ręcznie .env i zmień:
# DB_HOST=db         → DB_HOST=127.0.0.1
# DB_USERNAME=laravel → DB_USERNAME=root
# DB_PASSWORD=laravel → DB_PASSWORD=twoje_haslo
```

### 3. Utwórz bazę danych:

```bash
mysql -u root -p
```

```sql
CREATE DATABASE sklep_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

### 4. Uruchom setup:

```bash
chmod +x start-local.sh
./start-local.sh
```

### 5. Uruchom serwer:

```bash
php artisan serve
```

**Aplikacja:** http://localhost:8000

---

## 🔍 Weryfikacja

Po uruchomieniu sprawdź:

### 1. Czy link symboliczny działa:
```bash
ls -la public/ | grep storage
```
Powinno pokazać: `storage -> ../storage/app/public`

### 2. Czy baza działa:
```bash
php artisan tinker
```
```php
DB::table('products')->count();
```

### 3. Czy katalog products istnieje:
```bash
ls -la storage/app/public/
```

### 4. Sprawdź URL obrazu w przeglądarce:
Otwórz: `http://localhost:8000/storage/products/test.jpg`

---

## 📊 Struktura plików dla obrazów

```
storage/app/public/products/
├── abc123def.jpg          ← Tu są fizycznie zapisane obrazy
├── xyz789ghi.png
└── ...
         ↓
  (link symboliczny)
         ↓
public/storage/products/   ← Tu aplikacja ich szuka
├── abc123def.jpg
├── xyz789ghi.png
└── ...
         ↓
http://localhost:8000/storage/products/abc123def.jpg  ← URL w przeglądarce
```

---

## ❓ FAQ

### Q: Obrazy nadal się nie wyświetlają po wykonaniu wszystkich kroków
A: Sprawdź:
1. Czy w bazie są jakieś produkty z obrazami?
2. Otwórz Console w przeglądarce (F12) i sprawdź błędy
3. Sprawdź Network tab - jaki status HTTP zwraca obrazek? (404, 403, 500?)

### Q: Jak dodać testowe produkty z obrazami?
A: 
```bash
# W Dockerze
docker-compose exec app php artisan db:seed

# Lokalnie
php artisan db:seed
```

### Q: "Permission denied" przy tworzeniu linku
A:
```bash
# W Dockerze
docker-compose exec app chmod -R 775 storage

# Lokalnie
sudo php artisan storage:link
chmod -R 775 storage
```

### Q: Jak sprawdzić czy baza działa w Dockerze?
A:
```bash
docker-compose ps                    # Sprawdź status kontenerów
docker-compose logs db              # Zobacz logi MySQL
docker-compose exec db mysql -u laravel -p  # Połącz się bezpośrednio (hasło: laravel)
```

### Q: Port 3306 lub 8000 jest zajęty
A: Zmień porty w `docker-compose.yml` lub zatrzymaj inne usługi

---

## 🛠️ Przydatne komendy

### Docker:
```bash
docker-compose up -d              # Uruchom
docker-compose down              # Zatrzymaj
docker-compose restart           # Restart
docker-compose logs -f app       # Logi aplikacji
docker-compose exec app bash    # Wejdź do kontenera
```

### Lokalne:
```bash
php artisan serve               # Uruchom serwer
php artisan migrate:fresh       # Przebuduj bazę
php artisan storage:link        # Odtwórz link symboliczny
php artisan cache:clear         # Wyczyść cache
```

---

## 📝 Podsumowanie

**Główne problemy:**
1. ❌ Brak linku `public/storage` → `storage/app/public`
2. ❌ Baza danych nie działa (Docker nie uruchomiony LUB zła konfiguracja)

**Rozwiązanie:**
1. ✅ Użyj `start-docker.sh` (Docker) lub `start-local.sh` (lokalne)
2. ✅ Sprawdź czy wszystko działa
3. ✅ Dodaj produkty z obrazami przez panel admin lub seedery

---

**Potrzebujesz pomocy?** Sprawdź logi i uruchom `diagnoza-obrazy.sh` 🔍
