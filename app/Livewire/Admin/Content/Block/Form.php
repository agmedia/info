<?php

namespace App\Livewire\Admin\Content\Block;

use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\ContentBlock;
use App\Models\Settings\Local\Language;
use App\Services\Content\ContentBlockResolver;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    private const TAB_OPTIONS = ['content', 'sources', 'template', 'media'];

    private const ITEM_TYPE_MAP = [
        'categories' => 'category',
        'mobile_hero_banner' => 'category',
        'blogs' => 'blog',
    ];

    public ?int $blockId = null;

    public string $activeTab = 'content';

    public array $form = [];

    /**
     * @var array<string, string>
     */
    public array $types = [];

    /**
     * @var array<string, string>
     */
    public array $placements = [];

    /**
     * @var array<string, string>
     */
    public array $targetTypes = [
        '' => 'Global (no target)',
        'category' => 'Category',
        'blog_post' => 'Blog Post',
        'page' => 'Static Page',
        'custom' => 'Custom',
    ];

    /**
     * @var array<string, string>
     */
    public array $frontendVariants = [
        'all' => 'All Devices',
        'desktop' => 'Desktop Only',
        'mobile' => 'Mobile Only',
    ];

    public ?int $pickerItemId = null;

    public string $lastType = '';

    public function mount(?int $blockId = null): void
    {
        /** @var array<string, string> $types */
        $types = config('content_blocks.types', []);
        /** @var array<string, string> $placements */
        $placements = config('content_blocks.placements', []);

        $this->types = collect($this->orderedTypes($types))
            ->mapWithKeys(static fn ($label, $key) => [$key => __((string) $label)])
            ->all();
        $this->placements = $placements;
        $this->targetTypes = collect($this->targetTypes)
            ->map(static fn ($label) => __((string) $label))
            ->all();
        $this->frontendVariants = collect($this->frontendVariants)
            ->map(static fn ($label) => __((string) $label))
            ->all();

        $this->resetForm();
        $requestedLocale = strtolower(trim((string) request()->query('locale', '')));
        if ($requestedLocale !== '' && Language::query()
            ->where('code', $requestedLocale)
            ->where('is_active', true)
            ->exists()) {
            $this->form['locale'] = $requestedLocale;
        }
        $this->lastType = (string) ($this->form['type'] ?? '');

        if ($blockId) {
            $this->blockId = $blockId;
            $this->loadBlock();
        }
    }

    public function getIsEditProperty(): bool
    {
        return (bool) $this->blockId;
    }

    public function getCurrentItemTypeProperty(): ?string
    {
        return $this->itemTypeForBlockType((string) ($this->form['type'] ?? ''));
    }

    public function getIsItemBlockProperty(): bool
    {
        return $this->currentItemType !== null;
    }

    public function getItemOptionsProperty(): Collection
    {
        return $this->loadItemOptions($this->currentItemType);
    }

    public function getSelectedItemsProperty(): Collection
    {
        $optionsById = $this->itemOptions->keyBy('id');

        return collect((array) ($this->form['selected_item_ids'] ?? []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->map(function (int $id, int $index) use ($optionsById): array {
                $row = $optionsById->get($id);

                return [
                    'id' => $id,
                    'index' => $index,
                    'label' => (string) ($row['label'] ?? ('#'.$id)),
                ];
            });
    }

    public function updatedFormLocale(): void
    {
        $this->loadTranslationForLocale();
    }

    public function updatedFormType(string $type): void
    {
        if (! array_key_exists($type, $this->types)) {
            return;
        }

        if ($type !== 'blog_grid_3' && $this->activeTab === 'sources') {
            $this->activeTab = 'content';
        }

        $suggestedSurface = $this->suggestedFrontendVariantForType($type);
        if ($suggestedSurface !== null) {
            $this->form['slot_frontend_variant'] = $suggestedSurface;
        }

        $suggestedPlacement = $this->suggestedPlacementForType($type);
        if ($suggestedPlacement !== null && array_key_exists($suggestedPlacement, $this->placements)) {
            $this->form['slot_placement'] = $suggestedPlacement;
        }

        $currentItemType = $this->itemTypeForBlockType($type);
        $existingType = $this->itemTypeForBlockType($this->lastType);

        if ($currentItemType !== $existingType) {
            $this->form['selected_item_ids'] = [];
            $this->pickerItemId = null;
        }

        $currentTemplate = (string) ($this->form['template_body'] ?? '');
        $shouldLoadDefault = trim($currentTemplate) === '';

        if (! $shouldLoadDefault && $this->lastType !== '') {
            $previousDefault = $this->defaultTemplateForType($this->lastType);
            $shouldLoadDefault = $this->normalizedTemplate($currentTemplate) === $this->normalizedTemplate($previousDefault);
        }

        if ($shouldLoadDefault) {
            $this->form['template_body'] = $this->defaultTemplateForType($type);
        }

        $this->lastType = $type;
    }

    public function setTab(string $tab): void
    {
        if (! in_array($tab, self::TAB_OPTIONS, true)) {
            return;
        }

        $this->activeTab = $tab;
    }

    public function loadTemplatePreset(): void
    {
        $type = (string) ($this->form['type'] ?? 'banner');
        $this->form['template_body'] = $this->defaultTemplateForType($type);
    }

    public function addSelectedItem(): void
    {
        $id = (int) ($this->pickerItemId ?? 0);
        if ($id <= 0) {
            return;
        }

        $existing = collect((array) ($this->form['selected_item_ids'] ?? []))->map(fn ($v) => (int) $v);
        if ($existing->contains($id)) {
            return;
        }

        $optionExists = $this->itemOptions->contains(fn ($row) => (int) ($row['id'] ?? 0) === $id);
        if (! $optionExists) {
            return;
        }

        $this->form['selected_item_ids'][] = $id;
        $this->form['selected_item_ids'] = array_values(array_unique(array_map(
            static fn ($value): int => (int) $value,
            (array) $this->form['selected_item_ids']
        )));
    }

    public function removeSelectedItem(int $id): void
    {
        $this->form['selected_item_ids'] = array_values(array_filter(
            array_map(static fn ($value): int => (int) $value, (array) ($this->form['selected_item_ids'] ?? [])),
            static fn (int $value): bool => $value !== $id
        ));
    }

    public function moveSelectedItemUp(int $index): void
    {
        $rows = array_values(array_map(static fn ($value): int => (int) $value, (array) ($this->form['selected_item_ids'] ?? [])));
        if ($index <= 0 || $index >= count($rows)) {
            return;
        }

        [$rows[$index - 1], $rows[$index]] = [$rows[$index], $rows[$index - 1]];
        $this->form['selected_item_ids'] = $rows;
    }

    public function moveSelectedItemDown(int $index): void
    {
        $rows = array_values(array_map(static fn ($value): int => (int) $value, (array) ($this->form['selected_item_ids'] ?? [])));
        if ($index < 0 || $index >= count($rows) - 1) {
            return;
        }

        [$rows[$index + 1], $rows[$index]] = [$rows[$index], $rows[$index + 1]];
        $this->form['selected_item_ids'] = $rows;
    }

    public function addHomeStat(): void
    {
        $this->form['home_stats'] = array_values((array) ($this->form['home_stats'] ?? []));
        $this->form['home_stats'][] = ['value' => '', 'suffix' => '', 'label' => ''];
    }

    public function removeHomeStat(int $index): void
    {
        $rows = array_values((array) ($this->form['home_stats'] ?? []));
        unset($rows[$index]);
        $this->form['home_stats'] = array_values($rows);
    }

    public function addContactStat(): void
    {
        $this->form['contact_stats'] = array_values((array) ($this->form['contact_stats'] ?? []));
        $this->form['contact_stats'][] = ['value' => '', 'suffix' => '', 'label' => ''];
    }

    public function removeContactStat(int $index): void
    {
        $rows = array_values((array) ($this->form['contact_stats'] ?? []));
        unset($rows[$index]);
        $this->form['contact_stats'] = array_values($rows);
    }

    public function addHomeLocation(): void
    {
        $locations = is_array($this->form['home_locations'] ?? null)
            ? $this->form['home_locations']
            : $this->defaultHomeLocations();
        $locations['items'] = array_values((array) ($locations['items'] ?? []));
        $locations['items'][] = $this->emptyHomeLocation();
        $this->form['home_locations'] = $locations;
    }

    public function removeHomeLocation(int $index): void
    {
        $locations = is_array($this->form['home_locations'] ?? null)
            ? $this->form['home_locations']
            : $this->defaultHomeLocations();
        $rows = array_values((array) ($locations['items'] ?? []));
        unset($rows[$index]);
        $locations['items'] = array_values($rows);
        $this->form['home_locations'] = $locations;
    }

    public function addHomeValue(): void
    {
        $values = is_array($this->form['home_values'] ?? null)
            ? $this->form['home_values']
            : $this->defaultHomeValues();
        $values['items'] = array_values((array) ($values['items'] ?? []));
        $values['items'][] = ['title' => '', 'text' => ''];
        $this->form['home_values'] = $values;
    }

    public function removeHomeValue(int $index): void
    {
        $values = is_array($this->form['home_values'] ?? null)
            ? $this->form['home_values']
            : $this->defaultHomeValues();
        $rows = array_values((array) ($values['items'] ?? []));
        unset($rows[$index]);
        $values['items'] = array_values($rows);
        $this->form['home_values'] = $values;
    }

    public function addHomeProcessStep(): void
    {
        $process = is_array($this->form['home_process'] ?? null)
            ? $this->form['home_process']
            : $this->defaultHomeProcess();
        $process['items'] = array_values((array) ($process['items'] ?? []));
        $process['items'][] = ['title' => '', 'text' => ''];
        $this->form['home_process'] = $process;
    }

    public function removeHomeProcessStep(int $index): void
    {
        $process = is_array($this->form['home_process'] ?? null)
            ? $this->form['home_process']
            : $this->defaultHomeProcess();
        $rows = array_values((array) ($process['items'] ?? []));
        unset($rows[$index]);
        $process['items'] = array_values($rows);
        $this->form['home_process'] = $process;
    }

    public function addHomeService(): void
    {
        $this->form['home_services'] = array_values((array) ($this->form['home_services'] ?? []));
        $this->form['home_services'][] = [
            'key' => '',
            'title' => '',
            'subtitle' => '',
            'text' => '',
            'bullets_text' => '',
            'image_alt' => '',
            'url' => '',
            'action_label' => '',
        ];
    }

    public function removeHomeService(int $index): void
    {
        $rows = array_values((array) ($this->form['home_services'] ?? []));
        unset($rows[$index]);
        $this->form['home_services'] = array_values($rows);
    }

    public function save()
    {
        $validated = $this->validate($this->rules());
        $userId = auth()->id();
        $isEdit = $this->blockId !== null;
        $existingBlockPayload = null;
        $translationPayload = [];
        if ($this->blockId) {
            $existingBlock = ContentBlock::query()
                ->with(['translations' => fn ($q) => $q->where('locale', (string) $validated['form']['locale'])])
                ->find($this->blockId);
            $existingBlockPayload = is_array($existingBlock?->payload ?? null) ? $existingBlock->payload : null;
            $translationPayload = is_array($existingBlock?->translations->first()?->payload ?? null)
                ? $existingBlock->translations->first()->payload
                : [];
        }

        $bgCss = trim((string) ($validated['form']['bg_css'] ?? ''));
        if ($bgCss !== '') {
            $translationPayload['bg_css'] = $bgCss;
        } else {
            unset($translationPayload['bg_css']);
        }

        $customClasses = trim((string) ($validated['form']['custom_classes'] ?? ''));
        if ($customClasses !== '') {
            $translationPayload['custom_classes'] = $customClasses;
        } else {
            unset($translationPayload['custom_classes']);
        }

        $blockType = (string) ($validated['form']['type'] ?? '');
        $homePayloadKeys = match ($blockType) {
            'home_hero' => ['kicker', 'page_title', 'secondary_cta_label', 'secondary_cta_url'],
            'home_stats' => ['stats', 'contact_stats', 'locations', 'contact_page'],
            'home_services' => ['title_accent', 'services', 'values', 'process', 'news', 'contact_cta'],
            default => [],
        };
        foreach ($homePayloadKeys as $homePayloadKey) {
            unset($translationPayload[$homePayloadKey]);
        }

        if ($blockType === 'home_hero') {
            $kicker = trim((string) ($validated['form']['home_kicker'] ?? ''));
            $pageTitle = trim((string) ($validated['form']['home_page_title'] ?? ''));
            $secondaryCtaLabel = trim((string) ($validated['form']['secondary_cta_label'] ?? ''));
            $secondaryCtaUrl = trim((string) ($validated['form']['secondary_cta_url'] ?? ''));

            if ($kicker !== '') {
                $translationPayload['kicker'] = $kicker;
            }
            if ($pageTitle !== '') {
                $translationPayload['page_title'] = $pageTitle;
            }
            if ($secondaryCtaLabel !== '') {
                $translationPayload['secondary_cta_label'] = $secondaryCtaLabel;
            }
            if ($secondaryCtaUrl !== '') {
                $translationPayload['secondary_cta_url'] = $secondaryCtaUrl;
            }
        }

        if ($blockType === 'home_stats') {
            $stats = $this->normalizeHomeStats($validated['form']['home_stats'] ?? []);
            $contactStats = $this->normalizeHomeStats($validated['form']['contact_stats'] ?? []);
            $locations = $this->normalizeHomeLocations($validated['form']['home_locations'] ?? []);
            $contactPage = $this->normalizeHomeContactPage($validated['form']['home_contact_page'] ?? []);

            if ($stats !== []) {
                $translationPayload['stats'] = $stats;
            }
            if ($contactStats !== []) {
                $translationPayload['contact_stats'] = $contactStats;
            }
            if ($this->payloadHasContent($locations)) {
                $translationPayload['locations'] = $locations;
            }
            if ($this->payloadHasContent($contactPage)) {
                $translationPayload['contact_page'] = $contactPage;
            }
        }

        if ($blockType === 'home_services') {
            $titleAccent = trim((string) ($validated['form']['title_accent'] ?? ''));
            $services = $this->homeServicesPayloadRows($validated['form']['home_services'] ?? []);
            $values = $this->normalizeHomeValues($validated['form']['home_values'] ?? []);
            $process = $this->normalizeHomeProcess($validated['form']['home_process'] ?? []);
            $news = $this->normalizeHomeNews($validated['form']['home_news'] ?? []);
            $contactCta = $this->normalizeHomeContactCta($validated['form']['home_contact_cta'] ?? []);

            if ($titleAccent !== '') {
                $translationPayload['title_accent'] = $titleAccent;
            }
            if ($services !== []) {
                $translationPayload['services'] = $services;
            }
            if ($this->payloadHasContent($values)) {
                $translationPayload['values'] = $values;
            }
            if ($this->payloadHasContent($process)) {
                $translationPayload['process'] = $process;
            }
            if ($this->payloadHasContent($news)) {
                $translationPayload['news'] = $news;
            }
            if ($this->payloadHasContent($contactCta)) {
                $translationPayload['contact_cta'] = $contactCta;
            }
        }

        $itemsLimit = (int) ($validated['form']['items_limit'] ?? 0);
        if ($itemsLimit > 0) {
            $translationPayload['items_limit'] = $itemsLimit;
        } else {
            unset($translationPayload['items_limit']);
        }

        $reviewsFeaturedOnly = (bool) ($validated['form']['reviews_featured_only'] ?? false);
        if ($reviewsFeaturedOnly) {
            $translationPayload['reviews_featured_only'] = true;
        } else {
            unset($translationPayload['reviews_featured_only']);
        }
        $blogSource = (string) ($validated['form']['blog_source'] ?? 'latest');
        $translationPayload['blog_source'] = in_array($blogSource, ['latest', 'featured'], true) ? $blogSource : 'latest';
        unset($translationPayload['render_mode'], $translationPayload['body_html_container_class']);

        $blockPayload = is_array($existingBlockPayload) ? $existingBlockPayload : [];
        if ((string) ($validated['form']['type'] ?? '') === 'blog_grid_3') {
            $blogCategoryId = (int) ($validated['form']['blog_category_id'] ?? 0);
            $blogSort = (string) ($validated['form']['blog_sort'] ?? 'newest');

            $blockPayload['source'] = 'query';
            $blockPayload['category_ids'] = $blogCategoryId > 0 ? [$blogCategoryId] : [];
            $blockPayload['manual_blog_ids'] = [];
            $blockPayload['sort'] = in_array($blogSort, ['newest', 'featured', 'title'], true)
                ? $blogSort
                : 'newest';
        }

        $itemType = $this->itemTypeForBlockType((string) $validated['form']['type']);
        $selectedIds = collect((array) ($validated['form']['selected_item_ids'] ?? []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->all();

        if (trim((string) ($validated['form']['slot_target_ref'] ?? '')) !== '' && trim((string) ($validated['form']['slot_target_type'] ?? '')) === '') {
            $this->addError('form.slot_target_type', __('Target type is required when target ref is set.'));
            $this->dispatch('notify', type: 'warning', message: __('Choose target type when target ref is set.'));

            return null;
        }

        if ($itemType !== null && $selectedIds === []) {
            $this->addError('form.selected_item_ids', __('Select at least one item for this block type.'));
            $this->dispatch('notify', type: 'warning', message: __('Select at least one item.'));

            return null;
        }

        if ($itemType !== null && $selectedIds !== []) {
            $validIds = $this->validIdsForItemType($itemType, $selectedIds);
            if (count($validIds) !== count($selectedIds)) {
                $this->addError('form.selected_item_ids', __('One or more selected items are invalid.'));
                $this->dispatch('notify', type: 'warning', message: __('Invalid item selection detected.'));

                return null;
            }
        }

        $oldCode = null;
        if ($this->blockId) {
            $oldCode = (string) ContentBlock::query()->whereKey($this->blockId)->value('code');
        }

        DB::transaction(function () use (
            $validated,
            $blockPayload,
            $translationPayload,
            $itemType,
            $selectedIds,
            $userId,
            $oldCode,
            $isEdit
        ): void {
            $blockData = [
                'code' => trim((string) $validated['form']['code']),
                'name' => trim((string) $validated['form']['name']),
                'type' => (string) $validated['form']['type'],
                'is_active' => (bool) $validated['form']['is_active'],
                'payload' => $blockPayload === [] ? null : $blockPayload,
                'updated_by' => $userId,
            ];

            if ($this->blockId) {
                $block = ContentBlock::query()->findOrFail($this->blockId);
                $block->update($blockData);
            } else {
                $block = ContentBlock::query()->create($blockData + ['created_by' => $userId]);
                $this->blockId = $block->id;
            }

            $block->translations()->updateOrCreate(
                ['locale' => (string) $validated['form']['locale']],
                [
                    'title' => $validated['form']['title'] ?: null,
                    'subtitle' => $validated['form']['subtitle'] ?: null,
                    'body_html' => null,
                    'cta_label' => $validated['form']['cta_label'] ?: null,
                    'cta_url' => $validated['form']['cta_url'] ?: null,
                    'payload' => $translationPayload === [] ? null : $translationPayload,
                ]
            );

            $this->savePrimarySlot($block, $validated, $userId);
            $this->syncBlockItems($block, $itemType, $selectedIds);

            $template = trim((string) ($validated['form']['template_body'] ?? ''));
            if ($template === '') {
                $template = $this->defaultTemplateForType((string) $block->type);
            }
            $this->writeTemplateFile((string) $block->code, $template);

            if ($oldCode !== null && $oldCode !== '' && $oldCode !== $block->code) {
                $this->deleteTemplateFile($oldCode);
            }

            activity('content_blocks')
                ->performedOn($block)
                ->causedBy(auth()->user())
                ->event($isEdit ? 'updated' : 'created')
                ->withProperties([
                    'code' => $block->code,
                    'type' => $block->type,
                    'placement' => $validated['form']['slot_placement'],
                    'frontend_variant' => $validated['form']['slot_frontend_variant'],
                    'item_type' => $itemType,
                    'item_count' => count($selectedIds),
                    'template_file' => $this->templateViewName((string) $block->code),
                ])
                ->log('Content block saved (v2 builder)');
        });

        ContentBlockResolver::bumpCacheVersion();

        return redirect()->route('admin.content.blocks')->with('notify', [
            'type' => 'success',
            'message' => $isEdit ? __('Content block updated.') : __('Content block created.'),
        ]);
    }

    public function backToList()
    {
        return redirect()->route('admin.content.blocks');
    }

    public function render()
    {
        return view('livewire.admin.content.block.form');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'form.code' => ['required', 'string', 'max:120', 'alpha_dash', Rule::unique('content_blocks', 'code')->ignore($this->blockId)],
            'form.name' => ['required', 'string', 'max:180'],
            'form.type' => ['required', Rule::in(array_keys($this->types))],
            'form.is_active' => ['boolean'],

            'form.locale' => ['required', 'string', 'max:12'],
            'form.title' => ['nullable', 'string', 'max:255'],
            'form.subtitle' => ['nullable', 'string'],
            'form.cta_label' => ['nullable', 'string', 'max:120'],
            'form.cta_url' => ['nullable', 'string', 'max:2048'],
            'form.home_kicker' => ['nullable', 'string', 'max:180'],
            'form.home_page_title' => ['nullable', 'string', 'max:255'],
            'form.secondary_cta_label' => ['nullable', 'string', 'max:120'],
            'form.secondary_cta_url' => ['nullable', 'string', 'max:2048'],
            'form.title_accent' => ['nullable', 'string', 'max:255'],
            'form.home_stats' => ['nullable', 'array', 'max:6'],
            'form.home_stats.*.value' => ['nullable', 'string', 'max:40'],
            'form.home_stats.*.suffix' => ['nullable', 'string', 'max:10'],
            'form.home_stats.*.label' => ['nullable', 'string', 'max:160'],
            'form.contact_stats' => ['nullable', 'array', 'max:6'],
            'form.contact_stats.*.value' => ['nullable', 'string', 'max:40'],
            'form.contact_stats.*.suffix' => ['nullable', 'string', 'max:10'],
            'form.contact_stats.*.label' => ['nullable', 'string', 'max:160'],
            'form.home_locations' => ['nullable', 'array'],
            'form.home_locations.title' => ['nullable', 'string', 'max:255'],
            'form.home_locations.intro_lead' => ['nullable', 'string', 'max:255'],
            'form.home_locations.intro_text' => ['nullable', 'string', 'max:1000'],
            'form.home_locations.hero_aria_label' => ['nullable', 'string', 'max:255'],
            'form.home_locations.map_aria_label' => ['nullable', 'string', 'max:255'],
            'form.home_locations.map_image_alt' => ['nullable', 'string', 'max:500'],
            'form.home_locations.map_link_label' => ['nullable', 'string', 'max:120'],
            'form.home_locations.email_label' => ['nullable', 'string', 'max:120'],
            'form.home_locations.phone_label' => ['nullable', 'string', 'max:120'],
            'form.home_locations.stats_aria_label' => ['nullable', 'string', 'max:255'],
            'form.home_locations.region_label' => ['nullable', 'string', 'max:160'],
            'form.home_locations.items' => ['nullable', 'array', 'max:6'],
            'form.home_locations.items.*.entity_key' => ['nullable', 'string', 'max:120'],
            'form.home_locations.items.*.city' => ['nullable', 'string', 'max:160'],
            'form.home_locations.items.*.short_city' => ['nullable', 'string', 'max:120'],
            'form.home_locations.items.*.office_label' => ['nullable', 'string', 'max:160'],
            'form.home_locations.items.*.company' => ['nullable', 'string', 'max:191'],
            'form.home_locations.items.*.address' => ['nullable', 'string', 'max:500'],
            'form.home_locations.items.*.map_query' => ['nullable', 'string', 'max:500'],
            'form.home_locations.items.*.email' => ['nullable', 'email', 'max:191'],
            'form.home_locations.items.*.phone' => ['nullable', 'string', 'max:120'],
            'form.home_locations.items.*.number' => ['nullable', 'string', 'max:40'],
            'form.home_locations.items.*.coordinates_label' => ['nullable', 'string', 'max:160'],
            'form.home_locations.items.*.marker_aria_label' => ['nullable', 'string', 'max:255'],
            'form.home_contact_page' => ['nullable', 'array'],
            'form.home_contact_page.page_title' => ['nullable', 'string', 'max:255'],
            'form.home_contact_page.intro' => ['nullable', 'string', 'max:1000'],
            'form.home_contact_page.form_title' => ['nullable', 'string', 'max:255'],
            'form.home_contact_page.form_intro' => ['nullable', 'string', 'max:1000'],
            'form.home_contact_page.name_label' => ['nullable', 'string', 'max:160'],
            'form.home_contact_page.email_label' => ['nullable', 'string', 'max:160'],
            'form.home_contact_page.phone_label' => ['nullable', 'string', 'max:160'],
            'form.home_contact_page.subject_label' => ['nullable', 'string', 'max:160'],
            'form.home_contact_page.message_label' => ['nullable', 'string', 'max:160'],
            'form.home_contact_page.consent_label' => ['nullable', 'string', 'max:1000'],
            'form.home_contact_page.submit_label' => ['nullable', 'string', 'max:120'],
            'form.home_contact_page.direct_title' => ['nullable', 'string', 'max:255'],
            'form.home_contact_page.direct_body' => ['nullable', 'string', 'max:1000'],
            'form.home_contact_page.direct_email' => ['nullable', 'email', 'max:191'],
            'form.home_contact_page.direct_phone' => ['nullable', 'string', 'max:120'],
            'form.home_contact_page.direct_email_label' => ['nullable', 'string', 'max:120'],
            'form.home_contact_page.direct_phone_label' => ['nullable', 'string', 'max:120'],
            'form.home_contact_page.direct_response_time_label' => ['nullable', 'string', 'max:160'],
            'form.home_contact_page.direct_response_fallback' => ['nullable', 'string', 'max:255'],
            'form.home_contact_page.help_title' => ['nullable', 'string', 'max:255'],
            'form.home_contact_page.help_body' => ['nullable', 'string', 'max:1000'],
            'form.home_contact_page.sent_status' => ['nullable', 'string', 'max:500'],
            'form.home_services' => ['nullable', 'array', 'max:12'],
            'form.home_services.*.key' => ['nullable', 'string', Rule::in(['audit', 'accounting', 'advisory'])],
            'form.home_services.*.title' => ['nullable', 'string', 'max:160'],
            'form.home_services.*.subtitle' => ['nullable', 'string', 'max:220'],
            'form.home_services.*.text' => ['nullable', 'string', 'max:700'],
            'form.home_services.*.bullets_text' => ['nullable', 'string', 'max:4000'],
            'form.home_services.*.image_alt' => ['nullable', 'string', 'max:500'],
            'form.home_services.*.url' => ['nullable', 'string', 'max:2048'],
            'form.home_services.*.action_label' => ['nullable', 'string', 'max:80'],
            'form.home_values' => ['nullable', 'array'],
            'form.home_values.title' => ['nullable', 'string', 'max:255'],
            'form.home_values.intro' => ['nullable', 'string', 'max:1600'],
            'form.home_values.items' => ['nullable', 'array', 'max:8'],
            'form.home_values.items.*.title' => ['nullable', 'string', 'max:160'],
            'form.home_values.items.*.text' => ['nullable', 'string', 'max:1000'],
            'form.home_process' => ['nullable', 'array'],
            'form.home_process.title' => ['nullable', 'string', 'max:255'],
            'form.home_process.items' => ['nullable', 'array', 'max:8'],
            'form.home_process.items.*.title' => ['nullable', 'string', 'max:160'],
            'form.home_process.items.*.text' => ['nullable', 'string', 'max:1000'],
            'form.home_news' => ['nullable', 'array'],
            'form.home_news.title' => ['nullable', 'string', 'max:255'],
            'form.home_news.all_posts_label' => ['nullable', 'string', 'max:120'],
            'form.home_news.all_posts_url' => ['nullable', 'string', 'max:2048'],
            'form.home_news.post_action_label' => ['nullable', 'string', 'max:120'],
            'form.home_news.category_fallback' => ['nullable', 'string', 'max:160'],
            'form.home_news.excerpt_fallback' => ['nullable', 'string', 'max:1000'],
            'form.home_contact_cta' => ['nullable', 'array'],
            'form.home_contact_cta.title' => ['nullable', 'string', 'max:255'],
            'form.home_contact_cta.card_title' => ['nullable', 'string', 'max:255'],
            'form.home_contact_cta.text' => ['nullable', 'string', 'max:1200'],
            'form.home_contact_cta.button_label' => ['nullable', 'string', 'max:120'],
            'form.home_contact_cta.button_url' => ['nullable', 'string', 'max:2048'],
            'form.home_contact_cta.status' => ['nullable', 'string', 'max:255'],
            'form.bg_css' => ['nullable', 'string', 'max:6000'],
            'form.custom_classes' => ['nullable', 'string', 'max:1000'],
            'form.items_limit' => ['nullable', 'integer', 'min:1', 'max:50'],
            'form.reviews_featured_only' => ['boolean'],
            'form.blog_source' => ['nullable', Rule::in(['latest', 'featured'])],
            'form.blog_category_id' => [
                Rule::requiredIf(fn (): bool => (string) ($this->form['type'] ?? '') === 'blog_grid_3'),
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where(fn ($query) => $query->where('scope', Category::SCOPE_BLOG)),
            ],
            'form.blog_sort' => ['nullable', Rule::in(['newest', 'featured', 'title'])],
            'form.template_body' => ['nullable', 'string'],

            'form.slot_placement' => ['required', 'string', 'max:120'],
            'form.slot_frontend_variant' => ['required', Rule::in(array_keys($this->frontendVariants))],
            'form.slot_target_type' => ['nullable', 'string', 'max:80'],
            'form.slot_target_ref' => ['nullable', 'string', 'max:191'],
            'form.slot_sort_order' => ['nullable', 'integer', 'min:0'],
            'form.slot_is_active' => ['boolean'],
            'form.slot_starts_at' => ['nullable', 'date'],
            'form.slot_ends_at' => ['nullable', 'date', 'after_or_equal:form.slot_starts_at'],

            'form.selected_item_ids' => ['nullable', 'array'],
            'form.selected_item_ids.*' => ['integer', 'min:1'],
        ];
    }

    private function resetForm(): void
    {
        $defaultType = array_key_exists('banner', $this->types)
            ? 'banner'
            : (array_key_first($this->types) ?: 'custom');

        $this->form = [
            'code' => '',
            'name' => '',
            'type' => $defaultType,
            'is_active' => true,

            'locale' => config('app.locale'),
            'title' => '',
            'subtitle' => '',
            'cta_label' => '',
            'cta_url' => '',
            'home_kicker' => '',
            'home_page_title' => '',
            'secondary_cta_label' => '',
            'secondary_cta_url' => '',
            'title_accent' => '',
            'home_stats' => $this->defaultHomeStats(),
            'contact_stats' => $this->defaultHomeStats(),
            'home_locations' => $this->defaultHomeLocations(),
            'home_contact_page' => $this->defaultHomeContactPage(),
            'home_services' => $this->defaultHomeServices(),
            'home_values' => $this->defaultHomeValues(),
            'home_process' => $this->defaultHomeProcess(),
            'home_news' => $this->defaultHomeNews(),
            'home_contact_cta' => $this->defaultHomeContactCta(),
            'bg_css' => '',
            'custom_classes' => '',
            'items_limit' => 6,
            'reviews_featured_only' => false,
            'blog_source' => 'latest',
            'blog_category_id' => null,
            'blog_sort' => 'newest',
            'template_body' => $this->defaultTemplateForType($defaultType),

            'slot_placement' => array_key_first($this->placements) ?: 'home.hero',
            'slot_frontend_variant' => 'all',
            'slot_target_type' => '',
            'slot_target_ref' => '',
            'slot_sort_order' => 0,
            'slot_is_active' => true,
            'slot_starts_at' => '',
            'slot_ends_at' => '',

            'selected_item_ids' => [],
        ];

        $this->pickerItemId = null;
    }

    private function loadBlock(): void
    {
        if (! $this->blockId) {
            return;
        }

        $block = ContentBlock::query()
            ->with([
                'translations',
                'slots' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
                'items' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
            ])
            ->findOrFail($this->blockId);

        $translation = $this->resolveInitialTranslation(
            $block->translations,
            (string) ($this->form['locale'] ?? config('app.locale'))
        );

        $slot = $block->slots->first();
        $translationPayload = is_array($translation?->payload ?? null) ? $translation->payload : [];
        $blockPayload = is_array($block->payload ?? null) ? $block->payload : [];
        $legacyCategoryIds = collect($blockPayload['category_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->values();

        $this->form['code'] = $block->code;
        $this->form['name'] = $block->name;
        $this->form['type'] = $block->type;
        $this->form['is_active'] = (bool) $block->is_active;

        if ($translation) {
            $this->form['locale'] = $translation->locale;
            $this->form['title'] = $translation->title ?? '';
            $this->form['subtitle'] = $translation->subtitle ?? '';
            $this->form['cta_label'] = $translation->cta_label ?? '';
            $this->form['cta_url'] = $translation->cta_url ?? '';
        }

        $this->form['bg_css'] = (string) ($translationPayload['bg_css'] ?? '');
        $this->form['custom_classes'] = (string) ($translationPayload['custom_classes'] ?? '');
        $this->form['home_kicker'] = (string) ($translationPayload['kicker'] ?? '');
        $this->form['home_page_title'] = (string) ($translationPayload['page_title'] ?? '');
        $this->form['secondary_cta_label'] = (string) ($translationPayload['secondary_cta_label'] ?? '');
        $this->form['secondary_cta_url'] = (string) ($translationPayload['secondary_cta_url'] ?? '');
        $this->form['title_accent'] = (string) ($translationPayload['title_accent'] ?? '');
        $this->form['home_stats'] = $this->normalizeHomeStats($translationPayload['stats'] ?? [], true);
        $this->form['contact_stats'] = $this->normalizeHomeStats($translationPayload['contact_stats'] ?? [], true);
        $this->form['home_locations'] = $this->normalizeHomeLocations($translationPayload['locations'] ?? [], true);
        $this->form['home_contact_page'] = $this->normalizeHomeContactPage($translationPayload['contact_page'] ?? [], true);
        $this->form['home_services'] = $this->normalizeHomeServices($translationPayload['services'] ?? [], true);
        $this->form['home_values'] = $this->normalizeHomeValues($translationPayload['values'] ?? [], true);
        $this->form['home_process'] = $this->normalizeHomeProcess($translationPayload['process'] ?? [], true);
        $this->form['home_news'] = $this->normalizeHomeNews($translationPayload['news'] ?? [], true);
        $this->form['home_contact_cta'] = $this->normalizeHomeContactCta($translationPayload['contact_cta'] ?? [], true);
        $this->form['items_limit'] = (int) ($translationPayload['items_limit'] ?? 6);
        $this->form['reviews_featured_only'] = (bool) ($translationPayload['reviews_featured_only'] ?? false);
        $blogSource = (string) ($translationPayload['blog_source'] ?? 'latest');
        $this->form['blog_source'] = in_array($blogSource, ['latest', 'featured'], true)
            ? $blogSource
            : 'latest';
        $this->form['blog_category_id'] = (int) ($blockPayload['blog_category_id'] ?? 0) > 0
            ? (int) $blockPayload['blog_category_id']
            : ($legacyCategoryIds->first() ?: null);
        $blogSort = (string) ($blockPayload['sort'] ?? 'newest');
        $this->form['blog_sort'] = in_array($blogSort, ['newest', 'featured', 'title'], true)
            ? $blogSort
            : 'newest';

        $this->form['slot_placement'] = (string) ($slot?->placement ?? (array_key_first($this->placements) ?: 'home.hero'));
        $loadedVariant = (string) ($slot?->frontend_variant ?? 'all');
        $this->form['slot_frontend_variant'] = in_array($loadedVariant, ['all', 'desktop', 'mobile'], true) ? $loadedVariant : 'all';
        $this->form['slot_target_type'] = (string) ($slot?->target_type ?? '');
        $this->form['slot_target_ref'] = (string) ($slot?->target_ref ?? '');
        $this->form['slot_sort_order'] = (int) ($slot?->sort_order ?? 0);
        $this->form['slot_is_active'] = (bool) ($slot?->is_active ?? true);
        $this->form['slot_starts_at'] = $slot?->starts_at?->format('Y-m-d\\TH:i') ?? '';
        $this->form['slot_ends_at'] = $slot?->ends_at?->format('Y-m-d\\TH:i') ?? '';

        $expectedItemType = $this->itemTypeForBlockType($block->type);
        $selected = $block->items;
        if ($expectedItemType !== null) {
            $selected = $selected->where('item_type', $expectedItemType);
        }

        $this->form['selected_item_ids'] = $selected
            ->pluck('item_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $this->lastType = (string) $block->type;

        $existingTemplate = $this->readTemplateFile($block->code);
        $this->form['template_body'] = $existingTemplate !== ''
            ? $existingTemplate
            : $this->defaultTemplateForType($block->type);
    }

    private function loadTranslationForLocale(): void
    {
        if (! $this->blockId) {
            $this->clearTranslationFields();

            return;
        }

        $block = ContentBlock::query()
            ->with('translations')
            ->find($this->blockId);

        if (! $block) {
            return;
        }

        $translation = $block->translations->firstWhere('locale', $this->form['locale']);

        if (! $translation) {
            $this->clearTranslationFields();

            return;
        }

        $translationPayload = is_array($translation->payload ?? null) ? $translation->payload : [];

        $this->form['title'] = $translation->title ?? '';
        $this->form['subtitle'] = $translation->subtitle ?? '';
        $this->form['cta_label'] = $translation->cta_label ?? '';
        $this->form['cta_url'] = $translation->cta_url ?? '';
        $this->form['bg_css'] = (string) ($translationPayload['bg_css'] ?? '');
        $this->form['custom_classes'] = (string) ($translationPayload['custom_classes'] ?? '');
        $this->form['home_kicker'] = (string) ($translationPayload['kicker'] ?? '');
        $this->form['home_page_title'] = (string) ($translationPayload['page_title'] ?? '');
        $this->form['secondary_cta_label'] = (string) ($translationPayload['secondary_cta_label'] ?? '');
        $this->form['secondary_cta_url'] = (string) ($translationPayload['secondary_cta_url'] ?? '');
        $this->form['title_accent'] = (string) ($translationPayload['title_accent'] ?? '');
        $this->form['home_stats'] = $this->normalizeHomeStats($translationPayload['stats'] ?? [], true);
        $this->form['contact_stats'] = $this->normalizeHomeStats($translationPayload['contact_stats'] ?? [], true);
        $this->form['home_locations'] = $this->normalizeHomeLocations($translationPayload['locations'] ?? [], true);
        $this->form['home_contact_page'] = $this->normalizeHomeContactPage($translationPayload['contact_page'] ?? [], true);
        $this->form['home_services'] = $this->normalizeHomeServices($translationPayload['services'] ?? [], true);
        $this->form['home_values'] = $this->normalizeHomeValues($translationPayload['values'] ?? [], true);
        $this->form['home_process'] = $this->normalizeHomeProcess($translationPayload['process'] ?? [], true);
        $this->form['home_news'] = $this->normalizeHomeNews($translationPayload['news'] ?? [], true);
        $this->form['home_contact_cta'] = $this->normalizeHomeContactCta($translationPayload['contact_cta'] ?? [], true);
        $this->form['items_limit'] = (int) ($translationPayload['items_limit'] ?? 6);
        $this->form['reviews_featured_only'] = (bool) ($translationPayload['reviews_featured_only'] ?? false);
        $blogSource = (string) ($translationPayload['blog_source'] ?? 'latest');
        $this->form['blog_source'] = in_array($blogSource, ['latest', 'featured'], true)
            ? $blogSource
            : 'latest';
    }

    private function resolveInitialTranslation(Collection $translations, string $preferredLocale): mixed
    {
        $fallbackLocale = (string) config('app.locale');

        $preferredWithContent = $translations->first(
            fn ($row): bool => (string) ($row->locale ?? '') === $preferredLocale && $this->translationHasContent($row)
        );
        if ($preferredWithContent) {
            return $preferredWithContent;
        }

        $fallbackWithContent = $translations->first(
            fn ($row): bool => (string) ($row->locale ?? '') === $fallbackLocale && $this->translationHasContent($row)
        );
        if ($fallbackWithContent) {
            return $fallbackWithContent;
        }

        $anyWithContent = $translations->first(fn ($row): bool => $this->translationHasContent($row));
        if ($anyWithContent) {
            return $anyWithContent;
        }

        return $translations->firstWhere('locale', $preferredLocale)
            ?? $translations->firstWhere('locale', $fallbackLocale)
            ?? $translations->first();
    }

    private function translationHasContent(mixed $translation): bool
    {
        if (! $translation) {
            return false;
        }

        $payload = is_array($translation->payload ?? null) ? $translation->payload : [];

        return trim((string) ($translation->title ?? '')) !== ''
            || trim((string) ($translation->subtitle ?? '')) !== ''
            || trim((string) ($translation->cta_label ?? '')) !== ''
            || trim((string) ($translation->cta_url ?? '')) !== ''
            || trim((string) ($translation->body_html ?? '')) !== ''
            || trim((string) ($payload['bg_css'] ?? '')) !== ''
            || trim((string) ($payload['custom_classes'] ?? '')) !== ''
            || trim((string) ($payload['kicker'] ?? '')) !== ''
            || trim((string) ($payload['page_title'] ?? '')) !== ''
            || trim((string) ($payload['secondary_cta_label'] ?? '')) !== ''
            || trim((string) ($payload['secondary_cta_url'] ?? '')) !== ''
            || trim((string) ($payload['title_accent'] ?? '')) !== ''
            || ! empty($payload['stats'] ?? [])
            || ! empty($payload['contact_stats'] ?? [])
            || $this->payloadHasContent((array) ($payload['locations'] ?? []))
            || $this->payloadHasContent((array) ($payload['contact_page'] ?? []))
            || ! empty($payload['services'] ?? [])
            || $this->payloadHasContent((array) ($payload['values'] ?? []))
            || $this->payloadHasContent((array) ($payload['process'] ?? []))
            || $this->payloadHasContent((array) ($payload['news'] ?? []))
            || $this->payloadHasContent((array) ($payload['contact_cta'] ?? []))
            || (int) ($payload['items_limit'] ?? 0) > 0
            || (bool) ($payload['reviews_featured_only'] ?? false);
    }

    private function clearTranslationFields(): void
    {
        $this->form['title'] = '';
        $this->form['subtitle'] = '';
        $this->form['cta_label'] = '';
        $this->form['cta_url'] = '';
        $this->form['bg_css'] = '';
        $this->form['custom_classes'] = '';
        $this->form['home_kicker'] = '';
        $this->form['home_page_title'] = '';
        $this->form['secondary_cta_label'] = '';
        $this->form['secondary_cta_url'] = '';
        $this->form['title_accent'] = '';
        $this->form['home_stats'] = $this->defaultHomeStats();
        $this->form['contact_stats'] = $this->defaultHomeStats();
        $this->form['home_locations'] = $this->defaultHomeLocations();
        $this->form['home_contact_page'] = $this->defaultHomeContactPage();
        $this->form['home_services'] = $this->defaultHomeServices();
        $this->form['home_values'] = $this->defaultHomeValues();
        $this->form['home_process'] = $this->defaultHomeProcess();
        $this->form['home_news'] = $this->defaultHomeNews();
        $this->form['home_contact_cta'] = $this->defaultHomeContactCta();
        $this->form['items_limit'] = 6;
        $this->form['reviews_featured_only'] = false;
        $this->form['blog_source'] = 'latest';
    }

    public function getBlogCategoryOptionsProperty(): Collection
    {
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale', 'en'));
        $locales = array_values(array_unique([(string) $this->form['locale'], $fallbackLocale]));

        return Category::query()
            ->where('scope', Category::SCOPE_BLOG)
            ->withDepth()
            ->defaultOrder()
            ->with([
                'translations' => fn ($q) => $q
                    ->where('scope', Category::SCOPE_BLOG)
                    ->whereIn('locale', $locales),
            ])
            ->get()
            ->map(function (Category $category) use ($fallbackLocale): array {
                $translation = $category->translations->firstWhere('locale', (string) $this->form['locale'])
                    ?? $category->translations->firstWhere('locale', $fallbackLocale)
                    ?? $category->translations->first();
                $depth = max(0, (int) ($category->depth ?? 0) - 1);

                return [
                    'id' => (int) $category->id,
                    'label' => str_repeat('— ', $depth).(string) ($translation?->name ?? $category->code),
                ];
            });
    }

    private function itemTypeForBlockType(string $type): ?string
    {
        return self::ITEM_TYPE_MAP[$type] ?? null;
    }

    private function loadItemOptions(?string $itemType): Collection
    {
        $locale = (string) ($this->form['locale'] ?? config('app.locale'));
        $fallbackLocale = config('app.locale');

        if ($itemType === 'category') {
            return Category::query()
                ->with(['translations' => fn ($q) => $q->whereIn('locale', [$locale, $fallbackLocale])])
                ->orderByDesc('id')
                ->limit(300)
                ->get()
                ->map(function (Category $row) use ($locale, $fallbackLocale): array {
                    $translation = $row->translations->firstWhere('locale', $locale)
                        ?? $row->translations->firstWhere('locale', $fallbackLocale);
                    $name = $translation?->name ?: $row->code;

                    return ['id' => (int) $row->id, 'label' => '#'.$row->id.' - '.$name.' ('.$row->scope.')'];
                });
        }

        if ($itemType === 'blog') {
            return BlogPost::query()
                ->where('is_active', true)
                ->with(['translations' => fn ($q) => $q->whereIn('locale', [$locale, $fallbackLocale])])
                ->orderByDesc('id')
                ->limit(300)
                ->get()
                ->map(function (BlogPost $row) use ($locale, $fallbackLocale): array {
                    $translation = $row->translations->firstWhere('locale', $locale)
                        ?? $row->translations->firstWhere('locale', $fallbackLocale);
                    $title = $translation?->title ?: $row->code;

                    return ['id' => (int) $row->id, 'label' => '#'.$row->id.' - '.$title];
                });
        }

        return collect();
    }

    /**
     * @param  array<int, int>  $ids
     * @return array<int, int>
     */
    private function validIdsForItemType(string $itemType, array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        if ($itemType === 'category') {
            return Category::query()->whereIn('id', $ids)->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        if ($itemType === 'blog') {
            return BlogPost::query()->whereIn('id', $ids)->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        return [];
    }

    /**
     * @return array<int, array{value: string, suffix: string, label: string}>
     */
    private function defaultHomeStats(): array
    {
        return [];
    }

    /**
     * @return array<int, array{key: string, title: string, subtitle: string, text: string, bullets_text: string, image_alt: string, url: string, action_label: string}>
     */
    private function defaultHomeServices(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultHomeLocations(): array
    {
        return [
            'title' => '',
            'intro_lead' => '',
            'intro_text' => '',
            'hero_aria_label' => '',
            'map_aria_label' => '',
            'map_image_alt' => '',
            'map_link_label' => '',
            'email_label' => '',
            'phone_label' => '',
            'stats_aria_label' => '',
            'region_label' => '',
            'items' => [],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function emptyHomeLocation(): array
    {
        return [
            'entity_key' => '',
            'city' => '',
            'short_city' => '',
            'office_label' => '',
            'company' => '',
            'address' => '',
            'map_query' => '',
            'email' => '',
            'phone' => '',
            'number' => '',
            'coordinates_label' => '',
            'marker_aria_label' => '',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function defaultHomeContactPage(): array
    {
        return [
            'page_title' => '',
            'intro' => '',
            'form_title' => '',
            'form_intro' => '',
            'name_label' => '',
            'email_label' => '',
            'phone_label' => '',
            'subject_label' => '',
            'message_label' => '',
            'consent_label' => '',
            'submit_label' => '',
            'direct_title' => '',
            'direct_body' => '',
            'direct_email' => '',
            'direct_phone' => '',
            'direct_email_label' => '',
            'direct_phone_label' => '',
            'direct_response_time_label' => '',
            'direct_response_fallback' => '',
            'help_title' => '',
            'help_body' => '',
            'sent_status' => '',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultHomeValues(): array
    {
        return [
            'title' => '',
            'intro' => '',
            'items' => [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultHomeProcess(): array
    {
        return [
            'title' => '',
            'items' => [],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function defaultHomeNews(): array
    {
        return [
            'title' => '',
            'all_posts_label' => '',
            'all_posts_url' => '',
            'post_action_label' => '',
            'category_fallback' => '',
            'excerpt_fallback' => '',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function defaultHomeContactCta(): array
    {
        return [
            'title' => '',
            'card_title' => '',
            'text' => '',
            'button_label' => '',
            'button_url' => '',
            'status' => '',
        ];
    }

    /**
     * @return array<int, array{value: string, suffix: string, label: string}>
     */
    private function normalizeHomeStats(mixed $stats, bool $useDefaultsWhenEmpty = false): array
    {
        $rows = collect(is_array($stats) ? $stats : [])
            ->map(static function (mixed $row): array {
                $row = is_array($row) ? $row : [];

                return [
                    'value' => trim((string) ($row['value'] ?? '')),
                    'suffix' => trim((string) ($row['suffix'] ?? '')),
                    'label' => trim((string) ($row['label'] ?? '')),
                ];
            })
            ->filter(static fn (array $row): bool => $row['value'] !== '' || $row['label'] !== '')
            ->values()
            ->all();

        return $rows !== [] || ! $useDefaultsWhenEmpty
            ? $rows
            : $this->defaultHomeStats();
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeHomeLocations(mixed $locations, bool $useDefaultsWhenEmpty = false): array
    {
        $source = is_array($locations) ? $locations : [];
        $normalized = $this->defaultHomeLocations();

        foreach (array_keys($normalized) as $key) {
            if ($key === 'items') {
                continue;
            }

            $normalized[$key] = trim((string) ($source[$key] ?? ''));
        }

        $normalized['items'] = collect((array) ($source['items'] ?? []))
            ->map(function (mixed $item): array {
                $item = is_array($item) ? $item : [];
                $normalizedItem = $this->emptyHomeLocation();

                foreach (array_keys($normalizedItem) as $key) {
                    $normalizedItem[$key] = trim((string) ($item[$key] ?? ''));
                }

                return $normalizedItem;
            })
            ->filter(fn (array $item): bool => $this->payloadHasContent($item))
            ->values()
            ->all();

        return $this->payloadHasContent($normalized) || ! $useDefaultsWhenEmpty
            ? $normalized
            : $this->defaultHomeLocations();
    }

    /**
     * @return array<string, string>
     */
    private function normalizeHomeContactPage(mixed $content, bool $useDefaultsWhenEmpty = false): array
    {
        return $this->normalizeStringSection($content, $this->defaultHomeContactPage(), $useDefaultsWhenEmpty);
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeHomeValues(mixed $values, bool $useDefaultsWhenEmpty = false): array
    {
        $source = is_array($values) ? $values : [];
        $normalized = $this->defaultHomeValues();
        $normalized['title'] = trim((string) ($source['title'] ?? ''));
        $normalized['intro'] = trim((string) ($source['intro'] ?? ''));
        $normalized['items'] = $this->normalizeHomeCopyItems($source['items'] ?? []);

        return $this->payloadHasContent($normalized) || ! $useDefaultsWhenEmpty
            ? $normalized
            : $this->defaultHomeValues();
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeHomeProcess(mixed $process, bool $useDefaultsWhenEmpty = false): array
    {
        $source = is_array($process) ? $process : [];
        $normalized = $this->defaultHomeProcess();
        $normalized['title'] = trim((string) ($source['title'] ?? ''));
        $normalized['items'] = $this->normalizeHomeCopyItems($source['items'] ?? []);

        return $this->payloadHasContent($normalized) || ! $useDefaultsWhenEmpty
            ? $normalized
            : $this->defaultHomeProcess();
    }

    /**
     * @return array<string, string>
     */
    private function normalizeHomeNews(mixed $news, bool $useDefaultsWhenEmpty = false): array
    {
        return $this->normalizeStringSection($news, $this->defaultHomeNews(), $useDefaultsWhenEmpty);
    }

    /**
     * @return array<string, string>
     */
    private function normalizeHomeContactCta(mixed $contactCta, bool $useDefaultsWhenEmpty = false): array
    {
        return $this->normalizeStringSection($contactCta, $this->defaultHomeContactCta(), $useDefaultsWhenEmpty);
    }

    /**
     * @param  array<string, string>  $defaults
     * @return array<string, string>
     */
    private function normalizeStringSection(mixed $section, array $defaults, bool $useDefaultsWhenEmpty = false): array
    {
        $source = is_array($section) ? $section : [];
        $normalized = $defaults;

        foreach (array_keys($defaults) as $key) {
            $normalized[$key] = trim((string) ($source[$key] ?? ''));
        }

        return $this->payloadHasContent($normalized) || ! $useDefaultsWhenEmpty
            ? $normalized
            : $defaults;
    }

    /**
     * @return array<int, array{title: string, text: string}>
     */
    private function normalizeHomeCopyItems(mixed $items): array
    {
        return collect(is_array($items) ? $items : [])
            ->map(static function (mixed $item): array {
                $item = is_array($item) ? $item : [];

                return [
                    'title' => trim((string) ($item['title'] ?? '')),
                    'text' => trim((string) ($item['text'] ?? '')),
                ];
            })
            ->filter(static fn (array $item): bool => $item['title'] !== '' || $item['text'] !== '')
            ->values()
            ->all();
    }

    private function payloadHasContent(mixed $value): bool
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                if ($this->payloadHasContent($item)) {
                    return true;
                }
            }

            return false;
        }

        if (is_bool($value)) {
            return $value;
        }

        return trim((string) ($value ?? '')) !== '';
    }

    /**
     * @return array<int, array{key: string, title: string, subtitle: string, text: string, bullets_text: string, image_alt: string, url: string, action_label: string}>
     */
    private function normalizeHomeServices(mixed $services, bool $useDefaultsWhenEmpty = false): array
    {
        $rows = collect(is_array($services) ? $services : [])
            ->map(static function (mixed $row, int $index): array {
                $row = is_array($row) ? $row : [];
                $bullets = collect((array) ($row['bullets'] ?? []))
                    ->map(static fn (mixed $bullet): string => trim((string) $bullet))
                    ->filter(static fn (string $bullet): bool => $bullet !== '')
                    ->values()
                    ->all();
                $bulletsText = trim((string) ($row['bullets_text'] ?? ''));
                if ($bulletsText === '' && $bullets !== []) {
                    $bulletsText = implode("\n", $bullets);
                }

                return [
                    'key' => trim((string) ($row['key'] ?? (['audit', 'accounting', 'advisory'][$index] ?? ''))),
                    'title' => trim((string) ($row['title'] ?? '')),
                    'subtitle' => trim((string) ($row['subtitle'] ?? '')),
                    'text' => trim((string) ($row['text'] ?? '')),
                    'bullets_text' => $bulletsText,
                    'image_alt' => trim((string) ($row['image_alt'] ?? '')),
                    'url' => trim((string) ($row['url'] ?? '')),
                    'action_label' => trim((string) ($row['action_label'] ?? '')),
                ];
            })
            ->filter(static fn (array $row): bool => $row['title'] !== '')
            ->values()
            ->all();

        return $rows !== [] || ! $useDefaultsWhenEmpty
            ? $rows
            : $this->defaultHomeServices();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function homeServicesPayloadRows(mixed $services): array
    {
        return collect($this->normalizeHomeServices($services))
            ->map(static function (array $row): array {
                $bullets = preg_split('/\R/u', (string) ($row['bullets_text'] ?? '')) ?: [];

                return [
                    'key' => $row['key'],
                    'title' => $row['title'],
                    'subtitle' => $row['subtitle'],
                    'text' => $row['text'],
                    'bullets' => collect($bullets)
                        ->map(static fn (string $bullet): string => trim($bullet))
                        ->filter(static fn (string $bullet): bool => $bullet !== '')
                        ->values()
                        ->all(),
                    'image_alt' => $row['image_alt'],
                    'url' => $row['url'],
                    'action_label' => $row['action_label'],
                ];
            })
            ->values()
            ->all();
    }

    private function savePrimarySlot(ContentBlock $block, array $validated, ?int $userId): void
    {
        $slotData = [
            'placement' => (string) $validated['form']['slot_placement'],
            'frontend_variant' => (string) ($validated['form']['slot_frontend_variant'] ?? 'all'),
            'target_type' => $validated['form']['slot_target_type'] ?: null,
            'target_ref' => $validated['form']['slot_target_ref'] ?: null,
            'sort_order' => (int) $validated['form']['slot_sort_order'],
            'is_active' => (bool) $validated['form']['slot_is_active'],
            'starts_at' => $validated['form']['slot_starts_at'] ?: null,
            'ends_at' => $validated['form']['slot_ends_at'] ?: null,
            'updated_by' => $userId,
        ];

        $slot = $block->slots()->orderBy('sort_order')->orderBy('id')->first();
        if ($slot) {
            $slot->update($slotData);

            return;
        }

        $block->slots()->create($slotData + ['created_by' => $userId]);
    }

    /**
     * @param  array<int, int>  $itemIds
     */
    private function syncBlockItems(ContentBlock $block, ?string $itemType, array $itemIds): void
    {
        if ($itemType === null) {
            $block->items()->delete();

            return;
        }

        $block->items()->delete();

        foreach (array_values($itemIds) as $index => $itemId) {
            $block->items()->create([
                'item_type' => $itemType,
                'item_id' => (int) $itemId,
                'sort_order' => $index,
            ]);
        }
    }

    /**
     * @param  array<string, string>  $types
     * @return array<string, string>
     */
    private function orderedTypes(array $types): array
    {
        $priority = [
            'banner',
            'home_hero',
            'home_stats',
            'home_services',
            'desktop_hero_banner',
            'full_width_image_slider',
            'dual_image_cta',
            'mobile_hero_banner',
            'hero_highlights_strip',
            'blogs_carousel',
            'blog_grid_3',
            'categories',
            'blogs',
        ];
        $ordered = [];

        foreach ($priority as $key) {
            if (array_key_exists($key, $types)) {
                $ordered[$key] = $types[$key];
                unset($types[$key]);
            }
        }

        foreach ($types as $key => $label) {
            $ordered[$key] = $label;
        }

        return $ordered;
    }

    private function suggestedFrontendVariantForType(string $type): ?string
    {
        return match ($type) {
            'mobile_hero_banner' => 'mobile',
            'home_hero',
            'home_stats',
            'home_services',
            'desktop_hero_banner',
            'full_width_image_slider',
            'dual_image_cta',
            'blogs_carousel',
            'hero_highlights_strip' => 'desktop',
            default => null,
        };
    }

    private function suggestedPlacementForType(string $type): ?string
    {
        return match ($type) {
            'home_hero' => 'home.hero',
            'home_stats' => 'home.stats',
            'home_services' => 'home.services',
            default => null,
        };
    }

    private function defaultTemplateForType(string $type): string
    {
        return match ($type) {
            'home_hero' => <<<'BLADE'
@include('front.content-blocks.types.home_hero')
BLADE,
            'home_stats' => <<<'BLADE'
@include('front.content-blocks.types.home_stats')
BLADE,
            'home_services' => <<<'BLADE'
@include('front.content-blocks.types.home_services')
BLADE,
            'banner' => <<<'BLADE'
@php
    $basePayload = is_array($block->payload ?? null) ? $block->payload : [];
    $translationPayload = is_array($translation?->payload ?? null) ? $translation->payload : [];
    $payload = array_merge($basePayload, $translationPayload);

    $bgCss = trim((string) ($payload['bg_css'] ?? ''));
    $customClasses = trim((string) ($payload['custom_classes'] ?? ''));

    $bgUrl = $block->getFirstMediaUrl('block_background', 'hero_1440x480');
    if ($bgUrl === '') {
        $bgUrl = $block->getFirstMediaUrl('block_background');
    }

    $style = $bgCss;
    if ($bgUrl !== '') {
        $style = "background-image:url('{$bgUrl}');background-size:cover;background-position:center; ".$bgCss;
    }
@endphp

<section class="relative overflow-hidden rounded-3xl border border-slate-200/70 p-8 md:p-12 {{ $customClasses }}" @if($style !== '') style="{{ $style }}" @endif>
    <div class="absolute inset-0 bg-gradient-to-br from-white/80 via-white/60 to-white/40"></div>
    <div class="relative z-10 max-w-3xl">
        <h2 class="text-4xl font-extrabold tracking-tight md:text-5xl">{{ $translation?->title ?: $block->name }}</h2>
        @if(!empty($translation?->subtitle))
            <p class="mt-4 text-lg text-slate-700">{{ $translation->subtitle }}</p>
        @endif
        @if(!empty($translation?->cta_label) && !empty($translation?->cta_url))
            <a href="{{ $translation->cta_url }}" class="mt-8 inline-flex rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800">{{ $translation->cta_label }}</a>
        @endif
    </div>
</section>
BLADE,
            'five_star_reviews_carousel' => <<<'BLADE'
@php
    $locale = app()->getLocale();
    $fallbackLocale = config('app.locale');
    $translationPayload = is_array($translation?->payload ?? null) ? $translation->payload : [];
    $blockPayload = is_array($block->payload ?? null) ? $block->payload : [];
    $mergedPayload = array_merge($blockPayload, $translationPayload);
    $allowedRoutes = config('content_blocks.route_whitelist', []);
    $displayTitle = trim((string) ($translation?->title ?? ''));
    $displaySubtitle = trim((string) ($translation?->subtitle ?? ''));
    $itemsLimit = max(1, (int) ($mergedPayload['items_limit'] ?? 6));

    if ($displayTitle === '' || $displaySubtitle === '') {
        $allTranslations = $block->translations()->get(['locale', 'title', 'subtitle']);

        if ($displayTitle === '') {
            $displayTitle = trim((string) ($allTranslations->firstWhere('locale', $locale)?->title ?? ''));
            if ($displayTitle === '') {
                $displayTitle = trim((string) ($allTranslations->firstWhere('locale', $fallbackLocale)?->title ?? ''));
            }
            if ($displayTitle === '') {
                $displayTitle = trim((string) ($allTranslations->first(
                    static fn ($row): bool => trim((string) ($row->title ?? '')) !== ''
                )?->title ?? ''));
            }
        }

        if ($displaySubtitle === '') {
            $displaySubtitle = trim((string) ($allTranslations->firstWhere('locale', $locale)?->subtitle ?? ''));
            if ($displaySubtitle === '') {
                $displaySubtitle = trim((string) ($allTranslations->firstWhere('locale', $fallbackLocale)?->subtitle ?? ''));
            }
            if ($displaySubtitle === '') {
                $displaySubtitle = trim((string) ($allTranslations->first(
                    static fn ($row): bool => trim((string) ($row->subtitle ?? '')) !== ''
                )?->subtitle ?? ''));
            }
        }
    }

    if ($displayTitle === '') {
        $displayTitle = (string) $block->name;
    }

    $resolveRouteUrl = function (?string $routeName, mixed $routeParams, string $fallbackUrl = '#') use ($allowedRoutes): string {
        $name = trim((string) $routeName);
        if ($name === '') {
            return $fallbackUrl;
        }

        $isAllowed = $allowedRoutes === []
            || collect($allowedRoutes)->contains(fn ($pattern) => \Illuminate\Support\Str::is((string) $pattern, $name));

        if (! $isAllowed || !\Illuminate\Support\Facades\Route::has($name)) {
            return $fallbackUrl;
        }

        $params = is_array($routeParams) ? $routeParams : [];

        try {
            return route($name, $params);
        } catch (\Throwable) {
            return $fallbackUrl;
        }
    };

    $ctaLabel = trim((string) ($translation?->cta_label ?? ''));
    $ctaFallbackUrl = (string) ($translation?->cta_url ?? '#');
    $ctaRoute = (string) ($mergedPayload['cta_route'] ?? '');
    $ctaRouteParams = $mergedPayload['cta_route_params'] ?? [];
    $ctaUrl = $resolveRouteUrl($ctaRoute, $ctaRouteParams, $ctaFallbackUrl);
    $reviewRows = ($comments ?? collect())->take($itemsLimit);
@endphp

<section class="relative left-1/2 right-1/2 -ml-[50vw] -mr-[50vw] w-screen bg-white py-8">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <div class="mx-auto flex max-w-3xl items-center gap-4 md:gap-6">
                <span class="h-px flex-1 bg-slate-300"></span>
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl">{{ $displayTitle }}</h2>
                <span class="h-px flex-1 bg-slate-300"></span>
            </div>
            @if ($displaySubtitle !== '')
                <p class="mx-auto mt-2 max-w-2xl text-sm text-slate-600 md:text-base">{{ $displaySubtitle }}</p>
            @endif
            @if ($ctaLabel !== '' && $ctaUrl !== '')
                <a href="{{ $ctaUrl }}" class="mt-4 inline-flex h-10 items-center bg-slate-100 px-5 text-xs font-semibold uppercase tracking-[0.14em] text-slate-700 hover:bg-slate-200">
                    {{ $ctaLabel }}
                </a>
            @endif
        </div>

        @if ($reviewRows->isNotEmpty())
            <style>
                #reviews-carousel-{{ $block->id }} .splide__arrow {
                    opacity: 0;
                    width: 46px;
                    height: 46px;
                    border-radius: 9999px;
                    border: 1px solid rgba(255, 255, 255, 0.75);
                    background: rgba(15, 23, 42, 0.35);
                    backdrop-filter: blur(6px);
                    transform: translateY(-50%) scale(0.92);
                    transition: opacity .25s ease, transform .25s ease, background-color .25s ease;
                }

                #reviews-carousel-{{ $block->id }}:hover .splide__arrow,
                #reviews-carousel-{{ $block->id }}:focus-within .splide__arrow {
                    opacity: 1;
                    transform: translateY(-50%) scale(1);
                }

                #reviews-carousel-{{ $block->id }} .splide__arrow:hover {
                    background: rgba(15, 23, 42, 0.55);
                }

                #reviews-carousel-{{ $block->id }} .splide__arrow svg {
                    fill: #fff;
                }

                #reviews-carousel-{{ $block->id }} .review-card {
                    border: 1px solid #dbe3ef;
                    background: linear-gradient(180deg, #ffffff 0%, #f7f9fc 100%);
                }

                #reviews-carousel-{{ $block->id }} .review-quote {
                    color: #c9d3e5;
                    font-size: 2rem;
                    line-height: 1;
                    font-weight: 700;
                }
            </style>

            @once
                @push('scripts')
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
                    <script defer src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
                @endpush
            @endonce

            <div id="reviews-carousel-{{ $block->id }}" class="splide" data-five-star-reviews-splide>
                <div class="splide__track">
                    <ul class="splide__list">
                        @foreach ($reviewRows as $row)
                            <li class="splide__slide">
                                @php
                                    $author = $row->author_name ?: __('Anonymous');
                                    $authorInitial = mb_strtoupper(mb_substr(trim($author), 0, 1));
                                @endphp
                                <article class="review-card h-full p-6">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-amber-500">★★★★★</p>
                                        <span class="review-quote" aria-hidden="true">“</span>
                                    </div>
                                    <p class="mt-3 line-clamp-4 text-sm leading-relaxed text-slate-700">{{ $row->body }}</p>
                                    <div class="mt-5 flex items-center gap-3 border-t border-slate-200 pt-3">
                                        <span class="inline-flex h-8 w-8 items-center justify-center border border-slate-300 bg-white text-xs font-bold uppercase text-slate-700">{{ $authorInitial }}</span>
                                        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">{{ $author }}</p>
                                    </div>
                                </article>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            @once
                @push('scripts')
                    <script>
                        (function () {
                            const init = function () {
                                if (typeof window.Splide !== 'function') {
                                    return false;
                                }

                                const sliders = document.querySelectorAll('[data-five-star-reviews-splide]');
                                sliders.forEach(function (el) {
                                    if (el.dataset.splideReady === '1') {
                                        return;
                                    }
                                    el.dataset.splideReady = '1';

                                    const count = el.querySelectorAll('.splide__slide').length;
                                    new window.Splide(el, {
                                        type: count > 1 ? 'loop' : 'slide',
                                        perPage: Math.min(3, Math.max(1, count)),
                                        perMove: 1,
                                        gap: '1.25rem',
                                        drag: count > 1,
                                        snap: true,
                                        pagination: count > 1,
                                        arrows: count > 1,
                                        breakpoints: {
                                            1024: { perPage: Math.min(2, Math.max(1, count)) },
                                            640: { perPage: 1 },
                                        },
                                    }).mount();
                                });

                                return true;
                            };

                            if (init()) {
                                return;
                            }

                            let attempts = 0;
                            const timer = window.setInterval(function () {
                                attempts += 1;
                                if (init() || attempts > 40) {
                                    window.clearInterval(timer);
                                }
                            }, 120);
                        })();
                    </script>
                @endpush
            @endonce
        @endif
    </div>
</section>
BLADE,
            'blogs_carousel' => <<<'BLADE'
@include('front.content-blocks.types.blogs_carousel', [
    'block' => $block,
    'translation' => $translation,
    'slot' => $slot ?? null,
    'blockItems' => $blockItems ?? collect(),
])
BLADE,
            'blog_grid_3' => <<<'BLADE'
@include('front.content-blocks.types.blog_grid_3', [
    'block' => $block,
    'translation' => $translation,
    'slot' => $slot ?? null,
    'blockItems' => $blockItems ?? collect(),
])
BLADE,
            'categories' => <<<'BLADE'
<section class="rounded-3xl border border-slate-200 bg-white p-6">
    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">{{ $translation?->title ?: $block->name }}</h2>
    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @forelse($categories as $category)
            @php
                $ct = $category->translations->firstWhere('locale', app()->getLocale())
                    ?? $category->translations->firstWhere('locale', config('app.locale'));
            @endphp
            <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <h3 class="text-sm font-semibold text-slate-900">{{ $ct?->name ?? $category->code }}</h3>
                <p class="mt-1 text-xs uppercase tracking-[0.12em] text-slate-500">{{ $category->scope }}</p>
            </article>
        @empty
            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500 sm:col-span-2 xl:col-span-4">No categories selected.</div>
        @endforelse
    </div>
</section>
BLADE,
            'blogs' => <<<'BLADE'
<section class="rounded-3xl border border-slate-200 bg-white p-6">
    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">{{ $translation?->title ?: $block->name }}</h2>
    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($blogs as $post)
            @php
                $bt = $post->translations->firstWhere('locale', app()->getLocale())
                    ?? $post->translations->firstWhere('locale', config('app.locale'));
            @endphp
            <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <h3 class="text-sm font-semibold text-slate-900">{{ $bt?->title ?? $post->code }}</h3>
                @if(!empty($bt?->excerpt))
                    <p class="mt-1 text-xs text-slate-600">{{ \Illuminate\Support\Str::limit((string)$bt->excerpt, 100, '...') }}</p>
                @endif
            </article>
        @empty
            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500 sm:col-span-2 xl:col-span-3">No blog posts selected.</div>
        @endforelse
    </div>
</section>
BLADE,
            'desktop_hero_banner' => <<<'BLADE'
@php
    $title = $translation?->title ?: 'Modern essentials, built for everyday carry.';
    $subtitle = $translation?->subtitle ?: 'AGShop combines durable materials, clean silhouettes and practical storage to keep your daily setup lightweight and ready.';
    $primaryCtaLabel = $translation?->cta_label ?: 'Shop featured';
    $primaryCtaUrl = $translation?->cta_url ?: '#featured';
@endphp

<div class="max-w-3xl text-white">
    <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm">
        <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
        New season collection live now
    </p>

    <h1 class="mt-6 text-4xl font-extrabold leading-tight tracking-tight lg:text-6xl">
        {!! nl2br(e($title)) !!}
    </h1>

    @if ($subtitle !== '')
        <p class="mt-6 max-w-xl text-lg text-white/90">{{ $subtitle }}</p>
    @endif

    <div class="mt-10 flex flex-wrap items-center gap-4">
        <a href="{{ $primaryCtaUrl }}" class="rounded-xl bg-white px-6 py-3 font-semibold text-blue-700 hover:bg-slate-100">
            {{ $primaryCtaLabel }}
        </a>
        <a href="#categories" class="rounded-xl border border-white/30 px-6 py-3 text-white hover:bg-white/10">
            Browse categories
        </a>
    </div>
</div>
BLADE,
            'full_width_image_slider' => <<<'BLADE'
@php
    $translationPayload = is_array($translation?->payload ?? null) ? $translation->payload : [];
    $customClasses = trim((string) ($translationPayload['custom_classes'] ?? ''));
    $sliderId = 'full-width-slider-'.$block->id;
    $slides = $block->getMedia('block_slides');

    if ($slides->isEmpty()) {
        $fallback = $block->getFirstMedia('block_background');
        if ($fallback) {
            $slides = collect([$fallback]);
        }
    }

    $autoplayMs = 5000;
@endphp

@if ($slides->isNotEmpty())
    @once
        @push('scripts')
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
            <script defer src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
        @endpush
    @endonce

    <style>
        #{{ $sliderId }} .splide__arrow {
            opacity: 0;
            width: 46px;
            height: 46px;
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, 0.75);
            background: rgba(15, 23, 42, 0.35);
            backdrop-filter: blur(6px);
            transform: translateY(-50%) scale(0.92);
            transition: opacity .25s ease, transform .25s ease, background-color .25s ease;
        }

        #{{ $sliderId }}:hover .splide__arrow,
        #{{ $sliderId }}:focus-within .splide__arrow {
            opacity: 1;
            transform: translateY(-50%) scale(1);
        }

        #{{ $sliderId }} .splide__arrow:hover {
            background: rgba(15, 23, 42, 0.55);
        }

        #{{ $sliderId }} .splide__arrow svg {
            fill: #fff;
        }
    </style>

    <section class="relative left-1/2 right-1/2 -ml-[50vw] -mr-[50vw] w-screen overflow-hidden {{ $customClasses }}">
        <div id="{{ $sliderId }}" class="splide" data-fullwidth-splide>
            <div class="splide__track">
                <ul class="splide__list">
                    @foreach ($slides as $media)
                        @php
                            $slideUrl = $media->hasGeneratedConversion('hero_1440x480')
                                ? $media->getUrl('hero_1440x480')
                                : $media->getUrl();
                            $slideLink = trim((string) (
                                data_get($media->custom_properties, 'link_url.'.app()->getLocale())
                                ?: data_get($media->custom_properties, 'link_url.'.config('app.locale'))
                                ?: data_get($media->custom_properties, 'link_url_value', '')
                            ));
                            $hasSlideLink = $slideLink !== '';
                        @endphp
                        <li class="splide__slide">
                            <article class="relative min-w-full">
                                @if ($hasSlideLink)
                                    <a href="{{ $slideLink }}" class="block">
                                @endif
                                    <img src="{{ $slideUrl }}" alt="{{ $translation?->title ?: $block->name }} {{ $loop->iteration }}" class="h-[42vw] min-h-[420px] max-h-[880px] w-full object-cover">
                                    <div class="absolute inset-0 bg-black/10"></div>
                                    @if (($translation?->title ?? '') !== '' || ($translation?->subtitle ?? '') !== '')
                                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/50 via-black/20 to-transparent px-6 pb-10 pt-16 text-white md:px-12">
                                            @if (($translation?->title ?? '') !== '')
                                                <h2 class="text-3xl font-extrabold tracking-tight md:text-5xl">{{ $translation->title }}</h2>
                                            @endif
                                            @if (($translation?->subtitle ?? '') !== '')
                                                <p class="mt-3 max-w-3xl text-sm text-white/90 md:text-base">{{ $translation->subtitle }}</p>
                                            @endif
                                            @if (($translation?->cta_label ?? '') !== '' && (($translation?->cta_url ?? '') !== '' || $hasSlideLink))
                                                @if ($hasSlideLink)
                                                    <span class="mt-6 inline-flex h-11 items-center border border-white bg-white px-6 text-sm font-semibold text-slate-900">
                                                        {{ $translation->cta_label }}
                                                    </span>
                                                @else
                                                    <a href="{{ $translation->cta_url }}" class="mt-6 inline-flex h-11 items-center border border-white bg-white px-6 text-sm font-semibold text-slate-900 hover:bg-slate-100">
                                                        {{ $translation->cta_label }}
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                @if ($hasSlideLink)
                                    </a>
                                @endif
                            </article>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    @once
        @push('scripts')
            <script>
                (function () {
                    const init = function () {
                        if (typeof window.Splide !== 'function') {
                            return false;
                        }

                        const sliders = document.querySelectorAll('[data-fullwidth-splide]');
                        sliders.forEach(function (el) {
                            if (el.dataset.splideReady === '1') {
                                return;
                            }
                            el.dataset.splideReady = '1';

                            const count = el.querySelectorAll('.splide__slide').length;
                            new window.Splide(el, {
                                type: count > 1 ? 'loop' : 'slide',
                                perPage: 1,
                                perMove: 1,
                                arrows: count > 1,
                                pagination: count > 1,
                                autoplay: count > 1,
                                interval: {{ $autoplayMs }},
                                pauseOnHover: true,
                                pauseOnFocus: true,
                                speed: 700,
                                easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                            }).mount();
                        });

                        return true;
                    };

                    if (init()) {
                        return;
                    }

                    let attempts = 0;
                    const timer = window.setInterval(function () {
                        attempts += 1;
                        if (init() || attempts > 40) {
                            window.clearInterval(timer);
                        }
                    }, 120);
                })();
            </script>
        @endpush
    @endonce
@endif
BLADE,
            'dual_image_cta' => <<<'BLADE'
@php
    $locale = app()->getLocale();
    $fallbackLocale = config('app.locale');
    $translationPayload = is_array($translation?->payload ?? null) ? $translation->payload : [];
    $customClasses = trim((string) ($translationPayload['custom_classes'] ?? ''));
    $slides = $block->getMedia('block_slides')->take(2);
@endphp

@if ($slides->isNotEmpty())
    <section class="relative left-1/2 right-1/2 -ml-[50vw] -mr-[50vw] w-screen {{ $customClasses }}">
        <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
            @foreach ($slides as $media)
                @php
                    $imageUrl = $media->hasGeneratedConversion('hero_1440x480')
                        ? $media->getUrl('hero_1440x480')
                        : $media->getUrl();
                    $props = (array) ($media->custom_properties ?? []);
                    $slideTitle = trim((string) (
                        data_get($props, "block_title.$locale")
                        ?: data_get($props, "block_title.$fallbackLocale")
                        ?: $media->name
                    ));

                    $cta1Label = trim((string) (
                        data_get($props, "cta_1_label.$locale")
                        ?: data_get($props, "cta_1_label.$fallbackLocale")
                        ?: __('ui.content_blocks.dual_image_cta.default_cta_1')
                    ));
                    $cta1Url = trim((string) (
                        data_get($props, "cta_1_url.$locale")
                        ?: data_get($props, "cta_1_url.$fallbackLocale")
                        ?: '#'
                    ));

                    $cta2Label = trim((string) (
                        data_get($props, "cta_2_label.$locale")
                        ?: data_get($props, "cta_2_label.$fallbackLocale")
                        ?: __('ui.content_blocks.dual_image_cta.default_cta_2')
                    ));
                    $cta2Url = trim((string) (
                        data_get($props, "cta_2_url.$locale")
                        ?: data_get($props, "cta_2_url.$fallbackLocale")
                        ?: '#'
                    ));
                @endphp

                <article class="group relative min-h-[360px] overflow-hidden md:min-h-[560px]">
                    <img src="{{ $imageUrl }}" alt="{{ $slideTitle !== '' ? $slideTitle : $block->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/20 to-transparent"></div>

                    <div class="absolute inset-x-0 bottom-12 px-8 text-center text-white md:bottom-16 md:px-10">
                        @if ($slideTitle !== '')
                            <h3 class="text-3xl font-black uppercase tracking-[0.02em] md:text-4xl">{{ $slideTitle }}</h3>
                        @endif

                        <div class="mx-auto mt-5 flex max-w-[460px] flex-col justify-center gap-2.5 sm:flex-row">
                            @if ($cta1Label !== '')
                                <a href="{{ $cta1Url !== '' ? $cta1Url : '#' }}" class="inline-flex h-11 min-w-[145px] items-center justify-center border border-white bg-white px-5 text-base font-black uppercase tracking-[0.02em] text-slate-900 transition hover:bg-slate-100">
                                    {{ $cta1Label }}
                                </a>
                            @endif

                            @if ($cta2Label !== '')
                                <a href="{{ $cta2Url !== '' ? $cta2Url : '#' }}" class="inline-flex h-11 min-w-[145px] items-center justify-center border border-white bg-white px-5 text-base font-black uppercase tracking-[0.02em] text-slate-900 transition hover:bg-slate-100">
                                    {{ $cta2Label }}
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endif
BLADE,
            'mobile_hero_banner' => <<<'BLADE'
@php
    $basePayload = is_array($block->payload ?? null) ? $block->payload : [];
    $translationPayload = is_array($translation?->payload ?? null) ? $translation->payload : [];
    $payload = array_merge($basePayload, $translationPayload);

    $title = $translation?->title ?: 'Modern essentials';
    $subtitle = $translation?->subtitle ?: 'Browse category picks and essentials.';
    $ctaLabel = $translation?->cta_label ?: 'Shop';
    $ctaUrl = $translation?->cta_url ?: '#categories';
    $sliderId = 'mobile-hero-slider-'.$block->id;

    $slideClassList = ['bg-19', 'bg-18', 'bg-17', 'bg-20'];
    $customClasses = trim((string) ($payload['custom_classes'] ?? ''));
    if ($customClasses !== '') {
        $slideClassList = preg_split('/\s+/', $customClasses) ?: $slideClassList;
    }
@endphp

@if ($categories->isNotEmpty())
    <div class="splide single-slider slider-no-arrows slider-no-dots" id="{{ $sliderId }}">
        <div class="splide__track">
            <div class="splide__list">
                @foreach ($categories as $index => $category)
                    @php
                        $ct = $category->translations->firstWhere('locale', app()->getLocale())
                            ?? $category->translations->firstWhere('locale', config('app.locale'));
                        $categoryName = $ct?->name ?: $category->code;
                        $slideClass = $slideClassList[$index % max(count($slideClassList), 1)] ?? 'bg-19';
                    @endphp
                    <div class="splide__slide">
                        <div class="card card-style mb-3 {{ $slideClass }}" data-card-height="300">
                            <div class="card-bottom mb-3 ms-3 me-3">
                                <h1 class="color-white font-800 mb-n2">{{ $categoryName }}</h1>
                                <p class="color-white font-14 mb-2 opacity-60">{{ $subtitle }}</p>
                                <a href="{{ $ctaUrl }}" class="btn btn-xxs rounded-xs bg-white color-black font-700 mt-2">
                                    {{ trim($ctaLabel.' '.$categoryName) }}
                                </a>
                            </div>
                            <div class="card-overlay bg-black opacity-60"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@else
    <div class="card card-style mb-3 bg-19" data-card-height="300">
        <div class="card-bottom mb-3 ms-3 me-3">
            <h1 class="color-white font-800 mb-n2">{{ $title }}</h1>
            <p class="color-white font-14 mb-2 opacity-60">{{ $subtitle }}</p>
            <a href="{{ $ctaUrl }}" class="btn btn-xxs rounded-xs bg-white color-black font-700 mt-2">{{ $ctaLabel }}</a>
        </div>
        <div class="card-overlay bg-black opacity-60"></div>
    </div>
@endif
BLADE,
            'hero_highlights_strip' => <<<'BLADE'
<div class="mx-auto grid max-w-7xl gap-8 text-white md:grid-cols-3">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/15">
            <span class="text-xl font-bold">+</span>
        </div>
        <div>
            <div class="text-2xl font-bold leading-none">Fast Response</div>
            <div class="mt-1 text-sm leading-tight text-white/80">Most inquiries receive a reply within one business day.</div>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/15">
            <span class="text-xl font-bold">+</span>
        </div>
        <div>
            <div class="text-2xl font-bold leading-none">Senior Expertise</div>
            <div class="mt-1 text-sm leading-tight text-white/80">Finance, tax, accounting, and advisory support in one place.</div>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/15">
            <span class="text-xl font-bold">+</span>
        </div>
        <div>
            <div class="text-2xl font-bold leading-none">Trusted Delivery</div>
            <div class="mt-1 text-sm leading-tight text-white/80">Clear onboarding, documented scope, and direct communication.</div>
        </div>
    </div>
</div>
BLADE,
            default => <<<'BLADE'
<section class="rounded-2xl border border-slate-200 bg-white p-6">
    @if(!empty($translation?->title))
        <h2 class="text-xl font-semibold text-slate-900">{{ $translation->title }}</h2>
    @endif
    @if(!empty($translation?->subtitle))
        <p class="mt-2 text-sm text-slate-600">{{ $translation->subtitle }}</p>
    @endif
</section>
BLADE,
        };
    }

    private function templateViewName(string $code): string
    {
        return 'front.content-blocks.instances.'.$code;
    }

    private function templateFilePath(string $code): string
    {
        return resource_path('views/front/content-blocks/instances/'.$code.'.blade.php');
    }

    private function readTemplateFile(string $code): string
    {
        $path = $this->templateFilePath($code);
        if (! File::exists($path)) {
            return '';
        }

        return (string) File::get($path);
    }

    private function writeTemplateFile(string $code, string $contents): void
    {
        $dir = resource_path('views/front/content-blocks/instances');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put($this->templateFilePath($code), rtrim($contents)."\n");
    }

    private function deleteTemplateFile(string $code): void
    {
        $path = $this->templateFilePath($code);
        if (File::exists($path)) {
            File::delete($path);
        }
    }

    private function normalizedTemplate(string $template): string
    {
        return trim(str_replace(["\r\n", "\r"], "\n", $template));
    }
}
