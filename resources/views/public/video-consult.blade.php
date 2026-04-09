<x-layouts.public>
    <style>
        .video-container { position: relative; width: 100%; padding-top: 56.25%; }
        .video-frame { position: absolute; top: 0; left: 0; bottom: 0; right: 0; border-radius: 3rem; overflow: hidden; background: #0f172a; }
        .status-online { position: relative; }
        .status-online::after {
            content: ''; position: absolute; width: 14px; height: 14px; background: #10b981;
            border-radius: 50%; right: 5px; top: 5px; border: 3px solid white;
        }
    </style>

    <main class="max-w-7xl mx-auto py-12 px-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <div class="lg:col-span-8 space-y-8">
                <div class="video-container shadow-2xl rounded-[3.5rem] border-[12px] border-white relative">
                    <div id="jitsi-container" class="video-frame">
                        <div class="flex flex-col items-center justify-center h-full text-white space-y-4">
                            <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                            <p class="text-[10px] font-black uppercase tracking-widest opacity-60">{{ __('videoconsult.ready_link') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-10 rounded-[3rem] shadow-xl border border-slate-100 relative overflow-hidden min-h-[200px]">
                    <h2 class="text-xs font-black uppercase tracking-[0.3em] text-blue-600 mb-8 flex items-center">
                        <i class="fas fa-brain mr-3"></i> {{ __('videoconsult.ai_title') }}
                    </h2>
                    <div id="summary-content">
                        <p class="text-sm font-bold text-slate-400 italic" id="summary-text">
                            {{ __('videoconsult.ai_placeholder') }}
                        </p>
                    </div>
                </div>
            </div>

            <aside class="lg:col-span-4 space-y-8">
                <div class="bg-white p-10 rounded-[3.5rem] shadow-2xl border border-blue-50 text-center">
                    <div class="w-24 h-24 rounded-[2rem] mx-auto mb-6 border-4 border-white shadow-xl status-online overflow-hidden">
                        <img src="https://img.freepik.com/free-photo/smiling-doctor-with-stethoscope_23-2148168478.jpg" class="w-full h-full object-cover" alt="Doctor">
                    </div>
                    <h3 class="text-xl font-black text-slate-900 italic uppercase">{{ __('videoconsult.doctor_name') }}</h3>
                    <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest mb-8">{{ __('videoconsult.doctor_role') }}</p>

                    <button id="start-btn" onclick="initiateCall()" class="w-full bg-blue-600 text-white py-6 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl hover:scale-105 transition active:scale-95">
                        {{ __('videoconsult.start_call') }}
                    </button>
                </div>

                <div class="bg-slate-900 p-10 rounded-[3.5rem] text-white shadow-2xl relative overflow-hidden">
                    <i class="fas fa-satellite-dish absolute -right-4 -bottom-4 text-6xl opacity-10"></i>
                    <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-blue-400 mb-4 italic">{{ __('videoconsult.backup_title') }}</h4>
                    <p class="text-xs font-medium leading-loose opacity-60">{{ __('videoconsult.backup_desc') }}</p>
                </div>
            </aside>
        </div>
    </main>

    <script src="https://meet.jit.si/external_api.js"></script>
    <script>
        let api = null;

        function initiateCall() {
            const domain = "meet.jit.si";
            const options = {
                roomName: @json($roomName),
                width: "100%",
                height: "100%",
                parentNode: document.querySelector('#jitsi-container'),
                userInfo: { displayName: @json(auth()->user()->name) },
                configOverwrite: { startWithAudioMuted: false, enableWelcomePage: false },
                interfaceConfigOverwrite: { TOOLBAR_BUTTONS: ['microphone', 'camera', 'hangup', 'chat', 'tileview'] }
            };

            api = new JitsiMeetExternalAPI(domain, options);

            const btn = document.getElementById('start-btn');
            btn.innerText = @json(__('videoconsult.call_in_progress'));
            btn.classList.replace('bg-blue-600', 'bg-slate-400');
            btn.disabled = true;

            api.addEventListener('videoConferenceLeft', () => {
                document.getElementById('summary-text').innerText = @json(__('videoconsult.ai_not_ready'));
            });
        }
    </script>
</x-layouts.public>

