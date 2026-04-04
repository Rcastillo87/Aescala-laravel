<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('device-register', function ($request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('device-location', function ($request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', 'on');
        }

        // =======================================================
        //  GATES DE MÓDULOS
        //  Prefijo 'modulo.' — usados en el sidebar con @can / @canany
        //  Ej: @can('modulo.cartera') ... @endcan
        // =======================================================
        foreach (config('roles.modulos', []) as $modulo => $helpers) {
            Gate::define("modulo.{$modulo}", function ($user) use ($helpers) {
                return collect($helpers)
                    ->contains(fn($helper) => $user->{$helper} === true);
            });
        }

        // =======================================================
        //  GATES DE ACCIONES
        //  Nombre real de la ruta — usados en controladores y vistas
        //  Ej: Gate::authorize('cartera.deletePago')
        //  Ej: @can('proyecto.editStatus')
        // =======================================================
        foreach (config('roles.acciones', []) as $accion => $helpers) {
            Gate::define($accion, function ($user) use ($helpers) {
                return collect($helpers)
                    ->contains(fn($helper) => $user->{$helper} === true);
            });
        }
    }
}
