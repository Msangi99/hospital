<x-layouts::auth :title="__('authui.register_title')">
    <div>
        <div class="text-center mb-8">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-2">{{ __('authui.register_header') }}</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-50 text-red-600 text-xs font-bold border border-red-100 text-center">
                <i class="fas fa-exclamation-triangle mr-2"></i> {{ $errors->first() }}
            </div>
        @endif

        @if (session('status'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100 text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
            @csrf
            <div class="relative">
                <i class="fas fa-user absolute left-5 top-4 text-slate-300"></i>
                <input type="text" name="full_name" value="{{ old('full_name') }}" placeholder="{{ __('authui.full_name_placeholder') }}" required class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:border-blue-500 transition font-medium text-sm">
            </div>

            <div class="relative">
                <i class="fas fa-envelope absolute left-5 top-4 text-slate-300"></i>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('authui.email_placeholder') }}" required autocomplete="email" class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:border-blue-500 transition font-medium text-sm">
            </div>

            <div class="relative">
                <i class="fas fa-phone absolute left-5 top-4 text-slate-300"></i>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="{{ __('authui.phone_placeholder') }}" autocomplete="tel" class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:border-blue-500 transition font-medium text-sm">
            </div>

            <div class="relative">
                <i class="fas fa-briefcase absolute left-5 top-4 text-slate-300"></i>
                <select name="role" required class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:border-blue-500 transition font-medium text-sm appearance-none">
                    <option value="PATIENT" @selected(old('role') === 'PATIENT')>{{ __('authui.role_patient') }}</option>
                    <option value="MEDICAL_TEAM" @selected(old('role') === 'MEDICAL_TEAM')>{{ __('authui.role_medical_team') }}</option>
                    <option value="AMBULANCE" @selected(old('role') === 'AMBULANCE')>{{ __('authui.role_ambulance') }}</option>
                    <option value="FACILITY" @selected(old('role') === 'FACILITY')>{{ __('authui.role_facility') }}</option>
                </select>
            </div>

            <div class="relative">
                <i class="fas fa-lock absolute left-5 top-4 text-slate-300"></i>
                <input type="password" name="password" placeholder="{{ __('authui.password_placeholder') }}" required autocomplete="new-password" class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:border-blue-500 transition font-medium text-sm">
            </div>

            <div class="relative">
                <i class="fas fa-lock absolute left-5 top-4 text-slate-300"></i>
                <input type="password" name="password_confirmation" placeholder="{{ __('authui.confirm_password_label') }}" required autocomplete="new-password" class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:border-blue-500 transition font-medium text-sm">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-5 rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl shadow-blue-100 hover:bg-slate-900 transition transform hover:scale-[1.02] active:scale-95" data-test="register-user-button">
                {{ __('authui.create_account') }}
            </button>
        </form>

        <p class="text-center mt-8 text-xs font-bold text-slate-400 uppercase tracking-widest">
            {{ __('authui.already_have_account') }} <a href="{{ route('login') }}" class="text-blue-600 ml-1">{{ __('authui.login_title') }}</a>
        </p>
    </div>
</x-layouts::auth>
