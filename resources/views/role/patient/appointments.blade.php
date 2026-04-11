@php($sidebarTitle = __('roleui.patient_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.sidebar_appointments'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.patient._sidebar', ['active' => 'appointments'])
    @endslot

    <div class="space-y-6">
        <div class="overflow-hidden rounded-[2.5rem] border border-slate-100 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4">
                <h1 class="text-lg font-black tracking-tighter text-slate-900 sm:text-xl">{{ __('roleui.patient_appointments_heading') }}</h1>
                <p class="mt-1 text-xs font-bold text-slate-500">{{ __('roleui.patient_appointments_subheading') }}</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                            <th class="px-6 py-3">{{ __('roleui.patient_appt_col_doctor') }}</th>
                            <th class="px-6 py-3">{{ __('roleui.patient_appt_col_date') }}</th>
                            <th class="px-6 py-3">{{ __('roleui.patient_appt_col_time') }}</th>
                            <th class="px-6 py-3">{{ __('roleui.patient_appt_col_status') }}</th>
                            <th class="px-6 py-3">{{ __('roleui.patient_appt_col_reason') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($appointments as $appointment)
                            <tr>
                                <td class="px-6 py-3 text-sm font-black text-slate-800">{{ $doctorNames[$appointment->doctor_id] ?? '—' }}</td>
                                <td class="px-6 py-3 text-xs font-bold text-slate-500">{{ $appointment->appointment_date }}</td>
                                <td class="px-6 py-3 text-xs font-bold text-slate-500">{{ $appointment->appointment_time }}</td>
                                <td class="px-6 py-3 text-xs font-black text-slate-500">{{ $appointment->status }}</td>
                                <td class="px-6 py-3 text-xs font-bold text-slate-500">{{ $appointment->reason ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm font-bold text-slate-500">{{ __('roleui.patient_appointments_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endcomponent
