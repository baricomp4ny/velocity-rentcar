@extends('layouts.app')

@section('title', 'Booking '.$car->name)

@php
    $errorStep = match (true) {
        $errors->hasAny(['name', 'phone', 'address']) => 1,
        $errors->hasAny(['pickup_date', 'pickup_location_id', 'duration_days', 'notes']) => 2,
        $errors->has('payment_method_id') => 3,
        $errors->any() => 4,
        default => 1,
    };

    $state = [
        'step' => $errorStep,
        'price' => $car->price_per_day,
        'duration' => (int) old('duration_days', $prefill['duration_days']),
        'promoInput' => old('promo_code', ''),
        'name' => old('name', $user->name),
        'phone' => old('phone', $user->phone ?? ''),
        'address' => old('address', $user->address ?? ''),
        'location' => old('pickup_location_id', $prefill['pickup_location_id'] ?? $locations->first()?->id),
        'locationNames' => $locations->pluck('name', 'id'),
        'paymentNames' => $paymentMethods->flatten()->pluck('name', 'id'),
        'date' => old('pickup_date', ''),
        'payment' => old('payment_method_id', $prefill['payment_method_id']),
        'notes' => old('notes', ''),
        'booked' => $bookedRanges,
        'today' => today()->toDateString(),
        'promoUrl' => route('promo.check'),
    ];
@endphp

@section('content')
    <form method="POST" action="{{ route('bookings.store', $car) }}" x-data="bookingForm(@js($state))"
          class="min-h-screen bg-[#E8E8E8] px-5 pb-10 lg:min-h-0 lg:bg-transparent lg:px-0 lg:py-10">
        @csrf
        <div class="-mx-5 h-2 bg-primary lg:hidden"></div>
        <x-logo class="mx-auto my-6 h-24 w-auto lg:hidden"/>

        <div class="container-page lg:grid lg:grid-cols-[minmax(0,1fr)_minmax(0,1.35fr)] lg:items-start lg:gap-8">
        <h1 class="mb-2 hidden text-2xl font-semibold lg:col-span-2 lg:block">Booking {{ $car->name }}</h1>

        {{-- Rental Summary: estimasi biaya berubah realtime --}}
        <section class="card p-5 lg:sticky lg:top-24 lg:p-6">
            <h2 class="text-base font-semibold">Rental Summary</h2>
            <p class="mt-1 text-xs text-grey">Harga mungkin akan berubah sesuai durasi peminjaman yang anda pilih</p>

            <div class="mt-4 flex items-center gap-4 border-b border-gray-100 pb-4">
                <img src="{{ $car->imageUrl() }}" alt="{{ $car->name }}" class="h-[74px] w-[92px] flex-none rounded-lg object-cover lg:h-24 lg:w-32">
                <div>
                    <p class="text-xl font-semibold">{{ $car->brand->name }}</p>
                    <p class="text-xs font-semibold text-[#2B3A67]">{{ $car->modelName() }}</p>
                </div>
            </div>

            <dl class="mt-4 space-y-3 px-1 text-xs">
                <div class="flex items-center justify-between">
                    <dt class="text-grey">Harga/hari</dt>
                    <dd class="text-sm font-semibold">{{ rupiah($car->price_per_day) }}</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-grey">Durasi Peminjaman</dt>
                    <dd class="flex items-center gap-2 rounded-full bg-primary-50 text-sm font-medium">
                        <button type="button" @click="setDuration(duration - 1)" class="flex h-6 w-6 items-center justify-center rounded-full border border-primary text-primary" aria-label="Kurangi hari"><x-icon name="minus" class="h-3.5 w-3.5"/></button>
                        <span x-text="duration + ' Hari'" class="min-w-[3.5rem] text-center"></span>
                        <button type="button" @click="setDuration(duration + 1)" class="flex h-6 w-6 items-center justify-center rounded-full border border-primary text-primary" aria-label="Tambah hari"><x-icon name="plus" class="h-3.5 w-3.5"/></button>
                    </dd>
                </div>
            </dl>
            <input type="hidden" name="duration_days" :value="duration">

            <div class="mt-4 flex items-center rounded-lg bg-neutral px-4 py-1">
                <input type="text" x-model="promoInput" @keydown.enter.prevent="applyPromo()" placeholder="Gunakan kode promo"
                       class="flex-1 border-0 bg-transparent px-0 text-xs uppercase placeholder:normal-case placeholder:text-grey focus:ring-0">
                <button type="button" @click="applyPromo()" class="text-xs font-medium">Gunakan</button>
            </div>
            <input type="hidden" name="promo_code" :value="promo ? promo.code : ''">
            <p x-show="promoMessage" x-cloak x-text="promoMessage" class="mt-1.5 text-[11px]" :class="promo ? 'text-emerald-600' : 'text-red-500'"></p>
            @error('promo_code') <p class="error">{{ $message }}</p> @enderror

            <div x-show="discount > 0" x-cloak class="mt-3 flex justify-between px-1 text-xs">
                <span class="text-grey">Diskon promo</span>
                <span class="font-medium text-emerald-600" x-text="'- ' + rupiah(discount)"></span>
            </div>
            <div class="mt-4 flex items-start justify-between px-1">
                <div>
                    <p class="text-sm font-semibold">Total Harga Rental</p>
                    <p class="text-[11px] text-grey">Overall price rental</p>
                </div>
                <p class="text-base font-semibold" x-text="rupiah(total)"></p>
            </div>
        </section>

        {{-- Langkah pemesanan --}}
        <section class="card mt-5 p-5 lg:mt-0 lg:p-6">
            {{-- Step 1: Informasi Penyewa (tersimpan ke profil akun) --}}
            <div x-show="step === 1">
                <x-booking-step-title :step="1" title="Informasi Penyewa" subtitle="Tolong masukkan data diri anda"/>
                <div class="space-y-4">
                    <div>
                        <label class="label" for="name">Nama</label>
                        <input id="name" name="name" x-model="name" placeholder="Nama Kamu" class="input">
                        @error('name') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label" for="address">Alamat Lengkap</label>
                        <textarea id="address" name="address" x-model="address" rows="2" placeholder="Alamat Lengkap" class="input"></textarea>
                        @error('address') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label" for="phone">Nomor Telepon</label>
                        <input id="phone" name="phone" type="tel" x-model="phone" placeholder="08xxxxxxxxxx" class="input">
                        @error('phone') <p class="error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Step 2: Jadwal & lokasi pengambilan --}}
            <div x-show="step === 2" x-cloak>
                <x-booking-step-title :step="2" title="Jadwal Sewa" subtitle="Pilih tanggal dan lokasi pengambilan mobil"/>

                <div class="rounded-xl border border-gray-100 p-3">
                    <div class="mb-3 flex items-center justify-between">
                        <button type="button" @click="shiftMonth(-1)" :disabled="!canGoBack" class="flex h-8 w-8 items-center justify-center rounded-full hover:bg-neutral disabled:opacity-30" aria-label="Bulan sebelumnya"><x-icon name="chevron-left" class="h-4 w-4"/></button>
                        <p class="text-sm font-semibold" x-text="monthLabel"></p>
                        <button type="button" @click="shiftMonth(1)" class="flex h-8 w-8 items-center justify-center rounded-full hover:bg-neutral" aria-label="Bulan berikutnya"><x-icon name="chevron-right" class="h-4 w-4"/></button>
                    </div>
                    <div class="grid grid-cols-7 gap-1 text-center text-[11px] text-grey">
                        @foreach (['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $dayName)
                            <span>{{ $dayName }}</span>
                        @endforeach
                    </div>
                    <div class="mt-1 grid grid-cols-7 gap-1 text-center text-sm">
                        <template x-for="cell in calendar" :key="cell.key">
                            <button type="button" :disabled="!cell.date || cell.disabled" @click="date = cell.date"
                                    class="flex h-9 items-center justify-center rounded-lg transition"
                                    :class="{
                                        'invisible': !cell.date,
                                        'bg-primary text-white font-semibold': cell.date === date,
                                        'bg-primary-50 text-primary': cell.inRange && cell.date !== date,
                                        'text-gray-300 line-through': cell.disabled,
                                        'hover:bg-neutral': !cell.disabled && cell.date !== date && !cell.inRange,
                                    }"
                                    x-text="cell.day"></button>
                        </template>
                    </div>
                    <p class="mt-2 flex items-center gap-3 text-[10px] text-grey">
                        <span class="flex items-center gap-1"><span class="h-2.5 w-2.5 rounded bg-primary"></span>Pengambilan</span>
                        <span class="flex items-center gap-1"><span class="h-2.5 w-2.5 rounded bg-primary-50"></span>Masa sewa</span>
                        <span class="line-through">12</span> Sudah disewa
                    </p>
                </div>
                <input type="hidden" name="pickup_date" :value="date">
                <p x-show="conflict" x-cloak class="error">Mobil sudah disewa pada sebagian tanggal tersebut. Kurangi durasi atau pilih tanggal lain.</p>
                @error('pickup_date') <p class="error">{{ $message }}</p> @enderror

                <div class="mt-4 grid grid-cols-2 gap-3 text-xs">
                    <div class="rounded-lg bg-neutral p-3">
                        <p class="text-grey">Pengambilan</p>
                        <p class="mt-0.5 font-semibold" x-text="formatDate(date)"></p>
                    </div>
                    <div class="rounded-lg bg-neutral p-3">
                        <p class="text-grey">Pengembalian</p>
                        <p class="mt-0.5 font-semibold" x-text="formatDate(returnDate)"></p>
                    </div>
                </div>

                <label class="label mt-4" for="pickup_location_id">Lokasi Pengambilan</label>
                <select id="pickup_location_id" name="pickup_location_id" x-model="location" class="input">
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
                @error('pickup_location_id') <p class="error">{{ $message }}</p> @enderror

                <label class="label mt-4" for="notes">Catatan <span class="font-normal text-grey">(opsional)</span></label>
                <textarea id="notes" name="notes" x-model="notes" rows="2" placeholder="Contoh: minta diantar jam 08.00" class="input"></textarea>
            </div>

            {{-- Step 3: Metode Pembayaran --}}
            <div x-show="step === 3" x-cloak>
                <x-booking-step-title :step="3" title="Metode Pembayaran" subtitle="Pilih cara pembayaran yang kamu inginkan"/>
                @foreach ($paymentMethods as $group => $methods)
                    <p class="mb-2 mt-1 flex items-center gap-2 text-sm font-medium">
                        <x-icon :name="$methods->first()->type === 'ewallet' ? 'wallet' : 'card'" class="h-4 w-4 text-primary"/>{{ $group }}
                    </p>
                    <div class="mb-4 space-y-2">
                        @foreach ($methods as $method)
                            <label class="flex cursor-pointer items-center justify-between rounded-xl border px-4 py-3 text-sm transition"
                                   :class="payment === @js($method->id) ? 'border-primary bg-primary-50' : 'border-gray-200'">
                                {{ $method->name }}
                                <input type="radio" name="payment_method_id" value="{{ $method->id }}" x-model="payment" class="text-primary focus:ring-primary">
                            </label>
                        @endforeach
                    </div>
                @endforeach
                @error('payment_method_id') <p class="error">{{ $message }}</p> @enderror
            </div>

            {{-- Step 4: Konfirmasi --}}
            <div x-show="step === 4" x-cloak>
                <x-booking-step-title :step="4" title="Konfirmasi Pesanan" subtitle="Periksa kembali data pesananmu"/>
                <dl class="space-y-2.5 text-sm">
                    <template x-for="[label, value] in [
                        ['Kendaraan', @js($car->name)],
                        ['Nama', name],
                        ['Telepon', phone],
                        ['Alamat', address],
                        ['Pengambilan', formatDate(date) + ' · ' + locationNames[location]],
                        ['Pengembalian', formatDate(returnDate)],
                        ['Durasi Peminjaman', duration + ' Hari'],
                        ['Pembayaran', paymentNames[payment]],
                    ]">
                        <div class="flex justify-between gap-4">
                            <dt class="flex-none text-grey" x-text="label"></dt>
                            <dd class="text-right font-medium" x-text="value"></dd>
                        </div>
                    </template>
                    <div class="flex justify-between gap-4 border-t border-gray-100 pt-3">
                        <dt class="font-semibold">Total Harga</dt>
                        <dd class="font-semibold text-primary" x-text="rupiah(total)"></dd>
                    </div>
                </dl>
                @if ($errorStep === 4 && $errors->any())
                    <p class="error">{{ $errors->first() }}</p>
                @endif
            </div>

            <p x-show="stepError" x-cloak x-text="stepError" class="error mt-3"></p>

            <div class="mt-6 flex gap-3">
                <button type="button" x-show="step > 1" x-cloak @click="step--" class="flex-1 rounded-xl border border-gray-200 py-3 text-sm font-medium">Kembali</button>
                <button type="button" x-show="step < 4" @click="next()" class="btn-primary flex-1">Lanjut</button>
                <button type="submit" x-show="step === 4" x-cloak class="btn-primary flex-1">Rental Now</button>
            </div>
            <a href="{{ route('cars.show', $car) }}" x-show="step === 4" x-cloak class="mt-3 block text-center text-sm font-medium text-red-500">Batalkan</a>
        </section>
        </div>
    </form>
@endsection
