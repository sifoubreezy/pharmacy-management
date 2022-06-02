<?php

namespace App\Providers;

use App\Models\PurchaseContent;
use App\Models\Purchases;
use App\Repositories\PurchaseContentRepository;
use App\Repositories\PurchasesRepository;
use App\Services\PurchaseContentService;
use App\Services\PurchasesService;
use Illuminate\Support\ServiceProvider;

class CategorieServiceProvider extends ServiceProvider
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
        $this->app->singleton(CategorieService::class, function ($app) {
            return new CategorieService(new CategorieServiceRepository());
        });
    }


    public function provides()
    {
        return ['\App\Services\CategorieService'];
    }
}
