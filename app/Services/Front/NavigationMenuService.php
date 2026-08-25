<?php

namespace App\Services\Front;

use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Call\CallPost;
use App\Models\Content\ContentBlock;
use App\Models\Content\Glossary\GlossaryTerm;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Resource\ResourceDocument;
use App\Models\Content\Service\ServicePage;
use App\Models\Content\Support\Faq;
use App\Services\Settings\SystemSettingsService;
use App\Support\Localization\FrontendRoute;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class NavigationMenuService
{
    public const SETTINGS_KEY = 'front_navigation_main';

    public const CHROME_SETTINGS_KEY = 'front_navigation_chrome';

    /**
     * Localized global header/footer fields managed on the navigation CMS screen.
     *
     * @var array<string, int>
     */
    public const CHROME_FIELDS = [
        'header_primary_cta_label' => 80,
        'header_calculator_cta_label' => 80,
        'footer_newsletter_label' => 80,
        'footer_newsletter_title' => 191,
        'footer_newsletter_accent' => 80,
        'footer_newsletter_email_placeholder' => 191,
        'footer_newsletter_submit_label' => 191,
        'footer_newsletter_consent' => 255,
        'footer_tagline' => 255,
        'footer_services_label' => 80,
        'footer_contact_label' => 80,
        'footer_hours' => 255,
        'footer_copyright_text' => 255,
        'footer_cookie_settings_label' => 120,
        'footer_back_to_top_label' => 80,
    ];

    /**
     * @var array<string, array<int, array<string, mixed>>>
     */
    private array $resolvedCache = [];

    /**
     * @var array<string, array<int, array<string, mixed>>>
     */
    private array $resolvedServiceCache = [];

    /**
     * @var array<string, string>
     */
    private array $resolvedServicePageUrlCache = [];

    /**
     * @var array<string, array<string, string>>
     */
    private array $resolvedChromeCache = [];

    public function __construct(private readonly SystemSettingsService $settings) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forLocale(string $locale): array
    {
        $locale = strtolower(trim($locale));
        $fallbackLocale = $this->defaultLocale();
        $strictLocale = $locale !== '' && $locale !== $fallbackLocale;
        $cacheKey = $locale.'|'.$fallbackLocale;

        if (isset($this->resolvedCache[$cacheKey])) {
            return $this->resolvedCache[$cacheKey];
        }

        $items = collect($this->configuredItems())
            ->filter(fn ($item): bool => (bool) ($item['is_active'] ?? true))
            ->sortBy(fn ($item): int => (int) ($item['sort_order'] ?? 0))
            ->values();

        if ($items->isEmpty()) {
            return $this->resolvedCache[$cacheKey] = [];
        }

        $pageIds = $items
            ->where('type', 'page')
            ->pluck('page_id')
            ->map(fn ($id): int => (int) $id)
            ->filter(fn ($id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();

        $pagesById = InfoPage::query()
            ->where('is_active', true)
            ->whereIn('id', $pageIds)
            ->where(function ($query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->with([
                'translations' => fn ($q) => $q->whereIn('locale', [$locale, $fallbackLocale]),
            ])
            ->get()
            ->keyBy('id');

        $hasLocalizedBlogContent = ! $strictLocale || ! $items->contains('type', 'blog')
            || BlogPost::query()
                ->published()
                ->whereHas('translations', fn ($query) => $query->where('locale', $locale))
                ->exists();
        $hasLocalizedFaqContent = ! $strictLocale || ! $items->contains('type', 'faq')
            || Faq::query()
                ->where('is_active', true)
                ->whereHas('translations', fn ($query) => $query->where('locale', $locale))
                ->exists();
        $hasLocalizedContactContent = ! $strictLocale || ! $items->contains('type', 'contact')
            || $this->hasLocalizedContactContent($locale);

        $resolved = [];

        foreach ($items as $index => $item) {
            $type = (string) ($item['type'] ?? 'custom');
            $entry = null;

            if ($type === 'page') {
                $pageId = (int) ($item['page_id'] ?? 0);
                $page = $pageId > 0 ? $pagesById->get($pageId) : null;

                if ($page instanceof InfoPage) {
                    $translation = $strictLocale
                        ? $page->translations->firstWhere('locale', $locale)
                        : $this->pickPageTranslation($page, $locale, $fallbackLocale);
                    $slug = trim((string) ($translation?->slug ?? ''));

                    if ($slug !== '') {
                        $label = $this->labelForItem(
                            $item,
                            trim((string) ($translation?->title ?? '')),
                            $locale,
                            $fallbackLocale,
                            $strictLocale,
                        );
                        $entry = [
                            'key' => 'page-'.$page->id.'-'.$index,
                            'type' => 'page',
                            'label' => $label,
                            'url' => route('pages.show', ['slug' => $slug]),
                            'children' => [],
                            'open_in_new_tab' => (bool) ($item['open_in_new_tab'] ?? false),
                            'show_dropdown' => (bool) ($item['show_dropdown'] ?? true),
                            'mega_promo' => $this->resolveMegaPromo($item),
                        ];
                    }
                }
            } elseif ($type === 'blog' && $hasLocalizedBlogContent) {
                $blogTitle = $strictLocale
                    ? ''
                    : trim((string) $this->settings->get('store_blog_header_title', ''));
                $label = $this->labelForItem($item, $blogTitle, $locale, $fallbackLocale, $strictLocale);
                if ($label === '') {
                    continue;
                }

                $entry = [
                    'key' => 'blog-'.$index,
                    'type' => 'blog',
                    'label' => $label,
                    'url' => route('blog.index'),
                    'children' => [],
                    'open_in_new_tab' => (bool) ($item['open_in_new_tab'] ?? false),
                    'show_dropdown' => (bool) ($item['show_dropdown'] ?? true),
                    'mega_promo' => $this->resolveMegaPromo($item),
                ];
            } elseif ($type === 'contact' && $hasLocalizedContactContent) {
                $label = $this->labelForItem(
                    $item,
                    $this->localizedContactPageTitle($locale),
                    $locale,
                    $fallbackLocale,
                    $strictLocale,
                );
                if ($label === '') {
                    continue;
                }

                $entry = [
                    'key' => 'contact-'.$index,
                    'type' => 'contact',
                    'label' => $label,
                    'url' => FrontendRoute::url('contact.create', locale: $locale),
                    'children' => [],
                    'open_in_new_tab' => (bool) ($item['open_in_new_tab'] ?? false),
                    'show_dropdown' => (bool) ($item['show_dropdown'] ?? true),
                    'mega_promo' => $this->resolveMegaPromo($item),
                ];
            } elseif ($type === 'faq' && $hasLocalizedFaqContent) {
                $label = $this->labelForItem($item, '', $locale, $fallbackLocale, $strictLocale);
                if ($label === '') {
                    continue;
                }

                $entry = [
                    'key' => 'faq-'.$index,
                    'type' => 'faq',
                    'label' => $label,
                    'url' => route('faq.index'),
                    'children' => [],
                    'open_in_new_tab' => (bool) ($item['open_in_new_tab'] ?? false),
                    'show_dropdown' => (bool) ($item['show_dropdown'] ?? true),
                    'mega_promo' => $this->resolveMegaPromo($item),
                ];
            } elseif ($type === 'custom') {
                $url = $this->urlForItem($item, $locale, $fallbackLocale, $strictLocale);
                $label = $this->labelForItem($item, '', $locale, $fallbackLocale, $strictLocale);

                if ($url !== '' && $label !== '') {
                    $entry = [
                        'key' => 'custom-'.$index,
                        'type' => 'custom',
                        'label' => $label,
                        'url' => $url,
                        'children' => [],
                        'open_in_new_tab' => (bool) ($item['open_in_new_tab'] ?? false),
                        'show_dropdown' => (bool) ($item['show_dropdown'] ?? true),
                        'mega_promo' => $this->resolveMegaPromo($item),
                    ];
                }
            }

            if (is_array($entry) && trim((string) ($entry['url'] ?? '')) !== '') {
                $resolved[] = $entry;
            }
        }

        return $this->resolvedCache[$cacheKey] = $resolved;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function configuredItems(): array
    {
        $raw = $this->settings->get(self::SETTINGS_KEY, []);
        $defaultLocale = $this->defaultLocale();

        if (! is_array($raw)) {
            return [];
        }

        $items = [];
        foreach ($raw as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            $labelTranslations = $this->normalizeTranslations($item['label_translations'] ?? []);
            $urlTranslations = $this->normalizeTranslations($item['url_translations'] ?? []);

            $legacyLabel = trim((string) ($item['label'] ?? ''));
            if ($legacyLabel !== '' && $labelTranslations === []) {
                $labelTranslations[$defaultLocale] = $legacyLabel;
            }

            $legacyUrl = trim((string) ($item['url'] ?? ''));
            if ($legacyUrl !== '' && $urlTranslations === []) {
                $urlTranslations[$defaultLocale] = $legacyUrl;
            }

            $storedLabel = $this->pickLocalizedValue(
                $labelTranslations,
                $defaultLocale
            );
            $storedUrl = $this->pickLocalizedValue(
                $urlTranslations,
                $defaultLocale
            );

            $items[] = [
                'type' => (string) ($item['type'] ?? 'custom'),
                'label' => $storedLabel,
                'label_translations' => $labelTranslations,
                'category_id' => (int) ($item['category_id'] ?? 0),
                'page_id' => (int) ($item['page_id'] ?? 0),
                'url' => $storedUrl,
                'url_translations' => $urlTranslations,
                'open_in_new_tab' => (bool) ($item['open_in_new_tab'] ?? false),
                'show_dropdown' => (bool) ($item['show_dropdown'] ?? true),
                'is_active' => (bool) ($item['is_active'] ?? true),
                'sort_order' => (int) ($item['sort_order'] ?? $index),
                'desktop_promo_image_path' => trim((string) ($item['desktop_promo_image_path'] ?? ($item['desktop_promo_image_url'] ?? ''))),
                'desktop_promo_title' => trim((string) ($item['desktop_promo_title'] ?? '')),
                'desktop_promo_subtitle' => trim((string) ($item['desktop_promo_subtitle'] ?? '')),
                'desktop_promo_cta_label' => trim((string) ($item['desktop_promo_cta_label'] ?? '')),
                'desktop_promo_cta_url' => trim((string) ($item['desktop_promo_cta_url'] ?? '')),
            ];
        }

        return $items;
    }

    /**
     * Resolve the three service links from CMS service-page translations.
     * Route names and content codes are structural; labels remain fully CMS-driven.
     *
     * @return array<int, array{code:string,label:string,url:string,route_pattern:string}>
     */
    public function serviceNavigationForLocale(string $locale): array
    {
        $locale = strtolower(trim($locale));
        $fallbackLocale = $this->defaultLocale();
        $strictLocale = $locale !== '' && $locale !== $fallbackLocale;
        $cacheKey = $locale.'|'.$fallbackLocale;

        if (isset($this->resolvedServiceCache[$cacheKey])) {
            return $this->resolvedServiceCache[$cacheKey];
        }

        $definitions = [
            'audit' => ['route_pattern' => 'audit.*'],
            'racunovodstvo' => ['route_pattern' => 'accounting.*'],
            'advisory' => ['route_pattern' => 'advisory.*'],
        ];

        $pages = ServicePage::query()
            ->where('is_active', true)
            ->whereIn('code', array_keys($definitions))
            ->where(function ($query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', array_values(array_unique([$locale, $fallbackLocale]))),
            ])
            ->get()
            ->keyBy('code');

        $items = [];
        foreach ($definitions as $code => $definition) {
            $page = $pages->get($code);
            if (! $page instanceof ServicePage) {
                continue;
            }

            $translation = $strictLocale
                ? $page->translations->firstWhere('locale', $locale)
                : ($page->translations->firstWhere('locale', $locale)
                    ?? $page->translations->firstWhere('locale', $fallbackLocale)
                    ?? $page->translations->first());
            $label = trim((string) ($translation?->title ?? ''));
            $slug = trim((string) ($translation?->slug ?? ''), '/');

            if ($label === '' || $slug === '') {
                continue;
            }

            $items[] = [
                'code' => $code,
                'label' => $label,
                'url' => url('/'.$slug),
                'route_pattern' => (string) $definition['route_pattern'],
            ];
        }

        return $this->resolvedServiceCache[$cacheKey] = $items;
    }

    /**
     * Resolve a service page's public URL from its localized CMS slug.
     * Non-default locales never inherit the default locale's slug.
     */
    public function servicePageUrlForLocale(string $code, string $locale): string
    {
        $code = trim($code);
        $locale = strtolower(trim($locale));
        $fallbackLocale = $this->defaultLocale();
        $strictLocale = $locale !== '' && $locale !== $fallbackLocale;
        $cacheKey = $code.'|'.$locale.'|'.$fallbackLocale;

        if (array_key_exists($cacheKey, $this->resolvedServicePageUrlCache)) {
            return $this->resolvedServicePageUrlCache[$cacheKey];
        }

        if ($code === '' || $locale === '') {
            return $this->resolvedServicePageUrlCache[$cacheKey] = '';
        }

        $page = ServicePage::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', array_values(array_unique([$locale, $fallbackLocale]))),
            ])
            ->first();

        if (! $page instanceof ServicePage) {
            return $this->resolvedServicePageUrlCache[$cacheKey] = '';
        }

        $translation = $strictLocale
            ? $page->translations->firstWhere('locale', $locale)
            : ($page->translations->firstWhere('locale', $locale)
                ?? $page->translations->firstWhere('locale', $fallbackLocale)
                ?? $page->translations->first());
        $slug = trim((string) ($translation?->slug ?? ''), '/');

        return $this->resolvedServicePageUrlCache[$cacheKey] = $slug !== '' ? url('/'.$slug) : '';
    }

    /**
     * Return the localized equivalent of the current public URL when one is
     * available. Fixed service sub-pages use their guarded locale route alias;
     * CMS pages and primary services use their exact translated slug.
     */
    public function localizedCurrentUrlForLocale(string $targetLocale): string
    {
        $targetLocale = strtolower(trim($targetLocale));
        if ($targetLocale === '') {
            return route('home');
        }

        $currentLocale = strtolower((string) app()->getLocale());
        if ($targetLocale === $currentLocale) {
            return request()->fullUrl();
        }

        $route = request()->route();
        $routeName = is_object($route) ? (string) $route->getName() : '';
        $parameters = is_object($route) ? $route->parameters() : [];
        $baseRouteName = str_ends_with($routeName, '.en')
            ? substr($routeName, 0, -3)
            : $routeName;

        $serviceRouteCodes = [
            'services.index' => 'services',
            'audit.show' => 'audit',
            'accounting.show' => 'racunovodstvo',
            'advisory.show' => 'advisory',
            'eu-funds.show' => 'eu-fondovi',
        ];

        if (isset($serviceRouteCodes[$baseRouteName])) {
            $localizedUrl = $this->servicePageUrlForLocale($serviceRouteCodes[$baseRouteName], $targetLocale);

            return $localizedUrl !== '' ? $this->appendCurrentQuery($localizedUrl) : route('home');
        }

        if ($baseRouteName === 'team.index') {
            $hasLocalizedTeamPage = InfoPage::query()
                ->where('code', 'team-page')
                ->where('is_active', true)
                ->where(function ($query): void {
                    $query->whereNull('published_at')
                        ->orWhere('published_at', '<=', now());
                })
                ->whereHas('translations', fn ($query) => $query->where('locale', $targetLocale))
                ->exists();

            return $hasLocalizedTeamPage
                ? $this->appendCurrentQuery(FrontendRoute::url('team.index', locale: $targetLocale))
                : route('home');
        }

        $advisorySectionRoutes = [
            'advisory.finance.show' => 'financial',
            'advisory.ma.show' => 'ma',
            'advisory.due-diligence.show' => 'due_diligence',
            'advisory.valuations.show' => 'valuations',
            'advisory.tax.show' => 'tax',
            'advisory.funding.show' => 'funding',
            'advisory.bank-loans.show' => 'bank_loans',
            'advisory.investment-incentives.show' => 'zopu',
        ];
        $pairedRouteNames = [
            ...array_keys($advisorySectionRoutes),
            'eu-funds.questionnaire.create',
        ];

        if (in_array($baseRouteName, $pairedRouteNames, true)) {
            $parentServiceCode = str_starts_with($baseRouteName, 'advisory.')
                ? 'advisory'
                : 'eu-fondovi';
            $parentServiceUrl = $this->servicePageUrlForLocale($parentServiceCode, $targetLocale);
            if ($parentServiceUrl === '') {
                return route('home');
            }

            if (isset($advisorySectionRoutes[$baseRouteName])
                && ! $this->hasLocalizedAdvisorySection($targetLocale, $advisorySectionRoutes[$baseRouteName])) {
                return $this->appendCurrentQuery($parentServiceUrl);
            }

            $targetRouteName = $targetLocale === $this->defaultLocale()
                ? $baseRouteName
                : $baseRouteName.'.'.$targetLocale;

            if (Route::has($targetRouteName)) {
                return $this->appendCurrentQuery(route($targetRouteName, $parameters));
            }

            return route('home');
        }

        if ($routeName === 'pages.show') {
            $localizedUrl = $this->localizeInternalContentUrl('/'.trim((string) ($parameters['slug'] ?? ''), '/'), $targetLocale);

            return $localizedUrl !== '' ? $this->appendCurrentQuery($localizedUrl) : route('home');
        }

        $translatedRecordRoutes = [
            'blog.show' => [BlogPost::class, 'blog.show', true],
            'blog.legacy' => [BlogPost::class, 'blog.show', true],
            'resources.show' => [ResourceDocument::class, 'resources.show', true],
            'glossary.show' => [GlossaryTerm::class, 'glossary.show', false],
            'eu-funds.calls.show' => [CallPost::class, 'eu-funds.calls.show', true],
        ];

        if (isset($translatedRecordRoutes[$baseRouteName])) {
            [$modelClass, $targetBaseRouteName, $hasPublishWindow] = $translatedRecordRoutes[$baseRouteName];
            $localizedSlug = $this->localizedRecordSlug(
                $modelClass,
                (string) ($parameters['slug'] ?? ''),
                $currentLocale,
                $targetLocale,
                $hasPublishWindow,
            );

            if ($localizedSlug === '') {
                return route('home');
            }

            $targetRouteName = $targetBaseRouteName === 'eu-funds.calls.show' && $targetLocale !== $this->defaultLocale()
                ? $targetBaseRouteName.'.'.$targetLocale
                : $targetBaseRouteName;

            return Route::has($targetRouteName)
                ? $this->appendCurrentQuery(route($targetRouteName, ['slug' => $localizedSlug]))
                : route('home');
        }

        $translatedIndexModels = [
            'blog.index' => [BlogPost::class, true],
            'resources.index' => [ResourceDocument::class, true],
            'glossary.index' => [GlossaryTerm::class, false],
        ];

        if (isset($translatedIndexModels[$routeName])) {
            [$modelClass, $hasPublishWindow] = $translatedIndexModels[$routeName];

            return $this->hasLocalizedRecords($modelClass, $targetLocale, $hasPublishWindow)
                ? $this->appendCurrentQuery(route($routeName, $parameters))
                : route('home');
        }

        if ($routeName === 'faq.index') {
            return Faq::query()
                ->where('is_active', true)
                ->whereHas('translations', fn ($query) => $query->where('locale', $targetLocale))
                ->exists()
                    ? $this->appendCurrentQuery(route($routeName, $parameters))
                    : route('home');
        }

        if ($baseRouteName === 'contact.create') {
            return $this->hasLocalizedContactContent($targetLocale)
                ? $this->appendCurrentQuery(FrontendRoute::url('contact.create', $parameters, $targetLocale))
                : route('home');
        }

        if ($baseRouteName === 'search.index') {
            return $this->appendCurrentQuery(FrontendRoute::url('search.index', $parameters, $targetLocale));
        }

        $sharedRouteNames = [
            'home',
        ];

        if (in_array($routeName, $sharedRouteNames, true) && Route::has($routeName)) {
            return $this->appendCurrentQuery(route($routeName, $parameters));
        }

        return route('home');
    }

    /**
     * @param  class-string<BlogPost|CallPost|GlossaryTerm|ResourceDocument>  $modelClass
     */
    private function localizedRecordSlug(
        string $modelClass,
        string $sourceSlug,
        string $sourceLocale,
        string $targetLocale,
        bool $hasPublishWindow,
    ): string {
        $sourceSlug = trim($sourceSlug);
        if ($sourceSlug === '') {
            return '';
        }

        $query = $modelClass::query()
            ->where('is_active', true)
            ->when($hasPublishWindow, function ($query): void {
                $query->where(function ($publishedQuery): void {
                    $publishedQuery->whereNull('published_at')
                        ->orWhere('published_at', '<=', now());
                });
            })
            ->whereHas('translations', fn ($query) => $query
                ->where('locale', $sourceLocale)
                ->where('slug', $sourceSlug))
            ->with(['translations' => fn ($query) => $query->where('locale', $targetLocale)]);

        $record = $query->first();

        return trim((string) ($record?->translations->first()?->slug ?? ''));
    }

    /**
     * @param  class-string<BlogPost|GlossaryTerm|ResourceDocument>  $modelClass
     */
    private function hasLocalizedRecords(string $modelClass, string $locale, bool $hasPublishWindow): bool
    {
        return $modelClass::query()
            ->where('is_active', true)
            ->when($hasPublishWindow, function ($query): void {
                $query->where(function ($publishedQuery): void {
                    $publishedQuery->whereNull('published_at')
                        ->orWhere('published_at', '<=', now());
                });
            })
            ->whereHas('translations', fn ($query) => $query->where('locale', $locale))
            ->exists();
    }

    private function hasLocalizedAdvisorySection(string $locale, string $sectionKey): bool
    {
        $page = ServicePage::query()
            ->where('code', 'advisory')
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->first();
        $translation = $page?->translations->first();

        return $this->hasMeaningfulAdvisorySectionPayload(
            data_get($translation?->payload, $sectionKey)
        );
    }

    private function hasMeaningfulAdvisorySectionPayload(mixed $payload): bool
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
                if ($this->hasMeaningfulAdvisorySectionPayload($value)) {
                    return true;
                }

                continue;
            }

            if (is_bool($value)) {
                continue;
            }

            if (is_scalar($value) || $value instanceof \Stringable) {
                $text = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $text = trim((string) preg_replace('/\s+/u', ' ', $text));

                if ($text !== '') {
                    return true;
                }
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

    private function hasLocalizedContactContent(string $locale): bool
    {
        return collect($this->localizedContactPageContent($locale))
            ->contains(fn ($value): bool => trim((string) $value) !== '');
    }

    private function localizedContactPageTitle(string $locale): string
    {
        return trim((string) ($this->localizedContactPageContent($locale)['page_title'] ?? ''));
    }

    /**
     * @return array<string, mixed>
     */
    private function localizedContactPageContent(string $locale): array
    {
        $block = ContentBlock::query()
            ->where('code', 'home-alpha-stats')
            ->where('is_active', true)
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->first();
        $payload = (array) ($block?->translations->first()?->payload ?? []);

        return (array) ($payload['contact_page'] ?? []);
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function configuredChromeTranslations(): array
    {
        $raw = $this->settings->get(self::CHROME_SETTINGS_KEY, []);
        if (! is_array($raw)) {
            return [];
        }

        $translations = [];
        foreach ($raw as $locale => $values) {
            $normalizedLocale = strtolower(trim((string) $locale));
            if ($normalizedLocale === '' || ! is_array($values)) {
                continue;
            }

            $normalizedValues = [];
            foreach (self::CHROME_FIELDS as $field => $maxLength) {
                $value = trim((string) ($values[$field] ?? ''));
                if ($value !== '') {
                    $normalizedValues[$field] = $value;
                }
            }

            if ($normalizedValues !== []) {
                $translations[$normalizedLocale] = $normalizedValues;
            }
        }

        return $translations;
    }

    /**
     * @return array<string, string>
     */
    public function chromeForLocale(string $locale): array
    {
        $locale = strtolower(trim($locale));
        $fallbackLocale = $this->defaultLocale();
        $strictLocale = $locale !== '' && $locale !== $fallbackLocale;
        $cacheKey = $locale.'|'.$fallbackLocale;

        if (isset($this->resolvedChromeCache[$cacheKey])) {
            return $this->resolvedChromeCache[$cacheKey];
        }

        $translations = $this->configuredChromeTranslations();
        $values = [];

        foreach (array_keys(self::CHROME_FIELDS) as $field) {
            $preferredLocales = $strictLocale
                ? [$locale]
                : array_values(array_unique([$locale, $fallbackLocale, ...array_keys($translations)]));

            foreach ($preferredLocales as $preferredLocale) {
                $value = trim((string) ($translations[$preferredLocale][$field] ?? ''));
                if ($value !== '') {
                    $values[$field] = $value;
                    break;
                }
            }

            $values[$field] ??= '';
        }

        return $this->resolvedChromeCache[$cacheKey] = $values;
    }

    /**
     * @return array<string, string>
     */
    public function exactFooterLocationForLocale(string $locale, string $entityKey): array
    {
        $locale = strtolower(trim($locale));
        $entityKey = trim($entityKey);

        if ($locale === '') {
            return [];
        }

        $block = ContentBlock::query()
            ->where('code', 'home-alpha-stats')
            ->where('is_active', true)
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->first();
        $items = collect((array) data_get($block?->translations->first()?->payload, 'locations.items', []));
        $location = $entityKey !== ''
            ? $items->first(fn ($item): bool => is_array($item)
                && trim((string) ($item['entity_key'] ?? '')) === $entityKey)
            : $items->first();

        if (! is_array($location)) {
            return [];
        }

        return collect($location)
            ->map(static fn (mixed $value): string => trim((string) $value))
            ->all();
    }

    /**
     * Resolve the standard legal pages from their CMS translations when no
     * explicit footer-page selection has been configured.
     *
     * @return array<int, array{code:string,label:string,url:string,type:string}>
     */
    public function defaultFooterLegalNavigationForLocale(string $locale): array
    {
        $locale = strtolower(trim($locale));
        $fallbackLocale = $this->defaultLocale();
        $strictLocale = $locale !== '' && $locale !== $fallbackLocale;
        $codes = ['privacy-policy', 'terms-of-use'];

        $pages = InfoPage::query()
            ->where('is_active', true)
            ->whereIn('code', $codes)
            ->where(function ($query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', array_values(array_unique([$locale, $fallbackLocale]))),
            ])
            ->get()
            ->keyBy('code');

        $items = [];
        foreach ($codes as $code) {
            $page = $pages->get($code);
            if (! $page instanceof InfoPage) {
                continue;
            }

            $translation = $strictLocale
                ? $page->translations->firstWhere('locale', $locale)
                : $this->pickPageTranslation($page, $locale, $fallbackLocale);
            $label = trim((string) ($translation?->title ?? ''));
            $slug = trim((string) ($translation?->slug ?? ''));
            if ($label === '' || $slug === '') {
                continue;
            }

            $items[] = [
                'code' => $code,
                'label' => $label,
                'url' => route('pages.show', ['slug' => $slug]),
                'type' => 'page',
            ];
        }

        return $items;
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, string>
     */
    private function resolveMegaPromo(array $item): array
    {
        $imagePath = trim((string) ($item['desktop_promo_image_path'] ?? ''));
        $imageUrl = '';

        if ($imagePath !== '') {
            if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://') || str_starts_with($imagePath, '/')) {
                $imageUrl = $imagePath;
            } else {
                $imageUrl = Storage::disk('public')->url($imagePath);
            }
        }

        return [
            'image_url' => $imageUrl,
            'title' => trim((string) ($item['desktop_promo_title'] ?? '')),
            'subtitle' => trim((string) ($item['desktop_promo_subtitle'] ?? '')),
            'cta_label' => trim((string) ($item['desktop_promo_cta_label'] ?? '')),
            'cta_url' => trim((string) ($item['desktop_promo_cta_url'] ?? '')),
        ];
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function labelForItem(
        array $item,
        string $fallback,
        string $locale,
        string $fallbackLocale,
        bool $strictLocale = false,
    ): string {
        $translations = $this->normalizeTranslations($item['label_translations'] ?? []);
        $label = $strictLocale
            ? trim((string) ($translations[$locale] ?? ''))
            : $this->pickLocalizedValue($translations, $locale, $fallbackLocale);
        if ($label === '') {
            $label = $strictLocale ? '' : trim((string) ($item['label'] ?? ''));
        }

        return $label !== '' ? $label : $fallback;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function urlForItem(array $item, string $locale, string $fallbackLocale, bool $strictLocale = false): string
    {
        $translations = $this->normalizeTranslations($item['url_translations'] ?? []);
        $url = $strictLocale
            ? trim((string) ($translations[$locale] ?? ''))
            : $this->pickLocalizedValue($translations, $locale, $fallbackLocale);
        if ($url === '') {
            $url = $strictLocale ? '' : trim((string) ($item['url'] ?? ''));
        }

        if ($strictLocale && $url !== '') {
            $url = $this->localizeInternalContentUrl($url, $locale);
        }

        return $url;
    }

    private function localizeInternalContentUrl(string $url, string $locale): string
    {
        $parts = parse_url($url);
        if (! is_array($parts)) {
            return '';
        }

        $host = strtolower(trim((string) ($parts['host'] ?? '')));
        $applicationHost = strtolower(trim((string) parse_url((string) config('app.url'), PHP_URL_HOST)));
        $requestHost = app()->bound('request') ? strtolower((string) request()->getHost()) : '';
        if ($host !== '' && ! in_array($host, array_filter([$applicationHost, $requestHost]), true)) {
            return $url;
        }

        $path = '/'.ltrim((string) ($parts['path'] ?? ''), '/');
        $slug = trim($path, '/');
        if ($slug === '' || str_contains($slug, '/')) {
            return $url;
        }

        $servicePage = ServicePage::query()
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->whereHas('translations', fn ($query) => $query->where('slug', $slug))
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->first();

        if ($servicePage instanceof ServicePage) {
            $localizedSlug = trim((string) ($servicePage->translations->first()?->slug ?? ''), '/');

            return $localizedSlug !== ''
                ? $this->appendUrlSuffix(url('/'.$localizedSlug), $parts)
                : '';
        }

        $page = InfoPage::query()
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->whereHas('translations', fn ($query) => $query->where('slug', $slug))
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->first();

        if (! $page instanceof InfoPage) {
            return $url;
        }

        $localizedSlug = trim((string) ($page->translations->first()?->slug ?? ''));
        if ($localizedSlug === '') {
            return '';
        }

        return $this->appendUrlSuffix(route('pages.show', ['slug' => $localizedSlug]), $parts);
    }

    /**
     * @param  array<string, mixed>  $parts
     */
    private function appendUrlSuffix(string $url, array $parts): string
    {
        if (isset($parts['query']) && trim((string) $parts['query']) !== '') {
            $url .= '?'.trim((string) $parts['query']);
        }
        if (isset($parts['fragment']) && trim((string) $parts['fragment']) !== '') {
            $url .= '#'.trim((string) $parts['fragment']);
        }

        return $url;
    }

    private function appendCurrentQuery(string $url): string
    {
        $queryString = request()->getQueryString();

        return $queryString ? $url.(str_contains($url, '?') ? '&' : '?').$queryString : $url;
    }

    /**
     * @return array<string, string>
     */
    private function normalizeTranslations(mixed $translations): array
    {
        if (! is_array($translations)) {
            return [];
        }

        $normalized = [];
        foreach ($translations as $locale => $value) {
            $key = strtolower(trim((string) $locale));
            if ($key === '') {
                continue;
            }

            $text = trim((string) $value);
            if ($text === '') {
                continue;
            }

            $normalized[$key] = $text;
        }

        return $normalized;
    }

    /**
     * @param  array<string, string>  $translations
     */
    private function pickLocalizedValue(array $translations, string ...$preferredLocales): string
    {
        foreach ($preferredLocales as $locale) {
            $key = strtolower(trim($locale));
            if ($key !== '' && isset($translations[$key]) && trim((string) $translations[$key]) !== '') {
                return trim((string) $translations[$key]);
            }
        }

        foreach ($translations as $value) {
            $text = trim((string) $value);
            if ($text !== '') {
                return $text;
            }
        }

        return '';
    }

    private function pickPageTranslation(InfoPage $page, string $locale, string $fallbackLocale): mixed
    {
        return $page->translations->firstWhere('locale', $locale)
            ?? $page->translations->firstWhere('locale', $fallbackLocale)
            ?? $page->translations->first();
    }

    private function defaultLocale(): string
    {
        $locale = '';

        if (app()->bound('request')) {
            $locale = (string) request()->attributes->get('front_default_locale', '');
        }

        if ($locale === '') {
            $locale = (string) config('app.fallback_locale', 'hr');
        }

        $locale = strtolower(trim($locale));

        return ((string) preg_split('/[-_]/', $locale, 2)[0]) ?: 'hr';
    }
}
