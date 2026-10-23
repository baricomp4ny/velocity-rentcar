<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        $filters = $this->filters($request);
        $searching = collect($filters)->filter()->isNotEmpty();

        $cars = Car::forListing()
            ->available()
            ->filter($filters)
            ->when($searching, fn ($q) => $q->orderBy('price_per_day'))
            ->unless($searching, fn ($q) => $q->orderByDesc('is_popular')->orderByDesc('rating'))
            ->get();

        $featured = $searching ? null : $this->featuredCar($cars);

        return view('user.home', [
            'featured' => $featured,
            'cars' => $cars,
            'brands' => Brand::orderBy('sort_order')->get(),
            'filters' => $filters,
            'searching' => $searching,
            'favoriteIds' => $request->user()->favorites()->pluck('cars.id')->all(),
        ]);
    }

    public function explore(Request $request)
    {
        $brands = Brand::orderBy('sort_order')->get();
        $filters = $this->filters($request);

        // Semua mobil dirender; pilihan merek (bisa lebih dari satu) disaring langsung di browser.
        // ?brand=ID (dari logo di Home) atau ?brand[]=ID&brand[]=ID menentukan merek yang terpilih awal.
        $selected = $brands->pluck('id')->intersect((array) $request->query('brand', []))->values()->all();

        $cars = Car::forListing()
            ->available()
            ->filter($filters)
            ->orderBy('brand_id')
            ->orderBy('price_per_day')
            ->get()
            ->sortBy(fn (Car $car) => $car->brand->sort_order)
            ->values();

        return view('user.explore', [
            ...compact('brands', 'selected', 'cars', 'filters'),
            'favoriteIds' => $request->user()->favorites()->pluck('cars.id')->all(),
        ]);
    }

    public function updateLocation(Request $request)
    {
        $request->user()->update($request->validate([
            'location_id' => ['required', Rule::exists('locations', 'id')->where('is_active', true)],
        ]));

        return back();
    }

    /**
     * Mobil untuk banner Home: pilihan admin, atau mobil teratas di daftar populer.
     */
    private function featuredCar($cars): ?Car
    {
        return $cars->firstWhere('id', setting('featured_car_id')) ?? $cars->first();
    }

    private function filters(Request $request): array
    {
        return [
            'q' => $request->query('q'),
            'category' => array_filter((array) $request->query('category', [])),
            'transmission' => array_filter((array) $request->query('transmission', [])),
            'fuel' => array_filter((array) $request->query('fuel', [])),
            'seats' => $request->query('seats'),
        ];
    }
}
