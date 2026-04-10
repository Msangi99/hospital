@php($sidebarTitle = __('roleui.medical_staff'))

@component('layouts.role-dashboard', ['title' => __('roleui.sidebar_patients'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.doctor._sidebar', ['active' => 'patients'])
    @endslot

    <div class="overflow-hidden rounded-[2.5rem] border border-slate-100 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">Assigned Patients</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr class="text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                        <th class="px-6 py-3">Patient</th>
                        <th class="px-6 py-3">Phone</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Appointments</th>
                        <th class="px-6 py-3">Last Seen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($patients as $patient)
                        <tr>
                            <td class="px-6 py-3">
                                <div class="text-sm font-black text-slate-900">{{ $patient->name }}</div>
                                <div class="text-xs font-bold text-slate-400">{{ $patient->email }}</div>
                            </td>
                            <td class="px-6 py-3 text-xs font-bold text-slate-500">{{ $patient->phone ?: '—' }}</td>
                            <td class="px-6 py-3 text-xs font-black text-slate-500">{{ $patient->status }}</td>
                            <td class="px-6 py-3 text-xs font-black text-slate-500">{{ $appointmentCounts[$patient->id] ?? 0 }}</td>
                            <td class="px-6 py-3 text-xs font-bold text-slate-500">{{ $lastSeen[$patient->id] ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm font-bold text-slate-400">No assigned patients yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endcomponent
