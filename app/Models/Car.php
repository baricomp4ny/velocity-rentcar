<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasUuids;

    public const TRANSMISSIONS = ['Matic', 'Manual'];

    public const FUELS = ['Bensin', 'Diesel', 'Hybrid', 'Listrik'];

    protected $fillable = [
        'brand_id', 'category_id', 'name', 'model_code', 'transmission', 'fuel', 'fuel_grade',
        'seats', 'year', 'power_hp', 'acceleration', 'price_per_day', 'description', 'condition_note',
        'is_popular', 'is_available',
    ];

    protected $casts = [
        'acceleration' => 'float',
        'is_popular' => 'boolean',
        'is_available' => 'boolean',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function photos()
    {
        return $this->hasMany(CarPhoto::class)->orderBy('sort_order');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Data yang selalu dibutuhkan kartu mobil: merek, foto, dan rating rata-rata ulasan.
     */
    public function scopeForListing(Builder $query): Builder
    {
        return $query->with(['brand', 'category', 'photos'])->withAvg('bookings as rating', 'rating');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['q'] ?? null, function ($q, $search) {
                $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', "%{$search}%")));
            })
            ->when($filters['category'] ?? null, fn ($q, $v) => $q->whereIn('category_id', $v))
            ->when($filters['transmission'] ?? null, fn ($q, $v) => $q->whereIn('transmission', $v))
            ->when($filters['fuel'] ?? null, fn ($q, $v) => $q->whereIn('fuel', $v))
            ->when($filters['seats'] ?? null, fn ($q, $v) => $q->where('seats', '>=', (int) $v));
    }

    /**
     * Data ringkas untuk atribut data-car pada kartu (dipakai filter di demo statis).
     */
    public function filterData(): array
    {
        return [
            'text' => strtolower("{$this->name} {$this->brand->name} {$this->category->name}"),
            'brand' => $this->brand_id,
            'category' => $this->category_id,
            'transmission' => $this->transmission,
            'fuel' => $this->fuel,
            'seats' => $this->seats,
        ];
    }

    public function fuelLabel(): string
    {
        return $this->fuel_grade ? "{$this->fuel} ({$this->fuel_grade})" : $this->fuel;
    }

    /**
     * Nama model tanpa merek, contoh "Innova Zenix Q 2.0".
     */
    public function modelName(): string
    {
        $brand = $this->brand->name;

        return str_starts_with($this->name, $brand) ? trim(substr($this->name, strlen($brand))) : $this->name;
    }

    /**
     * Tenaga dalam HP dan kW, contoh "503 HP (375 kW)".
     */
    public function powerLabel(): ?string
    {
        return $this->power_hp ? "{$this->power_hp} HP (".round($this->power_hp * 0.7457).' kW)' : null;
    }

    public function accelerationLabel(): ?string
    {
        return $this->acceleration ? number_format($this->acceleration, 1, ',', '.').' detik' : null;
    }

    public function ratingLabel(): string
    {
        return $this->rating ? number_format($this->rating, 1) : 'Baru';
    }

    public function imageUrl(): string
    {
        return $this->photoUrls()[0];
    }

    public function photoUrls(): array
    {
        $urls = $this->photos->map(fn (CarPhoto $photo) => $photo->url())->all();

        return $urls ?: [asset('images/cars/placeholder.svg')];
    }

    /**
     * Cek apakah mobil sudah disewa pada rentang tanggal tersebut.
     */
    public function isBookedBetween(string $start, string $end): bool
    {
        return $this->bookings()
            ->active()
            ->where('pickup_date', '<', $end)
            ->where('return_date', '>', $start)
            ->exists();
    }
}
