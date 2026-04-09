@php($sidebarTitle = __('roleui.admin_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.admin_analytics_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.admin._sidebar', ['active' => 'analytics'])
    @endslot

    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('roleui.analytics_total_users') }}</p>
                <p class="mt-2 text-3xl font-black tracking-tighter text-slate-900">{{ $stats['users'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('roleui.analytics_total_facilities') }}</p>
                <p class="mt-2 text-3xl font-black tracking-tighter text-slate-900">{{ $stats['facilities'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('roleui.analytics_total_sos') }}</p>
                <p class="mt-2 text-3xl font-black tracking-tighter text-slate-900">{{ $stats['sos'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('roleui.analytics_total_subscribers') }}</p>
                <p class="mt-2 text-3xl font-black tracking-tighter text-slate-900">{{ $stats['subscribers'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('roleui.analytics_total_contacts') }}</p>
                <p class="mt-2 text-3xl font-black tracking-tighter text-slate-900">{{ $stats['contacts'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('roleui.analytics_total_symptoms') }}</p>
                <p class="mt-2 text-3xl font-black tracking-tighter text-slate-900">{{ $stats['symptoms'] }}</p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="overflow-hidden rounded-[2.5rem] border border-slate-100 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">{{ __('roleui.analytics_latest_users') }}</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                                <th class="px-6 py-3">{{ __('roleui.analytics_user') }}</th>
                                <th class="px-6 py-3">{{ __('roleui.users_table_role') }}</th>
                                <th class="px-6 py-3">{{ __('roleui.users_table_registered') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($latestUsers as $user)
                                <tr>
                                    <td class="px-6 py-3">
                                        <div class="text-sm font-black text-slate-900">{{ $user->name }}</div>
                                        <div class="text-xs font-bold text-slate-400">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-3 text-xs font-black text-slate-500">{{ $user->role }}</td>
                                    <td class="px-6 py-3 text-xs font-black text-slate-500">{{ optional($user->created_at)->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-sm font-bold text-slate-400">{{ __('roleui.analytics_no_users') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="overflow-hidden rounded-[2.5rem] border border-slate-100 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">{{ __('roleui.analytics_latest_sos') }}</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                                <th class="px-6 py-3">{{ __('roleui.analytics_phone') }}</th>
                                <th class="px-6 py-3">{{ __('roleui.analytics_address') }}</th>
                                <th class="px-6 py-3">{{ __('roleui.users_table_registered') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($latestSos as $sos)
                                <tr>
                                    <td class="px-6 py-3 text-xs font-black text-slate-600">{{ $sos->phone ?: '—' }}</td>
                                    <td class="px-6 py-3 text-xs font-bold text-slate-500">{{ $sos->address ?: '—' }}</td>
                                    <td class="px-6 py-3 text-xs font-black text-slate-500">{{ optional($sos->created_at)->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-sm font-bold text-slate-400">{{ __('roleui.analytics_no_sos') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endcomponent

