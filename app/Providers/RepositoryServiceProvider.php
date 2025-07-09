<?php

namespace App\Providers;

use App\Repositories\CartRepository;
use App\Repositories\CouponRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ShippingRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CartRepository::class, function () {
            return new CartRepository();
        });

        $this->app->singleton(CouponRepository::class, function () {
            return new CouponRepository();
        });

        $this->app->singleton(ShippingRepository::class, function () {
            return new ShippingRepository();
        });

        $this->app->singleton(OrderRepository::class, function () {
            return new OrderRepository();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
