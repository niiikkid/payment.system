<?php

declare(strict_types=1);

namespace App\Contracts\Address;

use App\Models\Address;

interface AddressServiceContract
{
    /**
     * Создать адрес с валидацией соответствия сети и валюты.
     * По умолчанию: is_active=true, balance='0', last_checked_at=null.
     */
    public function create(string $currency, string $network, string $address): Address;

    /** Включить адрес */
    public function enable(Address $address): Address;

    /** Отключить адрес */
    public function disable(Address $address): Address;

    /** Обновить баланс и выставить время последней проверки = now() */
    public function updateChecked(Address $address, string $balance): Address;
}


