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

    /** Статусы, при которых инвойс считается активным (ожидает оплату/подтверждение). */
    public static function active(): array
    {
        return [
            self::PENDING->value,
            self::PROCESSING->value,
        ];
    }

    /** Статусы, при которых инвойс считается финализированным (больше не активен). */
    public static function final(): array
    {
        return [
            self::PAID->value,
            self::EXPIRED->value,
            self::UNDERPAID->value,
            self::OVERPAID->value,
            self::CANCELLED->value,
        ];
    }

    /** Является ли статус активным. */
    public function isActive(): bool
    {
        return in_array($this->value, self::active(), true);
    }

    /** Является ли статус финальным. */
    public function isFinal(): bool
    {
        return in_array($this->value, self::final(), true);
    }
}


