<?php

namespace App\Providers;

use App\Repositories\CartRepository;
use App\Services\CartService;
use Illuminate\Support\ServiceProvider;

class cartProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    protected $defer = true;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CartService::class, function ($app) {
            return new CartService(new CartRepository());
        });
    }


    public function provides()
    {
        return ['\App\Services\CartService'];
    }
}
