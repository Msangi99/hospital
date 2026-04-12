@php
    $mine = (bool) $mine;
    $url = $msg->hasAttachment() ? $msg->attachmentDownloadUrl($chat) : null;
@endphp
@if ($mine)
    <div class="flex justify-end px-1 py-0.5 sm:px-2">
        <div class="max-w-[min(85%,28rem)]">
            <div
                class="rounded-[1.35rem] rounded-br-md bg-[#1a73e8] px-4 py-2.5 text-[0.9375rem] leading-snug text-white shadow-sm"
            >
                @if ($msg->hasAttachment() && $msg->attachment_kind === 'voice' && $url)
                    <audio class="w-full min-w-[200px] max-w-full" controls preload="metadata" src="{{ $url }}">
                        {{ __('roleui.conversations_audio_fallback') }}
                    </audio>
                @elseif ($msg->hasAttachment() && $msg->attachment_kind === 'document' && $url)
                    <a
                        href="{{ $url }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-3 py-2 text-sm font-semibold text-white underline-offset-2 hover:underline"
                    >
                        <i class="fas fa-file-lines" aria-hidden="true"></i>
                        <span class="truncate">{{ $msg->attachment_original_name ?? __('roleui.conversations_document') }}</span>
                    </a>
                @endif
                @if (filled($msg->body))
                    <p @class(['mt-2 whitespace-pre-wrap break-words font-normal', 'border-t border-white/20 pt-2' => $msg->hasAttachment()])>{{ $msg->body }}</p>
                @endif
            </div>
            <p class="mt-1 pr-1 text-right text-[11px] font-medium text-slate-500/90">
                {{ $msg->created_at?->format('g:i A') }}
            </p>
        </div>
    </div>
@else
    <div class="flex justify-start gap-2 px-1 py-0.5 sm:px-2">
        <div
            class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-xs font-bold text-slate-600 shadow-sm ring-1 ring-slate-200/80"
            aria-hidden="true"
        >
            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(trim((string) ($msg->user?->name ?? '?')), 0, 1)) }}
        </div>
        <div class="max-w-[min(85%,28rem)]">
            <p class="mb-0.5 pl-1 text-[11px] font-medium text-slate-500">{{ $msg->user?->name }}</p>
            <div
                class="rounded-[1.35rem] rounded-tl-md border border-slate-200/80 bg-white px-4 py-2.5 text-[0.9375rem] leading-snug text-slate-900 shadow-sm"
            >
                @if ($msg->hasAttachment() && $msg->attachment_kind === 'voice' && $url)
                    <audio class="w-full min-w-[200px] max-w-full" controls preload="metadata" src="{{ $url }}">
                        {{ __('roleui.conversations_audio_fallback') }}
                    </audio>
                @elseif ($msg->hasAttachment() && $msg->attachment_kind === 'document' && $url)
                    <a
                        href="{{ $url }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-3 py-2 text-sm font-semibold text-[#1a73e8] ring-1 ring-slate-200/80 hover:bg-slate-200"
                    >
                        <i class="fas fa-file-lines" aria-hidden="true"></i>
                        <span class="truncate">{{ $msg->attachment_original_name ?? __('roleui.conversations_document') }}</span>
                    </a>
                @endif
                @if (filled($msg->body))
                    <p @class(['mt-2 whitespace-pre-wrap break-words font-normal text-slate-900', 'border-t border-slate-200 pt-2' => $msg->hasAttachment()])>{{ $msg->body }}</p>
                @endif
            </div>
            <p class="mt-1 pl-1 text-[11px] font-medium text-slate-500/90">
                {{ $msg->created_at?->format('g:i A') }}
            </p>
        </div>
    </div>
@endif
