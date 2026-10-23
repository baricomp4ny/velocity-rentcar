<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use App\Rules\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user)],
            'phone' => ['nullable', new PhoneNumber, Rule::unique('users')->ignore($user)],
            'address' => ['nullable', 'string', 'max:255'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            // Saat edit, password boleh dikosongkan agar tidak berubah.
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::min(8)],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'Nama', 'phone' => 'Nomor Telepon', 'address' => 'Alamat', 'location_id' => 'Lokasi', 'role' => 'Peran'];
    }

    public function userData(): array
    {
        return collect($this->validated())->reject(fn ($value, $key) => $key === 'password' && blank($value))->all();
    }
}
