@props(['car'])

{{-- Kartu daftar mobil di halaman Explore / merek --}}
<div class="flex gap-3 rounded-2xl border border-primary-100 bg-neutral p-3 lg:gap-5 lg:border-gray-100 lg:bg-white lg:p-4 lg:shadow-card">
    <a href="{{ route('cars.show', $car) }}" class="h-[92px] w-[106px] flex-none overflow-hidden rounded-xl bg-white">
        <img src="{{ $car->imageUrl() }}" alt="{{ $car->name }}" class="h-full w-full object-cover" loading="lazy">
    </a>
    <div class="min-w-0 flex-1">
        <div class="flex items-start justify-between gap-2">
            <h3 class="line-clamp-2 text-[13px] font-semibold leading-snug">{{ $car->name }}</h3>
            <span class="chip flex-none">{{ $car->category->name }}</span>
        </div>
        <p class="text-[10px] italic text-ink">Mulai dari IDR {{ number_format($car->price_per_day, 0, ',', '.') }} / hari</p>
        <div class="mt-2 grid grid-cols-2 gap-x-2 gap-y-1.5 text-xs">
            <span class="flex items-center gap-1.5"><x-icon name="transmission" class="h-4 w-4 text-primary"/>{{ $car->transmission }}</span>
            <span class="flex items-center gap-1.5"><x-icon name="fuel" class="h-4 w-4 text-primary"/>{{ $car->fuel }}</span>
            <span class="flex items-center gap-1.5"><x-icon name="seat" class="h-4 w-4 text-primary"/>{{ $car->seats }} Kursi</span>
        </div>
        <div class="mt-1 text-right">
            <a href="{{ route('cars.show', $car) }}" class="btn-outline !px-2.5 !py-1 !text-[10px]">Lihat Detail</a>
        </div>
    </div>
</div>
