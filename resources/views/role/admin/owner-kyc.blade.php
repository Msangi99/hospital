@php($sidebarTitle = __('roleui.admin_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.admin_owner_kyc_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.admin._sidebar', ['active' => 'owner-kyc'])
    @endslot

    <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-10">
        <h1 class="mb-2 text-2xl font-black tracking-tighter sm:text-3xl">{{ __('roleui.admin_owner_kyc_title') }}</h1>
        <p class="mb-6 font-bold text-slate-500">{{ __('roleui.admin_owner_kyc_desc') }}</p>

        <div class="mb-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-amber-100 bg-amber-50 px-4 py-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-amber-800">{{ __('roleui.admin_owner_kyc_stat_pending') }}</p>
                <p class="mt-1 text-3xl font-black text-amber-700">{{ $stats['pending_review'] ?? 0 }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-emerald-800">{{ __('roleui.admin_owner_kyc_stat_approved') }}</p>
                <p class="mt-1 text-3xl font-black text-emerald-700">{{ $stats['approved'] ?? 0 }}</p>
            </div>
            <div class="rounded-2xl border border-rose-100 bg-rose-50 px-4 py-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-rose-800">{{ __('roleui.admin_owner_kyc_stat_rejected') }}</p>
                <p class="mt-1 text-3xl font-black text-rose-700">{{ $stats['rejected'] ?? 0 }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-600">{{ __('roleui.admin_owner_kyc_stat_suspended') }}</p>
                <p class="mt-1 text-3xl font-black text-slate-800">{{ $stats['suspended'] ?? 0 }}</p>
            </div>
        </div>

        <p class="mb-6 text-xs font-black uppercase tracking-widest text-slate-400">{{ __('roleui.admin_owner_kyc_superadmin_note') }}</p>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 text-[10px] font-black uppercase tracking-widest text-slate-400">
                        <th class="py-3 pr-3">{{ __('roleui.admin_owner_kyc_col_owner') }}</th>
                        <th class="py-3 pr-3">{{ __('roleui.admin_owner_kyc_col_hospital') }}</th>
                        <th class="py-3 pr-3">{{ __('roleui.facility_verification_status') }}</th>
                        <th class="py-3 pr-3">{{ __('roleui.admin_owner_kyc_col_submitted') }}</th>
                        <th class="py-3 pr-3">{{ __('roleui.facility_license') }}</th>
                        <th class="py-3">{{ __('roleui.admin_owner_kyc_col_actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hospitals as $h)
                        <tr class="border-b border-slate-50 align-top font-bold text-slate-700">
                            <td class="py-3 pr-3">
                                <div>{{ $h->owner?->name ?? '—' }}</div>
                                <div class="text-xs font-medium text-slate-500">{{ $h->owner?->email }}</div>
                                <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">{{ __('roleui.users_status') }}: {{ $h->owner?->status ?? '—' }}</div>
                            </td>
                            <td class="py-3 pr-3">{{ $h->name }}</td>
                            <td class="py-3 pr-3">{{ $h->verification_status }}</td>
                            <td class="py-3 pr-3 text-xs whitespace-nowrap">{{ $h->kyc_submitted_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') ?? '—' }}</td>
                            <td class="py-3 pr-3 text-xs">{{ $h->license_number ?? '—' }}</td>
                            <td class="py-3">
                                <a href="{{ route('admin.facilities') }}#hospital-{{ $h->id }}" class="text-xs font-black uppercase tracking-widest text-blue-600 hover:text-slate-900">
                                    {{ __('roleui.admin_owner_kyc_open_facilities') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 font-bold text-slate-400">{{ __('roleui.admin_owner_kyc_empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endcomponent
