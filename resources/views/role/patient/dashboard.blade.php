@php($sidebarTitle = __('roleui.patient_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.patient_dashboard'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.patient._sidebar', ['active' => 'dashboard'])
    @endslot

    <div class="space-y-8">
        <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-10">
            <h1 class="mb-2 text-2xl font-black tracking-tighter text-slate-900 sm:text-3xl">{{ __('roleui.patient_dashboard') }}</h1>
            <p class="max-w-2xl text-sm font-bold text-slate-500">{{ __('roleui.patient_dashboard_intro') }}</p>
        </div>

        <div class="space-y-3">
            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">{{ __('roleui.patient_dashboard_quick_links') }}</h2>
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('patient.hospitals') }}" class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-lg transition hover:border-blue-100 hover:shadow-xl">
                <p class="text-[10px] font-black uppercase tracking-widest text-blue-600">{{ __('roleui.patient_dashboard_cta_hospitals') }}</p>
                <p class="mt-2 text-sm font-bold text-slate-600">{{ __('roleui.sidebar_patient_hospitals') }}</p>
            </a>
            <a href="{{ route('patient.appointments') }}" class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-lg transition hover:border-blue-100 hover:shadow-xl">
                <p class="text-[10px] font-black uppercase tracking-widest text-blue-600">{{ __('roleui.patient_dashboard_cta_appts') }}</p>
                <p class="mt-2 text-sm font-bold text-slate-600">{{ __('roleui.sidebar_appointments') }}</p>
            </a>
            <a href="{{ route('patient.video-consult') }}" class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-lg transition hover:border-blue-100 hover:shadow-xl">
                <p class="text-[10px] font-black uppercase tracking-widest text-blue-600">{{ __('roleui.patient_dashboard_cta_video') }}</p>
                <p class="mt-2 text-sm font-bold text-slate-600">{{ __('roleui.sidebar_video_consult') }}</p>
            </a>
            <a href="{{ route('patient.conversations') }}" class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-lg transition hover:border-blue-100 hover:shadow-xl">
                <p class="text-[10px] font-black uppercase tracking-widest text-blue-600">{{ __('roleui.patient_dashboard_cta_messages') }}</p>
                <p class="mt-2 text-sm font-bold text-slate-600">{{ __('roleui.patient_dashboard_messages_count', ['count' => $conversationCount]) }}</p>
            </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-[2.5rem] border border-slate-100 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">{{ __('roleui.patient_dashboard_upcoming') }}</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($upcomingAppointments as $appointment)
                    <div class="flex flex-col gap-1 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-black text-slate-900">{{ $doctorNames[$appointment->doctor_id] ?? '—' }}</p>
                            <p class="text-xs font-bold text-slate-500">{{ $appointment->appointment_date }} · {{ $appointment->appointment_time }}</p>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-blue-600">{{ $appointment->status }}</span>
                    </div>
                @empty
                    <p class="px-6 py-10 text-center text-sm font-bold text-slate-500">{{ __('roleui.patient_dashboard_upcoming_empty') }}</p>
                @endforelse
            </div>
        </div>
    </div>
@endcomponent
