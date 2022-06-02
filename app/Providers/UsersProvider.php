<?php

namespace App\Providers;

use App\Repositories\PostRepository;
use App\Repositories\UsersRepository;
use App\Services\PostService;
use App\Services\UsersService;
use Illuminate\Support\ServiceProvider;

class UsersProvider extends ServiceProvider
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
        $this->app->singleton(UsersService::class, function ($app) {
            return new UsersService(new UsersRepository());
        });
    }


    public function provides()
    {
        return ['\App\Services\UsersService'];
    }
}
