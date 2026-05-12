<?php

namespace App\Services\Front;

use App\Models\Content\Service\ServicePage;
use App\Support\Content\ServicePageTemplateRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class ServiceCardService
{
    private const TEMPLATE_ORDER = [
        ServicePageTemplateRegistry::FINANCE,
        ServicePageTemplateRegistry::ACCOUNTING,
        ServicePageTemplateRegistry::AUDIT,
        ServicePageTemplateRegistry::TAX,
        ServicePageTemplateRegistry::EU_FUNDS,
        ServicePageTemplateRegistry::FAMILY_BUSINESS,
    ];

    /**
     * @return array<int, array<string, string>>
     */
    public function cards(string $locale, string $fallbackLocale): array
    {
        $servicePages = $this->servicePages($locale, $fallbackLocale);
        $defaults = $this->cardDefaults();

        return collect(self::TEMPLATE_ORDER)
            ->map(function (string $templateKey) use ($servicePages, $defaults, $locale, $fallbackLocale): array {
                $servicePage = $servicePages->get($templateKey);
                $default = $defaults[$templateKey];
                $translation = $servicePage?->translations->firstWhere('locale', $locale)
                    ?? $servicePage?->translations->firstWhere('locale', $fallbackLocale)
                    ?? $servicePage?->translations->first();

                $title = trim((string) ($translation?->title ?? '')) ?: $default['title'];

                return [
                    'title' => $title,
                    'url' => $default['url'],
                    'image_url' => $this->serviceImageUrl($servicePage, $default['fallback_image']),
                    'template_key' => $templateKey,
                ];
            })
            ->values()
            ->all();
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
                return $media->getUrl('hero_1440x480');
            }

            if ($media->hasGeneratedConversion('card_360x240')) {
                return $media->getUrl('card_360x240');
            }

            return $media->getUrl();
        }

        return $this->versionedAsset($fallbackImage);
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
    private function cardDefaults(): array
    {
        return [
            ServicePageTemplateRegistry::FINANCE => [
                'title' => 'Financije',
                'url' => route('finance.show'),
                'fallback_image' => 'front-theme/images/services/finance-editorial-3d.svg',
            ],
            ServicePageTemplateRegistry::ACCOUNTING => [
                'title' => 'Računovodstvo',
                'url' => route('accounting.show'),
                'fallback_image' => 'front-theme/images/services/accounting-editorial-3d.svg',
            ],
            ServicePageTemplateRegistry::AUDIT => [
                'title' => 'Revizija',
                'url' => route('audit.show'),
                'fallback_image' => 'front-theme/images/services/audit-editorial-3d.svg',
            ],
            ServicePageTemplateRegistry::TAX => [
                'title' => 'Porezi',
                'url' => route('tax.show'),
                'fallback_image' => 'front-theme/images/services/tax-editorial-3d.svg',
            ],
            ServicePageTemplateRegistry::EU_FUNDS => [
                'title' => 'EU fondovi',
                'url' => route('eu-funds.show'),
                'fallback_image' => 'front-theme/images/services/advisory-editorial-3d.svg',
            ],
            ServicePageTemplateRegistry::FAMILY_BUSINESS => [
                'title' => 'Obiteljski biznis',
                'url' => route('family-business.show'),
                'fallback_image' => 'front-theme/images/services/family-business-editorial-3d.svg',
            ],
        ];
    }
}
