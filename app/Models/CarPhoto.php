<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CarPhoto extends Model
{
    use HasUuids;

    protected $fillable = ['path', 'sort_order'];

    protected static function booted(): void
    {
        // Hapus file unggahan saat foto dihapus (foto bawaan di public/images dibiarkan).
        static::deleted(function (CarPhoto $photo) {
            if (! str_starts_with($photo->path, 'images/')) {
                Storage::disk('public')->delete($photo->path);
            }
        });
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function url(): string
    {
        return media_url($this->path);
    }
}
