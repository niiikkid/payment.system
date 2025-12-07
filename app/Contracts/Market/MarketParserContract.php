<?php

declare(strict_types=1);

namespace App\Contracts\Market;

use App\Enums\MarketEnum;
use App\Models\MarketFiat;
use App\Services\Market\Data\MarketPrices;

interface MarketParserContract
{
    public function market(): MarketEnum;

    public function getPrices(MarketFiat $fiat): ?MarketPrices;
}


