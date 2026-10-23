<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $cars = $request->user()->favorites()
            ->forListing()
            ->orderByPivot('created_at', 'desc')
            ->get();

        return view('user.saved', compact('cars'));
    }

    public function toggle(Request $request, Car $car)
    {
        $result = $request->user()->favorites()->toggle($car->id);

        return response()->json(['saved' => ! empty($result['attached'])]);
    }
}
