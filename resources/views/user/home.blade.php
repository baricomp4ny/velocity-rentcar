@extends('layouts.app')

@section('title', 'Home')
@section('bottom-nav', 'home')

@section('content')
    <x-app-header :filters="$filters" :action="route('home')"/>

    @if ($featured)
        {{-- Poster menyambung di bawah lengkung header biru (ponsel) --}}
        <div class="container-page -mt-6 lg:mt-8">
            <div class="pt-6 lg:pt-0">
                <x-featured-car :car="$featured"/>
            </div>
        </div>
    @endif

    <section class="container-page px-5 pt-7 lg:px-8 lg:pt-12">
        <div class="mb-4 flex items-center justify-between lg:mb-6">
            <h2 class="text-lg font-semibold text-primary lg:text-2xl">
                {{ $searching ? 'Hasil Pencarian' : 'Populer Cars' }}
                @if ($searching) <span class="text-sm font-normal text-grey">({{ $cars->count() }})</span> @endif
            </h2>
            <a href="{{ route('explore') }}" class="rounded-full border border-primary px-3 py-1 text-xs font-medium text-primary transition hover:bg-primary-50 lg:px-4 lg:py-2 lg:text-sm">Cari Merek</a>
        </div>

        <div class="grid gap-4 lg:grid-cols-3 lg:gap-6">
            @forelse ($cars as $car)
                <x-car-card :car="$car" :saved="in_array($car->id, $favoriteIds)"/>
            @empty
                <div class="rounded-2xl bg-neutral px-6 py-12 text-center lg:col-span-3 lg:bg-white">
                    <x-icon name="search" class="mx-auto h-10 w-10 text-grey"/>
                    <p class="mt-3 font-medium">Mobil tidak ditemukan</p>
                    <p class="mt-1 text-sm text-grey">Coba kata kunci atau filter lain.</p>
                    <a href="{{ route('home') }}" class="btn-outline mt-4">Reset pencarian</a>
                </div>
            @endforelse
        </div>
    </section>
@endsection
