<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Currency;
use App\Enums\Network;
use App\Contracts\Money\MoneyServiceContract;
use App\Services\Money\MoneyAmount;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read string $currency ISO код валюты (например, USDT)
 * @property-read string $network Сеть (например, tron)
 * @property-read string|int|float $amount Сумма в десятичном виде
 * @property-read string|null $external_invoice_id Внешний ID
 * @property-read string|null $callback_url URL для callback
 * @property-read string|null $tag Произвольная метка
 * @property-read array|null $metadata Доп. данные
 */
class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'currency' => ['required', 'string'],
            'network' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'external_invoice_id' => ['nullable', 'string', 'max:64'],
            'callback_url' => ['nullable', 'url', 'max:255'],
            'tag' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function toCurrencyEnum(): Currency
    {
        return Currency::from(strtoupper(trim((string) $this->input('currency'))));
    }

    public function toNetworkEnum(): Network
    {
        return Network::from(strtolower(trim((string) $this->input('network'))));
    }

    public function toMoneyAmount(MoneyServiceContract $money): MoneyAmount
    {
        return $money->create($this->input('amount'), $this->toCurrencyEnum());
    }
}


