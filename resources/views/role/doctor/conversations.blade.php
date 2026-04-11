@php($sidebarTitle = __('roleui.medical_staff'))

@component('layouts.role-dashboard', ['title' => __('roleui.conversations_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.doctor._sidebar', ['active' => 'conversations'])
    @endslot

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        <aside class="lg:col-span-4">
            <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">{{ __('roleui.conversations_people_column') }}</h2>
                <p class="mt-2 text-sm font-bold text-slate-600">{{ __('roleui.conversations_doctor_inbox_hint') }}</p>
                <ul class="mt-4 space-y-2">
                    @forelse ($conversations as $conv)
                        <li>
                            <a
                                href="{{ route('doctor.conversations', ['c' => $conv->id]) }}"
                                class="flex flex-col rounded-2xl border px-4 py-3 text-left transition {{ (int) $activeId === (int) $conv->id ? 'border-blue-400 bg-blue-50' : 'border-slate-100 bg-slate-50 hover:border-blue-200' }}"
                            >
                                <span class="text-sm font-black text-slate-900">{{ $conv->patient?->name }}</span>
                                <span class="text-[10px] font-black uppercase tracking-widest text-blue-600">{{ $conv->hospital?->name ?? '—' }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="text-sm font-bold text-slate-400">{{ __('roleui.conversations_select_chat') }}</li>
                    @endforelse
                </ul>
            </div>
        </aside>

        <div class="lg:col-span-8">
            @include('role.partials.conversation-chat-area', [
                'active' => $active,
                'peerLabel' => $active ? __('roleui.conversations_with_patient', ['name' => $active->patient?->name ?? '']) : '',
            ])
        </div>
    </div>
@endcomponent
