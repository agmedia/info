<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Admin / Users') }}</p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ __('Add Editor') }}</h1>
                <p class="mt-2 text-sm text-slate-600">{{ __('Create a new admin account with Editor access.') }}</p>
            </div>
            <button type="button" wire:click="backToList" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                {{ __('Back to List') }}
            </button>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="admin-panel admin-form-panel p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="admin-section-title">{{ __('Core Data') }}</p>
                <span class="admin-chip">{{ __('Role') }}: {{ __('Editor') }}</span>
            </div>

            <div class="mt-4 grid gap-3 md:grid-cols-3">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Name') }}</label>
                    <input type="text" wire:model="form.name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    @error('form.name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Email') }}</label>
                    <input type="email" wire:model="form.email" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    @error('form.email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-4">
                <button
                    type="button"
                    wire:click="$toggle('form.email_verified')"
                    class="admin-switch"
                    data-state="{{ $form['email_verified'] ? 'on' : 'off' }}"
                    role="switch"
                    aria-checked="{{ $form['email_verified'] ? 'true' : 'false' }}"
                    aria-label="{{ __('Toggle email verified state') }}"
                >
                    <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                    <span class="admin-switch-label">{{ $form['email_verified'] ? __('Email Verified') : __('Email Unverified') }}</span>
                </button>
            </div>
        </div>

        <div class="admin-panel admin-form-panel p-6">
            <p class="admin-section-title">{{ __('Contact Details') }}</p>

            <div class="mt-4 grid gap-3 md:grid-cols-3">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('First Name') }}</label>
                    <input type="text" wire:model="form.profile.first_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    @error('form.profile.first_name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Last Name') }}</label>
                    <input type="text" wire:model="form.profile.last_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    @error('form.profile.last_name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Phone') }}</label>
                    <input type="text" wire:model="form.profile.phone" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    @error('form.profile.phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="admin-panel admin-form-panel p-6">
            <p class="admin-section-title">{{ __('Password') }}</p>

            <div class="mt-4 grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Password') }}</label>
                    <input type="password" wire:model="form.password" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" autocomplete="new-password" />
                    @error('form.password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Confirm Password') }}</label>
                    <input type="password" wire:model="form.password_confirmation" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" autocomplete="new-password" />
                </div>
            </div>
        </div>

        <div class="admin-form-actions flex items-center gap-2 pt-2">
            <button type="submit" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                {{ __('Create Editor') }}
            </button>
            <button type="button" wire:click="backToList" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                {{ __('Cancel') }}
            </button>
        </div>
    </form>
</div>
