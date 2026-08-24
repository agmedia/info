<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Blog\BlogPostTranslation;
use App\Services\Content\ContentBlockResolver;
use App\Services\Front\StoreSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogController extends Controller
{
    use ResolvesFrontendView;

    /**
     * @var array<int, string>
     */
    private const RELATED_TITLE_STOP_WORDS = [
        'a',
        'an',
        'and',
        'are',
        'as',
        'at',
        'be',
        'by',
        'da',
        'do',
        'for',
        'from',
        'how',
        'i',
        'if',
        'ili',
        'in',
        'into',
        'is',
        'it',
        'iz',
        'na',
        'o',
        'od',
        'po',
        'sa',
        'se',
        'su',
        'the',
        'to',
        'u',
        'uz',
        'za',
    ];

    public function index(Request $request): View
    {
        return $this->renderIndex($request);
    }

    public function show(Request $request, string $slug): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale'));
        $resolvedCategory = $this->resolveBlogCategoryBySlug($slug, $locale, $fallbackLocale);

        if ($resolvedCategory) {
            return $this->renderIndex($request, $resolvedCategory);
        }

        $post = BlogPost::query()
            ->tap(function (Builder $query): void {
                $this->applyFrontPublishedConstraints($query);
            })
            ->whereHas('translations', function ($query) use ($locale, $fallbackLocale, $slug): void {
                $query->whereIn('locale', [$locale, $fallbackLocale])
                    ->where('slug', $slug);
            })
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
                'categories' => fn ($query) => $query
                    ->where('scope', Category::SCOPE_BLOG)
                    ->with([
                        'translations' => fn ($translationQuery) => $translationQuery
                            ->where('scope', Category::SCOPE_BLOG)
                            ->whereIn('locale', [$locale, $fallbackLocale]),
                    ]),
                'creator:id,name',
                'media',
            ])
            ->firstOrFail();

        $related = $this->resolveRelatedPosts($post, $locale, $fallbackLocale);

        return view($this->frontendView($request, 'blog.show'), [
            'post' => $post,
            'related' => $related,
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
            'isAdminPreview' => false,
        ]);
    }

    public function preview(Request $request, BlogPost $post): View
    {
        $requestedLocale = strtolower(trim((string) $request->query('locale', '')));
        $locale = preg_match('/^[a-z]{2}(?:[-_][a-z]{2})?$/', $requestedLocale) === 1
            ? $requestedLocale
            : (string) app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale'));
        $locales = array_values(array_unique([$locale, $fallbackLocale]));

        app()->setLocale($locale);

        $post->load([
            'translations' => fn ($query) => $query->whereIn('locale', $locales),
            'categories' => fn ($query) => $query
                ->where('scope', Category::SCOPE_BLOG)
                ->with([
                    'translations' => fn ($translationQuery) => $translationQuery
                        ->where('scope', Category::SCOPE_BLOG)
                        ->whereIn('locale', $locales),
                ]),
            'creator:id,name',
            'media',
        ]);

        abort_unless($post->translations->isNotEmpty(), 404);

        return view($this->frontendView($request, 'blog.show'), [
            'post' => $post,
            'related' => $this->resolveRelatedPosts($post, $locale, $fallbackLocale),
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
            'isAdminPreview' => true,
        ]);
    }

    public function legacy(Request $request, string $year, string $month, string $day, string $slug): RedirectResponse
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale'));
        $legacyPath = sprintf('/%s/%s/%s/%s', $year, $month, $day, $slug);
        $translation = $this->resolveLegacyTranslation($legacyPath, $slug, $locale, $fallbackLocale);

        abort_unless($translation, 404);

        $post = BlogPost::query()
            ->tap(function (Builder $query): void {
                $this->applyFrontPublishedConstraints($query);
            })
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', array_values(array_unique([$locale, $fallbackLocale]))),
            ])
            ->findOrFail($translation->post_id);

        $canonicalTranslation = $post->translations->firstWhere('locale', $locale)
            ?? $post->translations->firstWhere('locale', $fallbackLocale)
            ?? $translation
            ?? $post->translations->first();

        $canonicalSlug = trim((string) ($canonicalTranslation?->slug ?? ''));
        abort_if($canonicalSlug === '', 404);

        return redirect()->route('blog.show', ['slug' => $canonicalSlug], 301);
    }

    private function renderIndex(Request $request, ?Category $currentCategory = null): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale'));
        $variant = $this->frontendVariant($request);
        $searchTerm = trim((string) $request->query('q', ''));
        $selectedCategoryIds = $currentCategory
            ? [(int) $currentCategory->id]
            : $this->normalizeIdList($request->query('categories', []));
        $blogSettings = app(StoreSettingsService::class)->blog();
        $postsPerPage = (int) ($blogSettings['posts_per_page'] ?? 12);

        $categories = Category::query()
            ->where('scope', Category::SCOPE_BLOG)
            ->where('is_active', true)
            ->withCount([
                'blogPosts as published_posts_count' => function ($query): void {
                    $this->applyFrontPublishedConstraints($query);
                },
            ])
            ->with([
                'translations' => fn ($query) => $query
                    ->where('scope', Category::SCOPE_BLOG)
                    ->whereIn('locale', [$locale, $fallbackLocale]),
            ])
            ->orderBy('sort_order')
            ->orderBy('_lft')
            ->get()
            ->map(fn (Category $category): array => $this->mapBlogCategory($category, $locale, $fallbackLocale))
            ->filter(fn (array $category): bool => $category['count'] > 0)
            ->values();

        $currentCategoryData = $currentCategory
            ? ($categories->firstWhere('id', (int) $currentCategory->id)
                ?? $this->mapBlogCategory($currentCategory->loadMissing('translations'), $locale, $fallbackLocale))
            : null;

        $posts = BlogPost::query()
            ->tap(function (Builder $query): void {
                $this->applyFrontPublishedConstraints($query);
            })
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
                'categories' => fn ($query) => $query
                    ->where('scope', Category::SCOPE_BLOG)
                    ->with([
                        'translations' => fn ($translationQuery) => $translationQuery
                            ->where('scope', Category::SCOPE_BLOG)
                            ->whereIn('locale', [$locale, $fallbackLocale]),
                    ]),
                'media',
            ])
            ->when($searchTerm !== '', function (Builder $query) use ($searchTerm, $locale, $fallbackLocale): void {
                $query->where(function (Builder $nestedQuery) use ($searchTerm, $locale, $fallbackLocale): void {
                    $nestedQuery
                        ->where('code', 'like', '%'.$searchTerm.'%')
                        ->orWhereHas('translations', function (Builder $translationQuery) use ($searchTerm, $locale, $fallbackLocale): void {
                            $translationQuery
                                ->whereIn('locale', [$locale, $fallbackLocale])
                                ->where(function (Builder $searchQuery) use ($searchTerm): void {
                                    $searchQuery
                                        ->where('title', 'like', '%'.$searchTerm.'%')
                                        ->orWhere('slug', 'like', '%'.$searchTerm.'%')
                                        ->orWhere('excerpt', 'like', '%'.$searchTerm.'%');
                                });
                        });
                });
            })
            ->when($selectedCategoryIds !== [], function (Builder $query) use ($selectedCategoryIds): void {
                $query->whereHas('categories', function (Builder $categoryQuery) use ($selectedCategoryIds): void {
                    $categoryQuery->whereIn('categories.id', $selectedCategoryIds);
                });
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate($postsPerPage)
            ->withQueryString();

        $topBlocks = app(ContentBlockResolver::class)->forPlacement('blog.top', $locale, null, null, $variant);
        $bottomBlocks = app(ContentBlockResolver::class)->forPlacement('blog.bottom', $locale, null, null, $variant);

        return view($this->frontendView($request, 'blog.index'), [
            'posts' => $posts,
            'categories' => $categories,
            'currentCategory' => $currentCategoryData,
            'selectedCategoryIds' => $selectedCategoryIds,
            'selectedCategories' => $categories->whereIn('id', $selectedCategoryIds)->values(),
            'searchTerm' => $searchTerm,
            'topBlocks' => $topBlocks,
            'bottomBlocks' => $bottomBlocks,
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
        ]);
    }

    private function resolveBlogCategoryBySlug(string $slug, string $locale, string $fallbackLocale): ?Category
    {
        return Category::query()
            ->where('scope', Category::SCOPE_BLOG)
            ->where('is_active', true)
            ->whereHas('translations', function (Builder $query) use ($slug, $locale, $fallbackLocale): void {
                $query
                    ->where('scope', Category::SCOPE_BLOG)
                    ->whereIn('locale', [$locale, $fallbackLocale])
                    ->where('slug', $slug);
            })
            ->first();
    }

    private function resolveLegacyTranslation(string $legacyPath, string $slug, string $locale, string $fallbackLocale): ?BlogPostTranslation
    {
        $locales = array_values(array_unique(array_filter([$locale, $fallbackLocale])));

        return BlogPostTranslation::query()
            ->when($locales !== [], fn (Builder $query) => $query->whereIn('locale', $locales))
            ->where(function (Builder $query) use ($legacyPath, $slug): void {
                $query
                    ->where('payload->legacy_path', $legacyPath)
                    ->orWhere(function (Builder $slugQuery) use ($slug): void {
                        $slugQuery
                            ->where('slug', $slug)
                            ->whereNull('payload');
                    });
            })
            ->orderByDesc('id')
            ->first()
            ?? BlogPostTranslation::query()
                ->where('payload->legacy_path', $legacyPath)
                ->orderByDesc('id')
                ->first();
    }

    /**
     * @return Collection<int, BlogPost>
     */
    private function resolveRelatedPosts(BlogPost $post, string $locale, string $fallbackLocale): Collection
    {
        $currentTitle = $this->resolveBlogPostTitle($post, $locale, $fallbackLocale);
        $currentCategoryIds = $post->categories
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->unique()
            ->values()
            ->all();

        if ($currentTitle === '') {
            return collect();
        }

        $candidates = BlogPost::query()
            ->where('id', '!=', $post->id)
            ->tap(function (Builder $query): void {
                $this->applyFrontPublishedConstraints($query);
            })
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
                'categories' => fn ($query) => $query
                    ->where('scope', Category::SCOPE_BLOG)
                    ->with([
                        'translations' => fn ($translationQuery) => $translationQuery
                            ->where('scope', Category::SCOPE_BLOG)
                            ->whereIn('locale', [$locale, $fallbackLocale]),
                    ]),
            ])
            ->get();

        $rankedCandidates = $candidates
            ->map(function (BlogPost $candidate) use ($currentTitle, $currentCategoryIds, $locale, $fallbackLocale): array {
                return [
                    'id' => (int) $candidate->id,
                    'post' => $candidate,
                    'score' => $this->relatedTitleSimilarityScore(
                        $currentTitle,
                        $this->resolveBlogPostTitle($candidate, $locale, $fallbackLocale)
                    ),
                    'shares_category' => $this->blogPostsShareCategory($candidate, $currentCategoryIds),
                    'published_at' => $candidate->published_at?->getTimestamp() ?? 0,
                ];
            })
            ->filter(static fn (array $candidate): bool => $candidate['score'] > 0)
            ->values()
            ->all();

        usort($rankedCandidates, static function (array $left, array $right): int {
            return ((int) $right['shares_category'] <=> (int) $left['shares_category'])
                ?: ($right['score'] <=> $left['score'])
                ?: ($right['published_at'] <=> $left['published_at'])
                ?: ($right['id'] <=> $left['id']);
        });

        $relatedIds = collect($rankedCandidates)
            ->take(3)
            ->pluck('id')
            ->values();

        if ($relatedIds->isEmpty()) {
            return collect();
        }

        $relatedPosts = BlogPost::query()
            ->whereIn('id', $relatedIds->all())
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
                'categories' => fn ($query) => $query
                    ->where('scope', Category::SCOPE_BLOG)
                    ->with([
                        'translations' => fn ($translationQuery) => $translationQuery
                            ->where('scope', Category::SCOPE_BLOG)
                            ->whereIn('locale', [$locale, $fallbackLocale]),
                    ]),
                'media',
            ])
            ->get()
            ->keyBy('id');

        return $relatedIds
            ->map(static fn (int $id): ?BlogPost => $relatedPosts->get($id))
            ->filter()
            ->values();
    }

    private function resolveBlogPostTitle(BlogPost $post, string $locale, string $fallbackLocale): string
    {
        $translation = $post->translations->firstWhere('locale', $locale)
            ?? $post->translations->firstWhere('locale', $fallbackLocale)
            ?? $post->translations->first();

        return trim((string) ($translation?->title ?? $post->code));
    }

    /**
     * @param  array<int, int>  $currentCategoryIds
     */
    private function blogPostsShareCategory(BlogPost $candidate, array $currentCategoryIds): bool
    {
        if ($currentCategoryIds === []) {
            return false;
        }

        return $candidate->categories
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->intersect($currentCategoryIds)
            ->isNotEmpty();
    }

    private function relatedTitleSimilarityScore(string $currentTitle, string $candidateTitle): int
    {
        $currentNormalized = $this->normalizeRelatedTitle($currentTitle);
        $candidateNormalized = $this->normalizeRelatedTitle($candidateTitle);

        if ($currentNormalized === '' || $candidateNormalized === '') {
            return 0;
        }

        if ($currentNormalized === $candidateNormalized) {
            return 1_000;
        }

        $currentKeywords = $this->extractRelatedTitleKeywords($currentNormalized);
        $candidateKeywords = $this->extractRelatedTitleKeywords($candidateNormalized);
        $sharedKeywords = $this->sharedRelatedTitleKeywordCount($currentKeywords, $candidateKeywords);
        $containsSimilarPhrase = str_contains($currentNormalized, $candidateNormalized)
            || str_contains($candidateNormalized, $currentNormalized);

        similar_text($currentNormalized, $candidateNormalized, $percent);

        if ($sharedKeywords === 0 && ! $containsSimilarPhrase && $percent < 70.0) {
            return 0;
        }

        $shorterKeywordListLength = min(count($currentKeywords), count($candidateKeywords));
        $keywordCoverage = $sharedKeywords > 0 && $shorterKeywordListLength > 0
            ? $sharedKeywords / $shorterKeywordListLength
            : 0.0;

        return ($sharedKeywords * 30)
            + (int) round($keywordCoverage * 40)
            + (int) round($percent / 5)
            + ($containsSimilarPhrase ? 20 : 0);
    }

    private function normalizeRelatedTitle(string $title): string
    {
        $normalized = Str::lower(Str::ascii($title));
        $normalized = preg_replace('/[^a-z0-9]+/', ' ', $normalized) ?? '';

        return trim(preg_replace('/\s+/', ' ', $normalized) ?? '');
    }

    /**
     * @return array<int, string>
     */
    private function extractRelatedTitleKeywords(string $normalizedTitle): array
    {
        $parts = preg_split('/\s+/', $normalizedTitle, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return collect($parts)
            ->map(fn (string $keyword): string => $this->normalizeRelatedKeyword($keyword))
            ->filter(fn (string $keyword): bool => strlen($keyword) >= 2)
            ->reject(fn (string $keyword): bool => in_array($keyword, self::RELATED_TITLE_STOP_WORDS, true))
            ->unique()
            ->values()
            ->all();
    }

    private function normalizeRelatedKeyword(string $keyword): string
    {
        if (strlen($keyword) > 4 && str_ends_with($keyword, 's')) {
            return substr($keyword, 0, -1);
        }

        return $keyword;
    }

    /**
     * @param  array<int, string>  $currentKeywords
     * @param  array<int, string>  $candidateKeywords
     */
    private function sharedRelatedTitleKeywordCount(array $currentKeywords, array $candidateKeywords): int
    {
        $sharedCount = 0;
        $remainingKeywords = $candidateKeywords;

        foreach ($currentKeywords as $currentKeyword) {
            foreach ($remainingKeywords as $index => $candidateKeyword) {
                if (! $this->relatedKeywordsMatch($currentKeyword, $candidateKeyword)) {
                    continue;
                }

                $sharedCount++;
                unset($remainingKeywords[$index]);

                break;
            }
        }

        return $sharedCount;
    }

    private function relatedKeywordsMatch(string $left, string $right): bool
    {
        if ($left === $right) {
            return true;
        }

        if (strlen($left) < 4 || strlen($right) < 4) {
            return false;
        }

        return str_contains($left, $right) || str_contains($right, $left);
    }

    /**
     * @return array{id:int,name:string,slug:string,count:int}
     */
    private function mapBlogCategory(Category $category, string $locale, string $fallbackLocale): array
    {
        $translation = $category->translations->firstWhere('locale', $locale)
            ?? $category->translations->firstWhere('locale', $fallbackLocale)
            ?? $category->translations->first();

        return [
            'id' => (int) $category->id,
            'name' => trim((string) ($translation?->name ?? $category->code)),
            'slug' => trim((string) ($translation?->slug ?? '')),
            'count' => (int) ($category->published_posts_count ?? 0),
        ];
    }

    private function applyFrontPublishedConstraints(Builder|Relation $query): void
    {
        $query
            ->where('is_active', true)
            ->where(function (Builder $nestedQuery): void {
                $nestedQuery
                    ->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * @return array<int, int>
     */
    private function normalizeIdList(mixed $value): array
    {
        if (is_string($value)) {
            $value = preg_split('/[\s,]+/', $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        }

        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->map(fn (mixed $id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();
    }
}
