@props(['class' => 'bg-white/20 text-white hover:bg-white/30'])

{{-- Tombol "Butuh Bantuan" (hasil Ideate): langsung ke chat Customer Service. --}}
<a href="{{ route('chat') }}" title="Butuh Bantuan?" aria-label="Butuh Bantuan?"
   {{ $attributes->merge(['class' => "flex h-10 w-10 flex-none items-center justify-center rounded-xl transition $class"]) }}>
    <x-icon name="help" class="h-5 w-5"/>
</a>
