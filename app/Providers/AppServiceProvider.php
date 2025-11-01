<?php

namespace App\Providers;

use App\Contracts\Address\AddressServiceContract;
use App\Contracts\Blockchain\BlockchainServiceContract;
use App\Mixins\ResponseMixins;
use App\Contracts\Blockchain\ExplorerServiceContract;
use App\Services\Blockchain\Explorer\ExplorerService;
use App\Contracts\Money\MoneyServiceContract;
use App\Services\Address\AddressService;
use App\Services\Blockchain\BlockchainService;
use App\Services\Money\MoneyService;
use App\Contracts\Invoice\InvoiceServiceContract;
use App\Services\Invoice\InvoiceService;
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
        $this->app->bind(InvoiceServiceContract::class, InvoiceService::class);
        $this->app->singleton(MoneyServiceContract::class, MoneyService::class);
        $this->app->singleton(BlockchainServiceContract::class, BlockchainService::class);
        $this->app->singleton(ExplorerServiceContract::class, ExplorerService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::mixin(new ResponseMixins());
    }
}
