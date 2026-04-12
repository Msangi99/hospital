@php($sidebarTitle = __('roleui.patient_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.patient_hospitals_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.patient._sidebar', ['active' => 'hospitals'])
    @endslot

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-green-100 bg-green-50 p-4 text-sm font-bold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
            <h1 class="text-xl font-black tracking-tighter text-slate-900 sm:text-2xl">{{ __('roleui.patient_hospitals_title') }}</h1>
            <p class="mt-2 text-sm font-bold text-slate-500">{{ __('roleui.patient_hospitals_intro') }}</p>
        </div>

        <div class="overflow-hidden rounded-[2.5rem] border border-slate-100 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">{{ __('roleui.patient_hospitals_table_heading') }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                            <th class="px-6 py-3">{{ __('roleui.patient_hospitals_col_name') }}</th>
                            <th class="px-6 py-3">{{ __('roleui.patient_hospitals_col_location') }}</th>
                            <th class="px-6 py-3">{{ __('roleui.patient_hospitals_col_doctors') }}</th>
                            <th class="px-6 py-3">{{ __('roleui.patient_hospitals_col_action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($hospitals as $hospital)
                            @php($isLinked = isset($linkedHospitalIds[(int) $hospital->id]))
                            <tr>
                                <td class="px-6 py-3 text-sm font-black text-slate-900">{{ $hospital->name }}</td>
                                <td class="px-6 py-3 text-xs font-bold text-slate-600">{{ $hospital->location ?? '—' }}</td>
                                <td class="px-6 py-3 text-xs font-bold text-slate-500">{{ (int) ($hospital->active_medical_team_count ?? 0) }}</td>
                                <td class="px-6 py-3">
                                    @if ($isLinked)
                                        <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-emerald-800">{{ __('roleui.patient_hospitals_linked') }}</span>
                                    @else
                                        <form method="POST" action="{{ route('patient.hospitals.join', $hospital) }}" class="inline">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="inline-flex rounded-xl bg-blue-600 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-blue-700"
                                            >
                                                {{ __('roleui.patient_hospitals_link_cta') }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-sm font-bold text-slate-400">
                                    {{ __('roleui.patient_hospitals_none') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <p class="text-xs font-medium leading-relaxed text-slate-500">{{ __('roleui.patient_hospitals_footer_hint') }}</p>
    </div>
@endcomponent
