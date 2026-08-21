<?php

namespace App\Support\Front;

final class FontRegistry
{
    public const DEFAULT = 'manrope';

    /**
     * @var array<string, array{family: string, weights: string, provider?: string}>
     */
    private const FONTS = [
        'manrope' => ['family' => 'Manrope', 'weights' => '200;300;400;500;600;700;800'],
        'inter' => ['family' => 'Inter', 'weights' => '300;400;500;600;700;800'],
        'general-sans' => ['family' => 'General Sans', 'weights' => '200,300,400,500,600,700', 'provider' => 'fontshare'],
        'roboto' => ['family' => 'Roboto', 'weights' => '300;400;500;600;700;800'],
        'open-sans' => ['family' => 'Open Sans', 'weights' => '300;400;500;600;700;800'],
        'lato' => ['family' => 'Lato', 'weights' => '300;400;700;900'],
        'montserrat' => ['family' => 'Montserrat', 'weights' => '300;400;500;600;700;800'],
        'poppins' => ['family' => 'Poppins', 'weights' => '300;400;500;600;700;800'],
        'raleway' => ['family' => 'Raleway', 'weights' => '300;400;500;600;700;800'],
        'nunito-sans' => ['family' => 'Nunito Sans', 'weights' => '300;400;500;600;700;800'],
        'source-sans-3' => ['family' => 'Source Sans 3', 'weights' => '300;400;500;600;700;800'],
        'dm-sans' => ['family' => 'DM Sans', 'weights' => '300;400;500;600;700;800'],
        'plus-jakarta-sans' => ['family' => 'Plus Jakarta Sans', 'weights' => '300;400;500;600;700;800'],
        'outfit' => ['family' => 'Outfit', 'weights' => '300;400;500;600;700;800'],
        'urbanist' => ['family' => 'Urbanist', 'weights' => '300;400;500;600;700;800'],
        'work-sans' => ['family' => 'Work Sans', 'weights' => '300;400;500;600;700;800'],
        'figtree' => ['family' => 'Figtree', 'weights' => '300;400;500;600;700;800'],
        'ibm-plex-sans' => ['family' => 'IBM Plex Sans', 'weights' => '300;400;500;600;700'],
        'noto-sans' => ['family' => 'Noto Sans', 'weights' => '300;400;500;600;700;800'],
        'rubik' => ['family' => 'Rubik', 'weights' => '300;400;500;600;700;800'],
        'mulish' => ['family' => 'Mulish', 'weights' => '300;400;500;600;700;800'],
        'karla' => ['family' => 'Karla', 'weights' => '300;400;500;600;700;800'],
        'barlow' => ['family' => 'Barlow', 'weights' => '300;400;500;600;700;800'],
        'cabin' => ['family' => 'Cabin', 'weights' => '400;500;600;700'],
        'archivo' => ['family' => 'Archivo', 'weights' => '300;400;500;600;700;800'],
        'space-grotesk' => ['family' => 'Space Grotesk', 'weights' => '300;400;500;600;700'],
        'red-hat-display' => ['family' => 'Red Hat Display', 'weights' => '300;400;500;600;700;800'],
        'sora' => ['family' => 'Sora', 'weights' => '300;400;500;600;700;800'],
        'lexend' => ['family' => 'Lexend', 'weights' => '300;400;500;600;700;800'],
        'assistant' => ['family' => 'Assistant', 'weights' => '300;400;500;600;700;800'],
        'libre-franklin' => ['family' => 'Libre Franklin', 'weights' => '300;400;500;600;700;800'],
        'merriweather' => ['family' => 'Merriweather', 'weights' => '300;400;500;600;700;800'],
        'lora' => ['family' => 'Lora', 'weights' => '400;500;600;700'],
        'playfair-display' => ['family' => 'Playfair Display', 'weights' => '400;500;600;700;800'],
        'source-serif-4' => ['family' => 'Source Serif 4', 'weights' => '300;400;500;600;700;800'],
        'noto-serif' => ['family' => 'Noto Serif', 'weights' => '300;400;500;600;700;800'],
        'cormorant-garamond' => ['family' => 'Cormorant Garamond', 'weights' => '300;400;500;600;700'],
        'roboto-slab' => ['family' => 'Roboto Slab', 'weights' => '300;400;500;600;700;800'],
        'bitter' => ['family' => 'Bitter', 'weights' => '300;400;500;600;700;800'],
        'dm-serif-display' => ['family' => 'DM Serif Display', 'weights' => '400'],
        'spectral' => ['family' => 'Spectral', 'weights' => '300;400;500;600;700;800'],
        'oswald' => ['family' => 'Oswald', 'weights' => '300;400;500;600;700'],
        'bebas-neue' => ['family' => 'Bebas Neue', 'weights' => '400'],
        'anton' => ['family' => 'Anton', 'weights' => '400'],
    ];

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_map(
            static fn (array $font): string => $font['family'].' · '.self::providerLabel($font['provider'] ?? 'google'),
            self::FONTS,
        );
    }

    /**
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return array_keys(self::FONTS);
    }

    public static function normalize(mixed $key): string
    {
        $key = is_string($key) ? trim($key) : '';

        return array_key_exists($key, self::FONTS) ? $key : self::DEFAULT;
    }

    /**
     * @return array{
     *     key: string,
     *     family: string,
     *     provider: string,
     *     stylesheet_url: string,
     *     preconnect_urls: array<string, bool>
     * }
     */
    public static function resolve(mixed $key): array
    {
        $key = self::normalize($key);
        $font = self::FONTS[$key];
        $provider = $font['provider'] ?? 'google';

        if ($provider === 'fontshare') {
            return [
                'key' => $key,
                'family' => $font['family'],
                'provider' => $provider,
                'stylesheet_url' => 'https://api.fontshare.com/v2/css?f[]=general-sans@'.$font['weights'].'&display=swap',
                'preconnect_urls' => [
                    'https://api.fontshare.com' => false,
                    'https://cdn.fontshare.com' => true,
                ],
            ];
        }

        $familyQuery = str_replace('%20', '+', rawurlencode($font['family']));

        return [
            'key' => $key,
            'family' => $font['family'],
            'provider' => $provider,
            'stylesheet_url' => 'https://fonts.googleapis.com/css2?family='.$familyQuery.':wght@'.$font['weights'].'&display=swap',
            'preconnect_urls' => [
                'https://fonts.googleapis.com' => false,
                'https://fonts.gstatic.com' => true,
            ],
        ];
    }

    private static function providerLabel(string $provider): string
    {
        return $provider === 'fontshare' ? 'Fontshare' : 'Google Fonts';
    }
}
