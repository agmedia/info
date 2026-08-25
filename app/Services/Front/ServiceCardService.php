<?php

namespace App\Services\Front;

use App\Models\Content\Service\ServicePage;
use App\Support\Content\ServicePageTemplateRegistry;
use App\Support\Localization\FrontendLocalePolicy;
use App\Support\Localization\FrontendRoute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class ServiceCardService
{
    private const TEMPLATE_ORDER = [
        ServicePageTemplateRegistry::ADVISORY,
        ServicePageTemplateRegistry::FINANCE,
        ServicePageTemplateRegistry::ACCOUNTING,
        ServicePageTemplateRegistry::AUDIT,
        ServicePageTemplateRegistry::TAX,
        ServicePageTemplateRegistry::EU_FUNDS,
    ];

    /**
     * @return array<int, array<string, string>>
     */
    public function cards(string $locale, string $fallbackLocale): array
    {
        $fallbackLocale = FrontendLocalePolicy::fallbackLocale($locale, $fallbackLocale);
        $servicePages = $this->servicePages($locale, $fallbackLocale);
        $defaults = $this->cardDefaults($locale);

        return collect(self::TEMPLATE_ORDER)
            ->map(function (string $templateKey) use ($servicePages, $defaults, $locale): ?array {
                $servicePage = $servicePages->get($templateKey);
                $default = $defaults[$templateKey];
                $translation = $servicePage?->translations->firstWhere('locale', $locale);

                $title = trim((string) ($translation?->title ?? ''));
                if ($title === '') {
                    return null;
                }

                return [
                    'title' => $title,
                    'url' => $default['url'],
                    'image_url' => $this->serviceImageUrl($servicePage, $default['fallback_image']),
                    'template_key' => $templateKey,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function primaryPillars(
        string $locale,
        string $fallbackLocale,
        ?array $overrides = null,
        bool $useDefaultRoutes = true,
    ): array {
        if ($overrides === null) {
            $servicesIndexPage = $this->servicesIndexPage();
            $translation = $servicesIndexPage?->translations()
                ->where('locale', $locale)
                ->first();
            $overrides = (array) data_get($translation?->payload, 'primary_pillars', []);
        }

        $cards = collect($this->cards($locale, $fallbackLocale))->keyBy('template_key');
        $card = static fn (string $templateKey): array => (array) ($cards->get($templateKey) ?? []);

        $audit = $card(ServicePageTemplateRegistry::AUDIT);
        $accounting = $card(ServicePageTemplateRegistry::ACCOUNTING);
        $advisory = $card(ServicePageTemplateRegistry::ADVISORY);

        $defaults = [
            [
                'key' => 'audit',
                'title' => '',
                'subtitle' => '',
                'text' => '',
                'image_alt' => '',
                'bullets' => [],
                'url' => $audit['url'] ?? FrontendRoute::url('audit.show', locale: $locale),
                'image_url' => $audit['image_url'] ?? $this->versionedAsset('front-theme/images/services/audit-editorial-3d.svg'),
                'children' => [],
            ],
            [
                'key' => 'accounting',
                'title' => '',
                'subtitle' => '',
                'text' => '',
                'image_alt' => '',
                'bullets' => [],
                'url' => $accounting['url'] ?? FrontendRoute::url('accounting.show', locale: $locale),
                'image_url' => $accounting['image_url'] ?? $this->versionedAsset('front-theme/images/services/accounting-editorial-3d.svg'),
                'children' => [],
            ],
            [
                'key' => 'advisory',
                'title' => '',
                'subtitle' => '',
                'text' => '',
                'image_alt' => '',
                'bullets' => [],
                'url' => FrontendRoute::url('advisory.show', locale: $locale),
                'image_url' => $advisory['image_url'] ?? $this->versionedAsset('front-theme/images/services/advisory-editorial-3d.svg'),
                'children' => [],
            ],
        ];

        if (! is_array($overrides) || $overrides === []) {
            return [];
        }

        $defaultsByKey = collect($defaults)->keyBy('key');

        $pillars = collect($overrides)
            ->map(function ($override) use ($defaultsByKey, $locale, $useDefaultRoutes): ?array {
                if (! is_array($override)) {
                    return null;
                }

                $key = trim((string) ($override['key'] ?? ''));
                if ($key === '') {
                    return null;
                }

                $default = (array) ($defaultsByKey->get($key) ?? ['key' => $key]);
                $url = trim((string) ($override['url'] ?? ''));
                if ($url === '' && $useDefaultRoutes) {
                    $url = trim((string) ($default['url'] ?? ''));
                }

                $merged = array_merge($default, [
                    'key' => $key,
                    'title' => trim((string) ($override['title'] ?? ($default['title'] ?? ''))),
                    'subtitle' => trim((string) ($override['subtitle'] ?? ($default['subtitle'] ?? ''))),
                    'text' => trim((string) ($override['text'] ?? ($default['text'] ?? ''))),
                    'image_alt' => trim((string) ($override['image_alt'] ?? ($default['image_alt'] ?? ''))),
                    'url' => $this->normalizeCardUrl($url, $locale),
                    'action_label' => trim((string) ($override['action_label'] ?? ($default['action_label'] ?? ''))),
                    'bullets' => collect((array) ($override['bullets'] ?? ($default['bullets'] ?? [])))
                        ->map(fn ($bullet): string => trim((string) $bullet))
                        ->filter()
                        ->values()
                        ->all(),
                ]);

                if (trim((string) ($merged['image_url'] ?? '')) === '' && trim((string) ($default['image_url'] ?? '')) !== '') {
                    $merged['image_url'] = $default['image_url'];
                }

                return $merged;
            })
            ->filter()
            ->values()
            ->all();

        return $this->withServicesIndexCardImages($pillars);
    }

    /**
     * The homepage and the Services index must use the same CMS-managed card images.
     *
     * @param  array<int, array<string, mixed>>  $pillars
     * @return array<int, array<string, mixed>>
     */
    private function withServicesIndexCardImages(array $pillars): array
    {
        $servicesIndexPage = $this->servicesIndexPage();
        $fallbacks = [
            'audit' => 'alpha/service-revizija.jpg',
            'accounting' => 'alpha/service-racunovodstvo.jpg',
            'advisory' => 'alpha/service-savjetovanje.jpg',
        ];

        return collect($pillars)
            ->map(function (array $pillar) use ($servicesIndexPage, $fallbacks): array {
                $cardKey = trim((string) ($pillar['key'] ?? ''));
                $collection = ServicePageTemplateRegistry::SERVICES_INDEX_CARD_MEDIA_COLLECTIONS[$cardKey] ?? null;
                $media = $collection ? $servicesIndexPage?->getFirstMedia($collection) : null;

                if ($media?->hasGeneratedConversion('services_index_card_1080x1350')) {
                    $pillar['image_url'] = $this->mediaAssetUrl($media, 'services_index_card_1080x1350')
                        ?? $media->getUrl('services_index_card_1080x1350');
                } elseif ($media) {
                    $pillar['image_url'] = $this->mediaAssetUrl($media) ?? $media->getUrl();
                } else {
                    $fallback = (string) ($fallbacks[$cardKey] ?? '');
                    $pillar['image_url'] = $fallback !== ''
                        ? $this->versionedAsset($fallback)
                        : trim((string) ($pillar['image_url'] ?? ''));
                }

                return $pillar;
            })
            ->values()
            ->all();
    }

    private function servicesIndexPage(): ?ServicePage
    {
        if (! Schema::hasTable('content_service_pages')) {
            return null;
        }

        return ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::SERVICES_INDEX)
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->with('media')
            ->orderByRaw('case when code = ? then 0 else 1 end', [ServicePageTemplateRegistry::defaultCode(ServicePageTemplateRegistry::SERVICES_INDEX)])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();
    }

    private function normalizeCardUrl(string $url, string $locale): string
    {
        $url = trim($url);

        $url = FrontendRoute::localizeUrl($url, $locale);

        if ($url === '' || str_starts_with($url, '#') || preg_match('/^[a-z][a-z0-9+.-]*:/i', $url) === 1) {
            return $url;
        }

        return url($url);
    }

    /**
     * @return Collection<string, ServicePage>
     */
    private function servicePages(string $locale, string $fallbackLocale): Collection
    {
        if (! Schema::hasTable('content_service_pages') || ! Schema::hasTable('content_service_page_translations')) {
            return collect();
        }

        return ServicePage::query()
            ->whereIn('template_key', self::TEMPLATE_ORDER)
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
                'media',
            ])
            ->get()
            ->sortBy(function (ServicePage $page): int {
                $templateIndex = array_search($page->template_key, self::TEMPLATE_ORDER, true);
                $templateIndex = $templateIndex === false ? 99 : (int) $templateIndex;
                $defaultCodePriority = $page->code === ServicePageTemplateRegistry::defaultCode((string) $page->template_key) ? 0 : 1;

                return ($templateIndex * 100000) + ($defaultCodePriority * 10000) + ((int) $page->sort_order * 10) + (int) $page->id;
            })
            ->groupBy('template_key')
            ->map(fn (Collection $pages): ServicePage => $pages->first());
    }

    private function serviceImageUrl(?ServicePage $servicePage, string $fallbackImage): string
    {
        $media = $servicePage?->getFirstMedia('service_hero_image');

        if ($media) {
            if ($media->hasGeneratedConversion('hero_1440x480')) {
                return $this->mediaAssetUrl($media, 'hero_1440x480') ?? $media->getUrl('hero_1440x480');
            }

            if ($media->hasGeneratedConversion('card_360x240')) {
                return $this->mediaAssetUrl($media, 'card_360x240') ?? $media->getUrl('card_360x240');
            }

            return $this->mediaAssetUrl($media) ?? $media->getUrl();
        }

        return $this->versionedAsset($fallbackImage);
    }

    private function mediaAssetUrl(\Spatie\MediaLibrary\MediaCollections\Models\Media $media, ?string $conversionName = null): ?string
    {
        $url = $conversionName !== null ? $media->getUrl($conversionName) : $media->getUrl();
        $path = parse_url($url, PHP_URL_PATH);

        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $relativePath = ltrim($path, '/');
        $absolutePath = public_path($relativePath);

        return file_exists($absolutePath)
            ? asset($relativePath).'?v='.filemtime($absolutePath)
            : null;
    }

    private function versionedAsset(string $relativePath): string
    {
        $absolutePath = public_path($relativePath);

        return file_exists($absolutePath)
            ? asset($relativePath).'?v='.filemtime($absolutePath)
            : asset($relativePath);
    }

    /**
     * @return array<string, array{title: string, url: string, fallback_image: string}>
     */
    private function cardDefaults(string $locale): array
    {
        return [
            ServicePageTemplateRegistry::ADVISORY => [
                'title' => '',
                'url' => FrontendRoute::url('advisory.show', locale: $locale),
                'fallback_image' => 'front-theme/images/services/advisory-editorial-3d.svg',
            ],
            ServicePageTemplateRegistry::FINANCE => [
                'title' => '',
                'url' => FrontendRoute::url('advisory.finance.show', locale: $locale),
                'fallback_image' => 'front-theme/images/services/finance-editorial-3d.svg',
            ],
            ServicePageTemplateRegistry::ACCOUNTING => [
                'title' => '',
                'url' => FrontendRoute::url('accounting.show', locale: $locale),
                'fallback_image' => 'front-theme/images/services/accounting-editorial-3d.svg',
            ],
            ServicePageTemplateRegistry::AUDIT => [
                'title' => '',
                'url' => FrontendRoute::url('audit.show', locale: $locale),
                'fallback_image' => 'front-theme/images/services/audit-editorial-3d.svg',
            ],
            ServicePageTemplateRegistry::TAX => [
                'title' => '',
                'url' => FrontendRoute::url('advisory.tax.show', locale: $locale),
                'fallback_image' => 'front-theme/images/services/tax-editorial-3d.svg',
            ],
            ServicePageTemplateRegistry::EU_FUNDS => [
                'title' => '',
                'url' => FrontendRoute::url('eu-funds.show', locale: $locale),
                'fallback_image' => 'front-theme/images/services/advisory-editorial-3d.svg',
            ],
        ];
    }
}
