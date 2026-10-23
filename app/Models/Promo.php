<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasUuids;

    protected $fillable = ['code', 'description', 'discount_percent', 'max_discount', 'expires_at', 'is_active'];

    protected $casts = [
        'expires_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public static function findUsable(?string $code): ?self
    {
        if (! $code) {
            return null;
        }

        return static::where('code', strtoupper(trim($code)))
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhereDate('expires_at', '>=', today()))
            ->first();
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isBefore(today());
    }

    public function discountFor(int $subtotal): int
    {
        $discount = intdiv($subtotal * $this->discount_percent, 100);

        return $this->max_discount ? min($discount, $this->max_discount) : $discount;
    }

    public function summary(): string
    {
        return "diskon {$this->discount_percent}%".($this->max_discount ? ' (maks. '.rupiah($this->max_discount, false).')' : '');
    }
}
