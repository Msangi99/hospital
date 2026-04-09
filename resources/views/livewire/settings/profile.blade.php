<section class="w-full rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-10">
    <style>
        /* Tailwind CDN has no Flux theme CSS variables; primary Flux buttons can lose their background. */
        /* Match name/email in dark mode and tame Chrome autofill on email. */
        .profile-settings-form [data-flux-control] {
            --tw-border-opacity: 1;
        }
        .dark .profile-settings-form input:-webkit-autofill,
        .dark .profile-settings-form input:-webkit-autofill:hover,
        .dark .profile-settings-form input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px rgb(39 39 42) inset !important;
            -webkit-text-fill-color: rgb(228 228 231) !important;
            caret-color: rgb(228 228 231);
        }
    </style>

    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="profile-settings-form my-6 w-full space-y-6">
            <flux:input
                wire:model="name"
                :label="__('Name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                variant="filled"
            />

            <div>
                <flux:input
                    wire:model="email"
                    :label="__('Email')"
                    type="email"
                    required
                    autocomplete="email"
                    variant="filled"
                />

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex h-10 w-full shrink-0 items-center justify-center rounded-lg border border-blue-700 bg-blue-600 px-4 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-blue-600 dark:text-white dark:hover:bg-blue-500 sm:w-auto sm:min-w-[10rem]"
                >
                    <span wire:loading.remove wire:target="updateProfileInformation">{{ __('Save') }}</span>
                    <span wire:loading wire:target="updateProfileInformation">{{ __('Saving...') }}</span>
                </button>

                <x-action-message class="text-sm font-semibold text-green-700 dark:text-green-400" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:settings.delete-user-form />
        @endif
    </x-settings.layout>
</section>
