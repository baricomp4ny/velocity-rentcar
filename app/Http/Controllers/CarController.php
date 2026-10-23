<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function show(Request $request, Car $car)
    {
        $car->load(['brand', 'category', 'photos'])->loadAvg('bookings as rating', 'rating');

        return view('user.cars.show', [
            'car' => $car,
            'isFavorite' => $request->user()->favorites()->whereKey($car->id)->exists(),
            'reviews' => $car->bookings()->with('user')->whereNotNull('rating')->latest('updated_at')->take(5)->get(),
            'pickupLocation' => $request->user()->location?->name,
        ]);
    }

    /**
     * Layar panggilan ke Customer Service (tombol telepon di "Hubungi Penyewa").
     */
    public function call(Car $car)
    {
        return view('user.cars.call', ['car' => $car, 'phone' => setting('cs_phone')]);
    }
}
