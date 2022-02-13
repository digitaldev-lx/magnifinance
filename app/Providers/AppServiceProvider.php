<?php

namespace App\Providers;

use App\Gateways\Razorpay;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (\config('app.redirect_https')) {
            \URL::forceScheme('https');
        }
        Paginator::useBootstrap();
//        Schema::defaultStringLength(191);

        $this->app->singleton('razorpay', function () {
            return new Razorpay();
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

}
