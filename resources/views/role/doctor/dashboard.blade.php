@php($sidebarTitle = __('roleui.medical_staff'))

@component('layouts.role-dashboard', ['title' => __('roleui.doctor_dashboard'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.doctor._sidebar', ['active' => 'dashboard'])
    @endslot

    <div class="space-y-6">
        @if($videoRequests->isNotEmpty())
            <div class="sticky top-4 z-40 mx-auto max-w-3xl rounded-2xl border border-blue-100 bg-white/95 p-4 text-center shadow-xl backdrop-blur">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-600">Incoming Video Consultation</p>
                <p class="mt-1 text-sm font-bold text-slate-700">
                    {{ ($videoPatientNames[$videoRequests->first()->patient_id] ?? 'A patient') }} is requesting to join video consult.
                </p>
                <a href="{{ route('video-consult', ['room' => $videoRequests->first()->room_id]) }}"
                   class="mt-3 inline-flex rounded-xl bg-blue-600 px-5 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-white transition hover:bg-blue-700">
                    Join Call Now
                </a>
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Today Appointments</p>
                <p class="mt-2 text-3xl font-black tracking-tighter text-slate-900">{{ $stats['today_appointments'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Upcoming</p>
                <p class="mt-2 text-3xl font-black tracking-tighter text-slate-900">{{ $stats['upcoming_appointments'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Assigned Patients</p>
                <p class="mt-2 text-3xl font-black tracking-tighter text-slate-900">{{ $stats['patients'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Active Video Sessions</p>
                <p class="mt-2 text-3xl font-black tracking-tighter text-slate-900">{{ $stats['active_video_sessions'] }}</p>
            </div>
        </div>

        <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-sm">
            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">Verification Status</h2>
            <p class="mt-3 text-sm font-bold text-slate-600">
                {{ $profile ? ('Profile status: '.$profile->verification_status) : 'Profile not submitted yet. Please complete your profile.' }}
            </p>
        </div>

        <div class="overflow-hidden rounded-[2.5rem] border border-slate-100 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">Next Appointments</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                            <th class="px-6 py-3">Patient</th>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Time</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($nextAppointments as $appointment)
                            <tr>
                                <td class="px-6 py-3 text-sm font-black text-slate-800">{{ $patientNames[$appointment->patient_id] ?? 'Unknown' }}</td>
                                <td class="px-6 py-3 text-xs font-bold text-slate-500">{{ $appointment->appointment_date }}</td>
                                <td class="px-6 py-3 text-xs font-bold text-slate-500">{{ $appointment->appointment_time }}</td>
                                <td class="px-6 py-3 text-xs font-black text-slate-500">{{ $appointment->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm font-bold text-slate-400">No appointments scheduled yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endcomponent
