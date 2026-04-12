@php($sidebarTitle = __('roleui.owner_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.owner_dashboard_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.owner._sidebar', ['active' => 'dashboard'])
    @endslot

    @if (session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    @php($isPending = (string) (auth()->user()?->status ?? '') !== 'ACTIVE')
    @if ($isPending)
        <div class="mb-6 rounded-2xl border border-amber-100 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-800">
            {{ __('roleui.owner_verification_pending_desc') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-8">
            <h1 class="mb-2 text-2xl font-black tracking-tighter sm:text-3xl">{{ __('roleui.owner_dashboard_title') }}</h1>
            <p class="mb-6 font-bold text-slate-500">{{ __('roleui.owner_dashboard_desc') }}</p>
            <div class="space-y-4">
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.owner_hospital_name') }}</p>
                    <p class="text-lg font-black text-slate-900">{{ $hospital?->name ?? '—' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.facility_verification_status') }}</p>
                    <p class="text-lg font-black text-blue-600">{{ $hospital?->verification_status ?? '—' }}</p>
                    @if ($hospital?->kyc_submitted_at)
                        <p class="mt-1 text-xs font-bold text-slate-500">{{ __('roleui.owner_kyc_last_submitted') }}: {{ $hospital->kyc_submitted_at->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</p>
                    @endif
                </div>
                <a href="{{ route('owner.profile') }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-xs font-black uppercase tracking-widest text-white transition hover:bg-slate-900">
                    {{ __('roleui.owner_go_hospital_profile') }}
                </a>
            </div>
        </div>

        <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-8">
            <h2 class="mb-2 text-xl font-black tracking-tighter">{{ __('roleui.owner_kyc_panel_title') }}</h2>
            <p class="mb-6 text-sm font-bold text-slate-500">{{ __('roleui.owner_kyc_panel_desc') }}</p>
            @if (! $hospital)
                <p class="font-bold text-amber-700">{{ __('roleui.owner_kyc_no_hospital') }}</p>
            @elseif ((string) $hospital->verification_status === 'APPROVED')
                <p class="font-bold text-emerald-700">{{ __('roleui.owner_kyc_already_approved') }}</p>
            @else
                <form method="POST" action="{{ route('owner.kyc.submit') }}" class="space-y-4">
                    @csrf
                    <p class="text-sm font-bold text-slate-600">{{ __('roleui.owner_kyc_submit_hint') }}</p>
                    <button type="submit" class="rounded-2xl bg-slate-900 px-5 py-3 text-xs font-black uppercase tracking-widest text-white transition hover:bg-blue-600">
                        {{ __('roleui.owner_kyc_submit_button') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
@endcomponent
