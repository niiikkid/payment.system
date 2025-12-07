<?php

declare(strict_types=1);

namespace App\Services\Market;

use App\Contracts\Market\MarketServiceContract;
use App\Enums\MarketEnum;
use App\Models\MarketFiat;
use App\Models\MarketPrice;
use App\Services\Market\Parsers\BinanceParser;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Support\Collection;

final class MarketService implements MarketServiceContract
{
    public function __construct(
        private readonly BinanceParser $binanceParser,
    ) {
    }

    public function listFiatsWithPrices(): Collection
    {
        return MarketFiat::query()
            ->with('latestPrice')
            ->orderBy('code')
            ->get();
    }

    public function createFiat(string $code, int $rows, array $payTypes, int $pollingInterval, bool $isEnabled): MarketFiat
    {
        return MarketFiat::query()->create([
            'code' => strtoupper($code),
            'rows' => $rows,
            'pay_types' => array_values($payTypes),
            'polling_interval_seconds' => $pollingInterval,
            'is_enabled' => $isEnabled,
        ]);
    }

    public function updateFiat(MarketFiat $fiat, int $rows, array $payTypes, int $pollingInterval, bool $isEnabled): MarketFiat
    {
        $fiat->fill([
            'rows' => $rows,
            'pay_types' => array_values($payTypes),
            'polling_interval_seconds' => $pollingInterval,
            'is_enabled' => $isEnabled,
        ]);

        $fiat->save();

        return $fiat->refresh();
    }

    public function loadDuePrices(MarketEnum $market): int
    {
        $now = now();
        $fiats = MarketFiat::query()
            ->where('is_enabled', true)
            ->get();

        $processed = 0;

        foreach ($fiats as $fiat) {
            if ($fiat->last_polled_at && $fiat->last_polled_at->addSeconds($fiat->polling_interval_seconds) > $now) {
                continue;
            }

            $this->loadPricesFor($fiat, $market);
            $processed++;
        }

        return $processed;
    }

    public function loadPricesFor(MarketFiat $fiat, MarketEnum $market): ?MarketPrice
    {
        $parser = $this->parserFor($market);
        $prices = $parser->getPrices($fiat);

        $fiat->last_polled_at = now();
        $fiat->save();

        if (! $prices || ! $prices->hasPrices()) {
            return null;
        }

        return tap(
            MarketPrice::query()->firstOrNew([
                'market_fiat_id' => $fiat->id,
                'market' => $market->value,
            ]),
            function (MarketPrice $model) use ($prices): void {
                $model->asset = 'USDT';
                $model->buy_price = $prices->buyPrice ? $this->normalizePrice($prices->buyPrice) : null;
                $model->sell_price = $prices->sellPrice ? $this->normalizePrice($prices->sellPrice) : null;
                $model->fetched_at = now();
                $model->save();
            }
        );
    }

    private function parserFor(MarketEnum $market): BinanceParser
    {
        // Пока доступен только Binance, но оставляем метод для расширения.
        return $this->binanceParser;
    }

    private function normalizePrice(float $price): string
    {
        return BigDecimal::of((string) $price)->toScale(6, RoundingMode::DOWN)->__toString();
    }
}


