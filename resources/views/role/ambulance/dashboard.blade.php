@php($sidebarTitle = __('roleui.ambulance_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.ambulance_dispatch_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.ambulance._sidebar', ['active' => 'dashboard'])
    @endslot

    @if (session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
            {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-8 flex flex-wrap items-end justify-between gap-4 rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-8">
        <div>
            <h1 class="text-2xl font-black tracking-tighter sm:text-3xl">{{ __('roleui.ambulance_availability_title') }}</h1>
            <p class="mt-1 text-sm font-bold text-slate-500">{{ __('roleui.ambulance_availability_desc') }}</p>
        </div>
        <form method="POST" action="{{ route('ambulance.portal.availability') }}" class="flex flex-wrap items-center gap-3">
            @csrf
            <select name="ambulance_availability" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-black uppercase tracking-widest text-slate-800">
                <option value="AVAILABLE" @selected(($availability ?? 'AVAILABLE') === 'AVAILABLE')>{{ __('roleui.ambulance_status_available') }}</option>
                <option value="OFF_DUTY" @selected(($availability ?? '') === 'OFF_DUTY')>{{ __('roleui.ambulance_status_off_duty') }}</option>
            </select>
            <button type="submit" class="rounded-2xl bg-orange-600 px-5 py-3 text-xs font-black uppercase tracking-widest text-white hover:bg-slate-900">
                {{ __('roleui.ambulance_availability_save') }}
            </button>
        </form>
    </div>

    <div class="mb-10 grid gap-6 lg:grid-cols-2">
        <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-8">
            <h2 class="mb-4 text-lg font-black tracking-tighter">{{ __('roleui.ambulance_my_active') }}</h2>
            @forelse ($myActive as $req)
                @php($reqDest = rawurlencode((string) $req->latitude.','.(string) $req->longitude))
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-100 bg-slate-50 p-4 last:mb-0">
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">#{{ $req->id }} · {{ $req->status }}</p>
                        <p class="text-sm font-bold text-slate-700">{{ $req->address ?? __('roleui.ambulance_no_address') }}</p>
                        <p class="text-xs font-bold text-slate-500">{{ $req->phone }}</p>
                    </div>
                    <div class="flex flex-col items-stretch gap-2 sm:items-end">
                        <a href="{{ route('ambulance.portal.run', $req) }}" class="rounded-xl bg-slate-900 px-4 py-2 text-center text-[10px] font-black uppercase tracking-widest text-white hover:bg-orange-600">
                            {{ __('roleui.ambulance_open_run') }}
                        </a>
                        <a
                            href="https://www.google.com/maps/dir/?api=1&amp;destination={{ $reqDest }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="rounded-xl border border-orange-200 bg-white px-4 py-2 text-center text-[10px] font-black uppercase tracking-widest text-orange-800 hover:bg-orange-50"
                        >
                            {{ __('roleui.ambulance_dashboard_navigate') }}
                        </a>
                    </div>
                </div>
            @empty
                <p class="font-bold text-slate-400">{{ __('roleui.ambulance_no_active') }}</p>
            @endforelse
        </div>

        <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-8">
            <h2 class="mb-4 text-lg font-black tracking-tighter">{{ __('roleui.ambulance_open_pool') }}</h2>
            @if (($availability ?? 'AVAILABLE') !== 'AVAILABLE')
                <p class="font-bold text-amber-700">{{ __('roleui.ambulance_pool_off_duty') }}</p>
            @else
                @forelse ($openPool as $req)
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-100 bg-slate-50 p-4 last:mb-0">
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest text-slate-400">#{{ $req->id }}</p>
                            <p class="text-sm font-bold text-slate-700">{{ $req->address ?? __('roleui.ambulance_no_address') }}</p>
                            <p class="text-xs font-bold text-slate-500">{{ $req->phone }}</p>
                        </div>
                        <form method="POST" action="{{ route('ambulance.portal.claim', $req) }}">
                            @csrf
                            <button type="submit" class="rounded-xl bg-orange-600 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white hover:bg-slate-900">
                                {{ __('roleui.ambulance_claim') }}
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="font-bold text-slate-400">{{ __('roleui.ambulance_pool_empty') }}</p>
                @endforelse
            @endif
        </div>
    </div>
@endcomponent
