<?php

declare(strict_types=1);

namespace App\Services\Blockchain;

use App\Contracts\Blockchain\BlockchainServiceContract;
use App\Contracts\Blockchain\Networks\BlockchainNetworkServiceContract;
use App\Enums\Currency;
use App\Enums\Network;
use App\Enums\NetworkCurrency;
use App\Exceptions\Address\CurrencyNetworkMismatchException;

class BlockchainService implements BlockchainServiceContract
{
    /** @var array<string, BlockchainNetworkServiceContract> */
    private array $strategies = [];

    /** Зарегистрировать стратегию сети по её ключу */
    public function registerStrategy(BlockchainNetworkServiceContract $strategy): void
    {
        $this->strategies[$strategy->getNetworkKey()] = $strategy;
    }

    public function getIncomingTransfers(string $network, string $currency, string $address, int $limit = 30): array
    {
        $client = $this->strategies[$network] ?? null;
        if (!$client) {
            return [];
        }

        $networkEnum = Network::tryFrom(strtolower(trim($network)));
        $currencyEnum = Currency::tryFrom(strtoupper(trim($currency)));

        if (!$networkEnum || !$currencyEnum) {
            return [];
        }

        $allowedNetworks = NetworkCurrency::networksByCurrency($currencyEnum);
        if (!in_array($networkEnum, $allowedNetworks, true)) {
            throw new CurrencyNetworkMismatchException('Currency ' . $currencyEnum->value . ' is not available on network ' . $networkEnum->value);
        }

        return $client->getIncomingTransfers($currencyEnum, $address, $limit);
    }

    public function getOutgoingTransfers(string $network, string $currency, string $address, int $limit = 30): array
    {
        $client = $this->strategies[$network] ?? null;
        if (!$client) {
            return [];
        }

        $networkEnum = Network::tryFrom(strtolower(trim($network)));
        $currencyEnum = Currency::tryFrom(strtoupper(trim($currency)));

        if (!$networkEnum || !$currencyEnum) {
            return [];
        }

        $allowedNetworks = NetworkCurrency::networksByCurrency($currencyEnum);
        if (!in_array($networkEnum, $allowedNetworks, true)) {
            throw new CurrencyNetworkMismatchException('Currency ' . $currencyEnum->value . ' is not available on network ' . $networkEnum->value);
        }

        return $client->getOutgoingTransfers($currencyEnum, $address, $limit);
    }
}


