<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $modulo): mixed
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'No autenticado.');
        }

        // Trae los roles permitidos desde config/roles.php
        // Ejemplo: ['isAdmin', 'isAlmacenista']
        $rolesPermitidos = config("roles.modulos.{$modulo}", []);

        // Verifica si el usuario tiene AL MENOS UNO de los roles permitidos
        // Usa los helpers que ya tienes en tu modelo User
        $tieneAcceso = collect($rolesPermitidos)
            ->contains(fn($helper) => $user->{$helper} === true);

        if (!$tieneAcceso) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
