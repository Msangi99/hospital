<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ __('home.meta_title') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

        <script src="https://cdn.tailwindcss.com"></script>

        <style>
            :root {
                --sn-dark: #0f172a;
                --sn-primary: #2563eb;
                --sn-light: #38bdf8;
            }
            .glass-header { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(15px); }
            .sn-btn { background: linear-gradient(135deg, var(--sn-primary), var(--sn-light)); }
            @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-16px); } }
            .floating { animation: float 4s ease-in-out infinite; }
        </style>
    </head>
    <body class="bg-slate-50 text-slate-900">
        <div class="bg-[var(--sn-dark)] text-white text-[10px] py-2 px-6 sm:px-8 flex justify-between items-center tracking-widest uppercase font-bold relative z-[60]">
            <div class="flex gap-6 items-center">
                <span class="flex items-center">
                    <span class="text-sky-400 mr-2">●</span>
                    <span>{{ __('home.top_gprs') }}</span>
                </span>
                <span class="hidden md:block">
                    <span class="text-emerald-400 mr-2">●</span>
                    <span>{{ __('home.top_ussd') }}</span>
                </span>
            </div>
            <div class="flex gap-4 items-center">
                <a href="{{ route('contact') }}" class="hover:text-sky-300 transition">{{ __('home.top_help') }}</a>
                <span class="text-slate-700">|</span>
                <span class="text-sky-400">{{ __('home.top_node') }}</span>
            </div>
        </div>

        <nav class="sticky top-0 z-50 glass-header border-b border-slate-200">
            <div class="max-w-[1400px] mx-auto px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-8">
                    <a href="{{ route('home') }}" class="text-3xl font-black text-slate-900 tracking-tighter">
                        {{ __('home.brand_a') }}<span class="text-blue-600">{{ __('home.brand_b') }}</span>
                    </a>

                    <div class="hidden lg:flex gap-8 text-sm font-bold text-slate-500">
                        <a href="{{ route('home') }}" class="hover:text-blue-600 transition">{{ __('home.nav_home') }}</a>
                        <a href="{{ route('services') }}" class="hover:text-blue-600 transition">{{ __('home.nav_services') }}</a>
                        <a href="{{ route('hospitals') }}" class="hover:text-blue-600 transition">{{ __('home.nav_hospitals') }}</a>
                        <a href="{{ route('about') }}" class="hover:text-blue-600 transition">{{ __('home.nav_about') }}</a>
                        <a href="{{ route('docs') }}" class="hover:text-blue-600 transition">{{ __('public.nav_docs') }}</a>
                        <a href="{{ route('contact') }}" class="hover:text-blue-600 transition">{{ __('public.nav_help') }}</a>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('locale.set') }}">
                        @csrf
                        <select name="locale" onchange="this.form.submit()" class="border border-slate-300 rounded-full px-3 py-2 text-[12px] font-extrabold text-slate-900 bg-white">
                            @php($loc = $currentLocale ?? app()->getLocale())
                            <option value="sw" @selected($loc === 'sw')>SW</option>
                            <option value="en" @selected($loc === 'en')>EN</option>
                            <option value="fr" @selected($loc === 'fr')>FR</option>
                            <option value="ar" @selected($loc === 'ar')>AR</option>
                        </select>
                    </form>

                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('dashboard') }}" class="font-bold text-sm text-slate-600 hover:text-blue-600 px-4">
                                {{ __('home.dashboard') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="font-bold text-sm text-slate-600 hover:text-blue-600 px-4">
                                {{ __('home.login') }}
                            </a>
                        @endauth
                    @endif

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="sn-btn text-white px-8 py-3 rounded-full font-black text-xs shadow-lg shadow-blue-200 hover:scale-105 transition transform uppercase tracking-widest">
                            {{ __('home.join_now') }}
                        </a>
                    @endif
                </div>
            </div>
        </nav>

        <section class="pt-16 pb-20 px-6 overflow-hidden">
            <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest mb-6 inline-block">
                        {{ __('home.hero_badge') }}
                    </span>

                    <h1 class="text-5xl md:text-6xl font-black text-slate-900 leading-[1] mb-6 tracking-tighter">
                        {{ __('home.hero_title') }}
                    </h1>

                    <p class="text-lg text-slate-600 mb-10 max-w-2xl leading-relaxed">
                        {{ __('home.hero_desc') }}
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
                        <div class="bg-white/80 backdrop-blur border border-slate-200 rounded-2xl p-4 shadow-sm">
                            <p class="text-[10px] uppercase font-black tracking-widest text-slate-400">{{ __('home.hero_stat_1_label') }}</p>
                            <p class="text-sm font-bold text-slate-900">{{ __('home.hero_stat_1_value') }}</p>
                        </div>
                        <div class="bg-white/80 backdrop-blur border border-slate-200 rounded-2xl p-4 shadow-sm">
                            <p class="text-[10px] uppercase font-black tracking-widest text-slate-400">{{ __('home.hero_stat_2_label') }}</p>
                            <p class="text-sm font-bold text-slate-900">{{ __('home.hero_stat_2_value') }}</p>
                        </div>
                        <div class="bg-white/80 backdrop-blur border border-slate-200 rounded-2xl p-4 shadow-sm">
                            <p class="text-[10px] uppercase font-black tracking-widest text-slate-400">{{ __('home.hero_stat_3_label') }}</p>
                            <p class="text-sm font-bold text-slate-900">{{ __('home.hero_stat_3_value') }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('ambulance') }}" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-bold hover:bg-blue-600 transition text-center shadow-xl">
                            {{ __('home.hero_btn_primary') }}
                        </a>
                        <a href="{{ route('contact') }}" class="bg-white border-2 border-slate-200 text-slate-900 px-8 py-4 rounded-2xl font-bold hover:border-blue-600 transition text-center">
                            {{ __('home.hero_btn_secondary') }}
                        </a>
                    </div>
                </div>

                <div class="relative">
                    <div class="floating w-full h-[420px] bg-blue-600 rounded-[2.5rem] shadow-2xl overflow-hidden relative border-[10px] border-white">
                        <div class="absolute inset-0 bg-gradient-to-br from-sky-300/20 via-blue-700/30 to-indigo-900/50"></div>
                        <div class="absolute bottom-6 left-6 right-6 bg-white/90 backdrop-blur p-6 rounded-2xl shadow-xl border border-blue-100">
                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest">{{ __('home.live_now') }}</p>
                            <p class="text-sm font-bold text-slate-900">{{ __('home.live_status') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="px-6 -mt-8 relative z-10 pb-20">
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-8">
                    <h2 class="text-2xl font-black text-slate-900 mb-4">{{ __('home.problem_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('home.problem_desc') }}</p>
                </div>
                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-8">
                    <h2 class="text-2xl font-black text-slate-900 mb-4">{{ __('home.solution_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('home.solution_desc') }}</p>
                </div>
                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-8">
                    <h2 class="text-2xl font-black text-slate-900 mb-4">{{ __('home.investment_title') }}</h2>
                    <p class="text-slate-600 leading-relaxed">{{ __('home.investment_desc') }}</p>
                </div>
            </div>
        </section>

        <section class="py-24 px-6">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <span class="bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-xs font-black uppercase tracking-widest inline-block mb-5">
                        {{ __('home.features_badge') }}
                    </span>
                    <h2 class="text-4xl md:text-5xl font-black text-slate-900 mb-5">{{ __('home.features_title') }}</h2>
                    <p class="text-lg text-slate-600 max-w-3xl mx-auto">{{ __('home.features_subtitle') }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-8">
                        <h3 class="text-xl font-black mb-3">{{ __('home.feature1_title') }}</h3>
                        <p class="text-slate-600">{{ __('home.feature1_desc') }}</p>
                    </div>
                    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-8">
                        <h3 class="text-xl font-black mb-3">{{ __('home.feature2_title') }}</h3>
                        <p class="text-slate-600">{{ __('home.feature2_desc') }}</p>
                    </div>
                    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-8">
                        <h3 class="text-xl font-black mb-3">{{ __('home.feature3_title') }}</h3>
                        <p class="text-slate-600">{{ __('home.feature3_desc') }}</p>
                    </div>

                    <div class="bg-white rounded-3xl shadow-lg border border-pink-50 p-8 relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-pink-50 rounded-full blur-3xl"></div>
                        <span class="inline-block px-3 py-1 rounded-full bg-pink-50 text-pink-600 text-[10px] font-black tracking-widest uppercase mb-3">
                            {{ __('home.feature_safegirl_tag') }}
                        </span>
                        <h3 class="text-xl font-black mb-3 text-slate-900">{{ __('home.feature_safegirl_title') }}</h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-6">{{ __('home.feature_safegirl_desc') }}</p>
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <p class="text-[11px] text-slate-500 italic leading-snug">{{ __('home.feature_safegirl_safety') }}</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-8">
                        <h3 class="text-xl font-black mb-3">{{ __('home.feature4_title') }}</h3>
                        <p class="text-slate-600">{{ __('home.feature4_desc') }}</p>
                    </div>
                    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-8">
                        <h3 class="text-xl font-black mb-3">{{ __('home.feature5_title') }}</h3>
                        <p class="text-slate-600">{{ __('home.feature5_desc') }}</p>
                    </div>
                </div>
            </div>
        </section>

        @if (isset($networkHospitals) && $networkHospitals->isNotEmpty())
            <div
                id="home-network-geo-msgs"
                class="hidden"
                data-geo-unsupported="{{ e(__('hospitals.geo_unsupported')) }}"
                data-geo-denied="{{ e(__('hospitals.geo_denied')) }}"
                data-geo-error="{{ e(__('hospitals.geo_error')) }}"
            ></div>

            <section class="border-y border-slate-100 bg-white px-4 py-16 sm:px-6 sm:py-24">
                <div class="mx-auto max-w-7xl">
                    <div class="mb-12 text-center">
                        <span class="mb-5 inline-block rounded-full bg-blue-100 px-4 py-2 text-xs font-black uppercase tracking-widest text-blue-700">
                            {{ __('home.network_badge') }}
                        </span>
                        <h2 class="mb-4 text-3xl font-black text-slate-900 sm:text-4xl md:text-5xl">{{ __('home.network_title') }}</h2>
                        <p class="mx-auto max-w-2xl text-base text-slate-600 sm:text-lg">{{ __('home.network_subtitle') }}</p>
                        <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                            <button
                                type="button"
                                onclick="homeNetworkUseLocation()"
                                class="rounded-2xl bg-slate-900 px-8 py-4 text-[10px] font-black uppercase tracking-widest text-white shadow-lg transition hover:bg-blue-600"
                            >
                                {{ __('home.network_use_location') }}
                            </button>
                            <a href="{{ route('hospitals') }}" class="inline-flex items-center justify-center rounded-2xl border-2 border-slate-200 bg-white px-8 py-4 text-[10px] font-black uppercase tracking-widest text-slate-900 transition hover:border-blue-600">
                                {{ __('home.network_view_all') }}
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($networkHospitals as $nh)
                            <div class="rounded-3xl border border-slate-100 bg-slate-50 p-6 shadow-sm">
                                <div class="mb-3 flex items-start justify-between gap-2">
                                    <h3 class="text-lg font-black leading-snug text-slate-900">{{ $nh->name }}</h3>
                                    <span class="shrink-0 rounded-full px-2 py-1 text-[8px] font-black uppercase tracking-widest {{ $nh->status === 'Online' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                        {{ $nh->status }}
                                    </span>
                                </div>
                                <p class="mb-2 text-xs font-bold italic text-slate-500">
                                    <i class="fas fa-map-marker-alt mr-1 text-blue-500"></i> {{ $nh->location }}
                                </p>
                                @if ($networkUserLat !== null && $networkUserLng !== null && $nh->getAttribute('distance_km') !== null)
                                    <p class="text-[10px] font-black uppercase tracking-widest text-blue-600">
                                        {{ __('home.network_distance_km', ['distance' => number_format((float) $nh->getAttribute('distance_km'), 1)]) }}
                                    </p>
                                @endif
                                <p class="mt-3 text-[10px] font-black uppercase tracking-widest text-blue-500">{{ $nh->type }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <script>
                function homeNetworkUseLocation() {
                    var box = document.getElementById('home-network-geo-msgs');
                    var u = new URL(@json(route('home')));
                    if (!navigator.geolocation) {
                        alert(box ? box.dataset.geoUnsupported : '');
                        return;
                    }
                    navigator.geolocation.getCurrentPosition(
                        function (pos) {
                            u.searchParams.set('lat', String(pos.coords.latitude));
                            u.searchParams.set('lng', String(pos.coords.longitude));
                            window.location.href = u.toString();
                        },
                        function (err) {
                            var msg = box ? box.dataset.geoError : '';
                            if (err && err.code === 1 && box) msg = box.dataset.geoDenied;
                            alert(msg);
                        },
                        { enableHighAccuracy: true, timeout: 15000, maximumAge: 60000 }
                    );
                }
            </script>
        @endif

        <section class="py-24 px-6 bg-white">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <span class="bg-slate-100 text-slate-700 px-4 py-2 rounded-full text-xs font-black uppercase tracking-widest inline-block mb-5">
                        {{ __('home.how_badge') }}
                    </span>
                    <h2 class="text-4xl md:text-5xl font-black text-slate-900 mb-5">{{ __('home.how_title') }}</h2>
                    <p class="text-lg text-slate-600 max-w-3xl mx-auto">{{ __('home.how_subtitle') }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-slate-50 rounded-3xl p-8 border border-slate-100 shadow-sm">
                        <div class="text-5xl font-black text-blue-600 mb-6">1</div>
                        <h3 class="text-2xl font-black mb-4">{{ __('home.step1_title') }}</h3>
                        <p class="text-slate-600 leading-relaxed">{{ __('home.step1_desc') }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-3xl p-8 border border-slate-100 shadow-sm">
                        <div class="text-5xl font-black text-blue-600 mb-6">2</div>
                        <h3 class="text-2xl font-black mb-4">{{ __('home.step2_title') }}</h3>
                        <p class="text-slate-600 leading-relaxed">{{ __('home.step2_desc') }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-3xl p-8 border border-slate-100 shadow-sm">
                        <div class="text-5xl font-black text-blue-600 mb-6">3</div>
                        <h3 class="text-2xl font-black mb-4">{{ __('home.step3_title') }}</h3>
                        <p class="text-slate-600 leading-relaxed">{{ __('home.step3_desc') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-24 px-6 bg-slate-50">
            <div class="max-w-7xl mx-auto">
                <div class="bg-slate-900 rounded-[2.5rem] p-8 md:p-12 text-white relative overflow-hidden shadow-2xl">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/10 blur-3xl rounded-full"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-cyan-400/10 blur-3xl rounded-full"></div>

                    <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        <div>
                            <span class="bg-white/10 text-blue-200 px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest inline-block mb-6">
                                {{ __('home.ready_badge') }}
                            </span>
                            <h2 class="text-3xl md:text-4xl font-black mb-6 leading-tight">{{ __('home.ready_title') }}</h2>
                            <p class="text-slate-400 text-base leading-relaxed mb-8">{{ __('home.ready_desc') }}</p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                                    <p class="text-[10px] uppercase tracking-widest font-black text-blue-300 mb-1">{{ __('home.ready_box1_title') }}</p>
                                    <p class="text-xs text-slate-300">{{ __('home.ready_box1_desc') }}</p>
                                </div>
                                <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                                    <p class="text-[10px] uppercase tracking-widest font-black text-blue-300 mb-1">{{ __('home.ready_box2_title') }}</p>
                                    <p class="text-xs text-slate-300">{{ __('home.ready_box2_desc') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-white text-slate-900 rounded-2xl p-5 shadow-xl">
                                <h3 class="font-black text-lg mb-1">{{ __('home.fund_card1_title') }}</h3>
                                <p class="text-slate-500 text-[11px] leading-snug">{{ __('home.fund_card1_desc') }}</p>
                            </div>

                            <div class="bg-blue-600 text-white rounded-2xl p-5 shadow-xl relative overflow-hidden border border-blue-400">
                                <h3 class="font-black text-lg mb-1">{{ __('home.fund_donate_title') }}</h3>
                                <p class="text-[9px] uppercase font-bold text-blue-200 mb-2">{{ __('home.fund_donate_ac') }}</p>
                                <div class="text-[10px] space-y-1 font-mono">
                                    <p class="bg-white/10 px-2 py-1 rounded">{{ __('home.fund_donate_1') }}</p>
                                    <p class="bg-white/10 px-2 py-1 rounded">{{ __('home.fund_donate_2') }}</p>
                                    <p class="bg-white/10 px-2 py-1 rounded">{{ __('home.fund_donate_3') }}</p>
                                    <p class="bg-white/10 px-2 py-1 rounded">{{ __('home.fund_donate_4') }}</p>
                                </div>
                            </div>

                            <div class="bg-white text-slate-900 rounded-2xl p-5 shadow-xl">
                                <h3 class="font-black text-lg mb-1">{{ __('home.fund_card2_title') }}</h3>
                                <p class="text-slate-500 text-[11px] leading-snug">{{ __('home.fund_card2_desc') }}</p>
                            </div>

                            <div class="bg-white text-slate-900 rounded-2xl p-5 shadow-xl">
                                <h3 class="font-black text-lg mb-1">{{ __('home.fund_card4_title') }}</h3>
                                <p class="text-slate-500 text-[11px] leading-snug">{{ __('home.fund_card4_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 px-6 bg-slate-50">
            <div class="max-w-6xl mx-auto text-center">
                <span class="bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-xs font-black uppercase tracking-widest inline-block mb-6">
                    {{ __('home.cta_badge') }}
                </span>
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 mb-4">{{ __('home.cta_title') }}</h2>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto mb-12">{{ __('home.cta_desc') }}</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 text-left">
                    <a href="{{ Route::has('register') ? route('register') : '#' }}" class="bg-slate-900 rounded-2xl p-6 shadow-xl hover:bg-blue-700 transition-all group">
                        <h3 class="text-white text-lg font-black mb-1">{{ __('home.cta_btn_1') }}</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">{{ __('home.cta_btn_1_desc') }}</p>
                        <div class="mt-4 text-blue-400 font-bold text-xs tracking-widest uppercase">{{ __('home.cta_btn_1_hint') }}</div>
                    </a>

                    <a href="{{ route('contact') }}" class="bg-white rounded-2xl p-6 border border-blue-100 shadow-lg hover:border-blue-600 transition-all group">
                        <h3 class="text-slate-900 text-lg font-black mb-1">{{ __('home.cta_btn_2') }}</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">{{ __('home.cta_btn_2_desc') }}</p>
                        <div class="mt-4 text-indigo-600 font-bold text-xs tracking-widest uppercase">{{ __('home.cta_btn_2_hint') }}</div>
                    </a>

                    <a href="{{ route('docs') }}" class="bg-white rounded-2xl p-6 border border-slate-200 shadow-lg hover:border-blue-600 transition-all group">
                        <h3 class="text-slate-900 text-lg font-black mb-1">{{ __('home.cta_btn_3') }}</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">{{ __('home.cta_btn_3_desc') }}</p>
                        <div class="mt-4 text-blue-600 font-bold text-xs tracking-widest uppercase">{{ __('home.cta_btn_3_hint') }}</div>
                    </a>
                </div>
            </div>
        </section>

        <footer class="bg-slate-900 text-slate-400 mt-20 py-20 px-8 border-t-[10px] border-blue-600">
            <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-12">
                <div class="col-span-1 sm:col-span-2 md:col-span-1">
                    <h2 class="text-3xl font-black text-white mb-6">Sema<span class="text-blue-500">Nami</span></h2>
                    <p class="text-sm leading-relaxed mb-8 opacity-70 italic">{{ __('home.footer_desc') }}</p>
                </div>

                <div>
                    <h3 class="text-white font-black mb-8 text-xs uppercase tracking-[0.2em] border-l-4 border-blue-600 pl-4">{{ __('home.footer_services') }}</h3>
                    <ul class="text-sm space-y-4 font-bold">
                        <li><a href="{{ route('ambulance') }}" class="hover:text-white transition">{{ __('home.footer_service_1') }}</a></li>
                        @if (! auth()->check() || in_array((string) (auth()->user()->role ?? ''), ['PATIENT', 'MEDICAL_TEAM'], true))
                            <li>
                                @guest
                                    <a href="{{ route('video-consult') }}" class="hover:text-white transition">{{ __('home.footer_service_2') }}</a>
                                @else
                                    @if ((string) auth()->user()->role === 'PATIENT')
                                        <a href="{{ route('patient.video-consult') }}" class="hover:text-white transition">{{ __('home.footer_service_2') }}</a>
                                    @elseif ((string) auth()->user()->role === 'MEDICAL_TEAM')
                                        <a href="{{ route('doctor.video-requests') }}" class="hover:text-white transition">{{ __('home.footer_service_2') }}</a>
                                    @endif
                                @endguest
                            </li>
                        @endif
                        <li><a href="{{ route('safe-girl') }}" class="hover:text-white transition text-pink-400">{{ __('home.footer_service_3') }}</a></li>
                        <li><a href="{{ route('ussd') }}" class="hover:text-white transition">{{ __('home.footer_service_4') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-black mb-8 text-xs uppercase tracking-[0.2em] border-l-4 border-blue-600 pl-4">{{ __('home.footer_company') }}</h3>
                    <ul class="text-sm space-y-4 font-bold">
                        <li><a href="{{ route('about') }}" class="hover:text-white transition">{{ __('home.footer_company_1') }}</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-white transition">{{ __('home.footer_company_2') }}</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-white transition">{{ __('home.footer_company_3') }}</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition">{{ __('home.footer_company_4') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-black mb-8 text-xs uppercase tracking-[0.2em] border-l-4 border-blue-600 pl-4">{{ __('home.footer_newsletter') }}</h3>
                    <p class="text-sm mb-6">{{ __('home.footer_newsletter_desc') }}</p>

                    <form action="{{ route('subscribe') }}" method="POST" class="flex bg-slate-800 p-1 rounded-xl">
                        @csrf
                        <input
                            type="email"
                            name="subscriber_email"
                            required
                            placeholder="{{ __('home.footer_email_placeholder') }}"
                            class="bg-transparent border-none p-3 text-xs w-full focus:outline-none text-white"
                        >
                        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg text-xs font-black hover:bg-blue-700 transition">
                            {{ __('home.footer_join') }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="max-w-7xl mx-auto pt-12 mt-12 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center text-[10px] uppercase tracking-widest font-black opacity-50">
                <p>{{ __('home.footer_bottom_text') }}</p>
                <div class="flex gap-8 mt-4 md:mt-0 italic">
                    <span>{{ __('home.footer_bottom_ussd') }}</span>
                    <span>{{ __('home.footer_bottom_gprs') }}</span>
                </div>
            </div>
        </footer>
    </body>
</html>

