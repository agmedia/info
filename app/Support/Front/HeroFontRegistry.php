<?php

namespace App\Support\Front;

final class HeroFontRegistry
{
    public const DEFAULT = 'bodoni-moda';

    public const DEFAULT_WEIGHT = 450;

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            'website' => 'Koristi font web-stranice',
            'bodoni-moda' => 'Bodoni Moda · ugrađeni font',
            'instrument-sans' => 'Instrument Sans · ugrađeni font',
            ...FontRegistry::options(),
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return array_keys(self::options());
    }

    public static function normalize(mixed $key): string
    {
        $key = is_string($key) ? trim($key) : '';

        return in_array($key, self::keys(), true) ? $key : self::DEFAULT;
    }

    /**
     * @return array<int, int>
     */
    public static function weights(mixed $key, mixed $websiteFontKey = null): array
    {
        $key = self::normalize($key);

        return match ($key) {
            'website' => FontRegistry::weights($websiteFontKey),
            'bodoni-moda' => [400, 450, 500, 600, 700, 800, 900],
            'instrument-sans' => [400, 500, 600, 700],
            default => FontRegistry::weights($key),
        };
    }

    /**
     * @return array<int, string>
     */
    public static function weightOptions(mixed $key, mixed $websiteFontKey = null): array
    {
        $options = [];

        foreach (self::weights($key, $websiteFontKey) as $weight) {
            $options[$weight] = (string) $weight;
        }

        return $options;
    }

    public static function normalizeWeight(mixed $key, mixed $weight, mixed $websiteFontKey = null): int
    {
        $available = self::weights($key, $websiteFontKey);
        $weight = is_numeric($weight) ? (int) $weight : self::DEFAULT_WEIGHT;

        if (in_array($weight, $available, true)) {
            return $weight;
        }

        usort($available, static fn (int $left, int $right): int => abs($left - $weight) <=> abs($right - $weight));

        return $available[0] ?? 400;
    }

    /**
     * @param  array<string, mixed>  $websiteTypography
     * @return array{
     *     key: string,
     *     family: string,
     *     provider: string,
     *     stylesheet_url: string,
     *     preconnect_urls: array<string, bool>
     * }
     */
    public static function resolve(mixed $key, array $websiteTypography = []): array
    {
        $key = self::normalize($key);

        if ($key === 'website') {
            return [
                'key' => $key,
                'family' => (string) ($websiteTypography['family'] ?? 'Instrument Sans Variable'),
                'provider' => 'website',
                'stylesheet_url' => '',
                'preconnect_urls' => [],
            ];
        }

        if ($key === 'bodoni-moda') {
            return [
                'key' => $key,
                'family' => 'Bodoni Moda Variable',
                'provider' => 'bundled',
                'stylesheet_url' => '',
                'preconnect_urls' => [],
            ];
        }

        if ($key === 'instrument-sans') {
            return [
                'key' => $key,
                'family' => 'Instrument Sans Variable',
                'provider' => 'bundled',
                'stylesheet_url' => '',
                'preconnect_urls' => [],
            ];
        }

        return FontRegistry::resolve($key);
    }
}
