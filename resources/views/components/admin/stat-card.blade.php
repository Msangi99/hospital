@props([
    'label' => '',
    'value' => '',
    'tone' => 'neutral',
])

@php
    $toneMap = [
        'neutral' => 'border-slate-100 bg-white text-slate-900 label:text-slate-400',
        'amber' => 'border-amber-100 bg-amber-50 text-amber-900 label:text-amber-700',
        'emerald' => 'border-emerald-100 bg-emerald-50 text-emerald-900 label:text-emerald-700',
        'rose' => 'border-rose-100 bg-rose-50 text-rose-900 label:text-rose-700',
        'blue' => 'border-blue-100 bg-blue-50 text-blue-900 label:text-blue-700',
    ];
    $classes = $toneMap[$tone] ?? $toneMap['neutral'];
    $parts = explode(' label:', $classes);
    $cardClass = $parts[0];
    $labelClass = isset($parts[1]) ? $parts[1] : 'text-slate-400';
@endphp

<div {{ $attributes->merge(['class' => "rounded-3xl border p-5 shadow-sm {$cardClass}"]) }}>
    <p class="text-[10px] font-black uppercase tracking-[0.2em] {{ $labelClass }}">{{ $label }}</p>
    <p class="mt-2 text-3xl font-black tracking-tighter">{{ $value }}</p>
</div>
