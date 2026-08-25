@extends('front.desktop.layouts.store')

@php
    $aboutRequiresExactTranslation = (bool) ($aboutRequiresExactTranslation
        ?? \App\Support\Localization\FrontendLocalePolicy::requiresExactTranslation((string) $locale));
    $translation = $selectedTranslation
        ?? $page->translations->firstWhere('locale', $locale)
        ?? ($aboutRequiresExactTranslation ? null : $page->translations->firstWhere('locale', $fallbackLocale));

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
    $aboutHeroMediaAlt = trim((string) data_get($aboutHeroMedia?->custom_properties, 'alt.'.$locale));
    if (! $aboutRequiresExactTranslation && $aboutHeroMediaAlt === '') {
        $aboutHeroMediaAlt = trim((string) (
            data_get($aboutHeroMedia?->custom_properties, 'alt.'.$fallbackLocale)
            ?: $aboutHeroMedia?->name
        ));
    }
    $aboutHeroContentAlt = trim((string) ($hero['image_alt'] ?? ''));
    $aboutHeroPhoto = [
        'class' => 'ac-about-image--hero',
        'src' => $aboutHeroMedia?->hasGeneratedConversion('about_hero_1440x1059')
            ? $aboutHeroMedia->getUrl('about_hero_1440x1059')
            : ($aboutHeroMedia?->getUrl() ?: asset('front-theme/images/about/o-nama.jpg')),
        'alt' => $aboutHeroContentAlt !== ''
            ? $aboutHeroContentAlt
            : $aboutHeroMediaAlt,
    ];
    $referencePageUrl = route('pages.show', ['slug' => 'reference']);
    $teamButtonLabel = trim((string) ($team['button_label'] ?? ''));
    $referencesButtonLabel = trim((string) ($references['button_label'] ?? ''));
    $heroStatValue = trim((string) ($hero['stat_value'] ?? ''));
    $heroStatLabel = trim((string) ($hero['stat_label'] ?? ''));
    $whyQuote = trim((string) ($why['quote'] ?? ''));
    $cultureQuote = trim((string) ($culture['quote'] ?? ''));
    $responsibilityQuote = trim((string) ($responsibility['quote'] ?? ''));
    $responsibilityCtaIntro = trim((string) ($responsibility['cta_intro'] ?? ''));
    $responsibilityCtaText = trim((string) ($responsibility['cta_text'] ?? ''));
    $responsibilityCtaLabel = trim((string) ($responsibility['cta_button_label'] ?? ''));
    $responsibilityCtaCardTitle = trim((string) ($responsibility['cta_card_title'] ?? ''));
    $responsibilityCtaStatus = trim((string) ($responsibility['cta_status'] ?? ''));

    $pageTitle = trim((string) ($translation?->title ?? ''));
    $heroTitle = trim((string) ($hero['title'] ?? '')) ?: $pageTitle;
    $heroLead = trim((string) ($hero['lead'] ?? ''));
    $richTextBlocks = static function (array $section, array $legacyParagraphs): \Illuminate\Support\Collection {
        $bodyHtml = array_key_exists('body_html', $section)
            ? trim((string) $section['body_html'])
            : \App\Support\Content\StructuredRichText::fromParagraphs($legacyParagraphs);
        $plainText = trim(html_entity_decode(strip_tags(str_ireplace(['<br>', '<br/>', '<br />'], '', $bodyHtml))));

        return $bodyHtml === '' || $plainText === ''
            ? collect()
            : collect(\App\Support\Content\StructuredRichText::blocks($bodyHtml));
    };
    $linkTermsInHtml = static function (string $html, array $termLinks, string $class): string {
        $linkedHtml = preg_replace_callback(
            '/<a\b[^>]*>.*?<\/a>|<[^>]+>|[^<]+/isu',
            static function (array $matches) use ($termLinks, $class): string {
                $fragment = (string) ($matches[0] ?? '');

                if (str_starts_with($fragment, '<')) {
                    return $fragment;
                }

                $replacements = [];
                foreach ($termLinks as $term => $url) {
                    $replacements[(string) $term] = '<a class="'.e($class).'" href="'.e($url).'">'.e($term).'</a>';
                }

                return strtr($fragment, $replacements);
            },
            $html,
        );

        return is_string($linkedHtml) ? $linkedHtml : $html;
    };
    $storyBlocks = $richTextBlocks($story, (array) ($story['paragraphs'] ?? []));
    $storyContactLinks = ['ALPHA CAPITALIS' => \App\Support\Localization\FrontendRoute::url('contact.create')];
    $introStoryHtml = $storyBlocks->isNotEmpty()
        ? $linkTermsInHtml((string) $storyBlocks->first(), $storyContactLinks, 'services-index-inline-link')
        : '';
    $headingWords = static fn (string $title): array => preg_split('/\s+/u', trim($title), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $valuesLabel = trim((string) ($values['label'] ?? ''));
    $valuesTitle = trim((string) ($values['title'] ?? ''));
    $valuesIntro = trim((string) ($values['intro'] ?? ''));
    $valuesIntroLinkText = str_contains($valuesIntro, 'ALPHA CAPITALISU')
        ? 'ALPHA CAPITALISU'
        : 'ALPHA CAPITALIS';
    $valuesIntroHtml = str_replace(
        $valuesIntroLinkText,
        '<a class="services-index-inline-link" href="'.e(\App\Support\Localization\FrontendRoute::url('contact.create')).'">'.e($valuesIntroLinkText).'</a>',
        e($valuesIntro),
    );
    $whyLabel = trim((string) ($why['kicker'] ?? ''));
    $whyTitle = trim((string) ($why['title'] ?? ''));
    $whyBlocks = $richTextBlocks($why, (array) ($why['paragraphs'] ?? []));
    $whyServiceTermLinks = [
        'strateškog razvoja' => \App\Support\Localization\FrontendRoute::url('advisory.show'),
        'računovodstva' => \App\Support\Localization\FrontendRoute::url('accounting.show'),
        'EU fondova' => \App\Support\Localization\FrontendRoute::url('eu-funds.show'),
        'financija' => \App\Support\Localization\FrontendRoute::url('advisory.finance.show'),
        'revizije' => \App\Support\Localization\FrontendRoute::url('audit.show'),
        'strategic development' => \App\Support\Localization\FrontendRoute::url('advisory.show'),
        'accounting' => \App\Support\Localization\FrontendRoute::url('accounting.show'),
        'EU funds' => \App\Support\Localization\FrontendRoute::url('eu-funds.show'),
        'finance' => \App\Support\Localization\FrontendRoute::url('advisory.finance.show'),
        'audit' => \App\Support\Localization\FrontendRoute::url('audit.show'),
    ];
    $teamTitle = trim((string) ($team['title'] ?? ''));
    $teamLabel = trim((string) ($team['label'] ?? ''));
    $teamBlocks = $richTextBlocks(
        $team,
        [($team['intro'] ?? ''), ($team['body'] ?? '')],
    );
    $teamStats = collect((array) ($team['stats'] ?? []))
        ->map(static fn ($stat): array => is_array($stat) ? $stat : [])
        ->filter(static fn (array $stat): bool => trim((string) ($stat['value'] ?? '')) !== '')
        ->values();
    $cultureLabel = trim((string) ($culture['kicker'] ?? ''));
    $cultureTitle = trim((string) ($culture['title'] ?? ''));
    $cultureBlocks = $richTextBlocks($culture, (array) ($culture['paragraphs'] ?? []));
    $cultureColumnSplit = $cultureBlocks->count() >= 4 ? 2 : 1;
    $responsibilityLabel = trim((string) ($responsibility['kicker'] ?? ''));
    $responsibilityTitle = trim((string) ($responsibility['title'] ?? ''));
    $responsibilityBlocks = $richTextBlocks($responsibility, (array) ($responsibility['paragraphs'] ?? []));
    $referencesLabel = trim((string) ($references['label'] ?? ''));
    $referencesTitle = trim((string) ($references['title'] ?? ''));
    $referenceBlocks = $richTextBlocks($references, (array) ($references['paragraphs'] ?? []));
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
    $showStory = $sectionHasContent($hero) || $storyBlocks->isNotEmpty();
    $showValues = $sectionHasContent($values);
    $showWhy = $sectionHasContent($why);
    $showTeam = $sectionHasContent($team);
    $showCulture = $sectionHasContent($culture);
    $showResponsibility = $sectionHasContent($responsibility);
    $showReferences = $sectionHasContent($references);
@endphp

@section('title', $pageTitle)
@section('main_class', 'w-full px-0 py-0')
@section('hide_footer_newsletter', '1')

@section('content')
    <div class="ac-about-page">
        @if ($topBlocks->isNotEmpty())
            <section class="ac-about-blocks ac-about-blocks--top">@include('components.content-placement', ['items' => $topBlocks])</section>
        @endif

        @if ($showStory)
        <section class="values-section services-index-intro ac-about-intro" aria-labelledby="ac-about-hero-title">
            <div class="values-inner services-index-intro-layout ac-about-intro-layout">
                <div class="values-intro">
                    <h1 class="values-title services-index-intro-title ac-about-intro-title" id="ac-about-hero-title" data-words-slide-from-right aria-label="{{ $heroTitle }}">
                        @foreach ($headingWords($heroTitle) as $word)
                            <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h1>
                </div>

                @if ($storyBlocks->isNotEmpty())
                    <div class="values-copy services-index-intro-copy ac-about-intro-copy content-reveal" data-image-reveal>
                        {!! $introStoryHtml !!}
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

                        @if ($heroStatValue !== '' || $heroStatLabel !== '')
                            <div class="ac-about-stat-card">
                                @if ($heroStatValue !== '')
                                    <strong>{{ $heroStatValue }}</strong>
                                @endif
                                @if ($heroStatLabel !== '')
                                    <span>{{ $heroStatLabel }}</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="ac-about-copy-stack ac-about-hero-story">
                        @if ($heroLead !== '')
                            <h2 class="ac-about-story-title" data-words-slide-from-right aria-label="{{ $heroLead }}">
                                @foreach ($headingWords($heroLead) as $word)
                                    <span class="service-title-word animation-index-{{ $loop->index }}" aria-hidden="true">{{ $word }}</span>
                                @endforeach
                            </h2>
                        @endif

                        @if ($storyBlocks->count() > 1)
                            <div class="ac-about-hero-paragraphs content-reveal animation-index-1" data-image-reveal>
                                @foreach ($storyBlocks->skip(1) as $block)
                                    {!! $linkTermsInHtml((string) $block, $storyContactLinks, 'ac-about-dark-inline-link') !!}
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        @endif

        @if ($showValues)
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
                            $itemLegacyParagraphs = array_merge(
                                [($item['lead'] ?? '')],
                                (array) ($item['paragraphs'] ?? []),
                            );
                            $itemBlocks = $richTextBlocks($item, $itemLegacyParagraphs);
                        @endphp

                        @continue($itemTitle === '')

                        <article class="ac-about-value-card content-reveal animation-index-{{ $loop->index }}" data-image-reveal>
                            <span class="ac-about-value-icon" aria-hidden="true">
                                <i class="fa-duotone fa-thin fa-fw {{ $valueIconClasses[$loop->index] ?? 'fa-circle-check' }}"></i>
                            </span>
                            <h3>{{ $itemTitle }}</h3>

                            @if ($itemBlocks->isNotEmpty())
                                {!! \App\Support\Content\StructuredRichText::addClassToFirstBlock(
                                    (string) $itemBlocks->first(),
                                    'ac-about-card-lead',
                                ) !!}
                            @endif

                            @if ($itemBlocks->count() > 1)
                                <div class="ac-about-copy-stack">
                                    @foreach ($itemBlocks->skip(1) as $block)
                                        {!! $block !!}
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        @if ($showWhy)
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

                @if ($whyBlocks->isNotEmpty())
                    <div class="ac-about-why-body">
                        <div class="ac-about-why-body-lead content-reveal animation-index-1" data-image-reveal>
                            @foreach ($whyBlocks->take(1) as $block)
                                {!! $linkTermsInHtml((string) $block, $whyServiceTermLinks, 'ac-about-dark-inline-link') !!}
                            @endforeach
                        </div>

                        <div class="ac-about-copy-stack ac-about-why-body-copy content-reveal animation-index-2" data-image-reveal>
                            @foreach ($whyBlocks->skip(1) as $block)
                                {!! $linkTermsInHtml((string) $block, $whyServiceTermLinks, 'ac-about-dark-inline-link') !!}
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
        @endif

        @if ($showTeam)
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

                            @if ($teamBlocks->isNotEmpty())
                                <div class="ac-about-team-text content-reveal animation-index-1" data-image-reveal>
                                    {!! \App\Support\Content\StructuredRichText::addClassToFirstBlock(
                                        (string) $teamBlocks->first(),
                                        'ac-about-team-lead',
                                    ) !!}
                                    @foreach ($teamBlocks->skip(1) as $block)
                                        {!! $block !!}
                                    @endforeach
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

                        @if ($teamButtonLabel !== '')
                            <article
                                class="ac-about-member-card ac-about-member-cta-card content-reveal animation-index-{{ $aboutPreviewTeamMembers->count() }}"
                                data-image-reveal
                            >
                                <a href="{{ \App\Support\Localization\FrontendRoute::url('team.index') }}" class="ac-about-member-cta-link">
                                    <span class="ac-about-member-cta-button">
                                        <span>{{ $teamButtonLabel }}</span>
                                    </span>
                                </a>
                            </article>
                        @endif
                        </div>
                    @endif
                </div>
            </div>
        </section>
        @endif

        @if ($showCulture)
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

                @if ($cultureBlocks->isNotEmpty())
                    <div class="ac-about-culture-body">
                        <div class="ac-about-culture-body-lead content-reveal animation-index-1" data-image-reveal>
                            @foreach ($cultureBlocks->take($cultureColumnSplit) as $block)
                                {!! $block !!}
                            @endforeach
                        </div>

                        <div class="ac-about-copy-stack ac-about-culture-body-copy content-reveal animation-index-2" data-image-reveal>
                            @foreach ($cultureBlocks->skip($cultureColumnSplit) as $block)
                                {!! $block !!}
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
        @endif

        @if ($showResponsibility)
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

                @if ($responsibilityBlocks->isNotEmpty())
                    <div class="ac-about-responsibility-body">
                        <div class="ac-about-responsibility-body-lead content-reveal animation-index-1" data-image-reveal>
                            @foreach ($responsibilityBlocks->take(2) as $block)
                                {!! $block !!}
                            @endforeach
                        </div>

                        <div class="ac-about-copy-stack ac-about-responsibility-body-copy content-reveal animation-index-2" data-image-reveal>
                            @foreach ($responsibilityBlocks->skip(2) as $block)
                                {!! $block !!}
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </section>
        @endif

        @if ($showResponsibility && ($responsibilityCtaIntro !== '' || $responsibilityCtaText !== ''))
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
                        @if ($responsibilityCtaCardTitle !== '')
                            <div class="contact-cta-card-heading"><span>{{ $responsibilityCtaCardTitle }}</span></div>
                        @endif

                        @if ($responsibilityCtaText !== '')
                            <p>{{ $responsibilityCtaText }}</p>
                        @endif

                        @if ($responsibilityCtaLabel !== '')<a class="contact-cta-button" href="{{ \App\Support\Localization\FrontendRoute::url('contact.create') }}">
                            <span>{{ $responsibilityCtaLabel }}</span>
                            <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                        </a>@endif


                        @if ($responsibilityCtaStatus !== '')
                            <small><span class="contact-cta-status-dot" aria-hidden="true"></span>{{ $responsibilityCtaStatus }}</small>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        @if ($showReferences)
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

                @if ($referenceBlocks->isNotEmpty())
                    <div class="ac-about-reference-body">
                        <div class="ac-about-reference-body-lead content-reveal animation-index-1" data-image-reveal>
                            @foreach ($referenceBlocks->take(2) as $block)
                                {!! $block !!}
                            @endforeach
                        </div>

                        <div class="ac-about-copy-stack ac-about-reference-body-copy content-reveal animation-index-2" data-image-reveal>
                            @foreach ($referenceBlocks->skip(2) as $block)
                                {!! $block !!}
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
        @endif

        @if ($bottomBlocks->isNotEmpty())
            <section class="ac-about-blocks ac-about-blocks--bottom">@include('components.content-placement', ['items' => $bottomBlocks])</section>
        @endif
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/about.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/about.css')) }}">
@endpush
