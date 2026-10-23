<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Nomor HP Indonesia, contoh: 081234567890, 6281234567890, +6281234567890.
 */
class PhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match('/^(\+62|62|0)8[0-9]{7,12}$/', (string) $value)) {
            $fail('Format :attribute tidak valid (contoh: 081234567890).');
        }
    }
}
