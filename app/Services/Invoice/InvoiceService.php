<?php

declare(strict_types=1);

namespace App\Services\Invoice;

use App\Contracts\Invoice\InvoiceServiceContract;
use App\Contracts\Address\AddressServiceContract;
use App\Contracts\Money\MoneyServiceContract;
use App\Contracts\Blockchain\BlockchainServiceContract;
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
        private readonly BlockchainServiceContract $blockchain,
    ) {
    }

    public function create(Currency $currency, Network $network, MoneyAmount $amount, ?string $externalInvoiceId = null, ?string $callbackUrl = null, ?string $tag = null, array $metadata = []): Invoice
    {
        $address = $this->addresses->pickForPayment($currency, $network, $amount);
        $amountMinor = $this->money->toMinor($amount);

        $expiresAt = now()->addMinutes(30);

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

    /**
     * Найти точный входящий платёж в блокчейне для инвойса в пределах его окна оплаты.
     */
    public function findExactIncomingPayment(Invoice $invoice): ?array
    {
        $invoice->loadMissing('address');
        $address = $invoice->address?->address;
        if ($address === null || $address === '') {
            return null;
        }

        $transactions = $this->blockchain->getIncomingTransactions($invoice->network, $invoice->currency, $address);

        $createdAt = $invoice->created_at;
        $expiresAt = $invoice->expires_at;
        if ($createdAt === null || $expiresAt === null) {
            return null;
        }

        foreach ($transactions as $tx) {
            if (!is_array($tx)) {
                continue;
            }

            $amountStr = (string) ($tx['amount'] ?? '');
            if ($amountStr === '') {
                continue;
            }

            // Парсим сумму транзакции к MoneyAmount и сверяем по точному равенству
            $txAmount = $this->money->parse($amountStr, $invoice->currency);
            if ($this->money->compare($txAmount, $invoice->amount) !== 0) {
                continue;
            }

            // TronGrid отдаёт метку времени, как правило, в миллисекундах
            $ts = (int) ($tx['timestamp'] ?? 0);
            if ($ts <= 0) {
                continue;
            }
            $txTime = $ts > 2000000000 ? Carbon::createFromTimestampMs($ts) : Carbon::createFromTimestamp($ts);

            if ($txTime->lt($createdAt) || $txTime->gt($expiresAt)) {
                continue;
            }

            $txid = (string) ($tx['txid'] ?? '');
            if ($txid === '') {
                continue;
            }

            // Уточняем данные по транзакции, включая подтверждения
            return $this->blockchain->getTransactionInfoByHash($invoice->network, $invoice->currency, $txid);
        }

        return null;
    }
}


