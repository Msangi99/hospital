<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="scroll-smooth">
    <head>
        @include('partials.head')
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <style>
            body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
            .glass-nav { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); }
        </style>
    </head>
    <body class="bg-slate-50 text-slate-900">
        <nav class="sticky top-0 z-50 glass-nav border-b border-slate-100">
            <div class="max-w-[1400px] mx-auto px-8 py-4 flex justify-between items-center">
                <a href="{{ route('home') }}" class="text-3xl font-black text-slate-900 tracking-tighter">
                    {{ __('home.brand_a') }}<span class="text-blue-600">{{ __('home.brand_b') }}</span>
                </a>

                <div class="hidden lg:flex space-x-8 text-sm font-black uppercase tracking-widest text-slate-400">
                    <a href="{{ route('home') }}" class="hover:text-blue-600 transition">{{ __('home.nav_home') }}</a>
                    <a href="{{ route('services') }}" class="hover:text-blue-600 transition">{{ __('home.nav_services') }}</a>
                    <a href="{{ route('hospitals') }}" class="hover:text-blue-600 transition">{{ __('home.nav_hospitals') }}</a>
                    <a href="{{ route('about') }}" class="hover:text-blue-600 transition">{{ __('home.nav_about') }}</a>
                    <a href="{{ route('contact') }}" class="hover:text-blue-600 transition">{{ __('public.nav_help') }}</a>
                    <a href="{{ route('docs') }}" class="hover:text-blue-600 transition">{{ __('public.nav_docs') }}</a>
                </div>

                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('locale.set') }}">
                        @csrf
                        <select name="locale" onchange="this.form.submit()" class="border border-slate-200 rounded-full px-3 py-2 text-[12px] font-extrabold text-slate-900 bg-white">
                            @php($loc = $currentLocale ?? app()->getLocale())
                            <option value="sw" @selected($loc === 'sw')>SW</option>
                            <option value="en" @selected($loc === 'en')>EN</option>
                            <option value="fr" @selected($loc === 'fr')>FR</option>
                            <option value="ar" @selected($loc === 'ar')>AR</option>
                        </select>
                    </form>

                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="text-xs font-black uppercase bg-blue-600 text-white px-8 py-3 rounded-full shadow-lg">
                            {{ __('home.login') }}
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

