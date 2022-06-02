<?php

namespace App\Providers;

use App\Models\PurchaseContent;
use App\Models\Purchases;
use App\Repositories\PurchaseContentRepository;
use App\Repositories\PurchasesRepository;
use App\Services\PurchaseContentService;
use App\Services\PurchasesService;
use Illuminate\Support\ServiceProvider;

class PurchaseContentsServiceProvider extends ServiceProvider
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
        $this->app->singleton(PurchaseContent::class, function ($app) {
            return new PurchaseContentService(new PurchaseContentRepository());
        });
    }


    public function provides()
    {
        return ['\App\Services\PurchaseContentService'];
    }
}
