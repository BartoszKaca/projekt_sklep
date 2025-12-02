<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;



class GuestCheckoutMiddleware
{
    

    public function handle(Request $request, Closure $next): Response
    {
        

        $cart = session('cart', ['items' => [], 'total' => 0]);

        if (empty($cart['items'])) {
            return redirect()->route('cart.index')
                ->with('error', 'Twój koszyk jest pusty.');
        }

        

        if ($request->user() && 
            $request->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&
            !$request->user()->hasVerifiedEmail()) {
            return redirect()->route('verify.email.form')
                ->with('error', 'Musisz zweryfikować swój adres email, aby kontynuować.');
        }

        return $next($request);
    }
}
