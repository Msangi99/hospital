@php($sidebarTitle = __('roleui.medical_staff'))

@component('layouts.role-dashboard', ['title' => __('roleui.doctor_nurse_coordination_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.doctor._sidebar', ['active' => 'nurse_coordination'])
    @endslot

    <div
        class="flex min-h-[min(100dvh,38rem)] max-h-[calc(100dvh-7.5rem)] flex-col overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-slate-200/70 lg:max-h-[calc(100dvh-8rem)] lg:flex-row"
    >
        <aside
            class="flex max-h-[52vh] w-full shrink-0 flex-col border-b border-slate-200/80 bg-white lg:max-h-none lg:w-[min(100%,380px)] lg:border-b-0 lg:border-r"
        >
            <div class="shrink-0 border-b border-slate-100 px-4 py-3">
                <h1 class="text-lg font-semibold tracking-tight text-slate-900">{{ __('roleui.doctor_nurse_coordination_title') }}</h1>
                <p class="mt-0.5 text-xs text-slate-500">{{ __('roleui.doctor_nurse_coordination_hint') }}</p>
            </div>

            @if ($nurses->isNotEmpty())
                <div class="shrink-0 border-b border-slate-100 px-3 py-3">
                    <p class="mb-2 px-1 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        {{ __('roleui.coordination_new_heading') }}
                    </p>
                    <form method="POST" action="{{ route('doctor.nurse-coordination.start') }}" class="space-y-2">
                        @csrf
                        <label class="sr-only" for="coord-nurse">{{ __('roleui.coordination_select_nurse') }}</label>
                        <select
                            id="coord-nurse"
                            name="nurse_id"
                            required
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white"
                        >
                            <option value="">{{ __('roleui.coordination_select_nurse') }}</option>
                            @foreach ($nurses as $n)
                                <option value="{{ $n->id }}">{{ $n->name }}</option>
                            @endforeach
                        </select>
                        @if ($patients->isNotEmpty())
                            <label class="sr-only" for="coord-patient">{{ __('roleui.coordination_select_patient_optional') }}</label>
                            <select
                                id="coord-patient"
                                name="patient_id"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-800 outline-none transition focus:border-emerald-500"
                            >
                                <option value="">{{ __('roleui.coordination_select_patient_optional') }}</option>
                                @foreach ($patients as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        @endif
                        <label class="sr-only" for="coord-context">{{ __('roleui.coordination_patient_name_label') }}</label>
                        <input
                            id="coord-context"
                            type="text"
                            name="patient_context"
                            maxlength="255"
                            placeholder="{{ __('roleui.coordination_patient_name_placeholder') }}"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500"
                        />
                        <button
                            type="submit"
                            class="w-full rounded-2xl bg-emerald-600 py-2.5 text-center text-[10px] font-black uppercase tracking-widest text-white shadow-sm transition hover:bg-emerald-700"
                        >
                            {{ __('roleui.coordination_start_chat') }}
                        </button>
                    </form>
                    <p class="mt-2 px-1 text-[10px] font-medium leading-relaxed text-slate-400">{{ __('roleui.coordination_new_help') }}</p>
                </div>
            @else
                <p class="shrink-0 border-b border-slate-100 px-4 py-3 text-xs font-medium text-amber-800">{{ __('roleui.coordination_no_nurses') }}</p>
            @endif

            <div class="min-h-0 flex-1 overflow-y-auto px-3 py-2">
                <ul class="space-y-0.5">
                    @forelse ($chats as $c)
                        <li>
                            <a
                                href="{{ route('doctor.nurse-coordination', ['cc' => $c->id]) }}"
                                @class([
                                    'flex items-center gap-3 rounded-2xl px-2 py-2.5 transition',
                                    'bg-emerald-50 ring-1 ring-emerald-200' => (int) $activeId === (int) $c->id,
                                    'hover:bg-slate-50' => (int) $activeId !== (int) $c->id,
                                ])
                            >
                                <span
                                    @class([
                                        'flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-sm font-bold text-white shadow-sm',
                                        'bg-gradient-to-br from-emerald-600 to-teal-700' => (int) $activeId === (int) $c->id,
                                        'bg-gradient-to-br from-slate-500 to-slate-700' => (int) $activeId !== (int) $c->id,
                                    ])
                                >{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(trim($c->threadTitle()), 0, 1)) }}</span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-semibold text-slate-900">{{ $c->threadTitle() }}</span>
                                    <span class="block truncate text-xs text-slate-500">{{ $c->nurse?->name }}</span>
                                </span>
                            </a>
                        </li>
                    @empty
                        <li class="px-2 py-8 text-center text-sm text-slate-400">{{ __('roleui.coordination_inbox_empty') }}</li>
                    @endforelse
                </ul>
            </div>
        </aside>

        <div class="flex min-h-0 min-w-0 flex-1 flex-col bg-[#f0f4f9] p-2 sm:p-3">
            @include('role.partials.doctor-nurse-coordination-chat-area', [
                'active' => $active,
                'viewerRole' => 'doctor',
            ])
        </div>
    </div>
@endcomponent
