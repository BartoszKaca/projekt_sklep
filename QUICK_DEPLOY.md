# ⚡ Szybki Deploy - Instrukcja

Krótki przewodnik do szybkiego deploymentu na serwer.

---

## 🖥️ NA SERWERZE (pierwszy raz)

### 1. Przygotuj serwer

```bash
# Połącz się z serwerem
ssh -i twoj-klucz.pem ubuntu@TWOJ_IP

# Zainstaluj Docker (jeśli nie masz)
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER
sudo apt install -y docker-compose-plugin

# Wyloguj i zaloguj ponownie
exit
ssh -i twoj-klucz.pem ubuntu@TWOJ_IP
```

### 2. Sklonuj projekt

```bash
sudo mkdir -p /var/www
sudo chown -R $USER:$USER /var/www
cd /var/www
git clone https://github.com/TWOJ_USER/sklep.git sklep
cd sklep
```

### 3. Skonfiguruj .env

```bash
cp env.production.example .env
nano .env
```

**Ustaw w .env:**
```env
APP_URL=https://twoja-domena.pl
DB_PASSWORD=twoje_silne_haslo
DB_ROOT_PASSWORD=twoje_silne_haslo_root
PAYU_ENV=secure  # produkcyjne!
PAYU_MERCHANT_POS_ID=twoj_pos_id
PAYU_SIGNATURE_KEY=twoj_klucz
# ... reszta zmiennych
```

### 4. Deploy

```bash
chmod +x deploy.sh
./deploy.sh deploy
./deploy.sh artisan key:generate
```

### 5. Migracja bazy (jeśli masz dane lokalne)

**Na lokalnym komputerze:**
```bash
./migrate-database.sh export
scp -i klucz.pem database/backups/sklep_migration_*.sql.gz ubuntu@TWOJ_IP:~/
```

**Na serwerze:**
```bash
cd /var/www/sklep
gunzip ~/sklep_migration_*.sql.gz
docker compose -f docker-compose.prod.yml exec -T db mysql \
    -u root -p"${DB_ROOT_PASSWORD}" \
    sklep_laravel < ~/sklep_migration_*.sql
./deploy.sh artisan migrate --force
```

### 6. SSL (Certbot)

```bash
sudo apt install -y certbot
docker compose -f docker-compose.prod.yml stop nginx

sudo certbot certonly --standalone \
    -d twoja-domena.pl \
    -d www.twoja-domena.pl

mkdir -p docker/nginx/ssl
sudo cp /etc/letsencrypt/live/twoja-domena.pl/fullchain.pem docker/nginx/ssl/
sudo cp /etc/letsencrypt/live/twoja-domena.pl/privkey.pem docker/nginx/ssl/
sudo chown -R $USER:$USER docker/nginx/ssl/

# Edytuj docker/nginx/conf.d/default.conf
# - Odkomentuj sekcję HTTPS (linie 79-101)
# - Odkomentuj redirect HTTP→HTTPS (linia 7)

nano docker/nginx/conf.d/default.conf

docker compose -f docker-compose.prod.yml up -d nginx
```

---

## 🔄 AKTUALIZACJA (po zmianach w kodzie)

### Na lokalnym komputerze:

```bash
# Zbuduj assets
npm run build

# Commit i push
git add .
git commit -m "Aktualizacja"
git push origin main
```

### Na serwerze:

```bash
cd /var/www/sklep

# Pobierz zmiany
git pull origin main

# Szybka aktualizacja (bez przebudowy)
./deploy.sh update

# LUB pełna przebudowa (jeśli były zmiany w Dockerfile)
./deploy.sh rebuild
```

---

## 📋 Przydatne komendy

```bash
# Status kontenerów
./deploy.sh status

# Logi
./deploy.sh logs
./deploy.sh logs nginx
./deploy.sh logs app

# Artisan
./deploy.sh artisan migrate
./deploy.sh artisan cache:clear

# Backup bazy
./deploy.sh backup

# Shell w kontenerze
./deploy.sh shell

# Restart
./deploy.sh restart
```

---

## 🐛 Problemy?

### Błąd 502
```bash
./deploy.sh logs app
docker compose -f docker-compose.prod.yml restart app nginx
```

### Permission denied
```bash
docker compose -f docker-compose.prod.yml exec app chmod -R 775 storage bootstrap/cache
```

### MySQL nie startuje
```bash
./deploy.sh logs db
docker compose -f docker-compose.prod.yml restart db
```

---

## ✅ Checklist

- [ ] Docker zainstalowany
- [ ] Projekt sklonowany
- [ ] .env skonfigurowany
- [ ] `./deploy.sh deploy` wykonany
- [ ] Klucz aplikacji wygenerowany
- [ ] Baza danych zmigrowana
- [ ] SSL skonfigurowany
- [ ] Aplikacja działa na HTTPS

---

**Więcej szczegółów w `MIGRACJA.md`**
