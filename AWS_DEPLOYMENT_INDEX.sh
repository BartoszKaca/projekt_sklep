#!/bin/bash

# ===========================================================================
# AWS EC2 PRODUCTION SETUP - INDEX & QUICK START
# ===========================================================================
# Ten plik zawiera przegląd wszystkich plików do wdrażania

cat << 'EOF'

╔════════════════════════════════════════════════════════════════════════════╗
║                   🚀 RAP SHOP - AWS EC2 DEPLOYMENT 🚀                     ║
║                                                                            ║
║  Projekt został przygotowany do wdrażania na AWS EC2 z pełną dokumentacją ║
║  obsługi: Mailtrap SMTP, PHPMyAdmin, SSL/TLS i własną domeną              ║
╚════════════════════════════════════════════════════════════════════════════╝

📚 DOKUMENTACJA - START TUTAJ
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

1. 📖 DEPLOYMENT_QUICK_START.md
   👉 ZACZNIJ TUTAJ - 5 kroków do wdrożenia
   - Szybka instrukcja
   - Przydatne komendy
   - Troubleshooting

2. 📖 AWS_EC2_DEPLOYMENT.md
   🔬 PEŁNA DOKUMENTACJA - Wszystkie szczegóły
   - 8 faz wdrażania
   - Konfiguracja AWS
   - Setup Mailtrap
   - SSL/TLS
   - Backup strategy

3. 📖 DEPLOYMENT_SUMMARY.md
   📋 PODSUMOWANIE - Co zostało przygotowane
   - Lista wszystkich plików
   - Checklist wdrażania
   - Wymagane zmienne

4. 📖 HTPASSWD_SETUP.md
   🔐 BEZPIECZEŃSTWO - PHPMyAdmin authentication
   - Generowanie haseł
   - IP Whitelist
   - Basic Auth


🐳 PLIKI DOCKER - PRODUCTION READY
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Docker Compose:
  • docker-compose.prod.yml    ← Produkcyjne kontenery
  • Dockerfile.prod             ← Optimized PHP FPM
  • .dockerignore               ← Excludenuta pliki z build

Services:
  ✅ PHP 8.5-FPM (optimized, Opcache, security)
  ✅ MySQL 8.0 (z health checks)
  ✅ PHPMyAdmin (z IP protection)
  ✅ Redis (opcjonalny - cache/sessions)


⚙️ KONFIGURACJA - ZMIENNE ŚRODOWISKOWE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Pliki do edycji PRZED wdrażaniem:

1. .env (z .env.docker.prod lub .env.production)
   - MAIL_USERNAME = z Mailtrap
   - MAIL_PASSWORD = z Mailtrap
   - DB_PASSWORD = silne hasło
   - PHPMYADMIN_ROOT_PASSWORD = silne hasło
   - APP_URL = https://rapshop.pl

2. nginx.conf
   - YOUR_HOME_IP = Twoje IP (dla PHPMyAdmin)
   - rapshop.pl = Twoja domena

3. .htpasswd (wygeneruj wg HTPASSWD_SETUP.md)
   - admin = username
   - hasło = wygeneruj z: openssl passwd -apr1


🛠️ SKRYPTY AUTOMATYZACJI
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Uruchamialny:

1. ./verify-setup.sh
   Sprawdza czy wszystkie pliki są na miejscu
   $ chmod +x verify-setup.sh && ./verify-setup.sh

2. ./deploy-aws.sh rapshop.pl your@email.com
   Pełne wdrożenie (5 faz automatyzacji)
   Tworzy: Docker images, containers, database, SSL

3. ./setup-ssl.sh rapshop.pl your@email.com
   Generuje SSL certifikat Let's Encrypt
   Konfiguruje auto-renewal

4. ./backup-database.sh
   Tworzy backup bazy danych
   Dodaj do crontab: 0 2 * * * ~/projekt_sklep/backup-database.sh

5. ./test-smtp.sh
   Testuje połączenie z Mailtrap

6. ./health-check.sh
   Sprawdza status całego systemu


🚀 QUICK START - 5 KROKÓW
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Krok 1: SSH na serwer EC2
  $ ssh -i your-key.pem ec2-user@YOUR_ELASTIC_IP
  $ cd ~/projekt_sklep

Krok 2: Przygotuj zmienne środowiskowe
  $ cp .env.docker.prod .env
  $ nano .env
  → Zmień: MAIL_USERNAME, MAIL_PASSWORD, DB_PASSWORD

Krok 3: Uruchom deployment
  $ chmod +x deploy-aws.sh
  $ ./deploy-aws.sh rapshop.pl your@email.com
  → Czeka 5-10 minut

Krok 4: Setup SSL
  $ chmod +x setup-ssl.sh
  $ ./setup-ssl.sh rapshop.pl your@email.com
  → Generuje certifikat Let's Encrypt

Krok 5: Nginx configuration
  $ sudo cp nginx.conf /etc/nginx/conf.d/rapshop.conf
  $ sudo nano /etc/nginx/conf.d/rapshop.conf
  → Zmień: YOUR_HOME_IP na Twoje IP
  $ sudo nginx -t && sudo systemctl reload nginx


📧 MAILTRAP INTEGRATION
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

1. Zaloguj się: https://mailtrap.io
2. Przejdź do: Inbox → Integrations → SMTP Settings
3. Skopiuj:
   - Host: smtp.mailtrap.io
   - Port: 2525
   - Username: [z dashboarda]
   - Password: [z dashboarda]

4. Wstaw do .env:
   MAIL_HOST=smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your_username
   MAIL_PASSWORD=your_token
   MAIL_ENCRYPTION=tls

5. Test:
   $ ./test-smtp.sh smtp.mailtrap.io 2525 username token


🔐 BEZPIECZEŃSTWO
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

PHPMyAdmin (3 warstwy ochrony):
  ✅ IP Whitelist - tylko z whitelisted IP
  ✅ Basic Authentication - username + password
  ✅ HTTPS - szyfrowana komunikacja

Aplikacja:
  ✅ HTTPS/TLS - Let's Encrypt auto-renewal
  ✅ Security Headers - HSTS, X-Frame-Options, CSP
  ✅ Environment secrets - zmienne w .env

Baza danych:
  ✅ Automatyczne backupy
  ✅ MySQL 8.0 z health checks
  ✅ Dostęp tylko z kontenera


📊 DOSTĘPY PRODUKCYJNE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Aplikacja:          https://rapshop.pl
PHPMyAdmin:         https://rapshop.pl/admin/phpmyadmin
                    (login: root / password z .env)

Mailtrap Dashboard: https://mailtrap.io
AWS Console:        https://aws.amazon.com
SSH dostęp:         ssh -i key.pem ec2-user@ELASTIC_IP


🛡️ PRODUCTION CHECKLIST
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Przed wdrażaniem:
  ☐ Przeczytaj DEPLOYMENT_QUICK_START.md
  ☐ Przygotuj .env z danymi Mailtrap
  ☐ Wygeneruj hasła (openssl passwd -apr1)
  ☐ Skonfiguruj DNS (A record → Elastic IP)

Podczas wdrażania:
  ☐ ./deploy-aws.sh
  ☐ ./setup-ssl.sh
  ☐ Nginx configuration
  ☐ ./test-smtp.sh

Po wdrażaniu:
  ☐ Test aplikacji na domenie
  ☐ Test PHPMyAdmin
  ☐ Test mailingu (Mailtrap)
  ☐ ./health-check.sh
  ☐ Backup script w crontab
  ☐ Monitoring ustawiony


📞 TROUBLESHOOTING
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Problem                          Rozwiązanie
────────────────────────────────────────────────────────────────────────────

Aplikacja nie startuje           docker-compose -f docker-compose.prod.yml logs app

Baza danych niedostępna          docker-compose -f docker-compose.prod.yml restart db

Mail nie wysyła                  ./test-smtp.sh + sprawdzaj Mailtrap.io

PHPMyAdmin 401 Unauthorized      Sprawdzaj .htpasswd i nginx.conf

SSL Certificate issues           sudo certbot certificates
                                 sudo certbot renew --dry-run

Nginx test failed                sudo nginx -t
                                 sudo tail -f /var/log/nginx/rapshop_error.log


🔗 ZASOBY
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Dokumentacja:
  • AWS EC2: https://docs.aws.amazon.com/ec2/
  • Laravel: https://laravel.com/docs/deployment
  • Docker: https://docs.docker.com/
  • Nginx: https://nginx.org/en/docs/
  • Let's Encrypt: https://letsencrypt.org/

Support:
  • Mailtrap Help: https://help.mailtrap.io
  • AWS Support: https://aws.amazon.com/support/
  • Laravel Forge: https://forge.laravel.com/ (alternatywa)


✨ FEATURES
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Performance:
  ✅ PHP Opcache 256MB
  ✅ Database query caching
  ✅ Static file caching (1 rok)
  ✅ Gzip compression
  ✅ Optimized PHP-FPM

Security:
  ✅ HTTPS/TLS auto-renewal
  ✅ Security headers
  ✅ IP Whitelist
  ✅ Environment secrets
  ✅ Disabled dangerous PHP functions

Reliability:
  ✅ Health checks
  ✅ Auto-restart
  ✅ Data persistence
  ✅ Automated backups
  ✅ SSL auto-renewal


════════════════════════════════════════════════════════════════════════════

STATUS: ✅ PRODUCTION READY
WERSJA: 1.0
DATA: 2 grudnia 2025

════════════════════════════════════════════════════════════════════════════

👉 NASTĘPNY KROK: Przeczytaj DEPLOYMENT_QUICK_START.md

════════════════════════════════════════════════════════════════════════════

EOF
