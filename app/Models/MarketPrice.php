<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'market_fiat_id',
        'market',
        'asset',
        'buy_price',
        'sell_price',
        'fetched_at',
    ];

    protected $casts = [
        'fetched_at' => 'datetime',
    ];

    public function fiat(): BelongsTo
    {
        return $this->belongsTo(MarketFiat::class, 'market_fiat_id');
    }
}


