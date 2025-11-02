<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Currency;
use App\Contracts\Money\MoneyServiceContract;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read array $settings Массив объектов { currency: string, min_invoice_amount: string|number, max_invoice_amount: string|number }
 */
class UpdateAppSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'settings' => ['required', 'array', 'min:1'],
            'settings.*.currency' => ['required', 'string'],
            'settings.*.min_invoice_amount' => ['required'],
            'settings.*.max_invoice_amount' => ['required'],
        ];
    }

    /** Преобразовать входные суммы в минорные единицы по валютам */
    public function toMinorMap(MoneyServiceContract $money): array
    {
        $result = [];
        foreach ((array) $this->input('settings', []) as $item) {
            $currency = Currency::from(strtoupper((string) ($item['currency'] ?? '')));
            $min = $money->create($item['min_invoice_amount'], $currency);
            $max = $money->create($item['max_invoice_amount'], $currency);
            $result[] = [
                'currency' => $currency,
                'min_minor' => $money->toMinor($min),
                'max_minor' => $money->toMinor($max),
            ];
        }
        return $result;
    }
}


