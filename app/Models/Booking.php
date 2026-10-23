<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasUuids;

    public const STATUSES = [
        'paid' => 'Sudah Dibayar',
        'ongoing' => 'Sedang Disewa',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    /**
     * Ekspresi SQL total harga, untuk agregasi (mis. pendapatan di dashboard).
     */
    public const TOTAL_SQL = 'price_per_day * DATEDIFF(return_date, pickup_date) - discount';

    protected $fillable = [
        'car_id', 'promo_id', 'pickup_location_id', 'pickup_date', 'return_date',
        'price_per_day', 'discount', 'payment_method_id', 'status', 'rating', 'review', 'notes',
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'return_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            $booking->code ??= 'VRC-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        });
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    public function pickupLocation()
    {
        return $this->belongsTo(Location::class, 'pickup_location_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Pesanan yang sedang memakai jadwal mobil.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['paid', 'ongoing']);
    }

    public function scopeRevenue(Builder $query): Builder
    {
        return $query->where('status', '!=', 'cancelled');
    }

    public function scopeReviewed(Builder $query): Builder
    {
        return $query->whereNotNull('rating');
    }

    public function durationDays(): int
    {
        return (int) $this->pickup_date->diffInDays($this->return_date);
    }

    public function subtotal(): int
    {
        return $this->price_per_day * $this->durationDays();
    }

    public function total(): int
    {
        return $this->subtotal() - $this->discount;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status];
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'paid' => 'bg-primary-50 text-primary',
            'ongoing' => 'bg-amber-50 text-amber-600',
            'completed' => 'bg-emerald-50 text-emerald-600',
            'cancelled' => 'bg-red-50 text-red-500',
        };
    }

    /**
     * Isi notifikasi untuk pelanggan saat admin mengubah status.
     */
    public function statusMessage(): string
    {
        return match ($this->status) {
            'paid' => "Pembayaran pesanan {$this->code} sudah dikonfirmasi.",
            'ongoing' => "Mobil untuk pesanan {$this->code} sudah diambil. Selamat berkendara!",
            'completed' => "Pesanan {$this->code} selesai. Terima kasih! Yuk beri penilaian untuk mobilnya.",
            'cancelled' => "Pesanan {$this->code} dibatalkan. Hubungi CS jika ada pertanyaan.",
        };
    }

    /**
     * Pembatalan hanya boleh sebelum hari pengambilan mobil.
     */
    public function canBeCancelled(): bool
    {
        return $this->status === 'paid' && $this->pickup_date->isAfter(today());
    }

    public function canBeReviewed(): bool
    {
        return $this->status === 'completed' && $this->rating === null;
    }
}
