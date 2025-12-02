<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterSubscriptionMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    // Zapisz się do newslettera
    public function subscribe(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = $request->email;

        // Sprawdź czy email już istnieje
        $existing = NewsletterSubscriber::where('email', $email)->first();

        if ($existing) {
            if ($existing->is_active) {
                $message = 'Ten adres email jest już zapisany do newslettera.';
            } else {
                // Reaktywuj subskrypcję
                $existing->update([
                    'is_active' => true,
                    'subscribed_at' => now()
                ]);
                $message = 'Twoja subskrypcja została reaktywowana.';
            }
        } else {
            // Utwórz nową subskrypcję
            NewsletterSubscriber::create([
                'email' => $email,
                'is_active' => true,
                'subscribed_at' => now(),
            ]);
            $message = 'Dziękujemy za zapisanie do newslettera!';

            // Wyślij email powitalny
            try {
                Mail::to($email)->send(new NewsletterSubscriptionMail($email));
            } catch (\Exception $e) {
                Log::error('Błąd wysyłki emaila: ' . $e->getMessage());
            }
        }

        // Odpowiedź AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        // Zwykłe przekierowanie
        return redirect()->back()->with('success', $message);
    }

    // Wypisz się z newslettera
    public function unsubscribe(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $subscriber = NewsletterSubscriber::where('email', $request->email)->first();

        if ($subscriber) {
            $subscriber->update(['is_active' => false]);
            $message = 'Zostałeś wypisany z newslettera.';
        } else {
            $message = 'Podany adres email nie jest zapisany do newslettera.';
        }

        // Odpowiedź AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        // Zwykłe przekierowanie
        return redirect()->back()->with('success', $message);
    }
}
