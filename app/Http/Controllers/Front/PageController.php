<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Catalog\Category\Category;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Models\Content\Glossary\GlossaryTerm;
use App\Models\Content\Page\InfoPage;
use App\Services\Content\ContentBlockResolver;
use App\Services\Content\GlossaryImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageController extends Controller
{
    use ResolvesFrontendView;

    public function category(Request $request, string $slug): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.locale');
        $variant = $this->frontendVariant($request);

        $category = Category::query()
            ->where('scope', Category::SCOPE_PAGE)
            ->where('is_active', true)
            ->whereHas('translations', function ($q) use ($locale, $fallbackLocale, $slug): void {
                $q->where('scope', Category::SCOPE_PAGE)
                    ->whereIn('locale', [$locale, $fallbackLocale])
                    ->where('slug', $slug);
            })
            ->with(['translations' => fn ($q) => $q
                ->where('scope', Category::SCOPE_PAGE)
                ->whereIn('locale', [$locale, $fallbackLocale])])
            ->firstOrFail();

        $pages = InfoPage::query()
            ->where('is_active', true)
            ->where(function ($q): void {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->whereHas('categories', fn ($q) => $q->where('categories.id', $category->id))
            ->with(['translations' => fn ($q) => $q->whereIn('locale', [$locale, $fallbackLocale])])
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $topBlocks = app(ContentBlockResolver::class)->forPlacement(
            placement: 'page-category.top',
            locale: $locale,
            targetType: 'page-category',
            targetRef: $slug,
            frontendVariant: $variant
        );

        $bottomBlocks = app(ContentBlockResolver::class)->forPlacement(
            placement: 'page-category.bottom',
            locale: $locale,
            targetType: 'page-category',
            targetRef: $slug,
            frontendVariant: $variant
        );

        return view($this->frontendView($request, 'pages.category'), [
            'category' => $category,
            'pages' => $pages,
            'topBlocks' => $topBlocks,
            'bottomBlocks' => $bottomBlocks,
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
        ]);
    }

    private function pickTranslation(InfoPage $page, string $slug, string $locale, string $fallbackLocale)
    {
        return $page->translations
            ->sortBy(function ($translation) use ($slug, $locale, $fallbackLocale): int {
                $tLocale = (string) ($translation->locale ?? '');
                $tSlug = (string) ($translation->slug ?? '');

                return match (true) {
                    $tLocale === $locale && $tSlug === $slug => 0,
                    $tLocale === $fallbackLocale && $tSlug === $slug => 1,
                    $tSlug === $slug => 2,
                    $tLocale === $locale => 3,
                    $tLocale === $fallbackLocale => 4,
                    default => 5,
                };
            })
            ->first();
    }

    private function pageMatchScore(InfoPage $page, string $slug, string $locale, string $fallbackLocale): int
    {
        $translation = $this->pickTranslation($page, $slug, $locale, $fallbackLocale);
        if (!$translation) {
            return 99;
        }

        $tLocale = (string) ($translation->locale ?? '');
        $tSlug = (string) ($translation->slug ?? '');

        return match (true) {
            $tLocale === $locale && $tSlug === $slug => 0,
            $tLocale === $fallbackLocale && $tSlug === $slug => 1,
            $tSlug === $slug => 2,
            default => 10,
        };
    }

    public function show(Request $request, string $slug): View|RedirectResponse
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.locale');
        $variant = $this->frontendVariant($request);

        $pages = InfoPage::query()
            ->where('is_active', true)
            ->where(function ($q): void {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->whereHas('translations', function ($q) use ($slug): void {
                $q->where('slug', $slug);
            })
            ->with([
                'translations',
                'categories' => fn ($query) => $query
                    ->orderBy('content_info_page_category.sort_order')
                    ->with([
                        'translations' => fn ($translationQuery) => $translationQuery
                            ->where('scope', Category::SCOPE_PAGE)
                            ->whereIn('locale', [$locale, $fallbackLocale]),
                    ]),
            ])
            ->get();

        $page = $pages
            ->sortBy(fn (InfoPage $candidate): int => $this->pageMatchScore($candidate, $slug, (string) $locale, $fallbackLocale))
            ->first();

        abort_if(!$page, 404);

        $selectedTranslation = $this->pickTranslation($page, $slug, (string) $locale, $fallbackLocale);

        $topBlocks = app(ContentBlockResolver::class)->forPlacement(
            placement: 'page.top',
            locale: $locale,
            targetType: 'page',
            targetRef: $slug,
            frontendVariant: $variant
        );

        $bottomBlocks = app(ContentBlockResolver::class)->forPlacement(
            placement: 'page.bottom',
            locale: $locale,
            targetType: 'page',
            targetRef: $slug,
            frontendVariant: $variant
        );

        if ($page->layout === 'finance_glossary') {
            $targetUrl = route('glossary.index');
            $queryString = $request->getQueryString();

            if ($queryString) {
                $targetUrl .= '?'.$queryString;
            }

            return redirect()->to($targetUrl, 301);
        }

        if ($page->layout === 'career') {
            return view($this->frontendView($request, 'pages.career'), [
                'page' => $page,
                'selectedTranslation' => $selectedTranslation,
                'topBlocks' => $topBlocks,
                'bottomBlocks' => $bottomBlocks,
                'locale' => $locale,
                'fallbackLocale' => $fallbackLocale,
            ]);
        }

        return view($this->frontendView($request, 'pages.show'), [
            'page' => $page,
            'selectedTranslation' => $selectedTranslation,
            'topBlocks' => $topBlocks,
            'bottomBlocks' => $bottomBlocks,
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
        ]);
    }

    private function renderGlossaryPage(
        Request $request,
        InfoPage $page,
        mixed $selectedTranslation,
        mixed $topBlocks,
        mixed $bottomBlocks,
        string $locale,
        string $fallbackLocale
    ): View {
        $collectionCode = trim((string) data_get($page->payload, 'glossary_collection', GlossaryImportService::DEFAULT_COLLECTION))
            ?: GlossaryImportService::DEFAULT_COLLECTION;
        $alphabet = collect(['ALL', '0-9', ...range('A', 'Z')]);
        $search = trim((string) $request->query('q', ''));
        $activeLetter = strtoupper(trim((string) $request->query('letter', 'ALL')));
        $activeLetter = $alphabet->contains($activeLetter) ? $activeLetter : 'ALL';
        $normalizedSearch = $this->normalizeGlossarySearch($search);

        $terms = GlossaryTerm::query()
            ->where('is_active', true)
            ->where('collection_code', $collectionCode)
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
                $bodyHtml = trim((string) ($translation->body_html ?? ''));
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
                    'excerpt' => $excerpt,
                    'body_html' => $bodyHtml,
                    'letter_key' => $letterKey,
                    'search_text' => $searchText,
                    'abbreviation' => $abbreviation,
                    'synonyms' => $synonyms,
                    'variations' => $variations,
                    'categories' => $categories,
                    'tags' => $tags,
                    'initial_visible' => $initialVisible,
                ];
            })
            ->filter()
            ->values();

        $groupedTerms = $terms->groupBy('letter_key');
        $visibleGroups = $groupedTerms
            ->mapWithKeys(fn ($items, $letter): array => [$letter => $items->contains('initial_visible', true)])
            ->all();

        return view($this->frontendView($request, 'pages.glossary'), [
            'page' => $page,
            'selectedTranslation' => $selectedTranslation,
            'topBlocks' => $topBlocks,
            'bottomBlocks' => $bottomBlocks,
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
            'groupedGlossaryTerms' => $groupedTerms,
            'glossaryAlphabet' => $alphabet->all(),
            'glossaryAvailableLetters' => $terms->pluck('letter_key')->unique()->values()->all(),
            'glossarySearch' => $search,
            'glossaryActiveLetter' => $activeLetter,
            'glossaryInitialVisibleCount' => $terms->where('initial_visible', true)->count(),
            'glossaryVisibleGroups' => $visibleGroups,
        ]);
    }

    private function pickGlossaryTranslation(GlossaryTerm $term, string $locale, string $fallbackLocale): mixed
    {
        return $term->translations->firstWhere('locale', $locale)
            ?? $term->translations->firstWhere('locale', $fallbackLocale)
            ?? $term->translations->first();
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
        $text = trim((string) preg_replace('/\s+/u', ' ', strip_tags($bodyHtml)));
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
}
