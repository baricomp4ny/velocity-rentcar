@extends('layouts.app')

@section('title', 'Info Bayar')

@section('content')
    @php $cancelled = $booking->status === 'cancelled'; @endphp

    <div class="min-h-screen bg-neutral px-6 pb-12 lg:mx-auto lg:min-h-0 lg:max-w-lg lg:px-0 lg:pb-0 lg:pt-4">
        <div class="-mx-6 h-2 bg-primary lg:hidden"></div>

        {{-- Tiket "Info Bayar" seperti di prototipe --}}
        <div class="relative mt-10 overflow-hidden rounded-2xl border border-primary-100 bg-white">
            <div class="h-3 bg-primary"></div>
            <div class="flex items-center gap-3 px-7 py-4">
                <x-logo class="h-10 w-auto"/>
                <div class="text-xs">
                    <p class="font-medium">{{ setting('store_name') }}</p>
                    <p class="text-grey">{{ setting('store_tagline') }}</p>
                </div>
            </div>
            <div class="relative border-t border-dashed border-primary-100">
                <span class="absolute -left-3 -top-3 h-6 w-6 rounded-full border border-primary-100 bg-neutral"></span>
                <span class="absolute -right-3 -top-3 h-6 w-6 rounded-full border border-primary-100 bg-neutral"></span>
            </div>

            <div class="px-5 pb-6 pt-3 text-center">
                <h1 class="text-lg font-semibold text-primary">Info Bayar</h1>
                @if ($cancelled)
                    <span class="mx-auto mt-4 flex h-20 w-20 items-center justify-center rounded-full border-[6px] border-red-500 text-red-500"><x-icon name="x" class="h-10 w-10"/></span>
                    <p class="mt-3 text-lg font-semibold text-red-500">Pesanan Dibatalkan</p>
                @else
                    <span class="mx-auto mt-4 flex h-20 w-20 items-center justify-center rounded-full border-[6px] border-primary text-primary"><x-icon name="check" class="h-10 w-10"/></span>
                    <p class="mt-3 text-lg font-semibold text-primary">Pembayaran Sukses</p>
                @endif
                <p class="mt-1 text-sm text-grey">{{ setting('store_name') }} · {{ $booking->code }}</p>

                <div class="mt-5 rounded-xl border border-gray-100 p-4 text-left">
                    <h2 class="border-b border-primary pb-2 text-base text-primary">Informasi Pembayaran</h2>
                    <dl class="mt-3 space-y-3 text-sm">
                        @foreach ([
                            'Kendaraan' => $booking->car->modelName(),
                            'Peminjam' => $booking->user->name,
                            'Tanggal Pesan' => tanggal($booking->created_at),
                            'Pengambilan' => tanggal($booking->pickup_date),
                            'Pengembalian' => tanggal($booking->return_date),
                            'Lokasi' => $booking->pickupLocation->name,
                            'Durasi Peminjaman' => $booking->durationDays().' Hari',
                            'Pembayaran' => $booking->paymentMethod->name,
                        ] as $label => $value)
                            <div class="flex justify-between gap-4">
                                <dt class="flex-none font-medium">{{ $label }}</dt>
                                <dd class="text-right text-grey">{{ $value }}</dd>
                            </div>
                        @endforeach
                        @if ($booking->discount)
                            <div class="flex justify-between gap-4">
                                <dt class="font-medium">Diskon{{ $booking->promo ? " ({$booking->promo->code})" : '' }}</dt>
                                <dd class="whitespace-nowrap text-emerald-600">- {{ rupiah($booking->discount) }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between gap-4">
                            <dt class="font-medium">Total Harga</dt>
                            <dd class="font-semibold">{{ rupiah($booking->total()) }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="font-medium">Status</dt>
                            <dd><span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $booking->statusColor() }}">{{ $booking->statusLabel() }}</span></dd>
                        </div>
                    </dl>
                </div>

                <a href="{{ route('home') }}" class="btn-primary mt-6 rounded-xl px-6 font-semibold uppercase tracking-wide">Kembali ke Menu</a>
                <a href="{{ route('bookings.download', $booking) }}" class="mt-3 flex items-center justify-center gap-1.5 text-sm font-semibold uppercase">
                    <x-icon name="download" class="h-4 w-4"/> Download
                </a>
                <p class="mx-auto mt-3 max-w-[260px] text-[11px] text-grey">Silahkan download bukti pembayaran ini sebagai bukti bahwa anda telah menyelesaikan pembayaran</p>
            </div>
        </div>

        @if ($booking->canBeReviewed())
            <form method="POST" action="{{ route('bookings.review', $booking) }}" class="card mt-5 p-5" x-data="{ rating: 0 }">
                @csrf
                <h2 class="font-semibold">Beri Penilaian</h2>
                <p class="text-xs text-grey">Bagaimana pengalamanmu menyewa {{ $booking->car->name }}?</p>
                <div class="mt-3 flex gap-1">
                    @for ($s = 1; $s <= 5; $s++)
                        <button type="button" @click="rating = {{ $s }}" aria-label="{{ $s }} bintang">
                            <x-icon name="star" class="h-8 w-8 transition" ::class="rating >= {{ $s }} ? 'text-amber-400' : 'text-gray-200'"/>
                        </button>
                    @endfor
                </div>
                <input type="hidden" name="rating" :value="rating">
                <textarea name="review" rows="2" class="input mt-3" placeholder="Tulis ulasan (opsional)"></textarea>
                <button type="submit" class="btn-primary mt-3 w-full" :disabled="!rating">Kirim Penilaian</button>
            </form>
        @elseif ($booking->rating)
            <div class="card mt-5 p-5">
                <p class="text-sm font-semibold">Penilaianmu</p>
                <p class="mt-1 flex gap-0.5">
                    @for ($s = 1; $s <= 5; $s++)
                        <x-icon name="star" class="h-5 w-5 {{ $s <= $booking->rating ? 'text-amber-400' : 'text-gray-200' }}"/>
                    @endfor
                </p>
                @if ($booking->review) <p class="mt-1 text-sm text-grey">{{ $booking->review }}</p> @endif
            </div>
        @endif

        <div class="mt-5 flex gap-3">
            <a href="{{ route('bookings.create', [$booking->car, 'repeat' => $booking->code]) }}" class="btn-outline flex-1 py-3 text-sm">Pesan Lagi</a>
            @if ($booking->canBeCancelled())
                <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="flex-1"
                      onsubmit="return confirm('Batalkan pesanan {{ $booking->code }}?')">
                    @csrf
                    <button class="w-full rounded-lg border border-red-200 bg-white py-3 text-sm font-medium text-red-500">Batalkan Pesanan</button>
                </form>
            @endif
        </div>
    </div>
@endsection
