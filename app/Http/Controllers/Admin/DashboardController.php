<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $total = DB::raw(Booking::TOTAL_SQL);

        // Pendapatan 6 bulan terakhir untuk grafik batang.
        $monthly = collect(range(5, 0))->map(function (int $ago) use ($total) {
            $month = now()->startOfMonth()->subMonths($ago);

            return [
                'label' => $month->translatedFormat('M'),
                'total' => (int) Booking::revenue()
                    ->whereBetween('created_at', [$month, $month->copy()->endOfMonth()])
                    ->sum($total),
            ];
        });

        return view('admin.dashboard', [
            'stats' => [
                ['Total Pendapatan', rupiah(Booking::revenue()->sum($total), false), 'chart'],
                ['Pesanan Aktif', Booking::active()->count(), 'receipt'],
                ['Jumlah Mobil', Car::count(), 'car'],
                ['Pelanggan', User::where('role', 'user')->count(), 'users'],
            ],
            'monthly' => $monthly,
            'latestBookings' => Booking::with(['car', 'user', 'pickupLocation'])->latest()->take(6)->get(),
            'todayPickups' => Booking::with(['car', 'user'])->where('status', 'paid')->whereDate('pickup_date', today())->get(),
        ]);
    }
}
