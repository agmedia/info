<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Models\Content\Glossary\GlossaryTerm;
use App\Models\Content\Page\InfoPage;
use App\Services\Content\ContentBlockResolver;
use App\Services\Content\GlossaryImportService;
use App\Support\Localization\FrontendLocalePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GlossaryController extends Controller
{
    use ResolvesFrontendView;

    public function index(Request $request): View
    {
        $locale = (string) app()->getLocale();
        $fallbackLocale = FrontendLocalePolicy::fallbackLocale($locale, (string) config('app.locale'));
        $variant = $this->frontendVariant($request);

        [$page, $pageTranslation] = $this->resolveGlossaryPage($locale, $fallbackLocale);
        $targetRef = (string) ($pageTranslation?->slug ?: $page->code);

        $topBlocks = app(ContentBlockResolver::class)->forPlacement(
            placement: 'page.top',
            locale: $locale,
            targetType: 'page',
            targetRef: $targetRef,
            frontendVariant: $variant
        );

        $bottomBlocks = app(ContentBlockResolver::class)->forPlacement(
            placement: 'page.bottom',
            locale: $locale,
            targetType: 'page',
            targetRef: $targetRef,
            frontendVariant: $variant
        );

        $glossaryData = $this->buildGlossaryIndexData($request, $page, $locale, $fallbackLocale);

        return view($this->frontendView($request, 'pages.glossary'), [
            'page' => $page,
            'selectedTranslation' => $pageTranslation,
            'glossaryPage' => $page,
            'glossaryPageTranslation' => $pageTranslation,
            'topBlocks' => $topBlocks,
            'bottomBlocks' => $bottomBlocks,
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
            ...$glossaryData,
        ]);
    }

    public function show(Request $request, string $slug): View
    {
        $locale = (string) app()->getLocale();
        $fallbackLocale = FrontendLocalePolicy::fallbackLocale($locale, (string) config('app.locale'));
        $variant = $this->frontendVariant($request);

        [$page, $pageTranslation] = $this->resolveGlossaryPage($locale, $fallbackLocale);
        $targetRef = (string) ($pageTranslation?->slug ?: $page->code);

        $topBlocks = app(ContentBlockResolver::class)->forPlacement(
            placement: 'page.top',
            locale: $locale,
            targetType: 'page',
            targetRef: $targetRef,
            frontendVariant: $variant
        );

        $bottomBlocks = app(ContentBlockResolver::class)->forPlacement(
            placement: 'page.bottom',
            locale: $locale,
            targetType: 'page',
            targetRef: $targetRef,
            frontendVariant: $variant
        );

        $collectionCode = $this->glossaryCollectionCode($page);
        $term = $this->resolveGlossaryTerm($slug, $collectionCode, $locale, $fallbackLocale);
        $termTranslation = $this->pickGlossaryTranslationForSlug($term, $slug, $locale, $fallbackLocale);
        abort_if(! $termTranslation, 404);

        $payload = is_array($termTranslation->payload) ? $termTranslation->payload : [];
        $title = trim((string) ($termTranslation->title ?? ''));
        $detailContent = $this->prepareGlossaryDetailContent(
            excerpt: (string) ($termTranslation->excerpt ?? ''),
            bodyHtml: (string) ($termTranslation->body_html ?? '')
        );
        $relatedTerms = $this->relatedGlossaryTerms(
            collectionCode: $collectionCode,
            currentTermId: (int) $term->id,
            letterKey: $this->glossaryLetterKey($title),
            locale: $locale,
            fallbackLocale: $fallbackLocale
        );

        return view($this->frontendView($request, 'pages.glossary-term'), [
            'glossaryPage' => $page,
            'glossaryPageTranslation' => $pageTranslation,
            'glossaryTerm' => $term,
            'glossaryTermTranslation' => $termTranslation,
            'glossaryTermLead' => $detailContent['lead'],
            'glossaryTermBodyHtml' => $detailContent['body_html'],
            'glossaryTermPayload' => $payload,
            'relatedGlossaryTerms' => $relatedTerms,
            'topBlocks' => $topBlocks,
            'bottomBlocks' => $bottomBlocks,
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
        ]);
    }

    /**
     * @return array{0: InfoPage, 1: mixed}
     */
    private function resolveGlossaryPage(string $locale, string $fallbackLocale): array
    {
        $page = InfoPage::query()
            ->where('layout', 'finance_glossary')
            ->where('is_active', true)
            ->when(
                FrontendLocalePolicy::requiresExactTranslation($locale),
                fn (Builder $query) => $query->whereHas('translations', fn (Builder $translationQuery) => $translationQuery
                    ->where('locale', $locale))
            )
            ->where(function ($query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->with(['translations' => fn ($query) => $query->when(
                FrontendLocalePolicy::requiresExactTranslation($locale),
                fn ($translationQuery) => $translationQuery->whereIn('locale', [$locale, $fallbackLocale])
            )])
            ->get()
            ->sortBy(function (InfoPage $candidate): int {
                return $candidate->code === GlossaryImportService::DEFAULT_PAGE_CODE ? 0 : 1;
            })
            ->first();

        abort_if(! $page, 404);

        $translation = $page->translations->firstWhere('locale', $locale)
            ?? $page->translations->firstWhere('locale', $fallbackLocale)
            ?? $page->translations->first();

        abort_if(! $translation, 404);

        return [$page, $translation];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildGlossaryIndexData(Request $request, InfoPage $page, string $locale, string $fallbackLocale): array
    {
        $alphabet = collect(['ALL', '0-9', ...range('A', 'Z')]);
        $search = trim((string) $request->query('q', ''));
        $activeLetter = strtoupper(trim((string) $request->query('letter', 'ALL')));
        $activeLetter = $alphabet->contains($activeLetter) ? $activeLetter : 'ALL';
        $normalizedSearch = $this->normalizeGlossarySearch($search);

        $terms = GlossaryTerm::query()
            ->where('is_active', true)
            ->where('collection_code', $this->glossaryCollectionCode($page))
            ->when(
                FrontendLocalePolicy::requiresExactTranslation($locale),
                fn (Builder $query) => $query->whereHas('translations', fn (Builder $translationQuery) => $translationQuery
                    ->where('locale', $locale))
            )
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (GlossaryTerm $term) use ($locale, $fallbackLocale, $normalizedSearch, $activeLetter): ?array {
                $translation = $this->pickGlossaryTranslation($term, $locale, $fallbackLocale);
                if (! $translation) {
                    return null;
                }

                $payload = is_array($translation->payload) ? $translation->payload : [];
                $title = trim((string) $translation->title);
                $excerpt = trim((string) ($translation->excerpt ?? '')) ?: $this->excerptFromGlossaryBody((string) ($translation->body_html ?? ''));
                $bodyHtml = $this->formatGlossaryBodyHtml((string) ($translation->body_html ?? ''));
                $synonyms = $this->cleanGlossaryMetaList((array) ($payload['synonyms'] ?? []));
                $variations = $this->cleanGlossaryMetaList((array) ($payload['variations'] ?? []));
                $categories = $this->cleanGlossaryMetaList((array) ($payload['categories'] ?? []));
                $tags = $this->cleanGlossaryMetaList((array) ($payload['tags'] ?? []));
                $abbreviation = trim((string) ($payload['abbreviation'] ?? ''));
                $letterKey = $this->glossaryLetterKey($title);
                $searchText = $this->normalizeGlossarySearch(implode(' ', array_filter([
                    $title,
                    $excerpt,
                    strip_tags($bodyHtml),
                    $abbreviation,
                    implode(' ', $synonyms),
                    implode(' ', $variations),
                    implode(' ', $categories),
                    implode(' ', $tags),
                ])));
                $initialVisible = ($activeLetter === 'ALL' || $letterKey === $activeLetter)
                    && ($normalizedSearch === '' || str_contains($searchText, $normalizedSearch));

                return [
                    'id' => (int) $term->id,
                    'title' => $title,
                    'slug' => (string) $translation->slug,
                    'url' => route('glossary.show', ['slug' => $translation->slug]),
                    'excerpt' => $excerpt,
                    'letter_key' => $letterKey,
                    'search_text' => $searchText,
                    'abbreviation' => $abbreviation,
                    'categories' => $categories,
                    'initial_visible' => $initialVisible,
                ];
            })
            ->filter()
            ->values();

        $groupedTerms = $terms->groupBy('letter_key');
        $visibleGroups = $groupedTerms
            ->mapWithKeys(fn (Collection $items, string $letter): array => [$letter => $items->contains('initial_visible', true)])
            ->all();

        return [
            'groupedGlossaryTerms' => $groupedTerms,
            'glossaryTerms' => $terms,
            'glossaryAlphabet' => $alphabet->all(),
            'glossaryAvailableLetters' => $terms->pluck('letter_key')->unique()->values()->all(),
            'glossarySearch' => $search,
            'glossaryActiveLetter' => $activeLetter,
            'glossaryInitialVisibleCount' => $terms->where('initial_visible', true)->count(),
            'glossaryVisibleGroups' => $visibleGroups,
        ];
    }

    private function resolveGlossaryTerm(string $slug, string $collectionCode, string $locale, string $fallbackLocale): GlossaryTerm
    {
        $term = GlossaryTerm::query()
            ->where('is_active', true)
            ->where('collection_code', $collectionCode)
            ->whereHas('translations', fn ($query) => $query
                ->where('slug', $slug)
                ->when(
                    FrontendLocalePolicy::requiresExactTranslation($locale),
                    fn ($translationQuery) => $translationQuery->where('locale', $locale)
                ))
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
            ])
            ->get()
            ->sortBy(fn (GlossaryTerm $candidate): int => $this->glossaryTermMatchScore($candidate, $slug, $locale, $fallbackLocale))
            ->first();

        abort_if(! $term, 404);

        return $term;
    }

    private function glossaryTermMatchScore(GlossaryTerm $term, string $slug, string $locale, string $fallbackLocale): int
    {
        $translation = $this->pickGlossaryTranslationForSlug($term, $slug, $locale, $fallbackLocale);
        if (! $translation) {
            return 99;
        }

        $translationLocale = (string) ($translation->locale ?? '');
        $translationSlug = (string) ($translation->slug ?? '');

        return match (true) {
            $translationLocale === $locale && $translationSlug === $slug => 0,
            $translationLocale === $fallbackLocale && $translationSlug === $slug => 1,
            $translationSlug === $slug => 2,
            $translationLocale === $locale => 3,
            $translationLocale === $fallbackLocale => 4,
            default => 5,
        };
    }

    private function pickGlossaryTranslationForSlug(GlossaryTerm $term, string $slug, string $locale, string $fallbackLocale): mixed
    {
        return $term->translations
            ->sortBy(function ($translation) use ($slug, $locale, $fallbackLocale): int {
                $translationLocale = (string) ($translation->locale ?? '');
                $translationSlug = (string) ($translation->slug ?? '');

                return match (true) {
                    $translationLocale === $locale && $translationSlug === $slug => 0,
                    $translationLocale === $fallbackLocale && $translationSlug === $slug => 1,
                    $translationSlug === $slug => 2,
                    $translationLocale === $locale => 3,
                    $translationLocale === $fallbackLocale => 4,
                    default => 5,
                };
            })
            ->first();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function relatedGlossaryTerms(
        string $collectionCode,
        int $currentTermId,
        string $letterKey,
        string $locale,
        string $fallbackLocale
    ): array {
        return GlossaryTerm::query()
            ->where('is_active', true)
            ->where('collection_code', $collectionCode)
            ->whereKeyNot($currentTermId)
            ->when(
                FrontendLocalePolicy::requiresExactTranslation($locale),
                fn (Builder $query) => $query->whereHas('translations', fn (Builder $translationQuery) => $translationQuery
                    ->where('locale', $locale))
            )
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (GlossaryTerm $term) use ($locale, $fallbackLocale): ?array {
                $translation = $this->pickGlossaryTranslation($term, $locale, $fallbackLocale);
                if (! $translation) {
                    return null;
                }

                $title = trim((string) ($translation->title ?? ''));
                if ($title === '') {
                    return null;
                }

                return [
                    'title' => $title,
                    'slug' => (string) ($translation->slug ?? ''),
                    'excerpt' => trim((string) ($translation->excerpt ?? '')) ?: $this->excerptFromGlossaryBody((string) ($translation->body_html ?? '')),
                    'letter_key' => $this->glossaryLetterKey($title),
                ];
            })
            ->filter()
            ->sortBy(function (array $term) use ($letterKey): array {
                return [
                    $term['letter_key'] === $letterKey ? 0 : 1,
                    $term['title'],
                ];
            })
            ->take(6)
            ->map(function (array $term): array {
                $term['url'] = route('glossary.show', ['slug' => $term['slug']]);

                return $term;
            })
            ->values()
            ->all();
    }

    private function pickGlossaryTranslation(GlossaryTerm $term, string $locale, string $fallbackLocale): mixed
    {
        return $term->translations->firstWhere('locale', $locale)
            ?? $term->translations->firstWhere('locale', $fallbackLocale)
            ?? $term->translations->first();
    }

    private function glossaryCollectionCode(InfoPage $page): string
    {
        return trim((string) data_get($page->payload, 'glossary_collection', GlossaryImportService::DEFAULT_COLLECTION))
            ?: GlossaryImportService::DEFAULT_COLLECTION;
    }

    /**
     * @param  array<int, mixed>  $items
     * @return array<int, string>
     */
    private function cleanGlossaryMetaList(array $items): array
    {
        return collect($items)
            ->map(fn ($item): string => trim((string) $item))
            ->filter(fn (string $item): bool => $item !== '')
            ->values()
            ->all();
    }

    private function glossaryLetterKey(string $value): string
    {
        $normalized = (string) Str::of($value)
            ->ascii()
            ->upper()
            ->replaceMatches('/[^A-Z0-9]+/', '');

        if ($normalized === '') {
            return '#';
        }

        $first = substr($normalized, 0, 1);

        return ctype_digit($first) ? '0-9' : $first;
    }

    private function normalizeGlossarySearch(string $value): string
    {
        return (string) Str::of($value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish();
    }

    private function excerptFromGlossaryBody(string $bodyHtml): string
    {
        $text = $this->normalizeGlossaryText($bodyHtml);
        if ($text === '') {
            return '';
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

    /**
     * @return array{lead: string, body_html: string}
     */
    private function prepareGlossaryDetailContent(string $excerpt, string $bodyHtml): array
    {
        $formattedBodyHtml = $this->formatGlossaryBodyHtml($bodyHtml);
        $trimmedExcerpt = trim($excerpt);

        if ($formattedBodyHtml === '') {
            return [
                'lead' => $trimmedExcerpt,
                'body_html' => '',
            ];
        }

        $normalizedExcerpt = $this->normalizeGlossaryText($trimmedExcerpt);
        $normalizedBody = $this->normalizeGlossaryText($formattedBodyHtml);

        if ($normalizedExcerpt !== '' && ! str_starts_with($normalizedBody, $normalizedExcerpt)) {
            return [
                'lead' => $trimmedExcerpt,
                'body_html' => $formattedBodyHtml,
            ];
        }

        return $this->splitGlossaryLeadFromBody($formattedBodyHtml, $trimmedExcerpt);
    }

    /**
     * @return array{lead: string, body_html: string}
     */
    private function splitGlossaryLeadFromBody(string $formattedBodyHtml, string $fallbackLead = ''): array
    {
        $trimmedBodyHtml = trim($formattedBodyHtml);
        if ($trimmedBodyHtml === '') {
            return [
                'lead' => '',
                'body_html' => '',
            ];
        }

        if (preg_match('/^\s*<p>(.*?)<\/p>(.*)$/is', $trimmedBodyHtml, $matches) !== 1) {
            $lead = $this->firstGlossarySentence($this->normalizeGlossaryText($fallbackLead))
                ?: $this->firstGlossarySentence($this->normalizeGlossaryText($trimmedBodyHtml));

            return [
                'lead' => $lead,
                'body_html' => $trimmedBodyHtml,
            ];
        }

        $firstParagraphText = $this->normalizeGlossaryText((string) ($matches[1] ?? ''));
        $lead = $this->firstGlossarySentence($firstParagraphText)
            ?: $this->firstGlossarySentence($this->normalizeGlossaryText($fallbackLead));

        if ($lead === '') {
            return [
                'lead' => '',
                'body_html' => $trimmedBodyHtml,
            ];
        }

        $remainingParagraphText = trim((string) preg_replace(
            '/\s+/u',
            ' ',
            mb_substr($firstParagraphText, mb_strlen($lead))
        ));

        $remainingBodyHtml = ltrim((string) ($matches[2] ?? ''));
        if ($remainingParagraphText !== '') {
            $remainingBodyHtml = $this->formatGlossaryBodyHtml($remainingParagraphText).$remainingBodyHtml;
        }

        return [
            'lead' => $lead,
            'body_html' => trim($remainingBodyHtml),
        ];
    }

    private function firstGlossarySentence(string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            return '';
        }

        if (preg_match('/^(.+?[.!?]+)(?=\s|$)/u', $text, $matches) === 1) {
            return trim((string) ($matches[1] ?? ''));
        }

        return $text;
    }

    private function normalizeGlossaryText(string $value): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', strip_tags($value)));
    }

    private function formatGlossaryBodyHtml(string $bodyHtml): string
    {
        $bodyHtml = trim($bodyHtml);
        if ($bodyHtml === '') {
            return '';
        }

        if (preg_match('/<(?:p|div|ul|ol|li|table|blockquote|h[1-6]|br)\b/i', $bodyHtml) === 1) {
            return $bodyHtml;
        }

        $normalized = preg_replace("/\r\n?/", "\n", $bodyHtml) ?? $bodyHtml;

        return collect(preg_split('/\n{2,}/u', $normalized) ?: [$normalized])
            ->map(fn ($paragraph): string => trim((string) $paragraph))
            ->filter(fn (string $paragraph): bool => $paragraph !== '')
            ->map(function (string $paragraph): string {
                $encoded = e($paragraph);
                $encoded = preg_replace('/&lt;(\/?(?:strong|em|b|i|u|sup|sub|span)\b[^&]*?)&gt;/i', '<$1>', $encoded) ?? $encoded;
                $encoded = nl2br($encoded, false);

                return '<p>'.$encoded.'</p>';
            })
            ->implode('');
    }
}
