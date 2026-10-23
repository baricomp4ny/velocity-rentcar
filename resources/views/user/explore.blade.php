@extends('layouts.app')

@section('title', 'Explore')
@section('bottom-nav', 'explore')

@section('content')
    <x-app-header :filters="$filters" :action="route('explore')" :hidden="['brand' => $brand?->id]"/>

    <div class="container-page space-y-6 px-5 pt-2 lg:space-y-10 lg:px-8 lg:pt-8">
        <x-brand-row :brands="$brands" :active="$brand" :title="false"/>

        <section>
            <div class="mb-3 flex items-center justify-between lg:mb-5">
                <h2 class="text-base font-semibold lg:text-xl">{{ $brand?->name ?? 'Semua Mobil' }}</h2>
            </div>

            <div class="grid gap-4 lg:grid-cols-3 lg:gap-6">
                @forelse ($cars as $car)
                    <x-car-card :car="$car" :saved="in_array($car->id, $favoriteIds)"/>
                @empty
                    <div class="rounded-3xl bg-neutral px-6 py-12 text-center lg:col-span-3">
                        <x-icon name="car" class="mx-auto h-10 w-10 text-grey"/>
                        <p class="mt-3 font-medium">Belum ada mobil</p>
                        <p class="mt-1 text-sm text-grey">Tidak ada mobil {{ $brand?->name }} yang cocok dengan pencarianmu.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
