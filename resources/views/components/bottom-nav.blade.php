@props(['active' => 'home'])

@php
    // Riwayat pemesanan di ponsel dibuka dari menu profil.
    $active = $active === 'bookings' ? 'profile' : $active;

    $items = [
        'home' => ['Home', 'home', route('home')],
        'explore' => ['Explore', 'pin', route('explore')],
        'saved' => ['Saved', 'bookmark', route('saved')],
        'chat' => ['Chat', 'chat', route('chat')],
        'profile' => ['profile', 'user', route('profile')],
    ];
@endphp

<nav class="fixed bottom-0 left-1/2 z-40 w-full max-w-[430px] lg:hidden -translate-x-1/2 rounded-t-3xl bg-white px-4 pb-[max(.75rem,env(safe-area-inset-bottom))] pt-3 shadow-nav">
    <ul class="flex items-center justify-between">
        @foreach ($items as $key => [$label, $icon, $url])
            <li class="flex-1">
                <a href="{{ $url }}" class="flex flex-col items-center gap-1 text-[11px] {{ $active === $key ? 'text-primary' : 'text-[#B3B3B3] hover:text-grey' }}">
                    <x-icon :name="$icon" class="h-6 w-6"/>
                    {{ $label }}
                </a>
            </li>
        @endforeach
    </ul>
</nav>
