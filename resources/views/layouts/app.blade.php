<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#007DFC">
    <title>@yield('title', setting('store_name')) · {{ setting('store_name') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Permanent+Marker&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#E9EEF5] lg:bg-neutral">
    {{-- Ponsel & tablet: tampilan aplikasi mobile sesuai prototipe (selebar layar ponsel).
         Desktop (lg): lebar penuh dengan navigasi di atas. --}}
    <div class="relative mx-auto flex min-h-screen max-w-[430px] flex-col bg-white shadow-xl lg:max-w-none lg:bg-neutral lg:shadow-none">
        <x-flash />

        @auth
            @unless (Auth::user()->isAdmin())
                <x-desktop-nav :active="trim($__env->yieldContent('nav', $__env->yieldContent('bottom-nav')))" />
            @endunless
        @endauth

        <main class="flex-1 lg:pb-12 @hasSection('bottom-nav') pb-24 @endif">
            @yield('content')
        </main>

        @hasSection('bottom-nav')
            <x-bottom-nav :active="trim($__env->yieldContent('bottom-nav'))" />
        @endif
    </div>

    @stack('scripts')
</body>
</html>
