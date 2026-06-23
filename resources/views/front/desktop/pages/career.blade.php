@extends('front.desktop.layouts.store')

@php
    $translation = $selectedTranslation
        ?? $page->translations->firstWhere('locale', $locale)
        ?? $page->translations->firstWhere('locale', $fallbackLocale);

    $pageTitleBreadcrumbs = [
        ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
        ['label' => $translation?->title ?? $page->code, 'current' => true],
    ];

    $careerCaptchaSiteKey = trim((string) ($storeSettings['captcha']['recaptcha_v3_site_key'] ?? ''));
    $careerCaptchaEnabled = (bool) ($storeSettings['captcha']['recaptcha_v3_enabled'] ?? false) && $careerCaptchaSiteKey !== '';
    $careerFormShouldScroll = $errors->any() || session()->has('status');
    $careerContent = is_array($careerContent ?? null) ? $careerContent : [];
    $careerIntro = is_array($careerContent['intro'] ?? null) ? $careerContent['intro'] : [];
    $careerProcess = is_array($careerContent['process'] ?? null) ? $careerContent['process'] : [];
    $careerApplication = is_array($careerContent['application'] ?? null) ? $careerContent['application'] : [];
    $careerFormContent = is_array($careerContent['form'] ?? null) ? $careerContent['form'] : [];
    $careerValues = array_values(array_filter(
        (array) ($careerContent['values'] ?? []),
        static fn ($value): bool => trim((string) $value) !== ''
    ));
    $careerIntroBody = array_values(array_filter(
        (array) ($careerIntro['body'] ?? []),
        static fn ($paragraph): bool => trim((string) $paragraph) !== ''
    ));
    $careerApplicationParagraphs = array_values(array_filter(
        (array) ($careerApplication['paragraphs'] ?? []),
        static fn ($paragraph): bool => trim((string) $paragraph) !== ''
    ));
    $careerProcessTitle = trim(implode(' ', array_filter([
        (string) ($careerProcess['title_line_one'] ?? ''),
        (string) ($careerProcess['title_line_two'] ?? ''),
    ], static fn ($value): bool => trim((string) $value) !== '')));
    $careerStories = collect((array) ($careerContent['stories'] ?? []))
        ->map(static function ($story): array {
            $story = is_array($story) ? $story : [];

            return [
                'kicker' => (string) ($story['kicker'] ?? ''),
                'title' => (string) ($story['title'] ?? ''),
                'paragraphs' => array_values(array_filter(
                    (array) ($story['paragraphs'] ?? []),
                    static fn ($paragraph): bool => trim((string) $paragraph) !== ''
                )),
                'list' => array_values(array_filter(
                    (array) ($story['list'] ?? []),
                    static fn ($item): bool => trim((string) $item) !== ''
                )),
            ];
        })
        ->filter(static fn (array $story): bool => trim($story['title']) !== '')
        ->values()
        ->all();
    $careerCanonicalTitle = str_starts_with(strtolower((string) $locale), 'hr') ? 'Karijera' : 'Career';
    $careerTranslationTitle = trim((string) ($translation?->title ?? ''));
    $careerPageTitle = $careerTranslationTitle !== '' && ! in_array($careerTranslationTitle, ['Ljudski potencijali', 'Human potential'], true)
        ? $careerTranslationTitle
        : $careerCanonicalTitle;
    $careerHeroTitle = trim((string) ($careerIntro['title'] ?? '')) ?: 'Mjesto gdje karijera stvarno raste';
    $careerHeroHighlight = trim((string) ($careerIntro['highlight'] ?? ''));
    $careerApplicationTitle = trim((string) ($careerApplication['title'] ?? '')) ?: 'Otvorene pozicije';
    $careerApplicationHighlight = trim((string) ($careerApplication['highlight'] ?? ''));
    $careerCollagePhotos = [
        [
            'class' => 'ac-career-photo--one',
            'src' => asset('front-theme/images/careers/alpha-career-team.jpg'),
            'alt' => 'ALPHA CAPITALIS tim u uredu',
        ],
        [
            'class' => 'ac-career-photo--two',
            'src' => asset('front-theme/images/careers/alpha-career-office-zagreb.jpg'),
            'alt' => 'ALPHA CAPITALIS tim u zagrebačkom uredu',
        ],

        [
            'class' => 'ac-career-photo--four',
            'src' => asset('front-theme/images/careers/alpha-career-workshop-circle.jpg'),
            'alt' => 'Radionica i timski razgovor u uredu',
        ],
        [
            'class' => 'ac-career-photo--five',
            'src' => asset('front-theme/images/careers/alpha-career-workshop-room.jpg'),
            'alt' => 'Timska radionica u uredskom prostoru',
        ],
    ];
    $careerCollageTopPhotos = array_slice($careerCollagePhotos, 0, 2);
    $careerCollageBottomPhotos = array_slice($careerCollagePhotos, 2);
@endphp

@section('title', $careerPageTitle)
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-career-page">
        <x-front.page-title-band
            :breadcrumbs="$pageTitleBreadcrumbs"
            section-class="ac-career-title-band"
            breadcrumb-class="ac-career-title-breadcrumb"
        >
            <div class="ac-page-title-copy">
                <h1>{{ $careerPageTitle }}</h1>
                <p>{{ $careerHeroTitle }}</p>
            </div>
        </x-front.page-title-band>

        <section class="ac-career-hero" aria-labelledby="ac-career-hero-title">
            <div class="ac-career-container">
                <div class="ac-career-hero-grid">
                    <div class="ac-career-hero-copy">
                        <p class="ac-career-kicker">Karijera u ALPHA CAPITALISU</p>
                        <h2 id="ac-career-hero-title">{{ $careerHeroTitle }}</h2>

                        @if ($careerHeroHighlight !== '')
                            <p class="ac-career-hero-highlight">{{ $careerHeroHighlight }}</p>
                        @endif

                        <div class="ac-career-copy-stack">
                            @foreach ($careerIntroBody as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>

                        @if ($careerValues !== [])
                            <ul class="ac-career-value-list" aria-label="Što nudimo">
                                @foreach ($careerValues as $value)
                                    <li>
                                        <span aria-hidden="true">✓</span>
                                        <span>{{ $value }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="ac-career-hero-actions">
                            <a href="#career-open-positions" class="front-action-cta ac-career-primary-cta">
                                <span>Otvorene pozicije</span>
                                <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M4 12L12 4"></path>
                                    <path d="M6 4h6v6"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="ac-career-collage" aria-label="Fotografije ureda i tima">
                        @foreach ($careerCollageTopPhotos as $photo)
                            <figure class="ac-career-photo {{ $photo['class'] }}">
                                <img
                                    src="{{ $photo['src'] }}"
                                    alt="{{ $photo['alt'] }}"
                                    width="760"
                                    height="520"
                                    loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                                    decoding="async"
                                    @if ($loop->first) fetchpriority="high" @endif
                                >
                            </figure>
                        @endforeach

                        <div class="ac-career-video-teaser">
                            <span class="ac-career-video-play" aria-hidden="true">▶</span>
                            <div>
                                <strong>VIDEO</strong>
                                <small>Placeholder za video</small>
                            </div>
                        </div>
                        <div class="ac-career-stat-card">
                            <strong>70+</strong>
                            <span>stručnjaka iz financija, revizije, računovodstva i savjetovanja</span>
                        </div>

                        @foreach ($careerCollageBottomPhotos as $photo)
                            <figure class="ac-career-photo {{ $photo['class'] }}">
                                <img
                                    src="{{ $photo['src'] }}"
                                    alt="{{ $photo['alt'] }}"
                                    width="760"
                                    height="520"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </figure>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="ac-career-impact" aria-labelledby="ac-career-impact-title">
            <div class="ac-career-container">
                <div class="ac-career-impact-head">
                    @if (trim((string) ($careerProcess['kicker'] ?? '')) !== '')
                        <p class="ac-career-kicker">{{ $careerProcess['kicker'] }}</p>
                    @endif

                    @if ($careerProcessTitle !== '')
                        <h2 id="ac-career-impact-title">{{ $careerProcessTitle }}</h2>
                    @endif

                    @if (trim((string) ($careerProcess['intro'] ?? '')) !== '')
                        <p class="ac-career-impact-intro">{{ $careerProcess['intro'] }}</p>
                    @endif
                </div>

                <div class="ac-career-video-placeholder" aria-label="Video testimonial">
                    <span class="ac-career-video-play" aria-hidden="true">▶</span>
                </div>
            </div>
        </section>

        @if ($careerStories !== [])
            <section class="ac-career-stories" aria-label="Život u ALPHA CAPITALISU">
                <div class="ac-career-container">
                    <div class="ac-career-story-grid">
                        @foreach ($careerStories as $story)
                            <article class="ac-career-story-card">
                                @if (trim($story['kicker']) !== '')
                                    <p class="ac-career-kicker">{{ $story['kicker'] }}</p>
                                @endif

                                <h3>{{ $story['title'] }}</h3>

                                <div class="ac-career-copy-stack">
                                    @foreach ($story['paragraphs'] as $paragraph)
                                        <p>{{ $paragraph }}</p>
                                    @endforeach
                                </div>

                                @if ($story['list'] !== [])
                                    <ul class="ac-career-story-list">
                                        @foreach ($story['list'] as $item)

                                            <li> <span aria-hidden="true">✓</span> {{ $item }}</li>
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
            <div class="ac-career-container">
                <div class="ac-career-openings-grid">
                    <div class="ac-career-openings-copy">
                        <p class="ac-career-kicker">Prijave</p>
                        <h2 id="ac-career-openings-title">{{ $careerApplicationTitle }}</h2>

                        @if ($careerApplicationHighlight !== '')
                            <p class="ac-career-openings-lead">{{ $careerApplicationHighlight }}</p>
                        @endif

                        <div class="ac-career-copy-stack">
                            @foreach ($careerApplicationParagraphs as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </div>

                    <div id="career-cta" class="ac-career-form-wrap">
                        <div class="ac-career-form-card">
                            <div class="ac-career-form-head">
                                <p class="ac-career-form-kicker">{{ __('career.form.eyebrow') }}</p>
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
                                    <textarea id="career-message" name="message" rows="2" class="ac-career-form-textarea">{{ old('message') }}</textarea>
                                    <p class="ac-career-form-error {{ $errors->has('message') ? '' : 'hidden' }}" data-field-error="message">@error('message'){{ $message }}@enderror</p>
                                </div>

                                <div>
                                    <label class="ac-career-form-label" for="career-cv">{{ __('career.form.cv') }}</label>
                                    <div class="ac-career-form-file-wrap">
                                        <input id="career-cv" type="file" name="cv" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="ac-career-form-file" aria-describedby="career-cv-status career-cv-help" required>
                                        <div class="ac-career-form-file-ui">
                                            <span class="ac-career-form-file-button">{{ __('career.form.cv_button') }}</span>
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
                                    <button type="submit" class="ac-career-submit-button">
                                        {{ __('career.form.submit') }}
                                    </button>
                                    <p class="ac-career-form-error {{ $errors->has('recaptcha_token') ? '' : 'hidden' }}" data-field-error="recaptcha_token">@error('recaptcha_token'){{ $message }}@enderror</p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        html {
            scroll-behavior: smooth;
        }

        .ac-career-page {
            --ac-career-bg-warm: #f6f1e7;
            --ac-career-bg-light: #fbf6ed;
            --ac-career-section-line: rgba(15, 42, 67, 0.08);
            --ac-career-section-title-size: 2.18rem;
            --ac-career-section-title-line-height: 1.14;
            --ac-career-card-title-size: 1.48rem;
            --ac-career-card-title-line-height: 1.24;
            --ac-career-lead-size: 1.08rem;
            --ac-career-lead-line-height: 1.66;
            min-height: 100vh;
            padding: 0;
            background: var(--ac-career-bg-warm);
            color: #101820;
        }

        .ac-career-page p {
            margin: 0;
        }

        .ac-career-container {
            width: min(100% - 2rem, 1320px);
            margin: 0 auto;
        }

        @media (min-width: 640px) {
            .ac-career-container {
                width: min(100% - 3rem, 1272px);
            }
        }

        @media (min-width: 1024px) {
            .ac-career-container {
                width: min(100% - 4rem, 1256px);
            }
        }

        .ac-career-title-band {
            margin-bottom: 0;
            background: var(--ac-career-bg-warm);
            border-top-color: transparent;
            border-bottom-color: rgba(15, 42, 67, 0.08);
        }

        .ac-career-title-band .ac-page-title-copy h1 {
            color: #101820;
            font-size: 2.65rem;
            font-weight: 700;
            line-height: 1.1;
        }

        .ac-career-title-band .ac-page-title-copy > p,
        .ac-career-title-band .front-scroll-breadcrumb-link,
        .ac-career-title-band .front-scroll-breadcrumb-current,
        .ac-career-title-band .front-scroll-breadcrumb-separator {
            color: #4f4a43;
        }

        .ac-career-title-band .ac-page-title-breadcrumb::before,
        .ac-career-title-band .ac-page-title-breadcrumb::after {
            background: rgba(120, 96, 58, 0.16);
        }

        .ac-career-page .front-action-cta {
            letter-spacing: 0;
        }

        .ac-career-hero {
            position: relative;
            overflow: hidden;
            padding: clamp(3.8rem, 6.5vw, 7rem) 0 clamp(4.8rem, 8vw, 8rem);
            border-bottom: 1px solid var(--ac-career-section-line);
            background: var(--ac-career-bg-warm);
        }

        .ac-career-hero-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: minmax(0, 0.83fr) minmax(34rem, 1fr);
            gap: clamp(2rem, 5vw, 5rem);
            align-items: center;
        }

        .ac-career-hero-copy {
            max-width: 35rem;
        }

        .ac-career-kicker {
            margin: 0;
            color: #7c653b;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .ac-career-hero-copy h2,
        .ac-career-impact h2,
        .ac-career-openings-copy h2 {
            margin: 0;
            color: #101820;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            letter-spacing: 0;
            text-wrap: balance;
        }

        .ac-career-hero-copy h2 {
            margin-top: 0.95rem;
            max-width: 16ch;
            font-size: var(--ac-career-section-title-size);
            line-height: var(--ac-career-section-title-line-height);
        }

        .ac-career-hero-highlight {
            margin-top: clamp(1.35rem, 2vw, 1.9rem) !important;
            color: #24211c;
            font-size: var(--ac-career-lead-size);
            font-weight: 700;
            line-height: var(--ac-career-lead-line-height);
        }

        .ac-career-copy-stack {
            display: grid;
            gap: 0.85rem;
            margin-top: 1rem;
        }

        .ac-career-copy-stack p,
        .ac-career-impact-intro,
        .ac-career-openings-copy p,
        .ac-career-form-head p:last-child {
            color: #403a34;
            font-size: 1rem;
            line-height: 1.76;
            text-wrap: pretty;
        }

        .ac-career-value-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.65rem 0.8rem;
            margin: clamp(1.35rem, 2vw, 1.8rem) 0 0;
            padding: 0;
            list-style: none;
        }

        .ac-career-value-list li {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            min-height: 2.6rem;
            padding: 0.55rem 0.68rem;
            border: 1px solid rgba(171, 141, 82, 0.12);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.82);
            color: #403a34;
            font-size: 0.94rem;
            font-weight: 700;
        }

        .ac-career-value-list li span:first-child {
            color: #9a773d;
        }

        .ac-career-hero-actions {
            margin-top: clamp(1.5rem, 2.6vw, 2.15rem);
        }

        .ac-career-primary-cta {
            min-height: 2.9rem;
            min-width: 11.6rem;
            padding: 0.8rem 1.35rem;
            background: #10213a;
            color: #fff !important;
            font-size: 0.82rem;
        }

        .ac-career-primary-cta:hover {
            background: #173b5d;
            color: #fff !important;
        }

        .ac-career-collage {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            align-items: stretch;
            gap: clamp(0.75rem, 1.5vw, 1rem);
            min-width: 0;
        }

        .ac-career-photo,
        .ac-career-video-teaser,
        .ac-career-stat-card,
        .ac-career-video-placeholder,
        .ac-career-story-card,
        .ac-career-form-card {
            border: 1px solid rgba(171, 141, 82, 0.12);
            border-radius: 8px;
            box-shadow: none;
        }

        .ac-career-photo {
            position: relative;
            overflow: hidden;
            width: 100%;
            aspect-ratio: 4 / 3;
            margin: 0;
            background: #fff;
            box-shadow: 0 22px 46px rgba(15, 42, 67, 0.12);
        }

        .ac-career-photo::before {
            content: none;
        }

        .ac-career-photo img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ac-career-video-teaser {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            min-height: 100%;
            padding: clamp(1rem, 1.7vw, 1.2rem);
            background: rgba(255, 255, 255, 0.94);
            color: #101820;
            box-shadow: 0 18px 38px rgba(15, 42, 67, 0.12);
        }

        .ac-career-video-play {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.55rem;
            height: 2.55rem;
            border-radius: 999px;
            background: #9a773d;
            color: #fff;
            font-size: 0.8rem;
            flex: 0 0 auto;
        }

        .ac-career-video-teaser strong,
        .ac-career-video-placeholder strong {
            display: block;
            color: #101820;
            font-size: 1rem;
            line-height: 1.2;
        }

        .ac-career-video-teaser small,
        .ac-career-video-placeholder small {
            display: block;
            margin-top: 0.15rem;
            color: #6f6255;
            font-size: 0.86rem;
            line-height: 1.35;
        }

        .ac-career-stat-card {
            display: grid;
            align-content: center;
            gap: 0.25rem;
            min-height: 100%;
            padding: clamp(1rem, 1.7vw, 1.2rem);
            background: rgba(255, 255, 255, 0.94);
            color: #101820;
            box-shadow: 0 18px 38px rgba(15, 42, 67, 0.1);
        }

        .ac-career-stat-card strong {
            color: #9a773d;
            font-size: 3rem;
            line-height: 0.95;
        }

        .ac-career-stat-card span {
            color: #403a34;
            font-size: 0.88rem;
            line-height: 1.45;
        }

        .ac-career-impact,
        .ac-career-stories,
        .ac-career-openings {
            position: relative;
            overflow: hidden;
            padding: clamp(3rem, 6vw, 5.8rem) 0 clamp(5.2rem, 8vw, 5.4rem);
            border-bottom: 1px solid var(--ac-career-section-line);
        }

        .ac-career-impact {
            background: var(--ac-career-bg-light);
        }

        .ac-career-stories {
            background: var(--ac-career-bg-warm);
        }

        .ac-career-hero > .ac-career-container,
        .ac-career-impact > .ac-career-container,
        .ac-career-stories > .ac-career-container,
        .ac-career-openings > .ac-career-container {
            position: relative;
            z-index: 1;
        }

        .ac-career-openings-grid {
            display: grid;
            grid-template-columns: minmax(0, 0.82fr) minmax(0, 1fr);
            gap: clamp(1.6rem, 4vw, 4rem);
            align-items: start;
        }

        .ac-career-impact-head {
            display: grid;
            justify-items: center;
            max-width: 66rem;
            margin: 0 auto;
            text-align: center;
        }

        .ac-career-impact-head h2,
        .ac-career-openings-copy h2 {
            margin-top: 0.75rem;
            max-width: 16ch;
            font-size: var(--ac-career-section-title-size);
            line-height: var(--ac-career-section-title-line-height);
        }

        .ac-career-story-card,
        .ac-career-form-card {
            background: rgba(255, 255, 255, 0.82);
        }

        .ac-career-impact-intro {
            max-width: 64rem;
            margin: 1.35rem auto 0 !important;
            font-size: 1.02rem;
            line-height: 1.72;
        }

        .ac-career-video-placeholder {
            position: relative;
            display: grid;
            place-items: center;
            align-content: center;
            gap: 0.55rem;
            width: min(100%, 52rem);
            aspect-ratio: 16 / 7;
            min-height: clamp(16rem, 24vw, 22rem);
            margin: clamp(2rem, 4vw, 3rem) auto 0;
            padding: 2rem;
            overflow: hidden;
            text-align: center;
            background:
                linear-gradient(180deg, rgba(7, 19, 38, 0.58) 0%, rgba(7, 19, 38, 0.78) 100%),
                url('{{ asset('front-theme/images/careers/collage-meeting.jpg') }}') center / cover no-repeat;
            color: #ffffff;
        }

        .ac-career-video-placeholder .ac-career-video-play {
            width: 5.35rem;
            height: 5.35rem;
            margin: 0;
            font-size: 1.22rem;
            box-shadow: 0 16px 32px rgba(16, 24, 32, 0.32);
        }

        .ac-career-story-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            align-items: stretch;
            gap: 1rem;
        }

        .ac-career-story-card {
            position: relative;
            display: grid;
            align-content: start;
            height: 100%;
            min-height: 27rem;
            overflow: hidden;
            padding: clamp(1.2rem, 2.2vw, 1.7rem);
            box-shadow: none;
        }

        .ac-career-story-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto;
            height: 4px;
            background: #9a773d;
            pointer-events: none;
        }

        .ac-career-story-card:nth-child(2) {
            margin-top: 0;
        }

        .ac-career-story-card h3 {
            margin: 0;
            padding-bottom: 0.95rem;
            border-bottom: 1px solid rgba(15, 42, 67, 0.08);
            color: #101820;
            font-family: 'Montserrat', sans-serif;
            font-size: var(--ac-career-card-title-size);
            font-weight: 700;
            line-height: var(--ac-career-card-title-line-height);
            letter-spacing: 0;
        }

        .ac-career-story-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.55rem;
            margin: 1.2rem 0 0;
            padding: 0;
            list-style: none;
        }

        .ac-career-story-list li {
            padding: 0.5rem 0.65rem;
            border-radius: 8px;
            background: #f2eadc;
            color: #403a34;
            font-size: 0.9rem;
            font-weight: 700;
        }

        .ac-career-openings {
            background: var(--ac-career-bg-light);
        }

        .front-desktop-shell:has(.ac-career-page) .front-footer {
            --front-footer-bg: #071326;
            background: #071326;
        }

        .ac-career-openings-lead {
            margin-top: 1rem !important;
            color: #24211c !important;
            font-size: var(--ac-career-lead-size) !important;
            font-weight: 700;
            line-height: var(--ac-career-lead-line-height) !important;
        }

        .ac-career-form-card {
            width: 100%;
            padding: clamp(1.25rem, 2.4vw, 1.9rem);
            box-shadow: none;
        }

        .ac-career-form-head {
            display: grid;
            gap: 0.55rem;
            margin-bottom: 1.8rem;
        }

        .ac-career-form-kicker {
            color: #7c653b;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .ac-career-form-head h3 {
            margin: 0;
            color: #101820;
            font-family: 'Montserrat', sans-serif;
            font-size: var(--ac-career-card-title-size);
            font-weight: 700;
            line-height: var(--ac-career-card-title-line-height);
        }

        .ac-career-form-head p:last-child {
            max-width: 31rem;
        }

        .ac-career-form {
            display: grid;
            gap: 1.3rem;
        }

        .ac-career-form-grid {
            display: grid;
            gap: 1.1rem;
        }

        .ac-career-form-label {
            display: block;
            margin-bottom: 0.35rem;
            color: #6a7c92;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .ac-career-form-input,
        .ac-career-form-textarea {
            width: 100%;
            border-radius: 0;
            background: transparent;
            color: #0f172a;
            transition: border-color 0.18s ease, background-color 0.18s ease;
        }

        .ac-career-form-input {
            min-height: 3.15rem;
            padding: 0.8rem 0 0.9rem;
            border: 0;
            border-bottom: 1px solid rgba(15, 23, 42, 0.16);
        }

        .ac-career-form-textarea {
            min-height: 3.2rem;
            padding: 0.85rem 0 0.9rem;
            border: 0;
            border-bottom: 1px solid rgba(15, 23, 42, 0.16);
            resize: vertical;
        }

        .ac-career-form-file-wrap {
            position: relative;
            border-bottom: 1px solid rgba(15, 23, 42, 0.16);
            transition: border-color 0.18s ease, background-color 0.18s ease;
        }

        .ac-career-form-file-wrap:focus-within {
            border-color: #173b5d;
        }

        .ac-career-form-file-ui {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            min-height: 3.15rem;
            padding: 0.82rem 0;
            overflow: hidden;
        }

        .ac-career-form-file-button {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.15rem;
            padding: 0.5rem 0.9rem;
            border: 1px solid rgba(15, 23, 42, 0.18);
            border-radius: var(--front-button-radius);
            background: rgba(255, 255, 255, 0.76);
            color: #10213a;
            font-size: 0.88rem;
            line-height: 1;
            transition: border-color 0.18s ease, background-color 0.18s ease, color 0.18s ease;
        }

        .ac-career-form-file-wrap:hover .ac-career-form-file-button,
        .ac-career-form-file-wrap:focus-within .ac-career-form-file-button {
            border-color: #173b5d;
            background: rgba(236, 243, 251, 0.94);
            color: #0f2a43;
        }

        .ac-career-form-file-name {
            min-width: 0;
            color: #475569;
            font-size: 0.94rem;
            line-height: 1.55;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ac-career-form-file {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            margin: 0;
            border: 0;
            opacity: 0;
            cursor: pointer;
        }

        .ac-career-form-input:focus,
        .ac-career-form-textarea:focus {
            outline: none;
            border-color: #173b5d;
            box-shadow: none;
            background: transparent;
        }

        .ac-career-form-help {
            margin-top: 0.55rem;
            color: #64748b;
            font-size: 0.78rem;
            line-height: 1.5;
        }

        .ac-career-form-error {
            margin-top: 0.45rem;
            color: #b42318;
            font-size: 0.78rem;
            font-weight: 700;
            line-height: 1.45;
        }

        .ac-career-form-consent-wrap {
            display: grid;
            gap: 0.4rem;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(15, 23, 42, 0.08);
        }

        .ac-career-form-consent {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            color: #475569;
            font-size: 0.88rem;
            line-height: 1.6;
        }

        .ac-career-form-checkbox {
            width: 1rem;
            height: 1rem;
            margin-top: 0.18rem;
            accent-color: #10213a;
        }

        .ac-career-form-actions {
            display: grid;
            gap: 0.55rem;
            padding-top: 0.4rem;
        }

        .ac-career-submit-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 3.2rem;
            width: 100%;
            padding: 0.95rem 1.35rem;
            border: 1px solid #0f2a43;
            border-radius: var(--front-button-radius);
            background: #0f2a43;
            color: #fff;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0;
            text-transform: uppercase;
            transition: background-color 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
        }

        .ac-career-submit-button:hover {
            background: #173b5d;
            border-color: #173b5d;
            transform: translateY(-1px);
        }

        @media (min-width: 760px) {
            .ac-career-form-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 1120px) {
            .ac-career-hero-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .ac-career-hero-copy {
                max-width: 48rem;
            }

            .ac-career-hero-copy h2 {
                max-width: 13ch;
            }

            .ac-career-collage {
                width: min(100%, 48rem);
                margin: 0 auto;
            }

            .ac-career-story-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .ac-career-story-card:nth-child(2) {
                margin-top: 0;
            }
        }

        @media (max-width: 900px) {
            .ac-career-openings-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        @media (max-width: 720px) {
            .ac-career-page {
                --ac-career-section-title-size: 1.88rem;
                --ac-career-card-title-size: 1.32rem;
                --ac-career-lead-size: 1rem;
            }

            .ac-career-container {
                width: min(100% - 1.35rem, 1320px);
            }

            .ac-career-title-band .ac-page-title-copy h1 {
                font-size: 2.1rem;
            }

            .ac-career-hero {
                padding-top: 2.4rem;
            }

            .ac-career-value-list,
            .ac-career-story-grid,
            .ac-career-story-list {
                grid-template-columns: minmax(0, 1fr);
            }

            .ac-career-collage {
                gap: 0.75rem;
            }

            .ac-career-form-file-ui {
                flex-wrap: wrap;
                align-items: flex-start;
                gap: 0.65rem;
            }

            .ac-career-form-file-name {
                width: 100%;
                white-space: normal;
            }

        }

        @media (max-width: 520px) {
            .ac-career-collage {
                grid-template-columns: minmax(0, 1fr);
            }

            .ac-career-story-card,
            .ac-career-form-card {
                padding: 1rem;
            }

            .ac-career-video-placeholder {
                min-height: 14rem;
            }
        }
    </style>
@endpush

@if ($careerCaptchaEnabled)
    @push('scripts')
        <script src="https://www.google.com/recaptcha/api.js?render={{ $careerCaptchaSiteKey }}"></script>
    @endpush
@endif

@push('scripts')
    <script>
        (function () {
            const form = document.querySelector('[data-career-form]');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!form) {
                return;
            }

            const cvInput = form.querySelector('[name="cv"]');
            const fileNameNode = form.querySelector('[data-file-name]');
            const defaultFileLabel = form.dataset.fileEmptyLabel || '';

            const updateSelectedFileName = function () {
                if (!fileNameNode) {
                    return;
                }

                const file = cvInput && cvInput.files && cvInput.files[0] ? cvInput.files[0] : null;
                fileNameNode.textContent = file ? file.name : defaultFileLabel;
            };

            const clearError = function (field) {
                const errorNode = form.querySelector('[data-field-error="' + field + '"]');
                if (!errorNode) {
                    return;
                }

                errorNode.textContent = '';
                errorNode.classList.add('hidden');
            };

            const setError = function (field, message) {
                const errorNode = form.querySelector('[data-field-error="' + field + '"]');
                if (!errorNode) {
                    return;
                }

                errorNode.textContent = message;
                errorNode.classList.remove('hidden');
            };

            const getHeaderOffset = function () {
                const stickyHeader = document.querySelector('[data-front-sticky-header]');
                if (!(stickyHeader instanceof HTMLElement)) {
                    return 18;
                }

                return Math.round(stickyHeader.getBoundingClientRect().height) + 18;
            };

            const scrollToForm = function () {
                const target = document.getElementById('career-cta') || form;
                if (!(target instanceof HTMLElement)) {
                    return;
                }

                const targetTop = window.pageYOffset + target.getBoundingClientRect().top - getHeaderOffset();

                if (typeof window.__frontAnimateScrollTo === 'function') {
                    window.__frontAnimateScrollTo(targetTop);
                    return;
                }

                window.scrollTo(0, Math.max(0, targetTop));
            };

            form.querySelectorAll('[data-field-error]').forEach(function (node) {
                if ((node.textContent || '').trim() === '') {
                    node.classList.add('hidden');
                } else {
                    node.classList.remove('hidden');
                }
            });

            updateSelectedFileName();

            if (form.dataset.scrollOnLoad === 'true') {
                window.requestAnimationFrame(scrollToForm);
            }

            const validate = function () {
                ['first_name', 'last_name', 'email', 'cv', 'accept_terms', 'recaptcha_token'].forEach(clearError);

                const firstName = form.querySelector('[name="first_name"]');
                const lastName = form.querySelector('[name="last_name"]');
                const email = form.querySelector('[name="email"]');
                const cv = form.querySelector('[name="cv"]');
                const acceptTerms = form.querySelector('[name="accept_terms"]');
                let valid = true;

                if (!firstName || firstName.value.trim() === '') {
                    setError('first_name', form.dataset.msgFirstNameRequired || '');
                    valid = false;
                }

                if (!lastName || lastName.value.trim() === '') {
                    setError('last_name', form.dataset.msgLastNameRequired || '');
                    valid = false;
                }

                const emailValue = email ? email.value.trim() : '';
                if (emailValue === '') {
                    setError('email', form.dataset.msgEmailRequired || '');
                    valid = false;
                } else if (!emailRegex.test(emailValue)) {
                    setError('email', form.dataset.msgEmailInvalid || '');
                    valid = false;
                }

                if (!cv || !cv.files || cv.files.length === 0) {
                    setError('cv', form.dataset.msgCvRequired || '');
                    valid = false;
                }

                if (!acceptTerms || !acceptTerms.checked) {
                    setError('accept_terms', form.dataset.msgAcceptTerms || '');
                    valid = false;
                }

                return valid;
            };

            if (cvInput) {
                cvInput.addEventListener('change', function () {
                    updateSelectedFileName();

                    if (cvInput.files && cvInput.files.length > 0) {
                        clearError('cv');
                    }
                });
            }

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                if (!validate()) {
                    scrollToForm();
                    return;
                }

                const tokenInput = form.querySelector('[data-recaptcha-token]');
                const siteKey = form.dataset.recaptchaSiteKey;
                const action = form.dataset.recaptchaAction || 'career_application_form';

                if (!tokenInput || !window.grecaptcha || !siteKey) {
                    form.submit();
                    return;
                }

                grecaptcha.ready(function () {
                    grecaptcha.execute(siteKey, { action: action }).then(function (token) {
                        tokenInput.value = token || '';
                        form.submit();
                    });
                });
            });
        }());
    </script>
@endpush
