# Naprawa błędu newslettera - subscribed_at

## Problem
Błąd: `Field 'subscribed_at' doesn't have a default value`

## Rozwiązanie

### Opcja 1: Uruchom migrację (zalecane)

```bash
php artisan migrate
```

To uruchomi migrację która naprawi kolumnę `subscribed_at` w bazie danych.

### Opcja 2: Ręczna naprawa w bazie danych

Jeśli migracja nie działa, możesz naprawić to ręcznie:

1. Zaloguj się do phpMyAdmin lub innego narzędzia do MySQL
2. Wybierz swoją bazę danych
3. Wykonaj tę komendę SQL:

```sql
ALTER TABLE newsletter_subscribers 
MODIFY subscribed_at TIMESTAMP NULL;
```

### Opcja 3: Przebudowa tabeli (ostateczność)

Jeśli masz pustą tabelę lub możesz ją usunąć:

```bash
php artisan migrate:fresh
```

**UWAGA:** To usunie wszystkie dane! Używaj tylko w development!

## Co zostało naprawione

1. **NewsletterController.php** - teraz zawsze ustawia `subscribed_at`:
```php
NewsletterSubscriber::create([
    'email' => $email,
    'is_active' => true,
    'subscribed_at' => now(), // ← dodane
]);
```

2. **Migracja** - kolumna `subscribed_at` jest teraz nullable:
```php
$table->timestamp('subscribed_at')->nullable();
```

## Testowanie

Po naprawie, spróbuj zapisać się do newslettera:

1. Wejdź na stronę główną
2. Przewiń do stopki
3. Wpisz email w pole newslettera
4. Kliknij "Zapisz się"
5. Powinieneś zobaczyć: "Dziękujemy za zapisanie do newslettera!"

## Sprawdzenie w bazie

Możesz sprawdzić czy działa:

```sql
SELECT * FROM newsletter_subscribers;
```

Powinieneś zobaczyć:
- `email` - twój email
- `is_active` - 1
- `subscribed_at` - aktualna data i godzina (lub NULL)

## Problemy?

Jeśli nadal nie działa:

1. Sprawdź logi: `storage/logs/laravel.log`
2. Sprawdź czy migracja się wykonała: `php artisan migrate:status`
3. Sprawdź strukturę tabeli: 
```bash
php artisan tinker
>>> Schema::getColumnListing('newsletter_subscribers')
```

## W przyszłości

Zawsze ustawiaj kolumny jako `nullable()` jeśli nie są wymagane:

```php
// Źle
$table->timestamp('subscribed_at');

// Dobrze
$table->timestamp('subscribed_at')->nullable();

// Albo z wartością domyślną
$table->timestamp('subscribed_at')->default(DB::raw('CURRENT_TIMESTAMP'));
```
