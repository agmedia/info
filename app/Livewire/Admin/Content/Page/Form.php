<?php

namespace App\Livewire\Admin\Content\Page;

use App\Models\Catalog\Category\Category;
use App\Models\Content\Resource\ResourceDocument;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Page\InfoPageTranslation;
use App\Support\Content\AcademyPageDefaults;
use App\Support\Content\CareerPageDefaults;
use App\Support\Content\ResourceDocumentGroupRegistry;
use App\Support\Content\YouTubeUrl;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    private const TAB_OPTIONS = ['content', 'sources', 'media', 'seo'];

    public ?int $pageId = null;
    public string $activeTab = 'content';
    public ?int $academyDocumentPickerId = null;

    public array $form = [
        'code' => '',
        'layout' => 'default',
        'is_active' => true,
        'published_at' => '',
        'sort_order' => 0,
        'payload_text' => '',
        'locale' => 'en',
        'title' => '',
        'slug' => '',
        'excerpt' => '',
        'body_html' => '',
        'meta_title' => '',
        'meta_description' => '',
        'translation_payload_text' => '',
        'category_ids' => [],
        'academy_blog_category_id' => null,
        'academy_blog_limit' => 3,
        'academy_blog_title' => '',
        'academy_blog_intro' => '',
        'academy_resource_document_ids' => [],
        'academy_resource_title' => '',
        'academy_resource_intro' => '',
        'academy_video_items' => [],
        'academy_video_title' => '',
        'academy_video_intro' => '',
        'academy_programs' => [],
        'career_intro_title' => '',
        'career_intro_highlight' => '',
        'career_intro_body' => '',
        'career_process_kicker' => '',
        'career_process_title_line_one' => '',
        'career_process_title_line_two' => '',
        'career_process_intro' => '',
        'career_process_steps' => [],
        'career_application_title' => '',
        'career_application_highlight' => '',
        'career_application_paragraphs' => [],
        'career_form_title' => '',
    ];

    public function mount(?int $pageId = null): void
    {
        $this->form['locale'] = (string) (request()->query('locale') ?: app()->getLocale() ?: config('admin_ui.locale.default', 'hr'));

        if ($pageId) {
            $this->pageId = $pageId;
            $this->loadPage();
        }
    }

    public function updatedFormLocale(): void
    {
        $this->loadTranslationForLocale();
        $this->academyDocumentPickerId = null;
    }

    public function updatedFormLayout(string $layout): void
    {
        if ($layout === 'academy' && $this->form['academy_programs'] === []) {
            $this->form['academy_programs'] = $this->defaultAcademyPrograms();
        }

        if ($layout === 'career' && $this->careerFieldsAreEmpty()) {
            $this->fillCareerFields($this->defaultCareerContent());
        }

        if (
            ($this->activeTab === 'sources' && ! $this->layoutSupportsSources($layout))
            || ($this->activeTab === 'media' && ! $this->layoutSupportsMedia($layout))
        ) {
            $this->activeTab = 'content';
        }
    }

    public function generateSlug(): void
    {
        $title = trim((string) $this->form['title']);
        if ($title !== '') {
            $this->form['slug'] = Str::slug($title);
        }
    }

    public function setTab(string $tab): void
    {
        if (! in_array($tab, self::TAB_OPTIONS, true)) {
            return;
        }

        $layout = (string) ($this->form['layout'] ?? '');

        if ($tab === 'sources' && ! $this->layoutSupportsSources($layout)) {
            return;
        }

        if ($tab === 'media' && ! $this->layoutSupportsMedia($layout)) {
            return;
        }

        $this->activeTab = $tab;
    }

    public function addAcademyDocument(int $id): void
    {
        if ($id <= 0) {
            return;
        }

        $ids = collect((array) ($this->form['academy_resource_document_ids'] ?? []))
            ->map(fn ($value): int => (int) $value)
            ->filter()
            ->values();

        if (! $ids->contains($id)) {
            $ids->push($id);
            $this->form['academy_resource_document_ids'] = $ids->all();
        }

        $this->academyDocumentPickerId = null;
    }

    public function removeAcademyDocument(int $id): void
    {
        $this->form['academy_resource_document_ids'] = collect((array) ($this->form['academy_resource_document_ids'] ?? []))
            ->map(fn ($value): int => (int) $value)
            ->reject(fn (int $value): bool => $value === $id)
            ->values()
            ->all();
    }

    public function moveAcademyDocumentUp(int $index): void
    {
        $this->moveAcademyDocument($index, -1);
    }

    public function moveAcademyDocumentDown(int $index): void
    {
        $this->moveAcademyDocument($index, 1);
    }

    public function addAcademyVideo(): void
    {
        $rows = (array) ($this->form['academy_video_items'] ?? []);
        $rows[] = [
            'title' => '',
            'youtube_url' => '',
        ];

        $this->form['academy_video_items'] = array_values($rows);
    }

    public function removeAcademyVideo(int $index): void
    {
        $rows = array_values((array) ($this->form['academy_video_items'] ?? []));

        if (! array_key_exists($index, $rows)) {
            return;
        }

        unset($rows[$index]);
        $this->form['academy_video_items'] = array_values($rows);
    }

    public function moveAcademyVideoUp(int $index): void
    {
        $this->moveAcademyVideo($index, -1);
    }

    public function moveAcademyVideoDown(int $index): void
    {
        $this->moveAcademyVideo($index, 1);
    }

    public function save()
    {
        $validated = $this->validate($this->rules());
        $wasEditing = (bool) $this->pageId;

        $payload = $this->decodeJsonField('form.payload_text');
        if ($payload === false) {
            return null;
        }

        $translationPayload = $this->decodeJsonField('form.translation_payload_text');
        if ($translationPayload === false) {
            return null;
        }

        $payload = is_array($payload) ? $payload : [];
        $translationPayload = is_array($translationPayload) ? $translationPayload : [];

        $academyCategoryId = (int) ($validated['form']['academy_blog_category_id'] ?? 0);
        $academyBlogLimit = max(1, min(24, (int) ($validated['form']['academy_blog_limit'] ?? 3)));
        $academyBlogTitle = trim((string) ($validated['form']['academy_blog_title'] ?? ''));
        $academyBlogIntro = trim((string) ($validated['form']['academy_blog_intro'] ?? ''));
        $academyResourceDocumentIds = $this->normalizeIdList((array) ($validated['form']['academy_resource_document_ids'] ?? []));
        $academyResourceTitle = trim((string) ($validated['form']['academy_resource_title'] ?? ''));
        $academyResourceIntro = trim((string) ($validated['form']['academy_resource_intro'] ?? ''));
        $academyVideoTitle = trim((string) ($validated['form']['academy_video_title'] ?? ''));
        $academyVideoIntro = trim((string) ($validated['form']['academy_video_intro'] ?? ''));
        $academyVideoItems = $this->normalizeAcademyVideoItems((array) ($validated['form']['academy_video_items'] ?? []));
        $academyPrograms = $this->normalizeAcademyPrograms((array) ($validated['form']['academy_programs'] ?? []));
        $careerContent = $this->normalizeCareerContent($validated['form']);

        if ($academyVideoItems === false) {
            return null;
        }

        if ((string) ($validated['form']['layout'] ?? '') === 'academy' && $academyCategoryId > 0) {
            $payload['blog_source'] = [
                'mode' => 'category',
                'category_id' => $academyCategoryId,
                'limit' => $academyBlogLimit,
            ];
        } else {
            unset($payload['blog_source']);
        }

        if ((string) ($validated['form']['layout'] ?? '') === 'academy' && $academyResourceDocumentIds !== []) {
            $payload['resource_source'] = [
                'mode' => 'manual',
                'document_ids' => $academyResourceDocumentIds,
            ];
        } else {
            unset($payload['resource_source']);
        }

        if ((string) ($validated['form']['layout'] ?? '') === 'academy' && $academyVideoItems !== []) {
            $payload['video_source'] = [
                'mode' => 'manual',
                'items' => $academyVideoItems,
            ];
        } else {
            unset($payload['video_source']);
        }

        if ((string) ($validated['form']['layout'] ?? '') === 'academy') {
            $academyBlogSection = array_filter([
                'title' => $academyBlogTitle !== '' ? $academyBlogTitle : null,
                'intro' => $academyBlogIntro !== '' ? $academyBlogIntro : null,
            ], static fn ($value): bool => $value !== null && $value !== '');

            if ($academyBlogSection !== []) {
                $translationPayload['academy_blog_section'] = $academyBlogSection;
            } else {
                unset($translationPayload['academy_blog_section']);
            }

            $academyResourceSection = array_filter([
                'title' => $academyResourceTitle !== '' ? $academyResourceTitle : null,
                'intro' => $academyResourceIntro !== '' ? $academyResourceIntro : null,
            ], static fn ($value): bool => $value !== null && $value !== '');

            if ($academyResourceSection !== []) {
                $translationPayload['academy_resource_section'] = $academyResourceSection;
            } else {
                unset($translationPayload['academy_resource_section']);
            }

            $academyVideoSection = array_filter([
                'title' => $academyVideoTitle !== '' ? $academyVideoTitle : null,
                'intro' => $academyVideoIntro !== '' ? $academyVideoIntro : null,
            ], static fn ($value): bool => $value !== null && $value !== '');

            if ($academyVideoSection !== []) {
                $translationPayload['academy_video_section'] = $academyVideoSection;
            } else {
                unset($translationPayload['academy_video_section']);
            }

            $translationPayload['academy_programs'] = $academyPrograms;
        } else {
            unset($translationPayload['academy_blog_section']);
            unset($translationPayload['academy_resource_section']);
            unset($translationPayload['academy_video_section']);
            unset($translationPayload['academy_programs']);
        }

        if ((string) ($validated['form']['layout'] ?? '') === 'career') {
            $translationPayload['career_page'] = $careerContent;
        } else {
            unset($translationPayload['career_page']);
        }

        $payloadToSave = $payload === [] ? null : $payload;
        $translationPayloadToSave = $translationPayload === [] ? null : $translationPayload;

        $userId = auth()->id();

        DB::transaction(function () use ($validated, $payloadToSave, $translationPayloadToSave, $userId, $wasEditing): void {
            $pageData = [
                'code' => trim((string) $validated['form']['code']),
                'layout' => trim((string) $validated['form']['layout']) !== '' ? trim((string) $validated['form']['layout']) : 'default',
                'is_active' => (bool) $validated['form']['is_active'],
                'published_at' => $validated['form']['published_at'] ?: null,
                'sort_order' => (int) $validated['form']['sort_order'],
                'payload' => $payloadToSave,
                'updated_by' => $userId,
            ];

            if ($this->pageId) {
                $page = InfoPage::query()->findOrFail($this->pageId);
                $page->fill($pageData)->save();
            } else {
                $page = InfoPage::query()->create($pageData + ['created_by' => $userId]);
                $this->pageId = $page->id;
            }

            $page->translations()->updateOrCreate(
                ['locale' => $validated['form']['locale']],
                [
                    'title' => $validated['form']['title'],
                    'slug' => $validated['form']['slug'],
                    'excerpt' => $validated['form']['excerpt'] ?: null,
                    'body_html' => $validated['form']['body_html'] ?: null,
                    'meta_title' => $validated['form']['meta_title'] ?: null,
                    'meta_description' => $validated['form']['meta_description'] ?: null,
                    'payload' => $translationPayloadToSave,
                ]
            );

            $syncPayload = [];
            foreach (array_values($validated['form']['category_ids'] ?? []) as $index => $categoryId) {
                $syncPayload[(int) $categoryId] = [
                    'sort_order' => $index,
                    'is_primary' => $index === 0,
                ];
            }
            $page->categories()->sync($syncPayload);

            activity('content_pages')
                ->performedOn($page)
                ->causedBy(auth()->user())
                ->event($wasEditing ? 'updated' : 'created')
                ->withProperties([
                    'locale' => $validated['form']['locale'],
                    'slug' => $validated['form']['slug'],
                    'layout' => $validated['form']['layout'],
                    'category_count' => count($syncPayload),
                ])
                ->log(__('Info page saved'));
        });

        $message = $wasEditing ? __('Info page updated.') : __('Info page created.');

        return redirect()
            ->route('admin.content.pages.index', ['locale' => $this->form['locale']])
            ->with('notify', [
                'type' => 'success',
                'message' => $message,
            ]);
    }

    public function backToList()
    {
        return redirect()->route('admin.content.pages.index', ['locale' => $this->form['locale']]);
    }

    public function render()
    {
        return view('livewire.admin.content.page.form', [
            'isEdit' => (bool) $this->pageId,
        ]);
    }

    public function getCategoryOptionsProperty(): Collection
    {
        $categories = Category::query()
            ->where('scope', Category::SCOPE_PAGE)
            ->withDepth()
            ->defaultOrder()
            ->with([
                'translations' => fn ($q) => $q
                    ->where('scope', Category::SCOPE_PAGE)
                    ->where('locale', $this->form['locale']),
            ])
            ->get();

        $nameById = $categories->mapWithKeys(function (Category $category): array {
            $name = (string) ($category->translations->first()?->name ?? ($category->code ?: ('#'.$category->id)));

            return [(int) $category->id => $name];
        });
        $byId = $categories->keyBy(fn (Category $category): int => (int) $category->id);
        $labels = [];

        $build = function (int $id) use (&$build, &$labels, $byId, $nameById): string {
            if (isset($labels[$id])) {
                return $labels[$id];
            }

            $current = $byId->get($id);
            if ($current === null) {
                return '#'.$id;
            }

            $name = (string) ($nameById[$id] ?? ('#'.$id));
            $parentId = (int) ($current->parent_id ?? 0);
            $labels[$id] = ($parentId > 0 && $byId->has($parentId))
                ? $build($parentId).' > '.$name
                : $name;

            return $labels[$id];
        };

        return $categories->map(function (Category $category) use (&$build): array {
            return [
                'id' => (int) $category->id,
                'label' => $build((int) $category->id),
            ];
        });
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
                'translations' => fn ($query) => $query
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

    public function getResourceDocumentOptionsProperty(): Collection
    {
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale', 'en'));
        $locales = array_values(array_unique([(string) $this->form['locale'], $fallbackLocale]));

        return ResourceDocument::query()
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', $locales),
            ])
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->orderBy('id')
            ->get()
            ->map(function (ResourceDocument $document) use ($fallbackLocale): array {
                $translation = $document->translations->firstWhere('locale', (string) $this->form['locale'])
                    ?? $document->translations->firstWhere('locale', $fallbackLocale)
                    ?? $document->translations->first();
                $title = trim((string) ($translation?->title ?? '')) ?: $document->code;
                $groupLabel = ResourceDocumentGroupRegistry::label((string) $document->group_code);

                return [
                    'id' => (int) $document->id,
                    'label' => $title.' - '.$groupLabel,
                ];
            });
    }

    public function getSelectedAcademyDocumentsProperty(): Collection
    {
        $optionsById = $this->resourceDocumentOptions->keyBy('id');

        return collect((array) ($this->form['academy_resource_document_ids'] ?? []))
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

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'form.code' => ['required', 'string', 'max:120', Rule::unique('content_info_pages', 'code')->ignore($this->pageId)],
            'form.layout' => ['nullable', 'string', 'max:80'],
            'form.is_active' => ['boolean'],
            'form.published_at' => ['nullable', 'date'],
            'form.sort_order' => ['nullable', 'integer', 'min:0'],
            'form.payload_text' => ['nullable', 'string'],

            'form.locale' => ['required', 'string', 'max:12'],
            'form.title' => ['required', 'string', 'max:255'],
            'form.slug' => [
                'required',
                'string',
                'max:191',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::notIn($this->reservedSlugs()),
                Rule::unique('content_info_page_translations', 'slug')
                    ->where(fn ($q) => $q->where('locale', $this->form['locale']))
                    ->ignore($this->pageId, 'page_id'),
            ],
            'form.excerpt' => ['nullable', 'string'],
            'form.body_html' => ['nullable', 'string'],
            'form.meta_title' => ['nullable', 'string', 'max:255'],
            'form.meta_description' => ['nullable', 'string'],
            'form.translation_payload_text' => ['nullable', 'string'],
            'form.category_ids' => ['nullable', 'array'],
            'form.academy_blog_category_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where(fn ($query) => $query->where('scope', Category::SCOPE_BLOG)),
            ],
            'form.academy_blog_limit' => ['nullable', 'integer', 'min:1', 'max:24'],
            'form.academy_blog_title' => ['nullable', 'string', 'max:255'],
            'form.academy_blog_intro' => ['nullable', 'string'],
            'form.academy_resource_document_ids' => ['nullable', 'array'],
            'form.academy_resource_document_ids.*' => ['integer', Rule::exists('content_resource_documents', 'id')],
            'form.academy_resource_title' => ['nullable', 'string', 'max:255'],
            'form.academy_resource_intro' => ['nullable', 'string'],
            'form.academy_video_items' => ['nullable', 'array'],
            'form.academy_video_items.*.title' => ['nullable', 'string', 'max:255'],
            'form.academy_video_items.*.youtube_url' => ['nullable', 'string', 'max:2048'],
            'form.academy_video_title' => ['nullable', 'string', 'max:255'],
            'form.academy_video_intro' => ['nullable', 'string'],
            'form.academy_programs' => ['nullable', 'array'],
            'form.academy_programs.*.title' => ['nullable', 'string', 'max:255'],
            'form.academy_programs.*.intro' => ['nullable', 'string'],
            'form.academy_programs.*.items' => ['nullable', 'array'],
            'form.academy_programs.*.items.*.title' => ['nullable', 'string', 'max:255'],
            'form.academy_programs.*.items.*.text' => ['nullable', 'string'],
            'form.career_intro_title' => ['nullable', 'string', 'max:255'],
            'form.career_intro_highlight' => ['nullable', 'string'],
            'form.career_intro_body' => ['nullable', 'string'],
            'form.career_process_kicker' => ['nullable', 'string', 'max:255'],
            'form.career_process_title_line_one' => ['nullable', 'string', 'max:255'],
            'form.career_process_title_line_two' => ['nullable', 'string', 'max:255'],
            'form.career_process_intro' => ['nullable', 'string'],
            'form.career_process_steps' => ['nullable', 'array'],
            'form.career_process_steps.*.step' => ['nullable', 'string', 'max:255'],
            'form.career_process_steps.*.title' => ['nullable', 'string', 'max:255'],
            'form.career_process_steps.*.description' => ['nullable', 'string'],
            'form.career_application_title' => ['nullable', 'string', 'max:255'],
            'form.career_application_highlight' => ['nullable', 'string'],
            'form.career_application_paragraphs' => ['nullable', 'array'],
            'form.career_application_paragraphs.*' => ['nullable', 'string'],
            'form.career_form_title' => ['nullable', 'string', 'max:255'],
            'form.category_ids.*' => [
                'integer',
                Rule::exists('categories', 'id')->where(fn ($q) => $q->where('scope', Category::SCOPE_PAGE)),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'form.slug.not_in' => __('This slug is reserved for an existing site route.'),
        ];
    }

    private function loadPage(): void
    {
        if (!$this->pageId) {
            return;
        }

        $page = InfoPage::query()
            ->with('translations')
            ->with(['categories' => fn ($q) => $q->orderBy('content_info_page_category.sort_order')])
            ->findOrFail($this->pageId);

        $preferredLocale = $this->form['locale'] ?: config('app.locale', 'en');
        $translation = $page->translations->firstWhere('locale', $preferredLocale)
            ?? $page->translations->firstWhere('locale', config('app.locale', 'en'))
            ?? $page->translations->first();

        $this->form['code'] = $page->code;
        $this->form['layout'] = $page->layout;
        $this->form['is_active'] = (bool) $page->is_active;
        $this->form['published_at'] = $page->published_at?->format('Y-m-d\TH:i') ?? '';
        $this->form['sort_order'] = (int) $page->sort_order;
        $this->form['payload_text'] = $page->payload
            ? json_encode($page->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : '';
        $this->form['category_ids'] = $page->categories->pluck('id')->map(fn ($id) => (int) $id)->all();
        $pagePayload = is_array($page->payload) ? $page->payload : [];
        $blogSource = is_array($pagePayload['blog_source'] ?? null) ? $pagePayload['blog_source'] : [];
        $resourceSource = is_array($pagePayload['resource_source'] ?? null) ? $pagePayload['resource_source'] : [];
        $videoSource = is_array($pagePayload['video_source'] ?? null) ? $pagePayload['video_source'] : [];
        $this->form['academy_blog_category_id'] = (int) ($blogSource['category_id'] ?? 0) > 0
            ? (int) $blogSource['category_id']
            : null;
        $this->form['academy_blog_limit'] = max(1, min(24, (int) ($blogSource['limit'] ?? 3)));
        $this->form['academy_resource_document_ids'] = $this->normalizeIdList((array) ($resourceSource['document_ids'] ?? []));
        $this->form['academy_video_items'] = $this->normalizeAcademyVideoDraftItems((array) ($videoSource['items'] ?? []));
        $this->form['academy_programs'] = $page->layout === 'academy' ? $this->defaultAcademyPrograms() : [];

        if ($translation) {
            $translationPayload = is_array($translation->payload) ? $translation->payload : [];
            $academyBlogSection = is_array($translationPayload['academy_blog_section'] ?? null)
                ? $translationPayload['academy_blog_section']
                : [];
            $academyResourceSection = is_array($translationPayload['academy_resource_section'] ?? null)
                ? $translationPayload['academy_resource_section']
                : [];
            $academyVideoSection = is_array($translationPayload['academy_video_section'] ?? null)
                ? $translationPayload['academy_video_section']
                : [];
            $academyPrograms = $page->layout === 'academy'
                ? AcademyPageDefaults::mergePrograms($translationPayload['academy_programs'] ?? null)
                : [];
            $careerContent = $page->layout === 'career'
                ? CareerPageDefaults::merge($translationPayload['career_page'] ?? null, (string) $translation->locale)
                : null;

            $this->form['locale'] = $translation->locale;
            $this->form['title'] = $translation->title;
            $this->form['slug'] = $translation->slug;
            $this->form['excerpt'] = $translation->excerpt ?? '';
            $this->form['body_html'] = $translation->body_html ?? '';
            $this->form['meta_title'] = $translation->meta_title ?? '';
            $this->form['meta_description'] = $translation->meta_description ?? '';
            $this->form['academy_blog_title'] = (string) ($academyBlogSection['title'] ?? '');
            $this->form['academy_blog_intro'] = (string) ($academyBlogSection['intro'] ?? '');
            $this->form['academy_resource_title'] = (string) ($academyResourceSection['title'] ?? '');
            $this->form['academy_resource_intro'] = (string) ($academyResourceSection['intro'] ?? '');
            $this->form['academy_video_title'] = (string) ($academyVideoSection['title'] ?? '');
            $this->form['academy_video_intro'] = (string) ($academyVideoSection['intro'] ?? '');
            $this->form['academy_programs'] = $academyPrograms;
            if ($careerContent !== null) {
                $this->fillCareerFields($careerContent);
            } else {
                $this->clearCareerFields();
            }
            $this->form['translation_payload_text'] = $translation->payload
                ? json_encode($translation->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                : '';
        } else {
            $this->form['academy_blog_title'] = '';
            $this->form['academy_blog_intro'] = '';
            $this->form['academy_resource_title'] = '';
            $this->form['academy_resource_intro'] = '';
            $this->form['academy_video_title'] = '';
            $this->form['academy_video_intro'] = '';
            $this->form['academy_programs'] = $page->layout === 'academy' ? $this->defaultAcademyPrograms() : [];
            $this->clearCareerFields();
        }
    }

    private function loadTranslationForLocale(): void
    {
        if (!$this->pageId) {
            $this->clearTranslationFields();
            return;
        }

        $translation = InfoPageTranslation::query()
            ->where('page_id', $this->pageId)
            ->where('locale', $this->form['locale'])
            ->first();

        if (!$translation) {
            $this->clearTranslationFields();
            return;
        }

        $this->form['title'] = $translation->title;
        $this->form['slug'] = $translation->slug;
        $this->form['excerpt'] = $translation->excerpt ?? '';
        $this->form['body_html'] = $translation->body_html ?? '';
        $this->form['meta_title'] = $translation->meta_title ?? '';
        $this->form['meta_description'] = $translation->meta_description ?? '';
        $translationPayload = is_array($translation->payload) ? $translation->payload : [];
        $academyBlogSection = is_array($translationPayload['academy_blog_section'] ?? null)
            ? $translationPayload['academy_blog_section']
            : [];
        $academyResourceSection = is_array($translationPayload['academy_resource_section'] ?? null)
            ? $translationPayload['academy_resource_section']
            : [];
        $academyVideoSection = is_array($translationPayload['academy_video_section'] ?? null)
            ? $translationPayload['academy_video_section']
            : [];
        $careerContent = (string) ($this->form['layout'] ?? '') === 'career'
            ? CareerPageDefaults::merge($translationPayload['career_page'] ?? null, (string) $translation->locale)
            : null;
        $this->form['academy_programs'] = (string) ($this->form['layout'] ?? '') === 'academy'
            ? AcademyPageDefaults::mergePrograms($translationPayload['academy_programs'] ?? null)
            : [];
        $this->form['academy_blog_title'] = (string) ($academyBlogSection['title'] ?? '');
        $this->form['academy_blog_intro'] = (string) ($academyBlogSection['intro'] ?? '');
        $this->form['academy_resource_title'] = (string) ($academyResourceSection['title'] ?? '');
        $this->form['academy_resource_intro'] = (string) ($academyResourceSection['intro'] ?? '');
        $this->form['academy_video_title'] = (string) ($academyVideoSection['title'] ?? '');
        $this->form['academy_video_intro'] = (string) ($academyVideoSection['intro'] ?? '');
        if ($careerContent !== null) {
            $this->fillCareerFields($careerContent);
        } else {
            $this->clearCareerFields();
        }
        $this->form['translation_payload_text'] = $translation->payload
            ? json_encode($translation->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : '';
    }

    private function clearTranslationFields(): void
    {
        $this->form['title'] = '';
        $this->form['slug'] = '';
        $this->form['excerpt'] = '';
        $this->form['body_html'] = '';
        $this->form['meta_title'] = '';
        $this->form['meta_description'] = '';
        $this->form['academy_blog_title'] = '';
        $this->form['academy_blog_intro'] = '';
        $this->form['academy_resource_title'] = '';
        $this->form['academy_resource_intro'] = '';
        $this->form['academy_video_title'] = '';
        $this->form['academy_video_intro'] = '';
        $this->form['academy_programs'] = (string) ($this->form['layout'] ?? '') === 'academy'
            ? $this->defaultAcademyPrograms()
            : [];
        $this->clearCareerFields();
        $this->form['translation_payload_text'] = '';
    }

    /**
     * @return array<int, string>
     */
    private function reservedSlugs(): array
    {
        return [
            'ac-forma-robot',
            'admin',
            'alpha-capitalis-tim',
            'blog',
            'contact',
            'dashboard',
            'faq',
            'glossary',
            'leasing-kalkulator',
            'locale',
            'login',
            'logout',
            'obiteljski-biznis',
            'page',
            'pages',
            'profile',
        ];
    }

    /**
     * @return array<mixed>|null|false
     */
    private function decodeJsonField(string $field): array|null|false
    {
        $value = trim((string) data_get($this, $field));
        if ($value === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->addError($field, (string) __('Invalid JSON payload.'));
            $this->dispatch('notify', type: 'danger', message: __('Invalid JSON payload.'));
            return false;
        }

        if (!is_array($decoded)) {
            $this->addError($field, (string) __('JSON payload must decode to object/array.'));
            $this->dispatch('notify', type: 'danger', message: __('JSON payload must decode to object/array.'));
            return false;
        }

        return $decoded;
    }

    private function moveAcademyDocument(int $index, int $direction): void
    {
        $rows = array_values(array_map(
            static fn ($value): int => (int) $value,
            (array) ($this->form['academy_resource_document_ids'] ?? [])
        ));

        $swapIndex = $index + $direction;
        if ($index < 0 || $index >= count($rows) || $swapIndex < 0 || $swapIndex >= count($rows)) {
            return;
        }

        [$rows[$index], $rows[$swapIndex]] = [$rows[$swapIndex], $rows[$index]];
        $this->form['academy_resource_document_ids'] = $rows;
    }

    private function moveAcademyVideo(int $index, int $direction): void
    {
        $rows = array_values((array) ($this->form['academy_video_items'] ?? []));

        $swapIndex = $index + $direction;
        if ($index < 0 || $index >= count($rows) || $swapIndex < 0 || $swapIndex >= count($rows)) {
            return;
        }

        [$rows[$index], $rows[$swapIndex]] = [$rows[$swapIndex], $rows[$index]];
        $this->form['academy_video_items'] = array_values($rows);
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
    private function normalizeAcademyVideoItems(array $items): array|false
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
                $this->addError("form.academy_video_items.$index.youtube_url", __('YouTube URL je obavezan ako je red popunjen.'));
                $hasErrors = true;
                continue;
            }

            $parsed = YouTubeUrl::parse($youtubeUrl);

            if ($parsed === null) {
                $this->addError("form.academy_video_items.$index.youtube_url", __('Podržani su samo valjani YouTube linkovi.'));
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

    /**
     * @param  array<int, mixed>  $items
     * @return array<int, array{title:string, youtube_url:string}>
     */
    private function normalizeAcademyVideoDraftItems(array $items): array
    {
        return collect($items)
            ->map(function ($item): ?array {
                $title = trim((string) data_get($item, 'title', ''));
                $youtubeUrl = trim((string) data_get($item, 'youtube_url', ''));

                if ($title === '' && $youtubeUrl === '') {
                    return null;
                }

                return [
                    'title' => $title,
                    'youtube_url' => $youtubeUrl,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function defaultAcademyPrograms(): array
    {
        return AcademyPageDefaults::mergePrograms([]);
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultCareerContent(?string $locale = null): array
    {
        return CareerPageDefaults::merge([], (string) ($locale ?: $this->form['locale'] ?: config('app.locale', 'en')));
    }

    /**
     * @param  array<int, mixed>  $programs
     * @return array<int, array<string, mixed>>
     */
    private function normalizeAcademyPrograms(array $programs): array
    {
        return collect(AcademyPageDefaults::mergePrograms($programs))
            ->map(function (array $program): array {
                return [
                    'title' => (string) ($program['title'] ?? ''),
                    'intro' => (string) ($program['intro'] ?? ''),
                    'items' => collect((array) ($program['items'] ?? []))
                        ->map(fn (array $item): array => [
                            'title' => (string) ($item['title'] ?? ''),
                            'text' => (string) ($item['text'] ?? ''),
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    private function fillCareerFields(array $careerContent): void
    {
        $intro = is_array($careerContent['intro'] ?? null) ? $careerContent['intro'] : [];
        $process = is_array($careerContent['process'] ?? null) ? $careerContent['process'] : [];
        $application = is_array($careerContent['application'] ?? null) ? $careerContent['application'] : [];
        $form = is_array($careerContent['form'] ?? null) ? $careerContent['form'] : [];

        $this->form['career_intro_title'] = (string) ($intro['title'] ?? '');
        $this->form['career_intro_highlight'] = (string) ($intro['highlight'] ?? '');
        $this->form['career_intro_body'] = (string) ((is_array($intro['body'] ?? null) ? $intro['body'][0] ?? '' : ''));
        $this->form['career_process_kicker'] = (string) ($process['kicker'] ?? '');
        $this->form['career_process_title_line_one'] = (string) ($process['title_line_one'] ?? '');
        $this->form['career_process_title_line_two'] = (string) ($process['title_line_two'] ?? '');
        $this->form['career_process_intro'] = (string) ($process['intro'] ?? '');
        $this->form['career_process_steps'] = collect((array) ($process['steps'] ?? []))
            ->map(fn ($step): array => [
                'step' => (string) data_get($step, 'step', ''),
                'title' => (string) data_get($step, 'title', ''),
                'description' => (string) data_get($step, 'description', ''),
            ])
            ->values()
            ->all();
        $this->form['career_application_title'] = (string) ($application['title'] ?? '');
        $this->form['career_application_highlight'] = (string) ($application['highlight'] ?? '');
        $this->form['career_application_paragraphs'] = collect((array) ($application['paragraphs'] ?? []))
            ->map(fn ($paragraph): string => (string) $paragraph)
            ->values()
            ->all();
        $this->form['career_form_title'] = (string) ($form['title'] ?? '');
    }

    private function clearCareerFields(): void
    {
        if ((string) ($this->form['layout'] ?? '') === 'career') {
            $this->fillCareerFields($this->defaultCareerContent());

            return;
        }

        $this->form['career_intro_title'] = '';
        $this->form['career_intro_highlight'] = '';
        $this->form['career_intro_body'] = '';
        $this->form['career_process_kicker'] = '';
        $this->form['career_process_title_line_one'] = '';
        $this->form['career_process_title_line_two'] = '';
        $this->form['career_process_intro'] = '';
        $this->form['career_process_steps'] = [];
        $this->form['career_application_title'] = '';
        $this->form['career_application_highlight'] = '';
        $this->form['career_application_paragraphs'] = [];
        $this->form['career_form_title'] = '';
    }

    /**
     * @param  array<string, mixed>  $validatedForm
     * @return array<string, mixed>
     */
    private function normalizeCareerContent(array $validatedForm): array
    {
        return CareerPageDefaults::merge([
            'intro' => [
                'title' => trim((string) ($validatedForm['career_intro_title'] ?? '')),
                'highlight' => trim((string) ($validatedForm['career_intro_highlight'] ?? '')),
                'body' => [
                    trim((string) ($validatedForm['career_intro_body'] ?? '')),
                ],
            ],
            'process' => [
                'kicker' => trim((string) ($validatedForm['career_process_kicker'] ?? '')),
                'title_line_one' => trim((string) ($validatedForm['career_process_title_line_one'] ?? '')),
                'title_line_two' => trim((string) ($validatedForm['career_process_title_line_two'] ?? '')),
                'intro' => trim((string) ($validatedForm['career_process_intro'] ?? '')),
                'steps' => collect((array) ($validatedForm['career_process_steps'] ?? []))
                    ->map(fn ($step): array => [
                        'step' => trim((string) data_get($step, 'step', '')),
                        'title' => trim((string) data_get($step, 'title', '')),
                        'description' => trim((string) data_get($step, 'description', '')),
                    ])
                    ->values()
                    ->all(),
            ],
            'application' => [
                'title' => trim((string) ($validatedForm['career_application_title'] ?? '')),
                'highlight' => trim((string) ($validatedForm['career_application_highlight'] ?? '')),
                'paragraphs' => collect((array) ($validatedForm['career_application_paragraphs'] ?? []))
                    ->map(fn ($paragraph): string => trim((string) $paragraph))
                    ->values()
                    ->all(),
            ],
            'form' => [
                'title' => trim((string) ($validatedForm['career_form_title'] ?? '')),
            ],
        ], (string) ($validatedForm['locale'] ?? $this->form['locale'] ?? config('app.locale', 'en')));
    }

    private function careerFieldsAreEmpty(): bool
    {
        return trim((string) ($this->form['career_intro_title'] ?? '')) === ''
            && trim((string) ($this->form['career_intro_highlight'] ?? '')) === ''
            && trim((string) ($this->form['career_intro_body'] ?? '')) === ''
            && trim((string) ($this->form['career_process_kicker'] ?? '')) === ''
            && trim((string) ($this->form['career_process_title_line_one'] ?? '')) === ''
            && trim((string) ($this->form['career_process_title_line_two'] ?? '')) === ''
            && trim((string) ($this->form['career_process_intro'] ?? '')) === ''
            && (array) ($this->form['career_process_steps'] ?? []) === []
            && trim((string) ($this->form['career_application_title'] ?? '')) === ''
            && trim((string) ($this->form['career_application_highlight'] ?? '')) === ''
            && (array) ($this->form['career_application_paragraphs'] ?? []) === []
            && trim((string) ($this->form['career_form_title'] ?? '')) === '';
    }

    private function layoutSupportsSources(string $layout): bool
    {
        return $layout === 'academy';
    }

    private function layoutSupportsMedia(string $layout): bool
    {
        return in_array($layout, ['about', 'academy', 'career', 'references'], true);
    }
}
