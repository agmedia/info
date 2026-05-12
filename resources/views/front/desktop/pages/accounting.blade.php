@extends('front.desktop.layouts.store')

@php
    $captchaSiteKey = trim((string) ($storeSettings['captcha']['recaptcha_v3_site_key'] ?? ''));
    $captchaEnabled = (bool) ($storeSettings['captcha']['recaptcha_v3_enabled'] ?? false) && $captchaSiteKey !== '';
    $contactEmail = trim((string) ($storeSettings['footer']['email_support'] ?? '')) ?: 'info@alphacapitalis.com';
    $contactPhone = trim((string) ($storeSettings['footer']['phone'] ?? '')) ?: '+385 (1) 580 6656';
    $contactPhoneHref = preg_replace('/\s+/', '', $contactPhone);
    $isCroatianLocale = str_starts_with(strtolower((string) $locale), 'hr');
    $accountingSprite = asset('front-theme/fonts/sprites/solid.svg');
    $meetingFormLabels = $meetingSection['form_labels'] ?? [];
    $introBody = array_values($introSection['body'] ?? []);
    $introItems = array_values($introSection['items'] ?? []);
    $introLead = $introBody[0] ?? '';
    $introAnchorLabel = $introBody[1] ?? '';
    $editorialCards = array_values($editorialSection['cards'] ?? []);
    $videoSection = $videoSection ?? [];
    $accountingVideos = collect($accountingVideos ?? []);
    $videoSectionTitle = trim((string) ($videoSection['title'] ?? ''));
    $videoSectionIntro = trim((string) ($videoSection['intro'] ?? ''));
    $hasAccountingVideoHead = $videoSectionTitle !== '' || $videoSectionIntro !== '';
    $anchorLinkIcon = [
        'view_box' => '0 0 256 512',
        'href' => $accountingSprite.'#angle-right',
    ];
    $detailSectionIcons = [
        'book-open' => [
            'view_box' => '0 0 576 512',
            'href' => $accountingSprite.'#book-open',
        ],
        'file-lines' => [
            'view_box' => '0 0 384 512',
            'href' => $accountingSprite.'#file-lines',
        ],
        'chart-line' => [
            'view_box' => '0 0 512 512',
            'href' => $accountingSprite.'#chart-line',
        ],
        'briefcase' => [
            'view_box' => '0 0 512 512',
            'href' => $accountingSprite.'#briefcase',
        ],
        'user-group' => [
            'view_box' => '0 0 640 512',
            'href' => $accountingSprite.'#user-group',
        ],
        'building-columns' => [
            'view_box' => '0 0 512 512',
            'href' => $accountingSprite.'#building-columns',
        ],
        'magnifying-glass' => [
            'view_box' => '0 0 512 512',
            'href' => $accountingSprite.'#magnifying-glass',
        ],
    ];
    $detailQuoteIcon = [
        'view_box' => '0 0 448 512',
        'href' => $accountingSprite.'#quote-right',
    ];
    $detailServiceSections = collect($detailSections ?? [])->map(function (array $section, int $index) {
        $slug = \Illuminate\Support\Str::slug((string) ($section['slug'] ?? $section['title'] ?? 'section-'.($index + 1)));

        return array_merge($section, [
            'slug' => $slug,
            'anchor_id' => 'accounting-service-'.$slug,
            'index_label' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
            'items' => array_values($section['items'] ?? []),
            'downloads' => array_values($section['downloads'] ?? []),
            'after_list' => array_values($section['after_list'] ?? []),
        ]);
    })->values();
    $detailSectionIconByAnchor = $detailServiceSections
        ->mapWithKeys(fn (array $section): array => [$section['anchor_id'] => (string) ($section['icon'] ?? 'file-lines')])
        ->all();
    $introAnchorItems = collect($introItems)->map(function (string $item) use ($detailSectionIconByAnchor, $detailSectionIcons): array {
        $slug = \Illuminate\Support\Str::slug($item);
        $anchorId = 'accounting-service-'.$slug;
        $iconKey = $detailSectionIconByAnchor[$anchorId] ?? 'file-lines';
        $icon = $detailSectionIcons[$iconKey] ?? $detailSectionIcons['file-lines'];

        return [
            'label' => $item,
            'href' => '#'.$anchorId,
            'icon_view_box' => $icon['view_box'],
            'icon_href' => $icon['href'],
        ];
    })->values();
    $heroCtaLabel = trim((string) ($heroSection['cta_label'] ?? ''));
    $heroCtaUrl = trim((string) ($heroSection['cta_url'] ?? ''));

    if ($heroCtaLabel === '' || in_array($heroCtaLabel, ['Pošaljite upit', 'Send an inquiry'], true)) {
        $heroCtaLabel = $isCroatianLocale ? 'Pogledajte usluge' : 'View services';
    }

    if ($heroCtaUrl === '' || $heroCtaUrl === '#accounting-sastanak') {
        $heroCtaUrl = '#accounting-overview';
    }

    $readMoreLabel = $isCroatianLocale ? 'Opširnije' : 'Read more';
    $playVideoLabel = $isCroatianLocale ? 'Pokreni video' : 'Play video';
@endphp

@section('title', $servicePageMetaTitle !== '' ? $servicePageMetaTitle : ($servicePageTitle ?? 'Računovodstvo'))
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-family-business-page ac-accounting-page">
        <section class="ac-family-hero">
            <div class="ac-family-hero-media" aria-hidden="true" style="background-image: url('{{ $heroBackgroundUrl }}');"></div>
            <div class="ac-family-hero-overlay"></div>

            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-family-hero-content">
                    <div class="ac-family-hero-shell">
                        <div class="ac-family-hero-copy">
                            <h1 class="ac-family-hero-title">
                                <span class="is-brand">{{ $heroSection['brand_title'] ?? 'ALPHA CAPITALIS' }}</span>
                                <span class="is-subtitle">
                                    <span class="is-subtitle-lead">{{ $heroSection['subtitle_lead'] ?? 'Računovodstvo' }}</span>
                                    <span class="is-subtitle-accent">{{ $heroSection['subtitle_accent'] ?? 'i izvještavanje' }}</span>
                                </span>
                            </h1>

                            <p class="ac-family-hero-intro">{{ $heroSection['intro'] ?? '' }}</p>

                            <div class="ac-family-hero-actions">
                                <a href="{{ $heroCtaUrl }}" class="front-action-cta">
                                    <span>{{ $heroCtaLabel }}</span>
                                    <svg viewBox="0 0 320 512" fill="currentColor" aria-hidden="true">
                                        <use href="{{ asset('front-theme/fonts/sprites/solid.svg') }}#angle-down"></use>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="accounting-overview" class="ac-accounting-overview-section" aria-labelledby="ac-accounting-overview-title">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-support-story-hero ac-accounting-overview-hero">
                    <div class="ac-support-story-shell">
                        <div class="ac-services-head ac-support-story-head">
                            <div class="ac-services-eyebrow">
                                <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                                <p class="ac-services-kicker">{{ $introSection['kicker'] ?? 'RAČUNOVODSTVO' }}</p>
                                <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                            </div>
                            <h2 id="ac-accounting-overview-title">
                                <span>{{ $introSection['title'] ?? '' }}</span>
                            </h2>
                            @if (trim((string) $introLead) !== '')
                                <p class="ac-services-intro">{{ $introLead }}</p>
                            @endif
                            <div class="ac-services-divider" aria-hidden="true">
                                <span class="ac-services-divider-line"></span>
                                <span class="ac-services-divider-glyph"></span>
                                <span class="ac-services-divider-line"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ac-accounting-overview-grid">
                    <article class="ac-accounting-overview-copy">
                        @if ($introAnchorItems->isNotEmpty())
                            <section class="ac-accounting-anchor-nav" aria-labelledby="ac-accounting-anchor-nav-title">
                                <div class="ac-accounting-copy-head">
                                    <h3 id="ac-accounting-anchor-nav-title">
                                        {{ trim((string) $introAnchorLabel) !== '' ? $introAnchorLabel : ($introSection['title'] ?? 'Računovodstvene usluge') }}
                                    </h3>
                                </div>
                                <ul class="ac-accounting-anchor-list">
                                    @foreach ($introAnchorItems as $item)
                                        <li>
                                            <a href="{{ $item['href'] }}" class="ac-accounting-anchor-link">
                                                <span class="ac-accounting-anchor-link-icon" aria-hidden="true">
                                                    <svg viewBox="{{ $item['icon_view_box'] }}" fill="currentColor">
                                                        <use href="{{ $item['icon_href'] }}"></use>
                                                    </svg>
                                                </span>
                                                <span>{{ $item['label'] }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </section>
                        @endif

                    </article>

                    <article class="ac-accounting-video-card">
                        @if (trim((string) ($introSection['video_title'] ?? '')) !== '')
                            <div class="ac-accounting-video-card-body">
                                <h3>{{ $introSection['video_title'] }}</h3>
                            </div>
                        @endif

                        <div class="ac-accounting-video-frame-wrap" data-accounting-video-frame>
                            @if (($introVideo['embed_url'] ?? '') !== '')
                                <iframe
                                    data-accounting-video-iframe
                                    data-base-src="{{ $introVideo['embed_url'] }}"
                                    src="{{ $introVideo['embed_url'] }}"
                                    title="{{ trim((string) ($introSection['video_title'] ?? '')) !== '' ? $introSection['video_title'] : ($introSection['title'] ?? 'Accounting video') }}"
                                    loading="lazy"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    referrerpolicy="strict-origin-when-cross-origin"
                                    allowfullscreen
                                ></iframe>

                                @if (($introVideo['poster_url'] ?? '') !== '')
                                    <button
                                        type="button"
                                        class="ac-accounting-video-poster"
                                        data-accounting-video-activate
                                        aria-label="{{ $playVideoLabel }}: {{ trim((string) ($introSection['video_title'] ?? '')) !== '' ? $introSection['video_title'] : ($introSection['title'] ?? 'Accounting video') }}"
                                    >
                                        <span class="ac-accounting-video-poster-media" aria-hidden="true">
                                            <img src="{{ $introVideo['poster_url'] }}" alt="" loading="lazy">
                                        </span>
                                        <span class="ac-accounting-video-poster-shade" aria-hidden="true"></span>
                                        <span class="ac-accounting-video-poster-play" aria-hidden="true">
                                            <svg viewBox="0 0 384 512" fill="currentColor" focusable="false" aria-hidden="true">
                                                <path d="M73 39c-14.8-9.1-33.4-9.4-48.5-.9S0 62.5 0 80v352c0 17.5 9.4 33.8 24.5 42.9s33.7 8.2 48.5-.9l288-176c14.7-9 23-25 23-42.3s-8.3-33.4-23-42.3L73 39z"></path>
                                            </svg>
                                        </span>
                                    </button>
                                @endif
                            @else
                                <div class="ac-accounting-video-fallback">
                                    <span>{{ trim((string) ($introSection['video_title'] ?? '')) !== '' ? $introSection['video_title'] : 'Video' }}</span>
                                </div>
                            @endif
                        </div>
                    </article>
                </div>
            </div>
        </section>

        @if (trim((string) ($editorialSection['title'] ?? '')) !== '' || !empty($editorialCards))
            <section class="ac-support-story ac-accounting-editorial-section" aria-labelledby="ac-accounting-editorial-title">
                <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                    <div class="ac-support-story-hero ac-accounting-editorial-hero">
                        <div class="ac-support-story-shell">
                            <div class="ac-services-head ac-support-story-head ac-accounting-editorial-head">
                                @if (trim((string) ($editorialSection['eyebrow'] ?? '')) !== '')
                                    <div class="ac-services-eyebrow">
                                        <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                                        <p class="ac-services-kicker">{{ $editorialSection['eyebrow'] }}</p>
                                        <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                                    </div>
                                @endif

                                <h2 id="ac-accounting-editorial-title">
                                    <span>{{ $editorialSection['title'] }}</span>
                                </h2>

                                @if (trim((string) ($editorialSection['subtitle'] ?? '')) !== '')
                                    <p class="ac-services-intro">{{ $editorialSection['subtitle'] }}</p>
                                @endif

                                <div class="ac-services-divider" aria-hidden="true">
                                    <span class="ac-services-divider-line"></span>
                                    <span class="ac-services-divider-glyph"></span>
                                    <span class="ac-services-divider-line"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ac-support-story-grid ac-accounting-editorial-grid">
                        @foreach ($editorialCards as $card)
                            <article class="ac-support-story-card ac-accounting-editorial-card">
                                <span class="ac-accounting-editorial-card-badge" aria-hidden="true">
                                    {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                </span>
                                <div class="ac-accounting-editorial-card-inner">
                                    <h3>{{ $card['title'] ?? '' }}</h3>
                                    @if (trim((string) ($card['body'] ?? '')) !== '')
                                        <p class="ac-support-story-card-lead ac-accounting-editorial-card-copy">{{ $card['body'] }}</p>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @foreach ($detailServiceSections as $section)
            @php
                $sectionIcon = $detailSectionIcons[$section['icon'] ?? 'file-lines'] ?? $detailSectionIcons['file-lines'];
            @endphp
            <section id="{{ $section['anchor_id'] }}" class="ac-accounting-detail-section" aria-labelledby="ac-accounting-detail-title-{{ $section['slug'] }}">
                <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                    <div class="ac-accounting-detail-shell">
                        <div class="ac-accounting-detail-head">
                            <div class="ac-accounting-detail-title">
                                <div class="ac-accounting-detail-badge" aria-hidden="true">
                                    <span class="ac-accounting-detail-icon">
                                        <svg viewBox="{{ $sectionIcon['view_box'] }}" fill="currentColor">
                                            <use href="{{ $sectionIcon['href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span class="ac-accounting-detail-index">{{ $section['index_label'] }}</span>
                                </div>
                                <div class="ac-accounting-detail-heading">
                                    <h2 id="ac-accounting-detail-title-{{ $section['slug'] }}">{{ $section['title'] ?? '' }}</h2>
                                </div>
                            </div>

                            @if (trim((string) ($section['intro'] ?? '')) !== '')
                                <div class="ac-accounting-detail-intro-col">
                                    <p>{{ $section['intro'] }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="ac-accounting-detail-grid">
                            <div class="ac-accounting-detail-column">
                                @if (trim((string) ($section['list_title'] ?? '')) !== '')
                                    <p class="ac-accounting-detail-list-title">{{ $section['list_title'] }}</p>
                                @endif

                                @if (!empty($section['items']))
                                    <ul class="ac-accounting-detail-list">
                                        @foreach ($section['items'] as $item)
                                            <li>
                                                <span class="ac-accounting-detail-list-bullet" aria-hidden="true">
                                                    <svg viewBox="{{ $anchorLinkIcon['view_box'] }}" fill="currentColor">
                                                        <use href="{{ $anchorLinkIcon['href'] }}"></use>
                                                    </svg>
                                                </span>
                                                <span>{{ $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                @if (!empty($section['after_list']))
                                    <div class="ac-accounting-detail-after-list">
                                        @foreach ($section['after_list'] as $paragraph)
                                            <p>{{ $paragraph }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="ac-accounting-detail-column">
                                @if (!empty($section['downloads']))
                                    <div class="ac-accounting-detail-downloads">
                                        @foreach ($section['downloads'] as $download)
                                            <a href="{{ $download['url'] ?? '#' }}" class="ac-accounting-detail-download" target="_blank" rel="noopener noreferrer">
                                                <span class="ac-accounting-detail-download-title">{{ $download['title'] ?? '' }}</span>
                                                <span class="ac-accounting-detail-download-cta">{{ $download['label'] ?? ($isCroatianLocale ? 'Preuzmi' : 'Download') }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                @if (trim((string) ($section['quote'] ?? '')) !== '')
                                    <blockquote class="ac-accounting-detail-quote">
                                        <span class="ac-accounting-detail-quote-icon" aria-hidden="true">
                                            <svg viewBox="{{ $detailQuoteIcon['view_box'] }}" fill="currentColor">
                                                <use href="{{ $detailQuoteIcon['href'] }}"></use>
                                            </svg>
                                        </span>
                                        <p>{{ $section['quote'] }}</p>
                                    </blockquote>
                                @endif

                                @if (trim((string) ($section['cta_text'] ?? '')) !== '')
                                    <p class="ac-accounting-detail-cta-copy">{{ $section['cta_text'] }}</p>
                                @endif

                                @if (trim((string) ($section['cta_label'] ?? '')) !== '')
                                    <div class="ac-accounting-detail-action">
                                        <a
                                            href="{{ $section['cta_url'] ?? '#accounting-sastanak' }}"
                                            class="front-contact-submit inline-flex h-11 items-center justify-center px-6 text-sm font-semibold !text-white transition"
                                        >
                                            <span>{{ $section['cta_label'] }}</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endforeach

        @if ($accountingVideos->isNotEmpty())
            <section
                class="ac-support-story ac-home-blog ac-blog-related-section ac-family-blog-section ac-accounting-videos-section"
                @if ($videoSectionTitle !== '')
                    aria-labelledby="ac-accounting-videos-title"
                @else
                    aria-label="{{ $isCroatianLocale ? 'Video sekcija računovodstva' : 'Accounting video section' }}"
                @endif
            >
                <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                    @if ($hasAccountingVideoHead)
                        <div class="ac-support-story-hero">
                            <div class="ac-support-story-shell">
                                <div class="ac-services-head ac-support-story-head">
                                    @if ($videoSectionTitle !== '')
                                        <h2 id="ac-accounting-videos-title">
                                            <span>{{ $videoSectionTitle }}</span>
                                        </h2>
                                    @endif

                                    @if ($videoSectionIntro !== '')
                                        <p class="ac-services-intro">{{ $videoSectionIntro }}</p>
                                    @endif

                                    <div class="ac-services-divider" aria-hidden="true">
                                        <span class="ac-services-divider-line"></span>
                                        <span class="ac-services-divider-glyph"></span>
                                        <span class="ac-services-divider-line"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="ac-accounting-videos-carousel{{ $hasAccountingVideoHead ? '' : ' ac-accounting-videos-carousel--flush' }}">
                        <div id="ac-accounting-videos-splide" class="splide ac-accounting-videos-splide" data-accounting-videos-splide>
                            <div class="splide__track">
                                <ul class="splide__list">
                                    @foreach ($accountingVideos as $video)
                                        <li class="splide__slide ac-accounting-video-slide">
                                            <article class="ac-accounting-video-library-card">
                                                <div class="ac-accounting-video-library-frame" data-accounting-video-frame>
                                                    <iframe
                                                        data-accounting-video-iframe
                                                        data-base-src="{{ $video['embed_url'] }}"
                                                        src="{{ $video['embed_url'] }}"
                                                        title="{{ trim((string) ($video['title'] ?? '')) !== '' ? $video['title'] : ($videoSection['title'] ?? 'Video') }}"
                                                        loading="lazy"
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                        referrerpolicy="strict-origin-when-cross-origin"
                                                        allowfullscreen
                                                    ></iframe>

                                                    @if (trim((string) ($video['poster_url'] ?? '')) !== '')
                                                        <button
                                                            type="button"
                                                            class="ac-accounting-video-poster"
                                                            data-accounting-video-activate
                                                            aria-label="{{ $playVideoLabel }}: {{ trim((string) ($video['title'] ?? '')) !== '' ? $video['title'] : ($videoSection['title'] ?? 'Video') }}"
                                                        >
                                                            <span class="ac-accounting-video-poster-media" aria-hidden="true">
                                                                <img src="{{ $video['poster_url'] }}" alt="" loading="lazy">
                                                            </span>
                                                            <span class="ac-accounting-video-poster-shade" aria-hidden="true"></span>
                                                            <span class="ac-accounting-video-poster-play" aria-hidden="true">
                                                                <svg viewBox="0 0 384 512" fill="currentColor" focusable="false" aria-hidden="true">
                                                                    <path d="M73 39c-14.8-9.1-33.4-9.4-48.5-.9S0 62.5 0 80v352c0 17.5 9.4 33.8 24.5 42.9s33.7 8.2 48.5-.9l288-176c14.7-9 23-25 23-42.3s-8.3-33.4-23-42.3L73 39z"></path>
                                                                </svg>
                                                            </span>
                                                        </button>
                                                    @endif
                                                </div>

                                                @if (trim((string) ($video['title'] ?? '')) !== '')
                                                    <div class="ac-accounting-video-library-body">
                                                        <h3>{{ $video['title'] }}</h3>
                                                    </div>
                                                @endif
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

        <section class="ac-accounting-contact-shell">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <section id="accounting-sastanak" class="ac-family-section ac-accounting-contact pb-16 pt-12 md:pb-24 md:pt-16" aria-labelledby="ac-accounting-meeting-title">
                    <div class="ac-family-team-showcase-head">
                        <p class="ac-family-section-kicker">{{ $meetingSection['kicker'] ?? 'KONTAKT' }}</p>
                        <h2 id="ac-accounting-meeting-title">{{ $meetingSection['title'] ?? '' }}</h2>
                        <p>{{ $meetingSection['intro'] ?? '' }}</p>
                    </div>

                    <div class="mt-10 grid gap-5 lg:grid-cols-[300px_minmax(0,1fr)] lg:items-start">
                        <aside class="front-contact-sidebar">
                            <div class="front-contact-panel front-contact-panel--direct">
                                <h2>{{ $meetingSection['visit_title'] ?? 'Posjetite nas' }}</h2>
                                <div class="mt-4 space-y-1 text-[0.89rem] leading-6 text-slate-700">
                                    <p>{{ $meetingSection['visit_lines'][0] ?? '' }}</p>
                                    <p>{{ $meetingSection['visit_lines'][1] ?? '' }}</p>
                                </div>
                            </div>

                            <div class="front-contact-panel front-contact-panel--direct">
                                <h2>{{ $meetingSection['contact_title'] ?? 'Kontaktirajte nas' }}</h2>
                                <ul class="front-contact-direct-list">
                                    <li>
                                        <span>{{ $meetingSection['direct_phone_label'] ?? 'Telefon' }}</span>
                                        <a href="tel:{{ $contactPhoneHref }}">{{ $contactPhone }}</a>
                                    </li>
                                    <li>
                                        <span>{{ $meetingSection['direct_email_label'] ?? 'Email' }}</span>
                                        <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                                    </li>
                                </ul>
                            </div>
                        </aside>

                        <form
                            method="POST"
                            action="{{ route('contact.store') }}"
                            class="front-contact-form"
                            novalidate
                            data-contact-form
                            data-msg-name-required="{{ __('contact.validation.inline.name_required') }}"
                            data-msg-email-required="{{ __('contact.validation.inline.email_required') }}"
                            data-msg-email-invalid="{{ __('contact.validation.inline.email_invalid') }}"
                            data-msg-message-required="{{ __('contact.validation.inline.message_required') }}"
                            data-msg-message-min="{{ __('contact.validation.inline.message_min') }}"
                            data-msg-accept-terms="{{ __('contact.validation.inline.accept_terms') }}"
                            @if($captchaEnabled) data-recaptcha-form data-recaptcha-site-key="{{ $captchaSiteKey }}" data-recaptcha-action="contact_form" @endif
                        >
                            @csrf
                            <input type="hidden" name="recaptcha_token" value="" data-recaptcha-token>
                            <input type="hidden" name="redirect_to" value="{{ route('accounting.show') }}#accounting-sastanak">

                            @if (session('status'))
                                <div class="front-contact-status" role="status">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="accounting-first-name">{{ $meetingFormLabels['first_name'] ?? 'Ime' }}</label>
                                    <input id="accounting-first-name" type="text" name="first_name" value="{{ old('first_name') }}" class="front-contact-input h-11 w-full text-sm" required>
                                    <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('first_name') ? '' : 'hidden' }}" data-field-error="first_name">@error('first_name'){{ $message }}@enderror</p>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="accounting-last-name">{{ $meetingFormLabels['last_name'] ?? 'Prezime' }}</label>
                                    <input id="accounting-last-name" type="text" name="last_name" value="{{ old('last_name') }}" class="front-contact-input h-11 w-full text-sm">
                                    <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('last_name') ? '' : 'hidden' }}" data-field-error="last_name">@error('last_name'){{ $message }}@enderror</p>
                                </div>
                            </div>

                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="accounting-company">{{ $meetingFormLabels['company'] ?? 'Tvrtka' }}</label>
                                    <input id="accounting-company" type="text" name="company" value="{{ old('company') }}" class="front-contact-input h-11 w-full text-sm">
                                    <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('company') ? '' : 'hidden' }}" data-field-error="company">@error('company'){{ $message }}@enderror</p>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="accounting-phone">{{ $meetingFormLabels['phone'] ?? 'Broj telefona' }}</label>
                                    <input id="accounting-phone" type="text" name="phone" value="{{ old('phone') }}" class="front-contact-input h-11 w-full text-sm">
                                    <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('phone') ? '' : 'hidden' }}" data-field-error="phone">@error('phone'){{ $message }}@enderror</p>
                                </div>
                            </div>

                            <div class="mt-4">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="accounting-email">{{ $meetingFormLabels['email'] ?? 'Email' }}</label>
                                <input id="accounting-email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" class="front-contact-input h-11 w-full text-sm" required>
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('email') ? '' : 'hidden' }}" data-field-error="email">@error('email'){{ $message }}@enderror</p>
                            </div>

                            <div class="mt-4">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="accounting-subject">{{ $meetingFormLabels['subject'] ?? 'Naslov poruke' }}</label>
                                <input id="accounting-subject" type="text" name="subject" value="{{ old('subject') }}" class="front-contact-input h-11 w-full text-sm">
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('subject') ? '' : 'hidden' }}" data-field-error="subject">@error('subject'){{ $message }}@enderror</p>
                            </div>

                            <div class="mt-4">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="accounting-message">{{ $meetingFormLabels['message'] ?? 'Poruka' }}</label>
                                <textarea id="accounting-message" name="message" rows="8" class="front-contact-textarea w-full text-sm" required>{{ old('message') }}</textarea>
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('message') ? '' : 'hidden' }}" data-field-error="message">@error('message'){{ $message }}@enderror</p>
                            </div>

                            <div class="front-contact-consent-wrap">
                                <label class="front-contact-consent">
                                    <input type="checkbox" name="accept_terms" value="1" class="front-contact-checkbox mt-0.5 h-4 w-4 border-slate-300 text-slate-900 focus:ring-0" @checked((bool) old('accept_terms'))>
                                    <span>{{ __('contact.form.accept_terms') }}</span>
                                </label>
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('accept_terms') ? '' : 'hidden' }}" data-field-error="accept_terms">@error('accept_terms'){{ $message }}@enderror</p>
                            </div>

                            <div class="front-contact-form-actions">
                                <button type="submit" class="front-contact-submit inline-flex h-11 items-center justify-center px-6 text-sm font-semibold text-white transition">
                                    {{ $meetingSection['submit'] ?? 'Pošalji' }}
                                </button>
                                <p class="text-xs font-semibold text-rose-600 {{ $errors->has('recaptcha_token') ? '' : 'hidden' }}" data-field-error="recaptcha_token">@error('recaptcha_token'){{ $message }}@enderror</p>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </section>

        <section class="ac-support-story ac-home-blog ac-blog-related-section ac-family-blog-section ac-accounting-blog-section" aria-labelledby="ac-accounting-blog-title">
            <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                <div class="ac-support-story-hero">
                    <div class="ac-support-story-shell">
                        <div class="ac-services-head ac-support-story-head">
                            @if (trim((string) ($blogSection['kicker'] ?? '')) !== '')
                                <div class="ac-services-eyebrow">
                                    <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                                    <p class="ac-services-kicker">{{ $blogSection['kicker'] }}</p>
                                    <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                                </div>
                            @endif
                            <h2 id="ac-accounting-blog-title">
                                <span>{{ $blogSection['title'] ?? '' }}</span>
                            </h2>
                            <p class="ac-services-intro">{{ $blogSection['intro'] ?? '' }}</p>
                            <div class="ac-services-divider" aria-hidden="true">
                                <span class="ac-services-divider-line"></span>
                                <span class="ac-services-divider-glyph"></span>
                                <span class="ac-services-divider-line"></span>
                            </div>
                        </div>
                    </div>
                </div>

                @if (($accountingPosts ?? collect())->isNotEmpty())
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
                                            $postImageUrl = $postImage?->getUrl();
                                            $primaryCategory = $post->categories
                                                ->sortByDesc(fn ($category) => (int) ($category->pivot->is_primary ?? false))
                                                ->first();
                                            $categoryTranslation = $primaryCategory?->translations->firstWhere('locale', $locale)
                                                ?? $primaryCategory?->translations->firstWhere('locale', $fallbackLocale);
                                            $categoryLabel = trim((string) ($categoryTranslation?->name ?? ($accountingCategoryName ?? 'Novosti')));
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
                                                                loading="lazy"
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
                @else
                    <div class="ac-accounting-empty-state">
                        <p>{{ $blogSection['empty'] ?? 'Novosti iz ove kategorije uskoro će biti dostupne.' }}</p>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
    @endpush
@endonce

@push('styles')
    <style>
        .ac-accounting-page {
            background:
                radial-gradient(46% 32% at 100% 0%, rgba(95, 127, 145, 0.12), transparent 72%),
                linear-gradient(180deg, #eef4f6 0%, #f8fbfc 100%);
        }

        .ac-accounting-overview-section {
            scroll-margin-top: 7rem;
            padding: clamp(1.8rem, 3.4vw, 2.75rem) 0 clamp(3.25rem, 6vw, 5rem);
            background: linear-gradient(180deg, #f5f8fb 0%, #eef4f8 100%);
        }

        .ac-accounting-overview-hero .ac-support-story-head {
            max-width: 54rem;
            margin: 0 auto;
            text-align: center;
        }

        .ac-accounting-overview-hero .ac-services-eyebrow {
            justify-content: center;
        }

        .ac-accounting-overview-hero .ac-services-eyebrow-line {
            display: none;
        }

        .ac-accounting-overview-hero .ac-services-divider {
            justify-content: center;
        }

        .ac-accounting-anchor-nav {
            margin: 0;
        }

        .ac-accounting-copy-head {
            padding: 0 0 0.95rem;
        }

        .ac-accounting-copy-head h3,
        .ac-accounting-video-card h3 {
            margin: 0;
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.35rem, 2.1vw, 1.8rem);
            line-height: 1.12;
            color: #0f1b2d;
            text-wrap: balance;
        }

        .ac-accounting-anchor-list {
            display: grid;
            gap: 0.95rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .ac-accounting-anchor-list li {
            display: flex;
        }

        .ac-accounting-anchor-link {
            display: inline-flex;
            align-items: center;
            gap: 0.58rem;
            width: 100%;
            min-height: 3rem;
            padding: 0.72rem 1rem;
            border: 1px solid rgba(148, 163, 184, 0.24);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.96);
            font-size: 0.95rem;
            font-weight: 600;
            line-height: 1.6;
            color: #365a72;
            text-decoration: none;
            transition: color 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
        }

        .ac-accounting-anchor-link-icon {
            display: inline-flex;
            width: 1.15rem;
            height: 1.15rem;
            flex: none;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: rgba(171, 141, 82, 0.12);
            color: #8a6a38;
        }

        .ac-accounting-anchor-link-icon svg {
            width: 0.7rem;
            height: 0.7rem;
        }

        .ac-accounting-anchor-link:hover,
        .ac-accounting-anchor-link:focus-visible {
            color: #0f1b2d;
            transform: translateX(0.16rem);
            border-color: rgba(54, 90, 114, 0.28);
        }

        .ac-accounting-overview-grid {
            display: grid;
            margin-top: clamp(1.75rem, 3vw, 2.35rem);
            gap: clamp(1.75rem, 3vw, 2.65rem);
            grid-template-columns: minmax(0, 4fr) minmax(0, 8fr);
            align-items: start;
        }

        .ac-accounting-overview-copy {
            padding-top: 0;
        }

        .ac-accounting-overview-body {
            display: grid;
            gap: 1rem;
        }

        .ac-accounting-overview-body p {
            margin: 0;
            font-size: 1.02rem;
            line-height: 1.84;
            color: #425466;
        }

        .ac-accounting-video-card-body {
            padding: 0 0 0.95rem;
        }

        .ac-accounting-video-frame-wrap {
            position: relative;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            border-radius: 1.5rem;
            background: #0f1b2d;
            box-shadow: 0 22px 42px rgba(15, 27, 45, 0.16);
        }

        .ac-accounting-video-frame-wrap iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .ac-accounting-video-library-frame {
            position: relative;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            background: #0f1b2d;
        }

        .ac-accounting-video-library-frame iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .ac-accounting-video-poster {
            position: absolute;
            inset: 0;
            display: block;
            width: 100%;
            height: 100%;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
            z-index: 2;
            transition: opacity 0.24s ease, visibility 0.24s ease;
        }

        .ac-accounting-video-poster-media,
        .ac-accounting-video-poster-media img {
            display: block;
            width: 100%;
            height: 100%;
        }

        .ac-accounting-video-poster-media img {
            object-fit: cover;
        }

        .ac-accounting-video-poster-shade {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(15, 27, 45, 0.16) 0%, rgba(15, 27, 45, 0.34) 100%);
        }

        .ac-accounting-video-poster-play {
            position: absolute;
            top: 50%;
            left: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: clamp(3.7rem, 10vw, 4.9rem);
            height: clamp(3.7rem, 10vw, 4.9rem);
            border-radius: 999px;
            color: #0f1b2d;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 18px 36px rgba(15, 27, 45, 0.24);
            transform: translate(-50%, -50%);
            transition: transform 0.22s ease, box-shadow 0.22s ease;
        }

        .ac-accounting-video-poster-play svg {
            width: 1.1rem;
            height: 1.1rem;
            margin-left: 0.16rem;
            display: block;
        }

        .ac-accounting-video-poster:hover .ac-accounting-video-poster-play,
        .ac-accounting-video-poster:focus-visible .ac-accounting-video-poster-play {
            transform: translate(-50%, -50%) scale(1.06);
            box-shadow: 0 22px 40px rgba(15, 27, 45, 0.3);
        }

        .ac-accounting-video-frame-wrap.is-active .ac-accounting-video-poster,
        .ac-accounting-video-library-frame.is-active .ac-accounting-video-poster {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .ac-accounting-video-fallback {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.84);
            text-align: center;
            padding: 1rem;
        }

        .ac-accounting-editorial-section {
            padding: clamp(2.2rem, 4vw, 3rem) 0;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.34), rgba(255, 255, 255, 0.08)),
                repeating-linear-gradient(90deg, rgba(15, 42, 67, 0.05) 0 1px, transparent 1px 24px),
                radial-gradient(54% 76% at 86% 18%, rgba(65, 122, 176, 0.16), transparent 62%),
                radial-gradient(38% 56% at 12% 84%, rgba(171, 141, 82, 0.08), transparent 68%),
                linear-gradient(120deg, #eef3f7 0%, #e7eef4 48%, #dde7f0 100%);
            overflow: hidden;
        }

        .ac-accounting-editorial-head {
            max-width: 56rem;
            margin: 0 auto;
            padding: clamp(0.2rem, 1vw, 0.6rem) 0 0;
            text-align: center;
        }

        .ac-accounting-editorial-head .ac-services-eyebrow,
        .ac-accounting-editorial-head .ac-services-divider {
            justify-content: center;
        }

        .ac-accounting-editorial-head .ac-services-eyebrow-line {
            display: none;
        }

        .ac-accounting-editorial-head .ac-services-kicker {
            padding: 0.45rem 0.9rem;
            border: 1px solid rgba(148, 163, 184, 0.28);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 10px 24px rgba(15, 27, 45, 0.05);
            letter-spacing: 0.18em;
            color: #365a72;
        }

        .ac-accounting-editorial-head h2 {
            margin: 0 auto;
            width: 100%;
            max-width: none;
            font-size: clamp(1.72rem, 2.45vw, 2.5rem);
            line-height: 1.08;
            letter-spacing: -0.02em;
            color: #0f1b2d;
        }

        .ac-accounting-editorial-head .ac-services-divider-line {
            background: rgba(148, 163, 184, 0.26);
        }

        .ac-accounting-editorial-head .ac-services-divider-glyph {
            border-color: rgba(148, 163, 184, 0.24);
            color: rgba(95, 127, 145, 0.52);
        }

        .ac-accounting-editorial-head h2 span {
            display: block;
            white-space: normal;
            text-wrap: balance;
        }

        .ac-accounting-editorial-head .ac-services-intro {
            max-width: 43rem;
            margin: 0.9rem auto 0;
            font-size: clamp(0.98rem, 1.2vw, 1.08rem);
            line-height: 1.72;
            color: #4d6175;
            text-wrap: balance;
        }

        .ac-accounting-editorial-grid {
            margin-top: clamp(2rem, 4vw, 2.75rem);
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.35rem;
        }

        .ac-accounting-editorial-card {
            position: relative;
            min-height: 18rem;
            padding: 1.6rem 1.4rem 1.45rem;
        }

        .ac-accounting-editorial-card-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.7rem;
            height: 2.7rem;
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 999px;
            background: linear-gradient(180deg, rgba(250, 251, 252, 0.96), rgba(241, 245, 249, 0.96));
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: #6b7f92;
        }

        .ac-accounting-editorial-card-inner {
            max-width: calc(100% - 4.6rem);
        }

        .ac-accounting-editorial-card h3 {
            max-width: none;
        }

        .ac-accounting-editorial-card-copy {
            max-width: none;
            font-size: 0.95rem;
            line-height: 1.74;
            color: #4a5b6e;
        }

        .ac-accounting-detail-section {
            scroll-margin-top: 7rem;
            padding: clamp(2.6rem, 4.4vw, 3.5rem) 0;
            border-top: 1px solid rgba(171, 141, 82, 0.16);
            border-bottom: 1px solid rgba(171, 141, 82, 0.16);
            background: linear-gradient(180deg, #f8f4eb 0%, #fbf8f2 100%);
        }

        .ac-accounting-detail-section + .ac-accounting-detail-section {
            border-top: 0;
        }

        .ac-accounting-detail-shell {
            display: grid;
            gap: 2.1rem;
        }

        .ac-accounting-detail-head {
            display: grid;
            gap: 1.8rem;
            align-items: start;
        }

        .ac-accounting-detail-title {
            display: flex;
            gap: 1.15rem;
            align-items: flex-start;
            min-width: 0;
        }

        .ac-accounting-detail-badge {
            display: grid;
            gap: 0.65rem;
            justify-items: center;
            flex: none;
        }

        .ac-accounting-detail-icon {
            display: inline-flex;
            width: 3.6rem;
            height: 3.6rem;
            flex: none;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(171, 141, 82, 0.24);
        }

        .ac-accounting-detail-icon svg {
            width: 1.5rem;
            height: 1.5rem;
            color: #2d2925;
        }

        .ac-accounting-detail-index {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.55rem;
            min-height: 1.65rem;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            background: rgba(120, 96, 58, 0.08);
            color: #6d5633;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .ac-accounting-detail-heading {
            min-width: 0;
        }

        .ac-accounting-detail-heading h2 {
            margin: 0;
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.6rem, 2.3vw, 2.3rem);
            line-height: 1.15;
            font-weight: 600;
            color: #0f172a;
            text-wrap: balance;
            overflow-wrap: anywhere;
        }

        .ac-accounting-detail-intro-col {
            min-width: 0;
            max-width: 40rem;
        }

        .ac-accounting-detail-intro-col p {
            margin: 0;
            font-size: 0.98rem;
            line-height: 1.7;
            color: #403a34;
            text-wrap: pretty;
        }

        .ac-accounting-detail-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
            gap: 1.35rem 2rem;
        }

        .ac-accounting-detail-column {
            min-width: 0;
            padding-top: 0.15rem;
        }

        .ac-accounting-detail-list-title {
            margin: 0;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #6d5633;
            font-family: 'Sora', 'Public Sans', 'Segoe UI', Arial, sans-serif;
        }

        .ac-accounting-detail-list {
            margin: 1.35rem 0 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 0.8rem;
            color: #403a34;
        }

        .ac-accounting-detail-list li {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: 0.72rem;
            align-items: start;
            font-size: 0.98rem;
            line-height: 1.7rem;
            color: #403a34;
        }

        .ac-accounting-detail-after-list {
            margin-top: 1.15rem;
            display: grid;
            gap: 0.9rem;
        }

        .ac-accounting-detail-after-list p {
            margin: 0;
            font-size: 0.98rem;
            line-height: 1.72;
            color: #403a34;
        }

        .ac-accounting-detail-list-bullet {
            display: inline-flex;
            width: 1.15rem;
            height: 1.15rem;
            flex: none;
            align-items: center;
            justify-content: center;
            margin-top: 0.22rem;
            border-radius: 999px;
            background: rgba(171, 141, 82, 0.12);
            color: #7d6134;
        }

        .ac-accounting-detail-list-bullet svg {
            width: 0.42rem;
            height: 0.68rem;
        }

        .ac-accounting-detail-quote {
            position: relative;
            margin: 0;
            padding: 1.2rem 1.35rem 1.2rem 3.5rem;
            border-left: 2px solid rgba(171, 141, 82, 0.26);
            background: rgba(255, 255, 255, 0.58);
        }

        .ac-accounting-detail-quote-icon {
            position: absolute;
            top: 1.05rem;
            left: 1.2rem;
            display: inline-flex;
            width: 1.35rem;
            height: 1.35rem;
            align-items: center;
            justify-content: center;
            color: #8a6a38;
        }

        .ac-accounting-detail-quote-icon svg {
            width: 1rem;
            height: 1rem;
        }

        .ac-accounting-detail-quote p {
            margin: 0;
            font-size: 1rem;
            line-height: 1.82;
            color: #2d2925;
        }

        .ac-accounting-detail-downloads {
            display: grid;
            gap: 0.9rem;
            margin-bottom: 1.1rem;
        }

        .ac-accounting-detail-download {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.1rem;
            border: 1px solid rgba(171, 141, 82, 0.16);
            background: rgba(255, 255, 255, 0.58);
            color: #2d2925;
            text-decoration: none;
            transition: border-color 0.18s ease, background-color 0.18s ease;
        }

        .ac-accounting-detail-download:hover,
        .ac-accounting-detail-download:focus-visible {
            border-color: rgba(171, 141, 82, 0.28);
            background: rgba(255, 255, 255, 0.76);
        }

        .ac-accounting-detail-download-title {
            font-size: 0.98rem;
            line-height: 1.55;
            color: #2d2925;
        }

        .ac-accounting-detail-download-cta {
            flex: none;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #8a6a38;
        }

        .ac-accounting-detail-cta-copy {
            margin: 1rem 0 0;
            font-size: 0.96rem;
            line-height: 1.7;
            color: #5b5148;
        }

        .ac-accounting-detail-action {
            margin-top: 1.4rem;
        }

        .ac-accounting-videos-section {
            padding-top: clamp(4.2rem, 6vw, 5.2rem);
            padding-bottom: clamp(4.3rem, 6.5vw, 5.4rem);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.34), rgba(255, 255, 255, 0.12)),
                repeating-linear-gradient(90deg, rgba(15, 42, 67, 0.05) 0 1px, transparent 1px 24px),
                radial-gradient(48% 74% at 86% 16%, rgba(65, 122, 176, 0.12), transparent 62%),
                radial-gradient(34% 52% at 14% 84%, rgba(171, 141, 82, 0.06), transparent 68%),
                linear-gradient(120deg, #eef3f7 0%, #e8eff5 52%, #dfe9f2 100%);
        }

        .ac-accounting-videos-carousel {
            max-width: 100%;
            margin: 2rem auto 0;
        }

        .ac-accounting-videos-carousel--flush {
            margin-top: 0;
        }

        .ac-accounting-videos-splide .splide__track {
            overflow: hidden;
        }

        .ac-accounting-videos-splide .splide__list {
            align-items: stretch;
        }

        .ac-accounting-video-slide {
            display: flex;
            height: auto;
        }

        .ac-accounting-video-slide .ac-accounting-video-library-card {
            width: 100%;
            min-height: 100%;
        }

        .ac-accounting-video-library-card {
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: var(--front-card-radius);
            background: rgba(255, 255, 255, 0.92);
            height: 100%;
        }

        .ac-accounting-video-library-frame {
            border-bottom: 1px solid rgba(15, 27, 45, 0.08);
        }

        .ac-accounting-video-library-body {
            padding: 1.15rem 1.2rem 1.3rem;
        }

        .ac-accounting-video-library-body h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.5;
            color: #112033;
        }

        @media (min-width: 960px) {
            .ac-accounting-detail-head {
                grid-template-columns: minmax(0, 0.86fr) minmax(0, 1fr);
                gap: 2.4rem;
            }
        }

        .ac-accounting-videos-splide .splide__pagination {
            bottom: -2.1rem;
        }

        .ac-accounting-videos-splide .splide__pagination__page {
            width: 0.48rem;
            height: 0.48rem;
            margin: 0 0.22rem;
            background: rgba(54, 90, 114, 0.22);
            opacity: 1;
        }

        .ac-accounting-videos-splide .splide__pagination__page.is-active {
            background: #365a72;
            transform: scale(1.15);
        }

        .ac-accounting-contact-shell {
            background: linear-gradient(180deg, #eaf4fb 0%, #f4f9fd 100%);
        }

        .ac-accounting-contact {
            position: relative;
            z-index: 1;
            margin-top: 0;
        }

        .ac-accounting-blog-section {
            padding-bottom: clamp(8rem, 14vw, 11rem);
        }

        .ac-accounting-empty-state {
            margin-top: 2rem;
            padding: 1.4rem 1.5rem;
            border: 1px solid rgba(95, 127, 145, 0.2);
            border-radius: 1.25rem;
            background: rgba(255, 255, 255, 0.72);
            text-align: center;
            color: #475569;
        }

        @media (max-width: 767px) {
            .ac-accounting-overview-section {
                padding-top: 1.65rem;
                padding-bottom: 2.9rem;
            }

            .ac-accounting-anchor-nav {
                margin-top: 1.35rem;
            }

            .ac-accounting-overview-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .ac-accounting-video-card-body {
                padding-bottom: 0.8rem;
            }

            .ac-accounting-editorial-section {
                padding-bottom: 3.2rem;
            }

            .ac-accounting-editorial-grid {
                margin-top: 1rem;
                grid-template-columns: minmax(0, 1fr);
                padding: 0;
            }

            .ac-accounting-editorial-card {
                min-height: auto;
            }

            .ac-accounting-editorial-card-inner {
                max-width: none;
            }

            .ac-accounting-editorial-card-copy {
                max-width: none;
            }

            .ac-accounting-detail-section {
                padding: 2.2rem 0;
            }

            .ac-accounting-videos-section {
                padding-top: 3.8rem;
                padding-bottom: 4rem;
            }

            .ac-accounting-videos-carousel {
                margin-top: 1.2rem;
                max-width: 100%;
            }

            .ac-accounting-video-library-body {
                padding: 1rem 1rem 1.15rem;
            }

            .ac-accounting-detail-head,
            .ac-accounting-detail-grid {
                grid-template-columns: minmax(0, 1fr);
                gap: 1.2rem;
            }

            .ac-accounting-contact-shell {
                padding-left: 0;
                padding-right: 0;
            }
        }
    </style>
@endpush

@include('front.desktop.contact.partials.form-script', [
    'captchaEnabled' => $captchaEnabled,
    'captchaSiteKey' => $captchaSiteKey,
])

@once
    @push('scripts')
        <script defer src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
    @endpush
@endonce

@push('scripts')
    <script>
        (function () {
            const shouldFocusSection = {{ ($errors->any() || session('status')) ? 'true' : 'false' }};
            const section = document.getElementById('accounting-sastanak');

            if (shouldFocusSection && section) {
                requestAnimationFrame(function () {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            }

            document.querySelectorAll('[data-accounting-video-frame]').forEach(function (frame) {
                if (frame.dataset.videoReady === '1') {
                    return;
                }

                frame.dataset.videoReady = '1';

                const button = frame.querySelector('[data-accounting-video-activate]');
                const iframe = frame.querySelector('[data-accounting-video-iframe]');

                if (!button || !iframe) {
                    return;
                }

                button.addEventListener('click', function () {
                    const baseSrc = iframe.dataset.baseSrc || iframe.getAttribute('src') || '';

                    try {
                        const url = new URL(baseSrc, window.location.origin);
                        url.searchParams.set('autoplay', '1');
                        url.searchParams.set('playsinline', '1');
                        iframe.src = url.toString();
                    } catch (error) {
                        iframe.src = baseSrc + (baseSrc.includes('?') ? '&' : '?') + 'autoplay=1&playsinline=1';
                    }

                    frame.classList.add('is-active');
                });
            });

            const mountSplide = function (el, options) {
                if (el.dataset.splideReady === '1') {
                    return;
                }

                el.dataset.splideReady = '1';

                const slider = new window.Splide(el, options);
                slider.mount();
            };

            const initAccountingSliders = function () {
                if (typeof window.Splide !== 'function') {
                    return false;
                }

                document.querySelectorAll('[data-accounting-blog-splide]').forEach(function (el) {
                    const count = el.querySelectorAll('.splide__slide').length;
                    mountSplide(el, {
                        type: count > 1 ? 'loop' : 'slide',
                        perPage: Math.min(3, Math.max(1, count)),
                        perMove: 1,
                        gap: '1.25rem',
                        drag: count > 1,
                        snap: true,
                        pagination: count > 1,
                        arrows: count > 1,
                        updateOnMove: true,
                        speed: 520,
                        breakpoints: {
                            1180: { perPage: Math.min(2, Math.max(1, count)) },
                            760: { perPage: 1, gap: '1rem' },
                        },
                    });
                });

                document.querySelectorAll('[data-accounting-videos-splide]').forEach(function (el) {
                    const count = el.querySelectorAll('.splide__slide').length;
                    mountSplide(el, {
                        type: count > 2 ? 'loop' : 'slide',
                        perPage: Math.min(2, Math.max(1, count)),
                        perMove: 1,
                        gap: '1.4rem',
                        drag: count > 1,
                        snap: true,
                        pagination: count > 1,
                        arrows: false,
                        updateOnMove: true,
                        speed: 520,
                        breakpoints: {
                            760: { perPage: 1, gap: '1rem' },
                        },
                    });
                });

                return true;
            };

            if (initAccountingSliders()) {
                return;
            }

            let attempts = 0;
            const timer = window.setInterval(function () {
                attempts += 1;
                if (initAccountingSliders() || attempts > 40) {
                    window.clearInterval(timer);
                }
            }, 120);
        }());
    </script>
@endpush
