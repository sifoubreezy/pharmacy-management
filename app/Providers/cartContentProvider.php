<?php

namespace App\Providers;

use App\Repositories\CartContentRepository;
use App\Services\CartContentService;
use Illuminate\Support\ServiceProvider;

class cartContentProvider extends ServiceProvider
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
        $this->app->singleton(CartContentService::class, function ($app) {
            return new CartContentService(new CartContentRepository());
        });
    }


    public function provides()
    {
        return ['\App\Services\CartContentService'];
    }
}
