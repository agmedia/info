<?php

namespace App\Http\Middleware;

use App\Support\Localization\FrontendLocalePolicy;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class EnsureFrontendRouteLocale
{
    public function handle(Request $request, Closure $next, string $expectedLocale): Response
    {
        $expectedLocale = strtolower(trim($expectedLocale));

        abort_unless($expectedLocale !== '', 404);

        $availableLocales = array_map(
            static fn (mixed $availableLocale): string => strtolower(trim((string) $availableLocale)),
            (array) $request->attributes->get('front_available_locales', [])
        );

        abort_unless(in_array($expectedLocale, $availableLocales, true), 404);

        $defaultLocale = strtolower((string) $request->attributes->get(
            'front_default_locale',
            config('app.locale')
        ));
        $requiresExactTranslation = FrontendLocalePolicy::requiresExactTranslation(
            $expectedLocale,
            $defaultLocale
        );

        // The localized path is authoritative even when the application already
        // resolved the same locale (for example, a fresh request to the default
        // language). Persist it so subsequent shared routes keep that language.
        $request->session()->put('front_locale', $expectedLocale);
        $request->attributes->set('front_locale', $expectedLocale);
        $request->attributes->set('front_requires_exact_translation', $requiresExactTranslation);

        App::setLocale($expectedLocale);
        View::share('frontLocale', $expectedLocale);
        View::share('frontRequiresExactTranslation', $requiresExactTranslation);

        return $next($request);
    }
}
