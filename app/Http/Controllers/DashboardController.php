<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Models\Address;
use App\Models\Invoice;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard page.
     */
    public function index(): Response
    {
        $totalInvoices = Invoice::query()->count();
        $paidInvoices = Invoice::query()->where('status', InvoiceStatus::PAID)->count();
        $activeInvoices = Invoice::query()->whereIn('status', InvoiceStatus::active())->count();
        $expiredInvoices = Invoice::query()->where('status', InvoiceStatus::EXPIRED)->count();
        $addressesTotal = Address::query()->count();

        $successRate = $totalInvoices > 0
            ? round(($paidInvoices / $totalInvoices) * 100, 2)
            : 0.0;

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalInvoices' => $totalInvoices,
                'paidInvoices' => $paidInvoices,
                'activeInvoices' => $activeInvoices,
                'expiredInvoices' => $expiredInvoices,
                'addressesTotal' => $addressesTotal,
                'successRate' => $successRate,
            ],
        ]);
    }
}


