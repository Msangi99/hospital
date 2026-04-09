@php($sidebarTitle = __('roleui.owner_portal'))
@php($roleLabelMap = [
    'MEDICAL_TEAM' => __('authui.role_medical_team'),
    'PATIENT' => __('authui.role_patient'),
    'FACILITY' => __('authui.role_facility'),
    'AMBULANCE' => __('authui.role_ambulance'),
])

@component('layouts.role-dashboard', ['title' => __('roleui.owner_sidebar_workers'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.owner._sidebar', ['active' => 'workers'])
    @endslot

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <x-admin.hero
            :kicker="__('roleui.owner_sidebar_workers')"
            :title="__('roleui.owner_workers_title')"
            :description="__('roleui.owner_workers_desc')"
        >
            <x-slot:pills>
                <span class="rounded-full border border-blue-200/30 bg-blue-400/10 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-blue-100">
                    {{ __('roleui.owner_hospital_name') }}: {{ $hospital->name }}
                </span>
                <span class="rounded-full border border-emerald-200/30 bg-emerald-400/10 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-emerald-100">
                    {{ __('roleui.owner_workers_total') }}: {{ $workers->count() }}
                </span>
            </x-slot:pills>
        </x-admin.hero>

        <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-sm font-black uppercase tracking-[0.2em] text-slate-500">{{ __('roleui.owner_add_worker') }}</h2>
            <form method="POST" action="{{ route('owner.workers.store') }}" class="grid gap-4 lg:grid-cols-12">
                @csrf
                <div class="lg:col-span-4">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('authui.name_label') }}</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500">
                </div>
                <div class="lg:col-span-4">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('authui.email_label') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500">
                </div>
                <div class="lg:col-span-4">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('authui.phone_label') }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500">
                </div>
                <div class="lg:col-span-4">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.owner_worker_role') }}</label>
                    <select name="worker_role" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500">
                        @foreach($workerRoles as $role)
                            <option value="{{ $role }}" @selected(old('worker_role') === $role)>{{ $roleLabelMap[$role] ?? $role }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-4">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.users_status') }}</label>
                    <select name="status" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500">
                        @foreach($workerStatuses as $status)
                            <option value="{{ $status }}" @selected(old('status', 'ACTIVE') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-4">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('authui.password_label') }}</label>
                    <input type="password" name="password" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500">
                </div>
                <div class="lg:col-span-12">
                    <button type="submit" class="rounded-2xl bg-slate-900 px-5 py-3 text-xs font-black uppercase tracking-widest text-white transition hover:bg-slate-800">
                        {{ __('roleui.owner_add_worker') }}
                    </button>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50/70 px-6 py-4">
                <h2 class="text-sm font-black uppercase tracking-[0.2em] text-slate-500">{{ __('roleui.owner_workers_list') }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-900 text-white">
                        <tr class="text-left text-[10px] font-black uppercase tracking-[0.2em]">
                            <th class="px-6 py-4">{{ __('roleui.users_table_identity') }}</th>
                            <th class="px-6 py-4">{{ __('roleui.owner_worker_role') }}</th>
                            <th class="px-6 py-4">{{ __('roleui.users_status') }}</th>
                            <th class="px-6 py-4">{{ __('roleui.users_table_registered') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($workers as $member)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-black text-slate-900">{{ $member->user?->name }}</div>
                                    <div class="text-xs font-bold text-slate-500">{{ $member->user?->email }}</div>
                                </td>
                                <td class="px-6 py-4 text-xs font-black text-slate-700">{{ $roleLabelMap[$member->worker_role] ?? $member->worker_role }}</td>
                                <td class="px-6 py-4 text-xs font-black text-slate-500">{{ $member->status }}</td>
                                <td class="px-6 py-4 text-xs font-black text-slate-500">{{ optional($member->joined_at ?? $member->created_at)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-sm font-bold text-slate-500">{{ __('roleui.owner_workers_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endcomponent
