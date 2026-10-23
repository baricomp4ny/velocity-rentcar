@props(['step', 'title', 'subtitle'])

<div class="mb-5">
    <div class="flex items-center justify-between">
        <h2 class="text-base font-semibold">{{ $title }}</h2>
        <span class="text-sm text-grey">Step {{ $step }} of 4</span>
    </div>
    <p class="mt-1 text-xs text-grey">{{ $subtitle }}</p>
    <div class="mt-3 grid grid-cols-4 gap-1.5">
        @for ($i = 1; $i <= 4; $i++)
            <span class="h-1 rounded-full {{ $i <= $step ? 'bg-primary' : 'bg-gray-200' }}"></span>
        @endfor
    </div>
</div>
