@php
    $active = $active ?? null;
    $viewerRole = $viewerRole ?? 'doctor';
@endphp

@if ($active)
    <div
        id="dn-coordination-config"
        class="hidden"
        data-active-id="{{ $active->id }}"
        data-current-user-id="{{ auth()->id() }}"
        data-post-url="{{ route('portal.coordination.messages', $active) }}"
        data-voice-unsupported="{{ e(__('roleui.conversations_voice_unsupported')) }}"
        data-voice-recording="{{ e(__('roleui.conversations_voice_recording')) }}"
        data-voice-ready="{{ e(__('roleui.conversations_voice_ready')) }}"
        data-voice-denied="{{ e(__('roleui.conversations_voice_denied')) }}"
    ></div>
@endif

<div class="portal-chat-shell flex min-h-[min(100dvh,36rem)] max-h-[calc(100dvh-7.5rem)] flex-1 flex-col overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-slate-200/70">
    <header class="flex shrink-0 items-center gap-3 border-b border-slate-200/90 bg-white px-4 py-3 sm:px-5">
        @if ($active)
            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-600 to-teal-700 text-sm font-bold text-white shadow-sm"
                aria-hidden="true"
            >
                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(trim($active->threadTitle()), 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="truncate text-base font-semibold tracking-tight text-slate-900">{{ $active->threadTitle() }}</h2>
                <p class="truncate text-xs text-slate-500">
                    @if ($viewerRole === 'doctor')
                        {{ __('roleui.coordination_header_with_nurse', ['name' => $active->nurse?->name ?? '—']) }}
                    @else
                        {{ __('roleui.coordination_header_with_doctor', ['name' => $active->doctor?->name ?? '—']) }}
                    @endif
                </p>
            </div>
        @else
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-200 text-sm font-bold text-slate-500" aria-hidden="true">
                <i class="fas fa-user-friends text-slate-500"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="text-base font-semibold text-slate-900">{{ __('roleui.coordination_chat_title') }}</h2>
                <p class="text-xs text-slate-500">{{ __('roleui.coordination_select_thread') }}</p>
            </div>
        @endif
    </header>

    <div
        id="dn-coordination-messages"
        class="portal-chat-thread min-h-0 flex-1 space-y-1 overflow-y-auto overflow-x-hidden bg-[#f0f4f9] px-2 py-4 sm:px-4"
        role="log"
        aria-live="polite"
    >
        @if ($active)
            @if ($active->messages->isEmpty())
                <div class="flex flex-col items-center justify-center px-6 py-16 text-center" data-dn-thread-empty-placeholder="1">
                    <p class="max-w-xs text-sm font-medium text-slate-600">{{ __('roleui.coordination_thread_empty') }}</p>
                </div>
            @endif
            @foreach ($active->messages as $msg)
                @include('role.partials.doctor-nurse-coordination-message-bubble', [
                    'msg' => $msg,
                    'chat' => $active,
                    'mine' => (int) $msg->user_id === (int) auth()->id(),
                ])
            @endforeach
        @else
            <div class="flex flex-1 flex-col items-center justify-center px-6 py-20 text-center">
                <p class="max-w-sm text-sm font-medium text-slate-600">{{ __('roleui.coordination_select_thread') }}</p>
            </div>
        @endif
    </div>

    @if ($active)
        <form
            id="dn-coordination-form"
            method="POST"
            action="{{ route('portal.coordination.messages', $active) }}"
            enctype="multipart/form-data"
            class="shrink-0 border-t border-slate-200/90 bg-white px-3 py-3 sm:px-4 sm:py-3"
        >
            @csrf
            <input type="file" id="dn-attachment-file" class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.gif,.webp,application/pdf">

            <p id="dn-attachment-preview" class="mb-2 hidden rounded-xl bg-slate-100 px-3 py-2 text-xs font-medium text-slate-600 ring-1 ring-slate-200/80"></p>
            <p id="dn-voice-status" class="mb-2 hidden text-center text-xs font-semibold text-red-600"></p>

            <label class="sr-only" for="dn-coordination-body">{{ __('roleui.conversations_message_placeholder') }}</label>
            <div class="flex flex-wrap items-end gap-2">
                <div class="flex shrink-0 gap-1">
                    <button
                        type="button"
                        id="dn-attach-doc-btn"
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-600 ring-1 ring-slate-200/80 transition hover:bg-slate-200 hover:text-emerald-700"
                        title="{{ __('roleui.conversations_attach_document') }}"
                        aria-label="{{ __('roleui.conversations_attach_document') }}"
                    >
                        <i class="fas fa-paperclip" aria-hidden="true"></i>
                    </button>
                    <button
                        type="button"
                        id="dn-voice-btn"
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-600 ring-1 ring-slate-200/80 transition hover:bg-slate-200 hover:text-emerald-700"
                        title="{{ __('roleui.conversations_voice_note') }}"
                        aria-label="{{ __('roleui.conversations_voice_note') }}"
                    >
                        <i class="fas fa-microphone" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="relative min-h-[3rem] min-w-0 flex-1 rounded-[1.5rem] bg-slate-100 ring-1 ring-slate-200/80 transition focus-within:bg-white focus-within:ring-2 focus-within:ring-emerald-500/25">
                    <textarea
                        id="dn-coordination-body"
                        name="body"
                        rows="1"
                        maxlength="5000"
                        placeholder="{{ __('roleui.coordination_message_placeholder') }}"
                        class="max-h-36 min-h-[3rem] w-full resize-none rounded-[1.5rem] border-0 bg-transparent px-4 py-3 pr-3 text-[0.9375rem] leading-snug text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-0"
                    ></textarea>
                </div>
                <button
                    type="submit"
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white shadow-md transition hover:bg-emerald-700 hover:shadow-lg active:scale-95"
                    title="{{ __('roleui.conversations_send') }}"
                    aria-label="{{ __('roleui.conversations_send') }}"
                >
                    <i class="fas fa-paper-plane translate-x-px text-sm" aria-hidden="true"></i>
                </button>
            </div>
            <p class="mt-2 text-center text-[10px] font-medium text-slate-400">{{ __('roleui.conversations_attachment_hint') }}</p>
        </form>
    @endif
</div>
