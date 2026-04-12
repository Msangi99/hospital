@php($sidebarTitle = __('roleui.medical_staff'))

@component('layouts.role-dashboard', ['title' => __('roleui.video_requests_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.doctor._sidebar', ['active' => 'video-requests'])
    @endslot

    <div class="space-y-6">
        @if ($videoDoctorHasRingingRequest ?? false)
            <div class="rounded-2xl border border-blue-100 bg-white/95 px-6 py-3 text-center shadow-lg">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-600">{{ __('roleui.video_consult_alert_kicker') }}</p>
                <p class="mt-1 text-sm font-bold text-slate-700">{{ __('roleui.video_alert_doctor_join_request') }}</p>
            </div>
        @endif

        <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
            <h1 class="text-xl font-black tracking-tighter text-slate-900 sm:text-2xl">{{ __('roleui.video_requests_title') }}</h1>
            <p class="mt-2 text-sm font-bold text-slate-500">{{ __('roleui.video_requests_subtitle') }}</p>
            <p class="mt-3 text-xs font-medium leading-relaxed text-slate-600">{{ __('roleui.doctor_video_requests_hint') }}</p>
        </div>

        <div class="overflow-hidden rounded-[2.5rem] border border-slate-100 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">{{ __('roleui.video_requests_table_heading') }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                            <th class="px-6 py-3">{{ __('roleui.video_requests_col_patient') }}</th>
                            <th class="px-6 py-3">{{ __('roleui.video_requests_col_status') }}</th>
                            <th class="px-6 py-3">{{ __('roleui.video_requests_col_started') }}</th>
                            <th class="px-6 py-3">{{ __('roleui.video_requests_col_action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($videoRequests as $session)
                            @php($callStatus = (string) ($session->video_call_status ?? 'joined'))
                            <tr @class(['bg-amber-50/60' => $callStatus === 'ringing'])>
                                <td class="px-6 py-3 text-sm font-black text-slate-800">
                                    {{ $videoPatientNames[$session->patient_id] ?? __('roleui.video_requests_unknown_patient') }}
                                </td>
                                <td class="px-6 py-3">
                                    @if ($callStatus === 'ringing')
                                        <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-amber-800">{{ __('roleui.video_requests_status_ringing') }}</span>
                                    @elseif ($callStatus === 'missed')
                                        <span class="inline-flex rounded-full bg-slate-200 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-slate-700">{{ __('roleui.video_requests_status_missed') }}</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-emerald-800">{{ __('roleui.video_requests_status_joined') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-xs font-bold text-slate-500">
                                    {{ $session->start_time?->diffForHumans() ?? '—' }}
                                </td>
                                <td class="px-6 py-3">
                                    <a
                                        href="{{ route('doctor.video-consult', ['room' => $session->room_id]) }}"
                                        class="inline-flex rounded-xl bg-blue-600 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-blue-700"
                                    >
                                        {{ __('roleui.video_requests_join') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-sm font-bold text-slate-400">
                                    {{ __('roleui.video_requests_empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endcomponent
