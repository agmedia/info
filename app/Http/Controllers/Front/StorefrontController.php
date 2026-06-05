<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Support\Comment;
use App\Services\Front\ServiceCardService;
use App\Services\Front\StoreSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function __construct(
        private readonly ServiceCardService $serviceCardService,
        private readonly StoreSettingsService $storeSettingsService
    ) {
    }

    public function home(Request $request): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale', 'en'));
        $variant = (string) $request->attributes->get('frontend_variant', 'desktop');
        $latestBlogPosts = BlogPost::query()
            ->where('is_active', true)
            ->where(function ($q): void {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->with([
                'translations' => fn ($q) => $q->whereIn('locale', [$locale, $fallbackLocale]),
                'categories.translations' => fn ($q) => $q->whereIn('locale', [$locale, $fallbackLocale]),
                'media',
            ])
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();
        $clientTestimonials = $this->clientTestimonials($locale, $fallbackLocale);

        return view(
            $variant === 'mobile' ? 'front.mobile.home.index' : 'front.desktop.home.index',
            [
                'storeSettings' => $this->storeSettingsService->all(),
                'serviceCards' => $this->serviceCardService->cards((string) $locale, $fallbackLocale),
                'primaryServicePillars' => $this->serviceCardService->primaryPillars((string) $locale, $fallbackLocale),
                'latestBlogPosts' => $latestBlogPosts,
                'clientTestimonials' => $clientTestimonials,
                'locale' => $locale,
                'fallbackLocale' => $fallbackLocale,
            ]
        );
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
