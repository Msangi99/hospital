@php($sidebarTitle = __('roleui.patient_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.sidebar_patient_ambulance'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.patient._sidebar', ['active' => 'ambulance'])
    @endslot

    <div class="mb-8 rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-8">
        <p class="text-sm font-bold text-slate-600">{{ __('roleui.patient_ambulance_intro') }}</p>
    </div>

    <div class="rounded-[2.5rem] border border-slate-100 bg-slate-50/80 p-4 sm:p-8">
        @include('partials.ambulance-sos-inner', ['formAction' => route('patient.ambulance.sos')])
    </div>
@endcomponent
