@props(['active' => 'home'])

@php
    $user = Auth::user();
    $links = [
        'home' => ['Home', route('home')],
        'explore' => ['Explore', route('explore')],
        'saved' => ['Saved', route('saved')],
        'bookings' => ['Riwayat', route('bookings.index')],
        'chat' => ['Chat', route('chat')],
        'profile' => ['Profil', route('profile')],
    ];
    $hasUnread = $user->userNotifications()->whereNull('read_at')->exists();
@endphp

{{-- Navigasi atas khusus desktop (gaya minimalis): logo di kiri, menu & tombol Logout di kanan.
     Di ponsel diganti bottom navigation; Logout ada di halaman Profil. --}}
<nav class="sticky top-0 z-40 hidden bg-white/95 backdrop-blur lg:block">
    <div class="container-page flex h-20 items-center justify-between gap-8">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <x-logo class="h-9 w-auto"/>
            <span class="text-[15px] font-bold tracking-tight">{{ setting('store_name') }}</span>
        </a>

        <div class="flex items-center gap-9">
            <ul class="flex items-center gap-7">
                @foreach ($links as $key => [$label, $url])
                    <li>
                        <a href="{{ $url }}"
                           class="relative text-[13px] font-semibold transition {{ $active === $key ? 'text-ink after:absolute after:-bottom-1.5 after:left-0 after:h-0.5 after:w-full after:rounded-full after:bg-ink' : 'text-grey hover:text-ink' }}">{{ $label }}</a>
                    </li>
                @endforeach
            </ul>

            <div class="flex items-center gap-3">
                <a href="{{ route('notifications') }}" class="relative flex h-9 w-9 items-center justify-center rounded-full text-ink transition hover:bg-neutral" aria-label="Notifikasi">
                    <x-icon name="bell-o" class="h-5 w-5"/>
                    @if ($hasUnread)
                        <span class="absolute right-2 top-1.5 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                    @endif
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="flex items-center gap-2 rounded-full bg-ink py-2.5 pl-4 pr-5 text-xs font-semibold text-white transition hover:bg-ink/85">
                        <x-icon name="logout" class="h-4 w-4"/> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
