@php($sidebarTitle = __('roleui.admin_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.admin_dashboard'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.admin._sidebar', ['active' => 'overview'])
    @endslot

    <div class="space-y-8">
        <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-10">
            <h1 class="mb-2 text-2xl font-black tracking-tighter sm:text-3xl">{{ __('roleui.admin_dashboard') }}</h1>
            <p class="font-bold text-slate-500">{{ __('roleui.admin_dashboard_intro') }}</p>
        </div>

        <div class="rounded-[2.5rem] border border-blue-100 bg-gradient-to-br from-blue-50 to-white p-6 shadow-xl sm:p-10">
            <div class="flex flex-wrap items-start justify-between gap-6">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.35em] text-blue-600">{{ __('roleui.admin_dashboard_kyc_kicker') }}</p>
                    <h2 class="mt-2 text-2xl font-black tracking-tighter text-slate-900">{{ __('roleui.admin_dashboard_kyc_title') }}</h2>
                    <p class="mt-2 max-w-xl text-sm font-bold text-slate-600">{{ __('roleui.admin_dashboard_kyc_desc') }}</p>
                    <dl class="mt-6 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl border border-white/80 bg-white/90 px-4 py-3 shadow-sm">
                            <dt class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.admin_dashboard_kyc_pending') }}</dt>
                            <dd class="text-2xl font-black text-amber-600">{{ $ownerHospitalKyc['pending_review'] ?? 0 }}</dd>
                        </div>
                        <div class="rounded-2xl border border-white/80 bg-white/90 px-4 py-3 shadow-sm">
                            <dt class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.admin_dashboard_kyc_owner_hospitals') }}</dt>
                            <dd class="text-2xl font-black text-slate-900">{{ $ownerHospitalKyc['with_owner_total'] ?? 0 }}</dd>
                        </div>
                        <div class="rounded-2xl border border-white/80 bg-white/90 px-4 py-3 shadow-sm">
                            <dt class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.admin_dashboard_kyc_submitted_flag') }}</dt>
                            <dd class="text-2xl font-black text-blue-600">{{ $ownerHospitalKyc['submitted_timestamp_count'] ?? 0 }}</dd>
                        </div>
                    </dl>
                </div>
                <div class="flex shrink-0 flex-col gap-3">
                    <a href="{{ route('admin.owner-kyc') }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-6 py-4 text-xs font-black uppercase tracking-widest text-white shadow-lg transition hover:bg-blue-600">
                        {{ __('roleui.admin_dashboard_kyc_cta') }}
                    </a>
                    <a href="{{ route('admin.facilities') }}" class="text-center text-[10px] font-black uppercase tracking-widest text-blue-700 hover:text-slate-900">
                        {{ __('roleui.admin_dashboard_kyc_facilities_link') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endcomponent
