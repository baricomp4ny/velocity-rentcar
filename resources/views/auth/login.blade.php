@extends('layouts.app')

@section('title', 'Sign in')

@section('content')
    <x-auth-shell :subtitle="setting('login_subtitle')">
    <form method="POST" action="{{ route('login') }}" class="px-8 pb-10 pt-5 lg:pb-0 lg:pt-8" x-data="{ show: false }">
        @csrf
        <h2 class="mb-5 text-lg font-semibold">Welcome</h2>

        <div class="space-y-3">
            <div>
                <input type="text" name="login" value="{{ old('login') }}" placeholder="Email atau Nomor Telepon"
                       class="input" required autofocus autocomplete="username">
                @error('login') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="relative">
                <input :type="show ? 'text' : 'password'" name="password" placeholder="Password"
                       class="input pr-12" required autocomplete="current-password">
                <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-ink/70" aria-label="Lihat password">
                    <x-icon name="eye" class="h-5 w-5" x-show="!show"/>
                    <x-icon name="eye-off" class="h-5 w-5" x-show="show" x-cloak/>
                </button>
                @error('password') <p class="error">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Selector (toggle) "Ingatkan Saya" --}}
        <label class="mt-3 flex cursor-pointer items-center gap-2 text-xs">
            <input type="checkbox" name="remember" value="1" class="peer sr-only">
            <span class="relative h-5 w-9 rounded-full bg-gray-200 transition after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition peer-checked:bg-primary peer-checked:after:translate-x-4"></span>
            Ingatkan Saya
        </label>

        <button type="submit" class="btn-primary mt-7 w-full py-3.5 text-lg font-semibold">Sign in</button>

        <a href="{{ route('google.redirect') }}" class="btn-dark mt-3 w-full py-3.5">
            <x-icon name="google" class="h-5 w-5"/> Or sign in with Google
        </a>

        <p class="mt-6 text-center text-sm font-medium">
            Belum punya akun? <a href="{{ route('register') }}" class="text-primary underline">Buat Akun</a>
        </p>
    </form>

    @if (config('services.demo_login.enabled'))
        {{-- Masuk cepat untuk presentasi / uji coba --}}
        <div class="mx-8 mb-10 rounded-2xl border-2 border-dashed border-primary-100 p-4 lg:mb-0 lg:mt-6">
            <p class="text-center text-sm font-semibold">Akun Demo</p>
            <p class="mt-0.5 text-center text-xs text-grey">Masuk langsung tanpa mengetik email & password</p>
            <div class="mt-3 grid grid-cols-2 gap-2">
                @foreach (['user' => ['Pengguna', 'user'], 'admin' => ['Admin', 'settings']] as $role => [$label, $icon])
                    <form method="POST" action="{{ route('demo.login', $role) }}">
                        @csrf
                        <button class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary-50 py-3 text-sm font-medium text-primary transition hover:bg-primary-100">
                            <x-icon :name="$icon" class="h-4 w-4"/> Demo {{ $label }}
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    @endif
    </x-auth-shell>
@endsection
