import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;

    return div.innerHTML;
}

window.ambulanceSosToastShow = function ambulanceSosToastShow(payload) {
    const host = document.getElementById('ambulance-sos-toast');
    const labels = window.__ambulanceSosToastLabels || {};

    if (! host || ! payload) {
        return;
    }

    const addr = escapeHtml(payload.address || '');
    const phone = escapeHtml(payload.phone || '');
    const near = payload.nearest_hospital;
    const nearLine = near
        ? escapeHtml((near.name || '') + (near.distance_km != null ? ' · ' + near.distance_km + ' km' : ''))
        : '';

    host.innerHTML =
        '<div class="pointer-events-auto rounded-2xl border border-orange-100 bg-white/98 p-4 text-center shadow-2xl backdrop-blur">' +
        '<p class="text-[10px] font-black uppercase tracking-[0.2em] text-orange-600">' +
        escapeHtml(labels.title || 'SOS') +
        '</p>' +
        '<p class="mt-2 text-xs font-bold text-slate-500">#' +
        escapeHtml(String(payload.sos_request_id || '')) +
        '</p>' +
        (nearLine
            ? '<p class="mt-1 text-[10px] font-black uppercase tracking-widest text-slate-400">' + nearLine + '</p>'
            : '') +
        (addr ? '<p class="mt-2 text-sm font-bold text-slate-700">' + addr + '</p>' : '') +
        (phone ? '<p class="mt-1 text-xs font-bold text-slate-500">' + phone + '</p>' : '') +
        '<div class="mt-4 flex flex-wrap items-center justify-center gap-2">' +
        '<a href="' +
        escapeHtml(payload.open_url || '#') +
        '" class="inline-flex rounded-xl bg-orange-600 px-5 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-white transition hover:bg-slate-900">' +
        escapeHtml(labels.cta || 'Open') +
        '</a>' +
        '<button type="button" class="rounded-xl border border-slate-200 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50" data-ambulance-sos-dismiss>' +
        escapeHtml(labels.dismiss || 'Dismiss') +
        '</button>' +
        '</div>' +
        '</div>';

    host.classList.remove('hidden');

    host.querySelector('[data-ambulance-sos-dismiss]')?.addEventListener('click', function () {
        host.classList.add('hidden');
        host.innerHTML = '';
    });
};

window.doctorVideoToastShow = function doctorVideoToastShow(payload) {
    const host = document.getElementById('doctor-video-toast');
    const labels = window.__videoToastLabels || {};

    if (! host || ! payload) {
        return;
    }

    const sessionId = payload.video_session_id != null ? String(payload.video_session_id) : '';

    if (sessionId) {
        try {
            if (window.sessionStorage?.getItem('dismissVideoToast:' + sessionId)) {
                return;
            }
        } catch (e) {
            // ignore storage errors (private mode, etc.)
        }
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
        '"' +
        (sessionId ? ' data-video-join-session="' + escapeHtml(sessionId) + '"' : '') +
        ' class="inline-flex rounded-xl bg-blue-600 px-5 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-white transition hover:bg-blue-700">' +
        escapeHtml(labels.join || 'Join') +
        '</a>' +
        '<button type="button" class="rounded-xl border border-slate-200 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50" data-doctor-video-dismiss>' +
        escapeHtml(labels.dismiss || 'Dismiss') +
        '</button>' +
        '</div>' +
        '</div>';

    host.classList.remove('hidden');

    host.querySelector('a[data-video-join-session]')?.addEventListener('click', function () {
        const id = this.getAttribute('data-video-join-session');
        if (! id) {
            return;
        }
        try {
            window.sessionStorage?.removeItem('dismissVideoToast:' + id);
        } catch (e) {
            // ignore
        }
    });

    host.querySelector('[data-doctor-video-dismiss]')?.addEventListener('click', function () {
        if (sessionId) {
            try {
                window.sessionStorage?.setItem('dismissVideoToast:' + sessionId, '1');
            } catch (e) {
                // ignore
            }
        }
        host.classList.add('hidden');
        host.innerHTML = '';
    });
};

function wireAmbulanceSosChannels() {
    const raw = document.querySelector('meta[name="ambulance-hospital-ids"]')?.getAttribute('content') || '';
    const ids = raw
        .split(',')
        .map(function (s) {
            return s.trim();
        })
        .filter(Boolean);
    const mount = document.getElementById('ambulance-sos-toast');
    if (! ids.length || ! mount || ! window.Echo) {
        return;
    }

    const seen = {};
    ids.forEach(function (hid) {
        window.Echo.private('hospital.' + hid + '.ambulance').listen('.AmbulanceSosCreated', function (e) {
            const id = e.sos_request_id;
            if (id && seen[id]) {
                return;
            }
            if (id) {
                seen[id] = true;
            }
            window.ambulanceSosToastShow({
                sos_request_id: e.sos_request_id,
                phone: e.phone,
                address: e.address,
                nearest_hospital: e.nearest_hospital,
                open_url: e.open_url,
            });
        });
    });
}

function appendDnCoordinationMessage(e, currentUserId) {
    const box = document.getElementById('dn-coordination-messages');
    if (! box) {
        return;
    }

    const emptyHint = box.querySelector('[data-dn-thread-empty-placeholder]');
    if (emptyHint) {
        emptyHint.remove();
    }

    const html = buildConversationBubbleHtml(e, currentUserId).trim();
    const tpl = document.createElement('template');
    tpl.innerHTML = html;
    const node = tpl.content.firstElementChild;
    if (node) {
        box.appendChild(node);
    }
    box.scrollTop = box.scrollHeight;
}

function wireDnCoordinationChannel() {
    const cfg = document.getElementById('dn-coordination-config');
    if (! cfg || ! window.Echo) {
        return;
    }

    const id = cfg.dataset.activeId;
    const currentUserId = parseInt(cfg.dataset.currentUserId || '0', 10);
    if (! id) {
        return;
    }

    window.Echo.private('doctor-nurse-coordination.' + id).listen('.DoctorNurseCoordinationMessageSent', function (e) {
        if (parseInt(e.user_id, 10) === currentUserId) {
            return;
        }

        appendDnCoordinationMessage(e, currentUserId);
    });
}

function initDnCoordinationComposer() {
    const form = document.getElementById('dn-coordination-form');
    const fileInput = document.getElementById('dn-attachment-file');
    const attachBtn = document.getElementById('dn-attach-doc-btn');
    const voiceBtn = document.getElementById('dn-voice-btn');
    const preview = document.getElementById('dn-attachment-preview');
    const voiceStatus = document.getElementById('dn-voice-status');
    const cfg = document.getElementById('dn-coordination-config');

    if (! form || ! fileInput || ! attachBtn || ! voiceBtn || ! cfg) {
        return;
    }

    window.__dnPendingVoiceFile = null;
    let mediaRecorder = null;
    let mediaChunks = [];
    let recording = false;

    function clearAttachmentUi() {
        window.__dnPendingVoiceFile = null;
        fileInput.value = '';
        if (preview) {
            preview.textContent = '';
            preview.classList.add('hidden');
        }
        if (voiceStatus) {
            voiceStatus.textContent = '';
            voiceStatus.classList.add('hidden');
        }
        voiceBtn.classList.remove('bg-red-50', 'text-red-600', 'ring-red-200');
    }

    attachBtn.addEventListener('click', function () {
        window.__dnPendingVoiceFile = null;
        fileInput.click();
    });

    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files.length) {
            window.__dnPendingVoiceFile = null;
            if (preview) {
                preview.textContent = fileInput.files[0].name;
                preview.classList.remove('hidden');
            }
        }
    });

    voiceBtn.addEventListener('click', async function () {
        if (recording && mediaRecorder) {
            mediaRecorder.stop();
            recording = false;
            voiceBtn.classList.remove('bg-red-50', 'text-red-600', 'ring-red-200');
            return;
        }

        if (! navigator.mediaDevices || ! window.MediaRecorder) {
            if (voiceStatus) {
                voiceStatus.textContent = cfg.dataset.voiceUnsupported || '';
                voiceStatus.classList.remove('hidden');
            }
            return;
        }

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaChunks = [];
            mediaRecorder = new MediaRecorder(stream);
            mediaRecorder.ondataavailable = function (ev) {
                if (ev.data.size > 0) {
                    mediaChunks.push(ev.data);
                }
            };
            mediaRecorder.onstop = function () {
                stream.getTracks().forEach(function (t) {
                    t.stop();
                });
                const blob = new Blob(mediaChunks, { type: mediaRecorder.mimeType || 'audio/webm' });
                const ext = blob.type.indexOf('webm') !== -1 ? 'webm' : 'ogg';
                window.__dnPendingVoiceFile = new File([blob], 'voice-note.' + ext, { type: blob.type || 'audio/webm' });
                fileInput.value = '';
                if (preview) {
                    preview.textContent = cfg.dataset.voiceReady || 'Voice note ready';
                    preview.classList.remove('hidden');
                }
            };
            mediaRecorder.start();
            recording = true;
            voiceBtn.classList.add('bg-red-50', 'text-red-600', 'ring-red-200');
            if (voiceStatus) {
                voiceStatus.textContent = cfg.dataset.voiceRecording || '';
                voiceStatus.classList.remove('hidden');
            }
        } catch (err) {
            if (voiceStatus) {
                voiceStatus.textContent = cfg.dataset.voiceDenied || '';
                voiceStatus.classList.remove('hidden');
            }
        }
    });

    form.addEventListener('reset', clearAttachmentUi);
}

function wireDnCoordinationFormSubmit() {
    const form = document.getElementById('dn-coordination-form');
    const cfg = document.getElementById('dn-coordination-config');
    if (! form || ! cfg) {
        return;
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const ta = form.querySelector('textarea[name="body"]');
        const body = (ta?.value || '').trim();
        const fileInput = document.getElementById('dn-attachment-file');
        const hasDoc = fileInput && fileInput.files && fileInput.files.length > 0;
        const hasVoice = !! window.__dnPendingVoiceFile;

        if (! body && ! hasDoc && ! hasVoice) {
            return;
        }

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const fd = new FormData(form);
        if (hasDoc && fileInput.files[0]) {
            fd.append('attachment', fileInput.files[0]);
            fd.set('attachment_kind', 'document');
        } else if (hasVoice) {
            fd.append('attachment', window.__dnPendingVoiceFile);
            fd.set('attachment_kind', 'voice');
        }

        try {
            const res = await fetch(cfg.dataset.postUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                },
                body: fd,
            });

            if (res.status === 422) {
                const j = await res.json().catch(function () {
                    return {};
                });
                const err =
                    (j.errors && j.errors.body && j.errors.body[0]) ||
                    (j.errors && j.errors.attachment && j.errors.attachment[0]) ||
                    (j.errors && j.errors.attachment_kind && j.errors.attachment_kind[0]) ||
                    j.message ||
                    'Could not send message.';
                window.alert(err);
                return;
            }

            if (! res.ok) {
                window.location.reload();
                return;
            }

            const data = await res.json();
            const uid = parseInt(cfg.dataset.currentUserId || '0', 10);
            if (data.message) {
                appendDnCoordinationMessage(data.message, uid);
            }
            if (ta) {
                ta.value = '';
            }
            window.__dnPendingVoiceFile = null;
            if (fileInput) {
                fileInput.value = '';
            }
            const preview = document.getElementById('dn-attachment-preview');
            if (preview) {
                preview.textContent = '';
                preview.classList.add('hidden');
            }
            const voiceStatus = document.getElementById('dn-voice-status');
            if (voiceStatus) {
                voiceStatus.textContent = '';
                voiceStatus.classList.add('hidden');
            }
        } catch (err) {
            window.location.reload();
        }
    });
}

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

function readBroadcastMeta(name) {
    return document.querySelector('meta[name="' + name + '"]')?.getAttribute('content')?.trim() || '';
}

function initPortalConversationComposer() {
    const form = document.getElementById('portal-conversation-form');
    const fileInput = document.getElementById('portal-attachment-file');
    const attachBtn = document.getElementById('portal-attach-doc-btn');
    const voiceBtn = document.getElementById('portal-voice-btn');
    const preview = document.getElementById('portal-attachment-preview');
    const voiceStatus = document.getElementById('portal-voice-status');

    if (! form || ! fileInput || ! attachBtn || ! voiceBtn) {
        return;
    }

    window.__portalPendingVoiceFile = null;
    let mediaRecorder = null;
    let mediaChunks = [];
    let recording = false;

    function clearAttachmentUi() {
        window.__portalPendingVoiceFile = null;
        fileInput.value = '';
        if (preview) {
            preview.textContent = '';
            preview.classList.add('hidden');
        }
        if (voiceStatus) {
            voiceStatus.textContent = '';
            voiceStatus.classList.add('hidden');
        }
        voiceBtn.classList.remove('bg-red-50', 'text-red-600', 'ring-red-200');
    }

    attachBtn.addEventListener('click', function () {
        window.__portalPendingVoiceFile = null;
        fileInput.click();
    });

    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files.length) {
            window.__portalPendingVoiceFile = null;
            if (preview) {
                preview.textContent = fileInput.files[0].name;
                preview.classList.remove('hidden');
            }
        }
    });

    voiceBtn.addEventListener('click', async function () {
        if (recording && mediaRecorder) {
            mediaRecorder.stop();
            recording = false;
            voiceBtn.classList.remove('bg-red-50', 'text-red-600', 'ring-red-200');
            return;
        }

        if (! navigator.mediaDevices || ! window.MediaRecorder) {
            if (voiceStatus) {
                voiceStatus.textContent = document.getElementById('portal-conversation-config')?.dataset.voiceUnsupported || '';
                voiceStatus.classList.remove('hidden');
            }
            return;
        }

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaChunks = [];
            mediaRecorder = new MediaRecorder(stream);
            mediaRecorder.ondataavailable = function (ev) {
                if (ev.data.size > 0) {
                    mediaChunks.push(ev.data);
                }
            };
            mediaRecorder.onstop = function () {
                stream.getTracks().forEach(function (t) {
                    t.stop();
                });
                const blob = new Blob(mediaChunks, { type: mediaRecorder.mimeType || 'audio/webm' });
                const ext = blob.type.indexOf('webm') !== -1 ? 'webm' : 'ogg';
                window.__portalPendingVoiceFile = new File([blob], 'voice-note.' + ext, { type: blob.type || 'audio/webm' });
                fileInput.value = '';
                if (preview) {
                    preview.textContent =
                        document.getElementById('portal-conversation-config')?.dataset.voiceReady || 'Voice note ready';
                    preview.classList.remove('hidden');
                }
            };
            mediaRecorder.start();
            recording = true;
            voiceBtn.classList.add('bg-red-50', 'text-red-600', 'ring-red-200');
            if (voiceStatus) {
                voiceStatus.textContent =
                    document.getElementById('portal-conversation-config')?.dataset.voiceRecording || '';
                voiceStatus.classList.remove('hidden');
            }
        } catch (err) {
            if (voiceStatus) {
                voiceStatus.textContent =
                    document.getElementById('portal-conversation-config')?.dataset.voiceDenied || '';
                voiceStatus.classList.remove('hidden');
            }
        }
    });

    form.addEventListener('reset', clearAttachmentUi);
}

function wirePortalConversationFormSubmit() {
    const form = document.getElementById('portal-conversation-form');
    const cfg = document.getElementById('portal-conversation-config');
    if (! form || ! cfg) {
        return;
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const ta = form.querySelector('textarea[name="body"]');
        const body = (ta?.value || '').trim();
        const fileInput = document.getElementById('portal-attachment-file');
        const hasDoc = fileInput && fileInput.files && fileInput.files.length > 0;
        const hasVoice = !! window.__portalPendingVoiceFile;

        if (! body && ! hasDoc && ! hasVoice) {
            return;
        }

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const fd = new FormData(form);
        if (hasDoc && fileInput.files[0]) {
            fd.append('attachment', fileInput.files[0]);
            fd.set('attachment_kind', 'document');
        } else if (hasVoice) {
            fd.append('attachment', window.__portalPendingVoiceFile);
            fd.set('attachment_kind', 'voice');
        }

        try {
            const res = await fetch(cfg.dataset.postUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                },
                body: fd,
            });

            if (res.status === 422) {
                const j = await res.json().catch(function () {
                    return {};
                });
                const err =
                    (j.errors && j.errors.body && j.errors.body[0]) ||
                    (j.errors && j.errors.attachment && j.errors.attachment[0]) ||
                    (j.errors && j.errors.attachment_kind && j.errors.attachment_kind[0]) ||
                    j.message ||
                    'Could not send message.';
                window.alert(err);
                return;
            }

            if (! res.ok) {
                window.location.reload();
                return;
            }

            const data = await res.json();
            const uid = parseInt(cfg.dataset.currentUserId || '0', 10);
            if (data.message) {
                appendConversationMessage(data.message, uid);
            }
            if (ta) {
                ta.value = '';
            }
            window.__portalPendingVoiceFile = null;
            if (fileInput) {
                fileInput.value = '';
            }
            const preview = document.getElementById('portal-attachment-preview');
            if (preview) {
                preview.textContent = '';
                preview.classList.add('hidden');
            }
            const voiceStatus = document.getElementById('portal-voice-status');
            if (voiceStatus) {
                voiceStatus.textContent = '';
                voiceStatus.classList.add('hidden');
            }
        } catch (err) {
            window.location.reload();
        }
    });
}

function formatMessageTime(iso) {
    if (! iso) {
        return '';
    }
    const d = new Date(iso);
    if (Number.isNaN(d.getTime())) {
        return '';
    }
    return d.toLocaleTimeString(undefined, { hour: 'numeric', minute: '2-digit' });
}

function buildConversationBubbleHtml(e, currentUserId) {
    const hasAtt = !!(e && (e.has_attachment === true || e.has_attachment === 'true' || e.has_attachment === 1));
    const kind = e && e.attachment_kind ? String(e.attachment_kind) : '';
    const url = e && e.attachment_url ? escapeHtml(String(e.attachment_url)) : '';
    const attName = escapeHtml(e && e.attachment_name ? String(e.attachment_name) : '');
    const body = escapeHtml(e && e.body ? String(e.body) : '');
    const timeHtml = escapeHtml(formatMessageTime(e.created_at));
    const mine = parseInt(String(e.user_id), 10) === currentUserId;
    const initial = escapeHtml((e.user_name || '?').trim().charAt(0).toUpperCase());
    const userName = escapeHtml(e.user_name || '');

    let inner = '';
    if (hasAtt && kind === 'voice' && url) {
        inner +=
            '<audio class="w-full min-w-[200px] max-w-full" controls preload="metadata" src="' +
            url +
            '"></audio>';
    } else if (hasAtt && kind === 'document' && url) {
        inner +=
            '<a href="' +
            url +
            '" class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold underline-offset-2 hover:underline ' +
            (mine ? 'bg-white/15 text-white' : 'bg-slate-100 text-[#1a73e8] ring-1 ring-slate-200/80 hover:bg-slate-200') +
            '"><i class="fas fa-file-lines"></i><span class="truncate">' +
            (attName || 'File') +
            '</span></a>';
    }
    if (body) {
        const sep = hasAtt ? (mine ? ' border-t border-white/20 pt-2 mt-2' : ' border-t border-slate-200 pt-2 mt-2') : '';
        inner += '<p class="whitespace-pre-wrap break-words font-normal' + sep + '">' + body + '</p>';
    }

    if (mine) {
        return (
            '<div class="flex justify-end px-1 py-0.5 sm:px-2"><div class="max-w-[min(85%,28rem)]">' +
            '<div class="rounded-[1.35rem] rounded-br-md bg-[#1a73e8] px-4 py-2.5 text-[0.9375rem] leading-snug text-white shadow-sm">' +
            inner +
            '</div>' +
            '<p class="mt-1 pr-1 text-right text-[11px] font-medium text-slate-500/90">' +
            timeHtml +
            '</p></div></div>'
        );
    }

    return (
        '<div class="flex justify-start gap-2 px-1 py-0.5 sm:px-2">' +
        '<div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-xs font-bold text-slate-600 shadow-sm ring-1 ring-slate-200/80">' +
        initial +
        '</div>' +
        '<div class="max-w-[min(85%,28rem)]">' +
        '<p class="mb-0.5 pl-1 text-[11px] font-medium text-slate-500">' +
        userName +
        '</p>' +
        '<div class="rounded-[1.35rem] rounded-tl-md border border-slate-200/80 bg-white px-4 py-2.5 text-[0.9375rem] leading-snug text-slate-900 shadow-sm">' +
        inner +
        '</div>' +
        '<p class="mt-1 pl-1 text-[11px] font-medium text-slate-500/90">' +
        timeHtml +
        '</p></div></div>'
    );
}

function appendConversationMessage(e, currentUserId) {
    const box = document.getElementById('portal-conversation-messages');
    if (! box) {
        return;
    }

    const emptyHint = box.querySelector('[data-thread-empty-placeholder]');
    if (emptyHint) {
        emptyHint.remove();
    }

    const html = buildConversationBubbleHtml(e, currentUserId).trim();
    const tpl = document.createElement('template');
    tpl.innerHTML = html;
    const node = tpl.content.firstElementChild;
    if (node) {
        box.appendChild(node);
    }
    box.scrollTop = box.scrollHeight;
}

document.addEventListener('DOMContentLoaded', function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const key = readBroadcastMeta('reverb-app-key') || import.meta.env.VITE_REVERB_APP_KEY;
    const host = readBroadcastMeta('reverb-host') || import.meta.env.VITE_REVERB_HOST || '127.0.0.1';
    const port = readBroadcastMeta('reverb-port') || import.meta.env.VITE_REVERB_PORT || '8080';
    const scheme = readBroadcastMeta('reverb-scheme') || import.meta.env.VITE_REVERB_SCHEME || 'http';

    wirePortalConversationFormSubmit();
    initPortalConversationComposer();
    initDnCoordinationComposer();

    if (! key || ! csrf) {
        return;
    }

    if (! window.Echo) {
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: key,
            wsHost: host,
            wsPort: Number(port) || 8080,
            wssPort: Number(port) || 8080,
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
                video_session_id: e.video_session_id,
            });
        });
    }

    wireAmbulanceSosChannels();
    wireConversationChannel();
    wireDnCoordinationFormSubmit();
    wireDnCoordinationChannel();
});
