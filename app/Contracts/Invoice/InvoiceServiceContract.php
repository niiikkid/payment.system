<?php

declare(strict_types=1);

namespace App\Contracts\Invoice;

use App\Enums\Currency;
use App\Enums\Network;
use App\Models\Invoice;
use App\Services\Money\MoneyAmount;

interface InvoiceServiceContract
{
    /**
     * Создать инвойс, выбирая адрес через AddressService::pickForPayment.
     * Возвращает созданный инвойс или бросает исключение при отсутствии доступного адреса.
     */
    public function create(Currency $currency, Network $network, MoneyAmount $amount, ?string $externalInvoiceId = null, ?string $callbackUrl = null, ?string $tag = null, array $metadata = []): Invoice;

    /**
     * Пометить инвойс как просроченный, если он ещё активен.
     */
    public function expire(Invoice $invoice): void;
}


