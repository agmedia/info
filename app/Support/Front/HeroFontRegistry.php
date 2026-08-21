<?php

namespace App\Support\Front;

final class HeroFontRegistry
{
    public const DEFAULT = 'bodoni-moda';

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
