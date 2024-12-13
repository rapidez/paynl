<?php

namespace Rapidez\Paynl;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Rapidez\Paynl\Actions\CheckSuccessfulOrder;
use Rapidez\Paynl\Listeners\Healthcheck\PaynlHealthcheck;
use TorMorten\Eventy\Facades\Eventy;

class PaynlServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'paynl');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/paynl'),
            ], 'views');

            $this->publishes([
                __DIR__.'/../resources/payment-icons' => public_path('payment-icons'),
            ], 'payment-icons');
        }

        Route::get('paynl/checkout/finish', fn() => redirect(route('checkout.success', request()->query()), 308));

        Eventy::addFilter('checkout.queries.order.data', function($attributes = []) {
            $attributes[] = 'pay_redirect_url';
            return $attributes;
        });

        Eventy::addFilter('checkout.checksuccess', function($success = true) {
            return $success && App::call(CheckSuccessfulOrder::class);
        });

        PaynlHealthcheck::register();
    }
}
