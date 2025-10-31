<?php

declare(strict_types=1);

namespace App\Services\Blockchain;

use App\Contracts\Blockchain\BlockchainServiceContract;
use App\Contracts\Blockchain\Networks\BlockchainNetworkServiceContract;
use App\Enums\Currency;
use App\Enums\Network;
use App\Enums\NetworkCurrency;
use App\Exceptions\Address\CurrencyNetworkMismatchException;
use App\Services\Money\MoneyAmount;

class BlockchainService implements BlockchainServiceContract
{
    /** @var array<string, BlockchainNetworkServiceContract> */
    private array $strategies = [];

    /** Зарегистрировать стратегию сети по её ключу */
    public function registerStrategy(BlockchainNetworkServiceContract $strategy): void
    {
        $this->strategies[$strategy->getNetworkKey()] = $strategy;
    }

    public function getAddressBalance(Network $network, Currency $currency, string $address): MoneyAmount
    {
        $client = $this->strategies[$network->value] ?? null;
        if (!$client) {
            return new MoneyAmount(\Brick\Math\BigDecimal::of('0')->toScale(6), $currency);
        }

        $allowedNetworks = NetworkCurrency::networksByCurrency($currency);
        if (!in_array($network, $allowedNetworks, true)) {
            throw new CurrencyNetworkMismatchException('Currency ' . $currency->value . ' is not available on network ' . $network->value);
        }

        return $client->getAddressBalance($currency, $address);
    }
}


