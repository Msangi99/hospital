@php
    $user = auth()->user();
    $pwdTimeout = (int) config('auth.password_timeout', 10800);
    $pwdConfirmedAt = session('auth.password_confirmed_at');
    $passwordRecentlyConfirmed = is_numeric($pwdConfirmedAt) && (time() - (int) $pwdConfirmedAt) < $pwdTimeout;
    $hasSecret = filled($user->two_factor_secret);
@endphp

<div class="mb-10 space-y-6 rounded-[2rem] border border-amber-200 bg-amber-50/90 p-6 shadow-sm dark:border-amber-900/40 dark:bg-amber-950/30">
    @if (request()->query('admin_mfa') === '1' && session('status'))
        <div class="rounded-2xl border border-amber-300 bg-white px-4 py-3 text-sm font-bold text-amber-950 dark:border-amber-700 dark:bg-amber-950/60 dark:text-amber-50">
            {{ __('roleui.superadmin_mfa_redirect_banner') }}
            <span class="mt-1 block text-xs font-semibold text-amber-800 dark:text-amber-200">{{ session('status') }}</span>
        </div>
    @endif

    <div>
        <h2 class="text-lg font-black tracking-tight text-slate-900 dark:text-white">{{ __('roleui.superadmin_mfa_panel_title') }}</h2>
        <p class="mt-2 text-sm font-semibold text-slate-600 dark:text-amber-100/90">{{ __('roleui.superadmin_mfa_panel_intro') }}</p>
    </div>

    @if (! $passwordRecentlyConfirmed)
        <p class="text-sm font-bold text-slate-700 dark:text-amber-50">
            {{ __('roleui.superadmin_sidebar_mfa_notice') }}
        </p>
        <a
            href="{{ route('settings.prepare-admin-access') }}"
            class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-xs font-black uppercase tracking-widest text-white transition hover:bg-slate-800 dark:bg-amber-400 dark:text-slate-900 dark:hover:bg-amber-300"
        >
            {{ __('roleui.superadmin_mfa_continue_password') }}
        </a>
    @elseif (! $hasSecret)
        <form method="POST" action="{{ route('two-factor.enable') }}" class="space-y-4">
            @csrf
            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-xs font-black uppercase tracking-widest text-white transition hover:bg-slate-800 dark:bg-amber-400 dark:text-slate-900 dark:hover:bg-amber-300"
            >
                {{ __('roleui.superadmin_mfa_enable_button') }}
            </button>
        </form>
    @else
        <div class="space-y-4">
            <p class="text-sm font-bold text-slate-700 dark:text-amber-50">{{ __('roleui.superadmin_mfa_scan_hint') }}</p>
            <div
                id="superadmin-mfa-qr"
                class="flex min-h-[12rem] items-center justify-center rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900"
            ></div>
            <form method="POST" action="{{ route('two-factor.confirm') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="superadmin-mfa-code" class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-amber-200">
                        {{ __('roleui.superadmin_mfa_code_label') }}
                    </label>
                    <input
                        id="superadmin-mfa-code"
                        name="code"
                        type="text"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        required
                        class="w-full max-w-xs rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold tracking-widest text-slate-900 outline-none transition focus:border-blue-500 dark:border-slate-600 dark:bg-slate-950 dark:text-white"
                    />
                </div>
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-xs font-black uppercase tracking-widest text-white transition hover:bg-blue-700"
                >
                    {{ __('roleui.superadmin_mfa_confirm_button') }}
                </button>
            </form>
        </div>
        <script>
            (function () {
                var host = document.getElementById('superadmin-mfa-qr');
                if (!host) return;
                var token = document.querySelector('meta[name="csrf-token"]');
                fetch(@json(route('two-factor.qr-code')), {
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token ? token.getAttribute('content') || '' : '',
                    },
                })
                    .then(function (r) {
                        return r.json();
                    })
                    .then(function (data) {
                        if (data && data.svg) {
                            host.innerHTML = data.svg;
                        }
                    })
                    .catch(function () {});
            })();
        </script>
    @endif
</div>
