@props(['title', 'back' => null])

{{-- Header halaman turunan: tombol kembali, judul di tengah, tombol bantuan.
     Di desktop menjadi judul halaman biasa (navigasi ada di atas). --}}
<header class="sticky top-0 z-20 border-b border-gray-100 bg-white/95 px-4 py-3 backdrop-blur lg:static lg:border-0 lg:bg-transparent lg:px-0 lg:pb-0 lg:pt-10 lg:backdrop-blur-none">
    <div class="container-page flex items-center gap-2">
        <a href="{{ $back ?? url()->previous() }}" class="flex h-10 w-10 flex-none items-center justify-center rounded-full text-ink hover:bg-neutral lg:bg-white lg:shadow-card" aria-label="Kembali">
            <x-icon name="chevron-left" class="h-6 w-6"/>
        </a>
        <h1 class="flex-1 text-center text-base font-semibold lg:ml-2 lg:text-left lg:text-2xl">{{ $title }}</h1>
        <x-help-button class="bg-primary-50 text-primary hover:bg-primary-100 lg:hidden"/>
    </div>
</header>
