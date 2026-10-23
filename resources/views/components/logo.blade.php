@props(['variant' => 'blue', 'class' => 'h-24 w-auto'])

@php
    // Tiga varian logo di Style Guide: biru, abu-abu, dan hitam.
    $color = ['blue' => '#007DFC', 'grey' => '#888689', 'black' => '#121212'][$variant] ?? '#007DFC';
@endphp

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 220 180" role="img" aria-label="Velocity Rentals">
    <circle cx="110" cy="78" r="66" fill="#fff" stroke="{{ $color }}" stroke-width="9"/>
    <g fill="none" stroke="{{ $color }}" stroke-width="5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M58 98c-4 0-6-3-6-6v-8c0-4 2-6 6-7l18-4 18-15c4-3 8-4 13-4h28c6 0 10 2 14 5l17 14 13 3c4 1 6 4 6 8v8c0 3-2 6-6 6"/>
        <path d="M92 98h38M76 73h82M112 58v15"/>
        <circle cx="78" cy="98" r="10"/>
        <circle cx="146" cy="98" r="10"/>
        <path d="M60 85h10M152 85h9"/>
    </g>
    <rect x="20" y="124" width="180" height="38" rx="19" fill="{{ $color }}"/>
    <text x="110" y="150" text-anchor="middle" fill="#fff" font-family="Poppins, sans-serif" font-size="17" font-weight="700" textLength="160" lengthAdjust="spacingAndGlyphs">VELOCITY RENTALS</text>
</svg>
