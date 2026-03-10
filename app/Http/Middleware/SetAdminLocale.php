<?php

namespace App\Http\Middleware;

use App\Models\Settings\Local\Language;
use App\Support\Admin\AdminLocale;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetAdminLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $available = $this->availableLocales();
        $default = AdminLocale::default();

        $queryLocale = AdminLocale::normalize((string) $request->query('admin_locale', ''));
        $sessionLocale = AdminLocale::normalize((string) $request->session()->get('admin_locale', ''));

        $locale = in_array($queryLocale, $available, true)
            ? $queryLocale
            : (in_array($sessionLocale, $available, true) ? $sessionLocale : (in_array($default, $available, true) ? $default : (string) ($available[0] ?? $default)));

        App::setLocale($locale);
        $request->setLocale($locale);
        $request->session()->put('admin_locale', $locale);

        return $next($request);
    }

    /**
     * @return array<int, string>
     */
    private function availableLocales(): array
    {
        $fallbacks = AdminLocale::fallbackOptions();

        try {
            $codes = Language::query()
                ->where('is_active', true)
                ->pluck('code')
                ->map(fn ($code) => AdminLocale::normalize((string) $code))
                ->filter(fn ($code) => $code !== '')
                ->unique()
                ->values()
                ->all();

            $merged = array_values(array_unique(array_filter([
                ...$fallbacks,
                ...$codes,
            ])));

            return $merged !== [] ? $merged : $fallbacks;
        } catch (\Throwable) {
            return $fallbacks;
        }
    }
}
