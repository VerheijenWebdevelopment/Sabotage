<?php

namespace App\Providers;

use App\Services\UserService;
use App\Services\GameService;
use App\Services\PlayerService;

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

        $this->app->singleton("games", function() {
            return new GameService;
        });

        $this->app->singleton("players", function() {
            return new PlayerService;
        });
    }

    private function defineGates()
    {
        Gate::define("viewWebSocketsDashboard", function($user) {
            return $user->is_admin;
        });
    }
}
