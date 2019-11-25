<?php

namespace CenarioWeb;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PostmanServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->boot();
    }

    public function boot()
    {
        $this->registerRoutes();

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    public function registerRoutes()
    {
        Route::group([
            'namespace' => '\CenarioWeb\Controllers',
            'as'        => 'postman.',
            'prefix'    => '/postman/api'
        ], function () {
            Route::post('/request', 'RequestController@request')->name('request');
        });
    }
}
