@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan usaha rental mobil hari ini, '.now()->translatedFormat('l, d F Y'))

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as [$label, $value, $icon])
            <div class="card flex items-center gap-4 p-5">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-50 text-primary"><x-icon :name="$icon" class="h-6 w-6"/></span>
                <div>
                    <p class="text-xs text-grey">{{ $label }}</p>
                    <p class="text-xl font-semibold">{{ $value }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="card p-6 xl:col-span-2">
            <h2 class="font-semibold">Pendapatan 6 Bulan Terakhir</h2>
            @php $max = max(1, $monthly->max('total')); @endphp
            <div class="mt-6 flex h-56 items-end gap-4">
                @foreach ($monthly as $month)
                    <div class="flex flex-1 flex-col items-center gap-2">
                        <span class="text-[10px] text-grey">{{ $month['total'] ? number_format($month['total'] / 1000000, 1, ',', '.').' jt' : '0' }}</span>
                        <div class="w-full max-w-[48px] rounded-t-lg bg-primary transition-all" style="height: {{ max(4, $month['total'] / $max * 170) }}px"
                             title="{{ rupiah($month['total'], false) }}"></div>
                        <span class="text-xs text-grey">{{ $month['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-6">
            <a href="{{ route('admin.chats.index') }}" class="card flex items-center gap-4 p-5 transition hover:shadow-lg">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary text-white"><x-icon name="chat" class="h-6 w-6"/></span>
                <div class="flex-1">
                    <p class="font-semibold">Chat Customer Service</p>
                    <p class="text-xs text-grey">{{ $unreadChats ? "$unreadChats pelanggan menunggu balasan" : 'Semua chat sudah dibalas' }}</p>
                </div>
                <x-icon name="chevron-right" class="h-5 w-5 text-grey"/>
            </a>

            <div class="card p-5">
                <h2 class="font-semibold">Pengambilan Hari Ini</h2>
                <div class="mt-3 space-y-2">
                    @forelse ($todayPickups as $booking)
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="flex items-center justify-between rounded-lg bg-neutral px-3 py-2 text-sm">
                            <span>{{ $booking->car->name }}</span>
                            <span class="text-xs text-grey">{{ $booking->user->name }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-grey">Tidak ada jadwal pengambilan hari ini.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4">
            <h2 class="font-semibold">Pesanan Terbaru</h2>
            <a href="{{ route('admin.bookings.index') }}" class="text-sm text-primary">Lihat semua</a>
        </div>
        @include('admin.bookings._table', ['bookings' => $latestBookings])
    </div>
@endsection
