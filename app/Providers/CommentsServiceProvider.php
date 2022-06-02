<?php

namespace App\Providers;

use App\Repositories\CommentsRepository;
use App\Services\CommentsService;
use App\Services\PostService;
use Illuminate\Support\ServiceProvider;

class CommentsServiceProvider extends ServiceProvider
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
            return new CommentsService(new CommentsRepository());
        });
    }


    public function provides()
    {
        return ['\App\Services\CommentsService'];
    }
}
