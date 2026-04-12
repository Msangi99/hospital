@php($sidebarTitle = __('roleui.medical_staff'))

@component('layouts.role-dashboard', ['title' => __('roleui.nurse_doctor_coordination_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.doctor._sidebar', ['active' => 'nurse_doctor_coordination'])
    @endslot

    <div
        class="flex min-h-[min(100dvh,38rem)] max-h-[calc(100dvh-7.5rem)] flex-col overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-slate-200/70 lg:max-h-[calc(100dvh-8rem)] lg:flex-row"
    >
        <aside
            class="flex max-h-[46vh] w-full shrink-0 flex-col border-b border-slate-200/80 bg-white lg:max-h-none lg:w-[min(100%,380px)] lg:border-b-0 lg:border-r"
        >
            <div class="shrink-0 border-b border-slate-100 px-4 py-3">
                <h1 class="text-lg font-semibold tracking-tight text-slate-900">{{ __('roleui.nurse_doctor_coordination_title') }}</h1>
                <p class="mt-0.5 text-xs text-slate-500">{{ __('roleui.nurse_doctor_coordination_hint') }}</p>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto px-3 py-2">
                <ul class="space-y-0.5">
                    @forelse ($chats as $c)
                        <li>
                            <a
                                href="{{ route('nurse.doctor-coordination', ['cc' => $c->id]) }}"
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
                                    <span class="block truncate text-xs text-slate-500">{{ $c->doctor?->name }}</span>
                                </span>
                            </a>
                        </li>
                    @empty
                        <li class="px-2 py-10 text-center text-sm text-slate-400">{{ __('roleui.nurse_doctor_coordination_empty') }}</li>
                    @endforelse
                </ul>
            </div>
        </aside>

        <div class="flex min-h-0 min-w-0 flex-1 flex-col bg-[#f0f4f9] p-2 sm:p-3">
            @include('role.partials.doctor-nurse-coordination-chat-area', [
                'active' => $active,
                'viewerRole' => 'nurse',
            ])
        </div>
    </div>
@endcomponent
