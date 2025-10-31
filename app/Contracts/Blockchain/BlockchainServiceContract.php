<?php

declare(strict_types=1);

namespace App\Contracts\Blockchain;

use App\Services\Money\MoneyAmount;
use App\Enums\Network;
use App\Enums\Currency;

interface BlockchainServiceContract
{
    /**
     * Получить баланс адреса по связке сеть+валюта.
     * network: системный ключ сети, например: 'tron'.
     * currency: код валюты в верхнем регистре, например: 'USDT'.
     * Возвращает строку с десятичным числом в стандартной точности.
     */
    public function getAddressBalance(Network $network, Currency $currency, string $address): MoneyAmount;
}


