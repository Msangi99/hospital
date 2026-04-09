@php($sidebarTitle = __('roleui.medical_staff'))

@component('layouts.role-dashboard', ['title' => __('roleui.sidebar_patients'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.doctor._sidebar', ['active' => 'patients'])
    @endslot

    <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-10">
        <p class="font-bold text-slate-500">{{ __('roleui.coming_next') }}</p>
    </div>
@endcomponent
