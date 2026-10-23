@props(['filters' => [], 'action', 'hidden' => []])

@php
    $user = Auth::user();
    $activeFilters = collect($filters)->except('q')->flatten()->filter()->count();
    $groups = [
        'transmission' => ['Transmisi', array_combine(\App\Models\Car::TRANSMISSIONS, \App\Models\Car::TRANSMISSIONS)],
        'fuel' => ['Bahan Bakar', array_combine(\App\Models\Car::FUELS, \App\Models\Car::FUELS)],
        'category' => ['Jenis Mobil', $categories->pluck('name', 'id')->all()],
    ];
@endphp

{{-- Ponsel: header biru (Location, notifikasi, Search, filter) sesuai prototipe.
     Desktop: bilah pencarian putih yang ringkas di bawah navigasi. --}}
<header x-data="{ filterOpen: false }" class="relative z-10 rounded-b-3xl bg-primary px-6 pb-6 pt-[max(2rem,env(safe-area-inset-top))] text-white lg:rounded-none lg:border-b lg:border-gray-100 lg:bg-white lg:px-0 lg:py-4 lg:text-ink">
    <div class="container-page lg:flex lg:items-center lg:gap-8">
        <div class="flex items-start justify-between">
            <form method="POST" action="{{ route('location.update') }}">
                @csrf @method('PUT')
                <p class="text-xs text-white/80 lg:text-grey">Location</p>
                <label class="relative mt-0.5 flex cursor-pointer items-center gap-1.5 text-base font-medium">
                    <x-icon name="pin" class="h-5 w-5 lg:text-primary"/>
                    <select name="location_id" onchange="this.form.submit()"
                            class="cursor-pointer appearance-none border-0 bg-transparent p-0 pr-6 text-base font-medium text-white focus:ring-0 lg:text-ink"
                            style="background-image:none">
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" class="text-ink" @selected($user->location_id === $location->id)>{{ $location->name }}</option>
                        @endforeach
                    </select>
                    <x-icon name="chevron-down" class="pointer-events-none absolute right-0 h-4 w-4"/>
                </label>
            </form>

            {{-- Di desktop, bantuan & notifikasi ada di navigasi atas --}}
            <div class="flex gap-2 lg:hidden">
                <x-help-button/>
                <a href="{{ route('notifications') }}" class="relative flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 transition hover:bg-white/30" aria-label="Notifikasi">
                    <x-icon name="bell" class="h-5 w-5"/>
                    @if ($hasUnreadNotifications)
                        <span class="absolute right-2 top-2 h-2.5 w-2.5 rounded-full border-2 border-primary bg-red-500"></span>
                    @endif
                </a>
            </div>
        </div>

        <form id="car-filter" method="GET" action="{{ $action }}" class="mt-5 flex gap-3 lg:mt-0 lg:flex-1">
            @foreach ($hidden as $name => $value)
                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
            @endforeach

            <label class="flex flex-1 items-center gap-3 rounded-xl bg-white px-4 text-ink lg:bg-neutral">
                <x-icon name="search" class="h-5 w-5 text-primary"/>
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search"
                       class="w-full border-0 bg-transparent px-0 py-3 text-sm placeholder:text-grey focus:ring-0">
            </label>

            <button type="submit" class="hidden rounded-xl bg-ink px-6 text-sm font-medium text-white transition hover:bg-ink/90 lg:block">Cari</button>

            <button type="button" @click="filterOpen = true" class="relative flex h-12 w-12 flex-none items-center justify-center rounded-xl bg-white text-primary lg:w-auto lg:gap-2 lg:bg-primary-50 lg:px-4" aria-label="Filter">
                <x-icon name="filter" class="h-6 w-6"/>
                <span class="hidden text-sm font-medium lg:inline">Filter</span>
                @if ($activeFilters)
                    <span class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-ink text-[10px] text-white">{{ $activeFilters }}</span>
                @endif
            </button>
        </form>
    </div>

    {{-- Filter berdasarkan kebutuhan: transmisi, bahan bakar, kapasitas, jenis.
         Dipindah ke <body> agar tampil di atas navigasi; input tetap milik form #car-filter. --}}
    <template x-teleport="body">
        <div x-show="filterOpen" x-cloak class="fixed inset-0 z-[60] flex items-end justify-center lg:items-center" @keydown.escape.window="filterOpen = false">
            <div x-show="filterOpen" x-transition.opacity @click="filterOpen = false" class="absolute inset-0 bg-ink/50"></div>
            <div x-show="filterOpen" x-transition:enter="transition duration-200" x-transition:enter-start="translate-y-full lg:translate-y-4 lg:opacity-0"
                 class="relative max-h-[85vh] w-full max-w-[430px] overflow-y-auto rounded-t-3xl bg-white p-6 text-ink lg:max-w-lg lg:rounded-3xl">
                <div class="mx-auto mb-4 h-1.5 w-12 rounded-full bg-gray-200 lg:hidden"></div>
                <div class="mb-5 flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Filter Mobil</h3>
                    <a href="{{ $action }}{{ $hidden ? '?'.http_build_query($hidden) : '' }}" class="text-sm text-primary">Reset</a>
                </div>

                @foreach ($groups as $field => [$title, $options])
                    <p class="mb-2 text-sm font-medium">{{ $title }}</p>
                    <div class="mb-5 flex flex-wrap gap-2">
                        @foreach ($options as $value => $option)
                            <label>
                                <input type="checkbox" form="car-filter" name="{{ $field }}[]" value="{{ $value }}" class="peer sr-only"
                                       @checked(in_array($value, $filters[$field] ?? []))>
                                <span class="chip-outline">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                @endforeach

                <p class="mb-2 text-sm font-medium">Kapasitas Penumpang</p>
                <div class="mb-6 flex flex-wrap gap-2">
                    @foreach (['' => 'Semua', 4 => '4+ kursi', 5 => '5+ kursi', 7 => '7+ kursi'] as $value => $label)
                        <label>
                            <input type="radio" form="car-filter" name="seats" value="{{ $value }}" class="peer sr-only"
                                   @checked((string) ($filters['seats'] ?? '') === (string) $value)>
                            <span class="chip-outline">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                <button type="submit" form="car-filter" class="btn-primary w-full">Terapkan Filter</button>
            </div>
        </div>
    </template>
</header>
