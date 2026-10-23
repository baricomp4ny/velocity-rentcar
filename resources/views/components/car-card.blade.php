@props(['car', 'saved' => false])

{{-- Kartu "Populer Cars" di halaman Home --}}
<a href="{{ route('cars.show', $car) }}" class="block rounded-2xl border border-gray-100 bg-white p-3 shadow-card transition hover:-translate-y-0.5">
    <div class="relative flex h-40 items-center justify-center overflow-hidden rounded-xl bg-neutral lg:h-48">
        <img src="{{ $car->imageUrl() }}" alt="{{ $car->name }}" class="h-full w-full object-cover" loading="lazy">
        <span class="absolute left-3 top-3 flex items-center gap-1 rounded-md bg-primary-50 px-2 py-0.5 text-xs font-medium text-ink">
            <x-icon name="star" class="h-3.5 w-3.5 text-amber-400"/> {{ $car->ratingLabel() }}
        </span>
        <x-favorite-button :car="$car" :saved="$saved" class="absolute right-3 top-3"/>
    </div>

    <div class="mt-3 flex items-end justify-between gap-3 px-1">
        <div class="min-w-0 flex-1">
            <span class="chip">{{ $car->category->name }}</span>
            <h3 class="mt-1.5 line-clamp-2 text-base font-semibold leading-snug">{{ $car->name }}</h3>
        </div>
        <p class="flex-none whitespace-nowrap pb-0.5 text-xs font-semibold text-primary">{{ rupiah($car->price_per_day) }} <span class="font-normal text-ink">/hr</span></p>
    </div>

    <div class="mt-3 grid grid-cols-3 border-t border-gray-100 px-1 pt-3 text-sm">
        <span class="flex items-center gap-1.5"><x-icon name="transmission" class="h-5 w-5 text-primary"/>{{ $car->transmission }}</span>
        <span class="flex items-center justify-center gap-1.5"><x-icon name="fuel" class="h-5 w-5 text-primary"/>{{ $car->fuel }}</span>
        <span class="flex items-center justify-end gap-1.5"><x-icon name="seat" class="h-5 w-5 text-primary"/>{{ $car->seats }} seat</span>
    </div>
</a>
