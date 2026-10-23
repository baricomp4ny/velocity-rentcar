<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · Admin {{ setting('store_name') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral" x-data="{ sidebar: false }">
    @php
        $menu = [
            'Utama' => [
                ['admin.dashboard', 'Dashboard', 'grid', 'admin.dashboard'],
                ['admin.bookings.index', 'Pesanan', 'receipt', 'admin.bookings.*'],
                ['admin.chats.index', 'Chat CS', 'chat', 'admin.chats.*'],
            ],
            'Katalog' => [
                ['admin.cars.index', 'Mobil', 'car', 'admin.cars.*'],
                ['admin.brands.index', 'Merek', 'tag', 'admin.brands.*'],
                ['admin.categories.index', 'Kategori', 'layers', 'admin.categories.*'],
            ],
            'Penjualan' => [
                ['admin.promos.index', 'Kode Promo', 'wallet', 'admin.promos.*'],
                ['admin.payment-methods.index', 'Metode Pembayaran', 'card', 'admin.payment-methods.*'],
                ['admin.locations.index', 'Lokasi Layanan', 'map-pin', 'admin.locations.*'],
            ],
            'Pengguna' => [
                ['admin.users.index', 'Pelanggan & Admin', 'users', 'admin.users.*'],
                ['admin.reviews.index', 'Ulasan', 'star-outline', 'admin.reviews.*'],
                ['admin.announcements.create', 'Pengumuman', 'megaphone', 'admin.announcements.*'],
            ],
            'Toko' => [
                ['admin.settings.edit', 'Pengaturan Toko', 'settings', 'admin.settings.*'],
            ],
        ];
    @endphp

    <div x-show="sidebar" x-cloak @click="sidebar = false" class="fixed inset-0 z-30 bg-ink/40 lg:hidden"></div>

    <aside class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-white shadow-card transition lg:translate-x-0"
           :class="sidebar && 'translate-x-0'">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-6 py-6">
            <x-logo class="h-12 w-auto"/>
            <span>
                <span class="block text-sm font-semibold">{{ setting('store_name') }}</span>
                <span class="block text-xs text-grey">Panel Admin</span>
            </span>
        </a>

        <nav class="min-h-0 flex-1 space-y-4 overflow-y-auto px-4 pb-4">
            @foreach ($menu as $group => $items)
                <div class="space-y-1">
                    <p class="px-4 text-[10px] font-semibold uppercase tracking-wider text-grey/70">{{ $group }}</p>
                    @foreach ($items as [$route, $label, $icon, $pattern])
                        @php $active = request()->routeIs($pattern); @endphp
                        <a href="{{ route($route) }}"
                           class="flex items-center gap-3 rounded-xl px-4 py-2 text-sm font-medium transition {{ $active ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-grey hover:bg-neutral hover:text-ink' }}">
                            <x-icon :name="$icon" class="h-5 w-5"/>
                            <span class="flex-1">{{ $label }}</span>
                            @if ($icon === 'chat' && $unreadChats)
                                <span class="rounded-full px-2 py-0.5 text-[10px] {{ $active ? 'bg-white text-primary' : 'bg-red-500 text-white' }}">{{ $unreadChats }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endforeach
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100 p-4">
            @csrf
            <div class="mb-3 flex items-center gap-3 px-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-50 text-xs font-semibold text-primary">{{ Auth::user()->initials() }}</span>
                <span class="min-w-0 text-xs">
                    <span class="block truncate font-medium">{{ Auth::user()->name }}</span>
                    <span class="block truncate text-grey">{{ Auth::user()->email }}</span>
                </span>
            </div>
            <button class="flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium text-red-500 hover:bg-red-50">
                <x-icon name="logout" class="h-5 w-5"/> Keluar
            </button>
        </form>
    </aside>

    <div class="lg:pl-64">
        <header class="sticky top-0 z-20 flex items-center gap-3 border-b border-gray-100 bg-white/90 px-4 py-4 backdrop-blur sm:px-8">
            <button @click="sidebar = true" class="rounded-lg p-2 hover:bg-neutral lg:hidden" aria-label="Menu"><x-icon name="menu" class="h-5 w-5"/></button>
            <div class="flex-1">
                <h1 class="text-lg font-semibold">@yield('title', 'Dashboard')</h1>
                @hasSection('subtitle') <p class="text-xs text-grey">@yield('subtitle')</p> @endif
            </div>
            @yield('actions')
        </header>

        <x-flash/>

        <main class="p-4 sm:p-8">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
