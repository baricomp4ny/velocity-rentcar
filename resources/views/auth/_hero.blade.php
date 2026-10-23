<div class="relative lg:hidden">
    <div class="h-2 bg-primary"></div>
    <img src="{{ media_url(setting('login_image')) }}" alt="" class="h-52 w-full object-cover">
    <a href="{{ route('start') }}" class="icon-btn absolute left-4 top-6 h-11 w-11" aria-label="Kembali">
        <x-icon name="chevron-left" class="h-5 w-5"/>
    </a>
</div>

<div class="px-6 pt-5 text-center lg:px-8 lg:pt-0 lg:text-left">
    <h1 class="text-[22px] font-bold leading-snug lg:text-3xl">{{ setting('headline_prefix') }} <span class="text-primary">{{ setting('headline_highlight') }}</span><br>{{ setting('headline_suffix') }}</h1>
    <p class="mt-2 text-sm text-grey">{{ $subtitle }}</p>
</div>
