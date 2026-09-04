<?php

namespace App\Livewire\Admin\Content\Service;

use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Service\ServicePage;
use App\Models\Content\Service\ServicePageTranslation;
use App\Models\Content\Support\Faq;
use App\Models\Content\Team\TeamMember;
use App\Models\Settings\Local\Language;
use App\Support\Admin\AdminLocale;
use App\Support\Content\ServicePageTemplateRegistry;
use App\Support\Content\YouTubeUrl;
use App\Support\Media\MediaProfileRegistry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    private const ADVISORY_CONTENT_SECTIONS = [
        'main',
        'financial',
        'funding',
        'bank_loans',
        'zopu',
        'ma',
        'due_diligence',
        'valuations',
        'tax',
    ];

    private const TAB_OPTIONS = ['content', 'sources', 'seo', 'media'];

    private const SOURCE_ENABLED_TEMPLATES = [
        ServicePageTemplateRegistry::ADVISORY,
        ServicePageTemplateRegistry::FINANCE,
        ServicePageTemplateRegistry::ACCOUNTING,
        ServicePageTemplateRegistry::AUDIT,
        ServicePageTemplateRegistry::TAX,
        ServicePageTemplateRegistry::EU_FUNDS,
        ServicePageTemplateRegistry::FAMILY_BUSINESS,
    ];

    private const BLOG_SOURCE_ENABLED_TEMPLATES = [
        ServicePageTemplateRegistry::ADVISORY,
        ServicePageTemplateRegistry::FINANCE,
        ServicePageTemplateRegistry::ACCOUNTING,
        ServicePageTemplateRegistry::AUDIT,
        ServicePageTemplateRegistry::TAX,
        ServicePageTemplateRegistry::EU_FUNDS,
        ServicePageTemplateRegistry::FAMILY_BUSINESS,
    ];

    private const FAQ_SOURCE_ENABLED_TEMPLATES = [
        ServicePageTemplateRegistry::FAMILY_BUSINESS,
    ];

    private const TEAM_SOURCE_ENABLED_TEMPLATES = [
        ServicePageTemplateRegistry::FAMILY_BUSINESS,
    ];

    private const BROCHURE_ENABLED_TEMPLATES = [
        ServicePageTemplateRegistry::FAMILY_BUSINESS,
    ];

    private const MANUAL_SELECTION_PATHS = [
        'blog_posts' => 'page_payload.blog_source.post_ids',
        'faqs' => 'page_payload.faq_source.faq_ids',
        'team_members' => 'page_payload.team_source.member_ids',
    ];

    public ?int $servicePageId = null;

    public string $contentSection = 'main';

    public string $activeTab = 'content';

    public ?int $blogPickerId = null;

    public ?int $faqPickerId = null;

    public ?int $teamPickerId = null;

    /** @var array<string, TemporaryUploadedFile|null> */
    public array $assetUploads = [];

    /** @var array<string, TemporaryUploadedFile|null> */
    public array $landingImageUploads = [];

    public ?TemporaryUploadedFile $auditHeroImageUpload = null;

    public ?TemporaryUploadedFile $accountingHeroImageUpload = null;

    public ?TemporaryUploadedFile $advisoryHeroImageUpload = null;

    public ?TemporaryUploadedFile $advisoryPandeaLogoUpload = null;

    public ?TemporaryUploadedFile $euFundsHeroImageUpload = null;

    /** @var array<string, mixed> */
    public array $loadedTranslationPayload = [];

    /** @var array<string, mixed> */
    public array $translationPayloadBaseline = [];

    /** @var array<int, string> */
    private array $newEuFundsAssetPaths = [];

    /** @var array<int, string> */
    private array $replacedEuFundsAssetPaths = [];

    /**
     * @var array<string, string>
     */
    public array $audienceIconOptions = [
        'founders' => 'Osnivaci',
        'successors' => 'Nasljednici',
        'family' => 'Clanovi obitelji',
        'managers' => 'Neobiteljski menadzeri',
    ];

    /**
     * @var array<string, string>
     */
    public array $capabilityIconOptions = [
        'governance' => 'Upravljanje',
        'transition' => 'Tranzicija',
        'relations' => 'Dinamika odnosa',
    ];

    public array $form = [
        'code' => '',
        'template_key' => ServicePageTemplateRegistry::SERVICES_INDEX,
        'is_active' => true,
        'published_at' => '',
        'sort_order' => 0,
        'locale' => 'en',
        'title' => '',
        'slug' => '',
        'meta_title' => '',
        'meta_description' => '',
        'page_payload' => [],
        'translation_payload' => [],
    ];

    public function mount(?int $servicePageId = null): void
    {
        $requestedSection = trim((string) request()->query('section', 'main'));
        $this->contentSection = in_array($requestedSection, self::ADVISORY_CONTENT_SECTIONS, true)
            ? $requestedSection
            : 'main';
        $requestedLocale = AdminLocale::normalize((string) (
            request()->query('locale') ?: app()->getLocale() ?: AdminLocale::default()
        ));
        $localeOptions = $this->activeContentLocaleOptions();
        $this->form['locale'] = in_array($requestedLocale, $localeOptions, true)
            ? $requestedLocale
            : ($localeOptions[0] ?? AdminLocale::default());
        $this->initializeTemplateDefaults((string) $this->form['template_key']);

        if ($servicePageId) {
            $this->servicePageId = $servicePageId;
            $this->loadServicePage();
        }
    }

    public function updatedFormLocale(): void
    {
        $this->loadTranslationForLocale();
        $this->blogPickerId = null;
        $this->faqPickerId = null;
        $this->teamPickerId = null;
        $this->assetUploads = [];
        $this->landingImageUploads = [];
        $this->auditHeroImageUpload = null;
        $this->accountingHeroImageUpload = null;
        $this->advisoryHeroImageUpload = null;
        $this->advisoryPandeaLogoUpload = null;
        $this->euFundsHeroImageUpload = null;
    }

    public function updatedFormTemplateKey(string $templateKey): void
    {
        if ($this->servicePageId) {
            return;
        }

        $knownDefaultCodes = collect(array_keys(ServicePageTemplateRegistry::labels()))
            ->map(fn (string $key): string => ServicePageTemplateRegistry::defaultCode($key))
            ->all();

        if (trim((string) $this->form['code']) === '' || in_array($this->form['code'], $knownDefaultCodes, true)) {
            $this->form['code'] = ServicePageTemplateRegistry::defaultCode($templateKey);
        }

        $this->initializeTemplateDefaults($templateKey);
    }

    public function generateSlug(): void
    {
        $title = trim((string) $this->form['title']);
        if ($title !== '') {
            $this->form['slug'] = str($title)->slug()->value();
        }
    }

    public function setTab(string $tab): void
    {
        if (! in_array($tab, self::TAB_OPTIONS, true)) {
            return;
        }

        if ($tab === 'sources' && ! $this->templateSupportsSources()) {
            return;
        }

        $this->activeTab = $tab;
    }

    public function addManualItem(string $target, int $id): void
    {
        $path = $this->manualSelectionPath($target);
        if ($path === null || $id <= 0) {
            return;
        }

        $ids = collect((array) data_get($this->form, $path, []))
            ->map(fn ($value): int => (int) $value)
            ->filter()
            ->values();

        if ($ids->contains($id)) {
            return;
        }

        $ids->push($id);
        data_set($this->form, $path, $ids->all());
    }

    public function removeManualItem(string $target, int $id): void
    {
        $path = $this->manualSelectionPath($target);
        if ($path === null || $id <= 0) {
            return;
        }

        $ids = collect((array) data_get($this->form, $path, []))
            ->map(fn ($value): int => (int) $value)
            ->reject(fn (int $value): bool => $value === $id)
            ->values()
            ->all();

        data_set($this->form, $path, $ids);
    }

    public function moveManualItemUp(string $target, int $index): void
    {
        $this->moveManualItem($target, $index, -1);
    }

    public function moveManualItemDown(string $target, int $index): void
    {
        $this->moveManualItem($target, $index, 1);
    }

    public function addVideoSource(): void
    {
        $rows = (array) data_get($this->form, 'page_payload.video_source.items', []);
        $rows[] = [
            'title' => '',
            'youtube_url' => '',
        ];

        data_set($this->form, 'page_payload.video_source.items', array_values($rows));
    }

    public function removeVideoSource(int $index): void
    {
        $rows = array_values((array) data_get($this->form, 'page_payload.video_source.items', []));

        if (! array_key_exists($index, $rows)) {
            return;
        }

        unset($rows[$index]);

        data_set($this->form, 'page_payload.video_source.items', array_values($rows));
    }

    public function moveVideoSourceUp(int $index): void
    {
        $this->moveVideoSource($index, -1);
    }

    public function moveVideoSourceDown(int $index): void
    {
        $this->moveVideoSource($index, 1);
    }

    public function addTranslationListItem(string $path, string $preset = 'string'): void
    {
        $items = (array) data_get($this->form, 'translation_payload.'.$path, []);
        $items[] = $this->translationListItemPreset($preset);

        data_set($this->form, 'translation_payload.'.$path, array_values($items));
    }

    public function removeTranslationListItem(string $path, int $index): void
    {
        $items = (array) data_get($this->form, 'translation_payload.'.$path, []);

        if (! array_key_exists($index, $items)) {
            return;
        }

        unset($items[$index]);

        data_set($this->form, 'translation_payload.'.$path, array_values($items));
    }

    public function save()
    {
        if (trim((string) ($this->form['code'] ?? '')) === '') {
            $this->form['code'] = ServicePageTemplateRegistry::defaultCode((string) $this->form['template_key']);
        }

        if (trim((string) ($this->form['slug'] ?? '')) === '') {
            $this->form['slug'] = Str::slug((string) ($this->form['title'] ?? ''));
        }

        $pagePayloadInput = (array) data_get($this->form, 'page_payload', []);
        $translationPayloadInput = (array) data_get($this->form, 'translation_payload', []);
        $validated = $this->validate($this->rules());
        $wasEditing = (bool) $this->servicePageId;
        $userId = auth()->id();
        $savedServicePage = null;

        $pagePayload = $this->normalizedPagePayload($pagePayloadInput);
        if ($pagePayload === false) {
            return null;
        }

        $this->newEuFundsAssetPaths = [];
        $this->replacedEuFundsAssetPaths = [];

        try {
            $translationPayload = $this->normalizedTranslationPayload(
                (string) $validated['form']['template_key'],
                $translationPayloadInput
            );

            DB::transaction(function () use ($validated, $pagePayload, $translationPayload, $userId, $wasEditing, &$savedServicePage): void {
                $servicePageData = [
                    'code' => trim((string) $validated['form']['code']),
                    'template_key' => trim((string) $validated['form']['template_key']),
                    'is_active' => (bool) $validated['form']['is_active'],
                    'published_at' => $validated['form']['published_at'] ?: null,
                    'sort_order' => (int) $validated['form']['sort_order'],
                    'payload' => $pagePayload,
                    'updated_by' => $userId,
                ];

                if ($this->servicePageId) {
                    $servicePage = ServicePage::query()->findOrFail($this->servicePageId);

                    if ((array) $servicePage->payload == (array) $servicePageData['payload']) {
                        $servicePageData['payload'] = $servicePage->payload;
                    }

                    $servicePage->fill($servicePageData);

                    if ($servicePage->isDirty()) {
                        $servicePage->save();
                    }
                } else {
                    $servicePage = ServicePage::query()->create($servicePageData + ['created_by' => $userId]);
                    $this->servicePageId = (int) $servicePage->id;
                }

                $servicePage->translations()->updateOrCreate(
                    ['locale' => $validated['form']['locale']],
                    [
                        'title' => trim((string) $validated['form']['title']),
                        'slug' => trim((string) $validated['form']['slug']),
                        'meta_title' => trim((string) ($validated['form']['meta_title'] ?? '')) ?: null,
                        'meta_description' => trim((string) ($validated['form']['meta_description'] ?? '')) ?: null,
                        'payload' => $translationPayload,
                    ]
                );

                $savedServicePage = $servicePage;

                activity('content_service_pages')
                    ->performedOn($servicePage)
                    ->causedBy(auth()->user())
                    ->event($wasEditing ? 'updated' : 'created')
                    ->withProperties([
                        'locale' => $validated['form']['locale'],
                        'slug' => $validated['form']['slug'],
                        'template_key' => $validated['form']['template_key'],
                    ])
                    ->log('Service page saved');
            });
        } catch (\Throwable $exception) {
            $this->deleteManagedEuFundsAssets($this->newEuFundsAssetPaths);

            throw $exception;
        }

        $this->deleteUnreferencedReplacedEuFundsAssets();

        if ($savedServicePage instanceof ServicePage) {
            $this->storeServicesIndexCardImages($savedServicePage);
            $this->storeAuditHeroImage($savedServicePage);
            $this->storeAccountingHeroImage($savedServicePage);
            $this->storeAdvisoryMedia($savedServicePage);
            $this->storeEuFundsHeroImage($savedServicePage);
        }

        $message = $wasEditing ? __('Service page updated.') : __('Service page created.');

        return redirect()
            ->route('admin.content.services.index', ['locale' => $this->form['locale']])
            ->with('notify', [
                'type' => 'success',
                'message' => $message,
            ]);
    }

    public function backToList()
    {
        return redirect()->route('admin.content.services.index', ['locale' => $this->form['locale']]);
    }

    public function removeServicesIndexCardImage(string $cardKey): void
    {
        $collection = ServicePageTemplateRegistry::SERVICES_INDEX_CARD_MEDIA_COLLECTIONS[$cardKey] ?? null;
        if (! $collection || ! $this->servicePageId) {
            return;
        }

        $servicePage = ServicePage::query()->find($this->servicePageId);
        if (! $servicePage || $servicePage->template_key !== ServicePageTemplateRegistry::SERVICES_INDEX) {
            return;
        }

        $servicePage->clearMediaCollection($collection);
        unset($this->landingImageUploads[$cardKey]);

        $this->dispatch('notify', type: 'success', message: __('Default card image restored.'));
    }

    public function removeAuditHeroImage(): void
    {
        if (! $this->servicePageId) {
            return;
        }

        $servicePage = ServicePage::query()->find($this->servicePageId);
        if (! $servicePage || $servicePage->template_key !== ServicePageTemplateRegistry::AUDIT) {
            return;
        }

        $servicePage->clearMediaCollection('service_hero_image');
        $this->auditHeroImageUpload = null;

        $this->dispatch('notify', type: 'success', message: __('Default audit hero image restored.'));
    }

    public function removeAccountingHeroImage(): void
    {
        if (! $this->servicePageId) {
            return;
        }

        $servicePage = ServicePage::query()->find($this->servicePageId);
        if (! $servicePage || $servicePage->template_key !== ServicePageTemplateRegistry::ACCOUNTING) {
            return;
        }

        $servicePage->clearMediaCollection('service_hero_image');
        $this->accountingHeroImageUpload = null;

        $this->dispatch('notify', type: 'success', message: 'Vraćena je zadana hero slika za Računovodstvo i poreze.');
    }

    public function removeAdvisoryHeroImage(): void
    {
        $this->removeAdvisoryMedia('service_hero_image', 'Vraćena je zadana hero slika za Savjetovanje.');
        $this->advisoryHeroImageUpload = null;
    }

    public function removeAdvisoryPandeaLogo(): void
    {
        $this->removeAdvisoryMedia('service_logo', 'Vraćen je zadani Pandea logotip.');
        $this->advisoryPandeaLogoUpload = null;
    }

    public function removeEuFundsHeroImage(): void
    {
        if (! $this->servicePageId) {
            return;
        }

        $servicePage = ServicePage::query()->find($this->servicePageId);
        if (! $servicePage || $servicePage->template_key !== ServicePageTemplateRegistry::EU_FUNDS) {
            return;
        }

        $servicePage->clearMediaCollection('service_hero_image');
        $this->euFundsHeroImageUpload = null;

        $this->dispatch('notify', type: 'success', message: 'Vraćena je zadana hero slika za EU fondove.');
    }

    public function render()
    {
        return view('livewire.admin.content.service.form', [
            'isEdit' => (bool) $this->servicePageId,
            'contentSection' => $this->contentSection,
            'templateOptions' => ServicePageTemplateRegistry::labels(),
            'templateSupportsSources' => $this->templateSupportsSources(),
            'templateSupportsBlogSource' => $this->templateSupportsBlogSource(),
            'templateSupportsFaqSource' => $this->templateSupportsFaqSource(),
            'templateSupportsTeamSource' => $this->templateSupportsTeamSource(),
            'templateSupportsBrochure' => $this->templateSupportsBrochure(),
            'servicesIndexCardImages' => $this->servicesIndexCardImages(),
            'auditHeroImage' => $this->auditHeroImage(),
            'accountingHeroImage' => $this->accountingHeroImage(),
            'advisoryHeroImage' => $this->advisoryHeroImage(),
            'advisoryPandeaLogo' => $this->advisoryPandeaLogo(),
            'euFundsHeroImage' => $this->euFundsHeroImage(),
        ]);
    }

    public function getBlogCategoryOptionsProperty(): Collection
    {
        return Category::query()
            ->where('scope', Category::SCOPE_BLOG)
            ->withDepth()
            ->defaultOrder()
            ->with([
                'translations' => fn ($q) => $q
                    ->where('scope', Category::SCOPE_BLOG)
                    ->where('locale', $this->form['locale']),
            ])
            ->get()
            ->map(function (Category $category): array {
                $translation = $category->translations->first();

                return [
                    'id' => (int) $category->id,
                    'label' => (string) ($translation?->name ?? $category->code),
                ];
            });
    }

    public function getBlogPostOptionsProperty(): Collection
    {
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale', 'en'));
        $locales = array_values(array_unique([$this->form['locale'], $fallbackLocale]));

        return BlogPost::query()
            ->with([
                'translations' => fn ($q) => $q->whereIn('locale', $locales),
            ])
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get()
            ->map(function (BlogPost $post) use ($fallbackLocale): array {
                $translation = $post->translations->firstWhere('locale', $this->form['locale'])
                    ?? $post->translations->firstWhere('locale', $fallbackLocale)
                    ?? $post->translations->first();

                return [
                    'id' => (int) $post->id,
                    'label' => (string) ($translation?->title ?? $post->code),
                ];
            });
    }

    public function getFaqOptionsProperty(): Collection
    {
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale', 'en'));
        $locales = array_values(array_unique([$this->form['locale'], $fallbackLocale]));

        return Faq::query()
            ->with([
                'translations' => fn ($q) => $q->whereIn('locale', $locales),
            ])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (Faq $faq) use ($fallbackLocale): array {
                $translation = $faq->translations->firstWhere('locale', $this->form['locale'])
                    ?? $faq->translations->firstWhere('locale', $fallbackLocale)
                    ?? $faq->translations->first();

                return [
                    'id' => (int) $faq->id,
                    'label' => (string) ($translation?->question ?? $faq->code),
                ];
            });
    }

    public function getFaqGroupOptionsProperty(): Collection
    {
        return Faq::query()
            ->orderBy('group_code')
            ->pluck('group_code')
            ->filter(fn ($value): bool => trim((string) $value) !== '')
            ->unique()
            ->values()
            ->map(fn ($value): array => [
                'id' => (string) $value,
                'label' => (string) $value,
            ]);
    }

    public function getTeamOptionsProperty(): Collection
    {
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale', 'en'));
        $locales = array_values(array_unique([$this->form['locale'], $fallbackLocale]));

        return TeamMember::query()
            ->with([
                'translations' => fn ($q) => $q->whereIn('locale', $locales),
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (TeamMember $member) use ($fallbackLocale): array {
                $translation = $member->translations->firstWhere('locale', $this->form['locale'])
                    ?? $member->translations->firstWhere('locale', $fallbackLocale)
                    ?? $member->translations->first();

                $name = trim((string) ($translation?->name ?? '')) ?: $member->code;
                $position = trim((string) ($translation?->position ?? ''));

                return [
                    'id' => (int) $member->id,
                    'label' => $position !== '' ? $name.' - '.$position : $name,
                ];
            });
    }

    public function getSelectedBlogPostsProperty(): Collection
    {
        return $this->manualSelectionRows('blog_posts', $this->blogPostOptions);
    }

    public function getSelectedFaqsProperty(): Collection
    {
        return $this->manualSelectionRows('faqs', $this->faqOptions);
    }

    public function getSelectedTeamMembersProperty(): Collection
    {
        return $this->manualSelectionRows('team_members', $this->teamOptions);
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        $rules = [
            'form.code' => ['required', 'string', 'max:120', Rule::unique('content_service_pages', 'code')->ignore($this->servicePageId)],
            'form.template_key' => ['required', Rule::in(array_keys(ServicePageTemplateRegistry::labels()))],
            'form.is_active' => ['boolean'],
            'form.published_at' => ['nullable', 'date'],
            'form.sort_order' => ['nullable', 'integer', 'min:0'],
            'form.locale' => ['required', 'string', 'max:12'],
            'form.title' => ['required', 'string', 'max:255'],
            'form.slug' => [
                'required',
                'string',
                'max:191',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('content_service_page_translations', 'slug')
                    ->where(fn ($q) => $q->where('locale', $this->form['locale']))
                    ->ignore($this->servicePageId, 'service_page_id'),
            ],
            'form.meta_title' => ['nullable', 'string', 'max:255'],
            'form.meta_description' => ['nullable', 'string'],

            'form.page_payload' => ['nullable', 'array'],
            'form.translation_payload' => ['nullable', 'array'],
            'assetUploads.*' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
        ];

        if ($this->templateSupportsBlogSource()) {
            $rules['form.page_payload.blog_source.mode'] = ['required', Rule::in(['auto_category', 'category', 'manual'])];
            $rules['form.page_payload.blog_source.category_id'] = [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where(fn ($q) => $q->where('scope', Category::SCOPE_BLOG)),
            ];
            $rules['form.page_payload.blog_source.post_ids'] = ['nullable', 'array'];
            $rules['form.page_payload.blog_source.post_ids.*'] = ['integer', Rule::exists('content_blog_posts', 'id')];
            $rules['form.page_payload.blog_source.limit'] = ['nullable', 'integer', 'min:1', 'max:24'];
        }

        if ($this->templateSupportsFaqSource()) {
            $rules['form.page_payload.faq_source.mode'] = ['required', Rule::in(['auto_group', 'group', 'manual'])];
            $rules['form.page_payload.faq_source.group_code'] = ['nullable', 'string', 'max:120'];
            $rules['form.page_payload.faq_source.faq_ids'] = ['nullable', 'array'];
            $rules['form.page_payload.faq_source.faq_ids.*'] = ['integer', Rule::exists('content_faqs', 'id')];
        }

        if ($this->templateSupportsTeamSource()) {
            $rules['form.page_payload.team_source.mode'] = ['required', Rule::in(['auto', 'manual'])];
            $rules['form.page_payload.team_source.member_ids'] = ['nullable', 'array'];
            $rules['form.page_payload.team_source.member_ids.*'] = ['integer', Rule::exists('content_team_members', 'id')];
        }

        if ($this->templateSupportsBrochure()) {
            $rules['form.page_payload.brochure_url'] = ['nullable', 'string', 'max:2048'];
        }

        if ($this->templateSupportsSources()) {
            $rules['form.page_payload.video_source.items'] = ['nullable', 'array'];
            $rules['form.page_payload.video_source.items.*.title'] = ['nullable', 'string', 'max:255'];
            $rules['form.page_payload.video_source.items.*.youtube_url'] = ['nullable', 'string', 'max:2048'];
            $rules['form.translation_payload.video_section.title'] = ['nullable', 'string', 'max:255'];
            $rules['form.translation_payload.video_section.intro'] = ['nullable', 'string'];
        }

        if ((string) ($this->form['template_key'] ?? '') === ServicePageTemplateRegistry::SERVICES_INDEX) {
            $rules['form.translation_payload.showcase.title_lead'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.showcase.intro'] = ['required', 'string'];
            $rules['form.translation_payload.showcase.value_cards'] = ['required', 'array', 'size:2'];
            $rules['form.translation_payload.showcase.value_cards.*.key'] = ['required', Rule::in(['how', 'audience'])];
            $rules['form.translation_payload.showcase.value_cards.*.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.showcase.value_cards.*.items'] = ['required', 'array', 'size:5'];
            $rules['form.translation_payload.showcase.value_cards.*.items.*.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.showcase.value_cards.*.items.*.text'] = ['required', 'string', 'max:1000'];
            $rules['form.translation_payload.showcase.card_action_label'] = ['required', 'string', 'max:80'];
            $rules['form.translation_payload.primary_pillars'] = ['required', 'array', 'size:3'];
            $rules['form.translation_payload.primary_pillars.*.key'] = ['required', Rule::in(['audit', 'accounting', 'advisory'])];
            $rules['form.translation_payload.primary_pillars.*.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.primary_pillars.*.subtitle'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.primary_pillars.*.text'] = ['required', 'string'];
            $rules['form.translation_payload.primary_pillars.*.url'] = ['required', 'string', 'max:2048'];
            $rules['form.translation_payload.primary_pillars.*.image_alt'] = ['required', 'string', 'max:255'];
            $rules['landingImageUploads.*'] = ['nullable', 'file', $this->imageMimesRule(), 'max:8192'];
        }

        if ((string) ($this->form['template_key'] ?? '') === ServicePageTemplateRegistry::AUDIT) {
            $rules['form.translation_payload.hero.subtitle_lead'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.hero.intro'] = ['required', 'string'];
            $rules['form.translation_payload.hero.image_alt'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.overview.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.overview.intro'] = ['nullable', 'string'];
            $rules['form.translation_payload.overview.body'] = ['nullable', 'array'];
            $rules['form.translation_payload.overview.body.*'] = ['nullable', 'string'];
            $rules['form.translation_payload.overview.body_html'] = ['required', 'string'];
            $rules['form.translation_payload.obligors.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.obligors.primary_title'] = ['nullable', 'string', 'max:255'];
            $rules['form.translation_payload.obligors.primary_items'] = ['required', 'array', 'min:1'];
            $rules['form.translation_payload.obligors.primary_items.*.children_text'] = ['nullable', 'string'];
            $rules['form.translation_payload.obligors.note'] = ['nullable', 'string'];
            $rules['form.translation_payload.services.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.services.intro'] = ['nullable', 'string'];
            $rules['form.translation_payload.services.items'] = ['required', 'array', 'min:1'];
            $rules['form.translation_payload.services.items.*.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.services.items.*.text'] = ['required', 'string'];
            $rules['form.translation_payload.approach.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.approach.body'] = ['nullable', 'array'];
            $rules['form.translation_payload.approach.body.*'] = ['nullable', 'string'];
            $rules['form.translation_payload.approach.body_html'] = ['required', 'string'];
            $rules['form.translation_payload.blog_section.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.blog_section.all_posts_label'] = ['required', 'string', 'max:80'];
            $rules['form.translation_payload.blog_section.post_action_label'] = ['required', 'string', 'max:80'];
            $rules['form.translation_payload.meeting.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.meeting.contact_title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.meeting.intro'] = ['required', 'string'];
            $rules['form.translation_payload.meeting.button_label'] = ['required', 'string', 'max:80'];
            $rules['form.translation_payload.meeting.status'] = ['required', 'string', 'max:255'];
            $rules['auditHeroImageUpload'] = ['nullable', 'file', $this->imageMimesRule(includeSvg: true), 'max:8192'];
        }

        if ((string) ($this->form['template_key'] ?? '') === ServicePageTemplateRegistry::ACCOUNTING) {
            $rules['form.translation_payload.hero.subtitle_lead'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.hero.intro'] = ['required', 'string'];
            $rules['form.translation_payload.hero.image_alt'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.overview.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.overview.intro'] = ['nullable', 'string'];
            $rules['form.translation_payload.overview.body'] = ['nullable', 'array'];
            $rules['form.translation_payload.overview.body.0'] = ['nullable', 'string'];
            $rules['form.translation_payload.overview.body.*'] = ['nullable', 'string'];
            $rules['form.translation_payload.overview.body_html'] = ['required', 'string'];
            $rules['form.translation_payload.overview.partner_body_html'] = ['nullable', 'string'];
            $rules['form.translation_payload.services.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.services.intro'] = ['nullable', 'string'];
            $rules['form.translation_payload.services.items'] = ['required', 'array', 'min:1'];
            $rules['form.translation_payload.services.items.*.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.services.items.*.text'] = ['required', 'string'];
            $rules['form.translation_payload.approach.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.approach.body'] = ['nullable', 'array'];
            $rules['form.translation_payload.approach.body.*'] = ['nullable', 'string'];
            $rules['form.translation_payload.approach.body_html'] = ['required', 'string'];
            $rules['form.translation_payload.blog_section.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.blog_section.all_posts_label'] = ['required', 'string', 'max:80'];
            $rules['form.translation_payload.blog_section.post_action_label'] = ['required', 'string', 'max:80'];
            $rules['form.translation_payload.meeting.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.meeting.contact_title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.meeting.intro'] = ['required', 'string'];
            $rules['form.translation_payload.meeting.button_label'] = ['required', 'string', 'max:80'];
            $rules['form.translation_payload.meeting.status'] = ['required', 'string', 'max:255'];
            $rules['accountingHeroImageUpload'] = ['nullable', 'file', $this->imageMimesRule(includeSvg: true), 'max:8192'];
        }

        if ((string) ($this->form['template_key'] ?? '') === ServicePageTemplateRegistry::ADVISORY) {
            if ($this->contentSection === 'main') {
                $rules['form.translation_payload.hero.subtitle_lead'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.hero.intro'] = ['required', 'string'];
                $rules['form.translation_payload.hero.image_alt'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.overview.title'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.overview.body'] = ['nullable', 'array'];
                $rules['form.translation_payload.overview.body.*'] = ['nullable', 'string'];
                $rules['form.translation_payload.overview.body_html'] = ['required', 'string'];
                $rules['form.translation_payload.pandea.title'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.pandea.logo_alt'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.pandea.body'] = ['nullable', 'array'];
                $rules['form.translation_payload.pandea.body.*'] = ['nullable', 'string'];
                $rules['form.translation_payload.pandea.body_html'] = ['required', 'string'];
                $rules['form.translation_payload.services_intro.title'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.services_intro.intro'] = ['nullable', 'string'];
                $rules['form.translation_payload.services_intro.card_action_label'] = ['required', 'string', 'max:80'];
                $rules['form.translation_payload.service_cards'] = ['required', 'array', 'min:1'];
                $rules['form.translation_payload.service_cards.*.title'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.service_cards.*.text'] = ['required', 'string'];
                $rules['form.translation_payload.service_cards.*.url'] = ['required', 'string', 'max:2048'];
                $rules['form.translation_payload.approach.title'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.approach.body'] = ['nullable', 'array'];
                $rules['form.translation_payload.approach.body.*'] = ['nullable', 'string'];
                $rules['form.translation_payload.approach.body_html'] = ['nullable', 'string'];
                $rules['form.translation_payload.blog_section.title'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.blog_section.all_posts_label'] = ['required', 'string', 'max:80'];
                $rules['form.translation_payload.blog_section.post_action_label'] = ['required', 'string', 'max:80'];
                $rules['form.translation_payload.meeting.title'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.meeting.contact_title'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.meeting.intro'] = ['required', 'string'];
                $rules['form.translation_payload.meeting.button_label'] = ['required', 'string', 'max:80'];
                $rules['form.translation_payload.meeting.status'] = ['required', 'string', 'max:255'];
                $rules['advisoryHeroImageUpload'] = ['nullable', 'file', $this->imageMimesRule(includeSvg: true), 'max:8192'];
                $rules['advisoryPandeaLogoUpload'] = ['nullable', 'file', $this->imageMimesRule(includeSvg: true), 'max:4096'];
            } elseif ($this->contentSection === 'funding') {
                $rules['form.translation_payload.funding.title'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.funding.intro'] = ['required', 'string'];
                $rules['form.translation_payload.funding.hero_intro'] = ['required', 'string'];
                $rules['form.translation_payload.funding.hero_image_alt'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.funding.cards'] = ['nullable', 'array'];
                $rules['form.translation_payload.funding.cards.*.title'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.funding.cards.*.text'] = ['required', 'string'];
                $rules['form.translation_payload.funding.cards.*.url'] = ['required', 'string', 'max:2048'];
                $rules['form.translation_payload.funding.approach_title'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.funding.approach_body'] = ['nullable', 'array'];
                $rules['form.translation_payload.funding.approach_body.*'] = ['nullable', 'string'];
                $rules['form.translation_payload.funding.approach_body_html'] = ['nullable', 'string'];
                $rules['form.translation_payload.source_modules.title'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.source_modules.intro'] = ['nullable', 'string'];
            } else {
                $detailKey = $this->contentSection;
                $rules['form.translation_payload.'.$detailKey.'.title'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.'.$detailKey.'.hero_intro'] = ['required', 'string'];
                $rules['form.translation_payload.'.$detailKey.'.hero_image_alt'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.'.$detailKey.'.overview_title'] = ['nullable', 'string', 'max:255'];
                $rules['form.translation_payload.'.$detailKey.'.overview_body'] = ['nullable', 'array'];
                $rules['form.translation_payload.'.$detailKey.'.overview_body.*'] = ['nullable', 'string'];
                $rules['form.translation_payload.'.$detailKey.'.overview_body_html'] = ['nullable', 'string'];
                $rules['form.translation_payload.'.$detailKey.'.services_title'] = ['nullable', 'string', 'max:255'];
                $rules['form.translation_payload.'.$detailKey.'.services_body'] = ['nullable', 'array'];
                $rules['form.translation_payload.'.$detailKey.'.services_body.*'] = ['nullable', 'string'];
                $rules['form.translation_payload.'.$detailKey.'.services_body_html'] = ['nullable', 'string'];
                $rules['form.translation_payload.'.$detailKey.'.help_items'] = ['nullable', 'array'];
                $rules['form.translation_payload.'.$detailKey.'.help_items.*'] = ['nullable', 'string'];
                $rules['form.translation_payload.'.$detailKey.'.help_items_text'] = ['nullable', 'string'];
                $rules['form.translation_payload.'.$detailKey.'.approach_title'] = ['nullable', 'string', 'max:255'];
                $rules['form.translation_payload.'.$detailKey.'.approach_body'] = ['nullable', 'array'];
                $rules['form.translation_payload.'.$detailKey.'.approach_body.*'] = ['nullable', 'string'];
                $rules['form.translation_payload.'.$detailKey.'.approach_body_html'] = ['nullable', 'string'];

                if ($detailKey === 'ma') {
                    $rules['form.translation_payload.ma.show_pandea'] = ['boolean'];
                    $rules['form.translation_payload.ma.pandea.title'] = ['required', 'string', 'max:255'];
                    $rules['form.translation_payload.ma.pandea.logo_alt'] = ['required', 'string', 'max:255'];
                    $rules['form.translation_payload.ma.pandea.body'] = ['nullable', 'array'];
                    $rules['form.translation_payload.ma.pandea.body.*'] = ['nullable', 'string'];
                    $rules['form.translation_payload.ma.pandea.body_html'] = ['required', 'string'];
                }
            }

            if ($this->contentSection !== 'main') {
                $pageKey = $this->contentSection;
                $rules['form.translation_payload.'.$pageKey.'.meta_title'] = ['nullable', 'string', 'max:255'];
                $rules['form.translation_payload.'.$pageKey.'.meta_description'] = ['nullable', 'string', 'max:320'];
                $rules['form.translation_payload.'.$pageKey.'.blog_section.title'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.'.$pageKey.'.blog_section.all_posts_label'] = ['required', 'string', 'max:80'];
                $rules['form.translation_payload.'.$pageKey.'.blog_section.post_action_label'] = ['required', 'string', 'max:80'];
                $rules['form.translation_payload.'.$pageKey.'.meeting.title'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.'.$pageKey.'.meeting.contact_title'] = ['required', 'string', 'max:255'];
                $rules['form.translation_payload.'.$pageKey.'.meeting.intro'] = ['required', 'string'];
                $rules['form.translation_payload.'.$pageKey.'.meeting.button_label'] = ['required', 'string', 'max:80'];
                $rules['form.translation_payload.'.$pageKey.'.meeting.status'] = ['required', 'string', 'max:255'];
            }
        }

        if ((string) ($this->form['template_key'] ?? '') === ServicePageTemplateRegistry::EU_FUNDS) {
            $rules['form.translation_payload.hero.subtitle_lead'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.hero.intro'] = ['required', 'string'];
            $rules['form.translation_payload.hero.image_alt'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.overview.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.overview.body'] = ['nullable', 'array'];
            $rules['form.translation_payload.overview.body.*'] = ['nullable', 'string'];
            $rules['form.translation_payload.overview.body_html'] = ['required', 'string'];
            $rules['form.translation_payload.process.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.process.intro'] = ['nullable', 'string'];
            $rules['form.translation_payload.process.items'] = ['required', 'array', 'min:1'];
            $rules['form.translation_payload.process.items.*.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.process.items.*.text'] = ['required', 'string'];
            $rules['form.translation_payload.approach.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.approach.body'] = ['nullable', 'array'];
            $rules['form.translation_payload.approach.body.*'] = ['nullable', 'string'];
            $rules['form.translation_payload.approach.body_html'] = ['required', 'string'];
            $rules['form.translation_payload.source_modules.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.source_modules.intro'] = ['nullable', 'string'];
            $rules['form.translation_payload.source_modules.items'] = ['required', 'array', 'min:1'];
            $rules['form.translation_payload.source_modules.items.*.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.source_modules.items.*.text'] = ['required', 'string'];
            $rules['form.translation_payload.source_modules.items.*.url'] = ['required', 'string', 'max:2048'];
            $rules['form.translation_payload.calls.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.calls.intro'] = ['nullable', 'string'];
            $rules['form.translation_payload.calls.view_all_label'] = ['required', 'string', 'max:80'];
            $pdfLocaleRule = [
                'nullable',
                'string',
                'max:8',
                Rule::in($this->activeContentLocaleOptions()),
            ];
            $rules['form.translation_payload.calls.download_link.locale'] = $pdfLocaleRule;
            $rules['form.translation_payload.calls.other_calls.title'] = ['nullable', 'string', 'max:255'];
            $rules['form.translation_payload.calls.other_calls.intro'] = ['nullable', 'string'];
            $rules['form.translation_payload.calls.other_calls.items.*.title'] = ['nullable', 'string', 'max:255'];
            $rules['form.translation_payload.calls.other_calls.items.*.link.locale'] = $pdfLocaleRule;
            $rules['form.translation_payload.resources.cards.*.primary_link.locale'] = $pdfLocaleRule;
            $rules['form.translation_payload.resources.cards.*.secondary_link.locale'] = $pdfLocaleRule;
            $rules['form.translation_payload.resources.cards.*.groups.*.items.*.link.locale'] = $pdfLocaleRule;
            $rules['form.translation_payload.laws.cards.*.primary_link.locale'] = $pdfLocaleRule;
            $rules['form.translation_payload.laws.cards.*.secondary_link.locale'] = $pdfLocaleRule;
            $rules['form.translation_payload.resources.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.resources.intro'] = ['nullable', 'string'];
            $rules['form.translation_payload.resources.cards.*.body_html'] = ['nullable', 'string'];
            $rules['form.translation_payload.laws.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.laws.intro'] = ['nullable', 'string'];
            $rules['form.translation_payload.laws.cards.*.lists.*.items_text'] = ['nullable', 'string'];
            $rules['form.translation_payload.blog_section.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.blog_section.all_posts_label'] = ['required', 'string', 'max:80'];
            $rules['form.translation_payload.blog_section.post_action_label'] = ['required', 'string', 'max:80'];
            $rules['form.translation_payload.meeting.title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.meeting.intro'] = ['required', 'string'];
            $rules['form.translation_payload.meeting.contact_title'] = ['required', 'string', 'max:255'];
            $rules['form.translation_payload.meeting.button_label'] = ['required', 'string', 'max:80'];
            $rules['form.translation_payload.meeting.status'] = ['required', 'string', 'max:255'];
            $rules['euFundsHeroImageUpload'] = ['nullable', 'file', $this->imageMimesRule(includeSvg: true), 'max:8192'];
        }

        return $this->relaxTranslationPayloadRules($rules);
    }

    /**
     * Missing or intentionally partial non-default translations may be saved
     * without PHP defaults filling every section. Present values still retain
     * their type, length and allow-list validation.
     *
     * @param  array<string, array<int, mixed>>  $rules
     * @return array<string, array<int, mixed>>
     */
    private function relaxTranslationPayloadRules(array $rules): array
    {
        foreach ($rules as $field => $fieldRules) {
            if (! str_starts_with($field, 'form.translation_payload.')) {
                continue;
            }

            $fieldRules = array_values(array_filter(
                $fieldRules,
                static fn ($rule): bool => ! is_string($rule)
                    || ($rule !== 'required' && ! str_starts_with($rule, 'min:') && ! str_starts_with($rule, 'size:'))
            ));

            if (! in_array('nullable', $fieldRules, true)) {
                array_unshift($fieldRules, 'nullable');
            }

            $rules[$field] = $fieldRules;
        }

        return $rules;
    }

    private function loadServicePage(): void
    {
        if (! $this->servicePageId) {
            return;
        }

        $servicePage = ServicePage::query()
            ->with('translations')
            ->findOrFail($this->servicePageId);

        $preferredLocale = (string) ($this->form['locale'] ?: AdminLocale::default());
        $translation = $servicePage->translations->firstWhere('locale', $preferredLocale);

        $this->form['code'] = $servicePage->code;
        $this->form['template_key'] = $servicePage->template_key;
        $this->form['is_active'] = (bool) $servicePage->is_active;
        $this->form['published_at'] = $servicePage->published_at?->format('Y-m-d\TH:i') ?? '';
        $this->form['sort_order'] = (int) $servicePage->sort_order;
        $this->form['page_payload'] = ServicePageTemplateRegistry::mergePagePayload(
            $servicePage->template_key,
            $servicePage->payload
        );
        $this->assetUploads = [];
        $this->landingImageUploads = [];
        $this->auditHeroImageUpload = null;
        $this->accountingHeroImageUpload = null;
        $this->advisoryHeroImageUpload = null;
        $this->advisoryPandeaLogoUpload = null;
        $this->euFundsHeroImageUpload = null;

        if ($translation) {
            $this->form['title'] = $translation->title;
            $this->form['slug'] = $translation->slug;
            $this->form['meta_title'] = $translation->meta_title ?? '';
            $this->form['meta_description'] = $translation->meta_description ?? '';
            $this->setTranslationPayloadState(
                $servicePage->template_key,
                $translation->payload,
                $preferredLocale,
            );
        } else {
            $this->clearTranslationFields($servicePage->template_key);
        }
    }

    private function loadTranslationForLocale(): void
    {
        $templateKey = (string) $this->form['template_key'];

        if (! $this->servicePageId) {
            $this->clearTranslationFields($templateKey);

            return;
        }

        $translation = ServicePageTranslation::query()
            ->where('service_page_id', $this->servicePageId)
            ->where('locale', $this->form['locale'])
            ->first();

        if (! $translation) {
            $this->clearTranslationFields($templateKey);

            return;
        }

        $this->form['title'] = $translation->title;
        $this->form['slug'] = $translation->slug;
        $this->form['meta_title'] = $translation->meta_title ?? '';
        $this->form['meta_description'] = $translation->meta_description ?? '';
        $this->setTranslationPayloadState(
            $templateKey,
            $translation->payload,
            (string) $translation->locale,
        );
        $this->assetUploads = [];
        $this->landingImageUploads = [];
        $this->auditHeroImageUpload = null;
        $this->accountingHeroImageUpload = null;
        $this->advisoryHeroImageUpload = null;
        $this->advisoryPandeaLogoUpload = null;
        $this->euFundsHeroImageUpload = null;
    }

    private function clearTranslationFields(string $templateKey): void
    {
        $locale = (string) ($this->form['locale'] ?? config('app.locale', 'en'));
        $defaults = $this->defaultTranslationFields($templateKey, $locale);

        $this->form['title'] = $defaults['title'];
        $this->form['slug'] = $defaults['slug'];
        $this->form['meta_title'] = $defaults['meta_title'];
        $this->form['meta_description'] = $defaults['meta_description'];
        $this->setTranslationPayloadState($templateKey, null, $locale);
        $this->assetUploads = [];
        $this->landingImageUploads = [];
        $this->auditHeroImageUpload = null;
        $this->accountingHeroImageUpload = null;
        $this->advisoryHeroImageUpload = null;
        $this->advisoryPandeaLogoUpload = null;
        $this->euFundsHeroImageUpload = null;
    }

    private function initializeTemplateDefaults(string $templateKey): void
    {
        $this->form['template_key'] = $templateKey;
        $this->form['code'] = $this->form['code'] !== ''
            ? $this->form['code']
            : ServicePageTemplateRegistry::defaultCode($templateKey);
        $this->form['page_payload'] = ServicePageTemplateRegistry::defaultPagePayload($templateKey);
        $this->setTranslationPayloadState(
            $templateKey,
            null,
            (string) ($this->form['locale'] ?? config('app.locale', 'en')),
        );

        if (! $this->templateSupportsSources($templateKey) && $this->activeTab === 'sources') {
            $this->activeTab = 'content';
        }
    }

    private function setTranslationPayloadState(string $templateKey, ?array $payload, string $locale): void
    {
        $source = is_array($payload) ? $payload : [];
        $editorPayload = $this->copyFreeEditorPayload($templateKey, $source, $locale);

        $this->loadedTranslationPayload = $source;
        $this->translationPayloadBaseline = $editorPayload;
        $this->form['translation_payload'] = $editorPayload;
    }

    /**
     * @param  array<string, mixed>  $source
     * @return array<string, mixed>
     */
    private function copyFreeEditorPayload(string $templateKey, array $source, string $locale): array
    {
        $structure = ServicePageTemplateRegistry::blankTranslationPayload($templateKey, $locale);
        $merged = $this->mergeEditorStructure($structure, $source);

        return ServicePageTemplateRegistry::hydrateStructuredEditorFields(
            $templateKey,
            $merged,
            $source,
        );
    }

    /**
     * Merge exact CMS data into the copy-free editor structure, including list
     * items by index so fixed card editors remain usable.
     *
     * @param  array<string|int, mixed>  $structure
     * @param  array<string|int, mixed>  $source
     * @return array<string|int, mixed>
     */
    private function mergeEditorStructure(array $structure, array $source): array
    {
        foreach ($source as $key => $value) {
            if (array_key_exists($key, $structure) && is_array($structure[$key]) && is_array($value)) {
                $structure[$key] = $this->mergeEditorStructure($structure[$key], $value);

                continue;
            }

            $structure[$key] = $value;
        }

        return $structure;
    }

    /**
     * @param  array<string|int, mixed>  $current
     * @param  array<string|int, mixed>  $baseline
     * @param  array<string|int, mixed>  $original
     * @return array<string|int, mixed>
     */
    private function applyTranslationArrayChanges(array $current, array $baseline, array $original): array
    {
        $result = $original;
        $keys = array_unique([...array_keys($baseline), ...array_keys($current)]);

        foreach ($keys as $key) {
            $hasCurrent = array_key_exists($key, $current);
            $hasBaseline = array_key_exists($key, $baseline);
            $hasOriginal = array_key_exists($key, $original);

            if (! $hasCurrent) {
                unset($result[$key]);

                continue;
            }

            if (! $hasBaseline) {
                $result[$key] = $current[$key];

                continue;
            }

            if (is_array($current[$key]) && is_array($baseline[$key])) {
                if ($hasOriginal || $current[$key] !== $baseline[$key]) {
                    $result[$key] = $this->applyTranslationArrayChanges(
                        $current[$key],
                        $baseline[$key],
                        $hasOriginal && is_array($original[$key]) ? $original[$key] : [],
                    );
                } else {
                    unset($result[$key]);
                }

                continue;
            }

            if ($current[$key] !== $baseline[$key]) {
                $result[$key] = $current[$key];
            }
        }

        if (array_is_list($current) && $result !== []) {
            $numericKeys = array_filter(array_keys($result), 'is_int');
            if ($numericKeys !== []) {
                for ($index = 0, $lastIndex = max($numericKeys); $index <= $lastIndex; $index++) {
                    $result[$index] ??= [];
                }
                ksort($result);
                $result = array_values($result);
            }
        }

        return $result;
    }

    /**
     * @return array{title: string, slug: string, meta_title: string, meta_description: string}
     */
    private function defaultTranslationFields(string $templateKey, string $locale): array
    {
        return [
            'title' => '',
            'slug' => '',
            'meta_title' => '',
            'meta_description' => '',
        ];
    }

    private function moveManualItem(string $target, int $index, int $direction): void
    {
        $path = $this->manualSelectionPath($target);
        if ($path === null) {
            return;
        }

        $rows = array_values(array_map(
            static fn ($value): int => (int) $value,
            (array) data_get($this->form, $path, [])
        ));

        $swapIndex = $index + $direction;
        if ($index < 0 || $index >= count($rows) || $swapIndex < 0 || $swapIndex >= count($rows)) {
            return;
        }

        [$rows[$index], $rows[$swapIndex]] = [$rows[$swapIndex], $rows[$index]];
        data_set($this->form, $path, $rows);
    }

    private function moveVideoSource(int $index, int $direction): void
    {
        $rows = array_values((array) data_get($this->form, 'page_payload.video_source.items', []));

        $swapIndex = $index + $direction;
        if ($index < 0 || $index >= count($rows) || $swapIndex < 0 || $swapIndex >= count($rows)) {
            return;
        }

        [$rows[$index], $rows[$swapIndex]] = [$rows[$swapIndex], $rows[$index]];
        data_set($this->form, 'page_payload.video_source.items', array_values($rows));
    }

    private function manualSelectionPath(string $target): ?string
    {
        return self::MANUAL_SELECTION_PATHS[$target] ?? null;
    }

    private function manualSelectionRows(string $target, Collection $options): Collection
    {
        $path = $this->manualSelectionPath($target);
        if ($path === null) {
            return collect();
        }

        $optionsById = $options->keyBy('id');

        return collect((array) data_get($this->form, $path, []))
            ->map(fn ($id): int => (int) $id)
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

    private function translationListItemPreset(string $preset): mixed
    {
        return match ($preset) {
            'eu_chart_stat' => [
                'label' => '',
                'value' => '',
                'share' => 0,
                'description' => '',
            ],
            'eu_process_item' => [
                'title' => '',
                'text' => '',
            ],
            'phase' => [
                'title' => '',
                'label' => '',
                'items' => [''],
            ],
            'download_item' => [
                'title' => '',
                'url' => '',
                'label' => '',
            ],
            'service_card' => [
                'title' => '',
                'text' => '',
                'url' => '',
            ],
            'eu_funds_link_item' => [
                'title' => '',
                'link' => [
                    'label' => '',
                    'type' => 'none',
                    'url' => '',
                    'slug' => '',
                    'locale' => '',
                    'path' => '',
                ],
            ],
            'eu_funds_resource_card' => [
                'eyebrow' => '',
                'title' => '',
                'body_html' => '',
                'groups' => [],
                'primary_link' => ['label' => '', 'type' => 'none'],
                'secondary_link' => ['label' => '', 'type' => 'none'],
            ],
            'title_text' => [
                'title' => '',
                'text' => '',
            ],
            'audit_obligor' => [
                'text' => '',
                'children' => [],
            ],
            default => '',
        };
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>|false
     */
    private function normalizedPagePayload(array $payload): array|false
    {
        $merged = ServicePageTemplateRegistry::mergePagePayload((string) $this->form['template_key'], $payload);

        if (! $this->templateSupportsSources()) {
            return $merged;
        }

        $videoSourceItems = $this->normalizeVideoSourceItems((array) data_get($merged, 'video_source.items', []));
        if ($videoSourceItems === false) {
            return false;
        }

        data_set($merged, 'video_source.items', $videoSourceItems);

        if ($this->templateSupportsBlogSource()) {
            data_set($merged, 'blog_source.category_id', $this->nullableInt(data_get($merged, 'blog_source.category_id')));
            data_set($merged, 'blog_source.limit', max(1, min(24, (int) data_get($merged, 'blog_source.limit', 6))));
            data_set($merged, 'blog_source.post_ids', $this->normalizeIdList((array) data_get($merged, 'blog_source.post_ids', [])));
        }

        if ($this->templateSupportsFaqSource()) {
            data_set($merged, 'faq_source.group_code', trim((string) data_get($merged, 'faq_source.group_code', '')));
            data_set($merged, 'faq_source.faq_ids', $this->normalizeIdList((array) data_get($merged, 'faq_source.faq_ids', [])));
        }

        if ($this->templateSupportsTeamSource()) {
            data_set($merged, 'team_source.member_ids', $this->normalizeIdList((array) data_get($merged, 'team_source.member_ids', [])));
        }

        if ($this->templateSupportsBrochure()) {
            data_set($merged, 'brochure_url', trim((string) data_get($merged, 'brochure_url', '')));
        }

        return $merged;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function normalizedTranslationPayload(string $templateKey, array $payload): array
    {
        if ($templateKey === ServicePageTemplateRegistry::EU_FUNDS) {
            $payload = $this->applyEuFundsAssetUploads($payload);
        }

        $payload = ServicePageTemplateRegistry::hydrateStructuredEditorFields(
            $templateKey,
            $payload,
            $payload,
        );
        $baseline = ServicePageTemplateRegistry::hydrateStructuredEditorFields(
            $templateKey,
            $this->translationPayloadBaseline,
            $this->translationPayloadBaseline,
        );

        return $this->applyTranslationArrayChanges(
            $payload,
            $baseline,
            $this->loadedTranslationPayload,
        );
    }

    /**
     * @return array<string, array{url: string, is_custom: bool}>
     */
    private function servicesIndexCardImages(): array
    {
        $fallbacks = [
            'audit' => asset('alpha/service-revizija.jpg'),
            'accounting' => asset('alpha/service-racunovodstvo.jpg'),
            'advisory' => asset('alpha/service-savjetovanje.jpg'),
        ];
        $servicePage = $this->servicePageId
            ? ServicePage::query()->find($this->servicePageId)
            : null;

        return collect(ServicePageTemplateRegistry::SERVICES_INDEX_CARD_MEDIA_COLLECTIONS)
            ->mapWithKeys(function (string $collection, string $cardKey) use ($fallbacks, $servicePage): array {
                $media = $servicePage?->template_key === ServicePageTemplateRegistry::SERVICES_INDEX
                    ? $servicePage->getFirstMedia($collection)
                    : null;
                $url = $media
                    ? ($media->hasGeneratedConversion('services_index_card_1080x1350')
                        ? $media->getUrl('services_index_card_1080x1350')
                        : $media->getUrl())
                    : (string) ($fallbacks[$cardKey] ?? '');

                return [
                    $cardKey => [
                        'url' => $url,
                        'is_custom' => (bool) $media,
                    ],
                ];
            })
            ->all();
    }

    /**
     * @return array{url: string, is_custom: bool}
     */
    private function auditHeroImage(): array
    {
        $servicePage = $this->servicePageId
            ? ServicePage::query()->find($this->servicePageId)
            : null;
        $media = $servicePage?->template_key === ServicePageTemplateRegistry::AUDIT
            ? $servicePage->getFirstMedia('service_hero_image')
            : null;

        return [
            'url' => $media
                ? $media->getUrl()
                : asset('front-theme/images/services/audit-editorial-3d.svg'),
            'is_custom' => (bool) $media,
        ];
    }

    /**
     * @return array{url: string, is_custom: bool}
     */
    private function accountingHeroImage(): array
    {
        $servicePage = $this->servicePageId
            ? ServicePage::query()->find($this->servicePageId)
            : null;
        $media = $servicePage?->template_key === ServicePageTemplateRegistry::ACCOUNTING
            ? $servicePage->getFirstMedia('service_hero_image')
            : null;

        return [
            'url' => $media
                ? $media->getUrl()
                : asset('front-theme/images/services/accounting-editorial-3d.svg'),
            'is_custom' => (bool) $media,
        ];
    }

    /**
     * @return array{url: string, is_custom: bool}
     */
    private function advisoryHeroImage(): array
    {
        return $this->advisoryMediaPreview(
            'service_hero_image',
            'front-theme/images/services/advisory-editorial-3d.svg'
        );
    }

    /**
     * @return array{url: string, is_custom: bool}
     */
    private function advisoryPandeaLogo(): array
    {
        return $this->advisoryMediaPreview(
            'service_logo',
            'front-theme/images/logos/pandea-global-ma-logo.png'
        );
    }

    /**
     * @return array{url: string, is_custom: bool}
     */
    private function euFundsHeroImage(): array
    {
        $servicePage = $this->servicePageId
            ? ServicePage::query()->find($this->servicePageId)
            : null;
        $media = $servicePage?->template_key === ServicePageTemplateRegistry::EU_FUNDS
            ? $servicePage->getFirstMedia('service_hero_image')
            : null;

        return [
            'url' => $media
                ? $media->getUrl()
                : asset('front-theme/images/services/advisory-editorial-3d.svg'),
            'is_custom' => (bool) $media,
        ];
    }

    /**
     * @return array{url: string, is_custom: bool}
     */
    private function advisoryMediaPreview(string $collection, string $fallback): array
    {
        $servicePage = $this->servicePageId
            ? ServicePage::query()->find($this->servicePageId)
            : null;
        $media = $servicePage?->template_key === ServicePageTemplateRegistry::ADVISORY
            ? $servicePage->getFirstMedia($collection)
            : null;

        return [
            'url' => $media ? $media->getUrl() : asset($fallback),
            'is_custom' => (bool) $media,
        ];
    }

    private function storeServicesIndexCardImages(ServicePage $servicePage): void
    {
        if ($servicePage->template_key !== ServicePageTemplateRegistry::SERVICES_INDEX) {
            return;
        }

        foreach (ServicePageTemplateRegistry::SERVICES_INDEX_CARD_MEDIA_COLLECTIONS as $cardKey => $collection) {
            $upload = $this->landingImageUploads[$cardKey] ?? null;
            if (! $upload instanceof TemporaryUploadedFile) {
                continue;
            }

            $originalName = (string) pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME);
            $safeBaseName = Str::slug($originalName) ?: $cardKey.'-card';
            $extension = strtolower($upload->getClientOriginalExtension() ?: 'jpg');

            $servicePage->addMedia($upload->getRealPath())
                ->usingName($originalName !== '' ? $originalName : $safeBaseName)
                ->usingFileName($safeBaseName.'-'.Str::lower(Str::random(6)).'.'.$extension)
                ->toMediaCollection($collection);
        }

        $this->landingImageUploads = [];
    }

    private function storeAuditHeroImage(ServicePage $servicePage): void
    {
        if (
            $servicePage->template_key !== ServicePageTemplateRegistry::AUDIT
            || ! $this->auditHeroImageUpload instanceof TemporaryUploadedFile
        ) {
            return;
        }

        $originalName = (string) pathinfo($this->auditHeroImageUpload->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = Str::slug($originalName) ?: 'revizija-hero';
        $extension = strtolower($this->auditHeroImageUpload->getClientOriginalExtension() ?: 'jpg');

        $servicePage->addMedia($this->auditHeroImageUpload->getRealPath())
            ->usingName($originalName !== '' ? $originalName : $safeBaseName)
            ->usingFileName($safeBaseName.'-'.Str::lower(Str::random(6)).'.'.$extension)
            ->toMediaCollection('service_hero_image');

        $this->auditHeroImageUpload = null;
    }

    private function storeAccountingHeroImage(ServicePage $servicePage): void
    {
        if (
            $servicePage->template_key !== ServicePageTemplateRegistry::ACCOUNTING
            || ! $this->accountingHeroImageUpload instanceof TemporaryUploadedFile
        ) {
            return;
        }

        $originalName = (string) pathinfo($this->accountingHeroImageUpload->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = Str::slug($originalName) ?: 'racunovodstvo-hero';
        $extension = strtolower($this->accountingHeroImageUpload->getClientOriginalExtension() ?: 'jpg');

        $servicePage->addMedia($this->accountingHeroImageUpload->getRealPath())
            ->usingName($originalName !== '' ? $originalName : $safeBaseName)
            ->usingFileName($safeBaseName.'-'.Str::lower(Str::random(6)).'.'.$extension)
            ->toMediaCollection('service_hero_image');

        $this->accountingHeroImageUpload = null;
    }

    private function storeAdvisoryMedia(ServicePage $servicePage): void
    {
        if ($servicePage->template_key !== ServicePageTemplateRegistry::ADVISORY) {
            return;
        }

        $this->storeAdvisoryUpload(
            $servicePage,
            $this->advisoryHeroImageUpload,
            'service_hero_image',
            'savjetovanje-hero'
        );
        $this->storeAdvisoryUpload(
            $servicePage,
            $this->advisoryPandeaLogoUpload,
            'service_logo',
            'pandea-logo'
        );

        $this->advisoryHeroImageUpload = null;
        $this->advisoryPandeaLogoUpload = null;
    }

    private function storeEuFundsHeroImage(ServicePage $servicePage): void
    {
        if (
            $servicePage->template_key !== ServicePageTemplateRegistry::EU_FUNDS
            || ! $this->euFundsHeroImageUpload instanceof TemporaryUploadedFile
        ) {
            return;
        }

        $originalName = (string) pathinfo($this->euFundsHeroImageUpload->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = Str::slug($originalName) ?: 'eu-fondovi-hero';
        $extension = strtolower($this->euFundsHeroImageUpload->getClientOriginalExtension() ?: 'jpg');

        $servicePage->addMedia($this->euFundsHeroImageUpload->getRealPath())
            ->usingName($originalName !== '' ? $originalName : $safeBaseName)
            ->usingFileName($safeBaseName.'-'.Str::lower(Str::random(6)).'.'.$extension)
            ->toMediaCollection('service_hero_image');

        $this->euFundsHeroImageUpload = null;
    }

    private function storeAdvisoryUpload(
        ServicePage $servicePage,
        ?TemporaryUploadedFile $upload,
        string $collection,
        string $fallbackName
    ): void {
        if (! $upload instanceof TemporaryUploadedFile) {
            return;
        }

        $originalName = (string) pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = Str::slug($originalName) ?: $fallbackName;
        $extension = strtolower($upload->getClientOriginalExtension() ?: 'jpg');

        $servicePage->addMedia($upload->getRealPath())
            ->usingName($originalName !== '' ? $originalName : $safeBaseName)
            ->usingFileName($safeBaseName.'-'.Str::lower(Str::random(6)).'.'.$extension)
            ->toMediaCollection($collection);
    }

    private function removeAdvisoryMedia(string $collection, string $message): void
    {
        if (! $this->servicePageId) {
            return;
        }

        $servicePage = ServicePage::query()->find($this->servicePageId);
        if (! $servicePage || $servicePage->template_key !== ServicePageTemplateRegistry::ADVISORY) {
            return;
        }

        $servicePage->clearMediaCollection($collection);
        $this->dispatch('notify', type: 'success', message: $message);
    }

    /**
     * @param  array<int, mixed>  $items
     * @return array<int, int>
     */
    private function normalizeIdList(array $items): array
    {
        return collect($items)
            ->map(fn ($id): int => (int) $id)
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, mixed>  $items
     * @return array<int, array{title:string, youtube_url:string}>|false
     */
    private function normalizeVideoSourceItems(array $items): array|false
    {
        $normalized = [];
        $hasErrors = false;

        foreach (array_values($items) as $index => $item) {
            $title = trim((string) data_get($item, 'title', ''));
            $youtubeUrl = trim((string) data_get($item, 'youtube_url', ''));

            if ($title === '' && $youtubeUrl === '') {
                continue;
            }

            if ($youtubeUrl === '') {
                $this->addError("form.page_payload.video_source.items.$index.youtube_url", __('YouTube URL je obavezan ako je red popunjen.'));
                $hasErrors = true;

                continue;
            }

            $parsed = YouTubeUrl::parse($youtubeUrl);

            if ($parsed === null) {
                $this->addError("form.page_payload.video_source.items.$index.youtube_url", __('Podržani su samo valjani YouTube linkovi.'));
                $hasErrors = true;

                continue;
            }

            $normalized[] = [
                'title' => $title,
                'youtube_url' => $parsed['watch_url'],
            ];
        }

        if ($hasErrors) {
            $this->dispatch('notify', type: 'danger', message: __('Provjeri unesene YouTube linkove.'));

            return false;
        }

        return $normalized;
    }

    private function nullableInt(mixed $value): ?int
    {
        if (! is_numeric($value)) {
            return null;
        }

        $normalized = (int) $value;

        return $normalized > 0 ? $normalized : null;
    }

    private function templateSupportsSources(?string $templateKey = null): bool
    {
        return in_array(
            $templateKey ?: (string) ($this->form['template_key'] ?? ''),
            self::SOURCE_ENABLED_TEMPLATES,
            true
        );
    }

    private function templateSupportsBlogSource(?string $templateKey = null): bool
    {
        return in_array(
            $templateKey ?: (string) ($this->form['template_key'] ?? ''),
            self::BLOG_SOURCE_ENABLED_TEMPLATES,
            true
        );
    }

    private function templateSupportsFaqSource(?string $templateKey = null): bool
    {
        return in_array(
            $templateKey ?: (string) ($this->form['template_key'] ?? ''),
            self::FAQ_SOURCE_ENABLED_TEMPLATES,
            true
        );
    }

    private function templateSupportsTeamSource(?string $templateKey = null): bool
    {
        return in_array(
            $templateKey ?: (string) ($this->form['template_key'] ?? ''),
            self::TEAM_SOURCE_ENABLED_TEMPLATES,
            true
        );
    }

    private function templateSupportsBrochure(?string $templateKey = null): bool
    {
        return in_array(
            $templateKey ?: (string) ($this->form['template_key'] ?? ''),
            self::BROCHURE_ENABLED_TEMPLATES,
            true
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function applyEuFundsAssetUploads(array $payload): array
    {
        $paths = ['calls.download_link.path'];

        foreach ((array) data_get($payload, 'calls.other_calls.items', []) as $itemIndex => $item) {
            $paths[] = "calls.other_calls.items.$itemIndex.link.path";
        }

        foreach ((array) data_get($payload, 'resources.cards', []) as $cardIndex => $card) {
            $paths[] = "resources.cards.$cardIndex.primary_link.path";
            $paths[] = "resources.cards.$cardIndex.secondary_link.path";

            foreach ((array) ($card['groups'] ?? []) as $groupIndex => $group) {
                foreach ((array) ($group['items'] ?? []) as $itemIndex => $item) {
                    $paths[] = "resources.cards.$cardIndex.groups.$groupIndex.items.$itemIndex.link.path";
                }
            }
        }

        foreach ((array) data_get($payload, 'laws.cards', []) as $cardIndex => $card) {
            $paths[] = "laws.cards.$cardIndex.primary_link.path";
            $paths[] = "laws.cards.$cardIndex.secondary_link.path";
        }

        foreach ($paths as $path) {
            $payload = $this->storeUploadedAssetAtPath($payload, $path);
        }

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function storeUploadedAssetAtPath(array $payload, string $path): array
    {
        $upload = $this->assetUploads[$this->assetUploadKey($path)] ?? null;

        if (! $upload instanceof TemporaryUploadedFile) {
            return $payload;
        }

        $replacedPath = trim((string) data_get($payload, $path, ''));
        $storedPath = $upload->storeAs(
            'service-assets/eu-funds',
            Str::uuid().'.pdf',
            'public',
        );

        if (! is_string($storedPath) || ! $this->isManagedEuFundsAssetPath($storedPath)) {
            throw new \RuntimeException('EU funds PDF asset could not be stored.');
        }

        $this->newEuFundsAssetPaths[] = $storedPath;
        if ($replacedPath !== '' && $replacedPath !== $storedPath && $this->isManagedEuFundsAssetPath($replacedPath)) {
            $this->replacedEuFundsAssetPaths[] = $replacedPath;
        }

        data_set($payload, $path, $storedPath);

        return $payload;
    }

    private function assetUploadKey(string $path): string
    {
        return str_replace('.', '_', $path);
    }

    private function deleteUnreferencedReplacedEuFundsAssets(): void
    {
        $paths = collect($this->replacedEuFundsAssetPaths)
            ->filter(fn ($path): bool => is_string($path) && $this->isManagedEuFundsAssetPath($path))
            ->unique()
            ->reject(fn (string $path): bool => $this->serviceTranslationPayloadReferences($path))
            ->values()
            ->all();

        $this->deleteManagedEuFundsAssets($paths);
    }

    /**
     * @param  array<int, string>  $paths
     */
    private function deleteManagedEuFundsAssets(array $paths): void
    {
        $safePaths = collect($paths)
            ->filter(fn ($path): bool => is_string($path) && $this->isManagedEuFundsAssetPath($path))
            ->unique()
            ->values()
            ->all();

        if ($safePaths !== []) {
            Storage::disk('public')->delete($safePaths);
        }
    }

    private function isManagedEuFundsAssetPath(string $path): bool
    {
        return str_starts_with($path, 'service-assets/eu-funds/')
            && ! str_contains($path, '..')
            && preg_match('#^service-assets/eu-funds/[A-Za-z0-9][A-Za-z0-9._/-]*$#', $path) === 1;
    }

    private function serviceTranslationPayloadReferences(string $path): bool
    {
        return ServicePageTranslation::query()
            ->select(['id', 'payload'])
            ->get()
            ->contains(fn (ServicePageTranslation $translation): bool => $this->payloadContainsPath(
                $translation->payload,
                $path,
            ));
    }

    private function payloadContainsPath(mixed $value, string $path): bool
    {
        if (is_string($value)) {
            return hash_equals($path, $value);
        }

        if (! is_array($value)) {
            return false;
        }

        foreach ($value as $child) {
            if ($this->payloadContainsPath($child, $path)) {
                return true;
            }
        }

        return false;
    }

    private function imageMimesRule(bool $includeSvg = false): string
    {
        $extensions = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
        if ($includeSvg) {
            $extensions[] = 'svg';
        }

        return 'mimes:'.implode(',', MediaProfileRegistry::supportedImageExtensions($extensions));
    }

    /**
     * @return array<int, string>
     */
    private function activeContentLocaleOptions(): array
    {
        $fallbackOptions = AdminLocale::fallbackOptions();

        try {
            $activeOptions = Language::query()
                ->where('is_active', true)
                ->orderByDesc('is_default')
                ->orderBy('sort_order')
                ->orderBy('code')
                ->pluck('code')
                ->map(fn ($code): string => AdminLocale::normalize((string) $code))
                ->filter()
                ->unique()
                ->values()
                ->all();
        } catch (\Throwable) {
            $activeOptions = [];
        }

        if ($activeOptions === []) {
            return $fallbackOptions;
        }

        $preferred = array_values(array_filter(
            $fallbackOptions,
            static fn (string $locale): bool => in_array($locale, $activeOptions, true)
        ));
        $remaining = array_values(array_filter(
            $activeOptions,
            static fn (string $locale): bool => ! in_array($locale, $preferred, true)
        ));

        return array_values(array_unique([...$preferred, ...$remaining]));
    }
}
