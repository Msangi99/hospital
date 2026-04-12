@php($sidebarTitle = __('roleui.admin_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.admin_emergencies_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.admin._sidebar', ['active' => 'emergencies'])
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

    <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-10">
        <h1 class="mb-2 text-2xl font-black tracking-tighter sm:text-3xl">{{ __('roleui.admin_emergencies_title') }}</h1>
        <p class="mb-8 font-bold text-slate-500">{{ __('roleui.admin_emergencies_desc') }}</p>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 text-[10px] font-black uppercase tracking-widest text-slate-400">
                        <th class="py-3 pr-3">#</th>
                        <th class="py-3 pr-3">{{ __('roleui.admin_emergency_col_status') }}</th>
                        <th class="py-3 pr-3">{{ __('roleui.admin_emergency_col_phone') }}</th>
                        <th class="py-3 pr-3">{{ __('roleui.admin_emergency_col_address') }}</th>
                        <th class="py-3 pr-3">{{ __('roleui.admin_emergency_col_requester') }}</th>
                        <th class="py-3 pr-3">{{ __('roleui.admin_emergency_col_assigned') }}</th>
                        <th class="py-3 pr-3">{{ __('roleui.admin_emergency_col_created') }}</th>
                        <th class="py-3">{{ __('roleui.admin_emergency_col_actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $req)
                        <tr class="border-b border-slate-50 align-top font-bold text-slate-700">
                            <td class="py-3 pr-3">{{ $req->id }}</td>
                            <td class="py-3 pr-3">{{ $req->status }}</td>
                            <td class="py-3 pr-3 whitespace-nowrap">{{ $req->phone }}</td>
                            <td class="py-3 pr-3 max-w-[12rem] truncate" title="{{ $req->address }}">{{ $req->address ?? '—' }}</td>
                            <td class="py-3 pr-3 text-xs">
                                @if ($req->requester)
                                    {{ $req->requester->name }}
                                @else
                                    <span class="text-slate-400">{{ __('roleui.admin_emergency_guest') }}</span>
                                @endif
                            </td>
                            <td class="py-3 pr-3 text-xs">
                                {{ $req->assignedTo?->name ?? '—' }}
                            </td>
                            <td class="py-3 pr-3 whitespace-nowrap text-xs">{{ $req->created_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</td>
                            <td class="py-3">
                                <div class="flex flex-col gap-2">
                                    @if ($req->status === \App\Models\SosRequest::STATUS_RECEIVED && $req->assigned_user_id === null && $ambulanceUsers->isNotEmpty())
                                        <form method="POST" action="{{ route('admin.emergencies.assign', $req) }}" class="flex flex-wrap items-center gap-2">
                                            @csrf
                                            <select name="assigned_user_id" class="max-w-[10rem] rounded-xl border border-slate-200 bg-slate-50 px-2 py-1 text-[10px] font-black uppercase tracking-wider">
                                                @foreach ($ambulanceUsers as $u)
                                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->ambulance_availability ?? 'AVAILABLE' }})</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="rounded-lg bg-slate-900 px-2 py-1 text-[10px] font-black uppercase tracking-wider text-white hover:bg-blue-600">
                                                {{ __('roleui.admin_emergency_assign') }}
                                            </button>
                                        </form>
                                    @elseif ($req->status === \App\Models\SosRequest::STATUS_RECEIVED && $req->assigned_user_id === null && $ambulanceUsers->isEmpty())
                                        <span class="text-xs font-bold text-amber-600">{{ __('roleui.admin_emergency_no_crew') }}</span>
                                    @endif
                                    @if (! $req->isTerminal())
                                        <form method="POST" action="{{ route('admin.emergencies.cancel', $req) }}" onsubmit="return confirm(@json(__('roleui.admin_emergency_cancel_confirm')));">
                                            @csrf
                                            <button type="submit" class="text-[10px] font-black uppercase tracking-wider text-red-600 hover:text-red-800">
                                                {{ __('roleui.admin_emergency_cancel') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endcomponent
