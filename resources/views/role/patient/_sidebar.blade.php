@php($active = $active ?? '')
@php($linkInactive = 'flex items-center gap-3 rounded-2xl px-4 py-3 font-black text-xs uppercase tracking-widest text-slate-300 hover:text-white hover:bg-white/10 transition')
@php($linkActive = 'flex items-center gap-3 rounded-2xl px-4 py-3 font-black text-xs uppercase tracking-widest bg-white/10 hover:bg-white/15 transition')

<a href="{{ route('patient.dashboard') }}" class="{{ $active === 'dashboard' ? $linkActive : $linkInactive }}">
    <i class="fas fa-th-large w-5 text-blue-300"></i>
    <span>{{ __('roleui.sidebar_dashboard') }}</span>
</a>
<a href="{{ route('patient.appointments') }}" class="{{ $active === 'appointments' ? $linkActive : $linkInactive }}">
    <i class="fas fa-calendar-alt w-5 text-blue-300"></i>
    <span>{{ __('roleui.sidebar_appointments') }}</span>
</a>
<a href="{{ route('patient.video-consult') }}" class="{{ $active === 'video' ? $linkActive : $linkInactive }}">
    <i class="fas fa-video w-5 text-blue-300"></i>
    <span>{{ __('roleui.sidebar_video_consult') }}</span>
</a>
<a href="{{ route('patient.conversations') }}" class="{{ $active === 'conversations' ? $linkActive : $linkInactive }}">
    <i class="fas fa-comments w-5 text-blue-300"></i>
    <span>{{ __('roleui.sidebar_conversations') }}</span>
</a>
