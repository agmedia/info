<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Content / Pages') }}</p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ $isEdit ? __('Edit Info Page') : __('Create Info Page') }}</h1>
                <p class="mt-2 text-sm text-slate-600">{{ __('Static page structure, locale content and SEO.') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="admin-chip">{{ __('Locale:') }} {{ $form['locale'] }}</span>
                <button type="button" wire:click="backToList" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">{{ __('Back to List') }}</button>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="admin-panel admin-form-panel p-3 sm:p-4">
            <div class="flex flex-wrap gap-2">
                <button type="button" wire:click="setTab('content')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'content' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                    {{ __('Sadržaj') }}
                </button>
                @if (($form['layout'] ?? '') === 'academy')
                    <button type="button" wire:click="setTab('sources')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'sources' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                        {{ __('Sources') }}
                    </button>
                @endif
                @if (in_array(($form['layout'] ?? ''), ['about', 'academy', 'career', 'references'], true))
                    <button type="button" wire:click="setTab('media')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'media' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                        {{ __('Media') }}
                    </button>
                @endif
                <button type="button" wire:click="setTab('seo')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] {{ $activeTab === 'seo' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100' }}">
                    {{ __('SEO') }}
                </button>
            </div>
        </div>

        @if ($activeTab === 'content')
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('Core Data') }}</p>

                <div class="mt-4 grid gap-3" style="grid-template-columns: repeat(12, minmax(0, 1fr));">
                    <div style="grid-column: span 3;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Code') }}</label>
                        <input type="text" wire:model="form.code" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-mono" />
                        @error('form.code') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Layout') }}</label>
                        <input type="text" wire:model="form.layout" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.layout') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div style="grid-column: span 3;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Published At') }}</label>
                        <input type="datetime-local" wire:model="form.published_at" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.published_at') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Sort Order') }}</label>
                        <input type="number" min="0" wire:model="form.sort_order" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.sort_order') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Locale') }}</label>
                        <select wire:model.live="form.locale" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase">
                            @foreach ($adminLocaleOptions as $localeOption)
                                <option value="{{ $localeOption }}">{{ $localeOption }}</option>
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
                        aria-label="{{ __('Toggle info page active state') }}"
                    >
                        <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                        <span class="admin-switch-label">{{ $form['is_active'] ? __('Active') : __('Inactive') }}</span>
                    </button>
                </div>

                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                        <input type="text" wire:model="form.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
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
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Excerpt') }}</label>
                    <textarea rows="3" wire:model="form.excerpt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>

                <div class="mt-3" wire:key="info-page-body-{{ $pageId ?? 'new' }}-{{ $form['locale'] }}">
                    <label for="info-page-body-html" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Body') }}</label>
                    <textarea id="info-page-body-html" rows="10" wire:model.live.debounce.300ms="form.body_html" data-quill-editor class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>

                @if (($form['layout'] ?? '') === 'references')
                    <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50/80 p-5 sm:p-6">
                        <p class="admin-section-title">{{ __('Reference Logos') }}</p>
                        <p class="mt-2 text-sm text-slate-500">{{ __('Tvrtke i logotipi za ovu stranicu uređuju se na Media tabu. Naziv tvrtke upišite u polje Name za svaki logo.') }}</p>
                    </div>
                @endif

                @if (($form['layout'] ?? '') === 'academy')
                    <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50/80 p-5 sm:p-6">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="admin-section-title">{{ __('Programi Akademije') }}</p>
                                <p class="mt-2 text-sm text-slate-500">{{ __('Ovaj blok upravlja gornjim sectionom s 4 kartice na Academy stranici, slično kao strukturirani blokovi na uslugama.') }}</p>
                            </div>
                        </div>

                        <div class="mt-6 space-y-5">
                            @foreach ((array) ($form['academy_programs'] ?? []) as $programIndex => $program)
                                <div wire:key="academy-program-{{ $programIndex }}" class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <h3 class="text-sm font-semibold uppercase tracking-[0.12em] text-slate-900">
                                            {{ __('Program') }} {{ $programIndex + 1 }}
                                        </h3>
                                        @if (($program['accent'] ?? '') !== '')
                                            <span class="admin-chip">{{ strtoupper((string) $program['accent']) }}</span>
                                        @endif
                                    </div>

                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Naslov kartice') }}</label>
                                            <input type="text" wire:model="form.academy_programs.{{ $programIndex }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            @error('form.academy_programs.'.$programIndex.'.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro kartice') }}</label>
                                            <textarea rows="4" wire:model="form.academy_programs.{{ $programIndex }}.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                            @error('form.academy_programs.'.$programIndex.'.intro') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    <div class="mt-5 space-y-4">
                                        @foreach ((array) ($program['items'] ?? []) as $itemIndex => $item)
                                            <div wire:key="academy-program-{{ $programIndex }}-item-{{ $itemIndex }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">{{ __('Box') }} {{ $itemIndex + 1 }}</p>

                                                <div class="mt-3">
                                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Naslov boxa') }}</label>
                                                    <input type="text" wire:model="form.academy_programs.{{ $programIndex }}.items.{{ $itemIndex }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                                    @error('form.academy_programs.'.$programIndex.'.items.'.$itemIndex.'.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                                </div>

                                                <div class="mt-3">
                                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Tekst boxa') }}</label>
                                                    <textarea rows="5" wire:model="form.academy_programs.{{ $programIndex }}.items.{{ $itemIndex }}.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                                    @error('form.academy_programs.'.$programIndex.'.items.'.$itemIndex.'.text') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (($form['layout'] ?? '') === 'career')
                    <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50/80 p-5 sm:p-6">
                        <div>
                            <p class="admin-section-title">{{ __('Karijera copy') }}</p>
                            <p class="mt-2 text-sm text-slate-500">{{ __('Mijenja tekstove za hero, proces prijave, CTA blok i naslov forme na stranici Karijera.') }}</p>
                        </div>

                        <div class="mt-6 space-y-6">
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                                <p class="text-sm font-semibold uppercase tracking-[0.12em] text-slate-900">{{ __('Intro sekcija') }}</p>

                                <div class="mt-4 grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Naslov') }}</label>
                                        <input type="text" wire:model="form.career_intro_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        @error('form.career_intro_title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Istaknuti tekst') }}</label>
                                        <textarea rows="4" wire:model="form.career_intro_highlight" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                        @error('form.career_intro_highlight') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Glavni tekst') }}</label>
                                    <textarea rows="5" wire:model="form.career_intro_body" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                    @error('form.career_intro_body') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                                <p class="text-sm font-semibold uppercase tracking-[0.12em] text-slate-900">{{ __('Proces prijave') }}</p>

                                <div class="mt-4 grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
                                        <input type="text" wire:model="form.career_process_kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        @error('form.career_process_kicker') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Uvod') }}</label>
                                        <textarea rows="4" wire:model="form.career_process_intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                        @error('form.career_process_intro') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="mt-4 grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Naslov red 1') }}</label>
                                        <input type="text" wire:model="form.career_process_title_line_one" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        @error('form.career_process_title_line_one') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Naslov red 2') }}</label>
                                        <input type="text" wire:model="form.career_process_title_line_two" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        @error('form.career_process_title_line_two') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="mt-5 space-y-4">
                                    @foreach ((array) ($form['career_process_steps'] ?? []) as $stepIndex => $step)
                                        <div wire:key="career-process-step-{{ $stepIndex }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">{{ __('Korak') }} {{ $stepIndex + 1 }}</p>

                                            <div class="mt-3 grid gap-4 md:grid-cols-2">
                                                <div>
                                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Label') }}</label>
                                                    <input type="text" wire:model="form.career_process_steps.{{ $stepIndex }}.step" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                                    @error('form.career_process_steps.'.$stepIndex.'.step') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                                </div>

                                                <div>
                                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Naslov') }}</label>
                                                    <input type="text" wire:model="form.career_process_steps.{{ $stepIndex }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                                    @error('form.career_process_steps.'.$stepIndex.'.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                                </div>
                                            </div>

                                            <div class="mt-3">
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Opis') }}</label>
                                                <textarea rows="4" wire:model="form.career_process_steps.{{ $stepIndex }}.description" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                                @error('form.career_process_steps.'.$stepIndex.'.description') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                                <p class="text-sm font-semibold uppercase tracking-[0.12em] text-slate-900">{{ __('CTA sekcija') }}</p>

                                <div class="mt-4 grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Naslov') }}</label>
                                        <input type="text" wire:model="form.career_application_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        @error('form.career_application_title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Istaknuti tekst') }}</label>
                                        <textarea rows="4" wire:model="form.career_application_highlight" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                        @error('form.career_application_highlight') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="mt-5 space-y-4">
                                    @foreach ((array) ($form['career_application_paragraphs'] ?? []) as $paragraphIndex => $paragraph)
                                        <div wire:key="career-application-paragraph-{{ $paragraphIndex }}">
                                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Odlomak') }} {{ $paragraphIndex + 1 }}</label>
                                            <textarea rows="4" wire:model="form.career_application_paragraphs.{{ $paragraphIndex }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                            @error('form.career_application_paragraphs.'.$paragraphIndex) <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                                <p class="text-sm font-semibold uppercase tracking-[0.12em] text-slate-900">{{ __('Naslov forme') }}</p>
                                <p class="mt-2 text-sm text-slate-500">{{ __('Ovdje se mijenja samo naslov bloka forme, npr. "Pošaljite nam svoj CV".') }}</p>

                                <div class="mt-4">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Naslov') }}</label>
                                    <input type="text" wire:model="form.career_form_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-xl" />
                                    @error('form.career_form_title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Page Categories (order defines primary)') }}</label>
                    <select wire:model="form.category_ids" multiple size="8" class="admin-multiselect w-full rounded-xl border border-slate-300 text-sm">
                        @foreach ($this->categoryOptions as $category)
                            <option value="{{ $category['id'] }}">{{ $category['label'] }}</option>
                        @endforeach
                    </select>
                    @error('form.category_ids.*') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        @endif

        @if ($activeTab === 'sources' && ($form['layout'] ?? '') === 'academy')
            <div class="space-y-6">
                <div class="grid gap-6 xl:grid-cols-2">
                    <div class="admin-panel admin-form-panel p-6">
                        <p class="admin-section-title">{{ __('Blog Feed') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Način') }}</label>
                            <input type="text" value="{{ __('Specific blog category') }}" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-600" readonly />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Limit') }}</label>
                            <input type="number" min="1" max="24" wire:model="form.academy_blog_limit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-[12rem]" />
                            @error('form.academy_blog_limit') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Blog Category') }}</label>
                            <select wire:model="form.academy_blog_category_id" data-tom-select class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                <option value="">{{ __('Select category') }}</option>
                                @foreach ($this->blogCategoryOptions as $category)
                                    <option value="{{ $category['id'] }}">{{ $category['label'] }}</option>
                                @endforeach
                            </select>
                            @error('form.academy_blog_category_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="admin-panel admin-form-panel p-6">
                        <p class="admin-section-title">{{ __('Section Copy') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section Title') }}</label>
                            <input type="text" wire:model="form.academy_blog_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            <p class="mt-1 text-xs text-slate-500">{{ __('If left empty, the frontend will generate the title from the selected category.') }}</p>
                            @error('form.academy_blog_title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section Intro') }}</label>
                            <textarea rows="4" wire:model="form.academy_blog_intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            @error('form.academy_blog_intro') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div class="admin-panel admin-form-panel p-6">
                        <p class="admin-section-title">{{ __('Dokumenti Za Preuzimanje') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Način') }}</label>
                            <input type="text" value="{{ __('Manual document selection') }}" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-600" readonly />
                        </div>

                        <div class="mt-4 grid gap-3 md:grid-cols-[1fr_auto] md:items-end">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Dostupni dokumenti') }}</label>
                                <select wire:model="academyDocumentPickerId" data-tom-select class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                    <option value="">{{ __('Odaberi dokument...') }}</option>
                                    @foreach ($this->resourceDocumentOptions as $document)
                                        <option value="{{ $document['id'] }}">{{ $document['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" wire:click="addAcademyDocument({{ (int) ($academyDocumentPickerId ?? 0) }})" class="h-10 rounded-xl bg-cyan-700 px-4 text-sm font-semibold text-white hover:bg-cyan-800">
                                {{ __('Dodaj') }}
                            </button>
                        </div>
                        @error('form.academy_resource_document_ids.*') <p class="mt-2 text-xs text-rose-600">{{ $message }}</p> @enderror

                        <div class="mt-4 space-y-2">
                            @forelse ($this->selectedAcademyDocuments as $document)
                                <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                                    <div class="text-sm text-slate-800">{{ $document['label'] }}</div>
                                    <div class="inline-flex items-center gap-1">
                                        <button type="button" wire:click="moveAcademyDocumentUp({{ $document['index'] }})" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">{{ __('Up') }}</button>
                                        <button type="button" wire:click="moveAcademyDocumentDown({{ $document['index'] }})" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">{{ __('Down') }}</button>
                                        <button type="button" wire:click="removeAcademyDocument({{ $document['id'] }})" class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50">{{ __('Remove') }}</button>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500">{{ __('Nema odabranih dokumenata.') }}</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="admin-panel admin-form-panel p-6">
                        <p class="admin-section-title">{{ __('Copy Sekcije Dokumenata') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section Title') }}</label>
                            <input type="text" wire:model="form.academy_resource_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            <p class="mt-1 text-xs text-slate-500">{{ __('Ako ostane prazno, frontend koristi zadani naslov "Dokumenti za preuzimanje".') }}</p>
                            @error('form.academy_resource_title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section Intro') }}</label>
                            <textarea rows="4" wire:model="form.academy_resource_intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            @error('form.academy_resource_intro') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div class="admin-panel admin-form-panel p-6">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <p class="admin-section-title">{{ __('Video sadržaj') }}</p>
                            <button type="button" wire:click="addAcademyVideo" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                                {{ __('Dodaj video') }}
                            </button>
                        </div>

                        <p class="mt-2 text-sm text-slate-500">{{ __('Unesi naslov videa i YouTube link. Redoslijed ovdje je isti kao na stranici.') }}</p>

                        <div class="mt-4 space-y-4">
                            @forelse ((array) ($form['academy_video_items'] ?? []) as $index => $video)
                                <div wire:key="academy-video-row-{{ $index }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="grid gap-3 md:grid-cols-[1fr_1.2fr_auto] md:items-start">
                                        <div>
                                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Naslov videa') }}</label>
                                            <input type="text" wire:model="form.academy_video_items.{{ $index }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                            @error('form.academy_video_items.'.$index.'.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('YouTube URL') }}</label>
                                            <input type="text" wire:model="form.academy_video_items.{{ $index }}.youtube_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="https://www.youtube.com/watch?v=..." />
                                            @error('form.academy_video_items.'.$index.'.youtube_url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                        </div>

                                        <div class="flex flex-wrap gap-2 md:justify-end">
                                            <button type="button" wire:click="moveAcademyVideoUp({{ $index }})" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">{{ __('Up') }}</button>
                                            <button type="button" wire:click="moveAcademyVideoDown({{ $index }})" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">{{ __('Down') }}</button>
                                            <button type="button" wire:click="removeAcademyVideo({{ $index }})" class="rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50">{{ __('Remove') }}</button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500">{{ __('Nema unesenih videa.') }}</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="admin-panel admin-form-panel p-6">
                        <p class="admin-section-title">{{ __('Copy Sekcije Videa') }}</p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section Title') }}</label>
                            <input type="text" wire:model="form.academy_video_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            <p class="mt-1 text-xs text-slate-500">{{ __('Ako ostane prazno, frontend koristi zadani naslov za video sekciju.') }}</p>
                            @error('form.academy_video_title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section Intro') }}</label>
                            <textarea rows="4" wire:model="form.academy_video_intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            @error('form.academy_video_intro') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($activeTab === 'seo')
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title">{{ __('SEO & Payload') }}</p>

                <div class="mt-4">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Meta Title') }}</label>
                    <input type="text" wire:model="form.meta_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    @error('form.meta_title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Meta Description') }}</label>
                    <textarea rows="3" wire:model="form.meta_description" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Page Payload JSON') }}</label>
                    <textarea rows="6" wire:model="form.payload_text" class="w-full rounded-xl border border-slate-300 px-3 py-2 font-mono text-xs"></textarea>
                    @error('form.payload_text') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Translation Payload JSON') }}</label>
                    <textarea rows="6" wire:model="form.translation_payload_text" class="w-full rounded-xl border border-slate-300 px-3 py-2 font-mono text-xs"></textarea>
                    @error('form.translation_payload_text') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        @endif

        @if ($activeTab === 'media' && in_array(($form['layout'] ?? ''), ['about', 'academy', 'career', 'references'], true))
            @php
                $pageMediaCollections = match ($form['layout'] ?? '') {
                    'about' => ['about_hero_image'],
                    'academy' => ['academy_gallery'],
                    'career' => ['career_hero_image'],
                    'references' => ['reference_logos'],
                    default => [],
                };
            @endphp
            <livewire:admin.media.manager
                :model-class="\App\Models\Content\Page\InfoPage::class"
                :model-id="$pageId"
                :locale="$form['locale']"
                :only-collections="$pageMediaCollections"
                :wire:key="'info-page-media-manager-'.($pageId ?? 'new').'-'.$form['locale']"
            />
        @endif

        <div class="admin-form-actions flex items-center gap-2 pt-2">
            <button type="submit" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                {{ $isEdit ? __('Update Info Page') : __('Create Info Page') }}
            </button>
            <button type="button" wire:click="backToList" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                {{ __('Cancel') }}
            </button>
        </div>
    </form>
</div>
