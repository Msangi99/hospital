@php($active = $active ?? 'overview')
@php($linkInactive = 'flex items-center gap-3 rounded-2xl px-4 py-3 font-black text-xs uppercase tracking-widest text-slate-300 hover:text-white hover:bg-white/10 transition')
@php($linkActive = 'flex items-center gap-3 rounded-2xl px-4 py-3 font-black text-xs uppercase tracking-widest bg-white/10 hover:bg-white/15 transition')

<a href="{{ route('admin.dashboard') }}" class="{{ $active === 'overview' ? $linkActive : $linkInactive }}">
    <i class="fas fa-th-large w-5 text-blue-300"></i>
    <span>{{ __('roleui.sidebar_admin_overview') }}</span>
</a>
<a href="{{ route('admin.users') }}" class="{{ $active === 'users' ? $linkActive : $linkInactive }}">
    <i class="fas fa-users w-5 text-blue-300"></i>
    <span>{{ __('roleui.sidebar_user_management') }}</span>
</a>
<a href="{{ route('admin.facilities') }}" class="{{ $active === 'facilities' ? $linkActive : $linkInactive }}">
    <i class="fas fa-hospital w-5 text-blue-300"></i>
    <span>{{ __('roleui.sidebar_facility_management') }}</span>
</a>
<a href="{{ route('admin.analytics') }}" class="{{ $active === 'analytics' ? $linkActive : $linkInactive }}">
    <i class="fas fa-chart-line w-5 text-blue-300"></i>
    <span>{{ __('roleui.sidebar_platform_analytics') }}</span>
</a>
<a href="{{ route('admin.audit-logs') }}" class="{{ $active === 'audit-logs' ? $linkActive : $linkInactive }}">
    <i class="fas fa-clipboard-list w-5 text-blue-300"></i>
    <span>{{ __('roleui.sidebar_audit_logs') }}</span>
</a>
<a href="{{ route('admin.billing-integrations') }}" class="{{ $active === 'billing-integrations' ? $linkActive : $linkInactive }}">
    <i class="fas fa-credit-card w-5 text-blue-300"></i>
    <span>{{ __('roleui.sidebar_billing_integrations') }}</span>
</a>
<a href="{{ route('admin.emergencies') }}" class="{{ $active === 'emergencies' ? $linkActive : $linkInactive }}">
    <i class="fas fa-ambulance w-5 text-blue-300"></i>
    <span>{{ __('roleui.sidebar_emergency_hub') }}</span>
</a>
<a href="{{ route('admin.newsletter') }}" class="{{ $active === 'newsletter' ? $linkActive : $linkInactive }}">
    <i class="fas fa-paper-plane w-5 text-blue-300"></i>
    <span>{{ __('roleui.sidebar_newsletter_hub') }}</span>
</a>
<a href="{{ route('admin.alerts') }}" class="{{ $active === 'alerts' ? $linkActive : $linkInactive }}">
    <i class="fas fa-triangle-exclamation w-5 text-blue-300"></i>
    <span>{{ __('roleui.sidebar_system_alerts') }}</span>
</a>
<a href="{{ route('admin.ai-settings') }}" class="{{ $active === 'ai' ? $linkActive : $linkInactive }}">
    <i class="fas fa-brain w-5 text-blue-300"></i>
    <span>{{ __('roleui.sidebar_ai_settings') }}</span>
</a>