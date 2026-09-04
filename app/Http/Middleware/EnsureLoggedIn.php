<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware de autenticación mock (US-01).
 *
 * Protege las rutas de la app: si el usuario no está logueado
 * (session('logged_in') !== true), redirige a /login.
 */
class EnsureLoggedIn
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('logged_in')) {
            return redirect()->guest(route('login'));
        }

        return $next($request);
    }
}
