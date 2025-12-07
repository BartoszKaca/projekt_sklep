# 🚀 Instrukcja Migracji Sklepu na Serwer

Kompletny przewodnik krok po kroku do przeniesienia aplikacji Laravel na serwer produkcyjny.

---

## 📋 Spis Treści

1. [Przygotowanie lokalne](#przygotowanie-lokalne)
2. [Przygotowanie serwera](#przygotowanie-serwera)
3. [Deploy aplikacji](#deploy-aplikacji)
4. [Migracja bazy danych](#migracja-bazy-danych)
5. [Konfiguracja SSL](#konfiguracja-ssl)
6. [Weryfikacja](#weryfikacja)
7. [Rozwiązywanie problemów](#rozwiązywanie-problemów)

---

## 🔧 Przygotowanie lokalne

### 1. Sprawdź, czy wszystko działa lokalnie

```bash
# Uruchom aplikację lokalnie
docker-compose up -d

# Sprawdź czy działa
curl http://localhost
```

### 2. Przygotuj backup bazy danych

```bash
# Eksport lokalnej bazy
./migrate-database.sh export

# Backup zostanie zapisany w: database/backups/sklep_migration_YYYYMMDD_HHMMSS.sql.gz
```

### 3. Sprawdź plik .env

Upewnij się, że masz wszystkie potrzebne zmienne w `.env`:
- `APP_URL` - adres produkcyjny
- `DB_*` - dane do bazy danych
- `PAYU_*` - dane PayU (produkcyjne!)
- `MAIL_*` - konfiguracja email

### 4. Zbuduj assets produkcyjne

```bash
# Zbuduj assets (CSS, JS)
npm run build
```

### 5. Commit i push do repozytorium

```bash
git add .
git commit -m "Przygotowanie do deploy"
git push origin main
```

---

## 🖥️ Przygotowanie serwera

### Wymagania serwera

- **System**: Ubuntu 22.04 LTS lub nowszy
- **RAM**: Minimum 2GB (zalecane 4GB)
- **Dysk**: Minimum 20GB wolnego miejsca
- **Porty**: 22 (SSH), 80 (HTTP), 443 (HTTPS)

### 1. Połącz się z serwerem

```bash
ssh -i twoj-klucz.pem ubuntu@TWOJ_IP_SERWERA
```

### 2. Aktualizacja systemu

```bash
sudo apt update && sudo apt upgrade -y
```

### 3. Instalacja Docker

```bash
# Instalacja Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Dodaj użytkownika do grupy docker
sudo usermod -aG docker $USER

# Instalacja Docker Compose plugin
sudo apt install -y docker-compose-plugin

# Wyloguj i zaloguj ponownie
exit
```

Po ponownym zalogowaniu sprawdź:

```bash
docker --version
docker compose version
```

### 4. Przygotowanie katalogu aplikacji

```bash
# Utwórz katalog
sudo mkdir -p /var/www
sudo chown -R $USER:$USER /var/www
cd /var/www
```

---

## 📦 Deploy aplikacji

### 1. Sklonuj repozytorium

```bash
cd /var/www
git clone https://github.com/TWOJ_USER/sklep.git sklep
cd sklep
```

**Lub jeśli masz już repozytorium:**

```bash
cd /var/www/sklep
git pull origin main
```

### 2. Skonfiguruj środowisko

```bash
# Skopiuj przykładowy plik konfiguracji
cp env.production.example .env

# Edytuj konfigurację
nano .env
```

**Ważne zmienne do ustawienia:**

```env
APP_NAME="RapShop"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://twoja-domena.pl

# Baza danych
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=sklep_laravel
DB_USERNAME=laravel
DB_PASSWORD=twoje_silne_haslo
DB_ROOT_PASSWORD=twoje_silne_haslo_root

# Redis
REDIS_HOST=redis
REDIS_PORT=6379

# PayU (PRODUKCYJNE!)
PAYU_ENV=secure
PAYU_MERCHANT_POS_ID=twoj_pos_id
PAYU_SIGNATURE_KEY=twoj_klucz
PAYU_OAUTH_CLIENT_ID=twoj_client_id
PAYU_OAUTH_CLIENT_SECRET=twoj_secret

# Email
MAIL_MAILER=smtp
MAIL_HOST=twoj_smtp
MAIL_PORT=587
MAIL_USERNAME=twoj_login
MAIL_PASSWORD=twoje_haslo
MAIL_FROM_ADDRESS="sklep@twoja-domena.pl"
```

### 3. Nadaj uprawnienia skryptom

```bash
chmod +x deploy.sh
chmod +x migrate-database.sh
```

### 4. Uruchom pełny deploy

```bash
./deploy.sh deploy
```

Skrypt automatycznie:
- ✅ Zbuduje kontenery Docker
- ✅ Uruchomi MySQL, Redis, Nginx, PHP-FPM
- ✅ Zainstaluje zależności Composer
- ✅ Zbuduje assets (npm)
- ✅ Wykona migracje Laravel
- ✅ Zoptymalizuje cache
- ✅ Skonfiguruje storage

### 5. Wygeneruj klucz aplikacji

```bash
./deploy.sh artisan key:generate
```

### 6. Sprawdź status

```bash
./deploy.sh status
```

Powinieneś zobaczyć wszystkie kontenery uruchomione:
- `sklep_app` (PHP-FPM)
- `sklep_nginx` (Nginx)
- `sklep_db` (MySQL)
- `sklep_redis` (Redis)
- `sklep_queue` (Queue Worker)
- `sklep_phpmyadmin` (opcjonalnie)

---

## 💾 Migracja bazy danych

### Opcja A: Automatyczna migracja (zalecane)

**Na lokalnym komputerze:**

```bash
./migrate-database.sh full
```

Skrypt poprowadzi Cię przez:
1. Export lokalnej bazy
2. Transfer na serwer
3. Import na produkcji

### Opcja B: Ręczna migracja

**1. Na lokalnym komputerze - eksport:**

```bash
# Eksport z lokalnego Dockera
docker exec mysql_db mysqldump \
    -u root \
    -proot \
    --single-transaction \
    --routines \
    --triggers \
    sklep_laravel > backup.sql

# Kompresja
gzip backup.sql
```

**2. Transfer na serwer:**

```bash
scp -i twoj-klucz.pem backup.sql.gz ubuntu@TWOJ_IP:~/backup.sql.gz
```

**3. Na serwerze - import:**

```bash
cd /var/www/sklep

# Rozpakuj backup
gunzip ~/backup.sql.gz

# Import do MySQL
docker compose -f docker-compose.prod.yml exec -T db mysql \
    -u root \
    -p"${DB_ROOT_PASSWORD}" \
    sklep_laravel < ~/backup.sql

# Uruchom migracje Laravel (jeśli są nowe)
./deploy.sh artisan migrate --force

# Usuń plik backup
rm ~/backup.sql
```

### ⚠️ Ważne: Problem `lower_case_table_names`

Jeśli przenosisz dane z macOS na Linux, MySQL musi mieć to samo ustawienie.

**Nasza konfiguracja już to rozwiązuje** - MySQL w Docker używa `lower_case_table_names=2`.

Jeśli jednak wystąpi błąd:

```bash
# Usuń stary volume MySQL (UWAGA: kasuje dane!)
docker compose -f docker-compose.prod.yml down
docker volume rm sklep_sklep_mysql_data

# Uruchom ponownie
docker compose -f docker-compose.prod.yml up -d db
```

---

## 🔒 Konfiguracja SSL

### 1. Zainstaluj Certbot

```bash
sudo apt install -y certbot
```

### 2. Zatrzymaj Nginx

```bash
docker compose -f docker-compose.prod.yml stop nginx
```

### 3. Uzyskaj certyfikat

```bash
sudo certbot certonly --standalone \
    -d twoja-domena.pl \
    -d www.twoja-domena.pl
```

### 4. Przygotuj katalog na certyfikaty

```bash
mkdir -p docker/nginx/ssl
```

### 5. Skopiuj certyfikaty

```bash
sudo cp /etc/letsencrypt/live/twoja-domena.pl/fullchain.pem docker/nginx/ssl/
sudo cp /etc/letsencrypt/live/twoja-domena.pl/privkey.pem docker/nginx/ssl/
sudo chown -R $USER:$USER docker/nginx/ssl/
```

### 6. Włącz HTTPS w Nginx

Edytuj `docker/nginx/conf.d/default.conf`:

```bash
nano docker/nginx/conf.d/default.conf
```

**Odkomentuj sekcję HTTPS** (linie 79-101) i zmień:
- `server_name twoja-domena.pl www.twoja-domena.pl;`
- Upewnij się, że ścieżki do certyfikatów są poprawne

**Odkomentuj redirect HTTP → HTTPS** (linia 7):
```nginx
return 301 https://$host$request_uri;
```

### 7. Uruchom Nginx

```bash
docker compose -f docker-compose.prod.yml up -d nginx
```

### 8. Auto-renewal certyfikatu

```bash
# Edytuj crontab
sudo crontab -e
```

Dodaj linię:

```cron
0 3 * * * certbot renew --quiet --pre-hook "docker compose -f /var/www/sklep/docker-compose.prod.yml stop nginx" --post-hook "docker compose -f /var/www/sklep/docker-compose.prod.yml start nginx && cp /etc/letsencrypt/live/twoja-domena.pl/fullchain.pem /var/www/sklep/docker/nginx/ssl/ && cp /etc/letsencrypt/live/twoja-domena.pl/privkey.pem /var/www/sklep/docker/nginx/ssl/"
```

---

## ✅ Weryfikacja

### 1. Sprawdź czy aplikacja działa

```bash
# Sprawdź status kontenerów
./deploy.sh status

# Sprawdź logi
./deploy.sh logs

# Sprawdź konkretny serwis
./deploy.sh logs nginx
./deploy.sh logs app
```

### 2. Test w przeglądarce

- Otwórz: `http://twoja-domena.pl` (powinno przekierować na HTTPS)
- Sprawdź czy strona się ładuje
- Sprawdź czy logowanie działa
- Sprawdź czy płatności działają (testowe)

### 3. Sprawdź bazy danych

```bash
# Połącz się z MySQL
./deploy.sh mysql

# W konsoli MySQL:
SHOW DATABASES;
USE sklep_laravel;
SHOW TABLES;
SELECT COUNT(*) FROM users;
```

### 4. Sprawdź queue

```bash
# Sprawdź logi queue
./deploy.sh logs queue

# Sprawdź czy worker działa
./deploy.sh artisan queue:work --once
```

### 5. Sprawdź cache

```bash
# Wyczyść cache
./deploy.sh artisan cache:clear

# Sprawdź Redis
docker compose -f docker-compose.prod.yml exec redis redis-cli ping
# Powinno zwrócić: PONG
```

---

## 🔧 Komendy pomocnicze

### Podstawowe operacje

```bash
cd /var/www/sklep

# Status kontenerów
./deploy.sh status

# Logi wszystkich serwisów
./deploy.sh logs

# Logi konkretnego serwisu
./deploy.sh logs nginx
./deploy.sh logs app
./deploy.sh logs db
./deploy.sh logs queue

# Restart po zmianach
./deploy.sh update

# Pełny restart
./deploy.sh rebuild
```

### Artisan commands

```bash
# Migracje
./deploy.sh artisan migrate
./deploy.sh artisan migrate:status

# Cache
./deploy.sh artisan config:cache
./deploy.sh artisan route:cache
./deploy.sh artisan view:cache
./deploy.sh artisan cache:clear

# Queue
./deploy.sh artisan queue:restart
./deploy.sh artisan queue:work --once

# Inne
./deploy.sh artisan tinker
./deploy.sh artisan db:seed
```

### Baza danych

```bash
# Konsola MySQL
./deploy.sh mysql

# Backup bazy
./deploy.sh backup

# Backup ręczny
docker compose -f docker-compose.prod.yml exec -T db mysqldump \
    -u root -p"${DB_ROOT_PASSWORD}" \
    sklep_laravel > backup_$(date +%Y%m%d).sql
```

### Shell i debugowanie

```bash
# Shell w kontenerze PHP
./deploy.sh shell

# W kontenerze możesz uruchomić:
php artisan tinker
php artisan migrate
composer install
```

---

## 🐛 Rozwiązywanie problemów

### Błąd 502 Bad Gateway

```bash
# Sprawdź logi PHP-FPM
./deploy.sh logs app

# Sprawdź czy PHP działa
docker compose -f docker-compose.prod.yml exec app php -v

# Restart
docker compose -f docker-compose.prod.yml restart app nginx
```

### Błąd: Permission denied na storage

```bash
docker compose -f docker-compose.prod.yml exec app chmod -R 775 storage bootstrap/cache
docker compose -f docker-compose.prod.yml exec app chown -R www:www storage bootstrap/cache
```

### Queue nie przetwarza zadań

```bash
# Sprawdź logi
./deploy.sh logs queue

# Restart workera
docker compose -f docker-compose.prod.yml restart queue

# Sprawdź status
./deploy.sh artisan queue:work --once
```

### MySQL nie startuje

```bash
# Sprawdź logi
./deploy.sh logs db

# Sprawdź konfigurację
cat docker/mysql/conf.d/custom.cnf

# Zresetuj MySQL (UWAGA: kasuje dane!)
docker compose -f docker-compose.prod.yml down
docker volume rm sklep_sklep_mysql_data
docker compose -f docker-compose.prod.yml up -d db
```

### Błąd: `lower_case_table_names` mismatch

```
[ERROR] Different lower_case_table_names settings for server ('0') and data dictionary ('2')
```

**Rozwiązanie:**

```bash
# Usuń stary volume MySQL (UWAGA: kasuje dane!)
docker compose -f docker-compose.prod.yml down
docker volume rm sklep_sklep_mysql_data

# Uruchom ponownie - MySQL zainicjuje się z poprawnym ustawieniem
docker compose -f docker-compose.prod.yml up -d
```

### Aplikacja nie ładuje się / biały ekran

```bash
# Sprawdź logi Laravel
./deploy.sh logs app | grep -i error

# Sprawdź uprawnienia
docker compose -f docker-compose.prod.yml exec app ls -la storage

# Wyczyść cache
./deploy.sh artisan config:clear
./deploy.sh artisan cache:clear
./deploy.sh artisan view:clear

# Sprawdź .env
docker compose -f docker-compose.prod.yml exec app cat .env | grep APP_KEY
# Jeśli puste, wygeneruj:
./deploy.sh artisan key:generate
```

### Nginx zwraca 404

```bash
# Sprawdź konfigurację Nginx
cat docker/nginx/conf.d/default.conf

# Sprawdź czy root wskazuje na public
# Powinno być: root /var/www/html/public;

# Sprawdź logi
./deploy.sh logs nginx
```

### Composer install nie działa

```bash
# Wejdź do kontenera
./deploy.sh shell

# Uruchom ręcznie
composer install --optimize-autoloader --no-dev
```

### Assets nie ładują się

```bash
# Zbuduj assets w kontenerze
docker compose -f docker-compose.prod.yml exec app npm run build

# Sprawdź czy pliki istnieją
docker compose -f docker-compose.prod.yml exec app ls -la public/build
```

---

## 📊 Monitoring i utrzymanie

### Regularne backupy

```bash
# Dodaj do crontab (codziennie o 2:00)
0 2 * * * cd /var/www/sklep && ./deploy.sh backup
```

### Aktualizacja aplikacji

```bash
cd /var/www/sklep

# Pobierz najnowsze zmiany
git pull origin main

# Szybka aktualizacja (bez przebudowy)
./deploy.sh update

# Lub pełna przebudowa
./deploy.sh rebuild
```

### Sprawdzanie logów

```bash
# Logi aplikacji
./deploy.sh logs app | tail -100

# Logi Nginx
./deploy.sh logs nginx | tail -100

# Logi Laravel (w kontenerze)
docker compose -f docker-compose.prod.yml exec app tail -f storage/logs/laravel.log
```

### Sprawdzanie zasobów

```bash
# Użycie dysku
df -h

# Użycie pamięci
free -h

# Użycie przez kontenery
docker stats
```

---

## 🔐 Bezpieczeństwo

### 1. Firewall (UFW)

```bash
# Zainstaluj UFW
sudo apt install -y ufw

# Zezwól na SSH, HTTP, HTTPS
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Włącz firewall
sudo ufw enable

# Sprawdź status
sudo ufw status
```

### 2. Zmień domyślne hasła

- Zmień `DB_PASSWORD` i `DB_ROOT_PASSWORD` w `.env`
- Użyj silnych haseł (minimum 16 znaków)

### 3. Ukryj PhpMyAdmin

PhpMyAdmin jest dostępne na porcie 8080. Rozważ:
- Usunięcie z `docker-compose.prod.yml` po deploy
- Lub ograniczenie dostępu przez firewall

### 4. Regularne aktualizacje

```bash
# Aktualizuj system
sudo apt update && sudo apt upgrade -y

# Aktualizuj Docker images
docker compose -f docker-compose.prod.yml pull
docker compose -f docker-compose.prod.yml up -d
```

---

## 📞 Wsparcie

W razie problemów:

1. Sprawdź logi: `./deploy.sh logs`
2. Sprawdź status: `./deploy.sh status`
3. Sprawdź dokumentację Laravel: https://laravel.com/docs
4. Sprawdź dokumentację Docker: https://docs.docker.com

---

## ✅ Checklist przed go-live

- [ ] Aplikacja działa na HTTP
- [ ] SSL skonfigurowany i działa
- [ ] Baza danych zmigrowana
- [ ] PayU skonfigurowany (produkcyjne dane!)
- [ ] Email działa (test wysłany)
- [ ] Queue worker działa
- [ ] Backup automatyczny skonfigurowany
- [ ] Firewall skonfigurowany
- [ ] Monitoring skonfigurowany (opcjonalnie)
- [ ] Dokumentacja zaktualizowana

---

**Powodzenia z deployem! 🚀**
