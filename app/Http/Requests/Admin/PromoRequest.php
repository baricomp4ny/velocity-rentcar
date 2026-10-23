<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromoRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['code' => strtoupper(trim((string) $this->input('code')))]);
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'alpha_num', 'max:30', Rule::unique('promos')->ignore($this->route('promo'))],
            'description' => ['nullable', 'string', 'max:255'],
            'discount_percent' => ['required', 'integer', 'min:1', 'max:100'],
            'max_discount' => ['nullable', 'integer', 'min:0'],
            'expires_at' => ['nullable', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'Kode promo',
            'discount_percent' => 'Persentase diskon',
            'max_discount' => 'Maksimal diskon',
            'expires_at' => 'Berlaku sampai',
        ];
    }

    public function promoData(): array
    {
        return [...$this->validated(), 'is_active' => $this->boolean('is_active')];
    }
}
