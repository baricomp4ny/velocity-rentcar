<?php

namespace App\Models;

use App\Models\Concerns\HasActiveOrder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasActiveOrder, HasUuids;

    protected $fillable = ['name', 'sort_order', 'is_active'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'pickup_location_id');
    }

    public function isUsed(): bool
    {
        return $this->bookings()->exists();
    }
}
