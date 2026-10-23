<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['key', 'value'];

    private const CACHE_KEY = 'settings';

    /**
     * Semua pengaturan toko yang bisa diubah admin: grup => [kunci => [label, tipe, nilai awal]].
     */
    public const FIELDS = [
        'Identitas Toko' => [
            'store_name' => ['Nama Toko', 'text', 'Velocity RentCar'],
            'store_tagline' => ['Tagline (di bukti pembayaran)', 'text', 'Layanan Rental Mobil Bogor'],
            'cs_phone' => ['Nomor Customer Service / WhatsApp', 'tel', '6281299067622'],
        ],
        'Halaman Start & Login' => [
            'headline_prefix' => ['Judul: teks awal', 'text', 'Your Ultimate'],
            'headline_highlight' => ['Judul: teks biru', 'text', 'Car Rental'],
            'headline_suffix' => ['Judul: teks akhir', 'text', 'Experience'],
            'start_subtitle' => ['Deskripsi halaman Start', 'textarea', 'Layanan rental mobil untuk daerah Bogor dan sekitarnya'],
            'start_image' => ['Foto mobil halaman Start', 'image', 'images/cars/bmw-m4-competition-front.jpg'],
            'login_subtitle' => ['Deskripsi halaman Sign in', 'textarea', 'Silahkan masukkan E-mail atau Nomor Telepon anda yang telah didaftarkan'],
            'register_subtitle' => ['Deskripsi halaman Buat Akun', 'textarea', 'Buat akun baru untuk mulai menyewa mobil dengan mudah'],
            'login_image' => ['Foto atas halaman Sign in & Buat Akun', 'image', 'images/ui/login-hero.jpg'],
        ],
        'Banner Home' => [
            'featured_car_id' => ['Mobil di banner Home (kosongkan = mobil populer dengan rating tertinggi)', 'car', null],
        ],
    ];

    public static function definitions(): array
    {
        return array_merge(...array_values(self::FIELDS));
    }

    /**
     * Nilai pengaturan; memakai nilai awal jika admin belum mengisinya.
     */
    public static function value(string $key): ?string
    {
        $stored = Cache::rememberForever(self::CACHE_KEY, fn () => static::pluck('value', 'key')->all());

        return $stored[$key] ?? self::definitions()[$key][2] ?? null;
    }

    public static function saveMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget(self::CACHE_KEY);
    }
}
