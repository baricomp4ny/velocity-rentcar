@props(['car'])

@php
    // Tiga panel miring seperti poster referensi: depan, interior, belakang.
    // Jika fotonya kurang dari 3, foto utama dipotong dari sisi kiri / kanan.
    $photos = $car->photoUrls();
    $panels = [
        [$photos[0], 'left'],
        [$photos[1] ?? $photos[0], 'center'],
        [$photos[2] ?? $photos[0], $photos[2] ?? false ? 'center' : 'right'],
    ];
    $brand = $car->brand->name;
    // Mobil dengan kode model (mis. BMW M4 G82) ditulis singkat seperti poster: "M4".
    $model = \Illuminate\Support\Str::words($car->modelName(), $car->model_code ? 1 : 2, '');
@endphp

{{-- Poster mobil unggulan (gaya poster otomotif) --}}
<section class="relative isolate overflow-hidden bg-[#EFEEEA] px-5 pb-7 pt-6 lg:rounded-3xl lg:px-14 lg:pb-12 lg:pt-10 lg:shadow-card">
    {{-- Coretan grafiti samar di latar --}}
    <p aria-hidden="true" class="pointer-events-none absolute -left-6 top-24 -z-10 -rotate-12 select-none whitespace-nowrap font-marker text-[96px] leading-none text-ink/[.07] lg:-left-10 lg:top-28 lg:text-[220px]">{{ $brand }} {{ $model }}</p>
    <p aria-hidden="true" class="pointer-events-none absolute -right-8 top-[46%] -z-10 rotate-6 select-none whitespace-nowrap font-marker text-[72px] leading-none text-ink/[.06] lg:text-[180px]">{{ $car->model_code ?? $car->category->name }}</p>

    <div class="grid grid-cols-3 text-center text-[13px] font-extrabold uppercase tracking-wide lg:text-xl">
        <span class="text-left">{{ $brand }}</span>
        <span class="truncate px-1">{{ $model }}</span>
        <span class="text-right">{{ $car->model_code ?? $car->year ?? $car->category->name }}</span>
    </div>

    {{-- Tiga panel miring --}}
    <div class="mt-5 grid h-40 grid-cols-3 gap-1.5 lg:mt-8 lg:h-72 lg:gap-3">
        @foreach ($panels as $i => [$src, $position])
            <div class="overflow-hidden bg-ink shadow-lg {{ $i === 1 ? '' : 'mt-6 lg:mt-12' }}"
                 style="clip-path: polygon(14% 0, 100% 0, 86% 100%, 0 100%)">
                <img src="{{ $src }}" alt="" loading="lazy" class="h-full w-full object-cover" style="object-position: {{ $position }}">
            </div>
        @endforeach
    </div>

    {{-- Foto utama --}}
    <a href="{{ route('cars.show', $car) }}" class="relative mx-auto -mt-10 block w-[92%] overflow-hidden rounded-2xl shadow-2xl ring-4 ring-[#EFEEEA] lg:-mt-20 lg:w-[68%]">
        <img src="{{ $photos[0] }}" alt="{{ $car->name }}" class="h-52 w-full object-cover lg:h-[360px]">
        <span class="absolute left-3 top-3 flex items-center gap-1 rounded-md bg-white/90 px-2 py-0.5 text-xs font-medium">
            <x-icon name="star" class="h-3.5 w-3.5 text-amber-400"/> {{ $car->ratingLabel() }}
        </span>
    </a>

    {{-- Judul gaya grafiti --}}
    <h2 class="mt-6 flex items-end justify-center gap-2 lg:mt-8">
        <span class="font-marker text-6xl leading-none text-ink lg:text-8xl">{{ $brand }}</span>
        <span class="mb-1 font-marker text-2xl leading-none text-primary lg:mb-3 lg:text-4xl">{{ $model }}</span>
    </h2>

    <div class="mt-6 grid gap-5 lg:mt-10 lg:grid-cols-[minmax(0,1fr)_280px] lg:items-end lg:gap-12">
        <div>
            <p class="text-[10.5px] uppercase leading-relaxed tracking-wide text-ink/80 lg:text-[13px]">
                {{ \Illuminate\Support\Str::limit($car->description, 300) }}
            </p>
            <div class="mt-3 w-24 border-t-2 border-dashed border-ink/70"></div>
        </div>

        {{-- Kotak spesifikasi bergaris putus-putus seperti poster --}}
        <div class="rounded-2xl border-2 border-dashed border-ink/60 p-4">
            <div class="grid grid-cols-2 gap-x-3 gap-y-3 lg:grid-cols-1">
                @if ($car->powerLabel())
                    <div>
                        <p class="text-[11px] text-grey">power</p>
                        <p class="text-base font-semibold uppercase leading-tight lg:text-lg">{{ $car->powerLabel() }}</p>
                    </div>
                @endif
                @if ($car->accelerationLabel())
                    <div>
                        <p class="text-[11px] text-grey">0-100 km/jam</p>
                        <p class="text-base font-semibold leading-tight lg:text-lg">{{ $car->accelerationLabel() }}</p>
                    </div>
                @endif
                <div>
                    <p class="text-[11px] text-grey">harga / hari</p>
                    <p class="text-base font-semibold leading-tight lg:text-lg">{{ rupiah($car->price_per_day, false) }}</p>
                </div>
                <div>
                    <p class="text-[11px] text-grey">spesifikasi</p>
                    <p class="text-sm font-semibold leading-tight">{{ $car->seats }} kursi · {{ $car->transmission }}</p>
                    <p class="text-[11px] text-grey">{{ $car->fuelLabel() }}</p>
                </div>
            </div>
            <div class="mt-4 flex gap-2">
                <a href="{{ route('bookings.create', $car) }}" class="btn-primary flex-1 py-2.5 text-sm">Book Now</a>
                <a href="{{ route('cars.show', $car) }}" class="inline-flex flex-1 items-center justify-center rounded-xl border border-ink/20 bg-white py-2.5 text-sm font-medium transition hover:bg-neutral">Detail</a>
            </div>
        </div>
    </div>
</section>
