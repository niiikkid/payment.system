<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\Invoice\InvoiceServiceContract;
use App\Contracts\Money\MoneyServiceContract;
use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function store(StoreInvoiceRequest $request, InvoiceServiceContract $service, MoneyServiceContract $money)
    {
        try {
            $invoice = $service->create(
                $request->user(),
                $request->toCurrencyEnum(),
                $request->toNetworkEnum(),
                $request->toMoneyAmount($money),
                $request->input('external_invoice_id'),
                $request->input('callback_url'),
                $request->input('tag'),
                (array) $request->input('metadata', []),
                $request->merchant()
            );

            return response()->json((new InvoiceResource($invoice->load(['address', 'merchant'])))->resolve());
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(Invoice $invoice): array
    {
        $this->authorizeInvoice($invoice);

        return (new InvoiceResource($invoice->load(['address', 'merchant'])))->resolve();
    }

    public function status(Invoice $invoice): array
    {
        $this->authorizeInvoice($invoice);

        $data = (new InvoiceResource($invoice))->resolve();

        return [
            'id' => $data['id'] ?? $invoice->id,
            'status' => $data['status'] ?? $invoice->status->value,
            'amount' => $data['amount'] ?? null,
            'amount_received' => $data['amount_received'] ?? null,
            'confirmations' => $data['confirmations'] ?? 0,
            'expires_at' => $data['expires_at'] ?? null,
            'txid' => $data['txid'] ?? null,
        ];
    }

    public function public(Invoice $invoice): array
    {
        $this->authorizeInvoice($invoice);

        $invoice->load(['address', 'merchant']);

        return (new InvoiceResource($invoice))->resolve();
    }

    public function qr(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        $invoice->load('address');

        $address = $invoice->address?->address;
        if (!$address) {
            abort(404);
        }

        $writer = new PngWriter();
        $qrCode = new QrCode(
            data: $address,
            size: 400,
            margin: 10,
        );

        $result = $writer->write($qrCode);

        return response($result->getString(), 200, [
            'Content-Type' => $result->getMimeType(),
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    public function cancel(Invoice $invoice, InvoiceServiceContract $service)
    {
        $this->authorizeInvoice($invoice);

        if ($invoice->status->isFinal()) {
            return response()->json([
                'message' => __('messages.invoices.already_finalized'),
            ], 409);
        }

        if ($invoice->expires_at && now()->greaterThanOrEqualTo($invoice->expires_at)) {
            return response()->json([
                'message' => __('messages.invoices.already_expired'),
            ], 409);
        }

        $service->expire($invoice);

        return response()->json((new InvoiceResource($invoice->refresh()))->resolve());
    }

    private function authorizeInvoice(Invoice $invoice): void
    {
        if ($invoice->user_id !== Auth::id()) {
            abort(404);
        }
    }
}


