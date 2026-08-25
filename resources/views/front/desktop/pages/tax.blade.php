@extends('front.desktop.layouts.store')

@php
    $isCroatian = str_starts_with(strtolower((string) ($locale ?? app()->getLocale())), 'hr');
    $heroLabel = trim((string) ($heroSection['brand_title'] ?? '')) ?: 'ALPHA CAPITALIS';
    $heroHook = trim((string) ($heroSection['intro'] ?? ''));
    $heroImageAlt = trim((string) ($heroSection['image_alt'] ?? ''))
        ?: ($isCroatian ? 'Porezno savjetovanje' : 'Tax advisory');
    $taxServices = array_values(array_filter(
        (array) ($servicesSection['items'] ?? []),
        static fn ($item): bool => is_array($item) && trim((string) ($item['title'] ?? '')) !== '',
    ));
    $complianceGroups = array_values((array) data_get($complianceSection, 'corporate.groups', []));
    $editorialSections = array_values(array_filter([
        $reviewSection ?? [],
        $optimizationSection ?? [],
        $dueDiligenceSection ?? [],
        $transferPricingSection ?? [],
    ], static fn ($section): bool => is_array($section) && trim((string) ($section['title'] ?? '')) !== ''));
    $meetingTitle = trim((string) ($meetingSection['title'] ?? ''))
        ?: ($isCroatian ? 'Razgovarajmo o vašim poreznim pitanjima' : 'Let’s discuss your tax questions');
    $meetingIntro = trim((string) ($meetingSection['intro'] ?? ''));
    $meetingCardTitle = trim((string) ($meetingSection['contact_title'] ?? ''))
        ?: ($isCroatian ? 'Kontaktirajte nas' : 'Contact us');
    $meetingButtonLabel = trim((string) ($meetingSection['submit'] ?? ''))
        ?: ($isCroatian ? 'Pošaljite upit' : 'Send an inquiry');
    $meetingPhoneLabel = trim((string) ($meetingSection['direct_phone_label'] ?? ''))
        ?: ($isCroatian ? 'Telefon' : 'Phone');
    $contactPhone = trim((string) ($storeSettings['footer']['phone'] ?? '')) ?: '+385 (1) 580 6656';
    $contactPhoneHref = preg_replace('/[^0-9+]/', '', $contactPhone);
    $blogHeadingTitle = trim((string) ($blogSection['title'] ?? ''))
        ?: ($isCroatian ? 'Najnovije porezne objave' : 'Latest tax insights');
    $headingWords = static fn (string $heading): array => preg_split('/\s+/u', trim($heading)) ?: [];
    $sameOriginAssetUrl = static function (?string $url): string {
        $assetUrl = trim((string) $url);
        $assetHost = parse_url($assetUrl, PHP_URL_HOST);

        if ($assetUrl === '' || ($assetHost !== null && $assetHost !== request()->getHost())) {
            return $assetUrl;
        }

        $assetPath = parse_url($assetUrl, PHP_URL_PATH);
        $assetQuery = parse_url($assetUrl, PHP_URL_QUERY);

        return is_string($assetPath) && $assetPath !== ''
            ? $assetPath.($assetQuery ? '?'.$assetQuery : '')
            : $assetUrl;
    };
    $heroImageUrl = $sameOriginAssetUrl((string) ($heroBackgroundUrl ?? ''));
@endphp

@section('title', $servicePageMetaTitle !== '' ? $servicePageMetaTitle : ($servicePageTitle ?? $heroLabel))
@section('main_class', 'w-full px-0 py-0')

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/audit.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/audit.css')) }}">
@endpush

@section('content')
    <div class="ac-audit-page ac-tax-page">
        <section class="ac-audit-hero" aria-labelledby="ac-tax-hero-title">
            <div class="ac-audit-hero-media">
                <img
                    src="{{ $heroImageUrl }}"
                    alt="{{ $heroImageAlt }}"
                    class="ac-audit-hero-image"
                    width="1366"
                    height="768"
                    loading="eager"
                    decoding="async"
                    fetchpriority="high"
                >
            </div>
            <div class="ac-audit-hero-overlay" aria-hidden="true"></div>

            <div class="ac-audit-hero-shell">
                <div class="ac-audit-hero-copy">
                    <h1 id="ac-tax-hero-title" aria-label="{{ $heroLabel }}. {{ $heroHook }}">
                        <span class="ac-audit-hero-label">{{ $heroLabel }}</span>
                        @if ($heroHook !== '')
                            <span class="ac-audit-hero-hook">{{ $heroHook }}</span>
                        @endif
                    </h1>
                </div>
            </div>
        </section>

        <section class="ac-audit-intro" id="tax-overview" aria-labelledby="ac-tax-overview-title">
            <div class="ac-audit-wide-shell ac-audit-intro-grid">
                <div class="ac-audit-intro-heading">
                    <h2 id="ac-tax-overview-title" data-words-slide-from-right aria-label="{{ $overviewSection['title'] ?? '' }}">
                        @foreach ($headingWords((string) ($overviewSection['title'] ?? '')) as $word)
                            <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>
                </div>

                <div class="ac-audit-intro-copy content-reveal animation-index-1" data-image-reveal>
                    @if (trim((string) ($overviewSection['intro'] ?? '')) !== '')
                        <p>{{ $overviewSection['intro'] }}</p>
                    @endif
                    @foreach ((array) ($overviewSection['body'] ?? []) as $paragraph)
                        @if (trim((string) $paragraph) !== '')
                            <p>{{ $paragraph }}</p>
                        @endif
                    @endforeach
                    @if (trim((string) ($overviewSection['highlight_title'] ?? '')) !== '')
                        <p class="is-emphasis">{{ $overviewSection['highlight_title'] }}</p>
                    @endif
                    @if ((array) ($overviewSection['highlights'] ?? []) !== [])
                        <ul class="ac-tax-copy-list">
                            @foreach ((array) $overviewSection['highlights'] as $highlight)
                                <li>{{ $highlight }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </section>

        <section class="ac-audit-services" aria-labelledby="ac-tax-services-title">
            <div class="ac-audit-wide-shell">
                <header class="ac-audit-section-heading">
                    <h2 id="ac-tax-services-title" data-words-slide-from-right aria-label="{{ $servicesSection['title'] ?? '' }}">
                        @foreach ($headingWords((string) ($servicesSection['title'] ?? '')) as $word)
                            <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>
                    @if (trim((string) ($servicesSection['intro'] ?? '')) !== '')
                        <p>{{ $servicesSection['intro'] }}</p>
                    @endif
                </header>

                <div class="ac-audit-services-grid">
                    @foreach ($taxServices as $item)
                        <article class="ac-audit-service-card content-reveal animation-index-{{ $loop->index }}" data-image-reveal>
                            <span class="ac-audit-service-icon" aria-hidden="true">
                                <i class="fa-duotone fa-thin fa-fw {{ ['fa-file-certificate', 'fa-scale-balanced', 'fa-file-magnifying-glass', 'fa-chart-line-up', 'fa-magnifying-glass-dollar', 'fa-arrows-left-right-to-line'][$loop->index] ?? 'fa-badge-check' }}"></i>
                            </span>
                            <h3>{{ $item['title'] ?? '' }}</h3>
                            <p>{{ $item['text'] ?? '' }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="ac-audit-obligors" aria-labelledby="ac-tax-compliance-title">
            <div class="ac-audit-wide-shell ac-audit-obligors-grid">
                <div class="ac-audit-obligors-heading">
                    <h2 id="ac-tax-compliance-title" data-words-slide-from-right aria-label="{{ $complianceSection['title'] ?? '' }}">
                        @foreach ($headingWords((string) ($complianceSection['title'] ?? '')) as $word)
                            <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>
                </div>

                @if (trim((string) ($complianceSection['intro'] ?? '')) !== '')
                    <aside class="ac-audit-obligors-note content-reveal animation-index-1" data-image-reveal>
                        <p>{{ $complianceSection['intro'] }}</p>
                    </aside>
                @endif

                <div class="ac-audit-obligors-content content-reveal animation-index-1" data-image-reveal>
                    <ul class="ac-audit-obligors-list">
                        @foreach ($complianceGroups as $group)
                            <li class="ac-audit-obligor-card ac-audit-obligor-card--wide">
                                <span class="ac-audit-obligor-icon" aria-hidden="true">
                                    <i class="fa-duotone fa-thin fa-fw fa-building-columns"></i>
                                </span>
                                <div class="ac-audit-obligor-copy">
                                    <span class="ac-audit-obligor-title">{{ $group['title'] ?? '' }}</span>
                                    <ul class="ac-tax-compliance-list">
                                        @foreach ((array) ($group['items'] ?? []) as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                        @endforeach

                        @if (trim((string) data_get($complianceSection, 'individual.title', '')) !== '')
                            <li class="ac-audit-obligor-card ac-audit-obligor-card--wide">
                                <span class="ac-audit-obligor-icon" aria-hidden="true">
                                    <i class="fa-duotone fa-thin fa-fw fa-user-tie"></i>
                                </span>
                                <div class="ac-audit-obligor-copy">
                                    <span class="ac-audit-obligor-title">{{ data_get($complianceSection, 'individual.title') }}</span>
                                    <p>{{ data_get($complianceSection, 'individual.intro') }}</p>
                                    <ul class="ac-tax-compliance-list">
                                        @foreach ((array) data_get($complianceSection, 'individual.items', []) as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </section>

        @foreach ($editorialSections as $section)
            <section class="ac-audit-intro ac-tax-editorial-section" aria-labelledby="ac-tax-editorial-{{ $loop->index }}">
                <div class="ac-audit-wide-shell ac-audit-intro-grid">
                    <div class="ac-audit-intro-heading">
                        <h2 id="ac-tax-editorial-{{ $loop->index }}" data-words-slide-from-right aria-label="{{ $section['title'] ?? '' }}">
                            @foreach ($headingWords((string) ($section['title'] ?? '')) as $word)
                                <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                            @endforeach
                        </h2>
                    </div>

                    <div class="ac-audit-intro-copy content-reveal animation-index-1" data-image-reveal>
                        @if (trim((string) ($section['intro'] ?? '')) !== '')
                            <p class="is-emphasis">{{ $section['intro'] }}</p>
                        @endif
                        @foreach ((array) ($section['body'] ?? []) as $paragraph)
                            @if (trim((string) $paragraph) !== '')
                                <p>{{ $paragraph }}</p>
                            @endif
                        @endforeach
                        @if ((array) ($section['highlights'] ?? []) !== [])
                            <ul class="ac-tax-copy-list">
                                @foreach ((array) $section['highlights'] as $highlight)
                                    <li>{{ $highlight }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </section>
        @endforeach

        @include('front.desktop.partials.service-videos', [
            'serviceVideoSection' => $serviceVideoSection ?? [],
            'serviceVideos' => $serviceVideos ?? [],
            'locale' => $locale ?? app()->getLocale(),
        ])

        <section class="news-section ac-audit-news" data-tax-blog-splide aria-labelledby="ac-tax-news-title">
                <div class="news-shell">
                    <header class="news-header">
                        <div class="ac-audit-news-heading-copy">
                            <h2 class="news-title" id="ac-tax-news-title">{{ $blogHeadingTitle }}</h2>
                            @if (trim((string) ($blogSection['intro'] ?? '')) !== '')
                                <p>{{ $blogSection['intro'] }}</p>
                            @endif
                        </div>

                        <a class="news-all-link content-reveal" data-image-reveal href="{{ $taxArchiveUrl }}">
                            <span>{{ $isCroatian ? 'Pogledaj sve objave' : 'View all posts' }}</span>
                            <i class="fa-duotone fa-thin fa-arrow-right fa-fw" aria-hidden="true"></i>
                        </a>
                    </header>

                    <div class="news-grid">
                        @foreach ($taxPosts->take(3) as $post)
                            @php
                                $translation = $post->translations->firstWhere('locale', $locale)
                                    ?? $post->translations->firstWhere('locale', $fallbackLocale);
                                $postSlug = trim((string) ($translation?->slug ?? ''));
                                $postUrl = $postSlug !== '' ? route('blog.show', ['slug' => $postSlug]) : route('blog.index');
                                $postTitle = trim((string) ($translation?->title ?? $post->code));
                            @endphp
                            <a class="news-card animation-index-{{ $loop->index }}" data-image-reveal href="{{ $postUrl }}">
                                <span class="news-card-category">{{ $taxCategoryName }}</span>
                                <h3>{{ $postTitle }}</h3>
                                <p>{{ \Illuminate\Support\Str::limit((string) ($translation?->excerpt ?? ''), 190, '...', true) }}</p>
                                <span class="news-card-link" aria-hidden="true">
                                    {{ $isCroatian ? 'Opširnije' : 'Read more' }}
                                    <i class="fa-duotone fa-thin fa-arrow-right fa-fw"></i>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
        </section>

        <section class="contact-cta ac-audit-contact-cta" aria-labelledby="ac-tax-contact-title">
            <div class="contact-cta-shell">
                <div class="contact-cta-copy">
                    <h2 class="contact-cta-title" id="ac-tax-contact-title">{{ $meetingTitle }}</h2>
                </div>

                <div class="contact-cta-card" data-image-reveal>
                    <h3 class="contact-cta-card-heading">{{ $meetingCardTitle }}</h3>
                    @if ($meetingIntro !== '')
                        <p>{{ $meetingIntro }}</p>
                    @endif
                    <p class="ac-tax-contact-phone">
                        <span>{{ $meetingPhoneLabel }}:</span>
                        <a href="tel:{{ $contactPhoneHref }}">{{ $contactPhone }}</a>
                    </p>
                    <a class="contact-cta-button" href="{{ \App\Support\Localization\FrontendRoute::url('contact.create') }}">
                        <span>{{ $meetingButtonLabel }}</span>
                        <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
