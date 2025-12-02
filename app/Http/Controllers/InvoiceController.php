<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Invoice\InvoiceServiceContract;
use App\Contracts\Money\MoneyServiceContract;
use App\Enums\InvoiceStatus;
use App\Enums\Currency;
use App\Enums\Network;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use App\Contracts\Invoice\InvoiceCallbackServiceContract;

class InvoiceController extends Controller
{
    public function index(): Response
    {
        $paginator = Invoice::query()
            ->with('address')
            ->latest('id')
            ->paginate(20)
            ->through(fn ($invoice) => (new InvoiceResource($invoice))->resolve());

        $currencyOptions = array_map(fn (Currency $c) => ['value' => $c->value, 'label' => $c->value], Currency::cases());
        $networkOptions = array_map(fn (Network $n) => ['value' => $n->value, 'label' => strtoupper($n->value)], Network::cases());

        return Inertia::render('invoices/Index', [
            'invoices' => $paginator,
            'currencyOptions' => $currencyOptions,
            'networkOptions' => $networkOptions,
            'statuses' => [
                'active' => InvoiceStatus::active(),
                'final' => InvoiceStatus::final(),
            ],
        ]);
    }

    public function store(StoreInvoiceRequest $request, InvoiceServiceContract $service, MoneyServiceContract $money)
    {
        try {
            $invoice = $service->create(
                $request->toCurrencyEnum(),
                $request->toNetworkEnum(),
                $request->toMoneyAmount($money),
                $request->input('external_invoice_id'),
                $request->input('callback_url'),
                $request->input('tag'),
                (array) $request->input('metadata', [])
            );

            return response()->json([
                'success' => true,
                'invoice' => (new InvoiceResource($invoice))->resolve(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(Invoice $invoice): array
    {
        return (new InvoiceResource($invoice))->resolve();
    }

    public function public(Invoice $invoice): Response
    {
        $invoice->load('address');

        return Inertia::render('PaymentForm/Index', [
            'invoice' => (new InvoiceResource($invoice))->resolve(),
            'appName' => config('app.name'),
            'statuses' => [
                'active' => InvoiceStatus::active(),
                'final' => InvoiceStatus::final(),
            ],
        ]);
    }

    public function publicData(Invoice $invoice): array
    {
        $invoice->load('address');

        return (new InvoiceResource($invoice))->resolve();
    }

    public function publicQr(Invoice $invoice)
    {
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

    public function update(UpdateInvoiceRequest $request, Invoice $invoice, InvoiceServiceContract $service): array
    {
        $status = InvoiceStatus::from($request->input('status'));
        $txid = $request->input('txid');

        $updated = $service->updateStatusManually($invoice, $status, $txid);

        return (new InvoiceResource($updated))->resolve();
    }

    public function sendCallback(Invoice $invoice, InvoiceCallbackServiceContract $callbackService)
    {
        // Отправляем колбэк только если указан callback_url (сервис внутри сам проверит)
        $callbackService->send($invoice, 'manual');

        return response()->json([
            'success' => true,
        ]);
    }
}


