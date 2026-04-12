<section class="w-full max-w-2xl bg-white dark:bg-white">
    @if ((string) (auth()->user()->role ?? '') === 'SUPERADMIN' && config('admin-security.require_superadmin_mfa') && ! auth()->user()->hasEnabledTwoFactorAuthentication())
        @include('settings.partials.superadmin-mfa-panel')
    @endif

    <header class="mb-10 border-b border-slate-200 pb-8">
        <h1 class="text-2xl font-black tracking-tighter text-slate-900 sm:text-3xl">
            {{ __('roleui.sidebar_account') }}
        </h1>
        <p class="mt-2 max-w-lg text-sm font-bold leading-relaxed text-slate-500">
            {{ __('Manage your profile and account settings') }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="space-y-8">
        <div>
            <label for="profile-name" class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                {{ __('Name') }}
            </label>
            <input
                id="profile-name"
                wire:model="name"
                type="text"
                name="name"
                required
                autocomplete="name"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-semibold text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
            />
            @error('name')
                <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="profile-email" class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                {{ __('Email') }}
            </label>
            <input
                id="profile-email"
                wire:model="email"
                type="email"
                name="email"
                required
                autocomplete="email"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-semibold text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
            />
            @error('email')
                <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>
            @enderror

            @if ($this->hasUnverifiedEmail)
                <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50/80 px-4 py-3 text-sm font-semibold text-amber-900">
                    <p>{{ __('Your email address is unverified.') }}</p>
                    <button
                        type="button"
                        wire:click="resendVerificationNotification"
                        wire:loading.attr="disabled"
                        class="mt-2 text-left text-sm font-black text-blue-600 underline decoration-2 underline-offset-2 hover:text-blue-700"
                    >
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-bold text-green-700">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="inline-flex h-12 min-w-[10rem] shrink-0 items-center justify-center rounded-2xl bg-blue-600 px-6 text-sm font-black uppercase tracking-widest text-white shadow-sm transition hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:cursor-not-allowed disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="updateProfileInformation">{{ __('Save') }}</span>
                <span wire:loading wire:target="updateProfileInformation">{{ __('Saving...') }}</span>
            </button>

            <x-action-message class="text-sm font-bold text-green-600" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>

    <div class="mt-14 border-t border-slate-200 pt-14">
        <h2 class="text-lg font-black tracking-tight text-slate-900">
            {{ __('Change password') }}
        </h2>
        <p class="mt-2 max-w-lg text-sm font-semibold text-slate-500">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>

        <form wire:submit="updatePassword" class="mt-8 space-y-8">
            <div>
                <label for="profile-current-password" class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                    {{ __('Current password') }}
                </label>
                <input
                    id="profile-current-password"
                    wire:model="current_password"
                    type="password"
                    name="current_password"
                    autocomplete="current-password"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-semibold text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                />
                @error('current_password')
                    <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="profile-new-password" class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                    {{ __('New password') }}
                </label>
                <input
                    id="profile-new-password"
                    wire:model="password"
                    type="password"
                    name="password"
                    autocomplete="new-password"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-semibold text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                />
                @error('password')
                    <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="profile-confirm-password" class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                    {{ __('Confirm password') }}
                </label>
                <input
                    id="profile-confirm-password"
                    wire:model="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    autocomplete="new-password"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-semibold text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                />
                @error('password_confirmation')
                    <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex h-12 min-w-[10rem] shrink-0 items-center justify-center rounded-2xl bg-slate-900 px-6 text-sm font-black uppercase tracking-widest text-white shadow-sm transition hover:bg-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-900 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="updatePassword">{{ __('Update password') }}</span>
                    <span wire:loading wire:target="updatePassword">{{ __('Updating...') }}</span>
                </button>

                <x-action-message class="text-sm font-bold text-green-600" on="password-updated">
                    {{ __('Password updated.') }}
                </x-action-message>
            </div>
        </form>
    </div>

    @if ($this->showDeleteUser)
        <livewire:settings.delete-user-form />
    @endif
</section>
