@props(['car', 'saved' => false, 'class' => 'h-9 w-9 rounded-full'])

{{-- Tombol hati: simpan mobil ke menu Saved tanpa reload halaman --}}
<button type="button"
        x-data="{ saved: @js($saved), busy: false }"
        @click.prevent.stop="
            if (busy) return; busy = true;
            axios.post('{{ route('saved.toggle', $car) }}')
                .then(r => saved = r.data.saved)
                .finally(() => busy = false)"
        :aria-pressed="saved"
        aria-label="Simpan mobil"
        {{ $attributes->merge(['class' => "flex items-center justify-center bg-white/95 shadow-sm backdrop-blur transition hover:scale-105 $class"]) }}>
    <x-icon name="heart" class="h-[18px] w-[18px] text-ink" x-show="!saved"/>
    <x-icon name="heart-solid" class="h-[18px] w-[18px] text-primary" x-show="saved" x-cloak/>
</button>
