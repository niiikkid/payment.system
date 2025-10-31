<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Invoice\InvoiceServiceContract;
use App\Contracts\Money\MoneyServiceContract;
use App\Enums\InvoiceStatus;
use App\Enums\Currency;
use App\Enums\Network;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function index(): Response
    {
        $paginator = Invoice::query()
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
}


