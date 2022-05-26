<?php

namespace TheRiptide\LaravelDynamicDashboard;

use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;
use TheRiptide\LaravelDynamicDashboard\Http\Livewire\DashboardIndex;
use TheRiptide\LaravelDynamicDashboard\Http\Livewire\DashboardManage;

class DynamicDashboardServiceProvider extends ServiceProvider
{

    public function boot() {

        $this->loadViewsFrom(__DIR__.'/../views', 'dyndash');

        $this->publishes([
            __DIR__.'/../views' => resource_path('views/vendor/dyndash'),
            __DIR__.'/../config/dyndash.php' => config_path('dyndash.php'),
            __DIR__.'/ExampleType/Example.php' => app_path('Dyndash/Example.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/../config/dyndash.php', 'dyndash'
        );

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');


        Livewire::component('dashboard-manage', DashboardManage::class);
        Livewire::component('dashboard-index', DashboardIndex::class);

        $this->app->singleton('ManageImage', function() {
            return new ManageImage();
        });
    }

    public function register() {


    }
}