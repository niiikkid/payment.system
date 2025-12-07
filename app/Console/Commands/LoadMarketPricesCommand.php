<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\Market\MarketServiceContract;
use App\Enums\MarketEnum;
use Illuminate\Console\Command;

class LoadMarketPricesCommand extends Command
{
    protected $signature = 'markets:load {market=BINANCE : Маркет, для которого запускаем парсинг}';

    protected $description = 'Запустить парсинг курсов для включенных фиатных валют';

    public function handle(MarketServiceContract $service): int
    {
        $marketValue = strtoupper((string) $this->argument('market'));

        try {
            $market = MarketEnum::from($marketValue);
        } catch (\ValueError) {
            $this->error("Неизвестный маркет: {$marketValue}");
            return self::FAILURE;
        }

        $count = $service->loadDuePrices($market);
        $this->info("Задействовано валют: {$count}");

        return self::SUCCESS;
    }
}


