<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware for guest checkout functionality.
 * Allows both authenticated users and guests to proceed with checkout.
 */
class GuestCheckoutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if cart is empty
        $cart = session('cart', ['items' => [], 'total' => 0]);

        if (empty($cart['items'])) {
            return redirect()->route('cart.index')
                ->with('error', 'Twój koszyk jest pusty.');
        }

        return $next($request);
    }
}