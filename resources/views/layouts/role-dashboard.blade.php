<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @auth
            @if (in_array((string) auth()->user()->role, ['MEDICAL_TEAM', 'PATIENT'], true))
                @if ((string) auth()->user()->role === 'MEDICAL_TEAM')
                    <meta name="doctor-broadcast-id" content="{{ auth()->id() }}">
                    <script>
                        window.__videoToastLabels = {
                            title: @json(__('roleui.video_toast_title')),
                            join: @json(__('roleui.video_toast_join')),
                            dismiss: @json(__('roleui.video_toast_dismiss')),
                        };
                    </script>
                @endif
                @vite(['resources/js/app.js'])
            @endif
        @endauth
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    </head>
    @php
        $currentUser = auth()->user();
        $hospitalName = null;

        if ($currentUser) {
            if ((string) $currentUser->role === 'HOSPITAL_OWNER') {
                $hospitalName = \App\Models\Hospital::query()
                    ->where('owner_user_id', $currentUser->id)
                    ->value('name');
            } else {
                $hospitalName = \App\Models\Hospital::query()
                    ->join('hospital_worker_memberships', 'hospital_worker_memberships.hospital_id', '=', 'hospitals.id')
                    ->where('hospital_worker_memberships.user_id', $currentUser->id)
                    ->value('hospitals.name');
            }
        }
    @endphp
    <body class="h-screen overflow-hidden bg-white text-slate-900">
        @auth
            @if ((string) auth()->user()->role === 'MEDICAL_TEAM')
                <div
                    id="doctor-video-toast"
                    class="fixed left-1/2 top-6 z-[70] hidden w-[calc(100%-2rem)] max-w-lg -translate-x-1/2 px-2"
                    role="status"
                    aria-live="polite"
                ></div>
                @if (! empty($doctorInitialVideoToast))
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            if (typeof window.doctorVideoToastShow === 'function') {
                                window.doctorVideoToastShow(@json($doctorInitialVideoToast));
                            }
                        });
                    </script>
                @endif
            @endif
        @endauth
        <div class="h-screen flex overflow-hidden">
            <div
                id="role-sidebar-backdrop"
                class="fixed inset-0 z-40 bg-slate-950/60 opacity-0 pointer-events-none transition-opacity duration-200 lg:hidden"
                aria-hidden="true"
            ></div>

            <aside
                id="role-sidebar"
                class="fixed inset-y-0 left-0 z-50 flex h-screen w-72 max-w-[min(18rem,88vw)] flex-col border-r border-slate-900 bg-slate-950 text-white transition-transform duration-200 ease-out -translate-x-full max-lg:[&.is-open]:translate-x-0 lg:sticky lg:top-0 lg:z-auto lg:max-w-none lg:translate-x-0"
                aria-label="{{ __('roleui.main_navigation') }}"
            >
                <div class="flex items-start justify-between gap-3 border-b border-white/10 px-6 py-6 lg:block">
                    <div class="min-w-0">
                        <a href="{{ route('home') }}" class="block text-2xl font-black tracking-tighter italic">
                            {{ __('home.brand_a') }}<span class="text-blue-500">{{ __('home.brand_b') }}</span>
                        </a>
                        <div class="mt-2 text-[10px] font-black uppercase tracking-[0.35em] text-slate-400">
                            {{ $sidebarTitle ?? __('roleui.platform') }}
                        </div>
                    </div>
                    <button
                        type="button"
                        id="role-sidebar-close"
                        class="rounded-xl p-2 text-slate-300 hover:bg-white/10 hover:text-white lg:hidden"
                        aria-label="{{ __('roleui.close_menu') }}"
                    >
                        <i class="fas fa-times text-lg" aria-hidden="true"></i>
                    </button>
                </div>

                <nav class="flex-1 space-y-2 overflow-y-auto p-4">
                    {{ $sidebar ?? '' }}
                </nav>

                <div class="mt-auto space-y-2 border-t border-white/10 p-4">
                    <a
                        href="{{ route('profile.edit') }}"
                        class="flex w-full items-center justify-center gap-3 rounded-2xl bg-white/5 px-4 py-3 text-xs font-black uppercase tracking-widest text-white transition hover:bg-white/10"
                    >
                        <i class="fas fa-user-cog" aria-hidden="true"></i>
                        <span>{{ __('roleui.sidebar_account') }}</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center justify-center gap-3 rounded-2xl bg-white/5 px-4 py-3 text-xs font-black uppercase tracking-widest transition hover:bg-white/10">
                            <i class="fas fa-power-off"></i>
                            <span>{{ __('roleui.sidebar_logout') }}</span>
                        </button>
                    </form>
                </div>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
                <header class="sticky top-0 z-30 border-b border-slate-100 bg-white/90 backdrop-blur">
                    <div class="mx-auto flex max-w-[1440px] items-center justify-between gap-3 px-4 py-3 sm:px-6 sm:py-4">
                        <div class="flex min-w-0 items-center gap-2 sm:gap-3">
                            <button
                                type="button"
                                id="role-sidebar-open"
                                class="inline-flex shrink-0 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 p-3 text-slate-800 hover:border-blue-200 hover:bg-blue-50 lg:hidden"
                                aria-controls="role-sidebar"
                                aria-expanded="false"
                                aria-label="{{ __('roleui.open_menu') }}"
                            >
                                <i class="fas fa-bars text-lg" aria-hidden="true"></i>
                            </button>
                            <div class="min-w-0">
                                <div class="truncate text-xl font-black tracking-tighter italic lg:hidden">
                                    {{ __('home.brand_a') }}<span class="text-blue-600">{{ __('home.brand_b') }}</span>
                                </div>
                                <div class="hidden truncate text-sm font-black uppercase tracking-widest text-slate-400 lg:block">
                                    {{ $title ?? '' }}
                                </div>
                                <div class="truncate text-xs font-black uppercase tracking-widest text-slate-500 lg:hidden">
                                    {{ $title ?? '' }}
                                </div>
                            </div>
                        </div>

                        <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                            <a
                                href="{{ route('profile.edit') }}"
                                class="hidden items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50 px-3 py-2 transition hover:border-blue-200 hover:bg-blue-50/80 sm:flex"
                                title="{{ __('roleui.sidebar_account') }}"
                            >
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-900 text-xs font-black text-white">
                                    {{ auth()->user()?->initials() }}
                                </div>
                                <div class="max-w-[10rem] leading-tight">
                                    <div class="truncate text-sm font-black text-slate-900">{{ auth()->user()?->name }}</div>
                                    <div class="truncate text-[10px] font-black uppercase tracking-widest text-slate-400">{{ auth()->user()?->role }}</div>
                                    @if ($hospitalName)
                                        <div class="truncate text-[10px] font-black uppercase tracking-widest text-blue-500">{{ $hospitalName }}</div>
                                    @endif
                                </div>
                            </a>
                            <a href="{{ route('home') }}" class="whitespace-nowrap text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-blue-600 sm:text-xs">
                                {{ __('roleui.back_home') }}
                            </a>
                        </div>
                    </div>
                </header>

                <main class="mx-auto w-full max-w-[1440px] flex-1 overflow-y-auto px-4 py-6 sm:px-6 sm:py-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script>
            (function () {
                var sidebar = document.getElementById('role-sidebar');
                var backdrop = document.getElementById('role-sidebar-backdrop');
                var openBtn = document.getElementById('role-sidebar-open');
                var closeBtn = document.getElementById('role-sidebar-close');

                function isLarge() {
                    return window.matchMedia('(min-width: 1024px)').matches;
                }

                function setOpen(open) {
                    if (!sidebar || !backdrop || !openBtn) return;
                    if (isLarge()) {
                        sidebar.classList.remove('is-open');
                        backdrop.classList.add('opacity-0', 'pointer-events-none');
                        backdrop.classList.remove('opacity-100');
                        document.body.classList.remove('overflow-hidden');
                        openBtn.setAttribute('aria-expanded', 'false');
                        return;
                    }
                    if (open) {
                        sidebar.classList.add('is-open');
                        backdrop.classList.remove('opacity-0', 'pointer-events-none');
                        backdrop.classList.add('opacity-100');
                        document.body.classList.add('overflow-hidden');
                        openBtn.setAttribute('aria-expanded', 'true');
                    } else {
                        sidebar.classList.remove('is-open');
                        backdrop.classList.add('opacity-0', 'pointer-events-none');
                        backdrop.classList.remove('opacity-100');
                        document.body.classList.remove('overflow-hidden');
                        openBtn.setAttribute('aria-expanded', 'false');
                    }
                }

                openBtn?.addEventListener('click', function () {
                    setOpen(true);
                });
                closeBtn?.addEventListener('click', function () {
                    setOpen(false);
                });
                backdrop?.addEventListener('click', function () {
                    setOpen(false);
                });

                window.addEventListener('resize', function () {
                    if (isLarge()) {
                        setOpen(false);
                    }
                });

                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' && sidebar?.classList.contains('is-open')) {
                        setOpen(false);
                    }
                });

                sidebar?.querySelectorAll('a[href]').forEach(function (link) {
                    link.addEventListener('click', function () {
                        if (!isLarge()) setOpen(false);
                    });
                });
            })();
        </script>
    </body>
</html>
