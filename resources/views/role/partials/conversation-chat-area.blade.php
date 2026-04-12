@php
    $active = $active ?? null;
    $peerLabel = $peerLabel ?? '';
    $portalViewer = $portalViewer ?? null;
    $chatHeadline = '';
    $chatSubline = '';
    $peerInitial = '?';
    if ($active) {
        $uid = (int) auth()->id();
        if ($portalViewer === null) {
            if ((int) $active->patient_id === $uid) {
                $portalViewer = 'patient';
            } elseif ((int) $active->doctor_id === $uid) {
                $portalViewer = 'doctor';
            } else {
                $portalViewer = 'nurse';
            }
        }
        if ($portalViewer === 'nurse') {
            $chatHeadline = $active->displayTitle();
            $chatSubline = (string) __('roleui.conversations_nurse_thread_subtitle', [
                'patient' => $active->patient?->name ?? '—',
                'doctor' => $active->doctor?->name ?? '—',
            ]);
            $peerInitial = trim($chatHeadline) !== ''
                ? \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(trim($chatHeadline), 0, 1))
                : '?';
        } else {
            $peerUser = (int) $active->patient_id === $uid ? $active->doctor : $active->patient;
            $chatHeadline = (string) ($peerUser?->name ?? $peerLabel);
            $chatSubline = $active->hospital
                ? (string) $active->hospital->name
                : (string) __('roleui.conversations_network_sublabel');
            $peerInitial = $peerUser?->name
                ? \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(trim((string) $peerUser->name), 0, 1))
                : '?';
        }
    }
@endphp

@if ($active)
    <div
        id="portal-conversation-config"
        class="hidden"
        data-active-id="{{ $active->id }}"
        data-current-user-id="{{ auth()->id() }}"
        data-post-url="{{ route('portal.conversations.messages', $active) }}"
        data-peer-initial="{{ $peerInitial }}"
        data-voice-unsupported="{{ e(__('roleui.conversations_voice_unsupported')) }}"
        data-voice-recording="{{ e(__('roleui.conversations_voice_recording')) }}"
        data-voice-ready="{{ e(__('roleui.conversations_voice_ready')) }}"
        data-voice-denied="{{ e(__('roleui.conversations_voice_denied')) }}"
    ></div>
@endif

<div class="portal-chat-shell flex min-h-[min(100dvh,36rem)] max-h-[calc(100dvh-7.5rem)] flex-1 flex-col overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-slate-200/70">
    {{-- App bar --}}
    <header class="flex shrink-0 items-center gap-3 border-b border-slate-200/90 bg-white px-4 py-3 sm:px-5">
        @if ($active)
            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#1a73e8] to-blue-700 text-sm font-bold text-white shadow-sm"
                aria-hidden="true"
            >
                {{ $peerInitial }}
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="truncate text-base font-semibold tracking-tight text-slate-900">{{ $chatHeadline }}</h2>
                <p class="truncate text-xs text-slate-500">{{ $chatSubline }}</p>
            </div>
        @else
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-200 text-sm font-bold text-slate-500" aria-hidden="true">
                <i class="fas fa-comments text-slate-500"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="text-base font-semibold text-slate-900">{{ __('roleui.conversations_title') }}</h2>
                <p class="text-xs text-slate-500">{{ __('roleui.conversations_select_chat') }}</p>
            </div>
        @endif
    </header>

    {{-- Thread --}}
    <div
        id="portal-conversation-messages"
        class="portal-chat-thread min-h-0 flex-1 space-y-1 overflow-y-auto overflow-x-hidden bg-[#f0f4f9] px-2 py-4 sm:px-4"
        role="log"
        aria-live="polite"
    >
        @if ($active)
            @if ($active->messages->isEmpty())
                <div class="flex flex-col items-center justify-center px-6 py-16 text-center" data-thread-empty-placeholder>
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-slate-200/80">
                        <i class="fas fa-hand-sparkles text-2xl text-[#1a73e8]"></i>
                    </div>
                    <p class="max-w-xs text-sm font-medium text-slate-600">{{ __('roleui.conversations_thread_empty') }}</p>
                </div>
            @endif
            @foreach ($active->messages as $msg)
                @include('role.partials.conversation-message-bubble', [
                    'msg' => $msg,
                    'mine' => (int) $msg->user_id === (int) auth()->id(),
                    'conversation' => $active,
                ])
            @endforeach
        @else
            <div class="flex flex-1 flex-col items-center justify-center px-6 py-20 text-center">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-slate-200/80">
                    <i class="fas fa-message text-3xl text-slate-300"></i>
                </div>
                <p class="max-w-sm text-sm font-medium text-slate-600">{{ __('roleui.conversations_select_chat') }}</p>
            </div>
        @endif
    </div>

    @if ($active)
        <form
            id="portal-conversation-form"
            method="POST"
            action="{{ route('portal.conversations.messages', $active) }}"
            enctype="multipart/form-data"
            class="shrink-0 border-t border-slate-200/90 bg-white px-3 py-3 sm:px-4 sm:py-3"
        >
            @csrf
            <input type="hidden" id="portal-attachment-kind" value="">
            <input type="file" id="portal-attachment-file" class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.gif,.webp,application/pdf">

            <p id="portal-attachment-preview" class="mb-2 hidden rounded-xl bg-slate-100 px-3 py-2 text-xs font-medium text-slate-600 ring-1 ring-slate-200/80"></p>
            <p id="portal-voice-status" class="mb-2 hidden text-center text-xs font-semibold text-red-600"></p>

            <label class="sr-only" for="portal-conversation-body">{{ __('roleui.conversations_message_placeholder') }}</label>
            <div class="flex flex-wrap items-end gap-2">
                <div class="flex shrink-0 gap-1">
                    <button
                        type="button"
                        id="portal-attach-doc-btn"
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-600 ring-1 ring-slate-200/80 transition hover:bg-slate-200 hover:text-[#1a73e8]"
                        title="{{ __('roleui.conversations_attach_document') }}"
                        aria-label="{{ __('roleui.conversations_attach_document') }}"
                    >
                        <i class="fas fa-paperclip" aria-hidden="true"></i>
                    </button>
                    <button
                        type="button"
                        id="portal-voice-btn"
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-600 ring-1 ring-slate-200/80 transition hover:bg-slate-200 hover:text-[#1a73e8]"
                        title="{{ __('roleui.conversations_voice_note') }}"
                        aria-label="{{ __('roleui.conversations_voice_note') }}"
                    >
                        <i class="fas fa-microphone" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="relative min-h-[3rem] min-w-0 flex-1 rounded-[1.5rem] bg-slate-100 ring-1 ring-slate-200/80 transition focus-within:bg-white focus-within:ring-2 focus-within:ring-[#1a73e8]/25">
                    <textarea
                        id="portal-conversation-body"
                        name="body"
                        rows="1"
                        maxlength="5000"
                        placeholder="{{ __('roleui.conversations_message_placeholder') }}"
                        class="max-h-36 min-h-[3rem] w-full resize-none rounded-[1.5rem] border-0 bg-transparent px-4 py-3 pr-3 text-[0.9375rem] leading-snug text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-0"
                    ></textarea>
                </div>
                <button
                    type="submit"
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#1a73e8] text-white shadow-md transition hover:bg-blue-700 hover:shadow-lg active:scale-95"
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
