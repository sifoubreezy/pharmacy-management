<?php

namespace App\Providers;

use App\Models\Purchases;
use App\Repositories\PurchasesRepository;
use App\Services\PurchasesService;
use Illuminate\Support\ServiceProvider;

class PurchasesServiceProvider extends ServiceProvider
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
        $this->app->singleton(Purchases::class, function ($app) {
            return new PurchasesService(new PurchasesRepository());
        });
    }


    public function provides()
    {
        return ['\App\Services\PurchasesService'];
    }
}
