@php($sidebarTitle = __('roleui.patient_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.sidebar_patient_help'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.patient._sidebar', ['active' => 'help'])
    @endslot

    <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-10">
        <h1 class="mb-2 text-2xl font-black tracking-tighter text-slate-900 sm:text-3xl">{{ __('roleui.patient_help_heading') }}</h1>
        <p class="mb-8 text-sm font-bold text-slate-500">{{ __('roleui.patient_help_intro') }}</p>
        <ul class="space-y-4 text-sm font-bold text-slate-700">
            <li class="flex gap-3">
                <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-blue-500"></span>
                <span>{{ __('roleui.patient_help_li_hospitals') }}</span>
            </li>
            <li class="flex gap-3">
                <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-blue-500"></span>
                <span>{{ __('roleui.patient_help_li_video') }}</span>
            </li>
            <li class="flex gap-3">
                <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-blue-500"></span>
                <span>{{ __('roleui.patient_help_li_chat') }}</span>
            </li>
            <li class="flex gap-3">
                <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-blue-500"></span>
                <span>{{ __('roleui.patient_help_li_ambulance') }}</span>
            </li>
            <li class="flex gap-3">
                <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-blue-500"></span>
                <span>{{ __('roleui.patient_help_li_contact') }}</span>
            </li>
        </ul>
        <div class="mt-10 flex flex-wrap gap-4">
            <a href="{{ route('patient.hospitals') }}" class="inline-flex rounded-2xl bg-slate-900 px-6 py-3 text-xs font-black uppercase tracking-widest text-white transition hover:bg-blue-600">{{ __('roleui.sidebar_patient_hospitals') }}</a>
            <a href="{{ route('patient.video-consult') }}" class="inline-flex rounded-2xl border border-slate-200 px-6 py-3 text-xs font-black uppercase tracking-widest text-slate-800 transition hover:border-blue-200">{{ __('roleui.sidebar_video_consult') }}</a>
            <a href="{{ route('patient.conversations') }}" class="inline-flex rounded-2xl border border-slate-200 px-6 py-3 text-xs font-black uppercase tracking-widest text-slate-800 transition hover:border-blue-200">{{ __('roleui.sidebar_conversations') }}</a>
            <a href="{{ route('patient.ambulance') }}" class="inline-flex rounded-2xl border border-slate-200 px-6 py-3 text-xs font-black uppercase tracking-widest text-slate-800 transition hover:border-blue-200">{{ __('roleui.sidebar_patient_ambulance') }}</a>
            <a href="{{ route('contact') }}" target="_blank" rel="noopener noreferrer" class="inline-flex rounded-2xl border border-slate-200 px-6 py-3 text-xs font-black uppercase tracking-widest text-slate-800 transition hover:border-blue-200">{{ __('public.contact_badge') }}</a>
        </div>
    </div>
@endcomponent
