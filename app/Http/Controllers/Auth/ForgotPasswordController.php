<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Validate email
        $request->validate(['email' => 'required|email|max:255'], [
            'email.required' => 'Adres email jest wymagany.',
            'email.email' => 'Podaj poprawny adres email.',
        ]);

        try {
            // Get user by email - directly from database
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                // For security, return the same message as if user exists
                return back()
                    ->with('status', 'Link do resetowania hasła został wysłany na Twój adres email.');
            }

            // Create password reset token
            $token = Password::broker()->createToken($user);

            // Send custom email
            Mail::to($request->email)->send(new PasswordResetMail($token, $request->email));

            return back()
                ->with('status', 'Link do resetowania hasła został wysłany na Twój adres email.');

        } catch (\Exception $e) {
            Log::error('Password reset email error: ' . $e->getMessage());
            
            return back()
                ->withErrors(['email' => 'Wystąpił błąd podczas wysyłania emaila. Spróbuj ponownie później.']);
        }
    }
}
