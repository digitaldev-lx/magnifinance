<?php

namespace DigitalDevLX\Magnifinance;

use Illuminate\Support\ServiceProvider;

class MagnifinanceServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'magnifinance');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'magnifinance');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
         $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('magnifinance.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/magnifinance'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/magnifinance'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/magnifinance'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'magnifinance');

        app()->bind('magnifinance', function(){  //Keep in mind this "check" must be return from facades accessor
            return new Magnifinance();
        });

        // Register the main class to use with the facade
        $this->app->singleton('magnifinance', function () {
            return new Magnifinance;
        });
    }
}
