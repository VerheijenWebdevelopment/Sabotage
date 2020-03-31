<?php

namespace App\Providers;

use App\Services\UserService;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerServices();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->defineGates();
    }

    private function registerServices()
    {
        $this->app->singleton("users", function() {
            return new UserService;
        });
    }

    private function defineGates()
    {
        Gate::define("viewWebSocketsDashboard", function($user) {
            return $user->is_admin;
        });
    }
}
