<?php

declare(strict_types=1);

namespace App\Enums;

enum InvoiceStatus: string
{
    /** Создан, ждёт оплаты. */
    case PENDING = 'pending';

    /** Транзакция найдена, но подтверждений ещё мало. */
    case PROCESSING = 'processing';

    /** Полностью оплачен, достаточно подтверждений. */
    case PAID = 'paid';

    /** Время оплаты вышло, средств не поступило. */
    case EXPIRED = 'expired';

    /** Пришло меньше, чем нужно. */
    case UNDERPAID = 'underpaid';

    /** Пришло больше, чем нужно. */
    case OVERPAID = 'overpaid';

    /** Отменён вручную или API. */
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


