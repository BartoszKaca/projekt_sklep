# RAP SHOP - Pełna Dokumentacja

## Spis treści
1. [Opis projektu](#opis-projektu)
2. [Szybki start](#szybki-start)
3. [Struktura projektu](#struktura-projektu)
4. [Główne modele](#główne-modele)
5. [API i trasy](#api-i-trasy)
6. [System mailingowy](#system-mailingowy)
7. [Panel administracyjny](#panel-administracyjny)
8. [Wdrażanie](#wdrażanie)

---

## Opis projektu

**RAP SHOP** to nowoczesny sklep internetowy zbudowany w Laravel 11, specjalizujący się w sprzedaży płyt CD, vinylu i merchandisingu związanego z polską sceną hip-hopową.

### Główne funkcje:
- Katalog produktów z wyszukiwaniem, filtrowaniem i sortowaniem
- System koszyka z obsługą wariantów i kuponów rabatowych
- Pełny system zarządzania kontami użytkowników
- Historia zamówień i lista życzeń (wishlist)
- Zarządzanie zapasami magazynowymi
- Panel administracyjny z dashboard'em i raportami
- System mailingowy z automatycznymi powiadomieniami
- Integracja z PayU do obsługi płatności

### Technologie:
- **Backend:** Laravel 11, PHP 8.2+
- **Frontend:** Blade templates, JavaScript, Tailwind CSS
- **Baza danych:** MySQL 8.0+
- **Płatności:** PayU
- **Email:** SMTP (Mailtrap dla dewelopmentu)

---

## Szybki start

### Wymagania:
- PHP >= 8.2
- Composer >= 2.5
- MySQL >= 8.0
- Node.js >= 18.x
- NPM >= 9.x

### Instalacja:

```bash
# 1. Klonuj repozytorium
git clone [URL_REPOZYTORIUM]
cd sklep

# 2. Zainstaluj zależności
composer install
npm install

# 3. Skopiuj zmienne środowiskowe
cp .env.example .env

# 4. Wygeneruj klucz aplikacji
php artisan key:generate

# 5. Uruchom migracje bazy danych
php artisan migrate
php artisan db:seed

# 6. Utwórz linki do storage
php artisan storage:link

# 7. Uruchom build frontendu
npm run dev

# 8. Uruchom serwer
php artisan serve
```

Aplikacja będzie dostępna na: `http://localhost:8000`

### Konfiguracja zmiennych (.env):

```env
# Aplikacja
APP_NAME="Rap Shop"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Baza danych
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rap_shop
DB_USERNAME=root
DB_PASSWORD=

# Email (Mailtrap dla dewelopmentu)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@rapshop.pl"
MAIL_FROM_NAME="Rap Shop"

# PayU (sandbox dla testów)
PAYU_ENVIRONMENT=sandbox
PAYU_POS_ID=145227
PAYU_SIGNATURE_KEY=13a980d4e4f7e9d7ac078e2f6d1c3b4d
PAYU_CLIENT_ID=your_client_id
PAYU_CLIENT_SECRET=your_client_secret
PAYU_NOTIFY_URL=/payment/notify
PAYU_RETURN_URL=/payment/return
```

---

## Struktura projektu

```
sklep/
├── app/
│   ├── Http/
│   │   ├── Controllers/         # Kontrolery aplikacji
│   │   │   ├── Admin/          # Panel administracyjny
│   │   │   ├── Auth/           # Autoryzacja (rejestracja, logowanie)
│   │   │   ├── HomeController.php
│   │   │   ├── CartController.php
│   │   │   ├── AccountController.php
│   │   │   └── PaymentController.php
│   │   ├── Middleware/          # Middleware
│   │   └── Requests/            # Validacja formularzy
│   ├── Models/                  # Modele Eloquent
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Order.php
│   │   ├── Wishlist.php
│   │   └── ...
│   ├── Mail/                    # Klasy mailowe
│   │   ├── EmailVerificationMail.php
│   │   ├── OrderConfirmationMail.php
│   │   ├── PaymentConfirmationMail.php
│   │   ├── OrderStatusUpdateMail.php
│   │   └── PasswordResetMail.php
│   ├── Observers/               # Obserwatorzy zdarzeń
│   │   └── OrderObserver.php
│   └── Providers/               # Service providers
│
├── database/
│   ├── migrations/              # Migracje bazy danych
│   ├── seeders/                # Seedery (dane testowe)
│   └── factories/              # Fabryki do testów
│
├── resources/
│   ├── views/                   # Szablony Blade
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   └── shop.blade.php
│   │   ├── admin/              # Widoki panelu admin
│   │   ├── account/            # Widoki konta użytkownika
│   │   ├── cart/               # Widoki koszyka
│   │   ├── checkout/           # Widoki zamówienia
│   │   ├── products/           # Widoki produktów
│   │   ├── auth/               # Widoki rejestracji/logowania
│   │   ├── emails/             # Szablony emaili
│   │   └── home.blade.php
│   ├── css/
│   │   ├── app.css
│   │   ├── shop.css
│   │   ├── products.css
│   │   ├── footer.css
│   │   └── utilities.css
│   └── js/
│       └── app.js
│
├── routes/
│   └── web.php                  # Definicje tras (URL-i)
│
├── config/
│   ├── app.php                  # Konfiguracja aplikacji
│   ├── database.php             # Konfiguracja bazy
│   ├── mail.php                 # Konfiguracja emaila
│   ├── payu.php                 # Konfiguracja PayU
│   └── ...
│
├── storage/
│   ├── app/                     # Pliki użytkowników
│   ├── logs/                    # Logi aplikacji
│   └── framework/
│
├── tests/                       # Testy
│   ├── Feature/
│   └── Unit/
│
├── public/
│   ├── index.php
│   ├── css/
│   ├── js/
│   └── build/                   # Skompilowane assety
│
└── docker/                      # Pliki Docker

```

---

## Główne modele

### User (Użytkownik)
```php
- id: ID
- name: Imię i nazwisko
- email: Email
- email_verified_at: Data weryfikacji
- password: Hasło (haszowane)
- role: Rola (user, admin)
- timestamps: Data utworzenia/aktualizacji

Relacje:
- orders() - Zamówienia użytkownika
- addresses() - Adresy dostawy
- wishlist() - Lista życzeń
- newsletter_subscriber - Subskrypcja newslettera
```

### Product (Produkt)
```php
- id: ID
- name: Nazwa produktu
- slug: URL-przyjazna nazwa
- description: Opis
- price: Cena
- discount_price: Cena po rabacie
- stock_quantity: Ilość w magazynie
- category_id: Kategoria
- created_at/updated_at: Daty

Relacje:
- category() - Kategoria produktu
- images() - Zdjęcia produktu
- variants() - Warianty (wersje)
- orderItems() - Pozycje w zamówieniach
- wishlists() - Na listach życzeń użytkowników
```

### Category (Kategoria)
```php
- id: ID
- name: Nazwa kategorii
- slug: URL-przyjazna nazwa
- parent_id: Kategoria nadrzędna (dla podkategorii)

Relacje:
- products() - Produkty w kategorii
- children() - Podkategorie
```

### Order (Zamówienie)
```php
- id: ID
- order_number: Numer zamówienia (unikalny)
- user_id: Użytkownik
- status: Status (pending, processing, shipped, delivered, cancelled)
- payment_status: Status płatności (pending, paid, failed)
- payment_method: Metoda płatności (card, transfer, paypal)
- total: Całkowita kwota
- shipping_address_id: Adres dostawy
- created_at/updated_at: Daty

Relacje:
- user() - Użytkownik
- items() - Pozycje w zamówieniu
- shipping() - Dane wysyłki
```

### OrderItem (Pozycja w zamówieniu)
```php
- id: ID
- order_id: Zamówienie
- product_id: Produkt
- product_variant_id: Wariant produktu
- quantity: Ilość
- price: Cena jednostkowa
- subtotal: Razem za pozycję

Relacje:
- order() - Zamówienie
- product() - Produkt
```

### Wishlist (Lista życzeń)
```php
- id: ID
- user_id: Użytkownik
- product_id: Produkt
- created_at: Data dodania

Relacje:
- user() - Użytkownik
- product() - Produkt
```

### Address (Adres dostawy)
```php
- id: ID
- user_id: Użytkownik
- name: Nazwa adresu
- street: Ulica
- city: Miasto
- postal_code: Kod pocztowy
- country: Kraj
- phone: Telefon

Relacje:
- user() - Użytkownik
```

### NewsletterSubscriber (Subskrybent newslettera)
```php
- id: ID
- email: Email
- subscribed_at: Data subskrypcji
- unsubscribed_at: Data rezygnacji
```

---

## API i trasy

### Strony publiczne

```
GET  /                          Strona główna
GET  /produkty                  Lista produktów
GET  /produkt/{slug}            Szczegóły produktu
GET  /kategoria/{slug}          Produkty w kategorii
```

### Koszyk (bez logowania)

```
GET  /cart                      Wyświetl koszyk
POST /cart/add                  Dodaj produkt do koszyka
POST /cart/update               Aktualizuj ilość
POST /cart/remove               Usuń z koszyka
```

### Autoryzacja

```
GET  /register                  Formularz rejestracji
POST /register                  Prześlij rejestrację
GET  /login                     Formularz logowania
POST /login                     Prześlij logowanie
POST /logout                    Wyloguj

GET  /password/reset            Formularz resetu hasła
POST /password/email            Wyślij email reset
GET  /password/reset/{token}    Formularz zmiany hasła
POST /password/update           Zmień hasło

GET  /email/verify              Weryfikacja emaila
GET  /verify/{token}            Potwierdź email
POST /verify/resend             Wyślij ponownie link weryfikacji
```

### Konto użytkownika (auth)

```
GET  /account                   Dashboard użytkownika
GET  /account/profile           Profil
POST /account/profile/update    Aktualizuj profil
GET  /account/password          Zmiana hasła
POST /account/password/update   Aktualizuj hasło

GET  /account/orders            Zamówienia
GET  /account/orders/{id}       Szczegóły zamówienia

GET  /account/addresses         Adresy dostawy
POST /account/addresses/store   Dodaj adres
POST /account/addresses/{id}/update  Aktualizuj adres
POST /account/addresses/{id}/delete  Usuń adres

GET  /account/wishlist          Lista życzeń
POST /account/wishlist/add      Dodaj do wishlist (JSON)
POST /account/wishlist/remove   Usuń z wishlist (JSON)
```

### Koszyk i checkout (auth)

```
GET  /checkout                  Formularz zamówienia
POST /checkout                  Prześlij zamówienie
```

### Płatności

```
GET  /payment/return            Powrót z PayU
POST /payment/notify            Notifikacja od PayU
GET  /payment/check-status/{id} Sprawdź status płatności
```

### Panel administracyjny (auth + admin)

```
GET  /admin                         Dashboard
GET  /admin/products                Lista produktów
GET  /admin/products/create         Formularz tworzenia
POST /admin/products                Utwórz produkt
GET  /admin/products/{id}/edit      Formularz edycji
POST /admin/products/{id}           Aktualizuj produkt
POST /admin/products/{id}/delete    Usuń produkt

GET  /admin/categories              Lista kategorii
POST /admin/categories              Utwórz kategorię
POST /admin/categories/{id}         Aktualizuj kategorię
POST /admin/categories/{id}/delete  Usuń kategorię

GET  /admin/orders                  Lista zamówień
GET  /admin/orders/{id}             Szczegóły zamówienia
POST /admin/orders/{id}/status      Zmień status
POST /admin/orders/{id}/payment     Zmień status płatności

GET  /admin/stock                   Zarządzanie zapasami
POST /admin/stock/{id}/update       Aktualizuj ilość

GET  /admin/users                   Lista użytkowników
POST /admin/users/{id}/role         Zmień rolę
```

### Newsletter

```
POST /newsletter/subscribe      Subskrybuj newsletter
POST /newsletter/unsubscribe    Rezygnuj z newslettera
GET  /newsletter/unsubscribe/{token}  Link wypisania się
```

---

## System mailingowy

### Czym jest system mailingowy?

System automatycznie wysyła emaile do użytkowników i klientów w następujących sytuacjach:

1. **Weryfikacja emaila** - Po rejestracji nowego konta
2. **Potwierdzenie zamówienia** - Po złożeniu zamówienia
3. **Potwierdzenie płatności** - Po opłaceniu zamówienia przez PayU
4. **Aktualizacja statusu** - Przy każdej zmianie statusu zamówienia
5. **Reset hasła** - Podczas resetowania hasła

### Klasy mailingowe

#### EmailVerificationMail
- **Kiedy wysyłana:** Po rejestracji użytkownika
- **Parametry:** User, verification token
- **Szablon:** `resources/views/emails/email-verification.blade.php`
- **Działanie:** Wysyła link do potwierdzenia emaila

#### OrderConfirmationMail
- **Kiedy wysyłana:** Po złożeniu zamówienia
- **Parametry:** Order
- **Szablon:** `resources/views/emails/order-confirmation.blade.php`
- **Działanie:** Potwierdzenie zamówienia z numerem i szczegółami

#### PaymentConfirmationMail
- **Kiedy wysyłana:** Po opłaceniu zamówienia
- **Parametry:** Order
- **Szablon:** `resources/views/emails/payment-confirmation.blade.php`
- **Działanie:** Potwierdzenie płatności i info o wysyłce

#### OrderStatusUpdateMail
- **Kiedy wysyłana:** Przy zmianie statusu zamówienia
- **Parametry:** Order, old status, new status
- **Szablon:** `resources/views/emails/order-status-update.blade.php`
- **Działanie:** Powiadamia o zmianie statusu (np. wysłane, dostarczone)

#### PasswordResetMail
- **Kiedy wysyłana:** Po prośbie o reset hasła
- **Parametry:** Reset token, email
- **Szablon:** `resources/views/emails/password-reset.blade.php`
- **Działanie:** Link do resetowania hasła

### Obserwator OrderObserver

Plik: `app/Observers/OrderObserver.php`

Automatycznie monitoruje zmiany w zamówieniach i wysyła odpowiednie emaile:

- Zmiana statusu → wysyła `OrderStatusUpdateMail`
- Zmiana payment_status na "paid" → wysyła `PaymentConfirmationMail`

### Testowanie systemu mailingowego

#### 1. Konfiguracja Mailtrap

Mailtrap przechwytuje wszystkie emaile w środowisku testowym:

1. Zarejestruj się na [mailtrap.io](https://mailtrap.io)
2. Skopiuj dane SMTP do `.env`:

```env
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

3. Wszystkie emaile będą dostępne w panelu Mailtrap

#### 2. Test weryfikacji emaila

```bash
# 1. Zarejestruj się na http://localhost:8000/register
# 2. Sprawdź Mailtrap - powinien być email weryfikacyjny
# 3. Kliknij link weryfikacji
# 4. Email powinien być zweryfikowany
```

#### 3. Test potwierdzenia zamówienia

```bash
# 1. Zaloguj się na konto
# 2. Dodaj produkty do koszyka
# 3. Przejdź do checkout
# 4. Złóż zamówienie
# 5. Sprawdź Mailtrap - powinien być email potwierdzenia
```

#### 4. Test płatności PayU

```bash
# 1. Złóż zamówienie
# 2. Wybierz PayU jako metodę płatności
# 3. Dokończ płatność w sandbox PayU
# 4. Sprawdź Mailtrap - powinien być email potwierdzenia płatności
```

#### 5. Test zmiany statusu

```bash
# 1. W panelu admina otwórz zamówienie
# 2. Zmień status z "pending" na "processing"
# 3. Sprawdź Mailtrap - powinien być email powiadomienia
```

### Kolejkowanie emaili (dla produkcji)

Dla lepszej wydajności emaile mogą być wysyłane asynchronicznie.

#### Uruchomienie worker'a kolejki:

```bash
# W trybie deweloperskim
php artisan queue:listen

# W tle
php artisan queue:work
```

#### Konfiguracja dla Supervisor (produkcja):

```ini
[program:rapshop-queue]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/php /var/www/rapshop/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
```

---

## Panel administracyjny

### Dostęp

Panel administracyjny: `/admin` (wymaga roli `admin`)

### Tworzenie konta administratora

```bash
php artisan tinker
> $user = User::find(1);
> $user->role = 'admin';
> $user->save();
```

### Funkcje panelu

- **Dashboard** - Przegląd statystyk sprzedaży
- **Produkty** - CRUD produktów z obrazami i wariantami
- **Kategorie** - Zarządzanie kategoriami
- **Zamówienia** - Przeglądanie i zmiana statusu
- **Zapasy** - Zarządzanie ilościami w magazynie
- **Użytkownicy** - Zarządzanie kontami i rolami
- **Raporty** - Statystyki sprzedaży

---

## Wdrażanie

### Środowisko produkcyjne

#### 1. Zmień konfigurację .env

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://rapshop.pl

# Baza danych - serwer produkcyjny
DB_HOST=prod-db-server.example.com
DB_DATABASE=rap_shop_prod
DB_USERNAME=prod_user
DB_PASSWORD=very_strong_password

# Email - użyj produkcyjnego SMTP
# Rekomendujemy: SendGrid, Mailgun, Amazon SES

# PayU - produkcyjne dane
PAYU_ENVIRONMENT=production
PAYU_POS_ID=production_pos_id
PAYU_SIGNATURE_KEY=production_signature_key
```

#### 2. Migracje i seed'y

```bash
php artisan migrate --force
php artisan db:seed
```

#### 3. Optymalizacja

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
npm run build
```

#### 4. Uruchom worker kolejki

Systemd:

```bash
sudo systemctl start rapshop-queue
sudo systemctl enable rapshop-queue
```

Supervisor:

```bash
sudo supervisorctl start rapshop-queue:*
```

#### 5. Cron dla scheduled tasks

Dodaj do crontab:

```bash
* * * * * cd /var/www/rapshop && php artisan schedule:run >> /dev/null 2>&1
```

### Rekomendowane usługi SMTP

#### SendGrid (Rekomendowane)
- 100 emaili dziennie za darmo
- Prosta konfiguracja
- Dobre statystyki

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.xxxxx
```

#### Amazon SES
- 62,000 emaili miesięcznie za darmo w pierwszym roku
- Bardzo tanie później
- Wysoka deliverability

```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=eu-central-1
```

#### Mailgun
- 5000 emaili miesięcznie za darmo
- Dobre API

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.rapshop.pl
MAILGUN_SECRET=your_secret
```

### Bezpieczeństwo DNS

Dla najlepszej deliverability skonfiguruj DNS:

**SPF Record:**
```
v=spf1 include:_spf.sendgrid.net ~all
```

**DKIM:**
Skopiuj klucz z panelu SendGrid/Mailgun

**DMARC:**
```
v=DMARC1; p=quarantine; rua=mailto:dmarc@rapshop.pl
```

### Monitoring

#### Logi aplikacji

```bash
tail -f storage/logs/laravel.log
```

#### Sprawdzenie worker'ów kolejki

```bash
# Systemd
systemctl status rapshop-queue

# Supervisor
supervisorctl status rapshop-queue:*
```

#### Failed jobs

```bash
php artisan queue:failed
```

#### Health check endpoint

```
GET /health
```

Zwraca JSON ze statusem aplikacji, kolejki i emaila.

---

## Troubleshooting

### Email nie wysyła się

1. Sprawdź konfigurację SMTP w `.env`
2. Sprawdź logi: `storage/logs/laravel.log`
3. Przetestuj połączenie:
```bash
php artisan tinker
Mail::raw('Test', function($msg) { $msg->to('test@example.com'); });
```

### Błędy w queue worker

```bash
# Wyświetl failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry all

# Usuń failed job
php artisan queue:forget job_id
```

### Problemy z PayU

1. Sprawdź konfigurację w `config/payu.php`
2. Sprawdź logi: `storage/logs/laravel.log`
3. Upewnij się, że `/payment/notify` jest publicznie dostępny
4. Przetestuj w sandbox PayU

---

**Ostatnia aktualizacja:** 2 grudnia 2025  
**Wersja:** 1.0.0  
**Autor:** Zespół Deweloperski
