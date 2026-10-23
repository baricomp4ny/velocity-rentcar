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

{{-- Header bersih: Location + notifikasi, lalu kolom pencarian dengan tombol filter di dalamnya. --}}
<header x-data="{ filterOpen: false }" class="relative z-10 bg-white px-5 pb-4 pt-[max(1.5rem,env(safe-area-inset-top))] lg:border-b lg:border-gray-100 lg:px-0 lg:py-4">
    <div class="container-page lg:flex lg:items-center lg:gap-10">
        <div class="flex items-center justify-between">
            <form method="POST" action="{{ route('location.update') }}">
                @csrf @method('PUT')
                <p class="text-sm font-medium text-ink">Location</p>
                <label class="relative mt-0.5 flex cursor-pointer items-center gap-1 text-xs font-medium text-primary">
                    <x-icon name="map-pin" class="h-3.5 w-3.5"/>
                    <select name="location_id" onchange="this.form.submit()"
                            class="cursor-pointer appearance-none border-0 bg-transparent p-0 pr-5 text-xs font-medium text-primary focus:ring-0"
                            style="background-image:none">
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" class="text-ink" @selected($user->location_id === $location->id)>{{ $location->name }}</option>
                        @endforeach
                    </select>
                    <x-icon name="chevron-down" class="pointer-events-none absolute right-0 h-3.5 w-3.5"/>
                </label>
            </form>

            {{-- Di desktop, bantuan & notifikasi ada di navigasi atas --}}
            <a href="{{ route('notifications') }}" class="relative flex h-10 w-10 items-center justify-center rounded-full bg-primary-50 text-primary transition hover:bg-primary-100 lg:hidden" aria-label="Notifikasi">
                <x-icon name="bell-o" class="h-5 w-5"/>
                @if ($hasUnreadNotifications)
                    <span class="absolute right-2.5 top-2.5 h-2 w-2 rounded-full bg-red-500 ring-2 ring-primary-50"></span>
                @endif
            </a>
        </div>

        <form id="car-filter" method="GET" action="{{ $action }}" class="mt-4 lg:mt-0 lg:max-w-xl lg:flex-1">
            @foreach ($hidden as $name => $value)
                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
            @endforeach

            <label class="flex h-12 items-center gap-3 rounded-full bg-neutral pl-4 pr-1.5 text-ink">
                <x-icon name="search" class="h-5 w-5 flex-none text-grey"/>
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari mobil impianmu"
                       class="w-full border-0 bg-transparent px-0 text-sm placeholder:text-grey focus:ring-0">
                <button type="button" @click="filterOpen = true" class="relative flex h-9 w-9 flex-none items-center justify-center rounded-full bg-primary text-white transition hover:bg-primary-600" aria-label="Filter">
                    <x-icon name="filter" class="h-4 w-4"/>
                    @if ($activeFilters)
                        <span class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-ink text-[9px] text-white">{{ $activeFilters }}</span>
                    @endif
                </button>
            </label>
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
