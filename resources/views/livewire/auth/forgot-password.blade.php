<x-layouts::auth :title="__('authui.forgot_title')">
    <div>
        <h2 class="text-center text-slate-900 mb-2 font-extrabold tracking-tight text-2xl">{{ __('authui.forgot_header') }}</h2>
        <p class="text-center text-slate-500 text-sm font-semibold mb-6">{{ __('authui.forgot_desc') }}</p>

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

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4 relative">
                <i class="fas fa-envelope absolute left-5 top-4 text-slate-300"></i>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('authui.email_placeholder') }}" required autofocus class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-blue-500 transition font-semibold text-sm">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-extrabold text-xs tracking-widest uppercase hover:bg-slate-900 transition" data-test="email-password-reset-link-button">
                {{ __('authui.email_reset_link') }}
            </button>
        </form>

        <div class="text-center mt-8 text-sm text-slate-500 font-semibold">
            {{ __('authui.or_return_to') }}
            <a href="{{ route('login') }}" class="text-blue-600 font-extrabold hover:text-slate-900">{{ __('authui.log_in_lower') }}</a>
        </div>
    </div>
</x-layouts::auth>
