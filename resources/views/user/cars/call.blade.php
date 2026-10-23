@extends('layouts.app')

@section('title', 'Telpon Customer Service')

@section('content')
    {{-- Layar panggilan Customer Service seperti di prototipe (biru, tombol kontrol). --}}
    <div class="flex min-h-screen flex-col items-center bg-gradient-to-b from-primary to-primary-700 px-8 pb-12 pt-20 text-white"
         x-data="{ seconds: 0 }" x-init="setInterval(() => seconds++, 1000)">
        <span class="flex h-24 w-24 items-center justify-center rounded-full bg-white/20 text-3xl font-semibold ring-8 ring-white/10">CS</span>
        <h1 class="mt-6 text-2xl font-semibold tracking-wide">CUSTOMER SERVICE</h1>
        <p class="mt-1 text-sm text-white/80">{{ setting('store_name') }} · {{ $car->name }}</p>
        <p class="mt-3 text-sm text-white/70" x-text="'Menghubungkan... ' + String(Math.floor(seconds / 60)).padStart(2, '0') + ':' + String(seconds % 60).padStart(2, '0')"></p>

        <a href="tel:+{{ $phone }}" class="btn mt-10 inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-semibold text-primary shadow-lg">
            <x-icon name="phone" class="h-5 w-5"/> Panggil +{{ $phone }}
        </a>
        <a href="https://wa.me/{{ $phone }}?text={{ urlencode('Halo '.setting('store_name').", saya ingin menanyakan ketersediaan {$car->name}.") }}"
           target="_blank" rel="noopener" class="mt-3 inline-flex items-center gap-2 rounded-full bg-white/15 px-6 py-3 text-sm font-medium">
            <x-icon name="whatsapp" class="h-5 w-5"/> Hubungi lewat WhatsApp
        </a>

        <div class="mt-auto grid w-full grid-cols-3 gap-6 pt-16 text-center text-xs text-white/80">
            <span class="flex flex-col items-center gap-2"><span class="flex h-14 w-14 items-center justify-center rounded-full bg-white/15"><x-icon name="volume" class="h-6 w-6"/></span>Speaker</span>
            <a href="{{ route('chat', ['text' => "Halo, saya ingin bertanya tentang {$car->name}."]) }}" class="flex flex-col items-center gap-2"><span class="flex h-14 w-14 items-center justify-center rounded-full bg-white/15"><x-icon name="chat" class="h-6 w-6"/></span>Chat</a>
            <span class="flex flex-col items-center gap-2"><span class="flex h-14 w-14 items-center justify-center rounded-full bg-white/15"><x-icon name="mic-off" class="h-6 w-6"/></span>Mute</span>
        </div>

        <a href="{{ route('cars.show', $car) }}" class="mt-10 flex h-16 w-16 items-center justify-center rounded-full bg-red-500 shadow-lg transition hover:bg-red-600" aria-label="Akhiri panggilan">
            <x-icon name="phone-down" class="h-8 w-8"/>
        </a>
    </div>
@endsection
