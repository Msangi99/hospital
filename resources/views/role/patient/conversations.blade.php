@php($sidebarTitle = __('roleui.patient_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.conversations_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.patient._sidebar', ['active' => 'conversations'])
    @endslot

    <div
        class="flex min-h-[min(100dvh,38rem)] max-h-[calc(100dvh-7.5rem)] flex-col overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-slate-200/70 lg:max-h-[calc(100dvh-8rem)] lg:flex-row"
    >
        {{-- Thread list (Messages-style rail) --}}
        <aside
            class="flex max-h-[42vh] w-full shrink-0 flex-col border-b border-slate-200/80 bg-white lg:max-h-none lg:w-[min(100%,380px)] lg:border-b-0 lg:border-r"
        >
            <div class="shrink-0 border-b border-slate-100 px-4 py-3">
                <h1 class="text-lg font-semibold tracking-tight text-slate-900">{{ __('roleui.conversations_title') }}</h1>
                <p class="mt-0.5 text-xs text-slate-500">{{ __('roleui.conversations_pick_doctor') }}</p>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto">
                @if ($doctors->isNotEmpty())
                    <div class="border-b border-slate-100 px-3 py-2">
                        <p class="px-2 py-1 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                            {{ __('roleui.conversations_section_start') }}
                        </p>
                        <ul class="space-y-0.5">
                            @foreach ($doctors as $doctor)
                                <li>
                                    <form method="POST" action="{{ route('patient.conversations.start') }}" class="block">
                                        @csrf
                                        <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                                        <button
                                            type="submit"
                                            class="flex w-full items-center gap-3 rounded-2xl px-2 py-2.5 text-left transition hover:bg-slate-50"
                                        >
                                            <span
                                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-sm font-bold text-white shadow-sm"
                                            >{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(trim($doctor->name), 0, 1)) }}</span>
                                            <span class="min-w-0 flex-1">
                                                <span class="block truncate text-sm font-semibold text-slate-900">{{ $doctor->name }}</span>
                                                <span class="block truncate text-xs text-slate-500">{{ $doctor->getAttribute('hospital_display_name') }}</span>
                                            </span>
                                            <i class="fas fa-chevron-right text-xs text-slate-300" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="px-4 py-6">
                        <p class="rounded-2xl bg-amber-50 px-4 py-3 text-sm font-medium leading-relaxed text-amber-900 ring-1 ring-amber-100">
                            {{ __('roleui.conversations_no_hospital') }}
                        </p>
                    </div>
                @endif

                <div class="px-3 py-2">
                    <p class="px-2 py-1 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        {{ __('roleui.conversations_your_chats') }}
                    </p>
                    <ul class="space-y-0.5">
                        @forelse ($conversations as $conv)
                            <li>
                                <a
                                    href="{{ route('patient.conversations', ['c' => $conv->id]) }}"
                                    @class([
                                        'flex items-center gap-3 rounded-2xl px-2 py-2.5 transition',
                                        'bg-[#e8f1ff] ring-1 ring-[#1a73e8]/15' => (int) $activeId === (int) $conv->id,
                                        'hover:bg-slate-50' => (int) $activeId !== (int) $conv->id,
                                    ])
                                >
                                    <span
                                        @class([
                                            'flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-sm font-bold text-white shadow-sm',
                                            'bg-gradient-to-br from-[#1a73e8] to-blue-700' => (int) $activeId === (int) $conv->id,
                                            'bg-gradient-to-br from-slate-500 to-slate-700' => (int) $activeId !== (int) $conv->id,
                                        ])
                                    >{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(trim((string) $conv->doctor?->name), 0, 1)) }}</span>
                                    <span class="min-w-0 flex-1">
                                        <span class="flex items-baseline justify-between gap-2">
                                            <span class="truncate text-sm font-semibold text-slate-900">{{ $conv->doctor?->name }}</span>
                                            <span class="shrink-0 text-[11px] text-slate-400">{{ $conv->updated_at?->format('M j') }}</span>
                                        </span>
                                        <span class="block truncate text-xs text-slate-500">
                                            @if (trim((string) ($conv->title ?? '')) !== '')
                                                {{ $conv->title }}
                                            @else
                                                {{ $conv->hospital?->name ?? '—' }}
                                            @endif
                                        </span>
                                    </span>
                                </a>
                            </li>
                        @empty
                            <li class="px-2 py-6 text-center text-sm text-slate-400">{{ __('roleui.conversations_select_chat') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </aside>

        <div class="flex min-h-0 min-w-0 flex-1 flex-col bg-[#f0f4f9] p-2 sm:p-3">
            @include('role.partials.conversation-chat-area', [
                'active' => $active,
                'peerLabel' => $active ? __('roleui.conversations_with_doctor', ['name' => $active->doctor?->name ?? '']) : '',
            ])
        </div>
    </div>
@endcomponent
