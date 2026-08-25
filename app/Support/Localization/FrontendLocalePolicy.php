<?php

namespace App\Support\Localization;

final class FrontendLocalePolicy
{
    public static function defaultLocale(): string
    {
        $requestDefault = app()->bound('request')
            ? self::normalize((string) request()->attributes->get('front_default_locale', ''))
            : '';

        if ($requestDefault !== '') {
            return $requestDefault;
        }

        return self::normalize((string) config('app.fallback_locale', config('app.locale', 'hr'))) ?: 'hr';
    }

    public static function requiresExactTranslation(string $locale, ?string $defaultLocale = null): bool
    {
        $locale = self::normalize($locale);
        $defaultLocale = self::normalize((string) $defaultLocale) ?: self::defaultLocale();

        return $locale !== '' && $locale !== $defaultLocale;
    }

    public static function fallbackLocale(string $locale, ?string $configuredFallback = null): string
    {
        $locale = self::normalize($locale) ?: self::defaultLocale();

        if (self::requiresExactTranslation($locale)) {
            return $locale;
        }

        return self::normalize((string) ($configuredFallback ?? config('app.fallback_locale')))
            ?: self::defaultLocale();
    }

    /**
     * @return array<int, string>
     */
    public static function queryLocales(string $locale, ?string $configuredFallback = null): array
    {
        $locale = self::normalize($locale) ?: self::defaultLocale();

        return array_values(array_unique([
            $locale,
            self::fallbackLocale($locale, $configuredFallback),
        ]));
    }

    private static function normalize(string $locale): string
    {
        $locale = strtolower(trim($locale));

        if ($locale === '') {
            return '';
        }

        return (string) preg_split('/[-_]/', $locale, 2)[0];
    }
}
