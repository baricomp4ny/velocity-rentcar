@extends('layouts.app')

@section('title', 'Saved')
@section('bottom-nav', 'saved')

@section('content')
    <div class="container-page px-5 pt-[max(1.5rem,env(safe-area-inset-top))] lg:px-8 lg:pt-10">
        <h1 class="text-xl font-semibold lg:text-3xl">Saved</h1>
        <p class="mt-1 text-sm text-grey">Mobil yang kamu simpan untuk disewa nanti</p>

        <section class="mt-5 grid gap-4 lg:mt-8 lg:grid-cols-3 lg:gap-6">
            @forelse ($cars as $car)
                <x-car-card :car="$car" :saved="true"/>
            @empty
                <div class="rounded-3xl bg-neutral px-6 py-14 text-center lg:col-span-3">
                    <x-icon name="heart" class="mx-auto h-10 w-10 text-grey"/>
                    <p class="mt-3 font-medium">Belum ada mobil tersimpan</p>
                    <p class="mt-1 text-sm text-grey">Tekan ikon hati pada mobil untuk menyimpannya di sini.</p>
                    <a href="{{ route('explore') }}" class="btn-primary mt-5 rounded-full text-sm">Explore Mobil</a>
                </div>
            @endforelse
        </section>
    </div>
@endsection
