<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketFiatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $price = $this->whenLoaded('latestPrice');

        return [
            'id' => $this->id,
            'market' => $this->market,
            'asset' => 'USDT',
            'code' => $this->code,
            'rows' => (int) $this->rows,
            'pay_types' => $this->pay_types ?? [],
            'polling_interval_seconds' => (int) $this->polling_interval_seconds,
            'is_enabled' => (bool) $this->is_enabled,
            'last_polled_at' => optional($this->last_polled_at)?->toISOString(),
            'price' => $price ? [
                'buy_price' => $price->buy_price !== null ? (float) $price->buy_price : null,
                'sell_price' => $price->sell_price !== null ? (float) $price->sell_price : null,
                'fetched_at' => optional($price->fetched_at)?->toISOString(),
            ] : null,
        ];
    }
}


