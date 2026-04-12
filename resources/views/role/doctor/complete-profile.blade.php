@php($sidebarTitle = __('roleui.medical_staff'))

@component('layouts.role-dashboard', ['title' => __('roleui.complete_profile_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.doctor._sidebar', ['active' => 'profile'])
    @endslot

    <div class="mx-auto max-w-2xl rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-10">
        <h1 class="mb-2 text-2xl font-black tracking-tighter sm:text-3xl">{{ __('roleui.complete_profile_title') }}</h1>
        <p class="mb-8 font-bold text-slate-500">{{ __('roleui.complete_profile_desc') }}</p>

        @if (session('status'))
            <div class="mb-6 rounded-2xl border border-green-100 bg-green-50 p-4 text-sm font-bold text-green-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 p-4 text-sm font-bold text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('doctor.complete-profile.submit') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="mb-2 block px-2 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.staff_type') }}</label>
                <select name="staff_type" required class="w-full rounded-2xl border border-slate-100 bg-white p-4 text-sm font-bold outline-none transition focus:border-blue-500">
                    @php($staffTypeValue = old('staff_type', $profile->staff_type ?? ($defaultStaffType ?? 'MD')))
                    <option value="MD" @selected($staffTypeValue === 'MD')>{{ __('roleui.staff_md') }}</option>
                    <option value="GYNO" @selected($staffTypeValue === 'GYNO')>{{ __('roleui.staff_gyno') }}</option>
                    <option value="MIDWIFE" @selected($staffTypeValue === 'MIDWIFE')>{{ __('roleui.staff_midwife') }}</option>
                    <option value="NURSE" @selected($staffTypeValue === 'NURSE')>{{ __('roleui.staff_nurse') }}</option>
                    <option value="SPECIALIST" @selected($staffTypeValue === 'SPECIALIST')>{{ __('roleui.staff_specialist') }}</option>
                    <option value="AMBULANCE_STAFF" @selected($staffTypeValue === 'AMBULANCE_STAFF')>{{ __('roleui.staff_ambulance') }}</option>
                </select>
            </div>

            <div>
                <label class="mb-2 block px-2 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.specialization') }}</label>
                <input name="specialization" value="{{ old('specialization', $profile->specialization ?? '') }}" required class="w-full rounded-2xl border border-slate-100 bg-white p-4 text-sm font-bold outline-none transition focus:border-blue-500" placeholder="{{ __('roleui.specialization_placeholder') }}">
            </div>

            <div>
                <label class="mb-2 block px-2 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.registration_no') }}</label>
                <input name="registration_no" value="{{ old('registration_no', $profile->registration_no ?? '') }}" required class="w-full rounded-2xl border border-slate-100 bg-white p-4 text-sm font-bold outline-none transition focus:border-blue-500" placeholder="{{ __('roleui.registration_no_placeholder') }}">
            </div>

            <div>
                <label class="mb-2 block px-2 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.license_copy') }}</label>
                <input type="file" name="license_copy" class="w-full rounded-2xl border border-slate-100 bg-white p-4 text-sm font-bold outline-none transition focus:border-blue-500">
                @if(! empty($profile?->license_copy))
                    <p class="mt-2 text-xs font-bold text-slate-500">{{ __('roleui.doctor_license_replace_hint') }}</p>
                @endif
            </div>

            <button type="submit" class="w-full rounded-2xl bg-slate-900 p-5 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-blue-600">
                {{ __('roleui.submit_profile') }}
            </button>
        </form>
    </div>
@endcomponent
