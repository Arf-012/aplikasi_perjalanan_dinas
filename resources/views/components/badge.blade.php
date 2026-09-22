@props(['type' => 'neutral'])

@php
$classes = match($type) {
    'success' => 'bg-[#e8f5e9] text-[#2e7d32] border border-[#a5d6a7]',
    'warning' => 'bg-[#fff3e0] text-[#e65100] border border-[#ffcc80]',
    'danger' => 'bg-[#ffebee] text-[#ba1a1a] border border-[#ef9a9a]',
    'primary' => 'bg-[#dfe8ff] text-[#00254e] border border-[#c3c6d1]/40',
    default => 'bg-[#f0f3ff] text-[#43474f] border border-[#c3c6d1]/30',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide {$classes}"]) }}>
    {{ $slot }}
</span>
