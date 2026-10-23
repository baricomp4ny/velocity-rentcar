<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CarRequest;
use App\Models\Brand;
use App\Models\Car;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $cars = Car::forListing()
            ->withCount('bookings')
            ->filter(['q' => $request->query('q')])
            ->when($request->query('brand'), fn ($q, $brandId) => $q->where('brand_id', $brandId))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.cars.index', ['cars' => $cars, 'brands' => $this->brands()]);
    }

    public function create()
    {
        return view('admin.cars.form', ['car' => new Car(['is_available' => true]), ...$this->formOptions()]);
    }

    public function store(CarRequest $request)
    {
        $car = DB::transaction(function () use ($request) {
            $car = Car::create($request->carData());
            $this->syncPhotos($request, $car);

            return $car;
        });

        return redirect()->route('admin.cars.index')->with('success', "Mobil {$car->name} berhasil ditambahkan.");
    }

    public function edit(Car $car)
    {
        return view('admin.cars.form', ['car' => $car->load('photos'), ...$this->formOptions()]);
    }

    public function update(CarRequest $request, Car $car)
    {
        DB::transaction(function () use ($request, $car) {
            $car->update($request->carData());
            $this->syncPhotos($request, $car);
        });

        return redirect()->route('admin.cars.index')->with('success', "Mobil {$car->name} berhasil diperbarui.");
    }

    public function destroy(Car $car)
    {
        if ($car->bookings()->exists()) {
            return back()->with('error', 'Mobil sudah punya riwayat pesanan dan tidak bisa dihapus. Nonaktifkan saja ketersediaannya.');
        }

        // Hapus satu per satu agar event CarPhoto::deleted ikut menghapus filenya.
        $car->photos->each->delete();
        $car->delete();

        return back()->with('success', 'Mobil berhasil dihapus.');
    }

    /**
     * Hapus foto yang dicentang, lalu tambahkan foto baru di urutan akhir.
     */
    private function syncPhotos(CarRequest $request, Car $car): void
    {
        $car->photos()->whereIn('id', $request->validated('remove_photos', []))->get()->each->delete();

        $order = (int) $car->photos()->max('sort_order');

        foreach ($request->file('photos', []) as $file) {
            $car->photos()->create([
                'path' => $file->store('cars', 'public'),
                'sort_order' => ++$order,
            ]);
        }
    }

    private function brands()
    {
        return Brand::orderBy('sort_order')->get();
    }

    private function formOptions(): array
    {
        return ['brands' => $this->brands(), 'categories' => Category::ordered()->get()];
    }
}
