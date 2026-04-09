@php($sidebarTitle = __('roleui.admin_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.admin_audit_logs_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.admin._sidebar', ['active' => 'audit-logs'])
    @endslot

    <div class="space-y-4">
        <div class="rounded-[2.5rem] border border-slate-100 bg-white p-5 shadow-sm sm:p-6">
            <h1 class="text-2xl font-black tracking-tighter sm:text-3xl">{{ __('roleui.admin_audit_logs_title') }}</h1>
            <p class="mt-2 font-bold text-slate-500">{{ __('roleui.admin_audit_logs_desc') }}</p>
        </div>

        <div class="overflow-hidden rounded-[2.5rem] border border-slate-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-900 text-white">
                        <tr class="text-left text-[10px] font-black uppercase tracking-[0.2em]">
                            <th class="px-6 py-4">{{ __('roleui.audit_col_event') }}</th>
                            <th class="px-6 py-4">{{ __('roleui.audit_col_details') }}</th>
                            <th class="px-6 py-4">{{ __('roleui.audit_col_meta') }}</th>
                            <th class="px-6 py-4">{{ __('roleui.audit_col_time') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($events as $event)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-6 py-4 text-xs font-black text-slate-700">{{ $event['label'] }}</td>
                                <td class="px-6 py-4 text-xs font-bold text-slate-500">{{ $event['summary'] }}</td>
                                <td class="px-6 py-4 text-xs font-black text-slate-500">{{ $event['meta'] }}</td>
                                <td class="px-6 py-4 text-xs font-black text-slate-500">{{ optional($event['created_at'])->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-sm font-bold text-slate-400">{{ __('roleui.audit_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endcomponent

