<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Call\CallPost;
use App\Models\Content\Service\ServicePage;
use App\Models\Content\Service\ServicePageTranslation;
use App\Models\Content\Support\Comment;
use App\Support\Content\ServicePageTemplateRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EuFundsController extends Controller
{
    use ResolvesFrontendView;

    public function show(Request $request): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale', 'en'));

        [$servicePage, $servicePageTranslation] = $this->resolveServicePage($locale, $fallbackLocale);
        $pagePayload = ServicePageTemplateRegistry::mergePagePayload(
            ServicePageTemplateRegistry::EU_FUNDS,
            $servicePage?->payload
        );
        $translationPayload = ServicePageTemplateRegistry::mergeTranslationPayload(
            ServicePageTemplateRegistry::EU_FUNDS,
            $servicePageTranslation?->payload,
            (string) ($servicePageTranslation?->locale ?: $locale)
        );

        $euFundsCategory = $this->resolveConfiguredBlogCategory(
            (array) ($pagePayload['blog_source'] ?? []),
            $locale,
            $fallbackLocale
        );
        $categoryTranslation = $euFundsCategory?->translations->firstWhere('locale', $locale)
            ?? $euFundsCategory?->translations->firstWhere('locale', $fallbackLocale)
            ?? $euFundsCategory?->translations->first();
        $defaultCategoryName = str_starts_with(strtolower($locale), 'hr') ? 'EU fondovi' : 'EU Funds';
        $categoryName = trim((string) ($categoryTranslation?->name ?? '')) ?: $defaultCategoryName;
        $euFundsPosts = $this->resolveEuFundsPosts(
            (array) ($pagePayload['blog_source'] ?? []),
            $euFundsCategory,
            $locale,
            $fallbackLocale
        );

        $blogSection = (array) ($translationPayload['blog_section'] ?? []);
        $blogSection['title'] = str_replace(':category', $categoryName, (string) ($blogSection['title'] ?? ''));
        $resourcesSection = $this->pointQuestionnaireCardToInternalPage(
            $this->resolveCardsSection((array) ($translationPayload['resources'] ?? []))
        );

        return view($this->frontendView($request, 'pages.eu-funds'), [
            'heroSection' => (array) ($translationPayload['hero'] ?? []),
            'aboutSection' => (array) ($translationPayload['about'] ?? []),
            'overviewSection' => (array) ($translationPayload['overview'] ?? []),
            'chartSection' => (array) ($translationPayload['chart'] ?? []),
            'processSection' => (array) ($translationPayload['process'] ?? []),
            'callsSection' => $this->resolveCallsSection((array) ($translationPayload['calls'] ?? []), $locale, $fallbackLocale),
            'resourcesSection' => $resourcesSection,
            'lawsSection' => $this->resolveCardsSection((array) ($translationPayload['laws'] ?? [])),
            'testimonialsSection' => (array) ($translationPayload['testimonials'] ?? []),
            'meetingSection' => (array) ($translationPayload['meeting'] ?? []),
            'blogSection' => $blogSection,
            'euFundsTestimonials' => $this->resolveClientTestimonials($locale, $fallbackLocale),
            'euFundsPosts' => $euFundsPosts,
            'heroBackgroundUrl' => $this->resolveServiceHeroBackgroundUrl($servicePage),
            'servicePageTitle' => trim((string) ($servicePageTranslation?->title ?? '')) ?: 'EU fondovi',
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
            ->with([
                'translations' => fn ($query) => $query
                    ->where('scope', Category::SCOPE_BLOG)
                    ->whereIn('locale', [$locale, $fallbackLocale, 'hr', 'en']),
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
        $section['download_link'] = $this->resolveLink($section['download_link'] ?? null);

        $contentGroups = $this->resolveCallGroupsFromContent($locale, $fallbackLocale);
        if ($contentGroups !== []) {
            $section['groups'] = $contentGroups;

            return $section;
        }

        $section['groups'] = collect((array) ($section['groups'] ?? []))
            ->map(function (array $group): array {
                $group['items'] = collect((array) ($group['items'] ?? []))
                    ->map(function (array $item): array {
                        $item['resolved_link'] = $this->resolveLink($item['link'] ?? null);

                        return $item;
                    })
                    ->all();

                return $group;
            })
            ->all();

        return $section;
    }

    /**
     * @return array<int, array{title:string,tone:string,items:array<int,array{title:string,published_label:string,resolved_link:array<string,mixed>}>}>
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
            ->with([
                'translations' => fn ($query) => $query
                    ->where('scope', Category::SCOPE_CALL)
                    ->whereIn('locale', array_values(array_unique([$locale, $fallbackLocale, 'hr']))),
                'callPosts' => fn ($query) => $query
                    ->where('is_active', true)
                    ->where(function (Builder $nested): void {
                        $nested->whereNull('published_at')
                            ->orWhere('published_at', '<=', now());
                    })
                    ->with([
                        'translations' => fn ($translationQuery) => $translationQuery
                            ->whereIn('locale', array_values(array_unique([$locale, $fallbackLocale, 'hr']))),
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
                $groupTone = match ((string) ($translation?->slug ?? $group->code)) {
                    'otvoreni-pozivi' => 'open',
                    'zatvoreni-pozivi' => 'closed',
                    default => 'pending',
                };

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

                        return [
                            'title' => (string) ($translation?->title ?? $post->code),
                            'published_label' => (string) (($post->published_at ?? $post->created_at)?->translatedFormat('j. n. Y.') ?? ''),
                            'resolved_link' => [
                                'label' => '',
                                'url' => $hasContent && $slug !== ''
                                    ? route('eu-funds.calls.show', ['slug' => $slug])
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
                    'items' => $items,
                ];
            })
            ->filter(fn (array $group): bool => $group['items'] !== [])
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    private function resolveCardsSection(array $section): array
    {
        $section['cards'] = collect((array) ($section['cards'] ?? []))
            ->map(function (array $card): array {
                $card['primary_link'] = $this->resolveLink($card['primary_link'] ?? null);
                $card['secondary_link'] = $this->resolveLink($card['secondary_link'] ?? null);
                $card['groups'] = collect((array) ($card['groups'] ?? []))
                    ->map(function (array $group): array {
                        $group['items'] = collect((array) ($group['items'] ?? []))
                            ->map(function (array $item): array {
                                $item['resolved_link'] = $this->resolveLink($item['link'] ?? null);

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
                $looksLikeQuestionnaireCard = str_contains($title, 'eu fond')
                    && str_contains($label, 'upitnik');
                $targetsLegacyQuestionnaire = str_contains($url, 'alphacapitalis.com/eu-fondovi-upitnik');

                if (! $looksLikeQuestionnaireCard && ! $targetsLegacyQuestionnaire) {
                    return $card;
                }

                $card['primary_link'] = [
                    'label' => (string) ($card['primary_link']['label'] ?? ''),
                    'url' => route('eu-funds.questionnaire.create'),
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
    private function resolveLink(mixed $link): array
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

        return match ($type) {
            'blog' => [
                'label' => $label,
                'url' => trim((string) ($link['slug'] ?? '')) !== ''
                    ? route('blog.show', ['slug' => (string) $link['slug']])
                    : '',
                'open_in_new_tab' => false,
                'rel' => '',
                'is_external' => false,
            ],
            'call' => [
                'label' => $label,
                'url' => trim((string) ($link['slug'] ?? '')) !== ''
                    ? route('eu-funds.calls.show', ['slug' => (string) $link['slug']])
                    : '',
                'open_in_new_tab' => false,
                'rel' => '',
                'is_external' => false,
            ],
            'pdf' => [
                'label' => $label,
                'url' => trim((string) ($link['path'] ?? '')) !== ''
                    ? $this->versionedAsset((string) $link['path'])
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
