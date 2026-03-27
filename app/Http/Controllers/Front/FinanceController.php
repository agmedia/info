<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Models\Content\Service\ServicePage;
use App\Models\Content\Service\ServicePageTranslation;
use App\Support\Content\ServicePageTemplateRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class FinanceController extends Controller
{
    use ResolvesFrontendView;

    public function show(Request $request): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale', 'en'));

        [$servicePage, $servicePageTranslation] = $this->resolveServicePage($locale, $fallbackLocale);

        $translationPayload = ServicePageTemplateRegistry::mergeTranslationPayload(
            ServicePageTemplateRegistry::FINANCE,
            $servicePageTranslation?->payload,
            (string) ($servicePageTranslation?->locale ?: $locale)
        );

        return view($this->frontendView($request, 'pages.finance'), [
            'heroSection' => (array) ($translationPayload['hero'] ?? []),
            'pandeaSection' => (array) ($translationPayload['pandea'] ?? []),
            'servicesIntroSection' => (array) ($translationPayload['services_intro'] ?? []),
            'maSection' => (array) ($translationPayload['ma'] ?? []),
            'dueDiligenceSection' => (array) ($translationPayload['due_diligence'] ?? []),
            'valuationsSection' => (array) ($translationPayload['valuations'] ?? []),
            'capitalRaisingSection' => (array) ($translationPayload['capital_raising'] ?? []),
            'restructuringSection' => (array) ($translationPayload['restructuring'] ?? []),
            'meetingSection' => (array) ($translationPayload['meeting'] ?? []),
            'heroBackgroundUrl' => $this->resolveServiceHeroBackgroundUrl($servicePage),
            'financeIllustrationUrl' => $this->versionedAsset('front-theme/images/services/finance-editorial-3d.svg'),
            'pandeaLogoUrl' => $this->versionedAsset('front-theme/images/logos/pandea-logo-small.png'),
            'servicePageTitle' => trim((string) ($servicePageTranslation?->title ?? '')) ?: 'Financije',
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
            ->where('template_key', ServicePageTemplateRegistry::FINANCE)
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
                'media',
            ])
            ->orderByRaw('case when code = ? then 0 else 1 end', [ServicePageTemplateRegistry::defaultCode(ServicePageTemplateRegistry::FINANCE)])
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

    private function resolveServiceHeroBackgroundUrl(?ServicePage $servicePage): string
    {
        $mediaUrl = $servicePage
            ? (string) ($servicePage->getFirstMediaUrl('service_hero_image', 'hero_1440x480') ?: $servicePage->getFirstMediaUrl('service_hero_image'))
            : '';

        if ($mediaUrl !== '') {
            return $mediaUrl;
        }

        return $this->versionedAsset('front-theme/images/services/finance-editorial-3d.svg');
    }

    private function versionedAsset(string $relativePath): string
    {
        $absolutePath = public_path($relativePath);

        return is_file($absolutePath)
            ? asset($relativePath).'?v='.filemtime($absolutePath)
            : asset($relativePath);
    }
}
