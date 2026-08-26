<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Content\Blog\BlogPostTranslation;
use App\Models\Content\Call\CallPostTranslation;
use App\Models\Content\Glossary\GlossaryTermTranslation;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Page\InfoPageTranslation;
use App\Models\Content\Resource\ResourceDocumentTranslation;
use App\Models\Content\Service\ServicePage;
use App\Models\Settings\Local\Language;
use App\Services\Content\ContentBlockResolver;
use App\Services\Content\GlossaryImportService;
use App\Support\Content\ServicePageTemplateRegistry;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use XMLWriter;

class SitemapController extends Controller
{
    /**
     * @var array<int, string>
     */
    private const SHARED_ROUTE_NAMES = [
        'home',
        'blog.index',
        'resources.index',
        'faq.index',
        'assessment.create',
        'lease-calculator.show',
    ];

    /**
     * @var array<string, array<int, string>>
     */
    private const LOCALE_ROUTE_NAMES = [
        'hr' => [
            'eu-funds.questionnaire.create',
        ],
        'en' => [
            'eu-funds.questionnaire.create.en',
        ],
    ];

    /**
     * @var array<string, array<string, string>>
     */
    private const SERVICE_ROUTE_NAMES = [
        ServicePageTemplateRegistry::SERVICES_INDEX => [
            'hr' => 'services.index',
            'en' => 'services.index.en',
        ],
        ServicePageTemplateRegistry::AUDIT => [
            'hr' => 'audit.show',
            'en' => 'audit.show.en',
        ],
        ServicePageTemplateRegistry::ACCOUNTING => [
            'hr' => 'accounting.show',
            'en' => 'accounting.show.en',
        ],
        ServicePageTemplateRegistry::ADVISORY => [
            'hr' => 'advisory.show',
            'en' => 'advisory.show.en',
        ],
        ServicePageTemplateRegistry::EU_FUNDS => [
            'hr' => 'eu-funds.show',
            'en' => 'eu-funds.show.en',
        ],
    ];

    /**
     * @var array<string, array<string, string>>
     */
    private const ADVISORY_ROUTE_NAMES = [
        'financial' => ['hr' => 'advisory.finance.show', 'en' => 'advisory.finance.show.en'],
        'ma' => ['hr' => 'advisory.ma.show', 'en' => 'advisory.ma.show.en'],
        'due_diligence' => ['hr' => 'advisory.due-diligence.show', 'en' => 'advisory.due-diligence.show.en'],
        'valuations' => ['hr' => 'advisory.valuations.show', 'en' => 'advisory.valuations.show.en'],
        'tax' => ['hr' => 'advisory.tax.show', 'en' => 'advisory.tax.show.en'],
        'funding' => ['hr' => 'advisory.funding.show', 'en' => 'advisory.funding.show.en'],
        'bank_loans' => ['hr' => 'advisory.bank-loans.show', 'en' => 'advisory.bank-loans.show.en'],
        'zopu' => ['hr' => 'advisory.investment-incentives.show', 'en' => 'advisory.investment-incentives.show.en'],
    ];

    /**
     * @var array<int, string>
     */
    private const NON_INDEXABLE_PAGE_SLUGS = [
        'financije',
        'porezi',
        'pretraga',
        'search',
    ];

    public function __invoke(): Response
    {
        $urls = collect();
        $defaultLocale = $this->defaultLocale();
        $activeLocales = $this->activeLocales($defaultLocale);

        $this->addRoutes($urls, self::SHARED_ROUTE_NAMES);

        foreach ($activeLocales as $locale) {
            if (isset(self::LOCALE_ROUTE_NAMES[$locale])) {
                $this->addRoutes($urls, self::LOCALE_ROUTE_NAMES[$locale]);
            }
        }

        $this->addContactRoutes($urls, $activeLocales, $defaultLocale);
        $this->addServiceRoutes($urls, $activeLocales);
        $this->addTeamRoutes($urls, $activeLocales);

        BlogPostTranslation::query()
            ->where('locale', $defaultLocale)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->whereHas('post', fn (Builder $query) => $query->published())
            ->with('post:id,published_at,updated_at')
            ->orderBy('id')
            ->get()
            ->each(function (BlogPostTranslation $translation) use ($urls): void {
                $this->putUrl(
                    $urls,
                    route('blog.show', ['slug' => $translation->slug]),
                    $this->lastModified($translation, $translation->post),
                );
            });

        InfoPageTranslation::query()
            ->whereIn('locale', $activeLocales)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->whereNotIn('slug', self::NON_INDEXABLE_PAGE_SLUGS)
            ->whereHas('page', function (Builder $query): void {
                $this->publishedParent($query);
                $query
                    ->where('layout', '!=', 'finance_glossary')
                    ->where('code', '!=', 'team-page');
            })
            ->with('page:id,code,layout,published_at,updated_at')
            ->orderBy('id')
            ->get()
            ->each(function (InfoPageTranslation $translation) use ($defaultLocale, $urls): void {
                if ($translation->locale !== $defaultLocale
                    && $translation->page->layout === 'academy'
                    && ! $this->hasContent(data_get($translation->payload, 'academy_programs'))) {
                    return;
                }

                $this->putUrl(
                    $urls,
                    route('pages.show', ['slug' => $translation->slug]),
                    $this->lastModified($translation, $translation->page),
                );
            });

        CallPostTranslation::query()
            ->whereIn('locale', array_values(array_intersect($activeLocales, ['hr', 'en'])))
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->whereHas('post', fn (Builder $query) => $this->publishedParent($query))
            ->with('post:id,published_at,updated_at')
            ->orderBy('id')
            ->get()
            ->each(function (CallPostTranslation $translation) use ($urls): void {
                $routeName = $translation->locale === 'en'
                    ? 'eu-funds.calls.show.en'
                    : 'eu-funds.calls.show';

                $this->putUrl(
                    $urls,
                    route($routeName, ['slug' => $translation->slug]),
                    $this->lastModified($translation, $translation->post),
                );
            });

        ResourceDocumentTranslation::query()
            ->where('locale', $defaultLocale)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->whereHas('document', fn (Builder $query) => $this->publishedParent($query))
            ->with('document:id,published_at,updated_at')
            ->orderBy('id')
            ->get()
            ->each(function (ResourceDocumentTranslation $translation) use ($urls): void {
                $this->putUrl(
                    $urls,
                    route('resources.show', ['slug' => $translation->slug]),
                    $this->lastModified($translation, $translation->document),
                );
            });

        $this->addGlossaryRoutes($urls, $defaultLocale);

        return response($this->toXml($urls->sortBy('loc')->values()), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * @param  Collection<string, array{loc: string, lastmod?: string}>  $urls
     * @param  array<int, string>  $routeNames
     */
    private function addRoutes(Collection $urls, array $routeNames): void
    {
        foreach ($routeNames as $routeName) {
            if (Route::has($routeName)) {
                $this->putUrl($urls, route($routeName));
            }
        }
    }

    /**
     * @param  Collection<string, array{loc: string, lastmod?: string}>  $urls
     * @param  array<int, string>  $activeLocales
     */
    private function addContactRoutes(Collection $urls, array $activeLocales, string $defaultLocale): void
    {
        $routeNames = [
            'hr' => 'contact.create',
            'en' => 'contact.create.en',
        ];

        foreach ($activeLocales as $locale) {
            $routeName = $routeNames[$locale] ?? null;
            if ($routeName === null || ! Route::has($routeName)) {
                continue;
            }

            if ($locale !== $defaultLocale && ! $this->hasExactContactContent($locale)) {
                continue;
            }

            $this->putUrl($urls, route($routeName));
        }
    }

    private function hasExactContactContent(string $locale): bool
    {
        $statsItem = app(ContentBlockResolver::class)
            ->forPlacement('home.stats', $locale, null, null, 'desktop')
            ->first(static fn (array $item): bool => (string) (($item['block'] ?? null)?->type ?? '') === 'home_stats');
        $translation = $statsItem['translation'] ?? null;

        if (strtolower(trim((string) ($translation?->locale ?? ''))) !== $locale) {
            return false;
        }

        return $this->hasContent(data_get($translation?->payload, 'contact_page'));
    }

    /**
     * @param  Collection<string, array{loc: string, lastmod?: string}>  $urls
     * @param  array<int, string>  $activeLocales
     */
    private function addServiceRoutes(Collection $urls, array $activeLocales): void
    {
        foreach (self::SERVICE_ROUTE_NAMES as $templateKey => $routeNames) {
            $servicePage = ServicePage::query()
                ->where('template_key', $templateKey)
                ->where('is_active', true)
                ->where(function (Builder $query): void {
                    $query
                        ->whereNull('published_at')
                        ->orWhere('published_at', '<=', now());
                })
                ->with(['translations' => fn ($query) => $query->whereIn('locale', $activeLocales)])
                ->orderByRaw('case when code = ? then 0 else 1 end', [ServicePageTemplateRegistry::defaultCode($templateKey)])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->first();

            if (! $servicePage instanceof ServicePage) {
                continue;
            }

            foreach ($activeLocales as $locale) {
                $routeName = $routeNames[$locale] ?? null;
                $translation = $servicePage->translations->firstWhere('locale', $locale);

                if ($routeName === null || ! Route::has($routeName) || ! $translation) {
                    continue;
                }

                $this->putUrl(
                    $urls,
                    route($routeName),
                    $this->lastModified($translation, $servicePage),
                );

                if ($templateKey !== ServicePageTemplateRegistry::ADVISORY) {
                    continue;
                }

                foreach (self::ADVISORY_ROUTE_NAMES as $payloadKey => $advisoryRouteNames) {
                    $advisoryRouteName = $advisoryRouteNames[$locale] ?? null;
                    if ($advisoryRouteName === null
                        || ! Route::has($advisoryRouteName)
                        || ! $this->hasLocalizedSectionContent(data_get($translation->payload, $payloadKey))) {
                        continue;
                    }

                    $this->putUrl(
                        $urls,
                        route($advisoryRouteName),
                        $this->lastModified($translation, $servicePage),
                    );
                }
            }
        }
    }

    /**
     * @param  Collection<string, array{loc: string, lastmod?: string}>  $urls
     * @param  array<int, string>  $activeLocales
     */
    private function addTeamRoutes(Collection $urls, array $activeLocales): void
    {
        InfoPageTranslation::query()
            ->whereIn('locale', array_values(array_intersect($activeLocales, ['hr', 'en'])))
            ->whereHas('page', function (Builder $query): void {
                $this->publishedParent($query);
                $query->where('code', 'team-page');
            })
            ->with('page:id,published_at,updated_at')
            ->get()
            ->each(function (InfoPageTranslation $translation) use ($urls): void {
                $routeName = $translation->locale === 'en' ? 'team.index.en' : 'team.index';
                if (! Route::has($routeName)) {
                    return;
                }

                $this->putUrl(
                    $urls,
                    route($routeName),
                    $this->lastModified($translation, $translation->page),
                );
            });
    }

    /**
     * @param  Collection<string, array{loc: string, lastmod?: string}>  $urls
     */
    private function addGlossaryRoutes(Collection $urls, string $defaultLocale): void
    {
        $glossaryPage = InfoPage::query()
            ->where('layout', 'finance_glossary')
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->whereHas('translations', fn (Builder $query) => $query->where('locale', $defaultLocale))
            ->with(['translations' => fn ($query) => $query->where('locale', $defaultLocale)])
            ->orderByRaw('case when code = ? then 0 else 1 end', [GlossaryImportService::DEFAULT_PAGE_CODE])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if (! $glossaryPage instanceof InfoPage) {
            return;
        }

        $pageTranslation = $glossaryPage->translations->first();
        if ($pageTranslation) {
            $this->putUrl(
                $urls,
                route('glossary.index'),
                $this->lastModified($pageTranslation, $glossaryPage),
            );
        }

        $collectionCode = trim((string) data_get(
            $glossaryPage->payload,
            'glossary_collection',
            GlossaryImportService::DEFAULT_COLLECTION,
        )) ?: GlossaryImportService::DEFAULT_COLLECTION;

        GlossaryTermTranslation::query()
            ->where('locale', $defaultLocale)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->whereHas('term', fn (Builder $query) => $query
                ->where('is_active', true)
                ->where('collection_code', $collectionCode))
            ->with('term:id,updated_at')
            ->orderBy('id')
            ->get()
            ->each(function (GlossaryTermTranslation $translation) use ($urls): void {
                $this->putUrl(
                    $urls,
                    route('glossary.show', ['slug' => $translation->slug]),
                    $this->lastModified($translation, $translation->term),
                );
            });
    }

    private function hasContent(mixed $value): bool
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                if ($this->hasContent($item)) {
                    return true;
                }
            }

            return false;
        }

        return is_scalar($value) && trim(strip_tags((string) $value)) !== '';
    }

    private function hasLocalizedSectionContent(mixed $value): bool
    {
        if (! is_array($value) || $value === []) {
            return false;
        }

        foreach ($value as $key => $item) {
            $normalizedKey = strtolower(trim((string) $key));
            if (in_array($normalizedKey, ['pandea', 'meeting', 'blog_section'], true)
                || str_starts_with($normalizedKey, 'show_')
                || str_starts_with($normalizedKey, 'is_')
                || preg_match('/(^|_)(meta|seo|url|uri|slug|route|media|image|icon|alt|canonical|target)(_|$)/', $normalizedKey) === 1) {
                continue;
            }

            if (is_array($item) && $this->hasLocalizedSectionContent($item)) {
                return true;
            }

            if (! is_bool($item)
                && is_scalar($item)
                && trim(strip_tags((string) $item)) !== '') {
                return true;
            }
        }

        return false;
    }

    private function defaultLocale(): string
    {
        return strtolower(trim((string) Language::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->value('code')))
            ?: strtolower((string) config('app.fallback_locale', config('app.locale', 'hr')));
    }

    /**
     * @return array<int, string>
     */
    private function activeLocales(string $defaultLocale): array
    {
        $locales = Language::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->pluck('code')
            ->map(static fn (mixed $locale): string => strtolower(trim((string) $locale)))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return $locales !== [] ? $locales : [$defaultLocale];
    }

    private function publishedParent(Builder $query): void
    {
        $query
            ->where('is_active', true)
            ->where(function (Builder $publishedQuery): void {
                $publishedQuery
                    ->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * @param  Collection<string, array{loc: string, lastmod?: string}>  $urls
     */
    private function putUrl(Collection $urls, string $url, ?string $lastModified = null): void
    {
        $url = trim($url);
        if ($url === '') {
            return;
        }

        $entry = ['loc' => $url];
        if ($lastModified !== null) {
            $entry['lastmod'] = $lastModified;
        }

        $existing = $urls->get($url);
        if (is_array($existing) && ($existing['lastmod'] ?? '') > ($entry['lastmod'] ?? '')) {
            return;
        }

        $urls->put($url, $entry);
    }

    private function lastModified(Model ...$models): ?string
    {
        $lastModified = collect($models)
            ->flatMap(static fn (Model $model): array => [
                $model->getAttribute('updated_at'),
                $model->getAttribute('published_at'),
            ])
            ->filter(static fn (mixed $value): bool => $value instanceof DateTimeInterface)
            ->sortDesc()
            ->first();

        return $lastModified instanceof DateTimeInterface
            ? $lastModified->format(DateTimeInterface::ATOM)
            : null;
    }

    /**
     * @param  Collection<int, array{loc: string, lastmod?: string}>  $urls
     */
    private function toXml(Collection $urls): string
    {
        $xml = new XMLWriter;
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($urls as $url) {
            $xml->startElement('url');
            $xml->writeElement('loc', $url['loc']);

            if (isset($url['lastmod'])) {
                $xml->writeElement('lastmod', $url['lastmod']);
            }

            $xml->endElement();
        }

        $xml->endElement();
        $xml->endDocument();

        return $xml->outputMemory();
    }
}
