@php
    $active = $active ?? 'dashboard';
    $linkInactive = 'flex items-center gap-3 rounded-2xl px-4 py-3 font-black text-xs uppercase tracking-widest text-slate-300 hover:text-white hover:bg-white/10 transition';
    $linkActive = 'flex items-center gap-3 rounded-2xl px-4 py-3 font-black text-xs uppercase tracking-widest bg-white/10 hover:bg-white/15 transition';

    $doctorProfileNav = null;
    $doctorShowCompleteProfileNav = false;
    if (auth()->check() && (string) auth()->user()->role === 'MEDICAL_TEAM') {
        $doctorProfileNav = \App\Models\MedicalProfile::query()->where('user_id', auth()->id())->first();
        $doctorShowCompleteProfileNav = $doctorProfileNav === null
            || ! in_array((string) ($doctorProfileNav->verification_status ?? ''), ['APPROVED'], true);
    }
@endphp

<a href="{{ route('doctor.dashboard') }}" class="{{ $active === 'dashboard' ? $linkActive : $linkInactive }}">
    <i class="fas fa-th-large w-5 text-blue-300"></i>
    <span>{{ __('roleui.sidebar_dashboard') }}</span>
</a>
<a href="{{ route('doctor.appointments') }}" class="{{ $active === 'appointments' ? $linkActive : $linkInactive }}">
    <i class="fas fa-calendar-alt w-5 text-blue-300"></i>
    <span>{{ __('roleui.sidebar_appointments') }}</span>
</a>
<a href="{{ route('doctor.patients') }}" class="{{ $active === 'patients' ? $linkActive : $linkInactive }}">
    <i class="fas fa-user-injured w-5 text-blue-300"></i>
    <span>{{ __('roleui.sidebar_patients') }}</span>
</a>
@php
    $sidebarIsNurse = false;
    if (auth()->check() && (string) auth()->user()->role === 'MEDICAL_TEAM') {
        $sidebarIsNurse = \App\Services\ConversationAccess::isStaffNurse(auth()->user());
    }
@endphp
@if ($sidebarIsNurse)
    <a href="{{ route('nurse.patient-chats') }}" class="{{ $active === 'nurse_patient_chats' ? $linkActive : $linkInactive }}">
        <i class="fas fa-user-nurse w-5 text-blue-300"></i>
        <span>{{ __('roleui.sidebar_nurse_patient_chats') }}</span>
    </a>
    <a href="{{ route('nurse.doctor-coordination') }}" class="{{ $active === 'nurse_doctor_coordination' ? $linkActive : $linkInactive }}">
        <i class="fas fa-user-md w-5 text-blue-300"></i>
        <span>{{ __('roleui.sidebar_nurse_doctor_coordination') }}</span>
    </a>
@else
    <a href="{{ route('doctor.conversations') }}" class="{{ $active === 'conversations' ? $linkActive : $linkInactive }}">
        <i class="fas fa-comments w-5 text-blue-300"></i>
        <span>{{ __('roleui.sidebar_conversations') }}</span>
    </a>
    <a href="{{ route('doctor.nurse-coordination') }}" class="{{ $active === 'nurse_coordination' ? $linkActive : $linkInactive }}">
        <i class="fas fa-user-friends w-5 text-blue-300"></i>
        <span>{{ __('roleui.sidebar_nurse_coordination') }}</span>
    </a>
@endif
<a href="{{ route('doctor.video-requests') }}" class="{{ in_array($active, ['video-requests', 'video'], true) ? $linkActive : $linkInactive }}">
    <i class="fas fa-video w-5 text-blue-300"></i>
    <span>{{ __('roleui.sidebar_video_hub') }}</span>
</a>
@if ($doctorShowCompleteProfileNav)
    <a href="{{ route('doctor.complete-profile') }}" class="{{ $active === 'profile' ? $linkActive : $linkInactive }}">
        <i class="fas fa-id-card w-5 text-blue-300"></i>
        <span>{{ __('roleui.sidebar_complete_profile') }}</span>
    </a>
@endif
