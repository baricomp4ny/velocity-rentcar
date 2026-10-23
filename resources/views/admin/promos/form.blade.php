@extends('layouts.admin')

@section('title', $promo->exists ? 'Edit Promo '.$promo->code : 'Tambah Promo')

@section('actions')
    <a href="{{ route('admin.promos.index') }}" class="btn-outline py-2">Kembali</a>
@endsection

@section('content')
    <form method="POST" action="{{ $promo->exists ? route('admin.promos.update', $promo) : route('admin.promos.store') }}" class="card max-w-xl space-y-5 p-6">
        @csrf
        @if ($promo->exists) @method('PUT') @endif

        <div>
            <label class="label" for="code">Kode Promo</label>
            <input id="code" name="code" value="{{ old('code', $promo->code) }}" placeholder="VELOCITY10" class="admin-input uppercase" required>
            @error('code') <p class="error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="label" for="description">Keterangan</label>
            <input id="description" name="description" value="{{ old('description', $promo->description) }}" class="admin-input">
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="label" for="discount_percent">Diskon (%)</label>
                <input id="discount_percent" name="discount_percent" type="number" min="1" max="100" value="{{ old('discount_percent', $promo->discount_percent) }}" class="admin-input" required>
                @error('discount_percent') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="max_discount">Maksimal Diskon (Rp)</label>
                <input id="max_discount" name="max_discount" type="number" min="0" step="1000" value="{{ old('max_discount', $promo->max_discount) }}" placeholder="Kosongkan jika tanpa batas" class="admin-input">
                @error('max_discount') <p class="error">{{ $message }}</p> @enderror
            </div>
        </div>
        <div>
            <label class="label" for="expires_at">Berlaku Sampai</label>
            <input id="expires_at" name="expires_at" type="date" value="{{ old('expires_at', $promo->expires_at?->toDateString()) }}" class="admin-input">
            @error('expires_at') <p class="error">{{ $message }}</p> @enderror
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" class="rounded text-primary focus:ring-primary" @checked(old('is_active', $promo->is_active))>
            Aktif
        </label>

        <button class="btn-primary w-full">{{ $promo->exists ? 'Simpan Perubahan' : 'Tambah Promo' }}</button>
    </form>
@endsection
