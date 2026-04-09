<x-layouts.public>
    <style>
        .privacy-header { background: radial-gradient(circle at top right, #f8fafc, #eff6ff); }
        .content-card { border-radius: 3rem; border: 1px solid #f1f5f9; }
        .legal-text { line-height: 1.8; color: #475569; }
    </style>

    <header class="privacy-header pt-24 pb-20 px-6 border-b border-slate-100">
        <div class="max-w-4xl mx-auto text-center">
            <span class="bg-blue-100 text-blue-700 px-6 py-2 rounded-full text-xs font-black uppercase tracking-[0.3em] mb-8 inline-block">
                {{ __('privacy.badge') }}
            </span>
            <h1 class="text-5xl md:text-6xl font-black text-slate-900 leading-none mb-6 tracking-tighter uppercase">
                {!! __('privacy.title_html') !!}
            </h1>
            <p class="text-lg text-slate-500 font-medium italic">{{ __('privacy.subtitle') }}</p>
        </div>
    </header>

    <main class="max-w-5xl mx-auto py-20 px-6">
        <div class="bg-white p-12 md:p-20 content-card shadow-2xl shadow-blue-100/20 space-y-16">
            <section>
                <div class="flex items-center space-x-4 mb-6">
                    <div class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center shadow-lg"><i class="fas fa-user-shield"></i></div>
                    <h2 class="text-xl font-black uppercase tracking-tighter">{{ __('privacy.section1_title') }}</h2>
                </div>
                <p class="legal-text font-medium italic text-sm">{{ __('privacy.section1_body') }}</p>
            </section>

            <section>
                <div class="flex items-center space-x-4 mb-6">
                    <div class="w-10 h-10 bg-pink-600 text-white rounded-xl flex items-center justify-center shadow-lg"><i class="fas fa-database"></i></div>
                    <h2 class="text-xl font-black uppercase tracking-tighter">{{ __('privacy.section2_title') }}</h2>
                </div>
                <div class="space-y-4 legal-text text-sm font-bold">
                    <p><i class="fas fa-check text-green-500 mr-2"></i> {!! __('privacy.section2_item1_html') !!}</p>
                    <p><i class="fas fa-check text-green-500 mr-2"></i> {!! __('privacy.section2_item2_html') !!}</p>
                    <p><i class="fas fa-check text-green-500 mr-2"></i> {!! __('privacy.section2_item3_html') !!}</p>
                </div>
            </section>

            <section>
                <div class="flex items-center space-x-4 mb-6">
                    <div class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center shadow-lg"><i class="fas fa-lock"></i></div>
                    <h2 class="text-xl font-black uppercase tracking-tighter">{{ __('privacy.section3_title') }}</h2>
                </div>
                <p class="legal-text font-medium italic text-sm">{!! __('privacy.section3_body_html') !!}</p>
            </section>

            <section>
                <div class="flex items-center space-x-4 mb-6">
                    <div class="w-10 h-10 bg-blue-400 text-white rounded-xl flex items-center justify-center shadow-lg"><i class="fas fa-share-alt"></i></div>
                    <h2 class="text-xl font-black uppercase tracking-tighter">{{ __('privacy.section4_title') }}</h2>
                </div>
                <p class="legal-text font-medium italic text-sm">{{ __('privacy.section4_body') }}</p>
            </section>

            <section class="bg-slate-50 p-10 rounded-[2.5rem] border border-slate-100">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">{{ __('privacy.changes_title') }}</h3>
                <p class="text-xs font-bold text-slate-600 leading-relaxed italic">{{ __('privacy.changes_body') }}</p>
            </section>
        </div>
    </main>
</x-layouts.public>

