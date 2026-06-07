@extends('front.desktop.layouts.store')

@php
    $content = (array) ($advisoryContent ?? []);
    $heroSection = (array) ($content['hero'] ?? []);
    $overview = (array) ($content['overview'] ?? []);
    $servicesIntro = (array) ($content['services_intro'] ?? []);
    $serviceCards = array_values((array) ($content['service_cards'] ?? []));
    $pandea = (array) ($content['pandea'] ?? []);
    $funding = (array) ($content['funding'] ?? []);
    $sourceModules = (array) ($content['source_modules'] ?? []);
    $bankLoans = (array) ($content['bank_loans'] ?? []);
    $zopu = (array) ($content['zopu'] ?? []);
    $ma = (array) ($content['ma'] ?? []);
    $valuations = (array) ($content['valuations'] ?? []);
    $dueDiligence = (array) ($content['due_diligence'] ?? []);
    $tax = (array) ($content['tax'] ?? []);
    $approach = (array) ($content['approach'] ?? []);
    $meeting = (array) ($content['meeting'] ?? []);
    $blog = (array) ($content['blog_section'] ?? ($content['blog'] ?? []));
    $pandeaLogo = trim((string) ($pandeaLogoUrl ?? ''));
    $isCroatianLocale = str_starts_with(strtolower((string) ($locale ?? app()->getLocale())), 'hr');
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
    $resolveContentUrl = static function (?string $url): string {
        $target = trim((string) $url);

        if ($target === '' || str_starts_with($target, '#') || str_starts_with($target, 'http://') || str_starts_with($target, 'https://')) {
            return $target;
        }

        return url(str_starts_with($target, '/') ? $target : '/'.$target);
    };
    $heroImageUrl = $sameOriginAssetUrl((string) $heroBackgroundUrl);
    $hasAdvisoryPosts = ($advisoryPosts ?? collect())->isNotEmpty();
    $hasServiceVideos = collect($serviceVideos ?? [])->isNotEmpty();
@endphp

@section('title', $servicePageMetaTitle !== '' ? $servicePageMetaTitle : ($servicePageTitle ?? 'Savjetovanje'))
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-family-business-page ac-audit-page ac-advisory-page">
        <section class="ac-family-hero ac-service-hero ac-service-hero--advisory">
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
                                    <span class="is-subtitle-lead">{{ $heroSection['subtitle_lead'] ?? 'Poslovno' }}</span>
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

        <section id="advisory-overview" class="ac-audit-editorial-wrap" aria-labelledby="ac-advisory-overview-title">
            <div class="mx-auto w-full max-w-[1120px] px-5 lg:px-8">
                <div class="ac-audit-editorial-shell">
                    <article class="ac-audit-editorial-section ac-audit-editorial-section--overview">
                        <div class="ac-audit-section-head ac-audit-section-head--center">
                            <p class="ac-family-section-kicker">{{ $overview['kicker'] ?? 'ŠTO JE POSLOVNO SAVJETOVANJE?' }}</p>
                            <h2 id="ac-advisory-overview-title">{{ $overview['title'] ?? '' }}</h2>
                        </div>

                        <div class="ac-audit-copy ac-audit-copy--full ac-advisory-copy">
                            @foreach ((array) ($overview['body'] ?? []) as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </article>

                    <article id="advisory-usluge" class="ac-audit-editorial-section">
                        <div class="ac-audit-section-head ac-audit-section-head--center">
                            <p class="ac-family-section-kicker">{{ $servicesIntro['kicker'] ?? 'USLUGE POSLOVNOG SAVJETOVANJA' }}</p>
                            <h2>{{ $servicesIntro['title'] ?? 'Naše usluge' }}</h2>
                            @if (trim((string) ($servicesIntro['intro'] ?? '')) !== '')
                                <p>{{ $servicesIntro['intro'] }}</p>
                            @endif
                        </div>

                        <div class="ac-audit-card-grid">
                            @foreach ($serviceCards as $card)
                                @php $cardUrl = $resolveContentUrl($card['url'] ?? ''); @endphp
                                <article class="ac-audit-service-card ac-advisory-link-card">
                                    <h3>{{ $card['title'] ?? '' }}</h3>
                                    <p>{{ $card['text'] ?? '' }}</p>
                                    @if ($cardUrl !== '')
                                        <a href="{{ $cardUrl }}" class="ac-advisory-card-link">{{ $readMoreLabel }}</a>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </article>

                    <article class="ac-audit-editorial-section">
                        <div class="ac-advisory-network-panel">
                            <div class="ac-advisory-network-head">
                                @if ($pandeaLogo !== '')
                                    <div class="ac-advisory-network-logo-card">
                                        <img src="{{ $pandeaLogo }}" alt="{{ $pandea['logo_alt'] ?? 'Pandea Global M&A' }}" class="ac-advisory-network-logo" loading="lazy" decoding="async">
                                    </div>
                                @endif
                                <div>
                                    <p class="ac-family-section-kicker">Pandea Global M&amp;A</p>
                                    <h2>{{ $pandea['title'] ?? '' }}</h2>
                                </div>
                            </div>
                            <div class="ac-advisory-network-copy">
                                @foreach ((array) ($pandea['body'] ?? []) as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </div>
                        </div>
                    </article>

                    <article id="advisory-pribavljanje-financiranja" class="ac-audit-editorial-section">
                        <div class="ac-audit-section-head ac-audit-section-head--center">
                            <p class="ac-family-section-kicker">PRIBAVLJANJE FINANCIRANJA</p>
                            <h2>{{ $funding['title'] ?? 'Pribavljanje financiranja' }}</h2>
                            @if (trim((string) ($funding['intro'] ?? '')) !== '')
                                <p>{{ $funding['intro'] }}</p>
                            @endif
                        </div>

                        <div class="ac-advisory-three-grid">
                            @foreach ((array) ($funding['cards'] ?? []) as $card)
                                @php $cardUrl = $resolveContentUrl($card['url'] ?? ''); @endphp
                                <article class="ac-audit-service-card ac-advisory-link-card">
                                    <h3>{{ $card['title'] ?? '' }}</h3>
                                    <p>{{ $card['text'] ?? '' }}</p>
                                    @if ($cardUrl !== '')
                                        <a href="{{ $cardUrl }}" class="ac-advisory-card-link">{{ $readMoreLabel }}</a>
                                    @endif
                                </article>
                            @endforeach
                        </div>

                        <div class="ac-advisory-feature-block">
                            <h3>{{ $funding['overview_title'] ?? 'EU fondovi' }}</h3>
                            @foreach ((array) ($funding['overview_body'] ?? []) as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>

                        <div class="ac-audit-section-head ac-audit-section-head--center ac-advisory-subhead">
                            <h3>{{ $funding['services_title'] ?? 'Naše usluge' }}</h3>
                        </div>
                        <div class="ac-audit-card-grid">
                            @foreach ((array) ($funding['services'] ?? []) as $item)
                                <article class="ac-audit-service-card">
                                    <h3>{{ $item['title'] ?? '' }}</h3>
                                    <p>{{ $item['text'] ?? '' }}</p>
                                </article>
                            @endforeach
                        </div>

                        @if (! empty($funding['advisory_cards'] ?? []))
                            <div class="ac-advisory-three-grid">
                                @foreach ((array) ($funding['advisory_cards'] ?? []) as $card)
                                    <article class="ac-audit-service-card">
                                        <h3>{{ $card['title'] ?? '' }}</h3>
                                        <p>{{ $card['text'] ?? '' }}</p>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </article>

                    <article class="ac-audit-editorial-section">
                        <div class="ac-advisory-two-col">
                            <section id="advisory-bankovni-krediti" class="ac-advisory-text-panel">
                                <h2>{{ $bankLoans['title'] ?? 'Bankovni krediti' }}</h2>
                                @foreach ((array) ($bankLoans['body'] ?? []) as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </section>

                            <section id="advisory-zopu" class="ac-advisory-text-panel">
                                <h2>{{ $zopu['title'] ?? 'Zakon o poticanju ulaganja' }}</h2>
                                @foreach ((array) ($zopu['body'] ?? []) as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </section>
                        </div>
                    </article>

                    <article class="ac-audit-editorial-section">
                        <div class="ac-audit-section-head ac-audit-section-head--center">
                            <p class="ac-family-section-kicker">{{ $sourceModules['kicker'] ?? 'DOSTUPNI IZVORI FINANCIRANJA' }}</p>
                            <h2>{{ $sourceModules['title'] ?? '' }}</h2>
                            @if (trim((string) ($sourceModules['intro'] ?? '')) !== '')
                                <p>{{ $sourceModules['intro'] }}</p>
                            @endif
                        </div>

                        <div class="ac-advisory-module-grid">
                            @foreach ((array) ($sourceModules['items'] ?? []) as $module)
                                @php $moduleUrl = $resolveContentUrl($module['url'] ?? ''); @endphp
                                <article class="ac-advisory-source-card">
                                    <h3>{{ $module['title'] ?? '' }}</h3>
                                    <p>{{ $module['text'] ?? '' }}</p>
                                    @if ($moduleUrl !== '')
                                        <a href="{{ $moduleUrl }}" class="ac-advisory-card-link">{{ $readMoreLabel }}</a>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </article>

                    <article id="advisory-ma" class="ac-audit-editorial-section">
                        <div class="ac-audit-section-head ac-audit-section-head--center">
                            <p class="ac-family-section-kicker">M&amp;A SAVJETOVANJE</p>
                            <h2>{{ $ma['title'] ?? 'Spajanja i preuzimanja (M&A)' }}</h2>
                            @if (trim((string) ($ma['intro'] ?? '')) !== '')
                                <p>{{ $ma['intro'] }}</p>
                            @endif
                        </div>

                        <div class="ac-advisory-two-col">
                            <article class="ac-audit-service-card">
                                <h3>{{ $ma['sale']['title'] ?? 'Prodaja poduzeća' }}</h3>
                                <p>{{ $ma['sale']['body'] ?? '' }}</p>
                            </article>
                            <article class="ac-audit-service-card">
                                <h3>{{ $ma['acquisition']['title'] ?? 'Kupnja poduzeća' }}</h3>
                                <p>{{ $ma['acquisition']['body'] ?? '' }}</p>
                            </article>
                        </div>
                    </article>

                    <article class="ac-audit-editorial-section">
                        <div class="ac-advisory-two-col">
                            <section id="advisory-due-diligence" class="ac-advisory-text-panel">
                                <p class="ac-family-section-kicker">DUE DILIGENCE</p>
                                <h2>{{ $dueDiligence['title'] ?? 'Due diligence' }}</h2>
                                @if (trim((string) ($dueDiligence['intro'] ?? '')) !== '')
                                    <p>{{ $dueDiligence['intro'] }}</p>
                                @endif
                                <h3>{{ $dueDiligence['help_title'] ?? 'Pomažemo vam:' }}</h3>
                                <ul class="ac-advisory-list">
                                    @foreach ((array) ($dueDiligence['help_items'] ?? []) as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                                @if (trim((string) ($dueDiligence['closing'] ?? '')) !== '')
                                    <p>{{ $dueDiligence['closing'] }}</p>
                                @endif
                            </section>

                            <section id="advisory-procjene-vrijednosti" class="ac-advisory-text-panel">
                                <p class="ac-family-section-kicker">PROCJENE VRIJEDNOSTI</p>
                                <h2>{{ $valuations['title'] ?? 'Procjene vrijednosti' }}</h2>
                                @foreach ((array) ($valuations['body'] ?? []) as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                                <h3>{{ $valuations['methods_title'] ?? 'Metode vrednovanja' }}</h3>
                                <ul class="ac-advisory-list">
                                    @foreach ((array) ($valuations['methods'] ?? []) as $method)
                                        <li>{{ $method }}</li>
                                    @endforeach
                                </ul>
                            </section>
                        </div>
                    </article>

                    <article id="advisory-porezno-savjetovanje" class="ac-audit-editorial-section">
                        <div class="ac-audit-section-head ac-audit-section-head--center">
                            <p class="ac-family-section-kicker">POREZNO SAVJETOVANJE</p>
                            <h2>{{ $tax['title'] ?? 'Porezno savjetovanje' }}</h2>
                        </div>

                        <div class="ac-advisory-feature-block">
                            <h3>{{ $tax['overview_title'] ?? 'Što je porezno savjetovanje?' }}</h3>
                            @foreach ((array) ($tax['overview_body'] ?? []) as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>

                        <div class="ac-audit-section-head ac-audit-section-head--center ac-advisory-subhead">
                            <h3>{{ $tax['services_title'] ?? 'Naše porezne usluge' }}</h3>
                        </div>

                        <div class="ac-audit-card-grid">
                            @foreach ((array) ($tax['services'] ?? []) as $item)
                                <article class="ac-audit-service-card">
                                    <h3>{{ $item['title'] ?? '' }}</h3>
                                    <p>{{ $item['text'] ?? '' }}</p>
                                </article>
                            @endforeach
                        </div>

                        <div class="ac-audit-card-grid">
                            @foreach ((array) ($tax['cards'] ?? []) as $card)
                                <article class="ac-audit-service-card">
                                    <h3>{{ $card['title'] ?? '' }}</h3>
                                    <p>{{ $card['text'] ?? '' }}</p>
                                </article>
                            @endforeach
                        </div>

                        <div class="ac-advisory-feature-block">
                            <h3>{{ $tax['approach_title'] ?? '' }}</h3>
                            @foreach ((array) ($tax['approach_body'] ?? []) as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </article>

                    <article class="ac-audit-editorial-section">
                        <div class="ac-audit-section-head ac-audit-section-head--center">
                            <p class="ac-family-section-kicker">{{ $approach['kicker'] ?? 'NAŠ PRISTUP' }}</p>
                            <h2>{{ $approach['title'] ?? '' }}</h2>
                        </div>

                        <div class="ac-advisory-approach-panel">
                            <div class="ac-audit-copy ac-audit-copy--full ac-advisory-copy">
                                @foreach ((array) ($approach['body'] ?? []) as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        @include('front.desktop.partials.service-videos', [
            'serviceVideoSection' => $serviceVideoSection ?? [],
            'serviceVideos' => $serviceVideos ?? [],
            'locale' => $locale ?? app()->getLocale(),
        ])

        <section id="advisory-sastanak" class="ac-service-cta-section" aria-labelledby="ac-advisory-meeting-title">
            <div class="ac-service-cta-container">
                <div class="ac-service-cta-card">
                    <div class="ac-service-cta-copy">
                        <p class="ac-family-section-kicker">{{ $meeting['kicker'] ?? 'KONTAKT' }}</p>
                        <h2 id="ac-advisory-meeting-title">{{ $meeting['title'] ?? 'Razgovarajmo o poslovnom savjetovanju' }}</h2>
                        <p>{{ $meeting['intro'] ?? '' }}</p>
                    </div>

                    <a href="{{ route('contact.create') }}" class="ac-service-cta-link">
                        <span>{{ $meeting['contact_title'] ?? 'Kontaktirajte nas' }}</span>
                    </a>
                </div>
            </div>
        </section>

        @if ($hasAdvisoryPosts)
            <section class="ac-support-story ac-home-blog ac-blog-related-section ac-family-blog-section ac-audit-blog-section ac-advisory-blog-section" aria-labelledby="ac-advisory-blog-title">
                <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                    <div class="ac-support-story-hero">
                        <div class="ac-support-story-shell">
                            <div class="ac-services-head ac-support-story-head">
                                <p class="ac-family-section-kicker">{{ str_starts_with(strtolower((string) ($locale ?? app()->getLocale())), 'hr') ? 'NAJNOVIJE OBJAVE' : 'LATEST POSTS' }}</p>
                                <h2 id="ac-advisory-blog-title">
                                    <span>{{ $blog['title'] ?? 'Savjetovanje' }}</span>
                                </h2>
                                <p class="ac-services-intro">{{ $blog['intro'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="ac-home-blog-carousel">
                        <div id="ac-advisory-blog-splide" class="splide ac-home-blog-splide" data-advisory-blog-splide>
                            <div class="splide__track">
                                <ul class="splide__list">
                                    @foreach ($advisoryPosts as $post)
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

@if ($hasAdvisoryPosts || $hasServiceVideos)
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

@if ($hasAdvisoryPosts)
    @push('scripts')
        <script>
            (function () {
                const initAdvisoryBlogSlider = function () {
                    if (typeof window.Splide !== 'function') {
                        return false;
                    }

                    document.querySelectorAll('[data-advisory-blog-splide]').forEach(function (el) {
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

                if (initAdvisoryBlogSlider()) {
                    return;
                }

                let attempts = 0;
                const timer = window.setInterval(function () {
                    attempts += 1;
                    if (initAdvisoryBlogSlider() || attempts > 40) {
                        window.clearInterval(timer);
                    }
                }, 120);
            }());
        </script>
    @endpush
@endif
