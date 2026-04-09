<x-layouts::auth :title="__('authui.login_title')">
    <div>
        <div class="w-16 h-16 bg-blue-600 text-white rounded-[1.2rem] flex items-center justify-center text-2xl mx-auto mb-6">
            <i class="fas fa-stethoscope"></i>
        </div>

        <h2 class="text-center text-slate-900 mb-2 font-extrabold tracking-tight text-2xl">
            {{ __('authui.login_header') }}
        </h2>

        <p class="text-center text-slate-500 text-sm font-semibold mb-6">
            {{ __('authui.login_desc') }}
        </p>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-50 text-red-600 text-xs font-bold border border-red-100 text-center">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('status'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100 text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <div class="mb-4 relative">
                <i class="fas fa-envelope absolute left-5 top-4 text-slate-300"></i>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('authui.email_placeholder') }}" required autofocus autocomplete="email" class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-blue-500 transition font-semibold text-sm">
            </div>

            <div class="mb-4 relative">
                <i class="fas fa-key absolute left-5 top-4 text-slate-300"></i>
                <input type="password" name="password" placeholder="{{ __('authui.password_placeholder') }}" required autocomplete="current-password" class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-blue-500 transition font-semibold text-sm">
            </div>

            <div class="flex items-center justify-between mb-4">
                <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300">
                    {{ __('authui.remember_me') }}
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-extrabold text-blue-600 hover:text-slate-900">
                        {{ __('authui.forgot_password') }}
                    </a>
                @endif
            </div>

            <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-extrabold text-xs tracking-widest uppercase hover:bg-blue-600 transition" data-test="login-button">
                {{ __('authui.login_title') }}
            </button>
        </form>

        @if (Route::has('register'))
            <div class="text-center mt-8 text-sm text-slate-500 font-semibold">
                {{ __('authui.no_account') }}
                <a href="{{ route('register') }}" class="text-blue-600 font-extrabold hover:text-slate-900">{{ __('authui.sign_up') }}</a>
            </div>
        @endif
    </div>
</x-layouts::auth>
