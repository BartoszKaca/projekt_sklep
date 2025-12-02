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
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {type} {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        $email = $this->argument('email');

        $this->info("Testing {$type} email to {$email}...");

        try {
            switch ($type) {
                case 'verification':
                    $this->testEmailVerification($email);
                    break;
                case 'order-confirmation':
                    $this->testOrderConfirmation($email);
                    break;
                case 'payment-confirmation':
                    $this->testPaymentConfirmation($email);
                    break;
                case 'order-status':
                    $this->testOrderStatusUpdate($email);
                    break;
                case 'password-reset':
                    $this->testPasswordReset($email);
                    break;
                default:
                    $this->error("Unknown email type: {$type}");
                    $this->info("Available types: verification, order-confirmation, payment-confirmation, order-status, password-reset");
                    return 1;
            }

            $this->info("Email sent successfully!");
            return 0;

        } catch (\Exception $e) {
            $this->error("Failed to send email: " . $e->getMessage());
            return 1;
        }
    }

    private function testEmailVerification($email)
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        Mail::to($email)->send(
            new EmailVerificationMail($user, route('verification.verify', ['id' => $user->id, 'hash' => sha1($user->email)]))
        );
    }

    private function testOrderConfirmation($email)
    {
        $order = Order::with(['items.product', 'shipping'])->latest()->first();

        if (!$order) {
            $this->error("No orders found in database. Create an order first.");
            return;
        }

        Mail::to($email)->send(new OrderConfirmationMail($order));
    }

    private function testPaymentConfirmation($email)
    {
        $order = Order::with(['items.product', 'shipping'])->latest()->first();

        if (!$order) {
            $this->error("No orders found in database. Create an order first.");
            return;
        }

        Mail::to($email)->send(new PaymentConfirmationMail($order));
    }

    private function testOrderStatusUpdate($email)
    {
        $order = Order::with(['items.product', 'shipping'])->latest()->first();

        if (!$order) {
            $this->error("No orders found in database. Create an order first.");
            return;
        }

        Mail::to($email)->send(
            new OrderStatusUpdateMail($order, 'pending', 'processing')
        );
    }

    private function testPasswordReset($email)
    {
        Mail::to($email)->send(
            new PasswordResetMail('test-token-' . time(), $email)
        );
    }
}
