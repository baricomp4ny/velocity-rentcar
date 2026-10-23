@extends('layouts.app')

@section('title', 'Selamat Datang')

@section('content')
    {{-- Ponsel: logo, foto mobil, lalu teks (sesuai prototipe). Desktop: teks di kiri, foto di kanan. --}}
    <div class="lg:flex lg:min-h-screen lg:items-center lg:bg-white">
        <div class="container-page flex min-h-screen flex-col px-6 pb-8 pt-12 lg:grid lg:min-h-0 lg:grid-cols-2 lg:items-center lg:gap-x-16 lg:px-8 lg:py-16">
            <x-logo class="mx-auto h-28 w-auto lg:col-start-1 lg:row-start-1 lg:mx-0 lg:h-24 lg:self-end"/>

            <div class="relative -mr-6 mt-10 lg:col-start-2 lg:row-span-2 lg:row-start-1 lg:mr-0 lg:mt-0">
                {{-- Label (mis. "07 Seats") dengan garis penunjuk seperti di prototipe --}}
                <div class="absolute left-12 top-0 z-10 flex items-center gap-1.5 rounded-full border border-primary-100 bg-white px-3 py-1.5 text-sm font-medium shadow-card lg:left-16 lg:px-4 lg:py-2 lg:text-base">
                    <x-icon name="seat" class="h-4 w-4 text-primary"/>
                    {{ setting('start_badge') }}
                </div>
                <svg class="absolute left-2 top-4 h-20 w-12 text-primary lg:h-28 lg:w-16" viewBox="0 0 48 80" fill="none" aria-hidden="true">
                    <path d="M46 2H18C9 2 4 8 4 16v60" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                <img src="{{ media_url(setting('start_image')) }}" alt="" class="ml-auto mt-8 aspect-[4/3] w-[92%] rounded-l-3xl object-cover shadow-xl lg:mt-12 lg:rounded-3xl">
            </div>

            <div class="mt-auto pt-8 text-center lg:col-start-1 lg:row-start-2 lg:mt-6 lg:self-start lg:pt-0 lg:text-left">
                <h1 class="text-[26px] font-medium leading-snug lg:text-5xl lg:font-semibold lg:leading-tight">
                    {{ setting('headline_prefix') }} <span class="text-primary">{{ setting('headline_highlight') }}</span><br>{{ setting('headline_suffix') }}
                </h1>
                <p class="mt-4 text-sm text-grey lg:text-lg">{{ setting('start_subtitle') }}</p>

                <a href="{{ route('login') }}" class="btn-primary mt-7 w-full rounded-2xl py-3.5 text-base lg:w-auto lg:px-12">Let’s Get Started</a>

                <p class="mt-5 text-sm font-medium">
                    Belum punya akun? <a href="{{ route('register') }}" class="text-primary underline">Buat Akun</a>
                </p>
            </div>
        </div>
    </div>
@endsection
