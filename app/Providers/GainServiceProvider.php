<?php

namespace App\Providers;

use App\Repositories\PurchaseContentRepository;
use Illuminate\Support\ServiceProvider;

class GainServiceProvider extends ServiceProvider
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
        $this->app->singleton(GainService::class, function ($app) {
            return new GainService(new PurchaseContentRepository(), new PostRepository());
        });
    }

    public function provides()
    {
        return ['\App\Services\GainService'];
    }
}
