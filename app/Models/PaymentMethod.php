<?php

namespace App\Models;

use App\Models\Concerns\HasActiveOrder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasActiveOrder, HasUuids;

    public const TYPES = [
        'debit' => 'Kartu Debit',
        'ewallet' => 'E-Wallet',
    ];

    protected $fillable = ['name', 'type', 'sort_order', 'is_active'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type];
    }

    public function isUsed(): bool
    {
        return $this->bookings()->exists();
    }
}
