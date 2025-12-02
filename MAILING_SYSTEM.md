# System Mailingowy - Dokumentacja

## Przegląd

System mailingowy został w pełni zintegrowany z projektem Laravel. Automatycznie wysyła emaile w następujących sytuacjach:

1. **Weryfikacja emaila** - po rejestracji nowego konta
2. **Potwierdzenie zamówienia** - po złożeniu zamówienia
3. **Potwierdzenie płatności** - po opłaceniu zamówienia (PayU)
4. **Aktualizacja statusu zamówienia** - przy każdej zmianie statusu
5. **Reset hasła** - podczas procesu resetowania hasła

## Konfiguracja

### 1. Konfiguracja SMTP (plik .env)

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io  # Zmień na produkcyjny serwer SMTP
MAIL_PORT=2525                       # Port SMTP (zazwyczaj 587 dla TLS)
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@rapshop.pl"
MAIL_FROM_NAME="Rap Shop"
```

### 2. Mailtrap (Rozwój)

Dla środowiska rozwojowego zalecamy używanie [Mailtrap](https://mailtrap.io):
- Zarejestruj się na mailtrap.io
- Skopiuj dane SMTP do pliku .env
- Wszystkie emaile będą przechwytywane przez Mailtrap

### 3. Produkcja

Dla produkcji użyj jednego z:
- **Gmail** (dla małych projektów)
- **SendGrid** (do 100 emaili dziennie za darmo)
- **Amazon SES** (bardzo tani, wymagana weryfikacja)
- **Mailgun** (pierwszy miesiąc za darmo)

## Utworzone klasy Mail

### 1. EmailVerificationMail
**Lokalizacja:** `app/Mail/EmailVerificationMail.php`
**Szablon:** `resources/views/emails/email-verification.blade.php`
**Kiedy wysyłany:** Po rejestracji użytkownika
**Parametry:** User, verificationUrl

### 2. OrderConfirmationMail
**Lokalizacja:** `app/Mail/OrderConfirmationMail.php`
**Szablon:** `resources/views/emails/order-confirmation.blade.php`
**Kiedy wysyłany:** Po pomyślnym złożeniu zamówienia
**Parametry:** Order

### 3. PaymentConfirmationMail (NOWY)
**Lokalizacja:** `app/Mail/PaymentConfirmationMail.php`
**Szablon:** `resources/views/emails/payment-confirmation.blade.php`
**Kiedy wysyłany:** Po potwierdzeniu płatności przez PayU
**Parametry:** Order

### 4. OrderStatusUpdateMail (NOWY)
**Lokalizacja:** `app/Mail/OrderStatusUpdateMail.php`
**Szablon:** `resources/views/emails/order-status-update.blade.php`
**Kiedy wysyłany:** Przy każdej zmianie statusu zamówienia
**Parametry:** Order, oldStatus, newStatus

### 5. PasswordResetMail (NOWY)
**Lokalizacja:** `app/Mail/PasswordResetMail.php`
**Szablon:** `resources/views/emails/password-reset.blade.php`
**Kiedy wysyłany:** Po prośbie o reset hasła
**Parametry:** token, email

## OrderObserver

**Lokalizacja:** `app/Observers/OrderObserver.php`

OrderObserver automatycznie monitoruje zmiany w zamówieniach i wysyła odpowiednie emaile:

- **Zmiana statusu** (pending → processing → shipped → delivered) - wysyła OrderStatusUpdateMail
- **Zmiana payment_status na 'paid'** - wysyła PaymentConfirmationMail

Observer jest zarejestrowany w `app/Providers/AppServiceProvider.php`.

## Integracja z PayU

### Poprawki w PaymentController

1. **Lepsza obsługa statusów PayU:**
   - COMPLETED → markAsPaid() + status = 'processing' (wysyła email płatności)
   - CANCELED → payment_status = 'failed' + status = 'cancelled'
   - PENDING → payment_status = 'pending'
   - WAITING_FOR_CONFIRMATION → payment_status = 'pending'

2. **Nowa metoda checkStatus():**
   - Endpoint: `/payment/check-status/{order}`
   - Pozwala na polling statusu płatności z frontendu

3. **Lepsze logowanie:**
   - Wszystkie akcje PayU są logowane
   - Łatwiejsze debugowanie problemów z płatnościami

## Testowanie

### 1. Test weryfikacji emaila

```bash
# Zarejestruj nowego użytkownika przez formularz
# Sprawdź Mailtrap - powinien pojawić się email weryfikacyjny
```

### 2. Test potwierdzenia zamówienia

```bash
# Złóż zamówienie przez checkout
# Sprawdź Mailtrap - powinien pojawić się email z potwierdzeniem
```

### 3. Test płatności PayU

```bash
# Złóż zamówienie z metodą płatności PayU
# Dokończ płatność w sandbox PayU
# Sprawdź Mailtrap - powinien pojawić się email potwierdzenia płatności
```

### 4. Test aktualizacji statusu

```bash
# W panelu admina zmień status zamówienia
# Sprawdź Mailtrap - powinien pojawić się email z aktualizacją statusu
```

### 5. Test resetowania hasła

```bash
# Użyj formularza "Forgot Password"
# Sprawdź Mailtrap - powinien pojawić się email z linkiem do resetu
```

## Kolejkowanie emaili (opcjonalne)

Dla lepszej wydajności można skonfigurować kolejkowanie emaili:

### 1. Uruchom worker kolejki

```bash
php artisan queue:work
```

### 2. Zmień klasy Mail, aby implementowały ShouldQueue

```php
class OrderConfirmationMail extends Mailable implements ShouldQueue
{
    // ...
}
```

### 3. Konfiguracja Supervisor (produkcja)

Supervisor utrzyma worker kolejki zawsze uruchomiony:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/worker.log
```

## Struktura szablonów emaili

Wszystkie szablony są zgodne stylistycznie i zawierają:

- **Gradient header** - charakterystyczny dla Rap Shop
- **Responsywny design** - działa na mobilnych i desktop
- **Czytelny layout** - tabele, boxe, wyróżnienia
- **Brand identity** - ikony, kolory, typography
- **Footer** - informacje o sklepie i copyright

### Przykładowa struktura:

```html
<!DOCTYPE html>
<html lang="pl">
<head>
    <!-- Styles -->
</head>
<body>
    <div class="container">
        <div class="header"><!-- Gradient header --></div>
        <div class="content"><!-- Main content --></div>
        <div class="footer"><!-- Footer --></div>
    </div>
</body>
</html>
```

## Statusy zamówień

System obsługuje następujące statusy:

- **pending** - Oczekujące (nowe zamówienie)
- **processing** - W realizacji (opłacone, pakowane)
- **shipped** - Wysłane (przekazane kurierowi)
- **delivered** - Dostarczone (odebrane przez klienta)
- **cancelled** - Anulowane

Każda zmiana statusu automatycznie wysyła email do klienta.

## Troubleshooting

### Email nie wysyła się

1. Sprawdź konfigurację SMTP w .env
2. Sprawdź logi: `storage/logs/laravel.log`
3. Przetestuj połączenie SMTP:
   ```bash
   php artisan tinker
   Mail::raw('Test', function($msg) { $msg->to('test@example.com'); });
   ```

### Email wysyła się, ale nie dociera

1. Sprawdź folder SPAM
2. Sprawdź czy MAIL_FROM_ADDRESS jest poprawny
3. Skonfiguruj SPF i DKIM (produkcja)

### PayU nie aktualizuje statusu

1. Sprawdź logi PayU w `storage/logs/laravel.log`
2. Sprawdź czy route 'payment.notify' jest dostępny publicznie
3. Sprawdź konfigurację notifyUrl w PayU dashboard

## Produkcja - Checklist

- [ ] Zmień MAIL_MAILER na produkcyjny serwer SMTP
- [ ] Skonfiguruj MAIL_FROM_ADDRESS na prawdziwy email
- [ ] Ustaw APP_ENV=production
- [ ] Skonfiguruj SPF, DKIM i DMARC dla domeny
- [ ] Przetestuj wszystkie scenariusze mailowe
- [ ] Skonfiguruj monitoring emaili
- [ ] Ustaw limity wysyłki (rate limiting)
- [ ] Skonfiguruj kolejkowanie emaili
- [ ] Uruchom Supervisor dla worker'ów kolejki

## Wsparcie

W razie problemów:
- Sprawdź dokumentację Laravel: https://laravel.com/docs/mail
- Sprawdź logi aplikacji
- Skontaktuj się z zespołem deweloperskim
