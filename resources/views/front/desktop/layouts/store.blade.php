<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('front.partials.seo-meta')
    @include('front.partials.schema-markup')
    @include('front.partials.analytics')
    @if (!empty($storeSettings['branding']['favicons']['ico_url'] ?? null))
        <link rel="icon" href="{{ $storeSettings['branding']['favicons']['ico_url'] }}" sizes="any">
    @endif
    @if (!empty($storeSettings['branding']['favicons']['32_url'] ?? null))
        <link rel="icon" type="image/png" sizes="32x32" href="{{ $storeSettings['branding']['favicons']['32_url'] }}">
    @endif
    @if (!empty($storeSettings['branding']['favicons']['16_url'] ?? null))
        <link rel="icon" type="image/png" sizes="16x16" href="{{ $storeSettings['branding']['favicons']['16_url'] }}">
    @endif
    @if (!empty($storeSettings['branding']['favicons']['180_url'] ?? null))
        <link rel="apple-touch-icon" sizes="180x180" href="{{ $storeSettings['branding']['favicons']['180_url'] }}">
    @endif
    @if (!empty($storeSettings['branding']['favicons']['192_url'] ?? null))
        <link rel="icon" type="image/png" sizes="192x192" href="{{ $storeSettings['branding']['favicons']['192_url'] }}">
    @endif
    @if (!empty($storeSettings['branding']['favicons']['512_url'] ?? null))
        <link rel="icon" type="image/png" sizes="512x512" href="{{ $storeSettings['branding']['favicons']['512_url'] }}">
    @endif
    @if (empty($storeSettings['branding']['favicons']['ico_url'] ?? null) && !empty($storeSettings['branding']['favicon_url'] ?? null))
        <link rel="icon" href="{{ $storeSettings['branding']['favicon_url'] }}">
    @endif
    <style>
        body.front-preload-pending {
            background: #030b17;
        }

        #front-initial-preloader {
            position: fixed;
            inset: 0;
            z-index: 120;
            pointer-events: none;
            opacity: 1;
            visibility: visible;
            transition: opacity 0.22s ease, visibility 0.22s ease;
            background:
                radial-gradient(120% 160% at 82% -44%, rgba(4, 86, 146, 0.28), transparent 58%),
                linear-gradient(90deg, #050607 0%, #07090c 30%, #07213a 58%, #0a3d64 100%);
        }

        #front-initial-preloader.is-hidden {
            opacity: 0;
            visibility: hidden;
        }
    </style>
    @if (request()->routeIs('home'))
        <link
            rel="preload"
            as="image"
            href="{{ asset('alpha/alpha-zagreb-poster-640.webp') }}"
            imagesrcset="{{ asset('alpha/alpha-zagreb-poster-640.webp') }} 640w, {{ asset('alpha/alpha-zagreb-poster-1280.webp') }} 1280w"
            imagesizes="100vw"
            type="image/webp"
            fetchpriority="high"
        >
    @endif
    <script>
        window.CodexSearchLabels = {{ \Illuminate\Support\Js::from([
            'autosuggestEmpty' => __('ui.search.autosuggest_empty'),
            'viewAll' => __('ui.search.view_all'),
            'showMore' => __('ui.search.show_more'),
        ]) }};
    </script>
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="{{ asset('front-theme/styles/alpha-redesign.css') }}?v={{ filemtime(public_path('front-theme/styles/alpha-redesign.css')) }}">
    @foreach (['fontawesome.min.css', 'duotone-thin.min.css', 'light.min.css', 'brands.min.css'] as $fontAwesomeStylesheet)
        <link rel="stylesheet" href="{{ asset('fontawesome-pro-7.3.1-web/css/'.$fontAwesomeStylesheet) }}" media="print" data-deferred-stylesheet>
    @endforeach
    <noscript>
        @foreach (['fontawesome.min.css', 'duotone-thin.min.css', 'light.min.css', 'brands.min.css'] as $fontAwesomeStylesheet)
            <link rel="stylesheet" href="{{ asset('fontawesome-pro-7.3.1-web/css/'.$fontAwesomeStylesheet) }}">
        @endforeach
    </noscript>
    @stack('styles')
    <style>
        .front-desktop-shell {
            --front-cream-title-bg: #f6f1e7;
            --front-fixed-watermark: url("{{ asset('front-theme/images/bck-logo.png') }}");
            --front-fixed-watermark-size: clamp(38rem, 58vw, 70rem);
            --front-fixed-watermark-opacity: 0.42;
        }

        .front-desktop-shell :is(
            .ac-about-page,
            .ac-team-page,
            .ac-references-page,
            .ac-career-page,
            .ac-contact-page,
            .ac-assessment-page,
            .ac-lease-page,
            .ac-blog-page,
            .ac-family-business-page
        ) {
            position: relative;
        }

        .front-desktop-shell :is(
            .ac-family-hero,
            .ac-service-hero,
            .front-hero-video-section
        ) {
            position: relative;
            z-index: 4;
        }

        .front-fixed-watermark {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 2;
            overflow: hidden;
            pointer-events: none;
        }

        .front-desktop-shell:has(:is(
            .ac-about-page,
            .ac-team-page,
            .ac-references-page,
            .ac-career-page,
            .ac-contact-page,
            .ac-assessment-page,
            .ac-lease-page,
            .ac-blog-page,
            .ac-family-business-page
        )) .front-fixed-watermark {
            display: block;
        }

        .front-fixed-watermark::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            width: var(--front-fixed-watermark-size);
            aspect-ratio: 1 / 1;
            transform: translate(-18%, -50%);
            background-image: var(--front-fixed-watermark);
            background-position: center center;
            background-repeat: no-repeat;
            background-size: contain;
            opacity: var(--front-fixed-watermark-opacity);
            pointer-events: none;
        }

        .front-content-shell {
            isolation: isolate;
            position: relative;
            z-index: 1;
        }

        .front-desktop-shell:has(:is(
            .ac-about-page,
            .ac-team-page,
            .ac-references-page,
            .ac-career-page,
            .ac-contact-page,
            .ac-assessment-page,
            .ac-lease-page,
            .ac-blog-page,
            .ac-family-business-page
        )) .front-content-shell {
            background: transparent;
        }

        .front-header-meta,
        .front-site-header,
        .front-footer {
            position: relative;
            z-index: 3;
        }

        .front-desktop-shell :is(
            .ac-about-page .ac-about-title-band,
            .ac-team-page .ac-team-title-band,
            .ac-references-page .ac-references-title-band,
            .ac-career-page .ac-career-title-band,
            .ac-contact-page .ac-contact-title-band,
            .ac-assessment-page .ac-assessment-title-band,
            .ac-lease-page .ac-lease-title-band,
            .ac-blog-page .ac-blog-title-band
        ) {
            background-image: none;
            isolation: auto;
        }

        .front-desktop-shell :is(
            .ac-about-page .ac-about-title-band,
            .ac-team-page .ac-team-title-band,
            .ac-references-page .ac-references-title-band,
            .ac-career-page .ac-career-title-band,
            .ac-contact-page .ac-contact-title-band,
            .ac-assessment-page .ac-assessment-title-band,
            .ac-lease-page .ac-lease-title-band,
            .ac-blog-page .ac-blog-title-band
        ) :is(
            .ac-page-title-hero,
            .ac-page-title-breadcrumb,
            .ac-page-title-panel,
            .ac-page-title-copy,
            .ac-blog-article-head,
            .ac-blog-article-title,
            .ac-blog-article-meta
        ) {
            position: relative;
            z-index: 5;
        }

        .front-desktop-shell :is(
            .ac-about-title-band,
            .ac-about-hero,
            .ac-about-values,
            .ac-about-why,
            .ac-about-team,
            .ac-about-culture,
            .ac-about-responsibility,
            .ac-about-references,
            .ac-team-title-band,
            .ac-team-section,
            .ac-references-title-band,
            .ac-references-section,
            .ac-career-title-band,
            .ac-career-hero,
            .ac-career-impact,
            .ac-career-stories,
            .ac-career-openings,
            .ac-contact-title-band,
            .front-contact-offices-shell,
            .front-contact-content-shell,
            .front-contact-map-shell,
            .ac-assessment-title-band,
            .ac-lease-title-band,
            .ac-lease-shell,
            .ac-lease-results,
            .ac-blog-title-band,
            .ac-blog-list-shell,
            .ac-blog-article-shell,
            .ac-blog-related-section,
            .ac-inline-cta,
            .ac-audit-editorial-wrap,
            .ac-service-cta-section,
            .ac-audit-blog-section,
            .ac-family-section
        ) {
            position: relative;
            overflow: hidden;
        }

        .front-desktop-shell :is(
            .ac-about-title-band,
            .ac-about-hero,
            .ac-about-values,
            .ac-about-why,
            .ac-about-team,
            .ac-about-culture,
            .ac-about-responsibility,
            .ac-about-references,
            .ac-team-title-band,
            .ac-team-section,
            .ac-references-title-band,
            .ac-references-section,
            .ac-career-title-band,
            .ac-career-hero,
            .ac-career-impact,
            .ac-career-stories,
            .ac-career-openings,
            .ac-contact-title-band,
            .front-contact-offices-shell,
            .front-contact-content-shell,
            .front-contact-map-shell,
            .ac-assessment-title-band,
            .ac-lease-title-band,
            .ac-lease-shell,
            .ac-lease-results,
            .ac-blog-title-band,
            .ac-blog-list-shell,
            .ac-blog-article-shell,
            .ac-blog-related-section,
            .ac-inline-cta,
            .ac-audit-editorial-wrap,
            .ac-service-cta-section,
            .ac-audit-blog-section,
            .ac-family-section
        ) > * {
            position: relative;
            z-index: 5;
        }

        .front-desktop-shell .ac-inline-cta {
            z-index: auto;
        }

        .front-desktop-shell .ac-inline-cta :is(
            .ac-inline-cta-card,
            .ac-inline-cta-copy,
            .ac-inline-cta-title,
            .ac-inline-cta-action,
            .front-action-cta
        ) {
            position: relative;
            z-index: 5;
        }

        .front-desktop-shell .front-contact-map-shell :is(
            .front-contact-map-tabs,
            .front-contact-map-tab,
            .front-contact-map-stage,
            .front-contact-map-panel,
            .front-contact-map-frame,
            .front-contact-map-frame iframe
        ) {
            position: relative;
            z-index: 5;
        }

        .front-desktop-shell:is(
            .front-route-audit,
            .front-route-accounting,
            .front-route-advisory,
            .front-route-eu-funds
        ) :is(
            .ac-audit-editorial-wrap > .mx-auto,
            .ac-audit-blog-section > .mx-auto
        ) {
            z-index: auto;
        }

        .front-desktop-shell:is(
            .front-route-audit,
            .front-route-accounting,
            .front-route-advisory,
            .front-route-eu-funds
        ) .ac-audit-editorial-section {
            z-index: auto;
        }

        .front-desktop-shell:is(
            .front-route-audit,
            .front-route-accounting,
            .front-route-advisory,
            .front-route-eu-funds
        ) .ac-audit-editorial-section::before {
            z-index: 0;
        }

        .front-desktop-shell:is(
            .front-route-audit,
            .front-route-accounting,
            .front-route-advisory,
            .front-route-eu-funds
        ) :is(
            .ac-audit-editorial-section > *,
            .ac-audit-blog-section > .mx-auto > *
        ) {
            position: relative;
            z-index: 5;
        }
    </style>
</head>
@php
    $mainNavigation = app(\App\Services\Front\NavigationMenuService::class)->forLocale((string) app()->getLocale());
    $defaultLogoRelativePath = 'front-theme/images/branding/alpha-capitalis-logo.svg';
    $defaultLogoUrl = file_exists(public_path($defaultLogoRelativePath))
        ? asset($defaultLogoRelativePath)
        : null;
    $headerHeroBackdropRelativePath = 'assets/images/Naslovna.png';
    $headerHeroBackdropPath = public_path($headerHeroBackdropRelativePath);
    $headerHeroBackdropUrl = file_exists($headerHeroBackdropPath)
        ? asset($headerHeroBackdropRelativePath).'?v='.filemtime($headerHeroBackdropPath)
        : asset($headerHeroBackdropRelativePath);
    $homeHeroItems = collect($homeHeroBlocks ?? []);
    $homeStatsItems = collect($homeStatsBlocks ?? []);
    $homeHeroItem = $homeHeroItems->first(fn ($item): bool => (string) (($item['block'] ?? null)?->type ?? '') === 'home_hero')
        ?? $homeHeroItems->first();
    $homeStatsItem = $homeStatsItems->first(fn ($item): bool => (string) (($item['block'] ?? null)?->type ?? '') === 'home_stats')
        ?? $homeStatsItems->first();
    $homeHeroBlock = $homeHeroItem['block'] ?? null;
    $homeHeroTranslation = $homeHeroItem['translation'] ?? null;
    $homeStatsBlock = $homeStatsItem['block'] ?? null;
    $homeStatsTranslation = $homeStatsItem['translation'] ?? null;
    $homeHeroPayload = array_merge(
        is_array($homeHeroBlock?->payload ?? null) ? $homeHeroBlock->payload : [],
        is_array($homeHeroTranslation?->payload ?? null) ? $homeHeroTranslation->payload : [],
    );
    $homeStatsPayload = array_merge(
        is_array($homeStatsBlock?->payload ?? null) ? $homeStatsBlock->payload : [],
        is_array($homeStatsTranslation?->payload ?? null) ? $homeStatsTranslation->payload : [],
    );
    $homeHeroMediaUrl = $homeHeroBlock?->getFirstMediaUrl('block_background', 'hero_1440x480') ?: '';
    if ($homeHeroMediaUrl === '') {
        $homeHeroMediaUrl = $homeHeroBlock?->getFirstMediaUrl('block_background') ?: '';
    }
    if ($homeHeroMediaUrl !== '') {
        $headerHeroBackdropUrl = $homeHeroMediaUrl;
    }

    $homeHeroTitle = trim((string) ($homeHeroTranslation?->title ?? '')) ?: 'ALPHA CAPITALIS';
    $homeHeroSubtitle = trim((string) ($homeHeroTranslation?->subtitle ?? '')) ?: 'VAŠ KOMPAS KROZ SVIJET FINANCIJA';
    $homeHeroPrimaryLabel = trim((string) ($homeHeroTranslation?->cta_label ?? '')) ?: 'Naše usluge';
    $homeHeroPrimaryUrl = trim((string) ($homeHeroTranslation?->cta_url ?? '')) ?: route('services.index');
    $homeHeroSecondaryLabel = trim((string) ($homeHeroPayload['secondary_cta_label'] ?? '')) ?: 'Ugovori sastanak';
    $homeHeroSecondaryUrl = trim((string) ($homeHeroPayload['secondary_cta_url'] ?? '')) ?: route('contact.create');
    $homeHeroKicker = trim((string) ($homeHeroPayload['kicker'] ?? ''));
    $homeStats = collect((array) ($homeStatsPayload['stats'] ?? []))
        ->map(static function ($stat): array {
            $stat = is_array($stat) ? $stat : [];

            return [
                'value' => trim((string) ($stat['value'] ?? '')),
                'suffix' => trim((string) ($stat['suffix'] ?? '')),
                'label' => trim((string) ($stat['label'] ?? '')),
            ];
        })
        ->filter(static fn (array $stat): bool => $stat['value'] !== '' || $stat['label'] !== '')
        ->values();
    if ($homeStats->isEmpty()) {
        $homeStats = collect([
            ['value' => '300', 'suffix' => '+', 'label' => 'Odrađenih projekata'],
            ['value' => '600', 'suffix' => '+', 'label' => 'Redovnih klijenata'],
            ['value' => '60', 'suffix' => '+', 'label' => 'Kvalificiranih stručnjaka'],
        ]);
    }

@endphp
<body class="front-desktop-shell {{ request()->routeIs('home') ? 'front-route-home' : 'front-preload-pending' }} {{ request()->routeIs('audit.show') ? 'front-route-audit' : '' }} {{ request()->routeIs('accounting.show') ? 'front-route-accounting' : '' }} {{ request()->routeIs('advisory.*', 'eu-funds.show') ? 'front-route-advisory' : '' }} {{ request()->routeIs('eu-funds.show') ? 'front-route-eu-funds' : '' }} min-h-screen overflow-x-hidden antialiased" style="--front-header-hero-backdrop: url('{{ $headerHeroBackdropUrl }}');">
    @unless (request()->routeIs('home'))
        <div id="front-initial-preloader" aria-hidden="true"></div>
    @endunless
    @php
        $activeLocale = (string) ($frontLocale ?? app()->getLocale());
        $availableLanguages = collect($frontLanguages ?? [])->filter(
            static fn (array $language): bool => (string) ($language['code'] ?? '') !== ''
        )->values();
        $showLeaseCalculatorLink = request()->routeIs('accounting.show');
        $headerPhoneRaw = trim((string) ($storeSettings['footer']['phone'] ?? ''));
        $headerEmailRaw = trim((string) ($storeSettings['footer']['email_support'] ?? ''));
        $headerPhone = $headerPhoneRaw !== '' ? $headerPhoneRaw : '+385 (1) 580 6656';
        $headerEmail = $headerEmailRaw !== '' ? $headerEmailRaw : 'info@alphacapitalis.com';

    @endphp

    @if (false)
    <div class="front-header-meta hidden lg:block">
        <div class="front-header-meta-inner flex w-full items-center justify-between gap-4 px-5 sm:px-8 xl:px-10">
            <div class="flex min-w-0 items-center gap-5">
                <a href="tel:{{ preg_replace('/\s+/', '', $headerPhone) }}" class="front-meta-link inline-flex items-center gap-2 text-xs">
                    <svg class="front-meta-icon h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2A19.8 19.8 0 0 1 11.2 19a19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.9.8 2.7a2 2 0 0 1-.5 2.1L8.1 9.8a16 16 0 0 0 6.1 6.1l1.3-1.3a2 2 0 0 1 2.1-.5c.9.4 1.8.6 2.8.8a2 2 0 0 1 1.6 2z"/></svg>
                    <span>{{ $headerPhone }}</span>
                </a>
                <a href="mailto:{{ $headerEmail }}" class="front-meta-link inline-flex items-center gap-2 text-xs">
                    <svg class="front-meta-icon h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 8l9 6 9-6"/></svg>
                    <span>{{ $headerEmail }}</span>
                </a>
            </div>
            <div class="front-lang-switch inline-flex items-center p-0.5 text-xs font-semibold uppercase tracking-[0.08em]">
                <a href="{{ route('front.locale.switch', ['code' => 'hr']) }}" class="front-meta-lang {{ $activeLocale === 'hr' ? 'is-active' : '' }}" hreflang="hr">HR</a>
                <a href="{{ route('front.locale.switch', ['code' => 'en']) }}" class="front-meta-lang {{ $activeLocale === 'en' ? 'is-active' : '' }}" hreflang="en">EN</a>
            </div>
        </div>
    </div>

<header class="front-site-header sticky top-0 z-40 border-b" data-front-sticky-header>
    <div class="front-header-main">
        <div class="front-header-row flex w-full items-center justify-between gap-2.5 sm:px-8 xl:px-10">
            <a href="{{ route('home') }}" class="front-logo inline-flex items-center text-2xl font-black sm:text-4xl">
                @php
                    $headerLogoUrl = (string) ($storeSettings['branding']['logo_url'] ?? $defaultLogoUrl ?? '');
                    $stickyMarkRelativePath = 'front-theme/images/branding/znak-ac.svg';
                    $stickyMarkUrl = file_exists(public_path($stickyMarkRelativePath))
                        ? asset($stickyMarkRelativePath)
                        : $headerLogoUrl;
                @endphp
                @if ($headerLogoUrl !== '')
                    <img src="{{ $headerLogoUrl }}" alt="{{ $storeSettings['branding']['store_name'] ?? config('app.name', 'AG Info') }}" class="front-logo-full h-[52px] w-auto object-contain sm:h-[56px] xl:h-[66px]">
                    <img src="{{ $stickyMarkUrl }}" alt="" aria-hidden="true" class="front-logo-mark hidden h-10 w-auto object-contain sm:h-[50px]">
                @else
                    {{ (string) ($storeSettings['branding']['store_name'] ?? config('app.name', 'AG Info')) }}
                @endif
            </a>

            <nav class="front-nav relative hidden flex-1 items-center justify-center gap-7 px-4 text-sm font-semibold xl:flex">
                @include('front.desktop.partials.main-nav')
            </nav>

            <div class="front-header-actions hidden min-h-[84px] items-center gap-2.5 xl:flex">
                <a href="{{ route('assessment.create') }}" class="front-action-cta">
                    Zatraži ponudu
                </a>
                @if ($showLeaseCalculatorLink)
                    <a href="{{ route('lease-calculator.show') }}" class="front-action-cta front-action-cta-secondary">
                        MSFI 16 Kalkulator
                    </a>
                @endif
                <span class="front-actions-separator" aria-hidden="true"></span>
                <button type="button" class="front-search-action inline-flex h-10 w-10 items-center justify-center transition" aria-label="Pretraga" data-header-search-toggle>
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="M20 20l-3.2-3.2"></path>
                    </svg>
                </button>
            </div>

            <div class="front-mobile-actions flex self-stretch items-center xl:hidden">
                <button type="button" class="front-top-action flex h-full items-center justify-center transition" aria-label="Pretraga" data-header-search-toggle>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="M20 20l-3.2-3.2"></path>
                    </svg>
                </button>
                <button type="button" class="front-top-action flex h-full items-center justify-center transition" aria-label="{{ __('ui.front.desktop.open_navigation') }}" data-mobile-menu-open>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 7h16M4 12h16M4 17h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="front-header-search-panel pointer-events-none max-h-0 overflow-hidden opacity-0 transition-all duration-300" data-header-search-panel>
        <div class="w-full px-5 py-3 sm:px-8 xl:px-10">
            <form
                action="{{ route('search.index') }}"
                method="get"
                class="front-header-search-form"
                role="search"
                data-header-search-form
                data-search-suggest-endpoint="{{ route('search.suggest') }}"
                data-search-results-endpoint="{{ route('search.index') }}"
            >
                <label for="front-header-search-input" class="sr-only">Pretraga sadržaja</label>
                <div class="front-search-field-stack">
                    <div class="front-search-field">
                        <span class="front-search-field-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9">
                                <circle cx="11" cy="11" r="7"></circle>
                                <path d="M20 20l-3.2-3.2"></path>
                            </svg>
                        </span>
                        <input
                            id="front-header-search-input"
                            type="search"
                            name="q"
                            value="{{ request('q') }}"
                            class="front-search-input"
                            placeholder="{{ __('ui.search.input_placeholder') }}"
                            autocomplete="off"
                            spellcheck="false"
                            data-header-search-input
                        >
                    </div>

                    <div class="front-search-suggestions hidden" data-header-search-suggestions></div>
                </div>
                <button type="submit" class="front-search-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="M20 20l-3.2-3.2"></path>
                    </svg>
                    <span>{{ __('ui.search.submit') }}</span>
                </button>
            </form>
        </div>
    </div>
</header>

<div class="pointer-events-none fixed inset-0 z-[60] lg:hidden" data-mobile-menu-root>
    <button type="button" class="front-mobile-menu-backdrop absolute inset-0 opacity-0 transition-opacity duration-300" aria-label="{{ __('ui.front.desktop.close_navigation') }}" data-mobile-menu-close></button>
    <aside class="front-mobile-menu-panel absolute inset-0 flex w-full max-w-none -translate-x-full flex-col shadow-2xl transition-transform duration-300 ease-out" data-mobile-menu-panel>
        <div class="front-mobile-menu-head flex items-center justify-between border-b px-4 py-4">
            @php
                $mobileHeaderLogoUrl = (string) ($storeSettings['branding']['logo_url'] ?? $defaultLogoUrl ?? '');
            @endphp
            <a href="{{ route('home') }}" class="inline-flex items-center">
                @if ($mobileHeaderLogoUrl !== '')
                    <img src="{{ $mobileHeaderLogoUrl }}" alt="{{ $storeSettings['branding']['store_name'] ?? config('app.name', 'AG Info') }}" class="h-12 w-auto object-contain">
                @else
                    <span class="text-xl font-black tracking-tight text-white">{{ (string) ($storeSettings['branding']['store_name'] ?? 'AG Info') }}</span>
                @endif
            </a>
            <button type="button" class="inline-flex h-10 w-10 items-center justify-center border transition" aria-label="{{ __('ui.front.desktop.close_navigation') }}" data-mobile-menu-close>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18"></path>
                </svg>
            </button>
        </div>
        @include('front.desktop.partials.main-nav-mobile')
        @if ($availableLanguages->isNotEmpty())
            <div class="front-mobile-menu-locale mt-auto border-t px-4 py-4">
                <div class="front-mobile-lang-switch inline-flex items-center p-0.5 text-xs font-semibold uppercase tracking-[0.08em]">
                    @foreach ($availableLanguages as $language)
                        @php
                            $code = (string) ($language['code'] ?? '');
                        @endphp
                        <a href="{{ route('front.locale.switch', ['code' => $code]) }}" class="front-mobile-menu-locale-link {{ $activeLocale === $code ? 'is-active' : '' }}" hreflang="{{ $code }}">
                            {{ strtoupper($code) }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </aside>
</div>

@if (request()->routeIs('home'))
    <section id="video-sadrzaj" class="front-hero-video-section w-full border-b border-black/20 bg-black">
        <div class="front-hero-video-wrap relative w-full overflow-hidden">
            <div class="front-hero-image absolute inset-0"></div>

            <div class="front-hero-video-overlay absolute inset-0"></div>

            <div class="front-hero-video-content absolute inset-0 flex items-center justify-center px-6 text-center">
                <div>
                    @if ($homeHeroKicker !== '')
                        <p class="front-kicker mb-4 justify-center text-white/80">{{ $homeHeroKicker }}</p>
                    @endif
                    <h1 class="front-hero-video-title text-white">{{ $homeHeroTitle }}</h1>
                    <p class="front-hero-video-subtitle mt-5 text-white/90">{{ $homeHeroSubtitle }}</p>
                    <div class="front-hero-cta-row mt-8 flex flex-wrap items-center justify-center gap-3">
                        @if ($homeHeroPrimaryLabel !== '' && $homeHeroPrimaryUrl !== '')
                            <a href="{{ $homeHeroPrimaryUrl }}" class="front-hero-cta front-hero-cta-primary inline-flex items-center justify-center px-6 py-3 text-[11px] font-semibold uppercase tracking-[0.12em]">
                                {{ $homeHeroPrimaryLabel }}
                            </a>
                        @endif
                        @if ($homeHeroSecondaryLabel !== '' && $homeHeroSecondaryUrl !== '')
                            <a href="{{ $homeHeroSecondaryUrl }}" class="front-hero-cta front-hero-cta-secondary inline-flex items-center justify-center px-6 py-3 text-[11px] font-semibold uppercase tracking-[0.12em]">
                                {{ $homeHeroSecondaryLabel }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

        </div>
        <div class="front-hero-stats-card relative z-10" data-home-hero-stats>
            <div class="grid w-full grid-cols-2 md:grid-cols-3">
                @foreach ($homeStats->take(3) as $stat)
                    @php
                        $statValue = (string) ($stat['value'] ?? '');
                        $statSuffix = (string) ($stat['suffix'] ?? '');
                        $statLabel = (string) ($stat['label'] ?? '');
                        $statCountTo = preg_replace('/[^0-9]/', '', $statValue);
                        $statDisplayValue = $statValue.$statSuffix;
                    @endphp
                <article class="front-hero-stat-card {{ $loop->last ? 'front-hero-stat-card--wide' : '' }} px-6 py-8 text-center" data-home-hero-stat style="--front-hero-stat-delay: {{ $loop->index * 320 }}ms;">
                    <span class="front-hero-stat-icon mx-auto inline-flex h-11 w-11 items-center justify-center rounded-full" aria-hidden="true">
                        @if ($loop->first)
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8">
                                <path d="M4 19h16"/>
                                <rect x="6" y="11" width="2.8" height="6" rx="1"/>
                                <rect x="10.6" y="8" width="2.8" height="9" rx="1"/>
                                <rect x="15.2" y="5" width="2.8" height="12" rx="1"/>
                            </svg>
                        @elseif ($loop->iteration === 2)
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/>
                                <circle cx="10" cy="7" r="4"/>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        @else
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        @endif
                    </span>
                    <div class="front-hero-stat-value-shell" data-home-hero-display data-home-hero-display-value="{{ $statDisplayValue }}">
                        <p class="front-hero-stat-value" data-home-hero-count data-count-to="{{ $statCountTo !== '' ? $statCountTo : $statValue }}" data-count-suffix="{{ $statSuffix }}">{{ $statValue }}</p>
                    </div>
                    <span class="front-hero-stat-accent" aria-hidden="true"></span>
                    @if ($statLabel !== '')
                        <p class="front-hero-stat-label">{{ $statLabel }}</p>
                    @endif
                </article>
                @endforeach
            </div>
        </div>
</section>
@endif

    @endif
    {{-- Shared Alpha redesign shell used by every public storefront route. --}}
    @include('front.desktop.partials.alpha-global-header')

<main class="front-content-shell @yield('main_class', 'mx-auto w-full max-w-7xl px-6 py-10')">
    <div class="front-fixed-watermark" aria-hidden="true"></div>
    @include('front.desktop.partials.flash')
    @yield('content')
</main>

@if (false)
@unless (request()->routeIs('audit.show') || request()->routeIs('accounting.show'))
    <button type="button" class="front-footer-compass front-scroll-compass" data-scroll-top data-scroll-top-floating aria-label="Povratak na vrh">
        <img src="{{ asset('front-theme/images/icons/znak-zlatni.svg') }}" alt="" aria-hidden="true" class="front-footer-compass-mark">
    </button>
@endunless

<footer class="front-footer mt-0">
    @php
        $footerCompanies = collect((array) ($storeSettings['official_entities'] ?? []))
            ->filter(static fn ($company): bool => is_array($company) && trim((string) ($company['name'] ?? '')) !== '')
            ->values()
            ->all();

        $footerIsoCertificates = collect([
            ['code' => 'ISO 9001:2015', 'title' => 'Sustav upravljanja kvalitetom', 'icon' => 'front-theme/images/certificates/iso-9001-sgs.png'],
            ['code' => 'ISO 14001:2015', 'title' => 'Sustav upravljanja okolišem', 'icon' => 'front-theme/images/certificates/iso-14001-sgs.png'],
            ['code' => 'ISO 45001:2018', 'title' => 'Sustav upravljanja zaštitom zdravlja i sigurnošću na radu', 'icon' => 'front-theme/images/certificates/iso-45001-sgs.png'],
        ])->take(1)->values();
        $footerEuFundingLogo = 'assets/images/HR_Financira_Europska_unija_RGB_WHITE_Outline.png';
    @endphp

    <button type="button" class="front-footer-compass" data-scroll-top aria-label="Povratak na vrh">
        <img src="{{ asset('front-theme/images/icons/znak-zlatni.svg') }}" alt="" aria-hidden="true" class="front-footer-compass-mark">
    </button>

    <div class="mx-auto w-full max-w-[1320px] px-4 py-12 sm:px-6 lg:px-8">
        <div class="front-footer-newsletter">
            <div class="front-footer-newsletter-copy">
                <p class="front-kicker">Newsletter</p>
                <h2 class="front-footer-newsletter-title">Prijava na newsletter</h2>
                <p class="front-footer-muted mt-2 text-sm">Primajte novosti i praktične savjete iz financija, računovodstva i revizije.</p>
            </div>
            <form action="{{ route('contact.create') }}" method="get" class="front-footer-newsletter-form" aria-label="Prijava na newsletter">
                <div class="front-footer-newsletter-row">
                    <label for="footer-newsletter-email" class="sr-only">Email adresa</label>
                    <input id="footer-newsletter-email" type="email" name="newsletter_email" placeholder="Upišite email adresu" class="front-footer-newsletter-input" required>
                    <button type="submit" class="front-footer-newsletter-button">Prijavi me</button>
                </div>
                <label class="front-footer-newsletter-consent">
                    <input type="checkbox" name="newsletter_consent" value="1" required>
                    <span>Prihvaćam uvjete korištenja i obradu podataka za newsletter.</span>
                </label>
            </form>
        </div>

        @php
            $footerSocialLinks = [
                [
                    'key' => 'x',
                    'label' => 'X',
                    'url' => '',
                ],
                [
                    'key' => 'facebook',
                    'label' => 'Facebook',
                    'url' => trim((string) ($storeSettings['branding']['social']['facebook']['url'] ?? '')),
                ],
                [
                    'key' => 'linkedin',
                    'label' => 'LinkedIn',
                    'url' => '',
                ],
                [
                    'key' => 'instagram',
                    'label' => 'Instagram',
                    'url' => trim((string) ($storeSettings['branding']['social']['instagram']['url'] ?? '')),
                ],
            ];
        @endphp
        <div class="front-footer-social-band" aria-label="Društvene mreže">
            <div class="front-footer-social-copy">
                <p class="front-footer-social-kicker">Business Insights</p>
                <p class="front-footer-social-text">Stručni uvidi za bolje poslovne odluke.</p>
            </div>
            <div class="front-footer-social-links">
                @foreach ($footerSocialLinks as $social)
                    @php
                        $url = $social['url'];
                        $isPlaceholder = $url === '';
                    @endphp
                    <a
                        href="{{ $isPlaceholder ? '#' : $url }}"
                        class="front-footer-social-link front-footer-social-link--{{ $social['key'] }} {{ $isPlaceholder ? 'is-placeholder' : '' }}"
                        @if(!$isPlaceholder) target="_blank" rel="noopener noreferrer" @endif
                        aria-label="{{ $social['label'] }}"
                    >
                        @if ($social['key'] === 'x')
                            <svg viewBox="0 0 448 512" fill="currentColor" aria-hidden="true"><path d="M64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l320 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32zm297.1 84l-103.8 118.6 122.1 161.4-95.6 0-74.8-97.9-85.7 97.9-47.5 0 111-126.9-117.1-153.1 98 0 67.7 89.5 78.2-89.5 47.5 0zM323.3 367.6l-169.9-224.7-28.3 0 171.8 224.7 26.4 0z"/></svg>
                        @elseif ($social['key'] === 'facebook')
                            <svg viewBox="0 0 448 512" fill="currentColor" aria-hidden="true"><path d="M64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l98.2 0 0-145.8-52.8 0 0-78.2 52.8 0 0-33.7c0-87.1 39.4-127.5 125-127.5 16.2 0 44.2 3.2 55.7 6.4l0 70.8c-6-.6-16.5-1-29.6-1-42 0-58.2 15.9-58.2 57.2l0 27.8 83.6 0-14.4 78.2-69.3 0 0 145.8 129 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32z"/></svg>
                        @elseif ($social['key'] === 'linkedin')
                            <svg viewBox="0 0 448 512" fill="currentColor" aria-hidden="true"><path d="M64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l320 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32zm5 170.2l66.5 0 0 213.8-66.5 0 0-213.8zm71.7-67.7a38.5 38.5 0 1 1 -77 0 38.5 38.5 0 1 1 77 0zM317.9 416l0-104c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9l0 105.8-66.4 0 0-213.8 63.7 0 0 29.2 .9 0c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9l0 117.2-66.4 0z"/></svg>
                        @else
                            <svg viewBox="0 0 448 512" fill="currentColor" aria-hidden="true"><path d="M194.4 211.7a53.3 53.3 0 1 0 59.2 88.6 53.3 53.3 0 1 0 -59.2-88.6zm142.3-68.4c-5.2-5.2-11.5-9.3-18.4-12-18.1-7.1-57.6-6.8-83.1-6.5-4.1 0-7.9 .1-11.2 .1s-7.2 0-11.4-.1c-25.5-.3-64.8-.7-82.9 6.5-6.9 2.7-13.1 6.8-18.4 12s-9.3 11.5-12 18.4c-7.1 18.1-6.7 57.7-6.5 83.2 0 4.1 .1 7.9 .1 11.1s0 7-.1 11.1c-.2 25.5-.6 65.1 6.5 83.2 2.7 6.9 6.8 13.1 12 18.4s11.5 9.3 18.4 12c18.1 7.1 57.6 6.8 83.1 6.5 4.1 0 7.9-.1 11.2-.1s7.2 0 11.4 .1c25.5 .3 64.8 .7 82.9-6.5 6.9-2.7 13.1-6.8 18.4-12s9.3-11.5 12-18.4c7.2-18 6.8-57.4 6.5-83 0-4.2-.1-8.1-.1-11.4s0-7.1 .1-11.4c.3-25.5 .7-64.9-6.5-83-2.7-6.9-6.8-13.1-12-18.4l0 .2zm-67.1 44.5c18.1 12.1 30.6 30.9 34.9 52.2s-.2 43.5-12.3 61.6c-6 9-13.7 16.6-22.6 22.6s-19 10.1-29.6 12.2c-21.3 4.2-43.5-.2-61.6-12.3s-30.6-30.9-34.9-52.2 .2-43.5 12.2-61.6 30.9-30.6 52.2-34.9 43.5 .2 61.6 12.2l.1 0zm29.2-1.3c-3.1-2.1-5.6-5.1-7.1-8.6s-1.8-7.3-1.1-11.1 2.6-7.1 5.2-9.8 6.1-4.5 9.8-5.2 7.6-.4 11.1 1.1 6.5 3.9 8.6 7 3.2 6.8 3.2 10.6c0 2.5-.5 5-1.4 7.3s-2.4 4.4-4.1 6.2-3.9 3.2-6.2 4.2-4.8 1.5-7.3 1.5c-3.8 0-7.5-1.1-10.6-3.2l-.1 0zM448 96c0-35.3-28.7-64-64-64L64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l320 0c35.3 0 64-28.7 64-64l0-320zM357 389c-18.7 18.7-41.4 24.6-67 25.9-26.4 1.5-105.6 1.5-132 0-25.6-1.3-48.3-7.2-67-25.9s-24.6-41.4-25.8-67c-1.5-26.4-1.5-105.6 0-132 1.3-25.6 7.1-48.3 25.8-67s41.5-24.6 67-25.8c26.4-1.5 105.6-1.5 132 0 25.6 1.3 48.3 7.1 67 25.8s24.6 41.4 25.8 67c1.5 26.3 1.5 105.4 0 131.9-1.3 25.6-7.1 48.3-25.8 67l0 .1z"/></svg>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <div class="front-footer-company-grid">
            @foreach ($footerCompanies as $company)
                <article class="front-footer-company-card">
                    <h3 class="front-footer-company-name">{{ $company['name'] }}</h3>
                    <div class="front-footer-company-body">
                        <p class="front-footer-company-line">{{ $company['address'][0] }}</p>
                        <p class="front-footer-company-line">{{ $company['address'][1] }}</p>
                        <p class="front-footer-company-line">OIB: {{ $company['oib'] }}</p>
                        <p class="front-footer-company-line">MBS: {{ $company['mbs'] }}</p>
                        <p class="front-footer-company-line">IBAN: {{ $company['iban'] }}</p>
                        <p class="front-footer-company-line">T: <a href="tel:{{ preg_replace('/\s+/', '', $company['phone']) }}" class="front-footer-company-contact">{{ $company['phone'] }}</a></p>
                        <p class="front-footer-company-line">E: <a href="mailto:{{ $company['email'] }}" class="front-footer-company-mail">{{ $company['email'] }}</a></p>
                        <p class="front-footer-company-line mt-3">
                            <span class="front-footer-whatsapp" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8">
                                    <path d="M20 11.5a8.5 8.5 0 0 1-12.4 7.5L4 20l1.1-3.4A8.5 8.5 0 1 1 20 11.5z"/>
                                    <path d="M9.6 8.7c.2-.4.4-.4.7-.4h.6c.2 0 .4 0 .5.3.2.5.7 1.7.8 1.8.1.2.1.4 0 .5-.1.2-.2.3-.4.5l-.3.3c-.1.1-.2.3-.1.5.1.2.6 1 1.3 1.6.9.8 1.7 1.1 1.9 1.2.2.1.4 0 .5-.1l.6-.7c.2-.2.4-.2.6-.1l1.8.8c.3.1.5.2.5.4 0 .1 0 .8-.3 1.2-.3.4-1 .8-1.4.8-.4.1-.8.1-1.4 0-.4-.1-1-.3-1.8-.7-3.1-1.4-5-4.6-5.2-4.9-.2-.3-.9-1.2-.9-2.3 0-1.1.6-1.7.8-2z"/>
                                </svg>
                            </span>
                            WhatsApp
                        </p>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="front-footer-company-accordion">
            @foreach ($footerCompanies as $company)
                <details class="front-footer-company-dropdown">
                    <summary class="front-footer-company-dropdown-summary">
                        <span>{{ $company['name'] }}</span>
                        <span class="front-footer-company-dropdown-icon" aria-hidden="true"></span>
                    </summary>
                    <div class="front-footer-company-dropdown-body">
                        <p class="front-footer-company-line">{{ $company['address'][0] }}</p>
                        <p class="front-footer-company-line">{{ $company['address'][1] }}</p>
                        <p class="front-footer-company-line">OIB: {{ $company['oib'] }}</p>
                        <p class="front-footer-company-line">MBS: {{ $company['mbs'] }}</p>
                        <p class="front-footer-company-line">IBAN: {{ $company['iban'] }}</p>
                        <p class="front-footer-company-line">T: <a href="tel:{{ preg_replace('/\s+/', '', $company['phone']) }}" class="front-footer-company-contact">{{ $company['phone'] }}</a></p>
                        <p class="front-footer-company-line">E: <a href="mailto:{{ $company['email'] }}" class="front-footer-company-mail">{{ $company['email'] }}</a></p>
                        <p class="front-footer-company-line mt-3">
                            <span class="front-footer-whatsapp" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8">
                                    <path d="M20 11.5a8.5 8.5 0 0 1-12.4 7.5L4 20l1.1-3.4A8.5 8.5 0 1 1 20 11.5z"/>
                                    <path d="M9.6 8.7c.2-.4.4-.4.7-.4h.6c.2 0 .4 0 .5.3.2.5.7 1.7.8 1.8.1.2.1.4 0 .5-.1.2-.2.3-.4.5l-.3.3c-.1.1-.2.3-.1.5.1.2.6 1 1.3 1.6.9.8 1.7 1.1 1.9 1.2.2.1.4 0 .5-.1l.6-.7c.2-.2.4-.2.6-.1l1.8.8c.3.1.5.2.5.4 0 .1 0 .8-.3 1.2-.3.4-1 .8-1.4.8-.4.1-.8.1-1.4 0-.4-.1-1-.3-1.8-.7-3.1-1.4-5-4.6-5.2-4.9-.2-.3-.9-1.2-.9-2.3 0-1.1.6-1.7.8-2z"/>
                                </svg>
                            </span>
                            WhatsApp
                        </p>
                    </div>
                </details>
            @endforeach
        </div>

        <div class="front-footer-certification-row">
            <div class="front-footer-iso-grid {{ $footerIsoCertificates->count() === 1 ? 'is-single' : '' }}">
                @foreach ($footerIsoCertificates as $certificate)
                    <div class="front-footer-iso-item">
                        <span class="front-footer-iso-logo-wrap" aria-hidden="true">
                            <img
                                src="{{ asset((string) ($certificate['icon'] ?? '')) }}"
                                alt="{{ $certificate['code'] }} certifikat"
                                class="front-footer-iso-logo"
                                loading="lazy"
                                decoding="async"
                            >
                        </span>
                        <p><strong>{{ $certificate['code'] }}</strong> - {{ $certificate['title'] }}</p>
                    </div>
                @endforeach
            </div>
            <div class="front-footer-eu-funding">
                <img
                    src="{{ asset($footerEuFundingLogo) }}"
                    alt="Financira Europska unija"
                    class="front-footer-eu-funding-logo"
                    loading="lazy"
                    decoding="async"
                >
            </div>
        </div>

        <div class="front-footer-legal">
            <p>
                ALPHA CAPITALIS © Sva prava pridržana.
            </p>
            <p class="front-footer-legal-credit">
                Web by:
                <a href="https://www.agmedia.hr" target="_blank" rel="noopener noreferrer">AG media</a>
            </p>
        </div>
    </div>
</footer>
@endif

@include('front.desktop.partials.alpha-global-footer')
<script>
    (function () {
        var preloader = document.getElementById('front-initial-preloader');
        var hide = function () {
            document.body.classList.remove('front-preload-pending');

            if (!preloader) {
                return;
            }

            preloader.classList.add('is-hidden');
            window.setTimeout(function () {
                preloader.remove();
            }, 260);
        };

        if (document.readyState === 'complete') {
            hide();
            return;
        }

        window.addEventListener('load', hide, { once: true });
        window.setTimeout(hide, 1400);
    })();

    (function () {
        var prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var activeAnimationFrame = null;
        var getAutoDuration = function (startTop, finalTop, options) {
            if (options && typeof options.duration === 'number') {
                return options.duration;
            }

            var distance = Math.abs(finalTop - startTop);

            return Math.max(720, Math.min(980, 620 + (distance * 0.16)));
        };

        window.__frontAnimateScrollTo = function (targetTop, options) {
            var finalTop = Math.max(0, Math.round(targetTop));
            var onComplete = options && typeof options.onComplete === 'function' ? options.onComplete : null;
            var startTop = window.pageYOffset || document.documentElement.scrollTop || 0;
            var distance = finalTop - startTop;
            var duration = getAutoDuration(startTop, finalTop, options);

            if (typeof window.__frontLockWheelSmoothing === 'function') {
                window.__frontLockWheelSmoothing(duration, finalTop);
            }

            if (activeAnimationFrame) {
                window.cancelAnimationFrame(activeAnimationFrame);
                activeAnimationFrame = null;
            }

            if (prefersReducedMotion || duration <= 0 || Math.abs(distance) < 2) {
                window.scrollTo(0, finalTop);
                if (typeof window.__frontSyncWheelTarget === 'function') {
                    window.__frontSyncWheelTarget(finalTop);
                }
                if (onComplete) {
                    onComplete();
                }
                return;
            }

            var startTime = null;
            var easeSwing = function (progress) {
                return 0.5 - Math.cos(progress * Math.PI) / 2;
            };

            var step = function (currentTime) {
                if (startTime === null) {
                    startTime = currentTime;
                }

                var elapsed = currentTime - startTime;
                var progress = Math.min(elapsed / duration, 1);
                var nextTop = startTop + (distance * easeSwing(progress));

                window.scrollTo(0, nextTop);

                if (progress < 1) {
                    activeAnimationFrame = window.requestAnimationFrame(step);
                    return;
                }

                activeAnimationFrame = null;
                window.scrollTo(0, finalTop);
                if (typeof window.__frontSyncWheelTarget === 'function') {
                    window.__frontSyncWheelTarget(finalTop);
                }

                if (onComplete) {
                    onComplete();
                }
            };

            activeAnimationFrame = window.requestAnimationFrame(step);
        };
    })();

    (function () {
        var scrollTopButtons = Array.prototype.slice.call(document.querySelectorAll('[data-scroll-top]'));
        if (!scrollTopButtons.length) {
            return;
        }

        var footerButton = document.querySelector('.front-footer [data-scroll-top]');
        var floatingButton = document.querySelector('[data-scroll-top-floating]');
        var footer = footerButton ? footerButton.closest('.front-footer') : document.querySelector('.front-footer');
        var floatingVisible = null;
        var floatingSyncFrame = null;
        var syncCompassBackground = function () {
            if (!footer || !footerButton) {
                return;
            }

            var footerRect = footer.getBoundingClientRect();
            var compassRect = footerButton.getBoundingClientRect();
            var offsetX = -(compassRect.left - footerRect.left);

            footerButton.style.setProperty('--front-footer-compass-bg-pos', offsetX + 'px 0px');
            footerButton.style.setProperty('--front-footer-compass-bg-size', footerRect.width + 'px ' + footerRect.height + 'px');
        };

        var syncFloatingVisibility = function () {
            if (!floatingButton) {
                return;
            }

            var viewportHeight = window.innerHeight || document.documentElement.clientHeight || 0;
            var shouldShow = window.innerWidth > 900 && window.scrollY > Math.max(viewportHeight * 0.45, 420);

            if (floatingVisible === shouldShow) {
                return;
            }

            floatingVisible = shouldShow;
            floatingButton.classList.toggle('is-visible', shouldShow);
        };

        var requestFloatingVisibilitySync = function () {
            if (floatingSyncFrame !== null) {
                return;
            }

            floatingSyncFrame = window.requestAnimationFrame(function () {
                floatingSyncFrame = null;
                syncFloatingVisibility();
            });
        };

        syncCompassBackground();
        window.addEventListener('resize', syncCompassBackground);
        window.addEventListener('orientationchange', syncCompassBackground);
        window.setTimeout(syncCompassBackground, 120);

        if (floatingButton) {
            syncFloatingVisibility();
            window.addEventListener('resize', syncFloatingVisibility);
            window.addEventListener('orientationchange', syncFloatingVisibility);
            window.addEventListener('scroll', requestFloatingVisibilitySync, { passive: true });
            window.setTimeout(syncFloatingVisibility, 120);
        }

        scrollTopButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                if (typeof window.__frontAnimateScrollTo === 'function') {
                    window.__frontAnimateScrollTo(0);
                    return;
                }

                window.scrollTo(0, 0);
            });
        });
    })();

    (function () {
        var stickyHeader = document.querySelector('[data-front-sticky-header]');
        var getScrollOffset = function () {
            if (!stickyHeader) {
                return 2;
            }

            return Math.round(stickyHeader.getBoundingClientRect().height) + 2;
        };

        var getHashTarget = function (hash) {
            if (!hash || hash === '#') {
                return null;
            }

            var decodedHash = hash;
            try {
                decodedHash = decodeURIComponent(hash);
            } catch (error) {
                decodedHash = hash;
            }

            try {
                return document.querySelector(decodedHash);
            } catch (error) {
                return document.getElementById(decodedHash.replace(/^#/, ''));
            }
        };

        var scrollToHashTarget = function (hash, options) {
            var targetElement = getHashTarget(hash);
            if (!targetElement) {
                return false;
            }

            var targetTop = window.pageYOffset + targetElement.getBoundingClientRect().top - getScrollOffset();

            if (typeof window.__frontAnimateScrollTo === 'function') {
                window.__frontAnimateScrollTo(targetTop, options);
                return true;
            }

            window.scrollTo(0, Math.max(0, targetTop));

            return true;
        };

        document.addEventListener('click', function (event) {
            var link = event.target.closest('a[href*="#"]');
            if (!link || link.target === '_blank' || link.hasAttribute('download')) {
                return;
            }

            if (event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            var rawHref = link.getAttribute('href');
            if (!rawHref || rawHref === '#') {
                return;
            }

            var parsedUrl;
            try {
                parsedUrl = new URL(rawHref, window.location.href);
            } catch (error) {
                return;
            }

            if (!parsedUrl.hash || parsedUrl.origin !== window.location.origin || parsedUrl.pathname !== window.location.pathname) {
                return;
            }

            if (!getHashTarget(parsedUrl.hash)) {
                return;
            }

            event.preventDefault();
            scrollToHashTarget(parsedUrl.hash);

            if (window.history && typeof window.history.pushState === 'function') {
                window.history.pushState(null, '', parsedUrl.hash);
            }
        });

        var syncInitialHashScroll = function () {
            if (!window.location.hash || !getHashTarget(window.location.hash)) {
                return;
            }

            window.scrollTo(0, 0);
            window.requestAnimationFrame(function () {
                window.requestAnimationFrame(function () {
                    scrollToHashTarget(window.location.hash);
                });
            });
        };

        window.addEventListener('hashchange', function () {
            scrollToHashTarget(window.location.hash);
        });

        if (document.readyState === 'complete') {
            syncInitialHashScroll();
            return;
        }

        window.addEventListener('load', syncInitialHashScroll, { once: true });
    })();
</script>
@if (request()->routeIs('home'))
<script>
    (function () {
        var statsSection = document.querySelector('[data-home-hero-stats]');
        if (!statsSection) {
            return;
        }

        var items = Array.prototype.slice.call(statsSection.querySelectorAll('[data-home-hero-stat]'));
        if (!items.length) {
            return;
        }

        var prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var formatDisplayValue = function (value) {
            return Math.round(Number(value) || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        };

        var digitTrackMarkup = (function () {
            var trackDigits = '';

            for (var cycle = 0; cycle < 4; cycle += 1) {
                for (var digit = 0; digit <= 9; digit += 1) {
                    trackDigits += '<span>' + digit + '</span>';
                }
            }

            return trackDigits;
        })();

        var buildCounterMarkup = function (formattedValue) {
            return formattedValue.split('').map(function (character) {
                if (!/[0-9]/.test(character)) {
                    return '<span class="front-hero-formatting-mark">' + character + '</span>';
                }

                return '<span class="front-hero-digit"><span class="front-hero-digit-track" data-roll-target="' + character + '">' + digitTrackMarkup + '</span></span>';
            }).join('');
        };

        var prepareItem = function (item) {
            var valueElement = item.querySelector('[data-home-hero-count]');
            var valueShell = item.querySelector('[data-home-hero-display]');
            var targetValue = Number.parseInt(valueElement instanceof HTMLElement ? valueElement.dataset.countTo || '0' : '0', 10);
            var formattedValue = formatDisplayValue(targetValue);
            var suffix = valueElement instanceof HTMLElement ? valueElement.dataset.countSuffix || '' : '';

            if (valueShell instanceof HTMLElement) {
                valueShell.dataset.homeHeroDisplayValue = '0' + suffix;
            }

            if (valueElement instanceof HTMLElement) {
                valueElement.innerHTML = buildCounterMarkup(formattedValue);
            }
        };

        var animateValue = function (item, valueElement, valueShell, targetValue) {
            var duration = Math.max(2400, Math.min(3200, 2200 + (String(targetValue).length * 240)));
            var digitTracks = Array.prototype.slice.call(valueElement.querySelectorAll('.front-hero-digit-track'));

            item.classList.add('is-counting');

            digitTracks.forEach(function (track) {
                track.style.transitionDuration = duration + 'ms';
                track.style.transitionTimingFunction = 'cubic-bezier(0.16, 1, 0.3, 1)';
            });

            window.requestAnimationFrame(function () {
                window.requestAnimationFrame(function () {
                    digitTracks.forEach(function (track) {
                        var targetDigit = Number.parseInt(track.dataset.rollTarget || '0', 10);
                        var targetOffset = 20 + (Number.isNaN(targetDigit) ? 0 : targetDigit);

                        track.style.transform = 'translate3d(0, -' + targetOffset + 'em, 0)';
                    });
                });
            });

            window.setTimeout(function () {
                var suffix = valueElement instanceof HTMLElement ? valueElement.dataset.countSuffix || '' : '';

                if (valueShell instanceof HTMLElement) {
                    valueShell.dataset.homeHeroDisplayValue = formatDisplayValue(targetValue) + suffix;
                }

                item.classList.remove('is-counting');
                item.classList.add('is-counted');
            }, duration + 60);
        };

        var getItemDelay = function (item) {
            if (!(item instanceof HTMLElement)) {
                return 0;
            }

            var delayValue = window.getComputedStyle(item).getPropertyValue('--front-hero-stat-delay').trim();
            var parsedDelay = Number.parseFloat(delayValue);

            if (Number.isNaN(parsedDelay)) {
                return 0;
            }

            return delayValue.endsWith('s') && !delayValue.endsWith('ms')
                ? parsedDelay * 1000
                : parsedDelay;
        };

        var revealItem = function (item) {
            if (!(item instanceof HTMLElement) || item.dataset.heroStatAnimated === '1') {
                return;
            }

            item.dataset.heroStatAnimated = '1';
            window.setTimeout(function () {
                item.classList.add('is-revealed');

                var valueElement = item.querySelector('[data-home-hero-count]');
                var valueShell = item.querySelector('[data-home-hero-display]');
                var targetValue = Number.parseInt(valueElement instanceof HTMLElement ? valueElement.dataset.countTo || '0' : '0', 10);

                if (!(valueElement instanceof HTMLElement) || Number.isNaN(targetValue)) {
                    return;
                }

                if (prefersReducedMotion) {
                    var suffix = valueElement.dataset.countSuffix || '';

                    valueElement.textContent = formatDisplayValue(targetValue);

                    if (valueShell instanceof HTMLElement) {
                        valueShell.dataset.homeHeroDisplayValue = formatDisplayValue(targetValue) + suffix;
                    }

                    item.classList.add('is-counted');
                    return;
                }

                animateValue(item, valueElement, valueShell, targetValue);
            }, getItemDelay(item));
        };

        var revealAll = function () {
            items.forEach(function (item) {
                revealItem(item);
            });
        };

        items.forEach(function (item) {
            prepareItem(item);
        });

        statsSection.classList.add('is-enhanced');

        if (!('IntersectionObserver' in window)) {
            revealAll();
            return;
        }

        var observer = new IntersectionObserver(function (entries, currentObserver) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                    return;
                }

                revealAll();
                currentObserver.disconnect();
            });
        }, {
            threshold: 0.2,
            rootMargin: '0px 0px -8% 0px',
        });

        observer.observe(statsSection);
    })();
</script>
@endif
<script defer src="{{ asset('front-theme/scripts/alpha-redesign.js') }}?v={{ filemtime(public_path('front-theme/scripts/alpha-redesign.js')) }}"></script>
@stack('scripts')
</body>
</html>
