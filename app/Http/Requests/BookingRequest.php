<?php

namespace App\Http\Requests;

use App\Models\Promo;
use App\Rules\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class BookingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // Informasi Penyewa disimpan ke profil akun (tabel users).
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', new PhoneNumber, Rule::unique('users')->ignore($this->user()->id)],
            'address' => ['required', 'string', 'max:255'],
            'pickup_location_id' => ['required', Rule::exists('locations', 'id')->where('is_active', true)],
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:30'],
            'payment_method_id' => ['required', Rule::exists('payment_methods', 'id')->where('is_active', true)],
            'promo_code' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (filled($this->input('promo_code')) && ! $this->promo()) {
                    $validator->errors()->add('promo_code', 'Kode promo tidak valid atau sudah kedaluwarsa.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return ['pickup_date.after_or_equal' => 'Tanggal pengambilan tidak boleh sebelum hari ini.'];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama',
            'phone' => 'Nomor Telepon',
            'address' => 'Alamat Lengkap',
            'pickup_location_id' => 'Lokasi Pengambilan',
            'pickup_date' => 'Tanggal Pengambilan',
            'duration_days' => 'Durasi Peminjaman',
            'payment_method_id' => 'Metode Pembayaran',
        ];
    }

    public function promo(): ?Promo
    {
        return Promo::findUsable($this->input('promo_code'));
    }

    public function pickupDate(): Carbon
    {
        return Carbon::parse($this->validated('pickup_date'))->startOfDay();
    }

    public function returnDate(): Carbon
    {
        return $this->pickupDate()->addDays((int) $this->validated('duration_days'));
    }

    public function renter(): array
    {
        return $this->safe()->only(['name', 'phone', 'address']);
    }
}
