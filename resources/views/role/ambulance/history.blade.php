@php($sidebarTitle = __('roleui.ambulance_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.ambulance_history_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.ambulance._sidebar', ['active' => 'history'])
    @endslot

    <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-10">
        <h1 class="mb-6 text-2xl font-black tracking-tighter">{{ __('roleui.ambulance_history_title') }}</h1>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 text-[10px] font-black uppercase tracking-widest text-slate-400">
                        <th class="py-3 pr-4">#</th>
                        <th class="py-3 pr-4">{{ __('roleui.ambulance_col_status') }}</th>
                        <th class="py-3 pr-4">{{ __('roleui.ambulance_col_phone') }}</th>
                        <th class="py-3 pr-4">{{ __('roleui.ambulance_col_address') }}</th>
                        <th class="py-3">{{ __('roleui.ambulance_col_completed') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $req)
                        <tr class="border-b border-slate-50 font-bold text-slate-700">
                            <td class="py-3 pr-4">{{ $req->id }}</td>
                            <td class="py-3 pr-4">{{ $req->status }}</td>
                            <td class="py-3 pr-4">{{ $req->phone }}</td>
                            <td class="py-3 pr-4 max-w-xs truncate">{{ $req->address ?? '—' }}</td>
                            <td class="py-3">{{ $req->completed_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 font-bold text-slate-400">{{ __('roleui.ambulance_history_empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endcomponent
