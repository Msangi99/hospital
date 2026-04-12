@php($active = $active ?? 'dashboard')
@php($linkInactive = 'flex items-center gap-3 rounded-2xl px-4 py-3 font-black text-xs uppercase tracking-widest text-slate-300 hover:text-white hover:bg-white/10 transition')
@php($linkActive = 'flex items-center gap-3 rounded-2xl px-4 py-3 font-black text-xs uppercase tracking-widest bg-white/10 hover:bg-white/15 transition')

<a href="{{ route('ambulance.portal.dashboard') }}" class="{{ $active === 'dashboard' ? $linkActive : $linkInactive }}">
    <i class="fas fa-th-large w-5 text-orange-300"></i>
    <span>{{ __('roleui.ambulance_sidebar_dispatch') }}</span>
</a>
<a href="{{ route('ambulance.portal.history') }}" class="{{ $active === 'history' ? $linkActive : $linkInactive }}">
    <i class="fas fa-clock-rotate-left w-5 text-orange-300"></i>
    <span>{{ __('roleui.ambulance_sidebar_history') }}</span>
</a>
<a href="{{ route('ambulance') }}" target="_blank" rel="noopener noreferrer" class="{{ $linkInactive }}">
    <i class="fas fa-satellite-dish w-5 text-orange-300"></i>
    <span>{{ __('roleui.ambulance_sidebar_public_sos') }}</span>
</a>
