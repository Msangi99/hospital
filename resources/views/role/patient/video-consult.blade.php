@php($sidebarTitle = __('roleui.patient_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.sidebar_video_consult'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.patient._sidebar', ['active' => 'video'])
    @endslot

    @include('role.partials.video-consult-panel')
@endcomponent
