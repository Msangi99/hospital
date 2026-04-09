<x-layouts::auth :title="__('authui.two_factor_title')">
    <div
        x-cloak
        x-data="{
            showRecoveryInput: @js($errors->has('recovery_code')),
            toggleInput() { this.showRecoveryInput = !this.showRecoveryInput },
        }"
    >
        <h2 class="text-center text-slate-900 mb-2 font-extrabold tracking-tight text-2xl" x-show="!showRecoveryInput">
            {{ __('authui.auth_code_title') }}
        </h2>
        <h2 class="text-center text-slate-900 mb-2 font-extrabold tracking-tight text-2xl" x-show="showRecoveryInput">
            {{ __('authui.recovery_code_title') }}
        </h2>

        <p class="text-center text-slate-500 text-sm font-semibold mb-6" x-show="!showRecoveryInput">
            {{ __('authui.auth_code_desc') }}
        </p>
        <p class="text-center text-slate-500 text-sm font-semibold mb-6" x-show="showRecoveryInput">
            {{ __('authui.recovery_code_desc') }}
        </p>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-50 text-red-600 text-xs font-bold border border-red-100 text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('two-factor.login.store') }}">
            @csrf

            <div class="space-y-4">
                <div x-show="!showRecoveryInput" class="relative">
                    <i class="fas fa-shield-halved absolute left-5 top-4 text-slate-300"></i>
                    <input
                        type="text"
                        name="code"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        placeholder="{{ __('authui.otp_label') }}"
                        class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-blue-500 transition font-semibold text-sm"
                    >
                </div>

                <div x-show="showRecoveryInput" class="relative">
                    <i class="fas fa-key absolute left-5 top-4 text-slate-300"></i>
                    <input
                        type="text"
                        name="recovery_code"
                        autocomplete="one-time-code"
                        placeholder="{{ __('authui.recovery_code_title') }}"
                        class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-blue-500 transition font-semibold text-sm"
                    >
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-extrabold text-xs tracking-widest uppercase hover:bg-slate-900 transition">
                    {{ __('authui.continue') }}
                </button>
            </div>

            <div class="mt-6 text-center text-sm text-slate-500 font-semibold">
                <span class="opacity-70">{{ __('authui.or_you_can') }}</span>
                <button type="button" class="text-blue-600 font-extrabold hover:text-slate-900 underline" x-show="!showRecoveryInput" @click="toggleInput()">
                    {{ __('authui.login_using_recovery') }}
                </button>
                <button type="button" class="text-blue-600 font-extrabold hover:text-slate-900 underline" x-show="showRecoveryInput" @click="toggleInput()">
                    {{ __('authui.login_using_auth_code') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts::auth>
