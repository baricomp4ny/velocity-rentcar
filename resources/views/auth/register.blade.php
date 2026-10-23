@extends('layouts.app')

@section('title', 'Buat Akun')

@section('content')
    <x-auth-shell :subtitle="setting('register_subtitle')">
    <form method="POST" action="{{ route('register') }}" class="px-8 pb-10 pt-5 lg:pb-0 lg:pt-8" x-data="{ show: false }">
        @csrf
        <h2 class="mb-5 text-lg font-semibold">Welcome</h2>

        <div class="space-y-3">
            <div>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama Lengkap" class="input" required autocomplete="name">
                @error('name') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" class="input" required autocomplete="email">
                @error('email') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="Nomor Telepon" class="input" required autocomplete="tel">
                @error('phone') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="relative">
                <input :type="show ? 'text' : 'password'" name="password" placeholder="Buat password" class="input pr-12" required autocomplete="new-password">
                <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-ink/70" aria-label="Lihat password">
                    <x-icon name="eye" class="h-5 w-5"/>
                </button>
            </div>
            <div class="relative">
                <input :type="show ? 'text' : 'password'" name="password_confirmation" placeholder="Konfirmasi password" class="input pr-12" required autocomplete="new-password">
                <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-ink/70" aria-label="Lihat password">
                    <x-icon name="eye" class="h-5 w-5"/>
                </button>
                @error('password') <p class="error">{{ $message }}</p> @enderror
            </div>
        </div>

        <label class="mt-4 flex cursor-pointer items-start gap-2 text-xs text-grey">
            <input type="checkbox" name="terms" value="1" class="mt-0.5 rounded border-gray-300 text-primary focus:ring-primary" @checked(old('terms'))>
            Saya menyetujui syarat & ketentuan serta kebijakan privasi {{ setting('store_name') }}
        </label>
        @error('terms') <p class="error">{{ $message }}</p> @enderror

        <button type="submit" class="btn-primary mt-6 w-full py-3.5 text-lg font-semibold">Buat Akun</button>

        <a href="{{ route('google.redirect') }}" class="btn-dark mt-3 w-full py-3.5">
            <x-icon name="google" class="h-5 w-5"/> Or sign up with Google
        </a>

        <p class="mt-6 text-center text-sm font-medium">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-primary underline">Sign in</a>
        </p>
    </form>
    </x-auth-shell>
@endsection
