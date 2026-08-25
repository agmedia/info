<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Http\Controllers\Front\Concerns\ResolvesServiceVideos;
use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Call\CallPost;
use App\Models\Content\Service\ServicePage;
use App\Models\Content\Service\ServicePageTranslation;
use App\Models\Content\Support\Comment;
use App\Support\Content\ServicePageTemplateRegistry;
use App\Support\Localization\FrontendLocalePolicy;
use App\Support\Localization\FrontendRoute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EuFundsController extends Controller
{
    use ResolvesFrontendView;
    use ResolvesServiceVideos;

    public function show(Request $request): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = FrontendLocalePolicy::fallbackLocale(
            (string) $locale,
            (string) config('app.fallback_locale', config('app.locale', 'en'))
        );

        [$servicePage, $servicePageTranslation] = $this->resolveServicePage($locale, $fallbackLocale);
        abort_if(! $servicePageTranslation, 404);
        $pagePayload = (array) ($servicePage?->payload ?? []);
        $translationPayload = (array) ($servicePageTranslation->payload ?? []);
        $serviceVideoPayload = $this->resolveServiceVideoPayload($pagePayload, $translationPayload);

        $euFundsCategory = $this->resolveConfiguredBlogCategory(
            (array) ($pagePayload['blog_source'] ?? []),
            $locale,
            $fallbackLocale
        );
        $categoryTranslation = $euFundsCategory?->translations->firstWhere('locale', $locale)
            ?? $euFundsCategory?->translations->firstWhere('locale', $fallbackLocale)
            ?? $euFundsCategory?->translations->first();
        $servicePageTitle = trim((string) ($servicePageTranslation?->title ?? ''));
        $categoryName = trim((string) ($categoryTranslation?->name ?? '')) ?: $servicePageTitle;
        $categorySlug = trim((string) ($categoryTranslation?->slug ?? ''));
        $euFundsPosts = $this->resolveEuFundsPosts(
            (array) ($pagePayload['blog_source'] ?? []),
            $euFundsCategory,
            $locale,
            $fallbackLocale
        );

        $blogSection = (array) ($translationPayload['blog_section'] ?? []);
        $blogSection['title'] = str_replace(':category', $categoryName, (string) ($blogSection['title'] ?? ''));
        $resourcesSection = $this->pointQuestionnaireCardToInternalPage(
            $this->resolveCardsSection((array) ($translationPayload['resources'] ?? []), $locale)
        );

        return view($this->frontendView($request, 'pages.eu-funds'), [
            'heroSection' => (array) ($translationPayload['hero'] ?? []),
            'aboutSection' => (array) ($translationPayload['about'] ?? []),
            'overviewSection' => (array) ($translationPayload['overview'] ?? []),
            'chartSection' => (array) ($translationPayload['chart'] ?? []),
            'processSection' => (array) ($translationPayload['process'] ?? []),
            'approachSection' => (array) ($translationPayload['approach'] ?? []),
            'sourceModulesSection' => (array) ($translationPayload['source_modules'] ?? []),
            'callsSection' => $this->resolveCallsSection((array) ($translationPayload['calls'] ?? []), $locale, $fallbackLocale),
            'resourcesSection' => $resourcesSection,
            'lawsSection' => $this->resolveCardsSection((array) ($translationPayload['laws'] ?? []), $locale),
            'testimonialsSection' => (array) ($translationPayload['testimonials'] ?? []),
            'serviceVideoSection' => $serviceVideoPayload['section'],
            'serviceVideos' => $serviceVideoPayload['items'],
            'meetingSection' => (array) ($translationPayload['meeting'] ?? []),
            'blogSection' => $blogSection,
            'euFundsTestimonials' => $this->resolveClientTestimonials($locale, $fallbackLocale),
            'euFundsPosts' => $euFundsPosts,
            'euFundsArchiveUrl' => $categorySlug !== ''
                ? url('/blog/'.$categorySlug)
                : route('blog.index'),
            'heroBackgroundUrl' => $this->resolveServiceHeroBackgroundUrl($servicePage),
            'servicePageTitle' => $servicePageTitle,
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
            ->where('template_key', ServicePageTemplateRegistry::EU_FUNDS)
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
                'media',
            ])
            ->orderByRaw('case when code = ? then 0 else 1 end', [ServicePageTemplateRegistry::defaultCode(ServicePageTemplateRegistry::EU_FUNDS)])
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

        return $this->resolveEuFundsCategory($locale, $fallbackLocale);
    }

    /**
     * @return Collection<int, BlogPost>
     */
    private function resolveEuFundsPosts(
        array $blogSource,
        ?Category $euFundsCategory,
        string $locale,
        string $fallbackLocale
    ): Collection {
        $mode = (string) ($blogSource['mode'] ?? 'auto_category');
        $limit = 5;

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
                ->take($limit)
                ->values();
        }

        $resolvedCategoryId = $mode === 'category'
            ? (int) ($blogSource['category_id'] ?? 0)
            : (int) ($euFundsCategory?->id ?? 0);

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

    private function resolveEuFundsCategory(string $locale, string $fallbackLocale): ?Category
    {
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
            ->get()
            ->map(fn (Category $category): array => [
                'category' => $category,
                'score' => $this->euFundsCategoryScore($category),
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

    private function euFundsCategoryScore(Category $category): int
    {
        $slugCandidates = ['eu-fondovi', 'eu fondovi', 'eu-funds', 'eu funds'];
        $nameCandidates = ['eu fondovi', 'eu funds'];
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

            if ((str_contains($slug, 'eu') && str_contains($slug, 'fond')) || str_contains($slug, 'fund')) {
                $bestScore = min($bestScore, 2);

                continue;
            }

            if ((str_contains($name, 'eu') && str_contains($name, 'fond')) || str_contains($name, 'fund')) {
                $bestScore = min($bestScore, 3);
            }
        }

        return $bestScore;
    }

    /**
     * @return Collection<int, Comment>
     */
    private function resolveClientTestimonials(string $locale, string $fallbackLocale): Collection
    {
        $buildQuery = static fn (string $targetLocale) => Comment::query()
            ->whereNull('commentable_type')
            ->where('status', Comment::STATUS_APPROVED)
            ->where('locale', $targetLocale)
            ->orderByDesc('is_featured')
            ->orderByDesc('reviewed_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(6);

        $rows = $buildQuery($locale)->get();

        if ($rows->isEmpty() && $fallbackLocale !== $locale) {
            $rows = $buildQuery($fallbackLocale)->get();
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    private function resolveCallsSection(array $section, string $locale, string $fallbackLocale): array
    {
        $section['download_link'] = $this->resolveLink($section['download_link'] ?? null, $locale);

        $contentGroups = $this->resolveCallGroupsFromContent($locale, $fallbackLocale);
        if ($contentGroups !== []) {
            $section['groups'] = $contentGroups;

            return $section;
        }

        if (FrontendLocalePolicy::requiresExactTranslation($locale)) {
            $section['groups'] = [];

            return $section;
        }

        $section['groups'] = collect((array) ($section['groups'] ?? []))
            ->map(function (array $group) use ($locale): array {
                $group['tone'] = $this->resolveCallGroupTone((string) ($group['tone'] ?? $group['title'] ?? 'pending'));
                $group['items'] = collect((array) ($group['items'] ?? []))
                    ->map(function (array $item) use ($locale): array {
                        $item['resolved_link'] = $this->resolveLink($item['link'] ?? null, $locale);
                        $item['date_label'] = '';
                        $item['date_value'] = '';

                        return $item;
                    })
                    ->all();

                return $group;
            })
            ->all();

        return $section;
    }

    /**
     * @return array<int, array{title:string,tone:string,status_label:string,items:array<int,array{title:string,date_label:string,date_value:string,resolved_link:array<string,mixed>}>}>
     */
    private function resolveCallGroupsFromContent(string $locale, string $fallbackLocale): array
    {
        if (
            ! Schema::hasTable('content_call_posts')
            || ! Schema::hasTable('content_call_post_translations')
            || ! Schema::hasTable('content_call_post_category')
        ) {
            return [];
        }

        $groups = Category::query()
            ->where('scope', Category::SCOPE_CALL)
            ->where('is_active', true)
            ->when(
                FrontendLocalePolicy::requiresExactTranslation($locale),
                fn ($query) => $query->whereHas('translations', fn ($translationQuery) => $translationQuery
                    ->where('scope', Category::SCOPE_CALL)
                    ->where('locale', $locale))
            )
            ->with([
                'translations' => fn ($query) => $query
                    ->where('scope', Category::SCOPE_CALL)
                    ->whereIn('locale', array_values(array_unique([$locale, $fallbackLocale]))),
                'callPosts' => fn ($query) => $query
                    ->where('is_active', true)
                    ->when(
                        FrontendLocalePolicy::requiresExactTranslation($locale),
                        fn ($callQuery) => $callQuery->whereHas('translations', fn ($translationQuery) => $translationQuery
                            ->where('locale', $locale))
                    )
                    ->where(function (Builder $nested): void {
                        $nested->whereNull('published_at')
                            ->orWhere('published_at', '<=', now());
                    })
                    ->with([
                        'translations' => fn ($translationQuery) => $translationQuery
                            ->whereIn('locale', array_values(array_unique([$locale, $fallbackLocale]))),
                        'media',
                    ])
                    ->orderBy('content_call_post_category.sort_order')
                    ->orderBy('content_call_posts.sort_order')
                    ->orderByDesc('published_at')
                    ->orderByDesc('id'),
            ])
            ->orderBy('sort_order')
            ->orderBy('_lft')
            ->get();

        if ($groups->isEmpty()) {
            return [];
        }

        return $groups
            ->map(function (Category $group) use ($locale, $fallbackLocale): array {
                $translation = $group->translations->firstWhere('locale', $locale)
                    ?? $group->translations->firstWhere('locale', $fallbackLocale)
                    ?? $group->translations->first();

                $groupTitle = trim((string) ($translation?->name ?? $group->code));
                $groupTone = $this->resolveCallGroupTone((string) ($translation?->slug ?? $group->code));

                $items = $group->callPosts
                    ->sortByDesc(fn (CallPost $post): int => ($post->published_at ?? $post->created_at)?->getTimestamp() ?? 0)
                    ->values()
                    ->map(function (CallPost $post) use ($locale, $fallbackLocale): array {
                        $translation = $post->translations->firstWhere('locale', $locale)
                            ?? $post->translations->firstWhere('locale', $fallbackLocale)
                            ?? $post->translations->first();
                        $slug = trim((string) ($translation?->slug ?? ''));
                        $hasContent = trim((string) ($translation?->body_html ?? '')) !== ''
                            || trim((string) ($translation?->excerpt ?? '')) !== ''
                            || $post->getFirstMediaUrl('call_cover') !== '';
                        $dateMeta = $this->resolveCallDateMeta($post, $translation);

                        return [
                            'title' => (string) ($translation?->title ?? $post->code),
                            'date_label' => $dateMeta['label'],
                            'date_value' => $dateMeta['value'],
                            'resolved_link' => [
                                'label' => '',
                                'url' => $hasContent && $slug !== ''
                                    ? FrontendRoute::url('eu-funds.calls.show', ['slug' => $slug], $locale)
                                    : '',
                                'open_in_new_tab' => false,
                                'rel' => '',
                                'is_external' => false,
                            ],
                        ];
                    })
                    ->values()
                    ->all();

                return [
                    'title' => $groupTitle,
                    'tone' => $groupTone,
                    'status_label' => trim((string) data_get($translation?->payload, 'status_label')) ?: $groupTitle,
                    'items' => $items,
                ];
            })
            ->filter(fn (array $group): bool => $group['items'] !== [])
            ->values()
            ->all();
    }

    private function resolveCallGroupTone(string $value): string
    {
        $normalized = Str::of($value)->lower()->ascii()->replace(['_', '-'], ' ')->squish()->value();

        if (str_contains($normalized, 'otvoren') || str_contains($normalized, 'open')) {
            return 'open';
        }

        if (str_contains($normalized, 'zatvoren') || str_contains($normalized, 'closed')) {
            return 'closed';
        }

        return 'pending';
    }

    /**
     * @return array{label:string,value:string}
     */
    private function resolveCallDateMeta(CallPost $post, ?\App\Models\Content\Call\CallPostTranslation $translation): array
    {
        $dateLabels = is_array(data_get($translation?->payload, 'date_labels'))
            ? (array) data_get($translation?->payload, 'date_labels')
            : [];

        foreach ([$translation?->payload, $post->payload] as $payload) {
            if (! is_array($payload)) {
                continue;
            }

            foreach (['application_deadline', 'deadline_at', 'deadline', 'rok_za_prijavu'] as $key) {
                $date = $this->formatCallDate($payload[$key] ?? null);

                if ($date !== '') {
                    return [
                        'label' => trim((string) ($dateLabels['application_deadline'] ?? '')),
                        'value' => $date,
                    ];
                }
            }

            foreach (['updated_at', 'azurirano'] as $key) {
                $date = $this->formatCallDate($payload[$key] ?? null);

                if ($date !== '') {
                    return [
                        'label' => trim((string) ($dateLabels['updated'] ?? '')),
                        'value' => $date,
                    ];
                }
            }
        }

        if ($post->published_at) {
            return [
                'label' => trim((string) ($dateLabels['published'] ?? '')),
                'value' => $post->published_at->translatedFormat('j. n. Y.'),
            ];
        }

        if ($post->updated_at) {
            return [
                'label' => trim((string) ($dateLabels['updated'] ?? '')),
                'value' => $post->updated_at->translatedFormat('j. n. Y.'),
            ];
        }

        return [
            'label' => '',
            'value' => '',
        ];
    }

    private function formatCallDate(mixed $value): string
    {
        $rawValue = trim((string) $value);

        if ($rawValue === '') {
            return '';
        }

        try {
            return \Illuminate\Support\Carbon::parse($rawValue)->translatedFormat('j. n. Y.');
        } catch (\Throwable) {
            return '';
        }
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    private function resolveCardsSection(array $section, string $locale): array
    {
        $section['cards'] = collect((array) ($section['cards'] ?? []))
            ->map(function (array $card) use ($locale): array {
                $card['primary_link'] = $this->resolveLink($card['primary_link'] ?? null, $locale);
                $card['secondary_link'] = $this->resolveLink($card['secondary_link'] ?? null, $locale);
                $card['groups'] = collect((array) ($card['groups'] ?? []))
                    ->map(function (array $group) use ($locale): array {
                        $group['items'] = collect((array) ($group['items'] ?? []))
                            ->map(function (array $item) use ($locale): array {
                                $item['resolved_link'] = $this->resolveLink($item['link'] ?? null, $locale);

                                return $item;
                            })
                            ->all();

                        return $group;
                    })
                    ->all();

                return $card;
            })
            ->all();

        return $section;
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    private function pointQuestionnaireCardToInternalPage(array $section): array
    {
        $section['cards'] = collect((array) ($section['cards'] ?? []))
            ->map(function (array $card): array {
                $title = Str::of((string) ($card['title'] ?? ''))->lower()->ascii()->value();
                $label = Str::of((string) ($card['primary_link']['label'] ?? ''))->lower()->ascii()->value();
                $url = trim((string) ($card['primary_link']['url'] ?? ''));
                $urlPath = '/'.trim((string) parse_url($url, PHP_URL_PATH), '/');
                $looksLikeQuestionnaireCard = str_contains($label, 'upitnik') && (
                    str_contains($title, 'eu fond')
                    || str_contains($title, 'projektni')
                    || str_contains($url, '/eu-fondovi/upitnik')
                );
                $targetsLegacyQuestionnaire = str_contains($url, 'alphacapitalis.com/eu-fondovi-upitnik');
                $targetsQuestionnaireRoute = in_array($urlPath, [
                    '/eu-fondovi/upitnik',
                    '/eu-funds/questionnaire',
                ], true);

                if (! $looksLikeQuestionnaireCard && ! $targetsLegacyQuestionnaire && ! $targetsQuestionnaireRoute) {
                    return $card;
                }

                $card['primary_link'] = [
                    'label' => (string) ($card['primary_link']['label'] ?? ''),
                    'url' => FrontendRoute::url('eu-funds.questionnaire.create'),
                    'open_in_new_tab' => false,
                    'rel' => '',
                    'is_external' => false,
                ];

                return $card;
            })
            ->all();

        return $section;
    }

    /**
     * @return array{label:string,url:string,open_in_new_tab:bool,rel:string,is_external:bool}
     */
    private function resolveLink(mixed $link, string $locale): array
    {
        if (! is_array($link)) {
            return [
                'label' => '',
                'url' => '',
                'open_in_new_tab' => false,
                'rel' => '',
                'is_external' => false,
            ];
        }

        $type = trim((string) ($link['type'] ?? 'none'));
        $label = trim((string) ($link['label'] ?? ''));
        $sourceSlug = trim((string) ($link['slug'] ?? ''));
        $pdfPath = trim((string) ($link['path'] ?? ''));
        $normalizedLocale = strtolower((string) preg_split('/[-_]/', $locale, 2)[0]);
        $pdfLocale = strtolower((string) preg_split('/[-_]/', trim((string) ($link['locale'] ?? '')), 2)[0]);
        $pdfIsAvailable = $pdfPath !== '' && (
            ! FrontendLocalePolicy::requiresExactTranslation($locale)
            || ($pdfLocale !== '' && $pdfLocale === $normalizedLocale)
        );

        return match ($type) {
            'blog' => [
                'label' => $label,
                'url' => ($blogSlug = $this->localizedBlogSlug($sourceSlug, $locale)) !== ''
                    ? route('blog.show', ['slug' => $blogSlug])
                    : '',
                'open_in_new_tab' => false,
                'rel' => '',
                'is_external' => false,
            ],
            'call' => [
                'label' => $label,
                'url' => ($callSlug = $this->localizedCallSlug($sourceSlug, $locale)) !== ''
                    ? FrontendRoute::url('eu-funds.calls.show', ['slug' => $callSlug])
                    : '',
                'open_in_new_tab' => false,
                'rel' => '',
                'is_external' => false,
            ],
            'pdf' => [
                'label' => $label,
                'url' => $pdfIsAvailable
                    ? $this->versionedAsset($pdfPath)
                    : '',
                'open_in_new_tab' => true,
                'rel' => 'noopener noreferrer',
                'is_external' => false,
            ],
            'external' => [
                'label' => $label,
                'url' => trim((string) ($link['url'] ?? '')),
                'open_in_new_tab' => ! str_starts_with(trim((string) ($link['url'] ?? '')), '/'),
                'rel' => str_starts_with(trim((string) ($link['url'] ?? '')), '/')
                    ? ''
                    : 'noopener noreferrer',
                'is_external' => ! str_starts_with(trim((string) ($link['url'] ?? '')), '/'),
            ],
            default => [
                'label' => $label,
                'url' => '',
                'open_in_new_tab' => false,
                'rel' => '',
                'is_external' => false,
            ],
        };
    }

    private function localizedBlogSlug(string $sourceSlug, string $locale): string
    {
        if ($sourceSlug === '' || ! FrontendLocalePolicy::requiresExactTranslation($locale)) {
            return $sourceSlug;
        }

        return (string) (BlogPost::query()
            ->published()
            ->whereHas('translations', fn ($query) => $query->where('slug', $sourceSlug))
            ->with(['translations' => fn ($query) => $query
                ->where('locale', $locale)
                ->whereNotNull('slug')
                ->where('slug', '!=', '')])
            ->first()
            ?->translations
            ->first()
            ?->slug ?? '');
    }

    private function localizedCallSlug(string $sourceSlug, string $locale): string
    {
        if ($sourceSlug === '' || ! FrontendLocalePolicy::requiresExactTranslation($locale)) {
            return $sourceSlug;
        }

        return (string) (CallPost::query()
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->whereHas('translations', fn ($query) => $query->where('slug', $sourceSlug))
            ->with(['translations' => fn ($query) => $query
                ->where('locale', $locale)
                ->whereNotNull('slug')
                ->where('slug', '!=', '')])
            ->first()
            ?->translations
            ->first()
            ?->slug ?? '');
    }

    private function resolveServiceHeroBackgroundUrl(?ServicePage $servicePage): string
    {
        $mediaUrl = $servicePage
            ? (string) ($servicePage->getFirstMediaUrl('service_hero_image', 'hero_1440x480') ?: $servicePage->getFirstMediaUrl('service_hero_image'))
            : '';

        if ($mediaUrl !== '') {
            return $mediaUrl;
        }

        return $this->versionedAsset('front-theme/images/services/advisory-editorial-3d.svg');
    }

    private function versionedAsset(string $relativePath): string
    {
        $absolutePath = public_path($relativePath);

        return is_file($absolutePath)
            ? asset($relativePath).'?v='.filemtime($absolutePath)
            : asset($relativePath);
    }
}
