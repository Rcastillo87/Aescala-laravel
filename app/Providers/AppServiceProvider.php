<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Providers\CustomUserProvider;
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
            return Limit::perMinute(5)
                ->by($request->ip());
        });

        RateLimiter::for('device-location', function ($request) {
            return Limit::perMinute(30)
                ->by($request->ip());
        });

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', 'on');
        }

        $acciones = config('roles.acciones', []);
        foreach ($acciones as $accion => $helpers) {
            Gate::define($accion, function ($user) use ($helpers) {
                return collect($helpers)
                    ->contains(fn($helper) => $user->{$helper} === true);
            });
        }

    }
}
