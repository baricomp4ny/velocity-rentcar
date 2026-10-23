<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PromoRequest;
use App\Models\Promo;

class PromoController extends Controller
{
    public function index()
    {
        return view('admin.promos.index', ['promos' => Promo::withCount('bookings')->latest()->get()]);
    }

    public function create()
    {
        return view('admin.promos.form', ['promo' => new Promo(['is_active' => true])]);
    }

    public function store(PromoRequest $request)
    {
        Promo::create($request->promoData());

        return redirect()->route('admin.promos.index')->with('success', 'Kode promo berhasil dibuat.');
    }

    public function edit(Promo $promo)
    {
        return view('admin.promos.form', compact('promo'));
    }

    public function update(PromoRequest $request, Promo $promo)
    {
        $promo->update($request->promoData());

        return redirect()->route('admin.promos.index')->with('success', 'Kode promo berhasil diperbarui.');
    }

    public function destroy(Promo $promo)
    {
        if ($promo->bookings()->exists()) {
            return back()->with('error', 'Promo sudah dipakai di pesanan. Nonaktifkan saja agar riwayat tetap utuh.');
        }

        $promo->delete();

        return back()->with('success', 'Kode promo berhasil dihapus.');
    }
}
