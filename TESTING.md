# Instrukcje Testowania Aplikacji Sklep Internetowy

## Wymagania

- PHP 8.4+
- Composer
- Node.js i npm
- MySQL 8.0+ lub SQLite (dla testów lokalnych)
- Docker i Docker Compose (opcjonalnie)

## Opcja 1: Testowanie z Docker

### Krok 1: Uruchomienie środowiska

```bash
cd /Users/kaca/Documents/projekt_sklep/sklep
docker-compose up -d
```

### Krok 2: Instalacja zależności

```bash
docker-compose exec app composer install
docker-compose exec app npm install
```

### Krok 3: Konfiguracja środowiska

```bash
docker-compose exec app cp .env.example .env
docker-compose exec app php artisan key:generate
```

### Krok 4: Migracje i seedowanie bazy

```bash
docker-compose exec app php artisan migrate:fresh --seed
```

### Krok 5: Budowanie assetów

```bash
docker-compose exec app npm run build
```

### Krok 6: Dostęp do aplikacji

- **Aplikacja**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080
  - Server: db
  - Username: laravel
  - Password: laravel

## Opcja 2: Testowanie lokalne (bez Docker)

### Krok 1: Instalacja zależności

```bash
cd /Users/kaca/Documents/projekt_sklep/sklep
composer install
npm install
```

### Krok 2: Konfiguracja środowiska

```bash
cp .env.example .env
php artisan key:generate
```

**Edytuj plik .env:**

Dla SQLite (szybsze testowanie):
```
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Lub dla MySQL:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sklep_laravel
DB_USERNAME=root
DB_PASSWORD=
```

### Krok 3: Tworzenie bazy danych

Dla SQLite:
```bash
touch database/database.sqlite
```

Dla MySQL - stwórz bazę danych ręcznie lub:
```bash
mysql -u root -p -e "CREATE DATABASE sklep_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Krok 4: Migracje i seedowanie

```bash
php artisan migrate:fresh --seed
```

### Krok 5: Budowanie assetów

```bash
npm run build
```

### Krok 6: Uruchomienie serwera

```bash
php artisan serve
```

### Krok 7: Dostęp do aplikacji

- **Aplikacja**: http://localhost:8000

## Testowe Konta Użytkowników

Po wykonaniu `php artisan db:seed` dostępne będą następujące konta:

### Administrator
- **Email**: admin@rapshop.pl
- **Hasło**: password
- **Panel**: http://localhost:8000/admin

### Klient
- **Email**: customer@example.com
- **Hasło**: password
- **Panel**: http://localhost:8000/account

## Scenariusze Testowe

### 1. Test Przeglądania Produktów
1. Otwórz stronę główną
2. Sprawdź wyświetlanie produktów wyróżnionych
3. Przejdź do listy wszystkich produktów
4. Przetestuj filtry (kategoria, cena, typ)
5. Przetestuj sortowanie (cena, nazwa, popularność)
6. Wejdź na stronę szczegółów produktu

### 2. Test Koszyka
1. Dodaj produkt do koszyka
2. Sprawdź aktualizację licznika koszyka
3. Przejdź do koszyka
4. Zmień ilość produktów
5. Usuń produkt z koszyka
6. Dodaj więcej produktów różnych typów

### 3. Test Procesu Zamówienia (Gość)
1. Dodaj produkty do koszyka
2. Przejdź do checkout
3. Wypełnij dane wysyłki
4. Wybierz metodę dostawy
5. Zastosuj kupon rabatowy (kod testowy: TESTOWY10)
6. Wybierz metodę płatności
7. Złóż zamówienie
8. Sprawdź stronę potwierdzenia

### 4. Test Procesu Zamówienia (Zalogowany)
1. Zaloguj się jako klient
2. Dodaj adres w panelu konta
3. Ustaw adres jako domyślny
4. Dodaj produkty do koszyka
5. Przejdź do checkout (adres powinien być przedwypełniony)
6. Złóż zamówienie
7. Sprawdź historię zamówień w panelu konta

### 5. Test Wishlisty (Ulubione)
1. Zaloguj się jako klient
2. Dodaj produkty do wishlisty
3. Sprawdź licznik wishlisty
4. Przejdź do wishlisty
5. Usuń produkty z wishlisty
6. Dodaj produkt z wishlisty do koszyka

### 6. Test Panelu Administratora
1. Zaloguj się jako admin
2. Sprawdź dashboard ze statystykami
3. Zarządzaj produktami:
   - Dodaj nowy produkt
   - Edytuj istniejący produkt
   - Zmień stan magazynowy
   - Dezaktywuj produkt
4. Zarządzaj zamówieniami:
   - Wyświetl listę zamówień
   - Zmień status zamówienia
   - Oznacz płatność jako opłaconą
   - Dodaj numer trackingu
5. Zarządzaj kategoriami:
   - Dodaj nową kategorię
   - Edytuj kategorię
   - Dezaktywuj kategorię
6. Zarządzaj kuponami:
   - Dodaj nowy kupon
   - Edytuj kupon
   - Dezaktywuj kupon
7. Sprawdź raporty:
   - Raport sprzedaży
   - Stan magazynowy
   - Eksport danych

### 7. Test Newsletter
1. Zapisz się do newslettera z stopki
2. Sprawdź potwierdzenie
3. Wypisz się z newslettera

### 8. Test Wyszukiwania
1. Użyj wyszukiwarki w nagłówku
2. Wpisz nazwę produktu
3. Sprawdź live search results
4. Kliknij na wynik wyszukiwania

## Testowanie Płatności PayU (Sandbox)

**Uwaga**: Wymaga konfiguracji PayU w pliku .env

### Konfiguracja PayU Sandbox

1. Zarejestruj się na https://www.payu.pl/sandbox
2. Pobierz dane autoryzacyjne
3. Dodaj do .env:
```
PAYU_ENVIRONMENT=sandbox
PAYU_POS_ID=twoj_pos_id
PAYU_SIGNATURE_KEY=twoj_signature_key
PAYU_CLIENT_ID=twoj_client_id
PAYU_CLIENT_SECRET=twoj_client_secret
```

### Testowe Płatności
1. Złóż zamówienie z metodą płatności "PayU"
2. Zostaniesz przekierowany do sandbox PayU
3. Użyj testowych danych karty:
   - Numer: 4444 3333 2222 1111
   - Data ważności: 12/25
   - CVV: 123
4. Potwierdź płatność
5. Sprawdź status zamówienia

## Weryfikacja Funkcjonalności Email

**Dla środowiska lokalnego użyj Mailtrap lub MailHog**

### Konfiguracja Mailtrap
1. Zarejestruj się na https://mailtrap.io
2. Pobierz dane SMTP
3. Dodaj do .env:
```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=twoj_username
MAIL_PASSWORD=twoje_haslo
MAIL_ENCRYPTION=tls
```

### Testowanie Emaili
1. Potwierdzenie zamówienia
2. Newsletter subscription
3. Reset hasła
4. Weryfikacja email

## Testy Automatyczne

### Uruchomienie testów PHPUnit

```bash
php artisan test
```

### Uruchomienie testów z coverage

```bash
php artisan test --coverage
```

## Oczyszczanie Danych Testowych

### Reset bazy danych

```bash
php artisan migrate:fresh --seed
```

### Czyszczenie cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Problemy i Rozwiązania

### Problem: Brak uprawnień do storage
```bash
chmod -R 775 storage bootstrap/cache
```

### Problem: Błąd klucza aplikacji
```bash
php artisan key:generate
```

### Problem: Błąd migracji
```bash
php artisan migrate:fresh
```

### Problem: Brak assetów
```bash
npm run build
```

### Problem: Błąd 500 po zalogowaniu
Sprawdź:
1. Czy session driver jest ustawiony na 'file' lub 'database'
2. Czy folder storage/framework/sessions ma uprawnienia zapisu
3. Czy tabela sessions istnieje (jeśli używasz database driver)

## Monitorowanie Logów

### Logi Laravel
```bash
tail -f storage/logs/laravel.log
```

### Logi Queue
```bash
php artisan queue:listen --verbose
```

## Status Testowania

✅ **Zadanie wykonane**: Aplikacja jest gotowa do testowania
- Wszystkie kontrolery przejrzane i zweryfikowane
- Komentarze usunięte
- Migracje sprawdzone
- Seedery dostępne
- Instrukcje testowania przygotowane

## Następny Krok

Po przetestowaniu aplikacji lokalnie, przejdź do dokumentu **DIGITALOCEAN_DEPLOYMENT.md** dla instrukcji wdrożenia produkcyjnego.
