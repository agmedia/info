<?php

namespace App\Support\Localization;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use IntlDateFormatter;

final class FrontendDate
{
    public static function long(DateTimeInterface $date, string $locale, string $timezone): string
    {
        $language = strtolower((string) preg_split('/[-_]/', trim($locale), 2)[0]);
        $localizedDate = CarbonImmutable::instance($date)->setTimezone($timezone);

        if (class_exists(IntlDateFormatter::class)) {
            try {
                $formatter = new IntlDateFormatter(
                    $language === 'hr' ? 'hr_HR' : 'en_GB',
                    IntlDateFormatter::NONE,
                    IntlDateFormatter::NONE,
                    $timezone,
                    IntlDateFormatter::GREGORIAN,
                    $language === 'hr' ? 'd. MMMM y.' : 'MMMM d, y',
                );
                $formatted = $formatter->format($localizedDate->getTimestamp());

                if (is_string($formatted) && trim($formatted) !== '') {
                    return $formatted;
                }
            } catch (\Throwable) {
                // Fall back to Carbon when Intl cannot format the configured locale.
            }
        }

        if ($language === 'hr') {
            return $localizedDate->locale('hr')->isoFormat('Do MMMM YYYY.');
        }

        return $localizedDate
            ->locale($language !== '' ? $language : 'en')
            ->translatedFormat('F j, Y');
    }
}
