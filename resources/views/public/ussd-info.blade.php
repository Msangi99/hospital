<x-layouts.public>
    <style>
        :root { --ussd-green: #22c55e; --ussd-dark: #0f172a; }
        .ussd-card-gradient { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); }
        .phone-frame { border: 12px solid #334155; border-radius: 3.5rem; }
        .ussd-text { font-family: 'Courier New', Courier, monospace; }
        .pulse-green { animation: pulse-green 2s infinite; }
        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }
    </style>

    <section class="bg-slate-900 py-24 px-6 text-white overflow-hidden relative">
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600/10 rounded-full blur-[120px]"></div>
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
            <div>
                <span class="bg-green-500/20 text-green-400 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-[0.3em] mb-8 inline-block border border-green-500/30">
                    {{ __('ussdinfo.badge') }}
                </span>
                <h1 class="text-5xl md:text-7xl font-black mb-8 tracking-tighter leading-none">
                    {!! __('ussdinfo.title_html') !!}
                </h1>
                <p class="text-xl text-slate-400 mb-10 max-w-lg font-medium italic">
                    {{ __('ussdinfo.subtitle') }}
                </p>
                <div class="bg-white/5 p-8 rounded-[3rem] border border-white/10 inline-block pulse-green">
                    <p class="text-[10px] font-black uppercase tracking-[0.4em] mb-3 text-blue-400">{{ __('ussdinfo.dial_label') }}</p>
                    <h2 class="text-5xl md:text-6xl font-black tracking-widest text-white">{{ __('ussdinfo.code') }}</h2>
                </div>
            </div>

            <div class="flex justify-center">
                <div class="phone-frame w-[320px] h-[620px] bg-slate-800 p-4 shadow-2xl relative">
                    <div class="w-20 h-1 bg-slate-700 mx-auto rounded-full mb-8"></div>
                    <div class="bg-white rounded-3xl h-[480px] p-6 ussd-text text-sm leading-relaxed overflow-hidden">
                        <div class="text-slate-500 mb-4 italic">{{ __('ussdinfo.menu_intro') }}</div>
                        <div class="space-y-2 text-slate-900 font-bold">
                            {!! __('ussdinfo.menu_items_html') !!}
                        </div>
                        <div class="mt-10 pt-4 border-t border-slate-100">
                            {!! __('ussdinfo.menu_prompt_html') !!}
                        </div>
                    </div>
                    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 w-12 h-12 bg-slate-700 rounded-full border-4 border-slate-600"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-32 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-24">
                <h2 class="text-xs font-black text-blue-600 uppercase tracking-[0.4em] mb-4">{{ __('ussdinfo.how_badge') }}</h2>
                <h3 class="text-4xl font-black text-slate-900">{{ __('ussdinfo.how_title') }}</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-16">
                <div class="text-center group">
                    <div class="w-24 h-24 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8 transition group-hover:bg-blue-600 group-hover:text-white group-hover:rotate-12 shadow-sm">
                        <i class="fas fa-phone-alt text-3xl"></i>
                    </div>
                    <h4 class="text-xl font-black mb-4 uppercase tracking-tighter">{{ __('ussdinfo.step1_title') }}</h4>
                    <p class="text-slate-500 text-sm font-medium italic">{{ __('ussdinfo.step1_desc') }}</p>
                </div>
                <div class="text-center group">
                    <div class="w-24 h-24 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8 transition group-hover:bg-green-600 group-hover:text-white group-hover:rotate-12 shadow-sm">
                        <i class="fas fa-list-ol text-3xl"></i>
                    </div>
                    <h4 class="text-xl font-black mb-4 uppercase tracking-tighter">{{ __('ussdinfo.step2_title') }}</h4>
                    <p class="text-slate-500 text-sm font-medium italic">{{ __('ussdinfo.step2_desc') }}</p>
                </div>
                <div class="text-center group">
                    <div class="w-24 h-24 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8 transition group-hover:bg-pink-600 group-hover:text-white group-hover:rotate-12 shadow-sm">
                        <i class="fas fa-check-double text-3xl"></i>
                    </div>
                    <h4 class="text-xl font-black mb-4 uppercase tracking-tighter">{{ __('ussdinfo.step3_title') }}</h4>
                    <p class="text-slate-500 text-sm font-medium italic">{{ __('ussdinfo.step3_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-32 bg-slate-50 px-6">
        <div class="max-w-4xl mx-auto bg-white p-12 md:p-20 rounded-[4rem] shadow-2xl border border-slate-100">
            <h3 class="text-3xl font-black mb-12 text-center uppercase tracking-tighter">{{ __('ussdinfo.faq_title') }}</h3>

            <div class="space-y-10">
                <div class="border-b border-slate-100 pb-8">
                    <h4 class="font-black text-slate-900 mb-3 uppercase text-sm tracking-tight">{{ __('ussdinfo.faq1_q') }}</h4>
                    <p class="text-slate-500 text-sm font-medium italic">{{ __('ussdinfo.faq1_a') }}</p>
                </div>
                <div class="border-b border-slate-100 pb-8">
                    <h4 class="font-black text-slate-900 mb-3 uppercase text-sm tracking-tight">{{ __('ussdinfo.faq2_q') }}</h4>
                    <p class="text-slate-500 text-sm font-medium italic">{{ __('ussdinfo.faq2_a') }}</p>
                </div>
                <div>
                    <h4 class="font-black text-slate-900 mb-3 uppercase text-sm tracking-tight">{{ __('ussdinfo.faq3_q') }}</h4>
                    <p class="text-slate-500 text-sm font-medium italic">{{ __('ussdinfo.faq3_a') }}</p>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>

