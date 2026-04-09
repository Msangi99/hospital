@php($active = $active ?? 'dashboard')
@php($linkInactive = 'flex items-center gap-3 rounded-2xl px-4 py-3 font-black text-xs uppercase tracking-widest text-slate-300 hover:text-white hover:bg-white/10 transition')
@php($linkActive = 'flex items-center gap-3 rounded-2xl px-4 py-3 font-black text-xs uppercase tracking-widest bg-white/10 hover:bg-white/15 transition')

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
<a href="{{ route('doctor.complete-profile') }}" class="{{ $active === 'profile' ? $linkActive : $linkInactive }}">
    <i class="fas fa-id-card w-5 text-blue-300"></i>
    <span>{{ __('roleui.sidebar_complete_profile') }}</span>
</a>
