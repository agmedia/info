<div class="admin-panel admin-form-panel p-6">
    <p class="admin-section-title">{{ __('EU Funds Navigator') }}</p>
    <div class="mt-4 flex flex-wrap gap-2">
        @foreach ($euFundsEditorSections as $sectionId => $sectionLabel)
            <a href="#{{ $sectionId }}" class="admin-chip">{{ $sectionLabel }}</a>
        @endforeach
    </div>
    <p class="mt-4 text-sm text-slate-600">
        {{ __('EU fondovi koristi zaseban landing layout. Ovdje uređujete tekstove sekcija, kartice programa, zakonske blokove i završne kontakt/blog elemente koji se prikazuju na frontend stranici.') }}
    </p>
</div>

<div id="eu-funds-about" class="admin-panel admin-form-panel p-6 scroll-mt-24">
    <p class="admin-section-title">{{ __('About Block') }}</p>

    <div class="mt-4 grid gap-3 md:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
            <input type="text" wire:model="form.translation_payload.about.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
            <input type="text" wire:model="form.translation_payload.about.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>

    @foreach (($translationPayload['about']['body'] ?? []) as $index => $paragraph)
        <div class="mt-3">
            <div class="mb-1 flex items-center justify-between gap-3">
                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $index + 1 }}</label>
                <button type="button" wire:click="removeTranslationListItem('about.body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
            </div>
            <textarea rows="5" wire:model="form.translation_payload.about.body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>
    @endforeach

    <div class="mt-3">
        <button type="button" wire:click="addTranslationListItem('about.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
            {{ __('Add Paragraph') }}
        </button>
    </div>

    <div class="mt-6">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Supporting Box Title') }}</label>
        <input type="text" wire:model="form.translation_payload.about.box_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
    </div>

    @foreach (($translationPayload['about']['box_items'] ?? []) as $index => $item)
        <div class="mt-3">
            <div class="mb-1 flex items-center justify-between gap-3">
                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Supporting Box Item') }} #{{ $index + 1 }}</label>
                <button type="button" wire:click="removeTranslationListItem('about.box_items', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
            </div>
            <input type="text" wire:model="form.translation_payload.about.box_items.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    @endforeach

    <div class="mt-3">
        <button type="button" wire:click="addTranslationListItem('about.box_items')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
            {{ __('Add Supporting Item') }}
        </button>
    </div>
</div>

<div class="grid gap-6 xl:grid-cols-2">
    <div id="eu-funds-overview" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <p class="admin-section-title">{{ __('Overview Block') }}</p>

        <div class="mt-4">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
            <input type="text" wire:model="form.translation_payload.overview.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
            <input type="text" wire:model="form.translation_payload.overview.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
            <textarea rows="4" wire:model="form.translation_payload.overview.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>

        @foreach (($translationPayload['overview']['body'] ?? []) as $index => $paragraph)
            <div class="mt-3">
                <div class="mb-1 flex items-center justify-between gap-3">
                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $index + 1 }}</label>
                    <button type="button" wire:click="removeTranslationListItem('overview.body', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                </div>
                <textarea rows="4" wire:model="form.translation_payload.overview.body.{{ $index }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
            </div>
        @endforeach

        <div class="mt-3">
            <button type="button" wire:click="addTranslationListItem('overview.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                {{ __('Add Paragraph') }}
            </button>
        </div>
    </div>

    <div id="eu-funds-chart" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <p class="admin-section-title">{{ __('Funding Chart') }}</p>

        <div class="mt-4">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
            <input type="text" wire:model="form.translation_payload.chart.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
            <input type="text" wire:model="form.translation_payload.chart.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
            <textarea rows="4" wire:model="form.translation_payload.chart.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>

        @foreach (($translationPayload['chart']['stats'] ?? []) as $index => $stat)
            <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Stat') }} #{{ $index + 1 }}</p>
                    <button type="button" wire:click="removeTranslationListItem('chart.stats', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                </div>

                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Label') }}</label>
                        <input type="text" wire:model="form.translation_payload.chart.stats.{{ $index }}.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Value') }}</label>
                        <input type="text" wire:model="form.translation_payload.chart.stats.{{ $index }}.value" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>

                <div class="mt-3 grid gap-3 md:grid-cols-[160px_1fr]">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Share (%)') }}</label>
                        <input type="number" min="0" max="100" wire:model="form.translation_payload.chart.stats.{{ $index }}.share" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Description') }}</label>
                        <input type="text" wire:model="form.translation_payload.chart.stats.{{ $index }}.description" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>
            </div>
        @endforeach

        <div class="mt-3">
            <button type="button" wire:click="addTranslationListItem('chart.stats', 'eu_chart_stat')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                {{ __('Add Stat') }}
            </button>
        </div>

        <div class="mt-4">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Footnote') }}</label>
            <textarea rows="3" wire:model="form.translation_payload.chart.footnote" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>
    </div>
</div>

<div class="grid gap-6 xl:grid-cols-2">
    <div id="eu-funds-process" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <p class="admin-section-title">{{ __('Process Section') }}</p>

        <div class="mt-4">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
            <input type="text" wire:model="form.translation_payload.process.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
            <input type="text" wire:model="form.translation_payload.process.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
            <textarea rows="4" wire:model="form.translation_payload.process.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>

        @foreach (($translationPayload['process']['items'] ?? []) as $index => $item)
            <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Process Item') }} #{{ $index + 1 }}</p>
                    <button type="button" wire:click="removeTranslationListItem('process.items', {{ $index }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                    <input type="text" wire:model="form.translation_payload.process.items.{{ $index }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Text') }}</label>
                    <textarea rows="4" wire:model="form.translation_payload.process.items.{{ $index }}.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>
            </div>
        @endforeach

        <div class="mt-3">
            <button type="button" wire:click="addTranslationListItem('process.items', 'eu_process_item')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                {{ __('Add Process Item') }}
            </button>
        </div>
    </div>

    <div id="eu-funds-calls" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <p class="admin-section-title">{{ __('Calls Section') }}</p>

        <div class="mt-4">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
            <input type="text" wire:model="form.translation_payload.calls.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
            <input type="text" wire:model="form.translation_payload.calls.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
            <textarea rows="4" wire:model="form.translation_payload.calls.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>

        <p class="mt-4 text-sm text-slate-600">
            {{ __('Kartice poziva na frontend stranici popunjavaju se automatski iz Call sadržaja kada je dostupan. Ovdje uređujete naslov sekcije i fallback download CTA.') }}
        </p>

        <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Download CTA') }}</p>

            <div class="mt-3 grid gap-3 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Button Label') }}</label>
                    <input type="text" wire:model="form.translation_payload.calls.download_link.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Link Type') }}</label>
                    <select wire:model="form.translation_payload.calls.download_link.type" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        <option value="none">{{ __('None') }}</option>
                        <option value="external">{{ __('External / Internal URL') }}</option>
                        <option value="blog">{{ __('Blog Post') }}</option>
                        <option value="call">{{ __('Call Post') }}</option>
                        <option value="pdf">{{ __('PDF Asset') }}</option>
                    </select>
                </div>
            </div>

                <div class="mt-3 grid gap-3 md:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('URL') }}</label>
                        <input type="text" wire:model="form.translation_payload.calls.download_link.url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Slug') }}</label>
                        <input type="text" wire:model="form.translation_payload.calls.download_link.slug" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    @include('livewire.admin.content.service.partials.pdf-asset-upload-field', [
                        'currentPath' => (string) ($translationPayload['calls']['download_link']['path'] ?? ''),
                        'uploadModel' => 'assetUploads.calls_download_link_path',
                    ])
                </div>
            </div>
        </div>
</div>

<div id="eu-funds-resources" class="admin-panel admin-form-panel p-6 scroll-mt-24">
    <p class="admin-section-title">{{ __('Resources Section') }}</p>

    <div class="mt-4 grid gap-3 md:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
            <input type="text" wire:model="form.translation_payload.resources.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
            <input type="text" wire:model="form.translation_payload.resources.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>

    <div class="mt-3">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
        <textarea rows="4" wire:model="form.translation_payload.resources.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
    </div>

    <div class="mt-6 space-y-5">
        @foreach (($translationPayload['resources']['cards'] ?? []) as $cardIndex => $card)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Resource Card') }} #{{ $cardIndex + 1 }}</p>

                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Eyebrow') }}</label>
                        <input type="text" wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.eyebrow" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                        <input type="text" wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>

                @foreach (($card['body'] ?? []) as $paragraphIndex => $paragraph)
                    <div class="mt-3">
                        <div class="mb-1 flex items-center justify-between gap-3">
                            <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Paragraph') }} #{{ $paragraphIndex + 1 }}</label>
                            <button type="button" wire:click="removeTranslationListItem('resources.cards.{{ $cardIndex }}.body', {{ $paragraphIndex }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                        </div>
                        <textarea rows="4" wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.body.{{ $paragraphIndex }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>
                @endforeach

                <div class="mt-3">
                    <button type="button" wire:click="addTranslationListItem('resources.cards.{{ $cardIndex }}.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                        {{ __('Add Paragraph') }}
                    </button>
                </div>

                @foreach (($card['groups'] ?? []) as $groupIndex => $group)
                    <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Group Label') }} #{{ $groupIndex + 1 }}</label>
                        <input type="text" wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.groups.{{ $groupIndex }}.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />

                        @foreach (($group['items'] ?? []) as $itemIndex => $item)
                            <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Group Item') }} #{{ $itemIndex + 1 }}</p>

                                <div class="mt-3">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                                    <input type="text" wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.groups.{{ $groupIndex }}.items.{{ $itemIndex }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>

                                <div class="mt-3 grid gap-3 md:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Link Type') }}</label>
                                        <select wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.groups.{{ $groupIndex }}.items.{{ $itemIndex }}.link.type" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                            <option value="none">{{ __('None') }}</option>
                                            <option value="external">{{ __('External / Internal URL') }}</option>
                                            <option value="blog">{{ __('Blog Post') }}</option>
                                            <option value="call">{{ __('Call Post') }}</option>
                                            <option value="pdf">{{ __('PDF Asset') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Link Label') }}</label>
                                        <input type="text" wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.groups.{{ $groupIndex }}.items.{{ $itemIndex }}.link.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                    </div>
                                </div>

                                <div class="mt-3 grid gap-3 md:grid-cols-3">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('URL') }}</label>
                                        <input type="text" wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.groups.{{ $groupIndex }}.items.{{ $itemIndex }}.link.url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Slug') }}</label>
                                        <input type="text" wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.groups.{{ $groupIndex }}.items.{{ $itemIndex }}.link.slug" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                    </div>
                                    @include('livewire.admin.content.service.partials.pdf-asset-upload-field', [
                                        'currentPath' => (string) ($item['link']['path'] ?? ''),
                                        'uploadModel' => 'assetUploads.resources_cards_'.$cardIndex.'_groups_'.$groupIndex.'_items_'.$itemIndex.'_link_path',
                                    ])
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach

                <div class="mt-5 grid gap-4 xl:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Primary Link') }}</p>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.primary_link.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Type') }}</label>
                                <select wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.primary_link.type" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                    <option value="none">{{ __('None') }}</option>
                                    <option value="external">{{ __('External / Internal URL') }}</option>
                                    <option value="blog">{{ __('Blog Post') }}</option>
                                    <option value="call">{{ __('Call Post') }}</option>
                                    <option value="pdf">{{ __('PDF Asset') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('URL') }}</label>
                                <input type="text" wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.primary_link.url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Slug') }}</label>
                                <input type="text" wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.primary_link.slug" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            @include('livewire.admin.content.service.partials.pdf-asset-upload-field', [
                                'currentPath' => (string) ($card['primary_link']['path'] ?? ''),
                                'uploadModel' => 'assetUploads.resources_cards_'.$cardIndex.'_primary_link_path',
                            ])
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Secondary Link') }}</p>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.secondary_link.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Type') }}</label>
                                <select wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.secondary_link.type" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                    <option value="none">{{ __('None') }}</option>
                                    <option value="external">{{ __('External / Internal URL') }}</option>
                                    <option value="blog">{{ __('Blog Post') }}</option>
                                    <option value="call">{{ __('Call Post') }}</option>
                                    <option value="pdf">{{ __('PDF Asset') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('URL') }}</label>
                                <input type="text" wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.secondary_link.url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Slug') }}</label>
                                <input type="text" wire:model="form.translation_payload.resources.cards.{{ $cardIndex }}.secondary_link.slug" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            @include('livewire.admin.content.service.partials.pdf-asset-upload-field', [
                                'currentPath' => (string) ($card['secondary_link']['path'] ?? ''),
                                'uploadModel' => 'assetUploads.resources_cards_'.$cardIndex.'_secondary_link_path',
                            ])
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div id="eu-funds-laws" class="admin-panel admin-form-panel p-6 scroll-mt-24">
    <p class="admin-section-title">{{ __('Laws Section') }}</p>

    <div class="mt-4 grid gap-3 md:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
            <input type="text" wire:model="form.translation_payload.laws.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
            <input type="text" wire:model="form.translation_payload.laws.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>

    <div class="mt-3">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
        <textarea rows="4" wire:model="form.translation_payload.laws.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
    </div>

    <div class="mt-6 space-y-5">
        @foreach (($translationPayload['laws']['cards'] ?? []) as $cardIndex => $card)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Law Card') }} #{{ $cardIndex + 1 }}</p>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
                    <input type="text" wire:model="form.translation_payload.laws.cards.{{ $cardIndex }}.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Summary') }}</label>
                    <textarea rows="4" wire:model="form.translation_payload.laws.cards.{{ $cardIndex }}.summary" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>

                @foreach (($card['lists'] ?? []) as $listIndex => $list)
                    <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-4">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('List Label') }} #{{ $listIndex + 1 }}</label>
                        <input type="text" wire:model="form.translation_payload.laws.cards.{{ $cardIndex }}.lists.{{ $listIndex }}.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />

                        @foreach (($list['items'] ?? []) as $itemIndex => $item)
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('List Item') }} #{{ $itemIndex + 1 }}</label>
                                    <button type="button" wire:click="removeTranslationListItem('laws.cards.{{ $cardIndex }}.lists.{{ $listIndex }}.items', {{ $itemIndex }})" class="text-xs font-semibold text-rose-600 hover:text-rose-700">{{ __('Remove') }}</button>
                                </div>
                                <input type="text" wire:model="form.translation_payload.laws.cards.{{ $cardIndex }}.lists.{{ $listIndex }}.items.{{ $itemIndex }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('laws.cards.{{ $cardIndex }}.lists.{{ $listIndex }}.items')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                {{ __('Add List Item') }}
                            </button>
                        </div>
                    </div>
                @endforeach

                <div class="mt-4">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Note') }}</label>
                    <textarea rows="3" wire:model="form.translation_payload.laws.cards.{{ $cardIndex }}.note" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>

                <div class="mt-5 grid gap-4 xl:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Primary Link') }}</p>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.laws.cards.{{ $cardIndex }}.primary_link.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Type') }}</label>
                                <select wire:model="form.translation_payload.laws.cards.{{ $cardIndex }}.primary_link.type" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                    <option value="none">{{ __('None') }}</option>
                                    <option value="external">{{ __('External / Internal URL') }}</option>
                                    <option value="blog">{{ __('Blog Post') }}</option>
                                    <option value="call">{{ __('Call Post') }}</option>
                                    <option value="pdf">{{ __('PDF Asset') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('URL') }}</label>
                                <input type="text" wire:model="form.translation_payload.laws.cards.{{ $cardIndex }}.primary_link.url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Slug') }}</label>
                                <input type="text" wire:model="form.translation_payload.laws.cards.{{ $cardIndex }}.primary_link.slug" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            @include('livewire.admin.content.service.partials.pdf-asset-upload-field', [
                                'currentPath' => (string) ($card['primary_link']['path'] ?? ''),
                                'uploadModel' => 'assetUploads.laws_cards_'.$cardIndex.'_primary_link_path',
                            ])
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Secondary Link') }}</p>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Label') }}</label>
                                <input type="text" wire:model="form.translation_payload.laws.cards.{{ $cardIndex }}.secondary_link.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Type') }}</label>
                                <select wire:model="form.translation_payload.laws.cards.{{ $cardIndex }}.secondary_link.type" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                    <option value="none">{{ __('None') }}</option>
                                    <option value="external">{{ __('External / Internal URL') }}</option>
                                    <option value="blog">{{ __('Blog Post') }}</option>
                                    <option value="call">{{ __('Call Post') }}</option>
                                    <option value="pdf">{{ __('PDF Asset') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('URL') }}</label>
                                <input type="text" wire:model="form.translation_payload.laws.cards.{{ $cardIndex }}.secondary_link.url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Slug') }}</label>
                                <input type="text" wire:model="form.translation_payload.laws.cards.{{ $cardIndex }}.secondary_link.slug" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            @include('livewire.admin.content.service.partials.pdf-asset-upload-field', [
                                'currentPath' => (string) ($card['secondary_link']['path'] ?? ''),
                                'uploadModel' => 'assetUploads.laws_cards_'.$cardIndex.'_secondary_link_path',
                            ])
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="grid gap-6 xl:grid-cols-2">
    <div id="eu-funds-testimonials" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <p class="admin-section-title">{{ __('Testimonials Section') }}</p>

        <div class="mt-4">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kicker') }}</label>
            <input type="text" wire:model="form.translation_payload.testimonials.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
            <input type="text" wire:model="form.translation_payload.testimonials.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
            <textarea rows="4" wire:model="form.translation_payload.testimonials.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>
    </div>

    <div id="eu-funds-blog" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <p class="admin-section-title">{{ __('Blog Section') }}</p>

        <div class="mt-4">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Title') }}</label>
            <input type="text" wire:model="form.translation_payload.blog_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            <p class="mt-1 text-xs text-slate-500">{{ __('Use :category placeholder if you want the current blog category name inserted automatically.') }}</p>
        </div>

        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Intro') }}</label>
            <textarea rows="4" wire:model="form.translation_payload.blog_section.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>
    </div>
</div>

@include('livewire.admin.content.service.partials.meeting-cta-editor', [
    'sectionId' => 'eu-funds-meeting',
])
