@extends('layouts.app')

@section('title', 'Profile')
@section('bottom-nav', 'profile')

@section('content')
    <div class="min-h-screen bg-neutral lg:min-h-0">
        <header class="bg-primary px-6 pb-20 pt-8 text-center text-white lg:pb-24 lg:pt-12">
            @if ($user->avatar)
                <img src="{{ $user->avatar }}" alt="" class="mx-auto h-20 w-20 rounded-full object-cover ring-4 ring-white/30">
            @else
                <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-white text-2xl font-semibold text-primary ring-4 ring-white/30">{{ $user->initials() }}</span>
            @endif
            <h1 class="mt-3 text-lg font-semibold">{{ $user->name }}</h1>
            <p class="text-xs text-white/80">{{ $user->email }}</p>
        </header>

        <div class="-mt-14 px-5 pb-6 lg:mx-auto lg:max-w-3xl">
            <div class="card grid grid-cols-2 divide-x divide-gray-100 py-4 text-center">
                <a href="{{ route('bookings.index') }}"><p class="text-lg font-semibold text-primary">{{ $user->bookings_count }}</p><p class="text-xs text-grey">Pesanan</p></a>
                <a href="{{ route('saved') }}"><p class="text-lg font-semibold text-primary">{{ $user->favorites_count }}</p><p class="text-xs text-grey">Saved</p></a>
            </div>

            <div class="card mt-4 divide-y divide-gray-100">
                @foreach ([
                    ['edit', 'Edit Profil', 'Nama, email, telepon, alamat', route('profile.edit')],
                    ['receipt', 'Riwayat Pemesanan', 'Lihat & pesan ulang mobil', route('bookings.index')],
                    ['bell', 'Notification', 'Info pembayaran & status pesanan', route('notifications')],
                    ['chat', 'Bantuan', 'Chat dengan Customer Service', route('chat')],
                    ['pin', 'Lokasi', $user->location?->name ?? 'Belum dipilih', route('profile.edit')],
                ] as [$icon, $title, $subtitle, $url])
                    <a href="{{ $url }}" class="flex items-center gap-3 px-4 py-3.5 transition hover:bg-neutral">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-50 text-primary"><x-icon :name="$icon" class="h-5 w-5"/></span>
                        <span class="flex-1">
                            <span class="block text-sm font-medium">{{ $title }}</span>
                            <span class="block text-[11px] text-grey">{{ $subtitle }}</span>
                        </span>
                        <x-icon name="chevron-right" class="h-4 w-4 text-grey"/>
                    </a>
                @endforeach
            </div>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button class="card flex w-full items-center gap-3 px-4 py-3.5 text-left text-sm font-medium text-red-500">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50"><x-icon name="logout" class="h-5 w-5"/></span>
                    Keluar
                </button>
            </form>
        </div>
    </div>
@endsection
