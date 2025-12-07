<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\MarketEnum;
use App\Models\MarketFiat;
use App\Services\Market\MarketService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class LoadMarketFiatPriceJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 30;

    public int $tries = 1;

    public function __construct(
        public readonly int $marketFiatId,
        public readonly MarketEnum $market,
    ) {
        $this->onQueue('markets');
    }

    /**
     * Предотвращаем одновременный запуск для одной и той же фиатной записи.
     */
    public function middleware(): array
    {
        return [new WithoutOverlapping("market-fiat-{$this->marketFiatId}")];
    }

    public function handle(MarketService $marketService): void
    {
        $fiat = MarketFiat::query()->find($this->marketFiatId);
        if (! $fiat || ! $fiat->is_enabled) {
            return;
        }

        // Проверяем интервал, даже если job попала в очередь раньше.
        $now = now();
        if ($fiat->last_polled_at && $fiat->last_polled_at->addSeconds($fiat->polling_interval_seconds) > $now) {
            return;
        }

        $marketService->loadPricesFor($fiat, $this->market);
    }
}


