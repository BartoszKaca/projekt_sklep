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
    

    public function subscribe(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = $request->email;

        $existing = NewsletterSubscriber::where('email', $email)->first();
        $isNew = !$existing;

        if ($existing) {
            if ($existing->is_active) {
                $message = 'Ten adres email jest już zapisany do newslettera.';
                $shouldSendEmail = false;
            } else {
                $existing->update([
                    'is_active' => true,
                    'subscribed_at' => now()
                ]);
                $message = 'Twoja subskrypcja została reaktywowana.';
                $shouldSendEmail = true;
            }
        } else {
            NewsletterSubscriber::create([
                'email' => $email,
                'is_active' => true,
                'subscribed_at' => now(),
            ]);
            $message = 'Dziękujemy za zapisanie do newslettera!';
            $shouldSendEmail = true;
        }

        if ($shouldSendEmail) {
            try {
                Mail::to($email)->send(new NewsletterSubscriptionMail($email));
            } catch (\Exception $e) {
                Log::error('Błąd wysyłki emaila newslettera: ' . $e->getMessage());
            }
        }

        

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        

        return redirect()->back()->with('success', $message);
    }

    

    public function unsubscribe(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $subscriber = NewsletterSubscriber::where('email', $request->email)->first();

        if ($subscriber) {
            $subscriber->update(['is_active' => false, 'unsubscribed_at' => now()]);
            $message = 'Zostałeś wypisany z newslettera.';
        } else {
            $message = 'Podany adres email nie jest zapisany do newslettera.';
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }
}
