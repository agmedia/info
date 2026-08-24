@php
    $blogPreviewUrl = $isEdit && trim((string) ($form['slug'] ?? '')) !== ''
        ? route('admin.content.blog.preview', ['post' => $postId, 'locale' => $form['locale']])
        : null;
@endphp

<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Content / Blog') }}</p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ $isEdit ? __('Edit Blog Post') : __('Create Blog Post') }}</h1>
                <p class="mt-2 text-sm text-slate-600">{{ __('Core post data, locale content and SEO.') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="admin-chip">{{ __('Locale:') }} {{ $form['locale'] }}</span>
                <button type="button" wire:click="backToList" data-admin-leave class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">{{ __('Back to List') }}</button>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6" data-admin-dirty-form>
        @include('livewire.admin.partials.form-error-summary')
        <div class="admin-panel admin-form-panel p-3 sm:p-4">
            <div class="flex flex-wrap gap-2">
                <button type="button" wire:click="setTab('content')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'content' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                    {{ __('Sadržaj') }}
                </button>
                <button type="button" wire:click="setTab('categories')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'categories' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                    {{ __('Kategorije') }}
                </button>
                <button type="button" wire:click="setTab('seo')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'seo' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                    {{ __('SEO') }}
                </button>
                <button type="button" wire:click="setTab('media')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'media' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                    {{ __('Media') }}
                </button>
            </div>
        </div>

        @if ($activeTab === 'content')
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('Core Data') }}</p>

                <div class="mt-4 grid gap-3 md:grid-cols-12">
                    <div class="md:col-span-5">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                        <input type="text" wire:model.live.debounce.250ms="form.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-5">
                        <div class="flex items-center justify-between">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Slug') }}</label>
                            <button type="button" wire:click="generateSlug" class="text-xs font-semibold text-slate-600 hover:text-slate-900">{{ __('Generate') }}</button>
                        </div>
                        <input type="text" wire:model.live.debounce.250ms="form.slug" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase" />
                        @error('form.slug') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('admin.common.locale') }}</label>
                        <select wire:model.live="form.locale" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase">
                            @foreach ($adminLocaleOptions as $localeOption)
                                <option value="{{ $localeOption }}">{{ $localeOption }}</option>
                            @endforeach
                        </select>
                        @error('form.locale') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-3 grid gap-3 md:grid-cols-12">
                    <div class="md:col-span-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Published At') }}</label>
                        <input type="date" wire:model="form.published_at" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.published_at') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-end gap-3 md:col-span-9">
                        <button
                            type="button"
                            wire:click="$toggle('form.is_active')"
                            class="admin-switch"
                            data-state="{{ $form['is_active'] ? 'on' : 'off' }}"
                            role="switch"
                            aria-checked="{{ $form['is_active'] ? 'true' : 'false' }}"
                            aria-label="{{ __('Toggle blog post active state') }}"
                        >
                            <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                            <span class="admin-switch-label">{{ $form['is_active'] ? __('admin.common.active') : __('admin.common.inactive') }}</span>
                        </button>

                        <button
                            type="button"
                            wire:click="$toggle('form.is_featured')"
                            class="admin-switch"
                            data-state="{{ $form['is_featured'] ? 'on' : 'off' }}"
                            role="switch"
                            aria-checked="{{ $form['is_featured'] ? 'true' : 'false' }}"
                            aria-label="{{ __('Toggle featured state') }}"
                        >
                            <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                            <span class="admin-switch-label">{{ $form['is_featured'] ? __('Featured') : __('Normal') }}</span>
                        </button>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Excerpt') }}</label>
                    <textarea rows="3" wire:model="form.excerpt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>

                <div class="mt-4" wire:key="blog-post-body-{{ $postId ?? 'new' }}-{{ $form['locale'] }}">
                    <label for="blog-post-body-html" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Body') }}</label>
                    <textarea id="blog-post-body-html" rows="14" wire:model.live.debounce.300ms="form.body_html" data-quill-editor data-quill-image-upload-url="{{ route('admin.content.blog.editor-image.upload') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    <p class="mt-2 text-xs text-slate-500">{{ __('Image ikona u editoru podrzava upload direktno u clanak. Ako zelis promijeniti postojecu sliku, klikni nju u editoru pa opet odaberi image ikonu.') }}</p>
                </div>

            </div>
        @endif

        @if ($activeTab === 'categories')
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('Blog kategorije') }}</p>
                <p class="mt-2 text-sm text-slate-600">{{ __('Prva odabrana kategorija je primarna i prikazuje se u navigaciji članka.') }}</p>

                <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50/60 p-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-end">
                        <div class="flex-1">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-600">{{ __('Brzo dodaj novu kategoriju') }}</label>
                            <input type="text" wire:model="newCategoryName" wire:keydown.enter.prevent="quickCreateCategory" class="w-full rounded-xl border border-amber-200 bg-white px-3 py-2 text-sm" placeholder="{{ __('Upiši naziv kategorije') }}">
                            @error('newCategoryName') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <button type="button" wire:click="quickCreateCategory" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            <i class="fa-light fa-plus" aria-hidden="true"></i>
                            {{ __('Dodaj i odaberi') }}
                        </button>
                    </div>
                </div>

                <div class="mt-5 grid gap-6 lg:grid-cols-2">
                    <section class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4" aria-labelledby="available-blog-categories">
                        <h2 id="available-blog-categories" class="text-sm font-semibold text-slate-900">{{ __('Dostupne kategorije') }}</h2>
                        <input type="search" wire:model.live.debounce.250ms="categorySearch" class="mt-3 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" placeholder="{{ __('Pretraži kategorije...') }}">

                        <div class="mt-3 max-h-80 space-y-2 overflow-auto pr-1">
                            @forelse ($this->filteredCategoryOptions as $category)
                                <button type="button" wire:click="addCategory({{ $category['id'] }})" class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-left text-sm text-slate-700 hover:border-slate-300 hover:bg-slate-50">
                                    <span>{{ $category['label'] }}</span>
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-slate-100 text-slate-600" aria-hidden="true">
                                        <i class="fa-light fa-plus"></i>
                                    </span>
                                </button>
                            @empty
                                <p class="rounded-xl border border-dashed border-slate-300 bg-white px-3 py-6 text-center text-sm text-slate-500">{{ __('Nema rezultata.') }}</p>
                            @endforelse
                        </div>
                    </section>

                    <section class="rounded-2xl border border-slate-200 bg-white p-4" aria-labelledby="selected-blog-categories">
                        <h2 id="selected-blog-categories" class="text-sm font-semibold text-slate-900">{{ __('Odabrane kategorije') }}</h2>

                        <div class="mt-3 space-y-2">
                            @forelse ($this->selectedCategoryRows as $index => $row)
                                <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="truncate text-sm font-semibold text-slate-800">{{ $row['label'] }}</span>
                                                @if ($index === 0)
                                                    <span class="rounded-full bg-cyan-100 px-2 py-0.5 text-[0.68rem] font-semibold uppercase tracking-[0.1em] text-cyan-800">{{ __('Primarna') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-1">
                                            <button type="button" wire:click="moveCategoryUp({{ $index }})" @disabled($index === 0) title="{{ __('Pomakni kategoriju gore') }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-35" aria-label="{{ __('Pomakni kategoriju gore') }}">
                                                <i class="fa-light fa-arrow-up" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" wire:click="moveCategoryDown({{ $index }})" @disabled($index === $this->selectedCategoryRows->count() - 1) title="{{ __('Pomakni kategoriju dolje') }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-35" aria-label="{{ __('Pomakni kategoriju dolje') }}">
                                                <i class="fa-light fa-arrow-down" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" wire:click="removeCategory({{ $row['id'] }})" title="{{ __('Makni kategoriju') }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-white text-rose-600 hover:bg-rose-50" aria-label="{{ __('Makni kategoriju') }}">
                                                <i class="fa-light fa-xmark" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="rounded-xl border border-dashed border-slate-300 px-3 py-8 text-center text-sm text-slate-500">{{ __('Još nije odabrana nijedna kategorija.') }}</p>
                            @endforelse
                        </div>
                    </section>
                </div>
            </div>
        @endif

        @if ($activeTab === 'seo')
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('SEO') }}</p>
                <div class="mt-4">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Meta Title') }}</label>
                    <input type="text" wire:model.live.debounce.250ms="form.meta_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    @error('form.meta_title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Meta Description') }}</label>
                    <textarea rows="4" wire:model.live.debounce.250ms="form.meta_description" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>
            </div>
        @endif

        @if ($activeTab === 'media')
            <livewire:admin.media.manager
                :model-class="\App\Models\Content\Blog\BlogPost::class"
                :model-id="$postId"
                :locale="$form['locale']"
                :wire:key="'blog-post-media-manager-'.($postId ?? 'new').'-'.$form['locale']"
            />
        @endif

        @include('livewire.admin.partials.form-actions', [
            'previewUrl' => $blogPreviewUrl,
            'submitLabel' => $isEdit ? __('Spremi članak') : __('Kreiraj članak'),
        ])
    </form>
</div>
