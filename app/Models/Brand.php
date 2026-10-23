<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'logo', 'sort_order'];

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    public function logoUrl(): ?string
    {
        return media_url($this->logo);
    }
}
