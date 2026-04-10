<x-layouts.public>
    <style>
        :root { --safe-pink: #ec4899; --safe-blue: #2563eb; }
        .safe-gradient { background: radial-gradient(circle at top right, #fff1f2, #ffffff); }
        .chat-box { height: 350px; overflow-y: auto; scrollbar-width: thin; scroll-behavior: smooth; }
        .chat-box::-webkit-scrollbar { width: 4px; }
        .chat-box::-webkit-scrollbar-thumb { background: #fce7f3; border-radius: 10px; }
        .floating-heart { animation: float 3s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

        .typing-indicator-wrap { display: flex; justify-content: flex-start; }
        .typing-indicator-bubble {
            background: #e2e8f0;
            padding: 14px 18px;
            border-radius: 1.5rem;
            border-top-left-radius: 0.25rem;
            box-shadow: 0 1px 2px rgb(0 0 0 / 0.06);
        }
        .typing-indicator-dots {
            display: flex;
            align-items: center;
            gap: 5px;
            height: 8px;
        }
        .typing-indicator-dots span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #64748b;
            animation: typing-ios 1.2s ease-in-out infinite;
        }
        .typing-indicator-dots span:nth-child(1) { animation-delay: 0s; }
        .typing-indicator-dots span:nth-child(2) { animation-delay: 0.2s; }
        .typing-indicator-dots span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typing-ios {
            0%, 70%, 100% { transform: translateY(0) scale(1); opacity: 0.35; }
            35% { transform: translateY(-5px) scale(1.05); opacity: 1; }
        }

        .assistant-meta-block { opacity: 0; transition: opacity 0.35s ease; }
        .assistant-meta-block.is-visible { opacity: 1; }
    </style>

    <section class="safe-gradient pt-16 pb-32 px-6 overflow-hidden">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
            <div class="space-y-10">
                <div>
                    <span class="bg-pink-100 text-pink-700 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-[0.3em] mb-8 inline-block">{{ __('safe_girl.badge') }}</span>
                    <h1 class="text-6xl font-black text-slate-900 leading-[0.95] mb-8 tracking-tighter uppercase">{!! __('safe_girl.title_html') !!}</h1>
                    <p class="text-lg text-slate-600 font-medium italic">{{ __('safe_girl.subtitle') }}</p>
                </div>

                <div class="bg-white p-10 rounded-[4rem] shadow-2xl border border-pink-50 relative">
                    <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 italic">{{ __('safe_girl.chart_title') }}</h2>
                    <svg viewBox="0 0 400 150" class="w-full h-40">
                        <path d="M0,130 Q50,110 100,120 T200,60 T300,80 T400,20" fill="none" stroke="#ec4899" stroke-width="4" stroke-linecap="round" />
                        <circle cx="200" cy="60" r="6" fill="#ec4899" class="floating-heart" />
                    </svg>
                    <div class="flex justify-between mt-6 text-[10px] font-black uppercase text-slate-400 italic">
                        <span>{{ __('safe_girl.chart_left') }}</span>
                        <span>{{ __('safe_girl.chart_mid') }}</span>
                        <span>{{ __('safe_girl.chart_right') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[3.5rem] shadow-2xl border border-slate-100 overflow-hidden sticky top-24">
                <div class="bg-slate-900 p-8 text-white">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-pink-600 rounded-2xl flex items-center justify-center text-xl shadow-lg shadow-pink-500/20"><i class="fas fa-comment-medical"></i></div>
                        <div>
                            <h2 class="font-black uppercase text-xs tracking-widest">{{ __('safe_girl.chat_header') }}</h2>
                            <p class="text-[10px] font-bold text-blue-400 italic uppercase">{{ __('safe_girl.chat_status') }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <p class="text-xs font-bold text-slate-500 mb-6 italic bg-slate-50 p-4 rounded-2xl border-l-4 border-pink-500">
                        {{ __('safe_girl.chat_hint') }}
                    </p>

                    <div id="chatBox" class="chat-box space-y-4 mb-6 pr-2">
                        <div class="flex justify-start">
                            <div class="bg-slate-100 p-4 rounded-3xl rounded-tl-none max-w-[85%] text-xs font-bold text-slate-600 leading-relaxed shadow-sm">
                                {{ __('safe_girl.chat_first_message') }}
                            </div>
                        </div>
                    </div>

                    @auth
                        <form id="symptomForm" class="relative">
                            @csrf
                            <textarea id="symptomInput" name="symptom_message" required placeholder="{{ __('safe_girl.input_placeholder') }}" class="w-full p-5 pr-16 bg-white border border-slate-100 rounded-[2rem] text-sm font-bold outline-none focus:border-pink-500 transition resize-none h-24 shadow-inner"></textarea>
                            <button type="submit" id="symptomSendBtn" class="absolute right-3 bottom-3 w-12 h-12 bg-pink-600 text-white rounded-2xl flex items-center justify-center shadow-lg hover:bg-slate-900 transition transform hover:scale-105 active:scale-95 disabled:opacity-50 disabled:pointer-events-none disabled:scale-100">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    @else
                        <div class="text-center p-6 bg-pink-50 rounded-3xl border-2 border-dashed border-pink-200">
                            <p class="text-xs font-black text-pink-600 uppercase mb-4">{{ __('safe_girl.login_required') }}</p>
                            <a href="{{ route('login') }}" class="bg-pink-600 text-white px-6 py-2 rounded-full text-[10px] font-black uppercase">{{ __('safe_girl.login_now') }}</a>
                        </div>
                    @endauth

                    <div class="mt-4 flex justify-center items-center space-x-4 opacity-40">
                        <span class="text-[8px] font-black uppercase tracking-widest"><i class="fas fa-lock mr-1"></i> {{ __('safe_girl.e2e') }}</span>
                        <span class="text-[8px] font-black uppercase tracking-widest"><i class="fas fa-shield-alt mr-1"></i> {{ __('safe_girl.protected') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        const symptomForm = document.getElementById('symptomForm');
        const chatBox = document.getElementById('chatBox');
        const symptomInput = document.getElementById('symptomInput');
        const symptomSendBtn = document.getElementById('symptomSendBtn');
        const history = [];
        let typingIndicatorRow = null;

        if (symptomForm) {
            symptomForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const message = symptomInput.value.trim();
                if (!message) return;

                appendUserBubble(message);
                const priorHistory = history.slice(-18);
                history.push({ role: 'user', content: message });
                symptomInput.value = '';

                setChatBusy(true);
                showTypingIndicator();

                try {
                    const response = await fetch(@json(route('safe-girl.ai-chat')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': symptomForm.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            message,
                            history: priorHistory,
                        }),
                    });

                    if (!response.ok) {
                        throw new Error('request failed');
                    }

                    const data = await response.json();
                    const aiText = (data.assistant_message || @json(__('safe_girl.received_reply'))).toString();
                    removeTypingIndicator();
                    await appendAssistantBubbleAnimated(aiText, data);
                    history.push({ role: 'assistant', content: aiText });
                } catch (error) {
                    removeTypingIndicator();
                    await appendAssistantBubbleAnimated(@json(__('safe_girl.ai_error_reply')), {}, { fast: true });
                } finally {
                    setChatBusy(false);
                }
            });
        }

        function setChatBusy(busy) {
            if (symptomInput) symptomInput.disabled = busy;
            if (symptomSendBtn) symptomSendBtn.disabled = busy;
        }

        function showTypingIndicator() {
            removeTypingIndicator();
            const row = document.createElement('div');
            row.className = 'typing-indicator-wrap';
            row.setAttribute('aria-live', 'polite');
            row.setAttribute('aria-label', @json(__('safe_girl.typing_indicator_a11y')));
            row.innerHTML = `
                <div class="typing-indicator-bubble max-w-[85%]">
                    <div class="typing-indicator-dots" role="presentation">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            `;
            chatBox.appendChild(row);
            typingIndicatorRow = row;
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function removeTypingIndicator() {
            if (typingIndicatorRow && typingIndicatorRow.parentNode) {
                typingIndicatorRow.parentNode.removeChild(typingIndicatorRow);
            }
            typingIndicatorRow = null;
        }

        function typewriterInto(el, fullText, options = {}) {
            const fast = options.fast === true;
            const len = fullText.length;
            if (len === 0) {
                el.textContent = '';
                return Promise.resolve();
            }
            const msPerStep = fast ? 4 : 10;
            const charsPerStep = fast ? 4 : (len > 800 ? 3 : (len > 400 ? 2 : 1));

            return new Promise((resolve) => {
                let i = 0;
                function step() {
                    if (i >= len) {
                        el.textContent = fullText;
                        chatBox.scrollTop = chatBox.scrollHeight;
                        resolve();
                        return;
                    }
                    i = Math.min(i + charsPerStep, len);
                    el.textContent = fullText.slice(0, i);
                    chatBox.scrollTop = chatBox.scrollHeight;
                    setTimeout(step, msPerStep);
                }
                step();
            });
        }

        function appendUserBubble(message) {
            const div = document.createElement('div');
            div.className = 'flex justify-end';
            div.innerHTML = `
                <div class="bg-pink-600 p-4 rounded-3xl rounded-tr-none max-w-[85%] text-xs font-bold text-white shadow-lg">
                    ${escapeHTML(message)}
                </div>
            `;
            chatBox.appendChild(div);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function buildAssistantMetaHtml(data) {
            const lines = [];

            if (data.type === 'conclusion' && data.possible_condition) {
                lines.push(`<div class="mb-2"><strong>${escapeHTML(@json(__('safe_girl.possible_condition')))}:</strong> ${escapeHTML(data.possible_condition)}</div>`);
            }

            if (data.urgency) {
                lines.push(`<div class="mb-2"><strong>${escapeHTML(@json(__('safe_girl.urgency')))}:</strong> ${escapeHTML(data.urgency)}</div>`);
            }

            if (Array.isArray(data.advice) && data.advice.length > 0) {
                const items = data.advice.map((x) => `<li>${escapeHTML(String(x))}</li>`).join('');
                lines.push(`<div class="mb-2"><strong>${escapeHTML(@json(__('safe_girl.advice')))}:</strong><ul class="list-disc pl-4 mt-1 space-y-1">${items}</ul></div>`);
            }

            if (Array.isArray(data.red_flags) && data.red_flags.length > 0) {
                const items = data.red_flags.map((x) => `<li>${escapeHTML(String(x))}</li>`).join('');
                lines.push(`<div class="mb-2"><strong>${escapeHTML(@json(__('safe_girl.red_flags')))}:</strong><ul class="list-disc pl-4 mt-1 space-y-1">${items}</ul></div>`);
            }

            return lines.join('');
        }

        function appendAssistantBubbleAnimated(text, data, options = {}) {
            const metaHtml = buildAssistantMetaHtml(data);

            const row = document.createElement('div');
            row.className = 'flex justify-start';
            const bubble = document.createElement('div');
            bubble.className = 'bg-blue-50 p-4 rounded-3xl rounded-tl-none max-w-[85%] text-xs font-bold text-blue-700 border border-blue-100';

            const textEl = document.createElement('div');
            textEl.className = 'whitespace-pre-wrap';
            textEl.textContent = '';

            bubble.appendChild(textEl);

            let metaWrap = null;
            if (metaHtml) {
                metaWrap = document.createElement('div');
                metaWrap.className = 'assistant-meta-block mt-3 text-blue-800';
                metaWrap.innerHTML = metaHtml;
                bubble.appendChild(metaWrap);
            }

            row.appendChild(bubble);
            chatBox.appendChild(row);
            chatBox.scrollTop = chatBox.scrollHeight;

            return typewriterInto(textEl, text, options).then(() => {
                if (metaWrap) {
                    requestAnimationFrame(() => metaWrap.classList.add('is-visible'));
                }
                chatBox.scrollTop = chatBox.scrollHeight;
            });
        }

        function escapeHTML(str) {
            return String(str).replace(/[&<>"']/g, function (m) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
            });
        }
    </script>
</x-layouts.public>