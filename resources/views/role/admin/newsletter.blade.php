@php($sidebarTitle = __('roleui.admin_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.admin_newsletter_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.admin._sidebar', ['active' => 'newsletter'])
    @endslot

    <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-10">
        <h1 class="mb-2 text-2xl font-black tracking-tighter sm:text-3xl">{{ __('roleui.admin_newsletter_title') }}</h1>
        <p class="font-bold text-slate-500">{{ __('roleui.coming_next') }}</p>
    </div>
@endcomponent
