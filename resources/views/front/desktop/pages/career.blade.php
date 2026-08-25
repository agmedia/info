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
    $careerStoriesSection = is_array($careerContent['stories_section'] ?? null) ? $careerContent['stories_section'] : [];
    $careerRequiresExactTranslation = \App\Support\Localization\FrontendLocalePolicy::requiresExactTranslation((string) $locale);
    $careerValuesSource = array_key_exists('values_text', $careerContent)
        ? (preg_split('/\R/u', (string) $careerContent['values_text']) ?: [])
        : (array) ($careerContent['values'] ?? []);
    $careerValues = collect($careerValuesSource)
        ->map(static fn ($value): string => trim((string) $value))
        ->filter()
        ->values();
    $careerIntroBody = collect((array) ($careerIntro['body'] ?? []))
        ->map(static fn ($paragraph): string => trim((string) $paragraph))
        ->filter()
        ->values();
    $careerHeroBodyHtmlIsDefined = array_key_exists('hero_body_html', $careerIntro);
    $careerHeroBodyHtml = $careerHeroBodyHtmlIsDefined
        ? trim((string) $careerIntro['hero_body_html'])
        : '';
    $careerApplicationParagraphs = collect((array) ($careerApplication['paragraphs'] ?? []))
        ->map(static fn ($paragraph): string => trim((string) $paragraph))
        ->filter()
        ->values();
    $careerApplicationBodyHtmlIsDefined = array_key_exists('body_html', $careerApplication);
    $careerApplicationBodyHtml = $careerApplicationBodyHtmlIsDefined
        ? trim((string) $careerApplication['body_html'])
        : '';
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
                'body_html_is_defined' => array_key_exists('body_html', $story),
                'body_html' => trim((string) ($story['body_html'] ?? '')),
                'paragraphs' => collect((array) ($story['paragraphs'] ?? []))
                    ->map(static fn ($paragraph): string => trim((string) $paragraph))
                    ->filter()
                    ->values()
                    ->all(),
                'list' => collect(array_key_exists('list_text', $story)
                    ? (preg_split('/\R/u', (string) $story['list_text']) ?: [])
                    : (array) ($story['list'] ?? []))
                    ->map(static fn ($item): string => trim((string) $item))
                    ->filter()
                    ->values()
                    ->all(),
            ];
        })
        ->filter(static fn (array $story): bool => $story['title'] !== '')
        ->values();

    $careerTranslationTitle = trim((string) ($translation?->title ?? ''));
    $careerPageTitle = $careerTranslationTitle;
    $careerIntroTitle = trim((string) ($careerIntro['section_title'] ?? ''));
    $careerHeroTitle = trim((string) ($careerIntro['title'] ?? ''));
    $careerHeroHighlight = trim((string) ($careerIntro['highlight'] ?? ''));
    $careerHeroKicker = trim((string) ($careerIntro['kicker'] ?? ''));
    $careerValuesLabel = trim((string) ($careerIntro['values_label'] ?? ''));
    $careerHeroButtonLabel = trim((string) ($careerIntro['button_label'] ?? ''));
    $careerHeroStatValue = trim((string) ($careerIntro['stat_value'] ?? ''));
    $careerHeroStatLabel = trim((string) ($careerIntro['stat_label'] ?? ''));
    $careerIntroLead = (string) ($careerIntroBody->first() ?? '');
    $careerHeroParagraphs = $careerHeroBodyHtmlIsDefined
        ? collect()
        : $careerIntroBody->skip(1)->values();
    $careerProcessTitle = array_key_exists('title', $careerProcess)
        ? trim((string) $careerProcess['title'])
        : trim(implode(' ', array_filter([
            trim((string) ($careerProcess['title_line_one'] ?? '')),
            trim((string) ($careerProcess['title_line_two'] ?? '')),
        ])));
    $careerApplicationTitle = trim((string) ($careerApplication['title'] ?? ''));
    $careerApplicationHighlight = trim((string) ($careerApplication['highlight'] ?? ''));
    $careerApplicationKicker = trim((string) ($careerApplication['kicker'] ?? ''));
    $careerStoriesTitle = trim((string) ($careerStoriesSection['title'] ?? ''));
    $careerStoriesIntro = trim((string) ($careerStoriesSection['intro'] ?? ''));
    $headingWords = static fn (string $title): array => preg_split('/\s+/u', trim($title), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $processIconClasses = ['fa-handshake', 'fa-hands-holding-heart', 'fa-chart-line-up', 'fa-lightbulb-on'];
    $storyIconClasses = ['fa-people-group', 'fa-compass', 'fa-seedling'];
    $careerHeroMedia = $page->getFirstMedia('career_hero_image');
    $careerHeroMediaAlt = trim((string) data_get($careerHeroMedia?->custom_properties, 'alt.'.$locale));
    if (! $careerRequiresExactTranslation && $careerHeroMediaAlt === '') {
        $careerHeroMediaAlt = trim((string) (
            data_get($careerHeroMedia?->custom_properties, 'alt.'.$fallbackLocale)
            ?: $careerHeroMedia?->name
        ));
    }
    $careerHeroContentAlt = trim((string) ($careerIntro['image_alt'] ?? ''));
    $careerHeroPhoto = [
        'src' => $careerHeroMedia?->hasGeneratedConversion('career_hero_1440x1059')
            ? $careerHeroMedia->getUrl('career_hero_1440x1059')
            : ($careerHeroMedia?->getUrl() ?: asset('front-theme/images/career/karijera.png')),
        'alt' => $careerHeroContentAlt !== ''
            ? $careerHeroContentAlt
            : $careerHeroMediaAlt,
    ];
    $careerGallery = $page->getMedia('career_gallery_images')
        ->map(static function ($media) use ($locale, $fallbackLocale): array {
            $alt = trim((string) (
                data_get($media->custom_properties, 'alt.'.$locale)
                ?: data_get($media->custom_properties, 'alt.'.$fallbackLocale)
                ?: $media->name
            ));

            return [
                'src' => $media->hasGeneratedConversion('detail_960x960')
                    ? $media->getUrl('detail_960x960')
                    : $media->getUrl(),
                'alt' => $alt,
            ];
        })
        ->filter(static fn (array $image): bool => trim((string) $image['src']) !== '')
        ->take(3)
        ->values();
    $careerFormText = static fn (string $key): string => trim((string) ($careerFormContent[$key] ?? ''))
        ?: (string) __('career.form.'.$key);
    $sectionHasContent = static function (mixed $value) use (&$sectionHasContent): bool {
        if (is_array($value)) {
            foreach ($value as $child) {
                if ($sectionHasContent($child)) {
                    return true;
                }
            }

            return false;
        }

        return is_scalar($value) && trim((string) $value) !== '';
    };
    $showCareerIntro = $sectionHasContent($careerIntro);
    $showCareerDevelopment = $sectionHasContent($careerProcess);
    $showCareerOpenings = $sectionHasContent($careerApplication) || $sectionHasContent($careerFormContent);
@endphp

@section('title', $careerPageTitle)
@section('main_class', 'w-full px-0 py-0')
@section('hide_footer_newsletter', '1')

@section('content')
    <div class="ac-career-page">
        @if ($showCareerIntro)<section class="ac-career-intro" aria-labelledby="ac-career-intro-title">
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
        </section>@endif


        @if ($showCareerIntro)<section class="ac-career-hero" aria-labelledby="ac-career-hero-title">
            <div class="ac-career-container ac-career-hero-grid">
                <div class="ac-career-hero-copy">
                    @if ($careerHeroKicker !== '')
                        <p class="ac-family-section-kicker ac-career-kicker">{{ $careerHeroKicker }}</p>
                    @endif
                    <h2 class="ac-career-dark-title" id="ac-career-hero-title" data-words-slide-from-right aria-label="{{ $careerHeroTitle }}">
                        @foreach ($headingWords($careerHeroTitle) as $word)
                            <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>

                    @if ($careerHeroBodyHtml !== '' || $careerHeroParagraphs->isNotEmpty())
                        <div class="ac-career-copy-stack ac-career-copy-stack--light content-reveal animation-index-1" data-image-reveal>
                            @if ($careerHeroBodyHtmlIsDefined)
                                {!! $careerHeroBodyHtml !!}
                            @else
                                @foreach ($careerHeroParagraphs as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            @endif
                        </div>
                    @endif

                    @if ($careerValues->isNotEmpty())
                        <ul class="ac-career-value-list content-reveal animation-index-2" data-image-reveal aria-label="{{ $careerValuesLabel }}">
                            @foreach ($careerValues as $value)
                                <li>
                                    <i class="fa-solid fa-check" aria-hidden="true"></i>
                                    <span>{{ $value }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if ($careerHeroButtonLabel !== '')
                        <div class="ac-career-hero-actions content-reveal animation-index-3" data-image-reveal>
                            <a href="#career-open-positions" class="button button-gold">
                                <span>{{ $careerHeroButtonLabel }}</span>
                            </a>
                        </div>
                    @endif
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

                    @if ($careerHeroStatValue !== '' || $careerHeroStatLabel !== '')
                        <div class="ac-career-stat-card">
                            @if ($careerHeroStatValue !== '')
                                <strong>{{ $careerHeroStatValue }}</strong>
                            @endif
                            @if ($careerHeroStatLabel !== '')
                                <span>{{ $careerHeroStatLabel }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </section>@endif


        @if ($showCareerDevelopment)<section class="ac-career-development" aria-labelledby="ac-career-development-title">
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
        </section>@endif


        @if ($careerStories->isNotEmpty())
            <section class="ac-career-stories" aria-labelledby="ac-career-stories-title">
                <div class="ac-career-container">
                    <div class="ac-career-section-intro ac-career-stories-head">
                        <h2 class="values-title services-index-intro-title ac-career-section-title ac-career-stories-title" id="ac-career-stories-title" data-words-slide-from-right aria-label="{{ $careerStoriesTitle }}">
                            @foreach ($headingWords($careerStoriesTitle) as $word)
                                <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                            @endforeach
                        </h2>

                        <div class="ac-career-stories-intro content-reveal animation-index-1" data-image-reveal>
                            <h3>{{ $careerStoriesIntro }}</h3>
                        </div>
                    </div>

                    @if ($careerGallery->isNotEmpty())
                        <div class="ac-career-gallery" aria-label="{{ $careerStoriesTitle }}">
                            @foreach ($careerGallery as $image)
                                <figure class="ac-career-gallery-item content-reveal animation-index-{{ $loop->index }}" data-image-reveal>
                                    <div class="ac-career-gallery-image image-reveal-media">
                                        <img
                                            src="{{ $image['src'] }}"
                                            alt="{{ $image['alt'] }}"
                                            width="960"
                                            height="960"
                                            loading="lazy"
                                            decoding="async"
                                        >
                                        <span class="image-reveal-curtain" aria-hidden="true"></span>
                                    </div>
                                </figure>
                            @endforeach
                        </div>
                    @endif

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
                                    @if ($story['body_html_is_defined'])
                                        {!! $story['body_html'] !!}
                                    @else
                                        @foreach ($story['paragraphs'] as $paragraph)
                                            <p>{{ $paragraph }}</p>
                                        @endforeach
                                    @endif
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

        @if ($showCareerOpenings)<section id="career-open-positions" class="ac-career-openings" aria-labelledby="ac-career-openings-title">
            <div class="ac-career-container ac-career-openings-grid">
                <div class="ac-career-openings-copy">
                    @if ($careerApplicationKicker !== '')
                        <p class="ac-family-section-kicker ac-career-kicker">{{ $careerApplicationKicker }}</p>
                    @endif
                    <h2 class="values-title services-index-intro-title ac-career-section-title" id="ac-career-openings-title" data-words-slide-from-right aria-label="{{ $careerApplicationTitle }}">
                        @foreach ($headingWords($careerApplicationTitle) as $word)
                            <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>

                    @if ($careerApplicationHighlight !== '')
                        <p class="ac-career-openings-lead">{{ $careerApplicationHighlight }}</p>
                    @endif

                    <div class="ac-career-copy-stack">
                        @if ($careerApplicationBodyHtmlIsDefined)
                            {!! $careerApplicationBodyHtml !!}
                        @else
                            @foreach ($careerApplicationParagraphs as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div id="career-cta" class="ac-career-form-wrap content-reveal animation-index-1" data-image-reveal>
                    <div class="ac-career-form-head">
                        <h3 id="ac-career-form-title">{{ $careerFormText('title') }}</h3>
                        <p>{{ $careerFormText('intro') }}</p>
                    </div>

                    <form
                        method="POST"
                        action="{{ \App\Support\Localization\FrontendRoute::url('career.applications.store') }}"
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
                        data-file-empty-label="{{ $careerFormText('cv_empty') }}"
                        data-scroll-on-load="{{ $careerFormShouldScroll ? 'true' : 'false' }}"
                        @if($careerCaptchaEnabled) data-recaptcha-form data-recaptcha-site-key="{{ $careerCaptchaSiteKey }}" data-recaptcha-action="career_application_form" @endif
                    >
                        @csrf
                        <input type="hidden" name="recaptcha_token" value="" data-recaptcha-token>

                        <div class="ac-career-form-grid">
                            <div>
                                <label class="ac-career-form-label" for="career-first-name">{{ $careerFormText('first_name') }}</label>
                                <input id="career-first-name" type="text" name="first_name" value="{{ old('first_name') }}" class="ac-career-form-input" required>
                                <p class="ac-career-form-error {{ $errors->has('first_name') ? '' : 'hidden' }}" data-field-error="first_name">@error('first_name'){{ $message }}@enderror</p>
                            </div>
                            <div>
                                <label class="ac-career-form-label" for="career-last-name">{{ $careerFormText('last_name') }}</label>
                                <input id="career-last-name" type="text" name="last_name" value="{{ old('last_name') }}" class="ac-career-form-input" required>
                                <p class="ac-career-form-error {{ $errors->has('last_name') ? '' : 'hidden' }}" data-field-error="last_name">@error('last_name'){{ $message }}@enderror</p>
                            </div>
                        </div>

                        <div>
                            <label class="ac-career-form-label" for="career-email">{{ $careerFormText('email') }}</label>
                            <input id="career-email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" class="ac-career-form-input" required>
                            <p class="ac-career-form-error {{ $errors->has('email') ? '' : 'hidden' }}" data-field-error="email">@error('email'){{ $message }}@enderror</p>
                        </div>

                        <div>
                            <label class="ac-career-form-label" for="career-message">{{ $careerFormText('message') }}</label>
                            <textarea id="career-message" name="message" rows="3" class="ac-career-form-textarea">{{ old('message') }}</textarea>
                            <p class="ac-career-form-error {{ $errors->has('message') ? '' : 'hidden' }}" data-field-error="message">@error('message'){{ $message }}@enderror</p>
                        </div>

                        <div>
                            <label class="ac-career-form-label" for="career-cv">{{ $careerFormText('cv') }}</label>
                            <div class="ac-career-form-file-wrap">
                                <input id="career-cv" type="file" name="cv" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="ac-career-form-file" aria-describedby="career-cv-status career-cv-help" required>
                                <div class="ac-career-form-file-ui">
                                    <span class="ac-career-form-file-button">
                                        <i class="fa-duotone fa-thin fa-cloud-arrow-up" aria-hidden="true"></i>
                                        {{ $careerFormText('cv_button') }}
                                    </span>
                                    <span id="career-cv-status" class="ac-career-form-file-name" data-file-name aria-live="polite">{{ $careerFormText('cv_empty') }}</span>
                                </div>
                            </div>
                            <p id="career-cv-help" class="ac-career-form-help">{{ $careerFormText('cv_help') }}</p>
                            <p class="ac-career-form-error {{ $errors->has('cv') ? '' : 'hidden' }}" data-field-error="cv">@error('cv'){{ $message }}@enderror</p>
                        </div>

                        <div class="ac-career-form-consent-wrap">
                            <label class="ac-career-form-consent">
                                <input type="checkbox" name="accept_terms" value="1" class="ac-career-form-checkbox" @checked((bool) old('accept_terms'))>
                                <span>{{ $careerFormText('accept_terms') }}</span>
                            </label>
                            <p class="ac-career-form-error {{ $errors->has('accept_terms') ? '' : 'hidden' }}" data-field-error="accept_terms">@error('accept_terms'){{ $message }}@enderror</p>
                        </div>

                        <div class="ac-career-form-actions">
                            <button type="submit" class="editorial-dark-button ac-career-submit-button">
                                <span>{{ $careerFormText('submit') }}</span>
                                <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                            </button>
                            <p class="ac-career-form-error {{ $errors->has('recaptcha_token') ? '' : 'hidden' }}" data-field-error="recaptcha_token">@error('recaptcha_token'){{ $message }}@enderror</p>
                        </div>
                    </form>
                </div>
            </div>
        </section>@endif

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
