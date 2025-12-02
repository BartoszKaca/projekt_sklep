<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerificationMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class RegisterController extends Controller
{
    


    use RegistersUsers;
    

    protected function redirectPath()
    {
        return '/verify-email';
    }

    

    public function __construct()
    {
        $this->middleware('guest');
    }

    

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Imię jest wymagane.',
            'email.required' => 'Adres email jest wymagany.',
            'email.email' => 'Podaj poprawny adres email.',
            'email.unique' => 'Ten adres email jest już zarejestrowany.',
            'password.required' => 'Hasło jest wymagane.',
            'password.min' => 'Hasło musi mieć minimum 8 znaków.',
            'password.confirmed' => 'Hasła nie są identyczne.',
        ]);
    }

    

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        

        


        

        $this->sendVerificationEmail($user);

        

        $this->guard()->login($user);

        

        \request()->session()->regenerate();

        Log::info('Register: user created', ['id' => $user->id, 'email_verified_at' => $user->email_verified_at]);

        return $request->wantsJson()
                    ? response()->json([], 201)
                    : redirect('/verify-email')
                        ->with('success', 'Konto zostało utworzone! Sprawdź swoją skrzynkę email i kliknij link weryfikacyjny.');
    }

    

    protected function sendVerificationEmail(User $user)
    {
        try {
            

            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $user->id, 'hash' => sha1($user->email)]
            );

            Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationUrl));
        } catch (\Exception $e) {
            Log::error('Failed to send verification email: ' . $e->getMessage());
        }
    }

    

    public function showVerificationForm()
    {
        $user = Auth::user();
        Log::info('ShowVerificationForm: User:', ['id' => optional($user)->id, 'email_verified_at' => optional($user)->email_verified_at]);

        if ($user && $user->hasVerifiedEmail()) {
            Log::info('ShowVerificationForm: User already verified, redirecting to home', ['id' => $user->id]);
            return redirect()->route('home');
        }

        return view('auth.verify-email');
    }


    

}
