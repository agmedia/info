@extends('front.desktop.layouts.store')

@php
    $captchaSiteKey = trim((string) ($storeSettings['captcha']['recaptcha_v3_site_key'] ?? ''));
    $captchaEnabled = (bool) ($storeSettings['captcha']['recaptcha_v3_enabled'] ?? false) && $captchaSiteKey !== '';
    $contactEmail = trim((string) ($storeSettings['footer']['email_support'] ?? '')) ?: 'info@alphacapitalis.com';
    $contactPhone = trim((string) ($storeSettings['footer']['phone'] ?? '')) ?: '+385 (1) 580 6656';
    $contactPhoneHref = preg_replace('/\s+/', '', $contactPhone);
    $taxSprite = asset('front-theme/fonts/sprites/solid.svg');
    $meetingFormLabels = $meetingSection['form_labels'] ?? [];
    $overviewBody = array_values($overviewSection['body'] ?? []);
    $overviewHighlights = array_values($overviewSection['highlights'] ?? []);
    $reviewBody = array_values($reviewSection['body'] ?? []);
    $reviewHighlights = array_values($reviewSection['highlights'] ?? []);
    $optimizationBody = array_values($optimizationSection['body'] ?? []);
    $optimizationPrimaryBody = count($optimizationBody) > 1 ? array_slice($optimizationBody, 0, -1) : $optimizationBody;
    $optimizationSecondaryBody = count($optimizationBody) > 1 ? array_slice($optimizationBody, -1) : [];
    $dueDiligenceBody = array_values($dueDiligenceSection['body'] ?? []);
    $dueDiligencePrimaryBody = count($dueDiligenceBody) > 1 ? array_slice($dueDiligenceBody, 0, -1) : $dueDiligenceBody;
    $dueDiligenceSecondaryBody = count($dueDiligenceBody) > 1 ? array_slice($dueDiligenceBody, -1) : [];
    $transferPricingBody = array_values($transferPricingSection['body'] ?? []);
    $transferPricingPrimaryBody = count($transferPricingBody) > 1 ? array_slice($transferPricingBody, 0, -1) : $transferPricingBody;
    $transferPricingSecondaryBody = count($transferPricingBody) > 1 ? array_slice($transferPricingBody, -1) : [];
    $complianceGroups = array_values($complianceSection['corporate']['groups'] ?? []);
    $isCroatianLocale = str_starts_with(strtolower((string) $locale), 'hr');
    $readMoreLabel = $isCroatianLocale ? 'Opširnije' : 'Read more';
    $taxSectionMeta = [
        'overview' => [
            'number' => '01',
            'icon_view_box' => '0 0 384 512',
            'icon_href' => $taxSprite.'#file-lines',
        ],
        'services' => [
            'number' => '02',
            'icon_view_box' => '0 0 512 512',
            'icon_href' => $taxSprite.'#magnifying-glass',
        ],
        'compliance' => [
            'number' => '03',
            'icon_view_box' => '0 0 512 512',
            'icon_href' => $taxSprite.'#building-columns',
        ],
        'review' => [
            'number' => '04',
            'icon_view_box' => '0 0 512 512',
            'icon_href' => $taxSprite.'#bullseye',
        ],
        'optimization' => [
            'number' => '05',
            'icon_view_box' => '0 0 512 512',
            'icon_href' => $taxSprite.'#chart-line',
        ],
        'due_diligence' => [
            'number' => '06',
            'icon_view_box' => '0 0 512 512',
            'icon_href' => $taxSprite.'#magnifying-glass',
        ],
        'transfer_pricing' => [
            'number' => '07',
            'icon_view_box' => '0 0 512 512',
            'icon_href' => $taxSprite.'#arrow-right-arrow-left',
        ],
    ];
    $taxListBullet = [
        'view_box' => '0 0 256 512',
        'href' => $taxSprite.'#angle-right',
    ];
    $taxCtaIcon = [
        'view_box' => '0 0 320 512',
        'href' => $taxSprite.'#angle-down',
    ];
    $taxQuoteIcon = [
        'view_box' => '0 0 448 512',
        'href' => $taxSprite.'#quote-right',
    ];
@endphp

@section('title', $servicePageMetaTitle !== '' ? $servicePageMetaTitle : ($servicePageTitle ?? 'Porezi'))
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-tax-page">
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
                                    <span class="is-subtitle-lead">{{ $heroSection['subtitle_lead'] ?? 'Porezno' }}</span>
                                    <span class="is-subtitle-accent">{{ $heroSection['subtitle_accent'] ?? 'savjetovanje' }}</span>
                                </span>
                            </h1>

                            <p class="ac-family-hero-intro">{{ $heroSection['intro'] ?? '' }}</p>

                            <div class="ac-family-hero-actions">
                                <a href="{{ $heroSection['cta_url'] ?? '#tax-overview' }}" class="front-action-cta">
                                    <span>{{ $heroSection['cta_label'] ?? 'Pregledajte usluge' }}</span>
                                    <svg viewBox="{{ $taxCtaIcon['view_box'] }}" fill="currentColor" aria-hidden="true">
                                        <use href="{{ $taxCtaIcon['href'] }}"></use>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="tax-overview" class="ac-tax-shell-section ac-tax-shell-section--hero" aria-labelledby="ac-tax-overview-title">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-support-story-hero">
                    <div class="ac-support-story-shell">
                        <div class="ac-services-head ac-support-story-head">
                            <div class="ac-services-eyebrow">
                                <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                                <p class="ac-services-kicker">{{ $overviewSection['kicker'] ?? 'POREZI' }}</p>
                                <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                            </div>
                            <h2 id="ac-tax-overview-title">
                                <span>{{ $overviewSection['title'] ?? '' }}</span>
                            </h2>
                            <p class="ac-services-intro">{{ $overviewSection['intro'] ?? '' }}</p>
                            <div class="ac-services-divider" aria-hidden="true">
                                <span class="ac-services-divider-line"></span>
                                <span class="ac-services-divider-glyph"></span>
                                <span class="ac-services-divider-line"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ac-tax-overview-grid">
                    @php
                        $overviewMeta = $taxSectionMeta['overview'];
                    @endphp
                    <article class="ac-tax-panel ac-tax-panel--narrative">
                        <div class="ac-tax-section-title">
                            <div class="ac-tax-badge">
                                <span class="ac-tax-icon" aria-hidden="true">
                                    <svg viewBox="{{ $overviewMeta['icon_view_box'] }}" fill="currentColor">
                                        <use href="{{ $overviewMeta['icon_href'] }}"></use>
                                    </svg>
                                </span>
                                <span class="ac-tax-index">{{ $overviewMeta['number'] }}</span>
                            </div>
                            <div class="ac-tax-title-copy">
                                <h2>{{ $overviewSection['highlight_title'] ?? '' }}</h2>
                            </div>
                        </div>

                        <div class="ac-tax-copy">
                            @foreach ($overviewBody as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </article>

                    <article class="ac-tax-panel ac-tax-panel--highlight">
                        @if (trim((string) ($overviewSection['highlights_title'] ?? '')) !== '')
                            <div class="ac-tax-panel-head">
                                <h3 class="ac-tax-panel-title">{{ $overviewSection['highlights_title'] }}</h3>
                            </div>
                        @endif

                        <ul class="ac-tax-list">
                            @foreach ($overviewHighlights as $item)
                                <li>
                                    <span class="ac-tax-list-bullet" aria-hidden="true">
                                        <svg viewBox="{{ $taxListBullet['view_box'] }}" fill="currentColor">
                                            <use href="{{ $taxListBullet['href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </article>
                </div>
            </div>
        </section>

        <section id="tax-services" class="ac-tax-shell-section" aria-labelledby="ac-tax-services-title">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                @php
                    $servicesMeta = $taxSectionMeta['services'];
                @endphp
                <div class="ac-tax-section-head">
                    <div class="ac-tax-section-title">
                        <div class="ac-tax-badge">
                            <span class="ac-tax-icon" aria-hidden="true">
                                <svg viewBox="{{ $servicesMeta['icon_view_box'] }}" fill="currentColor">
                                    <use href="{{ $servicesMeta['icon_href'] }}"></use>
                                </svg>
                            </span>
                            <span class="ac-tax-index">{{ $servicesMeta['number'] }}</span>
                        </div>
                        <div class="ac-tax-title-copy">
                            <p class="ac-family-section-kicker">{{ $servicesSection['kicker'] ?? 'USLUGE' }}</p>
                            <h2 id="ac-tax-services-title">{{ $servicesSection['title'] ?? '' }}</h2>
                        </div>
                    </div>
                    <div class="ac-tax-section-intro">
                        <p>{{ $servicesSection['intro'] ?? '' }}</p>
                    </div>
                </div>

                <div class="ac-tax-card-grid">
                    @foreach (($servicesSection['items'] ?? []) as $item)
                        <article class="ac-tax-card">
                            <span class="ac-tax-card-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $item['title'] ?? '' }}</h3>
                            <p>{{ $item['text'] ?? '' }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="tax-compliance" class="ac-tax-shell-section ac-tax-shell-section--muted" aria-labelledby="ac-tax-compliance-title">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                @php
                    $complianceMeta = $taxSectionMeta['compliance'];
                @endphp
                <div class="ac-tax-section-head">
                    <div class="ac-tax-section-title">
                        <div class="ac-tax-badge">
                            <span class="ac-tax-icon" aria-hidden="true">
                                <svg viewBox="{{ $complianceMeta['icon_view_box'] }}" fill="currentColor">
                                    <use href="{{ $complianceMeta['icon_href'] }}"></use>
                                </svg>
                            </span>
                            <span class="ac-tax-index">{{ $complianceMeta['number'] }}</span>
                        </div>
                        <div class="ac-tax-title-copy">
                            <p class="ac-family-section-kicker">{{ $complianceSection['kicker'] ?? 'TAX COMPLIANCE' }}</p>
                            <h2 id="ac-tax-compliance-title">{{ $complianceSection['title'] ?? '' }}</h2>
                        </div>
                    </div>
                    <div class="ac-tax-section-intro">
                        <p>{{ $complianceSection['intro'] ?? '' }}</p>
                    </div>
                </div>

                <div class="ac-tax-compliance-grid">
                    <article class="ac-tax-panel">
                        <h3>{{ $complianceSection['corporate']['title'] ?? '' }}</h3>
                        <p class="ac-tax-panel-intro">{{ $complianceSection['corporate']['intro'] ?? '' }}</p>

                        <div class="ac-tax-group-grid">
                            @foreach ($complianceGroups as $group)
                                <div class="ac-tax-subpanel">
                                    <h4>{{ $group['title'] ?? '' }}</h4>
                                    <ul class="ac-tax-list">
                                        @foreach (($group['items'] ?? []) as $item)
                                            <li>
                                                <span class="ac-tax-list-bullet" aria-hidden="true">
                                                    <svg viewBox="{{ $taxListBullet['view_box'] }}" fill="currentColor">
                                                        <use href="{{ $taxListBullet['href'] }}"></use>
                                                    </svg>
                                                </span>
                                                <span>{{ $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </article>

                    <article class="ac-tax-panel">
                        <h3>{{ $complianceSection['individual']['title'] ?? '' }}</h3>
                        <p class="ac-tax-panel-intro">{{ $complianceSection['individual']['intro'] ?? '' }}</p>
                        <ul class="ac-tax-list">
                            @foreach (($complianceSection['individual']['items'] ?? []) as $item)
                                <li>
                                    <span class="ac-tax-list-bullet" aria-hidden="true">
                                        <svg viewBox="{{ $taxListBullet['view_box'] }}" fill="currentColor">
                                            <use href="{{ $taxListBullet['href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </article>
                </div>
            </div>
        </section>

        <section id="tax-review" class="ac-tax-shell-section" aria-labelledby="ac-tax-review-title">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                @php
                    $reviewMeta = $taxSectionMeta['review'];
                @endphp
                <div class="ac-tax-section-head">
                    <div class="ac-tax-section-title">
                        <div class="ac-tax-badge">
                            <span class="ac-tax-icon" aria-hidden="true">
                                <svg viewBox="{{ $reviewMeta['icon_view_box'] }}" fill="currentColor">
                                    <use href="{{ $reviewMeta['icon_href'] }}"></use>
                                </svg>
                            </span>
                            <span class="ac-tax-index">{{ $reviewMeta['number'] }}</span>
                        </div>
                        <div class="ac-tax-title-copy">
                            <p class="ac-family-section-kicker">{{ $reviewSection['kicker'] ?? 'POREZNI PREGLED' }}</p>
                            <h2 id="ac-tax-review-title">{{ $reviewSection['title'] ?? '' }}</h2>
                        </div>
                    </div>
                    <div class="ac-tax-section-intro">
                        <p>{{ $reviewSection['intro'] ?? '' }}</p>
                    </div>
                </div>

                <div class="ac-tax-rich-grid">
                    <article class="ac-tax-panel">
                        <div class="ac-tax-copy">
                            @foreach ($reviewBody as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </article>

                    <article class="ac-tax-panel ac-tax-panel--accent">
                        @if (trim((string) ($reviewSection['highlights_title'] ?? '')) !== '')
                            <div class="ac-tax-panel-head">
                                <h3 class="ac-tax-panel-title">{{ $reviewSection['highlights_title'] }}</h3>
                            </div>
                        @endif
                        <ul class="ac-tax-list">
                            @foreach ($reviewHighlights as $item)
                                <li>
                                    <span class="ac-tax-list-bullet" aria-hidden="true">
                                        <svg viewBox="{{ $taxListBullet['view_box'] }}" fill="currentColor">
                                            <use href="{{ $taxListBullet['href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </article>
                </div>
            </div>
        </section>

        <section id="tax-optimization" class="ac-tax-shell-section ac-tax-shell-section--muted" aria-labelledby="ac-tax-optimization-title">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                @php
                    $optimizationMeta = $taxSectionMeta['optimization'];
                @endphp
                <div class="ac-tax-section-head">
                    <div class="ac-tax-section-title">
                        <div class="ac-tax-badge">
                            <span class="ac-tax-icon" aria-hidden="true">
                                <svg viewBox="{{ $optimizationMeta['icon_view_box'] }}" fill="currentColor">
                                    <use href="{{ $optimizationMeta['icon_href'] }}"></use>
                                </svg>
                            </span>
                            <span class="ac-tax-index">{{ $optimizationMeta['number'] }}</span>
                        </div>
                        <div class="ac-tax-title-copy">
                            <p class="ac-family-section-kicker">{{ $optimizationSection['kicker'] ?? 'OPTIMIZACIJA' }}</p>
                            <h2 id="ac-tax-optimization-title">{{ $optimizationSection['title'] ?? '' }}</h2>
                        </div>
                    </div>
                    <div class="ac-tax-section-intro">
                        <p>{{ $optimizationSection['intro'] ?? '' }}</p>
                    </div>
                </div>

                <div class="ac-tax-columns ac-tax-columns--two">
                    <article class="ac-tax-panel">
                        <div class="ac-tax-copy">
                            @foreach ($optimizationPrimaryBody as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </article>

                    @if ($optimizationSecondaryBody !== [])
                        <article class="ac-tax-panel ac-tax-panel--accent">
                            @foreach ($optimizationSecondaryBody as $paragraph)
                                <blockquote class="ac-tax-quote">
                                    <span class="ac-tax-quote-icon" aria-hidden="true">
                                        <svg viewBox="{{ $taxQuoteIcon['view_box'] }}" fill="currentColor">
                                            <use href="{{ $taxQuoteIcon['href'] }}"></use>
                                        </svg>
                                    </span>
                                    <p>{{ $paragraph }}</p>
                                </blockquote>
                            @endforeach
                        </article>
                    @endif
                </div>
            </div>
        </section>

        <section id="tax-due-diligence" class="ac-tax-shell-section" aria-labelledby="ac-tax-due-diligence-title">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                @php
                    $dueMeta = $taxSectionMeta['due_diligence'];
                @endphp
                <div class="ac-tax-section-head">
                    <div class="ac-tax-section-title">
                        <div class="ac-tax-badge">
                            <span class="ac-tax-icon" aria-hidden="true">
                                <svg viewBox="{{ $dueMeta['icon_view_box'] }}" fill="currentColor">
                                    <use href="{{ $dueMeta['icon_href'] }}"></use>
                                </svg>
                            </span>
                            <span class="ac-tax-index">{{ $dueMeta['number'] }}</span>
                        </div>
                        <div class="ac-tax-title-copy">
                            <p class="ac-family-section-kicker">{{ $dueDiligenceSection['kicker'] ?? 'DUE DILIGENCE' }}</p>
                            <h2 id="ac-tax-due-diligence-title">{{ $dueDiligenceSection['title'] ?? '' }}</h2>
                        </div>
                    </div>
                    <div class="ac-tax-section-intro">
                        <p>{{ $dueDiligenceSection['intro'] ?? '' }}</p>
                    </div>
                </div>

                <div class="ac-tax-columns ac-tax-columns--two">
                    <article class="ac-tax-panel">
                        <div class="ac-tax-copy">
                            @foreach ($dueDiligencePrimaryBody as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </article>

                    @if ($dueDiligenceSecondaryBody !== [])
                        <article class="ac-tax-panel ac-tax-panel--accent">
                            @foreach ($dueDiligenceSecondaryBody as $paragraph)
                                <blockquote class="ac-tax-quote">
                                    <span class="ac-tax-quote-icon" aria-hidden="true">
                                        <svg viewBox="{{ $taxQuoteIcon['view_box'] }}" fill="currentColor">
                                            <use href="{{ $taxQuoteIcon['href'] }}"></use>
                                        </svg>
                                    </span>
                                    <p>{{ $paragraph }}</p>
                                </blockquote>
                            @endforeach
                        </article>
                    @endif
                </div>
            </div>
        </section>

        <section id="tax-transfer-pricing" class="ac-tax-shell-section ac-tax-shell-section--muted" aria-labelledby="ac-tax-transfer-pricing-title">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                @php
                    $transferMeta = $taxSectionMeta['transfer_pricing'];
                @endphp
                <div class="ac-tax-section-head">
                    <div class="ac-tax-section-title">
                        <div class="ac-tax-badge">
                            <span class="ac-tax-icon" aria-hidden="true">
                                <svg viewBox="{{ $transferMeta['icon_view_box'] }}" fill="currentColor">
                                    <use href="{{ $transferMeta['icon_href'] }}"></use>
                                </svg>
                            </span>
                            <span class="ac-tax-index">{{ $transferMeta['number'] }}</span>
                        </div>
                        <div class="ac-tax-title-copy">
                            <p class="ac-family-section-kicker">{{ $transferPricingSection['kicker'] ?? 'TRANSFERNE CIJENE' }}</p>
                            <h2 id="ac-tax-transfer-pricing-title">{{ $transferPricingSection['title'] ?? '' }}</h2>
                        </div>
                    </div>
                    <div class="ac-tax-section-intro">
                        <p>{{ $transferPricingSection['intro'] ?? '' }}</p>
                    </div>
                </div>

                <div class="ac-tax-columns ac-tax-columns--two">
                    <article class="ac-tax-panel">
                        <div class="ac-tax-copy">
                            @foreach ($transferPricingPrimaryBody as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </article>

                    @if ($transferPricingSecondaryBody !== [])
                        <article class="ac-tax-panel ac-tax-panel--accent">
                            @foreach ($transferPricingSecondaryBody as $paragraph)
                                <blockquote class="ac-tax-quote">
                                    <span class="ac-tax-quote-icon" aria-hidden="true">
                                        <svg viewBox="{{ $taxQuoteIcon['view_box'] }}" fill="currentColor">
                                            <use href="{{ $taxQuoteIcon['href'] }}"></use>
                                        </svg>
                                    </span>
                                    <p>{{ $paragraph }}</p>
                                </blockquote>
                            @endforeach
                        </article>
                    @endif
                </div>
            </div>
        </section>

        @include('front.desktop.partials.service-videos', [
            'serviceVideoSection' => $serviceVideoSection ?? [],
            'serviceVideos' => $serviceVideos ?? [],
            'locale' => $locale ?? app()->getLocale(),
        ])

        <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
            <section id="tax-sastanak" class="ac-family-section pb-16 md:pb-24" aria-labelledby="ac-tax-meeting-title">
                <div class="ac-family-team-showcase-head">
                    <p class="ac-family-section-kicker">{{ $meetingSection['kicker'] ?? 'KONTAKT' }}</p>
                    <h2 id="ac-tax-meeting-title">{{ $meetingSection['title'] ?? '' }}</h2>
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
                        <input type="hidden" name="redirect_to" value="{{ route('tax.show') }}#tax-sastanak">

                        @if (session('status'))
                            <div class="front-contact-status" role="status">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="tax-first-name">{{ $meetingFormLabels['first_name'] ?? 'Ime' }}</label>
                                <input id="tax-first-name" type="text" name="first_name" value="{{ old('first_name') }}" class="front-contact-input h-11 w-full text-sm" required>
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('first_name') ? '' : 'hidden' }}" data-field-error="first_name">@error('first_name'){{ $message }}@enderror</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="tax-last-name">{{ $meetingFormLabels['last_name'] ?? 'Prezime' }}</label>
                                <input id="tax-last-name" type="text" name="last_name" value="{{ old('last_name') }}" class="front-contact-input h-11 w-full text-sm">
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('last_name') ? '' : 'hidden' }}" data-field-error="last_name">@error('last_name'){{ $message }}@enderror</p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="tax-company">{{ $meetingFormLabels['company'] ?? 'Tvrtka' }}</label>
                                <input id="tax-company" type="text" name="company" value="{{ old('company') }}" class="front-contact-input h-11 w-full text-sm">
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('company') ? '' : 'hidden' }}" data-field-error="company">@error('company'){{ $message }}@enderror</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="tax-phone">{{ $meetingFormLabels['phone'] ?? 'Broj telefona' }}</label>
                                <input id="tax-phone" type="text" name="phone" value="{{ old('phone') }}" class="front-contact-input h-11 w-full text-sm">
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('phone') ? '' : 'hidden' }}" data-field-error="phone">@error('phone'){{ $message }}@enderror</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="tax-email">{{ $meetingFormLabels['email'] ?? 'Email' }}</label>
                            <input id="tax-email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" class="front-contact-input h-11 w-full text-sm" required>
                            <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('email') ? '' : 'hidden' }}" data-field-error="email">@error('email'){{ $message }}@enderror</p>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="tax-subject">{{ $meetingFormLabels['subject'] ?? 'Naslov poruke' }}</label>
                            <input id="tax-subject" type="text" name="subject" value="{{ old('subject') }}" class="front-contact-input h-11 w-full text-sm">
                            <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('subject') ? '' : 'hidden' }}" data-field-error="subject">@error('subject'){{ $message }}@enderror</p>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="tax-message">{{ $meetingFormLabels['message'] ?? 'Poruka' }}</label>
                            <textarea id="tax-message" name="message" rows="8" class="front-contact-textarea w-full text-sm" required>{{ old('message') }}</textarea>
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

        @if (($taxPosts ?? collect())->isNotEmpty())
            <section class="ac-support-story ac-home-blog ac-blog-related-section ac-family-blog-section" aria-labelledby="ac-tax-blog-title">
                <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                    <div class="ac-support-story-hero">
                        <div class="ac-support-story-shell">
                            <div class="ac-services-head ac-support-story-head">
                                <h2 id="ac-tax-blog-title">
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

                    <div class="ac-home-blog-carousel">
                        <div id="ac-tax-blog-splide" class="splide ac-home-blog-splide" data-tax-blog-splide>
                            <div class="splide__track">
                                <ul class="splide__list">
                                    @foreach ($taxPosts as $post)
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
                                            $categoryLabel = trim((string) ($categoryTranslation?->name ?? ($taxCategoryName ?? 'Novosti')));
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
                </div>
            </section>
        @endif
    </div>
@endsection

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
    @endpush
@endonce

@push('styles')
    <style>
        .ac-tax-shell-section {
            padding: clamp(2.25rem, 4vw, 3.6rem) 0;
            background: linear-gradient(180deg, #faf7f1 0%, #f7f2e8 100%);
        }

        .ac-tax-shell-section--hero {
            padding-top: clamp(1.8rem, 3vw, 2.6rem);
        }

        .ac-tax-shell-section--muted {
            background: linear-gradient(180deg, #f1ece2 0%, #f8f5ef 100%);
        }

        .ac-tax-shell-section--hero .ac-support-story-head {
            max-width: 54rem;
            margin: 0 auto;
            text-align: center;
        }

        .ac-tax-shell-section--hero .ac-services-eyebrow {
            justify-content: center;
        }

        .ac-tax-shell-section--hero .ac-services-eyebrow-line {
            display: none;
        }

        .ac-tax-shell-section--hero .ac-services-kicker {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.55rem;
            padding: 0.45rem 1.15rem;
            border: 1px solid rgba(120, 96, 58, 0.16);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.72);
            color: #3d3428;
            letter-spacing: 0.14em;
        }

        .ac-tax-shell-section--hero .ac-services-intro {
            max-width: 48rem;
            margin-right: auto;
            margin-left: auto;
            color: #57534e;
        }

        .ac-tax-shell-section--hero #ac-tax-overview-title {
            display: flex;
            justify-content: center;
            width: 100%;
            max-width: 100%;
            margin-right: auto;
            margin-left: auto;
            text-align: center;
        }

        .ac-tax-shell-section--hero #ac-tax-overview-title span {
            display: inline-block;
            width: auto;
            max-width: min(100%, 33ch);
            white-space: normal;
            text-wrap: balance;
        }

        .ac-tax-shell-section--hero .ac-services-divider {
            max-width: 32rem;
            margin: 1.7rem auto 0;
        }

        .ac-tax-shell-section--hero .ac-services-divider-line {
            background: rgba(120, 96, 58, 0.18);
        }

        .ac-tax-shell-section--hero .ac-services-divider-glyph {
            width: 2.55rem;
            height: 2.55rem;
            border: 1px solid rgba(171, 141, 82, 0.28);
            background: rgba(255, 255, 255, 0.78);
        }

        .ac-tax-overview-grid,
        .ac-tax-compliance-grid,
        .ac-tax-rich-grid {
            display: grid;
            gap: 1.35rem 2rem;
            margin-top: 2rem;
        }

        .ac-tax-overview-grid {
            grid-template-columns: minmax(0, 1.08fr) minmax(320px, 0.92fr);
            align-items: start;
        }

        .ac-tax-compliance-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            align-items: stretch;
        }

        .ac-tax-rich-grid {
            grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
            align-items: stretch;
        }

        .ac-tax-panel {
            min-width: 0;
            height: 100%;
            padding: 1.4rem 1.45rem;
            border: 1px solid rgba(150, 114, 54, 0.14);
            background: rgba(255, 255, 255, 0.76);
            box-shadow: none;
        }

        .ac-tax-panel--highlight,
        .ac-tax-panel--accent {
            background: rgba(255, 255, 255, 0.82);
            border-color: rgba(171, 141, 82, 0.16);
        }

        .ac-tax-panel--wide {
            grid-column: 1 / -1;
        }

        .ac-tax-section-head {
            display: grid;
            gap: 1.75rem;
            align-items: start;
        }

        .ac-tax-section-title {
            display: flex;
            gap: 1.15rem;
            align-items: flex-start;
            min-width: 0;
        }

        .ac-tax-badge {
            display: grid;
            gap: 0.65rem;
            justify-items: center;
            flex: none;
        }

        .ac-tax-icon {
            display: inline-flex;
            width: 3.6rem;
            height: 3.6rem;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.82);
            color: #211d1a;
            border: 1px solid rgba(171, 141, 82, 0.24);
        }

        .ac-tax-icon svg {
            width: 1.55rem;
            height: 1.55rem;
        }

        .ac-tax-index {
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

        .ac-tax-title-copy {
            min-width: 0;
        }

        .ac-tax-title-copy h2 {
            margin: 0;
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.6rem, 2.25vw, 2.3rem);
            line-height: 1.15;
            font-weight: 600;
            color: #0f172a;
            text-wrap: balance;
        }

        .ac-tax-section-intro,
        .ac-tax-copy,
        .ac-tax-panel p,
        .ac-tax-card p,
        .ac-tax-list li {
            font-size: 0.98rem;
            line-height: 1.7;
            color: #403a34;
            text-wrap: pretty;
        }

        .ac-tax-section-intro p,
        .ac-tax-copy p,
        .ac-tax-panel p {
            margin: 0;
        }

        .ac-tax-section-intro {
            max-width: 46rem;
        }

        .ac-tax-copy p + p,
        .ac-tax-panel p + p {
            margin-top: 0.95rem;
        }

        .ac-tax-copy--columns {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem 1.4rem;
            align-items: start;
        }

        .ac-tax-copy--columns p {
            margin: 0;
        }

        .ac-tax-list {
            margin: 1rem 0 0;
            display: grid;
            gap: 0.8rem;
            padding-left: 0;
            list-style: none;
        }

        .ac-tax-list li {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: 0.72rem;
            align-items: start;
        }

        .ac-tax-list-bullet {
            display: inline-flex;
            width: 1.15rem;
            height: 1.15rem;
            align-items: center;
            justify-content: center;
            margin-top: 0.22rem;
            border-radius: 999px;
            background: rgba(171, 141, 82, 0.12);
            color: #7d6134;
        }

        .ac-tax-list-bullet svg {
            width: 0.42rem;
            height: 0.68rem;
        }

        .ac-tax-columns {
            display: grid;
            gap: 1.35rem 2rem;
            margin-top: 2rem;
        }

        .ac-tax-columns--two {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            align-items: stretch;
        }

        .ac-tax-columns--two > * {
            width: 100%;
            min-width: 0;
        }

        .ac-tax-card-grid {
            display: grid;
            gap: 1rem;
            margin-top: 2rem;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            align-items: stretch;
        }

        .ac-tax-card {
            min-width: 0;
            height: 100%;
            padding: 1.25rem 1.2rem 1.3rem;
            border: 1px solid rgba(171, 141, 82, 0.12);
            background: rgba(255, 255, 255, 0.76);
            box-shadow: none;
        }

        .ac-tax-card-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.25rem;
            min-height: 1.55rem;
            margin-bottom: 0.9rem;
            padding: 0.16rem 0.55rem;
            border-radius: 999px;
            background: rgba(120, 96, 58, 0.08);
            color: #6d5633;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.16em;
        }

        .ac-tax-card h3,
        .ac-tax-panel h3,
        .ac-tax-subpanel h4 {
            margin: 0;
            font-size: 1.08rem;
            line-height: 1.5rem;
            color: #0f172a;
            font-weight: 700;
            letter-spacing: -0.02em;
            font-family: 'Sora', 'Public Sans', 'Segoe UI', Arial, sans-serif;
        }

        .ac-tax-panel-head {
            display: grid;
            gap: 0.35rem;
        }

        .ac-tax-panel-title {
            font-size: 1.05rem;
            letter-spacing: -0.02em;
        }

        .ac-tax-card p {
            margin-top: 0.85rem;
        }

        .ac-tax-quote {
            position: relative;
            margin: 0;
            padding: 1.2rem 1.35rem 1.2rem 3.5rem;
            border-left: 2px solid rgba(171, 141, 82, 0.26);
            background: rgba(255, 255, 255, 0.58);
        }

        .ac-tax-quote-icon {
            position: absolute;
            top: 1.15rem;
            left: 1.2rem;
            display: inline-flex;
            width: 1.35rem;
            height: 1.35rem;
            align-items: center;
            justify-content: center;
            color: #8a6a38;
        }

        .ac-tax-quote-icon svg {
            width: 1rem;
            height: 1rem;
        }

        .ac-tax-quote p {
            margin-top: 0;
            font-size: 1rem;
            line-height: 1.82;
            color: #2d2925;
        }

        .ac-tax-panel-intro {
            margin-top: 0.85rem !important;
        }

        .ac-tax-group-grid {
            display: grid;
            gap: 1rem;
            margin-top: 1.35rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ac-tax-subpanel {
            min-width: 0;
            height: 100%;
            padding: 1.1rem 1.15rem;
            border: 1px solid rgba(171, 141, 82, 0.1);
            background: rgba(249, 246, 240, 0.85);
        }

        .ac-tax-subpanel .ac-tax-list {
            margin-top: 0.85rem;
        }

        .ac-tax-page .ac-home-blog-card,
        .ac-tax-page .ac-home-blog-card-link {
            border-color: rgba(171, 141, 82, 0.16);
        }

        @media (min-width: 960px) {
            .ac-tax-section-head {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 2.4rem;
                align-items: center;
            }

            .ac-tax-section-head > * {
                width: 100%;
                min-width: 0;
            }

            .ac-tax-section-intro {
                max-width: none;
            }
        }

        @media (max-width: 1080px) {
            .ac-tax-card-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 960px) {
            .ac-tax-overview-grid,
            .ac-tax-columns--two,
            .ac-tax-compliance-grid,
            .ac-tax-rich-grid,
            .ac-tax-group-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .ac-tax-copy--columns {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        @media (max-width: 760px) {
            .ac-tax-shell-section {
                padding: 2rem 0;
            }

            .ac-tax-panel {
                padding: 1.15rem 1rem;
            }

            .ac-tax-card-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .ac-tax-section-title {
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                gap: 0.9rem;
            }

            .ac-tax-badge {
                gap: 0.45rem;
                justify-items: start;
            }

            .ac-tax-icon {
                width: 3.15rem;
                height: 3.15rem;
            }

            .ac-tax-icon svg {
                width: 1.35rem;
                height: 1.35rem;
            }

            .ac-tax-panel,
            .ac-tax-card {
                padding: 1.05rem 1rem 1.1rem;
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
            const section = document.getElementById('tax-sastanak');

            if (shouldFocusSection && section) {
                requestAnimationFrame(function () {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            }

            const initTaxBlogSlider = function () {
                if (typeof window.Splide !== 'function') {
                    return false;
                }

                document.querySelectorAll('[data-tax-blog-splide]').forEach(function (el) {
                    if (el.dataset.splideReady === '1') {
                        return;
                    }

                    el.dataset.splideReady = '1';

                    const count = el.querySelectorAll('.splide__slide').length;
                    const slider = new window.Splide(el, {
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

                    slider.mount();
                });

                return true;
            };

            if (initTaxBlogSlider()) {
                return;
            }

            let attempts = 0;
            const timer = window.setInterval(function () {
                attempts += 1;
                if (initTaxBlogSlider() || attempts > 40) {
                    window.clearInterval(timer);
                }
            }, 120);
        }());
    </script>
@endpush
