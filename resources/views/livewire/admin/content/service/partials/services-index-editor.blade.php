<div class="admin-panel admin-form-panel p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="admin-section-title">{{ __('Frontend content') }}</p>
            <h2 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">{{ __('Usluge landing page') }}</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                {{ __('The sections below follow the same order as the frontend. Every visible page-specific text on the Usluge landing page is editable here.') }}
            </p>
        </div>

        <a
            href="{{ route('services.index') }}"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-cyan-700 bg-white px-4 py-2 text-sm font-semibold text-cyan-800 hover:bg-cyan-50"
        >
            {{ __('Open frontend') }}
            <i class="fa-light fa-arrow-up-right-from-square" aria-hidden="true"></i>
        </a>
    </div>

    <div class="mt-5 flex flex-wrap gap-2" aria-label="{{ __('Page section navigation') }}">
        <a href="#services-index-editor" class="admin-chip">1. {{ __('Intro section') }}</a>
        <a href="#services-index-cards-editor" class="admin-chip">2. {{ __('Service cards') }}</a>
        <a href="#services-index-settings" class="admin-chip">3. {{ __('Page settings') }}</a>
    </div>
</div>

<div id="services-index-editor" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="flex flex-col gap-2 border-b border-slate-200 pb-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">1. {{ __('Intro section') }}</p>
            <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ __('Heading and introductory text') }}</h2>
            <p class="mt-1 text-sm text-slate-600">{{ __('This content appears at the top of the Usluge page.') }}</p>
        </div>
        <span class="admin-chip">{{ __('Frontend: top of page') }}</span>
    </div>

    <div class="mt-5">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Heading') }}</label>
        <input type="text" wire:model="form.translation_payload.showcase.title_lead" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        <p class="mt-1 text-xs text-slate-500">{{ __('Example: Naše usluge') }}</p>
        @error('form.translation_payload.showcase.title_lead') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="mt-4">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Introductory text') }}</label>
        <textarea rows="5" wire:model="form.translation_payload.showcase.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6"></textarea>
        <p class="mt-1 text-xs text-slate-500">{{ __('The service names in this text are automatically linked to their corresponding pages when they match the configured names.') }}</p>
        @error('form.translation_payload.showcase.intro') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
</div>

<div id="services-index-cards-editor" class="admin-panel admin-form-panel scroll-mt-24 p-6">
    <div class="flex flex-col gap-4 border-b border-slate-200 pb-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">2. {{ __('Service cards') }}</p>
            <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ __('Three main services') }}</h2>
            <p class="mt-1 text-sm text-slate-600">{{ __('The cards are fixed in the same order as on the frontend: audit, accounting, and advisory.') }}</p>
        </div>

        <div class="w-full lg:max-w-xs">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Card link text') }}</label>
            <input type="text" wire:model="form.translation_payload.showcase.card_action_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            <p class="mt-1 text-xs text-slate-500">{{ __('The same label is shown on all three cards.') }}</p>
            @error('form.translation_payload.showcase.card_action_label') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="mt-5 grid gap-5 xl:grid-cols-3">
        @foreach (($translationPayload['primary_pillars'] ?? []) as $cardIndex => $card)
            @php
                $cardKey = (string) ($card['key'] ?? '');
                $cardImage = (array) ($servicesIndexCardImages[$cardKey] ?? []);
                $cardImageUpload = $landingImageUploads[$cardKey] ?? null;
                $cardImagePreviewUrl = $cardImageUpload instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile
                    ? $cardImageUpload->temporaryUrl()
                    : (string) ($cardImage['url'] ?? '');
                $cardTypeLabel = match ($cardKey) {
                    'audit' => __('Audit'),
                    'accounting' => __('Accounting'),
                    'advisory' => __('Advisory'),
                    default => __('Card') . ' ' . ($cardIndex + 1),
                };
            @endphp

            <section class="rounded-2xl border border-slate-200 bg-slate-50 p-4" aria-labelledby="services-card-editor-{{ $cardIndex }}">
                <div class="flex items-center justify-between gap-3">
                    <h3 id="services-card-editor-{{ $cardIndex }}" class="font-semibold text-slate-900">{{ $cardTypeLabel }}</h3>
                    <span class="rounded-full bg-white px-2.5 py-1 text-[0.68rem] font-semibold uppercase tracking-[0.12em] text-slate-500">{{ __('Card') }} {{ $cardIndex + 1 }}</span>
                </div>

                <div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
                    @if ($cardImagePreviewUrl !== '')
                        <img src="{{ $cardImagePreviewUrl }}" alt="" class="aspect-[4/5] w-full object-cover" />
                    @endif
                </div>

                <div class="mt-3">
                    <div class="flex items-center justify-between gap-2">
                        <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Card image') }}</label>
                        <span class="admin-chip">{{ ($cardImage['is_custom'] ?? false) ? __('Custom image') : __('Default image') }}</span>
                    </div>
                    <input
                        type="file"
                        wire:model="landingImageUploads.{{ $cardKey }}"
                        accept="image/jpeg,image/png,image/webp,image/avif"
                        class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-200"
                    />
                    <p class="mt-1 text-xs text-slate-500">{{ __('Recommended ratio: 4:5. The selected image is saved together with the page.') }}</p>
                    @error('landingImageUploads.'.$cardKey) <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

                    @if (($cardImage['is_custom'] ?? false) && ! $cardImageUpload)
                        <button
                            type="button"
                            wire:click="removeServicesIndexCardImage('{{ $cardKey }}')"
                            wire:confirm="{{ __('Remove the custom image and restore the default image?') }}"
                            class="mt-2 text-xs font-semibold text-rose-600 hover:text-rose-700"
                        >
                            {{ __('Restore default image') }}
                        </button>
                    @endif
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Image alternative text') }}</label>
                    <textarea rows="3" wire:model="form.translation_payload.primary_pillars.{{ $cardIndex }}.image_alt" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm"></textarea>
                    <p class="mt-1 text-xs text-slate-500">{{ __('Describe the image briefly for screen-reader users.') }}</p>
                    @error('form.translation_payload.primary_pillars.'.$cardIndex.'.image_alt') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-4">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                    <input type="text" wire:model="form.translation_payload.primary_pillars.{{ $cardIndex }}.title" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" />
                    @error('form.translation_payload.primary_pillars.'.$cardIndex.'.title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Subtitle') }}</label>
                    <input type="text" wire:model="form.translation_payload.primary_pillars.{{ $cardIndex }}.subtitle" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" />
                    @error('form.translation_payload.primary_pillars.'.$cardIndex.'.subtitle') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Description') }}</label>
                    <textarea rows="5" wire:model="form.translation_payload.primary_pillars.{{ $cardIndex }}.text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm leading-6"></textarea>
                    @error('form.translation_payload.primary_pillars.'.$cardIndex.'.text') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <details class="mt-4 rounded-xl border border-slate-200 bg-white p-3">
                    <summary class="cursor-pointer text-sm font-semibold text-slate-700">{{ __('Link settings') }}</summary>
                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Destination URL') }}</label>
                        <input type="text" wire:model="form.translation_payload.primary_pillars.{{ $cardIndex }}.url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        @error('form.translation_payload.primary_pillars.'.$cardIndex.'.url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                </details>
            </section>
        @endforeach
    </div>
</div>
