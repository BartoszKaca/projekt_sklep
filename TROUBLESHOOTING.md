# 🔧 Rozwiązywanie problemów z deploymentem

## Lokalnie kontenery działają, ale aplikacja nie działa

### 1. Sprawdź status kontenerów lokalnie

```bash
docker-compose ps
```

Jeśli kontenery są "Up", ale aplikacja nie działa:

```bash
# Sprawdź logi aplikacji
docker-compose logs app

# Sprawdź logi bazy danych
docker-compose logs db

# Sprawdź czy aplikacja odpowiada
curl http://localhost:8000
```

### 2. Sprawdź błędy PHP

```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

### 3. Sprawdź błędy składni

```bash
docker-compose exec app php -l app/Http/Controllers/Admin/ProductController.php
docker-compose exec app php artisan route:list
```

---

## Na serwerze aplikacja nie działa po deploy

### 1. Połącz się z serwerem i sprawdź status

```bash
ssh -i klucz.pem ubuntu@TWOJ_IP
cd /var/www/sklep
./deploy.sh status
```

### 2. Sprawdź logi wszystkich kontenerów

```bash
./deploy.sh logs
```

### 3. Sprawdź logi konkretnego serwisu

```bash
# Logi aplikacji (PHP)
./deploy.sh logs app

# Logi Nginx
./deploy.sh logs nginx

# Logi bazy danych
./deploy.sh logs db
```

### 4. Najczęstsze problemy i rozwiązania

#### Błąd 500 Internal Server Error

```bash
# Sprawdź logi Laravel
./deploy.sh logs app | grep -i error

# Sprawdź uprawnienia storage
docker compose -f docker-compose.prod.yml exec app ls -la storage
docker compose -f docker-compose.prod.yml exec app chmod -R 775 storage bootstrap/cache

# Wyczyść cache
./deploy.sh artisan config:clear
./deploy.sh artisan cache:clear
./deploy.sh artisan route:clear
./deploy.sh artisan view:clear
```

#### Błąd 502 Bad Gateway

```bash
# Sprawdź czy PHP-FPM działa
./deploy.sh logs app

# Restart PHP i Nginx
docker compose -f docker-compose.prod.yml restart app nginx

# Sprawdź czy porty są otwarte
docker compose -f docker-compose.prod.yml ps
```

#### Błąd połączenia z bazą danych

```bash
# Sprawdź czy MySQL działa
./deploy.sh logs db

# Sprawdź połączenie
./deploy.sh mysql
# W konsoli MySQL:
SHOW DATABASES;
```

#### Aplikacja pokazuje biały ekran

```bash
# Sprawdź logi Laravel
docker compose -f docker-compose.prod.yml exec app tail -f storage/logs/laravel.log

# Sprawdź .env
docker compose -f docker-compose.prod.yml exec app cat .env | grep APP_KEY
# Jeśli puste:
./deploy.sh artisan key:generate

# Sprawdź uprawnienia
docker compose -f docker-compose.prod.yml exec app ls -la storage
docker compose -f docker-compose.prod.yml exec app chmod -R 775 storage bootstrap/cache
docker compose -f docker-compose.prod.yml exec app chown -R www:www storage bootstrap/cache
```

#### Błędy związane z ostatnimi zmianami (StockMovement)

```bash
# Sprawdź czy tabela stock_movements istnieje
./deploy.sh mysql
# W konsoli MySQL:
SHOW TABLES LIKE 'stock_movements';
DESCRIBE stock_movements;

# Jeśli brakuje, uruchom migracje
./deploy.sh artisan migrate

# Sprawdź czy model StockMovement istnieje
docker compose -f docker-compose.prod.yml exec app ls -la app/Models/StockMovement.php
```

### 5. Pełny restart (jeśli nic nie pomaga)

```bash
cd /var/www/sklep

# Zatrzymaj wszystko
docker compose -f docker-compose.prod.yml down

# Wyczyść cache i optymalizuj
./deploy.sh artisan config:clear
./deploy.sh artisan cache:clear
./deploy.sh artisan route:clear
./deploy.sh artisan view:clear

# Uruchom ponownie
./deploy.sh start

# Sprawdź status
./deploy.sh status

# Optymalizuj Laravel
./deploy.sh artisan config:cache
./deploy.sh artisan route:cache
./deploy.sh artisan view:cache
```

### 6. Sprawdź błędy składniowe w kodzie

```bash
# Sprawdź czy są błędy składni w PHP
docker compose -f docker-compose.prod.yml exec app php -l app/Http/Controllers/Admin/ProductController.php

# Sprawdź czy routy działają
./deploy.sh artisan route:list | grep admin.products

# Sprawdź autoloader
docker compose -f docker-compose.prod.yml exec app composer dump-autoload
```

### 7. Jeśli problem jest z konkretnym kontrolerem

```bash
# Sprawdź importy
docker compose -f docker-compose.prod.yml exec app grep "use App" app/Http/Controllers/Admin/ProductController.php

# Sprawdź czy model StockMovement istnieje
docker compose -f docker-compose.prod.yml exec app ls -la app/Models/StockMovement.php

# Sprawdź czy są błędy w widokach
./deploy.sh artisan view:clear
```

---

## Checklist diagnostyczny

- [ ] Kontenery działają: `./deploy.sh status`
- [ ] Logi bez błędów: `./deploy.sh logs app`
- [ ] Baza danych działa: `./deploy.sh logs db`
- [ ] Nginx działa: `./deploy.sh logs nginx`
- [ ] Storage ma poprawne uprawnienia
- [ ] Cache wyczyszczony
- [ ] .env skonfigurowany poprawnie
- [ ] APP_KEY wygenerowany
- [ ] Migracje wykonane
- [ ] Composer dependencies zainstalowane

---

## Kontakt z pomocą

Jeśli problem nadal występuje, zbierz informacje:

```bash
# Status wszystkich kontenerów
./deploy.sh status > status.txt

# Ostatnie 100 linii logów
./deploy.sh logs app > logs_app.txt
./deploy.sh logs nginx > logs_nginx.txt
./deploy.sh logs db > logs_db.txt

# Informacje o systemie
docker --version > docker_version.txt
docker compose version > docker_compose_version.txt
```

Wyślij te pliki razem z opisem problemu.
