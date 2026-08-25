<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Content / Team') }}</p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">
                    {{ $isEdit ? __('admin.content.team.form.edit_title') : __('admin.content.team.form.create_title') }}
                </h1>
                <p class="mt-2 text-sm text-slate-600">{{ __('admin.content.team.form.subtitle') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="admin-chip">{{ __('admin.common.locale') }}: {{ $form['locale'] }}</span>
                <button type="button" wire:click="backToList" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                    {{ __('admin.content.team.form.buttons.back') }}
                </button>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="admin-panel admin-form-panel p-3 sm:p-4">
            <div class="flex flex-wrap gap-2">
                <button type="button" wire:click="setTab('content')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'content' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                    {{ __('admin.content.team.form.tabs.content') }}
                </button>
                <button type="button" wire:click="setTab('media')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'media' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                    {{ __('admin.content.team.form.tabs.media') }}
                </button>
            </div>
        </div>

        @if ($activeTab === 'content')
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('Core Data') }}</p>

                <div class="mt-4 grid gap-3" style="grid-template-columns: repeat(12, minmax(0, 1fr));">
                    <div class="sm:col-span-4" style="grid-column: span 4;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.team.form.fields.name') }}</label>
                        <input type="text" wire:model.live.debounce.250ms="form.name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-3" style="grid-column: span 3;">
                        <div class="flex items-center justify-between">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Code') }}</label>
                            <button type="button" wire:click="generateCode" class="text-xs font-semibold text-slate-600 hover:text-slate-900">{{ __('Generate') }}</button>
                        </div>
                        <input type="text" wire:model.live.debounce.250ms="form.code" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-mono lowercase" />
                        @error('form.code') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-3" style="grid-column: span 3;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.team.form.fields.position') }}</label>
                        <input type="text" wire:model.live.debounce.250ms="form.position" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.position') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2" style="grid-column: span 2;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.common.locale') }}</label>
                        <select wire:model.live="form.locale" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase">
                            @foreach ($adminLocaleOptions as $localeOption)
                                <option value="{{ $localeOption }}" @selected(($form['locale'] ?? '') === $localeOption)>{{ $localeOption }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-3 grid gap-3" style="grid-template-columns: repeat(12, minmax(0, 1fr));">
                    <div class="sm:col-span-3" style="grid-column: span 3;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.team.form.fields.email') }}</label>
                        <input type="email" wire:model.live.debounce.250ms="form.email" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-3" style="grid-column: span 3;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.team.form.fields.mobile_phone') }}</label>
                        <input type="text" wire:model.live.debounce.250ms="form.mobile_phone" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.mobile_phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2" style="grid-column: span 2;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Sort Order') }}</label>
                        <input type="number" min="0" wire:model="form.sort_order" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.sort_order') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-4 flex items-end" style="grid-column: span 4;">
                        <button
                            type="button"
                            wire:click="$toggle('form.is_active')"
                            class="admin-switch"
                            data-state="{{ $form['is_active'] ? 'on' : 'off' }}"
                            role="switch"
                            aria-checked="{{ $form['is_active'] ? 'true' : 'false' }}"
                            aria-label="{{ __('Toggle team member active state') }}"
                        >
                            <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                            <span class="admin-switch-label">{{ $form['is_active'] ? __('admin.common.active') : __('admin.common.inactive') }}</span>
                        </button>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.team.form.fields.departments') }}</label>
                    <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_auto] md:items-start">
                        <div>
                            <select wire:model.live="departmentSelection" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                <option value="">{{ __('Select department') }}</option>
                                @foreach ($departmentOptions as $department)
                                    <option value="{{ $department }}">{{ $department }}</option>
                                @endforeach
                                <option value="{{ $customDepartmentOptionValue }}">{{ __('New Department') }}</option>
                            </select>

                            @if ($departmentSelection === $customDepartmentOptionValue)
                                <input
                                    type="text"
                                    wire:model.defer="customDepartment"
                                    class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
                                    placeholder="{{ __('Enter new department') }}"
                                />
                            @endif
                        </div>

                        <button type="button" wire:click="addDepartment" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                            {{ __('Add Department') }}
                        </button>
                    </div>

                    @if ($selectedDepartments !== [])
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($selectedDepartments as $index => $department)
                                <span class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-700">
                                    <span>{{ $department }}</span>
                                    <button type="button" wire:click="removeDepartment({{ $index }})" class="text-xs font-semibold text-slate-500 hover:text-rose-600" aria-label="{{ __('Remove') }}">×</button>
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <p class="mt-2 text-xs text-slate-500">{{ __('Choose an existing department or enter a new one, then click Add Department.') }}</p>
                    @error('selectedDepartments') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    @error('selectedDepartments.*') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-4" wire:key="team-member-description-{{ $memberId ?? 'new' }}-{{ $form['locale'] }}">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.team.form.fields.description') }}</label>
                    <textarea rows="12" wire:model.defer="form.description_html" data-quill-editor class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    @error('form.description_html') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-4 grid gap-3 md:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.team.form.fields.facebook_url') }}</label>
                        <input type="url" wire:model.live.debounce.250ms="form.facebook_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.facebook_url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.team.form.fields.twitter_url') }}</label>
                        <input type="url" wire:model.live.debounce.250ms="form.twitter_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.twitter_url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.team.form.fields.linkedin_url') }}</label>
                        <input type="url" wire:model.live.debounce.250ms="form.linkedin_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.linkedin_url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.content.team.form.fields.payload') }}</label>
                    <textarea rows="6" wire:model="form.payload_text" class="w-full rounded-xl border border-slate-300 px-3 py-2 font-mono text-xs"></textarea>
                    @error('form.payload_text') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        @endif

        @if ($activeTab === 'media')
            <livewire:admin.media.manager
                :model-class="\App\Models\Content\Team\TeamMember::class"
                :model-id="$memberId"
                :locale="$form['locale']"
                :wire:key="'team-member-media-manager-'.($memberId ?? 'new').'-'.$form['locale']"
            />
        @endif

        <div class="admin-form-actions flex items-center gap-2 pt-2">
            <button type="submit" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                {{ $isEdit ? __('admin.content.team.form.buttons.update') : __('admin.content.team.form.buttons.create') }}
            </button>
            <button type="button" wire:click="backToList" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                {{ __('admin.content.team.form.buttons.cancel') }}
            </button>
        </div>
    </form>
</div>
