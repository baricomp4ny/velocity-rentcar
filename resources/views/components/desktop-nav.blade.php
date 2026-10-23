@props(['active' => 'home'])

@php
    $user = Auth::user();
    $links = [
        'home' => ['Home', route('home')],
        'explore' => ['Explore', route('explore')],
        'saved' => ['Saved', route('saved')],
        'bookings' => ['Riwayat', route('bookings.index')],
        'chat' => ['Chat', route('chat')],
    ];
@endphp

{{-- Navigasi atas khusus desktop; di ponsel diganti bottom navigation. --}}
<nav class="sticky top-0 z-40 hidden border-b border-gray-100 bg-white/95 backdrop-blur lg:block">
    <div class="container-page flex h-[72px] items-center gap-8">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <x-logo class="h-11 w-auto"/>
            <span class="text-base font-semibold">{{ setting('store_name') }}</span>
        </a>

        <ul class="flex flex-1 items-center gap-1">
            @foreach ($links as $key => [$label, $url])
                <li>
                    <a href="{{ $url }}"
                       class="rounded-lg px-4 py-2 text-sm font-medium transition {{ $active === $key ? 'bg-primary-50 text-primary' : 'text-grey hover:text-ink' }}">{{ $label }}</a>
                </li>
            @endforeach
        </ul>

        <div class="flex items-center gap-2">
            <x-help-button class="bg-primary-50 text-primary hover:bg-primary-100"/>
            <a href="{{ route('notifications') }}" class="relative flex h-10 w-10 items-center justify-center rounded-xl bg-primary-50 text-primary transition hover:bg-primary-100" aria-label="Notifikasi">
                <x-icon name="bell" class="h-5 w-5"/>
                @if ($user->userNotifications()->whereNull('read_at')->exists())
                    <span class="absolute right-2 top-2 h-2.5 w-2.5 rounded-full border-2 border-white bg-red-500"></span>
                @endif
            </a>
            <a href="{{ route('profile') }}" class="ml-2 flex items-center gap-3 rounded-xl py-1.5 pl-1.5 pr-3 transition hover:bg-neutral {{ $active === 'profile' ? 'bg-primary-50' : '' }}">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary text-xs font-semibold text-white">{{ $user->initials() }}</span>
                <span class="text-sm font-medium">{{ \Illuminate\Support\Str::words($user->name, 2, '') }}</span>
            </a>
        </div>
    </div>
</nav>
