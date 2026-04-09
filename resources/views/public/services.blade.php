<x-layouts.public>
    <style>
        .hero-bg { background: radial-gradient(circle at top right, #f1f5f9, #ffffff); }
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(0,0,0,0.05); }
        .chat-box { height: 350px; overflow-y: auto; scroll-behavior: smooth; }
        .chat-box::-webkit-scrollbar { width: 4px; }
        .chat-box::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .service-hover { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .service-hover:hover { transform: translateY(-5px); }
    </style>

    <section class="hero-bg pt-24 pb-32 px-6 text-center">
        <div class="max-w-4xl mx-auto">
            <span class="text-[10px] font-black uppercase tracking-[0.5em] text-blue-600 mb-6 inline-block">{{ __('services.badge') }}</span>
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 mb-8 tracking-tighter leading-tight">
                {!! __('services.title_html') !!}
            </h1>
            <p class="text-xl text-slate-500 font-medium leading-relaxed max-w-2xl mx-auto italic">
                {{ __('services.subtitle') }}
            </p>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 -mt-16 mb-32">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
            <div class="md:col-span-7 lg:col-span-8 glass-card rounded-[3.5rem] shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-2">
                <div class="bg-pink-50 p-10 flex flex-col justify-center items-center text-center relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#ec4899_1.5px,transparent_1.5px)] [background-size:20px_20px]"></div>
                    <img
                        src="{{ __('services.safe_girl_image') }}"
                        class="w-48 h-48 rounded-[3rem] object-cover shadow-2xl mb-6 border-4 border-white relative z-10"
                        alt="{{ __('services.safe_girl_image_alt') }}"
                    >
                    <div class="relative z-10">
                        <h2 class="text-2xl font-black text-pink-600 mb-2 uppercase tracking-tighter">{{ __('services.safe_girl_title') }}</h2>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6">{{ __('services.safe_girl_target') }}</p>
                        <p class="text-xs text-slate-500 italic leading-relaxed">{{ __('services.safe_girl_desc') }}</p>
                    </div>
                </div>

                <div class="p-8 flex flex-col h-full">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-pink-100 text-pink-600 rounded-2xl flex items-center justify-center"><i class="fas fa-shield-heart"></i></div>
                        <h3 class="font-black uppercase text-[10px] tracking-widest">{{ __('services.chat_title') }}</h3>
                    </div>

                    <div class="chat-box space-y-4 mb-6 pr-2" id="chatDisplay">
                        <div class="flex justify-start">
                            <div class="bg-slate-100 p-4 rounded-2xl rounded-tl-none max-w-[90%] text-xs font-medium leading-relaxed">
                                {{ __('services.chat_first_message') }}
                            </div>
                        </div>
                    </div>

                    <form id="symptomForm" class="mt-auto relative">
                        <textarea id="userInput" rows="2" required
                            class="w-full p-4 bg-white border border-slate-200 rounded-3xl text-sm outline-none focus:border-pink-500 transition resize-none pr-16"
                            placeholder="{{ __('services.chat_placeholder') }}"></textarea>
                        <button type="submit" class="absolute right-3 bottom-3 w-10 h-10 bg-pink-600 text-white rounded-2xl flex items-center justify-center shadow-lg hover:bg-pink-700 transition">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="md:col-span-5 lg:col-span-4 glass-card bg-slate-900 rounded-[3.5rem] p-10 text-white flex flex-col justify-between service-hover shadow-2xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fas fa-truck-medical text-9xl -rotate-12"></i>
                </div>
                <div>
                    <div class="w-14 h-14 bg-red-600 rounded-2xl flex items-center justify-center text-xl mb-8 shadow-xl shadow-red-900/20 animate-pulse">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="text-3xl font-black uppercase tracking-tighter mb-4 leading-none">{{ __('services.emergency_title') }}</h3>
                    <p class="text-slate-400 text-sm font-medium leading-relaxed italic mb-8">
                        {{ __('services.emergency_desc') }}
                    </p>
                </div>
                <button onclick="window.location.href='#'" class="w-full py-4 bg-red-600 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-white hover:text-red-600 transition duration-300">
                    {{ __('services.emergency_cta') }}
                </button>
            </div>

            <div class="md:col-span-6 glass-card rounded-[3.5rem] p-10 flex gap-8 items-center service-hover">
                <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-[2rem] flex flex-shrink-0 items-center justify-center text-3xl">
                    <i class="fas fa-user-doctor"></i>
                </div>
                <div>
                    <h4 class="text-xl font-black uppercase tracking-tighter mb-2">{{ __('services.video_title') }}</h4>
                    <p class="text-slate-500 text-xs font-medium italic leading-loose">{{ __('services.video_desc') }}</p>
                </div>
            </div>

            <div class="md:col-span-6 glass-card rounded-[3.5rem] p-10 flex gap-8 items-center service-hover">
                <div class="w-20 h-20 bg-indigo-50 text-indigo-600 rounded-[2rem] flex flex-shrink-0 items-center justify-center text-3xl">
                    <i class="fas fa-braille"></i>
                </div>
                <div>
                    <h4 class="text-xl font-black uppercase tracking-tighter mb-2">{{ __('services.ussd_title') }}</h4>
                    <p class="text-slate-500 text-xs font-medium italic leading-loose">{{ __('services.ussd_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-16 text-center">
            <div>
                <i class="fas fa-shield-halved text-4xl text-blue-600 mb-6 opacity-30"></i>
                <h5 class="font-black uppercase text-xs tracking-widest mb-4">{{ __('services.feature1_title') }}</h5>
                <p class="text-slate-400 text-[11px] italic font-medium">{{ __('services.feature1_desc') }}</p>
            </div>
            <div>
                <i class="fas fa-user-check text-4xl text-blue-600 mb-6 opacity-30"></i>
                <h5 class="font-black uppercase text-xs tracking-widest mb-4">{{ __('services.feature2_title') }}</h5>
                <p class="text-slate-400 text-[11px] italic font-medium">{{ __('services.feature2_desc') }}</p>
            </div>
            <div>
                <i class="fas fa-clock-rotate-left text-4xl text-blue-600 mb-6 opacity-30"></i>
                <h5 class="font-black uppercase text-xs tracking-widest mb-4">{{ __('services.feature3_title') }}</h5>
                <p class="text-slate-400 text-[11px] italic font-medium">{{ __('services.feature3_desc') }}</p>
            </div>
        </div>
    </section>

    <script>
        document.getElementById('symptomForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const input = document.getElementById('userInput');
            const chatDisplay = document.getElementById('chatDisplay');

            if (input.value.trim() !== "") {
                const userMsg = `
                    <div class="flex justify-end">
                        <div class="bg-pink-600 text-white p-4 rounded-2xl rounded-tr-none text-xs font-bold shadow-lg max-w-[85%]">
                            ${input.value}
                        </div>
                    </div>
                `;
                chatDisplay.innerHTML += userMsg;

                setTimeout(() => {
                    const modMsg = `
                        <div class="flex justify-start">
                            <div class="bg-blue-50 text-blue-600 p-4 rounded-2xl rounded-tl-none text-[11px] font-bold border border-blue-100 max-w-[85%] italic">
                                <i class="fas fa-check-double mr-1"></i> ${@json(__('services.chat_auto_reply'))}
                            </div>
                        </div>
                    `;
                    chatDisplay.innerHTML += modMsg;
                    chatDisplay.scrollTop = chatDisplay.scrollHeight;
                }, 1000);

                input.value = "";
                chatDisplay.scrollTop = chatDisplay.scrollHeight;
            }
        });
    </script>
</x-layouts.public>

