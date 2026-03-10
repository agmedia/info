<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <h1 class="text-xl font-semibold tracking-tight">{{ __('User Settings') }}</h1>
        <p class="mt-2 text-sm text-slate-600">{{ __('Namespace:') }} <code>Settings/User</code></p>
        <p class="mt-2 text-xs text-slate-500">{{ __('Control user tracking capture for analytics and diagnostics.') }}</p>
    </div>

    <div class="admin-panel admin-form-panel p-6">
        <form wire:submit="save" class="admin-form mt-1 space-y-4">
            <div>
                <p class="admin-section-title">{{ __('Feature Switches') }}</p>
                <div class="mt-3 grid gap-3 md:grid-cols-1">
                    @php
                        $switches = [
                            'user_tracking_enabled' => [
                                'title' => __('User Tracking'),
                                'description' => __('Stores user/front interaction events for analytics and personalization.'),
                            ],
                        ];
                    @endphp

                    @foreach ($switches as $key => $item)
                        @php $enabled = (bool) ($form[$key] ?? false); @endphp
                        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <strong class="block text-slate-900">{{ $item['title'] }}</strong>
                                    <p class="mt-1 text-sm text-slate-600">{{ $item['description'] }}</p>
                                </div>
                                <button
                                    type="button"
                                    wire:click="toggle('{{ $key }}')"
                                    class="admin-switch"
                                    data-state="{{ $enabled ? 'on' : 'off' }}"
                                    role="switch"
                                    aria-checked="{{ $enabled ? 'true' : 'false' }}"
                                    aria-label="{{ $item['title'] }}"
                                >
                                    <span class="admin-switch-track">
                                        <span class="admin-switch-thumb"></span>
                                    </span>
                                    <span class="admin-switch-label">{{ $enabled ? __('On') : __('Off') }}</span>
                                </button>
                            </div>
                            <p class="mt-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $enabled ? 'text-emerald-700' : 'text-slate-500' }}">
                                {{ $enabled ? __('Enabled') : __('Disabled') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="admin-form-actions flex items-center gap-2">
                <button type="submit" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">{{ __('admin.common.save') }}</button>
                <button type="button" wire:click="resetToDefaults" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">{{ __('Reset Defaults') }}</button>
            </div>
        </form>
    </div>
</div>
