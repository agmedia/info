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
use App\Support\Localization\FrontendLocalePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AdvisoryController extends Controller
{
    use ResolvesFrontendView;
    use ResolvesServiceVideos;

    public function show(Request $request): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = $this->fallbackLocale((string) $locale);

        [$servicePage, $servicePageTranslation, $pagePayload, $translationPayload] = $this->resolveAdvisoryPayload($locale, $fallbackLocale);
        abort_if(! $servicePageTranslation, 404);
        $serviceVideoPayload = $this->resolveServiceVideoPayload($pagePayload, $translationPayload);
        $content = $translationPayload;

        $advisoryCategory = $this->resolveConfiguredBlogCategory(
            (array) ($pagePayload['blog_source'] ?? []),
            $locale,
            $fallbackLocale
        );
        $categoryTranslation = $advisoryCategory?->translations->firstWhere('locale', $locale)
            ?? $advisoryCategory?->translations->firstWhere('locale', $fallbackLocale)
            ?? $advisoryCategory?->translations->first();
        $categorySlug = trim((string) ($categoryTranslation?->slug ?? ''));
        $servicePageTitle = trim((string) ($servicePageTranslation?->title ?? ''));
        $categoryName = trim((string) ($categoryTranslation?->name ?? '')) ?: $servicePageTitle;
        $advisoryPosts = $this->resolveAdvisoryPosts(
            (array) ($pagePayload['blog_source'] ?? []),
            $advisoryCategory,
            $locale,
            $fallbackLocale
        );

        return view($this->frontendView($request, 'pages.advisory'), [
            'advisoryPosts' => $advisoryPosts,
            'advisoryCategoryName' => $categoryName,
            'advisoryArchiveUrl' => $categorySlug !== ''
                ? url('/blog/'.$categorySlug)
                : route('blog.index'),
            'advisoryContent' => $content,
            'heroSection' => (array) ($content['hero'] ?? []),
            'serviceVideoSection' => $serviceVideoPayload['section'],
            'serviceVideos' => $serviceVideoPayload['items'],
            'heroBackgroundUrl' => $this->resolveServiceHeroBackgroundUrl($servicePage),
            'servicePageTitle' => $servicePageTitle,
            'servicePageMetaTitle' => trim((string) ($servicePageTranslation?->meta_title ?? '')),
            'servicePageMetaDescription' => trim((string) ($servicePageTranslation?->meta_description ?? '')),
            'servicePageOgImage' => $this->resolveServiceHeroBackgroundUrl($servicePage),
            'pandeaLogoUrl' => $this->resolvePandeaLogoUrl($servicePage),
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
        ]);
    }

    public function financial(Request $request): View
    {
        return $this->subpage($request, 'financial');
    }

    public function tax(Request $request): View
    {
        return $this->subpage($request, 'tax');
    }

    public function funding(Request $request): View
    {
        return $this->subpage($request, 'funding');
    }

    public function ma(Request $request): View
    {
        return $this->subpage($request, 'ma');
    }

    public function dueDiligence(Request $request): View
    {
        return $this->subpage($request, 'due_diligence');
    }

    public function valuations(Request $request): View
    {
        return $this->subpage($request, 'valuations');
    }

    public function bankLoans(Request $request): View
    {
        return $this->subpage($request, 'bank_loans');
    }

    public function investmentIncentives(Request $request): View
    {
        return $this->subpage($request, 'zopu');
    }

    private function subpage(Request $request, string $type): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = $this->fallbackLocale((string) $locale);
        [$servicePage, $servicePageTranslation, $pagePayload, $translationPayload] = $this->resolveAdvisoryPayload($locale, $fallbackLocale);
        abort_if(! $servicePageTranslation, 404);

        $advisoryCategory = $this->resolveConfiguredBlogCategory(
            (array) ($pagePayload['blog_source'] ?? []),
            $locale,
            $fallbackLocale
        );
        $categoryTranslation = $advisoryCategory?->translations->firstWhere('locale', $locale)
            ?? $advisoryCategory?->translations->firstWhere('locale', $fallbackLocale)
            ?? $advisoryCategory?->translations->first();
        $categorySlug = trim((string) ($categoryTranslation?->slug ?? ''));
        $categoryName = trim((string) ($categoryTranslation?->name ?? ''))
            ?: trim((string) ($servicePageTranslation?->title ?? ''));
        $advisoryPosts = $this->resolveAdvisoryPosts(
            (array) ($pagePayload['blog_source'] ?? []),
            $advisoryCategory,
            $locale,
            $fallbackLocale
        );

        abort_unless(in_array($type, [
            'financial',
            'funding',
            'ma',
            'due_diligence',
            'valuations',
            'tax',
            'bank_loans',
            'zopu',
        ], true), 404);

        $localizedPagePayload = data_get($translationPayload, $type);
        abort_if(! $this->hasLocalizedSectionPayload($localizedPagePayload), 404);
        $localizedPage = is_array($localizedPagePayload) ? $localizedPagePayload : [];
        $title = trim((string) ($localizedPage['title'] ?? ''));
        $intro = trim((string) ($localizedPage['intro'] ?? ''));

        $subpage = [
            'type' => $type === 'funding' ? 'funding' : 'detail',
            'title' => $title,
            'intro' => $intro,
            'meta_title' => trim((string) ($localizedPage['meta_title'] ?? '')),
            'meta_description' => trim((string) ($localizedPage['meta_description'] ?? '')),
        ];

        if ($type !== 'funding') {
            $subpage['detail_key'] = $type;
        }

        $subpage['hook'] = trim((string) ($localizedPage['hero_intro'] ?? ''));
        $subpage['hero_image_alt'] = $type === 'funding'
            ? trim((string) data_get($translationPayload, 'funding.hero_image_alt'))
            : trim((string) data_get($translationPayload, $type.'.hero_image_alt'));

        return view($this->frontendView($request, 'pages.advisory-subpage'), [
            'advisoryPosts' => $advisoryPosts,
            'advisoryCategoryName' => $categoryName,
            'advisoryArchiveUrl' => $categorySlug !== ''
                ? url('/blog/'.$categorySlug)
                : route('blog.index'),
            'advisoryContent' => $translationPayload,
            'subpage' => $subpage,
            'heroSection' => [
                'subtitle_lead' => $subpage['title'],
                'intro' => $subpage['hook'],
            ],
            'heroBackgroundUrl' => $this->resolveServiceHeroBackgroundUrl($servicePage),
            'servicePageTitle' => $subpage['title'],
            'servicePageMetaTitle' => $subpage['meta_title'],
            'servicePageMetaDescription' => $subpage['meta_description'],
            'servicePageOgImage' => $this->resolveServiceHeroBackgroundUrl($servicePage),
            'pandeaLogoUrl' => $this->resolvePandeaLogoUrl($servicePage),
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
        ]);
    }

    private function firstLocalizedPlainText(mixed ...$candidates): string
    {
        foreach ($candidates as $candidate) {
            if (is_array($candidate)) {
                $text = $this->firstLocalizedPlainText(...array_values($candidate));
                if ($text !== '') {
                    return $text;
                }

                continue;
            }

            if (! is_scalar($candidate) && ! $candidate instanceof \Stringable) {
                continue;
            }

            $text = html_entity_decode(strip_tags((string) $candidate), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $text = trim((string) preg_replace('/\s+/u', ' ', $text));

            if ($text !== '') {
                return $text;
            }
        }

        return '';
    }

    private function hasLocalizedSectionPayload(mixed $payload): bool
    {
        if (! is_array($payload) || $payload === []) {
            return false;
        }

        foreach ($payload as $childKey => $value) {
            $normalizedKey = strtolower(trim((string) $childKey));
            if ($this->isAdvisoryPayloadMetadataKey($normalizedKey)) {
                continue;
            }

            if (is_array($value)) {
                if ($this->hasLocalizedSectionPayload($value)) {
                    return true;
                }

                continue;
            }

            if (is_bool($value)) {
                continue;
            }

            if ((is_scalar($value) || $value instanceof \Stringable)
                && $this->firstLocalizedPlainText($value) !== '') {
                return true;
            }
        }

        return false;
    }

    private function isAdvisoryPayloadMetadataKey(string $key): bool
    {
        if ($key === '') {
            return false;
        }

        if (in_array($key, ['pandea', 'meeting', 'blog_section'], true)
            || str_starts_with($key, 'show_')
            || str_starts_with($key, 'is_')) {
            return true;
        }

        return preg_match('/(^|_)(meta|seo|url|uri|slug|route|media|image|icon|alt|canonical|target)(_|$)/', $key) === 1;
    }

    /**
     * @return array{0: ServicePage|null, 1: ServicePageTranslation|null, 2: array<string, mixed>, 3: array<string, mixed>}
     */
    private function resolveAdvisoryPayload(string $locale, string $fallbackLocale): array
    {
        [$servicePage, $servicePageTranslation] = $this->resolveServicePage($locale, $fallbackLocale);
        $pagePayload = (array) ($servicePage?->payload ?? []);
        $translationPayload = (array) ($servicePageTranslation?->payload ?? []);

        return [$servicePage, $servicePageTranslation, $pagePayload, $translationPayload];
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
            ->where('template_key', ServicePageTemplateRegistry::ADVISORY)
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
                'media',
            ])
            ->orderByRaw('case when code = ? then 0 else 1 end', [ServicePageTemplateRegistry::defaultCode(ServicePageTemplateRegistry::ADVISORY)])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if (! $servicePage) {
            return [null, null];
        }

        $translation = $servicePage->translations->firstWhere('locale', $locale);

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
                ->when(
                    FrontendLocalePolicy::requiresExactTranslation($locale),
                    fn ($query) => $query->whereHas('translations', fn ($translationQuery) => $translationQuery
                        ->where('scope', Category::SCOPE_BLOG)
                        ->where('locale', $locale))
                )
                ->with([
                    'translations' => fn ($query) => $query
                        ->where('scope', Category::SCOPE_BLOG)
                        ->whereIn('locale', [$locale, $fallbackLocale]),
                ])
                ->first();

            if ($category) {
                return $category;
            }
        }

        return $this->resolveAdvisoryCategory($locale, $fallbackLocale);
    }

    /**
     * @return Collection<int, BlogPost>
     */
    private function resolveAdvisoryPosts(
        array $blogSource,
        ?Category $advisoryCategory,
        string $locale,
        string $fallbackLocale
    ): Collection {
        $mode = (string) ($blogSource['mode'] ?? 'auto_category');
        $limit = max(1, min(12, (int) ($blogSource['limit'] ?? 6)));

        $baseQuery = BlogPost::query()
            ->where('is_active', true)
            ->when(
                FrontendLocalePolicy::requiresExactTranslation($locale),
                fn (Builder $query) => $query->whereHas('translations', fn (Builder $translationQuery) => $translationQuery
                    ->where('locale', $locale))
            )
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
                'categories' => fn ($query) => $query
                    ->where('scope', Category::SCOPE_BLOG)
                    ->when(
                        FrontendLocalePolicy::requiresExactTranslation($locale),
                        fn ($categoryQuery) => $categoryQuery->whereHas('translations', fn ($translationQuery) => $translationQuery
                            ->where('scope', Category::SCOPE_BLOG)
                            ->where('locale', $locale))
                    )
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
            : (int) ($advisoryCategory?->id ?? 0);

        if ($resolvedCategoryId <= 0) {
            return collect();
        }

        $baseQuery->whereHas('categories', function (Builder $categoryQuery) use ($resolvedCategoryId): void {
            $categoryQuery->where('categories.id', $resolvedCategoryId);
        });

        return $baseQuery
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    private function resolveAdvisoryCategory(string $locale, string $fallbackLocale): ?Category
    {
        $slugCandidates = [
            'savjetovanje',
            'advisory',
            'poslovno-savjetovanje',
            'business-advisory',
            'corporate-advisory',
        ];

        return Category::query()
            ->where('scope', Category::SCOPE_BLOG)
            ->where('is_active', true)
            ->when(
                FrontendLocalePolicy::requiresExactTranslation($locale),
                fn ($query) => $query->whereHas('translations', fn ($translationQuery) => $translationQuery
                    ->where('scope', Category::SCOPE_BLOG)
                    ->where('locale', $locale))
            )
            ->with([
                'translations' => fn ($query) => $query
                    ->where('scope', Category::SCOPE_BLOG)
                    ->whereIn('locale', [$locale, $fallbackLocale]),
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Category $category): array => [
                'category' => $category,
                'score' => $this->advisoryCategoryScore($category, $locale, $fallbackLocale, $slugCandidates),
            ])
            ->filter(fn (array $item): bool => $item['score'] < 100)
            ->sortBy([
                ['score', 'asc'],
                [fn (array $item): int => (int) $item['category']->sort_order, 'asc'],
                [fn (array $item): int => (int) $item['category']->id, 'asc'],
            ])
            ->pluck('category')
            ->first();
    }

    private function fallbackLocale(string $locale): string
    {
        return FrontendLocalePolicy::fallbackLocale(
            $locale,
            (string) config('app.fallback_locale', config('app.locale', 'en'))
        );
    }

    /**
     * @param  array<int, string>  $slugCandidates
     */
    private function advisoryCategoryScore(
        Category $category,
        string $locale,
        string $fallbackLocale,
        array $slugCandidates
    ): int {
        $bestScore = 100;

        foreach ($category->translations as $translation) {
            $localeBonus = match ((string) $translation->locale) {
                $locale => 0,
                $fallbackLocale => 4,
                default => 8,
            };

            $slug = str((string) ($translation->slug ?? ''))->lower()->ascii()->value();
            $name = str((string) ($translation->name ?? ''))->lower()->ascii()->value();
            $haystack = trim($slug.' '.$name.' '.strtolower((string) $category->code));

            if (in_array($slug, $slugCandidates, true)) {
                return $localeBonus;
            }

            if (str_contains($haystack, 'savjet') || str_contains($haystack, 'advisory')) {
                $bestScore = min($bestScore, 6 + $localeBonus);
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

        return $this->versionedAsset('front-theme/images/services/advisory-editorial-3d.svg');
    }

    private function resolvePandeaLogoUrl(?ServicePage $servicePage): string
    {
        $mediaUrl = $servicePage
            ? (string) ($servicePage->getFirstMediaUrl('service_logo', 'detail_960x960') ?: $servicePage->getFirstMediaUrl('service_logo'))
            : '';

        if ($mediaUrl !== '') {
            return $mediaUrl;
        }

        return $this->versionedAsset('front-theme/images/logos/pandea-global-ma-logo.png');
    }

    private function versionedAsset(string $relativePath): string
    {
        $absolutePath = public_path($relativePath);

        return file_exists($absolutePath)
            ? asset($relativePath).'?v='.filemtime($absolutePath)
            : asset($relativePath);
    }
}
