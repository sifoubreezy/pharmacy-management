<?php

namespace App\Providers;

use App\Repositories\InboxRepository;
use App\Services\InboxService;
use Illuminate\Support\ServiceProvider;

class InboxServiceProvider extends ServiceProvider
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
        $this->app->singleton(InboxService::class, function ($app) {
            return new InboxService(new InboxRepository());
        });
    }


    public function provides()
    {
        return ['\App\Services\InboxService'];
    }
}
