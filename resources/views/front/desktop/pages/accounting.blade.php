@extends('front.desktop.layouts.store')

@php
    $overviewBody = array_values($overviewSection['body'] ?? []);
    $accountingServices = array_values($servicesSection['items'] ?? []);
    $approachBody = array_values($approachSection['body'] ?? []);
    $approachIntro = trim((string) ($approachSection['intro'] ?? ''));
    $meetingTitle = trim((string) ($meetingSection['title'] ?? '')) ?: 'Razgovarajmo o vašem računovodstvu';
    $meetingIntro = trim((string) ($meetingSection['intro'] ?? '')) ?: 'Javite nam se - procijenit ćemo vaše potrebe i predložiti model računovodstvene podrške.';
    $meetingLinkLabel = trim((string) ($meetingSection['contact_title'] ?? '')) ?: 'Kontaktirajte nas';
    $isCroatianLocale = str_starts_with(strtolower((string) ($locale ?? app()->getLocale())), 'hr');
    $blogHeadingKicker = $isCroatianLocale ? 'NAJNOVIJE OBJAVE' : 'LATEST POSTS';
    $blogHeadingTitle = trim((string) ($accountingCategoryName ?? '')) ?: ($isCroatianLocale ? 'Računovodstvo' : 'Accounting');
    $readMoreLabel = $isCroatianLocale ? 'Opširnije' : 'Read more';
    $currentHost = request()->getHost();
    $sameOriginAssetUrl = static function (?string $url) use ($currentHost): string {
        $assetUrl = trim((string) $url);
        $assetHost = parse_url($assetUrl, PHP_URL_HOST);

        if ($assetUrl === '' || ($assetHost !== null && $assetHost !== $currentHost)) {
            return $assetUrl;
        }

        $assetPath = parse_url($assetUrl, PHP_URL_PATH);
        $assetQuery = parse_url($assetUrl, PHP_URL_QUERY);

        if (is_string($assetPath) && $assetPath !== '') {
            return $assetPath.($assetQuery ? '?'.$assetQuery : '');
        }

        return $assetUrl;
    };
    $heroImageUrl = $sameOriginAssetUrl((string) $heroBackgroundUrl);

    if ($approachBody === [] && $approachIntro !== '') {
        $approachBody = [$approachIntro];
    }

    $hasAccountingPosts = ($accountingPosts ?? collect())->isNotEmpty();
    $hasServiceVideos = collect($serviceVideos ?? [])->isNotEmpty();
@endphp

@section('title', $servicePageMetaTitle !== '' ? $servicePageMetaTitle : ($servicePageTitle ?? 'Računovodstvo'))
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-family-business-page ac-audit-page ac-accounting-page">
        <section class="ac-family-hero ac-service-hero ac-service-hero--accounting">
            <div class="ac-family-hero-media" aria-hidden="true" style="--audit-hero-image: url('{{ $heroImageUrl }}'); background-image: url('{{ $heroImageUrl }}');">
                <img src="{{ $heroImageUrl }}" alt="" class="ac-family-hero-media-image" loading="eager" decoding="async">
            </div>
            <div class="ac-family-hero-overlay"></div>

            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-family-hero-content">
                    <div class="ac-family-hero-shell">
                        <div class="ac-family-hero-copy ac-service-hero-card">
                            <h1 class="ac-family-hero-title">
                                <span class="is-brand">{{ $heroSection['brand_title'] ?? 'ALPHA CAPITALIS' }}</span>
                                <span class="is-subtitle">
                                    <span class="is-subtitle-lead">{{ $heroSection['subtitle_lead'] ?? 'Računovodstvo' }}</span>
                                    @if (trim((string) ($heroSection['subtitle_accent'] ?? '')) !== '')
                                        <span class="is-subtitle-accent">{{ $heroSection['subtitle_accent'] }}</span>
                                    @endif
                                </span>
                            </h1>

                            <p class="ac-family-hero-intro">{{ $heroSection['intro'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="accounting-overview" class="ac-audit-editorial-wrap" aria-labelledby="ac-accounting-overview-title">
            <div class="mx-auto w-full max-w-[1120px] px-5 lg:px-8">
                <div class="ac-audit-editorial-shell">
                    <article class="ac-audit-editorial-section ac-audit-editorial-section--overview">
                        <div class="ac-audit-section-head ac-audit-section-head--center">
                            <p class="ac-family-section-kicker">{{ $overviewSection['kicker'] ?? 'RAČUNOVODSTVO' }}</p>
                            <h2 id="ac-accounting-overview-title">{{ $overviewSection['title'] ?? 'Što je računovodstvo?' }}</h2>
                            @if (trim((string) ($overviewSection['intro'] ?? '')) !== '')
                                <p>{{ $overviewSection['intro'] }}</p>
                            @endif
                        </div>

                        <div class="ac-audit-copy ac-audit-copy--full">
                            @foreach ($overviewBody as $paragraph)
                                @if (trim((string) $paragraph) !== '')
                                    <p>{{ $paragraph }}</p>
                                @endif
                            @endforeach
                        </div>
                    </article>

                    <article class="ac-audit-editorial-section">
                        <div class="ac-audit-section-head ac-audit-section-head--center">
                            <p class="ac-family-section-kicker">{{ $servicesSection['kicker'] ?? 'USLUGE' }}</p>
                            <h2>{{ $servicesSection['title'] ?? 'Naše računovodstvene usluge' }}</h2>
                            @if (trim((string) ($servicesSection['intro'] ?? '')) !== '')
                                <p>{{ $servicesSection['intro'] }}</p>
                            @endif
                        </div>

                        <div class="ac-audit-card-grid">
                            @foreach ($accountingServices as $item)
                                <article class="ac-audit-service-card">
                                    <h3>{{ $item['title'] ?? '' }}</h3>
                                    <p>{{ $item['text'] ?? '' }}</p>
                                </article>
                            @endforeach
                        </div>
                    </article>

                    <article class="ac-audit-editorial-section">
                        <div class="ac-audit-section-head ac-audit-section-head--center">
                            <p class="ac-family-section-kicker">{{ $approachSection['kicker'] ?? 'PRISTUP' }}</p>
                            <h2>{{ $approachSection['title'] ?? 'Naš pristup' }}</h2>
                        </div>

                        <blockquote class="ac-audit-copy ac-audit-copy--full ac-audit-approach-copy">
                            @foreach ($approachBody as $paragraph)
                                @if (trim((string) $paragraph) !== '')
                                    <p>{{ $paragraph }}</p>
                                @endif
                            @endforeach
                        </blockquote>
                    </article>
                </div>
            </div>
        </section>

        @include('front.desktop.partials.service-videos', [
            'serviceVideoSection' => $serviceVideoSection ?? [],
            'serviceVideos' => $serviceVideos ?? [],
            'locale' => $locale ?? app()->getLocale(),
        ])

        <section id="accounting-sastanak" class="ac-service-cta-section" aria-labelledby="ac-accounting-meeting-title">
            <div class="ac-service-cta-container">
                <div class="ac-service-cta-card">
                    <div class="ac-service-cta-copy">
                        <h2 id="ac-accounting-meeting-title">{{ $meetingTitle }}</h2>
                        <p>{{ $meetingIntro }}</p>
                    </div>

                    <a href="{{ route('contact.create') }}" class="ac-service-cta-link">
                        <span>{{ $meetingLinkLabel }}</span>
                    </a>
                </div>
            </div>
        </section>

        @if ($hasAccountingPosts)
            <section class="ac-support-story ac-home-blog ac-blog-related-section ac-family-blog-section ac-audit-blog-section ac-accounting-blog-section" aria-labelledby="ac-accounting-blog-title">
                <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                    <div class="ac-support-story-hero">
                        <div class="ac-support-story-shell">
                            <div class="ac-services-head ac-support-story-head">
                                <p class="ac-family-section-kicker">{{ $blogHeadingKicker }}</p>
                                <h2 id="ac-accounting-blog-title">
                                    <span>{{ $blogHeadingTitle }}</span>
                                </h2>
                                <p class="ac-services-intro">{{ $blogSection['intro'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="ac-home-blog-carousel">
                        <div id="ac-accounting-blog-splide" class="splide ac-home-blog-splide" data-accounting-blog-splide>
                            <div class="splide__track">
                                <ul class="splide__list">
                                    @foreach ($accountingPosts as $post)
                                        @php
                                            $translation = $post->translations->firstWhere('locale', $locale)
                                                ?? $post->translations->firstWhere('locale', $fallbackLocale);
                                            $postSlug = trim((string) ($translation?->slug ?? ''));
                                            $postUrl = $postSlug !== '' ? route('blog.show', ['slug' => $postSlug]) : route('blog.index');
                                            $postTitle = trim((string) ($translation?->title ?? $post->code));
                                            $postExcerpt = trim((string) ($translation?->excerpt ?? '')) ?: __('ui.blog.excerpt_fallback');
                                            $postExcerpt = \Illuminate\Support\Str::limit($postExcerpt, 180, '...', true);
                                            $postImage = $post->getFirstMedia('blog_cover');
                                            $postImageSource = $postImage
                                                ? ($postImage->hasGeneratedConversion('card_360x240') ? $postImage->getUrl('card_360x240') : $postImage->getUrl())
                                                : '';
                                            $postImageUrl = $sameOriginAssetUrl($postImageSource);
                                            $primaryCategory = $post->categories
                                                ->sortByDesc(fn ($category) => (int) ($category->pivot->is_primary ?? false))
                                                ->first();
                                            $categoryTranslation = $primaryCategory?->translations->firstWhere('locale', $locale)
                                                ?? $primaryCategory?->translations->firstWhere('locale', $fallbackLocale);
                                            $categoryLabel = trim((string) ($categoryTranslation?->name ?? 'Novosti'));
                                            $publishedLabel = ($post->published_at ?? $post->created_at)?->translatedFormat($isCroatianLocale ? 'j. F Y.' : 'F j, Y');
                                        @endphp
                                        <li class="splide__slide ac-home-blog-slide">
                                            <article class="ac-home-blog-card">
                                                <a href="{{ $postUrl }}" class="ac-home-blog-card-link" aria-label="{{ $readMoreLabel }}: {{ $postTitle }}">
                                                    <div class="ac-home-blog-card-media">
                                                        @if ($postImageUrl)
                                                            <img
                                                                src="{{ $postImageUrl }}"
                                                                alt="{{ $postTitle }}"
                                                                class="ac-home-blog-card-image"
                                                                width="360"
                                                                height="240"
                                                                sizes="(min-width: 1180px) 384px, (min-width: 760px) 50vw, 100vw"
                                                                loading="eager"
                                                                decoding="async"
                                                            >
                                                        @else
                                                            <div class="ac-home-blog-card-placeholder">
                                                                <span>{{ __('ui.blog.title') }}</span>
                                                            </div>
                                                        @endif

                                                        <div class="ac-home-blog-card-overlay">
                                                            <span class="ac-home-blog-card-overlay-kicker">
                                                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::limit($categoryLabel, 22, '')) }}
                                                            </span>
                                                            <span class="ac-home-blog-card-overlay-line" aria-hidden="true"></span>
                                                        </div>
                                                    </div>

                                                    <div class="ac-home-blog-card-body">
                                                        <h3 class="ac-home-blog-card-title">{{ $postTitle }}</h3>
                                                        <p class="ac-home-blog-card-excerpt">{{ $postExcerpt }}</p>
                                                    </div>

                                                    <div class="ac-home-blog-card-meta">
                                                        <span class="ac-home-blog-card-meta-link">
                                                            <span>{{ $readMoreLabel }}</span>
                                                            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                                <path d="M4 12L12 4"></path>
                                                                <path d="M6 4h6v6"></path>
                                                            </svg>
                                                        </span>
                                                        @if ($publishedLabel)
                                                            <span class="ac-home-blog-card-meta-date">{{ $publishedLabel }}</span>
                                                        @endif
                                                    </div>
                                                </a>
                                            </article>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection

@if ($hasAccountingPosts || $hasServiceVideos)
    @once
        @push('styles')
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
        @endpush
    @endonce

    @once
        @push('scripts')
            <script defer src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
        @endpush
    @endonce
@endif

@if ($hasAccountingPosts)
    @push('scripts')
        <script>
            (function () {
                const initAccountingBlogSlider = function () {
                    if (typeof window.Splide !== 'function') {
                        return false;
                    }

                    document.querySelectorAll('[data-accounting-blog-splide]').forEach(function (el) {
                        if (el.dataset.splideReady === '1') {
                            return;
                        }

                        el.dataset.splideReady = '1';

                        const count = el.querySelectorAll('.splide__slide').length;
                        const slider = new window.Splide(el, {
                            type: 'slide',
                            perPage: Math.min(3, Math.max(1, count)),
                            perMove: 1,
                            gap: '1.25rem',
                            drag: count > 1,
                            snap: true,
                            rewind: count > 1,
                            pagination: count > 1,
                            arrows: count > 1,
                            updateOnMove: true,
                            speed: 520,
                            breakpoints: {
                                1180: { perPage: Math.min(2, Math.max(1, count)) },
                                760: { perPage: 1, gap: '1rem' },
                            },
                        });

                        slider.mount();
                    });

                    return true;
                };

                if (initAccountingBlogSlider()) {
                    return;
                }

                let attempts = 0;
                const timer = window.setInterval(function () {
                    attempts += 1;
                    if (initAccountingBlogSlider() || attempts > 40) {
                        window.clearInterval(timer);
                    }
                }, 120);
            }());
        </script>
    @endpush
@endif
