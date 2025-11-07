<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        // Verificar si está autenticado
        if (!$user) {
            return response()->json([
                'message' => 'No autenticado',
                'success' => false
            ], 401);
        }

        // Verificar si tiene el rol permitido
        if (!in_array($user->rol, $roles)) {
            return response()->json([
                'message' => 'No tienes permisos para acceder a este recurso',
                'success' => false
            ], 403);
        }

        return $next($request);
    }
}
