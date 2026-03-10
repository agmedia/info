<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <h1 class="text-xl font-semibold tracking-tight">{{ __('Admin Appearance Controls') }}</h1>
        <p class="mt-2 text-sm text-slate-600">
            {{ __('Settings namespace:') }} <code>Settings/System/AdminAppearanceControls</code>
        </p>
    </div>

    <div class="admin-panel admin-panel-soft p-6">
            <p class="admin-section-title">{{ __('Admin Pagination') }}</p>
            <p class="mt-3 text-sm text-slate-600">{{ __('Used by admin item tables and structured list screens.') }}</p>

            <form wire:submit="save" class="admin-form mt-4 space-y-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Admin Items Per Page') }}</label>
                    <input type="number" min="5" max="200" wire:model="form.admin_items_per_page" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    @error('form.admin_items_per_page') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Admin Category Roots Per Page') }}</label>
                    <input type="number" min="5" max="100" wire:model="form.admin_category_roots_per_page" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    @error('form.admin_category_roots_per_page') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="admin-form-actions flex items-center gap-2">
                    <button type="submit" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">{{ __('admin.common.save') }}</button>
                    <button type="button" wire:click="resetToDefaults" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">{{ __('Reset Defaults') }}</button>
                </div>
            </form>
    </div>
</div>
