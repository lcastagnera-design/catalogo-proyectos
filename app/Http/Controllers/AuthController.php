<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Autenticación mock (US-01): sin OAuth real.
 * Los botones de login simulan el ingreso poniendo algo en session.
 */
class AuthController extends Controller
{
    /**
     * Muestra la vista de login con los botones mock.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Simula el login (mock). Los botones "Google" y "Microsoft" apuntan acá.
     */
    public function login(Request $request)
    {
        $request->validate([
            'proveedor' => ['required', 'string', 'in:google,microsoft'],
        ]);

        $request->session()->put('logged_in', true);
        $request->session()->put('proveedor', $request->string('proveedor'));

        return redirect()->route('dashboard');
    }

    /**
     * Cierra la sesión (mock) y vuelve al login.
     */
    public function logout(Request $request)
    {
        $request->session()->forget('logged_in');
        $request->session()->forget('proveedor');

        return redirect()->route('login');
    }
}
