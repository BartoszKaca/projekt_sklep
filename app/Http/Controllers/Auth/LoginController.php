<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    


    use AuthenticatesUsers;

    

    protected $redirectTo = '/';

    

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    

    public function showLoginForm()
    {
        return view('auth.login');
    }

    

    protected function authenticated(Request $request, $user)
    {
        

        if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail()) {
            return redirect()->route('verify.email.form')
                ->with('info', 'Zweryfikuj swój adres email, aby uzyskać pełny dostęp do konta.');
        }

        return redirect()->intended($this->redirectPath());
    }
}
