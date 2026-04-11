@php
    $active = $active ?? null;
    $peerLabel = $peerLabel ?? '';
@endphp

@if ($active)
    <div
        id="portal-conversation-config"
        class="hidden"
        data-active-id="{{ $active->id }}"
        data-current-user-id="{{ auth()->id() }}"
        data-post-url="{{ route('portal.conversations.messages', $active) }}"
    ></div>
@endif

<div class="flex h-[min(32rem,calc(100vh-14rem))] flex-col rounded-[2rem] border border-slate-100 bg-white shadow-sm">
    <div class="border-b border-slate-100 px-6 py-4">
        <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">{{ __('roleui.conversations_chat_column') }}</h2>
        @if ($active)
            <p class="mt-1 text-sm font-bold text-slate-800">{{ $peerLabel }}</p>
            @if ($active->hospital)
                <p class="text-[10px] font-black uppercase tracking-widest text-blue-600">{{ $active->hospital->name }}</p>
            @endif
        @else
            <p class="mt-1 text-sm font-bold text-slate-400">{{ __('roleui.conversations_select_chat') }}</p>
        @endif
    </div>

    <div id="portal-conversation-messages" class="min-h-0 flex-1 space-y-3 overflow-y-auto px-4 py-4 sm:px-6">
        @if ($active)
            @foreach ($active->messages as $msg)
                <div class="flex {{ (int) $msg->user_id === (int) auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[85%] rounded-2xl px-4 py-2 text-sm {{ (int) $msg->user_id === (int) auth()->id() ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-800' }}">
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-70">{{ $msg->user?->name }}</p>
                        <p class="mt-1 whitespace-pre-wrap font-medium">{{ $msg->body }}</p>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    @if ($active)
        <form method="POST" action="{{ route('portal.conversations.messages', $active) }}" class="border-t border-slate-100 p-4 sm:p-6">
            @csrf
            <label class="sr-only" for="portal-conversation-body">{{ __('roleui.conversations_message_placeholder') }}</label>
            <div class="flex flex-col gap-3 sm:flex-row">
                <textarea
                    id="portal-conversation-body"
                    name="body"
                    rows="2"
                    required
                    maxlength="5000"
                    placeholder="{{ __('roleui.conversations_message_placeholder') }}"
                    class="min-h-[3rem] flex-1 resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800 focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400"
                ></textarea>
                <button type="submit" class="shrink-0 rounded-2xl bg-blue-600 px-6 py-3 text-xs font-black uppercase tracking-widest text-white transition hover:bg-blue-700">
                    {{ __('roleui.conversations_send') }}
                </button>
            </div>
        </form>
    @endif
</div>
