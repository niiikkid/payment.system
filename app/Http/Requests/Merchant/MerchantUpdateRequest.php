<?php

declare(strict_types=1);

namespace App\Http\Requests\Merchant;

use App\Models\Merchant;
use Illuminate\Foundation\Http\FormRequest;

class MerchantUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Merchant $merchant */
        $merchant = $this->route('merchant');
        return $this->user() !== null && $merchant && $merchant->user_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'initials' => ['required', 'string', 'max:16'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048', 'dimensions:max_width=500,max_height=500'],
        ];
    }
}

