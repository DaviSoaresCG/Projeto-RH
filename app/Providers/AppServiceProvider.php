<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // gate para verificar se o usuario é admin
        Gate::define('admin', function () {
            return auth()->user()->role === 'admin';
        });

        // gate para verificar se o usuario é RH
        Gate::define('rh', function () {
            return auth()->user()->role === 'rh';
        });

        // gate para verificar se o usuario é um colaborador normal
        Gate::define('colaborator', function(){
            return auth()->user()->role === 'colaborator';
        });
    }
}
