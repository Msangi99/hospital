<x-layouts::auth :title="__('authui.verify_title')">
    <div>
        <h2 class="text-center text-slate-900 mb-4 font-extrabold tracking-tight text-2xl">{{ __('authui.verify_title') }}</h2>

        <p class="text-center text-slate-600 text-sm font-semibold mb-6">
            {{ __('authui.verify_prompt') }}
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100 text-center">
                {{ __('authui.verify_sent') }}
            </div>
        @endif

        <div class="flex flex-col items-center justify-between space-y-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-extrabold text-xs tracking-widest uppercase hover:bg-slate-900 transition">
                    {{ __('authui.verify_resend') }}
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm font-extrabold text-slate-600 hover:text-blue-600" data-test="logout-button">
                    {{ __('authui.logout') }}
                </button>
            </form>
        </div>
    </div>
</x-layouts::auth>
