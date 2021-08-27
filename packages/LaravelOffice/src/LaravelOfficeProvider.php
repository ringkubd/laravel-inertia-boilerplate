<?php

namespace Anwar\LaravelOffice;
use Illuminate\Support\ServiceProvider;

class LaravelOfficeProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/web.php');
        $this->publishes([
            __DIR__.'/view/component' => resource_path('js/Pages/LaravelOffice')
        ], 'laravel-office-component');
    }
}
