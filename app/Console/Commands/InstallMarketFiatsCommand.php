<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\Market\MarketServiceContract;
use App\Enums\MarketEnum;
use App\Models\MarketFiat;
use Illuminate\Console\Command;

class InstallMarketFiatsCommand extends Command
{
    protected $signature = 'markets:install-fiats';

    protected $description = 'Создать дефолтный список фиатных валют для парсинга маркетов';

    /**
     * @var array<string, string[]>
     */
    private array $defaults = [
        MarketEnum::BINANCE->value => ['KZT', 'BYN', 'EUR', 'TJS', 'KGS', 'UAH', 'USD', 'AZN', 'TRY', 'IDR'],
        MarketEnum::RAPIRA->value => ['RUB'],
    ];

    public function handle(MarketServiceContract $service): int
    {
        $created = 0;
        foreach ($this->defaults as $market => $codes) {
            foreach ($codes as $code) {
                $exists = MarketFiat::query()
                    ->where('market', $market)
                    ->where('code', $code)
                    ->exists();
                if ($exists) {
                    continue;
                }

                $service->createFiat(MarketEnum::from($market), $code, 5, [], 30, true);
                $created++;
            }
        }

        $this->info("Добавлено валют: {$created}");

        return self::SUCCESS;
    }
}


