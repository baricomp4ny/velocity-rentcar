@extends('layouts.app')

@section('title', 'Home')
@section('bottom-nav', 'home')

@section('content')
    <x-app-header :filters="$filters" :action="route('home')"/>

    <div class="container-page space-y-6 px-5 pt-2 lg:space-y-10 lg:px-8 lg:pt-8">
        @if ($featured)
            <div data-poster>
                <x-featured-car :car="$featured"/>
            </div>
        @endif

        @unless ($searching)
            <div data-poster>
                <x-brand-row :brands="$brands"/>
            </div>
        @endunless

        <section>
            <div class="mb-3 flex items-center justify-between lg:mb-5">
                <h2 class="text-base font-semibold lg:text-xl">
                    {{ $searching ? 'Hasil Pencarian' : 'Mobil Populer' }}
                    @if ($searching) <span class="text-sm font-normal text-grey">({{ $cars->count() }})</span> @endif
                </h2>
                @if ($searching)
                    <a href="{{ route('home') }}" class="text-xs font-medium text-primary lg:text-sm">Reset</a>
                @endif
            </div>

            <div class="grid gap-4 lg:grid-cols-3 lg:gap-6">
                @forelse ($cars as $car)
                    <x-car-card :car="$car" :saved="in_array($car->id, $favoriteIds)"/>
                @empty
                    <div class="rounded-3xl bg-neutral px-6 py-12 text-center lg:col-span-3">
                        <x-icon name="search" class="mx-auto h-10 w-10 text-grey"/>
                        <p class="mt-3 font-medium">Mobil tidak ditemukan</p>
                        <p class="mt-1 text-sm text-grey">Coba kata kunci atau filter lain.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
