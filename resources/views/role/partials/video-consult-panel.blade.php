<style>
    .video-container { position: relative; width: 100%; padding-top: 56.25%; }
    .video-frame { position: absolute; top: 0; left: 0; bottom: 0; right: 0; border-radius: 2rem; overflow: hidden; background: #0f172a; }
    .status-online { position: relative; }
    .status-online::after {
        content: ''; position: absolute; width: 14px; height: 14px; background: #10b981;
        border-radius: 50%; right: 5px; top: 5px; border: 3px solid white;
    }
</style>

<div class="sticky top-0 z-20 mx-auto mb-8 max-w-3xl rounded-2xl border border-blue-100 bg-white/95 px-6 py-3 text-center shadow-lg backdrop-blur">
    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-600">Video Alert</p>
    <p class="mt-1 text-sm font-bold text-slate-700">{{ $videoAlert ?? 'Video room ready.' }}</p>
</div>

<div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
    <div class="space-y-8 lg:col-span-8">
        <div class="video-container relative rounded-[2rem] border-[8px] border-white shadow-2xl">
            <div id="jitsi-container" class="video-frame">
                <div class="flex h-full flex-col items-center justify-center space-y-4 text-white">
                    <div class="h-16 w-16 animate-spin rounded-full border-4 border-blue-600 border-t-transparent"></div>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-60">{{ __('videoconsult.ready_link') }}</p>
                </div>
            </div>
        </div>

        <div class="relative min-h-[200px] overflow-hidden rounded-[2rem] border border-slate-100 bg-white p-8 shadow-xl sm:p-10">
            <h2 class="mb-6 flex items-center text-xs font-black uppercase tracking-[0.3em] text-blue-600">
                <i class="fas fa-brain mr-3"></i> {{ __('videoconsult.ai_title') }}
            </h2>
            <div id="summary-content">
                <p class="text-sm font-bold italic text-slate-400" id="summary-text">
                    {{ __('videoconsult.ai_placeholder') }}
                </p>
            </div>
        </div>
    </div>

    <aside class="space-y-8 lg:col-span-4">
        <div class="rounded-[2rem] border border-blue-50 bg-white p-8 text-center shadow-xl sm:p-10">
            <div class="status-online mx-auto mb-6 h-24 w-24 overflow-hidden rounded-[2rem] border-4 border-white shadow-xl">
                <img src="https://img.freepik.com/free-photo/smiling-doctor-with-stethoscope_23-2148168478.jpg" class="h-full w-full object-cover" alt="Doctor">
            </div>
            <h3 class="text-xl font-black uppercase italic text-slate-900">{{ $assignedDoctor?->name ?? __('videoconsult.doctor_name') }}</h3>
            <p class="mb-8 text-[9px] font-black uppercase tracking-widest text-blue-600">{{ $assignedDoctor ? 'Assigned Medical Doctor' : __('videoconsult.doctor_role') }}</p>

            <button id="start-btn" type="button" onclick="window.jitsiStartCall && window.jitsiStartCall()" class="w-full rounded-2xl bg-blue-600 py-6 text-xs font-black uppercase tracking-widest text-white shadow-xl transition hover:scale-105 active:scale-95">
                {{ __('videoconsult.start_call') }}
            </button>
        </div>

        <div class="relative overflow-hidden rounded-[2rem] bg-slate-900 p-8 text-white shadow-xl sm:p-10">
            <i class="fas fa-satellite-dish absolute -bottom-4 -right-4 text-6xl opacity-10"></i>
            <h4 class="mb-4 text-[10px] font-black uppercase tracking-[0.3em] text-blue-400 italic">{{ __('videoconsult.backup_title') }}</h4>
            <p class="text-xs font-medium leading-loose opacity-60">{{ __('videoconsult.backup_desc') }}</p>
        </div>
    </aside>
</div>

@include('laravel-jitsi::meet-embed', [
    'roomName' => $roomName,
    'displayName' => auth()->user()->name,
    'callInProgressLabel' => __('videoconsult.call_in_progress'),
    'afterLeaveSummaryLabel' => __('videoconsult.ai_not_ready'),
])
