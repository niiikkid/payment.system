<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceCallbackLogResource;
use App\Models\InvoiceCallbackLog;
use Inertia\Inertia;
use Inertia\Response;

class CallbackLogController extends Controller
{
    public function index(): Response
    {
        $paginator = InvoiceCallbackLog::query()
            ->latest('id')
            ->paginate(20)
            ->through(fn ($log) => (new InvoiceCallbackLogResource($log))->resolve());

        return Inertia::render('callbacks/Index', [
            'logs' => $paginator,
        ]);
    }
}


