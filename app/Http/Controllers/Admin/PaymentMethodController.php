<?php

namespace App\Http\Controllers\Admin;

use App\Models\PaymentMethod;

class PaymentMethodController extends MasterDataController
{
    protected function model(): string
    {
        return PaymentMethod::class;
    }

    protected function meta(): array
    {
        return [
            'title' => 'Metode Pembayaran',
            'subtitle' => 'Pilihan pembayaran pada langkah ke-3 pemesanan',
            'label' => 'Metode pembayaran',
            'route' => 'payment-methods',
            'usage' => ['bookings', 'pesanan'],
        ];
    }

    protected function extraFields(): array
    {
        return ['type' => ['Jenis', PaymentMethod::TYPES]];
    }
}
