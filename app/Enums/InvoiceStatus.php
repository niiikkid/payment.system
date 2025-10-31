<?php

declare(strict_types=1);

namespace App\Enums;

enum InvoiceStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case PAID = 'paid';
    case EXPIRED = 'expired';
    case UNDERPAID = 'underpaid';
    case OVERPAID = 'overpaid';
    case CANCELLED = 'cancelled';
}


