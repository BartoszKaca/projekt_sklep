<?php

namespace App\Console\Commands;

use App\Mail\EmailVerificationMail;
use App\Mail\OrderConfirmationMail;
use App\Mail\OrderStatusUpdateMail;
use App\Mail\PasswordResetMail;
use App\Mail\PaymentConfirmationMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;

class TestMailingSystem extends Command
{
    

    protected $signature = 'mailing:test {type?}';

    

    protected $description = 'Test mailing system - wysyła testowe emaile';

    

    public function handle()
    {
        $type = $this->argument('type');

        if (!$type) {
            $type = $this->choice(
                'Wybierz typ emaila do przetestowania:',
                [
                    'all' => 'Wszystkie emaile',
                    'verification' => 'Weryfikacja emailowa',
                    'password' => 'Reset hasła',
                    'order' => 'Potwierdzenie zamówienia',
                    'payment' => 'Potwierdzenie płatności',
                    'status' => 'Aktualizacja statusu',
                ],
                'all'
            );
        }

        $this->info("🎵 Rap Shop - Test systemu mailingowego");
        $this->newLine();

        switch ($type) {
            case 'all':
                $this->testAll();
                break;
            case 'verification':
                $this->testEmailVerification();
                break;
            case 'password':
                $this->testPasswordReset();
                break;
            case 'order':
                $this->testOrderConfirmation();
                break;
            case 'payment':
                $this->testPaymentConfirmation();
                break;
            case 'status':
                $this->testStatusUpdate();
                break;
            default:
                $this->error('Nieznany typ testu');
                return 1;
        }

        $this->newLine();
        $this->info('✓ Test zakończony. Sprawdź Mailtrap: https://mailtrap.io');

        return 0;
    }

    

    protected function testAll()
    {
        $this->info('Wysyłanie wszystkich typów emaili...');
        $this->newLine();

        $this->testEmailVerification();
        $this->newLine();

        $this->testPasswordReset();
        $this->newLine();

        $this->testOrderConfirmation();
        $this->newLine();

        $this->testPaymentConfirmation();
        $this->newLine();

        $this->testStatusUpdate();
    }

    

    protected function testEmailVerification()
    {
        $this->info('📧 Test weryfikacji emailowej...');

        $email = $this->ask('Podaj email testowego użytkownika', 'test@example.com');

        

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->warn('Użytkownik nie istnieje. Tworzenie nowego...');
            $user = User::create([
                'name' => 'Test User',
                'email' => $email,
                'password' => Hash::make('password123'),
            ]);
            $this->info("Utworzono użytkownika: {$email} (hasło: password123)");
        }

        

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        

        try {
            Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationUrl));
            $this->info("✓ Email weryfikacyjny wysłany do: {$email}");
            $this->line("Link weryfikacyjny: {$verificationUrl}");
        } catch (\Exception $e) {
            $this->error("✗ Błąd wysyłania: " . $e->getMessage());
        }
    }

    

    protected function testPasswordReset()
    {
        $this->info('🔐 Test resetowania hasła...');

        $email = $this->ask('Podaj email użytkownika', 'test@example.com');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Użytkownik {$email} nie istnieje!");
            return;
        }

        

        $token = Password::broker()->createToken($user);

        

        try {
            Mail::to($email)->send(new PasswordResetMail($token, $email));
            $this->info("✓ Email resetowania hasła wysłany do: {$email}");
            $this->line("Token: {$token}");
        } catch (\Exception $e) {
            $this->error("✗ Błąd wysyłania: " . $e->getMessage());
        }
    }

    

    protected function testOrderConfirmation()
    {
        $this->info('📦 Test potwierdzenia zamówienia...');

        

        $order = Order::with('items', 'shipping')->latest()->first();

        if (!$order) {
            $this->error('Brak zamówień w bazie!');
            return;
        }

        if (!$order->shipping) {
            $this->error('Zamówienie nie ma danych wysyłki!');
            return;
        }

        $this->line("Zamówienie: {$order->order_number}");
        $this->line("Email: {$order->shipping->email}");

        

        try {
            Mail::to($order->shipping->email)->send(new OrderConfirmationMail($order));
            $this->info("✓ Email potwierdzenia zamówienia wysłany");
        } catch (\Exception $e) {
            $this->error("✗ Błąd wysyłania: " . $e->getMessage());
        }
    }

    

    protected function testPaymentConfirmation()
    {
        $this->info('💳 Test potwierdzenia płatności...');

        

        $order = Order::with('items', 'shipping')->latest()->first();

        if (!$order) {
            $this->error('Brak zamówień w bazie!');
            return;
        }

        if (!$order->shipping) {
            $this->error('Zamówienie nie ma danych wysyłki!');
            return;
        }

        $this->line("Zamówienie: {$order->order_number}");
        $this->line("Email: {$order->shipping->email}");
        $this->line("Status płatności: {$order->payment_status}");

        if ($this->confirm('Oznaczyć zamówienie jako opłacone?', true)) {
            $order->markAsPaid();
            $this->info('Zamówienie oznaczone jako opłacone.');
            $this->info('Email zostanie wysłany automatycznie przez OrderObserver.');
        } else {
            

            try {
                Mail::to($order->shipping->email)->send(new PaymentConfirmationMail($order));
                $this->info("✓ Email potwierdzenia płatności wysłany");
            } catch (\Exception $e) {
                $this->error("✗ Błąd wysyłania: " . $e->getMessage());
            }
        }
    }

    

    protected function testStatusUpdate()
    {
        $this->info('🔔 Test aktualizacji statusu...');

        

        $order = Order::with('items', 'shipping')->latest()->first();

        if (!$order) {
            $this->error('Brak zamówień w bazie!');
            return;
        }

        if (!$order->shipping) {
            $this->error('Zamówienie nie ma danych wysyłki!');
            return;
        }

        $this->line("Zamówienie: {$order->order_number}");
        $this->line("Obecny status: {$order->status}");
        $this->line("Email: {$order->shipping->email}");

        $newStatus = $this->choice(
            'Wybierz nowy status:',
            ['pending', 'processing', 'shipped', 'delivered', 'cancelled'],
            1
        );

        $oldStatus = $order->status;

        if ($this->confirm("Zmienić status z '{$oldStatus}' na '{$newStatus}'?", true)) {
            $order->update(['status' => $newStatus]);
            $this->info("Status zmieniony na: {$newStatus}");
            $this->info('Email zostanie wysłany automatycznie przez OrderObserver.');
        } else {
            

            try {
                Mail::to($order->shipping->email)->send(
                    new OrderStatusUpdateMail($order, $oldStatus, $newStatus)
                );
                $this->info("✓ Email aktualizacji statusu wysłany");
            } catch (\Exception $e) {
                $this->error("✗ Błąd wysyłania: " . $e->getMessage());
            }
        }
    }
}
