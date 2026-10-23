<?php

use Illuminate\Support\Carbon;

if (! function_exists('rupiah')) {
    /**
     * Format harga sesuai prototipe, contoh: "Rp 1.500.000,-".
     */
    function rupiah(int|float|null $amount, bool $withDash = true): string
    {
        return 'Rp '.number_format((float) $amount, 0, ',', '.').($withDash ? ',-' : '');
    }
}

if (! function_exists('tanggal')) {
    function tanggal($date, string $format = 'd F Y'): string
    {
        return Carbon::parse($date)->translatedFormat($format);
    }
}

if (! function_exists('setting')) {
    /**
     * Pengaturan toko yang diatur admin, contoh: setting('store_name').
     */
    function setting(string $key): ?string
    {
        return \App\Models\Setting::value($key);
    }
}

if (! function_exists('media_url')) {
    /**
     * URL untuk file gambar: aset bawaan di public/images atau unggahan di storage.
     */
    function media_url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return str_starts_with($path, 'images/') ? asset($path) : asset('storage/'.$path);
    }
}
