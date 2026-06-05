<?php

namespace App\Livewire\Admin\Content\Service;

use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Service\ServicePage;
use App\Models\Content\Service\ServicePageTranslation;
use App\Models\Content\Support\Faq;
use App\Models\Content\Team\TeamMember;
use App\Support\Content\ServicePageTemplateRegistry;
use App\Support\Content\YouTubeUrl;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

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

    public string $activeTab = 'content';

    public ?int $blogPickerId = null;

    public ?int $faqPickerId = null;

    public ?int $teamPickerId = null;

    /** @var array<string, TemporaryUploadedFile|null> */
    public array $assetUploads = [];

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
        'template_key' => ServicePageTemplateRegistry::FAMILY_BUSINESS,
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
        $this->form['locale'] = (string) (request()->query('locale') ?: app()->getLocale() ?: config('admin_ui.locale.default', 'hr'));
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
        $pagePayloadInput = (array) data_get($this->form, 'page_payload', []);
        $translationPayloadInput = (array) data_get($this->form, 'translation_payload', []);
        $validated = $this->validate($this->rules());
        $wasEditing = (bool) $this->servicePageId;
        $userId = auth()->id();

        $pagePayload = $this->normalizedPagePayload($pagePayloadInput);
        if ($pagePayload === false) {
            return null;
        }

        $translationPayload = $this->normalizedTranslationPayload(
            (string) $validated['form']['template_key'],
            $translationPayloadInput
        );

        DB::transaction(function () use ($validated, $pagePayload, $translationPayload, $userId, $wasEditing): void {
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
                $servicePage->fill($servicePageData)->save();
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

    public function render()
    {
        return view('livewire.admin.content.service.form', [
            'isEdit' => (bool) $this->servicePageId,
            'templateOptions' => ServicePageTemplateRegistry::labels(),
            'templateSupportsSources' => $this->templateSupportsSources(),
            'templateSupportsBlogSource' => $this->templateSupportsBlogSource(),
            'templateSupportsFaqSource' => $this->templateSupportsFaqSource(),
            'templateSupportsTeamSource' => $this->templateSupportsTeamSource(),
            'templateSupportsBrochure' => $this->templateSupportsBrochure(),
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

        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale', 'en'));
        $preferredLocale = $this->form['locale'] ?: $fallbackLocale;
        $translation = $servicePage->translations->firstWhere('locale', $preferredLocale)
            ?? $servicePage->translations->firstWhere('locale', $fallbackLocale)
            ?? $servicePage->translations->first();

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

        if ($translation) {
            $this->form['locale'] = $translation->locale;
            $this->form['title'] = $translation->title;
            $this->form['slug'] = $translation->slug;
            $this->form['meta_title'] = $translation->meta_title ?? '';
            $this->form['meta_description'] = $translation->meta_description ?? '';
            $this->form['translation_payload'] = ServicePageTemplateRegistry::mergeTranslationPayload(
                $servicePage->template_key,
                $translation->payload,
                $translation->locale
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
        $this->form['translation_payload'] = ServicePageTemplateRegistry::mergeTranslationPayload(
            $templateKey,
            $translation->payload,
            $translation->locale
        );
        $this->assetUploads = [];
    }

    private function clearTranslationFields(string $templateKey): void
    {
        $locale = (string) ($this->form['locale'] ?? config('app.locale', 'en'));
        $defaults = $this->defaultTranslationFields($templateKey, $locale);

        $this->form['title'] = $defaults['title'];
        $this->form['slug'] = $defaults['slug'];
        $this->form['meta_title'] = $defaults['meta_title'];
        $this->form['meta_description'] = $defaults['meta_description'];
        $this->form['translation_payload'] = ServicePageTemplateRegistry::defaultTranslationPayload(
            $templateKey,
            $locale
        );
        $this->assetUploads = [];
    }

    private function initializeTemplateDefaults(string $templateKey): void
    {
        $this->form['template_key'] = $templateKey;
        $this->form['code'] = $this->form['code'] !== ''
            ? $this->form['code']
            : ServicePageTemplateRegistry::defaultCode($templateKey);
        $this->form['page_payload'] = ServicePageTemplateRegistry::defaultPagePayload($templateKey);
        $this->form['translation_payload'] = ServicePageTemplateRegistry::defaultTranslationPayload(
            $templateKey,
            (string) ($this->form['locale'] ?? config('app.locale', 'en'))
        );

        if (! $this->templateSupportsSources($templateKey) && $this->activeTab === 'sources') {
            $this->activeTab = 'content';
        }
    }

    /**
     * @return array{title: string, slug: string, meta_title: string, meta_description: string}
     */
    private function defaultTranslationFields(string $templateKey, string $locale): array
    {
        if ($templateKey === ServicePageTemplateRegistry::AUDIT) {
            if (str_starts_with(strtolower($locale), 'hr')) {
                return [
                    'title' => 'Revizija',
                    'slug' => 'revizija',
                    'meta_title' => 'Revizija',
                    'meta_description' => 'Revizija financijskih izvještaja, revizorski uvidi i posebni revizorski angažmani.',
                ];
            }

            return [
                'title' => 'Audit',
                'slug' => 'audit',
                'meta_title' => 'Audit',
                'meta_description' => 'Audit of financial statements, review engagements, and special audit services.',
            ];
        }

        if ($templateKey === ServicePageTemplateRegistry::ADVISORY) {
            if (str_starts_with(strtolower($locale), 'hr')) {
                return [
                    'title' => 'Savjetovanje',
                    'slug' => 'savjetovanje',
                    'meta_title' => 'Savjetovanje',
                    'meta_description' => 'Financijsko i porezno savjetovanje, pribavljanje financiranja, due diligence, procjene vrijednosti i M&A savjetovanje.',
                ];
            }

            return [
                'title' => 'Advisory',
                'slug' => 'advisory',
                'meta_title' => 'Advisory',
                'meta_description' => 'Financial and tax advisory, financing, due diligence, valuations, and M&A advisory.',
            ];
        }

        if ($templateKey === ServicePageTemplateRegistry::ACCOUNTING) {
            if (str_starts_with(strtolower($locale), 'hr')) {
                return [
                    'title' => 'Računovodstvo',
                    'slug' => 'racunovodstvo',
                    'meta_title' => 'Računovodstvo',
                    'meta_description' => 'Računovodstvena podrška, vođenje poslovnih knjiga, obračun plaća i izvještavanje za svakodnevno poslovanje.',
                ];
            }

            return [
                'title' => 'Accounting',
                'slug' => 'accounting',
                'meta_title' => 'Accounting',
                'meta_description' => 'Accounting support, bookkeeping, payroll processing, and reporting for day-to-day business operations.',
            ];
        }

        if ($templateKey === ServicePageTemplateRegistry::TAX) {
            if (str_starts_with(strtolower($locale), 'hr')) {
                return [
                    'title' => 'Porezi',
                    'slug' => 'porezi',
                    'meta_title' => 'Porezi',
                    'meta_description' => 'Porezno savjetovanje, tax compliance, porezni pregledi, optimizacija, due diligence i transferne cijene.',
                ];
            }

            return [
                'title' => 'Tax',
                'slug' => 'tax',
                'meta_title' => 'Tax',
                'meta_description' => 'Tax advisory, tax compliance, tax reviews, optimization, due diligence, and transfer pricing.',
            ];
        }

        if ($templateKey === ServicePageTemplateRegistry::EU_FUNDS) {
            if (str_starts_with(strtolower($locale), 'hr')) {
                return [
                    'title' => 'EU fondovi',
                    'slug' => 'eu-fondovi',
                    'meta_title' => 'EU fondovi',
                    'meta_description' => 'EU fondovi, natječaji i savjetovanje za pripremu i provedbu projekata.',
                ];
            }

            return [
                'title' => 'EU Funds',
                'slug' => 'eu-funds',
                'meta_title' => 'EU Funds',
                'meta_description' => 'EU funds, grants, and advisory for project preparation and implementation.',
            ];
        }

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
        $merged = ServicePageTemplateRegistry::mergeTranslationPayload($templateKey, $payload, (string) $this->form['locale']);

        if ($templateKey === ServicePageTemplateRegistry::ACCOUNTING) {
            $detailTitles = collect((array) data_get($merged, 'detail_sections', []))
                ->map(fn ($section): string => trim((string) data_get($section, 'title', '')))
                ->filter()
                ->values()
                ->all();

            if ($detailTitles !== []) {
                data_set($merged, 'intro_section.items', $detailTitles);
            }
        }

        if ($templateKey === ServicePageTemplateRegistry::EU_FUNDS) {
            $merged = $this->applyEuFundsAssetUploads($merged);
        }

        return $merged;
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

        data_set($payload, $path, $upload->store('service-assets/eu-funds', 'public'));

        return $payload;
    }

    private function assetUploadKey(string $path): string
    {
        return str_replace('.', '_', $path);
    }
}
