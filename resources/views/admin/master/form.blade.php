@extends('layouts.admin')

@section('title', ($item->exists ? 'Edit ' : 'Tambah ').$meta['label'])
@section('subtitle', $meta['title'])

@section('actions')
    <a href="{{ route("admin.{$meta['route']}.index") }}" class="btn-outline py-2">Kembali</a>
@endsection

@section('content')
    <form method="POST" action="{{ $item->exists ? route("admin.{$meta['route']}.update", $item) : route("admin.{$meta['route']}.store") }}"
          class="card max-w-xl space-y-5 p-6">
        @csrf
        @if ($item->exists) @method('PUT') @endif

        <div>
            <label class="label" for="name">Nama</label>
            <input id="name" name="name" value="{{ old('name', $item->name) }}" class="admin-input" required autofocus>
            @error('name') <p class="error">{{ $message }}</p> @enderror
        </div>

        @foreach ($extraFields as $field => [$label, $options])
            <div>
                <label class="label" for="{{ $field }}">{{ $label }}</label>
                <select id="{{ $field }}" name="{{ $field }}" class="admin-input" required>
                    @foreach ($options as $value => $text)
                        <option value="{{ $value }}" @selected(old($field, $item->$field) === $value)>{{ $text }}</option>
                    @endforeach
                </select>
                @error($field) <p class="error">{{ $message }}</p> @enderror
            </div>
        @endforeach

        <div>
            <label class="label" for="sort_order">Urutan Tampil</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $item->sort_order) }}" class="admin-input">
            <p class="mt-1 text-xs text-grey">Angka kecil tampil lebih dulu.</p>
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" class="rounded text-primary focus:ring-primary" @checked(old('is_active', $item->is_active))>
            Aktif (tampil untuk pelanggan)
        </label>

        <button class="btn-primary w-full">{{ $item->exists ? 'Simpan Perubahan' : 'Tambah' }}</button>
    </form>
@endsection
