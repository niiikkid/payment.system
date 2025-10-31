<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Currency;
use App\Enums\Network;
use App\Casts\MoneyAmountCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency',
        'network',
        'address',
        'is_active',
        'balance',
        'last_checked_at',
    ];

    protected $casts = [
        'currency' => Currency::class,
        'network' => Network::class,
        'is_active' => 'boolean',
        'last_checked_at' => 'datetime',
        'balance' => MoneyAmountCast::class,
    ];

    protected $attributes = [
        'is_active' => true,
        'balance' => '0',
        'last_checked_at' => null,
    ];
}


