<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Market\MarketServiceContract;
use App\Enums\MarketEnum;
use App\Http\Requests\Market\StoreMarketFiatRequest;
use App\Http\Requests\Market\UpdateMarketFiatRequest;
use App\Http\Resources\MarketFiatResource;
use App\Models\MarketFiat;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class MarketController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:admin']);
    }

    public function index(MarketServiceContract $service): Response
    {
        $fiats = $service->listFiatsWithPrices();

        return $this->inertia('markets/Index', [
            'fiats' => MarketFiatResource::collection($fiats)->resolve(),
            'market' => MarketEnum::BINANCE->value,
        ]);
    }

    public function store(StoreMarketFiatRequest $request, MarketServiceContract $service): RedirectResponse
    {
        $payload = $request->payload();
        $service->createFiat(
            $payload['code'],
            $payload['rows'],
            $payload['pay_types'],
            $payload['polling_interval_seconds'],
            $payload['is_enabled'],
        );

        return back()->with('success', __('messages.markets.created'));
    }

    public function update(UpdateMarketFiatRequest $request, MarketFiat $marketFiat, MarketServiceContract $service): RedirectResponse
    {
        $payload = $request->payload();
        $service->updateFiat(
            $marketFiat,
            $payload['rows'],
            $payload['pay_types'],
            $payload['polling_interval_seconds'],
            $payload['is_enabled'],
        );

        return back()->with('success', __('messages.markets.updated'));
    }

    public function refresh(MarketFiat $marketFiat, MarketServiceContract $service): RedirectResponse
    {
        $service->loadPricesFor($marketFiat, MarketEnum::BINANCE);

        return back()->with('success', __('messages.markets.refreshed'));
    }
}


