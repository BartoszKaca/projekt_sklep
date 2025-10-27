<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::auth()->user()->isAdmin()) {
            abort(403, 'Brak dostępu do panelu administracyjnego.');
        }

        return $next($request);
    }
}