<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\MarketEnum;
use App\Jobs\LoadMarketFiatPriceJob;
use App\Models\MarketFiat;
use Illuminate\Console\Command;

class DispatchMarketPricesCommand extends Command
{
    protected $signature = 'markets:dispatch {market=BINANCE : Маркет для загрузки цен}';

    protected $description = 'Отправить задачи загрузки цен для всех активных фиатных пар';

    public function handle(): int
    {
        try {
            $market = MarketEnum::from(strtoupper((string) $this->argument('market')));
        } catch (\ValueError) {
            $this->error('Неизвестный маркет');
            return self::FAILURE;
        }

        if ($market === MarketEnum::MANUAL) {
            $this->info('Ручной маркет не требует автоматического опроса.');
            return self::SUCCESS;
        }

        $now = now();
        $fiats = MarketFiat::query()
            ->where('is_enabled', true)
            ->where('market', $market->value)
            ->get();
        $dispatched = 0;

        foreach ($fiats as $fiat) {
            if ($fiat->last_polled_at && $fiat->last_polled_at->addSeconds($fiat->polling_interval_seconds) > $now) {
                continue;
            }

            LoadMarketFiatPriceJob::dispatch($fiat->id, $market);
            $dispatched++;
        }

        $this->info("Отправлено задач: {$dispatched}");

        return self::SUCCESS;
    }
}


