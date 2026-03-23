<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Catalog\Category\Category;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Glossary\GlossaryTerm;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Resource\ResourceDocument;
use App\Models\Content\Support\Comment;
use App\Services\Content\ContentBlockResolver;
use App\Services\Content\GlossaryImportService;
use App\Support\Content\ResourceDocumentGroupRegistry;
use App\Support\Content\YouTubeUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

        if ($page->layout === 'academy') {
            [$academyBlogCategory, $academyBlogPosts] = $this->resolveAcademyBlogFeed(
                $page,
                (string) $locale,
                $fallbackLocale
            );
            $academyResourceDocuments = $this->resolveAcademyResourceFeed(
                $page,
                (string) $locale,
                $fallbackLocale
            );

            $academyBlogSection = $this->resolveAcademyBlogSection(
                $selectedTranslation?->payload,
                $academyBlogCategory,
                (string) $locale,
                $fallbackLocale
            );
            $academyResourceSection = $this->resolveAcademyResourceSection(
                $selectedTranslation?->payload,
                (string) $locale
            );
            $academyVideos = $this->resolveAcademyVideoFeed($page);
            $academyVideoSection = $this->resolveAcademyVideoSection(
                $selectedTranslation?->payload,
                (string) $locale
            );
            $academyTestimonials = $this->resolveAcademyTestimonials(
                $page,
                (string) $locale,
                $fallbackLocale
            );
            $academyGalleryItems = $this->resolveAcademyGallery(
                $page,
                (string) $locale,
                $fallbackLocale
            );

            return view($this->frontendView($request, 'pages.academy'), [
                'page' => $page,
                'selectedTranslation' => $selectedTranslation,
                'topBlocks' => $topBlocks,
                'bottomBlocks' => $bottomBlocks,
                'academyBlogPosts' => $academyBlogPosts,
                'academyBlogSection' => $academyBlogSection,
                'academyResourceDocuments' => $academyResourceDocuments,
                'academyResourceSection' => $academyResourceSection,
                'academyVideos' => $academyVideos,
                'academyVideoSection' => $academyVideoSection,
                'academyTestimonials' => $academyTestimonials,
                'academyGalleryItems' => $academyGalleryItems,
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

    /**
     * @return array{0: Category|null, 1: Collection<int, BlogPost>}
     */
    private function resolveAcademyBlogFeed(InfoPage $page, string $locale, string $fallbackLocale): array
    {
        $pagePayload = is_array($page->payload) ? $page->payload : [];
        $blogSource = is_array($pagePayload['blog_source'] ?? null) ? $pagePayload['blog_source'] : [];
        $categoryId = (int) ($blogSource['category_id'] ?? 0);
        $limit = max(1, min(24, (int) ($blogSource['limit'] ?? 3)));

        if ($categoryId <= 0) {
            return [null, collect()];
        }

        $category = Category::query()
            ->where('scope', Category::SCOPE_BLOG)
            ->whereKey($categoryId)
            ->with([
                'translations' => fn ($query) => $query
                    ->where('scope', Category::SCOPE_BLOG)
                    ->whereIn('locale', [$locale, $fallbackLocale]),
            ])
            ->first();

        if (! $category) {
            return [null, collect()];
        }

        $posts = BlogPost::query()
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->whereHas('categories', function (Builder $query) use ($categoryId): void {
                $query->where('categories.id', $categoryId);
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
                'media',
            ])
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return [
            $category,
            $posts,
        ];
    }

    /**
     * @return array{title: string, intro: string}
     */
    private function resolveAcademyBlogSection(
        mixed $translationPayload,
        ?Category $category,
        string $locale,
        string $fallbackLocale
    ): array {
        $payload = is_array($translationPayload) ? $translationPayload : [];
        $section = is_array($payload['academy_blog_section'] ?? null) ? $payload['academy_blog_section'] : [];
        $title = trim((string) ($section['title'] ?? ''));
        $intro = trim((string) ($section['intro'] ?? ''));

        if ($title !== '') {
            return [
                'title' => $title,
                'intro' => $intro,
            ];
        }

        $categoryTranslation = $category?->translations->firstWhere('locale', $locale)
            ?? $category?->translations->firstWhere('locale', $fallbackLocale)
            ?? $category?->translations->first();
        $categoryName = trim((string) ($categoryTranslation?->name ?? ''));

        if ($categoryName === '') {
            return [
                'title' => '',
                'intro' => $intro,
            ];
        }

        return [
            'title' => $locale === 'hr'
                ? 'Najnovije objave iz kategorije '.$categoryName
                : 'Latest posts from category '.$categoryName,
            'intro' => $intro,
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function resolveAcademyResourceFeed(InfoPage $page, string $locale, string $fallbackLocale): Collection
    {
        $pagePayload = is_array($page->payload) ? $page->payload : [];
        $resourceSource = is_array($pagePayload['resource_source'] ?? null) ? $pagePayload['resource_source'] : [];
        $documentIds = collect((array) ($resourceSource['document_ids'] ?? []))
            ->map(fn ($value): int => (int) $value)
            ->filter()
            ->values();

        if ($documentIds->isEmpty()) {
            return collect();
        }

        $documentsById = ResourceDocument::query()
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->whereKey($documentIds->all())
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
            ])
            ->get()
            ->keyBy(fn (ResourceDocument $document): int => (int) $document->id);

        return $documentIds
            ->map(function (int $documentId) use ($documentsById, $locale, $fallbackLocale): ?array {
                $document = $documentsById->get($documentId);

                if (! $document) {
                    return null;
                }

                $translation = $document->translations->firstWhere('locale', $locale)
                    ?? $document->translations->firstWhere('locale', $fallbackLocale)
                    ?? $document->translations->first();

                if (! $translation) {
                    return null;
                }

                return [
                    'id' => (int) $document->id,
                    'code' => (string) $document->code,
                    'group_code' => (string) $document->group_code,
                    'group_label' => ResourceDocumentGroupRegistry::label((string) $document->group_code),
                    'title' => trim((string) $translation->title),
                    'slug' => trim((string) $translation->slug),
                    'excerpt' => trim((string) ($translation->excerpt ?? '')),
                    'cover_image_url' => trim((string) ($document->cover_image_url ?? '')) ?: null,
                    'download_url' => trim((string) ($document->download_url ?? '')) ?: null,
                    'download_available' => trim((string) ($document->download_url ?? '')) !== '',
                    'source_url' => trim((string) ($document->source_url ?? '')) ?: null,
                    'published_at' => $document->published_at,
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * @return array{title: string, intro: string}
     */
    private function resolveAcademyResourceSection(mixed $translationPayload, string $locale): array
    {
        $payload = is_array($translationPayload) ? $translationPayload : [];
        $section = is_array($payload['academy_resource_section'] ?? null) ? $payload['academy_resource_section'] : [];
        $title = trim((string) ($section['title'] ?? ''));
        $intro = trim((string) ($section['intro'] ?? ''));

        return [
            'title' => $title !== ''
                ? $title
                : ($locale === 'hr' ? 'Dokumenti za preuzimanje' : 'Download documents'),
            'intro' => $intro,
        ];
    }

    /**
     * @return Collection<int, array<string, string>>
     */
    private function resolveAcademyVideoFeed(InfoPage $page): Collection
    {
        $pagePayload = is_array($page->payload) ? $page->payload : [];
        $videoSource = is_array($pagePayload['video_source'] ?? null) ? $pagePayload['video_source'] : [];

        return collect((array) ($videoSource['items'] ?? []))
            ->map(function ($item): ?array {
                $title = trim((string) data_get($item, 'title', ''));
                $youtubeUrl = trim((string) data_get($item, 'youtube_url', ''));
                $parsedVideo = YouTubeUrl::parse($youtubeUrl);
                $embedUrl = (string) ($parsedVideo['embed_url'] ?? '');

                if ($embedUrl === '') {
                    return null;
                }

                $separator = str_contains($embedUrl, '?') ? '&' : '?';
                $videoId = trim((string) ($parsedVideo['video_id'] ?? ''));

                return [
                    'title' => $title,
                    'youtube_url' => $youtubeUrl,
                    'poster_url' => $videoId !== '' ? 'https://i.ytimg.com/vi/'.$videoId.'/hqdefault.jpg' : '',
                    'embed_url' => $embedUrl.$separator.'rel=0&modestbranding=1&playsinline=1&enablejsapi=1',
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * @return array{title: string, intro: string}
     */
    private function resolveAcademyVideoSection(mixed $translationPayload, string $locale): array
    {
        $payload = is_array($translationPayload) ? $translationPayload : [];
        $section = is_array($payload['academy_video_section'] ?? null) ? $payload['academy_video_section'] : [];
        $title = trim((string) ($section['title'] ?? ''));
        $intro = trim((string) ($section['intro'] ?? ''));

        return [
            'title' => $title !== ''
                ? $title
                : ($locale === 'hr' ? 'Online edukacija i personalizirani trening' : 'Online education and personalized training'),
            'intro' => $intro,
        ];
    }

    /**
     * @return Collection<int, Comment>
     */
    private function resolveAcademyTestimonials(InfoPage $page, string $locale, string $fallbackLocale): Collection
    {
        $buildQuery = static fn (string $targetLocale) => Comment::query()
            ->where('commentable_type', InfoPage::class)
            ->where('commentable_id', $page->getKey())
            ->where('status', Comment::STATUS_APPROVED)
            ->where('locale', $targetLocale)
            ->orderByDesc('is_featured')
            ->orderByDesc('reviewed_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $rows = $buildQuery($locale)->get();

        if ($rows->isEmpty() && $fallbackLocale !== $locale) {
            $rows = $buildQuery($fallbackLocale)->get();
        }

        return $rows;
    }

    /**
     * @return Collection<int, array{id:int,name:string,image_url:string,full_url:string,alt:string,caption:string}>
     */
    private function resolveAcademyGallery(InfoPage $page, string $locale, string $fallbackLocale): Collection
    {
        if (! method_exists($page, 'getMedia')) {
            return collect();
        }

        return $page->getMedia('academy_gallery')
            ->map(function ($media) use ($locale, $fallbackLocale): array {
                $custom = (array) ($media->custom_properties ?? []);
                $alt = trim((string) (
                    data_get($custom, "alt.$locale")
                    ?? data_get($custom, "alt.$fallbackLocale")
                    ?? $media->name
                ));
                $caption = trim((string) (
                    data_get($custom, "caption.$locale")
                    ?? data_get($custom, "caption.$fallbackLocale")
                    ?? ''
                ));
                $imageUrl = $media->hasGeneratedConversion('detail_960x960')
                    ? (string) $media->getUrl('detail_960x960')
                    : (string) $media->getUrl();
                $fullUrl = (string) $media->getUrl();

                return [
                    'id' => (int) $media->id,
                    'name' => (string) $media->name,
                    'image_url' => $imageUrl !== '' ? $imageUrl : $fullUrl,
                    'full_url' => $fullUrl,
                    'alt' => $alt !== '' ? $alt : (string) $media->name,
                    'caption' => $caption,
                ];
            })
            ->values();
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
