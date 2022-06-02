<?php

namespace App\Providers;

use App\Repositories\NotificationsRepository;
use App\Services\NotificationsService;
use Illuminate\Support\ServiceProvider;

class NotificationsServiceProvider extends ServiceProvider
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
        $this->app->singleton(NotificationsService::class, function ($app) {
            return new NotificationsService(new NotificationsRepository());
        });
    }


    public function provides()
    {
        return ['\App\Services\NotificationsService'];
    }
}
