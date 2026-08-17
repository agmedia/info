<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Models\Content\Service\ServicePage;
use App\Models\Content\Service\ServicePageTranslation;
use App\Services\Front\ServiceCardService;
use App\Support\Content\ServicePageTemplateRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ServicesController extends Controller
{
    use ResolvesFrontendView;

    public function __construct(
        private readonly ServiceCardService $serviceCardService
    ) {
    }

    public function index(Request $request): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale', 'en'));
        [$servicePage, $servicePageTranslation] = $this->resolveServicePage((string) $locale, $fallbackLocale);
        $translationPayload = ServicePageTemplateRegistry::mergeTranslationPayload(
            ServicePageTemplateRegistry::SERVICES_INDEX,
            $servicePageTranslation?->payload,
            (string) ($servicePageTranslation?->locale ?: $locale)
        );
        $showcase = (array) ($translationPayload['showcase'] ?? []);
        $primaryPillars = $this->serviceCardService->primaryPillars(
            (string) $locale,
            $fallbackLocale,
            (array) ($translationPayload['primary_pillars'] ?? [])
        );
        $primaryPillars = $this->withLandingCardImages($primaryPillars, $servicePage);

        return view($this->frontendView($request, 'pages.services'), [
            'serviceCards' => $this->serviceCardService->cards((string) $locale, $fallbackLocale),
            'primaryServicePillars' => $primaryPillars,
            'servicesShowcase' => $showcase,
            'servicePageTitle' => trim((string) ($servicePageTranslation?->title ?? '')) ?: 'Usluge',
            'servicePageMetaTitle' => trim((string) ($servicePageTranslation?->meta_title ?? '')) ?: 'Usluge | ALPHA CAPITALIS',
            'servicePageMetaDescription' => trim((string) ($servicePageTranslation?->meta_description ?? '')) ?: 'Pregled usluga ALPHA CAPITALISA: revizija, racunovodstvo i poslovno savjetovanje.',
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
            ->where('template_key', ServicePageTemplateRegistry::SERVICES_INDEX)
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
            ])
            ->orderByRaw('case when code = ? then 0 else 1 end', [ServicePageTemplateRegistry::defaultCode(ServicePageTemplateRegistry::SERVICES_INDEX)])
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

    /**
     * @param  array<int, array<string, mixed>>  $pillars
     * @return array<int, array<string, mixed>>
     */
    private function withLandingCardImages(array $pillars, ?ServicePage $servicePage): array
    {
        $fallbacks = [
            'audit' => asset('alpha/service-revizija.jpg'),
            'accounting' => asset('alpha/service-racunovodstvo.jpg'),
            'advisory' => asset('alpha/service-savjetovanje.jpg'),
        ];

        return collect($pillars)
            ->map(function (array $pillar) use ($servicePage, $fallbacks): array {
                $cardKey = trim((string) ($pillar['key'] ?? ''));
                $collection = ServicePageTemplateRegistry::SERVICES_INDEX_CARD_MEDIA_COLLECTIONS[$cardKey] ?? null;
                $media = $collection ? $servicePage?->getFirstMedia($collection) : null;

                $pillar['image_url'] = $media
                    ? ($media->hasGeneratedConversion('services_index_card_1080x1350')
                        ? $media->getUrl('services_index_card_1080x1350')
                        : $media->getUrl())
                    : (string) ($fallbacks[$cardKey] ?? '');

                return $pillar;
            })
            ->values()
            ->all();
    }
}
