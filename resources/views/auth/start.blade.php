@extends('layouts.app')

@section('title', 'Selamat Datang')

@section('content')
    {{-- Halaman pembuka gelap: mobil terpotong setengah di tepi kiri agar terlihat elegan. --}}
    <div class="relative flex min-h-screen flex-col overflow-hidden bg-[#141414] text-white lg:grid lg:grid-cols-2 lg:items-center">
        <div class="relative z-10 flex items-center gap-3 px-7 pt-[max(2rem,env(safe-area-inset-top))] lg:absolute lg:left-12 lg:top-10 lg:px-0 lg:pt-0">
            <x-logo class="h-12 w-auto"/>
            <span class="text-sm font-semibold tracking-wide">{{ setting('store_name') }}</span>
        </div>

        {{-- Foto mobil: dibalik agar menghadap kanan, lalu "keluar" dari tepi kiri layar --}}
        <div class="relative mt-12 h-80 lg:mt-0 lg:h-[560px]">
            <div class="absolute inset-y-0 -left-[30%] right-6 overflow-hidden rounded-r-[48px] shadow-2xl shadow-black/60 lg:-left-[20%] lg:right-0">
                <img src="{{ media_url(setting('start_image')) }}" alt="" class="h-full w-full -scale-x-100 object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-[#141414]/70 via-transparent to-transparent"></div>
            </div>
        </div>

        <div class="relative z-10 mt-auto px-7 pb-[max(2.5rem,env(safe-area-inset-bottom))] pt-10 lg:mt-0 lg:px-16 lg:pb-0 lg:pt-0">
            <h1 class="text-[34px] font-semibold leading-tight lg:text-6xl">
                {{ setting('headline_prefix') }} <span class="text-primary-100">{{ setting('headline_highlight') }}</span><br>{{ setting('headline_suffix') }}
            </h1>
            <p class="mt-4 max-w-sm text-sm leading-relaxed text-white/60 lg:text-lg">{{ setting('start_subtitle') }}</p>

            <a href="{{ route('login') }}"
               class="group mt-10 flex h-14 w-full items-center justify-between rounded-2xl bg-white pl-8 pr-6 text-base font-semibold text-ink transition hover:bg-white/90 lg:max-w-sm">
                <span class="flex-1 text-center">Let’s Go!</span>
                <x-icon name="chevrons-right" class="h-6 w-6 transition group-hover:translate-x-1"/>
            </a>

            <p class="mt-5 text-center text-sm text-white/60 lg:max-w-sm">
                Belum punya akun? <a href="{{ route('register') }}" class="font-medium text-white underline">Buat Akun</a>
            </p>
        </div>
    </div>
@endsection
