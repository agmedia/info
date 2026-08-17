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
use Illuminate\View\View;

class AdvisoryController extends Controller
{
    use ResolvesFrontendView;
    use ResolvesServiceVideos;

    public function show(Request $request): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale', 'en'));

        [$servicePage, $servicePageTranslation, $pagePayload, $translationPayload] = $this->resolveAdvisoryPayload($locale, $fallbackLocale);
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
        $categoryName = trim((string) ($categoryTranslation?->name ?? '')) ?: 'Savjetovanje';
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
            'servicePageTitle' => trim((string) ($servicePageTranslation?->title ?? '')) ?: 'Savjetovanje',
            'servicePageMetaTitle' => trim((string) ($servicePageTranslation?->meta_title ?? '')) ?: 'Savjetovanje | ALPHA CAPITALIS',
            'servicePageMetaDescription' => trim((string) ($servicePageTranslation?->meta_description ?? '')) ?: 'Financijsko i porezno savjetovanje, pribavljanje financiranja, due diligence, procjene vrijednosti i M&A savjetovanje.',
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
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale', 'en'));
        [$servicePage, $servicePageTranslation, $pagePayload, $translationPayload] = $this->resolveAdvisoryPayload($locale, $fallbackLocale);

        $advisoryCategory = $this->resolveConfiguredBlogCategory(
            (array) ($pagePayload['blog_source'] ?? []),
            $locale,
            $fallbackLocale
        );
        $categoryTranslation = $advisoryCategory?->translations->firstWhere('locale', $locale)
            ?? $advisoryCategory?->translations->firstWhere('locale', $fallbackLocale)
            ?? $advisoryCategory?->translations->first();
        $categorySlug = trim((string) ($categoryTranslation?->slug ?? ''));
        $categoryName = trim((string) ($categoryTranslation?->name ?? '')) ?: 'Savjetovanje';
        $advisoryPosts = $this->resolveAdvisoryPosts(
            (array) ($pagePayload['blog_source'] ?? []),
            $advisoryCategory,
            $locale,
            $fallbackLocale
        );

        $detailPages = [
            'financial' => [
                'title' => trim((string) data_get($translationPayload, 'financial.title')) ?: 'Financijsko savjetovanje',
                'intro' => (string) data_get($translationPayload, 'financial.overview_body.0', ''),
                'meta_title' => 'Financijsko savjetovanje | ALPHA CAPITALIS',
                'meta_description' => 'Financijska analiza, modeliranje, planiranje kapitala i stručna podrška pri važnim poslovnim odlukama.',
            ],
            'ma' => [
                'title' => trim((string) data_get($translationPayload, 'ma.title')) ?: 'Prodaja i kupnja poduzeća (M&A)',
                'intro' => (string) data_get($translationPayload, 'ma.overview_body.0', ''),
                'meta_title' => 'Prodaja i kupnja poduzeća (M&A) | ALPHA CAPITALIS',
                'meta_description' => 'Savjetovanje u prodaji i kupnji poduzeća, pripremi transakcije, procjeni vrijednosti i pregovorima.',
            ],
            'due_diligence' => [
                'title' => trim((string) data_get($translationPayload, 'due_diligence.title')) ?: 'Dubinska snimanja (Due Diligence)',
                'intro' => (string) data_get($translationPayload, 'due_diligence.overview_body.0', ''),
                'meta_title' => 'Dubinska snimanja (Due Diligence) | ALPHA CAPITALIS',
                'meta_description' => 'Dubinska analiza poslovanja, financijskih rezultata, rizika i prilika prije transakcija i strateških odluka.',
            ],
            'valuations' => [
                'title' => trim((string) data_get($translationPayload, 'valuations.title')) ?: 'Procjena vrijednosti društva',
                'intro' => (string) data_get($translationPayload, 'valuations.overview_body.0', ''),
                'meta_title' => 'Procjena vrijednosti društva | ALPHA CAPITALIS',
                'meta_description' => 'Procjena vrijednosti društva, financijsko modeliranje i stručna podloga za transakcije i strateške odluke.',
            ],
            'tax' => [
                'title' => trim((string) data_get($translationPayload, 'tax.title')) ?: 'Porezno savjetovanje',
                'intro' => (string) data_get($translationPayload, 'tax.overview_body.0', ''),
                'meta_title' => 'Porezno savjetovanje | ALPHA CAPITALIS',
                'meta_description' => 'Porezno planiranje, analiza poreznih rizika, porezna mišljenja, PDV savjetovanje i porezna podrška transakcijama.',
            ],
            'bank_loans' => [
                'title' => trim((string) data_get($translationPayload, 'bank_loans.title')) ?: 'Bankovni krediti',
                'intro' => (string) data_get($translationPayload, 'bank_loans.overview_body.0', ''),
                'meta_title' => 'Bankovni krediti | ALPHA CAPITALIS',
                'meta_description' => 'Podrška pri pribavljanju bankovnog financiranja, pripremi dokumentacije, projekcija i pregovorima s bankama.',
            ],
            'zopu' => [
                'title' => trim((string) data_get($translationPayload, 'zopu.title')) ?: 'Zakon o poticanju ulaganja',
                'intro' => (string) data_get($translationPayload, 'zopu.overview_body.0', ''),
                'meta_title' => 'Zakon o poticanju ulaganja | ALPHA CAPITALIS',
                'meta_description' => 'Podrška pri korištenju potpora prema Zakonu o poticanju ulaganja i provedbi investicijskih projekata.',
            ],
        ];

        $subpage = match ($type) {
            'funding' => [
                'type' => 'funding',
                'title' => trim((string) data_get($translationPayload, 'funding.title')) ?: 'Pribavljanje financiranja',
                'intro' => (string) data_get($translationPayload, 'funding.intro', ''),
                'meta_title' => 'Pribavljanje financiranja | ALPHA CAPITALIS',
                'meta_description' => 'EU fondovi, bankovni krediti, Zakon o poticanju ulaganja i strukturiranje financiranja.',
            ],
            'financial', 'ma', 'due_diligence', 'valuations', 'tax', 'bank_loans', 'zopu' => [
                'type' => 'detail',
                'detail_key' => $type,
                ...$detailPages[$type],
            ],
            default => abort(404),
        };

        $subpage['hook'] = $this->resolveSubpageHook($translationPayload, $type, $subpage);
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

    /**
     * @param array<string, mixed> $translationPayload
     * @param array<string, mixed> $subpage
     */
    private function resolveSubpageHook(array $translationPayload, string $type, array $subpage): string
    {
        $configuredHook = trim((string) data_get($translationPayload, $type.'.hero_intro'));
        if ($configuredHook !== '') {
            return $configuredHook;
        }

        $routeFragments = [
            'financial' => '/savjetovanje/financijsko-savjetovanje',
            'funding' => '/savjetovanje/pribavljanje-financiranja',
            'ma' => '/savjetovanje/prodaja-i-kupnja-poduzeca',
            'due_diligence' => '/savjetovanje/dubinska-snimanja',
            'valuations' => '/savjetovanje/procjena-vrijednosti-drustva',
            'tax' => '/savjetovanje/porezno-savjetovanje',
            'bank_loans' => '/savjetovanje/pribavljanje-financiranja/bankovni-krediti',
            'zopu' => '/savjetovanje/pribavljanje-financiranja/zakon-o-poticanju-ulaganja',
        ];

        $cards = in_array($type, ['bank_loans', 'zopu'], true)
            ? (array) data_get($translationPayload, 'funding.cards', [])
            : (array) ($translationPayload['service_cards'] ?? []);
        $routeFragment = $routeFragments[$type] ?? '';

        foreach ($cards as $card) {
            if (! is_array($card)) {
                continue;
            }

            $url = trim((string) ($card['url'] ?? ''));
            $text = trim((string) ($card['text'] ?? ''));

            if ($routeFragment !== '' && $text !== '' && str_ends_with($url, $routeFragment)) {
                return $text;
            }
        }

        return trim((string) ($subpage['intro'] ?? ''));
    }

    /**
     * @return array{0: ServicePage|null, 1: ServicePageTranslation|null, 2: array<string, mixed>, 3: array<string, mixed>}
     */
    private function resolveAdvisoryPayload(string $locale, string $fallbackLocale): array
    {
        [$servicePage, $servicePageTranslation] = $this->resolveServicePage($locale, $fallbackLocale);
        $pagePayload = ServicePageTemplateRegistry::mergePagePayload(
            ServicePageTemplateRegistry::ADVISORY,
            $servicePage?->payload
        );
        $translationPayload = ServicePageTemplateRegistry::mergeTranslationPayload(
            ServicePageTemplateRegistry::ADVISORY,
            $servicePageTranslation?->payload,
            (string) ($servicePageTranslation?->locale ?: $locale)
        );

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
