# 📧 System Mailingowy - Rap Shop

## 🎯 Przegląd

Kompleksowy system mailingowy obsługujący:

✅ **Weryfikację emailową** - automatyczna po rejestracji  
✅ **Resetowanie hasła** - bezpieczny proces z tokenem  
✅ **Potwierdzenie zamówienia** - szczegóły po checkout  
✅ **Potwierdzenie płatności** - po opłaceniu przez PayU  
✅ **Aktualizacje statusu** - przy każdej zmianie zamówienia  

## 🚀 Szybki start

### 1. Migracja bazy danych
```bash
php artisan migrate
```

### 2. Uruchomienie Queue Worker
```bash
# W osobnym terminalu
php artisan queue:work

# Lub w trybie deweloperskim
php artisan queue:listen
```

### 3. Testowanie przez Artisan Command
```bash
# Interaktywny test
php artisan mailing:test

# Lub konkretny typ
php artisan mailing:test verification
php artisan mailing:test password
php artisan mailing:test order
php artisan mailing:test payment
php artisan mailing:test status
php artisan mailing:test all
```

### 4. Testowanie przez Bash Script
```bash
chmod +x test-mailing.sh
./test-mailing.sh
```

## 📚 Dokumentacja

### Dla użytkownika
- **[MAILING_QUICK_START.md](MAILING_QUICK_START.md)** - Przewodnik szybkiego startu
- **[TESTING_MAILING_SYSTEM.md](TESTING_MAILING_SYSTEM.md)** - Szczegółowa dokumentacja testowa
- **[TINKER_COMMANDS.md](TINKER_COMMANDS.md)** - Gotowe komendy do Tinker

### Dla developera
- **[MAILING_CHANGES.md](MAILING_CHANGES.md)** - Log zmian i szczegóły techniczne

## 🧪 Metody testowania

### Metoda 1: Artisan Command (Rekomendowana)
```bash
php artisan mailing:test
```
Interaktywne menu z możliwością wyboru typu emaila.

### Metoda 2: Bash Script
```bash
./test-mailing.sh
```
Rozbudowane menu z dodatkowymi opcjami (logi, kolejka, itp).

### Metoda 3: Tinker
```bash
php artisan tinker
```
Następnie użyj komend z pliku `TINKER_COMMANDS.md`.

### Metoda 4: Przeglądarką
- Zarejestruj konto: http://localhost/register
- Zresetuj hasło: http://localhost/password/reset
- Złóż zamówienie: http://localhost/checkout
- Panel admin: http://localhost/admin/orders

## ⚙️ Konfiguracja

### .env - Wymagane zmienne
```env
# Mail
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=bc020a0ee9c722
MAIL_PASSWORD=5cf38349d21ed0
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@rapshop.pl"
MAIL_FROM_NAME="Rap Shop"

# Queue
QUEUE_CONNECTION=database

# PayU (opcjonalne, dla testowania płatności)
PAYU_ENVIRONMENT=sandbox
PAYU_POS_ID=501217
PAYU_SIGNATURE_KEY=12ba91835be2948ec0800d7c658e0e56
```

## 📊 Monitoring

### Sprawdzenie kolejki
```bash
# Status
php artisan queue:work --once

# Failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry {id}

# Clear all
php artisan queue:flush
```

### Logi
```bash
# Real-time
tail -f storage/logs/laravel.log

# Tylko maile
tail -f storage/logs/laravel.log | grep -i mail

# Tylko PayU
tail -f storage/logs/laravel.log | grep -i payu
```

## 📁 Struktura projektu

```
app/
├── Console/Commands/
│   └── TestMailingSystem.php          # Artisan command do testowania
├── Mail/
│   ├── EmailVerificationMail.php
│   ├── OrderConfirmationMail.php
│   ├── PaymentConfirmationMail.php
│   ├── OrderStatusUpdateMail.php
│   └── PasswordResetMail.php
├── Observers/
│   └── OrderObserver.php               # Auto-wysyłanie emaili
└── Http/Controllers/
    ├── Auth/
    │   ├── RegisterController.php      # Rejestracja + weryfikacja
    │   ├── VerificationController.php  # Obsługa weryfikacji
    │   ├── ForgotPasswordController.php
    │   └── ResetPasswordController.php
    ├── CheckoutController.php          # Zamówienia
    └── PaymentController.php           # PayU integration

resources/views/
├── emails/                             # Szablony emaili
│   ├── email-verification.blade.php
│   ├── order-confirmation.blade.php
│   ├── payment-confirmation.blade.php
│   ├── order-status-update.blade.php
│   └── password-reset.blade.php
└── auth/                               # Widoki autoryzacji
    ├── verify.blade.php
    └── passwords/
        ├── email.blade.php
        └── reset.blade.php

database/migrations/
└── 2025_12_02_000001_add_payu_order_id_to_orders_table.php

Dokumentacja/
├── MAILING_QUICK_START.md              # Quick start guide
├── TESTING_MAILING_SYSTEM.md           # Szczegółowe testy
├── MAILING_CHANGES.md                  # Log zmian
└── TINKER_COMMANDS.md                  # Komendy Tinker

Skrypty/
├── test-mailing.sh                     # Bash test script
└── MAILING_README.md                   # Ten plik
```

## 🔧 Funkcje

### OrderObserver - Automatyzacja
Reaguje na zmiany w zamówieniach:
- **payment_status → 'paid'** → wysyła PaymentConfirmationMail
- **status → zmiana** → wysyła OrderStatusUpdateMail

### Order Model - Nowe metody
```php
$order->markAsPaid();                              // Oznacz jako opłacone
$order->markAsShipped('123ABC', 'DPD');           // Oznacz jako wysłane
$order->markAsDelivered();                        // Oznacz jako dostarczone
$order->isPaid();                                 // bool
$order->canBeCancelled();                         // bool
```

### PaymentController - Integracja PayU
- Inicjalizuje płatność w PayU
- Odbiera webhooks z PayU
- Automatycznie aktualizuje statusy
- Loguje wszystkie akcje

## 🎨 Design emaili

Wszystkie emaile:
- 📱 W pełni responsywne
- 🎨 Brand colors Rap Shop (gradient indigo-pink)
- ✨ Nowoczesny design
- 📧 Fallback dla starych klientów email
- 🇵🇱 Pełne tłumaczenie na polski

## ✅ Checklist wdrożenia

- [x] Weryfikacja emailowa
- [x] Resetowanie hasła  
- [x] Potwierdzenie zamówienia
- [x] Potwierdzenie płatności
- [x] Aktualizacje statusu
- [x] PayU integration
- [x] OrderObserver
- [x] Queue system
- [x] Error handling
- [x] Logging
- [x] Dokumentacja
- [x] Skrypty testowe

## 🐛 Troubleshooting

### Email nie wysyłany?
1. Sprawdź queue worker: `ps aux | grep queue`
2. Sprawdź logi: `tail -f storage/logs/laravel.log`
3. Sprawdź failed jobs: `php artisan queue:failed`
4. Sprawdź konfigurację SMTP w `.env`

### Link weryfikacyjny wygasł?
- Linki są ważne 60 minut
- Użytkownik może poprosić o nowy w `/email/verify`

### PayU webhook nie działa lokalnie?
- To normalne - webhooks nie działają lokalnie
- Testuj statusy płatności manualnie
- W produkcji zadziała automatycznie

### Queue worker się zatrzymał?
```bash
# Sprawdź
ps aux | grep queue:work

# Uruchom ponownie
php artisan queue:work
```

## 🔗 Przydatne linki

- [Laravel Mail Docs](https://laravel.com/docs/mail)
- [Mailtrap](https://mailtrap.io)
- [PayU Docs](https://developers.payu.com/pl/)
- [Queue Docs](https://laravel.com/docs/queues)

## 📞 Wsparcie

Problemy? Sprawdź:
1. **Logi**: `storage/logs/laravel.log`
2. **Dokumentację**: pliki `*.md` w projekcie
3. **Failed jobs**: `php artisan queue:failed`

## 🎯 Quick Commands

```bash
# Start everything
php artisan migrate && php artisan queue:work &

# Test all emails
php artisan mailing:test all

# Monitor logs
tail -f storage/logs/laravel.log | grep -i --color=always "mail\|error"

# Check queue status
php artisan tinker --execute="echo DB::table('jobs')->count() . ' pending jobs';"

# Clear everything
php artisan queue:flush && php artisan cache:clear
```

---

## 🚀 Gotowe do użycia!

System mailingowy jest w pełni funkcjonalny. Wszystkie emaile są profesjonalnie zaprojektowane, responsywne i gotowe do testowania.

**Happy mailing! 📧**
