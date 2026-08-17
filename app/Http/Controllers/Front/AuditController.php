<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Http\Controllers\Front\Concerns\ResolvesServiceVideos;
use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Service\ServicePage;
use App\Models\Content\Service\ServicePageTranslation;
use App\Support\Content\ServicePageTemplateRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuditController extends Controller
{
    use ResolvesFrontendView;
    use ResolvesServiceVideos;

    public function show(Request $request): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale', 'en'));

        [$servicePage, $servicePageTranslation] = $this->resolveServicePage($locale, $fallbackLocale);
        $pagePayload = ServicePageTemplateRegistry::mergePagePayload(
            ServicePageTemplateRegistry::AUDIT,
            $servicePage?->payload
        );
        $translationPayload = ServicePageTemplateRegistry::mergeTranslationPayload(
            ServicePageTemplateRegistry::AUDIT,
            $servicePageTranslation?->payload,
            (string) ($servicePageTranslation?->locale ?: $locale)
        );
        $serviceVideoPayload = $this->resolveServiceVideoPayload($pagePayload, $translationPayload);

        $auditCategory = $this->resolveConfiguredBlogCategory(
            (array) ($pagePayload['blog_source'] ?? []),
            $locale,
            $fallbackLocale
        );
        $categoryTranslation = $auditCategory?->translations->firstWhere('locale', $locale)
            ?? $auditCategory?->translations->firstWhere('locale', $fallbackLocale)
            ?? $auditCategory?->translations->first();
        $categorySlug = trim((string) ($categoryTranslation?->slug ?? ''));
        $defaultCategoryName = str_starts_with(strtolower($locale), 'hr') ? 'Revizija' : 'Audit';
        $categoryName = trim((string) ($categoryTranslation?->name ?? '')) ?: $defaultCategoryName;
        $auditPosts = $this->resolveAuditPosts(
            (array) ($pagePayload['blog_source'] ?? []),
            $auditCategory,
            $locale,
            $fallbackLocale
        );

        $blogSection = (array) ($translationPayload['blog_section'] ?? []);
        $blogSection['title'] = str_replace(':category', $categoryName, (string) ($blogSection['title'] ?? ''));

        return view($this->frontendView($request, 'pages.audit'), [
            'auditPosts' => $auditPosts,
            'auditCategoryName' => $categoryName,
            'auditArchiveUrl' => $categorySlug !== ''
                ? url('/blog/'.$categorySlug)
                : route('blog.index'),
            'heroSection' => (array) ($translationPayload['hero'] ?? []),
            'overviewSection' => (array) ($translationPayload['overview'] ?? []),
            'obligorsSection' => (array) ($translationPayload['obligors'] ?? []),
            'servicesSection' => (array) ($translationPayload['services'] ?? []),
            'valueSection' => (array) ($translationPayload['value'] ?? []),
            'approachSection' => (array) ($translationPayload['approach'] ?? []),
            'serviceVideoSection' => $serviceVideoPayload['section'],
            'serviceVideos' => $serviceVideoPayload['items'],
            'meetingSection' => (array) ($translationPayload['meeting'] ?? []),
            'blogSection' => $blogSection,
            'heroBackgroundUrl' => $this->resolveServiceHeroBackgroundUrl($servicePage),
            'servicePageTitle' => trim((string) ($servicePageTranslation?->title ?? '')) ?: 'Revizija',
            'servicePageMetaTitle' => trim((string) ($servicePageTranslation?->meta_title ?? '')),
            'servicePageMetaDescription' => trim((string) ($servicePageTranslation?->meta_description ?? '')),
            'servicePageOgImage' => $this->resolveServiceHeroBackgroundUrl($servicePage),
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
        ]);
    }

    /**
     * @return array{0: ServicePage|null, 1: ServicePageTranslation|null}
     */
    private function resolveServicePage(string $locale, string $fallbackLocale): array
    {
        if (! Schema::hasTable('content_service_pages') || ! Schema::hasTable('content_service_page_translations')) {
            return [null, null];
        }

        $servicePage = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::AUDIT)
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
                'media',
            ])
            ->orderByRaw('case when code = ? then 0 else 1 end', [ServicePageTemplateRegistry::defaultCode(ServicePageTemplateRegistry::AUDIT)])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if (! $servicePage) {
            return [null, null];
        }

        $translation = $servicePage->translations->firstWhere('locale', $locale)
            ?? $servicePage->translations->firstWhere('locale', $fallbackLocale)
            ?? $servicePage->translations->first();

        return [$servicePage, $translation];
    }

    private function resolveConfiguredBlogCategory(array $blogSource, string $locale, string $fallbackLocale): ?Category
    {
        $mode = (string) ($blogSource['mode'] ?? 'auto_category');
        $configuredCategoryId = (int) ($blogSource['category_id'] ?? 0);

        if ($mode === 'category' && $configuredCategoryId > 0) {
            $category = Category::query()
                ->where('scope', Category::SCOPE_BLOG)
                ->where('id', $configuredCategoryId)
                ->with([
                    'translations' => fn ($query) => $query
                        ->where('scope', Category::SCOPE_BLOG)
                        ->whereIn('locale', [$locale, $fallbackLocale, 'hr', 'en']),
                ])
                ->first();

            if ($category) {
                return $category;
            }
        }

        return $this->resolveAuditCategory($locale, $fallbackLocale);
    }

    /**
     * @return Collection<int, BlogPost>
     */
    private function resolveAuditPosts(
        array $blogSource,
        ?Category $auditCategory,
        string $locale,
        string $fallbackLocale
    ): Collection {
        $mode = (string) ($blogSource['mode'] ?? 'auto_category');
        $limit = max(1, min(12, (int) ($blogSource['limit'] ?? 6)));

        $baseQuery = BlogPost::query()
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
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
            ]);

        if ($mode === 'manual') {
            $postIds = collect((array) ($blogSource['post_ids'] ?? []))
                ->map(fn ($id): int => (int) $id)
                ->filter()
                ->values();

            if ($postIds->isEmpty()) {
                return collect();
            }

            $posts = (clone $baseQuery)
                ->whereIn('id', $postIds->all())
                ->get();

            $order = $postIds->flip();

            return $posts
                ->sortBy(fn (BlogPost $post): int => (int) ($order[$post->id] ?? 9999))
                ->values();
        }

        $resolvedCategoryId = $mode === 'category'
            ? (int) ($blogSource['category_id'] ?? 0)
            : (int) ($auditCategory?->id ?? 0);

        if ($resolvedCategoryId > 0) {
            $baseQuery->whereHas('categories', function (Builder $categoryQuery) use ($resolvedCategoryId): void {
                $categoryQuery->where('categories.id', $resolvedCategoryId);
            });
        }

        return $baseQuery
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    private function resolveAuditCategory(string $locale, string $fallbackLocale): ?Category
    {
        return Category::query()
            ->where('scope', Category::SCOPE_BLOG)
            ->where('is_active', true)
            ->with([
                'translations' => fn ($query) => $query
                    ->where('scope', Category::SCOPE_BLOG)
                    ->whereIn('locale', [$locale, $fallbackLocale, 'hr', 'en']),
            ])
            ->get()
            ->map(fn (Category $category): array => [
                'category' => $category,
                'score' => $this->auditCategoryScore($category),
            ])
            ->sortBy(fn (array $item): string => sprintf(
                '%03d-%05d-%05d',
                (int) $item['score'],
                (int) $item['category']->sort_order,
                (int) $item['category']->id
            ))
            ->pluck('category')
            ->first();
    }

    private function auditCategoryScore(Category $category): int
    {
        $slugCandidates = ['audit', 'revizija'];
        $nameCandidates = ['audit', 'revizija'];
        $bestScore = 100;
        $code = Str::of((string) $category->code)->lower()->ascii()->squish()->value();

        if (in_array($code, $slugCandidates, true)) {
            return 0;
        }

        foreach ($category->translations as $translation) {
            $slug = Str::of((string) $translation->slug)->lower()->ascii()->squish()->value();
            $name = Str::of((string) $translation->name)->lower()->ascii()->squish()->value();

            if (in_array($slug, $slugCandidates, true)) {
                return 0;
            }

            if (in_array($name, $nameCandidates, true)) {
                $bestScore = min($bestScore, 1);

                continue;
            }

            if (str_contains($slug, 'reviz') || str_contains($slug, 'audit')) {
                $bestScore = min($bestScore, 2);

                continue;
            }

            if (str_contains($name, 'reviz') || str_contains($name, 'audit')) {
                $bestScore = min($bestScore, 3);
            }
        }

        return $bestScore;
    }

    private function resolveServiceHeroBackgroundUrl(?ServicePage $servicePage): string
    {
        $mediaUrl = $servicePage
            ? (string) ($servicePage->getFirstMediaUrl('service_hero_image') ?: $servicePage->getFirstMediaUrl('service_hero_image', 'hero_1440x480'))
            : '';

        if ($mediaUrl !== '') {
            return $mediaUrl;
        }

        return $this->versionedAsset('front-theme/images/services/audit-editorial-3d.svg');
    }

    private function versionedAsset(string $relativePath): string
    {
        $absolutePath = public_path($relativePath);

        return is_file($absolutePath)
            ? asset($relativePath).'?v='.filemtime($absolutePath)
            : asset($relativePath);
    }
}
