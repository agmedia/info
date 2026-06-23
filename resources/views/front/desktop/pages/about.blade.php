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
    $aboutHeroPhoto = [
        'class' => 'ac-about-image--hero',
        'src' => asset('front-theme/images/about/o-nama-alpha-capitalis.jpg'),
        'alt' => str_starts_with(strtolower((string) $locale), 'hr')
            ? 'ALPHA CAPITALIS tim'
            : 'ALPHA CAPITALIS team',
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

    $pageTitleBreadcrumbs = [
        ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
        ['label' => $translation?->title ?? $page->code, 'current' => true],
    ];

    $pageTitle = trim((string) ($translation?->title ?? '')) ?: 'O nama';
    $heroTitle = trim((string) ($hero['title'] ?? '')) ?: $pageTitle;
    $heroLead = trim((string) ($hero['lead'] ?? ''));
@endphp

@section('title', $pageTitle)
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-about-page">
        @if ($topBlocks->isNotEmpty())
            <section class="ac-about-blocks ac-about-blocks--top">@include('components.content-placement', ['items' => $topBlocks])</section>
        @endif

        <x-front.page-title-band
            :breadcrumbs="$pageTitleBreadcrumbs"
            section-class="ac-about-title-band"
            breadcrumb-class="ac-about-title-breadcrumb"
        >
            <div class="ac-page-title-copy">
                <h1>{{ $pageTitle }}</h1>
                <p>{{ $heroTitle }}</p>
            </div>
        </x-front.page-title-band>

        <section class="ac-about-hero" aria-labelledby="ac-about-hero-title">
            <div class="ac-about-container">
                <div class="ac-about-hero-grid">
                    <div class="ac-about-hero-copy">
                        @if (trim((string) ($hero['eyebrow'] ?? '')) !== '')
                            <p class="ac-about-kicker">{{ $hero['eyebrow'] }}</p>
                        @endif

                        <h2 id="ac-about-hero-title">{{ $heroTitle }}</h2>

                        @if ($heroLead !== '')
                            <p class="ac-about-lead">{{ $heroLead }}</p>
                        @endif

                        <div class="ac-about-copy-stack">
                            @foreach ((array) ($story['paragraphs'] ?? []) as $paragraph)
                                @continue(trim((string) $paragraph) === '')
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </div>

                    <div class="ac-about-hero-media">
                        <figure class="ac-about-image {{ $aboutHeroPhoto['class'] }}">
                            <img
                                src="{{ $aboutHeroPhoto['src'] }}"
                                alt="{{ $aboutHeroPhoto['alt'] }}"
                                width="1386"
                                height="925"
                                loading="eager"
                                decoding="async"
                                fetchpriority="high"
                            >
                        </figure>

                        <div class="ac-about-stat-card">
                            <strong>600+</strong>
                            <span>{{ $heroStatLabel }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="ac-about-values" aria-labelledby="ac-about-values-title">
            <div class="ac-about-container">
                <div class="ac-about-section-head">
                    @if (trim((string) ($values['kicker'] ?? '')) !== '')
                        <p class="ac-about-kicker">{{ $values['kicker'] }}</p>
                    @endif

                    <h2 id="ac-about-values-title">{{ $values['title'] ?? 'Vrijednosti' }}</h2>

                    @if (trim((string) ($values['intro'] ?? '')) !== '')
                        <p>{{ $values['intro'] }}</p>
                    @endif
                </div>

                <div class="ac-about-value-grid">
                    @foreach ((array) ($values['items'] ?? []) as $item)
                        @php
                            $item = is_array($item) ? $item : [];
                            $itemTitle = trim((string) ($item['title'] ?? ''));
                        @endphp

                        @continue($itemTitle === '')

                        <article class="ac-about-value-card">
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
                <div class="ac-about-split-grid">
                    <div>
                        @if (trim((string) ($why['kicker'] ?? '')) !== '')
                            <p class="ac-about-kicker">{{ $why['kicker'] }}</p>
                        @endif

                        <h2 id="ac-about-why-title">{{ $why['title'] ?? 'Zašto postojimo' }}</h2>

                        @if ($whyQuote !== '')
                            <blockquote class="ac-about-pullquote">
                                <p>{{ $whyQuote }}</p>
                            </blockquote>
                        @endif
                    </div>

                    <div class="ac-about-copy-stack ac-about-copy-stack--large">
                        @foreach ((array) ($why['paragraphs'] ?? []) as $paragraph)
                            @continue(trim((string) $paragraph) === '')
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="ac-about-team" aria-labelledby="ac-about-team-title">
            <div class="ac-about-container">
                <div class="ac-about-team-head">
                    <div>
                        @if (trim((string) ($team['kicker'] ?? '')) !== '')
                            <p class="ac-about-kicker">{{ $team['kicker'] }}</p>
                        @endif

                        <h2 id="ac-about-team-title">{{ $team['title'] ?? 'Tim' }}</h2>
                    </div>

                    <div class="ac-about-copy-stack">
                        @if (trim((string) ($team['intro'] ?? '')) !== '')
                            <p class="ac-about-team-intro">{{ $team['intro'] }}</p>
                        @endif

                        @if (trim((string) ($team['body'] ?? '')) !== '')
                            <p>{{ $team['body'] }}</p>
                        @endif
                    </div>
                </div>

                <div class="ac-about-stat-grid">
                    @foreach ((array) ($team['stats'] ?? []) as $stat)
                        @php
                            $stat = is_array($stat) ? $stat : [];
                            $statValue = trim((string) ($stat['value'] ?? ''));
                        @endphp

                        @continue($statValue === '')

                        <div class="ac-about-stat-item">
                            <strong>{{ $statValue }}</strong>
                            <span>{{ $stat['label'] ?? '' }}</span>
                        </div>
                    @endforeach
                </div>

                @if ($aboutPreviewTeamMembers->isNotEmpty())
                    <div class="ac-about-member-grid">
                        @foreach ($aboutPreviewTeamMembers as $member)
                            <article class="ac-about-member-card">
                                <div class="ac-about-member-photo">
                                    @if (($member['photo_url'] ?? '') !== '')
                                        <img
                                            src="{{ $member['photo_url'] }}"
                                            alt="{{ $member['name'] }}"
                                            loading="lazy"
                                            decoding="async"
                                        >
                                    @else
                                        <span>{{ $member['initials'] ?? 'AC' }}</span>
                                    @endif
                                </div>

                                <div>
                                    <h3>{{ $member['name'] }}</h3>
                                    @if (trim((string) ($member['position'] ?? '')) !== '')
                                        <p>{{ $member['position'] }}</p>
                                    @endif
                                </div>
                            </article>
                        @endforeach

                        <article class="ac-about-member-card ac-about-member-cta-card">
                            <a href="{{ route('team.index') }}" class="ac-about-member-cta-link">
                                <span class="ac-about-member-cta-button">
                                    <span>{{ $teamButtonLabel }}</span>
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M4 12L12 4"></path>
                                        <path d="M6 4h6v6"></path>
                                    </svg>
                                </span>
                            </a>
                        </article>
                    </div>
                @endif
            </div>
        </section>

        <section class="ac-about-culture" aria-labelledby="ac-about-culture-title">
            <div class="ac-about-container">
                <div class="ac-about-split-grid">
                    <div>
                        @if (trim((string) ($culture['kicker'] ?? '')) !== '')
                            <p class="ac-about-kicker">{{ $culture['kicker'] }}</p>
                        @endif

                        <h2 id="ac-about-culture-title">{{ $culture['title'] ?? 'Naša kultura' }}</h2>

                        @if ($cultureQuote !== '')
                            <blockquote class="ac-about-pullquote">
                                <p>{{ $cultureQuote }}</p>
                            </blockquote>
                        @endif
                    </div>

                    <div class="ac-about-copy-stack ac-about-copy-stack--large">
                        @foreach ((array) ($culture['paragraphs'] ?? []) as $paragraph)
                            @continue(trim((string) $paragraph) === '')
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="ac-about-responsibility" aria-labelledby="ac-about-responsibility-title">
            <div class="ac-about-container">
                <div class="ac-about-responsibility-grid">
                    <div>
                        @if (trim((string) ($responsibility['kicker'] ?? '')) !== '')
                            <p class="ac-about-kicker">{{ $responsibility['kicker'] }}</p>
                        @endif

                        <h2 id="ac-about-responsibility-title">{{ $responsibility['title'] ?? '' }}</h2>

                        @if ($responsibilityQuote !== '')
                            <blockquote class="ac-about-pullquote">
                                <p>{{ $responsibilityQuote }}</p>
                            </blockquote>
                        @endif
                    </div>

                    <div>
                        <div class="ac-about-copy-stack">
                            @foreach ((array) ($responsibility['paragraphs'] ?? []) as $paragraph)
                                @continue(trim((string) $paragraph) === '')
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if ($responsibilityCtaIntro !== '' || $responsibilityCtaText !== '')
                    <div class="ac-about-wide-cta">
                        <div class="ac-about-wide-cta-copy">
                            @if ($responsibilityCtaIntro !== '')
                                <h3>{{ $responsibilityCtaIntro }}</h3>
                            @endif

                            @if ($responsibilityCtaText !== '')
                                <p>{{ $responsibilityCtaText }}</p>
                            @endif
                        </div>

                        <a href="{{ route('contact.create') }}" class="ac-about-wide-cta-link">
                            <span>{{ $responsibilityCtaLabel }}</span>
                        </a>
                    </div>
                @endif
            </div>
        </section>

        <section class="ac-about-references" aria-labelledby="ac-about-references-title">
            <div class="ac-about-container">
                <div class="ac-about-reference-head">
                    <div>
                        @if (trim((string) ($references['kicker'] ?? '')) !== '')
                            <p class="ac-about-kicker">{{ $references['kicker'] }}</p>
                        @endif

                        <h2 id="ac-about-references-title">{{ $references['title'] ?? 'Reference' }}</h2>
                    </div>

                    <div class="ac-about-copy-stack">
                        @foreach ((array) ($references['paragraphs'] ?? []) as $paragraph)
                            @continue(trim((string) $paragraph) === '')
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                </div>

                @if ($aboutReferenceItems->isNotEmpty())
                    <div class="ac-about-reference-grid">
                        @foreach ($aboutReferenceItems as $item)
                            <article class="ac-about-reference-card" aria-label="{{ $item['name'] }}">
                                <img
                                    src="{{ $item['url'] }}"
                                    alt="{{ $item['alt'] }}"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </article>
                        @endforeach
                    </div>
                @endif

                <div class="ac-about-section-actions">
                    <a href="{{ $referencePageUrl }}" class="front-action-cta ac-about-secondary-cta">
                        <span>{{ $referencesButtonLabel }}</span>
                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 12L12 4"></path>
                            <path d="M6 4h6v6"></path>
                        </svg>
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
    <style>
        .ac-about-page {
            --ac-about-bg-warm: #f6f1e7;
            --ac-about-bg-light: #fbf6ed;
            --ac-about-bg-blue: #10213a;
            --ac-about-section-line: rgba(15, 42, 67, 0.08);
            --ac-about-section-title-size: 2.18rem;
            --ac-about-section-title-line-height: 1.14;
            --ac-about-card-title-size: 1.48rem;
            --ac-about-card-title-line-height: 1.24;
            --ac-about-lead-size: 1.08rem;
            --ac-about-lead-line-height: 1.66;
            min-height: 100vh;
            background: var(--ac-about-bg-warm);
            color: #101820;
        }

        .ac-about-page p {
            margin: 0;
        }

        .ac-about-container,
        .ac-about-blocks {
            width: min(100% - 2rem, 1320px);
            margin: 0 auto;
        }

        @media (min-width: 640px) {
            .ac-about-container,
            .ac-about-blocks {
                width: min(100% - 3rem, 1272px);
            }
        }

        @media (min-width: 1024px) {
            .ac-about-container,
            .ac-about-blocks {
                width: min(100% - 4rem, 1256px);
            }
        }

        .ac-about-blocks {
            padding: 2.5rem 0 0;
        }

        .ac-about-blocks--bottom {
            padding: 2.5rem 0 4rem;
        }

        .ac-about-title-band {
            margin-bottom: 0;
            background-color: var(--ac-about-bg-warm);
            border-top-color: transparent;
            border-bottom-color: rgba(15, 42, 67, 0.08);
        }

        .ac-about-title-band .ac-page-title-copy h1 {
            color: #101820;
            font-size: 2.65rem;
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: 0;
        }

        .ac-about-title-band .ac-page-title-copy > p,
        .ac-about-title-band .front-scroll-breadcrumb-link,
        .ac-about-title-band .front-scroll-breadcrumb-current,
        .ac-about-title-band .front-scroll-breadcrumb-separator {
            color: #4f4a43;
        }

        .ac-about-title-band .ac-page-title-breadcrumb::before,
        .ac-about-title-band .ac-page-title-breadcrumb::after {
            background: rgba(120, 96, 58, 0.16);
        }

        .ac-about-page .front-action-cta {
            letter-spacing: 0;
        }

        .ac-about-hero,
        .ac-about-values,
        .ac-about-why,
        .ac-about-team,
        .ac-about-culture,
        .ac-about-responsibility,
        .ac-about-references {
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid var(--ac-about-section-line);
        }

        .ac-about-hero {
            padding: clamp(3.8rem, 6.5vw, 7rem) 0 clamp(4.8rem, 8vw, 8rem);
            background-color: var(--ac-about-bg-warm);
        }

        .ac-about-values,
        .ac-about-team,
        .ac-about-responsibility {
            padding: clamp(3rem, 6vw, 5.8rem) 0 clamp(5.2rem, 8vw, 7.4rem);
            background-color: var(--ac-about-bg-light);
        }

        .ac-about-why,
        .ac-about-culture,
        .ac-about-references {
            padding: clamp(3rem, 6vw, 5.8rem) 0 clamp(5.2rem, 8vw, 7.4rem);
            background-color: var(--ac-about-bg-warm);
        }

        .ac-about-hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 0.83fr) minmax(34rem, 1fr);
            gap: clamp(2rem, 5vw, 5rem);
            align-items: center;
        }

        .ac-about-hero-copy {
            max-width: 39rem;
        }

        .ac-about-kicker {
            margin: 0;
            color: #7c653b;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .ac-about-hero-copy h2,
        .ac-about-section-head h2,
        .ac-about-split-grid h2,
        .ac-about-team-head h2,
        .ac-about-responsibility-grid h2,
        .ac-about-reference-head h2 {
            margin: 0;
            color: #101820;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            letter-spacing: 0;
            text-wrap: balance;
        }

        .ac-about-hero-copy h2,
        .ac-about-section-head h2,
        .ac-about-split-grid h2,
        .ac-about-team-head h2,
        .ac-about-responsibility-grid h2,
        .ac-about-reference-head h2 {
            margin-top: 0.75rem;
            max-width: 18ch;
            font-size: var(--ac-about-section-title-size);
            line-height: var(--ac-about-section-title-line-height);
        }

        .ac-about-lead,
        .ac-about-card-lead,
        .ac-about-team-intro {
            margin-top: clamp(1.1rem, 1.8vw, 1.5rem) !important;
            color: #24211c;
            font-size: var(--ac-about-lead-size);
            font-weight: 700;
            line-height: var(--ac-about-lead-line-height);
        }

        .ac-about-copy-stack {
            display: grid;
            gap: 0.85rem;
            margin-top: 1rem;
        }

        .ac-about-copy-stack--large {
            margin-top: 0;
        }

        .ac-about-copy-stack p,
        .ac-about-section-head > p,
        .ac-about-wide-cta p {
            color: #403a34;
            font-size: 1rem;
            line-height: 1.76;
            text-wrap: pretty;
        }

        .ac-about-pullquote {
            position: relative;
            width: 100%;
            max-width: 29rem;
            margin: clamp(1.5rem, 2.6vw, 2.15rem) 0 0;
            padding: 0.2rem 2.7rem 0.25rem 3rem;
            text-align: left;
        }

        .ac-about-pullquote::before,
        .ac-about-pullquote::after {
            position: absolute;
            color: rgba(171, 141, 82, 0.42);
            font-family: Georgia, serif;
            font-size: 4.45rem;
            font-weight: 400;
            line-height: 1;
            pointer-events: none;
        }

        .ac-about-pullquote::before {
            content: '\201C';
            top: -0.6rem;
            left: 0;
        }

        .ac-about-pullquote::after {
            content: '\201D';
            right: 0;
            bottom: -1.6rem;
        }

        .ac-about-pullquote p {
            color: #24211c;
            font-family: 'Montserrat', sans-serif;
            font-size: 1.04rem;
            font-weight: 500;
            line-height: 1.76;
            text-wrap: balance;
        }

        .ac-about-hero-media {
            position: relative;
            justify-self: end;
            width: min(100%, 45rem);
        }

        .ac-about-image,
        .ac-about-stat-card,
        .ac-about-value-card,
        .ac-about-stat-item,
        .ac-about-member-card,
        .ac-about-wide-cta,
        .ac-about-reference-card {
            border: 1px solid rgba(171, 141, 82, 0.12);
            border-radius: 8px;
            box-shadow: none;
        }

        .ac-about-image {
            position: relative;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 22px 46px rgba(15, 42, 67, 0.12);
        }

        .ac-about-image img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ac-about-image--hero {
            width: 100%;
            aspect-ratio: 1386 / 925;
        }

        .ac-about-stat-card {
            position: absolute;
            right: clamp(0.85rem, 2.2vw, 1.35rem);
            bottom: clamp(-5.6rem, -6vw, -3.4rem);
            z-index: 2;
            display: grid;
            align-content: center;
            gap: 0.25rem;
            width: min(16rem, calc(100% - 2rem));
            padding: 1.1rem;
            background: rgba(255, 255, 255, 0.94);
            color: #101820;
            box-shadow: 0 18px 38px rgba(15, 42, 67, 0.1);
        }

        .ac-about-stat-card strong {
            color: #9a773d;
            font-size: 3rem;
            line-height: 0.95;
        }

        .ac-about-stat-card span {
            color: #403a34;
            font-size: 0.88rem;
            line-height: 1.45;
        }

        .ac-about-section-head {
            display: grid;
            justify-items: center;
            max-width: 66rem;
            margin: 0 auto clamp(2rem, 4vw, 3rem);
            text-align: center;
        }

        .ac-about-section-head > p {
            max-width: 64rem;
            margin-top: 1.35rem !important;
        }

        .ac-about-value-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        .ac-about-value-card {
            position: relative;
            display: grid;
            align-content: start;
            min-height: 31rem;
            overflow: hidden;
            padding: clamp(1.35rem, 2.3vw, 1.9rem);
            border-color: rgba(154, 119, 61, 0.18);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(255, 255, 255, 0.84) 100%);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.72);
        }

        .ac-about-value-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto;
            height: 4px;
            background: #9a773d;
            pointer-events: none;
        }

        .ac-about-value-card h3 {
            padding-bottom: 0.95rem;
            border-bottom: 1px solid rgba(15, 42, 67, 0.08);
        }

        .ac-about-value-card .ac-about-card-lead {
            margin-top: 1rem !important;
        }

        .ac-about-value-card h3,
        .ac-about-member-card h3 {
            margin: 0;
            color: #101820;
            font-family: 'Montserrat', sans-serif;
            font-size: var(--ac-about-card-title-size);
            font-weight: 700;
            line-height: var(--ac-about-card-title-line-height);
            letter-spacing: 0;
        }

        .ac-about-split-grid,
        .ac-about-team-head,
        .ac-about-responsibility-grid,
        .ac-about-reference-head {
            display: grid;
            grid-template-columns: minmax(0, 0.74fr) minmax(0, 1fr);
            gap: clamp(1.6rem, 4vw, 4rem);
            align-items: start;
        }

        .ac-about-stat-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.85rem;
            margin-top: clamp(2rem, 4vw, 3rem);
        }

        .ac-about-stat-item {
            display: grid;
            gap: 0.4rem;
            min-height: 8.5rem;
            padding: 1.1rem;
            background: rgba(255, 255, 255, 0.82);
        }

        .ac-about-stat-item strong {
            color: #9a773d;
            font-size: 2.45rem;
            line-height: 0.95;
        }

        .ac-about-stat-item span {
            color: #403a34;
            font-size: 0.9rem;
            font-weight: 700;
            line-height: 1.45;
        }

        .ac-about-member-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            align-items: stretch;
            gap: 0.85rem;
            margin-top: 1rem;
        }

        .ac-about-member-card {
            display: grid;
            grid-template-rows: auto 1fr;
            overflow: hidden;
            background: #fff;
        }

        .ac-about-member-photo {
            display: grid;
            place-items: center;
            aspect-ratio: 4 / 5.35;
            overflow: hidden;
            background: #fff;
        }

        .ac-about-member-photo img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center top;
        }

        .ac-about-member-photo span {
            color: #fff;
            font-size: 2.8rem;
            font-weight: 700;
            letter-spacing: 0.1em;
        }

        .ac-about-member-card > div:last-child {
            display: grid;
            align-content: start;
            min-height: 5.8rem;
            padding: 1rem 1rem 1.05rem;
            border-top: 1px solid rgba(15, 42, 67, 0.06);
        }

        .ac-about-member-card h3 {
            font-size: 1.02rem;
            line-height: 1.18;
        }

        .ac-about-member-card p {
            margin-top: 0.45rem;
            color: #7c653b;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            line-height: 1.45;
            text-transform: uppercase;
        }

        .ac-about-member-cta-card {
            position: relative;
            grid-template-rows: minmax(0, 1fr);
            min-height: clamp(14rem, 32vw, 31.25rem);
            border-color: rgba(154, 119, 61, 0.18);
            background: #fff;
        }

        .ac-about-member-cta-link {
            position: relative;
            z-index: 1;
            display: grid;
            height: 100%;
            min-height: 100%;
            place-items: center;
            padding: clamp(1.15rem, 2vw, 1.55rem);
            color: #fff !important;
            text-align: center;
            text-decoration: none;
        }

        .ac-about-member-cta-button {
            display: inline-flex;
            min-height: 2.9rem;
            max-width: 100%;
            align-items: center;
            justify-content: center;
            gap: 0.48rem;
            padding: 0.82rem 1.05rem;
            border: 1px solid #8a6d3b;
            border-radius: 8px;
            background: #8a6d3b;
            color: #fff;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0;
            line-height: 1.2;
            text-transform: uppercase;
            box-shadow: 0 16px 28px -22px rgba(124, 101, 59, 0.9);
            transition: transform 0.18s ease, background 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
        }

        .ac-about-member-cta-button svg {
            flex: 0 0 auto;
        }

        .ac-about-member-cta-link:hover .ac-about-member-cta-button,
        .ac-about-member-cta-link:focus-visible .ac-about-member-cta-button {
            border-color: #755a2e;
            background: #755a2e;
            box-shadow: 0 18px 32px -22px rgba(124, 101, 59, 1);
            transform: translateY(-1px);
        }

        .ac-about-section-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
            justify-content: center;
            margin-top: clamp(1.5rem, 2.6vw, 2.15rem);
        }

        .ac-about-secondary-cta {
            min-height: 2.9rem;
            min-width: 11.6rem;
            padding: 0.8rem 1.35rem;
            background: #10213a;
            color: #fff !important;
            font-size: 0.82rem;
        }

        .ac-about-secondary-cta:hover {
            background: #173b5d;
            color: #fff !important;
        }

        .ac-about-wide-cta {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 1.35rem;
            margin-top: clamp(2rem, 4vw, 3rem);
            padding: 1.45rem 1.55rem;
            border-left: 4px solid rgba(183, 150, 82, 0.76);
            background: rgba(255, 255, 255, 0.82);
            color: #101820;
            text-align: left;
        }

        .ac-about-wide-cta-copy {
            display: grid;
            gap: 0.55rem;
            max-width: 48rem;
        }

        .ac-about-wide-cta h3 {
            margin: 0;
            color: #101820;
            font-family: 'Montserrat', sans-serif;
            font-size: 1.48rem;
            font-weight: 700;
            line-height: 1.24;
            letter-spacing: 0;
            text-wrap: balance;
        }

        .ac-about-wide-cta p {
            font-size: 0.95rem;
            font-weight: 500;
            line-height: 1.66;
        }

        .front-desktop-shell .ac-about-wide-cta-link {
            display: inline-flex;
            min-height: 2.65rem;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            margin-top: 0.1rem;
            padding: 0 1.35rem;
            border: 2px solid #123250;
            border-radius: 8px;
            background: linear-gradient(180deg, #204d76 0%, #123250 100%);
            color: #ffffff;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.08em;
            line-height: 1;
            text-decoration: none;
            text-transform: uppercase;
            box-shadow: 0 14px 24px -18px rgba(18, 50, 80, 0.85);
            transition: border-color 0.18s ease, background 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
        }

        .front-desktop-shell .ac-about-wide-cta-link:hover,
        .front-desktop-shell .ac-about-wide-cta-link:focus-visible {
            border-color: rgba(183, 150, 82, 0.9);
            background: linear-gradient(180deg, #255783 0%, #153a5c 100%);
            color: #ffffff;
            box-shadow: 0 18px 30px -20px rgba(18, 50, 80, 0.95);
            transform: translateY(-1px);
        }

        .ac-about-reference-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 1rem;
            margin-top: clamp(2rem, 4vw, 3rem);
        }

        .ac-about-reference-card {
            display: grid;
            place-items: center;
            min-height: 8.25rem;
            padding: clamp(1.1rem, 1.8vw, 1.45rem);
            background: rgba(255, 255, 255, 0.86);
        }

        .ac-about-reference-card img {
            display: block;
            max-width: 100%;
            max-height: 4.95rem;
            object-fit: contain;
            opacity: 0.86;
            filter: grayscale(1) contrast(1.08);
        }

        .front-desktop-shell:has(.ac-about-page) .front-footer {
            --front-footer-bg: #071326;
            background: #071326;
        }

        @media (max-width: 1120px) {
            .ac-about-hero-grid,
            .ac-about-split-grid,
            .ac-about-team-head,
            .ac-about-responsibility-grid,
            .ac-about-reference-head {
                grid-template-columns: minmax(0, 1fr);
            }

            .ac-about-hero-copy {
                max-width: 52rem;
            }

            .ac-about-hero-media {
                width: min(100%, 50rem);
                margin: 0 auto;
            }

            .ac-about-pullquote {
                max-width: 42rem;
            }

            .ac-about-wide-cta {
                grid-template-columns: minmax(0, 1fr);
                justify-items: start;
            }

            .ac-about-value-grid,
            .ac-about-member-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .ac-about-reference-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (max-width: 820px) {
            .ac-about-stat-grid,
            .ac-about-reference-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 720px) {
            .ac-about-page {
                --ac-about-section-title-size: 1.88rem;
                --ac-about-card-title-size: 1.32rem;
                --ac-about-lead-size: 1rem;
            }

            .ac-about-container,
            .ac-about-blocks {
                width: min(100% - 1.35rem, 1320px);
            }

            .ac-about-title-band .ac-page-title-copy h1 {
                font-size: 2.1rem;
            }

            .ac-about-hero {
                padding-top: 2.4rem;
            }

            .ac-about-hero-media {
                display: grid;
                min-height: 0;
            }

            .ac-about-value-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .ac-about-stat-grid,
            .ac-about-member-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .ac-about-stat-grid {
                gap: 0.55rem;
                margin-top: 1.35rem;
            }

            .ac-about-stat-item {
                min-height: 6.15rem;
                padding: 0.78rem;
            }

            .ac-about-stat-item strong {
                font-size: 1.95rem;
            }

            .ac-about-stat-item span {
                font-size: 0.76rem;
                line-height: 1.32;
            }

            .ac-about-member-grid {
                gap: 0.55rem;
            }

            .ac-about-member-photo {
                aspect-ratio: 4 / 5.1;
            }

            .ac-about-member-card > div:last-child {
                min-height: 4.9rem;
                padding: 0.72rem;
            }

            .ac-about-member-card h3 {
                font-size: 0.88rem;
                line-height: 1.16;
            }

            .ac-about-member-card p {
                margin-top: 0.34rem;
                font-size: 0.64rem;
                line-height: 1.35;
            }

            .ac-about-member-cta-card {
                min-height: auto;
            }

            .ac-about-member-cta-button {
                min-height: 2.5rem;
                padding: 0.7rem 0.76rem;
                font-size: 0.66rem;
            }

            .ac-about-value-card {
                min-height: 0;
            }

            .ac-about-pullquote {
                max-width: 100%;
                padding: 0.05rem 2.15rem 0.2rem;
            }

            .ac-about-reference-card {
                min-height: 7.4rem;
            }

            .ac-about-pullquote::before,
            .ac-about-pullquote::after {
                font-size: 3.35rem;
            }

            .ac-about-pullquote::before {
                top: -0.35rem;
            }

            .ac-about-pullquote::after {
                bottom: -1.15rem;
            }

            .ac-about-pullquote p {
                font-size: 0.98rem;
                line-height: 1.72;
            }

            .ac-about-wide-cta {
                gap: 1rem;
                padding: 1.15rem;
            }

            .ac-about-wide-cta h3 {
                font-size: 1.18rem;
            }

            .front-desktop-shell .ac-about-wide-cta-link {
                width: 100%;
                min-height: 2.75rem;
            }

            .ac-about-section-actions {
                align-items: center;
                flex-direction: row;
            }
        }

    </style>
@endpush
