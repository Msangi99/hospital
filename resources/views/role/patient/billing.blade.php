@php($sidebarTitle = __('roleui.patient_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.sidebar_patient_billing'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.patient._sidebar', ['active' => 'billing'])
    @endslot

    <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-10">
        <h1 class="mb-2 text-2xl font-black tracking-tighter text-slate-900 sm:text-3xl">{{ __('roleui.patient_billing_heading') }}</h1>
        <p class="mb-4 text-sm font-bold text-slate-500">{{ __('roleui.patient_billing_subheading') }}</p>
        <p class="max-w-2xl text-sm font-medium leading-relaxed text-slate-600">{{ __('roleui.patient_billing_body') }}</p>
    </div>
@endcomponent
