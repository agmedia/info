<?php

namespace App\Services\Content;

use App\Models\Content\Glossary\GlossaryTerm;
use App\Models\Content\Glossary\GlossaryTermTranslation;
use App\Models\Content\Page\InfoPage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class GlossaryImportService
{
    public const DEFAULT_COLLECTION = 'svijet-financija';
    public const DEFAULT_PAGE_CODE = 'finance-glossary';
    public const DEFAULT_PAGE_TITLE = 'Svijet financija';
    public const DEFAULT_PAGE_SLUG = 'svijet-financija';
    public const DEFAULT_PAGE_KICKER = 'Rječnik pojmova';
    public const DEFAULT_PAGE_EXCERPT = 'Pretražite financijske i računovodstvene pojmove na jednom mjestu.';

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function import(string $file, array $options = []): array
    {
        if (! is_file($file) || ! is_readable($file)) {
            throw new InvalidArgumentException('CSV file is not readable: '.$file);
        }

        $rows = $this->readCsv($file);
        $locale = trim((string) ($options['locale'] ?? 'hr')) ?: 'hr';
        $collectionCode = $this->normalizeCode((string) ($options['collection'] ?? self::DEFAULT_COLLECTION), self::DEFAULT_COLLECTION);
        $pageCode = $this->normalizeCode((string) ($options['page_code'] ?? self::DEFAULT_PAGE_CODE), self::DEFAULT_PAGE_CODE);
        $pageTitle = trim((string) ($options['page_title'] ?? self::DEFAULT_PAGE_TITLE)) ?: self::DEFAULT_PAGE_TITLE;
        $pageSlug = Str::slug((string) ($options['page_slug'] ?? self::DEFAULT_PAGE_SLUG)) ?: self::DEFAULT_PAGE_SLUG;
        $pageKicker = trim((string) ($options['page_kicker'] ?? self::DEFAULT_PAGE_KICKER)) ?: self::DEFAULT_PAGE_KICKER;
        $pageExcerpt = trim((string) ($options['page_excerpt'] ?? self::DEFAULT_PAGE_EXCERPT)) ?: self::DEFAULT_PAGE_EXCERPT;
        $userId = isset($options['user_id']) && $options['user_id'] !== null
            ? (int) $options['user_id']
            : null;

        $imported = [];
        $page = null;

        DB::transaction(function () use (
            $rows,
            $file,
            $locale,
            $collectionCode,
            $pageCode,
            $pageTitle,
            $pageSlug,
            $pageKicker,
            $pageExcerpt,
            $userId,
            &$imported,
            &$page
        ): void {
            $page = $this->upsertPage(
                pageCode: $pageCode,
                locale: $locale,
                collectionCode: $collectionCode,
                pageTitle: $pageTitle,
                pageSlug: $pageSlug,
                pageKicker: $pageKicker,
                pageExcerpt: $pageExcerpt,
                userId: $userId
            );

            /** @var array<string, int> $slugRegistry */
            $slugRegistry = GlossaryTermTranslation::query()
                ->where('locale', $locale)
                ->pluck('term_id', 'slug')
                ->map(fn ($termId): int => (int) $termId)
                ->all();

            foreach ($rows as $index => $row) {
                $title = trim((string) ($row['Title'] ?? ''));
                if ($title === '') {
                    continue;
                }

                $legacyId = trim((string) ($row['Id'] ?? ''));
                $description = trim((string) ($row['Description'] ?? ''));
                $code = $this->buildTermCode($collectionCode, $legacyId, $title, $index);

                $term = GlossaryTerm::query()->firstOrNew(['code' => $code]);
                $term->fill([
                    'collection_code' => $collectionCode,
                    'is_active' => true,
                    'sort_order' => $index,
                    'payload' => [
                        'legacy_id' => $legacyId !== '' ? $legacyId : null,
                        'source' => 'csv',
                        'source_file' => basename($file),
                    ],
                    'updated_by' => $userId,
                ]);

                if (! $term->exists) {
                    $term->created_by = $userId;
                }

                $term->save();

                $slugBase = Str::slug($title) ?: $code;
                $slug = $this->resolveUniqueSlug($slugBase, $slugRegistry, $term->id);

                $translationPayload = [
                    'legacy_id' => $legacyId !== '' ? $legacyId : null,
                    'synonyms' => $this->parseListField((string) ($row['Synonyms'] ?? '')),
                    'variations' => $this->parseListField((string) ($row['Variations'] ?? '')),
                    'categories' => $this->parseListField((string) ($row['Categories'] ?? '')),
                    'abbreviation' => trim((string) ($row['Abbreviation'] ?? '')) ?: null,
                    'tags' => $this->parseListField((string) ($row['Tags'] ?? '')),
                    'image' => trim((string) ($row['Image'] ?? '')) ?: null,
                ];

                $term->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'title' => $title,
                        'slug' => $slug,
                        'excerpt' => $this->extractExcerpt((string) ($row['Excerpt'] ?? ''), $description),
                        'body_html' => $this->ensureHtmlBody($description),
                        'meta_title' => $title,
                        'meta_description' => $this->extractExcerpt((string) ($row['Excerpt'] ?? ''), $description),
                        'payload' => $translationPayload,
                    ]
                );

                $slugRegistry[$slug] = (int) $term->id;

                $imported[] = [
                    'code' => $term->code,
                    'title' => $title,
                    'slug' => $slug,
                ];
            }
        });

        return [
            'file' => $file,
            'locale' => $locale,
            'collection' => $collectionCode,
            'page_code' => $pageCode,
            'page_slug' => $pageSlug,
            'page_id' => $page?->id,
            'imported_count' => count($imported),
            'imported' => $imported,
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function readCsv(string $file): array
    {
        $handle = fopen($file, 'rb');
        if (! $handle) {
            throw new InvalidArgumentException('Unable to open CSV file: '.$file);
        }

        try {
            $header = fgetcsv($handle);
            if (! is_array($header)) {
                throw new InvalidArgumentException('CSV header is missing or invalid.');
            }

            $header = array_map(function ($column): string {
                $clean = (string) $column;
                $clean = preg_replace('/^\xEF\xBB\xBF/', '', $clean) ?? $clean;

                return trim($clean);
            }, $header);

            $rows = [];

            while (($values = fgetcsv($handle)) !== false) {
                if ($values === [null] || $values === []) {
                    continue;
                }

                $row = [];
                foreach ($header as $index => $column) {
                    $row[$column] = trim((string) ($values[$index] ?? ''));
                }

                $rows[] = $row;
            }
        } finally {
            fclose($handle);
        }

        return $rows;
    }

    private function upsertPage(
        string $pageCode,
        string $locale,
        string $collectionCode,
        string $pageTitle,
        string $pageSlug,
        string $pageKicker,
        string $pageExcerpt,
        ?int $userId
    ): InfoPage {
        $page = InfoPage::query()->firstOrNew(['code' => $pageCode]);
        $payload = is_array($page->payload) ? $page->payload : [];

        $payload['glossary_collection'] = $collectionCode;
        $payload['glossary_kicker'] = $pageKicker;
        $payload['glossary_search_placeholder'] = 'Pretražite pojam, kraticu ili povezani izraz';
        $payload['glossary_empty_title'] = 'Nema rezultata za zadane filtre';
        $payload['glossary_empty_body'] = 'Pokušajte s drugim pojmom ili vratite prikaz na sva slova.';

        $page->fill([
            'layout' => 'finance_glossary',
            'is_active' => true,
            'published_at' => $page->published_at ?? now(),
            'sort_order' => (int) ($page->sort_order ?? 0),
            'payload' => $payload,
            'updated_by' => $userId,
        ]);

        if (! $page->exists) {
            $page->created_by = $userId;
        }

        $page->save();

        $page->translations()->updateOrCreate(
            ['locale' => $locale],
            [
                'title' => $pageTitle,
                'slug' => $pageSlug,
                'excerpt' => $pageExcerpt,
                'body_html' => '<p>'.$pageExcerpt.'</p>',
                'meta_title' => $pageTitle,
                'meta_description' => $pageExcerpt,
                'payload' => [
                    'glossary_kicker' => $pageKicker,
                ],
            ]
        );

        return $page;
    }

    private function buildTermCode(string $collectionCode, string $legacyId, string $title, int $index): string
    {
        if ($legacyId !== '') {
            return $collectionCode.'-'.Str::slug($legacyId);
        }

        $base = Str::slug($title) ?: 'term-'.($index + 1);

        return $collectionCode.'-'.$base;
    }

    /**
     * @param  array<string, int>  $slugRegistry
     */
    private function resolveUniqueSlug(string $baseSlug, array $slugRegistry, int $termId): string
    {
        $baseSlug = trim($baseSlug) !== '' ? $baseSlug : 'pojam-'.$termId;

        if (! isset($slugRegistry[$baseSlug]) || $slugRegistry[$baseSlug] === $termId) {
            return $baseSlug;
        }

        $suffix = 2;
        do {
            $candidate = $baseSlug.'-'.$suffix;
            $suffix++;
        } while (isset($slugRegistry[$candidate]) && $slugRegistry[$candidate] !== $termId);

        return $candidate;
    }

    /**
     * @return array<int, string>
     */
    private function parseListField(string $value): array
    {
        return collect(preg_split('/[\r\n,;|]+/u', $value) ?: [])
            ->map(fn ($item): string => trim((string) $item))
            ->filter(fn (string $item): bool => $item !== '')
            ->unique()
            ->values()
            ->all();
    }

    private function extractExcerpt(string $excerpt, string $description): ?string
    {
        $text = $this->plainText($excerpt !== '' ? $excerpt : $description);
        if ($text === '') {
            return null;
        }

        if (mb_strlen($text) <= 220) {
            return $text;
        }

        $snippet = mb_substr($text, 0, 220);
        $lastSpace = mb_strrpos($snippet, ' ');
        if ($lastSpace !== false && $lastSpace > 140) {
            $snippet = mb_substr($snippet, 0, $lastSpace);
        }

        return rtrim($snippet, " \t\n\r\0\x0B,.;:-").'...';
    }

    private function ensureHtmlBody(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        if ($this->containsBlockHtml($value)) {
            return $value;
        }

        $normalized = preg_replace("/\r\n?/", "\n", $value) ?? $value;
        $paragraphs = collect(preg_split('/\n{2,}/u', $normalized) ?: [$normalized])
            ->map(fn ($paragraph): string => trim((string) $paragraph))
            ->filter(fn (string $paragraph): bool => $paragraph !== '')
            ->map(function (string $paragraph): string {
                $encoded = e($paragraph);
                $encoded = preg_replace('/&lt;(\/?(?:strong|em|b|i|u|sup|sub|span)\b[^&]*?)&gt;/i', '<$1>', $encoded) ?? $encoded;
                $encoded = nl2br($encoded, false);

                return '<p>'.$encoded.'</p>';
            });

        return $paragraphs->implode('');
    }

    private function plainText(string $value): string
    {
        $value = strip_tags(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        return trim($value);
    }

    private function looksLikeHtml(string $value): bool
    {
        return $value !== strip_tags($value);
    }

    private function containsBlockHtml(string $value): bool
    {
        return preg_match('/<(?:p|div|ul|ol|li|table|blockquote|h[1-6]|br)\b/i', $value) === 1;
    }

    private function normalizeCode(string $value, string $fallback): string
    {
        $normalized = (string) Str::of($value)
            ->lower()
            ->ascii()
            ->replace('_', '-')
            ->replaceMatches('/[^a-z0-9\-]+/', '-')
            ->replaceMatches('/\-+/', '-')
            ->trim('-');

        return $normalized !== '' ? $normalized : $fallback;
    }
}
