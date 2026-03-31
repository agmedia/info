<?php

namespace App\Support\Content;

class EuFundsCallCategoryRegistry
{
    public const UPCOMING = 'pozivi-u-najavi';
    public const OPEN = 'otvoreni-pozivi';
    public const CLOSED = 'zatvoreni-pozivi';

    /**
     * @return array<int, array{key:string,code:string,slug:string,title:string,tone:string}>
     */
    public static function definitions(string $locale = 'hr'): array
    {
        $isCroatian = str_starts_with(strtolower($locale), 'hr');

        return [
            [
                'key' => self::UPCOMING,
                'code' => self::UPCOMING,
                'slug' => self::UPCOMING,
                'title' => $isCroatian ? 'Pozivi u najavi' : 'Upcoming Calls',
                'tone' => 'pending',
            ],
            [
                'key' => self::OPEN,
                'code' => self::OPEN,
                'slug' => self::OPEN,
                'title' => $isCroatian ? 'Otvoreni pozivi' : 'Open Calls',
                'tone' => 'open',
            ],
            [
                'key' => self::CLOSED,
                'code' => self::CLOSED,
                'slug' => self::CLOSED,
                'title' => $isCroatian ? 'Zatvoreni pozivi' : 'Closed Calls',
                'tone' => 'closed',
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function orderedKeys(): array
    {
        return [
            self::UPCOMING,
            self::OPEN,
            self::CLOSED,
        ];
    }

    /**
     * @return array{key:string,code:string,slug:string,title:string,tone:string}|null
     */
    public static function definition(string $key, string $locale = 'hr'): ?array
    {
        foreach (self::definitions($locale) as $definition) {
            if ($definition['key'] === $key) {
                return $definition;
            }
        }

        return null;
    }
}
