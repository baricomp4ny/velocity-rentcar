@extends('layouts.admin')

@section('title', 'Pesanan '.$booking->code)
@section('subtitle', 'Dibuat '.$booking->created_at->translatedFormat('d F Y H:i'))

@section('actions')
    <a href="{{ route('admin.bookings.index') }}" class="btn-outline py-2">Kembali</a>
@endsection

@section('content')
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="card p-6 xl:col-span-2">
            <div class="flex flex-wrap items-center gap-4 border-b border-gray-100 pb-5">
                <img src="{{ $booking->car->imageUrl() }}" alt="" class="h-20 w-28 rounded-xl object-cover">
                <div class="flex-1">
                    <p class="text-lg font-semibold">{{ $booking->car->name }}</p>
                    <p class="text-sm text-grey">{{ $booking->car->category->name }} · {{ $booking->car->transmission }} · {{ $booking->car->fuel }}</p>
                </div>
                <span class="rounded-full px-3 py-1 text-sm font-medium {{ $booking->statusColor() }}">{{ $booking->statusLabel() }}</span>
            </div>

            <dl class="mt-5 grid gap-x-8 gap-y-4 text-sm sm:grid-cols-2">
                @foreach ([
                    'Nama Penyewa' => $booking->user->name,
                    'Nomor Telepon' => $booking->user->phone ?? '-',
                    'Email' => $booking->user->email,
                    'Alamat' => $booking->user->address ?? '-',
                    'Lokasi Pengambilan' => $booking->pickupLocation->name,
                    'Tanggal Pengambilan' => tanggal($booking->pickup_date, 'l, d F Y'),
                    'Tanggal Pengembalian' => tanggal($booking->return_date, 'l, d F Y'),
                    'Durasi' => $booking->durationDays().' hari',
                    'Harga/hari' => rupiah($booking->price_per_day),
                    'Diskon' => $booking->discount ? '- '.rupiah($booking->discount, false).($booking->promo ? " ({$booking->promo->code})" : '') : '-',
                    'Metode Pembayaran' => $booking->paymentMethod->name,
                    'Dibayar' => $booking->created_at->translatedFormat('d F Y H:i'),
                ] as $label => $value)
                    <div>
                        <dt class="text-xs text-grey">{{ $label }}</dt>
                        <dd class="mt-0.5 font-medium">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="mt-6 flex items-center justify-between rounded-xl bg-primary-50 px-5 py-4">
                <span class="font-medium">Total Harga</span>
                <span class="text-xl font-semibold text-primary">{{ rupiah($booking->total()) }}</span>
            </div>

            @if ($booking->rating)
                <div class="mt-5 rounded-xl bg-neutral p-4 text-sm">
                    <p class="font-medium">Penilaian pelanggan: {{ $booking->rating }}/5</p>
                    @if ($booking->review) <p class="mt-1 text-grey">{{ $booking->review }}</p> @endif
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <form method="POST" action="{{ route('admin.bookings.update', $booking) }}" class="card p-6">
                @csrf @method('PUT')
                <h2 class="font-semibold">Ubah Status</h2>
                <p class="text-xs text-grey">Pelanggan akan menerima notifikasi setiap status berubah.</p>
                <div class="mt-4 space-y-2">
                    @foreach (\App\Models\Booking::STATUSES as $value => $label)
                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-200 px-4 py-2.5 text-sm has-[:checked]:border-primary has-[:checked]:bg-primary-50">
                            <input type="radio" name="status" value="{{ $value }}" class="text-primary focus:ring-primary" @checked($booking->status === $value)>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                <label class="mt-4 block text-sm font-medium" for="notes">Catatan</label>
                <textarea id="notes" name="notes" rows="3" class="admin-input mt-1">{{ old('notes', $booking->notes) }}</textarea>
                <button class="btn-primary mt-4 w-full">Simpan</button>
            </form>

            <div class="card space-y-2 p-6">
                <h2 class="font-semibold">Hubungi Penyewa</h2>
                <a href="{{ route('admin.chats.show', $booking->user) }}" class="btn-outline w-full py-2.5 text-sm">Chat di aplikasi</a>
                @if ($booking->user->phone)
                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', $booking->user->phone)) }}" target="_blank" rel="noopener" class="btn-outline w-full py-2.5 text-sm">WhatsApp {{ $booking->user->phone }}</a>
                @endif
            </div>
        </div>
    </div>
@endsection
