@php($sidebarTitle = __('roleui.medical_staff'))

@component('layouts.role-dashboard', ['title' => __('roleui.conversations_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.doctor._sidebar', ['active' => 'conversations'])
    @endslot

    <div
        class="flex min-h-[min(100dvh,38rem)] max-h-[calc(100dvh-7.5rem)] flex-col overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-slate-200/70 lg:max-h-[calc(100dvh-8rem)] lg:flex-row"
    >
        <aside
            class="flex max-h-[46vh] w-full shrink-0 flex-col border-b border-slate-200/80 bg-white lg:max-h-none lg:w-[min(100%,380px)] lg:border-b-0 lg:border-r"
        >
            <div class="shrink-0 border-b border-slate-100 px-4 py-3">
                <h1 class="text-lg font-semibold tracking-tight text-slate-900">{{ __('roleui.conversations_title') }}</h1>
                <p class="mt-0.5 text-xs text-slate-500">{{ __('roleui.conversations_doctor_inbox_hint') }}</p>
            </div>

            @if ($startablePatients->isNotEmpty())
                <div class="shrink-0 border-b border-slate-100 px-3 py-3">
                    <p class="mb-2 px-1 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        {{ __('roleui.conversations_doctor_new_chat_heading') }}
                    </p>
                    <form method="POST" action="{{ route('doctor.conversations.start') }}" class="space-y-2">
                        @csrf
                        <label class="sr-only" for="doctor-conv-patient">{{ __('roleui.conversations_doctor_new_chat_patient') }}</label>
                        <select
                            id="doctor-conv-patient"
                            name="patient_id"
                            required
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-800 outline-none transition focus:border-blue-400 focus:bg-white"
                        >
                            <option value="">{{ __('roleui.conversations_doctor_new_chat_patient') }}</option>
                            @foreach ($startablePatients as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                        <label class="sr-only" for="doctor-conv-title">{{ __('roleui.conversations_doctor_new_chat_title_optional') }}</label>
                        <input
                            id="doctor-conv-title"
                            type="text"
                            name="title"
                            maxlength="120"
                            placeholder="{{ __('roleui.conversations_doctor_new_chat_title_optional') }}"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-800 outline-none transition focus:border-blue-400"
                        />
                        <button
                            type="submit"
                            class="w-full rounded-2xl bg-[#1a73e8] py-2.5 text-center text-[10px] font-black uppercase tracking-widest text-white shadow-sm transition hover:bg-blue-700"
                        >
                            {{ __('roleui.conversations_doctor_new_chat_submit') }}
                        </button>
                    </form>
                </div>
            @endif

            <div class="min-h-0 flex-1 overflow-y-auto px-3 py-2">
                <ul class="space-y-0.5">
                    @forelse ($conversations as $conv)
                        <li>
                            <a
                                href="{{ route('doctor.conversations', ['c' => $conv->id]) }}"
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
                                >{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(trim($conv->displayTitle()), 0, 1)) }}</span>
                                <span class="min-w-0 flex-1">
                                    <span class="flex items-baseline justify-between gap-2">
                                        <span class="truncate text-sm font-semibold text-slate-900">{{ $conv->displayTitle() }}</span>
                                        <span class="shrink-0 text-[11px] text-slate-400">{{ $conv->updated_at?->format('M j') }}</span>
                                    </span>
                                    <span class="block truncate text-xs text-slate-500">{{ $conv->hospital?->name ?? '—' }}</span>
                                </span>
                            </a>
                        </li>
                    @empty
                        <li class="px-2 py-10 text-center text-sm text-slate-400">{{ __('roleui.conversations_doctor_inbox_empty') }}</li>
                    @endforelse
                </ul>
            </div>
        </aside>

        <div class="flex min-h-0 min-w-0 flex-1 flex-col bg-[#f0f4f9] p-2 sm:p-3">
            @if ($active)
                <div class="mb-2 shrink-0 rounded-2xl border border-slate-200/80 bg-white px-4 py-3 shadow-sm">
                    <form method="POST" action="{{ route('portal.conversations.title', $active) }}" class="flex flex-col gap-2 sm:flex-row sm:items-end">
                        @csrf
                        @method('PATCH')
                        <div class="min-w-0 flex-1">
                            <label for="conversation-title-input" class="mb-1 block text-[10px] font-black uppercase tracking-widest text-slate-400">
                                {{ __('roleui.conversations_doctor_chat_title_label') }}
                            </label>
                            <input
                                id="conversation-title-input"
                                type="text"
                                name="title"
                                value="{{ old('title', $active->title) }}"
                                maxlength="120"
                                placeholder="{{ $active->patient?->name }}"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-800 outline-none transition focus:border-blue-400 focus:bg-white"
                            />
                        </div>
                        <button
                            type="submit"
                            class="shrink-0 rounded-xl bg-slate-900 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-slate-800"
                        >
                            {{ __('roleui.conversations_doctor_save_title') }}
                        </button>
                    </form>
                    <p class="mt-2 text-[10px] font-medium text-slate-400">{{ __('roleui.conversations_doctor_chat_title_hint') }}</p>
                </div>
            @endif

            @include('role.partials.conversation-chat-area', [
                'active' => $active,
                'peerLabel' => $active ? __('roleui.conversations_with_patient', ['name' => $active->patient?->name ?? '']) : '',
            ])
        </div>
    </div>
@endcomponent
