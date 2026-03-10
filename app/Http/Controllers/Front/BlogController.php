<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Services\Content\ContentBlockResolver;
use App\Services\Front\StoreSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    use ResolvesFrontendView;

    public function index(Request $request): View
    {
        return $this->renderIndex($request);
    }

    public function show(Request $request, string $slug): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.locale');
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

        $related = BlogPost::query()
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
                'media',
            ])
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        return view($this->frontendView($request, 'blog.show'), [
            'post' => $post,
            'related' => $related,
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
        ]);
    }

    private function renderIndex(Request $request, ?Category $currentCategory = null): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.locale');
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
            ->orderBy('id')
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
