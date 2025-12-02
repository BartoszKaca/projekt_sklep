# 🧪 Rap Shop - Komendy testowe dla systemu mailingowego

Ten plik zawiera gotowe komendy do testowania systemu mailingowego przez Artisan Tinker.

## Przygotowanie

Uruchom Tinker:
```bash
php artisan tinker
```

## 1. Test weryfikacji emailowej

### Utworzenie testowego użytkownika i wysłanie emaila
```php
$user = App\Models\User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => Hash::make('password123')
]);

$verificationUrl = URL::temporarySignedRoute(
    'verification.verify',
    now()->addMinutes(60),
    ['id' => $user->id, 'hash' => sha1($user->email)]
);

Mail::to($user->email)->send(new App\Mail\EmailVerificationMail($user, $verificationUrl));

echo "Email weryfikacyjny wysłany do: {$user->email}\n";
echo "Link: {$verificationUrl}\n";
```

### Ponowne wysłanie emaila dla istniejącego użytkownika
```php
$user = App\Models\User::where('email', 'test@example.com')->first();

$verificationUrl = URL::temporarySignedRoute(
    'verification.verify',
    now()->addMinutes(60),
    ['id' => $user->id, 'hash' => sha1($user->email)]
);

Mail::to($user->email)->send(new App\Mail\EmailVerificationMail($user, $verificationUrl));
```

## 2. Test resetowania hasła

### Wysłanie emaila resetującego
```php
$email = 'test@example.com';
$user = App\Models\User::where('email', $email)->first();

if ($user) {
    $token = Password::broker()->createToken($user);
    Mail::to($email)->send(new App\Mail\PasswordResetMail($token, $email));
    echo "Email resetowania hasła wysłany do: {$email}\n";
} else {
    echo "Użytkownik nie istnieje!\n";
}
```

## 3. Test potwierdzenia zamówienia

### Wysłanie emaila dla ostatniego zamówienia
```php
$order = App\Models\Order::with('items', 'shipping')->latest()->first();

if ($order && $order->shipping) {
    Mail::to($order->shipping->email)->send(new App\Mail\OrderConfirmationMail($order));
    echo "Email potwierdzenia wysłany dla zamówienia: {$order->order_number}\n";
    echo "Wysłano do: {$order->shipping->email}\n";
} else {
    echo "Brak zamówień w bazie!\n";
}
```

### Wysłanie dla konkretnego zamówienia
```php
$orderNumber = 'ORD-20251202-ABC123'; // Zmień na rzeczywisty numer
$order = App\Models\Order::with('items', 'shipping')
    ->where('order_number', $orderNumber)
    ->first();

if ($order && $order->shipping) {
    Mail::to($order->shipping->email)->send(new App\Mail\OrderConfirmationMail($order));
    echo "Email wysłany!\n";
}
```

## 4. Test potwierdzenia płatności

### Oznacz zamówienie jako opłacone (wyśle email automatycznie)
```php
$order = App\Models\Order::with('items', 'shipping')
    ->where('payment_status', '!=', 'paid')
    ->latest()
    ->first();

if ($order) {
    echo "Zamówienie: {$order->order_number}\n";
    echo "Status przed: {$order->payment_status}\n";
    
    $order->markAsPaid();
    
    echo "Status po: {$order->payment_status}\n";
    echo "Email potwierdzenia płatności zostanie wysłany automatycznie przez OrderObserver.\n";
} else {
    echo "Brak nieopłaconych zamówień!\n";
}
```

### Ręczne wysłanie emaila potwierdzenia płatności
```php
$order = App\Models\Order::with('items', 'shipping')->latest()->first();

if ($order && $order->shipping) {
    Mail::to($order->shipping->email)->send(new App\Mail\PaymentConfirmationMail($order));
    echo "Email potwierdzenia płatności wysłany!\n";
}
```

## 5. Test aktualizacji statusu zamówienia

### Zmiana statusu (wyśle email automatycznie)
```php
$order = App\Models\Order::with('items', 'shipping')->latest()->first();

if ($order) {
    $oldStatus = $order->status;
    $newStatus = 'processing'; // Zmień na: pending, processing, shipped, delivered, cancelled
    
    echo "Zamówienie: {$order->order_number}\n";
    echo "Status przed: {$oldStatus}\n";
    
    $order->update(['status' => $newStatus]);
    
    echo "Status po: {$newStatus}\n";
    echo "Email aktualizacji zostanie wysłany automatycznie przez OrderObserver.\n";
}
```

### Zmiana statusu na 'shipped' z danymi śledzenia
```php
$order = App\Models\Order::with('items', 'shipping')->latest()->first();

if ($order) {
    $order->markAsShipped('1234567890', 'DPD');
    echo "Zamówienie {$order->order_number} oznaczone jako wysłane.\n";
    echo "Numer śledzenia: {$order->tracking_number}\n";
    echo "Kurier: {$order->carrier}\n";
}
```

### Zmiana statusu na 'delivered'
```php
$order = App\Models\Order::with('items', 'shipping')->latest()->first();

if ($order) {
    $order->markAsDelivered();
    echo "Zamówienie {$order->order_number} oznaczone jako dostarczone.\n";
}
```

## 6. Sprawdzanie statusu kolejki

### Liczba zadań w kolejce
```php
$pendingJobs = DB::table('jobs')->count();
$failedJobs = DB::table('failed_jobs')->count();

echo "Pending jobs: {$pendingJobs}\n";
echo "Failed jobs: {$failedJobs}\n";
```

### Szczegóły pending jobs
```php
DB::table('jobs')->take(10)->get()->each(function($job) {
    echo "Queue: {$job->queue}, Attempts: {$job->attempts}\n";
});
```

### Szczegóły failed jobs
```php
DB::table('failed_jobs')->take(10)->get()->each(function($job) {
    echo "Connection: {$job->connection}\n";
    echo "Queue: {$job->queue}\n";
    echo "Failed at: {$job->failed_at}\n";
    echo "Exception: " . substr($job->exception, 0, 100) . "...\n";
    echo "---\n";
});
```

## 7. Sprawdzanie użytkowników i zamówień

### Lista użytkowników
```php
App\Models\User::select('id', 'name', 'email', 'email_verified_at')->get()->each(function($user) {
    $verified = $user->email_verified_at ? '✓' : '✗';
    echo "{$user->id}. {$user->name} ({$user->email}) {$verified}\n";
});
```

### Lista zamówień
```php
App\Models\Order::with('shipping')->latest()->take(10)->get()->each(function($order) {
    $email = $order->shipping ? $order->shipping->email : 'brak';
    echo "{$order->order_number} | Status: {$order->status} | Płatność: {$order->payment_status} | Email: {$email}\n";
});
```

### Zamówienia nieopłacone
```php
App\Models\Order::unpaid()->with('shipping')->get()->each(function($order) {
    echo "{$order->order_number} | {$order->payment_status} | {$order->total} zł\n";
});
```

### Zamówienia opłacone
```php
App\Models\Order::paid()->with('shipping')->get()->each(function($order) {
    echo "{$order->order_number} | Opłacone: {$order->paid_at}\n";
});
```

## 8. Symulacja webhook PayU

### Symulacja płatności PayU - sukces
```php
$order = App\Models\Order::latest()->first();

if ($order) {
    echo "Symulacja webhook PayU dla zamówienia: {$order->order_number}\n";
    echo "Status przed: payment_status={$order->payment_status}, status={$order->status}\n";
    
    // Symuluj webhook PayU - płatność zakończona sukcesem
    $order->markAsPaid();
    if ($order->status === 'pending') {
        $order->update(['status' => 'processing']);
    }
    
    $order->refresh();
    echo "Status po: payment_status={$order->payment_status}, status={$order->status}\n";
    echo "Paid at: {$order->paid_at}\n";
}
```

### Symulacja płatności PayU - anulowanie
```php
$order = App\Models\Order::latest()->first();

if ($order) {
    $order->update([
        'payment_status' => 'failed',
        'status' => 'cancelled'
    ]);
    
    echo "Zamówienie {$order->order_number} anulowane (symulacja PayU CANCELED)\n";
}
```

## 9. Testowanie w locie

### Wysłanie wszystkich typów emaili na raz
```php
// Użytkownik
$user = App\Models\User::first();

// Zamówienie
$order = App\Models\Order::with('items', 'shipping')->latest()->first();

// 1. Email weryfikacyjny
$verificationUrl = URL::temporarySignedRoute(
    'verification.verify',
    now()->addMinutes(60),
    ['id' => $user->id, 'hash' => sha1($user->email)]
);
Mail::to($user->email)->send(new App\Mail\EmailVerificationMail($user, $verificationUrl));
echo "1. Email weryfikacyjny wysłany\n";

// 2. Reset hasła
$token = Password::broker()->createToken($user);
Mail::to($user->email)->send(new App\Mail\PasswordResetMail($token, $user->email));
echo "2. Email resetowania hasła wysłany\n";

// 3. Potwierdzenie zamówienia
if ($order && $order->shipping) {
    Mail::to($order->shipping->email)->send(new App\Mail\OrderConfirmationMail($order));
    echo "3. Potwierdzenie zamówienia wysłane\n";
    
    // 4. Potwierdzenie płatności
    Mail::to($order->shipping->email)->send(new App\Mail\PaymentConfirmationMail($order));
    echo "4. Potwierdzenie płatności wysłane\n";
    
    // 5. Aktualizacja statusu
    Mail::to($order->shipping->email)->send(
        new App\Mail\OrderStatusUpdateMail($order, 'pending', 'processing')
    );
    echo "5. Aktualizacja statusu wysłana\n";
}

echo "\nWszystkie emaile wysłane! Sprawdź Mailtrap.\n";
```

## 10. Czyszczenie i reset

### Usunięcie testowych użytkowników
```php
App\Models\User::where('email', 'LIKE', 'test%')->delete();
echo "Testowi użytkownicy usunięci.\n";
```

### Wyczyszczenie kolejki
```php
DB::table('jobs')->truncate();
DB::table('failed_jobs')->truncate();
echo "Kolejka wyczyszczona.\n";
```

## Przydatne aliasy

Jeśli często używasz Tinker, dodaj te aliasy do `~/.bashrc` lub `~/.zshrc`:

```bash
alias tinker='php artisan tinker'
alias queue='php artisan queue:work'
alias queuel='php artisan queue:listen'
alias migrate='php artisan migrate'
alias fresh='php artisan migrate:fresh --seed'
```

## Tips & Tricks

### Logging w Tinker
```php
Log::info('Test message from Tinker');
```

### Sprawdzenie konfiguracji mail
```php
config('mail.from.address');
config('mail.from.name');
config('mail.mailers.smtp.host');
```

### Sprawdzenie czy queue worker działa
W osobnym terminalu:
```bash
ps aux | grep "queue:work"
```

### Real-time monitoring logów podczas testowania
W osobnym terminalu:
```bash
tail -f storage/logs/laravel.log | grep -i --color=always "mail\|error\|payu"
```

---

**Happy Testing! 🧪**
