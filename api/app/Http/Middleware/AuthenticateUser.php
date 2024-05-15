<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateUser
{
    public function handle(Request $request, Closure $next)
    {
        // Verifica si el usuario está autenticado
        if (!$request->session()->has('user_id')) {
            // Redirige a la página de inicio si no está autenticado
            return redirect('/');
        }
        // Continua con la solicitud
        return $next($request);
    }
}

