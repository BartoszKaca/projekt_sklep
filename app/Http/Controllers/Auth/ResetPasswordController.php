<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    


    use ResetsPasswords;

    

    protected $redirectTo = '/';

    

    public function __construct()
    {
        

        $this->middleware('guest');
    }

    

    protected function authenticated(Request $request, $user)
    {
        return redirect()->route('login')->with('success', 'Hasło zostało pomyślnie zmienione. Zaloguj się nowym hasłem.');
    }
}
