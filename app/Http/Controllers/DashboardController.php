<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Models\Address;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard page.
     */
    public function index(): Response
    {
        $userId = Auth::id();

        $totalInvoices = Invoice::query()->where('user_id', $userId)->count();
        $paidInvoices = Invoice::query()->where('user_id', $userId)->where('status', InvoiceStatus::PAID)->count();
        $activeInvoices = Invoice::query()->where('user_id', $userId)->whereIn('status', InvoiceStatus::active())->count();
        $expiredInvoices = Invoice::query()->where('user_id', $userId)->where('status', InvoiceStatus::EXPIRED)->count();
        $addressesTotal = Address::query()->where('user_id', $userId)->count();

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


