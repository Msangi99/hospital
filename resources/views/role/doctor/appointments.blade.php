@php($sidebarTitle = __('roleui.medical_staff'))

@component('layouts.role-dashboard', ['title' => __('roleui.sidebar_appointments'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.doctor._sidebar', ['active' => 'appointments'])
    @endslot

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-green-100 bg-green-50 p-4 text-sm font-bold text-green-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-red-100 bg-red-50 p-4 text-sm font-bold text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="mb-4 text-xs font-black uppercase tracking-[0.2em] text-slate-500">Create Appointment</h2>
            <form method="POST" action="{{ route('doctor.appointments.store') }}" class="grid gap-4 md:grid-cols-2">
                @csrf
                <div class="md:col-span-2">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Patient</label>
                    <select name="patient_id" required class="w-full rounded-2xl border border-slate-100 bg-white p-4 text-sm font-bold outline-none focus:border-blue-500">
                        <option value="">Select patient</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" @selected((int) old('patient_id') === (int) $patient->id)>
                                {{ $patient->name }} ({{ $patient->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Date</label>
                    <input type="date" name="appointment_date" value="{{ old('appointment_date', $today) }}" required class="w-full rounded-2xl border border-slate-100 bg-white p-4 text-sm font-bold outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Time</label>
                    <input type="time" name="appointment_time" value="{{ old('appointment_time') }}" required class="w-full rounded-2xl border border-slate-100 bg-white p-4 text-sm font-bold outline-none focus:border-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Reason</label>
                    <textarea name="reason" rows="3" class="w-full rounded-2xl border border-slate-100 bg-white p-4 text-sm font-bold outline-none focus:border-blue-500" placeholder="Optional consultation reason">{{ old('reason') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="rounded-2xl bg-slate-900 px-6 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-white transition hover:bg-blue-600">
                        Save Appointment
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-[2.5rem] border border-slate-100 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">Appointment History</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                            <th class="px-6 py-3">Patient</th>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Time</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Reason</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($appointments as $appointment)
                            <tr>
                                <td class="px-6 py-3 text-sm font-black text-slate-800">{{ $patientNames[$appointment->patient_id] ?? 'Unknown' }}</td>
                                <td class="px-6 py-3 text-xs font-bold text-slate-500">{{ $appointment->appointment_date }}</td>
                                <td class="px-6 py-3 text-xs font-bold text-slate-500">{{ $appointment->appointment_time }}</td>
                                <td class="px-6 py-3 text-xs font-black text-slate-500">{{ $appointment->status }}</td>
                                <td class="px-6 py-3 text-xs font-bold text-slate-500">{{ $appointment->reason ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm font-bold text-slate-400">No appointments yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endcomponent
