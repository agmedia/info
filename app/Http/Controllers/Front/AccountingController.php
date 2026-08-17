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
use App\Support\Content\YouTubeUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AccountingController extends Controller
{
    use ResolvesFrontendView;
    use ResolvesServiceVideos;

    public function show(Request $request): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale', 'en'));

        [$servicePage, $servicePageTranslation] = $this->resolveServicePage($locale, $fallbackLocale);
        $pagePayload = ServicePageTemplateRegistry::mergePagePayload(
            ServicePageTemplateRegistry::ACCOUNTING,
            $servicePage?->payload
        );
        $translationPayload = ServicePageTemplateRegistry::mergeTranslationPayload(
            ServicePageTemplateRegistry::ACCOUNTING,
            $servicePageTranslation?->payload,
            (string) ($servicePageTranslation?->locale ?: $locale)
        );
        $serviceVideoPayload = $this->resolveServiceVideoPayload($pagePayload, $translationPayload);

        $accountingCategory = $this->resolveConfiguredBlogCategory(
            (array) ($pagePayload['blog_source'] ?? []),
            $locale,
            $fallbackLocale
        );
        $categoryTranslation = $accountingCategory?->translations->firstWhere('locale', $locale)
            ?? $accountingCategory?->translations->firstWhere('locale', $fallbackLocale)
            ?? $accountingCategory?->translations->first();
        $categorySlug = trim((string) ($categoryTranslation?->slug ?? ''));
        $defaultCategoryName = str_starts_with(strtolower($locale), 'hr') ? 'Računovodstvo' : 'Accounting';
        $categoryName = trim((string) ($categoryTranslation?->name ?? '')) ?: $defaultCategoryName;
        $accountingPosts = $this->resolveAccountingPosts(
            (array) ($pagePayload['blog_source'] ?? []),
            $accountingCategory,
            $locale,
            $fallbackLocale
        );

        $blogSection = (array) ($translationPayload['blog_section'] ?? []);
        $blogSection['title'] = str_replace(':category', $categoryName, (string) ($blogSection['title'] ?? ''));
        $introSection = (array) ($translationPayload['intro_section'] ?? []);
        $introVideo = $this->resolveIntroVideo((string) ($introSection['video_url'] ?? ''));

        return view($this->frontendView($request, 'pages.accounting'), [
            'accountingPosts' => $accountingPosts,
            'accountingCategoryName' => $categoryName,
            'accountingArchiveUrl' => $categorySlug !== ''
                ? url('/blog/'.$categorySlug)
                : route('blog.index'),
            'heroSection' => (array) ($translationPayload['hero'] ?? []),
            'overviewSection' => (array) ($translationPayload['overview'] ?? []),
            'servicesSection' => (array) ($translationPayload['services'] ?? []),
            'approachSection' => (array) ($translationPayload['approach'] ?? []),
            'introSection' => $introSection,
            'editorialSection' => (array) ($translationPayload['editorial_section'] ?? []),
            'detailSections' => array_values((array) ($translationPayload['detail_sections'] ?? [])),
            'serviceVideoSection' => $serviceVideoPayload['section'],
            'serviceVideos' => $serviceVideoPayload['items'],
            'videoSection' => $serviceVideoPayload['section'],
            'accountingVideos' => $serviceVideoPayload['items'],
            'bookkeepingSection' => (array) ($translationPayload['bookkeeping_section'] ?? []),
            'introVideo' => $introVideo,
            'meetingSection' => (array) ($translationPayload['meeting'] ?? []),
            'blogSection' => $blogSection,
            'heroBackgroundUrl' => $this->resolveServiceHeroBackgroundUrl($servicePage),
            'servicePageTitle' => trim((string) ($servicePageTranslation?->title ?? '')) ?: $defaultCategoryName,
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
            ->where('template_key', ServicePageTemplateRegistry::ACCOUNTING)
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
                'media',
            ])
            ->orderByRaw('case when code = ? then 0 else 1 end', [ServicePageTemplateRegistry::defaultCode(ServicePageTemplateRegistry::ACCOUNTING)])
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

        return $this->resolveAccountingCategory($locale, $fallbackLocale);
    }

    /**
     * @return Collection<int, BlogPost>
     */
    private function resolveAccountingPosts(
        array $blogSource,
        ?Category $accountingCategory,
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
            : (int) ($accountingCategory?->id ?? 0);

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

    private function resolveAccountingCategory(string $locale, string $fallbackLocale): ?Category
    {
        $match = Category::query()
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
                'score' => $this->accountingCategoryScore($category),
            ])
            ->sortBy(fn (array $item): string => sprintf(
                '%03d-%05d-%05d',
                (int) $item['score'],
                (int) $item['category']->sort_order,
                (int) $item['category']->id
            ))
            ->first();

        if (! is_array($match) || (int) ($match['score'] ?? 100) >= 100) {
            return null;
        }

        return $match['category'];
    }

    private function accountingCategoryScore(Category $category): int
    {
        $slugCandidates = ['accounting', 'racunovodstvo', 'knjigovodstvo'];
        $nameCandidates = ['accounting', 'racunovodstvo', 'knjigovodstvo'];
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

            if (
                str_contains($slug, 'racun')
                || str_contains($slug, 'account')
                || str_contains($slug, 'knjig')
            ) {
                $bestScore = min($bestScore, 2);

                continue;
            }

            if (
                str_contains($name, 'racun')
                || str_contains($name, 'account')
                || str_contains($name, 'knjig')
            ) {
                $bestScore = min($bestScore, 3);
            }
        }

        return $bestScore;
    }

    /**
     * @return array{video_id:string,start_seconds:int,watch_url:string,embed_url:string,poster_url:string}|array{}
     */
    private function resolveIntroVideo(string $rawVideoUrl): array
    {
        $parsedVideo = YouTubeUrl::parse($rawVideoUrl);

        if (! $parsedVideo) {
            return [];
        }

        $separator = str_contains($parsedVideo['embed_url'], '?') ? '&' : '?';

        return [
            ...$parsedVideo,
            'embed_url' => $parsedVideo['embed_url'].$separator.'rel=0&modestbranding=1&playsinline=1&enablejsapi=1',
            'poster_url' => 'https://i.ytimg.com/vi/'.$parsedVideo['video_id'].'/hqdefault.jpg',
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $videos
     * @return array<int, array<string, mixed>>
     */
    private function resolveVideos(array $videos): array
    {
        return collect($videos)
            ->map(function (array $video): ?array {
                $resolvedVideo = $this->resolveIntroVideo((string) ($video['video_url'] ?? ''));

                if ($resolvedVideo === []) {
                    return null;
                }

                return [
                    ...$resolvedVideo,
                    'title' => trim((string) ($video['title'] ?? '')),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function resolveServiceHeroBackgroundUrl(?ServicePage $servicePage): string
    {
        $mediaUrl = $servicePage
            ? (string) ($servicePage->getFirstMediaUrl('service_hero_image', 'hero_1440x480') ?: $servicePage->getFirstMediaUrl('service_hero_image'))
            : '';

        if ($mediaUrl !== '') {
            return $mediaUrl;
        }

        return $this->versionedAsset('front-theme/images/services/accounting-editorial-3d.svg');
    }

    private function versionedAsset(string $relativePath): string
    {
        $absolutePath = public_path($relativePath);

        return is_file($absolutePath)
            ? asset($relativePath).'?v='.filemtime($absolutePath)
            : asset($relativePath);
    }
}
