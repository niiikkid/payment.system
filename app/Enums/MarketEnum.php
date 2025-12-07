<?php

declare(strict_types=1);

namespace App\Enums;

enum MarketEnum: string
{
    case BINANCE = 'BINANCE';
    case RAPIRA = 'RAPIRA';

    public function label(): string
    {
        return __(
            match ($this) {
                self::BINANCE => 'frontend.markets.market_names.binance',
                self::RAPIRA => 'frontend.markets.market_names.rapira',
            }
        );
    }
}


