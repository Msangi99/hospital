@php($sidebarTitle = __('roleui.medical_staff'))

@component('layouts.role-dashboard', ['title' => __('roleui.sidebar_video_consult'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.doctor._sidebar', ['active' => 'video'])
    @endslot

    @include('role.partials.video-consult-panel')
@endcomponent
