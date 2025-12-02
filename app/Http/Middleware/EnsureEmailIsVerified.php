<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || 
            ($request->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&
            !$request->user()->hasVerifiedEmail())) {
            return redirect()->route('verify.email.form');
        }

        return $next($request);
    }
}
