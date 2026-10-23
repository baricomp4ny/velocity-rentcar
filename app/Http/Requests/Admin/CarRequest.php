<?php

namespace App\Http\Requests\Admin;

use App\Models\Car;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CarRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'brand_id' => ['required', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:100'],
            'model_code' => ['nullable', 'string', 'max:20'],
            'category_id' => ['required', 'exists:categories,id'],
            'transmission' => ['required', Rule::in(Car::TRANSMISSIONS)],
            'fuel' => ['required', Rule::in(Car::FUELS)],
            'fuel_grade' => ['nullable', 'string', 'max:50'],
            'seats' => ['required', 'integer', 'min:2', 'max:20'],
            'year' => ['nullable', 'integer', 'min:1990', 'max:'.(now()->year + 1)],
            'power_hp' => ['nullable', 'integer', 'min:30', 'max:2000'],
            'acceleration' => ['nullable', 'numeric', 'min:1', 'max:30'],
            'price_per_day' => ['required', 'integer', 'min:50000'],
            'description' => ['required', 'string'],
            'condition_note' => ['nullable', 'string', 'max:150'],
            'photos' => ['array'],
            'photos.*' => ['image', 'max:4096'],
            'remove_photos' => ['array'],
            'remove_photos.*' => ['uuid'],
        ];
    }

    public function attributes(): array
    {
        return [
            'brand_id' => 'Merek',
            'category_id' => 'Kategori',
            'name' => 'Nama mobil',
            'fuel_grade' => 'Jenis BBM',
            'model_code' => 'Kode model',
            'power_hp' => 'Tenaga',
            'acceleration' => '0-100 km/jam',
            'price_per_day' => 'Harga per hari',
            'seats' => 'Jumlah kursi',
            'photos.*' => 'Foto',
        ];
    }

    /**
     * Kolom yang disimpan ke tabel cars.
     */
    public function carData(): array
    {
        return [
            ...$this->safe()->except(['photos', 'remove_photos', 'condition_note']),
            'condition_note' => $this->validated('condition_note') ?: 'Kendaraan dalam keadaan baik',
            'is_popular' => $this->boolean('is_popular'),
            'is_available' => $this->boolean('is_available'),
        ];
    }
}
