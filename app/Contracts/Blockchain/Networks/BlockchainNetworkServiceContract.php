<?php

declare(strict_types=1);

namespace App\Contracts\Blockchain\Networks;

use App\Enums\Currency;

interface BlockchainNetworkServiceContract
{
    /** Уникальный ключ сети, например: 'tron' */
    public function getNetworkKey(): string;

    /** Универсальные входящие переводы по валюте для адреса в сети */
    public function getIncomingTransfers(Currency $currency, string $address, int $limit = 30): array;

    /** Универсальные исходящие переводы по валюте для адреса в сети */
    public function getOutgoingTransfers(Currency $currency, string $address, int $limit = 30): array;
}


