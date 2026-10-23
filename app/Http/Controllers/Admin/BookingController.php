<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with(['car', 'user', 'pickupLocation'])
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->query('q'), function ($q, $search) {
                $q->where(fn ($q) => $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('car', fn ($c) => $c->where('name', 'like', "%{$search}%")));
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        return view('admin.bookings.show', ['booking' => $booking->load(['car.brand', 'car.category', 'car.photos', 'user', 'promo', 'pickupLocation', 'paymentMethod'])]);
    }

    public function update(Request $request, Booking $booking)
    {
        $booking->fill($request->validate([
            'status' => ['required', Rule::in(array_keys(Booking::STATUSES))],
            'notes' => ['nullable', 'string', 'max:500'],
        ]));

        $statusChanged = $booking->isDirty('status');
        $booking->save();

        if ($statusChanged) {
            $booking->user->sendNotification(
                'Status Pesanan: '.$booking->statusLabel(),
                $booking->statusMessage(),
                route('bookings.show', $booking)
            );
        }

        return back()->with('success', 'Pesanan berhasil diperbarui.');
    }
}
