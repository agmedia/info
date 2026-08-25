<?php

namespace App\Livewire\Admin\Content\Call;

use App\Models\Catalog\Category\Category;
use App\Models\Content\Call\CallPost;
use App\Models\Content\Call\CallPostTranslation;
use App\Models\Settings\Local\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    public ?int $postId = null;

    public string $activeTab = 'content';

    public string $categorySearch = '';

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
        $requestedLocale = strtolower((string) (request()->query('locale') ?: app()->getLocale() ?: config('admin_ui.locale.default', 'hr')));
        $this->form['locale'] = in_array($requestedLocale, $this->localeOptions(), true)
            ? $requestedLocale
            : $this->resolveDefaultLocale();

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
        if (! in_array($tab, ['content', 'seo', 'media'], true)) {
            return;
        }

        $this->activeTab = $tab;
    }

    public function updatedFormTitle($value): void
    {
        $title = trim((string) $value);
        if ($title === '') {
            return;
        }

        $slug = Str::slug($title);
        if ($slug !== '') {
            $this->form['slug'] = $slug;
            if (! $this->postId) {
                $this->form['code'] = $this->uniqueCodeFromBase($slug);
            }
        }

        if (! $this->postId && trim((string) ($this->form['meta_title'] ?? '')) === '') {
            $this->form['meta_title'] = Str::limit($title, 255, '');
        }
    }

    public function updatedFormBodyHtml($value): void
    {
        if (trim((string) ($this->form['meta_description'] ?? '')) !== '') {
            return;
        }

        $this->form['meta_description'] = $this->metaDescriptionFromBody((string) $value);
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

    public function save()
    {
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
            $post = $this->postId ? CallPost::query()->findOrFail($this->postId) : null;
            $publishedAt = $validated['form']['published_at'] ?: null;

            if (
                $post?->published_at
                && is_string($publishedAt)
                && $publishedAt === $post->published_at->format('Y-m-d')
            ) {
                $publishedAt = $post->published_at;
            }

            $postData = [
                'code' => trim((string) $validated['form']['code']),
                'is_active' => (bool) $validated['form']['is_active'],
                'is_featured' => (bool) $validated['form']['is_featured'],
                'published_at' => $publishedAt,
                'sort_order' => (int) $validated['form']['sort_order'],
                'payload' => $payload,
                'updated_by' => $userId,
            ];

            if ($post) {
                $post->fill($postData)->save();
            } else {
                $post = CallPost::query()->create($postData + ['created_by' => $userId]);
                $this->postId = $post->id;
            }

            $post->translations()->updateOrCreate(
                ['locale' => $validated['form']['locale']],
                [
                    'title' => $validated['form']['title'],
                    'slug' => $validated['form']['slug'],
                    'excerpt' => $validated['form']['excerpt'] ?: null,
                    'body_html' => $validated['form']['body_html'] ?: null,
                    'meta_title' => $this->resolvedMetaTitle($validated['form']['meta_title'] ?? null),
                    'meta_description' => $this->resolvedMetaDescription((string) ($validated['form']['body_html'] ?? ''), $validated['form']['meta_description'] ?? null),
                    'payload' => $translationPayload,
                ]
            );

            $existingPivots = $post->categories()
                ->get()
                ->mapWithKeys(fn ($category): array => [
                    (int) $category->id => [
                        'sort_order' => (int) $category->pivot->sort_order,
                        'is_primary' => (bool) $category->pivot->is_primary,
                    ],
                ]);
            $syncPayload = [];
            foreach (array_values($validated['form']['category_ids'] ?? []) as $index => $categoryId) {
                $existingPivot = $existingPivots->get((int) $categoryId);
                $syncPayload[(int) $categoryId] = [
                    'sort_order' => $existingPivot['sort_order'] ?? $index,
                    'is_primary' => $existingPivot['is_primary'] ?? ($index === 0),
                ];
            }
            $post->categories()->sync($syncPayload);

            activity('content_call')
                ->performedOn($post)
                ->causedBy(auth()->user())
                ->event($wasEditing ? 'updated' : 'created')
                ->withProperties([
                    'locale' => $validated['form']['locale'],
                    'slug' => $validated['form']['slug'],
                    'category_count' => count($syncPayload),
                ])
                ->log('Call post saved');
        });

        $message = $wasEditing ? 'Call post updated.' : 'Call post created.';

        return redirect()
            ->route('admin.content.calls.index', ['locale' => $this->form['locale']])
            ->with('notify', [
                'type' => 'success',
                'message' => $message,
            ]);
    }

    public function backToList()
    {
        return redirect()->route('admin.content.calls.index', ['locale' => $this->form['locale']]);
    }

    public function render()
    {
        return view('livewire.admin.content.call.form', [
            'isEdit' => (bool) $this->postId,
        ]);
    }

    public function getCategoryOptionsProperty(): Collection
    {
        return Category::query()
            ->where('scope', Category::SCOPE_CALL)
            ->withDepth()
            ->defaultOrder()
            ->with([
                'translations' => fn ($q) => $q
                    ->where('scope', Category::SCOPE_CALL)
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
            'form.code' => ['required', 'string', 'max:120', Rule::unique('content_call_posts', 'code')->ignore($this->postId)],
            'form.is_active' => ['boolean'],
            'form.is_featured' => ['boolean'],
            'form.published_at' => ['nullable', 'date'],
            'form.sort_order' => ['nullable', 'integer', 'min:0'],
            'form.payload_text' => ['nullable', 'string'],

            'form.locale' => ['required', Rule::in($this->localeOptions())],
            'form.title' => ['required', 'string', 'max:255'],
            'form.slug' => [
                'required',
                'string',
                'max:191',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('content_call_post_translations', 'slug')
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
                Rule::exists('categories', 'id')->where(fn ($q) => $q->where('scope', Category::SCOPE_CALL)),
            ],
        ];
    }

    private function loadPost(): void
    {
        if (! $this->postId) {
            return;
        }

        $post = CallPost::query()
            ->with('translations')
            ->with(['categories' => fn ($q) => $q->orderBy('content_call_post_category.sort_order')])
            ->findOrFail($this->postId);

        $preferredLocale = $this->form['locale'] ?: $this->resolveDefaultLocale();
        $translation = $post->translations->firstWhere('locale', $preferredLocale);

        $this->form['code'] = $post->code;
        $this->form['is_active'] = (bool) $post->is_active;
        $this->form['is_featured'] = (bool) $post->is_featured;
        $this->form['published_at'] = $post->published_at?->format('Y-m-d') ?? '';
        $this->form['sort_order'] = (int) $post->sort_order;
        $this->form['payload_text'] = $post->payload
            ? json_encode($post->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : '';
        $this->form['category_ids'] = $post->categories->pluck('id')->map(fn ($id) => (int) $id)->all();

        if ($translation) {
            $this->form['title'] = $translation->title;
            $this->form['slug'] = $translation->slug;
            $this->form['excerpt'] = $translation->excerpt ?? '';
            $this->form['body_html'] = $translation->body_html ?? '';
            $this->form['meta_title'] = $translation->meta_title ?? '';
            $this->form['meta_description'] = $translation->meta_description ?? '';
            $this->form['translation_payload_text'] = $translation->payload
                ? json_encode($translation->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                : '';
        } else {
            $this->clearTranslationFields();
        }
    }

    private function loadTranslationForLocale(): void
    {
        if (! $this->postId) {
            $this->clearTranslationFields();

            return;
        }

        $translation = CallPostTranslation::query()
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
        $cleanBase = trim($base) !== '' ? trim($base) : 'call-post';
        $code = $cleanBase;
        $suffix = 2;

        while (
            CallPost::query()
                ->when($this->postId, fn ($q) => $q->where('id', '!=', $this->postId))
                ->where('code', $code)
                ->exists()
        ) {
            $code = $cleanBase.'-'.$suffix;
            $suffix++;
        }

        return $code;
    }

    private function metaDescriptionFromBody(string $bodyHtml): string
    {
        $plain = preg_replace('/\s+/u', ' ', trim(strip_tags($bodyHtml)));
        $plain = html_entity_decode((string) $plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return Str::limit(trim($plain), 160, '...');
    }

    private function resolvedMetaTitle(?string $metaTitle): ?string
    {
        $value = trim((string) $metaTitle);

        return $value === '' ? null : Str::limit($value, 255, '');
    }

    private function resolvedMetaDescription(string $bodyHtml, ?string $metaDescription): ?string
    {
        $value = trim((string) $metaDescription);
        if ($value !== '') {
            return $value;
        }

        $auto = $this->metaDescriptionFromBody($bodyHtml);

        return $auto !== '' ? $auto : null;
    }

    /**
     * @return array<int, string>
     */
    private function localeOptions(): array
    {
        $locales = Language::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->map(fn ($code): string => strtolower((string) $code))
            ->all();

        if ($locales === []) {
            return [strtolower((string) (config('admin_ui.locale.default', 'hr') ?: 'hr'))];
        }

        return array_values(array_unique($locales));
    }

    private function resolveDefaultLocale(): string
    {
        $default = Language::query()
            ->where('is_active', true)
            ->where('is_default', true)
            ->value('code');

        if (is_string($default) && $default !== '') {
            return strtolower($default);
        }

        return $this->localeOptions()[0] ?? 'hr';
    }
}
