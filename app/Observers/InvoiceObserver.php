<?php

declare(strict_types=1);

namespace App\Observers;

use App\Contracts\Invoice\InvoiceCallbackServiceContract;
use App\Models\Invoice;

class InvoiceObserver
{
    /**
     * Срабатывает при создании инвойса.
     */
    public function created(Invoice $invoice): void
    {
        app(InvoiceCallbackServiceContract::class)->send($invoice, 'created');
    }

    /**
     * Срабатывает при обновлении инвойса. Отправляем callback только при изменении статуса.
     */
    public function updated(Invoice $invoice): void
    {
        if ($invoice->wasChanged('status')) {
            app(InvoiceCallbackServiceContract::class)->send($invoice, 'status_changed');
        }
    }
}


