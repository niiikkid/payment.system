<?php

declare(strict_types=1);

namespace App\Contracts\Blockchain;

interface BlockchainServiceContract
{
    /**
     * Универсальные входящие переводы по связке сеть+валюта.
     * network: системный ключ сети, например: 'tron'.
     * currency: код валюты в верхнем регистре, например: 'USDT'.
     */
    public function getIncomingTransfers(string $network, string $currency, string $address, int $limit = 30): array;

    /**
     * Универсальные исходящие переводы по связке сеть+валюта.
     */
    public function getOutgoingTransfers(string $network, string $currency, string $address, int $limit = 30): array;
}


