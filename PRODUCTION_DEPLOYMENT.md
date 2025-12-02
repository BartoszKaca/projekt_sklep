# 🚀 Wdrożenie Systemu Mailingowego - Produkcja

Ten dokument opisuje kroki potrzebne do wdrożenia systemu mailingowego w środowisku produkcyjnym.

## ⚠️ WAŻNE - Przed wdrożeniem

### 1. Zmień konfigurację SMTP
W pliku `.env` produkcyjnym **MUSISZ** zmienić dane SMTP z Mailtrap na prawdziwy serwer:

```env
# ❌ NIE UŻYWAJ w produkcji
MAIL_HOST=sandbox.smtp.mailtrap.io

# ✅ Użyj prawdziwego SMTP, np:

# SendGrid
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=twoj_sendgrid_api_key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@rapshop.pl"
MAIL_FROM_NAME="Rap Shop"

# Lub Mailgun
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.rapshop.pl
MAILGUN_SECRET=twoj_mailgun_secret
MAIL_FROM_ADDRESS="noreply@rapshop.pl"
MAIL_FROM_NAME="Rap Shop"

# Lub AWS SES
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=twoj_aws_key
AWS_SECRET_ACCESS_KEY=twoj_aws_secret
AWS_DEFAULT_REGION=eu-central-1
MAIL_FROM_ADDRESS="noreply@rapshop.pl"
MAIL_FROM_NAME="Rap Shop"
```

### 2. Zmień konfigurację PayU
```env
# ❌ NIE UŻYWAJ sandbox w produkcji
PAYU_ENVIRONMENT=sandbox

# ✅ Użyj produkcyjnych danych
PAYU_ENVIRONMENT=production
PAYU_POS_ID=twoj_produkcyjny_pos_id
PAYU_SIGNATURE_KEY=twoj_produkcyjny_signature_key
PAYU_CLIENT_ID=twoj_produkcyjny_client_id
PAYU_CLIENT_SECRET=twoj_produkcyjny_client_secret
```

### 3. Ustaw APP_URL
```env
APP_URL=https://rapshop.pl
```

## 🔧 Konfiguracja serwera produkcyjnego

### 1. Uruchom migracje
```bash
php artisan migrate --force
```

### 2. Skonfiguruj Queue Worker jako Service

#### Dla systemd (Ubuntu/Debian)

Utwórz plik `/etc/systemd/system/rapshop-queue.service`:

```ini
[Unit]
Description=Rap Shop Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
RestartSec=10
ExecStart=/usr/bin/php /var/www/rapshop/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

Następnie:
```bash
# Przeładuj systemd
sudo systemctl daemon-reload

# Włącz autostart
sudo systemctl enable rapshop-queue

# Uruchom
sudo systemctl start rapshop-queue

# Sprawdź status
sudo systemctl status rapshop-queue
```

#### Dla Supervisor (alternatywa)

Zainstaluj Supervisor:
```bash
sudo apt-get install supervisor
```

Utwórz plik `/etc/supervisor/conf.d/rapshop-queue.conf`:

```ini
[program:rapshop-queue]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/php /var/www/rapshop/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/rapshop/storage/logs/queue-worker.log
stopwaitsecs=3600
```

Uruchom:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start rapshop-queue:*
```

### 3. Skonfiguruj Cron dla scheduled tasks

Dodaj do crontab (`crontab -e`):
```bash
* * * * * cd /var/www/rapshop && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Optymalizacja

```bash
# Cache konfiguracji
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optymalizacja autoloadera
composer install --optimize-autoloader --no-dev
```

## 📧 Rekomendowane usługi SMTP

### 1. SendGrid (Rekomendowane)
- ✅ 100 emaili dziennie za darmo
- ✅ Łatwa konfiguracja
- ✅ Dobre statistics
- ✅ Dobra deliverability

**Instalacja:**
```bash
composer require symfony/sendgrid-mailer
```

**Konfiguracja .env:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=twoj_sendgrid_api_key
MAIL_ENCRYPTION=tls
```

### 2. Amazon SES
- ✅ Bardzo tani (0.10$ za 1000 emaili)
- ✅ Wysoka deliverability
- ⚠️ Wymaga weryfikacji domeny

**Instalacja:**
```bash
composer require aws/aws-sdk-php
```

**Konfiguracja .env:**
```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=twoj_key
AWS_SECRET_ACCESS_KEY=twoj_secret
AWS_DEFAULT_REGION=eu-central-1
```

### 3. Mailgun
- ✅ 5000 emaili miesięcznie za darmo
- ✅ Dobre API
- ✅ Statistics i tracking

**Instalacja:**
```bash
composer require symfony/mailgun-mailer
```

**Konfiguracja .env:**
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.rapshop.pl
MAILGUN_SECRET=twoj_secret
MAILGUN_ENDPOINT=api.eu.mailgun.net
```

## 🔐 SPF, DKIM i DMARC

Dla najlepszej deliverability skonfiguruj DNS:

### SPF Record
```
v=spf1 include:_spf.sendgrid.net ~all
```

### DKIM
Skopiuj klucz DKIM z panelu SendGrid/Mailgun i dodaj do DNS jako TXT record.

### DMARC
```
v=DMARC1; p=quarantine; rua=mailto:dmarc@rapshop.pl
```

## 📊 Monitoring produkcyjny

### 1. Logi

Skonfiguruj rotację logów. Utwórz `/etc/logrotate.d/rapshop`:

```
/var/www/rapshop/storage/logs/*.log {
    daily
    rotate 14
    compress
    delaycompress
    notifempty
    missingok
    create 0644 www-data www-data
}
```

### 2. Alerty dla failed jobs

Dodaj monitoring dla failed jobs:

```bash
# Sprawdź codziennie o 9:00
0 9 * * * cd /var/www/rapshop && php artisan tinker --execute="if(DB::table('failed_jobs')->count() > 0) mail('admin@rapshop.pl', 'Failed Jobs Alert', 'Są nieudane zadania w kolejce');"
```

### 3. Health Check Endpoint

Dodaj endpoint do monitorowania:

`routes/web.php`:
```php
Route::get('/health', function() {
    $queueConnection = config('queue.default');
    $pendingJobs = DB::table('jobs')->count();
    $failedJobs = DB::table('failed_jobs')->count();
    
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'queue' => [
            'connection' => $queueConnection,
            'pending' => $pendingJobs,
            'failed' => $failedJobs
        ],
        'mail' => [
            'mailer' => config('mail.default'),
            'from' => config('mail.from.address')
        ]
    ]);
});
```

## 🔔 Powiadomienia o problemach

Zainstaluj Laravel Telescope dla debugowania w produkcji (opcjonalne):

```bash
composer require laravel/telescope
php artisan telescope:install
php artisan migrate
```

Lub użyj Sentry dla error tracking:

```bash
composer require sentry/sentry-laravel
php artisan sentry:publish --dsn=twoj_sentry_dsn
```

## 🚦 Rate Limiting

Dodaj rate limiting dla endpointów mailingowych:

`app/Http/Kernel.php`:
```php
protected $middlewareGroups = [
    'api' => [
        'throttle:60,1', // 60 requestów na minutę
        // ...
    ],
];
```

Dla resetowania hasła i weryfikacji:
```php
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->middleware('throttle:5,1'); // 5 requestów na minutę
    
Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
    ->middleware('throttle:6,1'); // 6 requestów na minutę
```

## 📈 Skalowalność

### Dla większego ruchu użyj Redis

```bash
# Zainstaluj Redis
sudo apt-get install redis-server

# Zainstaluj PHP extension
sudo apt-get install php-redis
```

Zmień konfigurację w `.env`:
```env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Uruchom wiele queue workers

W Supervisor:
```ini
numprocs=4  # Uruchom 4 workery
```

## ✅ Checklist wdrożenia

- [ ] Zmieniono Mailtrap na prawdziwy SMTP
- [ ] Zmieniono PayU z sandbox na production
- [ ] Ustawiono APP_URL na produkcyjny
- [ ] Uruchomiono migracje
- [ ] Skonfigurowano queue worker (systemd/supervisor)
- [ ] Skonfigurowano cron dla scheduled tasks
- [ ] Zoptymalizowano cache (config, routes, views)
- [ ] Skonfigurowano SPF/DKIM/DMARC
- [ ] Skonfigurowano rotację logów
- [ ] Dodano monitoring failed jobs
- [ ] Dodano health check endpoint
- [ ] Przetestowano wszystkie typy emaili
- [ ] Sprawdzono deliverability

## 🧪 Testowanie produkcyjne

Po wdrożeniu:

1. **Zarejestruj testowe konto** z prawdziwym emailem
2. **Zresetuj hasło** - sprawdź czy email przychodzi
3. **Złóż testowe zamówienie** - sprawdź wszystkie emaile
4. **Testuj PayU** - użyj karty testowej PayU w trybie produkcyjnym
5. **Zmień status zamówienia** - sprawdź email aktualizacji

## 📞 Wsparcie techniczne

W razie problemów sprawdź:
1. Logi Laravel: `/var/www/rapshop/storage/logs/laravel.log`
2. Queue worker logi: `/var/www/rapshop/storage/logs/queue-worker.log`
3. System logs: `sudo journalctl -u rapshop-queue -f`
4. Failed jobs: `php artisan queue:failed`

---

**Powodzenia z wdrożeniem! 🚀**
