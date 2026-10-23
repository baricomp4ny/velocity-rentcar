@extends('layouts.admin')

@section('title', $car->exists ? 'Edit '.$car->name : 'Tambah Mobil')

@section('actions')
    <a href="{{ route('admin.cars.index') }}" class="btn-outline py-2">Kembali</a>
@endsection

@section('content')
    <form method="POST" enctype="multipart/form-data"
          action="{{ $car->exists ? route('admin.cars.update', $car) : route('admin.cars.store') }}"
          class="grid gap-6 xl:grid-cols-3">
        @csrf
        @if ($car->exists) @method('PUT') @endif

        <div class="card space-y-5 p-6 xl:col-span-2">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="label" for="brand_id">Merek</label>
                    <select id="brand_id" name="brand_id" class="admin-input" required>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(old('brand_id', $car->brand_id) == $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                    @error('brand_id') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label" for="name">Nama Mobil</label>
                    <input id="name" name="name" value="{{ old('name', $car->name) }}" placeholder="Toyota Innova Zenix Q 2.0" class="admin-input" required>
                    @error('name') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-3">
                <div>
                    <label class="label" for="category_id">Kategori</label>
                    <select id="category_id" name="category_id" class="admin-input" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $car->category_id) === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="error">{{ $message }}</p> @enderror
                </div>
                @foreach ([
                    'transmission' => ['Transmisi', \App\Models\Car::TRANSMISSIONS],
                    'fuel' => ['Bahan Bakar', \App\Models\Car::FUELS],
                ] as $field => [$label, $options])
                    <div>
                        <label class="label" for="{{ $field }}">{{ $label }}</label>
                        <select id="{{ $field }}" name="{{ $field }}" class="admin-input" required>
                            @foreach ($options as $option)
                                <option @selected(old($field, $car->$field) === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                        @error($field) <p class="error">{{ $message }}</p> @enderror
                    </div>
                @endforeach
            </div>

            <div class="grid gap-5 sm:grid-cols-4">
                <div class="sm:col-span-2">
                    <label class="label" for="fuel_grade">Jenis BBM <span class="font-normal text-grey">(opsional)</span></label>
                    <input id="fuel_grade" name="fuel_grade" value="{{ old('fuel_grade', $car->fuel_grade) }}" placeholder="RON 92 / Dexlite" class="admin-input">
                    @error('fuel_grade') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label" for="seats">Kursi</label>
                    <input id="seats" name="seats" type="number" min="2" max="20" value="{{ old('seats', $car->seats ?? 7) }}" class="admin-input" required>
                    @error('seats') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label" for="year">Tahun</label>
                    <input id="year" name="year" type="number" value="{{ old('year', $car->year) }}" class="admin-input">
                    @error('year') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Data performa: tampil di poster Home dan halaman detail jika diisi --}}
            <div class="grid gap-5 sm:grid-cols-3">
                <div>
                    <label class="label" for="model_code">Kode Model <span class="font-normal text-grey">(opsional)</span></label>
                    <input id="model_code" name="model_code" value="{{ old('model_code', $car->model_code) }}" placeholder="G82" class="admin-input">
                    @error('model_code') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label" for="power_hp">Tenaga (HP) <span class="font-normal text-grey">(opsional)</span></label>
                    <input id="power_hp" name="power_hp" type="number" min="30" max="2000" value="{{ old('power_hp', $car->power_hp) }}" placeholder="503" class="admin-input">
                    @error('power_hp') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label" for="acceleration">0-100 km/jam (detik) <span class="font-normal text-grey">(opsional)</span></label>
                    <input id="acceleration" name="acceleration" type="number" step="0.1" min="1" max="30" value="{{ old('acceleration', $car->acceleration) }}" placeholder="3.9" class="admin-input">
                    @error('acceleration') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="label" for="price_per_day">Harga per Hari (Rp)</label>
                    <input id="price_per_day" name="price_per_day" type="number" min="50000" step="1000" value="{{ old('price_per_day', $car->price_per_day) }}" class="admin-input" required>
                    @error('price_per_day') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label" for="condition_note">Keterangan Kondisi</label>
                    <input id="condition_note" name="condition_note" value="{{ old('condition_note', $car->condition_note ?? 'Kendaraan dalam keadaan baik') }}" class="admin-input">
                </div>
            </div>

            <div>
                <label class="label" for="description">Deskripsi</label>
                <textarea id="description" name="description" rows="6" class="admin-input" required>{{ old('description', $car->description) }}</textarea>
                @error('description') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-wrap gap-6">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_available" value="1" class="rounded text-primary focus:ring-primary" @checked(old('is_available', $car->is_available))>
                    Tersedia untuk disewa
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_popular" value="1" class="rounded text-primary focus:ring-primary" @checked(old('is_popular', $car->is_popular))>
                    Tampilkan di "Populer Cars"
                </label>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-6" x-data="{ previews: [] }">
                <label class="label">Foto Mobil</label>
                <p class="mb-3 text-xs text-grey">Foto pertama menjadi foto utama. Unggah foto dari berbagai sudut secara berurutan agar pelanggan bisa menggesernya seperti tampilan 360°.</p>

                @if ($car->photos->isNotEmpty())
                    <div class="mb-3 grid grid-cols-3 gap-2">
                        @foreach ($car->photos as $photo)
                            <label class="relative block cursor-pointer">
                                <img src="{{ $photo->url() }}" alt="" class="h-20 w-full rounded-lg object-cover">
                                @if ($loop->first)
                                    <span class="absolute left-1 top-1 rounded bg-primary px-1.5 py-0.5 text-[9px] text-white">Utama</span>
                                @endif
                                <span class="absolute inset-x-0 bottom-0 flex items-center justify-center gap-1 rounded-b-lg bg-ink/60 py-0.5 text-[10px] text-white">
                                    <input type="checkbox" name="remove_photos[]" value="{{ $photo->id }}" class="h-3 w-3 rounded text-red-500"> hapus
                                </span>
                            </label>
                        @endforeach
                    </div>
                @endif

                <div x-show="previews.length" x-cloak class="mb-3 grid grid-cols-3 gap-2">
                    <template x-for="src in previews" :key="src">
                        <img :src="src" alt="" class="h-20 w-full rounded-lg object-cover ring-2 ring-primary-100">
                    </template>
                </div>

                <input type="file" name="photos[]" accept="image/*" multiple
                       class="block w-full text-xs file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:text-primary"
                       @change="previews = [...$event.target.files].map((file) => URL.createObjectURL(file))">
                @error('photos.*') <p class="error">{{ $message }}</p> @enderror
            </div>

            <button class="btn-primary w-full">{{ $car->exists ? 'Simpan Perubahan' : 'Tambah Mobil' }}</button>
        </div>
    </form>
@endsection
