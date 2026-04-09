@php($linkBase = 'flex items-center gap-3 rounded-2xl px-4 py-3 font-black text-xs uppercase tracking-widest transition')
@php($activeClass = 'bg-white text-slate-900')
@php($idleClass = 'bg-white/5 text-slate-100 hover:bg-white/10')

<a href="{{ route('owner.dashboard') }}" class="{{ $linkBase }} {{ ($active ?? '') === 'dashboard' ? $activeClass : $idleClass }}">
    <i class="fas fa-th-large w-5 {{ ($active ?? '') === 'dashboard' ? 'text-blue-600' : 'text-blue-300' }}"></i>
    <span>{{ __('roleui.sidebar_dashboard') }}</span>
</a>

<a href="{{ route('owner.dashboard') }}" class="{{ $linkBase }} {{ ($active ?? '') === 'profile' ? $activeClass : $idleClass }}">
    <i class="fas fa-hospital w-5 {{ ($active ?? '') === 'profile' ? 'text-blue-600' : 'text-blue-300' }}"></i>
    <span>{{ __('roleui.owner_sidebar_profile') }}</span>
</a>

<a href="{{ route('owner.workers') }}" class="{{ $linkBase }} {{ ($active ?? '') === 'workers' ? $activeClass : $idleClass }}">
    <i class="fas fa-users w-5 {{ ($active ?? '') === 'workers' ? 'text-blue-600' : 'text-blue-300' }}"></i>
    <span>{{ __('roleui.owner_sidebar_workers') }}</span>
</a>

<a href="{{ route('owner.departments') }}" class="{{ $linkBase }} {{ ($active ?? '') === 'departments' ? $activeClass : $idleClass }}">
    <i class="fas fa-building w-5 {{ ($active ?? '') === 'departments' ? 'text-blue-600' : 'text-blue-300' }}"></i>
    <span>{{ __('roleui.owner_sidebar_departments') }}</span>
</a>

<a href="{{ route('owner.services') }}" class="{{ $linkBase }} {{ ($active ?? '') === 'services' ? $activeClass : $idleClass }}">
    <i class="fas fa-stethoscope w-5 {{ ($active ?? '') === 'services' ? 'text-blue-600' : 'text-blue-300' }}"></i>
    <span>{{ __('roleui.owner_sidebar_services') }}</span>
</a>

<a href="{{ route('owner.schedules') }}" class="{{ $linkBase }} {{ ($active ?? '') === 'schedules' ? $activeClass : $idleClass }}">
    <i class="fas fa-calendar-alt w-5 {{ ($active ?? '') === 'schedules' ? 'text-blue-600' : 'text-blue-300' }}"></i>
    <span>{{ __('roleui.owner_sidebar_schedules') }}</span>
</a>

<a href="{{ route('owner.reports') }}" class="{{ $linkBase }} {{ ($active ?? '') === 'reports' ? $activeClass : $idleClass }}">
    <i class="fas fa-chart-line w-5 {{ ($active ?? '') === 'reports' ? 'text-blue-600' : 'text-blue-300' }}"></i>
    <span>{{ __('roleui.owner_sidebar_reports') }}</span>
</a>

<a href="{{ route('owner.billing') }}" class="{{ $linkBase }} {{ ($active ?? '') === 'billing' ? $activeClass : $idleClass }}">
    <i class="fas fa-file-invoice-dollar w-5 {{ ($active ?? '') === 'billing' ? 'text-blue-600' : 'text-blue-300' }}"></i>
    <span>{{ __('roleui.owner_sidebar_billing') }}</span>
</a>

<a href="{{ route('owner.settings') }}" class="{{ $linkBase }} {{ ($active ?? '') === 'settings' ? $activeClass : $idleClass }}">
    <i class="fas fa-cog w-5 {{ ($active ?? '') === 'settings' ? 'text-blue-600' : 'text-blue-300' }}"></i>
    <span>{{ __('roleui.owner_sidebar_settings') }}</span>
</a>
