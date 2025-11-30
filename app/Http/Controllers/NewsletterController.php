<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterSubscriptionMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * Controller for newsletter subscriptions.
 */
class NewsletterController extends Controller
{
    /**
     * Subscribe to newsletter.
     */
    public function subscribe(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = $request->email;

        // Check if already subscribed
        $existing = NewsletterSubscriber::where('email', $email)->first();

        if ($existing) {
            if ($existing->is_active) {
                $message = 'Ten adres email jest już zapisany do newslettera.';
            } else {
                // Reactivate subscription
                $existing->update(['is_active' => true]);
                $message = 'Twoja subskrypcja została reaktywowana.';
            }
        } else {
            NewsletterSubscriber::create([
                'email' => $email,
                'is_active' => true,
            ]);
            $message = 'Dziękujemy za zapisanie do newslettera!';

            // Send confirmation email
            try {
                Mail::to($email)->send(new NewsletterSubscriptionMail($email));
            } catch (\Exception $e) {
                Log::error('Failed to send newsletter confirmation: ' . $e->getMessage());
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

    /**
     * Unsubscribe from newsletter.
     */
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

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }
}