@props(['subtitle'])

{{-- Kerangka Sign in & Buat Akun. Ponsel: foto di atas lalu form (sesuai prototipe).
     Desktop: layar terbagi, foto besar di kiri dan form di kanan. --}}
<div class="lg:grid lg:min-h-screen lg:grid-cols-2 lg:bg-white">
    <div class="relative hidden lg:block">
        <img src="{{ media_url(setting('login_image')) }}" alt="" class="absolute inset-0 h-full w-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-ink/80 via-ink/20 to-transparent"></div>
        <a href="{{ route('start') }}" class="absolute left-10 top-10 flex items-center gap-3 rounded-2xl bg-white/90 py-2 pl-2 pr-4 backdrop-blur">
            <x-logo class="h-10 w-auto"/>
            <span class="text-sm font-semibold">{{ setting('store_name') }}</span>
        </a>
        <div class="absolute inset-x-12 bottom-12 text-white">
            <p class="text-4xl font-bold leading-tight">
                {{ setting('headline_prefix') }} <span class="text-primary-100">{{ setting('headline_highlight') }}</span><br>{{ setting('headline_suffix') }}
            </p>
            <p class="mt-3 max-w-md text-white/80">{{ setting('start_subtitle') }}</p>
        </div>
    </div>

    <div class="lg:flex lg:items-center lg:justify-center lg:px-12 lg:py-12">
        <div class="lg:w-full lg:max-w-md">
            @include('auth._hero', ['subtitle' => $subtitle])
            {{ $slot }}
        </div>
    </div>
</div>
