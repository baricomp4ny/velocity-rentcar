@props(['car', 'saved' => false])

{{-- Kartu mobil minimalis: foto, rating, simpan, nama, harga --}}
<a href="{{ route('cars.show', $car) }}" data-car="{{ json_encode($car->filterData()) }}"
   class="block rounded-3xl border border-gray-100 bg-white p-2.5 transition hover:shadow-card">
    <div class="relative h-40 overflow-hidden rounded-2xl bg-neutral lg:h-44">
        <img src="{{ $car->imageUrl() }}" alt="{{ $car->name }}" class="h-full w-full object-cover" loading="lazy">
        <span class="absolute left-2.5 top-2.5 flex items-center gap-1 rounded-full bg-white/90 px-2 py-0.5 text-[11px] font-medium backdrop-blur">
            <x-icon name="star" class="h-3 w-3 text-amber-400"/> {{ $car->ratingLabel() }}
        </span>
        <x-favorite-button :car="$car" :saved="$saved" class="absolute right-2.5 top-2.5 h-8 w-8 rounded-full"/>
    </div>

    <div class="flex items-start justify-between gap-3 px-1.5 pb-1 pt-3">
        <div class="min-w-0">
            <h3 class="truncate text-sm font-semibold">{{ $car->name }}</h3>
            <p class="mt-0.5 text-[11px] text-grey">{{ $car->category->name }} · {{ $car->transmission }} · {{ $car->seats }} kursi</p>
        </div>
        <p class="flex-none text-sm font-semibold text-primary">{{ rupiah($car->price_per_day, false) }}<span class="text-[11px] font-normal text-grey">/hari</span></p>
    </div>
</a>
