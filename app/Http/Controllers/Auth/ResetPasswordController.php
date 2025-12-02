<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Reset password should be accessible to guests
        $this->middleware('guest');
    }

    /**
     * Redirect path after successful password reset.
     *
     * @param Request $request
     * @param mixed $user
     * @return string
     */
    protected function authenticated(Request $request, $user)
    {
        return redirect()->route('login')->with('success', 'Hasło zostało pomyślnie zmienione. Zaloguj się nowym hasłem.');
    }
}
