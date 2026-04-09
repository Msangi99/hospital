@php($sidebarTitle = __('roleui.admin_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.admin_billing_integrations_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.admin._sidebar', ['active' => 'billing-integrations'])
    @endslot

    <div class="space-y-6">
        <div class="rounded-[2.5rem] border border-slate-100 bg-white p-5 shadow-sm sm:p-6">
            <h1 class="text-2xl font-black tracking-tighter sm:text-3xl">{{ __('roleui.admin_billing_integrations_title') }}</h1>
            <p class="mt-2 font-bold text-slate-500">{{ __('roleui.admin_billing_integrations_desc') }}</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('roleui.billing_total_users') }}</p>
                <p class="mt-2 text-3xl font-black tracking-tighter text-slate-900">{{ $stats['users'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('roleui.billing_total_facilities') }}</p>
                <p class="mt-2 text-3xl font-black tracking-tighter text-slate-900">{{ $stats['facilities'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('roleui.billing_total_subscribers') }}</p>
                <p class="mt-2 text-3xl font-black tracking-tighter text-slate-900">{{ $stats['subscribers'] }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-[2.5rem] border border-slate-100 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">{{ __('roleui.integrations_status_title') }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                            <th class="px-6 py-4">{{ __('roleui.integrations_col_name') }}</th>
                            <th class="px-6 py-4">{{ __('roleui.integrations_col_env_key') }}</th>
                            <th class="px-6 py-4">{{ __('roleui.integrations_col_status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($integrations as $integration)
                            <tr>
                                <td class="px-6 py-4 text-sm font-black text-slate-900">{{ $integration['name'] }}</td>
                                <td class="px-6 py-4 text-xs font-black text-slate-500">{{ $integration['key'] }}</td>
                                <td class="px-6 py-4">
                                    @if($integration['configured'])
                                        <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-emerald-700">{{ __('roleui.integrations_status_configured') }}</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-amber-700">{{ __('roleui.integrations_status_missing') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endcomponent

