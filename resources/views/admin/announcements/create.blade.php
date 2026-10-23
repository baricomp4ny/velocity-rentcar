@extends('layouts.admin')

@section('title', 'Pengumuman')
@section('subtitle', 'Kirim info (promo baru, jam operasional, dll.) ke menu Notification semua pelanggan')

@section('content')
    <form method="POST" action="{{ route('admin.announcements.store') }}" class="card max-w-2xl space-y-5 p-6"
          x-data="{ title: @js(old('title', '')), body: @js(old('body', '')) }">
        @csrf

        <div>
            <label class="label" for="title">Judul</label>
            <input id="title" name="title" x-model="title" maxlength="100" placeholder="Promo akhir pekan!" class="admin-input" required>
            @error('title') <p class="error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="label" for="body">Isi Pengumuman</label>
            <textarea id="body" name="body" x-model="body" rows="3" maxlength="255" placeholder="Pakai kode VELOCITY10 untuk diskon 10% semua mobil." class="admin-input" required></textarea>
            @error('body') <p class="error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="label" for="url">Tautan saat diklik <span class="font-normal text-grey">(opsional)</span></label>
            <input id="url" name="url" value="{{ old('url') }}" placeholder="/home" class="admin-input">
            @error('url') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div>
            <p class="label">Pratinjau</p>
            <div class="flex gap-3 rounded-xl bg-primary-50/60 p-4">
                <span class="flex h-11 w-11 flex-none items-center justify-center rounded-full bg-primary-50 text-primary"><x-icon name="bell" class="h-5 w-5"/></span>
                <div class="min-w-0">
                    <p class="text-sm font-semibold" x-text="title || 'Judul pengumuman'"></p>
                    <p class="mt-0.5 text-xs text-grey" x-text="body || 'Isi pengumuman akan tampil di sini.'"></p>
                </div>
            </div>
        </div>

        <button class="btn-primary w-full" onclick="return confirm('Kirim pengumuman ke {{ $customerCount }} pelanggan?')">
            <x-icon name="send" class="h-4 w-4"/> Kirim ke {{ $customerCount }} pelanggan
        </button>
    </form>
@endsection
