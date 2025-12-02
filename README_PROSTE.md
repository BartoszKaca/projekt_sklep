# Rap Shop - Sklep internetowy

Sklep internetowy z płytami i merchem hip-hopowym zbudowany w Laravel.

## 📁 Struktura projektu

### Ważne foldery:

- **app/Http/Controllers/** - kontrolery obsługujące logikę aplikacji
- **app/Models/** - modele (tabele z bazy danych)
- **resources/views/** - widoki (strony HTML)
- **public/css/** - pliki ze stylami CSS
- **routes/web.php** - definicje ścieżek (URL-i)
- **database/migrations/** - migracje bazy danych

## 🎨 Pliki CSS

Wszystkie style są podzielone na moduły:

- **shop.css** - nawigacja, layout główny
- **footer.css** - stopka strony
- **products.css** - karty produktów
- **utilities.css** - pomocnicze klasy (przyciski, alerty)

## 🧭 Główne trasy (URL-e)

### Strony publiczne:
- `/` - strona główna
- `/produkty` - lista produktów
- `/produkt/{slug}` - szczegóły produktu
- `/kategoria/{slug}` - produkty w kategorii
- `/cart` - koszyk

### Panel użytkownika:
- `/account` - dashboard użytkownika
- `/account/wishlist` - lista życzeń
- `/account/orders` - zamówienia
- `/account/addresses` - adresy
- `/account/profile` - profil
- `/account/password` - zmiana hasła

### Panel admina:
- `/admin` - dashboard admina
- `/admin/products` - zarządzanie produktami
- `/admin/orders` - zarządzanie zamówieniami
- `/admin/users` - zarządzanie użytkownikami

## 🎯 Główne kontrolery

### HomeController
- `index()` - strona główna
- `products()` - lista produktów z filtrami

### AccountController
- `dashboard()` - panel główny użytkownika
- `wishlist()` - lista życzeń
- `orders()` - zamówienia użytkownika
- `addToWishlist()` - dodaj do wishlisty
- `removeFromWishlist()` - usuń z wishlisty

### NewsletterController
- `subscribe()` - zapisz do newslettera
- `unsubscribe()` - wypisz z newslettera

### CartController
- `index()` - wyświetl koszyk
- `add()` - dodaj do koszyka
- `update()` - aktualizuj ilość
- `remove()` - usuń z koszyka

## 🗄️ Główne modele

- **User** - użytkownicy
- **Product** - produkty
- **Category** - kategorie
- **Order** - zamówienia
- **Wishlist** - lista życzeń
- **Address** - adresy dostawy
- **NewsletterSubscriber** - subskrybenci newslettera

## 🔧 Jak to działa?

### Dodawanie produktu do wishlisty:

1. Użytkownik klika przycisk ❤️ na produkcie
2. JavaScript wywołuje funkcję `toggleWishlist(productId)`
3. Funkcja wysyła request AJAX do `/account/wishlist/add`
4. Kontroler `AccountController::addToWishlist()` zapisuje w bazie
5. Zwraca odpowiedź JSON z nowym licznikiem
6. JavaScript aktualizuje licznik w nawigacji

### Newsletter:

1. Użytkownik wpisuje email w formularzu
2. JavaScript wywołuje `subscribeNewsletter(event)`
3. Wysyła request do `/newsletter/subscribe`
4. `NewsletterController::subscribe()` zapisuje w bazie
5. Wysyła email powitalny
6. Wyświetla powiadomienie

## 📱 Responsywność

- Desktop: pełny layout z sidebarem
- Tablet: 2 kolumny
- Mobile: 1 kolumna, ukryte menu kategorii

## 🎨 Kolory (CSS variables)

```css
--primary: #6366f1      /* Niebieski główny */
--secondary: #ec4899    /* Różowy */
--dark: #0f172a         /* Ciemny tekst */
--gray: #64748b         /* Szary */
--light: #f8fafc        /* Jasłe tło */
--success: #10b981      /* Zielony (sukces) */
--warning: #f59e0b      /* Pomarańczowy (ostrzeżenie) */
--danger: #ef4444       /* Czerwony (błąd) */
```

## 🚀 Uruchomienie

1. Zainstaluj zależności: `composer install`
2. Skopiuj `.env.example` do `.env`
3. Wygeneruj klucz: `php artisan key:generate`
4. Skonfiguruj bazę danych w `.env`
5. Uruchom migracje: `php artisan migrate`
6. Uruchom serwer: `php artisan serve`

## 📝 Dodawanie nowego produktu (admin)

1. Zaloguj się do `/admin`
2. Przejdź do "Produkty"
3. Kliknij "Dodaj produkt"
4. Wypełnij formularz
5. Dodaj zdjęcia
6. Zapisz

## 🛠️ Troubleshooting

### Nie działa wishlist?
- Sprawdź czy użytkownik jest zalogowany
- Sprawdź CSRF token w meta tag
- Zobacz console w przeglądarce (F12)

### Nie wysyła emaili?
- Skonfiguruj MAIL w .env
- Sprawdź logi w `storage/logs/`

### Nie widać stylów?
- Uruchom `php artisan serve`
- Sprawdź ścieżki w `<link>` tagach
- Wyczyść cache: `php artisan cache:clear`
