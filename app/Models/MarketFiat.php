<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MarketFiat extends Model
{
    use HasFactory;

    protected $fillable = [
        'market',
        'code',
        'pay_types',
        'bybit_payment_method',
        'bybit_amount',
        'rows',
        'is_enabled',
        'polling_interval_seconds',
        'last_polled_at',
    ];

    protected $casts = [
        'market' => 'string',
        'pay_types' => 'array',
        'bybit_payment_method' => 'string',
        'bybit_amount' => 'decimal:8',
        'is_enabled' => 'boolean',
        'rows' => 'integer',
        'polling_interval_seconds' => 'integer',
        'last_polled_at' => 'datetime',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(MarketPrice::class);
    }

    public function latestPrice(): HasOne
    {
        return $this->hasOne(MarketPrice::class)
            ->latestOfMany('fetched_at');
    }
}


