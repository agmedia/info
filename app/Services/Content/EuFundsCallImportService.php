<?php

namespace App\Services\Content;

use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Call\CallPost;
use App\Models\Settings\Local\Language;
use App\Support\Content\EuFundsCallCategoryRegistry;
use App\Support\Content\EuFundsServicePageDefaults;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class EuFundsCallImportService
{
    public function __construct(
        private readonly WordPressBlogImportService $wordPressBlogImportService
    ) {}

    /**
     * @param  array{
     *     limit?:int,
     *     offset?:int,
     *     locale?:string|null,
     *     force?:bool,
     *     user_id?:int|null
     * }  $options
     * @return array{
     *     locale:string,
     *     processed_count:int,
     *     localized_asset_count:int,
     *     categories:array<int,array{id:int,code:string,name:string,slug:string}>,
     *     imported:array<int,array{
     *         id:int,
     *         code:string,
     *         title:string,
     *         slug:string,
     *         category:string,
     *         source:string,
     *         status:string,
     *         asset_count:int
     *     }>
     * }
     */
    public function import(string $filePath, array $options = []): array
    {
        $locale = $this->resolveLocale($options['locale'] ?? null);
        $limit = max(0, (int) ($options['limit'] ?? 0));
        $offset = max(0, (int) ($options['offset'] ?? 0));
        $force = (bool) ($options['force'] ?? false);
        $userId = isset($options['user_id']) ? (int) $options['user_id'] : null;

        $allBlueprintItems = collect($this->buildBlueprintItems($locale));
        $blueprintItems = $allBlueprintItems;
        if ($offset > 0) {
            $blueprintItems = $blueprintItems->slice($offset)->values();
        }
        if ($limit > 0) {
            $blueprintItems = $blueprintItems->take($limit)->values();
        }

        if ($blueprintItems->isEmpty()) {
            throw new RuntimeException('No EU funds call items matched the requested import window.');
        }

        $xmlPosts = collect($this->wordPressBlogImportService->parsePublishedPosts($filePath))
            ->map(function (array $post): array {
                $post['normalized_title'] = $this->normalizeMatchValue((string) ($post['title'] ?? ''));
                $post['normalized_slug'] = $this->normalizeMatchValue((string) ($post['source_slug'] ?? ''));

                return $post;
            })
            ->values();

        $blogSources = $this->loadBlogSourceRows($locale);
        $isFullCroatianImport = str_starts_with(strtolower($locale), 'hr')
            && $offset === 0
            && $limit === 0
            && $blueprintItems->count() === $allBlueprintItems->count();

        $result = DB::transaction(function () use (
            $locale,
            $userId,
            $blueprintItems,
            $xmlPosts,
            $blogSources,
            $force,
            $isFullCroatianImport
        ): array {
            $categories = $this->ensureCallCategories($locale, $userId);
            $reconciliation = $isFullCroatianImport
                ? $this->buildFullReconciliationPlan($blueprintItems, $xmlPosts, $categories['models'], $locale)
                : null;

            if (is_array($reconciliation)) {
                $this->prepareReconciliationCodes($reconciliation, $blueprintItems);
            }

            $result = [
                'locale' => $locale,
                'processed_count' => 0,
                'localized_asset_count' => 0,
                'categories' => array_values($categories['summary']),
                'imported' => [],
            ];

            foreach ($blueprintItems as $blueprintItem) {
                $category = $categories['models'][$blueprintItem['group_key']] ?? null;
                if (! $category instanceof Category) {
                    continue;
                }

                $plannedPostId = is_array($reconciliation)
                    ? ($reconciliation['assignments'][$blueprintItem['code']] ?? null)
                    : null;
                $row = $this->importBlueprintItem(
                    blueprintItem: $blueprintItem,
                    category: $category,
                    locale: $locale,
                    blogSources: $blogSources,
                    xmlPosts: $xmlPosts,
                    force: $force,
                    userId: $userId,
                    plannedPostId: is_numeric($plannedPostId) ? (int) $plannedPostId : null,
                    hasReconciliationPlan: is_array($reconciliation),
                    syncAssets: ! $isFullCroatianImport
                );

                $result['processed_count']++;
                $result['localized_asset_count'] += (int) ($row['asset_count'] ?? 0);
                $result['imported'][] = $row;
            }

            if (is_array($reconciliation)) {
                $this->completeFullReconciliation(
                    reconciliation: $reconciliation,
                    categories: $categories['models'],
                    blueprintItems: $blueprintItems,
                    importedRows: $result['imported'],
                    locale: $locale
                );
                $result['reconciliation'] = [
                    'detached_count' => count($reconciliation['extra_ids']),
                    'status_counts' => $this->statusCategoryCounts($categories['models']),
                ];
            }

            return $result;
        }, 1);

        if ($isFullCroatianImport) {
            $assetSync = $this->syncReconciledAssets(
                blueprintItems: $blueprintItems,
                xmlPosts: $xmlPosts,
                blogSources: $blogSources,
                locale: $locale,
                force: $force
            );

            foreach ($result['imported'] as &$importedRow) {
                $assetCount = (int) ($assetSync['counts'][$importedRow['code']] ?? 0);
                $importedRow['asset_count'] = $assetCount;
                $result['localized_asset_count'] += $assetCount;
            }
            unset($importedRow);

            $result['reconciliation']['asset_sync_failed_count'] = count($assetSync['failed_codes']);
        }

        return $result;
    }

    /**
     * @return array<int, array{
     *     group_key:string,
     *     group_title:string,
     *     tone:string,
     *     title:string,
     *     code:string,
     *     sort_order:int,
     *     blog_slug:string,
     *     xml_slug_hint:string,
     *     preferred_title:string
     * }>
     */
    private function buildBlueprintItems(string $locale): array
    {
        $defaults = EuFundsServicePageDefaults::defaultsForLocale($locale);
        $definitions = collect(EuFundsCallCategoryRegistry::definitions($locale))->keyBy('title');
        $manualSourceMap = [
            'Jačanje strateških partnerstava za inovacije u procesu industrijske tranzicije (SPIN – STEP)' => [
                'xml_slug_hint' => 'poziv-u-najavi-jacanje-strateskih-partnerstva-za-inovacije-u-procesu-industrijske-tranzicije-faza-ii-spin',
                'preferred_title' => 'Jačanje strateških partnerstava za inovacije u procesu industrijske tranzicije (SPIN – STEP)',
            ],
        ];
        $items = [];

        foreach ((array) ($defaults['calls']['groups'] ?? []) as $group) {
            $groupTitle = trim((string) ($group['title'] ?? ''));
            $definition = $definitions->get($groupTitle);
            if (! is_array($definition)) {
                continue;
            }

            foreach (array_values((array) ($group['items'] ?? [])) as $index => $item) {
                $title = trim((string) ($item['title'] ?? ''));
                if ($title === '') {
                    continue;
                }

                $manualSource = $manualSourceMap[$title] ?? [];
                $preferredTitle = trim((string) ($item['preferred_title'] ?? ''));

                $items[] = [
                    'group_key' => (string) $definition['key'],
                    'group_title' => (string) $definition['title'],
                    'tone' => trim((string) ($group['tone'] ?? $definition['tone'] ?? 'pending')),
                    'title' => $title,
                    'code' => $this->resolveImportCode($title),
                    'sort_order' => $index,
                    'blog_slug' => (string) data_get($item, 'link.type') === 'blog'
                        ? trim((string) data_get($item, 'link.slug'))
                        : '',
                    'xml_slug_hint' => trim((string) ($manualSource['xml_slug_hint'] ?? '')),
                    'preferred_title' => $preferredTitle !== ''
                        ? $preferredTitle
                        : trim((string) ($manualSource['preferred_title'] ?? '')),
                ];
            }
        }

        return $items;
    }

    /**
     * @return array{
     *     models:array<string, Category>,
     *     summary:array<int,array{id:int,code:string,name:string,slug:string}>
     * }
     */
    private function ensureCallCategories(string $locale, ?int $userId): array
    {
        $models = [];
        $summary = [];

        foreach (EuFundsCallCategoryRegistry::definitions($locale) as $index => $definition) {
            $slug = (string) $definition['slug'];
            $name = (string) $definition['title'];

            $category = Category::query()
                ->where('scope', Category::SCOPE_CALL)
                ->where(function (Builder $query) use ($slug): void {
                    $query
                        ->where('code', $slug)
                        ->orWhereHas('translations', function (Builder $translationQuery) use ($slug): void {
                            $translationQuery
                                ->where('scope', Category::SCOPE_CALL)
                                ->where('slug', $slug);
                        });
                })
                ->first();

            if (! $category) {
                $category = new Category([
                    'scope' => Category::SCOPE_CALL,
                    'code' => $slug,
                    'is_active' => true,
                    'show_in_menu' => false,
                    'sort_order' => $index,
                    'payload' => ['import_source' => 'eu_funds_calls'],
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);

                $category->saveAsRoot();
            } else {
                $category->fill([
                    'is_active' => true,
                    'show_in_menu' => false,
                    'sort_order' => $index,
                    'updated_by' => $userId,
                ])->save();
            }

            $category->translations()->updateOrCreate(
                ['scope' => Category::SCOPE_CALL, 'locale' => $locale],
                [
                    'name' => $name,
                    'slug' => $slug,
                    'description' => null,
                    'meta_title' => $name,
                    'meta_description' => null,
                    'payload' => [
                        'import_source' => 'eu_funds_calls',
                        'status_label' => (string) ($definition['status_label'] ?? $name),
                    ],
                ]
            );

            $models[(string) $definition['key']] = $category;
            $summary[] = [
                'id' => (int) $category->id,
                'code' => (string) $category->code,
                'name' => $name,
                'slug' => $slug,
            ];
        }

        return [
            'models' => $models,
            'summary' => $summary,
        ];
    }

    /**
     * @return Collection<int, array{
     *     post:BlogPost,
     *     translation:\App\Models\Content\Blog\BlogPostTranslation|null,
     *     title:string,
     *     slug:string,
     *     normalized_title:string
     * }>
     */
    private function loadBlogSourceRows(string $locale): Collection
    {
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale', 'hr'));

        return BlogPost::query()
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', array_values(array_unique([$locale, $fallbackLocale, 'hr']))),
                'media',
            ])
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->get()
            ->map(function (BlogPost $post) use ($locale, $fallbackLocale): array {
                $translation = $post->translations->firstWhere('locale', $locale)
                    ?? $post->translations->firstWhere('locale', $fallbackLocale)
                    ?? $post->translations->first();

                return [
                    'post' => $post,
                    'translation' => $translation,
                    'title' => trim((string) ($translation?->title ?? $post->code)),
                    'slug' => trim((string) ($translation?->slug ?? '')),
                    'normalized_title' => $this->normalizeMatchValue((string) ($translation?->title ?? $post->code)),
                ];
            })
            ->values();
    }

    /**
     * @param  array{
     *     group_key:string,
     *     group_title:string,
     *     tone:string,
     *     title:string,
     *     code:string,
     *     sort_order:int,
     *     blog_slug:string,
     *     xml_slug_hint:string,
     *     preferred_title:string
     * }  $blueprintItem
     * @param  Collection<int, array{
     *     post:BlogPost,
     *     translation:\App\Models\Content\Blog\BlogPostTranslation|null,
     *     title:string,
     *     slug:string,
     *     normalized_title:string
     * }>  $blogSources
     * @param  Collection<int, array<string,mixed>>  $xmlPosts
     * @return array{
     *     id:int,
     *     code:string,
     *     title:string,
     *     slug:string,
     *     category:string,
     *     source:string,
     *     status:string,
     *     asset_count:int
     * }
     */
    private function importBlueprintItem(
        array $blueprintItem,
        Category $category,
        string $locale,
        Collection $blogSources,
        Collection $xmlPosts,
        bool $force,
        ?int $userId,
        ?int $plannedPostId = null,
        bool $hasReconciliationPlan = false,
        bool $syncAssets = true
    ): array {
        // A newly uploaded WXR file is authoritative for this re-import. The
        // existing blog copy remains a fallback only when the XML has no match.
        $xmlSource = $this->resolveXmlSource($blueprintItem, $xmlPosts);
        $blogSource = $xmlSource === null ? $this->resolveBlogSource($blueprintItem, $blogSources) : null;

        $existing = $hasReconciliationPlan
            ? ($plannedPostId !== null ? $this->loadCallPost($plannedPostId, $locale) : null)
            : $this->resolveExistingPost($blueprintItem, $locale, $xmlSource);

        $attributes = [
            'code' => $blueprintItem['code'],
            'is_active' => true,
            'is_featured' => false,
            'published_at' => $blogSource['post']->published_at ?? ($xmlSource['published_at'] ?? null),
            'sort_order' => (int) $blueprintItem['sort_order'],
            'payload' => array_merge((array) ($existing?->payload ?? []), array_filter([
                'group_key' => $blueprintItem['group_key'],
                'group_title' => $blueprintItem['group_title'],
                'source_hint_blog_slug' => $blueprintItem['blog_slug'] !== '' ? $blueprintItem['blog_slug'] : null,
                'import_source' => $blogSource !== null ? 'blog' : ($xmlSource !== null ? 'xml' : 'blueprint'),
            ], static fn (mixed $value): bool => $value !== null && $value !== '')),
            'created_by' => $existing?->created_by ?? $userId,
            'updated_by' => $userId,
        ];

        if ($existing instanceof CallPost) {
            $existing->fill($attributes);
            $existing->save();
            $post = $existing->fresh(['translations' => fn ($query) => $query->where('locale', $locale), 'media', 'categories']);
        } else {
            $post = CallPost::query()->create($attributes);
        }

        $status = $existing ? 'updated' : 'created';
        $sourceType = 'stub';
        $assetCount = 0;

        if ($blogSource !== null) {
            $sourceType = 'blog';
            $translation = $blogSource['translation'];
            $resolvedTitle = trim((string) ($translation?->title ?? $blueprintItem['title']));
            $slugBase = trim((string) ($translation?->slug ?? Str::slug($resolvedTitle)));

            $post->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $resolvedTitle,
                    'slug' => $this->resolveUniqueSlug($slugBase, $locale, (int) $post->id),
                    'excerpt' => $translation?->excerpt,
                    'body_html' => $translation?->body_html,
                    'meta_title' => $translation?->meta_title ?: $resolvedTitle,
                    'meta_description' => $translation?->meta_description,
                    'payload' => array_merge((array) ($post->translations->firstWhere('locale', $locale)?->payload ?? []), [
                        'import_source' => 'blog',
                        'source_post_id' => (int) $blogSource['post']->id,
                        'source_slug' => (string) ($translation?->slug ?? ''),
                    ]),
                ]
            );

            $this->syncCategories($post, $category, (int) $blueprintItem['sort_order']);
            if ($syncAssets) {
                $this->syncMediaFromBlog($post, $blogSource['post']);
            }
        } elseif ($xmlSource !== null) {
            $sourceType = 'xml';
            $mediaMap = $syncAssets ? $this->syncMediaFromXml($post, $xmlSource, $force) : [];
            $assetLocalization = $syncAssets
                ? $this->localizeBodyAssets(
                    code: $blueprintItem['code'],
                    bodyHtml: (string) ($xmlSource['body_html'] ?? ''),
                    initialMap: $mediaMap,
                    force: $force
                )
                : [
                    'body_html' => $this->wordPressBlogImportService->sanitizeImportedBodyHtml(
                        (string) ($xmlSource['body_html'] ?? '')
                    ),
                    'localized_count' => 0,
                ];
            $assetCount = count($mediaMap) + (int) $assetLocalization['localized_count'];
            $preferredTitle = trim((string) ($blueprintItem['preferred_title'] ?? ''));
            $resolvedTitle = $preferredTitle !== ''
                ? $preferredTitle
                : (trim((string) ($xmlSource['title'] ?? $blueprintItem['title'])) ?: $blueprintItem['title']);
            $existingSlug = trim((string) ($existing?->translations->firstWhere('locale', $locale)?->slug ?? ''));
            $slugBase = $existingSlug !== ''
                ? $existingSlug
                : (trim((string) ($xmlSource['source_slug'] ?? '')) ?: Str::slug($resolvedTitle));

            $post->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $resolvedTitle,
                    'slug' => $this->resolveUniqueSlug($slugBase, $locale, (int) $post->id),
                    'excerpt' => $xmlSource['excerpt'] ?: null,
                    'body_html' => $assetLocalization['body_html'] !== '' ? $assetLocalization['body_html'] : null,
                    'meta_title' => $resolvedTitle,
                    'meta_description' => $xmlSource['meta_description'] ?: null,
                    'payload' => array_merge((array) ($post->translations->firstWhere('locale', $locale)?->payload ?? []), [
                        'import_source' => 'xml',
                        'wp_post_id' => $xmlSource['wp_post_id'],
                        'source_slug' => $xmlSource['source_slug'],
                        'legacy_url' => $xmlSource['legacy_url'],
                        'legacy_path' => $xmlSource['legacy_path'],
                    ]),
                ]
            );

            $this->syncCategories($post, $category, (int) $blueprintItem['sort_order']);
        } else {
            $translation = $post->translations()->where('locale', $locale)->first();
            $post->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $blueprintItem['title'],
                    'slug' => $this->resolveUniqueSlug(Str::slug($blueprintItem['title']), $locale, (int) $post->id),
                    'excerpt' => $translation?->excerpt,
                    'body_html' => $translation?->body_html,
                    'meta_title' => $translation?->meta_title ?: $blueprintItem['title'],
                    'meta_description' => $translation?->meta_description,
                    'payload' => array_merge((array) ($translation?->payload ?? []), [
                        'import_source' => 'blueprint',
                        'needs_content' => true,
                    ]),
                ]
            );

            $this->syncCategories($post, $category, (int) $blueprintItem['sort_order']);
        }

        $post->loadMissing(['translations' => fn ($query) => $query->where('locale', $locale)]);
        $translation = $post->translations->first();

        return [
            'id' => (int) $post->id,
            'code' => (string) $post->code,
            'title' => (string) ($translation?->title ?? $blueprintItem['title']),
            'slug' => (string) ($translation?->slug ?? ''),
            'category' => $blueprintItem['group_title'],
            'source' => $sourceType,
            'status' => $status,
            'asset_count' => $assetCount,
        ];
    }

    /**
     * @param  array{
     *     group_key:string,
     *     group_title:string,
     *     tone:string,
     *     title:string,
     *     code:string,
     *     sort_order:int,
     *     blog_slug:string
     * }  $blueprintItem
     */
    private function resolveExistingPost(array $blueprintItem, string $locale, ?array $xmlSource = null): ?CallPost
    {
        $sourceSlug = trim((string) ($xmlSource['source_slug'] ?? $blueprintItem['blog_slug'] ?? ''));
        $wpPostId = isset($xmlSource['wp_post_id']) ? (int) $xmlSource['wp_post_id'] : null;
        $posts = CallPost::query()
            ->with([
                'translations' => fn ($query) => $query->where('locale', $locale),
                'media',
                'categories',
            ])
            ->where(function (Builder $query) use ($blueprintItem, $locale, $sourceSlug, $wpPostId): void {
                $query->where('code', $blueprintItem['code']);

                if ($sourceSlug !== '') {
                    $query->orWhereHas('translations', function (Builder $translationQuery) use ($locale, $sourceSlug): void {
                        $translationQuery
                            ->where('locale', $locale)
                            ->where(function (Builder $identityQuery) use ($sourceSlug): void {
                                $identityQuery
                                    ->where('slug', $sourceSlug)
                                    ->orWhere('payload->source_slug', $sourceSlug);
                            });
                    });
                }

                if ($wpPostId !== null && $wpPostId > 0) {
                    $query->orWhereHas('translations', function (Builder $translationQuery) use ($locale, $wpPostId): void {
                        $translationQuery
                            ->where('locale', $locale)
                            ->where('payload->wp_post_id', $wpPostId);
                    });
                }
            })
            ->get();

        $publicSlugMatches = $sourceSlug === ''
            ? collect()
            : $posts->filter(fn (CallPost $post): bool => trim((string) $post->translations->first()?->slug) === $sourceSlug);
        if ($publicSlugMatches->count() === 1) {
            return $publicSlugMatches->first();
        }

        $identityMatches = $posts->filter(function (CallPost $post) use ($sourceSlug, $wpPostId): bool {
            $translation = $post->translations->first();

            return ($sourceSlug !== '' && trim((string) data_get($translation?->payload, 'source_slug')) === $sourceSlug)
                || ($wpPostId !== null && (int) data_get($translation?->payload, 'wp_post_id') === $wpPostId);
        });

        if ($identityMatches->count() === 1) {
            return $identityMatches->first();
        }

        $codeMatches = $posts->where('code', $blueprintItem['code']);
        if ($codeMatches->count() === 1) {
            return $codeMatches->first();
        }

        if ($identityMatches->count() > 1 || $publicSlugMatches->count() > 1) {
            throw new RuntimeException(sprintf(
                'Ambiguous EU funds call identity for "%s". No records were changed.',
                $blueprintItem['title']
            ));
        }

        return null;
    }

    private function loadCallPost(int $postId, string $locale): CallPost
    {
        return CallPost::query()
            ->with([
                'translations' => fn ($query) => $query->where('locale', $locale),
                'media',
                'categories',
            ])
            ->findOrFail($postId);
    }

    /**
     * Build one global identity assignment before changing any call post. A
     * public slug and the WXR identity are stable; category position is not.
     *
     * @param  Collection<int, array<string,mixed>>  $blueprintItems
     * @param  Collection<int, array<string,mixed>>  $xmlPosts
     * @param  array<string, Category>  $categories
     * @return array{
     *     assignments:array<string,int|null>,
     *     extra_ids:array<int,int>,
     *     managed_ids:array<int,int>,
     *     category_ids:array<int,int>,
     *     identities:array<string,array{source_slug:string,wp_post_id:int}>
     * }
     */
    private function buildFullReconciliationPlan(
        Collection $blueprintItems,
        Collection $xmlPosts,
        array $categories,
        string $locale
    ): array {
        $categoryIds = collect($categories)
            ->filter(fn (mixed $category): bool => $category instanceof Category)
            ->map(fn (Category $category): int => (int) $category->id)
            ->values()
            ->all();

        Category::query()->whereKey($categoryIds)->lockForUpdate()->get();
        DB::table('content_call_post_category')
            ->whereIn('category_id', $categoryIds)
            ->lockForUpdate()
            ->get();

        $posts = CallPost::query()
            ->with([
                'translations' => fn ($query) => $query->where('locale', $locale),
                'categories',
            ])
            ->lockForUpdate()
            ->get();

        if ($posts->isNotEmpty()) {
            DB::table('content_call_post_translations')
                ->whereIn('post_id', $posts->modelKeys())
                ->where('locale', $locale)
                ->lockForUpdate()
                ->get();
        }

        $manifest = $blueprintItems->mapWithKeys(function (array $item) use ($xmlPosts): array {
            $sourceSlug = trim((string) ($item['blog_slug'] ?? ''));
            if ($sourceSlug === '') {
                throw new RuntimeException(sprintf(
                    'Full EU funds reconciliation is missing a stable source slug for "%s".',
                    $item['title']
                ));
            }

            $matches = $xmlPosts->filter(
                static fn (array $post): bool => trim((string) ($post['source_slug'] ?? '')) === $sourceSlug
            )->values();
            if ($matches->count() !== 1) {
                throw new RuntimeException(sprintf(
                    'Full EU funds reconciliation expected one WXR post for "%s" (%s), found %d. No records were changed.',
                    $item['title'],
                    $sourceSlug,
                    $matches->count()
                ));
            }

            $xmlSource = $matches->first();
            $wpPostId = (int) ($xmlSource['wp_post_id'] ?? 0);
            if ($wpPostId <= 0) {
                throw new RuntimeException(sprintf(
                    'Full EU funds reconciliation is missing the WordPress post ID for "%s". No records were changed.',
                    $item['title']
                ));
            }

            return [(string) $item['code'] => [
                'item' => $item,
                'source_slug' => $sourceSlug,
                'wp_post_id' => $wpPostId,
            ]];
        });

        if ($manifest->pluck('source_slug')->unique()->count() !== $manifest->count()
            || $manifest->pluck('wp_post_id')->unique()->count() !== $manifest->count()) {
            throw new RuntimeException('The EU funds WXR identities are not unique. No records were changed.');
        }

        $assignments = [];
        $reservedPostIds = [];

        $this->assignReconciliationTier(
            manifest: $manifest,
            posts: $posts,
            assignments: $assignments,
            reservedPostIds: $reservedPostIds,
            matcher: static function (array $target, CallPost $post): bool {
                return trim((string) $post->translations->first()?->slug) === $target['source_slug'];
            },
            failOnMultiple: true,
            tier: 'public slug'
        );
        $this->assignReconciliationTier(
            manifest: $manifest,
            posts: $posts,
            assignments: $assignments,
            reservedPostIds: $reservedPostIds,
            matcher: static function (array $target, CallPost $post): bool {
                $translation = $post->translations->first();

                return trim((string) data_get($translation?->payload, 'source_slug')) === $target['source_slug']
                    || (int) data_get($translation?->payload, 'wp_post_id') === $target['wp_post_id'];
            },
            failOnMultiple: true,
            tier: 'WXR identity'
        );
        $this->assignReconciliationTier(
            manifest: $manifest,
            posts: $posts,
            assignments: $assignments,
            reservedPostIds: $reservedPostIds,
            matcher: static fn (array $target, CallPost $post): bool => $post->code === $target['item']['code'],
            failOnMultiple: true,
            tier: 'canonical code'
        );

        foreach ($manifest as $code => $target) {
            if (array_key_exists($code, $assignments)) {
                continue;
            }

            $remainingEvidence = $posts->reject(
                fn (CallPost $post): bool => isset($reservedPostIds[(int) $post->id])
            )->filter(function (CallPost $post) use ($target): bool {
                $translation = $post->translations->first();

                return trim((string) $translation?->slug) === $target['source_slug']
                    || trim((string) data_get($translation?->payload, 'source_slug')) === $target['source_slug']
                    || (int) data_get($translation?->payload, 'wp_post_id') === $target['wp_post_id']
                    || $post->code === $target['item']['code'];
            });

            if ($remainingEvidence->count() > 1) {
                throw new RuntimeException(sprintf(
                    'Ambiguous EU funds call identity for "%s". No records were changed.',
                    $target['item']['title']
                ));
            }

            if ($remainingEvidence->count() === 1) {
                $postId = (int) $remainingEvidence->first()->id;
                $assignments[$code] = $postId;
                $reservedPostIds[$postId] = true;
            } else {
                $assignments[$code] = null;
            }
        }

        $visiblePostIds = DB::table('content_call_post_category')
            ->whereIn('category_id', $categoryIds)
            ->pluck('post_id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();
        $selectedPostIds = collect($assignments)
            ->filter(static fn (mixed $id): bool => is_int($id) && $id > 0)
            ->values();
        $extraIds = $visiblePostIds->diff($selectedPostIds)->values();
        $managedIds = $posts
            ->filter(fn (CallPost $post): bool => $this->isManagedCallPost($post))
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->values();
        $unmanagedExtraIds = $extraIds->diff($managedIds)->values();

        if ($unmanagedExtraIds->isNotEmpty()) {
            throw new RuntimeException(sprintf(
                'Full EU funds import found unmanaged call record(s) in a status category: %s. No records were changed.',
                $unmanagedExtraIds->implode(', ')
            ));
        }

        return [
            'assignments' => $assignments,
            'extra_ids' => $extraIds->all(),
            'managed_ids' => $managedIds->all(),
            'category_ids' => $categoryIds,
            'identities' => $manifest->map(
                static fn (array $target): array => [
                    'source_slug' => (string) $target['source_slug'],
                    'wp_post_id' => (int) $target['wp_post_id'],
                ]
            )->all(),
        ];
    }

    /**
     * @param  Collection<string,array<string,mixed>>  $manifest
     * @param  Collection<int,CallPost>  $posts
     * @param  array<string,int|null>  $assignments
     * @param  array<int,bool>  $reservedPostIds
     * @param  callable(array<string,mixed>,CallPost):bool  $matcher
     */
    private function assignReconciliationTier(
        Collection $manifest,
        Collection $posts,
        array &$assignments,
        array &$reservedPostIds,
        callable $matcher,
        bool $failOnMultiple,
        string $tier
    ): void {
        $proposals = [];

        foreach ($manifest as $code => $target) {
            if (array_key_exists($code, $assignments)) {
                continue;
            }

            $matches = $posts
                ->reject(fn (CallPost $post): bool => isset($reservedPostIds[(int) $post->id]))
                ->filter(fn (CallPost $post): bool => $matcher($target, $post))
                ->values();

            if ($matches->count() > 1) {
                if ($failOnMultiple) {
                    throw new RuntimeException(sprintf(
                        'Ambiguous EU funds %s identity for "%s". No records were changed.',
                        $tier,
                        $target['item']['title']
                    ));
                }

                continue;
            }

            if ($matches->count() === 1) {
                $proposals[$code] = (int) $matches->first()->id;
            }
        }

        $collisions = collect($proposals)->groupBy(static fn (int $postId): int => $postId)
            ->filter(static fn (Collection $codes): bool => $codes->count() > 1);
        if ($collisions->isNotEmpty()) {
            throw new RuntimeException(sprintf(
                'One existing call record matched multiple EU funds targets during %s reconciliation. No records were changed.',
                $tier
            ));
        }

        foreach ($proposals as $code => $postId) {
            $assignments[$code] = $postId;
            $reservedPostIds[$postId] = true;
        }
    }

    /**
     * @param  array{assignments:array<string,int|null>,extra_ids:array<int,int>,managed_ids:array<int,int>}  $reconciliation
     * @param  Collection<int,array<string,mixed>>  $blueprintItems
     */
    private function prepareReconciliationCodes(array $reconciliation, Collection $blueprintItems): void
    {
        $desiredCodes = $blueprintItems->pluck('code')->all();
        $assignmentByCode = $reconciliation['assignments'];
        $selectedPostIds = collect($assignmentByCode)->filter()->map(static fn (mixed $id): int => (int) $id);
        $mutablePostIds = $selectedPostIds
            ->merge($reconciliation['extra_ids'])
            ->unique()
            ->values();

        $owners = CallPost::query()->whereIn('code', $desiredCodes)->get(['id', 'code']);
        foreach ($owners as $owner) {
            $assignedOwnerId = $assignmentByCode[(string) $owner->code] ?? null;
            if ((int) $assignedOwnerId === (int) $owner->id) {
                continue;
            }

            if (! $mutablePostIds->contains((int) $owner->id)) {
                throw new RuntimeException(sprintf(
                    'Canonical EU funds code "%s" belongs to an unmanaged record. No records were changed.',
                    $owner->code
                ));
            }

            $temporaryCode = sprintf(
                'eu-funds-reconcile-%d-%s',
                (int) $owner->id,
                substr(sha1((string) $owner->code), 0, 12)
            );
            CallPost::query()->whereKey($owner->id)->update(['code' => $temporaryCode]);
        }
    }

    /**
     * @param  array{
     *     extra_ids:array<int,int>,
     *     category_ids:array<int,int>,
     *     identities:array<string,array{source_slug:string,wp_post_id:int}>
     * }  $reconciliation
     * @param  array<string,Category>  $categories
     * @param  Collection<int,array<string,mixed>>  $blueprintItems
     * @param  array<int,array<string,mixed>>  $importedRows
     */
    private function completeFullReconciliation(
        array $reconciliation,
        array $categories,
        Collection $blueprintItems,
        array $importedRows,
        string $locale
    ): void {
        if ($reconciliation['extra_ids'] !== []) {
            DB::table('content_call_post_category')
                ->whereIn('post_id', $reconciliation['extra_ids'])
                ->whereIn('category_id', $reconciliation['category_ids'])
                ->delete();
        }

        $importedIds = collect($importedRows)
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id);
        if ($importedIds->count() !== $blueprintItems->count()
            || $importedIds->unique()->count() !== $blueprintItems->count()) {
            throw new RuntimeException('EU funds reconciliation did not produce 32 distinct call records. Changes were rolled back.');
        }

        $expectedCounts = $blueprintItems->countBy('group_key')->all();
        $actualCounts = $this->statusCategoryCounts($categories);
        foreach ($expectedCounts as $groupKey => $expectedCount) {
            if (($actualCounts[$groupKey] ?? -1) !== $expectedCount) {
                throw new RuntimeException(sprintf(
                    'EU funds reconciliation postflight failed for %s: expected %d, found %d. Changes were rolled back.',
                    $groupKey,
                    $expectedCount,
                    $actualCounts[$groupKey] ?? 0
                ));
            }
        }

        $pivotCounts = DB::table('content_call_post_category')
            ->whereIn('post_id', $importedIds->all())
            ->whereIn('category_id', $reconciliation['category_ids'])
            ->select('post_id', DB::raw('COUNT(*) AS aggregate'))
            ->groupBy('post_id')
            ->pluck('aggregate', 'post_id');
        if ($pivotCounts->count() !== $blueprintItems->count()
            || $pivotCounts->contains(fn (mixed $count): bool => (int) $count !== 1)) {
            throw new RuntimeException('EU funds reconciliation postflight found an invalid status assignment. Changes were rolled back.');
        }

        foreach ($blueprintItems as $blueprintItem) {
            $code = (string) $blueprintItem['code'];
            $identity = $reconciliation['identities'][$code] ?? null;
            $category = $categories[$blueprintItem['group_key']] ?? null;
            $post = CallPost::query()
                ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
                ->where('code', $code)
                ->first();
            $translation = $post?->translations->first();
            $expectedTitle = trim((string) ($blueprintItem['preferred_title'] ?? ''))
                ?: (string) $blueprintItem['title'];

            if (! is_array($identity)
                || ! $post instanceof CallPost
                || ! $category instanceof Category
                || $translation === null
                || (string) $translation->title !== $expectedTitle
                || trim((string) data_get($translation->payload, 'source_slug')) !== $identity['source_slug']
                || (int) data_get($translation->payload, 'wp_post_id') !== $identity['wp_post_id']
                || trim((string) $translation->body_html) === ''
                || (int) $post->sort_order !== (int) $blueprintItem['sort_order']) {
                throw new RuntimeException(sprintf(
                    'EU funds reconciliation identity postflight failed for "%s". Changes were rolled back.',
                    $blueprintItem['title']
                ));
            }

            $pivot = DB::table('content_call_post_category')
                ->where('post_id', $post->id)
                ->where('category_id', $category->id)
                ->first(['sort_order', 'is_primary']);
            if ($pivot === null
                || (int) $pivot->sort_order !== (int) $blueprintItem['sort_order']
                || ! (bool) $pivot->is_primary) {
                throw new RuntimeException(sprintf(
                    'EU funds reconciliation ordering postflight failed for "%s". Changes were rolled back.',
                    $blueprintItem['title']
                ));
            }
        }
    }

    /**
     * Media and storage writes run only after the database identity/status
     * transaction commits. A failed download therefore cannot cause a DB
     * retry to repeat filesystem side effects; a later re-import can retry it.
     *
     * @param  Collection<int,array<string,mixed>>  $blueprintItems
     * @param  Collection<int,array<string,mixed>>  $xmlPosts
     * @param  Collection<int,array<string,mixed>>  $blogSources
     * @return array{counts:array<string,int>,failed_codes:array<int,string>}
     */
    private function syncReconciledAssets(
        Collection $blueprintItems,
        Collection $xmlPosts,
        Collection $blogSources,
        string $locale,
        bool $force
    ): array {
        $counts = [];
        $failedCodes = [];

        foreach ($blueprintItems as $blueprintItem) {
            $code = (string) $blueprintItem['code'];

            try {
                $post = CallPost::query()
                    ->with([
                        'translations' => fn ($query) => $query->where('locale', $locale),
                        'media',
                    ])
                    ->where('code', $code)
                    ->firstOrFail();
                $xmlSource = $this->resolveXmlSource($blueprintItem, $xmlPosts);

                if ($xmlSource !== null) {
                    $mediaMap = $this->syncMediaFromXml($post, $xmlSource, $force);
                    $localized = $this->localizeBodyAssets(
                        code: $code,
                        bodyHtml: (string) ($xmlSource['body_html'] ?? ''),
                        initialMap: $mediaMap,
                        force: $force
                    );
                    $post->translations()
                        ->where('locale', $locale)
                        ->update([
                            'body_html' => $localized['body_html'] !== '' ? $localized['body_html'] : null,
                        ]);
                    $counts[$code] = count($mediaMap) + (int) $localized['localized_count'];

                    continue;
                }

                $blogSource = $this->resolveBlogSource($blueprintItem, $blogSources);
                if ($blogSource !== null) {
                    $this->syncMediaFromBlog($post, $blogSource['post']);
                }
                $counts[$code] = 0;
            } catch (\Throwable $exception) {
                report($exception);
                $counts[$code] = 0;
                $failedCodes[] = $code;
            }
        }

        return [
            'counts' => $counts,
            'failed_codes' => $failedCodes,
        ];
    }

    /** @param array<string,Category> $categories */
    private function statusCategoryCounts(array $categories): array
    {
        $counts = [];
        foreach ($categories as $groupKey => $category) {
            if ($category instanceof Category) {
                $counts[$groupKey] = DB::table('content_call_post_category')
                    ->where('category_id', $category->id)
                    ->count();
            }
        }

        return $counts;
    }

    private function isManagedCallPost(CallPost $post): bool
    {
        $postSource = trim((string) data_get($post->payload, 'import_source'));
        $translationSource = trim((string) data_get($post->translations->first()?->payload, 'import_source'));
        $managedSources = ['eu_funds_calls', 'xml', 'blog', 'blueprint'];

        return str_starts_with((string) $post->code, 'eu-funds-call-')
            || in_array($postSource, $managedSources, true)
            || in_array($translationSource, $managedSources, true)
            || (string) $post->code === 'gs-integrator-2018';
    }

    private function syncCategories(CallPost $post, Category $category, int $sortOrder): void
    {
        $post->categories()->sync([
            (int) $category->id => [
                'sort_order' => $sortOrder,
                'is_primary' => true,
            ],
        ]);
    }

    /**
     * @param  array{
     *     group_key:string,
     *     group_title:string,
     *     tone:string,
     *     title:string,
     *     code:string,
     *     sort_order:int,
     *     blog_slug:string
     * }  $blueprintItem
     * @param  Collection<int, array{
     *     post:BlogPost,
     *     translation:\App\Models\Content\Blog\BlogPostTranslation|null,
     *     title:string,
     *     slug:string,
     *     normalized_title:string
     * }>  $blogSources
     * @return array{
     *     post:BlogPost,
     *     translation:\App\Models\Content\Blog\BlogPostTranslation|null,
     *     title:string,
     *     slug:string,
     *     normalized_title:string
     * }|null
     */
    private function resolveBlogSource(array $blueprintItem, Collection $blogSources): ?array
    {
        $blogSlug = trim((string) ($blueprintItem['blog_slug'] ?? ''));
        if ($blogSlug !== '') {
            $match = $blogSources->first(fn (array $row): bool => $row['slug'] === $blogSlug);
            if (is_array($match)) {
                return $match;
            }
        }

        return $this->resolveBestTitleMatch(
            title: (string) $blueprintItem['title'],
            candidates: $blogSources,
            titleResolver: static fn (array $row): string => (string) $row['title'],
            normalizedResolver: static fn (array $row): string => (string) $row['normalized_title']
        );
    }

    /**
     * @param  array{
     *     group_key:string,
     *     group_title:string,
     *     tone:string,
     *     title:string,
     *     code:string,
     *     sort_order:int,
     *     blog_slug:string,
     *     xml_slug_hint:string,
     *     preferred_title:string
     * }  $blueprintItem
     * @param  Collection<int, array<string,mixed>>  $xmlPosts
     * @return array<string,mixed>|null
     */
    private function resolveXmlSource(array $blueprintItem, Collection $xmlPosts): ?array
    {
        $blogSlug = trim((string) ($blueprintItem['blog_slug'] ?? ''));
        if ($blogSlug !== '') {
            $match = $xmlPosts->first(
                static fn (array $row): bool => trim((string) ($row['source_slug'] ?? '')) === $blogSlug
            );

            if (is_array($match)) {
                return $match;
            }
        }

        $xmlSlugHint = trim((string) ($blueprintItem['xml_slug_hint'] ?? ''));
        if ($xmlSlugHint !== '') {
            $match = $xmlPosts->first(function (array $row) use ($xmlSlugHint): bool {
                return $this->normalizeMatchValue((string) ($row['source_slug'] ?? '')) === $this->normalizeMatchValue($xmlSlugHint);
            });

            if (is_array($match)) {
                return $match;
            }
        }

        return $this->resolveBestTitleMatch(
            title: (string) $blueprintItem['title'],
            candidates: $xmlPosts,
            titleResolver: static fn (array $row): string => (string) ($row['title'] ?? ''),
            normalizedResolver: static fn (array $row): string => (string) ($row['normalized_title'] ?? '')
        );
    }

    /**
     * @template T of array
     *
     * @param  Collection<int, T>  $candidates
     * @param  callable(T):string  $titleResolver
     * @param  callable(T):string  $normalizedResolver
     * @return T|null
     */
    private function resolveBestTitleMatch(
        string $title,
        Collection $candidates,
        callable $titleResolver,
        callable $normalizedResolver
    ): ?array {
        $candidateTokens = $this->titleCandidates($title);
        $best = null;
        $bestScore = PHP_INT_MAX;

        foreach ($candidates as $candidate) {
            $candidateTitle = $titleResolver($candidate);
            $candidateNormalized = $normalizedResolver($candidate);

            foreach ($candidateTokens as $token) {
                $score = $this->matchScore($token, $candidateNormalized, $candidateTitle);
                if ($score >= $bestScore) {
                    continue;
                }

                $best = $candidate;
                $bestScore = $score;
            }
        }

        return $bestScore <= 3 ? $best : null;
    }

    private function matchScore(string $needle, string $haystack, string $rawTitle): int
    {
        if ($needle === '' || $haystack === '') {
            return 99;
        }

        if ($needle === $haystack) {
            return 0;
        }

        if (str_starts_with($haystack, $needle) || str_starts_with($needle, $haystack)) {
            return 1;
        }

        if (str_contains($haystack, $needle) || str_contains($needle, $haystack)) {
            return 2;
        }

        $rawNormalized = $this->normalizeMatchValue($rawTitle);

        return str_contains($rawNormalized, $needle) ? 3 : 99;
    }

    /**
     * @return array<int, string>
     */
    private function titleCandidates(string $title): array
    {
        $normalized = $this->normalizeMatchValue($title);
        $candidates = [$normalized];
        $aliases = [
            'bespovratne potpore za komercijalizaciju inovacija' => [
                'komercijalizacija inovacija',
            ],
        ];

        foreach ([
            'poziv u najavi',
            'natjecaj u najavi',
            'objavljen poziv',
            'otvoren poziv',
            'blog',
        ] as $prefix) {
            if (str_starts_with($normalized, $prefix.' ')) {
                $candidates[] = trim(Str::after($normalized, $prefix));
            }
        }

        if (str_contains($normalized, ' 3 poziv')) {
            $candidates[] = str_replace(' 3 poziv', '', $normalized);
        }

        if (str_contains($normalized, ' msp ova')) {
            $candidates[] = str_replace(' msp ova', ' msp', $normalized);
        }

        foreach (array_values($candidates) as $candidate) {
            if (str_contains($candidate, 'partnerstava')) {
                $candidates[] = str_replace('partnerstava', 'partnerstva', $candidate);
            }

            if (str_contains($candidate, ' spin step')) {
                $candidates[] = str_replace(' spin step', ' spin', $candidate);
                $candidates[] = str_replace(' spin step', ' faza ii spin', $candidate);
            }
        }

        foreach ($aliases[$normalized] ?? [] as $alias) {
            $candidates[] = $alias;
        }

        return array_values(array_unique(array_filter(array_map(
            fn (string $value): string => trim($value),
            $candidates
        ))));
    }

    private function normalizeMatchValue(string $value): string
    {
        $normalized = Str::of(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'))
            ->replace(["\u{00A0}", "\u{2013}", "\u{2014}", '/', '(', ')', '"', '“', '”', ':', ',', '.', '–'], ' ')
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->value();

        return trim($normalized);
    }

    private function syncMediaFromBlog(CallPost $target, BlogPost $source): void
    {
        $this->clearImportedMedia($target, ['blog', 'xml']);

        $mapping = [
            'blog_cover' => 'call_cover',
            'blog_gallery' => 'call_gallery',
        ];

        foreach ($mapping as $sourceCollection => $targetCollection) {
            $order = 1;

            foreach ($source->getMedia($sourceCollection) as $media) {
                $sourcePath = $media->getPath();
                if (! is_file($sourcePath)) {
                    continue;
                }

                try {
                    $newMedia = $target->addMedia($sourcePath)
                        ->usingName((string) ($media->name ?? $media->file_name ?? $target->code))
                        ->usingFileName((string) ($media->file_name ?? basename($sourcePath)))
                        ->withCustomProperties([
                            'import_source' => 'blog',
                            'source_media_id' => (int) $media->id,
                            'source_collection' => $sourceCollection,
                        ])
                        ->toMediaCollection($targetCollection);

                    $newMedia->forceFill([
                        'order_column' => $order++,
                    ])->save();
                } catch (\Throwable) {
                    continue;
                }
            }
        }
    }

    /**
     * @param  array<string, mixed>  $xmlSource
     * @return array<string, string>
     */
    private function syncMediaFromXml(CallPost $target, array $xmlSource, bool $force): array
    {
        $this->clearImportedMedia($target, ['blog']);

        $map = [];
        $desiredCoverUrls = [];
        $desiredGalleryUrls = [];

        $featuredImageUrl = $this->normalizeRemoteUrl((string) ($xmlSource['featured_image_url'] ?? ''));
        if ($featuredImageUrl !== '') {
            $desiredCoverUrls[] = $featuredImageUrl;
            $cover = $this->findOrImportImageMedia($target, $featuredImageUrl, 'call_cover', (string) ($xmlSource['title'] ?? $target->code), $force);
            if ($cover instanceof Media) {
                $map[$featuredImageUrl] = $cover->getUrl();
            }
        }

        foreach (array_values(array_unique((array) ($xmlSource['inline_image_urls'] ?? []))) as $index => $remoteUrl) {
            $remoteUrl = $this->normalizeRemoteUrl((string) $remoteUrl);
            if ($remoteUrl === '' || isset($map[$remoteUrl])) {
                continue;
            }

            $desiredGalleryUrls[] = $remoteUrl;
            $galleryMedia = $this->findOrImportImageMedia(
                $target,
                $remoteUrl,
                'call_gallery',
                sprintf('%s image %d', (string) ($xmlSource['title'] ?? $target->code), $index + 1),
                $force
            );

            if ($galleryMedia instanceof Media) {
                $map[$remoteUrl] = $galleryMedia->getUrl();
            }
        }

        $this->clearObsoleteImportedMedia($target, 'call_cover', $desiredCoverUrls, 'xml');
        $this->clearObsoleteImportedMedia($target, 'call_gallery', $desiredGalleryUrls, 'xml');

        return $map;
    }

    /**
     * @param  array<string, string>  $initialMap
     * @return array{body_html:string,localized_count:int}
     */
    private function localizeBodyAssets(string $code, string $bodyHtml, array $initialMap, bool $force): array
    {
        $assetMap = $initialMap;

        foreach ($this->extractHtmlAssetUrls($bodyHtml) as $remoteUrl) {
            if (isset($assetMap[$remoteUrl]) || ! $this->shouldLocalizeRemoteAsset($remoteUrl)) {
                continue;
            }

            $localizedUrl = $this->localizeRemoteAsset($remoteUrl, $code, $force);
            if ($localizedUrl !== null) {
                $assetMap[$remoteUrl] = $localizedUrl;
            }
        }

        $rewrittenBodyHtml = $this->rewriteBodyAssetUrls($bodyHtml, $assetMap);

        return [
            'body_html' => $this->wordPressBlogImportService->sanitizeImportedBodyHtml($rewrittenBodyHtml),
            'localized_count' => max(0, count($assetMap) - count($initialMap)),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function extractHtmlAssetUrls(string $html): array
    {
        preg_match_all('/\b(?:src|href)=(["\'])(.*?)\1/i', $html, $matches);

        return array_values(array_unique(array_filter(array_map(
            fn (mixed $url): string => $this->normalizeRemoteUrl((string) $url),
            $matches[2] ?? []
        ))));
    }

    /**
     * @param  array<string, string>  $assetUrlMap
     */
    private function rewriteBodyAssetUrls(string $html, array $assetUrlMap): string
    {
        if ($html === '' || $assetUrlMap === []) {
            return $html;
        }

        return preg_replace_callback(
            '/\b(src|href)=(["\'])(.*?)\2/i',
            function (array $matches) use ($assetUrlMap): string {
                $attribute = (string) $matches[1];
                $quote = (string) $matches[2];
                $original = (string) $matches[3];
                $normalized = $this->normalizeRemoteUrl($original);
                $replacement = $assetUrlMap[$normalized] ?? null;

                if ($replacement === null) {
                    return (string) $matches[0];
                }

                return sprintf('%s=%s%s%s', $attribute, $quote, $replacement, $quote);
            },
            $html
        ) ?? $html;
    }

    private function shouldLocalizeRemoteAsset(string $remoteUrl): bool
    {
        $url = trim($remoteUrl);
        if ($url === '') {
            return false;
        }

        $host = Str::lower((string) parse_url($url, PHP_URL_HOST));
        $path = Str::lower((string) parse_url($url, PHP_URL_PATH));

        return str_contains($host, 'alphacapitalis.com')
            && str_contains($path, '/wp-content/uploads/');
    }

    private function localizeRemoteAsset(string $remoteUrl, string $code, bool $force): ?string
    {
        if (! $this->shouldLocalizeRemoteAsset($remoteUrl)) {
            return null;
        }

        $extension = $this->resolveAssetExtension($remoteUrl);
        $hashedName = sha1($remoteUrl).'.'.$extension;
        $storagePath = 'call-posts/imported-assets/'.trim($code, '/').'/'.$hashedName;

        if (! $force && Storage::disk('public')->exists($storagePath)) {
            return Storage::disk('public')->url($storagePath);
        }

        try {
            $response = Http::timeout(90)
                ->retry(2, 400)
                ->withHeaders([
                    'User-Agent' => 'AlphaCapitalis-EuFundsCallImport/1.0',
                ])
                ->get($remoteUrl);
        } catch (\Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        Storage::disk('public')->put($storagePath, $response->body());

        return Storage::disk('public')->url($storagePath);
    }

    private function findOrImportImageMedia(
        CallPost $post,
        string $remoteUrl,
        string $collectionName,
        string $name,
        bool $force
    ): ?Media {
        $remoteUrl = $this->normalizeRemoteUrl($remoteUrl);
        if ($remoteUrl === '' || ! $this->shouldLocalizeRemoteAsset($remoteUrl)) {
            return null;
        }

        if (! $force) {
            $existing = collect($post->getMedia($collectionName))->first(function (Media $media) use ($remoteUrl): bool {
                return (string) data_get($media->custom_properties, 'import_source') === 'xml'
                    && (string) data_get($media->custom_properties, 'source_url') === $remoteUrl;
            });

            if ($existing instanceof Media && is_file($existing->getPath())) {
                return $existing;
            }
        }

        try {
            $response = Http::timeout(60)
                ->retry(2, 250)
                ->withHeaders([
                    'User-Agent' => 'AlphaCapitalis-EuFundsCallImport/1.0',
                    'Accept' => 'image/*,*/*;q=0.8',
                ])
                ->get($remoteUrl);
        } catch (\Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $contentType = Str::lower(trim((string) $response->header('Content-Type', '')));
        if ($contentType !== '' && ! str_starts_with($contentType, 'image/')) {
            return null;
        }

        $extension = $this->resolveAssetExtension($remoteUrl, $contentType);
        $tempPath = tempnam(sys_get_temp_dir(), 'call-import-');
        if ($tempPath === false) {
            return null;
        }

        $finalTempPath = $tempPath.'.'.$extension;
        @rename($tempPath, $finalTempPath);
        file_put_contents($finalTempPath, $response->body());

        try {
            $media = $post->addMedia($finalTempPath)
                ->usingName(Str::limit(trim($name), 200, ''))
                ->usingFileName($this->resolveMediaFileName($remoteUrl, $extension))
                ->withCustomProperties([
                    'import_source' => 'xml',
                    'source_url' => $remoteUrl,
                ])
                ->toMediaCollection($collectionName);

            return $media;
        } catch (\Throwable) {
            return null;
        } finally {
            if (is_file($finalTempPath)) {
                @unlink($finalTempPath);
            }
        }
    }

    /**
     * @param  array<int, string>  $desiredUrls
     */
    private function clearObsoleteImportedMedia(CallPost $post, string $collectionName, array $desiredUrls, string $source): void
    {
        $desired = array_values(array_unique(array_map([$this, 'normalizeRemoteUrl'], $desiredUrls)));

        foreach ($post->getMedia($collectionName) as $media) {
            if ((string) data_get($media->custom_properties, 'import_source') !== $source) {
                continue;
            }

            $sourceUrl = $this->normalizeRemoteUrl((string) data_get($media->custom_properties, 'source_url'));
            if (in_array($sourceUrl, $desired, true)) {
                continue;
            }

            $media->delete();
        }
    }

    /**
     * @param  array<int, string>  $sources
     */
    private function clearImportedMedia(CallPost $post, array $sources): void
    {
        foreach ($post->media as $media) {
            if (! in_array((string) data_get($media->custom_properties, 'import_source'), $sources, true)) {
                continue;
            }

            $media->delete();
        }
    }

    private function resolveUniqueSlug(string $base, string $locale, int $postId): string
    {
        $cleanBase = Str::slug($base);
        $slug = $cleanBase !== '' ? $cleanBase : 'poziv';
        $suffix = 2;

        while (
            CallPost::query()
                ->where('id', '!=', $postId)
                ->whereHas('translations', function (Builder $query) use ($locale, $slug): void {
                    $query->where('locale', $locale)->where('slug', $slug);
                })
                ->exists()
        ) {
            $slug = ($cleanBase !== '' ? $cleanBase : 'poziv').'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function resolveImportCode(string $title): string
    {
        $prefix = 'eu-funds-call-';
        $slug = Str::slug($title) ?: 'poziv';
        $hash = substr(sha1($title), 0, 8);
        $maxSlugLength = 120 - strlen($prefix) - 1 - strlen($hash);
        $trimmedSlug = trim(substr($slug, 0, max(1, $maxSlugLength)), '-');

        return $prefix.($trimmedSlug !== '' ? $trimmedSlug : 'poziv').'-'.$hash;
    }

    private function resolveAssetExtension(string $remoteUrl, string $contentType = ''): string
    {
        $path = (string) parse_url($remoteUrl, PHP_URL_PATH);
        $extension = strtolower(trim(pathinfo($path, PATHINFO_EXTENSION)));

        if ($extension !== '') {
            return match ($extension) {
                'jpeg' => 'jpg',
                default => $extension,
            };
        }

        return match (Str::lower($contentType)) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/avif' => 'avif',
            'application/pdf' => 'pdf',
            default => 'bin',
        };
    }

    private function resolveMediaFileName(string $remoteUrl, string $extension): string
    {
        $path = (string) parse_url($remoteUrl, PHP_URL_PATH);
        $baseName = pathinfo($path, PATHINFO_FILENAME);
        $baseName = Str::slug((string) $baseName) ?: 'asset';

        return $baseName.'.'.$extension;
    }

    private function normalizeRemoteUrl(string $url): string
    {
        $normalized = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($normalized === '') {
            return '';
        }

        if (str_starts_with($normalized, '//')) {
            $normalized = 'https:'.$normalized;
        }

        return preg_replace('/\s+/u', '', $normalized) ?? $normalized;
    }

    private function resolveLocale(?string $locale): string
    {
        $normalized = $this->normalizeLocale((string) $locale);
        if ($normalized !== '') {
            return $normalized;
        }

        try {
            $language = Language::query()
                ->where('is_active', true)
                ->orderByDesc('is_default')
                ->orderBy('sort_order')
                ->value('code');

            if (is_string($language) && trim($language) !== '') {
                return $this->normalizeLocale($language);
            }
        } catch (\Throwable) {
            // Fall back to the configured app locale.
        }

        return $this->normalizeLocale((string) config('app.locale', 'hr'));
    }

    private function normalizeLocale(string $locale): string
    {
        $normalized = Str::lower(trim($locale));

        if ($normalized === '') {
            return 'hr';
        }

        foreach (['_', '-'] as $separator) {
            if (str_contains($normalized, $separator)) {
                $normalized = (string) explode($separator, $normalized)[0];
            }
        }

        return $normalized !== '' ? $normalized : 'hr';
    }
}
