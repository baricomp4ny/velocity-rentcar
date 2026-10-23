@extends('layouts.app')

@section('title', 'Riwayat Pemesanan')
@section('bottom-nav', 'bookings')

@section('content')
    <x-page-header title="Riwayat Pemesanan" :back="route('profile')"/>

    <div class="container-page grid gap-4 px-5 py-5 lg:grid-cols-2 lg:gap-6 lg:px-8 lg:py-6">
        @forelse ($bookings as $booking)
            <div class="min-w-0 rounded-2xl border border-gray-100 bg-white p-4 shadow-card">
                <a href="{{ route('bookings.show', $booking) }}" class="flex gap-3">
                    <img src="{{ $booking->car->imageUrl() }}" alt="" class="h-16 w-20 flex-none rounded-lg object-cover">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <p class="truncate text-sm font-semibold">{{ $booking->car->name }}</p>
                            <span class="flex-none rounded-full px-2 py-0.5 text-[10px] font-medium {{ $booking->statusColor() }}">{{ $booking->statusLabel() }}</span>
                        </div>
                        <p class="text-[11px] text-grey">{{ $booking->code }}</p>
                        <p class="mt-1 flex items-center gap-1 text-xs"><x-icon name="calendar" class="h-3.5 w-3.5 text-primary"/>{{ tanggal($booking->pickup_date, 'd M Y') }} · {{ $booking->durationDays() }} Hari</p>
                    </div>
                </a>
                <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-3">
                    <p class="text-sm font-semibold text-primary">{{ rupiah($booking->total()) }}</p>
                    <div class="flex gap-2">
                        <a href="{{ route('bookings.show', $booking) }}" class="btn-outline">Detail</a>
                        {{-- Repeat order untuk pengguna tetap --}}
                        <a href="{{ route('bookings.create', [$booking->car, 'repeat' => $booking->code]) }}" class="inline-flex items-center rounded-lg bg-primary px-3 py-1.5 text-xs font-medium text-white">Pesan Lagi</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl bg-neutral px-6 py-14 text-center lg:col-span-2 lg:bg-white">
                <x-icon name="receipt" class="mx-auto h-10 w-10 text-grey"/>
                <p class="mt-3 font-medium">Belum ada pesanan</p>
                <p class="mt-1 text-sm text-grey">Pesanan yang kamu buat akan muncul di sini.</p>
                <a href="{{ route('home') }}" class="btn-primary mt-5 text-sm">Cari Mobil</a>
            </div>
        @endforelse
    </div>
@endsection
