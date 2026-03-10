<?php

namespace App\Support\Admin;

class AdminLocale
{
    public static function current(): string
    {
        $locale = self::normalize((string) app()->getLocale());

        return $locale !== '' ? $locale : self::default();
    }

    public static function default(): string
    {
        return self::normalize((string) config('admin_ui.locale.default', 'hr')) ?: 'hr';
    }

    public static function frontendFallback(): string
    {
        return self::normalize((string) config('app.locale', 'en')) ?: 'en';
    }

    /**
     * @return array<int, string>
     */
    public static function fallbackOptions(): array
    {
        $configured = array_map(
            static fn ($locale): string => self::normalize((string) $locale),
            (array) config('admin_ui.locale.fallback_options', [self::default(), self::frontendFallback()])
        );

        return array_values(array_unique(array_filter([
            self::default(),
            ...$configured,
            self::frontendFallback(),
        ])));
    }

    public static function normalize(string $locale): string
    {
        $normalized = strtolower(trim($locale));
        if ($normalized === '') {
            return '';
        }

        if (str_contains($normalized, '_')) {
            $normalized = (string) explode('_', $normalized)[0];
        }

        if (str_contains($normalized, '-')) {
            $normalized = (string) explode('-', $normalized)[0];
        }

        return $normalized;
    }
}
