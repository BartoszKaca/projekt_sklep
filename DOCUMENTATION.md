# RAP SHOP - Dokumentacja Projektu

## Spis treści
1. [Opis projektu](#opis-projektu)
2. [Wymagania systemowe](#wymagania-systemowe)
3. [Instalacja](#instalacja)
4. [Konfiguracja](#konfiguracja)
5. [Struktura projektu](#struktura-projektu)
6. [Funkcjonalności](#funkcjonalności)
7. [Modele i baza danych](#modele-i-baza-danych)
8. [API i routing](#api-i-routing)
9. [Panel administracyjny](#panel-administracyjny)
10. [System płatności](#system-płatności)
11. [System mailingu](#system-mailingu)
12. [Testowanie](#testowanie)
13. [Deployment](#deployment)

---

## Opis projektu

RAP SHOP to zaawansowany sklep internetowy zbudowany w Laravel 11, specjalizujący się w sprzedaży płyt CD, vinylu oraz merchandisingu związanego z polską sceną hip-hopową.

### Główne funkcje:
- Katalog produktów z zaawansowanym wyszukiwaniem i filtrowaniem
- System koszyka zakupowego z obsługą wariantów produktów
- Integracja z systemami płatności online
- Panel administracyjny z zarządzaniem produktami, zamówieniami i magazynem
- System kont użytkowników z historią zamówień
- System mailingowy z powiadomieniami
- Newsletter
- Zarządzanie zapasami magazynowymi

### Technologie:
- **Backend:** Laravel 11, PHP 8.2+
- **Frontend:** Blade templates, Vanilla JavaScript
- **Baza danych:** MySQL 8.0+
- **Kolejkowanie:** Redis (opcjonalne)
- **Mail:** SMTP / Mailgun / Mailtrap
- **Płatności:** Przelewy24 / PayU (gotowe do integracji)

---

## Wymagania systemowe

### Minimalne wymagania:
- PHP >= 8.2
- Composer >= 2.5
- MySQL >= 8.0 lub MariaDB >= 10.3
- Node.js >= 18.x (dla kompilacji assetów)
- NPM >= 9.x

### Zalecane:
- PHP 8.3
- MySQL 8.0+
- Redis (dla cache i kolejek)
- SSL/TLS (dla produkcji)

### Wymagane rozszerzenia PHP:
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- GD lub Imagick (dla przetwarzania obrazów)

---

## Instalacja

### 1. Klonowanie repozytorium
```bash
git clone [URL_REPOZYTORIUM]
cd sklep
```

### 2. Instalacja zależności PHP
```bash
composer install
```

### 3. Instalacja zależności JavaScript
```bash
npm install
```

### 4. Konfiguracja środowiska
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Konfiguracja bazy danych
Edytuj plik `.env` i ustaw dane dostępowe do bazy danych:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rap_shop
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Uruchomienie migracji
```bash
php artisan migrate
```

### 7. Seedowanie bazy danych (opcjonalne)
```bash
php artisan db:seed
```

### 8. Utworzenie linku symbolicznego dla storage
```bash
php artisan storage:link
```

### 9. Kompilacja assetów
```bash
npm run dev
# lub dla produkcji:
npm run build
```

### 10. Uruchomienie serwera deweloperskiego
```bash
php artisan serve
```

Aplikacja będzie dostępna pod adresem: `http://localhost:8000`

---

## Konfiguracja

### Konfiguracja mailingu
W pliku `.env` skonfiguruj parametry mailingu:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@rapshop.pl
MAIL_FROM_NAME="${APP_NAME}"
```

Szczegółowa dokumentacja mailingu znajduje się w pliku `MAILING_SYSTEM.md`.

### Konfiguracja płatności
Obecnie projekt jest przygotowany do integracji z systemami płatności. Szczegóły w sekcji [System płatności](#system-płatności).

### Konfiguracja cache i kolejek
Dla lepszej wydajności zalecane jest użycie Redis:

```env
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

---

## Struktura projektu

```
sklep/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Kontrolery
│   │   │   ├── Admin/         # Kontrolery panelu admin
│   │   │   ├── Auth/          # Kontrolery autoryzacji
│   │   │   └── ...            # Kontrolery frontendu
│   │   ├── Middleware/        # Middleware aplikacji
│   │   └── Requests/          # Form requests
│   ├── Mail/                  # Klasy mailowe
│   ├── Models/                # Modele Eloquent
│   ├── Notifications/         # Powiadomienia
│   └── Providers/            # Service Providers
├── config/                    # Pliki konfiguracyjne
├── database/
│   ├── migrations/           # Migracje bazy danych
│   ├── seeders/             # Seedery
│   └── factories/           # Fabryki danych testowych
├── public/                   # Publiczne pliki (CSS, JS, obrazy)
├── resources/
│   ├── views/               # Szablony Blade
│   │   ├── admin/          # Widoki panelu admin
│   │   ├── account/        # Widoki konta użytkownika
│   │   ├── cart/           # Widoki koszyka
│   │   ├── layouts/        # Layouty
│   │   ├── mail/           # Szablony emaili
│   │   └── pages/          # Strony statyczne
│   └── css/                # Style CSS
├── routes/
│   ├── web.php             # Trasy webowe
│   ├── api.php             # Trasy API (opcjonalne)
│   └── console.php         # Komendy Artisan
├── storage/                 # Storage aplikacji
│   ├── app/                # Pliki aplikacji
│   ├── framework/          # Cache, sessions
│   └── logs/               # Logi
└── tests/                   # Testy aplikacji
```

---

## Funkcjonalności

### 1. Katalog produktów
- Przeglądanie produktów z podziałem na kategorie
- Wyszukiwanie produktów
- Filtrowanie (cena, format, dostępność)
- Sortowanie (cena, nazwa, data dodania)
- Strony szczegółów produktu
- Warianty produktów (rozmiary, kolory dla merchu)

### 2. Koszyk i zamówienia
- Dodawanie produktów do koszyka
- Aktualizacja ilości w koszyku
- Obsługa kuponów rabatowych
- Wybór metody dostawy
- Wybór metody płatności
- Potwierdzenie zamówienia
- Historia zamówień

### 3. Konto użytkownika
- Rejestracja z weryfikacją email
- Logowanie/wylogowanie
- Reset hasła
- Profil użytkownika
- Lista adresów dostawczych
- Historia zamówień
- Lista życzeń (wishlist)

### 4. Panel administracyjny
- Dashboard z statystykami
- Zarządzanie produktami (CRUD)
- Zarządzanie kategoriami
- Zarządzanie zamówieniami
- Zarządzanie użytkownikami
- Zarządzanie kuponami rabatowymi
- System magazynowy z historią ruchów
- Raporty sprzedażowe
- Moderacja opinii

### 5. System mailingowy
- Potwierdzenie rejestracji
- Weryfikacja email
- Potwierdzenie zamówienia
- Statusy zamówienia
- Reset hasła
- Newsletter

---

## Modele i baza danych

### Główne modele:

#### User
- Użytkownicy systemu (klienci i administratorzy)
- Pola: name, email, password, role, verified_at
- Relacje: addresses, orders, wishlist, reviews

#### Product
- Produkty w sklepie
- Pola: name, slug, description, price, discount_price, sku, stock_quantity, format, type, artist
- Relacje: category, images, variants, reviews, stock_movements

#### ProductVariant
- Warianty produktów (dla odzieży)
- Pola: product_id, name, sku, price, stock
- Relacje: product

#### Category
- Kategorie produktów
- Pola: name, slug, description, parent_id
- Relacje: products, parent, children

#### Order
- Zamówienia
- Pola: order_number, user_id, status, payment_status, total, shipping_method
- Relacje: user, items, coupon

#### OrderItem
- Pozycje zamówienia
- Pola: order_id, product_id, variant_id, quantity, price
- Relacje: order, product, variant

#### Address
- Adresy dostawcze użytkowników
- Pola: user_id, name, street, city, postal_code, phone, is_default
- Relacje: user

#### Coupon
- Kupony rabatowe
- Pola: code, type, value, min_order_value, valid_from, valid_to, max_uses
- Relacje: orders

#### Review
- Opinie o produktach
- Pola: product_id, user_id, rating, content, status
- Relacje: product, user

#### StockMovement
- Historia ruchów magazynowych
- Pola: product_id, type, quantity, stock_before, stock_after, reason
- Relacje: product, user, order

### Diagramy ERD
Szczegółowe diagramy bazy danych znajdują się w katalogu `database/diagrams/`.

---

## API i routing

### Publiczne trasy (web.php)

#### Strona główna i produkty
```
GET  /                          - Strona główna
GET  /produkty                  - Lista produktów
GET  /produkt/{slug}            - Szczegóły produktu
GET  /kategoria/{slug}          - Produkty w kategorii
GET  /szukaj                    - Wyszukiwanie
```

#### Koszyk
```
GET  /cart                      - Widok koszyka
POST /cart/add                  - Dodaj do koszyka
POST /cart/update               - Aktualizuj koszyk
POST /cart/remove               - Usuń z koszyka
```

#### Checkout i płatności
```
GET  /checkout                  - Formularz zamówienia
POST /checkout                  - Przetwórz zamówienie
GET  /checkout/success/{order}  - Potwierdzenie zamówienia
GET  /payment/{order}           - Procesowanie płatności
```

#### Strony statyczne
```
GET  /o-nas                     - O nas
GET  /regulamin                 - Regulamin
GET  /polityka-prywatnosci      - Polityka prywatności
GET  /dostawa-i-platnosc        - Informacje o dostawie
GET  /zwroty-i-reklamacje       - Zwroty i reklamacje
```

### Trasy konta użytkownika (wymagana autoryzacja)
```
GET  /account                   - Dashboard konta
GET  /account/profile           - Edycja profilu
GET  /account/orders            - Historia zamówień
GET  /account/addresses         - Zarządzanie adresami
GET  /account/wishlist          - Lista życzeń
```

### Trasy panelu admin (wymagana autoryzacja + rola admin)
```
GET  /admin                     - Dashboard admina
      
# Produkty
GET  /admin/products            - Lista produktów
GET  /admin/products/create     - Formularz nowego produktu
POST /admin/products            - Zapisz nowy produkt
GET  /admin/products/{id}/edit  - Edycja produktu
PUT  /admin/products/{id}       - Zaktualizuj produkt
DELETE /admin/products/{id}     - Usuń produkt

# Zamówienia
GET  /admin/orders              - Lista zamówień
GET  /admin/orders/{id}         - Szczegóły zamówienia
PATCH /admin/orders/{id}/status - Zmień status zamówienia

# Magazyn
GET  /admin/stock               - Stan magazynowy
GET  /admin/stock/history       - Historia ruchów
GET  /admin/stock/export        - Eksport CSV

# Inne
GET  /admin/categories          - Kategorie
GET  /admin/coupons             - Kupony
GET  /admin/users               - Użytkownicy
GET  /admin/reviews             - Opinie
GET  /admin/reports/sales       - Raporty sprzedaży
```

---

## Panel administracyjny

### Dostęp
Panel administracyjny dostępny jest pod adresem `/admin` i wymaga:
- Zalogowania użytkownika
- Posiadania roli `admin`

### Middleware
```php
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // trasy admina
});
```

Middleware `admin` znajduje się w `app/Http/Middleware/AdminMiddleware.php`.

### Tworzenie konta administratora
```bash
php artisan tinker
> $user = User::find(1); // lub create new user
> $user->role = 'admin';
> $user->save();
```

Lub przez seeder:
```bash
php artisan db:seed --class=AdminSeeder
```

### Funkcje panelu

#### Dashboard
- Podsumowanie zamówień (dzisiaj, ten miesiąc)
- Wykres sprzedaży
- Ostatnie zamówienia
- Produkty o niskim stanie
- Najlepiej sprzedające się produkty

#### Zarządzanie produktami
- Lista wszystkich produktów z filtrowaniem
- Dodawanie nowych produktów z obrazami
- Edycja produktów
- Zarządzanie wariantami
- Masowe operacje (usuwanie, zmiana statusu)
- Korekta stanów magazynowych

#### Zarządzanie zamówieniami
- Lista zamówień z filtrowaniem po statusie
- Szczegóły zamówienia
- Zmiana statusu zamówienia
- Zmiana statusu płatności
- Generowanie PDF faktur (planowane)
- Historia komunikacji z klientem

#### System magazynowy
- Aktualny stan wszystkich produktów
- Alerty o niskim stanie
- Historia ruchów magazynowych
- Eksport do CSV/Excel
- Korekty stanów

---

## System płatności

### Obecnie zaimplementowane
Projekt zawiera podstawową integrację z systemami płatności online:

#### PaymentController
Lokalizacja: `app/Http/Controllers/PaymentController.php`

Metody:
- `process($order)` - Inicjalizacja płatności
- `return($order)` - Obsługa powrotu z bramki
- `notify()` - Webhook dla powiadomień o statusie

### Integracja z operatorami

#### Przelewy24
```php
// config/services.php
'przelewy24' => [
    'merchant_id' => env('P24_MERCHANT_ID'),
    'pos_id' => env('P24_POS_ID'),
    'crc' => env('P24_CRC'),
    'api_key' => env('P24_API_KEY'),
    'test_mode' => env('P24_TEST_MODE', true),
],
```

#### PayU
```php
// config/services.php
'payu' => [
    'pos_id' => env('PAYU_POS_ID'),
    'signature_key' => env('PAYU_SIGNATURE_KEY'),
    'oauth_client_id' => env('PAYU_CLIENT_ID'),
    'oauth_client_secret' => env('PAYU_CLIENT_SECRET'),
    'test_mode' => env('PAYU_TEST_MODE', true),
],
```

### Obsługiwane metody płatności
- Karty płatnicze (Visa, Mastercard, Maestro)
- BLIK
- Przelewy bankowe
- PayPal (opcjonalnie)
- Płatność przy odbiorze (za pobraniem)
- Przelew tradycyjny

---

## System mailingu

Aplikacja posiada zaawansowany system mailingu z szablonami HTML.

### Szczegółowa dokumentacja
- `MAILING_SYSTEM.md` - Kompletny opis systemu mailingu
- `MAILING_QUICK_START.md` - Szybki start
- `MAILING_CHANGES.md` - Historia zmian
- `TESTING_MAILING_SYSTEM.md` - Testowanie mailingu

### Typy wysyłanych emaili

#### Weryfikacja konta
- `App\Mail\VerificationMail`
- Wysyłany po rejestracji
- Zawiera kod weryfikacyjny

#### Potwierdzenie zamówienia
- `App\Mail\OrderConfirmation`
- Wysyłany po złożeniu zamówienia
- Zawiera szczegóły zamówienia

#### Statusy zamówienia
- `App\Mail\OrderStatusChanged`
- Wysyłany przy zmianie statusu
- Statusy: processing, shipped, delivered, cancelled

#### Reset hasła
- Laravel domyślnie obsługuje resetowanie hasła
- Szablon: `resources/views/emails/reset-password.blade.php`

### Konfiguracja szablonów
Szablony znajdują się w `resources/views/mail/`.

Wspólny layout: `resources/views/layouts/mail.blade.php`

### Kolejkowanie emaili
Dla lepszej wydajności emaile są kolejkowane:

```php
Mail::to($user)->queue(new OrderConfirmation($order));
```

Uruchomienie workera:
```bash
php artisan queue:work
```

---

## Testowanie

### Uruchamianie testów
```bash
# Wszystkie testy
php artisan test

# Konkretny test
php artisan test --filter=ProductTest

# Z pokryciem kodu
php artisan test --coverage
```

### Struktura testów
```
tests/
├── Feature/              # Testy funkcjonalne
│   ├── ProductTest.php
│   ├── CartTest.php
│   ├── OrderTest.php
│   └── ...
└── Unit/                 # Testy jednostkowe
    ├── ProductModelTest.php
    └── ...
```

### Testowanie mailingu
```bash
# Uruchom skrypt testowy
./test-mailing.sh

# Lub manualnie
php artisan tinker
> \App\Mail\TestMailingSystem::sendTest();
```

Szczegóły w pliku `TESTING_MAILING_SYSTEM.md`.

### Dane testowe
```bash
# Wygeneruj testowe dane
php artisan db:seed

# Konkretny seeder
php artisan db:seed --class=ProductSeeder
```

---

## Deployment

### Przygotowanie do produkcji

#### 1. Optymalizacja
```bash
# Cache konfiguracji
php artisan config:cache

# Cache tras
php artisan route:cache

# Cache widoków
php artisan view:cache

# Optymalizacja autoloadera
composer install --optimize-autoloader --no-dev
```

#### 2. Kompilacja assetów
```bash
npm run build
```

#### 3. Ustawienia środowiska
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://rapshop.pl

# Zmień APP_KEY na produkcyjny
php artisan key:generate
```

#### 4. Uprawnienia
```bash
# Storage i cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Deployment na shared hosting

#### Via FTP
1. Upload plików (bez `node_modules`, `vendor`)
2. Uruchom `composer install --no-dev` na serwerze
3. Skonfiguruj `.env`
4. Uruchom migracje
5. Ustaw document root na `public/`

### Deployment na VPS (Docker)

Szczegóły w pliku `PRODUCTION_DEPLOYMENT.md`.

```bash
# Build obrazu
docker-compose -f docker-compose.prod.yml build

# Uruchom kontenery
docker-compose -f docker-compose.prod.yml up -d

# Migracje
docker-compose exec app php artisan migrate --force
```

### SSL/HTTPS
Zalecane jest użycie Let's Encrypt:
```bash
certbot --nginx -d rapshop.pl -d www.rapshop.pl
```

### Monitorowanie
- Logi: `storage/logs/laravel.log`
- Monitoring: Sentry, New Relic (opcjonalnie)
- Uptime monitoring: UptimeRobot

### Backup
```bash
# Automatyczny backup bazy danych
php artisan backup:run

# Cron job (dodaj do crontab)
0 2 * * * cd /path/to/project && php artisan backup:run
```

---

## Wsparcie i kontakt

### Dokumentacja pomocnicza
- `README.md` - Podstawowe informacje
- `MAILING_SYSTEM.md` - System mailingu
- `PRODUCTION_DEPLOYMENT.md` - Deployment produkcyjny
- `TESTING_EMAILS.md` - Testowanie emaili

### Zgłaszanie błędów
Zgłoś błąd przez system issues w repozytorium.

### Rozwój projektu
Pull requesty są mile widziane!

---

## Licencja
[Określ licencję projektu]

---

## Changelog

### v1.0.0 (2024-12-02)
- Pierwsza wersja aplikacji
- Podstawowe funkcje sklepu
- Panel administracyjny
- System mailingu
- Integracja płatności (podstawowa)

---

**Dokumentacja wygenerowana:** 2024-12-02
**Wersja projektu:** 1.0.0
**Laravel:** 11.x
