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

        /*Gate::define('userIsValid', function (User $user) {
            return $user->userIsValid;
        });


        Gate::define('isSuperAdmin', function (User $user) {
            return $user->isSuperAdmin;
        });

        Gate::define('isAdminCli', function (User $user) {
            return $user->isAdminCli;
        });

        Gate::define('isAdminOrSuperAdmin', function ($user) {
            return $user->can('isSuperAdmin') || $user->can('isAdminCli');
        });

        Gate::define('isNotAdminCli', function (User $user) {
            return !$user->isAdminCli;
        });*/
    }
}