@extends('layouts.admin')

@section('title', 'Pengaturan Toko')
@section('subtitle', 'Nama toko, kontak Customer Service, serta teks dan gambar di halaman pelanggan')

@section('content')
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="max-w-4xl space-y-6">
        @csrf @method('PUT')

        @foreach ($groups as $group => $fields)
            <section class="card p-6">
                <h2 class="mb-5 font-semibold">{{ $group }}</h2>

                <div class="grid gap-5 sm:grid-cols-2">
                    @foreach ($fields as $key => [$label, $type, $default])
                        <div class="{{ in_array($type, ['textarea', 'image'], true) ? 'sm:col-span-2' : '' }}">
                            <label class="label" for="{{ $key }}">{{ $label }}</label>

                            @if ($type === 'image')
                                <div class="flex flex-wrap items-center gap-4" x-data="{ preview: @js(media_url(setting($key))) }">
                                    <img :src="preview" alt="" class="h-24 w-40 rounded-xl bg-neutral object-cover">
                                    <input id="{{ $key }}" type="file" name="{{ $key }}" accept="image/*"
                                           class="block text-xs file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:text-primary"
                                           @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : preview">
                                </div>
                            @elseif ($type === 'car')
                                <select id="{{ $key }}" name="{{ $key }}" class="admin-input">
                                    <option value="">Otomatis</option>
                                    @foreach ($cars as $car)
                                        <option value="{{ $car->id }}" @selected(old($key, setting($key)) === $car->id)>{{ $car->name }}</option>
                                    @endforeach
                                </select>
                            @elseif ($type === 'textarea')
                                <textarea id="{{ $key }}" name="{{ $key }}" rows="2" class="admin-input" placeholder="{{ $default }}">{{ old($key, setting($key)) }}</textarea>
                            @else
                                <input id="{{ $key }}" name="{{ $key }}" type="{{ $type === 'tel' ? 'tel' : 'text' }}" value="{{ old($key, setting($key)) }}"
                                       placeholder="{{ $default }}" class="admin-input">
                            @endif

                            @error($key) <p class="error">{{ $message }}</p> @enderror
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach

        <p class="text-xs text-grey">Teks yang dikosongkan akan kembali memakai teks bawaan.</p>

        <button class="btn-primary w-full sm:w-auto sm:px-10">Simpan Pengaturan</button>
    </form>
@endsection
