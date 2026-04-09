@php($sidebarTitle = __('roleui.patient_portal'))
@php($linkActive = 'flex items-center gap-3 rounded-2xl px-4 py-3 font-black text-xs uppercase tracking-widest bg-white/10 hover:bg-white/15 transition')

@component('layouts.role-dashboard', ['title' => __('roleui.patient_dashboard'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        <a href="{{ route('patient.dashboard') }}" class="{{ $linkActive }}">
            <i class="fas fa-th-large w-5 text-blue-300"></i>
            <span>{{ __('roleui.sidebar_dashboard') }}</span>
        </a>
    @endslot

    <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-10">
        <h1 class="mb-2 text-2xl font-black tracking-tighter sm:text-3xl">{{ __('roleui.patient_dashboard') }}</h1>
        <p class="font-bold text-slate-500">{{ __('roleui.coming_next') }}</p>
    </div>
@endcomponent
