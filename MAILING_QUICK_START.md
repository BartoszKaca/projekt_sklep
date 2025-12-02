# 🎵 Rap Shop - System Mailingowy

## ✅ Co zostało zaimplementowane

System mailingowy został w pełni zintegrowany z projektem i obejmuje:

### 1. **Weryfikacja emailowa (Email Verification)**
- ✉️ Automatyczne wysyłanie emaila po rejestracji
- 🔗 Link weryfikacyjny ważny 60 minut
- ♻️ Możliwość ponownego wysłania linku
- 🎨 Responsywny design emaila

**Pliki:**
- `app/Http/Controllers/Auth/RegisterController.php` - wysyłanie przy rejestracji
- `app/Http/Controllers/Auth/VerificationController.php` - obsługa weryfikacji
- `app/Mail/EmailVerificationMail.php` - klasa mailowa
- `resources/views/emails/email-verification.blade.php` - template emaila
- `resources/views/auth/verify.blade.php` - strona weryfikacji

### 2. **Resetowanie hasła (Password Reset)**
- 🔐 Bezpieczne resetowanie hasła
- 📧 Email z linkiem resetującym
- ⏱️ Token ważny 60 minut
- 🔄 System throttling (1 request/minute)

**Pliki:**
- `app/Http/Controllers/Auth/ForgotPasswordController.php` - żądanie resetu
- `app/Http/Controllers/Auth/ResetPasswordController.php` - ustawienie nowego hasła
- `app/Mail/PasswordResetMail.php` - klasa mailowa
- `resources/views/emails/password-reset.blade.php` - template emaila
- `resources/views/auth/passwords/email.blade.php` - formularz żądania
- `resources/views/auth/passwords/reset.blade.php` - formularz nowego hasła

### 3. **Potwierdzenie zamówienia (Order Confirmation)**
- 📦 Automatyczny email po złożeniu zamówienia
- 📋 Pełne szczegóły zamówienia
- 💳 Informacje o płatności
- 🚚 Dane dostawy

**Pliki:**
- `app/Mail/OrderConfirmationMail.php` - klasa mailowa
- `resources/views/emails/order-confirmation.blade.php` - template emaila
- `app/Http/Controllers/CheckoutController.php` - wysyłanie po checkout

### 4. **Potwierdzenie płatności (Payment Confirmation)**
- ✅ Email po udanej płatności
- 💰 Podsumowanie transakcji
- 📊 Status płatności
- 🔄 Integracja z PayU webhook

**Pliki:**
- `app/Mail/PaymentConfirmationMail.php` - klasa mailowa
- `resources/views/emails/payment-confirmation.blade.php` - template emaila
- `app/Http/Controllers/PaymentController.php` - obsługa PayU
- `app/Observers/OrderObserver.php` - automatyczne wysyłanie

### 5. **Aktualizacje statusu zamówienia (Order Status Updates)**
- 🔔 Automatyczne powiadomienia o zmianach statusu
- 📍 Informacje o śledzeniu przesyłki
- 📦 Statusy: pending → processing → shipped → delivered
- ❌ Powiadomienia o anulowaniu

**Pliki:**
- `app/Mail/OrderStatusUpdateMail.php` - klasa mailowa
- `resources/views/emails/order-status-update.blade.php` - template emaila
- `app/Observers/OrderObserver.php` - automatyczne wysyłanie przy zmianie

## 🚀 Szybki Start

### 1. Uruchom migrację (jeśli jeszcze nie)
```bash
php artisan migrate
```

### 2. Uruchom Queue Worker
System używa kolejek dla wydajności:
```bash
php artisan queue:work
```

Lub w trybie deweloperskim (automatyczny restart):
```bash
php artisan queue:listen
```

### 3. Testowanie w Mailtrap

Wszystkie emaile trafiają do Mailtrap (sandbox SMTP):
- Host: `sandbox.smtp.mailtrap.io`
- Login: `bc020a0ee9c722`
- Hasło: `5cf38349d21ed0`

Zaloguj się na [mailtrap.io](https://mailtrap.io) aby zobaczyć wysłane emaile.

## 📝 Testowanie poszczególnych funkcji

### Test 1: Weryfikacja Emailowa
```bash
# 1. Zarejestruj nowe konto
Przejdź do: http://localhost/register

# 2. Wypełnij formularz i zatwierdź

# 3. Sprawdź Mailtrap - pojawi się email weryfikacyjny

# 4. Kliknij link lub wklej URL z emaila

# 5. Konto zostanie zweryfikowane
```

### Test 2: Resetowanie Hasła
```bash
# 1. Przejdź do strony resetowania
http://localhost/password/reset

# 2. Wprowadź email i wyślij

# 3. Sprawdź Mailtrap - pojawi się email

# 4. Kliknij link i wprowadź nowe hasło

# 5. Zaloguj się nowym hasłem
```

### Test 3: Potwierdzenie Zamówienia
```bash
# 1. Dodaj produkty do koszyka

# 2. Przejdź do checkout: http://localhost/checkout

# 3. Wypełnij dane i zatwierdź zamówienie

# 4. Sprawdź Mailtrap - email potwierdzający zostanie wysłany
```

### Test 4: Płatność PayU (sandbox)
```bash
# 1. W checkout wybierz płatność PayU

# 2. Użyj testowej karty:
Numer: 4444 3333 2222 1111
Data: dowolna przyszła (np. 12/25)
CVV: 123

# 3. Potwierdź płatność

# 4. Zostaniesz przekierowany z powrotem

# 5. Sprawdź Mailtrap - email potwierdzenia płatności
```

### Test 5: Zmiana Statusu Zamówienia
```bash
# Jako admin w panelu:
# 1. Przejdź do: http://localhost/admin/orders

# 2. Wybierz zamówienie

# 3. Zmień status (np. z 'pending' na 'processing')

# 4. Sprawdź Mailtrap - email aktualizacji statusu
```

## 🔧 Konfiguracja

### Zmiana nadawcy emaili
W pliku `.env`:
```env
MAIL_FROM_ADDRESS="twoj-email@rapshop.pl"
MAIL_FROM_NAME="Twoja Nazwa Sklepu"
```

### Zmiana czasu ważności tokenów
W pliku `config/auth.php`:
```php
'passwords' => [
    'users' => [
        'expire' => 60, // czas w minutach
    ],
],
```

## 📊 Monitoring

### Sprawdzanie kolejki
```bash
# Zobacz pending jobs
php artisan queue:work --once

# Zobacz failed jobs
php artisan queue:failed

# Ponów failed job
php artisan queue:retry {id}

# Wyczyść wszystkie failed jobs
php artisan queue:flush
```

### Sprawdzanie logów
```bash
# Live monitoring
tail -f storage/logs/laravel.log

# Grep dla emaili
tail -f storage/logs/laravel.log | grep "mail\|Mail"

# Grep dla PayU
tail -f storage/logs/laravel.log | grep "PayU"
```

## 🐛 Rozwiązywanie problemów

### Email nie wysyłany?
1. Sprawdź czy queue worker działa: `ps aux | grep queue`
2. Sprawdź logi: `tail -f storage/logs/laravel.log`
3. Sprawdź tabelę jobs: `SELECT * FROM jobs;` (powinno być puste po wysłaniu)
4. Sprawdź failed_jobs: `SELECT * FROM failed_jobs;`

### Link weryfikacyjny nie działa?
- Link jest ważny tylko 60 minut
- Użytkownik może poprosić o nowy link na `/email/verify`
- Sprawdź czy APP_URL w `.env` jest poprawny

### PayU webhook nie działa?
- W środowisku sandbox webhooks mogą nie działać - testuj manualnie
- Sprawdź logi PayU: `tail -f storage/logs/laravel.log | grep PayU`
- Zweryfikuj signature key w `.env`

## 📁 Struktura plików

```
app/
├── Mail/                           # Klasy emaili
│   ├── EmailVerificationMail.php
│   ├── OrderConfirmationMail.php
│   ├── PaymentConfirmationMail.php
│   ├── OrderStatusUpdateMail.php
│   └── PasswordResetMail.php
│
├── Observers/                      # Automatyczne akcje
│   └── OrderObserver.php
│
└── Http/Controllers/
    ├── Auth/                       # Kontrolery Auth
    │   ├── RegisterController.php
    │   ├── VerificationController.php
    │   ├── ForgotPasswordController.php
    │   └── ResetPasswordController.php
    ├── CheckoutController.php      # Zamówienia
    └── PaymentController.php       # Płatności

resources/views/
├── emails/                         # Template emaili
│   ├── email-verification.blade.php
│   ├── order-confirmation.blade.php
│   ├── payment-confirmation.blade.php
│   ├── order-status-update.blade.php
│   └── password-reset.blade.php
│
└── auth/                          # Widoki autoryzacji
    ├── verify.blade.php
    └── passwords/
        ├── email.blade.php
        └── reset.blade.php

database/migrations/
└── 2025_12_02_000001_add_payu_order_id_to_orders_table.php
```

## ✨ Funkcje

### OrderObserver
Automatycznie reaguje na zmiany w zamówieniach:
- **Zmiana statusu** → wysyła OrderStatusUpdateMail
- **Zmiana payment_status na 'paid'** → wysyła PaymentConfirmationMail

### PaymentController
Obsługuje integrację PayU:
- Inicjalizuje płatność
- Odbiera webhook notifications
- Aktualizuje status zamówienia i płatności
- Loguje wszystkie akcje

### Order Model
Nowe metody pomocnicze:
- `markAsPaid()` - oznacz jako opłacone
- `markAsShipped($tracking, $carrier)` - oznacz jako wysłane
- `markAsDelivered()` - oznacz jako dostarczone
- `isPaid()` - sprawdź czy opłacone
- `canBeCancelled()` - sprawdź czy można anulować

## 📞 Dodatkowe informacje

### Queue Configuration
System używa `database` jako driver kolejki. Jobs są przechowywane w tabeli `jobs`.

### Email Design
Wszystkie emaile:
- Są w pełni responsywne
- Używają gradientów brand Rap Shop
- Mają spójny design
- Zawierają emoji dla lepszej czytelności
- Mają fallback dla klientów bez wsparcia CSS

### Bezpieczeństwo
- Linki weryfikacyjne są signed i mają timestamp
- Tokeny resetowania hasła są hashowane
- Throttling chroni przed spam
- Payment webhooks są weryfikowane sygnaturą

## 🎯 Checklist wdrożenia

- [x] Weryfikacja emailowa przy rejestracji
- [x] Resetowanie hasła
- [x] Potwierdzenie zamówienia
- [x] Potwierdzenie płatności
- [x] Aktualizacje statusu zamówienia
- [x] Integracja z PayU
- [x] OrderObserver dla automatyzacji
- [x] Responsywne template emaili
- [x] Logging i error handling
- [x] Queue dla wydajności
- [x] Dokumentacja

## 📚 Więcej informacji

Zobacz szczegółową dokumentację testową:
- `TESTING_MAILING_SYSTEM.md` - szczegółowe testy
- `MAILING_SYSTEM.md` - ogólny przegląd (jeśli istnieje)

---

**Projekt gotowy do testowania! 🚀**

Jeśli masz pytania lub problemy, sprawdź logi lub dokumentację testową.
