<?php

declare(strict_types=1);

namespace App\Contracts\Market;

use App\Enums\MarketEnum;
use App\Models\MarketFiat;
use App\Models\MarketPrice;
use Illuminate\Support\Collection;

interface MarketServiceContract
{
    /** Получить список всех фиатных валют с последними ценами */
    public function listFiatsWithPrices(): Collection; // of MarketFiat

    /** Создать новую фиатную валюту для парсинга */
    public function createFiat(string $code, int $rows, array $payTypes, int $pollingInterval, bool $isEnabled): MarketFiat;

    /** Обновить настройки фиатной валюты */
    public function updateFiat(MarketFiat $fiat, int $rows, array $payTypes, int $pollingInterval, bool $isEnabled): MarketFiat;

    /** Запустить парсинг по всем доступным валютам для рынка */
    public function loadDuePrices(MarketEnum $market): int;

    /** Запустить парсинг конкретной валюты для рынка */
    public function loadPricesFor(MarketFiat $fiat, MarketEnum $market): ?MarketPrice;
}


