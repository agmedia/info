<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Support\Comment;
use App\Services\Content\ContentBlockResolver;
use App\Services\Front\ServiceCardService;
use App\Services\Front\StoreSettingsService;
use App\Support\Localization\FrontendLocalePolicy;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Vite;

class StorefrontController extends Controller
{
    public function __construct(
        private readonly ContentBlockResolver $contentBlockResolver,
        private readonly ServiceCardService $serviceCardService,
        private readonly StoreSettingsService $storeSettingsService
    ) {}

    public function home(Request $request): Response
    {
        $locale = (string) app()->getLocale();
        $requiresExactTranslation = (bool) $request->attributes->get(
            'front_requires_exact_translation',
            FrontendLocalePolicy::requiresExactTranslation($locale)
        );
        $fallbackLocale = FrontendLocalePolicy::fallbackLocale(
            $locale,
            (string) config('app.fallback_locale', config('app.locale', 'en'))
        );
        $queryLocales = FrontendLocalePolicy::queryLocales($locale, $fallbackLocale);
        $variant = (string) $request->attributes->get('frontend_variant', 'desktop');
        $homeHeroBlocks = $this->contentBlockResolver->forPlacement('home.hero', $locale, null, null, $variant);
        $homeStatsBlocks = $this->contentBlockResolver->forPlacement('home.stats', $locale, null, null, $variant);
        $homeServicesBlocks = $this->contentBlockResolver->forPlacement('home.services', $locale, null, null, $variant);
        $latestBlogPosts = BlogPost::query()
            ->where('is_active', true)
            ->when($requiresExactTranslation, static function ($query) use ($locale): void {
                $query->whereHas('translations', static fn ($translations) => $translations->where('locale', $locale));
            })
            ->where(function ($q): void {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->with([
                'translations' => fn ($q) => $q->whereIn('locale', $queryLocales),
                'categories.translations' => fn ($q) => $q->whereIn('locale', $queryLocales),
                'media',
            ])
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();
        $clientTestimonials = $this->clientTestimonials($locale, $fallbackLocale);

        $response = response()->view(
            $variant === 'mobile' ? 'front.mobile.home.index' : 'front.desktop.home.index',
            [
                'storeSettings' => $this->storeSettingsService->all(),
                'homeHeroBlocks' => $homeHeroBlocks,
                'homeStatsBlocks' => $homeStatsBlocks,
                'homeServicesBlocks' => $homeServicesBlocks,
                'serviceCards' => $this->serviceCardService->cards((string) $locale, $fallbackLocale),
                'primaryServicePillars' => $this->serviceCardService->primaryPillars((string) $locale, $fallbackLocale),
                'latestBlogPosts' => $latestBlogPosts,
                'clientTestimonials' => $clientTestimonials,
                'locale' => $locale,
                'fallbackLocale' => $fallbackLocale,
            ]
        );

        $alphaStylesheetPath = 'front-theme/styles/alpha-redesign.css';
        $response->headers->set('Link', implode(', ', [
            '<'.Vite::asset('resources/css/app.css').'>; rel=preload; as=style',
            '<'.asset($alphaStylesheetPath).'?v='.filemtime(public_path($alphaStylesheetPath)).'>; rel=preload; as=style',
        ]));

        return $response;
    }

    /**
     * @return Collection<int, Comment>
     */
    private function clientTestimonials(string $locale, string $fallbackLocale): Collection
    {
        $buildQuery = static fn (string $targetLocale) => Comment::query()
            ->whereNull('commentable_type')
            ->where('status', Comment::STATUS_APPROVED)
            ->where('locale', $targetLocale)
            ->orderByDesc('is_featured')
            ->orderByDesc('reviewed_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(8);

        $rows = $buildQuery($locale)->get();

        if ($rows->isEmpty() && $fallbackLocale !== $locale) {
            $rows = $buildQuery($fallbackLocale)->get();
        }

        return $rows;
    }
}
