<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="scroll-smooth">
    <head>
        @include('partials.head')
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <style>
            :root {
                --sn-dark: #0f172a;
                --sn-primary: #2563eb;
                --sn-light: #38bdf8;
            }
            body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
            .glass-header { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(15px); }
            .sn-btn { background: linear-gradient(135deg, var(--sn-primary), var(--sn-light)); }
        </style>
        @stack('head-scripts')
    </head>
    <body class="bg-slate-50 text-slate-900">
        <div class="bg-[var(--sn-dark)] text-white text-[10px] py-2 px-6 sm:px-8 flex justify-between items-center tracking-widest uppercase font-bold relative z-[60]">
            <div class="flex gap-6 items-center">
                <span class="flex items-center">
                    <span class="text-sky-400 mr-2">?</span>
                    <span>{{ __('home.top_gprs') }}</span>
                </span>
                <span class="hidden md:block">
                    <span class="text-emerald-400 mr-2">?</span>
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

        <main>
            {{ $slot }}
        </main>

        <footer class="py-20 text-center bg-white border-t border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">{{ __('public.footer_small') }}</p>
        </footer>
    </body>
</html>