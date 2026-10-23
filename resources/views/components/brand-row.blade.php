@props(['brands', 'active' => null, 'title' => true])

{{-- Baris merek: logo bulat, merek aktif berwarna biru --}}
<section>
    @if ($title)
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-base font-semibold lg:text-xl">Brands</h2>
            <a href="{{ route('explore') }}" class="text-xs font-medium text-primary lg:text-sm">Lihat semua</a>
        </div>
    @endif

    <div class="-mx-5 flex gap-4 overflow-x-auto px-5 pb-1 lg:mx-0 lg:gap-8 lg:px-0">
        @foreach ($brands as $brand)
            @php $isActive = $active && $active->id === $brand->id; @endphp
            <a href="{{ route('explore', ['brand' => $brand->id]) }}" class="flex w-14 flex-none flex-col items-center gap-1.5 lg:w-20">
                <span class="flex h-14 w-14 items-center justify-center rounded-full transition lg:h-16 lg:w-16 {{ $isActive ? 'bg-primary shadow-lg shadow-primary/30' : 'bg-neutral hover:bg-primary-50 lg:bg-white lg:shadow-sm' }}">
                    @if ($brand->logoUrl())
                        <img src="{{ $brand->logoUrl() }}" alt="" class="h-7 w-7 object-contain lg:h-8 lg:w-8 {{ $isActive ? 'brightness-0 invert' : '' }}">
                    @else
                        <span class="text-lg font-bold {{ $isActive ? 'text-white' : '' }}">{{ mb_substr($brand->name, 0, 1) }}</span>
                    @endif
                </span>
                <span class="text-[11px] font-medium {{ $isActive ? 'text-primary' : 'text-ink/80' }} lg:text-xs">{{ $brand->name }}</span>
            </a>
        @endforeach
    </div>
</section>
