<?php

namespace App\Services\Front;

use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Blog\BlogPostTranslation;
use App\Models\Content\Glossary\GlossaryTerm;
use App\Models\Content\Glossary\GlossaryTermTranslation;
use App\Models\Content\Page\InfoPage;
use App\Services\Content\GlossaryImportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SiteSearchService
{
    /**
     * @return array{services: Collection<int, array<string, mixed>>, glossary: Collection<int, array<string, mixed>>, blog: Collection<int, array<string, mixed>>}
     */
    public function search(string $query, string $locale, string $fallbackLocale, ?int $limitPerSection = null): array
    {
        $query = trim($query);
        $normalizedQuery = $this->normalize($query);

        if ($normalizedQuery === '') {
            return [
                'services' => collect(),
                'glossary' => collect(),
                'blog' => collect(),
            ];
        }

        return [
            'services' => $this->searchServices($normalizedQuery, $locale, $limitPerSection),
            'glossary' => $this->searchGlossary($normalizedQuery, $locale, $fallbackLocale, $limitPerSection),
            'blog' => $this->searchBlog($normalizedQuery, $locale, $fallbackLocale, $limitPerSection),
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function searchServices(string $normalizedQuery, string $locale, ?int $limit = null): Collection
    {
        $isCroatian = Str::startsWith(Str::lower($locale), 'hr');
        $catalog = collect([
            [
                'title' => $isCroatian ? 'Financije' : 'Finance',
                'eyebrow' => $isCroatian ? 'Kapital' : 'Capital',
                'excerpt' => $isCroatian
                    ? 'Kapital, poslovno savjetovanje, kontroling i podrška pri financijskim odlukama.'
                    : 'Capital planning, business advisory, controlling, and support for key financial decisions.',
                'search' => $isCroatian
                    ? 'financije finacije kapital poslovno savjetovanje kontroling financijske odluke'
                    : 'finance capital business advisory controlling financial decisions',
                'url' => route('finance.show'),
            ],
            [
                'title' => $isCroatian ? 'Računovodstvo' : 'Accounting',
                'eyebrow' => $isCroatian ? 'Preciznost' : 'Precision',
                'excerpt' => $isCroatian
                    ? 'Pouzdano vođenje poslovnih knjiga i jasni izvještaji za svakodnevno upravljanje.'
                    : 'Reliable bookkeeping and clear reporting for day-to-day management.',
                'search' => $isCroatian
                    ? 'računovodstvo racunovodstvo poslovne knjige izvještaji izvjestaji upravljanje'
                    : 'accounting bookkeeping reporting management',
                'url' => route('accounting.show'),
            ],
            [
                'title' => $isCroatian ? 'Revizija' : 'Audit',
                'eyebrow' => $isCroatian ? 'Povjerenje' : 'Trust',
                'excerpt' => $isCroatian
                    ? 'Neovisna stručna mišljenja i procjena poslovnih procesa, rizika i kontrola.'
                    : 'Independent expert opinions and assessments of business processes, risks, and controls.',
                'search' => $isCroatian
                    ? 'revizija neovisna mišljenja misljenja procesi rizici kontrole'
                    : 'audit independent opinions processes risks controls',
                'url' => route('audit.show'),
            ],
            [
                'title' => $isCroatian ? 'Porezi' : 'Tax',
                'eyebrow' => $isCroatian ? 'Usklađenost' : 'Compliance',
                'excerpt' => $isCroatian
                    ? 'Porezno planiranje, usklađenost i podrška u složenim poreznim pitanjima.'
                    : 'Tax planning, compliance, and support in complex tax matters.',
                'search' => $isCroatian
                    ? 'porezi porezno planiranje usklađenost porezna pitanja'
                    : 'tax tax planning compliance tax matters',
                'url' => route('tax.show'),
            ],
            [
                'title' => $isCroatian ? 'EU fondovi' : 'EU Funds',
                'eyebrow' => $isCroatian ? 'Natječaji' : 'Funding',
                'excerpt' => $isCroatian
                    ? 'Priprema projektnih prijedloga i usklađivanje ulaganja s aktualnim natječajima.'
                    : 'Project proposal preparation and aligning investments with active funding calls.',
                'search' => $isCroatian
                    ? 'eu fondovi natječaji natjecaji projekti ulaganja prijedlozi'
                    : 'eu funds funding calls projects investments proposals',
                'url' => route('eu-funds.show'),
            ],
            [
                'title' => $isCroatian ? 'Savjetovanje' : 'Advisory',
                'eyebrow' => $isCroatian ? 'Advisory' : 'Advisory',
                'excerpt' => $isCroatian
                    ? 'Financijsko i porezno savjetovanje te pribavljanje kapitala - sve na jednom mjestu.'
                    : 'Financial and tax advisory, capital raising, and transaction support in one place.',
                'search' => $isCroatian
                    ? 'savjetovanje advisory poslovno financijsko porezno porezi eu fondovi pribavljanje financiranja due diligence procjene vrijednosti m&a spajanja preuzimanja'
                    : 'advisory business financial tax eu funds financing due diligence valuations m&a mergers acquisitions',
                'url' => route('advisory.show'),
            ],
        ]);

        $results = $catalog
            ->filter(fn (array $item): bool => str_contains($this->normalize(implode(' ', [
                $item['title'],
                $item['eyebrow'],
                $item['excerpt'],
                $item['search'],
            ])), $normalizedQuery))
            ->values();

        return $limit ? $results->take($limit)->values() : $results;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function searchGlossary(
        string $normalizedQuery,
        string $locale,
        string $fallbackLocale,
        ?int $limit = null,
    ): Collection {
        $collectionCode = $this->resolveGlossaryCollectionCode();

        if ($collectionCode === null) {
            return collect();
        }

        $query = GlossaryTerm::query()
            ->where('is_active', true)
            ->where('collection_code', $collectionCode)
            ->whereHas('translations', function (Builder $query) use ($locale, $fallbackLocale, $normalizedQuery): void {
                $query->whereIn('locale', [$locale, $fallbackLocale]);
                $this->whereNormalizedContains($query, [
                    'title',
                    'slug',
                    'excerpt',
                    'body_html',
                    'payload',
                ], $normalizedQuery);
            })
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
            ])
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get()
            ->map(function (GlossaryTerm $term) use ($locale, $fallbackLocale): ?array {
                $translation = $this->pickGlossaryTranslation($term, $locale, $fallbackLocale);

                if (! $translation) {
                    return null;
                }

                $payload = is_array($translation->payload) ? $translation->payload : [];
                $title = trim((string) ($translation->title ?? ''));
                $bodyText = $this->plainText((string) ($translation->body_html ?? ''));
                $excerpt = trim((string) ($translation->excerpt ?? ''));
                $excerpt = $excerpt !== '' ? $excerpt : Str::limit($bodyText, 180, '...');
                $abbreviation = trim((string) ($payload['abbreviation'] ?? ''));
                $synonyms = collect((array) ($payload['synonyms'] ?? []))
                    ->map(fn (mixed $value): string => trim((string) $value))
                    ->filter()
                    ->implode(' ');

                return [
                    'title' => $title !== '' ? $title : (string) $term->code,
                    'eyebrow' => $abbreviation !== '' ? Str::upper($abbreviation) : __('ui.search.badges.glossary'),
                    'excerpt' => $excerpt,
                    'url' => route('glossary.show', ['slug' => (string) $translation->slug]),
                    'image_url' => null,
                    'meta' => null,
                    'search_text' => $this->normalize(implode(' ', [
                        $title,
                        $excerpt,
                        $bodyText,
                        $abbreviation,
                        $synonyms,
                    ])),
                    'sort_title' => $this->normalize($title),
                ];
            })
            ->filter(fn (?array $item): bool => is_array($item) && str_contains((string) $item['search_text'], $normalizedQuery))
            ->sortBy('sort_title')
            ->values()
            ->map(function (array $item): array {
                unset($item['search_text'], $item['sort_title']);

                return $item;
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function searchBlog(
        string $normalizedQuery,
        string $locale,
        string $fallbackLocale,
        ?int $limit = null,
    ): Collection {
        $query = BlogPost::query()
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->where(function (Builder $query) use ($locale, $fallbackLocale, $normalizedQuery): void {
                $query
                    ->whereHas('translations', function (Builder $translationQuery) use ($locale, $fallbackLocale, $normalizedQuery): void {
                        $translationQuery->whereIn('locale', [$locale, $fallbackLocale]);
                        $this->whereNormalizedContains($translationQuery, [
                            'title',
                            'slug',
                            'excerpt',
                            'body_html',
                        ], $normalizedQuery);
                    })
                    ->orWhereHas('categories', function (Builder $categoryQuery) use ($locale, $fallbackLocale, $normalizedQuery): void {
                        $categoryQuery
                            ->where('scope', Category::SCOPE_BLOG)
                            ->whereHas('translations', function (Builder $translationQuery) use ($locale, $fallbackLocale, $normalizedQuery): void {
                                $translationQuery
                                    ->where('scope', Category::SCOPE_BLOG)
                                    ->whereIn('locale', [$locale, $fallbackLocale]);
                                $this->whereNormalizedContains($translationQuery, ['name', 'slug'], $normalizedQuery);
                            });
                    });
            })
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
                'categories' => fn ($query) => $query
                    ->where('scope', Category::SCOPE_BLOG)
                    ->with([
                        'translations' => fn ($translationQuery) => $translationQuery
                            ->where('scope', Category::SCOPE_BLOG)
                            ->whereIn('locale', [$locale, $fallbackLocale]),
                    ]),
            ])
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get()
            ->map(function (BlogPost $post) use ($locale, $fallbackLocale): ?array {
                $translation = $this->pickBlogTranslation($post, $locale, $fallbackLocale);

                if (! $translation) {
                    return null;
                }

                $title = trim((string) ($translation->title ?? ''));
                $excerpt = trim((string) ($translation->excerpt ?? ''));
                $bodyText = $this->plainText((string) ($translation->body_html ?? ''));
                $excerpt = $excerpt !== '' ? $excerpt : Str::limit($bodyText, 180, '...');
                $category = $post->categories
                    ->sortByDesc(fn (Category $blogCategory): int => (int) ($blogCategory->pivot->is_primary ?? false))
                    ->first();
                $categoryTranslation = $category?->translations->firstWhere('locale', $locale)
                    ?? $category?->translations->firstWhere('locale', $fallbackLocale)
                    ?? $category?->translations->first();
                $categoryLabel = trim((string) ($categoryTranslation?->name ?? __('ui.blog.default_category')));
                $dateFormat = Str::startsWith(Str::lower($locale), 'hr') ? 'j. F Y.' : 'F j, Y';

                return [
                    'title' => $title !== '' ? $title : (string) $post->code,
                    'eyebrow' => $categoryLabel,
                    'excerpt' => $excerpt,
                    'url' => route('blog.show', ['slug' => (string) $translation->slug]),
                    'image_url' => null,
                    'meta' => ($post->published_at ?? $post->created_at)?->translatedFormat($dateFormat),
                    'search_text' => $this->normalize(implode(' ', [
                        $title,
                        (string) ($translation->slug ?? ''),
                        $excerpt,
                        $bodyText,
                        $categoryLabel,
                    ])),
                ];
            })
            ->filter(fn (?array $item): bool => is_array($item) && str_contains((string) $item['search_text'], $normalizedQuery))
            ->values()
            ->map(function (array $item): array {
                unset($item['search_text']);

                return $item;
            });
    }

    private function resolveGlossaryCollectionCode(): ?string
    {
        $page = InfoPage::query()
            ->where('layout', 'finance_glossary')
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if (! $page) {
            return null;
        }

        $pagePayload = is_array($page->payload ?? null) ? $page->payload : [];

        return trim((string) (($pagePayload['glossary_collection'] ?? null) ?: GlossaryImportService::DEFAULT_COLLECTION));
    }

    private function pickGlossaryTranslation(GlossaryTerm $term, string $locale, string $fallbackLocale): ?GlossaryTermTranslation
    {
        return $term->translations->firstWhere('locale', $locale)
            ?? $term->translations->firstWhere('locale', $fallbackLocale)
            ?? $term->translations->first();
    }

    private function pickBlogTranslation(BlogPost $post, string $locale, string $fallbackLocale): ?BlogPostTranslation
    {
        return $post->translations->firstWhere('locale', $locale)
            ?? $post->translations->firstWhere('locale', $fallbackLocale)
            ?? $post->translations->first();
    }

    private function plainText(string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', strip_tags(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'))) ?? '');
    }

    private function normalize(string $value): string
    {
        $value = $this->plainText($value);

        return Str::lower(Str::squish(Str::transliterate($value)));
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function whereNormalizedContains(Builder $query, array $columns, string $normalizedQuery): void
    {
        $query->where(function (Builder $query) use ($columns, $normalizedQuery): void {
            foreach ($columns as $index => $column) {
                $expression = sprintf('COALESCE(%s, \'\')', $query->qualifyColumn($column));

                foreach ([
                    'Č' => 'c',
                    'Ć' => 'c',
                    'Ž' => 'z',
                    'Š' => 's',
                    'Đ' => 'd',
                    'č' => 'c',
                    'ć' => 'c',
                    'ž' => 'z',
                    'š' => 's',
                    'đ' => 'd',
                ] as $source => $replacement) {
                    $expression = sprintf("REPLACE(%s, '%s', '%s')", $expression, $source, $replacement);
                }

                $method = $index === 0 ? 'whereRaw' : 'orWhereRaw';
                $query->{$method}('LOWER('.$expression.') LIKE ?', ['%'.$normalizedQuery.'%']);
            }
        });
    }
}
