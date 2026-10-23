<?php

namespace App\Http\Requests;

use App\Rules\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($userId)],
            'phone' => ['nullable', new PhoneNumber, Rule::unique('users')->ignore($userId)],
            'address' => ['nullable', 'string', 'max:255'],
            'location_id' => ['required', Rule::exists('locations', 'id')->where('is_active', true)],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'Nama', 'phone' => 'Nomor Telepon', 'address' => 'Alamat', 'location_id' => 'Lokasi'];
    }
}
