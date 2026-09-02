<?php

namespace App\Support\Content;

class EuFundsLinkedBlogPostRegistry
{
    /**
     * Return only the blog posts linked as supporting EU-funds content.
     * Status-group entries are handled by the dedicated call importer and are
     * deliberately excluded from this list.
     *
     * @return array<int, string>
     */
    public static function slugs(string $locale = 'hr'): array
    {
        $defaults = EuFundsServicePageDefaults::defaultsForLocale($locale);
        $slugs = [];

        foreach ([
            data_get($defaults, 'calls.other_calls', []),
            data_get($defaults, 'resources', []),
            data_get($defaults, 'laws', []),
        ] as $section) {
            self::collectBlogSlugs($section, $slugs);
        }

        return array_values(array_unique($slugs));
    }

    /**
     * @param  array<int, string>  $slugs
     */
    private static function collectBlogSlugs(mixed $value, array &$slugs): void
    {
        if (! is_array($value)) {
            return;
        }

        if (($value['type'] ?? null) === 'blog') {
            $slug = trim((string) ($value['slug'] ?? ''));
            if ($slug !== '') {
                $slugs[] = $slug;
            }

            return;
        }

        foreach ($value as $child) {
            self::collectBlogSlugs($child, $slugs);
        }
    }
}
