@extends('layouts.app')

@section('title', 'Saved')
@section('bottom-nav', 'saved')

@section('content')
    <header class="rounded-b-3xl bg-primary px-6 pb-6 pt-8 text-white lg:rounded-none lg:px-0 lg:py-10">
        <div class="container-page flex items-start justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold lg:text-3xl">Saved</h1>
            <p class="mt-1 text-sm text-white/80">Mobil yang kamu simpan untuk disewa nanti</p>
        </div>
        <x-help-button class="bg-white/20 text-white hover:bg-white/30 lg:hidden"/>
        </div>
    </header>

    <section class="container-page grid gap-4 px-6 pt-6 lg:grid-cols-3 lg:gap-6 lg:px-8 lg:pt-10">
        @forelse ($cars as $car)
            <x-car-card :car="$car" :saved="true"/>
        @empty
            <div class="rounded-2xl bg-neutral px-6 py-14 text-center lg:col-span-3 lg:bg-white">
                <x-icon name="bookmark" class="mx-auto h-10 w-10 text-grey"/>
                <p class="mt-3 font-medium">Belum ada mobil tersimpan</p>
                <p class="mt-1 text-sm text-grey">Tekan ikon hati pada mobil untuk menyimpannya di sini.</p>
                <a href="{{ route('explore') }}" class="btn-primary mt-5 text-sm">Explore Mobil</a>
            </div>
        @endforelse
    </section>
@endsection
