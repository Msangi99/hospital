@php($sidebarTitle = __('roleui.patient_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.patient_dashboard'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.patient._sidebar', ['active' => 'dashboard'])
    @endslot

    <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-10">
        <h1 class="mb-2 text-2xl font-black tracking-tighter sm:text-3xl">{{ __('roleui.patient_dashboard') }}</h1>
        <p class="font-bold text-slate-500">{{ __('roleui.coming_next') }}</p>
    </div>
@endcomponent
