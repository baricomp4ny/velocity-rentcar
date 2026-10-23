@foreach (['success' => 'bg-emerald-500', 'error' => 'bg-red-500'] as $key => $color)
    @if (session($key))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition.opacity
             class="fixed left-1/2 top-4 z-50 w-[calc(100%-2rem)] max-w-[398px] -translate-x-1/2 rounded-xl {{ $color }} px-4 py-3 text-sm text-white shadow-lg">
            {{ session($key) }}
        </div>
    @endif
@endforeach
