@extends('front.desktop.layouts.store')

@php
    $captchaSiteKey = trim((string) ($storeSettings['captcha']['recaptcha_v3_site_key'] ?? ''));
    $captchaEnabled = (bool) ($storeSettings['captcha']['recaptcha_v3_enabled'] ?? false) && $captchaSiteKey !== '';
    $contactEmail = trim((string) ($storeSettings['footer']['email_support'] ?? '')) ?: 'info@alphacapitalis.com';
    $contactPhone = trim((string) ($storeSettings['footer']['phone'] ?? '')) ?: '+385 (1) 580 6656';
    $contactPhoneHref = preg_replace('/\s+/', '', $contactPhone);
    $meetingFormLabels = $meetingSection['form_labels'] ?? [];
    $valuationBody = array_values($valuationsSection['body'] ?? []);
    $capitalBody = array_values($capitalRaisingSection['body'] ?? []);
    $restructuringBody = array_values($restructuringSection['body'] ?? []);
    $pandeaBody = array_values($pandeaSection['body'] ?? []);
    $pandeaLeadParagraph = trim((string) ($pandeaBody[0] ?? ''));
    $pandeaSecondaryParagraph = trim((string) ($pandeaBody[1] ?? ''));
    $pandeaHeadline = trim((string) \Illuminate\Support\Str::before($pandeaLeadParagraph, ','));
    $servicesIntroTitle = trim((string) ($servicesIntroSection['title'] ?? ''));
    $servicesIntroTitleLines = [$servicesIntroTitle];

    $servicesIntroWords = preg_split('/\s+/u', $servicesIntroTitle, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    if (count($servicesIntroWords) > 2 && mb_strlen($servicesIntroTitle) > 28) {
        $bestSplitIndex = null;
        $bestScore = null;
        $lastWordIndex = count($servicesIntroWords) - 1;

        for ($index = 1; $index < $lastWordIndex; $index++) {
            $left = implode(' ', array_slice($servicesIntroWords, 0, $index));
            $right = implode(' ', array_slice($servicesIntroWords, $index));
            $score = max(mb_strlen($left), mb_strlen($right)) * 100 + abs(mb_strlen($left) - mb_strlen($right));

            if ($bestScore === null || $score < $bestScore) {
                $bestScore = $score;
                $bestSplitIndex = $index;
            }
        }

        if ($bestSplitIndex !== null) {
            $servicesIntroTitleLines = [
                implode(' ', array_slice($servicesIntroWords, 0, $bestSplitIndex)),
                implode(' ', array_slice($servicesIntroWords, $bestSplitIndex)),
            ];
        }
    }

    $networkName = 'Pandea Global M&A';
@endphp

@section('title', $servicePageMetaTitle !== '' ? $servicePageMetaTitle : ($servicePageTitle ?? 'Financije'))
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-family-business-page ac-finance-page">
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
                                    <span class="is-subtitle-lead">{{ $heroSection['subtitle_lead'] ?? 'Savjetnici za' }}</span>
                                    <span class="is-subtitle-accent">{{ $heroSection['subtitle_accent'] ?? 'financije' }}</span>
                                </span>
                            </h1>

                            <p class="ac-family-hero-intro">{{ $heroSection['intro'] ?? '' }}</p>

                            <div class="ac-family-hero-actions">
                                <a href="{{ $heroSection['cta_url'] ?? '#finance-usluge' }}" class="front-action-cta">
                                    <span>{{ $heroSection['cta_label'] ?? 'Pregledajte usluge' }}</span>
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 5v14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                                        <path d="m6 13 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
            <section class="ac-family-section ac-family-section--intro ac-finance-network-section">
                <div class="ac-family-ffi-banner">
                    <div class="ac-family-ffi-banner-copy">
                        @if ($pandeaHeadline !== '')
                            <h3>{{ $pandeaHeadline }}</h3>
                        @endif
                        @if ($pandeaLeadParagraph !== '')
                            <p>{{ $pandeaLeadParagraph }}</p>
                        @endif
                        @if ($pandeaSecondaryParagraph !== '')
                            <p>{{ $pandeaSecondaryParagraph }}</p>
                        @endif
                    </div>

                    <div class="ac-family-ffi-logo-wrap">
                        <img src="{{ $pandeaLogoUrl }}" alt="{{ $pandeaSection['logo_alt'] ?? $networkName }}" class="ac-family-ffi-logo">
                    </div>
                </div>
            </section>
        </div>

        <section id="finance-usluge" class="ac-finance-editorial-wrap" aria-labelledby="ac-finance-services-title">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-support-story-hero">
                    <div class="ac-support-story-shell">
                        <div class="ac-services-head ac-support-story-head">
                            <div class="ac-services-eyebrow">
                                <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                                <p class="ac-services-kicker">{{ $servicesIntroSection['kicker'] ?? 'USLUGE' }}</p>
                                <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                            </div>
                            <h2 id="ac-finance-services-title">
                                @foreach ($servicesIntroTitleLines as $line)
                                    <span>{{ $line }}</span>
                                @endforeach
                            </h2>
                            <p class="ac-services-intro">{{ $servicesIntroSection['intro'] ?? '' }}</p>
                            <div class="ac-services-divider" aria-hidden="true">
                                <span class="ac-services-divider-line"></span>
                                <span class="ac-services-divider-glyph"></span>
                                <span class="ac-services-divider-line"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ac-finance-editorial-shell">
                    <article class="ac-finance-editorial-section">
                        <div class="ac-finance-editorial-head">
                            <div class="ac-finance-editorial-title">
                                <span class="ac-finance-editorial-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M4 8.5 12 5l8 3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M6 10v8M12 10v8M18 10v8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <path d="M4 18.5h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                </span>
                                <h2>{{ $maSection['title'] ?? '' }}</h2>
                            </div>
                            <div class="ac-finance-editorial-intro">
                                <p>{{ $maSection['intro'] ?? '' }}</p>
                            </div>
                        </div>

                        <div class="ac-finance-columns ac-finance-columns--two">
                            <article class="ac-finance-column">
                                <h3>{{ $maSection['sale']['title'] ?? '' }}</h3>
                                <p>{{ $maSection['sale']['body'] ?? '' }}</p>
                            </article>

                            <article class="ac-finance-column">
                                <h3>{{ $maSection['acquisition']['title'] ?? '' }}</h3>
                                <p>{{ $maSection['acquisition']['body'] ?? '' }}</p>
                            </article>
                        </div>

                        <div class="ac-finance-followup">
                            <p class="ac-family-section-kicker">{{ $maSection['sale']['process_title'] ?? '' }}</p>

                            <div class="ac-finance-columns ac-finance-columns--five">
                                @foreach (($maSection['sale']['phases'] ?? []) as $phase)
                                    <article class="ac-finance-column ac-finance-column--compact">
                                        <p class="ac-finance-phase-step">{{ $phase['title'] ?? '' }}</p>
                                        <h3>{{ $phase['label'] ?? '' }}</h3>
                                        <ul class="ac-finance-list">
                                            @foreach (($phase['items'] ?? []) as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </article>

                    <article class="ac-finance-editorial-section">
                        <div class="ac-finance-editorial-head">
                            <div class="ac-finance-editorial-title">
                                <span class="ac-finance-editorial-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <circle cx="11" cy="11" r="5.5" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M16 16 20 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <path d="M11 8.5v5M8.5 11h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                </span>
                                <h2>{{ $dueDiligenceSection['title'] ?? '' }}</h2>
                            </div>
                            <div class="ac-finance-editorial-intro">
                                <p>{{ $dueDiligenceSection['intro'] ?? '' }}</p>
                            </div>
                        </div>

                        <div class="ac-finance-columns ac-finance-columns--two-wide">
                            <article class="ac-finance-column">
                                <p class="ac-family-section-kicker">{{ $dueDiligenceSection['help_title'] ?? '' }}</p>
                                <ul class="ac-finance-list">
                                    @foreach (($dueDiligenceSection['help_items'] ?? []) as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </article>

                            <article class="ac-finance-column">
                                <p>{{ $dueDiligenceSection['closing'] ?? '' }}</p>
                            </article>
                        </div>
                    </article>

                    <article class="ac-finance-editorial-section">
                        <div class="ac-finance-editorial-head">
                            <div class="ac-finance-editorial-title">
                                <span class="ac-finance-editorial-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M5 18.5h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <path d="M7 15V9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <path d="M12 15V6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <path d="M17 15v-3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                </span>
                                <h2>{{ $valuationsSection['title'] ?? '' }}</h2>
                            </div>
                            <div class="ac-finance-editorial-intro">
                                <p>{{ $valuationBody[0] ?? '' }}</p>
                            </div>
                        </div>

                        <div class="ac-finance-columns ac-finance-columns--two-wide">
                            <article class="ac-finance-column">
                                @foreach (array_slice($valuationBody, 1) as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </article>

                            <article class="ac-finance-column">
                                <p class="ac-family-section-kicker">{{ $valuationsSection['methods_title'] ?? '' }}</p>
                                <ul class="ac-finance-list">
                                    @foreach (($valuationsSection['methods'] ?? []) as $method)
                                        <li>{{ $method }}</li>
                                    @endforeach
                                </ul>
                            </article>
                        </div>
                    </article>

                    <article class="ac-finance-editorial-section">
                        <div class="ac-finance-editorial-head">
                            <div class="ac-finance-editorial-title">
                                <span class="ac-finance-editorial-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M4.5 16.5 9 12l3 3 7.5-8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M16 7h3.5v3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5 19h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                </span>
                                <h2>{{ $capitalRaisingSection['title'] ?? '' }}</h2>
                            </div>
                            <div class="ac-finance-editorial-intro">
                                <p>{{ $capitalBody[0] ?? '' }}</p>
                            </div>
                        </div>

                        <div class="ac-finance-columns ac-finance-columns--two-wide">
                            <article class="ac-finance-column">
                                @foreach (array_slice($capitalBody, 1) as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </article>

                            <article class="ac-finance-column">
                                <p class="ac-family-section-kicker">{{ $capitalRaisingSection['sources_title'] ?? '' }}</p>
                                <ul class="ac-finance-list">
                                    @foreach (($capitalRaisingSection['sources'] ?? []) as $source)
                                        <li>{{ $source }}</li>
                                    @endforeach
                                </ul>
                            </article>
                        </div>
                    </article>

                    <article class="ac-finance-editorial-section">
                        <div class="ac-finance-editorial-head">
                            <div class="ac-finance-editorial-title">
                                <span class="ac-finance-editorial-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M6 8h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <path d="M6 12h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <path d="M6 16h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <path d="M17 9.5 20 12l-3 2.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <h2>{{ $restructuringSection['title'] ?? '' }}</h2>
                            </div>
                            <div class="ac-finance-editorial-intro">
                                <p>{{ $restructuringBody[0] ?? '' }}</p>
                            </div>
                        </div>

                        <div class="ac-finance-columns ac-finance-columns--single">
                            <article class="ac-finance-column ac-finance-column--spacious">
                                @foreach (array_slice($restructuringBody, 1) as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </article>
                        </div>

                        <div class="ac-finance-followup">
                            <p class="ac-family-section-kicker">{{ $restructuringSection['prebankruptcy_title'] ?? '' }}</p>
                            <div class="ac-finance-columns ac-finance-columns--single">
                                <article class="ac-finance-column">
                                    <p>{{ $restructuringSection['prebankruptcy_body'] ?? '' }}</p>
                                </article>
                            </div>
                        </div>

                        <div class="ac-finance-columns ac-finance-columns--three">
                            <article class="ac-finance-column">
                                <p class="ac-family-section-kicker">{{ $restructuringSection['options_title'] ?? '' }}</p>
                                <ul class="ac-finance-list">
                                    @foreach (($restructuringSection['options'] ?? []) as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </article>

                            <article class="ac-finance-column">
                                <p class="ac-family-section-kicker">{{ $restructuringSection['reasons_title'] ?? '' }}</p>
                                <ul class="ac-finance-list">
                                    @foreach (($restructuringSection['reasons'] ?? []) as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </article>

                            <article class="ac-finance-column">
                                <p class="ac-family-section-kicker">{{ $restructuringSection['team_services_title'] ?? '' }}</p>
                                <ul class="ac-finance-list">
                                    @foreach (($restructuringSection['team_services'] ?? []) as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </article>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
            <section id="finance-sastanak" class="ac-family-section pb-16 md:pb-24" aria-labelledby="ac-finance-meeting-title">
                <div class="ac-family-team-showcase-head">
                    <p class="ac-family-section-kicker">{{ $meetingSection['kicker'] ?? 'KONTAKT' }}</p>
                    <h2 id="ac-finance-meeting-title">{{ $meetingSection['title'] ?? '' }}</h2>
                    <p>{{ $meetingSection['intro'] ?? '' }}</p>
                </div>

                <div class="mt-10 grid gap-5 lg:grid-cols-[300px_minmax(0,1fr)] lg:items-start">
                    <aside class="front-contact-sidebar">
                        <div class="front-contact-panel front-contact-panel--direct">
                            <h2>{{ $meetingSection['visit_title'] ?? 'Posjetite nas' }}</h2>
                            <div class="mt-4 space-y-1 text-[0.89rem] leading-6 text-slate-700">
                                <p style="white-space: nowrap;">{{ $meetingSection['visit_lines'][0] ?? '' }}</p>
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
                        <input type="hidden" name="redirect_to" value="{{ route('finance.show') }}#finance-sastanak">

                        @if (session('status'))
                            <div class="front-contact-status" role="status">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="finance-first-name">{{ $meetingFormLabels['first_name'] ?? 'Ime' }}</label>
                                <input id="finance-first-name" type="text" name="first_name" value="{{ old('first_name') }}" class="front-contact-input h-11 w-full text-sm" required>
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('first_name') ? '' : 'hidden' }}" data-field-error="first_name">@error('first_name'){{ $message }}@enderror</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="finance-last-name">{{ $meetingFormLabels['last_name'] ?? 'Prezime' }}</label>
                                <input id="finance-last-name" type="text" name="last_name" value="{{ old('last_name') }}" class="front-contact-input h-11 w-full text-sm">
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('last_name') ? '' : 'hidden' }}" data-field-error="last_name">@error('last_name'){{ $message }}@enderror</p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="finance-company">{{ $meetingFormLabels['company'] ?? 'Tvrtka' }}</label>
                                <input id="finance-company" type="text" name="company" value="{{ old('company') }}" class="front-contact-input h-11 w-full text-sm">
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('company') ? '' : 'hidden' }}" data-field-error="company">@error('company'){{ $message }}@enderror</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="finance-phone">{{ $meetingFormLabels['phone'] ?? 'Broj telefona' }}</label>
                                <input id="finance-phone" type="text" name="phone" value="{{ old('phone') }}" class="front-contact-input h-11 w-full text-sm">
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('phone') ? '' : 'hidden' }}" data-field-error="phone">@error('phone'){{ $message }}@enderror</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="finance-email">{{ $meetingFormLabels['email'] ?? 'Email' }}</label>
                            <input id="finance-email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" class="front-contact-input h-11 w-full text-sm" required>
                            <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('email') ? '' : 'hidden' }}" data-field-error="email">@error('email'){{ $message }}@enderror</p>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="finance-subject">{{ $meetingFormLabels['subject'] ?? 'Naslov poruke' }}</label>
                            <input id="finance-subject" type="text" name="subject" value="{{ old('subject') }}" class="front-contact-input h-11 w-full text-sm">
                            <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('subject') ? '' : 'hidden' }}" data-field-error="subject">@error('subject'){{ $message }}@enderror</p>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="finance-message">{{ $meetingFormLabels['message'] ?? 'Poruka' }}</label>
                            <textarea id="finance-message" name="message" rows="8" class="front-contact-textarea w-full text-sm" required>{{ old('message') }}</textarea>
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
    </div>
@endsection

@push('styles')
    <style>
        .ac-finance-page {
            background:
                radial-gradient(circle at top right, rgba(171, 141, 82, 0.14), transparent 28%),
                linear-gradient(180deg, #f5f8fb 0%, #ffffff 20%, #fbfaf7 100%);
        }

        .ac-finance-network-section {
            margin-top: clamp(2rem, 3.6vw, 2.8rem);
            padding-top: 0;
            padding-bottom: clamp(2rem, 3.6vw, 2.8rem);
        }

        .ac-finance-network-section .ac-family-ffi-banner {
            margin-top: 0;
        }

        .ac-finance-editorial-wrap {
            position: relative;
            padding: clamp(2.6rem, 4vw, 3.5rem) 0;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.82) 0%, rgba(249, 250, 252, 0.94) 100%),
                repeating-linear-gradient(
                    90deg,
                    rgba(15, 27, 45, 0.045) 0,
                    rgba(15, 27, 45, 0.045) 1px,
                    transparent 1px,
                    transparent 18px
                );
            border-top: 1px solid rgba(216, 196, 160, 0.3);
            border-bottom: 1px solid rgba(216, 196, 160, 0.3);
        }

        .ac-finance-editorial-shell {
            border-top: 1px solid rgba(216, 196, 160, 0.4);
        }

        .ac-finance-editorial-section {
            padding: clamp(2rem, 3vw, 3rem) 0;
            border-bottom: 1px solid rgba(216, 196, 160, 0.42);
        }

        .ac-finance-editorial-section:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .ac-finance-editorial-head {
            display: grid;
            gap: 1.75rem;
            align-items: start;
        }

        .ac-finance-editorial-title {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .ac-finance-editorial-icon {
            display: inline-flex;
            width: 3.6rem;
            height: 3.6rem;
            flex: none;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: linear-gradient(180deg, #0f1b2d 0%, #123250 100%);
            color: #fff;
            box-shadow: 0 14px 28px rgba(15, 27, 45, 0.12);
        }

        .ac-finance-editorial-icon svg {
            width: 1.55rem;
            height: 1.55rem;
        }

        .ac-finance-editorial-title h2 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.9rem, 2.6vw, 3.05rem);
            line-height: 1.05;
            font-weight: 600;
            color: #0f172a;
        }

        .ac-finance-editorial-intro {
            max-width: 40rem;
            font-size: 0.98rem;
            line-height: 2rem;
            color: #516173;
        }

        .ac-finance-columns {
            display: grid;
            gap: 1.75rem;
            margin-top: 2rem;
        }

        .ac-finance-columns--two {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ac-finance-columns--two-wide {
            grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
        }

        .ac-finance-columns--three {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .ac-finance-columns--five {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .ac-finance-columns--single {
            grid-template-columns: minmax(0, 1fr);
        }

        .ac-finance-column {
            min-width: 0;
            padding-left: 1.15rem;
            border-left: 2px solid rgba(216, 196, 160, 0.82);
        }

        .ac-finance-column--compact {
            padding-left: 1rem;
        }

        .ac-finance-column h3 {
            font-size: 1.12rem;
            font-weight: 700;
            line-height: 1.5rem;
            color: #0f172a;
        }

        .ac-finance-column p {
            margin-top: 0.85rem;
            font-size: 0.98rem;
            line-height: 1.95rem;
            color: #516173;
        }

        .ac-finance-column p + p {
            margin-top: 0.95rem;
        }

        .ac-finance-list {
            margin-top: 1rem;
            display: grid;
            gap: 0.8rem;
            padding-left: 1.05rem;
            color: #516173;
        }

        .ac-finance-list li {
            line-height: 1.7rem;
        }

        .ac-finance-followup {
            margin-top: 2rem;
        }

        .ac-finance-phase-step {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #9a773d;
        }

        @media (min-width: 960px) {
            .ac-finance-editorial-head {
                grid-template-columns: minmax(0, 0.86fr) minmax(0, 1fr);
                gap: 2.4rem;
            }
        }

        @media (max-width: 1180px) {
            .ac-finance-columns--five {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 900px) {
            .ac-finance-columns--two,
            .ac-finance-columns--two-wide,
            .ac-finance-columns--three,
            .ac-finance-columns--five {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        @media (max-width: 640px) {
            .ac-finance-network-section {
                margin-top: 1.35rem;
                padding-bottom: 1.35rem;
            }

            .ac-finance-editorial-wrap {
                padding: 2.1rem 0;
            }

            .ac-finance-editorial-section {
                padding: 1.7rem 0;
            }

            .ac-finance-editorial-title {
                align-items: flex-start;
            }

            .ac-finance-editorial-icon {
                width: 3.15rem;
                height: 3.15rem;
                border-radius: 16px;
            }

            .ac-finance-editorial-icon svg {
                width: 1.35rem;
                height: 1.35rem;
            }

            .ac-finance-column {
                padding-left: 0.9rem;
            }
        }
    </style>
@endpush

@include('front.desktop.contact.partials.form-script', [
    'captchaEnabled' => $captchaEnabled,
    'captchaSiteKey' => $captchaSiteKey,
])

@push('scripts')
    <script>
        (function () {
            const shouldFocusSection = {{ ($errors->any() || session('status')) ? 'true' : 'false' }};
            const section = document.getElementById('finance-sastanak');

            if (shouldFocusSection && section) {
                requestAnimationFrame(function () {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            }
        }());
    </script>
@endpush
