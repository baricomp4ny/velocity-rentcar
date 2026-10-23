@extends('layouts.admin')

@section('title', $brand->exists ? 'Edit Merek '.$brand->name : 'Tambah Merek')

@section('actions')
    <a href="{{ route('admin.brands.index') }}" class="btn-outline py-2">Kembali</a>
@endsection

@section('content')
    <form method="POST" enctype="multipart/form-data"
          action="{{ $brand->exists ? route('admin.brands.update', $brand) : route('admin.brands.store') }}"
          class="card max-w-xl space-y-5 p-6" x-data="{ preview: @js($brand->logoUrl()) }">
        @csrf
        @if ($brand->exists) @method('PUT') @endif

        <div class="flex items-center gap-5">
            <span class="flex h-20 w-20 flex-none items-center justify-center overflow-hidden rounded-full bg-neutral">
                <img x-show="preview" :src="preview" class="h-full w-full object-contain p-4" alt="">
                <x-icon name="tag" class="h-6 w-6 text-grey" x-show="!preview"/>
            </span>
            <div class="flex-1">
                <label class="label">Logo</label>
                <input type="file" name="logo" accept="image/*" class="block w-full text-xs file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:text-primary"
                       @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : preview">
                @error('logo') <p class="error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="label" for="name">Nama Merek</label>
            <input id="name" name="name" value="{{ old('name', $brand->name) }}" class="admin-input" required>
            @error('name') <p class="error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="label" for="sort_order">Urutan Tampil</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $brand->sort_order) }}" class="admin-input">
        </div>

        <button class="btn-primary w-full">{{ $brand->exists ? 'Simpan Perubahan' : 'Tambah Merek' }}</button>
    </form>
@endsection
