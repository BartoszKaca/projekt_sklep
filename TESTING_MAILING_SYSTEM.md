# System Mailingowy - Dokumentacja Testowa

## Przegląd

System mailingowy w Rap Shop obsługuje następujące scenariusze:

1. **Weryfikacja emailowa** - po rejestracji nowego konta
2. **Potwierdzenie zamówienia** - po złożeniu zamówienia
3. **Potwierdzenie płatności** - po opłaceniu zamówienia
4. **Aktualizacje statusu zamówienia** - przy zmianie statusu
5. **Resetowanie hasła** - na żądanie użytkownika

## Konfiguracja

### 1. Konfiguracja SMTP

Plik `.env` zawiera już konfigurację dla Mailtrap:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=bc020a0ee9c722
MAIL_PASSWORD=5cf38349d21ed0
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@rapshop.pl"
MAIL_FROM_NAME="Rap Shop"
```

### 2. Uruchomienie Queue Worker

Maile są wysyłane asynchronicznie przez system kolejek. Uruchom worker:

```bash
php artisan queue:work
```

Lub w trybie deweloperskim:

```bash
php artisan queue:listen
```

## Testowanie funkcjonalności

### 1. Weryfikacja Emailowa

**Przebieg:**
1. Zarejestruj nowe konto w `/register`
2. Po rejestracji użytkownik jest automatycznie zalogowany
3. Email weryfikacyjny zostaje wysłany
4. Sprawdź Mailtrap - powinieneś zobaczyć email z linkiem weryfikacyjnym
5. Kliknij link lub wklej URL z emaila
6. Konto zostanie zweryfikowane

**Testowanie ponownego wysyłania:**
1. Zaloguj się na niezweryfikowane konto
2. Zostaniesz przekierowany na `/email/verify`
3. Kliknij "Wyślij ponownie link weryfikacyjny"
4. Nowy email zostanie wysłany

**Klasy:**
- `App\Mail\EmailVerificationMail`
- `App\Http\Controllers\Auth\RegisterController`
- `App\Http\Controllers\Auth\VerificationController`

### 2. Resetowanie Hasła

**Przebieg:**
1. Przejdź do `/password/reset`
2. Wprowadź adres email
3. Otrzymasz email z linkiem resetującym
4. Kliknij link lub wklej URL
5. Wprowadź nowe hasło (min. 8 znaków)
6. Hasło zostanie zmienione

**Testowanie:**
```bash
# Wejdź na stronę resetowania
curl http://localhost/password/reset

# Wyślij żądanie resetowania
curl -X POST http://localhost/password/email \
  -d "email=test@example.com"
```

**Klasy:**
- `App\Mail\PasswordResetMail`
- `App\Http\Controllers\Auth\ForgotPasswordController`
- `App\Http\Controllers\Auth\ResetPasswordController`

### 3. Potwierdzenie Zamówienia

**Przebieg:**
1. Dodaj produkty do koszyka
2. Przejdź do `/checkout`
3. Wypełnij dane wysyłki i płatności
4. Zatwierdź zamówienie
5. Email potwierdzający zostaje wysłany na adres podany w zamówieniu

**Co zawiera email:**
- Numer zamówienia
- Lista produktów z cenami
- Adres dostawy
- Metoda płatności
- Całkowita kwota
- Dane do przelewu (jeśli wybrano przelew)

**Klasy:**
- `App\Mail\OrderConfirmationMail`
- `App\Http\Controllers\CheckoutController@processOrder`

### 4. Potwierdzenie Płatności

**Przebieg:**
1. Złóż zamówienie z płatnością PayU
2. Po opłaceniu przez PayU, webhook aktualizuje status
3. Gdy `payment_status` zmienia się na `paid`, OrderObserver wysyła email

**Testowanie z PayU (sandbox):**
1. Wybierz płatność PayU podczas checkout
2. Użyj danych testowych PayU:
   - Karta: 4444 3333 2222 1111
   - Data: dowolna przyszła
   - CVV: 123
3. Potwierdź płatność
4. Webhook PayU wywoła `/payment/notify`
5. Email potwierdzenia płatności zostanie wysłany

**Testowanie manualne:**
```php
// W Tinker lub kontrolerze testowym
$order = Order::find(1);
$order->markAsPaid(); // To wywoła OrderObserver i wyśle email
```

**Klasy:**
- `App\Mail\PaymentConfirmationMail`
- `App\Observers\OrderObserver`
- `App\Http\Controllers\PaymentController`

### 5. Aktualizacje Statusu Zamówienia

**Przebieg:**
1. Admin zmienia status zamówienia w panelu `/admin/orders/{id}`
2. Przy każdej zmianie statusu wysyłany jest email

**Dostępne statusy:**
- `pending` → `processing` - "Zamówienie w realizacji"
- `processing` → `shipped` - "Zamówienie wysłane"
- `shipped` → `delivered` - "Zamówienie dostarczone"
- `*` → `cancelled` - "Zamówienie anulowane"

**Testowanie:**
```php
// W Tinker
$order = Order::find(1);
$order->update(['status' => 'shipped']);
// Email zostanie wysłany automatycznie
```

**Klasy:**
- `App\Mail\OrderStatusUpdateMail`
- `App\Observers\OrderObserver`

## Testowanie w Mailtrap

### Logowanie do Mailtrap
1. Otwórz [mailtrap.io](https://mailtrap.io)
2. Zaloguj się (dane dostępowe znajdziesz w .env lub dokumentacji projektu)
3. Wybierz inbox "Rap Shop"

### Sprawdzanie emaili
- Wszystkie wysłane emaile pojawią się w Mailtrap
- Możesz sprawdzić HTML, Plain Text i Raw wersje
- Testuj linki (działają w sandbox)
- Sprawdź attachmenty (jeśli są)
- Zweryfikuj nagłówki (From, To, Subject)

## Uruchomienie migracji

Jeśli dodano nową kolumnę `payu_order_id`:

```bash
php artisan migrate
```

## Monitoring logów

Wszystkie błędy mailingowe są logowane. Sprawdź:

```bash
tail -f storage/logs/laravel.log
```

Logi zawierają:
- Błędy wysyłania emaili
- PayU webhook notifications
- Status changes
- Payment updates

## Częste problemy

### Email nie wysyłany
1. Sprawdź czy queue worker jest uruchomiony: `ps aux | grep queue:work`
2. Sprawdź logi: `storage/logs/laravel.log`
3. Sprawdź konfigurację SMTP w `.env`
4. Sprawdź czy tabela `jobs` ma wpisy: `SELECT * FROM jobs;`

### Link weryfikacyjny wygasł
- Linki są ważne 60 minut
- Użytkownik może poprosić o nowy link w `/email/verify`

### Webhook PayU nie działa
1. W sandbox PayU webhooks mogą nie działać - testuj manualnie
2. Sprawdź logi: `tail -f storage/logs/laravel.log | grep PayU`
3. Zweryfikuj signature key w `.env`

## Przydatne komendy

```bash
# Wyczyść kolejkę
php artisan queue:flush

# Sprawdź failed jobs
php artisan queue:failed

# Ponów failed job
php artisan queue:retry {id}

# Testuj wysyłkę emaila (Tinker)
php artisan tinker
>>> Mail::to('test@example.com')->send(new App\Mail\EmailVerificationMail($user, $url));

# Sprawdź status kolejki
php artisan queue:work --once

# Debug mode - zobacz co się dzieje
php artisan queue:listen --verbose
```

## Struktura plików

```
app/
├── Mail/
│   ├── EmailVerificationMail.php
│   ├── OrderConfirmationMail.php
│   ├── PaymentConfirmationMail.php
│   ├── OrderStatusUpdateMail.php
│   └── PasswordResetMail.php
├── Observers/
│   └── OrderObserver.php
└── Http/Controllers/
    ├── Auth/
    │   ├── RegisterController.php
    │   ├── VerificationController.php
    │   ├── ForgotPasswordController.php
    │   └── ResetPasswordController.php
    ├── CheckoutController.php
    └── PaymentController.php

resources/views/emails/
├── email-verification.blade.php
├── order-confirmation.blade.php
├── payment-confirmation.blade.php
├── order-status-update.blade.php
└── password-reset.blade.php
```

## Checklist testowy

- [ ] Rejestracja nowego użytkownika wysyła email weryfikacyjny
- [ ] Link weryfikacyjny działa i aktywuje konto
- [ ] Ponowne wysyłanie emaila weryfikacyjnego działa
- [ ] Resetowanie hasła wysyła email z linkiem
- [ ] Link resetowania hasła działa i zmienia hasło
- [ ] Złożenie zamówienia wysyła email potwierdzający
- [ ] Email zawiera wszystkie szczegóły zamówienia
- [ ] Opłacenie zamówienia wysyła email potwierdzenia płatności
- [ ] Zmiana statusu zamówienia wysyła email z aktualizacją
- [ ] Wszystkie emaile mają poprawny design i są responsywne
- [ ] Linki w emailach działają poprawnie
- [ ] Dane osobowe są prawidłowo wyświetlane

## Uwagi końcowe

- Wszystkie emaile używają polskiego języka
- Design jest spójny z identyfikacją wizualną Rap Shop
- Emaile są responsywne i działają na urządzeniach mobilnych
- System używa kolejki (queue) dla lepszej wydajności
- Wszystkie błędy są logowane dla łatwego debugowania
