@extends('front.desktop.layouts.store')

@php
    $translation = $selectedTranslation
        ?? $page->translations->firstWhere('locale', $locale)
        ?? $page->translations->firstWhere('locale', $fallbackLocale);

    $content = is_array($aboutContent ?? null) ? $aboutContent : [];
    $hero = is_array($content['hero'] ?? null) ? $content['hero'] : [];
    $story = is_array($content['story'] ?? null) ? $content['story'] : [];
    $values = is_array($content['values'] ?? null) ? $content['values'] : [];
    $why = is_array($content['why'] ?? null) ? $content['why'] : [];
    $team = is_array($content['team'] ?? null) ? $content['team'] : [];
    $culture = is_array($content['culture'] ?? null) ? $content['culture'] : [];
    $responsibility = is_array($content['responsibility'] ?? null) ? $content['responsibility'] : [];
    $references = is_array($content['references'] ?? null) ? $content['references'] : [];
    $aboutTeamMembers = collect($aboutTeamMembers ?? [])->values();
    $aboutPreviewTeamMembers = $aboutTeamMembers->take(3)->values();
    $aboutReferenceItems = collect($aboutReferenceItems ?? [])->values();
    $aboutHeroMedia = $page->getFirstMedia('about_hero_image');
    $aboutHeroMediaAlt = trim((string) (
        data_get($aboutHeroMedia?->custom_properties, 'alt.'.$locale)
        ?: data_get($aboutHeroMedia?->custom_properties, 'alt.'.$fallbackLocale)
        ?: $aboutHeroMedia?->name
    ));
    $aboutHeroPhoto = [
        'class' => 'ac-about-image--hero',
        'src' => $aboutHeroMedia?->hasGeneratedConversion('about_hero_1440x1059')
            ? $aboutHeroMedia->getUrl('about_hero_1440x1059')
            : ($aboutHeroMedia?->getUrl() ?: asset('front-theme/images/about/o-nama.jpg')),
        'alt' => $aboutHeroMediaAlt !== ''
            ? $aboutHeroMediaAlt
            : (str_starts_with(strtolower((string) $locale), 'hr')
                ? 'ALPHA CAPITALIS tim'
                : 'ALPHA CAPITALIS team'),
    ];
    $referencePageUrl = route('pages.show', ['slug' => 'reference']);
    $teamButtonLabel = str_starts_with(strtolower((string) $locale), 'hr') ? 'Upoznaj cijeli tim' : 'Meet the full team';
    $referencesButtonLabel = str_starts_with(strtolower((string) $locale), 'hr') ? 'Sve reference' : 'All references';
    $heroStatLabel = str_starts_with(strtolower((string) $locale), 'hr')
        ? 'klijenata kojima svakodnevno pružamo podršku'
        : 'clients supported by our team';
    $whyQuote = trim((string) ($why['quote'] ?? ''));
    $cultureQuote = trim((string) ($culture['quote'] ?? ''));
    $responsibilityQuote = trim((string) ($responsibility['quote'] ?? ''));
    $responsibilityCtaIntro = trim((string) ($responsibility['cta_intro'] ?? ''));
    $responsibilityCtaText = trim((string) ($responsibility['cta_text'] ?? ''));
    $responsibilityCtaLabel = trim((string) ($responsibility['cta_button_label'] ?? '')) ?: (
        str_starts_with(strtolower((string) $locale), 'hr') ? 'Kontaktirajte nas' : 'Contact us'
    );
    $responsibilityCtaCardTitle = str_starts_with(strtolower((string) $locale), 'hr')
        ? 'Zajedno možemo više.'
        : 'Together, we can do more.';
    $responsibilityCtaStatus = str_starts_with(strtolower((string) $locale), 'hr')
        ? 'Otvoreni smo za razgovor i nova partnerstva.'
        : 'We are open to conversations and new partnerships.';

    $pageTitle = trim((string) ($translation?->title ?? '')) ?: 'O nama';
    $heroTitle = trim((string) ($hero['title'] ?? '')) ?: $pageTitle;
    $heroLead = trim((string) ($hero['lead'] ?? ''));
    $storyParagraphs = collect((array) ($story['paragraphs'] ?? []))
        ->map(static fn ($paragraph): string => trim((string) $paragraph))
        ->filter()
        ->values();
    $introStoryHtml = e((string) ($storyParagraphs->first() ?? ''));
    $introStoryHtml = str_replace(
        'ALPHA CAPITALIS',
        '<a class="services-index-inline-link" href="'.e(route('contact.create')).'">ALPHA CAPITALIS</a>',
        $introStoryHtml,
    );
    $headingWords = static fn (string $title): array => preg_split('/\s+/u', trim($title), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $valuesLabel = str_starts_with(strtolower((string) $locale), 'hr')
        ? 'Naše vrijednosti'
        : 'Our values';
    $valuesTitle = trim((string) ($values['title'] ?? '')) ?: 'Jednostavni principi koji vode svaki dan';
    $valuesIntro = trim((string) ($values['intro'] ?? ''));
    $valuesIntroLinkText = str_contains($valuesIntro, 'ALPHA CAPITALISU')
        ? 'ALPHA CAPITALISU'
        : 'ALPHA CAPITALIS';
    $valuesIntroHtml = str_replace(
        $valuesIntroLinkText,
        '<a class="services-index-inline-link" href="'.e(route('contact.create')).'">'.e($valuesIntroLinkText).'</a>',
        e($valuesIntro),
    );
    $whyLabel = trim((string) ($why['kicker'] ?? '')) ?: 'Zašto postojimo';
    $whyTitle = trim((string) ($why['title'] ?? '')) ?: 'Podrška za sigurno, kvalitetno i održivo poslovanje';
    $whyParagraphs = collect((array) ($why['paragraphs'] ?? []))
        ->map(static fn ($paragraph): string => trim((string) $paragraph))
        ->filter()
        ->values();
    $whyServiceTermLinks = [
        'strateškog razvoja' => route('advisory.show'),
        'računovodstva' => route('accounting.show'),
        'EU fondova' => route('eu-funds.show'),
        'financija' => route('advisory.finance.show'),
        'revizije' => route('audit.show'),
        'strategic development' => route('advisory.show'),
        'accounting' => route('accounting.show'),
        'EU funds' => route('eu-funds.show'),
        'finance' => route('advisory.finance.show'),
        'audit' => route('audit.show'),
    ];
    $linkWhyServiceTerms = static function (string $paragraph) use ($whyServiceTermLinks): string {
        $replacements = [];

        foreach ($whyServiceTermLinks as $term => $url) {
            $replacements[e($term)] = '<a class="ac-about-dark-inline-link" href="'.e($url).'">'.e($term).'</a>';
        }

        return strtr(e($paragraph), $replacements);
    };
    $teamTitle = trim((string) ($team['title'] ?? '')) ?: 'Tim';
    $teamLabel = str_starts_with(strtolower((string) $locale), 'hr') ? 'Naš tim' : 'Our team';
    $teamStats = collect((array) ($team['stats'] ?? []))
        ->map(static fn ($stat): array => is_array($stat) ? $stat : [])
        ->filter(static fn (array $stat): bool => trim((string) ($stat['value'] ?? '')) !== '')
        ->values();
    $cultureLabel = trim((string) ($culture['kicker'] ?? '')) ?: (
        str_starts_with(strtolower((string) $locale), 'hr') ? 'Naša kultura' : 'Our culture'
    );
    $cultureTitle = trim((string) ($culture['title'] ?? '')) ?: 'Kvalitetno poslovanje počinje kvalitetnim odnosima';
    $cultureParagraphs = collect((array) ($culture['paragraphs'] ?? []))
        ->map(static fn ($paragraph): string => trim((string) $paragraph))
        ->filter()
        ->values();
    $cultureColumnSplit = $cultureParagraphs->count() >= 4 ? 2 : 1;
    $responsibilityLabel = trim((string) ($responsibility['kicker'] ?? '')) ?: (
        str_starts_with(strtolower((string) $locale), 'hr') ? 'Društveno odgovorno poslovanje' : 'Social responsibility'
    );
    $responsibilityTitle = trim((string) ($responsibility['title'] ?? ''));
    $responsibilityParagraphs = collect((array) ($responsibility['paragraphs'] ?? []))
        ->map(static fn ($paragraph): string => trim((string) $paragraph))
        ->filter()
        ->values();
    $referencesLabel = str_starts_with(strtolower((string) $locale), 'hr')
        ? 'Naše reference'
        : 'Our references';
    $referencesTitle = trim((string) ($references['title'] ?? '')) ?: 'Reference';
    $referenceParagraphs = collect((array) ($references['paragraphs'] ?? []))
        ->map(static fn ($paragraph): string => trim((string) $paragraph))
        ->filter()
        ->values();
    $valueIconClasses = [
        'fa-brain-circuit',
        'fa-lightbulb-gear',
        'fa-hands-holding-heart',
    ];
    $teamStatIconClasses = [
        'fa-people-group',
        'fa-users-crown',
        'fa-handshake',
        'fa-buildings',
    ];
@endphp

@section('title', $pageTitle)
@section('main_class', 'w-full px-0 py-0')
@section('hide_footer_newsletter', '1')

@section('content')
    <div class="ac-about-page">
        @if ($topBlocks->isNotEmpty())
            <section class="ac-about-blocks ac-about-blocks--top">@include('components.content-placement', ['items' => $topBlocks])</section>
        @endif

        <section class="values-section services-index-intro ac-about-intro" aria-labelledby="ac-about-hero-title">
            <div class="values-inner services-index-intro-layout ac-about-intro-layout">
                <div class="values-intro">
                    <h1 class="values-title services-index-intro-title ac-about-intro-title" id="ac-about-hero-title" data-words-slide-from-right aria-label="{{ $heroTitle }}">
                        @foreach ($headingWords($heroTitle) as $word)
                            <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h1>
                </div>

                @if ($storyParagraphs->isNotEmpty())
                    <div class="values-copy services-index-intro-copy ac-about-intro-copy content-reveal" data-image-reveal>
                        <p>{!! $introStoryHtml !!}</p>
                    </div>
                @endif
            </div>
        </section>

        <section class="ac-about-hero" aria-label="{{ $heroTitle }}">
            <div class="ac-about-container">
                <div class="ac-about-hero-grid">
                    <div class="ac-about-hero-media content-reveal animation-index-0" data-image-reveal>
                        <figure class="ac-about-image image-reveal-media {{ $aboutHeroPhoto['class'] }}">
                            <img
                                src="{{ $aboutHeroPhoto['src'] }}"
                                alt="{{ $aboutHeroPhoto['alt'] }}"
                                width="1448"
                                height="1086"
                                loading="eager"
                                decoding="async"
                                fetchpriority="high"
                            >
                            <span class="image-reveal-curtain" aria-hidden="true"></span>
                        </figure>

                        <div class="ac-about-stat-card">
                            <strong>600+</strong>
                            <span>{{ $heroStatLabel }}</span>
                        </div>
                    </div>

                    <div class="ac-about-copy-stack ac-about-hero-story">
                        @if ($heroLead !== '')
                            <h2 class="ac-about-story-title" data-words-slide-from-right aria-label="{{ $heroLead }}">
                                @foreach ($headingWords($heroLead) as $word)
                                    <span class="service-title-word animation-index-{{ $loop->index }}" aria-hidden="true">{{ $word }}</span>
                                @endforeach
                            </h2>
                        @endif

                        @if ($storyParagraphs->count() > 1)
                            <div class="ac-about-hero-paragraphs content-reveal animation-index-1" data-image-reveal>
                                @foreach ($storyParagraphs->skip(1) as $paragraph)
                                    @php
                                        $storyParagraphHtml = str_replace(
                                            'ALPHA CAPITALIS',
                                            '<a class="ac-about-dark-inline-link" href="'.e(route('contact.create')).'">ALPHA CAPITALIS</a>',
                                            e($paragraph),
                                        );
                                    @endphp
                                    <p>{!! $storyParagraphHtml !!}</p>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="ac-about-values" aria-labelledby="ac-about-values-title">
            <div class="ac-about-container">
                <div class="ac-about-section-intro ac-about-values-intro">
                    <h2 class="values-title services-index-intro-title ac-about-section-intro-title ac-about-values-label" id="ac-about-values-title" data-words-slide-from-right aria-label="{{ $valuesLabel }}">
                        @foreach ($headingWords($valuesLabel) as $word)
                            <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>

                    @if ($valuesIntro !== '')
                        <div class="values-copy services-index-intro-copy ac-about-section-intro-copy ac-about-values-copy content-reveal animation-index-1" data-image-reveal>
                            <h3 class="ac-about-copy-heading ac-about-values-copy-title">{{ $valuesTitle }}</h3>
                            <p>{!! $valuesIntroHtml !!}</p>
                        </div>
                    @endif
                </div>

                <div class="ac-about-value-grid">
                    @foreach ((array) ($values['items'] ?? []) as $item)
                        @php
                            $item = is_array($item) ? $item : [];
                            $itemTitle = trim((string) ($item['title'] ?? ''));
                        @endphp

                        @continue($itemTitle === '')

                        <article class="ac-about-value-card content-reveal animation-index-{{ $loop->index }}" data-image-reveal>
                            <span class="ac-about-value-icon" aria-hidden="true">
                                <i class="fa-duotone fa-thin fa-fw {{ $valueIconClasses[$loop->index] ?? 'fa-circle-check' }}"></i>
                            </span>
                            <h3>{{ $itemTitle }}</h3>

                            @if (trim((string) ($item['lead'] ?? '')) !== '')
                                <p class="ac-about-card-lead">{{ $item['lead'] }}</p>
                            @endif

                            <div class="ac-about-copy-stack">
                                @foreach ((array) ($item['paragraphs'] ?? []) as $paragraph)
                                    @continue(trim((string) $paragraph) === '')
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="ac-about-why" aria-labelledby="ac-about-why-title">
            <div class="ac-about-container">
                <div class="ac-about-section-intro ac-about-why-intro">
                    <h2 class="values-title services-index-intro-title ac-about-section-intro-title ac-about-why-title" id="ac-about-why-title" data-words-slide-from-right aria-label="{{ $whyLabel }}">
                        @foreach ($headingWords($whyLabel) as $word)
                            <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>

                    @if ($whyTitle !== '' || $whyQuote !== '')
                        <div class="values-copy services-index-intro-copy ac-about-section-intro-copy ac-about-why-copy content-reveal animation-index-1" data-image-reveal>
                            @if ($whyTitle !== '')
                                <h3 class="ac-about-copy-heading ac-about-copy-heading--light">{{ $whyTitle }}</h3>
                            @endif

                            @if ($whyQuote !== '')
                                <blockquote class="ac-about-why-quote">
                                    <p>{{ $whyQuote }}</p>
                                </blockquote>
                            @endif
                        </div>
                    @endif
                </div>

                @if ($whyParagraphs->isNotEmpty())
                    <div class="ac-about-why-body">
                        <div class="ac-about-why-body-lead content-reveal animation-index-1" data-image-reveal>
                            @foreach ($whyParagraphs->take(2) as $paragraph)
                                <p>{!! $linkWhyServiceTerms($paragraph) !!}</p>
                            @endforeach
                        </div>

                        <div class="ac-about-copy-stack ac-about-why-body-copy content-reveal animation-index-2" data-image-reveal>
                            @foreach ($whyParagraphs->skip(2) as $paragraph)
                                <p>{!! $linkWhyServiceTerms($paragraph) !!}</p>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <section class="ac-about-team" aria-labelledby="ac-about-team-intro-title">
            <div class="ac-about-team-intro">
                <div class="ac-about-container">
                    <div class="ac-about-section-intro ac-about-team-intro-grid">
                        <h2 class="values-title services-index-intro-title ac-about-section-intro-title ac-about-team-label" id="ac-about-team-intro-title" data-words-slide-from-right aria-label="{{ $teamLabel }}">
                            @foreach ($headingWords($teamLabel) as $word)
                                <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                            @endforeach
                        </h2>

                        <div class="values-copy services-index-intro-copy ac-about-section-intro-copy ac-about-team-copy">
                            <h2 class="ac-about-story-title ac-about-copy-heading ac-about-team-members-title" id="ac-about-team-title" data-words-slide-from-right aria-label="{{ $teamTitle }}">
                                @foreach ($headingWords($teamTitle) as $word)
                                    <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->remaining < 2 ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                                @endforeach
                            </h2>

                            @if (trim((string) ($team['intro'] ?? '')) !== '' || trim((string) ($team['body'] ?? '')) !== '')
                                <div class="ac-about-team-text content-reveal animation-index-1" data-image-reveal>
                                    @if (trim((string) ($team['intro'] ?? '')) !== '')
                                        <p class="ac-about-team-lead">{{ $team['intro'] }}</p>
                                    @endif

                                    @if (trim((string) ($team['body'] ?? '')) !== '')
                                        <p>{{ $team['body'] }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if ($teamStats->isNotEmpty())
                <div class="ac-about-team-stats" role="region" aria-label="{{ $teamLabel }}" data-locations-reveal>
                    <div class="ac-about-team-stats-shell">
                        <div class="locations-stats ac-about-stat-grid">
                        @foreach ($teamStats as $stat)
                            @php
                                $statValue = trim((string) ($stat['value'] ?? ''));
                                $statCountTo = preg_replace('/\D+/', '', $statValue) ?: '';
                                $statSuffix = $statCountTo !== '' ? trim(str_replace($statCountTo, '', $statValue)) : '';
                            @endphp

                            <article class="location-stat ac-about-stat-item">
                                <div class="location-stat-icon" aria-hidden="true">
                                    <i class="fa-duotone fa-thin fa-fw {{ $teamStatIconClasses[$loop->index] ?? 'fa-circle-check' }}"></i>
                                </div>
                                <div>
                                    <strong>
                                        @if ($statCountTo !== '')
                                            <span data-count-target="{{ $statCountTo }}">0</span><span class="location-stat-suffix">{{ $statSuffix }}</span>
                                        @else
                                            {{ $statValue }}
                                        @endif
                                    </strong>
                                    <p>{{ $stat['label'] ?? '' }}</p>
                                </div>
                            </article>
                        @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="ac-about-team-members" role="region" aria-labelledby="ac-about-team-title">
                <div class="ac-about-container">
                    @if ($aboutPreviewTeamMembers->isNotEmpty())
                        <div class="ac-about-member-grid">
                        @foreach ($aboutPreviewTeamMembers as $member)
                            <article class="ac-about-member-card content-reveal animation-index-{{ $loop->index }}" data-image-reveal>
                                <div class="ac-about-member-photo {{ ($member['photo_url'] ?? '') !== '' ? 'image-reveal-media' : '' }}">
                                    @if (($member['photo_url'] ?? '') !== '')
                                        <img
                                            src="{{ $member['photo_url'] }}"
                                            alt="{{ $member['name'] }}"
                                            loading="lazy"
                                            decoding="async"
                                        >
                                        <span class="image-reveal-curtain" aria-hidden="true"></span>
                                    @else
                                        <span>{{ $member['initials'] ?? 'AC' }}</span>
                                    @endif

                                    <div class="ac-about-member-info">
                                        <h3>{{ $member['name'] }}</h3>
                                        @if (trim((string) ($member['position'] ?? '')) !== '')
                                            <p>{{ $member['position'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach

                        <article
                            class="ac-about-member-card ac-about-member-cta-card content-reveal animation-index-{{ $aboutPreviewTeamMembers->count() }}"
                            data-image-reveal
                        >
                            <a href="{{ route('team.index') }}" class="ac-about-member-cta-link">
                                <span class="ac-about-member-cta-button">
                                    <span>{{ $teamButtonLabel }}</span>
                                </span>
                            </a>
                        </article>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <section class="ac-about-culture" aria-labelledby="ac-about-culture-title">
            <div class="ac-about-container">
                <div class="ac-about-section-intro ac-about-culture-intro">
                    <h2 class="values-title services-index-intro-title ac-about-section-intro-title ac-about-culture-label" id="ac-about-culture-title" data-words-slide-from-right aria-label="{{ $cultureLabel }}">
                        @foreach ($headingWords($cultureLabel) as $word)
                            <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>

                    @if ($cultureTitle !== '' || $cultureQuote !== '')
                        <div class="values-copy services-index-intro-copy ac-about-section-intro-copy ac-about-culture-copy content-reveal animation-index-1" data-image-reveal>
                            @if ($cultureTitle !== '')
                                <h3 class="ac-about-copy-heading ac-about-culture-copy-title">{{ $cultureTitle }}</h3>
                            @endif

                            @if ($cultureQuote !== '')
                                <blockquote class="ac-about-culture-quote">
                                    <p>{{ $cultureQuote }}</p>
                                </blockquote>
                            @endif
                        </div>
                    @endif
                </div>

                @if ($cultureParagraphs->isNotEmpty())
                    <div class="ac-about-culture-body">
                        <div class="ac-about-culture-body-lead content-reveal animation-index-1" data-image-reveal>
                            @foreach ($cultureParagraphs->take($cultureColumnSplit) as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>

                        <div class="ac-about-copy-stack ac-about-culture-body-copy content-reveal animation-index-2" data-image-reveal>
                            @foreach ($cultureParagraphs->skip($cultureColumnSplit) as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <section class="ac-about-responsibility" aria-labelledby="ac-about-responsibility-title">
            <div class="ac-about-container">
                <div class="ac-about-section-intro ac-about-responsibility-intro">
                    <h2 class="values-title services-index-intro-title ac-about-section-intro-title ac-about-responsibility-label" id="ac-about-responsibility-title" data-words-slide-from-right aria-label="{{ $responsibilityLabel }}">
                        @foreach ($headingWords($responsibilityLabel) as $word)
                            <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>

                    @if ($responsibilityTitle !== '' || $responsibilityQuote !== '')
                        <div class="values-copy services-index-intro-copy ac-about-section-intro-copy ac-about-responsibility-copy content-reveal animation-index-1" data-image-reveal>
                            @if ($responsibilityTitle !== '')
                                <h3 class="ac-about-copy-heading ac-about-responsibility-copy-title">{{ $responsibilityTitle }}</h3>
                            @endif

                            @if ($responsibilityQuote !== '')
                                <blockquote class="ac-about-responsibility-quote">
                                    <p>{{ $responsibilityQuote }}</p>
                                </blockquote>
                            @endif
                        </div>
                    @endif
                </div>

                @if ($responsibilityParagraphs->isNotEmpty())
                    <div class="ac-about-responsibility-body">
                        <div class="ac-about-responsibility-body-lead content-reveal animation-index-1" data-image-reveal>
                            @foreach ($responsibilityParagraphs->take(2) as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>

                        <div class="ac-about-copy-stack ac-about-responsibility-body-copy content-reveal animation-index-2" data-image-reveal>
                            @foreach ($responsibilityParagraphs->skip(2) as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </section>

        @if ($responsibilityCtaIntro !== '' || $responsibilityCtaText !== '')
            <section class="contact-cta ac-about-contact-cta" aria-labelledby="ac-about-contact-cta-title">
                <div class="contact-cta-shell">
                    <div class="contact-cta-copy">
                        <h2 class="contact-cta-title" id="ac-about-contact-cta-title" data-words-slide-from-right aria-label="{{ $responsibilityCtaIntro }}">
                            @foreach ($headingWords($responsibilityCtaIntro) as $word)
                                <span class="contact-cta-title-word animation-index-{{ $loop->index }} {{ $loop->remaining < 2 ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                            @endforeach
                        </h2>
                    </div>

                    <div class="contact-cta-card" data-image-reveal>
                        <div class="contact-cta-card-heading"><span>{{ $responsibilityCtaCardTitle }}</span></div>

                        @if ($responsibilityCtaText !== '')
                            <p>{{ $responsibilityCtaText }}</p>
                        @endif

                        <a class="contact-cta-button" href="{{ route('contact.create') }}">
                            <span>{{ $responsibilityCtaLabel }}</span>
                            <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                        </a>

                        <small><span class="contact-cta-status-dot" aria-hidden="true"></span>{{ $responsibilityCtaStatus }}</small>
                    </div>
                </div>
            </section>
        @endif

        <section class="ac-about-references" aria-labelledby="ac-about-references-title">
            <div class="ac-about-container">
                <div class="ac-about-section-intro ac-about-reference-head">
                    <h2 class="values-title services-index-intro-title ac-about-section-intro-title ac-about-reference-label" id="ac-about-references-title" data-words-slide-from-right aria-label="{{ $referencesLabel }}">
                        @foreach ($headingWords($referencesLabel) as $word)
                            <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>

                    <div class="values-copy services-index-intro-copy ac-about-section-intro-copy ac-about-reference-copy content-reveal animation-index-1" data-image-reveal>
                        <h3 class="ac-about-copy-heading ac-about-reference-copy-title">{{ $referencesTitle }}</h3>
                    </div>
                </div>

                @if ($referenceParagraphs->isNotEmpty())
                    <div class="ac-about-reference-body">
                        <div class="ac-about-reference-body-lead content-reveal animation-index-1" data-image-reveal>
                            @foreach ($referenceParagraphs->take(2) as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>

                        <div class="ac-about-copy-stack ac-about-reference-body-copy content-reveal animation-index-2" data-image-reveal>
                            @foreach ($referenceParagraphs->skip(2) as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($aboutReferenceItems->isNotEmpty())
                    <div class="ac-about-reference-grid">
                        @foreach ($aboutReferenceItems as $item)
                            <article class="ac-about-reference-card" aria-label="{{ $item['name'] }}">
                                <div class="ac-about-reference-logo content-reveal animation-index-{{ $loop->index % 2 }}" data-image-reveal>
                                    <img
                                        src="{{ $item['url'] }}"
                                        alt="{{ $item['alt'] }}"
                                        loading="eager"
                                        decoding="async"
                                    >
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif

                <div class="ac-about-section-actions content-reveal animation-index-1" data-image-reveal>
                    <a href="{{ $referencePageUrl }}" class="front-action-cta ac-about-secondary-cta">
                        <span>{{ $referencesButtonLabel }}</span>
                        <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </section>

        @if ($bottomBlocks->isNotEmpty())
            <section class="ac-about-blocks ac-about-blocks--bottom">@include('components.content-placement', ['items' => $bottomBlocks])</section>
        @endif
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/about.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/about.css')) }}">
@endpush
