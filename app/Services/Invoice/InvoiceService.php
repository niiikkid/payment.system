<?php

declare(strict_types=1);

namespace App\Services\Invoice;

use App\Contracts\Invoice\InvoiceServiceContract;
use App\Contracts\Address\AddressServiceContract;
use App\Contracts\Money\MoneyServiceContract;
use App\Enums\Currency;
use App\Enums\InvoiceStatus;
use App\Enums\Network;
use App\Models\Invoice;
use App\Services\Money\MoneyAmount;

class InvoiceService implements InvoiceServiceContract
{
    public function __construct(
        private readonly AddressServiceContract $addresses,
        private readonly MoneyServiceContract $money,
    ) {
    }

    public function create(Currency $currency, Network $network, MoneyAmount $amount, ?string $externalInvoiceId = null, ?string $callbackUrl = null, ?string $tag = null, array $metadata = []): Invoice
    {
        $address = $this->addresses->pickForPayment($currency, $network, $amount);
        $amountMinor = $this->money->toMinor($amount);

        return Invoice::query()->create([
            'external_invoice_id' => $externalInvoiceId,
            'address_id' => $address->id,
            'amount' => $amountMinor,
            'currency' => $currency,
            'network' => $network,
            'status' => InvoiceStatus::PENDING,
            'callback_url' => $callbackUrl,
            'tag' => $tag,
            'metadata' => $metadata,
        ]);
    }
}


