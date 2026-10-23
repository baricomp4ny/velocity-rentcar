<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrandRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', Rule::unique('brands')->ignore($this->route('brand'))],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'Nama merek'];
    }

    public function brandData(): array
    {
        return [
            'name' => $this->validated('name'),
            'sort_order' => (int) $this->validated('sort_order'),
        ];
    }
}
