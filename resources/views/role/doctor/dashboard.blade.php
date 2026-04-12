@php($sidebarTitle = __('roleui.medical_staff'))

@component('layouts.role-dashboard', ['title' => __('roleui.doctor_dashboard'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.doctor._sidebar', ['active' => 'dashboard'])
    @endslot

    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('roleui.doctor_dashboard_stat_today') }}</p>
                <p class="mt-2 text-3xl font-black tracking-tighter text-slate-900">{{ $stats['today_appointments'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('roleui.doctor_dashboard_stat_upcoming') }}</p>
                <p class="mt-2 text-3xl font-black tracking-tighter text-slate-900">{{ $stats['upcoming_appointments'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('roleui.doctor_dashboard_stat_patients') }}</p>
                <p class="mt-2 text-3xl font-black tracking-tighter text-slate-900">{{ $stats['patients'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('roleui.doctor_dashboard_stat_video') }}</p>
                <p class="mt-2 text-3xl font-black tracking-tighter text-slate-900">{{ $stats['active_video_sessions'] }}</p>
            </div>
        </div>

        <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-xs font-black uppercase tracking-[0.2em] text-slate-500">{{ __('roleui.doctor_dashboard_quick_title') }}</h2>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('doctor.appointments') }}" class="inline-flex rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-800 transition hover:border-blue-200 hover:bg-blue-50">
                    {{ __('roleui.doctor_dashboard_quick_appointments') }}
                </a>
                <a href="{{ route('doctor.patients') }}" class="inline-flex rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-800 transition hover:border-blue-200 hover:bg-blue-50">
                    {{ __('roleui.doctor_dashboard_quick_patients') }}
                </a>
                @if (! empty($medicalIsNurse))
                    <a href="{{ route('nurse.patient-chats') }}" class="inline-flex rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-800 transition hover:border-blue-200 hover:bg-blue-50">
                        {{ __('roleui.nurse_dashboard_quick_patient_chats') }}
                    </a>
                @else
                    <a href="{{ route('doctor.conversations') }}" class="inline-flex rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-800 transition hover:border-blue-200 hover:bg-blue-50">
                        {{ __('roleui.doctor_dashboard_quick_messages') }}
                    </a>
                    <a href="{{ route('doctor.nurse-coordination') }}" class="inline-flex rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-800 transition hover:border-emerald-200 hover:bg-emerald-50">
                        {{ __('roleui.doctor_dashboard_quick_nurse_coordination') }}
                    </a>
                @endif
                <a href="{{ route('doctor.video-requests') }}" class="inline-flex rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-800 transition hover:border-blue-200 hover:bg-blue-50">
                    {{ __('roleui.doctor_dashboard_quick_video') }}
                </a>
            </div>
        </div>

        <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-sm">
            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">{{ __('roleui.doctor_dashboard_verification_heading') }}</h2>
            <p class="mt-3 text-sm font-bold text-slate-600">
                @if ($profile)
                    {{ __('roleui.doctor_dashboard_verification_with_status', ['status' => (string) $profile->verification_status]) }}
                @else
                    {{ __('roleui.doctor_dashboard_verification_missing') }}
                @endif
            </p>
        </div>

        <div class="overflow-hidden rounded-[2.5rem] border border-slate-100 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">{{ __('roleui.doctor_dashboard_next_appts_heading') }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                            <th class="px-6 py-3">{{ __('roleui.doctor_patients_col_patient') }}</th>
                            <th class="px-6 py-3">{{ __('roleui.doctor_appt_form_date') }}</th>
                            <th class="px-6 py-3">{{ __('roleui.doctor_appt_form_time') }}</th>
                            <th class="px-6 py-3">{{ __('roleui.patient_appt_col_status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($nextAppointments as $appointment)
                            <tr>
                                <td class="px-6 py-3 text-sm font-black text-slate-800">{{ $patientNames[$appointment->patient_id] ?? __('roleui.doctor_dashboard_unknown_patient') }}</td>
                                <td class="px-6 py-3 text-xs font-bold text-slate-500">{{ $appointment->appointment_date }}</td>
                                <td class="px-6 py-3 text-xs font-bold text-slate-500">{{ $appointment->appointment_time }}</td>
                                <td class="px-6 py-3 text-xs font-black text-slate-500">{{ $appointment->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm font-bold text-slate-400">{{ __('roleui.doctor_dashboard_next_appts_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endcomponent
