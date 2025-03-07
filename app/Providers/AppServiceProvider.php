<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Providers\CustomUserProvider;


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