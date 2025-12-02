# Przykłady dla ucznia - Jak pracować z projektem

## 🎯 Zadanie 1: Zmień kolor przycisku "Dodaj do koszyka"

### Krok po kroku:

1. Otwórz plik: `public/css/utilities.css`

2. Znajdź:
```css
.btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
}
```

3. Zmień na:
```css
.btn-primary {
    background: linear-gradient(135deg, #10b981, #059669); /* Zielony */
    color: white;
}
```

4. Odśwież stronę (F5)

**Wynik:** Wszystkie przyciski główne będą teraz zielone!

---

## 🎯 Zadanie 2: Dodaj nową kategorię w menu

### Krok po kroku:

1. Zaloguj się do panelu admina: `/admin`

2. Przejdź do "Kategorie"

3. Kliknij "Dodaj kategorię"

4. Wypełnij:
   - Nazwa: `Gadżety`
   - Slug: `gadzety` (automatycznie)
   - Aktywna: ✓

5. Zapisz

**Wynik:** Nowa kategoria pojawi się w menu górnym!

---

## 🎯 Zadanie 3: Zmień tekst w stopce

### Krok po kroku:

1. Otwórz: `resources/views/layouts/shop.blade.php`

2. Znajdź (około linii 180):
```php
<p>
    Twój numer jeden w świecie polskiego hip-hopu.
    Oferujemy największy wybór płyt...
</p>
```

3. Zmień na swój tekst:
```php
<p>
    Najlepszy sklep z muzyką hip-hop w Polsce!
    Mamy wszystko czego potrzebujesz.
</p>
```

4. Odśwież stronę

**Wynik:** Nowy tekst w stopce!

---

## 🎯 Zadanie 4: Dodaj powiadomienie przy logowaniu

### Krok po kroku:

1. Otwórz: `app/Http/Controllers/Auth/LoginController.php`

2. Znajdź metodę `authenticated()` albo dodaj ją:
```php
protected function authenticated(Request $request, $user)
{
    // Wyświetl powiadomienie
    return redirect()->intended('/')->with('success', 'Witaj z powrotem, ' . $user->name . '!');
}
```

3. Upewnij się że w `home.blade.php` jest:
```blade
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
```

**Wynik:** Po zalogowaniu użytkownik zobaczy powiadomienie!

---

## 🎯 Zadanie 5: Zmień liczbę produktów na stronie

### Krok po kroku:

1. Otwórz: `app/Http/Controllers/HomeController.php`

2. Znajdź linię:
```php
->paginate(20)->withQueryString();
```

3. Zmień 20 na inną liczbę, np.:
```php
->paginate(12)->withQueryString(); // 12 produktów na stronie
```

**Wynik:** Teraz będzie wyświetlanych 12 produktów zamiast 20!

---

## 🎯 Zadanie 6: Dodaj nowe pole do produktu

### Krok po kroku:

1. **Utwórz migrację:**
```bash
php artisan make:migration add_producer_to_products_table
```

2. **Edytuj migrację** (`database/migrations/...add_producer...`):
```php
public function up()
{
    Schema::table('products', function (Blueprint $table) {
        $table->string('producer')->nullable()->after('artist');
    });
}

public function down()
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn('producer');
    });
}
```

3. **Uruchom migrację:**
```bash
php artisan migrate
```

4. **Dodaj do modelu** (`app/Models/Product.php`):
```php
protected $fillable = [
    'name', 'slug', 'artist', 'producer', // <- dodaj
    // ... reszta pól
];
```

5. **Wyświetl w widoku** (`resources/views/products/show.blade.php`):
```blade
@if($product->producer)
    <p>Producent: {{ $product->producer }}</p>
@endif
```

**Wynik:** Produkty mogą teraz mieć producenta!

---

## 🎯 Zadanie 7: Zmień logo sklepu

### Krok po kroku:

1. Otwórz: `resources/views/layouts/shop.blade.php`

2. Znajdź:
```html
<a href="{{ route('home') }}" class="logo">
    <i class="fas fa-compact-disc"></i>
    RAP SHOP
</a>
```

3. Zmień na:
```html
<a href="{{ route('home') }}" class="logo">
    <i class="fas fa-music"></i>
    MUZYKA24
</a>
```

**Wynik:** Nowa nazwa i ikona sklepu!

---

## 🎯 Zadanie 8: Dodaj licznik odwiedzin produktu

### Krok po kroku:

1. **Dodaj kolumnę** (migracja):
```php
$table->integer('views_count')->default(0);
```

2. **W kontrolerze** (`ProductController.php`):
```php
public function show($slug)
{
    $product = Product::where('slug', $slug)->firstOrFail();
    
    // Zwiększ licznik
    $product->increment('views_count');
    
    return view('products.show', compact('product'));
}
```

3. **Wyświetl w widoku**:
```blade
<p>Wyświetleń: {{ $product->views_count }}</p>
```

**Wynik:** Każde wejście na produkt zwiększa licznik!

---

## 🎯 Zadanie 9: Dodaj przycisk "Kup teraz"

### Krok po kroku:

1. **W widoku produktu** (`products/show.blade.php`):
```blade
<a href="{{ route('checkout.index') }}" 
   class="btn btn-primary"
   onclick="quickBuy(event, {{ $product->id }})">
    <i class="fas fa-bolt"></i> Kup teraz
</a>
```

2. **Dodaj funkcję JavaScript**:
```javascript
function quickBuy(event, productId) {
    event.preventDefault();
    
    // Dodaj do koszyka
    addToCart(productId);
    
    // Po chwili przekieruj
    setTimeout(() => {
        window.location.href = '{{ route("checkout.index") }}';
    }, 500);
}
```

**Wynik:** Przycisk dodaje do koszyka i od razu przekierowuje do kasy!

---

## 🎯 Zadanie 10: Zmień wygląd alertów

### Krok po kroku:

1. Otwórz: `public/css/utilities.css`

2. Znajdź:
```css
.alert-success {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
    border: 1px solid var(--success);
}
```

3. Zmień na:
```css
.alert-success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: none;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}
```

**Wynik:** Ładniejsze, gradientowe alerty!

---

## 💡 Wskazówki

### Debugging (szukanie błędów):

1. **PHP:**
```php
dd($zmienna); // Wyświetl i zatrzymaj
dump($zmienna); // Tylko wyświetl
```

2. **JavaScript:**
```javascript
console.log(zmienna); // Wyświetl w konsoli
```

3. **Blade (widoki):**
```blade
@dump($zmienna) {{-- Wyświetl zmienną --}}
```

### Często używane komendy:

```bash
php artisan serve              # Uruchom serwer
php artisan migrate            # Uruchom migracje
php artisan migrate:fresh      # Zresetuj bazę
php artisan cache:clear        # Wyczyść cache
php artisan route:list         # Lista tras
php artisan make:controller    # Nowy kontroler
php artisan make:model         # Nowy model
php artisan make:migration     # Nowa migracja
```

### Gdzie czego szukać:

- **Trasy (URL-e):** `routes/web.php`
- **Kontrolery:** `app/Http/Controllers/`
- **Modele (tabele):** `app/Models/`
- **Widoki (HTML):** `resources/views/`
- **Style (CSS):** `public/css/`
- **JavaScript:** `resources/views/layouts/shop.blade.php` (na razie)

---

## 🏆 Ćwiczenia zaawansowane

### Dla ambitnych:

1. Dodaj system ocen produktów (gwiazdki)
2. Zrób filtr produktów w czasie rzeczywistym (AJAX)
3. Dodaj porównywarkę produktów
4. Zrób eksport zamówień do PDF
5. Dodaj powiadomienia email przy nowym zamówieniu

**Powodzenia!** 🚀
