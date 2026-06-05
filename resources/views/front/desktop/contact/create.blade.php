@extends('front.desktop.layouts.store')

@section('title', __('contact.page_title'))
@section('main_class', 'w-full px-0 py-0')

@section('content')
    @php
        $captchaSiteKey = trim((string) ($storeSettings['captcha']['recaptcha_v3_site_key'] ?? ''));
        $captchaEnabled = (bool) ($storeSettings['captcha']['recaptcha_v3_enabled'] ?? false) && $captchaSiteKey !== '';
        $contactOffices = collect((array) ($storeSettings['official_entities'] ?? []))
            ->filter(static fn ($office): bool => is_array($office) && (bool) ($office['show_on_contact'] ?? false))
            ->map(static function (array $office): array {
                $address = $office['contact_address'] ?? $office['address'] ?? [];
                $office['label'] = trim((string) ($office['label'] ?? $office['office_label'] ?? ''));
                $office['address'] = collect(is_array($address) ? $address : [])
                    ->map(static fn ($line): string => trim((string) $line))
                    ->filter()
                    ->values()
                    ->all();
                $office['phone_href'] = preg_replace('/\s+/', '', (string) ($office['phone'] ?? ''));

                $query = trim((string) ($office['map_query'] ?? ''));
                $encodedQuery = rawurlencode($query);
                $embedUrl = trim((string) ($office['map_embed_url'] ?? ''));

                $office['map_embed_url'] = $embedUrl !== ''
                    ? $embedUrl
                    : ($encodedQuery !== '' ? 'https://www.google.com/maps?q='.$encodedQuery.'&z=16&output=embed' : '');
                $office['map_external_url'] = $encodedQuery !== ''
                    ? 'https://www.google.com/maps/search/?api=1&query='.$encodedQuery
                    : '';

                return $office;
            })
            ->values()
            ->all();
        $primaryOffice = collect($contactOffices)->firstWhere('key', 'alpha-capitalis-timia') ?? ($contactOffices[0] ?? null);
        $contactEmail = trim((string) ($storeSettings['footer']['email_support'] ?? ''))
            ?: trim((string) ($primaryOffice['email'] ?? ''))
            ?: 'info@alphacapitalis.com';
        $contactPhone = trim((string) ($storeSettings['footer']['phone'] ?? ''))
            ?: trim((string) ($primaryOffice['phone'] ?? ''))
            ?: '+385 (0) 51 301 503';
        $contactPhoneHref = preg_replace('/\s+/', '', $contactPhone);
        $contactHours = trim((string) ($storeSettings['footer']['hours'] ?? '')) ?: __('contact.direct.response_fallback');
        $pageTitleBreadcrumbs = [
            ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
            ['label' => __('contact.page_title'), 'current' => true],
        ];
    @endphp

    <div class="front-contact-page ac-contact-page">
        <x-front.page-title-band :breadcrumbs="$pageTitleBreadcrumbs" section-class="ac-contact-title-band">
            <div class="ac-page-title-copy">
                <h1>{{ __('contact.heading') }}</h1>
                <p>{{ __('contact.subheading') }}</p>
            </div>
        </x-front.page-title-band>

        <section class="front-contact-offices-shell">
            <div class="mx-auto w-full max-w-[1320px] px-4 sm:px-6 lg:px-8">
                <div class="front-contact-offices-head">
                    <p class="front-contact-section-kicker">{{ __('contact.offices.kicker') }}</p>
                    <h2>{{ __('contact.offices.title') }}</h2>
                    <p>{{ __('contact.offices.intro') }}</p>
                </div>

                <div class="front-contact-offices-grid">
                    @foreach ($contactOffices as $office)
                        <article class="front-contact-office-card">
                            <div class="front-contact-office-top">
                                <p class="front-contact-office-label">{{ $office['label'] }}</p>
                                <h3>{{ $office['company'] }}</h3>
                            </div>

                            <div class="front-contact-office-body">
                                @foreach ($office['address'] as $line)
                                    <p>{{ $line }}</p>
                                @endforeach

                                @if (trim((string) ($office['mbs'] ?? '')) !== '')
                                    <p>MBS: {{ $office['mbs'] }}</p>
                                @endif

                                @if (trim((string) ($office['iban'] ?? '')) !== '')
                                    <p>IBAN: {{ $office['iban'] }}</p>
                                @endif

                                <button type="button" class="front-contact-office-map-trigger" data-office-map-trigger="{{ $office['key'] }}">
                                    <span class="front-contact-inline-icon" aria-hidden="true">
                                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8">
                                            <path d="M10 18s5-4.6 5-9a5 5 0 1 0-10 0c0 4.4 5 9 5 9z"/>
                                            <circle cx="10" cy="9" r="1.9"/>
                                        </svg>
                                    </span>
                                    <span>{{ __('contact.offices.view_map') }}</span>
                                </button>
                            </div>

                            <div class="front-contact-office-meta">
                                <a href="mailto:{{ $office['email'] }}" class="front-contact-office-link">
                                    <span>{{ __('contact.direct.email') }}</span>
                                    <strong>
                                        <span class="front-contact-inline-icon" aria-hidden="true">
                                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8">
                                                <path d="M3 5.5h14v9H3z"/>
                                                <path d="m4 6 6 4.8L16 6"/>
                                            </svg>
                                        </span>
                                        {{ $office['email'] }}
                                    </strong>
                                </a>
                                <a href="tel:{{ $office['phone_href'] }}" class="front-contact-office-link">
                                    <span>{{ __('contact.direct.phone') }}</span>
                                    <strong>
                                        <span class="front-contact-inline-icon" aria-hidden="true">
                                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8">
                                                <path d="M6.2 3.8h2.1l1.2 3.2-1.5 1.5a11 11 0 0 0 3.9 3.9l1.5-1.5 3.2 1.2v2.1c0 .6-.5 1-1.1 1A12.6 12.6 0 0 1 4.1 4.9c0-.6.5-1.1 1.1-1.1z"/>
                                            </svg>
                                        </span>
                                        {{ $office['phone'] }}
                                    </strong>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="front-contact-content-shell">
            <div class="mx-auto w-full max-w-[1320px] px-4 sm:px-6 lg:px-8">
                <div class="front-contact-layout">
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

                        <div class="front-contact-form-head">
                            <p class="front-contact-section-kicker">{{ __('contact.form.kicker') }}</p>
                            <h2>{{ __('contact.form.title') }}</h2>
                            <p>{{ __('contact.form.intro') }}</p>
                        </div>

                        @if (session('status'))
                            <div class="front-contact-status" role="status">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('contact.form.name') }}</label>
                                <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" class="front-contact-input h-11 w-full text-sm" required>
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('name') ? '' : 'hidden' }}" data-field-error="name">@error('name'){{ $message }}@enderror</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('contact.form.email') }}</label>
                                <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" class="front-contact-input h-11 w-full text-sm" required>
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('email') ? '' : 'hidden' }}" data-field-error="email">@error('email'){{ $message }}@enderror</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('contact.form.phone') }}</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="front-contact-input h-11 w-full text-sm">
                            <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('phone') ? '' : 'hidden' }}" data-field-error="phone">@error('phone'){{ $message }}@enderror</p>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('contact.form.subject') }}</label>
                            <input type="text" name="subject" value="{{ old('subject') }}" class="front-contact-input h-11 w-full text-sm">
                            <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('subject') ? '' : 'hidden' }}" data-field-error="subject">@error('subject'){{ $message }}@enderror</p>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('contact.form.message') }}</label>
                            <textarea name="message" rows="8" class="front-contact-textarea w-full text-sm" required>{{ old('message') }}</textarea>
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
                                {{ __('contact.form.submit') }}
                            </button>
                            <p class="text-xs font-semibold text-rose-600 {{ $errors->has('recaptcha_token') ? '' : 'hidden' }}" data-field-error="recaptcha_token">@error('recaptcha_token'){{ $message }}@enderror</p>
                        </div>
                    </form>

                    <aside class="front-contact-sidebar">
                        <div class="front-contact-panel front-contact-panel--direct">
                            <h2>{{ __('contact.direct.title') }}</h2>
                            <p class="front-contact-panel-intro">{{ __('contact.direct.body') }}</p>

                            <ul class="front-contact-direct-list">
                                <li>
                                    <span>{{ __('contact.direct.email') }}</span>
                                    <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                                </li>
                                <li>
                                    <span>{{ __('contact.direct.phone') }}</span>
                                    <a href="tel:{{ $contactPhoneHref }}">{{ $contactPhone }}</a>
                                </li>
                                <li>
                                    <span>{{ __('contact.direct.response_time') }}</span>
                                    <strong>{{ $contactHours }}</strong>
                                </li>
                            </ul>
                        </div>

                        <div class="front-contact-help">
                            <h3>{{ __('contact.help.title') }}</h3>
                            <p>{{ __('contact.help.body') }}</p>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        <section id="contact-map-section" class="front-contact-map-shell">
            <div class="mx-auto w-full max-w-[1320px] px-4 sm:px-6 lg:px-8">
                <div class="front-contact-map-tabs" role="tablist" aria-label="{{ __('contact.map.title') }}">
                    @foreach ($contactOffices as $index => $office)
                        <button
                            type="button"
                            class="front-contact-map-tab{{ $index === 0 ? ' is-active' : '' }}"
                            data-office-map-tab="{{ $office['key'] }}"
                            role="tab"
                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                        >
                            {{ $office['map_label'] }}
                        </button>
                    @endforeach
                </div>

                <div class="front-contact-map-stage">
                    @foreach ($contactOffices as $index => $office)
                        <div
                            class="front-contact-map-panel{{ $index === 0 ? ' is-active' : '' }}"
                            data-office-map-panel="{{ $office['key'] }}"
                            @if ($index !== 0) hidden @endif
                        >
                            <div class="front-contact-map-frame">
                                <iframe
                                    src="{{ $office['map_embed_url'] }}"
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    allowfullscreen
                                    title="{{ $office['map_label'] }}"
                                ></iframe>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    @include('front.desktop.contact.partials.form-script', [
        'captchaEnabled' => $captchaEnabled,
        'captchaSiteKey' => $captchaSiteKey,
    ])

    @push('scripts')
        <script>
            (function () {
                const mapSection = document.getElementById('contact-map-section');
                const mapStage = mapSection ? mapSection.querySelector('.front-contact-map-stage') : null;
                const mapPanels = Array.from(document.querySelectorAll('[data-office-map-panel]'));
                const mapTabs = Array.from(document.querySelectorAll('[data-office-map-tab]'));
                const mapTriggers = Array.from(document.querySelectorAll('[data-office-map-trigger]'));

                const getHeaderOffset = function () {
                    const stickyHeader = document.querySelector('[data-front-sticky-header]');
                    if (!(stickyHeader instanceof HTMLElement)) {
                        return 18;
                    }

                    return Math.round(stickyHeader.getBoundingClientRect().height) + 18;
                };

                const scrollToMap = function () {
                    const scrollTarget = mapStage || mapSection;
                    if (!(scrollTarget instanceof HTMLElement)) {
                        return;
                    }

                    const targetTop = window.pageYOffset + scrollTarget.getBoundingClientRect().top - getHeaderOffset();

                    if (typeof window.__frontAnimateScrollTo === 'function') {
                        window.__frontAnimateScrollTo(targetTop);
                        return;
                    }

                    window.scrollTo(0, Math.max(0, targetTop));
                };

                const activateOfficeMap = function (officeKey) {
                    if (!officeKey) {
                        return;
                    }

                    mapPanels.forEach(function (panel) {
                        const isActive = panel.dataset.officeMapPanel === officeKey;
                        panel.hidden = !isActive;
                        panel.classList.toggle('is-active', isActive);
                    });

                    mapTabs.forEach(function (tab) {
                        const isActive = tab.dataset.officeMapTab === officeKey;
                        tab.classList.toggle('is-active', isActive);
                        tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
                    });
                };

                mapTabs.forEach(function (tab) {
                    tab.addEventListener('click', function () {
                        activateOfficeMap(tab.dataset.officeMapTab || '');
                    });
                });

                mapTriggers.forEach(function (trigger) {
                    trigger.addEventListener('click', function () {
                        const officeKey = trigger.dataset.officeMapTrigger || '';
                        activateOfficeMap(officeKey);

                        window.requestAnimationFrame(scrollToMap);
                    });
                });
            }());
        </script>
    @endpush
@endsection
