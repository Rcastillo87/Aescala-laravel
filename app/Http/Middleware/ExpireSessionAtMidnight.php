<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ExpireSessionAtMidnight
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // 1. Si el usuario no tiene una fecha de expiración asignada, le ponemos la medianoche de HOY
            if (!session()->has('session_expires_at')) {
                session(['session_expires_at' => Carbon::today()->endOfDay()->timestamp]);
            }

            // 2. Si la hora actual ya superó la medianoche guardada, destruimos la sesión
            if (time() > session('session_expires_at')) {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();

                return redirect()->route('login')->with('status', 'Tu sesión ha expirado automáticamente a la medianoche.');
            }
        }

        return $next($request);
    }
}
