# 📋 LISTA KROKÓW WDRAŻANIA - PROJEKT RAP SHOP NA AWS EC2

**Data**: 2 grudnia 2025  
**Projekt**: Rap Shop Laravel  
**Status**: ✅ GOTOWY DO PRODUKCJI

---

## 🎯 CEL

Wdrożenie aplikacji Laravel (Rap Shop) na AWS EC2 z:
- ✅ HTTPS/TLS (Let's Encrypt)
- ✅ Mailtrap SMTP (konto produkcyjne)
- ✅ PHPMyAdmin (zabezpieczony)
- ✅ Własną domeną (rapshop.pl)
- ✅ Docker containerization
- ✅ Automatyczne backupy
- ✅ Health monitoring

---

## 📚 DOKUMENTACJA - GDZIE CZYTAĆ

| Dokument | Dla kogo | Opis |
|----------|----------|------|
| **`DEPLOYMENT_QUICK_START.md`** | 👈 ZACZNIJ TUTAJ | 5 kroków + szybka referenca |
| **`AWS_EC2_DEPLOYMENT.md`** | Szczegóły | Wszystkie 8 faz + troubleshooting |
| **`DEPLOYMENT_SUMMARY.md`** | Przegląd | Co się zmieniło, checklist |
| **`HTPASSWD_SETUP.md`** | PHPMyAdmin | Generowanie haseł, bezpieczeństwo |

---

## 🚀 5 KROKÓW WDRAŻANIA

### KROK 1: SSH na serwer EC2

```bash
# Zaloguj się na instancję
ssh -i your-key.pem ec2-user@YOUR_ELASTIC_IP

# Przejdź do projektu
cd ~/projekt_sklep
```

**Wymagania:**
- [ ] Instancja EC2 uruchomiona
- [ ] Elastic IP przydzielone
- [ ] Security Group otwiera porty 22, 80, 443
- [ ] Plik .pem z key pair

---

### KROK 2: Przygotowanie zmiennych środowiskowych

```bash
# Skopiuj template .env
cp .env.docker.prod .env

# Edytuj zmienne
nano .env
```

**Co zmienić w `.env`:**

```env
# Aplikacja
APP_URL=https://rapshop.pl
APP_ENV=production
APP_DEBUG=false

# Baza danych
DB_PASSWORD=YOUR_SECURE_DB_PASSWORD_HERE
PHPMYADMIN_ROOT_PASSWORD=YOUR_SECURE_ROOT_PASSWORD_HERE

# Mail - Mailtrap (OBOWIĄZKOWE)
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username_from_mailtrap
MAIL_PASSWORD=your_token_from_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@rapshop.pl
```

**Gdzie znaleźć dane Mailtrap?**
1. Zaloguj się: https://mailtrap.io
2. Inbox → Integrations → SMTP Settings
3. Skopiuj Host, Port, Username, Password

---

### KROK 3: Automatyczne wdrożenie Docker

```bash
# Ustaw uprawnienia
chmod +x deploy-aws.sh

# Uruchom deployment (6-10 minut)
./deploy-aws.sh rapshop.pl your-email@example.com
```

**Co robi ten skrypt?**
- ✅ Sprawdza Docker/Docker Compose
- ✅ Build images
- ✅ Start kontenerów
- ✅ Inicjalizacja bazy danych
- ✅ Migracje & seedy
- ✅ Storage link
- ✅ Cache optimization

**Po skrypcie:** Otrzymasz instrukcje do KROKU 4

---

### KROK 4: Generowanie SSL Certifikatu

```bash
# Ustaw uprawnienia
chmod +x setup-ssl.sh

# Generuj certifikat (3 minuty)
./setup-ssl.sh rapshop.pl your-email@example.com
```

**Co robisz:**
- Generujesz SSL z Let's Encrypt
- Konfigurujesz auto-renewal
- Ustawiasz Nginx hook do reload

**Certyfikat będzie w:**
```
/etc/letsencrypt/live/rapshop.pl/
├── fullchain.pem
└── privkey.pem
```

---

### KROK 5: Konfiguracja Nginx (Reverse Proxy)

```bash
# Skopiuj konfigurację
sudo cp nginx.conf /etc/nginx/conf.d/rapshop.conf

# Edytuj plik - ZMIEŃ YOUR_HOME_IP
sudo nano /etc/nginx/conf.d/rapshop.conf
```

**Co zmienić:**

W pliku szukaj: `YOUR_HOME_IP` i zamień na swoje IP

```nginx
# Przykład:
allow 127.0.0.1;
allow 10.0.0.0/8;
allow 123.45.67.89;  # ← TWOJE IP TUTAJ
deny all;
```

**Aby znaleźć swoje IP:**
```bash
curl https://ifconfig.me
```

**Test i reload:**
```bash
sudo nginx -t
sudo systemctl reload nginx
```

---

## ✅ WERYFIKACJA - Czy wszystko działa?

```bash
# Test 1: Health Check
./health-check.sh

# Test 2: SMTP Mailtrap
./test-smtp.sh smtp.mailtrap.io 2525 username token

# Test 3: Aplikacja (w przeglądarce)
https://rapshop.pl

# Test 4: PHPMyAdmin
https://rapshop.pl/admin/phpmyadmin
# Login: root / hasło z .env

# Test 5: SSL Certificate
curl -I https://rapshop.pl
# Powinno: HTTP/2 200
```

---

## 📋 NOWE PLIKI - STRONA SERWERA EC2

### Konfiguracyjne:
```
✅ docker-compose.prod.yml    - Produkcyjne kontenery
✅ Dockerfile.prod             - PHP-FPM optimized
✅ nginx.conf                  - Reverse proxy + SSL
✅ .env.production             - Zmienne (AWS RDS)
✅ .env.docker.prod            - Zmienne (Docker MySQL)
✅ .htpasswd                   - PHPMyAdmin auth
```

### Konfiguracja PHP:
```
✅ docker/php/php-production.ini   - Production PHP settings
✅ docker/php/php-fpm.conf         - FPM configuration
✅ docker/php/opcache.ini          - Cache optimization
```

### Skrypty Automatyzacji:
```
✅ deploy-aws.sh               - Pełne deployment
✅ setup-ssl.sh                - SSL Let's Encrypt
✅ backup-database.sh          - Database backup
✅ test-smtp.sh                - Test Mailtrap
✅ health-check.sh             - System status
✅ verify-setup.sh             - Verify files
```

### Dokumentacja:
```
✅ AWS_EC2_DEPLOYMENT.md       - Pełna instrukcja (8 faz)
✅ DEPLOYMENT_QUICK_START.md   - Quick start (5 kroków)
✅ DEPLOYMENT_SUMMARY.md       - Podsumowanie zmian
✅ HTPASSWD_SETUP.md           - PHPMyAdmin security
```

---

## 🔧 DODATKOWA KONFIGURACJA

### PHPMyAdmin - Hasło Basic Auth

```bash
# Wygeneruj hasło
openssl passwd -apr1
# Wpisz hasło 2 razy
# Skopiuj wynikowy hash

# Otwórz .htpasswd
nano .htpasswd

# Wstaw:
admin:$apr1$r31....kx0  # ← wygenerowany hash
```

**Skopiuj na serwer:**
```bash
scp -i your-key.pem .htpasswd ec2-user@YOUR_ELASTIC_IP:~/projekt_sklep/

# Na serwerze:
sudo cp .htpasswd /etc/nginx/.htpasswd
sudo chown www-data:www-data /etc/nginx/.htpasswd
```

---

### Backup Bazy Danych - Crontab

```bash
# Na serwerze EC2
crontab -e

# Dodaj linię (backup o 2:00 AM codziennie):
0 2 * * * cd /home/ec2-user/projekt_sklep && ./backup-database.sh >> /tmp/backup.log 2>&1
```

**Backupy będą w:** `~/backups/`

---

## 📧 MAIL CONFIGURATION - QUICK CHECK

### Zmienne .env:
```bash
grep MAIL .env
```

Powinno pokazać:
```
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_token
MAIL_ENCRYPTION=tls
```

### Test mail:
```bash
./test-smtp.sh smtp.mailtrap.io 2525 username token
```

### Sprawdzenie w Mailtrap:
1. Zaloguj się: https://mailtrap.io
2. Otwórz Inbox
3. Sprawdź czy test email się pojawił

---

## 🔐 SECURITY CHECKLIST

- [ ] APP_DEBUG=false (produkcja)
- [ ] APP_ENV=production
- [ ] HTTPS włączone (Let's Encrypt)
- [ ] IP Whitelist dla PHPMyAdmin
- [ ] Basic Auth dla PHPMyAdmin
- [ ] Silne hasła do bazy danych
- [ ] Silne hasła do PHPMyAdmin
- [ ] Security headers włączone (w nginx.conf)
- [ ] HSTS header włączony
- [ ] .env file NIE w Git

---

## 🎯 DOSTĘPY PRODUKCYJNE

| Usługa | URL | Dane logowania |
|--------|-----|---|
| **Aplikacja** | https://rapshop.pl | - |
| **PHPMyAdmin** | https://rapshop.pl/admin/phpmyadmin | root / DB_ROOT_PASSWORD |
| **Mailtrap** | https://mailtrap.io | your-email@example.com |
| **SSH** | ssh -i key.pem ec2-user@IP | - |
| **AWS Console** | https://aws.amazon.com | Your AWS account |

---

## 🛠️ PRZYDATNE KOMENDY

### Docker
```bash
# Statusy
docker-compose -f docker-compose.prod.yml ps

# Logi aplikacji
docker-compose -f docker-compose.prod.yml logs -f app

# Logi bazy
docker-compose -f docker-compose.prod.yml logs -f db

# Restart
docker-compose -f docker-compose.prod.yml restart app
```

### Laravel Artisan
```bash
# Clear cache
docker-compose -f docker-compose.prod.yml exec app php artisan cache:clear

# Database migrate
docker-compose -f docker-compose.prod.yml exec app php artisan migrate

# Tinker (console)
docker-compose -f docker-compose.prod.yml exec app php artisan tinker
```

### System
```bash
# Check SSL
sudo certbot certificates

# Nginx test
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx

# Check disk
df -h

# Check memory
free -h
```

---

## 🚨 TROUBLESHOOTING

### "Connection refused" na domenę
```bash
# Sprawdzenie Nginx
sudo systemctl status nginx
sudo nginx -t

# Sprawdzenie SSL
sudo certbot certificates

# Sprawdzenie Docker
docker-compose -f docker-compose.prod.yml ps
```

### Mail nie wysyła
```bash
# Test SMTP
./test-smtp.sh

# Logi
docker-compose -f docker-compose.prod.yml logs app | grep -i mail

# Sprawdzenie zmiennych
grep MAIL .env
```

### PHPMyAdmin 401 Unauthorized
```bash
# Sprawdzenie .htpasswd
sudo cat /etc/nginx/.htpasswd

# Sprawdzenie nginx config
sudo grep -A 10 "phpmyadmin" /etc/nginx/conf.d/rapshop.conf

# Sprawdzenie permissji
sudo ls -la /etc/nginx/.htpasswd
```

### Certyfikat SSL error
```bash
# Dry-run
sudo certbot renew --dry-run

# Force renew
sudo certbot renew --force-renewal

# Info o certach
sudo certbot certificates
```

---

## 📞 ZASOBY

- **Mailtrap Help**: https://help.mailtrap.io
- **AWS EC2 Docs**: https://docs.aws.amazon.com/ec2/
- **Laravel Deployment**: https://laravel.com/docs/deployment
- **Let's Encrypt**: https://letsencrypt.org/docs/
- **Nginx Docs**: https://nginx.org/en/docs/
- **Docker Docs**: https://docs.docker.com/

---

## ✨ CO ZOSTAŁO ZROBIONE

### Dokumentacja (100% ✅)
- [x] Pełna instrukcja wdrażania (8 faz)
- [x] Quick start guide
- [x] Podsumowanie zmian
- [x] PHPMyAdmin security guide
- [x] Troubleshooting

### Docker (100% ✅)
- [x] Production docker-compose.yml
- [x] Optimized Dockerfile
- [x] Health checks
- [x] Volume management
- [x] Network configuration

### Konfiguracja (100% ✅)
- [x] PHP Production settings
- [x] PHP-FPM optimization
- [x] Opcache configuration
- [x] Nginx reverse proxy
- [x] SSL/TLS setup

### Skrypty (100% ✅)
- [x] Automatyczne deployment
- [x] SSL certificate generation
- [x] Database backup
- [x] SMTP testing
- [x] Health monitoring

### Bezpieczeństwo (100% ✅)
- [x] HTTPS enforcement
- [x] Security headers
- [x] IP whitelist
- [x] Basic authentication
- [x] Environment variables

---

## 📊 HARMONOGRAM WDRAŻANIA

| Etap | Czas | Co robisz |
|------|------|----------|
| Przygotowanie AWS | 10 min | Instancja, IP, Security Group |
| Preparation | 5 min | SSH, git clone, .env |
| Deploy (KROK 3) | 8 min | ./deploy-aws.sh |
| SSL Setup (KROK 4) | 3 min | ./setup-ssl.sh |
| Nginx Config (KROK 5) | 5 min | nginx.conf + reload |
| Testy | 5 min | ./health-check.sh + testy |
| **RAZEM** | **~40 minut** | |

---

## ✅ FINAL CHECKLIST

### PRE-DEPLOYMENT
- [ ] Przeczytaj DEPLOYMENT_QUICK_START.md
- [ ] AWS EC2 instancja uruchomiona
- [ ] Elastic IP przydzielone
- [ ] Domain skonfigurowana w DNS
- [ ] Konto Mailtrap gotowe

### DEPLOYMENT
- [ ] KROK 1: SSH ✓
- [ ] KROK 2: .env przygotowany ✓
- [ ] KROK 3: ./deploy-aws.sh ✓
- [ ] KROK 4: ./setup-ssl.sh ✓
- [ ] KROK 5: nginx.conf ✓

### POST-DEPLOYMENT
- [ ] ./health-check.sh ✓
- [ ] Test aplikacji na domenie ✓
- [ ] Test mail (Mailtrap) ✓
- [ ] Test PHPMyAdmin ✓
- [ ] Backup script w crontab ✓
- [ ] Monitoring ustawiony ✓

### PRODUCTION
- [ ] Logi monitored
- [ ] Backupy działają
- [ ] SSL auto-renewal ✓
- [ ] Dokumentacja przeczytana
- [ ] Team wdrożony

---

## 🎉 GOTOWO!

Projekt jest w 100% przygotowany do wdrażania na AWS EC2.

### Następne kroki:
1. **Przeczytaj**: `DEPLOYMENT_QUICK_START.md`
2. **Zaloguj się**: SSH na serwer EC2
3. **Uruchom**: 5 kroków wdrażania
4. **Testuj**: ./health-check.sh
5. **Monitoruj**: Logi i backupy

---

**Status**: ✅ Production Ready  
**Wersja**: 1.0  
**Data**: 2 grudnia 2025  

Powodzenia z wdrażaniem! 🚀
