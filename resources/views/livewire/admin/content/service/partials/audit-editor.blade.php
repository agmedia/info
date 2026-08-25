<div class="admin-panel admin-form-panel p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="admin-section-title">{{ __('Frontend content') }}</p>
            <h2 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">{{ __('Audit landing page') }}</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                {{ __('The sections below follow the frontend order. Only content that is actually displayed on the Audit page is shown here.') }}
            </p>
        </div>

        <a
            href="{{ route('audit.show') }}"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-cyan-700 bg-white px-4 py-2 text-sm font-semibold text-cyan-800 hover:bg-cyan-50"
        >
            {{ __('Open frontend') }}
            <i class="fa-light fa-arrow-up-right-from-square" aria-hidden="true"></i>
        </a>
    </div>

    <div class="mt-5 flex flex-wrap gap-2" aria-label="{{ __('Page section navigation') }}">
        <a href="#audit-hero-admin" class="admin-chip">1. {{ __('Hero and image') }}</a>
        <a href="#audit-overview-admin" class="admin-chip">2. {{ __('Why audit matters') }}</a>
        <a href="#audit-obligors-admin" class="admin-chip">3. {{ __('Audit obligors') }}</a>
        <a href="#audit-services-admin" class="admin-chip">4. {{ __('Audit services') }}</a>
        <a href="#audit-approach-admin" class="admin-chip">5. {{ __('Our approach') }}</a>
        <a href="#audit-blog-admin" class="admin-chip">6. {{ __('Expert posts') }}</a>
        <a href="#audit-meeting-admin" class="admin-chip">7. {{ __('Contact CTA') }}</a>
        <a href="#audit-settings-admin" class="admin-chip">8. {{ __('Page settings') }}</a>
    </div>
</div>

<div id="audit-hero-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">1. {{ __('Hero and image') }}</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ __('Top of the Audit page') }}</h2>
        <p class="mt-1 text-sm text-slate-600">{{ __('Edit the service name, main statement, background image, and accessible image description.') }}</p>
    </div>

    @php
        $auditHeroUpload = $auditHeroImageUpload ?? null;
        $auditHeroPreviewUrl = $auditHeroUpload instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile
            ? $auditHeroUpload->temporaryUrl()
            : (string) ($auditHeroImage['url'] ?? '');
    @endphp

    <div class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1.2fr)_minmax(320px,0.8fr)]">
        <div class="space-y-4">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Service name') }}</label>
                <input type="text" wire:model="form.translation_payload.hero.subtitle_lead" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                @error('form.translation_payload.hero.subtitle_lead') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Main hero statement') }}</label>
                <textarea rows="5" wire:model="form.translation_payload.hero.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
                @error('form.translation_payload.hero.intro') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Image alternative text') }}</label>
                <input type="text" wire:model="form.translation_payload.hero.image_alt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                <p class="mt-1 text-xs text-slate-500">{{ __('Describe the hero image briefly for screen-reader users.') }}</p>
                @error('form.translation_payload.hero.image_alt') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
                @if ($auditHeroPreviewUrl !== '')
                    <img src="{{ $auditHeroPreviewUrl }}" alt="" class="aspect-video w-full object-cover" />
                @endif
            </div>

            <div class="mt-3 flex items-center justify-between gap-3">
                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Hero image') }}</label>
                <span class="admin-chip">{{ ($auditHeroImage['is_custom'] ?? false) ? __('Custom image') : __('Default image') }}</span>
            </div>
            <input
                type="file"
                wire:model="auditHeroImageUpload"
                accept="{{ $serviceImageAccept }}"
                class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-200"
            />
            <p class="mt-1 text-xs text-slate-500">{{ __('Recommended ratio: 16:9. The selected image is saved together with the page.') }}</p>
            @error('auditHeroImageUpload') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

            @if (($auditHeroImage['is_custom'] ?? false) && ! $auditHeroUpload)
                <button
                    type="button"
                    wire:click="removeAuditHeroImage"
                    wire:confirm="{{ __('Remove the custom hero image and restore the default image?') }}"
                    class="mt-2 text-xs font-semibold text-rose-600 hover:text-rose-700"
                >
                    {{ __('Restore default image') }}
                </button>
            @endif
        </div>
    </div>
</div>

<div id="audit-overview-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">2. {{ __('Why audit matters') }}</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ __('Introductory 50/50 section') }}</h2>
    </div>

    <div class="mt-5">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Heading') }}</label>
        <input type="text" wire:model="form.translation_payload.overview.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        @error('form.translation_payload.overview.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="mt-4">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section text') }}</label>
        <textarea
            id="audit-overview-body-html"
            rows="12"
            wire:model.live.debounce.300ms="form.translation_payload.overview.body_html"
            data-quill-editor
            data-quill-profile="service-text"
            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"
        ></textarea>
        <p class="mt-1 text-xs text-slate-500">{{ __('The final content block is displayed as the emphasized paragraph.') }}</p>
        @error('form.translation_payload.overview.body_html') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
</div>

<div id="audit-obligors-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">3. {{ __('Audit obligors') }}</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ __('Statutory audit obligors and criteria') }}</h2>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section heading') }}</label>
            <input type="text" wire:model="form.translation_payload.obligors.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            @error('form.translation_payload.obligors.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('List heading') }}</label>
            <input type="text" wire:model="form.translation_payload.obligors.primary_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>

    <div class="mt-4">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Highlighted note') }}</label>
        <textarea rows="4" wire:model="form.translation_payload.obligors.note" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
    </div>

    <div class="mt-5 space-y-4">
        @foreach (($translationPayload['obligors']['primary_items'] ?? []) as $index => $item)
            @php
                $isStructuredObligor = is_array($item);
            @endphp
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Obligor item') }} {{ $index + 1 }}</p>
                    <button type="button" wire:click="removeTranslationListItem('obligors.primary_items', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                </div>

                @if ($isStructuredObligor)
                    <textarea rows="3" wire:model="form.translation_payload.obligors.primary_items.{{ $index }}.text" class="mt-3 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>

                    <div class="mt-4 border-l-2 border-cyan-200 pl-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Criteria shown under this item') }}</p>
                        <textarea
                            rows="6"
                            wire:model="form.translation_payload.obligors.primary_items.{{ $index }}.children_text"
                            class="mt-3 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"
                        ></textarea>
                        <p class="mt-1 text-xs text-slate-500">{{ __('Enter each criterion on a new line.') }}</p>
                    </div>
                @else
                    <textarea rows="3" wire:model="form.translation_payload.obligors.primary_items.{{ $index }}" class="mt-3 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>
                @endif
            </div>
        @endforeach
    </div>

    <button type="button" wire:click="addTranslationListItem('obligors.primary_items', 'audit_obligor')" class="mt-4 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
        {{ __('Add obligor') }}
    </button>
</div>

<div id="audit-services-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">4. {{ __('Audit services') }}</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ __('Service cards displayed on the page') }}</h2>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section heading') }}</label>
            <input type="text" wire:model="form.translation_payload.services.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            @error('form.translation_payload.services.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Optional section intro') }}</label>
            <textarea rows="3" wire:model="form.translation_payload.services.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
        </div>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-3">
        @foreach (($translationPayload['services']['items'] ?? []) as $index => $item)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Service card') }} {{ $index + 1 }}</p>
                    <button type="button" wire:click="removeTranslationListItem('services.items', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                </div>
                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                    <input type="text" wire:model="form.translation_payload.services.items.{{ $index }}.title" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" />
                    @error('form.translation_payload.services.items.'.$index.'.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Description') }}</label>
                    <textarea rows="6" wire:model="form.translation_payload.services.items.{{ $index }}.text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>
                    @error('form.translation_payload.services.items.'.$index.'.text') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        @endforeach
    </div>

    <button type="button" wire:click="addTranslationListItem('services.items', 'title_text')" class="mt-4 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
        {{ __('Add service card') }}
    </button>
</div>

<div id="audit-approach-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">5. {{ __('Our approach') }}</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ __('Dark quote section') }}</h2>
    </div>

    <div class="mt-5">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section heading') }}</label>
        <input type="text" wire:model="form.translation_payload.approach.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        @error('form.translation_payload.approach.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="mt-5">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Quote text') }}</label>
        <textarea
            id="audit-approach-body-html"
            rows="12"
            wire:model.live.debounce.300ms="form.translation_payload.approach.body_html"
            data-quill-editor
            data-quill-profile="service-text"
            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"
        ></textarea>
        @error('form.translation_payload.approach.body_html') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
</div>

<div id="audit-blog-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">6. {{ __('Expert posts') }}</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ __('Blog section labels') }}</h2>
        <p class="mt-1 text-sm text-slate-600">{{ __('The category and posts are selected under Sources. This section controls the visible labels.') }}</p>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-3">
        <div class="xl:col-span-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Section heading') }}</label>
            <input type="text" wire:model="form.translation_payload.blog_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            <p class="mt-1 text-xs text-slate-500">{{ __('Use :category placeholder if you want the current blog category name inserted automatically.') }}</p>
            @error('form.translation_payload.blog_section.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('All posts link label') }}</label>
            <input type="text" wire:model="form.translation_payload.blog_section.all_posts_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Post card link label') }}</label>
            <input type="text" wire:model="form.translation_payload.blog_section.post_action_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>
</div>

<div id="audit-meeting-admin" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="border-b border-slate-200 pb-4">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">7. {{ __('Contact CTA') }}</p>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ __('Final contact section') }}</h2>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Main heading') }}</label>
            <input type="text" wire:model="form.translation_payload.meeting.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            @error('form.translation_payload.meeting.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Contact card heading') }}</label>
            <input type="text" wire:model="form.translation_payload.meeting.contact_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div class="xl:col-span-2">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Contact text') }}</label>
            <textarea rows="4" wire:model="form.translation_payload.meeting.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Button label') }}</label>
            <input type="text" wire:model="form.translation_payload.meeting.button_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Status note') }}</label>
            <input type="text" wire:model="form.translation_payload.meeting.status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>
</div>
