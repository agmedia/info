@extends('front.desktop.layouts.store')

{{-- Editorial career layout following the About page visual system. --}}

@php
    $translation = $selectedTranslation
        ?? $page->translations->firstWhere('locale', $locale)
        ?? $page->translations->firstWhere('locale', $fallbackLocale);

    $careerCaptchaSiteKey = trim((string) ($storeSettings['captcha']['recaptcha_v3_site_key'] ?? ''));
    $careerCaptchaEnabled = (bool) ($storeSettings['captcha']['recaptcha_v3_enabled'] ?? false) && $careerCaptchaSiteKey !== '';
    $careerFormShouldScroll = $errors->any() || session()->has('status');
    $careerContent = is_array($careerContent ?? null) ? $careerContent : [];
    $careerIntro = is_array($careerContent['intro'] ?? null) ? $careerContent['intro'] : [];
    $careerProcess = is_array($careerContent['process'] ?? null) ? $careerContent['process'] : [];
    $careerApplication = is_array($careerContent['application'] ?? null) ? $careerContent['application'] : [];
    $careerFormContent = is_array($careerContent['form'] ?? null) ? $careerContent['form'] : [];
    $isCroatian = str_starts_with(strtolower((string) $locale), 'hr');
    $careerValues = collect((array) ($careerContent['values'] ?? []))
        ->map(static fn ($value): string => trim((string) $value))
        ->filter()
        ->values();
    $careerIntroBody = collect((array) ($careerIntro['body'] ?? []))
        ->map(static fn ($paragraph): string => trim((string) $paragraph))
        ->filter()
        ->values();
    $careerApplicationParagraphs = collect((array) ($careerApplication['paragraphs'] ?? []))
        ->map(static fn ($paragraph): string => trim((string) $paragraph))
        ->filter()
        ->values();
    $careerProcessSteps = collect((array) ($careerProcess['steps'] ?? []))
        ->map(static fn ($step): array => is_array($step) ? $step : [])
        ->filter(static fn (array $step): bool => trim((string) ($step['title'] ?? '')) !== '')
        ->values();
    $careerStories = collect((array) ($careerContent['stories'] ?? []))
        ->map(static function ($story): array {
            $story = is_array($story) ? $story : [];

            return [
                'kicker' => trim((string) ($story['kicker'] ?? '')),
                'title' => trim((string) ($story['title'] ?? '')),
                'paragraphs' => collect((array) ($story['paragraphs'] ?? []))
                    ->map(static fn ($paragraph): string => trim((string) $paragraph))
                    ->filter()
                    ->values()
                    ->all(),
                'list' => collect((array) ($story['list'] ?? []))
                    ->map(static fn ($item): string => trim((string) $item))
                    ->filter()
                    ->values()
                    ->all(),
            ];
        })
        ->filter(static fn (array $story): bool => $story['title'] !== '')
        ->values();

    $careerCanonicalTitle = $isCroatian ? 'Karijera' : 'Career';
    $careerTranslationTitle = trim((string) ($translation?->title ?? ''));
    $careerPageTitle = $careerTranslationTitle !== '' && ! in_array($careerTranslationTitle, ['Ljudski potencijali', 'Human potential'], true)
        ? $careerTranslationTitle
        : $careerCanonicalTitle;
    $careerIntroTitle = $isCroatian ? 'Karijera u ALPHA CAPITALISU' : 'A career at ALPHA CAPITALIS';
    $careerHeroTitle = trim((string) ($careerIntro['title'] ?? '')) ?: ($isCroatian ? 'Mjesto gdje karijera stvarno raste' : 'A place where careers truly grow');
    $careerHeroHighlight = trim((string) ($careerIntro['highlight'] ?? '')) ?: ($isCroatian ? 'Ne tražimo samo zaposlenike.' : 'We are not simply looking for employees.');
    $careerIntroLead = (string) ($careerIntroBody->first() ?? '');
    $careerHeroParagraphs = $careerIntroBody->skip(1)->values();
    $careerProcessTitle = trim(implode(' ', array_filter([
        trim((string) ($careerProcess['title_line_one'] ?? '')),
        trim((string) ($careerProcess['title_line_two'] ?? '')),
    ])));
    $careerProcessTitle = $careerProcessTitle !== '' ? $careerProcessTitle : ($isCroatian ? 'Razvoj koji nije samo fraza' : 'Growth that is more than a phrase');
    $careerApplicationTitle = trim((string) ($careerApplication['title'] ?? '')) ?: ($isCroatian ? 'Otvorene pozicije' : 'Open positions');
    $careerApplicationHighlight = trim((string) ($careerApplication['highlight'] ?? ''));
    $headingWords = static fn (string $title): array => preg_split('/\s+/u', trim($title), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $processIconClasses = ['fa-handshake', 'fa-hands-holding-heart', 'fa-chart-line-up', 'fa-lightbulb-on'];
    $storyIconClasses = ['fa-people-group', 'fa-compass', 'fa-seedling'];
    $careerHeroMedia = $page->getFirstMedia('career_hero_image');
    $careerHeroMediaAlt = trim((string) (
        data_get($careerHeroMedia?->custom_properties, 'alt.'.$locale)
        ?: data_get($careerHeroMedia?->custom_properties, 'alt.'.$fallbackLocale)
        ?: $careerHeroMedia?->name
    ));
    $careerHeroPhoto = [
        'src' => $careerHeroMedia?->hasGeneratedConversion('career_hero_1440x1059')
            ? $careerHeroMedia->getUrl('career_hero_1440x1059')
            : ($careerHeroMedia?->getUrl() ?: asset('front-theme/images/career/karijera.png')),
        'alt' => $careerHeroMediaAlt !== ''
            ? $careerHeroMediaAlt
            : ($isCroatian ? 'ALPHA CAPITALIS tim' : 'ALPHA CAPITALIS team'),
    ];
@endphp

@section('title', $careerPageTitle)
@section('main_class', 'w-full px-0 py-0')
@section('hide_footer_newsletter', '1')

@section('content')
    <div class="ac-career-page">
        <section class="ac-career-intro" aria-labelledby="ac-career-intro-title">
            <div class="ac-career-container ac-career-intro-layout">
                <div class="ac-career-intro-heading">
                    <h1 class="values-title services-index-intro-title ac-career-display-title" id="ac-career-intro-title" data-words-slide-from-right aria-label="{{ $careerIntroTitle }}">
                        @foreach ($headingWords($careerIntroTitle) as $word)
                            <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h1>
                </div>

                <div class="ac-career-intro-copy content-reveal animation-index-1" data-image-reveal>
                    <h2 class="ac-career-copy-heading">{{ $careerHeroHighlight }}</h2>
                    @if ($careerIntroLead !== '')
                        <p>{{ $careerIntroLead }}</p>
                    @endif
                </div>
            </div>
        </section>

        <section class="ac-career-hero" aria-labelledby="ac-career-hero-title">
            <div class="ac-career-container ac-career-hero-grid">
                <div class="ac-career-hero-copy">
                    <p class="ac-family-section-kicker ac-career-kicker">{{ $isCroatian ? 'Rastemo zajedno' : 'Growing together' }}</p>
                    <h2 class="ac-career-dark-title" id="ac-career-hero-title" data-words-slide-from-right aria-label="{{ $careerHeroTitle }}">
                        @foreach ($headingWords($careerHeroTitle) as $word)
                            <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>

                    @if ($careerHeroParagraphs->isNotEmpty())
                        <div class="ac-career-copy-stack ac-career-copy-stack--light content-reveal animation-index-1" data-image-reveal>
                            @foreach ($careerHeroParagraphs as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    @endif

                    @if ($careerValues->isNotEmpty())
                        <ul class="ac-career-value-list content-reveal animation-index-2" data-image-reveal aria-label="{{ $isCroatian ? 'Što nudimo' : 'What we offer' }}">
                            @foreach ($careerValues as $value)
                                <li>
                                    <i class="fa-solid fa-check" aria-hidden="true"></i>
                                    <span>{{ $value }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="ac-career-hero-actions content-reveal animation-index-3" data-image-reveal>
                        <a href="#career-open-positions" class="button button-gold">
                            <span>{{ $isCroatian ? 'OTVORENE POZICIJE' : 'OPEN POSITIONS' }}</span>
                        </a>
                    </div>
                </div>

                <div class="ac-career-hero-media content-reveal animation-index-1" data-image-reveal>
                    <figure class="ac-career-hero-image image-reveal-media">
                        <img
                            src="{{ $careerHeroPhoto['src'] }}"
                            alt="{{ $careerHeroPhoto['alt'] }}"
                            width="1448"
                            height="1086"
                            loading="eager"
                            decoding="async"
                            fetchpriority="high"
                        >
                        <span class="image-reveal-curtain" aria-hidden="true"></span>
                    </figure>

                    <div class="ac-career-stat-card">
                        <strong>70+</strong>
                        <span>{{ $isCroatian ? 'stručnjaka iz računovodstva, financija, revizije i savjetovanja' : 'experts in accounting, finance, audit and advisory' }}</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="ac-career-development" aria-labelledby="ac-career-development-title">
            <div class="ac-career-container">
                <div class="ac-career-section-intro">
                    <h2 class="values-title services-index-intro-title ac-career-section-title" id="ac-career-development-title" data-words-slide-from-right aria-label="{{ $careerProcessTitle }}">
                        @foreach ($headingWords($careerProcessTitle) as $word)
                            <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>

                    <div class="ac-career-section-copy content-reveal animation-index-1" data-image-reveal>
                        @if (trim((string) ($careerProcess['kicker'] ?? '')) !== '')
                            <h3 class="ac-career-copy-heading">{{ $careerProcess['kicker'] }}</h3>
                        @endif
                        @if (trim((string) ($careerProcess['intro'] ?? '')) !== '')
                            <p>{{ $careerProcess['intro'] }}</p>
                        @endif
                    </div>
                </div>

                @if ($careerProcessSteps->isNotEmpty())
                    <div class="ac-career-development-grid">
                        @foreach ($careerProcessSteps as $step)
                            <article class="ac-career-development-card content-reveal animation-index-{{ $loop->index % 4 }}" data-image-reveal>
                                <div class="ac-career-card-head">
                                    <span class="ac-career-card-icon" aria-hidden="true">
                                        <i class="fa-duotone fa-thin fa-fw {{ $processIconClasses[$loop->index] ?? 'fa-circle-check' }}"></i>
                                    </span>
                                </div>
                                <h3>{{ $step['title'] }}</h3>
                                @if (trim((string) ($step['description'] ?? '')) !== '')
                                    <p>{{ $step['description'] }}</p>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        @if ($careerStories->isNotEmpty())
            <section class="ac-career-stories" aria-labelledby="ac-career-stories-title">
                <div class="ac-career-container">
                    <div class="ac-career-section-intro ac-career-stories-head">
                        <h2 class="values-title services-index-intro-title ac-career-section-title ac-career-stories-title" id="ac-career-stories-title" data-words-slide-from-right aria-label="{{ $isCroatian ? 'Život u ALPHA CAPITALISU' : 'Life at ALPHA CAPITALIS' }}">
                            @foreach ($headingWords($isCroatian ? 'Život u ALPHA CAPITALISU' : 'Life at ALPHA CAPITALIS') as $word)
                                <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                            @endforeach
                        </h2>

                        <div class="ac-career-stories-intro content-reveal animation-index-1" data-image-reveal>
                            <h3>{{ $isCroatian ? 'Više od radnog mjesta' : 'More than a workplace' }}</h3>
                        </div>
                    </div>

                    <div class="ac-career-story-grid">
                        @foreach ($careerStories as $story)
                            <article class="ac-career-story content-reveal animation-index-{{ $loop->index }}" data-image-reveal>
                                <span class="ac-career-story-icon" aria-hidden="true">
                                    <i class="fa-duotone fa-thin fa-fw {{ $storyIconClasses[$loop->index] ?? 'fa-star' }}"></i>
                                </span>
                                @if ($story['kicker'] !== '')
                                    <p class="ac-career-story-kicker">{{ $story['kicker'] }}</p>
                                @endif
                                <h3>{{ $story['title'] }}</h3>
                                <div class="ac-career-copy-stack ac-career-copy-stack--light">
                                    @foreach ($story['paragraphs'] as $paragraph)
                                        <p>{{ $paragraph }}</p>
                                    @endforeach
                                </div>
                                @if ($story['list'] !== [])
                                    <ul class="ac-career-story-list">
                                        @foreach ($story['list'] as $item)
                                            <li>
                                                <i class="fa-solid fa-check" aria-hidden="true"></i>
                                                <span>{{ $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section id="career-open-positions" class="ac-career-openings" aria-labelledby="ac-career-openings-title">
            <div class="ac-career-container ac-career-openings-grid">
                <div class="ac-career-openings-copy">
                    <p class="ac-family-section-kicker ac-career-kicker">{{ $isCroatian ? 'Prijave' : 'Applications' }}</p>
                    <h2 class="values-title services-index-intro-title ac-career-section-title" id="ac-career-openings-title" data-words-slide-from-right aria-label="{{ $careerApplicationTitle }}">
                        @foreach ($headingWords($careerApplicationTitle) as $word)
                            <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>

                    @if ($careerApplicationHighlight !== '')
                        <p class="ac-career-openings-lead">{{ $careerApplicationHighlight }}</p>
                    @endif

                    <div class="ac-career-copy-stack">
                        @foreach ($careerApplicationParagraphs as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                </div>

                <div id="career-cta" class="ac-career-form-wrap content-reveal animation-index-1" data-image-reveal>
                    <div class="ac-career-form-head">
                        <h3 id="ac-career-form-title">{{ trim((string) ($careerFormContent['title'] ?? '')) ?: __('career.form.title') }}</h3>
                        <p>{{ __('career.form.intro') }}</p>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('career.applications.store') }}"
                        enctype="multipart/form-data"
                        class="ac-career-form"
                        novalidate
                        data-career-form
                        data-msg-first-name-required="{{ __('career.validation.inline.first_name_required') }}"
                        data-msg-last-name-required="{{ __('career.validation.inline.last_name_required') }}"
                        data-msg-email-required="{{ __('career.validation.inline.email_required') }}"
                        data-msg-email-invalid="{{ __('career.validation.inline.email_invalid') }}"
                        data-msg-cv-required="{{ __('career.validation.inline.cv_required') }}"
                        data-msg-accept-terms="{{ __('career.validation.inline.accept_terms') }}"
                        data-file-empty-label="{{ __('career.form.cv_empty') }}"
                        data-scroll-on-load="{{ $careerFormShouldScroll ? 'true' : 'false' }}"
                        @if($careerCaptchaEnabled) data-recaptcha-form data-recaptcha-site-key="{{ $careerCaptchaSiteKey }}" data-recaptcha-action="career_application_form" @endif
                    >
                        @csrf
                        <input type="hidden" name="recaptcha_token" value="" data-recaptcha-token>

                        <div class="ac-career-form-grid">
                            <div>
                                <label class="ac-career-form-label" for="career-first-name">{{ __('career.form.first_name') }}</label>
                                <input id="career-first-name" type="text" name="first_name" value="{{ old('first_name') }}" class="ac-career-form-input" required>
                                <p class="ac-career-form-error {{ $errors->has('first_name') ? '' : 'hidden' }}" data-field-error="first_name">@error('first_name'){{ $message }}@enderror</p>
                            </div>
                            <div>
                                <label class="ac-career-form-label" for="career-last-name">{{ __('career.form.last_name') }}</label>
                                <input id="career-last-name" type="text" name="last_name" value="{{ old('last_name') }}" class="ac-career-form-input" required>
                                <p class="ac-career-form-error {{ $errors->has('last_name') ? '' : 'hidden' }}" data-field-error="last_name">@error('last_name'){{ $message }}@enderror</p>
                            </div>
                        </div>

                        <div>
                            <label class="ac-career-form-label" for="career-email">{{ __('career.form.email') }}</label>
                            <input id="career-email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" class="ac-career-form-input" required>
                            <p class="ac-career-form-error {{ $errors->has('email') ? '' : 'hidden' }}" data-field-error="email">@error('email'){{ $message }}@enderror</p>
                        </div>

                        <div>
                            <label class="ac-career-form-label" for="career-message">{{ __('career.form.message') }}</label>
                            <textarea id="career-message" name="message" rows="3" class="ac-career-form-textarea">{{ old('message') }}</textarea>
                            <p class="ac-career-form-error {{ $errors->has('message') ? '' : 'hidden' }}" data-field-error="message">@error('message'){{ $message }}@enderror</p>
                        </div>

                        <div>
                            <label class="ac-career-form-label" for="career-cv">{{ __('career.form.cv') }}</label>
                            <div class="ac-career-form-file-wrap">
                                <input id="career-cv" type="file" name="cv" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="ac-career-form-file" aria-describedby="career-cv-status career-cv-help" required>
                                <div class="ac-career-form-file-ui">
                                    <span class="ac-career-form-file-button">
                                        <i class="fa-duotone fa-thin fa-cloud-arrow-up" aria-hidden="true"></i>
                                        {{ __('career.form.cv_button') }}
                                    </span>
                                    <span id="career-cv-status" class="ac-career-form-file-name" data-file-name aria-live="polite">{{ __('career.form.cv_empty') }}</span>
                                </div>
                            </div>
                            <p id="career-cv-help" class="ac-career-form-help">{{ __('career.form.cv_help') }}</p>
                            <p class="ac-career-form-error {{ $errors->has('cv') ? '' : 'hidden' }}" data-field-error="cv">@error('cv'){{ $message }}@enderror</p>
                        </div>

                        <div class="ac-career-form-consent-wrap">
                            <label class="ac-career-form-consent">
                                <input type="checkbox" name="accept_terms" value="1" class="ac-career-form-checkbox" @checked((bool) old('accept_terms'))>
                                <span>{{ __('career.form.accept_terms') }}</span>
                            </label>
                            <p class="ac-career-form-error {{ $errors->has('accept_terms') ? '' : 'hidden' }}" data-field-error="accept_terms">@error('accept_terms'){{ $message }}@enderror</p>
                        </div>

                        <div class="ac-career-form-actions">
                            <button type="submit" class="editorial-dark-button ac-career-submit-button">
                                <span>{{ __('career.form.submit') }}</span>
                                <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                            </button>
                            <p class="ac-career-form-error {{ $errors->has('recaptcha_token') ? '' : 'hidden' }}" data-field-error="recaptcha_token">@error('recaptcha_token'){{ $message }}@enderror</p>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/career.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/career.css')) }}">
@endpush

@if ($careerCaptchaEnabled)
    @push('scripts')
        <script src="https://www.google.com/recaptcha/api.js?render={{ $careerCaptchaSiteKey }}"></script>
    @endpush
@endif

@push('scripts')
    <script defer src="{{ asset('front-theme/scripts/career.js') }}?v={{ filemtime(public_path('front-theme/scripts/career.js')) }}"></script>
@endpush
