# Dokumentacja Techniczna - Sklep Internetowy Rap Shop

## 1. Informacje ogólne

### 1.1 Opis projektu
Sklep internetowy Rap Shop to aplikacja e-commerce do sprzedaży odzieży. System umożliwia użytkownikom przeglądanie produktów, składanie zamówień oraz zarządzanie kontem. Administratorzy mają dostęp do panelu administracyjnego do zarządzania całym sklepem.

### 1.2 Użyte technologie
- Kontener: Docker
- Backend: PHP, Laravel
- Frontend: HTML5, CSS3, JavaScript, Node.Js, Vite
- Baza danych: MySQL 8.0
- Serwer: EC2 Instance na AWS - w środku nginx

### 1.3 Wymagania systemowe
- Docker
- Nginx
- PHP w wersji 8.4 lub nowszej
- MySQL w wersji 8.0 lub nowszej
- Composer 
- Co najmniej 2GB RAM
- Co najmniej 40GB wolnego miejsca na dysku

## 2. Architektura aplikacji

### 2.1 Struktura katalogów
```
sklep/
├── app/                    # Logika aplikacji
│   ├── Http/
│   │   ├── Controllers/   # Kontrolery
│   │   └── Middleware/    # Middleware
│   └── Models/            # Modele danych
├── database/
│   ├── migrations/        # Migracje bazy danych
│   └── seeders/          # Dane testowe
├── public/               # Pliki publiczne
│   ├── css/             # Style CSS
│   ├── js/              # Skrypty JavaScript
│   └── images/          # Obrazy
├── resources/
│   └── views/           # Widoki Blade
└── routes/
    └── web.php          # Definicje tras
```

### 2.2 Wzorzec MVC
Aplikacja wykorzystuje wzorzec MVC (Model-View-Controller):
- Model: reprezentuje dane i logikę biznesową
- View: odpowiada za prezentację danych
- Controller: obsługuje żądania użytkownika i koordynuje model z widokiem

## 3. Moduły aplikacji

### 3.1 Moduł użytkownika
Funkcje dostępne dla zwykłych użytkowników:

**Rejestracja i logowanie**
- Rejestracja nowego konta
- Logowanie do systemu
- Resetowanie hasła przez email
- Weryfikacja adresu email

**Zarządzanie kontem**
- Edycja danych osobowych (imię, nazwisko, email, telefon)
- Zmiana hasła
- Zarządzanie adresami dostawy
- Przeglądanie historii zamówień

**Przeglądanie produktów**
- Lista wszystkich produktów
- Filtrowanie po kategorii
- Wyszukiwanie produktów
- Sortowanie (cena, nazwa, nowość)
- Szczegóły produktu z opisem i zdjęciami

**Koszyk**
- Dodawanie produktów do koszyka
- Zmiana ilości produktów
- Usuwanie produktów z koszyka
- Podgląd sumy zamówienia

**Składanie zamówienia**
- Wybór adresu dostawy
- Wybór metody płatności
- Aplikowanie kuponów rabatowych
- Potwierdzenie zamówienia

**Lista życzeń**
- Dodawanie produktów do listy życzeń
- Usuwanie produktów z listy
- Szybkie dodanie do koszyka z listy

**Opinie**
- Wystawianie opinii o kupionych produktach
- Ocena w skali 1-5 gwiazdek
- Dodawanie komentarza

### 3.2 Moduł administratora
Funkcje dostępne dla administratorów:

**Dashboard**
- Statystyki sprzedaży
- Ostatnie zamówienia
- Produkty o niskim stanie
- Wykres przychodów

**Zarządzanie produktami**
- Dodawanie nowych produktów
- Edycja istniejących produktów
- Usuwanie produktów
- Zarządzanie stanem magazynowym
- Historia zmian stanu magazynowego
- Dodawanie zdjęć produktów

**Zarządzanie kategoriami**
- Dodawanie kategorii
- Edycja kategorii
- Usuwanie kategorii
- Ustalanie kolejności wyświetlania

**Zarządzanie zamówieniami**
- Lista wszystkich zamówień
- Filtrowanie zamówień (status, data, klient)
- Szczegóły zamówienia
- Zmiana statusu zamówienia (oczekujące, w trakcie realizacji, wysłane, dostarczone, anulowane)
- Zmiana statusu płatności

**Zarządzanie użytkownikami**
- Lista wszystkich użytkowników
- Filtrowanie użytkowników (rola, status)
- Szczegóły użytkownika
- Edycja danych użytkownika
- Zmiana roli użytkownika (klient, administrator)
- Aktywacja/dezaktywacja konta
- Usuwanie użytkownika

**Zarządzanie kuponami rabatowymi**
- Tworzenie kuponów
- Edycja kuponów
- Usuwanie kuponów
- Ustawianie warunków (minimalna wartość zamówienia, limit użyć)
- Ustawianie okresu ważności

**Zarządzanie opiniami**
- Lista wszystkich opinii
- Zatwierdzanie opinii
- Odrzucanie opinii
- Usuwanie opinii

**Raporty**
- Raport sprzedaży (dzienny, tygodniowy, miesięczny)
- Raport magazynowy
- Eksport danych do pliku

**Zarządzanie magazynem**
- Przeglądanie stanów magazynowych
- Korekta stanów
- Historia zmian stanów
- Produkty o niskim stanie
- Eksport danych magazynowych

## 4. Baza danych

### 4.1 Główne tabele

**users** - użytkownicy systemu
- Zawiera: id, name, email, password, role, is_active, phone, email_verified_at
- Role: customer (klient), admin (administrator)

**products** - produkty w sklepie
- Zawiera: id, name, slug, description, price, compare_price, cost, quantity, sku, category_id, is_active
- Relacje: należy do kategorii, ma wiele wariantów, opinii, zdjęć

**categories** - kategorie produktów
- Zawiera: id, name, slug, description, parent_id, sort_order, is_active
- Struktura drzewa: kategoria może mieć kategorię nadrzędną

**orders** - zamówienia
- Zawiera: id, user_id, order_number, status, payment_status, total, shipping_cost
- Statusy zamówienia: pending, processing, shipped, delivered, cancelled
- Statusy płatności: pending, paid, failed, refunded

**order_items** - pozycje zamówienia
- Zawiera: id, order_id, product_id, product_name, quantity, price
- Przechowuje nazwę produktu na wypadek usunięcia produktu

**addresses** - adresy dostaw
- Zawiera: id, user_id, first_name, last_name, street_address, city, postal_code, country, phone, is_default

**coupons** - kupony rabatowe
- Zawiera: id, code, type (fixed, percentage), value, min_order_value, max_uses, uses_count, valid_from, valid_to

**reviews** - opinie o produktach
- Zawiera: id, product_id, user_id, rating, title, comment, is_approved

**product_images** - zdjęcia produktów
- Zawiera: id, product_id, image_path, sort_order, is_primary

**stock_movements** - historia zmian stanów magazynowych
- Zawiera: id, product_id, type (in, out, adjustment), quantity, reference_type, reference_id, note

**wishlists** - lista życzeń użytkowników
- Zawiera: id, user_id, product_id

**newsletter_subscribers** - subskrybenci newslettera
- Zawiera: id, email, is_active

### 4.2 Relacje między tabelami

**User (użytkownik)**
- Ma wiele zamówień (orders)
- Ma wiele adresów (addresses)
- Ma wiele opinii (reviews)
- Ma wiele produktów na liście życzeń (wishlists)

**Product (produkt)**
- Należy do kategorii (category)
- Ma wiele pozycji zamówień (order_items)
- Ma wiele opinii (reviews)
- Ma wiele zdjęć (product_images)
- Ma wiele ruchów magazynowych (stock_movements)

**Order (zamówienie)**
- Należy do użytkownika (user)
- Ma wiele pozycji (order_items)
- Ma jeden adres dostawy (address)
- Może mieć jeden kupon (coupon)

**Category (kategoria)**
- Ma wiele produktów (products)
- Może mieć kategorię nadrzędną (parent_category)
- Może mieć wiele podkategorii (subcategories)

## 5. Kontrolery

### 5.1 Kontrolery strony głównej

**HomeController**
- index(): wyświetla stronę główną z promocjami
- products(): wyświetla listę wszystkich produktów

**ProductController**
- show(): wyświetla szczegóły produktu

**CategoryController**
- show(): wyświetla produkty z danej kategorii

**CartController**
- index(): wyświetla koszyk
- add(): dodaje produkt do koszyka
- update(): aktualizuje ilość produktu
- remove(): usuwa produkt z koszyka
- count(): zwraca liczbę produktów w koszyku

**CheckoutController**
- index(): wyświetla stronę kasy
- applyCoupon(): aplikuje kupon rabatowy
- removeCoupon(): usuwa kupon
- processOrder(): przetwarza zamówienie

**AccountController**
- dashboard(): wyświetla panel użytkownika
- editProfile(): formularz edycji profilu
- updateProfile(): zapisuje zmiany profilu
- showPasswordForm(): formularz zmiany hasła
- updatePassword(): zmienia hasło
- addresses(): wyświetla listę adresów
- createAddress(): formularz dodawania adresu
- storeAddress(): zapisuje nowy adres
- editAddress(): formularz edycji adresu
- updateAddress(): zapisuje zmiany adresu
- destroyAddress(): usuwa adres
- setDefaultAddress(): ustawia domyślny adres
- orders(): wyświetla listę zamówień
- showOrder(): wyświetla szczegóły zamówienia
- wishlist(): wyświetla listę życzeń
- addToWishlist(): dodaje produkt do listy
- removeFromWishlist(): usuwa produkt z listy

### 5.2 Kontrolery panelu admina

**DashboardController**
- index(): wyświetla dashboard z statystykami

**Admin/ProductController**
- index(): lista produktów
- create(): formularz dodawania produktu
- store(): zapisuje nowy produkt
- edit(): formularz edycji produktu
- update(): zapisuje zmiany produktu
- destroy(): usuwa produkt
- stock(): wyświetla stan magazynowy
- adjustStock(): koryguje stan magazynowy

**Admin/CategoryController**
- index(): lista kategorii
- store(): dodaje kategorię
- update(): aktualizuje kategorię
- destroy(): usuwa kategorię

**Admin/OrderController**
- index(): lista zamówień
- show(): szczegóły zamówienia
- updateStatus(): zmienia status zamówienia
- updatePaymentStatus(): zmienia status płatności

**Admin/UserController**
- index(): lista użytkowników
- show(): szczegóły użytkownika
- update(): aktualizuje dane użytkownika
- destroy(): usuwa użytkownika
- toggleStatus(): aktywuje/dezaktywuje konto

**Admin/CouponController**
- index(): lista kuponów
- store(): dodaje kupon
- update(): aktualizuje kupon
- destroy(): usuwa kupon

**Admin/ReviewController**
- index(): lista opinii
- approve(): zatwierdza opinię
- reject(): odrzuca opinię
- destroy(): usuwa opinię

**Admin/StockController**
- index(): stan magazynowy
- history(): historia zmian stanów
- export(): eksport danych magazynowych

**Admin/ReportController**
- sales(): raport sprzedaży
- inventory(): raport magazynowy

## 6. Middleware

**auth** - sprawdza czy użytkownik jest zalogowany
**admin** - sprawdza czy użytkownik ma rolę administratora
**verified** - sprawdza czy email został zweryfikowany
**guest.checkout** - pozwala na zamówienie bez logowania lub zalogowanym

## 7. Walidacja danych

### 7.1 Rejestracja użytkownika
- name: wymagane, minimum 2 znaki
- email: wymagane, poprawny format email, unikalny
- password: wymagane, minimum 8 znaków, potwierdzenie
- phone: opcjonalne, tylko cyfry

### 7.2 Produkt
- name: wymagane, maksymalnie 255 znaków
- price: wymagane, liczba większa od 0
- quantity: wymagane, liczba całkowita większa lub równa 0
- category_id: wymagane, musi istnieć w tabeli kategorii
- sku: opcjonalne, unikalne

### 7.3 Zamówienie
- shipping_address_id: wymagane, musi należeć do użytkownika
- payment_method: wymagane (card, transfer, cod)
- terms: wymagane zaakceptowanie regulaminu

### 7.4 Kupon
- code: wymagane, unikalne, tylko duże litery i cyfry
- type: wymagane (fixed, percentage)
- value: wymagane, liczba większa od 0
- valid_from: wymagane, data
- valid_to: wymagane, data po valid_from

## 8. Bezpieczeństwo

### 8.1 Zabezpieczenia
- Hasła są szyfrowane algorytmem bcrypt
- Tokeny CSRF zabezpieczają formularze
- Walidacja danych po stronie serwera
- Sesje przechowywane bezpiecznie

### 8.2 Uprawnienia
- Panel administratora dostępny tylko dla użytkowników z rolą admin
- Użytkownicy mogą edytować tylko swoje dane
- Użytkownicy widzą tylko swoje zamówienia
- Administrator nie może usunąć swojego konta

## 9. Sesje i ciasteczka

### 9.1 Sesja użytkownika
- Przechowuje dane zalogowanego użytkownika
- Przechowuje zawartość koszyka
- Przechowuje zastosowane kupony
- Wygasa po 2 godzinach nieaktywności

### 9.2 Koszyk
- Dla niezalogowanych użytkowników koszyk w sesji
- Dla zalogowanych użytkowników koszyk w bazie danych
- Koszyk zachowywany przez 7 dni

## 10. System plików

### 10.1 Przechowywanie plików
- Zdjęcia produktów: storage/app/public/products

### 10.2 Obsługa zdjęć
- Przesyłanie: maksymalnie 5MB, formaty: jpg, jpeg, png
- Automatyczne generowanie miniatur
- Kompresja obrazów dla lepszej wydajności
- Nazwa pliku: losowy hash dla bezpieczeństwa

## 11. System powiadomień

### 11.1 Email
Wysyłane są następujące emaile:
- Potwierdzenie rejestracji
- Link do resetowania hasła
- Potwierdzenie zamówienia
- Zmiana statusu zamówienia
- Faktura po opłaceniu zamówienia

### 11.2 Newsletter
- Użytkownicy mogą zapisać się do newslettera
- Wysyłka informacji o promocjach
- Możliwość wypisania się z newslettera

## 12. Płatności

### 12.1 Dostępne metody płatności
- Karta płatnicza (symulacja)
- Przelew bankowy
- Płatność przy odbiorze (pobranie)

### 12.2 Obsługa płatności
- Status płatności: pending, paid, failed, refunded
- Automatyczna aktualizacja statusu po płatności
- Historia wszystkich transakcji

## 13. Wydajność

### 13.1 Optymalizacja
- Indeksy w bazie danych na często wyszukiwanych kolumnach
- Eager loading relacji (zapobiega problemowi N+1)
- Cache dla często używanych zapytań
- Kompresja obrazów
- Minifikacja CSS i JavaScript

### 13.2 Paginacja
- Lista produktów: 12 na stronę
- Lista zamówień: 20 na stronę
- Lista użytkowników: 20 na stronę
- Historia magazynowa: 50 na stronę

## 14. Testowanie

### 14.1 Dane testowe
- Seeder tworzy przykładowe kategorie
- Seeder tworzy przykładowe produkty
- Seeder tworzy konto administratora (admin@example.com / password)
- Seeder tworzy przykładowych użytkowników

### 14.2 Jak uruchomić seedery
```
php artisan migrate:fresh --seed
```

## 15. Instalacja i konfiguracja

### 15.1 Kroki instalacji
1. Sklonuj repozytorium
2. Skopiuj plik .env.example jako .env
3. Skonfiguruj połączenie z bazą danych w .env
4. Uruchom: composer install
5. Uruchom: php artisan key:generate
6. Uruchom: php artisan migrate --seed
7. Uruchom: php artisan storage:link
8. Uruchom: php artisan serve

### 15.2 Konfiguracja .env
```
APP_NAME="Rap Shop"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sklep
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
```

## 16. Znane problemy i ograniczenia

### 16.1 Aktualne ograniczenia
- Brak integracji z prawdziwymi bramkami płatności
- Brak automatycznego generowania faktur PDF
- Brak wielojęzyczności
- Brak zaawansowanego systemu SEO
- Brak integracji z firmami kurierskimi

### 16.2 Planowane rozszerzenia
- Integracja z Przelewy24 / PayU
- Generowanie faktur PDF
- System wielojęzyczny (polski, angielski)
- System śledzenia przesyłek
- Zaawansowane raporty sprzedażowe
- System rekomendacji produktów
- Program lojalnościowy

## 17. Wsparcie i kontakt

### 17.1 Dokumentacja Laravel
- https://laravel.com/docs

### 17.2 Struktura projektu
- Kod źródłowy: /app
- Widoki: /resources/views
- Style: /public/css
- Skrypty: /public/js
- Dokumentacja: /docs
