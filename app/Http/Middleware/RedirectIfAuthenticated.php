<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware mock (US-01): si el usuario ya está logueado y visita una
 * página pública (/login), se lo redirige al /dashboard.
 */
class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('logged_in')) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
