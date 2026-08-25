@extends('front.desktop.layouts.store')

@section('title', trim((string) data_get($contactPageContent ?? [], 'page_title', '')))
@section('description', trim((string) data_get($contactPageContent ?? [], 'intro', '')))
@section('main_class', 'w-full px-0 py-0')
@section('hide_footer_newsletter', '1')

@section('content')
    @php
        $captchaSiteKey = trim((string) ($storeSettings['captcha']['recaptcha_v3_site_key'] ?? ''));
        $captchaEnabled = (bool) ($storeSettings['captcha']['recaptcha_v3_enabled'] ?? false) && $captchaSiteKey !== '';
        $contactPageContent = is_array($contactPageContent ?? null) ? $contactPageContent : [];
        $contactCopy = static fn (string $key): string => trim((string) data_get($contactPageContent, $key, ''));
        $contactEmail = $contactCopy('direct_email');
        $contactPhone = $contactCopy('direct_phone');
        $contactPhoneHref = preg_replace('/[^+0-9]/', '', str_replace('(0)', '', $contactPhone));
        $contactOfficeOrder = [
            'alpha-capitalis' => 0,
            'alpha-capitalis-east' => 1,
            'alpha-capitalis-timia' => 2,
        ];
        $contactOfficePhones = collect((array) data_get($contactLocationsContent ?? [], 'items', []))
            ->filter(static fn ($item): bool => is_array($item) && trim((string) ($item['phone'] ?? '')) !== '')
            ->map(static function (array $item) use ($contactOfficeOrder): array {
                $entityKey = trim((string) ($item['entity_key'] ?? ''));
                $phone = trim((string) ($item['phone'] ?? ''));

                return [
                    'label' => trim((string) ($item['office_label'] ?? '')),
                    'phone' => $phone,
                    'phone_href' => preg_replace('/[^+0-9]/', '', str_replace('(0)', '', $phone)),
                    'sort_order' => $contactOfficeOrder[$entityKey] ?? 99,
                ];
            })
            ->sortBy('sort_order')
            ->values();
        $contactHours = trim((string) ($storeSettings['footer']['hours'] ?? '')) ?: $contactCopy('direct_response_fallback');
        $headingWords = static fn (string $title): array => preg_split('/\s+/u', trim($title), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $contactPageTitle = $contactCopy('page_title');
        $contactPageIntro = $contactCopy('intro');
        $contactFormTitle = $contactCopy('form_title');
        $contactFormIntro = $contactCopy('form_intro');
        $contactNameLabel = $contactCopy('name_label');
        $contactEmailLabel = $contactCopy('email_label');
        $contactPhoneLabel = $contactCopy('phone_label');
        $contactSubjectLabel = $contactCopy('subject_label');
        $contactMessageLabel = $contactCopy('message_label');
        $contactConsentLabel = $contactCopy('consent_label');
        $contactSubmitLabel = $contactCopy('submit_label');
        $contactDirectTitle = $contactCopy('direct_title');
        $contactDirectBody = $contactCopy('direct_body');
        $contactDirectEmailLabel = $contactCopy('direct_email_label');
        $contactDirectPhoneLabel = $contactCopy('direct_phone_label');
        $contactDirectResponseTimeLabel = $contactCopy('direct_response_time_label');
        $contactHelpTitle = $contactCopy('help_title');
        $contactHelpBody = $contactCopy('help_body');
    @endphp

    <div class="front-contact-page ac-contact-page">
        <section class="ac-contact-intro" aria-labelledby="ac-contact-title">
            <div class="ac-contact-container ac-contact-intro-layout">
                <div class="ac-contact-intro-heading">
                    @php($contactHeadingWords = $headingWords($contactPageTitle))
                    <h1 class="values-title services-index-intro-title ac-contact-display-title" id="ac-contact-title" data-words-slide-from-right aria-label="{{ $contactPageTitle }}">
                        @foreach ($contactHeadingWords as $word)
                            <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last && count($contactHeadingWords) > 1 ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h1>
                </div>

                <div class="ac-contact-intro-copy content-reveal animation-index-1" data-image-reveal>
                    <p>{{ $contactPageIntro }}</p>
                </div>
            </div>
        </section>

        @include('front.desktop.partials.locations-showcase', [
            'locationsSectionId' => 'contact-locations',
            'locationsTitleId' => 'contact-locations-title',
            'locationDetailsPrefix' => 'contact-location-details',
            'showLocationStats' => true,
            'locationsContent' => $contactLocationsContent ?? [],
            'locationStats' => $contactLocationStats ?? collect(),
        ])

        <section class="front-contact-content-shell ac-contact-form-section" aria-labelledby="ac-contact-form-title">
            <div class="ac-contact-container front-contact-layout">
                <form
                    method="POST"
                    action="{{ \App\Support\Localization\FrontendRoute::url('contact.store') }}"
                    class="front-contact-form content-reveal animation-index-0"
                    novalidate
                    data-contact-form
                    data-image-reveal
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
                        <h2 id="ac-contact-form-title">{{ $contactFormTitle }}</h2>
                        <p>{{ $contactFormIntro }}</p>
                    </div>

                    @if (session('status'))
                        <div class="front-contact-status" role="status">
                            <i class="fa-light fa-circle-check" aria-hidden="true"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <div class="ac-contact-field-grid">
                        <div class="ac-contact-field">
                            <label for="contact-name">{{ $contactNameLabel }}</label>
                            <input id="contact-name" type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" class="front-contact-input" autocomplete="name" required aria-describedby="contact-name-error" aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}">
                            <p id="contact-name-error" class="front-contact-field-error" data-field-error="name" @if (! $errors->has('name')) hidden @endif>@error('name'){{ $message }}@enderror</p>
                        </div>
                        <div class="ac-contact-field">
                            <label for="contact-email">{{ $contactEmailLabel }}</label>
                            <input id="contact-email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" class="front-contact-input" autocomplete="email" required aria-describedby="contact-email-error" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}">
                            <p id="contact-email-error" class="front-contact-field-error" data-field-error="email" @if (! $errors->has('email')) hidden @endif>@error('email'){{ $message }}@enderror</p>
                        </div>
                        <div class="ac-contact-field">
                            <label for="contact-phone">{{ $contactPhoneLabel }}</label>
                            <input id="contact-phone" type="tel" name="phone" value="{{ old('phone') }}" class="front-contact-input" autocomplete="tel" aria-describedby="contact-phone-error" aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}">
                            <p id="contact-phone-error" class="front-contact-field-error" data-field-error="phone" @if (! $errors->has('phone')) hidden @endif>@error('phone'){{ $message }}@enderror</p>
                        </div>
                        <div class="ac-contact-field">
                            <label for="contact-subject">{{ $contactSubjectLabel }}</label>
                            <input id="contact-subject" type="text" name="subject" value="{{ old('subject') }}" class="front-contact-input" aria-describedby="contact-subject-error" aria-invalid="{{ $errors->has('subject') ? 'true' : 'false' }}">
                            <p id="contact-subject-error" class="front-contact-field-error" data-field-error="subject" @if (! $errors->has('subject')) hidden @endif>@error('subject'){{ $message }}@enderror</p>
                        </div>
                        <div class="ac-contact-field ac-contact-field--full">
                            <label for="contact-message">{{ $contactMessageLabel }}</label>
                            <textarea id="contact-message" name="message" rows="7" class="front-contact-textarea" required aria-describedby="contact-message-error" aria-invalid="{{ $errors->has('message') ? 'true' : 'false' }}">{{ old('message') }}</textarea>
                            <p id="contact-message-error" class="front-contact-field-error" data-field-error="message" @if (! $errors->has('message')) hidden @endif>@error('message'){{ $message }}@enderror</p>
                        </div>
                    </div>

                    <div class="front-contact-consent-wrap">
                        <label class="front-contact-consent" for="contact-accept-terms">
                            <input id="contact-accept-terms" type="checkbox" name="accept_terms" value="1" class="front-contact-checkbox" @checked((bool) old('accept_terms')) aria-describedby="contact-accept-terms-error" aria-invalid="{{ $errors->has('accept_terms') ? 'true' : 'false' }}">
                            <span>{{ $contactConsentLabel }}</span>
                        </label>
                        <p id="contact-accept-terms-error" class="front-contact-field-error" data-field-error="accept_terms" @if (! $errors->has('accept_terms')) hidden @endif>@error('accept_terms'){{ $message }}@enderror</p>
                    </div>

                    <div class="front-contact-form-actions">
                        <button type="submit" class="editorial-dark-button ac-contact-submit">
                            <span>{{ $contactSubmitLabel }}</span>
                            <i class="fa-light fa-arrow-up-right" aria-hidden="true"></i>
                        </button>
                        <p class="front-contact-field-error" data-field-error="recaptcha_token" @if (! $errors->has('recaptcha_token')) hidden @endif>@error('recaptcha_token'){{ $message }}@enderror</p>
                    </div>
                </form>

                <aside class="front-contact-sidebar content-reveal animation-index-1" data-image-reveal aria-label="{{ $contactDirectTitle }}">
                    <div class="front-contact-panel front-contact-panel--direct">
                        <h2>{{ $contactDirectTitle }}</h2>
                        <p class="front-contact-panel-intro">{{ $contactDirectBody }}</p>

                        <ul class="front-contact-direct-list">
                            @if ($contactEmail !== '')
                            <li>
                                <i class="fa-light fa-envelope" aria-hidden="true"></i>
                                <span>
                                    <small>{{ $contactDirectEmailLabel }}</small>
                                    <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                                </span>
                            </li>
                            @endif
                            @forelse ($contactOfficePhones as $officePhone)
                                <li>
                                    <i class="fa-light fa-phone" aria-hidden="true"></i>
                                    <span>
                                        <small>{{ $officePhone['label'] !== '' ? $officePhone['label'] : $contactDirectPhoneLabel }}</small>
                                        <a href="tel:{{ $officePhone['phone_href'] }}">{{ $officePhone['phone'] }}</a>
                                    </span>
                                </li>
                            @empty
                                @if ($contactPhone !== '')
                                    <li>
                                        <i class="fa-light fa-phone" aria-hidden="true"></i>
                                        <span>
                                            <small>{{ $contactDirectPhoneLabel }}</small>
                                            <a href="tel:{{ $contactPhoneHref }}">{{ $contactPhone }}</a>
                                        </span>
                                    </li>
                                @endif
                            @endforelse
                            <li>
                                <i class="fa-light fa-clock" aria-hidden="true"></i>
                                <span>
                                    <small>{{ $contactDirectResponseTimeLabel }}</small>
                                    <strong>{{ $contactHours }}</strong>
                                </span>
                            </li>
                        </ul>
                    </div>

                    <div class="front-contact-help">
                        <span class="front-contact-help-icon" aria-hidden="true">
                            <i class="fa-light fa-message-lines"></i>
                        </span>
                        <div>
                            <h3>{{ $contactHelpTitle }}</h3>
                            <p>{{ $contactHelpBody }}</p>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

    </div>

    @include('front.desktop.contact.partials.form-script', [
        'captchaEnabled' => $captchaEnabled,
        'captchaSiteKey' => $captchaSiteKey,
    ])
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/contact.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/contact.css')) }}">
@endpush
