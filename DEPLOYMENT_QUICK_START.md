# 🚀 AWS EC2 Production Deployment - Quick Reference

## 📚 Dokumentacja

Pełna dokumentacja znajduje się w: **`AWS_EC2_DEPLOYMENT.md`**

---

## ⚡ Quick Start (5 kroków)

### 1️⃣ SSH do instancji EC2
```bash
ssh -i your-key.pem ec2-user@YOUR_ELASTIC_IP
cd ~/projekt_sklep
```

### 2️⃣ Przygotowanie zmiennych środowiska
```bash
cp .env.docker.prod .env
nano .env  # Edytuj zmienne Mailtrap i bazy danych
```

### 3️⃣ Automatyczne wdrożenie
```bash
chmod +x deploy-aws.sh
./deploy-aws.sh rapshop.pl your-email@example.com
```

### 4️⃣ Konfiguracja SSL (Po FAZIE 6)
```bash
chmod +x setup-ssl.sh
./setup-ssl.sh rapshop.pl your-email@example.com
```

### 5️⃣ Konfiguracja Nginx (Ręcznie)
```bash
sudo cp nginx.conf /etc/nginx/conf.d/rapshop.conf
sudo nano /etc/nginx/conf.d/rapshop.conf  # Zmień YOUR_HOME_IP
sudo nginx -t
sudo systemctl reload nginx
```

---

## 📋 Pliki Konfiguracyjne

| Plik | Opis | Użycie |
|------|------|--------|
| `docker-compose.prod.yml` | Produkcyjne kontenery | `docker-compose -f docker-compose.prod.yml up -d` |
| `Dockerfile.prod` | Image produkcyjny | Używany przez docker-compose.prod.yml |
| `.env.production` | Zmienne produkcyjne (AWS RDS) | Dla wdrożeń z AWS RDS |
| `.env.docker.prod` | Zmienne Docker produkcyjne | Dla wdrożeń z MySQL w Docker |
| `nginx.conf` | Reverse proxy & SSL | Skopiować do `/etc/nginx/conf.d/` |
| `.dockerignore` | Exclude pliki z build | Automatycznie używany |

---

## 🔧 Pliki Narzędziowe

| Skrypt | Opis |
|--------|------|
| `deploy-aws.sh` | Pełne automatyczne wdrożenie |
| `setup-ssl.sh` | Generowanie SSL (Let's Encrypt) |
| `backup-database.sh` | Backup bazy danych |
| `test-smtp.sh` | Test połączenia Mailtrap |
| `health-check.sh` | Sprawdzenie statusu systemu |

---

## 🌐 Zmienne Środowiskowe - Mailtrap

### Znalezienie danych dostępu:
1. Zaloguj się na https://mailtrap.io
2. Przejdź do **Inbox** > **Integrations** > **SMTP Settings**
3. Skopiuj:
   - **Host**: `smtp.mailtrap.io`
   - **Port**: `2525`
   - **Username**: Twój email/ID
   - **Password**: Token API

### Wstaw do `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username_from_mailtrap
MAIL_PASSWORD=your_token_from_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@rapshop.pl
```

---

## 🔐 Bezpieczeństwo PHPMyAdmin

### IP Whitelist
W `nginx.conf`:
```nginx
allow 127.0.0.1;        # Localhost
allow 10.0.0.0/8;       # AWS Private IPs
allow YOUR.HOME.IP.HERE; # Twoje IP
deny all;
```

### Basic Authentication
```bash
# Generuj hasło
openssl passwd -apr1

# Kopiuj wynik do .htpasswd
echo "admin:$apr1$..." > .htpasswd

# Skopiuj na serwer
sudo cp .htpasswd /etc/nginx/.htpasswd
sudo chown www-data:www-data /etc/nginx/.htpasswd
```

---

## 📊 Dostępy do Systemu

| Usługa | URL | Login |
|--------|-----|-------|
| **Aplikacja** | https://rapshop.pl | - |
| **PHPMyAdmin** | https://rapshop.pl/admin/phpmyadmin | root/DB_ROOT_PASSWORD |
| **Mailtrap Dashboard** | https://mailtrap.io | your-email@example.com |
| **AWS EC2 Console** | https://aws.amazon.com | Your AWS Account |

---

## 🛠️ Komendy Przydatne

### Docker
```bash
# Pokaż statusy
docker-compose -f docker-compose.prod.yml ps

# Logi
docker-compose -f docker-compose.prod.yml logs -f app
docker-compose -f docker-compose.prod.yml logs -f db

# Restart service
docker-compose -f docker-compose.prod.yml restart app

# Wykonaj artisan command
docker-compose -f docker-compose.prod.yml exec app php artisan migrate
docker-compose -f docker-compose.prod.yml exec app php artisan cache:clear
```

### System
```bash
# Sprawdzenie SSL
sudo certbot certificates

# Testy Nginx
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx

# Logi systemowe
sudo tail -f /var/log/nginx/rapshop_error.log
sudo tail -f /var/log/php-fpm.log
```

---

## 📧 Test Mailingu

```bash
chmod +x test-smtp.sh
./test-smtp.sh smtp.mailtrap.io 2525 your_username your_password
```

---

## 💾 Backup & Restore

### Backup
```bash
chmod +x backup-database.sh
./backup-database.sh ~/backups

# Lub w crontab (daily o 2:00 AM):
0 2 * * * cd /home/ec2-user/projekt_sklep && ./backup-database.sh
```

### Restore
```bash
# Rozpakuj
gunzip ~/backups/db_sklep_laravel_20251202_020000.sql.gz

# Wstaw do bazy
docker-compose -f docker-compose.prod.yml exec -T db mysql \
  -u laravel -plasyon sklep_laravel < ~/backups/db_sklep_laravel_20251202_020000.sql
```

---

## 🩺 Health Check

```bash
chmod +x health-check.sh
./health-check.sh
```

Sprawdza:
- ✅ Status kontenerów Docker
- ✅ Połączenie z bazą danych
- ✅ Odpowiadanie aplikacji
- ✅ Konfiguracja mail
- ✅ Użycie dysku
- ✅ Stan backupów

---

## 🔗 DNS Configuration

W Twoim domain registrze (Route 53 / Cloudflare / Namecheap etc.):

```
Type: A
Name: @
Value: [YOUR_ELASTIC_IP]

Type: A
Name: www
Value: [YOUR_ELASTIC_IP]
# Lub CNAME: www CNAME rapshop.pl
```

---

## ⚠️ Troubleshooting

### Aplikacja nie startuje
```bash
docker-compose -f docker-compose.prod.yml logs app
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache --clear
```

### Baza danych niedostępna
```bash
docker-compose -f docker-compose.prod.yml logs db
docker-compose -f docker-compose.prod.yml restart db
```

### Mail nie wysyła
```bash
# Sprawdź zmienne w .env
grep MAIL .env

# Test SMTP
./test-smtp.sh

# Sprawdź logi
docker-compose -f docker-compose.prod.yml logs app | grep -i mail
```

### SSL Certificate issues
```bash
# Sprawdź certyfikaty
sudo certbot certificates

# Force renewal
sudo certbot renew --force-renewal

# Test dry-run
sudo certbot renew --dry-run
```

### PHPMyAdmin niedostępny
```bash
# Sprawdzenie statusu
docker-compose -f docker-compose.prod.yml ps phpmyadmin

# Restart
docker-compose -f docker-compose.prod.yml restart phpmyadmin

# Sprawdzenie portów
curl -I http://localhost:8080
```

---

## 📞 Support & Resources

- **Mailtrap Docs**: https://help.mailtrap.io
- **AWS EC2 Docs**: https://docs.aws.amazon.com/ec2/
- **Laravel Deployment**: https://laravel.com/docs/deployment
- **Let's Encrypt**: https://letsencrypt.org/docs/
- **Nginx Docs**: https://nginx.org/en/docs/

---

## 📝 Checklist Wdrażania

- [ ] AWS EC2 instancja uruchomiona
- [ ] Elastic IP przydzielone
- [ ] Security Group skonfigurowany
- [ ] Domain skonfigurowana w DNS
- [ ] Kod sclonowany na serwer
- [ ] `.env` przygotowany z danymi Mailtrap
- [ ] `deploy-aws.sh` uruchomiony
- [ ] SSL certyfikat wygenerowany
- [ ] Nginx skonfigurowany
- [ ] Aplikacja testowana na domenie
- [ ] Backup skrypt dodany do crontab
- [ ] Health check wykonany

---

**Wersja**: 1.0  
**Data**: 2 grudnia 2025  
**Status**: ✅ Production Ready

Pytania? Sprawdzaj AWS_EC2_DEPLOYMENT.md dla pełnej dokumentacji.
