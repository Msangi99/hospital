@props([
    'kicker' => '',
    'title' => '',
    'description' => '',
    'variant' => 'dark',
])

@php
    $variants = [
        'dark' => 'border-slate-200 bg-gradient-to-br from-slate-900 via-slate-800 to-blue-950 text-white',
        'light' => 'border-slate-200 bg-white text-slate-900',
    ];
    $variantClass = $variants[$variant] ?? $variants['dark'];
    $pillsSlot = isset($pills) ? trim((string) $pills) : '';
    $actionsSlot = isset($actions) ? trim((string) $actions) : '';
@endphp

<section {{ $attributes->merge(['class' => "overflow-hidden rounded-[2rem] border p-6 shadow-lg sm:p-8 {$variantClass}"]) }}>
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            @if ($kicker !== '')
                <p class="text-[10px] font-black uppercase tracking-[0.25em] {{ $variant === 'dark' ? 'text-blue-200/90' : 'text-slate-500' }}">{{ $kicker }}</p>
            @endif
            <h1 class="mt-2 text-2xl font-black tracking-tight {{ $variant === 'dark' ? 'text-white' : 'text-slate-900' }} sm:text-3xl">{{ $title }}</h1>
            @if ($description !== '')
                <p class="mt-2 max-w-2xl text-sm font-semibold {{ $variant === 'dark' ? 'text-slate-300' : 'text-slate-600' }}">{{ $description }}</p>
            @endif
            @if ($pillsSlot !== '')
                <div class="mt-4 flex flex-wrap gap-2">
                    {{ $pills }}
                </div>
            @endif
        </div>
        @if ($actionsSlot !== '')
            <div>
                {{ $actions }}
            </div>
        @endif
    </div>
</section>
