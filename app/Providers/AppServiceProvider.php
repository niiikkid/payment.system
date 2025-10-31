<?php

namespace App\Providers;

use App\Contracts\Address\AddressServiceContract;
use App\Contracts\Blockchain\BlockchainServiceContract;
use App\Mixins\ResponseMixins;
use App\Services\Address\AddressService;
use App\Services\Blockchain\BlockchainService;
use App\Services\Blockchain\Networks\Tron\TronService as TronNetworkService;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AddressServiceContract::class, AddressService::class);
        $this->app->singleton(BlockchainServiceContract::class, function ($app) {
            $service = new BlockchainService();
            // регистрируем доступные стратегии сетей
            $service->registerStrategy($app->make(TronNetworkService::class));
            return $service;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::mixin(new ResponseMixins());
    }
}
