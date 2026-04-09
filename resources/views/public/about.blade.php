<x-layouts.public>
    <section class="bg-slate-50 py-16 px-6 md:py-20">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-10">
                <span class="inline-block rounded-full bg-blue-100 px-4 py-1.5 text-[10px] font-black uppercase tracking-[0.3em] text-blue-700">{{ __('public_pages.about_badge') }}</span>
                <h1 class="mt-4 text-4xl md:text-5xl font-black tracking-tighter text-slate-900">{{ __('public_pages.about_title') }}</h1>
                <p class="mt-4 text-base md:text-lg text-slate-600 max-w-3xl mx-auto">{{ __('public_pages.about_intro') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-[2.5rem] border border-slate-100 p-8 shadow-sm">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black mb-5">V</div>
                    <h2 class="text-2xl font-black tracking-tight mb-3">{{ __('public_pages.about_vision_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('public_pages.about_vision_body') }}</p>
                </div>

                <div class="bg-white rounded-[2.5rem] border border-slate-100 p-8 shadow-sm">
                    <div class="w-11 h-11 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center font-black mb-5">M</div>
                    <h2 class="text-2xl font-black tracking-tight mb-3 text-pink-600">{{ __('public_pages.about_mission_title') }}</h2>
                    <ul class="space-y-3 text-slate-600 leading-relaxed">
                        <li>• {{ __('public_pages.about_mission_point_1') }}</li>
                        <li>• {{ __('public_pages.about_mission_point_2') }}</li>
                    </ul>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-6">
                <div class="bg-slate-900 text-white rounded-3xl p-6 shadow-xl">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-blue-300 mb-3">{{ __('public_pages.about_mvp_title') }}</h3>
                    <p class="text-sm text-slate-200">{{ __('public_pages.about_mvp_body') }}</p>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-blue-100 shadow-sm">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-blue-600 mb-3">{{ __('public_pages.about_scaling_title') }}</h3>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li>• {{ __('public_pages.about_scaling_1') }}</li>
                        <li>• {{ __('public_pages.about_scaling_2') }}</li>
                        <li>• {{ __('public_pages.about_scaling_3') }}</li>
                    </ul>
                </div>

                <div class="bg-blue-600 text-white rounded-3xl p-6 shadow-lg flex items-center">
                    <p class="text-lg font-black leading-tight">{{ __('public_pages.about_impact_quote') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-16 px-6 md:py-20">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white">
                <h3 class="text-2xl font-black text-blue-300 mb-4">{{ __('public_pages.about_digital_title') }}</h3>
                <p class="text-slate-200 leading-relaxed mb-6">{{ __('public_pages.about_digital_body') }}</p>
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div>
                        <p class="text-3xl font-black">100%</p>
                        <p class="text-[10px] uppercase tracking-widest text-blue-300">{{ __('public_pages.about_stat_1') }}</p>
                    </div>
                    <div>
                        <p class="text-3xl font-black">24/7</p>
                        <p class="text-[10px] uppercase tracking-widest text-blue-300">{{ __('public_pages.about_stat_2') }}</p>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-[10px] font-black uppercase tracking-widest text-blue-600 mb-2">{{ __('public_pages.about_approach_badge') }}</h4>
                <h5 class="text-3xl font-black tracking-tight text-slate-900 mb-4">{{ __('public_pages.about_approach_title') }}</h5>
                <p class="text-slate-600 leading-relaxed">{{ __('public_pages.about_approach_body') }}</p>
            </div>
        </div>
    </section>
</x-layouts.public>