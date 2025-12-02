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
    


    use SendsPasswordResetEmails;

    

    public function __construct()
    {
        $this->middleware('guest');
    }

    

    public function sendResetLinkEmail(Request $request)
    {
        

        $request->validate(['email' => 'required|email|max:255'], [
            'email.required' => 'Adres email jest wymagany.',
            'email.email' => 'Podaj poprawny adres email.',
        ]);

        try {
            

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                

                return back()
                    ->with('status', 'Link do resetowania hasła został wysłany na Twój adres email.');
            }

            

            $token = Password::broker()->createToken($user);

            

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
