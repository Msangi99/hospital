@php($sidebarTitle = __('roleui.admin_portal'))
@php($badgeMap = [
    'SUPERADMIN' => 'bg-amber-50 text-amber-700',
    'MEDICAL_TEAM' => 'bg-blue-50 text-blue-700',
    'PATIENT' => 'bg-fuchsia-50 text-fuchsia-700',
    'FACILITY' => 'bg-emerald-50 text-emerald-700',
    'AMBULANCE' => 'bg-orange-50 text-orange-700',
])
@php($roleLabelMap = [
    'SUPERADMIN' => __('roleui.role_superadmin'),
    'MEDICAL_TEAM' => __('authui.role_medical_team'),
    'PATIENT' => __('authui.role_patient'),
    'FACILITY' => __('authui.role_facility'),
    'AMBULANCE' => __('authui.role_ambulance'),
])

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<style>
    .users-table-wrap .dataTables_wrapper {
        padding: 0.5rem 0;
    }
    .users-table-wrap .dataTables_filter,
    .users-table-wrap .dataTables_length {
        display: none;
    }
    .users-table-wrap .dataTables_info {
        font-size: 11px;
        font-weight: 800;
        color: #64748b;
        padding: 1rem 1.25rem;
    }
    .users-table-wrap .dataTables_paginate {
        padding: 0.75rem 1rem 1rem;
    }
    .users-table-wrap .dataTables_paginate .paginate_button {
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        background: #fff !important;
        margin: 0 3px !important;
        color: #334155 !important;
        font-size: 11px !important;
        font-weight: 800 !important;
    }
    .users-table-wrap .dataTables_paginate .paginate_button.current {
        background: #0f172a !important;
        color: #fff !important;
        border-color: #0f172a !important;
    }
    .users-table-wrap .dataTables_paginate .paginate_button:hover {
        background: #f8fafc !important;
        color: #0f172a !important;
        border-color: #cbd5e1 !important;
    }
</style>

@component('layouts.role-dashboard', ['title' => __('roleui.admin_users_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.admin._sidebar', ['active' => 'users'])
    @endslot

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-[2.5rem] border border-slate-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h2 class="text-sm font-black uppercase tracking-[0.2em] text-slate-500">{{ __('roleui.users_add_new_user') }}</h2>
                <button
                    type="button"
                    id="toggle-add-user"
                    class="rounded-2xl bg-blue-600 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-blue-700"
                >
                    {{ __('roleui.users_add_button') }}
                </button>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" id="add-user-form" class="hidden grid gap-3 lg:grid-cols-12">
                @csrf
                <div class="lg:col-span-4">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('authui.name_label') }}</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500" required>
                    @error('name')<p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="lg:col-span-4">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('authui.email_label') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500" required>
                    @error('email')<p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="lg:col-span-4">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('authui.phone_label') }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500">
                    @error('phone')<p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="lg:col-span-4">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.users_role') }}</label>
                    <select name="role" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500" required>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" @selected(old('role') === $role)>{{ $roleLabelMap[$role] ?? $role }}</option>
                        @endforeach
                    </select>
                    @error('role')<p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="lg:col-span-4">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.users_status') }}</label>
                    <select name="status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500">
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected(old('status', 'ACTIVE') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="lg:col-span-4">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('authui.password_label') }}</label>
                    <input type="password" name="password" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500" required>
                    @error('password')<p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="lg:col-span-12 flex flex-wrap items-center gap-3 pt-1">
                    <button type="submit" class="rounded-2xl bg-slate-900 px-5 py-3 text-xs font-black uppercase tracking-widest text-white transition hover:bg-slate-800">
                        {{ __('roleui.users_create_user') }}
                    </button>
                </div>
            </form>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <x-admin.stat-card :label="__('roleui.users_total')" :value="$stats['total']" tone="neutral" />
            <x-admin.stat-card :label="__('roleui.role_superadmin')" :value="$stats['superadmin']" tone="amber" />
            <x-admin.stat-card :label="__('roleui.users_medical_team')" :value="$stats['medical_team']" tone="blue" />
            <x-admin.stat-card :label="__('roleui.users_patients')" :value="$stats['patient']" tone="emerald" />
            <x-admin.stat-card :label="__('authui.role_facility')" :value="$stats['facility']" tone="neutral" />
        </div>

        <div class="users-table-wrap overflow-hidden rounded-[2.5rem] border border-slate-100 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-slate-50/70 p-4 sm:p-5">
                <div class="grid gap-3 lg:grid-cols-12">
                    <div class="lg:col-span-6">
                        <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.users_search') }}</label>
                        <input
                            type="text"
                            id="users-table-search"
                            placeholder="{{ __('roleui.users_search_placeholder') }}"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500"
                        >
                    </div>
                    <div class="lg:col-span-3">
                        <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.users_role') }}</label>
                        <select id="users-table-role-filter" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500">
                            <option value="">{{ __('roleui.users_all_roles') }}</option>
                            @foreach($roles as $role)
                                <option value="{{ $roleLabelMap[$role] ?? $role }}">{{ $roleLabelMap[$role] ?? $role }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="lg:col-span-3">
                        <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.users_status') }}</label>
                        <select id="users-table-status-filter" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500">
                            <option value="">{{ __('roleui.users_all_statuses') }}</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table id="admin-users-table" class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-900 text-white">
                        <tr class="text-left text-[10px] font-black uppercase tracking-[0.2em]">
                            <th class="px-6 py-4">{{ __('roleui.users_table_identity') }}</th>
                            <th class="px-6 py-4">{{ __('roleui.users_table_role') }}</th>
                            <th class="px-6 py-4">{{ __('roleui.users_table_status') }}</th>
                            <th class="px-6 py-4">{{ __('roleui.users_table_registered') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            @php($displayName = $user->full_name ?: $user->name)
                            @php($initials = strtoupper(substr((string) $displayName, 0, 2)))
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-xs font-black text-blue-700">{{ $initials }}</div>
                                        <div>
                                            <div class="text-sm font-black text-slate-900">{{ $displayName }}</div>
                                            <div class="text-xs font-bold text-slate-400">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-widest {{ $badgeMap[$user->role] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ $roleLabelMap[$user->role] ?? $user->role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs font-black text-slate-500">{{ $user->status ?? 'ACTIVE' }}</td>
                                <td class="px-6 py-4 text-xs font-black text-slate-500">{{ optional($user->created_at)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-sm font-bold text-slate-400">
                                    {{ __('roleui.users_empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script>
        (function () {
            var form = document.getElementById('add-user-form');
            var toggle = document.getElementById('toggle-add-user');
            if (!form || !toggle) return;

            function openForm() {
                form.classList.remove('hidden');
                toggle.classList.add('hidden');
            }

            toggle.addEventListener('click', openForm);

            @if ($errors->any())
                openForm();
            @endif
        })();

        $(function () {
            var table = $('#admin-users-table').DataTable({
                pageLength: 12,
                order: [[3, 'desc']],
            });

            $('#users-table-search').on('keyup', function () {
                table.search(this.value).draw();
            });

            $.fn.dataTable.ext.search.push(function (settings, data) {
                if (settings.nTable.id !== 'admin-users-table') return true;
                var selectedRole = ($('#users-table-role-filter').val() || '').toLowerCase();
                var selectedStatus = ($('#users-table-status-filter').val() || '').toLowerCase();
                var rowRole = (data[1] || '').toLowerCase();
                var rowStatus = (data[2] || '').toLowerCase();
                var roleMatch = selectedRole === '' || rowRole.indexOf(selectedRole) !== -1;
                var statusMatch = selectedStatus === '' || rowStatus.indexOf(selectedStatus) !== -1;
                return roleMatch && statusMatch;
            });

            $('#users-table-role-filter, #users-table-status-filter').on('change', function () {
                table.draw();
            });
        });
    </script>
@endcomponent

