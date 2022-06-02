<?php

namespace App\Providers;

use App\Repositories\PostRepository;
use Illuminate\Support\ServiceProvider;
use App\Services\PostService;

class postProvider extends ServiceProvider
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
        $this->app->singleton(PostService::class, function ($app) {
            return new PostService(new PostRepository());
        });
    }


    public function provides()
    {
        return ['\App\Services\PostService'];
    }
}
