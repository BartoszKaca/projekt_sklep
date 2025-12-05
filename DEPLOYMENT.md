# 🚀 Deployment Guide - Sklep Laravel na AWS EC2

## Spis Treści
1. [Wymagania](#wymagania)
2. [Przygotowanie EC2](#przygotowanie-ec2)
3. [Instalacja Docker](#instalacja-docker)
4. [Deploy aplikacji](#deploy-aplikacji)
5. [Migracja bazy danych](#migracja-bazy-danych)
6. [Konfiguracja SSL](#konfiguracja-ssl)
7. [Rozwiązywanie problemów](#rozwiązywanie-problemów)

---

## Wymagania

### Serwer AWS EC2
- **Typ instancji**: t3.small (2 vCPU, 2GB RAM)
- **System**: Ubuntu 24.04 LTS
- **Storage**: 30GB gp3 SSD
- **Security Groups**:
  - Port 22 (SSH) - Twoje IP
  - Port 80 (HTTP) - 0.0.0.0/0
  - Port 443 (HTTPS) - 0.0.0.0/0

### Domena
- Rekord A wskazujący na Elastic IP instancji EC2

---

## Przygotowanie EC2

### 1. Połącz się z serwerem

```bash
ssh -i twoj-klucz.pem ubuntu@TWOJ_ELASTIC_IP
```

### 2. Aktualizacja systemu

```bash
sudo apt update && sudo apt upgrade -y
```

---

## Instalacja Docker

```bash
# Instalacja Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Dodaj użytkownika do grupy docker
sudo usermod -aG docker $USER

# Instalacja Docker Compose plugin
sudo apt install -y docker-compose-plugin

# Wyloguj i zaloguj ponownie, aby zastosować zmiany grupy
exit
```

Po ponownym zalogowaniu:

```bash
# Sprawdź instalację
docker --version
docker compose version
```

---

## Deploy aplikacji

### 1. Sklonuj repozytorium

```bash
sudo mkdir -p /var/www
sudo chown -R $USER:$USER /var/www
cd /var/www

git clone https://github.com/TWOJ_USER/sklep.git sklep
cd sklep
```

### 2. Skonfiguruj środowisko

```bash
# Skopiuj przykładowy plik konfiguracji
cp env.production.example .env

# Edytuj konfigurację
nano .env
```

**Ważne zmienne do ustawienia:**
- `APP_URL` - Twoja domena (https://twoja-domena.pl)
- `DB_PASSWORD` - Silne hasło do bazy
- `DB_ROOT_PASSWORD` - Silne hasło root MySQL
- `PAYU_*` - Dane PayU produkcyjne
- `MAIL_*` - Konfiguracja SMTP

### 3. Uruchom deploy

```bash
# Nadaj uprawnienia skryptowi
chmod +x deploy.sh

# Pełny deploy
./deploy.sh deploy
```

Skrypt automatycznie:
- Zbuduje kontenery Docker
- Uruchomi MySQL, Redis, Nginx, PHP-FPM
- Wykona migracje Laravel
- Zoptymalizuje cache

### 4. Wygeneruj klucz aplikacji

```bash
./deploy.sh artisan key:generate
```

---

## Migracja bazy danych

### ⚠️ Ważne: Problem `lower_case_table_names`

Jeśli przenosisz dane z macOS na Linux, musisz upewnić się, że MySQL używa tego samego ustawienia `lower_case_table_names`.

**Nasza konfiguracja już to rozwiązuje** poprzez:
- `docker/mysql/conf.d/custom.cnf` - ustawia `lower_case_table_names=2`
- `docker-compose.prod.yml` - przekazuje parametr do MySQL

### Migracja z lokalnej bazy

**Na lokalnym komputerze:**

```bash
# Export lokalnej bazy
docker exec mysql_db mysqldump -u root -proot sklep_laravel > backup.sql
gzip backup.sql

# Transfer na serwer
scp -i klucz.pem backup.sql.gz ubuntu@TWOJ_IP:~/
```

**Na serwerze produkcyjnym:**

```bash
cd /var/www/sklep

# Rozpakuj backup
gunzip ~/backup.sql.gz

# Import do MySQL w Docker
docker compose -f docker-compose.prod.yml exec -T db mysql -u root -pTWOJE_ROOT_HASLO sklep_laravel < ~/backup.sql

# Uruchom dodatkowe migracje jeśli są
./deploy.sh artisan migrate --force

# Usuń plik backup
rm ~/backup.sql
```

---

## Konfiguracja SSL

### Opcja A: Certbot (Let's Encrypt) - Rekomendowane

```bash
# Zainstaluj certbot
sudo apt install -y certbot

# Zatrzymaj nginx tymczasowo
docker compose -f docker-compose.prod.yml stop nginx

# Uzyskaj certyfikat
sudo certbot certonly --standalone -d twoja-domena.pl -d www.twoja-domena.pl

# Skopiuj certyfikaty do Docker
sudo cp /etc/letsencrypt/live/twoja-domena.pl/fullchain.pem docker/nginx/ssl/
sudo cp /etc/letsencrypt/live/twoja-domena.pl/privkey.pem docker/nginx/ssl/
sudo chown $USER:$USER docker/nginx/ssl/*

# Odkomentuj sekcję HTTPS w docker/nginx/conf.d/default.conf
nano docker/nginx/conf.d/default.conf

# Uruchom nginx
docker compose -f docker-compose.prod.yml up -d nginx
```

### Auto-renewal certyfikatu

```bash
# Dodaj do crontab
sudo crontab -e
```

Dodaj linię:
```cron
0 3 * * * certbot renew --pre-hook "docker compose -f /var/www/sklep/docker-compose.prod.yml stop nginx" --post-hook "docker compose -f /var/www/sklep/docker-compose.prod.yml start nginx"
```

---

## Komendy pomocnicze

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

# Konsola MySQL
./deploy.sh mysql

# Artisan
./deploy.sh artisan migrate:status
./deploy.sh artisan cache:clear
./deploy.sh artisan queue:restart

# Backup bazy
./deploy.sh backup

# Shell w kontenerze PHP
./deploy.sh shell

# Restart po zmianach
./deploy.sh update
```

---

## Rozwiązywanie problemów

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

### Błąd 502 Bad Gateway

```bash
# Sprawdź logi PHP-FPM
./deploy.sh logs app

# Sprawdź czy PHP działa
docker compose -f docker-compose.prod.yml exec app php -v

# Restart
docker compose -f docker-compose.prod.yml restart app nginx
```

### Błąd permission denied na storage

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

---

## Struktura plików Docker

```
sklep/
├── docker-compose.prod.yml    # Główna konfiguracja produkcyjna
├── Dockerfile.prod            # Obraz PHP dla produkcji
├── deploy.sh                  # Skrypt deploy
├── migrate-database.sh        # Skrypt migracji bazy
├── env.production.example     # Przykładowa konfiguracja .env
└── docker/
    ├── nginx/
    │   ├── conf.d/
    │   │   └── default.conf   # Konfiguracja Nginx
    │   ├── ssl/               # Certyfikaty SSL
    │   └── logs/              # Logi Nginx
    ├── mysql/
    │   ├── conf.d/
    │   │   └── custom.cnf     # Konfiguracja MySQL (lower_case_table_names=2)
    │   └── init/
    │       └── 01-init.sql    # Skrypt inicjalizacyjny
    └── supervisor/
        └── supervisord.conf   # Konfiguracja supervisor
```

---

## Szacunkowe koszty AWS (miesięcznie)

| Usługa | Koszt USD |
|--------|-----------|
| EC2 t3.small | ~$15-18 |
| EBS 30GB gp3 | ~$2.5 |
| Elastic IP | $0 (przy użyciu) |
| Data Transfer | ~$1-5 |
| **Razem** | **~$20-25** |

---

## Kontakt & Wsparcie

W razie problemów sprawdź:
1. Logi: `./deploy.sh logs`
2. Status: `./deploy.sh status`
3. Dokumentację Laravel: https://laravel.com/docs

