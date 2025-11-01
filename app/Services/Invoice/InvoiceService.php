<?php

declare(strict_types=1);

namespace App\Services\Invoice;

use App\Contracts\Invoice\InvoiceServiceContract;
use App\Contracts\Address\AddressServiceContract;
use App\Contracts\Money\MoneyServiceContract;
use App\Jobs\ExpireInvoiceJob;
use App\Enums\Currency;
use App\Enums\InvoiceStatus;
use App\Enums\Network;
use App\Models\Invoice;
use App\Services\Money\MoneyAmount;
use Illuminate\Support\Carbon;

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

        $expiresAt = now()->addMinutes(1);

        $invoice = Invoice::query()->create([
            'external_invoice_id' => $externalInvoiceId,
            'address_id' => $address->id,
            'amount' => $amountMinor,
            'currency' => $currency,
            'network' => $network,
            'status' => InvoiceStatus::PENDING,
            'expires_at' => $expiresAt,
            'callback_url' => $callbackUrl,
            'tag' => $tag,
            'metadata' => $metadata,
        ]);

        // Планируем асинхронную экспирацию ровно через 30 минут
        ExpireInvoiceJob::dispatch($invoice->id)->delay($expiresAt);

        return $invoice;
    }

    /**
     * Пометить инвойс как просроченный, если он ещё активен.
     */
    public function expire(Invoice $invoice): void
    {
        $invoice->refresh();

        if ($invoice->status->isFinal()) {
            return;
        }

        // Если уже оплачен/финализирован конкурентно, выходим, иначе просрочим
        $invoice->status = InvoiceStatus::EXPIRED;
        $invoice->save();
    }
}


