@extends('front.desktop.layouts.store')

@php
    use Illuminate\Support\Str;

    $translation = $post->translations->firstWhere('locale', $locale)
        ?? $post->translations->firstWhere('locale', $fallbackLocale);
    $mediaItems = $post->relationLoaded('media')
        ? $post->media
            ->sortBy(static fn ($mediaItem) => (int) ($mediaItem->order_column ?? 0))
            ->values()
        : collect();
    $coverImage = $mediaItems->firstWhere('collection_name', 'blog_cover') ?? $post->getFirstMedia('blog_cover');
    $coverImageUrl = $coverImage ? $coverImage->getUrl() : null;
    $galleryItems = $mediaItems->where('collection_name', 'blog_gallery')->values();
    if ($galleryItems->isEmpty()) {
        $galleryItems = $post->getMedia('blog_gallery')
            ->sortBy(static fn ($mediaItem) => (int) ($mediaItem->order_column ?? 0))
            ->values();
    }
    $bodyHtml = (string) ($translation?->body_html ?? '');
    $normalizeAssetUrl = static function (?string $url): string {
        $value = trim((string) $url);
        if ($value === '') {
            return '';
        }

        $path = parse_url($value, PHP_URL_PATH);

        return rawurldecode(is_string($path) && $path !== '' ? $path : $value);
    };
    $inlineImagePaths = collect();
    if ($bodyHtml !== '') {
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $bodyHtml, $bodyImageMatches);
        $inlineImagePaths = collect($bodyImageMatches[1] ?? [])
            ->map($normalizeAssetUrl)
            ->filter()
            ->values();
    }
    $galleryItems = $galleryItems
        ->reject(fn ($mediaItem) => $inlineImagePaths->contains($normalizeAssetUrl($mediaItem->getUrl())))
        ->values();
    $galleryCount = $galleryItems->count();
    $galleryColumnsClass = match (true) {
        $galleryCount <= 1 => 'grid-cols-1',
        $galleryCount === 2 => 'grid-cols-1 md:grid-cols-2',
        $galleryCount === 4 => 'grid-cols-1 md:grid-cols-2',
        default => 'grid-cols-1 md:grid-cols-3',
    };
    $postCategories = $post->categories
        ->sortByDesc(fn ($category) => (int) ($category->pivot->is_primary ?? false))
        ->values();
    $primaryCategory = $postCategories->first();
    $primaryCategoryTranslation = $primaryCategory?->translations->firstWhere('locale', $locale)
        ?? $primaryCategory?->translations->firstWhere('locale', $fallbackLocale)
        ?? $primaryCategory?->translations->first();
    $primaryCategoryName = trim((string) ($primaryCategoryTranslation?->name ?? $primaryCategory?->code ?? ''));
    $primaryCategorySlug = trim((string) ($primaryCategoryTranslation?->slug ?? ''));
    $primaryCategoryUrl = $primaryCategorySlug !== ''
        ? url('/blog/'.$primaryCategorySlug)
        : route('blog.index');
    $articleTitle = trim((string) ($translation?->title ?? $post->code));
    $breadcrumbCurrentTitle = Str::limit($articleTitle, 72, '...');
    $publishedLabel = ($post->published_at ?? $post->created_at)?->translatedFormat('j. F Y.');
    $shareUrl = urlencode(url()->current());
    $shareTitle = urlencode($articleTitle);
    $shareLinks = [
        [
            'key' => 'x',
            'label' => __('ui.blog.share.x'),
            'url' => 'https://twitter.com/intent/tweet?url=' . $shareUrl . '&text=' . $shareTitle,
        ],
        [
            'key' => 'facebook',
            'label' => __('ui.blog.share.facebook'),
            'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . $shareUrl,
        ],
        [
            'key' => 'linkedin',
            'label' => __('ui.blog.share.linkedin'),
            'url' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $shareUrl,
        ],
    ];
    $articleCta = [
        'title_lines' => [
            __('ui.blog.article_cta.title_line_1'),
            __('ui.blog.article_cta.title_line_2'),
        ],
        'button' => [
            'label' => __('ui.blog.article_cta.button'),
            'url' => route('contact.create'),
        ],
    ];
    $articleCta['title_lines'] = array_values(array_filter(
        $articleCta['title_lines'],
        static fn ($line) => trim((string) $line) !== '',
    ));
    $relatedHeadingKicker = str_starts_with(strtolower((string) ($locale ?? app()->getLocale())), 'hr')
        ? 'NAJNOVIJE OBJAVE'
        : 'LATEST POSTS';
    $pageTitleBreadcrumbs = [
        ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
        ['label' => __('ui.blog.title'), 'url' => route('blog.index')],
    ];

    if ($primaryCategoryName !== '') {
        $pageTitleBreadcrumbs[] = ['label' => $primaryCategoryName, 'url' => $primaryCategoryUrl];
    }

    $pageTitleBreadcrumbs[] = [
        'label' => $breadcrumbCurrentTitle,
        'current' => true,
        'current_class' => 'ac-blog-breadcrumb-current',
        'title' => $articleTitle,
    ];
@endphp

@section('title', $translation?->title ?? __('ui.blog.page_title'))
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-blog-page ac-blog-article-page">
        <x-front.page-title-band
            :breadcrumbs="$pageTitleBreadcrumbs"
            section-class="ac-blog-title-band ac-blog-article-title-band"
            hero-class="ac-blog-article-hero"
            panel-class="ac-blog-article-panel"
            breadcrumb-class="ac-blog-hero-breadcrumb ac-blog-article-breadcrumb"
        >
            <div class="ac-blog-article-head">
                <h1 class="ac-blog-article-title">{{ $articleTitle }}</h1>

                <div class="ac-blog-article-meta">
                    @if ($publishedLabel)
                        <span class="ac-blog-article-chip is-date">{{ $publishedLabel }}</span>
                    @endif

                    @foreach ($postCategories as $category)
                        @php
                            $categoryTranslation = $category->translations->firstWhere('locale', $locale)
                                ?? $category->translations->firstWhere('locale', $fallbackLocale);
                            $categoryLabel = trim((string) ($categoryTranslation?->name ?? $category->code));
                        @endphp
                        <span class="ac-blog-article-chip">{{ $categoryLabel }}</span>
                    @endforeach
                </div>
            </div>
        </x-front.page-title-band>

        <div class="ac-blog-article-shell ac-blog-post-article-shell mx-auto w-full max-w-[1320px] px-4 sm:px-6 lg:px-8">
            <article class="ac-blog-article-body">
                <div class="ac-blog-article-body-inner">
                    @if ($coverImageUrl)
                        <figure class="ac-blog-article-cover">
                            <img
                                src="{{ $coverImageUrl }}"
                                alt="{{ $translation?->title ?? $post->code }}"
                                class="h-auto w-full object-cover"
                                loading="eager"
                                decoding="async"
                            >
                        </figure>
                    @endif

                    <div class="content-richtext">
                        {!! $bodyHtml !== '' ? $bodyHtml : '<p>No body content available.</p>' !!}
                    </div>
                </div>
            </article>

            @if ($galleryItems->isNotEmpty())
                <section class="ac-blog-article-gallery">
                    <div class="grid gap-5 {{ $galleryColumnsClass }}" data-blog-gallery>
                        @foreach ($galleryItems as $mediaItem)
                            @php
                                $galleryImageUrl = $mediaItem->getUrl();
                            @endphp
                            <a
                                href="{{ $galleryImageUrl }}"
                                class="block aspect-[3/4] overflow-hidden rounded-[8px] bg-slate-100"
                                data-blog-gallery-item
                                data-sub-html="{{ $translation?->title ?? $post->code }}"
                            >
                                <img
                                    src="{{ $galleryImageUrl }}"
                                    alt="{{ $translation?->title ?? $post->code }}"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="ac-blog-share" aria-label="{{ __('ui.blog.share.title') }}">
                <div class="ac-blog-results-head is-centered">
                    <div>
                        <p class="ac-blog-results-kicker">{{ __('ui.blog.eyebrow') }}</p>
                        <h2>{{ __('ui.blog.share.title') }}</h2>
                        <p class="ac-blog-section-intro">{{ __('ui.blog.share.subtitle') }}</p>
                    </div>
                </div>

                <div class="ac-blog-share-links">
                    @foreach ($shareLinks as $shareLink)
                        <a
                            href="{{ $shareLink['url'] }}"
                            class="ac-blog-share-link ac-blog-share-link--{{ $shareLink['key'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="{{ $shareLink['label'] }}"
                        >
                            @if ($shareLink['key'] === 'x')
                                <svg viewBox="0 0 448 512" fill="currentColor" aria-hidden="true"><path d="M64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l320 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32zm297.1 84l-103.8 118.6 122.1 161.4-95.6 0-74.8-97.9-85.7 97.9-47.5 0 111-126.9-117.1-153.1 98 0 67.7 89.5 78.2-89.5 47.5 0zM323.3 367.6l-169.9-224.7-28.3 0 171.8 224.7 26.4 0z"/></svg>
                            @elseif ($shareLink['key'] === 'facebook')
                                <svg viewBox="0 0 448 512" fill="currentColor" aria-hidden="true"><path d="M64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l98.2 0 0-145.8-52.8 0 0-78.2 52.8 0 0-33.7c0-87.1 39.4-127.5 125-127.5 16.2 0 44.2 3.2 55.7 6.4l0 70.8c-6-.6-16.5-1-29.6-1-42 0-58.2 15.9-58.2 57.2l0 27.8 83.6 0-14.4 78.2-69.3 0 0 145.8 129 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32z"/></svg>
                            @else
                                <svg viewBox="0 0 448 512" fill="currentColor" aria-hidden="true"><path d="M64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l320 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32zm5 170.2l66.5 0 0 213.8-66.5 0 0-213.8zm71.7-67.7a38.5 38.5 0 1 1 -77 0 38.5 38.5 0 1 1 77 0zM317.9 416l0-104c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9l0 105.8-66.4 0 0-213.8 63.7 0 0 29.2 .9 0c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9l0 117.2-66.4 0z"/></svg>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        </div>

        <section class="ac-inline-cta ac-inline-cta--blog" aria-labelledby="ac-blog-inline-cta-title">
            <div class="mx-auto w-full max-w-[1320px] px-4 sm:px-6 lg:px-8">
                <div class="ac-inline-cta-card ac-inline-cta-card--blog">
                    <div class="mx-auto grid w-full max-w-[860px] gap-4 py-5 md:grid-cols-[minmax(0,1fr)_auto] md:items-center">
                        <div class="ac-inline-cta-copy">
                            <h2 id="ac-blog-inline-cta-title" class="ac-inline-cta-title">
                                @foreach ($articleCta['title_lines'] as $line)
                                    <span>{{ $line }}</span>
                                @endforeach
                            </h2>
                        </div>

                        <div class="ac-inline-cta-action">
                            <a href="{{ $articleCta['button']['url'] }}" class="front-action-cta">
                                <span>{{ $articleCta['button']['label'] }}</span>
                                <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M4 12L12 4"></path>
                                    <path d="M6 4h6v6"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if ($related->isNotEmpty())
            <section class="ac-support-story ac-home-blog ac-blog-related-section" aria-labelledby="ac-blog-related-title">
                <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                    <div class="ac-support-story-hero">
                        <div class="ac-support-story-shell">
                            <div class="ac-services-head ac-support-story-head">
                                <p class="ac-family-section-kicker">{{ $relatedHeadingKicker }}</p>
                                <h2 id="ac-blog-related-title">
                                    <span>{{ __('ui.blog.related_title') }}</span>
                                </h2>
                                <p class="ac-services-intro">{{ __('ui.blog.related_intro') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="ac-home-blog-carousel ac-blog-related-content">
                        <div class="ac-blog-grid ac-blog-grid-related">
                        @foreach ($related as $relatedPost)
                            @include('front.desktop.blog.partials.card', [
                                'post' => $relatedPost,
                                'locale' => $locale,
                                'fallbackLocale' => $fallbackLocale,
                            ])
                        @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/css/lightgallery-bundle.min.css">
@endpush

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.min.js"></script>
    <script defer>
        document.addEventListener('DOMContentLoaded', function () {
            const galleryRoot = document.querySelector('[data-blog-gallery]');
            if (!galleryRoot || typeof window.lightGallery !== 'function') {
                return;
            }

            window.lightGallery(galleryRoot, {
                selector: '[data-blog-gallery-item]',
                download: false,
                counter: true,
            });
        });
    </script>
@endpush
