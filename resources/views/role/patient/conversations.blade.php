@php($sidebarTitle = __('roleui.patient_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.conversations_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.patient._sidebar', ['active' => 'conversations'])
    @endslot

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        <aside class="space-y-6 lg:col-span-4">
            <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">{{ __('roleui.conversations_people_column') }}</h2>
                <p class="mt-2 text-sm font-bold text-slate-600">{{ __('roleui.conversations_pick_doctor') }}</p>

                @if ($doctors->isEmpty())
                    <p class="mt-4 text-sm font-bold text-slate-400">{{ __('roleui.conversations_no_hospital') }}</p>
                @else
                    <ul class="mt-4 space-y-2">
                        @foreach ($doctors as $doctor)
                            <li>
                                <form method="POST" action="{{ route('patient.conversations.start') }}" class="block">
                                    @csrf
                                    <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                                    <button
                                        type="submit"
                                        class="flex w-full flex-col rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3 text-left transition hover:border-blue-200 hover:bg-blue-50/50"
                                    >
                                        <span class="text-sm font-black text-slate-900">{{ $doctor->name }}</span>
                                        <span class="text-[10px] font-black uppercase tracking-widest text-blue-600">{{ $doctor->getAttribute('hospital_display_name') }}</span>
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">{{ __('roleui.conversations_your_chats') }}</h3>
                <ul class="mt-4 space-y-2">
                    @forelse ($conversations as $conv)
                        <li>
                            <a
                                href="{{ route('patient.conversations', ['c' => $conv->id]) }}"
                                class="flex flex-col rounded-2xl border px-4 py-3 text-left transition {{ (int) $activeId === (int) $conv->id ? 'border-blue-400 bg-blue-50' : 'border-slate-100 bg-slate-50 hover:border-blue-200' }}"
                            >
                                <span class="text-sm font-black text-slate-900">{{ $conv->doctor?->name }}</span>
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
                'peerLabel' => $active ? __('roleui.conversations_with_doctor', ['name' => $active->doctor?->name ?? '']) : '',
            ])
        </div>
    </div>
@endcomponent
