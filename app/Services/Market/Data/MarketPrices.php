<?php

declare(strict_types=1);

namespace App\Services\Market\Data;

final class MarketPrices
{
    public function __construct(
        public readonly ?float $buyPrice,
        public readonly ?float $sellPrice,
    ) {
    }

    public function hasPrices(): bool
    {
        return $this->buyPrice !== null || $this->sellPrice !== null;
    }
}


