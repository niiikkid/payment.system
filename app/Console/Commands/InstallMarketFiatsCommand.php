<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\Market\MarketServiceContract;
use Illuminate\Console\Command;

class InstallMarketFiatsCommand extends Command
{
    protected $signature = 'markets:install-fiats';

    protected $description = 'Создать дефолтный список фиатных валют для парсинга Binance';

    /**
     * @var string[]
     */
    private array $defaults = ['KZT', 'BYN', 'EUR', 'TJS', 'KGS', 'UAH', 'USD', 'AZN', 'TRY', 'IDR'];

    public function handle(MarketServiceContract $service): int
    {
        $created = 0;
        foreach ($this->defaults as $code) {
            $exists = \App\Models\MarketFiat::query()->where('code', $code)->exists();
            if ($exists) {
                continue;
            }

            $service->createFiat($code, 5, [], 30, true);
            $created++;
        }

        $this->info("Добавлено валют: {$created}");

        return self::SUCCESS;
    }
}


