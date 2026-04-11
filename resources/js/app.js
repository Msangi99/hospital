import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;

    return div.innerHTML;
}

window.doctorVideoToastShow = function doctorVideoToastShow(payload) {
    const host = document.getElementById('doctor-video-toast');
    const labels = window.__videoToastLabels || {};

    if (! host || ! payload) {
        return;
    }

    const patient = escapeHtml(payload.patient_name || '');
    const joinUrl = escapeHtml(payload.join_url || '#');

    host.innerHTML =
        '<div class="pointer-events-auto rounded-2xl border border-blue-100 bg-white/98 p-4 text-center shadow-2xl backdrop-blur">' +
        '<p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-600">' +
        escapeHtml(labels.title || 'Video') +
        '</p>' +
        '<p class="mt-2 text-sm font-bold text-slate-700">' +
        patient +
        '</p>' +
        '<div class="mt-4 flex flex-wrap items-center justify-center gap-2">' +
        '<a href="' +
        joinUrl +
        '" class="inline-flex rounded-xl bg-blue-600 px-5 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-white transition hover:bg-blue-700">' +
        escapeHtml(labels.join || 'Join') +
        '</a>' +
        '<button type="button" class="rounded-xl border border-slate-200 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50" data-doctor-video-dismiss>' +
        escapeHtml(labels.dismiss || 'Dismiss') +
        '</button>' +
        '</div>' +
        '</div>';

    host.classList.remove('hidden');

    host.querySelector('[data-doctor-video-dismiss]')?.addEventListener('click', function () {
        host.classList.add('hidden');
        host.innerHTML = '';
    });
};

function wireConversationChannel() {
    const cfg = document.getElementById('portal-conversation-config');
    if (! cfg || ! window.Echo) {
        return;
    }

    const id = cfg.dataset.activeId;
    const currentUserId = parseInt(cfg.dataset.currentUserId || '0', 10);
    if (! id) {
        return;
    }

    window.Echo.private('conversation.' + id).listen('.ConversationMessageSent', function (e) {
        if (parseInt(e.user_id, 10) === currentUserId) {
            return;
        }

        appendConversationMessage(e, currentUserId);
    });
}

function appendConversationMessage(e, currentUserId) {
    const box = document.getElementById('portal-conversation-messages');
    if (! box) {
        return;
    }

    const mine = parseInt(e.user_id, 10) === currentUserId;
    const wrap = document.createElement('div');
    wrap.className = 'flex ' + (mine ? 'justify-end' : 'justify-start');
    wrap.innerHTML =
        '<div class="max-w-[85%] rounded-2xl px-4 py-2 text-sm ' +
        (mine ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-800') +
        '">' +
        '<p class="text-[10px] font-black uppercase tracking-widest opacity-70">' +
        escapeHtml(e.user_name || '') +
        '</p>' +
        '<p class="mt-1 whitespace-pre-wrap font-medium">' +
        escapeHtml(e.body || '') +
        '</p>' +
        '</div>';

    box.appendChild(wrap);
    box.scrollTop = box.scrollHeight;
}

document.addEventListener('DOMContentLoaded', function () {
    const key = import.meta.env.VITE_REVERB_APP_KEY;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (! key || ! csrf) {
        return;
    }

    const host = import.meta.env.VITE_REVERB_HOST ?? '127.0.0.1';
    const port = import.meta.env.VITE_REVERB_PORT ?? '8080';
    const scheme = import.meta.env.VITE_REVERB_SCHEME ?? 'http';

    if (! window.Echo) {
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: key,
            wsHost: host,
            wsPort: port,
            wssPort: port,
            forceTLS: scheme === 'https',
            enabledTransports: ['ws', 'wss'],
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': csrf ?? '',
                },
            },
        });
    }

    const toastMount = document.getElementById('doctor-video-toast');
    const doctorId = document.querySelector('meta[name="doctor-broadcast-id"]')?.getAttribute('content');
    if (toastMount && doctorId) {
        window.Echo.private('doctor.' + doctorId).listen('.VideoConsultationRequested', function (e) {
            window.doctorVideoToastShow({
                patient_name: e.patient_name,
                join_url: e.join_url,
            });
        });
    }

    wireConversationChannel();
});
