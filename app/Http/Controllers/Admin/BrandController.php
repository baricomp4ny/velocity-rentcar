<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BrandRequest;
use App\Models\Brand;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index()
    {
        return view('admin.brands.index', ['brands' => Brand::withCount('cars')->orderBy('sort_order')->get()]);
    }

    public function create()
    {
        return view('admin.brands.form', ['brand' => new Brand(['sort_order' => Brand::max('sort_order') + 1])]);
    }

    public function store(BrandRequest $request)
    {
        $brand = new Brand($request->brandData());
        $this->storeLogo($request, $brand);
        $brand->save();

        return redirect()->route('admin.brands.index')->with('success', "Merek {$brand->name} berhasil ditambahkan.");
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.form', compact('brand'));
    }

    public function update(BrandRequest $request, Brand $brand)
    {
        $brand->fill($request->brandData());
        $this->storeLogo($request, $brand);
        $brand->save();

        return redirect()->route('admin.brands.index')->with('success', "Merek {$brand->name} berhasil diperbarui.");
    }

    public function destroy(Brand $brand)
    {
        if ($brand->cars()->exists()) {
            return back()->with('error', 'Merek masih memiliki mobil. Hapus atau pindahkan mobilnya terlebih dahulu.');
        }

        $this->deleteLogo($brand);
        $brand->delete();

        return back()->with('success', 'Merek berhasil dihapus.');
    }

    private function storeLogo(BrandRequest $request, Brand $brand): void
    {
        if ($request->hasFile('logo')) {
            $this->deleteLogo($brand);
            $brand->logo = $request->file('logo')->store('brands', 'public');
        }
    }

    private function deleteLogo(Brand $brand): void
    {
        if ($brand->logo && ! str_starts_with($brand->logo, 'images/')) {
            Storage::disk('public')->delete($brand->logo);
        }
    }
}
