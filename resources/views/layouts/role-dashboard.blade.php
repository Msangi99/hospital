<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    </head>
    <body class="min-h-screen bg-white text-slate-900">
        <div class="min-h-screen flex">
            <aside class="w-72 bg-slate-950 text-white border-r border-slate-900 hidden lg:flex flex-col">
                <div class="px-6 py-6 border-b border-white/10">
                    <a href="{{ route('home') }}" class="text-2xl font-black tracking-tighter italic">
                        {{ __('home.brand_a') }}<span class="text-blue-500">{{ __('home.brand_b') }}</span>
                    </a>
                    <div class="mt-2 text-[10px] font-black uppercase tracking-[0.35em] text-slate-400">
                        {{ $sidebarTitle ?? __('roleui.platform') }}
                    </div>
                </div>

                <nav class="p-4 space-y-2">
                    {{ $sidebar ?? '' }}
                </nav>

                <div class="mt-auto p-4 border-t border-white/10">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-3 rounded-2xl px-4 py-3 font-black text-xs uppercase tracking-widest bg-white/5 hover:bg-white/10 transition">
                            <i class="fas fa-power-off"></i>
                            <span>{{ __('roleui.sidebar_logout') }}</span>
                        </button>
                    </form>
                </div>
            </aside>

            <div class="flex-1 min-w-0">
                <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-100">
                    <div class="max-w-[1440px] mx-auto px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="lg:hidden text-xl font-black tracking-tighter italic">
                                {{ __('home.brand_a') }}<span class="text-blue-600">{{ __('home.brand_b') }}</span>
                            </div>
                            <div class="hidden lg:block text-sm font-black uppercase tracking-widest text-slate-400">
                                {{ $title ?? '' }}
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="hidden sm:flex items-center gap-3 bg-slate-50 border border-slate-100 rounded-2xl px-4 py-2">
                                <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center text-xs font-black">
                                    {{ auth()->user()?->initials() }}
                                </div>
                                <div class="leading-tight">
                                    <div class="text-sm font-black text-slate-900">{{ auth()->user()?->name }}</div>
                                    <div class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ auth()->user()?->role }}</div>
                                </div>
                            </div>
                            <a href="{{ route('home') }}" class="text-xs font-black uppercase tracking-widest text-slate-500 hover:text-blue-600 transition">
                                {{ __('roleui.back_home') }}
                            </a>
                        </div>
                    </div>
                </header>

                <main class="max-w-[1440px] mx-auto px-6 py-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>

