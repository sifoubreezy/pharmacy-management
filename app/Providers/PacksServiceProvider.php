<?php

namespace App\Providers;

use App\Repositories\PacksRepository;
use App\Repositories\PostRepository;
use App\Services\PacksService;
use Illuminate\Support\ServiceProvider;
use App\Services\PostService;

class PacksServiceProvider extends ServiceProvider
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
        $this->app->singleton(PacksService::class, function ($app) {
            return new PacksService(new PacksRepository());
        });
    }


    public function provides()
    {
        return ['\App\Services\PostService'];
    }
}
