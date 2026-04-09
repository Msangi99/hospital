<div class="mt-14 border-t border-slate-200 pt-12">
    <h2 class="text-lg font-black tracking-tight text-slate-900">
        {{ __('Delete account') }}
    </h2>
    <p class="mt-2 max-w-lg text-sm font-semibold text-slate-500">
        {{ __('Delete your account and all of its resources') }}
    </p>

    @if (! $confirmingDeletion)
        <button
            type="button"
            wire:click="startConfirmingDeletion"
            class="mt-6 inline-flex h-11 items-center justify-center rounded-2xl border border-red-200 bg-red-50 px-5 text-xs font-black uppercase tracking-widest text-red-700 transition hover:bg-red-100"
        >
            {{ __('Delete account') }}
        </button>
    @else
        <div class="mt-6 rounded-[2rem] border border-slate-200 bg-white p-6 sm:p-8">
            <p class="text-sm font-bold leading-relaxed text-slate-700">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <form wire:submit="deleteUser" class="mt-6 space-y-5">
                <div>
                    <label for="delete-account-password" class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                        {{ __('Password') }}
                    </label>
                    <input
                        id="delete-account-password"
                        wire:model="password"
                        type="password"
                        name="password"
                        autocomplete="current-password"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-semibold text-slate-900 outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-400/20"
                    />
                    @error('password')
                        <p class="mt-2 text-sm font-bold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap gap-3">
                    <button
                        type="button"
                        wire:click="cancelDeletion"
                        class="inline-flex h-11 items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 text-xs font-black uppercase tracking-widest text-slate-700 transition hover:bg-slate-50"
                    >
                        {{ __('Cancel') }}
                    </button>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex h-11 items-center justify-center rounded-2xl bg-red-600 px-5 text-xs font-black uppercase tracking-widest text-white transition hover:bg-red-700 disabled:opacity-60"
                    >
                        {{ __('Delete account') }}
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
