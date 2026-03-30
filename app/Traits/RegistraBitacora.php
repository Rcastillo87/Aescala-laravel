<?php

namespace App\Traits;

use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait RegistraBitacora
{
    protected function registrar(
        Request $request,
        string  $servicio,
        string  $tipo,
        int     $statusCode,
        int     $inicio,
        array   $error = null
    ): void {
        try {
            Bitacora::create([
                'id_user'     => Auth::id(),
                'servicio'    => $servicio,
                'metodo'      => $request->method(),
                'url'         => $request->fullUrl(),
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
                'payload'     => $request->all(),
                'error'       => $error,
                'tipo'        => $tipo,
                'status_code' => $statusCode,
                'duracion_ms' => intval((microtime(true) - $inicio) * 1000),
            ]);
        } catch (\Exception $e) {
            // Si falla la bitácora no debe romper el flujo principal
            \Log::error('Error al registrar bitácora: ' . $e->getMessage());
        }
    }
}
