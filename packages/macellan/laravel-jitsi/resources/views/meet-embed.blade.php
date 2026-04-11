@props([
    'roomName' => '',
    'displayName' => '',
    'containerId' => 'jitsi-container',
    'startButtonId' => 'start-btn',
    'toolbarButtons' => ['microphone', 'camera', 'hangup', 'chat', 'tileview'],
    'callInProgressLabel' => 'Call in progress',
    'afterLeaveSummaryLabel' => '',
])
@php
    $domain = config('jitsi.domain', 'meet.jit.si');
    $apiUrl = config('jitsi.external_api_url', 'https://meet.jit.si/external_api.js');
@endphp
<script src="{{ $apiUrl }}"></script>
<script>
(function () {
    const domain = @json($domain);
    const roomName = @json($roomName);
    const displayName = @json($displayName);
    const containerSelector = @json('#'.$containerId);
    const startButtonId = @json($startButtonId);
    const toolbarButtons = @json($toolbarButtons);
    let api = null;

    window.jitsiStartCall = function jitsiStartCall() {
        const parent = document.querySelector(containerSelector);
        if (!parent) return;

        const options = {
            roomName: roomName,
            width: '100%',
            height: '100%',
            parentNode: parent,
            userInfo: { displayName: displayName },
            configOverwrite: { startWithAudioMuted: false, enableWelcomePage: false },
            interfaceConfigOverwrite: { TOOLBAR_BUTTONS: toolbarButtons },
        };

        api = new JitsiMeetExternalAPI(domain, options);

        const btn = document.getElementById(startButtonId);
        if (btn) {
            btn.innerText = @json($callInProgressLabel);
            btn.classList.replace('bg-blue-600', 'bg-slate-400');
            btn.disabled = true;
        }

        api.addEventListener('videoConferenceLeft', function () {
            const summary = document.getElementById('summary-text');
            const leaveMsg = @json($afterLeaveSummaryLabel);
            if (summary && leaveMsg) {
                summary.innerText = leaveMsg;
            }
        });
    };
})();
</script>
