<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <h1 class="text-xl font-semibold tracking-tight">{{ __('WordPress Calls Import') }}</h1>
        <p class="mt-2 text-sm text-slate-600">
            {{ __('Import the current EU funds calls from the WordPress XML into the new Pozivi module. Categories stay fixed to the three frontend groups, while matching local blog articles are reused when available.') }}
        </p>
        <p class="mt-2 text-xs text-slate-500">
            {{ __('Tip: keep limit on 5 for a smoke test, then switch to 0 to import the full current frontend list.') }}
        </p>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)]">
        <div class="admin-panel admin-form-panel p-6">
            <form wire:submit="import" class="admin-form space-y-6">
                <section class="rounded-xl border border-slate-200 bg-white p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="admin-section-title">{{ __('XML upload') }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ __('Upload the WordPress WXR export. Imported assets referenced from WordPress uploads are localized into public storage during import.') }}</p>
                        </div>
                        @if ($xmlUpload || $storedXmlName)
                            <span class="admin-chip">{{ $xmlUpload?->getClientOriginalName() ?: $storedXmlName }}</span>
                        @endif
                    </div>

                    <div class="mt-4">
                        <input
                            type="file"
                            wire:model="xmlUpload"
                            accept=".xml,text/xml,application/xml"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-200"
                        />
                        @error('xmlUpload') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        <div wire:loading wire:target="xmlUpload" class="mt-2 text-xs text-slate-500">{{ __('Uploading XML...') }}</div>
                    </div>
                </section>

                <section class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <label class="block text-sm font-semibold text-slate-800" for="wp-call-import-locale">{{ __('Locale') }}</label>
                        <input id="wp-call-import-locale" type="text" wire:model.live="locale" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase" />
                        @error('locale') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <label class="block text-sm font-semibold text-slate-800" for="wp-call-import-limit">{{ __('Limit') }}</label>
                        <input id="wp-call-import-limit" type="number" min="0" wire:model="limit" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('limit') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <label class="block text-sm font-semibold text-slate-800" for="wp-call-import-offset">{{ __('Offset') }}</label>
                        <input id="wp-call-import-offset" type="number" min="0" wire:model="offset" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('offset') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </section>

                <section class="rounded-xl border border-slate-200 bg-white p-4">
                    <label class="inline-flex items-center gap-3 text-sm font-medium text-slate-800">
                        <input type="checkbox" wire:model="force" class="h-4 w-4 rounded border-slate-300 text-cyan-700 focus:ring-cyan-700">
                        <span>{{ __('Force re-download local assets') }}</span>
                    </label>
                    <p class="mt-2 text-xs text-slate-500">{{ __('Leave this off for normal reruns. Turn it on only when you explicitly want to refresh localized XML assets.') }}</p>
                </section>

                <div class="admin-form-actions flex flex-wrap items-center gap-2">
                    <button type="submit" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800" wire:loading.attr="disabled">
                        {{ __('Run Import') }}
                    </button>
                    @if ($storedXmlPath)
                        <button type="button" wire:click="reimport" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100" wire:loading.attr="disabled">
                            {{ __('Reimport / Update Existing') }}
                        </button>
                    @endif
                    <div wire:loading wire:target="import" class="text-sm text-slate-500">{{ __('Import in progress... this may take a bit while assets are localized.') }}</div>
                    <div wire:loading wire:target="reimport" class="text-sm text-slate-500">{{ __('Reimport in progress... existing imported calls will be updated.') }}</div>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <div class="admin-panel admin-panel-soft p-6">
                <p class="admin-section-title">{{ __('Import behavior') }}</p>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    <li>{{ __('The importer follows the current frontend groups: Pozivi u najavi, Otvoreni pozivi, and Zatvoreni pozivi.') }}</li>
                    <li>{{ __('If a frontend item already points to a local blog article, that local article is reused as the primary source for the new call post.') }}</li>
                    <li>{{ __('If no local blog article exists, the importer falls back to the matching WordPress XML article or creates a stub item that you can complete in admin.') }}</li>
                </ul>
                <pre class="mt-4 overflow-x-auto rounded-xl bg-slate-950 px-4 py-3 text-xs text-slate-100"><code>php artisan content:import-wordpress-calls /path/to/export.xml --locale=hr --limit=5</code></pre>
            </div>

            @if ($errorMessage)
                <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                    {{ $errorMessage }}
                </div>
            @endif

            @if ($result)
                <div class="admin-panel admin-panel-soft p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="admin-section-title">{{ __('Last import result') }}</p>
                            <p class="mt-1 text-sm text-slate-600">
                                {{ __('Processed :count EU funds call item(s) in locale :locale.', ['count' => $result['processed_count'] ?? 0, 'locale' => $result['locale'] ?? $locale]) }}
                            </p>
                            @if ($storedXmlName)
                                <p class="mt-1 text-xs text-slate-500">{{ __('Source XML: :name', ['name' => $storedXmlName]) }}</p>
                            @endif
                        </div>
                        <span class="admin-chip">{{ __('Localized assets') }}: {{ $result['localized_asset_count'] ?? 0 }}</span>
                    </div>

                    @if (! empty($result['categories']))
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ((array) $result['categories'] as $category)
                                <span class="admin-chip">{{ $category['name'] }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if ($result && ! empty($result['imported']))
        <div class="admin-panel admin-form-panel p-6">
            <div class="mb-4">
                <p class="admin-section-title">{{ __('Imported call posts') }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ __('Review status, source and localized asset counts before continuing with a full rerun.') }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="admin-items-table min-w-full">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 text-left">{{ __('Title') }}</th>
                            <th class="px-3 py-2 text-left">{{ __('Slug') }}</th>
                            <th class="px-3 py-2 text-left">{{ __('Category') }}</th>
                            <th class="px-3 py-2 text-left">{{ __('Source') }}</th>
                            <th class="px-3 py-2 text-left">{{ __('Assets') }}</th>
                            <th class="px-3 py-2 text-left">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ((array) $result['imported'] as $row)
                            <tr>
                                <td class="px-3 py-3 align-top text-sm font-semibold text-slate-900">{{ $row['title'] }}</td>
                                <td class="px-3 py-3 align-top text-sm text-slate-600">{{ $row['slug'] }}</td>
                                <td class="px-3 py-3 align-top text-sm text-slate-600">{{ $row['category'] }}</td>
                                <td class="px-3 py-3 align-top text-sm text-slate-600">{{ strtoupper((string) $row['source']) }}</td>
                                <td class="px-3 py-3 align-top text-sm text-slate-600">{{ $row['asset_count'] }}</td>
                                <td class="px-3 py-3 align-top">
                                    <span class="admin-chip">{{ strtoupper((string) $row['status']) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
