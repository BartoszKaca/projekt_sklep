# Rap Shop - Sklep z polskim rapem 🎵

Projekt sklepu internetowego z płytami muzycznymi i merchendise polskich artystów hip-hop.

## Spis treści

- [O projekcie](#o-projekcie)
- [Funkcjonalności](#funkcjonalności)
- [Technologie](#technologie)
- [Instalacja](#instalacja)
- [Konfiguracja](#konfiguracja)
- [Uruchomienie z Docker](#uruchomienie-z-docker)
- [Struktura bazy danych](#struktura-bazy-danych)
- [Konto testowe](#konto-testowe)
- [API Endpoints](#api-endpoints)

## O projekcie

Rap Shop to kompletny sklep internetowy zbudowany w Laravel 12, oferujący:
- Sprzedaż płyt CD, winyli i kaset
- Sprzedaż merchandise (odzież, akcesoria)
- Obsługę gości i zarejestrowanych użytkowników
- Panel administracyjny
- System płatności PayU (sandbox)

## Funkcjonalności

### Dla klientów
- 🛒 **Koszyk zakupowy** - dodawanie, edycja ilości, usuwanie produktów
- 📦 **Checkout** - kompletny proces zamawiania dla gości i zalogowanych
- 👤 **Panel klienta** - zarządzanie kontem, historia zamówień, adresy
- ❤️ **Lista życzeń** - dodawanie ulubionych produktów
- 🔍 **Wyszukiwanie i filtrowanie** - po kategorii, cenie, typie
- 📧 **Newsletter** - zapis i automatyczne powiadomienia

### Dla administratora
- 📊 **Dashboard** - przegląd statystyk sprzedaży
- 📦 **Zarządzanie produktami** - dodawanie, edycja, warianty, zdjęcia
- 📋 **Zamówienia** - przeglądanie, zmiana statusu, zarządzanie płatnościami
- 🏷️ **Kategorie** - organizacja produktów
- 🎟️ **Kupony rabatowe** - tworzenie i zarządzanie promocjami
- 👥 **Użytkownicy** - zarządzanie kontami klientów
- ⭐ **Opinie** - moderacja recenzji produktów
- 📈 **Raporty** - sprzedaż, stan magazynowy

## Technologie

- **Framework**: Laravel 12.x
- **PHP**: 8.2+
- **Baza danych**: MySQL 8.0+ / SQLite (development)
- **Frontend**: Blade templates, CSS3
- **Autoryzacja**: Laravel UI
- **Płatności**: PayU (integracja sandbox)
- **Email**: SMTP / Mailtrap (testing)

## Instalacja

### Wymagania
- PHP 8.2+
- Composer
- Node.js 18+ (dla assets)
- MySQL 8.0+ lub SQLite

### Kroki instalacji

1. **Klonowanie repozytorium**
```bash
git clone https://github.com/BartoszKaca/projekt_sklep.git
cd projekt_sklep
```

2. **Instalacja zależności PHP**
```bash
composer install
```

3. **Konfiguracja środowiska**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Konfiguracja bazy danych** (edytuj .env)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sklep_laravel
DB_USERNAME=root
DB_PASSWORD=
```

5. **Migracje bazy danych**
```bash
php artisan migrate
```

6. **Inicjalizacja widoków i procedur SQL** (opcjonalnie)
```bash
mysql -u root -p sklep_laravel < database/scripts/init_database.sql
```

7. **Instalacja assets**
```bash
npm install
npm run build
```

8. **Utworzenie linku storage**
```bash
php artisan storage:link
```

9. **Uruchomienie serwera**
```bash
php artisan serve
```

Aplikacja dostępna pod: http://localhost:8000

## Konfiguracja

### Konfiguracja Email (SMTP)

Dla testów zalecamy [Mailtrap](https://mailtrap.io/):

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS="sklep@rapshop.pl"
MAIL_FROM_NAME="Rap Shop"
```

### Konfiguracja PayU (Sandbox)

1. Zarejestruj się na [PayU Sandbox](https://www.payu.pl/)
2. Utwórz punkt płatności i pobierz dane
3. Ustaw w .env:

```env
PAYU_ENVIRONMENT=sandbox
PAYU_POS_ID=your_pos_id
PAYU_SIGNATURE_KEY=your_signature_key
PAYU_CLIENT_ID=your_client_id
PAYU_CLIENT_SECRET=your_client_secret
```

## Uruchomienie z Docker

### Wymagania
- Docker
- Docker Compose

### Uruchomienie

```bash
docker-compose up -d
```

### Dostępne usługi
- **Aplikacja**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080
- **MySQL**: localhost:3307

### Konfiguracja Docker
```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=sklep_laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel
```

### Migracje w Docker
```bash
docker-compose exec app php artisan migrate
```

## Struktura bazy danych

### Główne tabele

| Tabela | Opis |
|--------|------|
| `users` | Użytkownicy (klienci i admini) |
| `categories` | Kategorie produktów |
| `products` | Produkty (albumy, merch) |
| `product_images` | Zdjęcia produktów |
| `product_variants` | Warianty (rozmiary, kolory) |
| `orders` | Zamówienia |
| `order_items` | Pozycje zamówień |
| `order_shipping` | Dane dostawy |
| `addresses` | Zapisane adresy użytkowników |
| `wishlists` | Lista życzeń |
| `coupons` | Kupony rabatowe |
| `reviews` | Opinie produktów |
| `stock_movements` | Historia zmian magazynowych |
| `newsletter_subscribers` | Subskrybenci newslettera |

### Widoki raportowania

Skrypt `database/scripts/init_database.sql` tworzy:
- `vw_sales_summary` - podsumowanie sprzedaży dziennej
- `vw_product_stock_status` - status magazynowy produktów
- `vw_customer_orders` - szczegóły zamówień klientów
- `vw_best_selling_products` - najlepiej sprzedające się produkty

### Procedury składowane
- `sp_calculate_order_total` - przeliczenie wartości zamówienia
- `sp_sales_report` - raport sprzedaży w zakresie dat

## Konto testowe

### Administrator
Po uruchomieniu migracji, utwórz konto admina przez tinker:

```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@rapshop.pl',
    'password' => bcrypt('Admin123!'),
    'role' => 'admin',
    'is_active' => true,
]);
```

Dane logowania:
- Email: admin@rapshop.pl
- Hasło: Admin123!

## API Endpoints

### Publiczne

| Metoda | Endpoint | Opis |
|--------|----------|------|
| GET | `/` | Strona główna |
| GET | `/produkty` | Lista produktów z filtrami |
| GET | `/produkt/{slug}` | Szczegóły produktu |
| GET | `/kategoria/{slug}` | Produkty kategorii |
| GET | `/szukaj?q=` | Wyszukiwanie AJAX |
| GET | `/cart` | Koszyk |
| POST | `/cart/add` | Dodaj do koszyka |
| POST | `/cart/update` | Aktualizuj ilość |
| POST | `/cart/remove` | Usuń z koszyka |

### Checkout

| Metoda | Endpoint | Opis |
|--------|----------|------|
| GET | `/checkout` | Strona checkout |
| POST | `/checkout` | Złóż zamówienie |
| POST | `/checkout/coupon` | Zastosuj kupon |
| DELETE | `/checkout/coupon` | Usuń kupon |
| GET | `/checkout/success/{order}` | Potwierdzenie |

### Panel klienta (wymaga autoryzacji)

| Metoda | Endpoint | Opis |
|--------|----------|------|
| GET | `/account` | Dashboard |
| GET | `/account/profile` | Edycja profilu |
| PUT | `/account/profile` | Zapisz profil |
| GET | `/account/password` | Zmiana hasła |
| PUT | `/account/password` | Zapisz hasło |
| GET | `/account/addresses` | Lista adresów |
| POST | `/account/addresses` | Dodaj adres |
| PUT | `/account/addresses/{id}` | Edytuj adres |
| DELETE | `/account/addresses/{id}` | Usuń adres |
| GET | `/account/orders` | Historia zamówień |
| GET | `/account/wishlist` | Lista życzeń |

### Panel admina (wymaga autoryzacji + rola admin)

Prefix: `/admin`

| Metoda | Endpoint | Opis |
|--------|----------|------|
| GET | `/` | Dashboard |
| Resource | `/products` | CRUD produktów |
| Resource | `/categories` | CRUD kategorii |
| GET | `/orders` | Lista zamówień |
| GET | `/stock` | Stan magazynowy |
| Resource | `/coupons` | CRUD kuponów |
| GET | `/users` | Lista użytkowników |
| GET | `/reviews` | Moderacja opinii |
| GET | `/reports/sales` | Raport sprzedaży |

## Testowanie

```bash
php artisan test
```

## Licencja

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
