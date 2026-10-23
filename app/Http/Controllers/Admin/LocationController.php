<?php

namespace App\Http\Controllers\Admin;

use App\Models\Location;

class LocationController extends MasterDataController
{
    protected function model(): string
    {
        return Location::class;
    }

    protected function meta(): array
    {
        return [
            'title' => 'Lokasi Layanan',
            'subtitle' => 'Wilayah pilihan "Location" dan lokasi pengambilan mobil',
            'label' => 'Lokasi',
            'route' => 'locations',
            'usage' => ['bookings', 'pesanan'],
        ];
    }
}
