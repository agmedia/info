<?php

namespace App\Livewire\Admin\Content\Blog;

use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Blog\BlogPostTranslation;
use Carbon\CarbonImmutable;
use Closure;
use DateTimeZone;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    private const SEO_AUTOMATION_PAYLOAD_KEY = '_seo_automation';

    public ?int $postId = null;

    public string $activeTab = 'content';

    public string $categorySearch = '';

    public string $newCategoryName = '';

    public bool $metaTitleIsAutomatic = true;

    public bool $metaDescriptionIsAutomatic = true;

    public array $form = [
        'code' => '',
        'is_active' => true,
        'is_featured' => false,
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
    ];

    public function mount(?int $postId = null): void
    {
        $this->form['locale'] = (string) (request()->query('locale') ?: app()->getLocale() ?: config('admin_ui.locale.default', 'hr'));

        if ($postId) {
            $this->postId = $postId;
            $this->loadPost();
        }
    }

    public function updatedFormLocale(): void
    {
        $this->loadTranslationForLocale();
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
        if (! in_array($tab, ['content', 'categories', 'seo', 'media'], true)) {
            return;
        }

        $this->activeTab = $tab;
    }

    public function updatedFormTitle($value): void
    {
        $title = trim((string) $value);
        $this->syncMetaTitleFromTitle($title);

        if ($title === '') {
            return;
        }

        $slug = Str::slug($title);
        if ($slug !== '') {
            $this->form['slug'] = $slug;
            $this->form['code'] = $this->uniqueCodeFromBase($slug);
        }
    }

    public function updatedFormExcerpt(): void
    {
        $this->syncMetaDescriptionFromContent();
    }

    public function updatedFormBodyHtml(): void
    {
        $this->syncMetaDescriptionFromContent();
    }

    public function updatedFormMetaTitle($value): void
    {
        $this->metaTitleIsAutomatic = trim((string) $value) === '';

        if ($this->metaTitleIsAutomatic) {
            $this->syncMetaTitleFromTitle((string) ($this->form['title'] ?? ''));
        }
    }

    public function updatedFormMetaDescription($value): void
    {
        $this->metaDescriptionIsAutomatic = trim((string) $value) === '';

        if ($this->metaDescriptionIsAutomatic) {
            $this->syncMetaDescriptionFromContent();
        }
    }

    public function addCategory(int $categoryId): void
    {
        $ids = collect($this->form['category_ids'] ?? [])->map(fn ($id) => (int) $id)->filter()->values();
        if ($ids->contains($categoryId)) {
            return;
        }

        $ids->push($categoryId);
        $this->form['category_ids'] = $ids->all();
    }

    public function removeCategory(int $categoryId): void
    {
        $this->form['category_ids'] = collect($this->form['category_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->reject(fn ($id) => $id === $categoryId)
            ->values()
            ->all();
    }

    public function moveCategoryUp(int $index): void
    {
        $this->moveCategory($index, -1);
    }

    public function moveCategoryDown(int $index): void
    {
        $this->moveCategory($index, 1);
    }

    public function quickCreateCategory(): void
    {
        $validated = $this->validate([
            'newCategoryName' => ['required', 'string', 'max:255'],
        ]);
        $name = trim((string) $validated['newCategoryName']);
        $slug = Str::slug($name);
        $locale = (string) ($this->form['locale'] ?: config('app.locale', 'hr'));

        if ($slug === '') {
            $this->addError('newCategoryName', __('Naziv mora sadržavati barem jedno slovo ili broj.'));

            return;
        }

        $slugExists = Category::query()
            ->whereHas('translations', fn ($query) => $query
                ->where('locale', $locale)
                ->where('slug', $slug))
            ->exists();

        if ($slugExists) {
            $this->addError('newCategoryName', __('Kategorija s ovim nazivom već postoji.'));

            return;
        }

        $category = DB::transaction(function () use ($locale, $name, $slug): Category {
            $userId = auth()->id();
            $category = new Category([
                'scope' => Category::SCOPE_BLOG,
                'code' => null,
                'is_active' => true,
                'show_in_menu' => true,
                'sort_order' => 0,
                'payload' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
            $category->saveAsRoot();
            $category->translations()->create([
                'scope' => Category::SCOPE_BLOG,
                'locale' => $locale,
                'name' => $name,
                'slug' => $slug,
                'meta_title' => $name,
            ]);

            return $category;
        });

        $this->addCategory((int) $category->id);
        $this->newCategoryName = '';
        $this->categorySearch = '';
        $this->dispatch('notify', type: 'success', message: __('Kategorija je dodana i odmah odabrana.'));
    }

    public function save()
    {
        if (trim((string) ($this->form['slug'] ?? '')) === '') {
            $this->form['slug'] = Str::slug((string) ($this->form['title'] ?? ''));
        }

        if (trim((string) ($this->form['code'] ?? '')) === '') {
            $this->form['code'] = $this->uniqueCodeFromBase((string) $this->form['slug']);
        }

        $validated = $this->validate($this->rules());
        $wasEditing = (bool) $this->postId;

        $payload = $this->decodeJsonField('form.payload_text');
        if ($payload === false) {
            return null;
        }

        $translationPayload = $this->decodeJsonField('form.translation_payload_text');
        if ($translationPayload === false) {
            return null;
        }

        $userId = auth()->id();

        DB::transaction(function () use ($validated, $payload, $translationPayload, $userId, $wasEditing): void {
            $payload = is_array($payload) ? $payload : [];
            $translationPayload = is_array($translationPayload) ? $translationPayload : [];
            $translationPayload[self::SEO_AUTOMATION_PAYLOAD_KEY] = [
                'meta_title' => $this->metaTitleIsAutomatic,
                'meta_description' => $this->metaDescriptionIsAutomatic,
            ];
            $isActive = (bool) $validated['form']['is_active'];

            $postData = [
                'code' => trim((string) $validated['form']['code']),
                'is_active' => $isActive,
                'is_featured' => (bool) $validated['form']['is_featured'],
                'published_at' => $this->publishedAtForStorage(
                    $validated['form']['published_at'] ?? null,
                    $isActive
                ),
                'sort_order' => (int) $validated['form']['sort_order'],
                'payload' => $payload,
                'updated_by' => $userId,
            ];

            if ($this->postId) {
                $post = BlogPost::query()->findOrFail($this->postId);
                $post->fill($postData)->save();
            } else {
                $post = BlogPost::query()->create($postData + ['created_by' => $userId]);
                $this->postId = $post->id;
            }

            $post->translations()->updateOrCreate(
                ['locale' => $validated['form']['locale']],
                [
                    'title' => $validated['form']['title'],
                    'slug' => $validated['form']['slug'],
                    'excerpt' => $validated['form']['excerpt'] ?: null,
                    'body_html' => $validated['form']['body_html'] ?: null,
                    'meta_title' => $this->resolvedMetaTitle((string) $validated['form']['title'], $validated['form']['meta_title'] ?? null),
                    'meta_description' => $this->resolvedMetaDescription(
                        (string) ($validated['form']['excerpt'] ?? ''),
                        (string) ($validated['form']['body_html'] ?? ''),
                        $validated['form']['meta_description'] ?? null
                    ),
                    'payload' => $translationPayload,
                ]
            );

            $syncPayload = [];
            foreach (array_values($validated['form']['category_ids'] ?? []) as $index => $categoryId) {
                $syncPayload[(int) $categoryId] = [
                    'sort_order' => $index,
                    'is_primary' => $index === 0,
                ];
            }
            $post->categories()->sync($syncPayload);

            activity('content_blog')
                ->performedOn($post)
                ->causedBy(auth()->user())
                ->event($wasEditing ? 'updated' : 'created')
                ->withProperties([
                    'locale' => $validated['form']['locale'],
                    'slug' => $validated['form']['slug'],
                    'category_count' => count($syncPayload),
                ])
                ->log('Blog post saved');
        });

        $message = $wasEditing ? 'Blog post updated.' : 'Blog post created.';

        return redirect()
            ->route('admin.content.blog.index', ['locale' => $this->form['locale']])
            ->with('notify', [
                'type' => 'success',
                'message' => $message,
            ]);
    }

    public function backToList()
    {
        return redirect()->route('admin.content.blog.index', ['locale' => $this->form['locale']]);
    }

    public function render()
    {
        return view('livewire.admin.content.blog.form', [
            'isEdit' => (bool) $this->postId,
        ]);
    }

    public function getCategoryOptionsProperty(): Collection
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
            ->get();
    }

    public function getFilteredCategoryOptionsProperty(): Collection
    {
        $search = Str::lower(trim($this->categorySearch));
        $selected = collect($this->form['category_ids'] ?? [])->map(fn ($id) => (int) $id)->filter()->all();
        $labels = $this->categoryLabelMap;

        return $this->categoryOptions
            ->reject(fn ($category) => in_array((int) $category->id, $selected, true))
            ->map(function ($category) use ($labels): array {
                $id = (int) $category->id;

                return [
                    'id' => $id,
                    'label' => (string) ($labels[$id] ?? ('#'.$id)),
                ];
            })
            ->filter(function (array $row) use ($search): bool {
                if ($search === '') {
                    return true;
                }

                return Str::contains(Str::lower($row['label']), $search);
            })
            ->values()
            ->take(80);
    }

    public function getSelectedCategoryRowsProperty(): Collection
    {
        $labels = $this->categoryLabelMap;

        return collect($this->form['category_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->map(function (int $id) use ($labels): array {
                return ['id' => $id, 'label' => (string) ($labels[$id] ?? ('#'.$id))];
            });
    }

    /**
     * @return array<int, string>
     */
    public function getCategoryLabelMapProperty(): array
    {
        $categories = $this->categoryOptions;
        $nameById = $categories->mapWithKeys(function ($category): array {
            $name = (string) ($category->translations->first()?->name ?? ($category->code ?: ('#'.$category->id)));

            return [(int) $category->id => $name];
        });
        $byId = $categories->keyBy(fn ($category): int => (int) $category->id);
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

        foreach ($byId->keys() as $id) {
            $build((int) $id);
        }

        return $labels;
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'form.code' => ['required', 'string', 'max:120', Rule::unique('content_blog_posts', 'code')->ignore($this->postId)],
            'form.is_active' => ['boolean'],
            'form.is_featured' => ['boolean'],
            'form.published_at' => [
                'bail',
                'nullable',
                'date_format:Y-m-d\TH:i',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $publishedAt = trim((string) $value);

                    if (! $this->publicationDateTimeIsUnambiguous($publishedAt)) {
                        $fail(__('The publication date and time is not valid in the configured timezone.'));
                    }
                },
            ],
            'form.sort_order' => ['nullable', 'integer', 'min:0'],
            'form.payload_text' => ['nullable', 'string'],

            'form.locale' => ['required', 'string', 'max:12'],
            'form.title' => ['required', 'string', 'max:255'],
            'form.slug' => [
                'required',
                'string',
                'max:191',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('content_blog_post_translations', 'slug')
                    ->where(fn ($q) => $q->where('locale', $this->form['locale']))
                    ->ignore($this->postId, 'post_id'),
            ],
            'form.excerpt' => ['nullable', 'string'],
            'form.body_html' => ['nullable', 'string'],
            'form.meta_title' => ['nullable', 'string', 'max:255'],
            'form.meta_description' => ['nullable', 'string'],
            'form.translation_payload_text' => ['nullable', 'string'],
            'form.category_ids' => ['nullable', 'array'],
            'form.category_ids.*' => [
                'integer',
                Rule::exists('categories', 'id')->where(fn ($q) => $q->where('scope', Category::SCOPE_BLOG)),
            ],
        ];
    }

    private function loadPost(): void
    {
        if (! $this->postId) {
            return;
        }

        $post = BlogPost::query()
            ->with('translations')
            ->with(['categories' => fn ($q) => $q->orderBy('content_blog_post_category.sort_order')])
            ->findOrFail($this->postId);

        $preferredLocale = $this->form['locale'] ?: config('app.locale', 'en');
        $translation = $post->translations->firstWhere('locale', $preferredLocale)
            ?? $post->translations->firstWhere('locale', config('app.locale', 'en'))
            ?? $post->translations->first();

        $this->form['code'] = $post->code;
        $this->form['is_active'] = (bool) $post->is_active;
        $this->form['is_featured'] = (bool) $post->is_featured;
        $this->form['published_at'] = $post->published_at
            ?->copy()
            ->setTimezone($this->publicationTimezone())
            ->format('Y-m-d\TH:i') ?? '';
        $this->form['sort_order'] = (int) $post->sort_order;
        $this->form['payload_text'] = $post->payload
            ? json_encode($post->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : '';
        $this->form['category_ids'] = $post->categories->pluck('id')->map(fn ($id) => (int) $id)->all();

        if ($translation) {
            $this->form['locale'] = $translation->locale;
            $this->form['title'] = $translation->title;
            $this->form['slug'] = $translation->slug;
            $this->form['excerpt'] = $translation->excerpt ?? '';
            $this->form['body_html'] = $translation->body_html ?? '';
            $this->form['meta_title'] = $translation->meta_title ?? '';
            $this->form['meta_description'] = $translation->meta_description ?? '';
            $this->form['translation_payload_text'] = $translation->payload
                ? json_encode($translation->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                : '';
            $this->loadSeoAutomationState($translation);
        }
    }

    private function loadTranslationForLocale(): void
    {
        if (! $this->postId) {
            $this->clearTranslationFields();

            return;
        }

        $translation = BlogPostTranslation::query()
            ->where('post_id', $this->postId)
            ->where('locale', $this->form['locale'])
            ->first();

        if (! $translation) {
            $this->clearTranslationFields();

            return;
        }

        $this->form['title'] = $translation->title;
        $this->form['slug'] = $translation->slug;
        $this->form['excerpt'] = $translation->excerpt ?? '';
        $this->form['body_html'] = $translation->body_html ?? '';
        $this->form['meta_title'] = $translation->meta_title ?? '';
        $this->form['meta_description'] = $translation->meta_description ?? '';
        $this->form['translation_payload_text'] = $translation->payload
            ? json_encode($translation->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : '';
        $this->loadSeoAutomationState($translation);
    }

    private function clearTranslationFields(): void
    {
        $this->form['title'] = '';
        $this->form['slug'] = '';
        $this->form['excerpt'] = '';
        $this->form['body_html'] = '';
        $this->form['meta_title'] = '';
        $this->form['meta_description'] = '';
        $this->form['translation_payload_text'] = '';
        $this->metaTitleIsAutomatic = true;
        $this->metaDescriptionIsAutomatic = true;
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
            $this->addError($field, __('Invalid JSON payload.'));
            $this->dispatch('notify', type: 'danger', message: __('Invalid JSON payload.'));

            return false;
        }

        if (! is_array($decoded)) {
            $this->addError($field, __('JSON payload must decode to object/array.'));
            $this->dispatch('notify', type: 'danger', message: __('JSON payload must decode to object/array.'));

            return false;
        }

        return $decoded;
    }

    private function uniqueCodeFromBase(string $base): string
    {
        $cleanBase = trim($base) !== '' ? trim($base) : 'blog-post';
        $code = $cleanBase;
        $suffix = 2;

        while (
            BlogPost::query()
                ->when($this->postId, fn ($q) => $q->where('id', '!=', $this->postId))
                ->where('code', $code)
                ->exists()
        ) {
            $code = $cleanBase.'-'.$suffix;
            $suffix++;
        }

        return $code;
    }

    private function moveCategory(int $index, int $offset): void
    {
        $ids = array_values((array) ($this->form['category_ids'] ?? []));
        $targetIndex = $index + $offset;

        if (! array_key_exists($index, $ids) || ! array_key_exists($targetIndex, $ids)) {
            return;
        }

        [$ids[$index], $ids[$targetIndex]] = [$ids[$targetIndex], $ids[$index]];
        $this->form['category_ids'] = $ids;
    }

    private function metaDescriptionFromBody(string $bodyHtml): string
    {
        return $this->metaDescriptionFromText($bodyHtml);
    }

    private function publishedAtForStorage(?string $value, bool $isActive): ?CarbonImmutable
    {
        $publishedAt = trim((string) $value);
        if ($publishedAt === '') {
            return $isActive ? CarbonImmutable::now('UTC')->startOfMinute() : null;
        }

        return CarbonImmutable::createFromFormat(
            '!Y-m-d\TH:i',
            $publishedAt,
            $this->publicationTimezone()
        )->utc();
    }

    private function publicationTimezone(): string
    {
        return (string) config('admin_ui.timezone', 'Europe/Zagreb');
    }

    private function publicationDateTimeIsUnambiguous(string $value): bool
    {
        $wallClock = CarbonImmutable::createFromFormat('!Y-m-d\TH:i', $value, 'UTC');
        $timezone = new DateTimeZone($this->publicationTimezone());
        $wallTimestamp = $wallClock->getTimestamp();
        $transitions = $timezone->getTransitions($wallTimestamp - 172800, $wallTimestamp + 172800);

        if (! is_array($transitions)) {
            return false;
        }

        $matchingOffsets = collect($transitions)
            ->pluck('offset')
            ->unique()
            ->filter(function (mixed $offset) use ($timezone, $value, $wallTimestamp): bool {
                return CarbonImmutable::createFromTimestampUTC($wallTimestamp - (int) $offset)
                    ->setTimezone($timezone)
                    ->format('Y-m-d\TH:i') === $value;
            });

        return $matchingOffsets->count() === 1;
    }

    private function metaDescriptionFromContent(string $excerpt, string $bodyHtml): string
    {
        $fromExcerpt = $this->metaDescriptionFromText($excerpt);

        return $fromExcerpt !== '' ? $fromExcerpt : $this->metaDescriptionFromBody($bodyHtml);
    }

    private function metaDescriptionFromText(string $value): string
    {
        $plain = preg_replace('/\s+/u', ' ', trim(strip_tags($value)));
        $plain = html_entity_decode((string) $plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return Str::limit(trim($plain), 160, '...');
    }

    private function syncMetaTitleFromTitle(string $title): void
    {
        $current = trim((string) ($this->form['meta_title'] ?? ''));
        $nextAuto = $title === '' ? '' : Str::limit($title, 255, '');

        if ($this->metaTitleIsAutomatic || $current === '') {
            $this->form['meta_title'] = $nextAuto;
            $this->metaTitleIsAutomatic = true;
        }
    }

    private function syncMetaDescriptionFromContent(): void
    {
        $current = trim((string) ($this->form['meta_description'] ?? ''));
        $nextAuto = $this->metaDescriptionFromContent(
            (string) ($this->form['excerpt'] ?? ''),
            (string) ($this->form['body_html'] ?? '')
        );

        if ($this->metaDescriptionIsAutomatic || $current === '') {
            $this->form['meta_description'] = $nextAuto;
            $this->metaDescriptionIsAutomatic = true;
        }
    }

    private function loadSeoAutomationState(BlogPostTranslation $translation): void
    {
        $payload = is_array($translation->payload) ? $translation->payload : [];
        $storedTitleState = data_get($payload, self::SEO_AUTOMATION_PAYLOAD_KEY.'.meta_title');
        $storedDescriptionState = data_get($payload, self::SEO_AUTOMATION_PAYLOAD_KEY.'.meta_description');
        $generatedTitle = Str::limit(trim((string) ($this->form['title'] ?? '')), 255, '');
        $generatedDescription = $this->metaDescriptionFromContent(
            (string) ($this->form['excerpt'] ?? ''),
            (string) ($this->form['body_html'] ?? '')
        );
        $currentTitle = trim((string) ($this->form['meta_title'] ?? ''));
        $currentDescription = trim((string) ($this->form['meta_description'] ?? ''));

        $this->metaTitleIsAutomatic = is_bool($storedTitleState)
            ? $storedTitleState
            : $currentTitle === '' || $currentTitle === $generatedTitle;
        $this->metaDescriptionIsAutomatic = is_bool($storedDescriptionState)
            ? $storedDescriptionState
            : $currentDescription === '' || $currentDescription === $generatedDescription;
    }

    private function resolvedMetaTitle(string $title, ?string $metaTitle): ?string
    {
        $value = trim((string) $metaTitle);
        if ($value !== '') {
            return Str::limit($value, 255, '');
        }

        $fallback = trim($title);

        return $fallback === '' ? null : Str::limit($fallback, 255, '');
    }

    private function resolvedMetaDescription(string $excerpt, string $bodyHtml, ?string $metaDescription): ?string
    {
        $value = trim((string) $metaDescription);
        if ($value !== '') {
            return $value;
        }

        $auto = $this->metaDescriptionFromContent($excerpt, $bodyHtml);

        return $auto !== '' ? $auto : null;
    }
}
