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
        'code',
        'pay_types',
        'rows',
        'is_enabled',
        'polling_interval_seconds',
        'last_polled_at',
    ];

    protected $casts = [
        'pay_types' => 'array',
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
        return $this->hasOne(MarketPrice::class)->latestOfMany('fetched_at');
    }
}


