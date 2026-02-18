<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HmacMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $secret = env('API_SECRET_KEY');

        $timestamp = $request->header('X-TIMESTAMP');
        $signature = $request->header('X-SIGNATURE');

        if (!$timestamp || !$signature) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        // 1️⃣ Validar expiración (60 segundos)
        if (abs(time() - (int)$timestamp) > 60) {
            return response()->json(['message' => 'Request expirado'], 401);
        }

        // 2️⃣ Obtener body exacto
        $body = $request->getContent();

        // 3️⃣ Crear firma esperada
        $data = $timestamp . $body;
        $expectedSignature = hash_hmac('sha256', $data, $secret);

        // 4️⃣ Comparación segura
        if (!hash_equals($expectedSignature, $signature)) {
            return response()->json(['message' => 'Firma inválida'], 401);
        }

        return $next($request);
    }
}