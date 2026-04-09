@php($sidebarTitle = __('roleui.medical_staff'))

@component('layouts.role-dashboard', ['title' => __('roleui.sidebar_appointments'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        <a href="{{ route('doctor.dashboard') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 font-black text-xs uppercase tracking-widest text-slate-300 hover:text-white hover:bg-white/10 transition">
            <i class="fas fa-th-large w-5 text-blue-300"></i>
            <span>{{ __('roleui.sidebar_dashboard') }}</span>
        </a>
        <a href="{{ route('doctor.appointments') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 font-black text-xs uppercase tracking-widest bg-white/10 hover:bg-white/15 transition">
            <i class="fas fa-calendar-alt w-5 text-blue-300"></i>
            <span>{{ __('roleui.sidebar_appointments') }}</span>
        </a>
        <a href="{{ route('doctor.patients') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 font-black text-xs uppercase tracking-widest text-slate-300 hover:text-white hover:bg-white/10 transition">
            <i class="fas fa-user-injured w-5 text-blue-300"></i>
            <span>{{ __('roleui.sidebar_patients') }}</span>
        </a>
    @endslot

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl p-10">
        <p class="text-slate-500 font-bold">{{ __('roleui.coming_next') }}</p>
    </div>
@endcomponent

