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
use Illuminate\Http\Request;
use Inertia\Response;

class MarketController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:admin']);
    }

    public function index(Request $request, MarketServiceContract $service): Response
    {
        $marketValue = strtoupper($request->string('market', MarketEnum::BINANCE->value)->toString());
        $market = MarketEnum::tryFrom($marketValue) ?? MarketEnum::BINANCE;

        $fiats = $service->listFiatsWithPrices($market);

        return $this->inertia('markets/Index', [
            'fiats' => MarketFiatResource::collection($fiats)->resolve(),
            'market' => $market->value,
            'markets' => collect(MarketEnum::cases())
                ->map(fn (MarketEnum $item) => [
                    'code' => $item->value,
                    'title' => $item->label(),
                ])->values(),
        ]);
    }

    public function store(StoreMarketFiatRequest $request, MarketServiceContract $service): RedirectResponse
    {
        $payload = $request->payload();
        $service->createFiat(
            MarketEnum::from($payload['market']),
            $payload['code'],
            $payload['rows'],
            $payload['pay_types'],
            $payload['polling_interval_seconds'],
            $payload['is_enabled'],
            $payload['bybit_payment_method'],
            $payload['bybit_amount'],
            $payload['manual_buy_price'],
            $payload['manual_sell_price'],
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
            $payload['bybit_payment_method'],
            $payload['bybit_amount'],
            $payload['manual_buy_price'],
            $payload['manual_sell_price'],
        );

        return back()->with('success', __('messages.markets.updated'));
    }

    public function refresh(MarketFiat $marketFiat, MarketServiceContract $service): RedirectResponse
    {
        $service->loadPricesFor($marketFiat, MarketEnum::from($marketFiat->market));

        return back()->with('success', __('messages.markets.refreshed'));
    }
}


