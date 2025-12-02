# RAP SHOP - Dokumentacja Projektu

## Spis treści
1. [Opis projektu](#opis-projektu)
2. [Wymagania systemowe](#wymagania-systemowe)
3. [Instalacja](#instalacja)
4. [Konfiguracja](#konfiguracja)
5. [Struktura projektu](#struktura-projektu)
6. [Funkcjonalności](#funkcjonalności)
7. [Modele i baza danych](#modele-i-baza-danych)
8. [Routing](#routing)
9. [Panel administracyjny](#panel-administracyjny)

---

## Opis projektu

RAP SHOP to sklep internetowy zbudowany w Laravel 11, specjalizujący się w sprzedaży płyt CD, vinylu oraz merchandisingu związanego z polską sceną hip-hopową.

### Główne funkcje:
- Katalog produktów z wyszukiwaniem i filtrowaniem
- System koszyka zakupowego z obsługą wariantów
- Panel administracyjny
- System kont użytkowników z historią zamówień
- Zarządzanie zapasami magazynowymi
- Lista życzeń (wishlist)

### Technologie:
- **Backend:** Laravel 11, PHP 8.2+
- **Frontend:** Blade templates, JavaScript
- **Baza danych:** MySQL 8.0+

---

## Wymagania systemowe

### Minimalne wymagania:
- PHP >= 8.2
- Composer >= 2.5
- MySQL >= 8.0 lub MariaDB >= 10.3
- Node.js >= 18.x
- NPM >= 9.x

### Wymagane rozszerzenia PHP:
- BCMath, Ctype, Fileinfo, JSON, Mbstring
- OpenSSL, PDO, Tokenizer, XML
- GD lub Imagick (dla obrazów)

---

## Instalacja

### Szybka instalacja

```bash
# 1. Klonowanie
git clone [URL_REPOZYTORIUM]
cd sklep

# 2. Zależności
composer install
npm install

# 3. Konfiguracja
cp .env.example .env
php artisan key:generate

# 4. Baza danych (edytuj .env)
php artisan migrate
php artisan db:seed

# 5. Storage i assets
php artisan storage:link
npm run dev

# 6. Start
php artisan serve
```

Aplikacja: `http://localhost:8000`

---

## Konfiguracja

### Baza danych (.env)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rap_shop
DB_USERNAME=root
DB_PASSWORD=
```

### Mail (.env)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@rapshop.pl
```

---

## Struktura projektu

```
sklep/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Kontrolery
│   │   │   ├── Admin/       # Panel admin
│   │   │   └── Auth/        # Autoryzacja
│   │   └── Middleware/      # Middleware
│   ├── Models/              # Modele Eloquent
│   └── Mail/                # Klasy mailowe
├── database/
│   ├── migrations/          # Migracje
│   └── seeders/            # Seedery
├── resources/
│   └── views/              # Szablony Blade
│       ├── admin/          # Widoki admin
│       ├── account/        # Konto użytkownika
│       ├── layouts/        # Layouty
│       └── mail/           # Szablony emaili
├── routes/
│   └── web.php             # Trasy
└── public/                 # Pliki publiczne
```

---

## Funkcjonalności

### 1. Katalog produktów
- Przeglądanie z kategoriami
- Wyszukiwanie i filtrowanie
- Sortowanie (cena, nazwa, data)
- Szczegóły produktu
- Warianty produktów

### 2. Koszyk
- Dodawanie produktów
- Aktualizacja ilości
- Kupony rabatowe
- Wybór dostawy i płatności

### 3. Konto użytkownika
- Rejestracja z weryfikacją email
- Profil użytkownika
- Lista adresów
- Historia zamówień
- Lista życzeń (wishlist)

### 4. Panel admin
- Dashboard ze statystykami
- Zarządzanie produktami
- Zarządzanie zamówieniami
- Zarządzanie użytkownikami
- System magazynowy
- Raporty

---

## Modele i baza danych

### Główne modele:

**User** - Użytkownicy (klienci i admini)
- Pola: name, email, password, role
- Relacje: addresses, orders, wishlist

**Product** - Produkty
- Pola: name, slug, description, price, stock_quantity
- Relacje: category, images, variants

**Category** - Kategorie
- Pola: name, slug, parent_id
- Relacje: products, children

**Order** - Zamówienia
- Pola: order_number, user_id, status, total
- Relacje: user, items

**Wishlist** - Lista życzeń
- Pola: user_id, product_id
- Relacje: user, product

---

## Routing

### Publiczne trasy

```
GET  /                          Strona główna
GET  /produkty                  Lista produktów
GET  /produkt/{slug}            Szczegóły produktu
GET  /kategoria/{slug}          Produkty w kategorii

GET  /cart                      Koszyk
POST /cart/add                  Dodaj do koszyka
POST /cart/update               Aktualizuj
POST /cart/remove               Usuń

GET  /checkout                  Zamówienie
POST /checkout                  Przetwórz zamówienie
```

### Konto użytkownika (auth)

```
GET  /account                   Dashboard
GET  /account/profile           Profil
GET  /account/orders            Zamówienia
GET  /account/addresses         Adresy
GET  /account/wishlist          Lista życzeń
POST /account/wishlist/add      Dodaj do wishlist
POST /account/wishlist/remove   Usuń z wishlist
```

### Panel admin (auth + admin)

```
GET  /admin                     Dashboard
GET  /admin/products            Produkty
GET  /admin/orders              Zamówienia
GET  /admin/stock               Magazyn
GET  /admin/users               Użytkownicy
```

---

## Panel administracyjny

### Dostęp
Panel: `/admin` (wymaga roli `admin`)

### Tworzenie admina
```bash
php artisan tinker
> $user = User::find(1);
> $user->role = 'admin';
> $user->save();
```

### Funkcje
- Dashboard z wykresami
- CRUD produktów z obrazami
- Zarządzanie zamówieniami
- System magazynowy
- Raporty sprzedaży

---

## Testowanie

```bash
# Wszystkie testy
php artisan test

# Z pokryciem
php artisan test --coverage

# Dane testowe
php artisan db:seed
```

---

## Deployment

### Optymalizacja produkcyjna
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
npm run build
```

### Środowisko produkcyjne
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://rapshop.pl
```

---

## Wsparcie

### Problemy znalezione i naprawione
- ✅ Naprawiono funkcję wishlist (zmiana trasy z `wishlist.add` na `account.wishlist.add`)
- ✅ Uproszczono dokumentację
- ✅ Usunięto zbędne pliki dokumentacji mailingu

### Zgłaszanie błędów
Zgłoś przez system issues w repozytorium.

---

**Dokumentacja zaktualizowana:** 2024-12-02
**Wersja:** 1.1.0
**Laravel:** 11.x
