<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings.edit', [
            'groups' => Setting::FIELDS,
            'cars' => Car::with('brand')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request)
    {
        $definitions = Setting::definitions();

        // Teks yang dikosongkan kembali memakai teks bawaan (lihat Setting::value).
        $rules = collect($definitions)->map(fn ($field) => match ($field[1]) {
            'image' => ['nullable', 'image', 'max:4096'],
            'car' => ['nullable', 'exists:cars,id'],
            'tel' => ['required', 'regex:/^62[0-9]{8,13}$/'],
            'textarea' => ['nullable', 'string', 'max:500'],
            default => ['nullable', 'string', 'max:150'],
        })->all();

        $labels = collect($definitions)->map(fn ($field) => $field[0])->all();

        $data = $request->validate($rules, ['cs_phone.regex' => 'Nomor CS diawali 62, contoh: 6281299067622.'], $labels);

        $values = collect($data)->except(array_keys(array_filter($definitions, fn ($f) => $f[1] === 'image')))->all();

        foreach ($definitions as $key => [, $type]) {
            if ($type === 'image' && $request->hasFile($key)) {
                $this->deleteUpload(setting($key));
                $values[$key] = $request->file($key)->store('settings', 'public');
            }
        }

        Setting::saveMany($values);

        return back()->with('success', 'Pengaturan toko berhasil disimpan.');
    }

    private function deleteUpload(?string $path): void
    {
        // Gambar bawaan di public/images tidak dihapus.
        if ($path && ! str_starts_with($path, 'images/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
