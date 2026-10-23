<?php

namespace App\Http\Requests;

use App\Rules\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['required', new PhoneNumber, 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return ['terms.accepted' => 'Kamu harus menyetujui syarat & ketentuan.'];
    }

    public function attributes(): array
    {
        return ['name' => 'Nama', 'phone' => 'Nomor Telepon'];
    }
}
