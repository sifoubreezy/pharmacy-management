<?php

namespace App\Providers;
use App\Repositories\ReturnsRepository;
use App\Repositories\DepositsRepository;

use App\Repositories\UsersRepository;
use App\Repositories\PurchasesRepository;
use Illuminate\Support\ServiceProvider;

class EcheanceServiceProvider extends ServiceProvider
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
        $this->app->singleton(EcheanceServic::class, function ($app) {
            return new EcheanceServic(new ReturnsRepository(), new DepositsRepository(),new PurchasesRepository(),new UsersRepository());
        });
    }

    public function provides()
    {
        return ['\App\Services\EcheanceServic'];
    }
}
