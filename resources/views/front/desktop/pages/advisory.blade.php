@extends('front.desktop.layouts.store')

@php
    $content = (array) ($advisoryContent ?? []);
    $hero = (array) ($content['hero'] ?? []);
    $overview = (array) ($content['overview'] ?? []);
    $services = (array) ($content['services_intro'] ?? []);
    $serviceCards = array_values(array_filter(
        (array) ($content['service_cards'] ?? []),
        static fn ($item): bool => is_array($item) && trim((string) ($item['title'] ?? '')) !== '',
    ));
    $pandea = (array) ($content['pandea'] ?? []);
    $approach = (array) ($content['approach'] ?? []);
    $meeting = (array) ($content['meeting'] ?? []);
    $blogSection = (array) ($content['blog_section'] ?? []);
    $isCroatian = str_starts_with(strtolower((string) ($locale ?? app()->getLocale())), 'hr');
    $overviewBody = array_values(array_filter(
        (array) ($overview['body'] ?? []),
        static fn ($paragraph): bool => trim((string) $paragraph) !== '',
    ));
    $pandeaBody = array_values(array_filter(
        (array) ($pandea['body'] ?? []),
        static fn ($paragraph): bool => trim((string) $paragraph) !== '',
    ));
    $networkTitle = trim((string) ($pandea['title'] ?? ''));
    $networkTitleLines = preg_split('/(?=Pandea Global M&A)/u', $networkTitle, 2, PREG_SPLIT_NO_EMPTY) ?: [$networkTitle];
    $approachBody = array_values(array_filter(
        (array) ($approach['body'] ?? []),
        static fn ($paragraph): bool => trim((string) $paragraph) !== '',
    ));
    $serviceIcons = [
        'fa-hand-holding-circle-dollar',
        'fa-people-arrows-left-right',
        'fa-magnifying-glass-dollar',
        'fa-chart-user',
        'fa-badge-percent',
    ];
    $heroLabel = trim((string) ($hero['subtitle_lead'] ?? '')) ?: ($isCroatian ? 'Savjetovanje' : 'Advisory');
    $heroHook = trim((string) ($hero['intro'] ?? ''));
    $heroImageAlt = trim((string) ($hero['image_alt'] ?? ''))
        ?: ($isCroatian ? 'Stručno financijsko i strateško savjetovanje' : 'Expert financial and strategic advisory');
    $meetingTitle = trim((string) ($meeting['title'] ?? ''))
        ?: ($isCroatian ? 'Razgovarajmo o vašim poslovnim odlukama' : 'Let’s discuss your business decisions');
    $meetingIntro = trim((string) ($meeting['intro'] ?? ''))
        ?: ($isCroatian
            ? 'Javite nam se i zajedno ćemo procijeniti koji oblik savjetodavne podrške najbolje odgovara vašem cilju.'
            : 'Contact us and we will assess which form of advisory support best fits your goal.');
    $meetingCardTitle = trim((string) ($meeting['contact_title'] ?? ''))
        ?: ($isCroatian ? 'Kontaktirajte nas' : 'Contact us');
    $meetingButtonLabel = trim((string) ($meeting['button_label'] ?? ''))
        ?: ($isCroatian ? 'Dogovorite sastanak' : 'Schedule a meeting');
    $meetingStatus = trim((string) ($meeting['status'] ?? ''))
        ?: ($isCroatian ? 'Termin razgovora prilagođavamo vama.' : 'We arrange the meeting around your schedule.');
    $blogHeadingTitle = trim((string) ($blogSection['title'] ?? ''))
        ?: ($isCroatian ? 'Stručni uvidi u financije, poreze i transakcije' : 'Expert insights into finance, tax and transactions');
    $allPostsLabel = trim((string) ($blogSection['all_posts_label'] ?? ''))
        ?: ($isCroatian ? 'Pogledaj sve objave' : 'View all posts');
    $readMoreLabel = trim((string) ($blogSection['post_action_label'] ?? ''))
        ?: ($isCroatian ? 'Opširnije' : 'Read more');
    $serviceActionLabel = trim((string) ($services['card_action_label'] ?? '')) ?: $readMoreLabel;
    $currentHost = request()->getHost();
    $sameOriginAssetUrl = static function (?string $url) use ($currentHost): string {
        $assetUrl = trim((string) $url);
        $assetHost = parse_url($assetUrl, PHP_URL_HOST);

        if ($assetUrl === '' || ($assetHost !== null && $assetHost !== $currentHost)) {
            return $assetUrl;
        }

        $assetPath = parse_url($assetUrl, PHP_URL_PATH);
        $assetQuery = parse_url($assetUrl, PHP_URL_QUERY);

        return is_string($assetPath) && $assetPath !== ''
            ? $assetPath.($assetQuery ? '?'.$assetQuery : '')
            : $assetUrl;
    };
    $resolveContentUrl = static function (?string $url): string {
        $target = trim((string) $url);

        if ($target === '' || str_starts_with($target, '#') || str_starts_with($target, 'http://') || str_starts_with($target, 'https://')) {
            return $target;
        }

        return url(str_starts_with($target, '/') ? $target : '/'.$target);
    };
    $headingWords = static fn (string $heading): array => preg_split('/\s+/u', trim($heading)) ?: [];
    $heroImageUrl = $sameOriginAssetUrl((string) $heroBackgroundUrl);
    $networkLogoUrl = $sameOriginAssetUrl((string) ($pandeaLogoUrl ?? ''));
    $hasAdvisoryPosts = ($advisoryPosts ?? collect())->isNotEmpty();
@endphp

@section('title', $servicePageMetaTitle !== '' ? $servicePageMetaTitle : ($servicePageTitle ?? $heroLabel))
@section('main_class', 'w-full px-0 py-0')

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/advisory.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/advisory.css')) }}">
@endpush

@section('content')
    <div class="ac-advisory-page">
        <section class="ac-advisory-hero" id="vrh" aria-labelledby="ac-advisory-hero-title">
            <div class="ac-advisory-hero-media">
                <img
                    src="{{ $heroImageUrl }}"
                    alt="{{ $heroImageAlt }}"
                    class="ac-advisory-hero-image"
                    width="1366"
                    height="768"
                    loading="eager"
                    decoding="async"
                    fetchpriority="high"
                >
            </div>
            <div class="ac-advisory-hero-overlay" aria-hidden="true"></div>

            <div class="ac-advisory-hero-shell">
                <div class="ac-advisory-hero-copy">
                    <h1 id="ac-advisory-hero-title" aria-label="{{ $heroLabel }}. {{ $heroHook }}">
                        <span class="ac-advisory-hero-label">{{ $heroLabel }}</span>
                        @if ($heroHook !== '')
                            <span class="ac-advisory-hero-hook">{{ $heroHook }}</span>
                        @endif
                    </h1>
                </div>
            </div>
        </section>

        <section class="ac-advisory-intro" id="advisory-overview" aria-labelledby="ac-advisory-overview-title">
            <div class="ac-advisory-wide-shell ac-advisory-intro-grid">
                <div class="ac-advisory-intro-heading">
                    <h2 id="ac-advisory-overview-title" data-words-slide-from-right aria-label="{{ $overview['title'] ?? '' }}">
                        @foreach ($headingWords((string) ($overview['title'] ?? '')) as $word)
                            <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>
                </div>

                <div class="ac-advisory-intro-copy content-reveal animation-index-1" data-image-reveal>
                    @foreach ($overviewBody as $paragraph)
                        <p class="{{ $loop->last ? 'is-emphasis' : '' }}">{{ $paragraph }}</p>
                    @endforeach
                </div>
            </div>
        </section>

        @if ($pandeaBody !== [])
            <section class="ac-advisory-network" id="advisory-network" aria-labelledby="ac-advisory-network-title">
                <div class="ac-advisory-wide-shell ac-advisory-network-grid">
                    <div class="ac-advisory-network-heading">
                        <h2 id="ac-advisory-network-title" data-words-slide-from-right aria-label="{{ $networkTitle }}">
                            @foreach ($networkTitleLines as $line)
                                <span class="ac-advisory-network-title-line">
                                    @foreach ($headingWords($line) as $word)
                                        <span class="service-title-word animation-index-{{ $loop->parent->index + $loop->index }} {{ $loop->parent->last && $loop->index > 0 ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                                    @endforeach
                                </span>
                            @endforeach
                        </h2>

                        @if ($networkLogoUrl !== '')
                            <div class="ac-advisory-network-logo-card content-reveal" data-image-reveal>
                                <img
                                    src="{{ $networkLogoUrl }}"
                                    alt="{{ $pandea['logo_alt'] ?? 'Pandea Global M&A' }}"
                                    class="ac-advisory-network-logo"
                                    width="380"
                                    height="100"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </div>
                        @endif
                    </div>

                    <div class="ac-advisory-network-copy content-reveal animation-index-1" data-image-reveal>
                        @foreach ($pandeaBody as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="ac-advisory-services" id="advisory-services" aria-labelledby="ac-advisory-services-title">
            <div class="ac-advisory-wide-shell">
                <header class="ac-advisory-section-heading">
                    <h2 id="ac-advisory-services-title" data-words-slide-from-right aria-label="{{ $services['title'] ?? '' }}">
                        @foreach ($headingWords((string) ($services['title'] ?? '')) as $word)
                            <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>
                    @if (trim((string) ($services['intro'] ?? '')) !== '')
                        <p>{{ $services['intro'] }}</p>
                    @endif
                </header>

                <div class="ac-advisory-services-grid">
                    @foreach ($serviceCards as $card)
                        @php $cardUrl = $resolveContentUrl($card['url'] ?? ''); @endphp

                        <a class="ac-advisory-service-card animation-index-{{ $loop->index }}" data-image-reveal href="{{ $cardUrl !== '' ? $cardUrl : '#advisory-services' }}">
                            <span class="ac-advisory-service-icon" aria-hidden="true">
                                <i class="fa-duotone fa-thin fa-fw {{ $serviceIcons[$loop->index] ?? 'fa-chart-network' }}"></i>
                            </span>
                            <h3>{{ $card['title'] ?? '' }}</h3>
                            <p>{{ $card['text'] ?? '' }}</p>
                            <span class="ac-advisory-service-link" aria-hidden="true">
                                {{ $serviceActionLabel }}
                                <i class="fa-duotone fa-thin fa-arrow-right fa-fw"></i>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        @if ($approachBody !== [])
            <section class="ac-advisory-approach" aria-labelledby="ac-advisory-approach-title">
                <div class="ac-advisory-wide-shell ac-advisory-approach-grid">
                    <div class="ac-advisory-approach-heading">
                        <h2 id="ac-advisory-approach-title" data-words-slide-from-right aria-label="{{ $approach['title'] ?? '' }}">
                            @foreach ($headingWords((string) ($approach['title'] ?? '')) as $word)
                                <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                            @endforeach
                        </h2>
                    </div>

                    <blockquote class="ac-advisory-approach-quote content-reveal animation-index-1" data-image-reveal>
                        <i class="fa-duotone fa-thin fa-quote-left" aria-hidden="true"></i>
                        @foreach ($approachBody as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </blockquote>
                </div>
            </section>
        @endif

        @if ($hasAdvisoryPosts)
            <section class="news-section ac-advisory-news" aria-labelledby="ac-advisory-news-title">
                <div class="news-shell">
                    <header class="news-header">
                        <h2 class="news-title" id="ac-advisory-news-title" data-words-slide-from-right aria-label="{{ $blogHeadingTitle }}">
                            @foreach ($headingWords($blogHeadingTitle) as $word)
                                <span class="news-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                            @endforeach
                        </h2>

                        <a class="news-all-link content-reveal" data-image-reveal href="{{ $advisoryArchiveUrl }}">
                            <span>{{ $allPostsLabel }}</span>
                            <i class="fa-duotone fa-thin fa-arrow-right fa-fw" aria-hidden="true"></i>
                        </a>
                    </header>

                    <div class="news-grid">
                        @foreach ($advisoryPosts->take(3) as $post)
                            @php
                                $translation = $post->translations->firstWhere('locale', $locale)
                                    ?? $post->translations->firstWhere('locale', $fallbackLocale);
                                $postSlug = trim((string) ($translation?->slug ?? ''));
                                $postUrl = $postSlug !== '' ? route('blog.show', ['slug' => $postSlug]) : route('blog.index');
                                $postTitle = trim((string) ($translation?->title ?? $post->code));
                                $postExcerpt = trim((string) ($translation?->excerpt ?? '')) ?: __('ui.blog.excerpt_fallback');
                                $postExcerpt = \Illuminate\Support\Str::limit($postExcerpt, 190, '...', true);
                                $primaryCategory = $post->categories
                                    ->sortByDesc(fn ($category) => (int) ($category->pivot->is_primary ?? false))
                                    ->first();
                                $categoryTranslation = $primaryCategory?->translations->firstWhere('locale', $locale)
                                    ?? $primaryCategory?->translations->firstWhere('locale', $fallbackLocale);
                                $categoryLabel = trim((string) ($categoryTranslation?->name ?? ($isCroatian ? 'Novosti' : 'News')));
                            @endphp

                            <a class="news-card animation-index-{{ $loop->index }}" data-image-reveal href="{{ $postUrl }}" aria-label="{{ $isCroatian ? 'Otvori blog post' : 'Open blog post' }}: {{ $postTitle }}">
                                <span class="news-card-category">{{ $categoryLabel }}</span>
                                <h3>{{ $postTitle }}</h3>
                                <p>{{ $postExcerpt }}</p>
                                <span class="news-card-link" aria-hidden="true">
                                    {{ $readMoreLabel }}
                                    <i class="fa-duotone fa-thin fa-arrow-right fa-fw"></i>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="contact-cta ac-advisory-contact-cta" aria-labelledby="ac-advisory-contact-title">
            <div class="contact-cta-shell">
                <div class="contact-cta-copy">
                    <h2 class="contact-cta-title" id="ac-advisory-contact-title" data-words-slide-from-right aria-label="{{ $meetingTitle }}">
                        @foreach ($headingWords($meetingTitle) as $word)
                            <span class="contact-cta-title-word animation-index-{{ $loop->index }} {{ $loop->remaining < 2 ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>
                </div>

                <div class="contact-cta-card" data-image-reveal>
                    <h3 class="contact-cta-card-heading">{{ $meetingCardTitle }}</h3>
                    <p>{{ $meetingIntro }}</p>
                    <a class="contact-cta-button" href="{{ route('contact.create') }}">
                        <span>{{ $meetingButtonLabel }}</span>
                        <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                    </a>
                    <small><span class="contact-cta-status-dot" aria-hidden="true"></span>{{ $meetingStatus }}</small>
                </div>
            </div>
        </section>
    </div>
@endsection
