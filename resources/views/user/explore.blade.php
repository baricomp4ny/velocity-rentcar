@extends('layouts.app')

@section('title', 'Explore')
@section('bottom-nav', 'explore')

@section('content')
    <x-app-header :filters="$filters" :action="route('explore')" :hidden="['brand' => $brand?->id]"/>

    {{-- Pilih merek: Toyota, Honda, Hyundai, Suzuki --}}
    <div class="container-page lg:mt-8">
    <nav class="-mt-2 flex justify-between gap-2 overflow-x-auto rounded-b-2xl px-6 pb-4 pt-6 shadow-[0_6px_12px_-8px_rgba(0,125,252,.35)] lg:mt-0 lg:justify-center lg:gap-14 lg:rounded-2xl lg:bg-white lg:py-6 lg:shadow-card">
        @foreach ($brands as $item)
            @php $active = $brand && $brand->id === $item->id; @endphp
            <a href="{{ route('explore', ['brand' => $item->id] + array_filter(['q' => $filters['q']])) }}" class="flex flex-none flex-col items-center gap-2">
                <span class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-full bg-neutral {{ $active ? 'ring-2 ring-primary' : '' }}">
                    @if ($item->logoUrl())
                        <img src="{{ $item->logoUrl() }}" alt="{{ $item->name }}" class="h-full w-full object-contain p-3.5">
                    @else
                        <span class="text-lg font-bold text-ink">{{ mb_substr($item->name, 0, 1) }}</span>
                    @endif
                </span>
                <span class="rounded-full px-2 py-0.5 text-sm font-semibold {{ $active ? 'bg-primary text-white' : 'text-primary' }}">{{ $item->name }}</span>
            </a>
        @endforeach
    </nav>
    </div>

    <section class="container-page px-6 pt-6 lg:px-8 lg:pt-10">
        <h2 class="mb-4 text-xl font-semibold text-primary lg:mb-6 lg:text-2xl">{{ $brand?->name ?? 'Semua Mobil' }}</h2>

        <div class="grid gap-4 lg:grid-cols-2 lg:gap-6">
            @forelse ($cars as $car)
                <x-car-row :car="$car"/>
            @empty
                <div class="rounded-2xl bg-neutral px-6 py-12 text-center lg:col-span-2 lg:bg-white">
                    <x-icon name="car" class="mx-auto h-10 w-10 text-grey"/>
                    <p class="mt-3 font-medium">Belum ada mobil</p>
                    <p class="mt-1 text-sm text-grey">Tidak ada mobil {{ $brand?->name }} yang cocok dengan pencarianmu.</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
