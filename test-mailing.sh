#!/bin/bash

# Rap Shop - Mailing System Test Script
# Ten skrypt pomaga testować system mailingowy

echo "🎵 Rap Shop - Mailing System Test Helper"
echo "=========================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if Laravel is in project directory
if [ ! -f "artisan" ]; then
    echo -e "${RED}Error: Nie znaleziono pliku artisan. Uruchom ten skrypt z głównego katalogu projektu Laravel.${NC}"
    exit 1
fi

show_menu() {
    echo ""
    echo "Wybierz test do wykonania:"
    echo ""
    echo "1) Test weryfikacji emailowej"
    echo "2) Test resetowania hasła"
    echo "3) Test potwierdzenia zamówienia"
    echo "4) Test potwierdzenia płatności"
    echo "5) Test aktualizacji statusu"
    echo "6) Uruchom queue worker"
    echo "7) Sprawdź logi"
    echo "8) Sprawdź kolejkę"
    echo "9) Wyczyść kolejkę"
    echo "0) Wyjście"
    echo ""
    echo -n "Wybór: "
}

test_email_verification() {
    echo -e "${GREEN}Test weryfikacji emailowej${NC}"
    echo "================================"
    echo ""
    echo "Ten test utworzy testowego użytkownika i wyśle email weryfikacyjny."
    echo ""
    echo -n "Podaj email testowego użytkownika: "
    read email
    echo -n "Podaj hasło (min. 8 znaków): "
    read -s password
    echo ""
    
    php artisan tinker --execute="
        \$user = App\Models\User::create([
            'name' => 'Test User',
            'email' => '$email',
            'password' => Hash::make('$password')
        ]);
        
        \$verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => \$user->id, 'hash' => sha1(\$user->email)]
        );
        
        Mail::to(\$user->email)->send(new App\Mail\EmailVerificationMail(\$user, \$verificationUrl));
        
        echo 'Email weryfikacyjny wysłany do: ' . \$user->email . PHP_EOL;
        echo 'Link weryfikacyjny: ' . \$verificationUrl . PHP_EOL;
    "
    
    echo -e "${GREEN}✓ Test zakończony. Sprawdź Mailtrap.${NC}"
}

test_password_reset() {
    echo -e "${GREEN}Test resetowania hasła${NC}"
    echo "================================"
    echo ""
    echo -n "Podaj email użytkownika: "
    read email
    
    php artisan tinker --execute="
        \$user = App\Models\User::where('email', '$email')->first();
        
        if (!\$user) {
            echo 'Użytkownik nie istnieje!' . PHP_EOL;
            exit(1);
        }
        
        \$token = Password::broker()->createToken(\$user);
        Mail::to('$email')->send(new App\Mail\PasswordResetMail(\$token, '$email'));
        
        echo 'Email resetowania hasła wysłany do: $email' . PHP_EOL;
    "
    
    echo -e "${GREEN}✓ Test zakończony. Sprawdź Mailtrap.${NC}"
}

test_order_confirmation() {
    echo -e "${GREEN}Test potwierdzenia zamówienia${NC}"
    echo "================================"
    echo ""
    
    php artisan tinker --execute="
        \$order = App\Models\Order::with('items', 'shipping')->latest()->first();
        
        if (!\$order) {
            echo 'Brak zamówień w bazie!' . PHP_EOL;
            exit(1);
        }
        
        if (!\$order->shipping) {
            echo 'Zamówienie nie ma danych wysyłki!' . PHP_EOL;
            exit(1);
        }
        
        Mail::to(\$order->shipping->email)->send(new App\Mail\OrderConfirmationMail(\$order));
        
        echo 'Email potwierdzenia wysłany dla zamówienia: ' . \$order->order_number . PHP_EOL;
        echo 'Wysłano do: ' . \$order->shipping->email . PHP_EOL;
    "
    
    echo -e "${GREEN}✓ Test zakończony. Sprawdź Mailtrap.${NC}"
}

test_payment_confirmation() {
    echo -e "${GREEN}Test potwierdzenia płatności${NC}"
    echo "================================"
    echo ""
    
    php artisan tinker --execute="
        \$order = App\Models\Order::with('items', 'shipping')->where('payment_status', '!=', 'paid')->latest()->first();
        
        if (!\$order) {
            echo 'Brak nieopłaconych zamówień!' . PHP_EOL;
            exit(1);
        }
        
        \$order->markAsPaid();
        
        echo 'Zamówienie ' . \$order->order_number . ' oznaczone jako opłacone.' . PHP_EOL;
        echo 'Email potwierdzenia płatności zostanie wysłany automatycznie przez OrderObserver.' . PHP_EOL;
    "
    
    echo -e "${GREEN}✓ Test zakończony. Sprawdź Mailtrap i logi.${NC}"
}

test_status_update() {
    echo -e "${GREEN}Test aktualizacji statusu${NC}"
    echo "================================"
    echo ""
    echo "Dostępne statusy:"
    echo "1) pending"
    echo "2) processing"
    echo "3) shipped"
    echo "4) delivered"
    echo "5) cancelled"
    echo ""
    echo -n "Wybierz nowy status (1-5): "
    read status_choice
    
    case $status_choice in
        1) new_status="pending";;
        2) new_status="processing";;
        3) new_status="shipped";;
        4) new_status="delivered";;
        5) new_status="cancelled";;
        *) echo "Nieprawidłowy wybór"; return;;
    esac
    
    php artisan tinker --execute="
        \$order = App\Models\Order::with('items', 'shipping')->latest()->first();
        
        if (!\$order) {
            echo 'Brak zamówień w bazie!' . PHP_EOL;
            exit(1);
        }
        
        \$oldStatus = \$order->status;
        \$order->update(['status' => '$new_status']);
        
        echo 'Status zamówienia ' . \$order->order_number . ' zmieniony z ' . \$oldStatus . ' na $new_status' . PHP_EOL;
        echo 'Email aktualizacji zostanie wysłany automatycznie przez OrderObserver.' . PHP_EOL;
    "
    
    echo -e "${GREEN}✓ Test zakończony. Sprawdź Mailtrap.${NC}"
}

start_queue_worker() {
    echo -e "${GREEN}Uruchamianie Queue Worker${NC}"
    echo "================================"
    echo ""
    echo "Queue worker będzie działał w trybie verbose."
    echo "Naciśnij Ctrl+C aby zatrzymać."
    echo ""
    
    php artisan queue:work --verbose
}

check_logs() {
    echo -e "${GREEN}Ostatnie 50 linii logów${NC}"
    echo "================================"
    echo ""
    
    if [ -f "storage/logs/laravel.log" ]; then
        tail -50 storage/logs/laravel.log | grep -i --color=always -E "mail|error|payu|payment|order"
    else
        echo -e "${RED}Plik z logami nie istnieje.${NC}"
    fi
}

check_queue() {
    echo -e "${GREEN}Status kolejki${NC}"
    echo "================================"
    echo ""
    
    echo "Pending jobs:"
    php artisan tinker --execute="
        \$jobs = DB::table('jobs')->count();
        echo 'Liczba oczekujących zadań: ' . \$jobs . PHP_EOL;
        
        if (\$jobs > 0) {
            echo PHP_EOL . 'Szczegóły:' . PHP_EOL;
            DB::table('jobs')->take(10)->get()->each(function(\$job) {
                echo '- Queue: ' . \$job->queue . ', Attempts: ' . \$job->attempts . PHP_EOL;
            });
        }
    "
    
    echo ""
    echo "Failed jobs:"
    php artisan tinker --execute="
        \$failed = DB::table('failed_jobs')->count();
        echo 'Liczba nieudanych zadań: ' . \$failed . PHP_EOL;
        
        if (\$failed > 0) {
            echo PHP_EOL . 'Szczegóły:' . PHP_EOL;
            DB::table('failed_jobs')->take(10)->get()->each(function(\$job) {
                echo '- Connection: ' . \$job->connection . ', Failed at: ' . \$job->failed_at . PHP_EOL;
            });
        }
    "
}

clear_queue() {
    echo -e "${YELLOW}Czyszczenie kolejki${NC}"
    echo "================================"
    echo ""
    echo -n "Czy na pewno chcesz wyczyścić kolejkę? (y/n): "
    read confirm
    
    if [ "$confirm" = "y" ] || [ "$confirm" = "Y" ]; then
        php artisan queue:flush
        echo -e "${GREEN}✓ Kolejka wyczyszczona.${NC}"
    else
        echo "Anulowano."
    fi
}

# Main loop
while true; do
    show_menu
    read choice
    
    case $choice in
        1) test_email_verification;;
        2) test_password_reset;;
        3) test_order_confirmation;;
        4) test_payment_confirmation;;
        5) test_status_update;;
        6) start_queue_worker;;
        7) check_logs;;
        8) check_queue;;
        9) clear_queue;;
        0) echo "Do widzenia!"; exit 0;;
        *) echo -e "${RED}Nieprawidłowy wybór. Spróbuj ponownie.${NC}";;
    esac
    
    echo ""
    echo -n "Naciśnij Enter aby kontynuować..."
    read
done
