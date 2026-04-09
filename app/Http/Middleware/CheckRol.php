<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRol
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // 2. Si es Administrador, siempre tiene acceso a todo
        if ($user->rol === 'Administrador') {
            return $next($request);
        }

        // 3. Verificar si el rol del usuario está en la lista permitida para esta ruta
        if (in_array($user->rol, $roles)) {
            return $next($request);
        }

        // 4. Si no tiene permiso, redirigir al dashboard con un mensaje
        return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
    }
}
