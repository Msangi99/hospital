<x-layouts::auth :title="__('authui.reset_title')">
    <div>
        <h2 class="text-center text-slate-900 mb-2 font-extrabold tracking-tight text-2xl">{{ __('authui.reset_header') }}</h2>
        <p class="text-center text-slate-500 text-sm font-semibold mb-6">{{ __('authui.reset_desc') }}</p>

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

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <!-- Token -->
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <div class="mb-4 relative">
                <i class="fas fa-envelope absolute left-5 top-4 text-slate-300"></i>
                <input type="email" name="email" value="{{ request('email') }}" required autocomplete="email" class="w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-2xl outline-none focus:border-blue-500 transition font-semibold text-sm">
            </div>

            <div class="mb-4 relative">
                <i class="fas fa-lock absolute left-5 top-4 text-slate-300"></i>
                <input type="password" name="password" placeholder="{{ __('authui.password_placeholder') }}" required autocomplete="new-password" class="w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-2xl outline-none focus:border-blue-500 transition font-semibold text-sm">
            </div>

            <div class="mb-6 relative">
                <i class="fas fa-lock absolute left-5 top-4 text-slate-300"></i>
                <input type="password" name="password_confirmation" placeholder="{{ __('authui.confirm_password_label') }}" required autocomplete="new-password" class="w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-2xl outline-none focus:border-blue-500 transition font-semibold text-sm">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-extrabold text-xs tracking-widest uppercase hover:bg-slate-900 transition" data-test="reset-password-button">
                {{ __('authui.reset_button') }}
            </button>
        </form>
    </div>
</x-layouts::auth>
