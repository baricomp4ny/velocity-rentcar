<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Booking::reviewed()
            ->with(['car', 'user'])
            ->when($request->query('rating'), fn ($q, $rating) => $q->where('rating', $rating))
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Hapus ulasan yang tidak pantas; rating mobil otomatis ikut berubah.
     */
    public function destroy(Booking $booking)
    {
        $booking->update(['rating' => null, 'review' => null]);

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
