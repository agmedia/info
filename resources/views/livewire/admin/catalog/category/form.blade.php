<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Content / Categories') }}</p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ $isEdit ? __('Edit Category') : __('Create Category') }}</h1>
                <p class="mt-2 text-sm text-slate-600">{{ __('Dedicated edit page for large trees and cleaner workflow.') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="admin-chip">
                    {{ __('Scope:') }}
                    {{ match ($form['scope']) {
                        'blog' => __('Blog'),
                        'page' => __('Pages'),
                        default => str($form['scope'])->replace('_', ' ')->title()->toString(),
                    } }}
                </span>
                <span class="admin-chip">{{ __('Locale:') }} {{ $form['locale'] }}</span>
                <button type="button" wire:click="backToList" data-admin-leave class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">{{ __('Back to List') }}</button>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6" data-admin-dirty-form>
        @include('livewire.admin.partials.form-error-summary')
        <div class="admin-panel admin-form-panel p-6">
            <p class="admin-section-title">{{ __('Core Data') }}</p>

            <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Scope') }}</label>
                    <select wire:model.live="form.scope" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        @foreach ($this->scopeOptions as $scope)
                            @php
                                $scopeLabel = match ($scope) {
                                    'blog' => __('Blog'),
                                    'page' => __('Pages'),
                                    default => str($scope)->replace('_', ' ')->title()->toString(),
                                };
                            @endphp
                            <option value="{{ $scope }}">{{ $scopeLabel }}</option>
                        @endforeach
                    </select>
                    @error('form.scope') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Parent Category') }}</label>
                    <select wire:model="form.parent_id" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        <option value="">{{ __('(Root)') }}</option>
                        @foreach ($this->parentOptions as $parent)
                            @php
                                $parentTranslation = $parent->translations->first();
                                $parentName = $parentTranslation?->name ?? ($parent->code ?: __('Category #:id', ['id' => $parent->id]));
                                $pad = str_repeat('— ', max(0, (int) $parent->depth));
                            @endphp
                            <option value="{{ $parent->id }}">{{ $pad.$parentName }}</option>
                        @endforeach
                    </select>
                    @error('form.parent_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.common.locale') }}</label>
                    <select wire:model.live="form.locale" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase">
                        @foreach ($this->localeOptions as $localeOption)
                            <option value="{{ $localeOption }}" @selected(($form['locale'] ?? '') === $localeOption)>{{ $localeOption }}</option>
                        @endforeach
                    </select>
                    @error('form.locale') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

            </div>

            <div class="mt-4 flex flex-wrap items-center gap-3">
                <button
                    type="button"
                    wire:click="$toggle('form.is_active')"
                    class="admin-switch"
                    data-state="{{ $form['is_active'] ? 'on' : 'off' }}"
                    role="switch"
                    aria-checked="{{ $form['is_active'] ? 'true' : 'false' }}"
                    aria-label="{{ __('Toggle category active state') }}"
                >
                    <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                    <span class="admin-switch-label">{{ $form['is_active'] ? __('admin.common.active') : __('admin.common.inactive') }}</span>
                </button>

                <button
                    type="button"
                    wire:click="$toggle('form.show_in_menu')"
                    class="admin-switch"
                    data-state="{{ $form['show_in_menu'] ? 'on' : 'off' }}"
                    role="switch"
                    aria-checked="{{ $form['show_in_menu'] ? 'true' : 'false' }}"
                    aria-label="{{ __('Toggle category menu visibility') }}"
                >
                    <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                    <span class="admin-switch-label">{{ $form['show_in_menu'] ? __('In Menu') : __('Hidden') }}</span>
                </button>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('Content') }}</p>

                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Name') }}</label>
                        <input type="text" wire:model="form.name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Slug') }}</label>
                            <button type="button" wire:click="generateSlug" class="text-xs font-semibold text-slate-600 hover:text-slate-900">{{ __('Generate') }}</button>
                        </div>
                        <input type="text" wire:model="form.slug" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase" />
                        @error('form.slug') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <label for="category-description-html" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Description') }}</label>
                    <textarea id="category-description-html" rows="6" wire:model.live.debounce.300ms="form.description" data-quill-editor class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>

                @if ($form['scope'] === \App\Models\Catalog\Category\Category::SCOPE_CALL)
                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Status badge label') }}</label>
                        <input type="text" wire:model="form.status_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.status_label') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                @endif
            </div>

            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('SEO') }}</p>

                <div class="mt-4">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Meta Title') }}</label>
                    <input type="text" wire:model="form.meta_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    @error('form.meta_title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Meta Description') }}</label>
                    <textarea rows="3" wire:model="form.meta_description" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    @error('form.meta_description') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        <livewire:admin.media.manager
            :model-class="\App\Models\Catalog\Category\Category::class"
            :model-id="$categoryId"
            :locale="$form['locale']"
            :wire:key="'category-media-manager-'.($categoryId ?? 'new').'-'.$form['locale']"
        />

        @include('livewire.admin.partials.form-actions', [
            'submitLabel' => $isEdit ? __('Spremi kategoriju') : __('Kreiraj kategoriju'),
        ])
    </form>
</div>
