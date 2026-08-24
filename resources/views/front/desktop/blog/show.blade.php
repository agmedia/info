@extends('front.desktop.layouts.store')

@php
    $translation = $post->translations->firstWhere('locale', $locale)
        ?? $post->translations->firstWhere('locale', $fallbackLocale);
    $mediaItems = $post->relationLoaded('media')
        ? $post->media
            ->sortBy(static fn ($mediaItem) => (int) ($mediaItem->order_column ?? 0))
            ->values()
        : collect();
    $coverImage = $mediaItems->firstWhere('collection_name', 'blog_cover') ?? $post->getFirstMedia('blog_cover');
    $sameOriginStorageUrl = static function (?string $url): string {
        $value = trim((string) $url);
        if ($value === '') {
            return '';
        }

        $path = parse_url(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'), PHP_URL_PATH);
        if (! is_string($path) || ! str_starts_with($path, '/storage/')) {
            return $value;
        }

        $query = parse_url($value, PHP_URL_QUERY);
        $fragment = parse_url($value, PHP_URL_FRAGMENT);

        return $path
            .(is_string($query) && $query !== '' ? '?'.$query : '')
            .(is_string($fragment) && $fragment !== '' ? '#'.$fragment : '');
    };
    $coverImageUrl = $coverImage ? $sameOriginStorageUrl($coverImage->getUrl()) : null;
    $galleryItems = $mediaItems->where('collection_name', 'blog_gallery')->values();
    if ($galleryItems->isEmpty()) {
        $galleryItems = $post->getMedia('blog_gallery')
            ->sortBy(static fn ($mediaItem) => (int) ($mediaItem->order_column ?? 0))
            ->values();
    }
    $bodyHtml = (string) ($translation?->body_html ?? '');
    if ($bodyHtml !== '') {
        $bodyHtml = preg_replace_callback(
            '/\b(src|href)=(["\'])(.*?)\2/i',
            static function (array $matches) use ($sameOriginStorageUrl): string {
                $normalizedUrl = $sameOriginStorageUrl((string) ($matches[3] ?? ''));

                return (string) $matches[1].'='.(string) $matches[2].$normalizedUrl.(string) $matches[2];
            },
            $bodyHtml
        ) ?? $bodyHtml;
    }
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
    $articleTitle = trim((string) ($translation?->title ?? $post->code));
    $publishedAt = ($post->published_at ?? $post->created_at)?->copy()->setTimezone(config('admin_ui.timezone'));
    $publishedLabel = $publishedAt?->translatedFormat('j. F Y.');
    $primaryCategory = $postCategories->first();
    $primaryCategoryTranslation = $primaryCategory
        ? ($primaryCategory->translations->firstWhere('locale', $locale)
            ?? $primaryCategory->translations->firstWhere('locale', $fallbackLocale)
            ?? $primaryCategory->translations->first())
        : null;
    $primaryCategoryLabel = trim((string) ($primaryCategoryTranslation?->name ?? $primaryCategory?->code ?? ''));
    $primaryCategorySlug = trim((string) ($primaryCategoryTranslation?->slug ?? ''));
    $primaryCategoryUrl = $primaryCategorySlug !== '' ? url('/blog/'.$primaryCategorySlug) : route('blog.index');
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
    $articleCtaTitle = implode(' ', $articleCta['title_lines']);
    $headingWords = static fn (string $title): array => preg_split('/\s+/u', trim($title), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $isCroatian = str_starts_with(strtolower((string) ($locale ?? app()->getLocale())), 'hr');
    $pageTitleBreadcrumbs = [
        ['label' => $isCroatian ? 'Početna' : 'Home', 'url' => route('home')],
        ['label' => $isCroatian ? 'Objave' : 'Posts', 'url' => route('blog.index')],
    ];
    if ($primaryCategoryLabel !== '') {
        $pageTitleBreadcrumbs[] = ['label' => $primaryCategoryLabel, 'url' => $primaryCategoryUrl];
    }
    $articleCtaCardTitle = $isCroatian ? 'Razgovarajmo o vašem poslovanju.' : 'Let’s talk about your business.';
    $articleCtaCardCopy = $isCroatian
        ? 'Naš multidisciplinarni tim pomoći će vam jasno sagledati sljedeći korak.'
        : 'Our multidisciplinary team will help you clearly identify the next step.';
    $articleCtaStatus = $isCroatian
        ? 'Odgovaramo brzo i konkretno.'
        : 'We respond quickly and with clarity.';
@endphp

@section('title', $translation?->title ?? __('ui.blog.page_title'))
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-blog-page ac-blog-article-page">
        @if ($isAdminPreview ?? false)
            <div class="ac-blog-admin-preview" role="status">
                <div class="ac-blog-container ac-blog-admin-preview__inner">
                    <i class="fa-light fa-eye" aria-hidden="true"></i>
                    <strong>{{ __('Admin preview') }}</strong>
                    <span>{{ __('This is the saved version. The article may still be inactive or unpublished for public visitors.') }}</span>
                </div>
            </div>
        @endif
        <x-front.page-title-band
            :breadcrumbs="$pageTitleBreadcrumbs"
            section-class="ac-blog-article-intro"
            container-class="ac-blog-container"
            hero-class="ac-blog-article-intro-hero"
            panel-class="ac-blog-article-intro-grid"
            breadcrumb-class="ac-blog-article-breadcrumb"
        >
            <h1 class="ac-blog-article-title content-reveal animation-index-1" id="ac-blog-article-title" data-image-reveal>{{ $articleTitle }}</h1>

            <div class="ac-blog-article-meta content-reveal animation-index-2" data-image-reveal>
                @if ($publishedLabel)
                    <time datetime="{{ $publishedAt?->toDateString() }}">{{ $publishedLabel }}</time>
                @endif

                @foreach ($postCategories as $category)
                    @php
                        $categoryTranslation = $category->translations->firstWhere('locale', $locale)
                            ?? $category->translations->firstWhere('locale', $fallbackLocale)
                            ?? $category->translations->first();
                        $categoryLabel = trim((string) ($categoryTranslation?->name ?? $category->code));
                        $categorySlug = trim((string) ($categoryTranslation?->slug ?? ''));
                        $categoryUrl = $categorySlug !== '' ? url('/blog/'.$categorySlug) : route('blog.index');
                    @endphp
                    <a href="{{ $categoryUrl }}">{{ $categoryLabel }}</a>
                @endforeach
            </div>
        </x-front.page-title-band>

        <article class="ac-blog-article-body">
            <div class="ac-blog-container ac-blog-article-shell ac-blog-post-article-shell">
                @if ($coverImageUrl)
                    <figure class="ac-blog-article-cover content-reveal" data-image-reveal>
                        <div class="image-reveal-media">
                            <img
                                src="{{ $coverImageUrl }}"
                                alt="{{ $translation?->title ?? $post->code }}"
                                loading="eager"
                                decoding="async"
                                fetchpriority="high"
                            >
                            <span class="image-reveal-curtain" aria-hidden="true"></span>
                        </div>
                    </figure>
                @endif

                <div class="ac-blog-article-body-inner content-reveal animation-index-1" data-image-reveal>
                    <div class="content-richtext">
                        {!! $bodyHtml !== '' ? $bodyHtml : '<p>No body content available.</p>' !!}
                    </div>
                </div>

                @if ($galleryItems->isNotEmpty())
                    <section class="ac-blog-article-gallery" aria-label="{{ $articleTitle }}">
                        <div class="ac-blog-gallery-grid {{ $galleryColumnsClass }}" data-blog-gallery>
                            @foreach ($galleryItems as $mediaItem)
                                @php
                                    $galleryImageUrl = $sameOriginStorageUrl($mediaItem->getUrl());
                                @endphp
                                <a
                                    href="{{ $galleryImageUrl }}"
                                    class="ac-blog-gallery-item"
                                    data-blog-gallery-item
                                    data-sub-html="{{ $translation?->title ?? $post->code }}"
                                >
                                    <img
                                        src="{{ $galleryImageUrl }}"
                                        alt="{{ $translation?->title ?? $post->code }}"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                <section class="ac-blog-share" aria-labelledby="ac-blog-share-title">
                    <div class="ac-blog-share-copy">
                        <p>{{ __('ui.blog.eyebrow') }}</p>
                        <h2 id="ac-blog-share-title">{{ __('ui.blog.share.title') }}</h2>
                        <span>{{ __('ui.blog.share.subtitle') }}</span>
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
                                <i class="fa-brands {{ $shareLink['key'] === 'x' ? 'fa-x-twitter' : 'fa-'.$shareLink['key'] }}" aria-hidden="true"></i>
                            </a>
                        @endforeach
                    </div>
                </section>
            </div>
        </article>

        <section class="contact-cta ac-blog-contact-cta" aria-labelledby="ac-blog-contact-cta-title">
            <div class="contact-cta-shell">
                <div class="contact-cta-copy">
                    <h2 class="contact-cta-title" id="ac-blog-contact-cta-title" data-words-slide-from-right aria-label="{{ $articleCtaTitle }}">
                        @foreach ($headingWords($articleCtaTitle) as $word)
                            <span class="contact-cta-title-word animation-index-{{ $loop->index }} {{ $loop->remaining < 2 ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>
                </div>

                <div class="contact-cta-card" data-image-reveal>
                    <div class="contact-cta-card-heading"><span>{{ $articleCtaCardTitle }}</span></div>
                    <p>{{ $articleCtaCardCopy }}</p>
                    <a class="contact-cta-button" href="{{ $articleCta['button']['url'] }}">
                        <span>{{ $articleCta['button']['label'] }}</span>
                        <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                    </a>
                    <small><span class="contact-cta-status-dot" aria-hidden="true"></span>{{ $articleCtaStatus }}</small>
                </div>
            </div>
        </section>

        @if ($related->isNotEmpty())
            <section class="ac-blog-related-section" aria-labelledby="ac-blog-related-title">
                <div class="ac-blog-container ac-blog-related-container">
                    <div class="ac-blog-related-head">
                        <h2 id="ac-blog-related-title" aria-label="{{ __('ui.blog.related_title') }}">
                            @foreach ($headingWords(__('ui.blog.related_title')) as $word)
                                <span class="{{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                            @endforeach
                        </h2>
                        <span>{{ __('ui.blog.related_intro') }}</span>
                    </div>

                    <div class="ac-blog-grid ac-blog-grid-related">
                        @foreach ($related as $relatedPost)
                            @include('front.desktop.blog.partials.card', [
                                'post' => $relatedPost,
                                'locale' => $locale,
                                'fallbackLocale' => $fallbackLocale,
                                'headingLevel' => 3,
                                'revealIndex' => $loop->index,
                            ])
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/blog.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/blog.css')) }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/css/lightgallery-bundle.min.css">
@endpush

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.min.js"></script>
    <script defer src="{{ asset('front-theme/scripts/blog.js') }}?v={{ filemtime(public_path('front-theme/scripts/blog.js')) }}"></script>
@endpush
