<x-layouts.public>
    <style>
        .terms-header { background: radial-gradient(circle at top right, #f8fafc, #eff6ff); }
        .legal-card { border-radius: 3.5rem; border: 1px solid #f1f5f9; }
        .policy-text { line-height: 1.8; color: #334155; font-size: 0.875rem; }
        .accent-border { border-left: 4px solid #2563eb; padding-left: 1rem; }
    </style>

    <header class="terms-header pt-24 pb-20 px-6 border-b border-slate-100">
        <div class="max-w-4xl mx-auto text-center">
            <span class="bg-slate-900 text-white px-6 py-2 rounded-full text-[10px] font-black uppercase tracking-[0.4em] mb-8 inline-block">
                {{ __('terms.badge') }}
            </span>
            <h1 class="text-5xl md:text-6xl font-black text-slate-900 leading-none mb-6 tracking-tighter uppercase">
                {!! __('terms.title_html') !!}
            </h1>
            <p class="text-lg text-slate-500 font-medium italic">{{ __('terms.subtitle') }}</p>
        </div>
    </header>

    <main class="max-w-5xl mx-auto py-20 px-6">
        <div class="bg-white p-12 md:p-20 legal-card shadow-2xl shadow-blue-100/20 space-y-16">
            <section>
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-12 h-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center shadow-lg text-xl"><i class="fas fa-user-graduate"></i></div>
                    <h2 class="text-2xl font-black uppercase tracking-tighter">{{ __('terms.section1_title') }}</h2>
                </div>
                <div class="policy-text space-y-4 font-bold italic">
                    <p class="accent-border">{{ __('terms.section1_intro') }}</p>
                    <ul class="list-none space-y-3 ml-4">
                        <li><i class="fas fa-check-circle text-blue-600 mr-2"></i> {!! __('terms.section1_item1_html') !!}</li>
                        <li><i class="fas fa-check-circle text-blue-600 mr-2"></i> {!! __('terms.section1_item2_html') !!}</li>
                        <li><i class="fas fa-check-circle text-blue-600 mr-2"></i> {!! __('terms.section1_item3_html') !!}</li>
                        <li><i class="fas fa-check-circle text-blue-600 mr-2"></i> {!! __('terms.section1_item4_html') !!}</li>
                    </ul>
                </div>
            </section>

            <section>
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-12 h-12 bg-green-600 text-white rounded-2xl flex items-center justify-center shadow-lg text-xl"><i class="fas fa-ambulance"></i></div>
                    <h2 class="text-2xl font-black uppercase tracking-tighter">{{ __('terms.section2_title') }}</h2>
                </div>
                <div class="policy-text space-y-4 font-bold italic">
                    <p class="accent-border">{{ __('terms.section2_intro') }}</p>
                    <ul class="list-none space-y-3 ml-4">
                        <li><i class="fas fa-check-circle text-green-600 mr-2"></i> {!! __('terms.section2_item1_html') !!}</li>
                        <li><i class="fas fa-check-circle text-green-600 mr-2"></i> {!! __('terms.section2_item2_html') !!}</li>
                        <li><i class="fas fa-check-circle text-green-600 mr-2"></i> {!! __('terms.section2_item3_html') !!}</li>
                    </ul>
                </div>
            </section>

            <section>
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center shadow-lg text-xl"><i class="fas fa-balance-scale"></i></div>
                    <h2 class="text-2xl font-black uppercase tracking-tighter">{{ __('terms.section3_title') }}</h2>
                </div>
                <p class="policy-text font-bold italic mb-6">{{ __('terms.section3_intro') }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 italic font-medium">
                        <h3 class="text-blue-600 font-black text-xs mb-2 uppercase">{{ __('terms.section3_card1_title') }}</h3>
                        {{ __('terms.section3_card1_body') }}
                    </div>
                    <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 italic font-medium">
                        <h3 class="text-blue-600 font-black text-xs mb-2 uppercase">{{ __('terms.section3_card2_title') }}</h3>
                        {{ __('terms.section3_card2_body') }}
                    </div>
                </div>
            </section>

            <section class="bg-red-50 p-10 rounded-[3rem] border border-red-100">
                <h3 class="text-xs font-black text-red-600 uppercase tracking-widest mb-4 italic">{{ __('terms.warning_title') }}</h3>
                <p class="text-xs font-bold text-slate-700 leading-relaxed italic">{{ __('terms.warning_body') }}</p>
            </section>
        </div>
    </main>
</x-layouts.public>

