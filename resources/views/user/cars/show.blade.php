@extends('layouts.app')

@section('title', $car->name)

@section('content')
    @php
        $photos = $car->photoUrls();
        $specs = array_filter([
            $car->powerLabel() ? ['gauge', 'Tenaga', \Illuminate\Support\Str::before($car->powerLabel(), ' (')] : null,
            $car->accelerationLabel() ? ['clock', '0-100 km/j', $car->accelerationLabel()] : null,
            ['seat', 'Kursi', $car->seats.' penumpang'],
            ['fuel', 'BBM', $car->fuelLabel()],
            ['transmission', 'Transmisi', $car->transmission === 'Matic' ? 'Automatic' : 'Manual'],
            ['car', 'Jenis', $car->category->name],
        ]);
    @endphp

    <div class="container-page pb-28 lg:px-8 lg:pb-0 lg:pt-8">
        {{-- Bar atas: kembali, judul, simpan --}}
        <div class="flex items-center justify-between px-5 pb-2 pt-[max(1rem,env(safe-area-inset-top))] lg:px-0 lg:pt-0">
            <a href="{{ url()->previous() === url()->current() ? route('home') : url()->previous() }}" class="flex h-10 w-10 items-center justify-center rounded-full border border-gray-100 bg-white transition hover:bg-neutral" aria-label="Kembali">
                <x-icon name="chevron-left" class="h-5 w-5"/>
            </a>
            <h1 class="text-base font-semibold">Detail Mobil</h1>
            <x-favorite-button :car="$car" :saved="$isFavorite" class="h-10 w-10 rounded-full border border-gray-100"/>
        </div>

        <div class="lg:mt-4 lg:grid lg:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)] lg:items-start lg:gap-10">
            {{-- Galeri: foto besar + thumbnail --}}
            <div x-data="{ i: 0 }" class="px-5 lg:px-0">
                <div class="relative overflow-hidden rounded-3xl bg-neutral">
                    @foreach ($photos as $index => $photo)
                        <img src="{{ $photo }}" alt="{{ $car->name }}" x-show="i === {{ $index }}" @if ($index) x-cloak @endif
                             class="h-60 w-full object-cover lg:h-[420px]">
                    @endforeach
                </div>
                @if (count($photos) > 1)
                    <div class="mt-3 flex gap-2 overflow-x-auto">
                        @foreach ($photos as $index => $photo)
                            <button type="button" @click="i = {{ $index }}" aria-label="Foto {{ $index + 1 }}"
                                    class="h-14 w-20 flex-none overflow-hidden rounded-xl border-2 transition lg:h-20 lg:w-28"
                                    :class="i === {{ $index }} ? 'border-primary' : 'border-transparent opacity-70 hover:opacity-100'">
                                <img src="{{ $photo }}" alt="" class="h-full w-full object-cover" loading="lazy">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Info, tab, harga --}}
            <div x-data="{ tab: 'spec' }" class="mt-5 px-5 lg:mt-0 lg:rounded-3xl lg:bg-white lg:p-7 lg:shadow-card">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-semibold lg:text-2xl">{{ $car->name }}</h2>
                        <p class="mt-0.5 text-xs text-grey">{{ $car->condition_note }}</p>
                    </div>
                    <span class="flex flex-none items-center gap-1 text-sm font-semibold">
                        <x-icon name="star" class="h-4 w-4 text-amber-400"/> {{ $car->ratingLabel() }}
                    </span>
                </div>

                <div class="mt-5 grid grid-cols-3 border-b border-gray-100 text-center text-sm">
                    @foreach (['about' => 'Tentang', 'spec' => 'Spesifikasi', 'review' => 'Ulasan'] as $key => $label)
                        <button type="button" @click="tab = '{{ $key }}'" class="-mb-px border-b-2 pb-2.5 font-medium transition"
                                :class="tab === '{{ $key }}' ? 'border-primary text-primary' : 'border-transparent text-grey hover:text-ink'">{{ $label }}</button>
                    @endforeach
                </div>

                <div class="pt-5">
                    <div x-show="tab === 'about'" x-cloak>
                        <p class="text-sm leading-relaxed text-ink/80">{{ $car->description }}</p>
                    </div>

                    <div x-show="tab === 'spec'">
                        <div class="grid grid-cols-3 gap-2.5">
                            @foreach ($specs as [$icon, $label, $value])
                                <div class="rounded-2xl bg-neutral p-3">
                                    <x-icon :name="$icon" class="h-5 w-5 text-primary"/>
                                    <p class="mt-2 text-xs font-medium">{{ $label }}</p>
                                    <p class="truncate text-[11px] text-grey">{{ $value }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div x-show="tab === 'review'" x-cloak class="space-y-3">
                        @forelse ($reviews as $review)
                            <div class="rounded-2xl bg-neutral p-3">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium">{{ $review->user->name }}</p>
                                    <p class="flex gap-0.5">
                                        @for ($s = 1; $s <= 5; $s++)
                                            <x-icon name="star" class="h-3.5 w-3.5 {{ $s <= $review->rating ? 'text-amber-400' : 'text-gray-300' }}"/>
                                        @endfor
                                    </p>
                                </div>
                                @if ($review->review) <p class="mt-1 text-xs text-grey">{{ $review->review }}</p> @endif
                            </div>
                        @empty
                            <p class="py-6 text-center text-sm text-grey">Belum ada ulasan.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Lokasi pengambilan & kontak CS --}}
                <div class="mt-5">
                    <div class="flex items-center gap-3 border-b border-dashed border-gray-200 pb-3">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-primary-100"><span class="h-2 w-2 rounded-full bg-primary"></span></span>
                        <p class="flex-1 text-sm">{{ $pickupLocation ?? 'Pilih lokasi di Home' }}</p>
                        <span class="text-[11px] text-grey">Lokasi ambil</span>
                    </div>
                    <div class="flex items-center gap-3 pt-3">
                        <x-icon name="map-pin" class="h-5 w-5 text-grey"/>
                        <p class="flex-1 text-sm text-grey">Tanya ketersediaan ke CS</p>
                        <a href="{{ route('cars.call', $car) }}" class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-50 text-primary" aria-label="Telpon CS"><x-icon name="phone" class="h-4 w-4"/></a>
                        <a href="{{ route('chat', ['text' => "Halo, apakah {$car->name} tersedia untuk disewa?"]) }}" class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-50 text-primary" aria-label="Chat CS"><x-icon name="chat-o" class="h-4 w-4"/></a>
                    </div>
                </div>

                {{-- Harga + Book Now (desktop; di ponsel memakai bar bawah) --}}
                <div class="mt-6 hidden items-center justify-between border-t border-gray-100 pt-5 lg:flex">
                    <div>
                        <p class="text-xs text-grey">Harga</p>
                        <p class="text-xl font-semibold">{{ rupiah($car->price_per_day, false) }}<span class="text-sm font-normal text-grey">/hari</span></p>
                    </div>
                    <a href="{{ route('bookings.create', $car) }}" class="btn-primary rounded-full px-8">Book Now</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Bar harga + Book Now di ponsel --}}
    <div class="fixed bottom-0 left-1/2 z-30 flex w-full max-w-[430px] -translate-x-1/2 items-center justify-between border-t border-gray-100 bg-white px-5 pb-[max(1rem,env(safe-area-inset-bottom))] pt-3 lg:hidden">
        <div>
            <p class="text-[11px] text-grey">Harga</p>
            <p class="text-lg font-semibold">{{ rupiah($car->price_per_day, false) }}<span class="text-xs font-normal text-grey">/hari</span></p>
        </div>
        <a href="{{ route('bookings.create', $car) }}" class="btn-primary rounded-full px-8 py-3">Book Now</a>
    </div>
@endsection
