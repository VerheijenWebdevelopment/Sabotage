<?php

namespace App\Providers;

use App\Services\UserService;
use App\Services\GameService;
use App\Services\RoleService;
use App\Services\CardService;
use App\Services\BoardService;
use App\Services\PlayerService;
use App\Services\UploaderService;

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

        $this->app->singleton("cards", function() {
            return new CardService;
        });

        $this->app->singleton("roles", function() {
            return new RoleService;
        });

        $this->app->singleton("board", function() {
            return new BoardService;
        });

        $this->app->singleton("uploader", function() {
            return new UploaderService;
        });
    }

    private function defineGates()
    {
        Gate::define("viewWebSocketsDashboard", function($user) {
            return $user->is_admin;
        });
    }
}
