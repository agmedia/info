@extends('front.desktop.layouts.store')

@php
    $captchaSiteKey = trim((string) ($storeSettings['captcha']['recaptcha_v3_site_key'] ?? ''));
    $captchaEnabled = (bool) ($storeSettings['captcha']['recaptcha_v3_enabled'] ?? false) && $captchaSiteKey !== '';
    $contactEmail = trim((string) ($storeSettings['footer']['email_support'] ?? '')) ?: 'info@alphacapitalis.com';
    $contactPhone = trim((string) ($storeSettings['footer']['phone'] ?? '')) ?: '+385 (1) 580 6656';
    $contactPhoneHref = preg_replace('/\s+/', '', $contactPhone);
    $auditSprite = asset('front-theme/fonts/sprites/solid.svg');
    $meetingFormLabels = $meetingSection['form_labels'] ?? [];
    $auditSectionMeta = [
        'overview' => [
            'number' => '01',
            'icon_view_box' => '0 0 384 512',
            'icon_href' => $auditSprite.'#file-lines',
        ],
        'obligors' => [
            'number' => '02',
            'icon_view_box' => '0 0 512 512',
            'icon_href' => $auditSprite.'#building-columns',
        ],
        'services' => [
            'number' => '03',
            'icon_view_box' => '0 0 512 512',
            'icon_href' => $auditSprite.'#magnifying-glass',
        ],
        'value' => [
            'number' => '04',
            'icon_view_box' => '0 0 512 512',
            'icon_href' => $auditSprite.'#chart-line',
        ],
        'approach' => [
            'number' => '05',
            'icon_view_box' => '0 0 512 512',
            'icon_href' => $auditSprite.'#bullseye',
        ],
    ];
    $auditListBullet = [
        'view_box' => '0 0 256 512',
        'href' => $auditSprite.'#angle-right',
    ];
    $auditCtaIcon = [
        'view_box' => '0 0 320 512',
        'href' => $auditSprite.'#angle-down',
    ];
    $quoteIcon = [
        'view_box' => '0 0 448 512',
        'href' => $auditSprite.'#quote-right',
    ];
@endphp

@section('title', $servicePageMetaTitle !== '' ? $servicePageMetaTitle : ($servicePageTitle ?? 'Revizija'))
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-family-business-page ac-audit-page">
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
                                    <span class="is-subtitle-lead">{{ $heroSection['subtitle_lead'] ?? 'Revizija' }}</span>
                                    <span class="is-subtitle-accent">{{ $heroSection['subtitle_accent'] ?? 'financijskih izvještaja' }}</span>
                                </span>
                            </h1>

                            <p class="ac-family-hero-intro">{{ $heroSection['intro'] ?? '' }}</p>

                            <div class="ac-family-hero-actions">
                                <a href="{{ $heroSection['cta_url'] ?? '#audit-overview' }}" class="front-action-cta">
                                    <span>{{ $heroSection['cta_label'] ?? 'Pregledajte sekcije' }}</span>
                                    <svg viewBox="{{ $auditCtaIcon['view_box'] }}" fill="currentColor" aria-hidden="true">
                                        <use href="{{ $auditCtaIcon['href'] }}"></use>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="audit-overview" class="ac-audit-editorial-wrap" aria-labelledby="ac-audit-overview-title">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-support-story-hero">
                    <div class="ac-support-story-shell">
                        <div class="ac-services-head ac-support-story-head">
                            <div class="ac-services-eyebrow">
                                <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                                <p class="ac-services-kicker">{{ $overviewSection['kicker'] ?? 'REVIZIJA' }}</p>
                                <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                            </div>
                            <h2 id="ac-audit-overview-title">
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

                <div class="ac-audit-editorial-shell">
                    @php
                        $overviewMeta = $auditSectionMeta['overview'];
                    @endphp
                    <article class="ac-audit-editorial-section ac-audit-editorial-section--overview">
                        <div class="ac-audit-overview-grid">
                            <div class="ac-audit-editorial-title">
                                <div class="ac-audit-editorial-badge">
                                    <span class="ac-audit-editorial-icon" aria-hidden="true">
                                        <svg viewBox="{{ $overviewMeta['icon_view_box'] }}" fill="currentColor">
                                            <use href="{{ $overviewMeta['icon_href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span class="ac-audit-editorial-index">{{ $overviewMeta['number'] }}</span>
                                </div>
                                <div class="ac-audit-editorial-heading">
                                    <h2>{{ $overviewSection['highlight_title'] ?? '' }}</h2>
                                </div>
                            </div>
                            <div class="ac-audit-editorial-intro ac-audit-overview-intro">
                                <p>{{ $overviewSection['body'][0] ?? '' }}</p>
                            </div>
                            <div class="ac-audit-column ac-audit-column--spacious ac-audit-overview-body">
                                @foreach (array_slice((array) ($overviewSection['body'] ?? []), 1) as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </div>
                        </div>
                    </article>

                    @php
                        $obligorsMeta = $auditSectionMeta['obligors'];
                    @endphp
                    <article class="ac-audit-editorial-section ac-audit-editorial-section--obligors">
                        <div class="ac-audit-obligors-grid">
                            <div class="ac-audit-editorial-title">
                                <div class="ac-audit-editorial-badge">
                                    <span class="ac-audit-editorial-icon" aria-hidden="true">
                                        <svg viewBox="{{ $obligorsMeta['icon_view_box'] }}" fill="currentColor">
                                            <use href="{{ $obligorsMeta['icon_href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span class="ac-audit-editorial-index">{{ $obligorsMeta['number'] }}</span>
                                </div>
                                <div class="ac-audit-editorial-heading">
                                    <p class="ac-family-section-kicker">{{ $obligorsSection['kicker'] ?? 'OBVEZNICI' }}</p>
                                    <h2>{{ $obligorsSection['title'] ?? '' }}</h2>
                                </div>
                            </div>
                            <div class="ac-audit-editorial-intro ac-audit-obligors-intro">
                                <p>{{ $obligorsSection['intro'] ?? '' }}</p>
                            </div>
                            <article class="ac-audit-panel">
                                <h3>{{ $obligorsSection['primary_title'] ?? '' }}</h3>
                                <ul class="ac-audit-list">
                                    @foreach (($obligorsSection['primary_items'] ?? []) as $item)
                                        <li>
                                            <span class="ac-audit-list-bullet" aria-hidden="true">
                                                <svg viewBox="{{ $auditListBullet['view_box'] }}" fill="currentColor">
                                                    <use href="{{ $auditListBullet['href'] }}"></use>
                                                </svg>
                                            </span>
                                            <span>{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </article>

                            <article class="ac-audit-panel">
                                <h3>{{ $obligorsSection['thresholds_title'] ?? '' }}</h3>
                                <p class="ac-audit-panel-intro">{{ $obligorsSection['thresholds_intro'] ?? '' }}</p>
                                <ul class="ac-audit-list">
                                    @foreach (($obligorsSection['thresholds'] ?? []) as $item)
                                        <li>
                                            <span class="ac-audit-list-bullet" aria-hidden="true">
                                                <svg viewBox="{{ $auditListBullet['view_box'] }}" fill="currentColor">
                                                    <use href="{{ $auditListBullet['href'] }}"></use>
                                                </svg>
                                            </span>
                                            <span>{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </article>
                        </div>

                        @if (trim((string) ($obligorsSection['note'] ?? '')) !== '')
                            <div class="ac-audit-followup">
                                <blockquote class="ac-audit-quote">
                                    <span class="ac-audit-quote-icon" aria-hidden="true">
                                        <svg viewBox="{{ $quoteIcon['view_box'] }}" fill="currentColor">
                                            <use href="{{ $quoteIcon['href'] }}"></use>
                                        </svg>
                                    </span>
                                    <p>{{ $obligorsSection['note'] }}</p>
                                </blockquote>
                            </div>
                        @endif
                    </article>

                    @php
                        $servicesMeta = $auditSectionMeta['services'];
                    @endphp
                    <article class="ac-audit-editorial-section">
                        <div class="ac-audit-editorial-head">
                            <div class="ac-audit-editorial-title">
                                <div class="ac-audit-editorial-badge">
                                    <span class="ac-audit-editorial-icon" aria-hidden="true">
                                        <svg viewBox="{{ $servicesMeta['icon_view_box'] }}" fill="currentColor">
                                            <use href="{{ $servicesMeta['icon_href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span class="ac-audit-editorial-index">{{ $servicesMeta['number'] }}</span>
                                </div>
                                <div class="ac-audit-editorial-heading">
                                    <p class="ac-family-section-kicker">{{ $servicesSection['kicker'] ?? 'USLUGE' }}</p>
                                    <h2>{{ $servicesSection['title'] ?? '' }}</h2>
                                </div>
                            </div>
                            <div class="ac-audit-editorial-intro">
                                <p>{{ $servicesSection['intro'] ?? '' }}</p>
                            </div>
                        </div>

                        <div class="ac-audit-card-grid">
                            @foreach (($servicesSection['items'] ?? []) as $item)
                                <article class="ac-audit-service-card">
                                    <span class="ac-audit-service-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <h3>{{ $item['title'] ?? '' }}</h3>
                                    <p>{{ $item['text'] ?? '' }}</p>
                                </article>
                            @endforeach
                        </div>
                    </article>

                    @php
                        $valueMeta = $auditSectionMeta['value'];
                    @endphp
                    <article class="ac-audit-editorial-section">
                        <div class="ac-audit-editorial-head">
                            <div class="ac-audit-editorial-title">
                                <div class="ac-audit-editorial-badge">
                                    <span class="ac-audit-editorial-icon" aria-hidden="true">
                                        <svg viewBox="{{ $valueMeta['icon_view_box'] }}" fill="currentColor">
                                            <use href="{{ $valueMeta['icon_href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span class="ac-audit-editorial-index">{{ $valueMeta['number'] }}</span>
                                </div>
                                <div class="ac-audit-editorial-heading">
                                    <p class="ac-family-section-kicker">{{ $valueSection['kicker'] ?? 'VRIJEDNOST' }}</p>
                                    <h2>{{ $valueSection['title'] ?? '' }}</h2>
                                </div>
                            </div>
                            <div class="ac-audit-editorial-intro">
                                <p>{{ $valueSection['intro'] ?? '' }}</p>
                            </div>
                        </div>

                        <div class="ac-audit-columns ac-audit-columns--two-wide">
                            <article class="ac-audit-panel">
                                <ul class="ac-audit-list ac-audit-list--multi">
                                    @foreach (($valueSection['benefits'] ?? []) as $item)
                                        <li>
                                            <span class="ac-audit-list-bullet" aria-hidden="true">
                                                <svg viewBox="{{ $auditListBullet['view_box'] }}" fill="currentColor">
                                                    <use href="{{ $auditListBullet['href'] }}"></use>
                                                </svg>
                                            </span>
                                            <span>{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </article>

                            <article class="ac-audit-panel ac-audit-panel--accent">
                                <blockquote class="ac-audit-quote">
                                    <span class="ac-audit-quote-icon" aria-hidden="true">
                                        <svg viewBox="{{ $quoteIcon['view_box'] }}" fill="currentColor">
                                            <use href="{{ $quoteIcon['href'] }}"></use>
                                        </svg>
                                    </span>
                                    <p>{{ $valueSection['conclusion'] ?? '' }}</p>
                                </blockquote>
                            </article>
                        </div>
                    </article>

                    @php
                        $approachMeta = $auditSectionMeta['approach'];
                    @endphp
                    <article class="ac-audit-editorial-section">
                        <div class="ac-audit-editorial-head">
                            <div class="ac-audit-editorial-title">
                                <div class="ac-audit-editorial-badge">
                                    <span class="ac-audit-editorial-icon" aria-hidden="true">
                                        <svg viewBox="{{ $approachMeta['icon_view_box'] }}" fill="currentColor">
                                            <use href="{{ $approachMeta['icon_href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span class="ac-audit-editorial-index">{{ $approachMeta['number'] }}</span>
                                </div>
                                <div class="ac-audit-editorial-heading">
                                    <p class="ac-family-section-kicker">{{ $approachSection['kicker'] ?? 'PRISTUP' }}</p>
                                    <h2>{{ $approachSection['title'] ?? '' }}</h2>
                                </div>
                            </div>
                            <div class="ac-audit-editorial-intro">
                                <p>{{ $approachSection['intro'] ?? '' }}</p>
                            </div>
                        </div>

                        <div class="ac-audit-columns ac-audit-columns--two">
                            <article class="ac-audit-panel">
                                <h3>{{ $approachSection['principles_title'] ?? '' }}</h3>
                                <ul class="ac-audit-list">
                                    @foreach (($approachSection['principles'] ?? []) as $item)
                                        <li>
                                            <span class="ac-audit-list-bullet" aria-hidden="true">
                                                <svg viewBox="{{ $auditListBullet['view_box'] }}" fill="currentColor">
                                                    <use href="{{ $auditListBullet['href'] }}"></use>
                                                </svg>
                                            </span>
                                            <span>{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </article>

                            <article class="ac-audit-panel">
                                <h3>{{ $approachSection['reasons_title'] ?? '' }}</h3>
                                <ul class="ac-audit-list">
                                    @foreach (($approachSection['reasons'] ?? []) as $item)
                                        <li>
                                            <span class="ac-audit-list-bullet" aria-hidden="true">
                                                <svg viewBox="{{ $auditListBullet['view_box'] }}" fill="currentColor">
                                                    <use href="{{ $auditListBullet['href'] }}"></use>
                                                </svg>
                                            </span>
                                            <span>{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </article>
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

        <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
            <section id="audit-sastanak" class="ac-family-section pb-16 md:pb-24" aria-labelledby="ac-audit-meeting-title">
                <div class="ac-family-team-showcase-head">
                    <p class="ac-family-section-kicker">{{ $meetingSection['kicker'] ?? 'KONTAKT' }}</p>
                    <h2 id="ac-audit-meeting-title">{{ $meetingSection['title'] ?? '' }}</h2>
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
                        <input type="hidden" name="redirect_to" value="{{ route('audit.show') }}#audit-sastanak">

                        @if (session('status'))
                            <div class="front-contact-status" role="status">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="audit-first-name">{{ $meetingFormLabels['first_name'] ?? 'Ime' }}</label>
                                <input id="audit-first-name" type="text" name="first_name" value="{{ old('first_name') }}" class="front-contact-input h-11 w-full text-sm" required>
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('first_name') ? '' : 'hidden' }}" data-field-error="first_name">@error('first_name'){{ $message }}@enderror</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="audit-last-name">{{ $meetingFormLabels['last_name'] ?? 'Prezime' }}</label>
                                <input id="audit-last-name" type="text" name="last_name" value="{{ old('last_name') }}" class="front-contact-input h-11 w-full text-sm">
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('last_name') ? '' : 'hidden' }}" data-field-error="last_name">@error('last_name'){{ $message }}@enderror</p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="audit-company">{{ $meetingFormLabels['company'] ?? 'Tvrtka' }}</label>
                                <input id="audit-company" type="text" name="company" value="{{ old('company') }}" class="front-contact-input h-11 w-full text-sm">
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('company') ? '' : 'hidden' }}" data-field-error="company">@error('company'){{ $message }}@enderror</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="audit-phone">{{ $meetingFormLabels['phone'] ?? 'Broj telefona' }}</label>
                                <input id="audit-phone" type="text" name="phone" value="{{ old('phone') }}" class="front-contact-input h-11 w-full text-sm">
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('phone') ? '' : 'hidden' }}" data-field-error="phone">@error('phone'){{ $message }}@enderror</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="audit-email">{{ $meetingFormLabels['email'] ?? 'Email' }}</label>
                            <input id="audit-email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" class="front-contact-input h-11 w-full text-sm" required>
                            <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('email') ? '' : 'hidden' }}" data-field-error="email">@error('email'){{ $message }}@enderror</p>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="audit-subject">{{ $meetingFormLabels['subject'] ?? 'Naslov poruke' }}</label>
                            <input id="audit-subject" type="text" name="subject" value="{{ old('subject') }}" class="front-contact-input h-11 w-full text-sm">
                            <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('subject') ? '' : 'hidden' }}" data-field-error="subject">@error('subject'){{ $message }}@enderror</p>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="audit-message">{{ $meetingFormLabels['message'] ?? 'Poruka' }}</label>
                            <textarea id="audit-message" name="message" rows="8" class="front-contact-textarea w-full text-sm" required>{{ old('message') }}</textarea>
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

        @if (($auditPosts ?? collect())->isNotEmpty())
            <section class="ac-support-story ac-home-blog ac-blog-related-section ac-family-blog-section" aria-labelledby="ac-audit-blog-title">
                <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                    <div class="ac-support-story-hero">
                        <div class="ac-support-story-shell">
                            <div class="ac-services-head ac-support-story-head">
                                <h2 id="ac-audit-blog-title">
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
                        <div id="ac-audit-blog-splide" class="splide ac-home-blog-splide" data-audit-blog-splide>
                            <div class="splide__track">
                                <ul class="splide__list">
                                    @foreach ($auditPosts as $post)
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
                                            $categoryLabel = trim((string) ($categoryTranslation?->name ?? 'Novosti'));
                                            $publishedLabel = ($post->published_at ?? $post->created_at)?->translatedFormat('j. F Y.');
                                        @endphp
                                        <li class="splide__slide ac-home-blog-slide">
                                            <article class="ac-home-blog-card">
                                                <a href="{{ $postUrl }}" class="ac-home-blog-card-link" aria-label="Otvori blog post: {{ $postTitle }}">
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
                                                            <span>Opširnije</span>
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
        .ac-audit-editorial-wrap {
            position: relative;
            overflow: hidden;
            padding: clamp(1.5rem, 2.5vw, 2rem) 0 clamp(1.7rem, 2.6vw, 2.2rem);
            background: linear-gradient(180deg, #f4f1eb 0%, #f8f6f1 100%);
        }

        .ac-audit-editorial-wrap::before {
            content: '';
            position: absolute;
            inset: 0;
            background: repeating-linear-gradient(90deg, rgba(53, 45, 35, 0.018) 0 1px, transparent 1px 180px);
            opacity: 0.2;
            pointer-events: none;
        }

        .ac-audit-editorial-wrap > .mx-auto {
            position: relative;
            z-index: 1;
        }

        .ac-audit-editorial-wrap .ac-support-story-head {
            max-width: 54rem;
            margin: 0 auto;
            padding-top: clamp(0.4rem, 0.9vw, 0.7rem);
            text-align: center;
        }

        .ac-audit-editorial-wrap .ac-services-eyebrow {
            justify-content: center;
        }

        .ac-audit-editorial-wrap .ac-services-eyebrow-line {
            display: none;
        }

        .ac-audit-editorial-wrap .ac-services-kicker {
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

        .ac-audit-editorial-wrap .ac-services-intro {
            max-width: 48rem;
            margin-right: auto;
            margin-left: auto;
            color: #57534e;
        }

        .ac-audit-editorial-wrap #ac-audit-overview-title {
            display: flex;
            justify-content: center;
            width: 100%;
            max-width: 100%;
            margin-right: auto;
            margin-left: auto;
            text-align: center;
        }

        .ac-audit-editorial-wrap #ac-audit-overview-title span {
            display: inline-block;
            width: auto;
            max-width: min(100%, 33ch);
            white-space: normal;
            text-wrap: balance;
        }

        .ac-audit-editorial-wrap .ac-services-divider {
            max-width: 32rem;
            margin: 1.7rem auto 0;
        }

        .ac-audit-editorial-wrap .ac-services-divider-line {
            background: rgba(120, 96, 58, 0.18);
        }

        .ac-audit-editorial-wrap .ac-services-divider-glyph {
            width: 2.55rem;
            height: 2.55rem;
            border: 1px solid rgba(171, 141, 82, 0.28);
            background: rgba(255, 255, 255, 0.78);
        }

        .ac-audit-editorial-shell {
            display: grid;
            gap: 0;
            margin-top: clamp(1.8rem, 3vw, 2.35rem);
        }

        .ac-audit-editorial-section {
            width: 100%;
            min-width: 0;
            padding: clamp(2.2rem, 3vw, 3rem) 0;
        }

        .ac-audit-editorial-section + .ac-audit-editorial-section {
            border-top: 1px solid rgba(120, 96, 58, 0.14);
            padding-top: clamp(2.5rem, 3.4vw, 3.2rem);
        }

        .ac-audit-editorial-head {
            display: grid;
            gap: 1.75rem;
            align-items: start;
        }

        .ac-audit-editorial-title {
            display: flex;
            gap: 1.15rem;
            align-items: flex-start;
            min-width: 0;
        }

        .ac-audit-editorial-badge {
            display: grid;
            gap: 0.65rem;
            justify-items: center;
            flex: none;
        }

        .ac-audit-editorial-icon {
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

        .ac-audit-editorial-icon svg {
            width: 1.55rem;
            height: 1.55rem;
        }

        .ac-audit-editorial-index {
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

        .ac-audit-editorial-heading {
            min-width: 0;
        }

        .ac-audit-editorial-section:first-child .ac-audit-editorial-head {
            align-items: center;
        }

        .ac-audit-editorial-section:first-child .ac-audit-editorial-title {
            align-items: center;
        }

        .ac-audit-editorial-section:first-child .ac-audit-editorial-heading {
            display: flex;
            align-items: center;
            min-height: 3.6rem;
        }

        .ac-audit-editorial-heading h2 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.6rem, 2.3vw, 2.3rem);
            line-height: 1.15;
            font-weight: 600;
            color: #0f172a;
            text-wrap: balance;
            overflow-wrap: anywhere;
        }

        .ac-audit-editorial-intro {
            min-width: 0;
            max-width: 40rem;
            font-size: 0.98rem;
            line-height: 1.7;
            color: #403a34;
            text-wrap: pretty;
        }

        .ac-audit-editorial-intro p {
            margin: 0;
        }

        .ac-audit-columns {
            display: grid;
            gap: 1.35rem 2rem;
            margin-top: 2rem;
        }

        .ac-audit-obligors-grid {
            display: grid;
            gap: 1.75rem 2rem;
            align-items: start;
        }

        .ac-audit-editorial-section--obligors .ac-audit-editorial-title,
        .ac-audit-editorial-section--obligors .ac-audit-obligors-intro,
        .ac-audit-editorial-section--obligors .ac-audit-panel {
            min-width: 0;
            width: 100%;
        }

        .ac-audit-editorial-section--obligors .ac-audit-obligors-intro {
            max-width: none;
        }

        .ac-audit-columns--single {
            grid-template-columns: minmax(0, 1fr);
        }

        .ac-audit-columns--two {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ac-audit-columns--two-wide {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ac-audit-columns--two,
        .ac-audit-columns--two-wide {
            align-items: stretch;
        }

        .ac-audit-columns--two > *,
        .ac-audit-columns--two-wide > * {
            width: 100%;
            min-width: 0;
        }

        .ac-audit-column p,
        .ac-audit-panel p {
            font-size: 0.98rem;
            line-height: 1.72;
            color: #403a34;
            overflow-wrap: anywhere;
        }

        .ac-audit-column p + p,
        .ac-audit-panel p + p {
            margin-top: 0.95rem;
        }

        .ac-audit-panel {
            padding: 1.4rem 1.45rem;
            border: 1px solid rgba(171, 141, 82, 0.12);
            background: rgba(255, 255, 255, 0.76);
            box-shadow: none;
            height: 100%;
        }

        .ac-audit-panel--accent {
            background: rgba(255, 255, 255, 0.82);
            border-color: rgba(171, 141, 82, 0.16);
        }

        .ac-audit-panel h3,
        .ac-audit-service-card h3 {
            font-size: 1.08rem;
            font-weight: 700;
            line-height: 1.5rem;
            color: #0f172a;
            font-family: 'Sora', 'Public Sans', 'Segoe UI', Arial, sans-serif;
            letter-spacing: -0.02em;
        }

        .ac-audit-panel-intro {
            margin-top: 0.85rem;
        }

        .ac-audit-list {
            margin-top: 1rem;
            display: grid;
            gap: 0.8rem;
            padding-left: 0;
            list-style: none;
            color: #403a34;
        }

        .ac-audit-list--multi {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.95rem 1.9rem;
        }

        .ac-audit-list li {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: 0.72rem;
            align-items: start;
            line-height: 1.7rem;
        }

        .ac-audit-list-bullet {
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

        .ac-audit-list-bullet svg {
            width: 0.42rem;
            height: 0.68rem;
        }

        .ac-audit-followup {
            margin-top: 1.75rem;
            min-width: 0;
            max-width: 100%;
        }

        .ac-audit-quote {
            position: relative;
            margin: 0;
            padding: 1.2rem 1.35rem 1.2rem 3.5rem;
            border-left: 2px solid rgba(171, 141, 82, 0.26);
            background: rgba(255, 255, 255, 0.58);
        }

        .ac-audit-quote-icon {
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

        .ac-audit-quote-icon svg {
            width: 1rem;
            height: 1rem;
        }

        .ac-audit-quote p {
            margin-top: 0;
            font-size: 1rem;
            line-height: 1.82;
            color: #2d2925;
        }

        .ac-audit-card-grid {
            display: grid;
            gap: 1rem;
            margin-top: 2rem;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .ac-audit-service-card {
            position: relative;
            min-width: 0;
            padding: 1.25rem 1.2rem 1.3rem;
            border: 1px solid rgba(171, 141, 82, 0.12);
            background: rgba(255, 255, 255, 0.76);
            box-shadow: none;
        }

        .ac-audit-service-card p {
            margin-top: 0.85rem;
            font-size: 0.95rem;
            line-height: 1.7;
            color: #403a34;
        }

        .ac-audit-service-number {
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

        .ac-audit-page #audit-sastanak {
            margin-top: clamp(1.2rem, 2vw, 1.6rem);
        }

        .ac-audit-page .front-contact-input:focus,
        .ac-audit-page .front-contact-textarea:focus {
            box-shadow: none;
            outline: 2px solid rgba(171, 141, 82, 0.22);
            outline-offset: 0;
        }

        @media (min-width: 960px) {
            .ac-audit-editorial-head {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 2.4rem;
                align-items: center;
            }

            .ac-audit-editorial-head > * {
                width: 100%;
                min-width: 0;
            }

            .ac-audit-editorial-intro {
                max-width: none;
            }

            .ac-audit-obligors-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                align-items: start;
            }

            .ac-audit-editorial-section--obligors .ac-audit-obligors-intro {
                padding-top: 0.1rem;
            }
        }

        @media (max-width: 900px) {
            .ac-audit-obligors-grid,
            .ac-audit-columns--two,
            .ac-audit-columns--two-wide,
            .ac-audit-card-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .ac-audit-list--multi {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        @media (max-width: 640px) {
            .ac-audit-editorial-wrap {
                padding: 2.1rem 0;
            }

            .ac-audit-editorial-section {
                padding: 1.7rem 0;
            }

            .ac-audit-editorial-section + .ac-audit-editorial-section {
                padding-top: 2rem;
            }

            .ac-audit-editorial-title {
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                gap: 0.9rem;
            }

            .ac-audit-editorial-badge {
                gap: 0.45rem;
                justify-items: start;
            }

            .ac-audit-editorial-icon {
                width: 3.15rem;
                height: 3.15rem;
                border-radius: 16px;
            }

            .ac-audit-editorial-icon svg {
                width: 1.35rem;
                height: 1.35rem;
            }

            .ac-audit-panel,
            .ac-audit-service-card {
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
            const section = document.getElementById('audit-sastanak');

            if (shouldFocusSection && section) {
                requestAnimationFrame(function () {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            }

            const initAuditBlogSlider = function () {
                if (typeof window.Splide !== 'function') {
                    return false;
                }

                document.querySelectorAll('[data-audit-blog-splide]').forEach(function (el) {
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

            if (initAuditBlogSlider()) {
                return;
            }

            let attempts = 0;
            const timer = window.setInterval(function () {
                attempts += 1;
                if (initAuditBlogSlider() || attempts > 40) {
                    window.clearInterval(timer);
                }
            }, 120);
        }());
    </script>
@endpush
