# Wdrażanie Projektu na AWS EC2

## 🎯 Przegląd

Dokument zawiera szczegółową instrukcję przygotowania i wdrażania aplikacji Laravel Rap Shop na instancji AWS EC2 z obsługą:
- **Domain**: Własna domena (np. rapshop.pl)
- **PHPMyAdmin**: Dostęp do bazy danych
- **Mailtrap SMTP**: Profesjonalny serwis mailingu (konto produkcyjne)
- **SSL/TLS**: Szyfrowanie komunikacji
- **Nginx**: Reverse proxy i web server

---

## 📋 Lista Kroków Wdrażania

### FAZA 1: Przygotowanie Infrastruktury AWS

#### Krok 1.1: Uruchomienie Instancji EC2
- [ ] Zaloguj się do AWS Console
- [ ] Przejdź do EC2 Dashboard
- [ ] Kliknij "Launch Instances"
- [ ] **Wybór AMI**: Amazon Linux 2 (albo Ubuntu 22.04 LTS) - Free Tier eligible
- [ ] **Typ instancji**: t2.micro (Free Tier) lub t3.small dla lepszej wydajności
- [ ] **Security Group**: Otwórz porty:
  - `22` (SSH) - dla zarządzania
  - `80` (HTTP) - dla sieci
  - `443` (HTTPS) - dla sieci szyfrowanej
  - `3306` (MySQL) - TYLKO dla prywatnego dostępu (opcjonalnie)
  - `8080` (PHPMyAdmin) - NIE publicznie! Tylko IP whitelist
- [ ] **Storage**: Minimum 30GB
- [ ] Utwórz/wybierz Key Pair (zapisz plik .pem w bezpiecznym miejscu)

#### Krok 1.2: Elastyczne IP (EIP)
- [ ] Przydziel Elastic IP do instancji
- [ ] Skonfiguruj DNS (Route 53 lub zewnętrzny registrar):
  ```
  A record: rapshop.pl -> [YOUR_ELASTIC_IP]
  www CNAME: www.rapshop.pl -> rapshop.pl
  ```

#### Krok 1.3: RDS Database (opcjonalnie)
- [ ] **Alternatywa**: Użyj AWS RDS MySQL zamiast lokalnego kontenera
- [ ] **Zaleta**: Automatyczne backupy, skalowanie, wysokiej dostępności
- [ ] Jeśli używasz - skip punkt 2.3 (MySQL w Docker)

---

### FAZA 2: Konfiguracja Serwera EC2

#### Krok 2.1: Połączenie SSH
```bash
chmod 600 your-key.pem
ssh -i your-key.pem ec2-user@[ELASTIC_IP]
# lub dla Ubuntu:
ssh -i your-key.pem ubuntu@[ELASTIC_IP]
```

#### Krok 2.2: Aktualizacja Systemu
```bash
sudo yum update -y  # Amazon Linux 2
# lub
sudo apt update && sudo apt upgrade -y  # Ubuntu
```

#### Krok 2.3: Instalacja Docker i Docker Compose
```bash
# Amazon Linux 2
sudo amazon-linux-extras install docker -y
sudo systemctl start docker
sudo usermod -aG docker $USER

# Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Ubuntu
sudo apt install -y docker.io docker-compose
sudo systemctl start docker
sudo usermod -aG docker $USER
```

#### Krok 2.4: Instalacja Git, Certbot (Let's Encrypt)
```bash
sudo yum install -y git certbot  # Amazon Linux 2
# lub
sudo apt install -y git certbot python3-certbot-nginx  # Ubuntu
```

#### Krok 2.5: Clone Projektu
```bash
cd /home/ec2-user  # lub /home/ubuntu
git clone https://github.com/BartoszKaca/projekt_sklep.git
cd projekt_sklep
newgrp docker  # Aktywuj nową sesję z grupą docker
```

---

### FAZA 3: Konfiguracja Aplikacji

#### Krok 3.1: Przygotowanie Plików Środowiska
```bash
cp .env.production .env
# Edytuj .env z danymi produkcyjnymi (patrz sekcja Konfiguracja .env)
nano .env
```

#### Krok 3.2: Konfiguracja Mail Mailtrap
- [ ] Zaloguj się na https://mailtrap.io
- [ ] Przejdź do **Integrations** > **SMTP Settings**
- [ ] Skopiuj dane dostępu:
  - Host: smtp.mailtrap.io
  - Port: 2525 (lub 465/587)
  - Username: [Twój username z Mailtrap]
  - Password: [Twój token z Mailtrap]
- [ ] Wstaw do pliku `.env`

#### Krok 3.3: Generowanie Certyfikatu SSL
```bash
# Pobierz domeny
sudo certbot certonly --standalone -d rapshop.pl -d www.rapshop.pl

# Certyfikat zostanie zapisany w: /etc/letsencrypt/live/rapshop.pl/
# fullchain.pem i privkey.pem
```

---

### FAZA 4: Uruchomienie Docker Containers

#### Krok 4.1: Build i Start Kontenerów
```bash
docker-compose -f docker-compose.prod.yml build
docker-compose -f docker-compose.prod.yml up -d
```

#### Krok 4.2: Inicjalizacja Bazy Danych
```bash
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --seed
docker-compose -f docker-compose.prod.yml exec app php artisan storage:link
```

---

### FAZA 5: Konfiguracja Nginx (Reverse Proxy)

#### Krok 5.1: Instalacja i Start Nginx
```bash
sudo yum install -y nginx  # Amazon Linux 2
# lub
sudo apt install -y nginx  # Ubuntu

sudo systemctl start nginx
sudo systemctl enable nginx
```

#### Krok 5.2: Konfiguracja Nginx VirtualHost
```bash
sudo nano /etc/nginx/conf.d/rapshop.conf
# Paste zawartość z: nginx.conf (poniżej)

sudo nginx -t  # Test konfiguracji
sudo systemctl reload nginx
```

---

### FAZA 6: Konfiguracja PHPMyAdmin

#### Krok 6.1: Security - Access Control
- [ ] PHPMyAdmin będzie dostępny TYLKO:
  - Na prywatnym IP instancji
  - Z IP whitelisty (np. Twoje IP)
  - Z dodatkową autoryzacją (basic auth)

#### Krok 6.2: Nginx Proxy do PHPMyAdmin
```nginx
# PHPMyAdmin dostępny pod: https://rapshop.pl:8080 
# lub subdomeny: https://admin.rapshop.pl/
# (patrz nginx.conf)
```

---

### FAZA 7: SSL/TLS i Certbot Auto-Renewal

#### Krok 7.1: Auto-Renewal
```bash
sudo systemctl enable certbot-renewal.timer
sudo systemctl start certbot-renewal.timer

# Test dry-run
sudo certbot renew --dry-run
```

---

### FAZA 8: Backup i Monitoring

#### Krok 8.1: Backup Bazy Danych
```bash
# Daily backup script
0 2 * * * docker-compose -f /path/docker-compose.prod.yml exec -T db mysqldump -u root -proot sklep_laravel > /backups/db_$(date +\%Y\%m\%d).sql
```

#### Krok 8.2: Monitorowanie Logów
```bash
docker-compose -f docker-compose.prod.yml logs -f app
docker-compose -f docker-compose.prod.yml logs -f db
```

---

## 🔧 Zmodyfikowane Pliki Konfiguracji

### 1. `.env.production` (NOWY)
[Patrz plik: `.env.production`]

### 2. `docker-compose.prod.yml` (NOWY)
[Patrz plik: `docker-compose.prod.yml`]

### 3. `Dockerfile.prod` (NOWY)
[Patrz plik: `Dockerfile.prod`]

### 4. `nginx.conf` (NOWY)
[Patrz plik: `nginx.conf`]

### 5. `.dockerignore` (NOWY)
[Patrz plik: `.dockerignore`]

---

## 🔐 Zmienne Środowiskowe (Production)

```env
# APP
APP_ENV=production
APP_DEBUG=false
APP_URL=https://rapshop.pl

# DATABASE (AWS RDS lub lokalny MySQL)
DB_HOST=[RDS_ENDPOINT_lub_localhost]
DB_DATABASE=sklep_laravel
DB_USERNAME=[SECURE_USERNAME]
DB_PASSWORD=[SECURE_PASSWORD]

# MAIL - Mailtrap Production
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=[MAILTRAP_USERNAME]
MAIL_PASSWORD=[MAILTRAP_TOKEN]
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@rapshop.pl

# SECURITY
TRUSTED_PROXIES=*
PHPMYADMIN_ROOT_PASSWORD=[STRONG_PASSWORD]
```

---

## 🚀 Quick Start (Production)

```bash
# 1. SSH do serwera
ssh -i your-key.pem ec2-user@your-elastic-ip

# 2. Clone i setup
cd ~
git clone https://github.com/BartoszKaca/projekt_sklep.git
cd projekt_sklep

# 3. Konfiguracja
cp .env.production .env
# Edytuj .env z rzeczywistymi danymi

# 4. SSL Certifikat
sudo certbot certonly --standalone -d rapshop.pl -d www.rapshop.pl

# 5. Start Docker
docker-compose -f docker-compose.prod.yml build
docker-compose -f docker-compose.prod.yml up -d

# 6. Init Database
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --seed

# 7. Start Nginx
sudo systemctl start nginx
sudo systemctl enable nginx

# Sprawdzenie statusu
docker-compose -f docker-compose.prod.yml ps
curl https://rapshop.pl
```

---

## 🛠️ Troubleshooting

### Aplikacja nie uruchamia się
```bash
docker-compose -f docker-compose.prod.yml logs app
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
```

### PHPMyAdmin niedostępny
```bash
docker-compose -f docker-compose.prod.yml ps
curl http://localhost:8080
```

### Mail nie wysyła
```bash
# Test SMTP connection
telnet smtp.mailtrap.io 2525
# Sprawdź logi
docker-compose -f docker-compose.prod.yml logs app | grep -i mail
```

### SSL Certificate issues
```bash
sudo certbot certificates
sudo certbot renew --force-renewal
```

---

## 📞 Kontakty i Zasoby

- **Mailtrap Support**: https://support.mailtrap.io
- **AWS EC2 Docs**: https://docs.aws.amazon.com/ec2/
- **Let's Encrypt**: https://letsencrypt.org
- **Laravel Deployment**: https://laravel.com/docs/deployment

---

**Ostatnia aktualizacja**: 2 grudnia 2025
**Status**: ✅ Gotowy do produkcji
