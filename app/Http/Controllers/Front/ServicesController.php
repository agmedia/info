<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Models\Content\Service\ServicePage;
use App\Models\Content\Service\ServicePageTranslation;
use App\Services\Front\ServiceCardService;
use App\Support\Content\ServicePageTemplateRegistry;
use App\Support\Localization\FrontendLocalePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ServicesController extends Controller
{
    use ResolvesFrontendView;

    public function __construct(
        private readonly ServiceCardService $serviceCardService
    ) {}

    public function index(Request $request): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = FrontendLocalePolicy::fallbackLocale(
            (string) $locale,
            (string) config('app.fallback_locale', config('app.locale', 'en'))
        );
        [$servicePage, $servicePageTranslation] = $this->resolveServicePage((string) $locale, $fallbackLocale);
        abort_if(! $servicePageTranslation, 404);
        $translationPayload = (array) ($servicePageTranslation->payload ?? []);
        $showcase = (array) ($translationPayload['showcase'] ?? []);
        $primaryPillars = $this->serviceCardService->primaryPillars(
            (string) $locale,
            $fallbackLocale,
            (array) ($translationPayload['primary_pillars'] ?? []),
            useDefaultRoutes: false,
        );

        return view($this->frontendView($request, 'pages.services'), [
            'serviceCards' => $this->serviceCardService->cards((string) $locale, $fallbackLocale),
            'primaryServicePillars' => $primaryPillars,
            'servicesShowcase' => $showcase,
            'servicePageTitle' => $servicePageTitle = trim((string) ($servicePageTranslation?->title ?? '')),
            'servicePageMetaTitle' => trim((string) ($servicePageTranslation?->meta_title ?? '')),
            'servicePageMetaDescription' => trim((string) ($servicePageTranslation?->meta_description ?? '')),
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

        $translation = $servicePage->translations->firstWhere('locale', $locale);

        return [$servicePage, $translation];
    }
}
