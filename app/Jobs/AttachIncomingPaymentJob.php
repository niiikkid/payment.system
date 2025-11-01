<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\Invoice\InvoiceServiceContract;
use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AttachIncomingPaymentJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly string $invoiceId,
    ) {
        $this->onQueue('invoices');
    }

    public function handle(InvoiceServiceContract $service): void
    {
        $invoice = Invoice::query()->find($this->invoiceId);
        if (!$invoice) {
            return;
        }

        // Если инвойс уже финализирован — прекращаем
        if ($invoice->status->isFinal()) {
            return;
        }

        // Если истёк — отдельно обработает ExpireInvoiceJob, здесь просто выходим
        if ($invoice->expires_at && now()->greaterThanOrEqualTo($invoice->expires_at)) {
            return;
        }

        $updated = $service->attachExactIncomingPayment($invoice);

        // Если платёж найден — переключаемся на проверку подтверждений
        if ($updated && $updated->status === InvoiceStatus::PROCESSING) {
            ConfirmInvoicePaymentJob::dispatch($updated->id)->delay(now()->addMinute());
            return;
        }

        // Иначе — пробуем снова через минуту, пока инвойс активен
        self::dispatch($invoice->id)->delay(now()->addMinute());
    }
}


