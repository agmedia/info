<?php

namespace App\Http\Middleware;

use App\Models\Content\Page\InfoPageTranslation;
use App\Support\Localization\FrontendLocalePolicy;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class InferFrontendLocaleFromInfoPageSlug
{
    public function handle(Request $request, Closure $next): Response
    {
        $slug = trim((string) $request->route('slug'));
        $availableLocales = array_values(array_filter(array_map(
            static fn (mixed $locale): string => strtolower(trim((string) $locale)),
            (array) $request->attributes->get('front_available_locales', [])
        )));

        if ($slug === '' || $availableLocales === []) {
            return $next($request);
        }

        try {
            $matchingLocales = InfoPageTranslation::query()
                ->where('slug', $slug)
                ->whereIn('locale', $availableLocales)
                ->whereHas('page', fn ($query) => $query
                    ->where('is_active', true)
                    ->where(function ($publishedQuery): void {
                        $publishedQuery->whereNull('published_at')
                            ->orWhere('published_at', '<=', now());
                    }))
                ->pluck('locale')
                ->map(static fn (mixed $locale): string => strtolower(trim((string) $locale)))
                ->filter()
                ->unique()
                ->values();
        } catch (\Throwable) {
            return $next($request);
        }

        if ($matchingLocales->count() !== 1) {
            return $next($request);
        }

        $targetLocale = (string) $matchingLocales->first();
        if (strtolower((string) app()->getLocale()) !== $targetLocale) {
            $defaultLocale = strtolower((string) $request->attributes->get(
                'front_default_locale',
                config('app.locale')
            ));
            $requiresExactTranslation = FrontendLocalePolicy::requiresExactTranslation(
                $targetLocale,
                $defaultLocale
            );

            $request->session()->put('front_locale', $targetLocale);
            $request->attributes->set('front_locale', $targetLocale);
            $request->attributes->set('front_requires_exact_translation', $requiresExactTranslation);

            App::setLocale($targetLocale);
            View::share('frontLocale', $targetLocale);
            View::share('frontRequiresExactTranslation', $requiresExactTranslation);
        }

        return $next($request);
    }
}
