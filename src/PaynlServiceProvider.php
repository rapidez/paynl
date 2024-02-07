<?php

namespace Rapidez\Paynl;

use Illuminate\Support\ServiceProvider;

class PaynlServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'paynl');

        $this->mergeConfigFrom(__DIR__.'/../config/rapidez/paynl.php', 'rapidez.paynl');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/paynl'),
            ], 'views');

            $this->publishes([
                __DIR__.'/../resources/payment-icons' => public_path('payment-icons'),
            ], 'payment-icons');

            $this->publishes([
                __DIR__.'/../config/rapidez/paynl.php' => config_path('rapidez/paynl.php'),
            ], 'config');
        }
    }
}
