<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Contracts\Blockchain\ExplorerServiceContract;
use App\Contracts\Money\MoneyServiceContract;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /** @var Invoice */
    public $resource;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'external_invoice_id' => $this->external_invoice_id,
            'address_id' => $this->address_id,
            'address' => $this->whenLoaded('address', fn () => $this->address?->address),
            'amount' => app(MoneyServiceContract::class)->format($this->amount),
            'currency' => $this->currency->value,
            'currency_label' => strtoupper($this->currency->value),
            'network' => $this->network->value,
            'network_label' => strtoupper($this->network->value),
            'status' => $this->status->value,
            'txid' => $this->txid,
            'tx_explorer_url' => $this->txid ? app(ExplorerServiceContract::class)->getTransactionUrl($this->network, $this->currency, $this->txid) : null,
            'amount_received' => app(MoneyServiceContract::class)->format($this->amount_received),
            'confirmations' => $this->confirmations,
            'expires_at' => optional($this->expires_at)?->toDateTimeString(),
            'callback_url' => $this->callback_url,
            'tag' => $this->tag,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}


