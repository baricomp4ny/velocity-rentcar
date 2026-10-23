<?php

namespace App\Models;

use App\Models\Concerns\HasActiveOrder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasActiveOrder, HasUuids;

    protected $fillable = ['name', 'sort_order', 'is_active'];

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    public function isUsed(): bool
    {
        return $this->cars()->exists();
    }
}
