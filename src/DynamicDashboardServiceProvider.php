<?php

namespace TheRiptide\LaravelDynamicDashboard;

use Illuminate\Support\ServiceProvider;

class DynamicDashboardServiceProvider extends ServiceProvider
{

    public function boot() {

        $this->loadViewsFrom(__DIR__.'/../views', 'dyndash');

        $this->publishes([
            __DIR__.'/../views' => resource_path('views/vendor/dyndash'),
            __DIR__.'/../config/dyndash.php' => config_path('dyndash.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/../config/dyndash.php', 'dyndash'
        );

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register() {


    }
}