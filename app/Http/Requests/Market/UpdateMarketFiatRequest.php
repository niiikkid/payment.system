<?php

declare(strict_types=1);

namespace App\Http\Requests\Market;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read int $rows Количество объявлений для усреднения
 * @property-read array<int, string> $pay_types Список payTypes Binance
 * @property-read int $polling_interval_seconds Интервал парсинга в секундах
 * @property-read bool $is_enabled Признак включения парсера
 */
class UpdateMarketFiatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'rows' => ['required', 'integer', 'min:1', 'max:50'],
            'pay_types' => ['sometimes', 'array'],
            'pay_types.*' => ['string', 'max:64'],
            'polling_interval_seconds' => ['required', 'integer', 'min:5', 'max:3600'],
            'is_enabled' => ['required', 'boolean'],
        ];
    }

    public function payload(): array
    {
        return [
            'rows' => (int) $this->input('rows', 5),
            'pay_types' => $this->preparePayTypes(),
            'polling_interval_seconds' => (int) $this->input('polling_interval_seconds', 30),
            'is_enabled' => $this->boolean('is_enabled'),
        ];
    }

    private function preparePayTypes(): array
    {
        $types = $this->input('pay_types', []);
        if (! is_array($types)) {
            return [];
        }

        return array_values(array_filter(array_map(static fn ($item) => trim((string) $item), $types)));
    }
}


