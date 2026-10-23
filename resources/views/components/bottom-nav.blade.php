@props(['active' => 'home'])

@php
    // Riwayat pemesanan di ponsel dibuka dari menu profil.
    $active = $active === 'bookings' ? 'profile' : $active;

    $items = [
        'home' => ['Home', 'home-o', route('home')],
        'explore' => ['Explore', 'compass', route('explore')],
        'saved' => ['Saved', 'heart', route('saved')],
        'chat' => ['Chat', 'chat-o', route('chat')],
        'profile' => ['Profile', 'user-o', route('profile')],
    ];
@endphp

<nav class="fixed bottom-0 left-1/2 z-40 w-full max-w-[430px] lg:hidden -translate-x-1/2 border-t border-gray-100 bg-white px-4 pb-[max(.75rem,env(safe-area-inset-bottom))] pt-2.5">
    <ul class="flex items-center justify-between">
        @foreach ($items as $key => [$label, $icon, $url])
            <li class="flex-1">
                <a href="{{ $url }}" class="flex flex-col items-center gap-1 text-[11px] {{ $active === $key ? 'font-medium text-primary' : 'text-grey hover:text-ink' }}">
                    <x-icon :name="$icon" class="h-[22px] w-[22px]"/>
                    {{ $label }}
                </a>
            </li>
        @endforeach
    </ul>
</nav>
