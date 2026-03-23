<?php

namespace App\Support\Content;

use Illuminate\Support\Str;

class ResourceDocumentGroupRegistry
{
    public const DOWNLOADS = 'downloads';

    public const TRANSACTION_ANALYSIS = 'transaction-analysis';

    public const SECTOR_ANALYSIS = 'sector-analysis';

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::DOWNLOADS => 'Dokumenti za preuzimanje',
            self::TRANSACTION_ANALYSIS => 'Analiza transakcija',
            self::SECTOR_ANALYSIS => 'Analiza sektora',
        ];
    }

    public static function label(string $groupCode): string
    {
        return self::labels()[$groupCode]
            ?? Str::of($groupCode)->replace('-', ' ')->title()->value();
    }

    public static function inferFromTitle(string $title): string
    {
        $normalized = Str::lower(trim($title));

        return match (true) {
            Str::startsWith($normalized, 'analiza sektora') => self::SECTOR_ANALYSIS,
            Str::startsWith($normalized, 'analiza transakcija') => self::TRANSACTION_ANALYSIS,
            default => self::DOWNLOADS,
        };
    }
}
