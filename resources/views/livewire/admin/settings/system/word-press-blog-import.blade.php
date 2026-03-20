<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <h1 class="text-xl font-semibold tracking-tight">{{ __('WordPress Blog Import') }}</h1>
        <p class="mt-2 text-sm text-slate-600">
            {{ __('Upload a WordPress XML export, test with a small limit first, and keep old WordPress article URLs working through the existing 301 redirect.') }}
        </p>
        <p class="mt-2 text-xs text-slate-500">
            {{ __('Tip: keep limit at 3 for the first run, then set 0 to import the whole XML after you verify content and images.') }}
        </p>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)]">
        <div class="admin-panel admin-form-panel p-6">
            <form wire:submit="import" class="admin-form space-y-6">
                <section class="rounded-xl border border-slate-200 bg-white p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="admin-section-title">{{ __('XML upload') }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ __('Use the WordPress WXR export file (`.xml`). Images are downloaded during import from the URLs inside the export.') }}</p>
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

                <section class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <label class="block text-sm font-semibold text-slate-800" for="wp-import-locale">{{ __('Locale') }}</label>
                        <input id="wp-import-locale" type="text" wire:model.live="locale" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase" />
                        @error('locale') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        <p class="mt-2 text-xs text-slate-500">{{ __('Default is `hr`, which is also used for the imported blog translation.') }}</p>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <label class="block text-sm font-semibold text-slate-800" for="wp-import-mode">{{ __('Import mode') }}</label>
                        <select id="wp-import-mode" wire:model.live="categoryMode" data-tom-select data-tom-no-search="1" class="admin-select mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                            <option value="single">{{ __('All posts into one category') }}</option>
                            <option value="source">{{ __('Use categories from XML') }}</option>
                        </select>
                        @error('categoryMode') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        <p class="mt-2 text-xs text-slate-500">
                            {{ $categoryMode === 'single'
                                ? __('Best when one XML belongs to one section, e.g. Novosti.')
                                : __('Best for future XML files that contain multiple source categories.') }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <label class="block text-sm font-semibold text-slate-800" for="wp-import-limit">{{ __('Limit') }}</label>
                        <input id="wp-import-limit" type="number" min="0" wire:model="limit" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('limit') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        <p class="mt-2 text-xs text-slate-500">{{ __('Use `3` for a test import. Use `0` to import all matching posts.') }}</p>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <label class="block text-sm font-semibold text-slate-800" for="wp-import-offset">{{ __('Offset') }}</label>
                        <input id="wp-import-offset" type="number" min="0" wire:model="offset" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('offset') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        <p class="mt-2 text-xs text-slate-500">{{ __('Useful for importing large XML files in batches without duplicating old items.') }}</p>
                    </div>
                </section>

                <section class="rounded-xl border border-slate-200 bg-white p-4">
                    <div>
                        <p class="admin-section-title">{{ __('Destination category') }}</p>
                        <p class="mt-1 text-sm text-slate-600">
                            {{ $categoryMode === 'single'
                                ? __('Pick an existing blog category or leave it on manual entry to create/use a new one.')
                                : __('Used as fallback if a post in the XML has no source category.') }}
                        </p>
                    </div>

                    <div class="mt-4 grid gap-4 md:grid-cols-3">
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-slate-800" for="wp-import-category-id">{{ __('Existing category') }}</label>
                            <select id="wp-import-category-id" wire:model.live="selectedCategoryId" data-tom-select class="admin-select mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                <option value="">{{ __('Manual / create new') }}</option>
                                @foreach ($categoryOptions as $category)
                                    <option value="{{ $category['id'] }}">{{ $category['name'] }} ({{ $category['slug'] }})</option>
                                @endforeach
                            </select>
                            @error('selectedCategoryId') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-800" for="wp-import-category-name">{{ __('Category name') }}</label>
                            <input id="wp-import-category-name" type="text" wire:model="categoryName" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            @error('categoryName') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-800" for="wp-import-category-slug">{{ __('Category slug') }}</label>
                            <input id="wp-import-category-slug" type="text" wire:model="categorySlug" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase" />
                            @error('categorySlug') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-800" for="wp-import-slugs">{{ __('Specific WP slugs') }}</label>
                            <input id="wp-import-slugs" type="text" wire:model="slugsText" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="slug-1 slug-2" />
                            @error('slugsText') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            <p class="mt-2 text-xs text-slate-500">{{ __('Optional. Separate slugs with spaces, commas or new lines for a precise test import.') }}</p>
                        </div>
                    </div>
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
                    <div wire:loading wire:target="import" class="text-sm text-slate-500">{{ __('Import in progress... this may take a bit while images are downloaded.') }}</div>
                    <div wire:loading wire:target="reimport" class="text-sm text-slate-500">{{ __('Reimport in progress... existing imported posts will be updated.') }}</div>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <div class="admin-panel admin-panel-soft p-6">
                <p class="admin-section-title">{{ __('SEO behavior') }}</p>
                <p class="mt-3 text-sm text-slate-600">
                    {{ __('Imported posts use the canonical `/blog/{slug}` route, while old WordPress date URLs keep working through the existing permanent redirect.') }}
                </p>
                <p class="mt-3 text-xs text-slate-500">{{ __('This is the cleaner SEO setup than keeping multiple public URLs live for the same article.') }}</p>
            </div>

            <div class="admin-panel admin-panel-soft p-6">
                <p class="admin-section-title">{{ __('Server notes') }}</p>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    <li>{{ __('The server must be able to reach the old WordPress image URLs so thumbnails and inline images can be downloaded.') }}</li>
                    <li>{{ __('The public storage symlink (`php artisan storage:link`) must exist so imported images are visible on the site.') }}</li>
                    <li>{{ __('If you prefer terminal usage, the Artisan command remains available for batch imports.') }}</li>
                </ul>
                <pre class="mt-4 overflow-x-auto rounded-xl bg-slate-950 px-4 py-3 text-xs text-slate-100"><code>php artisan content:import-wordpress-blog /path/to/export.xml --locale=hr --category-mode=single --category-name="Novosti" --category-slug=novosti --limit=3</code></pre>
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
                                {{ __('Imported :count post(s) in locale :locale.', ['count' => count((array) ($result['imported'] ?? [])), 'locale' => $result['locale'] ?? $locale]) }}
                            </p>
                            @if ($storedXmlName)
                                <p class="mt-1 text-xs text-slate-500">{{ __('Source XML: :name', ['name' => $storedXmlName]) }}</p>
                            @endif
                        </div>
                        <span class="admin-chip">{{ strtoupper((string) ($result['category_mode'] ?? $categoryMode)) }}</span>
                    </div>

                    @if (! empty($result['categories']))
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ((array) $result['categories'] as $category)
                                <span class="admin-chip">{{ $category['name'] }} ({{ $category['slug'] }})</span>
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
                <p class="admin-section-title">{{ __('Imported posts') }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ __('Review slugs, categories and legacy paths right away before you continue with a larger batch.') }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="admin-items-table min-w-full">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 text-left">{{ __('Title') }}</th>
                            <th class="px-3 py-2 text-left">{{ __('Slug') }}</th>
                            <th class="px-3 py-2 text-left">{{ __('Categories') }}</th>
                            <th class="px-3 py-2 text-left">{{ __('Legacy URL') }}</th>
                            <th class="px-3 py-2 text-left">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ((array) $result['imported'] as $row)
                            <tr>
                                <td class="px-3 py-3 align-top text-sm font-semibold text-slate-900">{{ $row['title'] }}</td>
                                <td class="px-3 py-3 align-top text-sm text-slate-600">{{ $row['slug'] }}</td>
                                <td class="px-3 py-3 align-top text-sm text-slate-600">{{ implode(', ', (array) ($row['categories'] ?? [])) }}</td>
                                <td class="px-3 py-3 align-top text-sm text-slate-600">{{ $row['legacy_path'] }}</td>
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
