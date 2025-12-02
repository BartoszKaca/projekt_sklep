# Testowanie Systemu Mailingowego

Ten przewodnik pomoże Ci przetestować wszystkie funkcje systemu mailingowego.

## Wymagania wstępne

1. Skonfiguruj SMTP w pliku `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@rapshop.pl"
MAIL_FROM_NAME="Rap Shop"
```

2. Dla testów zalecamy [Mailtrap](https://mailtrap.io) - darmowy serwis do testowania emaili

## Test przez Artisan Command

Najłatwiejszy sposób testowania emaili:

```bash
# Test weryfikacji emaila
php artisan mail:test verification test@example.com

# Test potwierdzenia zamówienia
php artisan mail:test order-confirmation test@example.com

# Test potwierdzenia płatności
php artisan mail:test payment-confirmation test@example.com

# Test aktualizacji statusu
php artisan mail:test order-status test@example.com

# Test resetowania hasła
php artisan mail:test password-reset test@example.com
```

**Uwaga:** Niektóre testy wymagają istniejących danych w bazie (np. zamówień).

## Test przez Tinker

Alternatywnie możesz użyć Laravel Tinker:

```bash
php artisan tinker
```

### Test weryfikacji emaila

```php
$user = App\Models\User::first();
$url = route('verification.verify', ['id' => $user->id, 'hash' => sha1($user->email)]);
Mail::to('test@example.com')->send(new App\Mail\EmailVerificationMail($user, $url));
```

### Test potwierdzenia zamówienia

```php
$order = App\Models\Order::with(['items.product', 'shipping'])->first();
Mail::to('test@example.com')->send(new App\Mail\OrderConfirmationMail($order));
```

### Test potwierdzenia płatności

```php
$order = App\Models\Order::with(['items.product', 'shipping'])->first();
Mail::to('test@example.com')->send(new App\Mail\PaymentConfirmationMail($order));
```

### Test aktualizacji statusu

```php
$order = App\Models\Order::with(['items.product', 'shipping'])->first();
Mail::to('test@example.com')->send(new App\Mail\OrderStatusUpdateMail($order, 'pending', 'processing'));
```

### Test resetowania hasła

```php
Mail::to('test@example.com')->send(new App\Mail\PasswordResetMail('test-token', 'test@example.com'));
```

## Testy end-to-end

### 1. Test rejestracji użytkownika

1. Otwórz http://localhost/register
2. Wypełnij formularz rejestracyjny
3. Sprawdź Mailtrap - powinien pojawić się email weryfikacyjny
4. Kliknij link weryfikacyjny w emailu

**Oczekiwany rezultat:** Konto zostanie zweryfikowane

### 2. Test złożenia zamówienia

1. Dodaj produkty do koszyka
2. Przejdź do checkout
3. Wypełnij formularz i złóż zamówienie
4. Sprawdź Mailtrap - powinien pojawić się email potwierdzający zamówienie

**Oczekiwany rezultat:** Email z szczegółami zamówienia

### 3. Test płatności PayU

1. Złóż zamówienie z metodą płatności "PayU"
2. Dokończ płatność w sandbox PayU (użyj danych testowych)
3. Sprawdź Mailtrap - powinny pojawić się 2 emaile:
   - Potwierdzenie zamówienia
   - Potwierdzenie płatności

**Dane testowe PayU Sandbox:**
- Karta: 4444 3333 2222 1111
- Data ważności: dowolna przyszła
- CVV: 123

**Oczekiwany rezultat:** 
- Status zamówienia zmieni się na "processing"
- Payment_status zmieni się na "paid"
- Użytkownik otrzyma email z potwierdzeniem płatności

### 4. Test zmiany statusu zamówienia

1. Zaloguj się do panelu admina
2. Przejdź do zamówień
3. Zmień status zamówienia (np. z "processing" na "shipped")
4. Sprawdź Mailtrap - powinien pojawić się email z aktualizacją statusu

**Oczekiwany rezultat:** Email informujący o zmianie statusu

### 5. Test resetowania hasła

1. Przejdź do http://localhost/password/reset
2. Wpisz adres email
3. Sprawdź Mailtrap - powinien pojawić się email z linkiem do resetu
4. Kliknij link i ustaw nowe hasło

**Oczekiwany rezultat:** Hasło zostanie zmienione

## Testowanie PayU Webhook

Webhook PayU wymaga publicznego URL. Dla testów lokalnych możesz użyć:

### Opcja 1: ngrok

```bash
# Zainstaluj ngrok
brew install ngrok  # macOS
# lub pobierz z https://ngrok.com

# Uruchom tunnel
ngrok http 80

# Skopiuj URL (np. https://abc123.ngrok.io)
# Zaktualizuj notifyUrl w PaymentController na ten URL
```

### Opcja 2: Ręczne testowanie webhook

```bash
# Symuluj webhook PayU
curl -X POST http://localhost/payment/notify \
  -H "Content-Type: application/json" \
  -d '{
    "order": {
      "extOrderId": "ORD-20241202-ABC123",
      "status": "COMPLETED"
    }
  }'
```

## Debugowanie problemów

### Email nie wysyła się

1. Sprawdź konfigurację w `.env`
2. Sprawdź logi: `storage/logs/laravel.log`
3. Test połączenia SMTP:
```bash
php artisan tinker
Mail::raw('Test', function($msg) { $msg->to('test@example.com'); });
```

### Email wysyła się ale wygląda źle

1. Sprawdź szablon blade w `resources/views/emails/`
2. Usuń cache widoków: `php artisan view:clear`

### PayU nie aktualizuje statusu

1. Sprawdź logi: `storage/logs/laravel.log`
2. Sprawdź czy webhook URL jest dostępny publicznie
3. Sprawdź konfigurację PayU w `.env`

## Checklist testów

- [ ] Email weryfikacyjny wysyła się po rejestracji
- [ ] Email potwierdzenia zamówienia wysyła się po checkout
- [ ] Email płatności wysyła się po opłaceniu przez PayU
- [ ] Email aktualizacji statusu wysyła się przy zmianie statusu
- [ ] Email resetowania hasła wysyła się i działa
- [ ] Wszystkie emaile mają poprawny format HTML
- [ ] Wszystkie linki w emailach działają
- [ ] Emaile zawierają poprawne dane (zamówienia, użytkownika, etc.)

## Produkcja

Przed wdrożeniem na produkcję:

1. Zmień `MAIL_MAILER` na produkcyjny serwis
2. Skonfiguruj `MAIL_FROM_ADDRESS` na prawdziwy adres
3. Przetestuj wszystkie scenariusze na środowisku staging
4. Skonfiguruj monitoring emaili
5. Ustaw limity wysyłki (rate limiting)

## Wsparcie

W razie problemów:
- Sprawdź dokumentację w `MAILING_SYSTEM.md`
- Sprawdź logi aplikacji
- Sprawdź dokumentację Laravel: https://laravel.com/docs/mail
