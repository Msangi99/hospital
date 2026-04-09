<x-layouts.public>
    <style>
        :root { --ussd-dark: #020617; --ussd-green: #22c55e; }
        .ussd-bg { background: linear-gradient(135deg, #020617 0%, #0f172a 100%); }
        .phone-mockup { border: 12px solid #1e293b; border-radius: 3rem; box-shadow: 0 50px 100px -20px rgba(0,0,0,0.5); }
        .ussd-screen { background: #f8fafc; font-family: 'Courier New', Courier, monospace; }
        .pulse-green { animation: pulse-green 2s infinite; }
        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
            70% { box-shadow: 0 0 0 20px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }
    </style>

    <main class="ussd-bg min-h-screen py-20 px-6">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
            <div class="text-white">
                <span class="bg-green-500/20 text-green-400 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-[0.2em] mb-8 inline-block border border-green-500/30">
                    {{ __('ussd.badge') }}
                </span>
                <h1 class="text-5xl md:text-7xl font-black mb-8 tracking-tighter leading-[0.9]">
                    {!! __('ussd.title_html') !!}
                </h1>
                <p class="text-xl text-slate-400 mb-12 max-w-lg leading-relaxed font-medium">
                    {{ __('ussd.subtitle') }}
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                    <div class="p-6 bg-white/5 border border-white/10 rounded-3xl">
                        <i class="fas fa-bolt text-green-500 text-2xl mb-4"></i>
                        <h2 class="font-black uppercase text-xs tracking-widest mb-2">{{ __('ussd.fast_title') }}</h2>
                        <p class="text-xs text-slate-500 font-bold">{{ __('ussd.fast_desc') }}</p>
                    </div>
                    <div class="p-6 bg-white/5 border border-white/10 rounded-3xl">
                        <i class="fas fa-broadcast-tower text-blue-500 text-2xl mb-4"></i>
                        <h2 class="font-black uppercase text-xs tracking-widest mb-2">{{ __('ussd.universal_title') }}</h2>
                        <p class="text-xs text-slate-500 font-bold">{{ __('ussd.universal_desc') }}</p>
                    </div>
                </div>

                <div class="bg-green-600 p-8 rounded-[2.5rem] inline-block shadow-2xl pulse-green">
                    <p class="text-[10px] font-black uppercase tracking-[0.4em] mb-2 opacity-80">{{ __('ussd.dial_label') }}</p>
                    <h3 class="text-5xl font-black tracking-widest">{{ __('ussd.code') }}</h3>
                </div>
            </div>

            <div class="flex justify-center">
                <div class="phone-mockup w-[320px] h-[600px] ussd-bg relative overflow-hidden">
                    <div class="absolute top-0 w-full h-8 bg-black/20 flex justify-center items-center">
                        <div class="w-16 h-1 bg-white/20 rounded-full"></div>
                    </div>

                    <div class="mt-20 mx-4 ussd-screen rounded-xl p-6 shadow-inner min-h-[300px]">
                        <div class="text-slate-800 text-sm font-bold leading-relaxed">
                            {!! __('ussd.menu_html') !!}
                        </div>
                        <input type="text" maxlength="1" class="w-full mt-4 border-b-2 border-slate-900 bg-transparent outline-none text-xl font-black text-center" placeholder="_">
                    </div>

                    <div class="absolute bottom-10 w-full px-10">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="w-12 h-12 bg-white/5 rounded-full"></div>
                            <div class="w-12 h-12 bg-white/5 rounded-full"></div>
                            <div class="w-12 h-12 bg-white/5 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <section class="py-32 px-6 max-w-7xl mx-auto">
        <div class="text-center mb-20">
            <h2 class="text-xs font-black text-blue-600 uppercase tracking-[0.4em] mb-4">{{ __('ussd.steps_badge') }}</h2>
            <h3 class="text-4xl font-black text-slate-900 leading-tight">{{ __('ussd.steps_title') }}</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div class="text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6 font-black text-xl">1</div>
                <h4 class="font-black mb-3 uppercase tracking-tighter">{{ __('ussd.step1_title') }}</h4>
                <p class="text-sm text-slate-500 font-medium italic">{{ __('ussd.step1_desc') }}</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6 font-black text-xl">2</div>
                <h4 class="font-black mb-3 uppercase tracking-tighter">{{ __('ussd.step2_title') }}</h4>
                <p class="text-sm text-slate-500 font-medium italic">{{ __('ussd.step2_desc') }}</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6 font-black text-xl">3</div>
                <h4 class="font-black mb-3 uppercase tracking-tighter">{{ __('ussd.step3_title') }}</h4>
                <p class="text-sm text-slate-500 font-medium italic">{{ __('ussd.step3_desc') }}</p>
            </div>
        </div>
    </section>
</x-layouts.public>

