<?php

namespace App\Support\Localization;

use Illuminate\Support\Facades\Route;

final class FrontendRoute
{
    private const LOCALIZED_ROUTE_NAMES = [
        'advisory.investment-incentives.show',
        'advisory.bank-loans.show',
        'advisory.due-diligence.show',
        'advisory.valuations.show',
        'advisory.finance.show',
        'advisory.funding.show',
        'advisory.tax.show',
        'advisory.ma.show',
        'accounting.show',
        'advisory.show',
        'services.index',
        'eu-funds.show',
        'eu-funds.questionnaire.create',
        'eu-funds.questionnaire.store',
        'career.applications.store',
        'newsletter.subscribe',
        'contact.create',
        'contact.store',
        'search.index',
        'team.index',
        'audit.show',
    ];

    public static function name(string $baseName, ?string $locale = null): string
    {
        $locale = strtolower(trim((string) ($locale ?: app()->getLocale())));
        $localizedName = $baseName.'.'.$locale;

        return FrontendLocalePolicy::requiresExactTranslation($locale) && Route::has($localizedName)
            ? $localizedName
            : $baseName;
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function url(string $baseName, array $parameters = [], ?string $locale = null): string
    {
        return route(self::name($baseName, $locale), $parameters);
    }

    public static function localizeUrl(string $url, ?string $locale = null): string
    {
        $url = trim($url);
        $locale = strtolower(trim((string) ($locale ?: app()->getLocale())));

        if ($url === '') {
            return $url;
        }

        $inputHost = parse_url($url, PHP_URL_HOST);
        if (is_string($inputHost) && $inputHost !== '' && ! hash_equals(request()->getHost(), $inputHost)) {
            return $url;
        }

        $inputPath = (string) (parse_url($url, PHP_URL_PATH) ?: $url);
        $query = parse_url($url, PHP_URL_QUERY);
        $fragment = parse_url($url, PHP_URL_FRAGMENT);

        foreach (self::LOCALIZED_ROUTE_NAMES as $baseName) {
            $candidateRouteNames = [$baseName];
            if (Route::has($baseName.'.en')) {
                $candidateRouteNames[] = $baseName.'.en';
            }
            $candidatePaths = array_map(
                static fn (string $routeName): string => (string) parse_url(route($routeName), PHP_URL_PATH),
                $candidateRouteNames
            );

            if (! in_array($inputPath, $candidatePaths, true)) {
                continue;
            }

            $localizedName = self::name($baseName, $locale);
            $localizedUrl = self::url($baseName, locale: $locale);
            $localizedPath = (string) parse_url($localizedUrl, PHP_URL_PATH);
            $suffix = ($query ? '?'.$query : '').($fragment ? '#'.$fragment : '');

            return is_string($inputHost) && $inputHost !== ''
                ? rtrim((string) preg_replace('#/+$#', '', $localizedUrl), '/').$suffix
                : $localizedPath.$suffix;
        }

        return $url;
    }
}
