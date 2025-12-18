<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Currency;
use App\Enums\Network;
use App\Contracts\Money\MoneyServiceContract;
use App\Services\Money\MoneyAmount;
use App\Models\Merchant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read string $currency ISO код валюты (например, USDT)
 * @property-read string $network Сеть (например, tron)
 * @property-read string|int|float $amount Сумма в десятичном виде
 * @property-read string|null $external_invoice_id Внешний ID
 * @property-read int|null $merchant_id Идентификатор мерчанта
 * @property-read string|null $product_name Название товара
 * @property-read string|null $product_description Описание товара
 * @property-read string|null $callback_url URL для callback
 * @property-read string|null $tag Произвольная метка
 * @property-read array|null $metadata Доп. данные
 * @property-read string|null $client_id Внешний идентификатор клиента
 * @property-read string|null $client_name Имя клиента
 * @property-read string|null $client_telegram Контакт Telegram
 * @property-read string|null $client_contact Дополнительный контакт
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
            'merchant_id' => [
                'nullable',
                'integer',
                Rule::exists('merchants', 'id')->where(fn ($query) => $query->where('user_id', $this->user()?->id)),
            ],
            'product_name' => ['nullable', 'string', 'max:255'],
            'product_description' => ['nullable', 'string', 'max:2000'],
            'callback_url' => ['nullable', 'url', 'max:255'],
            'tag' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
            'client_id' => ['nullable', 'string', 'max:128'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'client_telegram' => ['nullable', 'string', 'max:255'],
            'client_contact' => ['nullable', 'string', 'max:255'],
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

    public function clientExternalId(): ?string
    {
        $value = $this->input('client_id');
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);
        return $trimmed === '' ? null : $trimmed;
    }

    public function clientName(): ?string
    {
        return $this->nullableString('client_name');
    }

    public function clientTelegram(): ?string
    {
        return $this->nullableString('client_telegram');
    }

    public function clientContact(): ?string
    {
        return $this->nullableString('client_contact');
    }

    public function merchant(): ?Merchant
    {
        $merchantId = $this->input('merchant_id');
        if (!$merchantId) {
            return null;
        }

        return Merchant::query()
            ->where('id', (int) $merchantId)
            ->where('user_id', $this->user()?->id)
            ->first();
    }

    public function productName(): ?string
    {
        $value = $this->input('product_name');
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);
        return $trimmed !== '' ? $trimmed : null;
    }

    public function productDescription(): ?string
    {
        $value = $this->input('product_description');
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);
        return $trimmed !== '' ? $trimmed : null;
    }

    private function nullableString(string $key): ?string
    {
        $value = $this->input($key);
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);
        return $trimmed === '' ? null : $trimmed;
    }
}


