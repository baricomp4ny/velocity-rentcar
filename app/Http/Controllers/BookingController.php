<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Car;
use App\Models\Location;
use App\Models\PaymentMethod;
use App\Models\Promo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    /**
     * Halaman pemesanan: Rental Summary + 4 langkah (Informasi Penyewa,
     * Jadwal, Pembayaran, Konfirmasi). Parameter ?repeat= mengisi ulang
     * pilihan dari pesanan lama (fitur repeat order).
     */
    public function create(Request $request, Car $car)
    {
        abort_unless($car->is_available, 404);

        $user = $request->user();
        $previous = $user->bookings()->where('code', $request->query('repeat'))->first();

        return view('user.bookings.create', [
            'car' => $car->load(['brand', 'photos']),
            'user' => $user,
            'locations' => Location::active()->ordered()->get(),
            'paymentMethods' => PaymentMethod::active()->ordered()->get()->groupBy(fn (PaymentMethod $m) => $m->typeLabel()),
            'prefill' => [
                'pickup_location_id' => $previous->pickup_location_id ?? $user->location_id,
                'duration_days' => $previous?->durationDays() ?? 1,
                'payment_method_id' => $previous->payment_method_id ?? null,
            ],
            'bookedRanges' => $car->bookings()
                ->active()
                ->where('return_date', '>=', today())
                ->get(['pickup_date', 'return_date'])
                ->map(fn (Booking $b) => ['from' => $b->pickup_date->toDateString(), 'to' => $b->return_date->toDateString()]),
        ]);
    }

    public function checkPromo(Request $request)
    {
        $promo = Promo::findUsable($request->query('code'));

        if (! $promo) {
            return response()->json(['valid' => false, 'message' => 'Kode promo tidak valid atau sudah kedaluwarsa.']);
        }

        return response()->json([
            'valid' => true,
            'code' => $promo->code,
            'percent' => $promo->discount_percent,
            'max' => $promo->max_discount,
            'message' => "Promo {$promo->code} dipakai: {$promo->summary()}",
        ]);
    }

    public function store(BookingRequest $request, Car $car)
    {
        abort_unless($car->is_available, 404);

        $user = $request->user();
        $pickup = $request->pickupDate();
        $return = $request->returnDate();
        $promo = $request->promo();

        $booking = DB::transaction(function () use ($request, $user, $car, $pickup, $return, $promo) {
            // Kunci baris mobil agar dua pesanan bersamaan tidak bentrok tanggal.
            Car::whereKey($car->id)->lockForUpdate()->first();

            if ($car->isBookedBetween($pickup->toDateString(), $return->toDateString())) {
                throw ValidationException::withMessages([
                    'pickup_date' => 'Mobil sudah disewa orang lain pada tanggal tersebut. Silakan pilih tanggal lain.',
                ]);
            }

            $user->update($request->renter());

            $subtotal = $car->price_per_day * $pickup->diffInDays($return);

            // Pembayaran disimulasikan langsung berhasil seperti alur prototipe
            // (Rental Now -> Info Bayar "Pembayaran Sukses").
            return $user->bookings()->create([
                'car_id' => $car->id,
                'promo_id' => $promo?->id,
                'pickup_location_id' => $request->validated('pickup_location_id'),
                'pickup_date' => $pickup,
                'return_date' => $return,
                'price_per_day' => $car->price_per_day,
                'discount' => $promo?->discountFor($subtotal) ?? 0,
                'payment_method_id' => $request->validated('payment_method_id'),
                'notes' => $request->validated('notes'),
            ]);
        });

        $user->sendNotification(
            'Pembayaran Sukses',
            "Pesanan {$booking->code} untuk {$car->name} berhasil dibayar. Ambil mobil pada ".tanggal($booking->pickup_date).'.',
            route('bookings.show', $booking)
        );

        return redirect()->route('bookings.show', $booking);
    }

    public function index(Request $request)
    {
        $bookings = $request->user()->bookings()->with(['car.photos', 'pickupLocation'])->latest()->get();

        return view('user.bookings.index', compact('bookings'));
    }

    public function show(Request $request, Booking $booking)
    {
        $this->authorizeOwner($request, $booking);

        return view('user.bookings.show', ['booking' => $booking->load(['car.brand', 'promo', 'user', 'pickupLocation', 'paymentMethod'])]);
    }

    public function download(Request $request, Booking $booking)
    {
        $this->authorizeOwner($request, $booking);

        return Pdf::loadView('user.bookings.pdf', ['booking' => $booking->load(['car.brand', 'promo', 'user', 'pickupLocation', 'paymentMethod'])])
            ->setPaper([0, 0, 360, 640])
            ->download("Bukti-Pembayaran-{$booking->code}.pdf");
    }

    public function cancel(Request $request, Booking $booking)
    {
        $this->authorizeOwner($request, $booking);

        if (! $booking->canBeCancelled()) {
            return back()->with('error', 'Pesanan ini sudah tidak bisa dibatalkan.');
        }

        $booking->update(['status' => 'cancelled']);

        $request->user()->sendNotification(
            'Pesanan Dibatalkan',
            "Pesanan {$booking->code} telah dibatalkan. Dana akan dikembalikan ke metode pembayaran {$booking->paymentMethod->name}.",
            route('bookings.show', $booking)
        );

        return redirect()->route('bookings.index')->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function review(Request $request, Booking $booking)
    {
        $this->authorizeOwner($request, $booking);
        abort_unless($booking->canBeReviewed(), 403);

        $booking->update($request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:500'],
        ]));

        return back()->with('success', 'Terima kasih atas penilaianmu!');
    }

    private function authorizeOwner(Request $request, Booking $booking): void
    {
        abort_unless($booking->user_id === $request->user()->id, 404);
    }
}
