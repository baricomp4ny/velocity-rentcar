@props(['car'])

@php
    $model = \Illuminate\Support\Str::words($car->modelName(), $car->model_code ? 1 : 2, '');
    $highlight = $car->powerLabel() ? \Illuminate\Support\Str::before($car->powerLabel(), ' (') : $car->category->name;
@endphp

{{-- Banner biru mobil unggulan (dipilih admin di Pengaturan Toko) --}}
<a href="{{ route('cars.show', $car) }}"
   class="relative block h-48 overflow-hidden rounded-3xl bg-gradient-to-br from-primary to-[#3D9BFF] text-white shadow-lg shadow-primary/25 lg:h-72">
    {{-- Lingkaran dekoratif --}}
    <span class="absolute -right-10 -top-16 h-56 w-56 rounded-full bg-white/10 lg:h-96 lg:w-96"></span>
    <span class="absolute -bottom-20 right-24 h-44 w-44 rounded-full bg-white/10 lg:right-72"></span>

    <img src="{{ $car->imageUrl() }}" alt="{{ $car->name }}"
         class="absolute inset-y-0 right-0 h-full w-[55%] object-cover"
         style="-webkit-mask-image: linear-gradient(to right, transparent, #000 38%); mask-image: linear-gradient(to right, transparent, #000 38%)">

    <div class="relative z-10 flex h-full max-w-[60%] flex-col justify-center p-5 lg:max-w-[45%] lg:p-10">
        <p class="text-[11px] font-medium uppercase tracking-wider text-white/75 lg:text-sm">{{ $car->brand->name }} {{ $model }} · {{ $highlight }}</p>
        <p class="mt-1 text-[17px] font-semibold leading-snug lg:text-4xl">Rasakan Performa,<br>Nikmati Perjalanan</p>
        <p class="mt-1 text-xs text-white/80 lg:mt-3 lg:text-base">Mulai {{ rupiah($car->price_per_day, false) }}/hari</p>
        <span class="mt-3 inline-flex w-fit items-center gap-1.5 rounded-full bg-white/20 px-3 py-1.5 text-xs font-medium backdrop-blur lg:mt-6 lg:px-5 lg:py-2.5 lg:text-sm">
            <x-icon name="car" class="h-4 w-4"/> Sewa Sekarang
        </span>
    </div>
</a>
