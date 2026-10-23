@extends('layouts.app')

@section('title', $car->name)

@section('content')
    @php $photos = $car->photoUrls(); @endphp

    {{-- Ponsel: susunan vertikal seperti prototipe. Desktop: foto & deskripsi di kiri,
         info + tombol Book Now di kanan. --}}
    <div class="container-page lg:grid lg:grid-cols-[minmax(0,1.45fr)_minmax(0,1fr)] lg:gap-10 lg:py-10">

        {{-- Foto mobil: bisa digeser/diklik panah. Jika admin mengunggah foto
             berurutan dari berbagai sudut, geseran ini berfungsi seperti tampilan 360°. --}}
        <div class="relative bg-neutral lg:col-start-1 lg:row-start-1 lg:overflow-hidden lg:rounded-2xl lg:shadow-card" x-data="{
                i: 0, n: {{ count($photos) }}, startX: null,
                go(d) { this.i = (this.i + d + this.n) % this.n },
                down(e) { this.startX = e.clientX },
                move(e) { if (this.startX === null) return; const dx = e.clientX - this.startX; if (Math.abs(dx) > 40) { this.go(dx < 0 ? 1 : -1); this.startX = e.clientX } },
                up() { this.startX = null }
            }">
            <div class="h-2 bg-primary lg:hidden"></div>
            <div class="relative h-[260px] touch-pan-y select-none overflow-hidden lg:h-[440px]"
                 @pointerdown="down($event)" @pointermove="move($event)" @pointerup="up()" @pointerleave="up()">
                @foreach ($photos as $index => $photo)
                    <img src="{{ $photo }}" alt="{{ $car->name }}" draggable="false"
                         x-show="i === {{ $index }}" @if ($index) x-cloak @endif
                         class="absolute inset-0 h-full w-full object-cover">
                @endforeach
            </div>

            <a href="{{ url()->previous() === url()->current() ? route('home') : url()->previous() }}" class="icon-btn absolute left-4 top-6 h-11 w-11" aria-label="Kembali">
                <x-icon name="chevron-left" class="h-5 w-5"/>
            </a>
            <x-favorite-button :car="$car" :saved="$isFavorite" class="absolute right-4 top-6 h-11 w-11 rounded-xl"/>

            @if (count($photos) > 1)
                <button type="button" @click="go(-1)" class="absolute left-2 top-1/2 flex h-8 w-8 items-center justify-center rounded-full bg-white/70 text-ink lg:left-4 lg:h-10 lg:w-10" aria-label="Foto sebelumnya">
                    <x-icon name="chevron-left" class="h-4 w-4"/>
                </button>
                <button type="button" @click="go(1)" class="absolute right-2 top-1/2 flex h-8 w-8 items-center justify-center rounded-full bg-white/70 text-ink lg:right-4 lg:h-10 lg:w-10" aria-label="Foto berikutnya">
                    <x-icon name="chevron-right" class="h-4 w-4"/>
                </button>
                <div class="absolute bottom-3 left-1/2 flex -translate-x-1/2 gap-1.5">
                    @foreach ($photos as $index => $photo)
                        <span class="h-1.5 rounded-full transition-all" :class="i === {{ $index }} ? 'w-5 bg-primary' : 'w-1.5 bg-white'"></span>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Info utama, kontak CS, spesifikasi, harga --}}
        <aside class="px-6 pt-6 lg:col-start-2 lg:row-span-2 lg:row-start-1 lg:p-0">
            <div class="lg:sticky lg:top-24 lg:rounded-2xl lg:bg-white lg:p-6 lg:shadow-card">
                <div class="flex items-start justify-between gap-3">
                    <h1 class="text-xl font-semibold lg:text-2xl">{{ $car->name }}</h1>
                    <span class="chip mt-1">{{ $car->category->name }}</span>
                </div>
                <div class="mt-1.5 flex items-center gap-2 text-sm">
                    <x-icon name="star" class="h-4 w-4 text-amber-400"/>
                    <span>{{ $car->ratingLabel() }}</span>
                    <span class="ml-2 text-xs text-grey">{{ $car->condition_note }}</span>
                </div>

                <h2 class="mt-6 text-base font-semibold">Hubungi Penyewa</h2>
                <div class="mt-3 flex items-center gap-3 rounded-full bg-primary-600 px-4 py-3 text-white">
                    <span class="flex h-11 w-11 flex-none items-center justify-center rounded-full bg-primary text-sm font-semibold">CS</span>
                    <div class="flex-1">
                        <p class="text-sm font-semibold">Customer Service</p>
                        <p class="text-[11px] text-white/80">Tanyakan ketersediaan mobil</p>
                    </div>
                    <a href="{{ route('cars.call', $car) }}" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/25 transition hover:bg-white/35" aria-label="Telpon Customer Service">
                        <x-icon name="phone" class="h-5 w-5"/>
                    </a>
                    <a href="{{ route('chat', ['text' => "Halo, apakah {$car->name} tersedia untuk disewa?"]) }}" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/25 transition hover:bg-white/35" aria-label="Chat Customer Service">
                        <x-icon name="chat" class="h-5 w-5"/>
                    </a>
                </div>

                <h2 class="mt-6 text-base font-semibold">Specs</h2>
                <div class="mt-3 grid grid-cols-3 gap-2">
                    @foreach (array_filter([
                        ['seat', 'Kursi', $car->seats.' Penumpang'],
                        ['fuel', 'BBM', $car->fuelLabel()],
                        ['transmission', 'Transmisi', $car->transmission === 'Matic' ? 'Automatic' : 'Manual'],
                        $car->powerLabel() ? ['chart', 'Power', $car->powerLabel()] : null,
                        $car->accelerationLabel() ? ['clock', '0-100 km/j', $car->accelerationLabel()] : null,
                    ]) as [$icon, $label, $value])
                        <div class="rounded-lg border border-primary p-2.5">
                            <p class="flex items-center gap-1.5 text-xs font-semibold"><x-icon :name="$icon" class="h-4 w-4 text-primary"/>{{ $label }}</p>
                            <p class="mt-1 text-[11px] text-ink/80">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Harga + Book Now versi desktop (di ponsel memakai bar bawah) --}}
                <div class="mt-6 hidden items-center justify-between border-t border-gray-100 pt-5 lg:flex">
                    <div>
                        <p class="text-sm text-grey">Harga / hari</p>
                        <p class="text-xl font-semibold">{{ rupiah($car->price_per_day) }}</p>
                    </div>
                    <a href="{{ route('bookings.create', $car) }}" class="btn-primary rounded-2xl px-8 py-3.5 text-base font-semibold">Book Now</a>
                </div>
            </div>
        </aside>

        {{-- Deskripsi & ulasan --}}
        <div class="px-6 pb-32 lg:col-start-1 lg:row-start-2 lg:p-0">
            <h2 class="mt-6 text-base font-semibold lg:mt-0">Deskripsi</h2>
            <div class="mt-3 rounded-lg border border-gray-200 px-4 py-3 text-sm leading-relaxed text-ink/80 lg:border-0 lg:bg-white lg:p-5 lg:shadow-card">
                {{ $car->description }}
            </div>

            @if ($reviews->isNotEmpty())
                <h2 class="mt-6 text-base font-semibold">Ulasan Penyewa</h2>
                <div class="mt-3 grid gap-3 lg:grid-cols-2">
                    @foreach ($reviews as $review)
                        <div class="rounded-xl bg-neutral p-3 lg:bg-white lg:p-4 lg:shadow-card">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium">{{ $review->user->name }}</p>
                                <p class="flex items-center gap-0.5">
                                    @for ($s = 1; $s <= 5; $s++)
                                        <x-icon name="star" class="h-3.5 w-3.5 {{ $s <= $review->rating ? 'text-amber-400' : 'text-gray-300' }}"/>
                                    @endfor
                                </p>
                            </div>
                            @if ($review->review)
                                <p class="mt-1 text-xs text-grey">{{ $review->review }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Bar harga + Book Now di ponsel --}}
    <div class="fixed bottom-0 left-1/2 z-30 flex w-full max-w-[430px] -translate-x-1/2 items-center justify-between rounded-t-3xl bg-white px-6 pb-[max(1rem,env(safe-area-inset-bottom))] pt-4 shadow-nav lg:hidden">
        <div>
            <p class="text-sm text-grey">Harga</p>
            <p class="text-base font-semibold">{{ rupiah($car->price_per_day) }}</p>
        </div>
        <a href="{{ route('bookings.create', $car) }}" class="btn-primary rounded-2xl px-7 py-3.5 text-base font-semibold">Book Now</a>
    </div>
@endsection
