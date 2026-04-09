<x-layouts.public>
    <section class="py-16 px-6 bg-slate-50">
        <div class="max-w-2xl mx-auto bg-white rounded-[3rem] border border-slate-100 shadow-2xl p-10">
            <h1 class="text-3xl font-black tracking-tighter mb-2">{{ __('roleui.complete_profile_title') }}</h1>
            <p class="text-slate-500 font-bold mb-8">{{ __('roleui.complete_profile_desc') }}</p>

            @if (session('status'))
                <div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-100 text-green-700 font-bold text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-100 text-red-700 font-bold text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('doctor.complete-profile.submit') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 px-2">{{ __('roleui.staff_type') }}</label>
                    <select name="staff_type" required class="w-full bg-slate-50 border border-slate-100 p-4 rounded-2xl font-bold text-sm outline-none focus:border-blue-500 transition">
                        <option value="MD">{{ __('roleui.staff_md') }}</option>
                        <option value="GYNO">{{ __('roleui.staff_gyno') }}</option>
                        <option value="MIDWIFE">{{ __('roleui.staff_midwife') }}</option>
                        <option value="NURSE">{{ __('roleui.staff_nurse') }}</option>
                        <option value="SPECIALIST">{{ __('roleui.staff_specialist') }}</option>
                        <option value="AMBULANCE_STAFF">{{ __('roleui.staff_ambulance') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 px-2">{{ __('roleui.specialization') }}</label>
                    <input name="specialization" value="{{ old('specialization') }}" required class="w-full bg-slate-50 border border-slate-100 p-4 rounded-2xl font-bold text-sm outline-none focus:border-blue-500 transition" placeholder="{{ __('roleui.specialization_placeholder') }}">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 px-2">{{ __('roleui.registration_no') }}</label>
                    <input name="registration_no" value="{{ old('registration_no') }}" required class="w-full bg-slate-50 border border-slate-100 p-4 rounded-2xl font-bold text-sm outline-none focus:border-blue-500 transition" placeholder="{{ __('roleui.registration_no_placeholder') }}">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 px-2">{{ __('roleui.license_copy') }}</label>
                    <input type="file" name="license_copy" required class="w-full bg-slate-50 border border-slate-100 p-4 rounded-2xl font-bold text-sm outline-none focus:border-blue-500 transition">
                </div>

                <button type="submit" class="w-full bg-slate-900 text-white p-5 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-blue-600 transition">
                    {{ __('roleui.submit_profile') }}
                </button>
            </form>
        </div>
    </section>
</x-layouts.public>

