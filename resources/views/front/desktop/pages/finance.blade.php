@extends('front.desktop.layouts.store')

@php
    $captchaSiteKey = trim((string) ($storeSettings['captcha']['recaptcha_v3_site_key'] ?? ''));
    $captchaEnabled = (bool) ($storeSettings['captcha']['recaptcha_v3_enabled'] ?? false) && $captchaSiteKey !== '';
    $contactEmail = trim((string) ($storeSettings['footer']['email_support'] ?? '')) ?: 'info@alphacapitalis.com';
    $contactPhone = trim((string) ($storeSettings['footer']['phone'] ?? '')) ?: '+385 (1) 580 6656';
    $contactPhoneHref = preg_replace('/\s+/', '', $contactPhone);
    $financeSprite = asset('front-theme/fonts/sprites/solid.svg');
    $meetingFormLabels = $meetingSection['form_labels'] ?? [];
    $maSalePhases = array_values($maSection['sale']['phases'] ?? []);
    $valuationBody = array_values($valuationsSection['body'] ?? []);
    $capitalBody = array_values($capitalRaisingSection['body'] ?? []);
    $capitalBodyLead = array_slice($capitalBody, 1, 2);
    $capitalBodyTail = array_slice($capitalBody, 3);
    $restructuringBody = array_values($restructuringSection['body'] ?? []);
    $pandeaBody = array_values($pandeaSection['body'] ?? []);
    $pandeaLeadParagraph = trim((string) ($pandeaBody[0] ?? ''));
    $pandeaSecondaryParagraph = trim((string) ($pandeaBody[1] ?? ''));
    $pandeaTitle = trim((string) ($pandeaSection['title'] ?? ''));
    $pandeaHeadline = $pandeaTitle !== ''
        ? $pandeaTitle
        : trim((string) \Illuminate\Support\Str::before($pandeaLeadParagraph, ','));
    $pandeaHeadlineLines = [$pandeaHeadline];
    $isCroatianLocale = str_starts_with(strtolower((string) $locale), 'hr');
    $phaseTableLabels = $isCroatianLocale
        ? ['step' => 'Faza', 'focus' => 'Fokus', 'activities' => 'Ključne aktivnosti']
        : ['step' => 'Phase', 'focus' => 'Focus', 'activities' => 'Key activities'];
    $financeSectionMeta = [
        'ma' => [
            'number' => '01',
            'icon_view_box' => '0 0 512 512',
            'icon_href' => $financeSprite.'#building-columns',
        ],
        'due_diligence' => [
            'number' => '02',
            'icon_view_box' => '0 0 512 512',
            'icon_href' => $financeSprite.'#magnifying-glass',
        ],
        'valuations' => [
            'number' => '03',
            'icon_view_box' => '0 0 512 512',
            'icon_href' => $financeSprite.'#chart-column',
        ],
        'capital_raising' => [
            'number' => '04',
            'icon_view_box' => '0 0 576 512',
            'icon_href' => $financeSprite.'#hand-holding-dollar',
        ],
        'restructuring' => [
            'number' => '05',
            'icon_view_box' => '0 0 512 512',
            'icon_href' => $financeSprite.'#arrow-right-arrow-left',
        ],
    ];
    $financeListBullet = [
        'view_box' => '0 0 256 512',
        'href' => $financeSprite.'#angle-right',
    ];
    $financeCtaIcon = [
        'view_box' => '0 0 320 512',
        'href' => $financeSprite.'#angle-down',
    ];
    $financeAccentIcons = [
        'process' => [
            'view_box' => '0 0 384 512',
            'href' => $financeSprite.'#file-lines',
        ],
        'help' => [
            'view_box' => '0 0 512 512',
            'href' => $financeSprite.'#bullseye',
        ],
        'quote' => [
            'view_box' => '0 0 448 512',
            'href' => $financeSprite.'#quote-right',
        ],
        'methods' => [
            'view_box' => '0 0 512 512',
            'href' => $financeSprite.'#chart-line',
        ],
        'sources' => [
            'view_box' => '0 0 512 512',
            'href' => $financeSprite.'#coins',
        ],
        'options' => [
            'view_box' => '0 0 576 512',
            'href' => $financeSprite.'#sitemap',
        ],
        'reasons' => [
            'view_box' => '0 0 576 512',
            'href' => $financeSprite.'#arrow-trend-up',
        ],
        'team' => [
            'view_box' => '0 0 640 512',
            'href' => $financeSprite.'#user-group',
        ],
    ];
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
                                    <svg viewBox="{{ $financeCtaIcon['view_box'] }}" fill="currentColor" aria-hidden="true">
                                        <use href="{{ $financeCtaIcon['href'] }}"></use>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="ac-finance-network-section">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-finance-network-banner">
                    <div class="ac-finance-network-top">
                        <div class="ac-finance-network-headcard">
                            <div class="ac-finance-network-logo-card">
                                <img src="{{ $pandeaLogoUrl }}" alt="{{ $pandeaSection['logo_alt'] ?? $networkName }}" class="ac-finance-network-logo">
                            </div>

                            @if ($pandeaHeadline !== '')
                                <div class="ac-finance-network-title{{ $isCroatianLocale ? ' is-single-line' : '' }}">
                                    <h3>
                                        @foreach ($pandeaHeadlineLines as $line)
                                            <span>{{ $line }}</span>
                                        @endforeach
                                    </h3>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="ac-finance-network-columns">
                        @if ($pandeaLeadParagraph !== '')
                            <p>{{ $pandeaLeadParagraph }}</p>
                        @endif
                        @if ($pandeaSecondaryParagraph !== '')
                            <p>{{ $pandeaSecondaryParagraph }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>

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
                    @php
                        $maMeta = $financeSectionMeta['ma'];
                    @endphp
                    <article class="ac-finance-editorial-section">
                        <div class="ac-finance-editorial-head">
                            <div class="ac-finance-editorial-title">
                                <div class="ac-finance-editorial-badge">
                                    <span class="ac-finance-editorial-icon" aria-hidden="true">
                                        <svg viewBox="{{ $maMeta['icon_view_box'] }}" fill="currentColor">
                                            <use href="{{ $maMeta['icon_href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span class="ac-finance-editorial-index">{{ $maMeta['number'] }}</span>
                                </div>
                                <div class="ac-finance-editorial-heading">
                                    <h2>{{ $maSection['title'] ?? '' }}</h2>
                                </div>
                            </div>
                            <div class="ac-finance-editorial-intro">
                                <p>{{ $maSection['intro'] ?? '' }}</p>
                            </div>
                        </div>

                        <div class="ac-finance-columns ac-finance-columns--single">
                            <article class="ac-finance-column ac-finance-column--spacious">
                                <h3>{{ $maSection['sale']['title'] ?? '' }}</h3>
                                <p>{{ $maSection['sale']['body'] ?? '' }}</p>
                            </article>
                        </div>

                        @if ($maSalePhases !== [])
                            <div class="ac-finance-followup">
                                <p class="ac-family-section-kicker ac-finance-kicker-label">
                                    <span class="ac-finance-kicker-icon" aria-hidden="true">
                                        <svg viewBox="{{ $financeAccentIcons['process']['view_box'] }}" fill="currentColor">
                                            <use href="{{ $financeAccentIcons['process']['href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span>{{ $maSection['sale']['process_title'] ?? '' }}</span>
                                </p>
                                <p class="ac-finance-phase-caption">{{ $phaseTableLabels['activities'] }}</p>

                                <div class="ac-finance-phase-table-shell">
                                    <table class="ac-finance-phase-table">
                                        <caption class="sr-only">{{ $maSection['sale']['process_title'] ?? '' }}</caption>
                                        <thead>
                                            <tr>
                                                @foreach ($maSalePhases as $phase)
                                                    <th scope="col">
                                                        <span class="ac-finance-phase-step">{{ $phase['title'] ?? '' }}</span>
                                                        <p class="ac-finance-phase-focus">{{ $phase['label'] ?? '' }}</p>
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                @foreach ($maSalePhases as $phase)
                                                    <td>
                                                        <ul class="ac-finance-phase-list">
                                                            @foreach (($phase['items'] ?? []) as $item)
                                                                <li>
                                                                    <span class="ac-finance-list-bullet" aria-hidden="true">
                                                                        <svg viewBox="{{ $financeListBullet['view_box'] }}" fill="currentColor">
                                                                            <use href="{{ $financeListBullet['href'] }}"></use>
                                                                        </svg>
                                                                    </span>
                                                                    <span>{{ $item }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <div class="ac-finance-followup">
                            <div class="ac-finance-columns ac-finance-columns--single">
                                <article class="ac-finance-column ac-finance-column--spacious">
                                    <h3>{{ $maSection['acquisition']['title'] ?? '' }}</h3>
                                    <p>{{ $maSection['acquisition']['body'] ?? '' }}</p>
                                </article>
                            </div>
                        </div>
                    </article>

                    @php
                        $dueMeta = $financeSectionMeta['due_diligence'];
                    @endphp
                    <article class="ac-finance-editorial-section">
                        <div class="ac-finance-editorial-head">
                            <div class="ac-finance-editorial-title">
                                <div class="ac-finance-editorial-badge">
                                    <span class="ac-finance-editorial-icon" aria-hidden="true">
                                        <svg viewBox="{{ $dueMeta['icon_view_box'] }}" fill="currentColor">
                                            <use href="{{ $dueMeta['icon_href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span class="ac-finance-editorial-index">{{ $dueMeta['number'] }}</span>
                                </div>
                                <div class="ac-finance-editorial-heading">
                                    <h2>{{ $dueDiligenceSection['title'] ?? '' }}</h2>
                                </div>
                            </div>
                            <div class="ac-finance-editorial-intro">
                                <p>{{ $dueDiligenceSection['intro'] ?? '' }}</p>
                            </div>
                        </div>

                        <div class="ac-finance-columns ac-finance-columns--two-wide">
                            <article class="ac-finance-column">
                                <p class="ac-family-section-kicker ac-finance-kicker-label">
                                    <span class="ac-finance-kicker-icon" aria-hidden="true">
                                        <svg viewBox="{{ $financeAccentIcons['help']['view_box'] }}" fill="currentColor">
                                            <use href="{{ $financeAccentIcons['help']['href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span>{{ $dueDiligenceSection['help_title'] ?? '' }}</span>
                                </p>
                                <ul class="ac-finance-list">
                                    @foreach (($dueDiligenceSection['help_items'] ?? []) as $item)
                                        <li>
                                            <span class="ac-finance-list-bullet" aria-hidden="true">
                                                <svg viewBox="{{ $financeListBullet['view_box'] }}" fill="currentColor">
                                                    <use href="{{ $financeListBullet['href'] }}"></use>
                                                </svg>
                                            </span>
                                            <span>{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </article>

                            <article class="ac-finance-column">
                                <blockquote class="ac-finance-quote">
                                    <span class="ac-finance-quote-icon" aria-hidden="true">
                                        <svg viewBox="{{ $financeAccentIcons['quote']['view_box'] }}" fill="currentColor">
                                            <use href="{{ $financeAccentIcons['quote']['href'] }}"></use>
                                        </svg>
                                    </span>
                                    <p>{{ $dueDiligenceSection['closing'] ?? '' }}</p>
                                </blockquote>
                            </article>
                        </div>
                    </article>

                    @php
                        $valuationMeta = $financeSectionMeta['valuations'];
                    @endphp
                    <article class="ac-finance-editorial-section">
                        <div class="ac-finance-editorial-head">
                            <div class="ac-finance-editorial-title">
                                <div class="ac-finance-editorial-badge">
                                    <span class="ac-finance-editorial-icon" aria-hidden="true">
                                        <svg viewBox="{{ $valuationMeta['icon_view_box'] }}" fill="currentColor">
                                            <use href="{{ $valuationMeta['icon_href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span class="ac-finance-editorial-index">{{ $valuationMeta['number'] }}</span>
                                </div>
                                <div class="ac-finance-editorial-heading">
                                    <h2>{{ $valuationsSection['title'] ?? '' }}</h2>
                                </div>
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
                                <p class="ac-family-section-kicker ac-finance-kicker-label">
                                    <span class="ac-finance-kicker-icon" aria-hidden="true">
                                        <svg viewBox="{{ $financeAccentIcons['methods']['view_box'] }}" fill="currentColor">
                                            <use href="{{ $financeAccentIcons['methods']['href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span>{{ $valuationsSection['methods_title'] ?? '' }}</span>
                                </p>
                                <ul class="ac-finance-list">
                                    @foreach (($valuationsSection['methods'] ?? []) as $method)
                                        <li>
                                            <span class="ac-finance-list-bullet" aria-hidden="true">
                                                <svg viewBox="{{ $financeListBullet['view_box'] }}" fill="currentColor">
                                                    <use href="{{ $financeListBullet['href'] }}"></use>
                                                </svg>
                                            </span>
                                            <span>{{ $method }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </article>
                        </div>
                    </article>

                    @php
                        $capitalMeta = $financeSectionMeta['capital_raising'];
                    @endphp
                    <article class="ac-finance-editorial-section">
                        <div class="ac-finance-editorial-head">
                            <div class="ac-finance-editorial-title">
                                <div class="ac-finance-editorial-badge">
                                    <span class="ac-finance-editorial-icon" aria-hidden="true">
                                        <svg viewBox="{{ $capitalMeta['icon_view_box'] }}" fill="currentColor">
                                            <use href="{{ $capitalMeta['icon_href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span class="ac-finance-editorial-index">{{ $capitalMeta['number'] }}</span>
                                </div>
                                <div class="ac-finance-editorial-heading">
                                    <h2>{{ $capitalRaisingSection['title'] ?? '' }}</h2>
                                </div>
                            </div>
                            <div class="ac-finance-editorial-intro">
                                <p>{{ $capitalBody[0] ?? '' }}</p>
                            </div>
                        </div>

                        <div class="ac-finance-columns ac-finance-columns--single">
                            <article class="ac-finance-column ac-finance-column--spacious">
                                @foreach ($capitalBodyLead as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </article>
                        </div>

                        <div class="ac-finance-followup">
                            <div class="ac-finance-columns ac-finance-columns--two-wide">
                                <article class="ac-finance-column">
                                    <p class="ac-family-section-kicker ac-finance-kicker-label">
                                        <span class="ac-finance-kicker-icon" aria-hidden="true">
                                            <svg viewBox="{{ $financeAccentIcons['sources']['view_box'] }}" fill="currentColor">
                                                <use href="{{ $financeAccentIcons['sources']['href'] }}"></use>
                                            </svg>
                                        </span>
                                        <span>{{ $capitalRaisingSection['sources_title'] ?? '' }}</span>
                                    </p>
                                    <ul class="ac-finance-list">
                                        @foreach (($capitalRaisingSection['sources'] ?? []) as $source)
                                            <li>
                                                <span class="ac-finance-list-bullet" aria-hidden="true">
                                                    <svg viewBox="{{ $financeListBullet['view_box'] }}" fill="currentColor">
                                                        <use href="{{ $financeListBullet['href'] }}"></use>
                                                    </svg>
                                                </span>
                                                <span>{{ $source }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </article>

                                <article class="ac-finance-column">
                                    @foreach ($capitalBodyTail as $paragraph)
                                        <p>{{ $paragraph }}</p>
                                    @endforeach
                                </article>
                            </div>
                        </div>

                        @if ($capitalBodyLead === [] && $capitalBodyTail === [])
                            <div class="ac-finance-followup">
                                <div class="ac-finance-columns ac-finance-columns--single">
                                    <article class="ac-finance-column">
                                        <p class="ac-family-section-kicker ac-finance-kicker-label">
                                            <span class="ac-finance-kicker-icon" aria-hidden="true">
                                                <svg viewBox="{{ $financeAccentIcons['sources']['view_box'] }}" fill="currentColor">
                                                    <use href="{{ $financeAccentIcons['sources']['href'] }}"></use>
                                                </svg>
                                            </span>
                                            <span>{{ $capitalRaisingSection['sources_title'] ?? '' }}</span>
                                        </p>
                                        <ul class="ac-finance-list">
                                            @foreach (($capitalRaisingSection['sources'] ?? []) as $source)
                                                <li>
                                                    <span class="ac-finance-list-bullet" aria-hidden="true">
                                                        <svg viewBox="{{ $financeListBullet['view_box'] }}" fill="currentColor">
                                                            <use href="{{ $financeListBullet['href'] }}"></use>
                                                        </svg>
                                                    </span>
                                                    <span>{{ $source }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </article>
                                </div>
                            </div>
                        @endif
                    </article>

                    @php
                        $restructuringMeta = $financeSectionMeta['restructuring'];
                    @endphp
                    <article class="ac-finance-editorial-section">
                        <div class="ac-finance-editorial-head">
                            <div class="ac-finance-editorial-title">
                                <div class="ac-finance-editorial-badge">
                                    <span class="ac-finance-editorial-icon" aria-hidden="true">
                                        <svg viewBox="{{ $restructuringMeta['icon_view_box'] }}" fill="currentColor">
                                            <use href="{{ $restructuringMeta['icon_href'] }}"></use>
                                        </svg>
                                    </span>
                                    <span class="ac-finance-editorial-index">{{ $restructuringMeta['number'] }}</span>
                                </div>
                                <div class="ac-finance-editorial-heading">
                                    <h2>{{ $restructuringSection['title'] ?? '' }}</h2>
                                </div>
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

                        <div class="ac-finance-followup">
                            <div class="ac-finance-restructuring-stack">
                                <article class="ac-finance-column">
                                    <p class="ac-family-section-kicker ac-finance-kicker-label">
                                        <span class="ac-finance-kicker-icon" aria-hidden="true">
                                            <svg viewBox="{{ $financeAccentIcons['options']['view_box'] }}" fill="currentColor">
                                                <use href="{{ $financeAccentIcons['options']['href'] }}"></use>
                                            </svg>
                                        </span>
                                        <span>{{ $restructuringSection['options_title'] ?? '' }}</span>
                                    </p>
                                    <ul class="ac-finance-list ac-finance-list--multi">
                                        @foreach (($restructuringSection['options'] ?? []) as $item)
                                            <li>
                                                <span class="ac-finance-list-bullet" aria-hidden="true">
                                                    <svg viewBox="{{ $financeListBullet['view_box'] }}" fill="currentColor">
                                                        <use href="{{ $financeListBullet['href'] }}"></use>
                                                    </svg>
                                                </span>
                                                <span>{{ $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </article>

                                <article class="ac-finance-column">
                                    <p class="ac-family-section-kicker ac-finance-kicker-label">
                                        <span class="ac-finance-kicker-icon" aria-hidden="true">
                                            <svg viewBox="{{ $financeAccentIcons['reasons']['view_box'] }}" fill="currentColor">
                                                <use href="{{ $financeAccentIcons['reasons']['href'] }}"></use>
                                            </svg>
                                        </span>
                                        <span>{{ $restructuringSection['reasons_title'] ?? '' }}</span>
                                    </p>
                                    <ul class="ac-finance-list ac-finance-list--multi">
                                        @foreach (($restructuringSection['reasons'] ?? []) as $item)
                                            <li>
                                                <span class="ac-finance-list-bullet" aria-hidden="true">
                                                    <svg viewBox="{{ $financeListBullet['view_box'] }}" fill="currentColor">
                                                        <use href="{{ $financeListBullet['href'] }}"></use>
                                                    </svg>
                                                </span>
                                                <span>{{ $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </article>

                                <article class="ac-finance-column">
                                    <p class="ac-family-section-kicker ac-finance-kicker-label">
                                        <span class="ac-finance-kicker-icon" aria-hidden="true">
                                            <svg viewBox="{{ $financeAccentIcons['team']['view_box'] }}" fill="currentColor">
                                                <use href="{{ $financeAccentIcons['team']['href'] }}"></use>
                                            </svg>
                                        </span>
                                        <span>{{ $restructuringSection['team_services_title'] ?? '' }}</span>
                                    </p>
                                    <ul class="ac-finance-list ac-finance-list--multi ac-finance-list--triple">
                                        @foreach (($restructuringSection['team_services'] ?? []) as $item)
                                            <li>
                                                <span class="ac-finance-list-bullet" aria-hidden="true">
                                                    <svg viewBox="{{ $financeListBullet['view_box'] }}" fill="currentColor">
                                                        <use href="{{ $financeListBullet['href'] }}"></use>
                                                    </svg>
                                                </span>
                                                <span>{{ $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </article>
                            </div>
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
                                <p class="ac-finance-visit-line">{{ $meetingSection['visit_lines'][0] ?? '' }}</p>
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

        @if (($financePosts ?? collect())->isNotEmpty())
            <section class="ac-support-story ac-home-blog ac-blog-related-section ac-family-blog-section" aria-labelledby="ac-finance-blog-title">
                <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                    <div class="ac-support-story-hero">
                        <div class="ac-support-story-shell">
                            <div class="ac-services-head ac-support-story-head">
                                <h2 id="ac-finance-blog-title">
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
                        <div id="ac-finance-blog-splide" class="splide ac-home-blog-splide" data-finance-blog-splide>
                            <div class="splide__track">
                                <ul class="splide__list">
                                    @foreach ($financePosts as $post)
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
        .ac-finance-network-section {
            position: relative;
            overflow: hidden;
            margin-top: 0;
            padding: clamp(1.45rem, 2.7vw, 2rem) 0 clamp(1.9rem, 3.2vw, 2.6rem);
            background: linear-gradient(90deg, rgba(225, 233, 242, 0.9) 0%, rgba(247, 249, 252, 0.98) 16%, rgba(249, 250, 252, 0.98) 84%, rgba(225, 233, 242, 0.9) 100%);
        }

        .ac-finance-network-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                repeating-linear-gradient(90deg, rgba(118, 145, 178, 0.11) 0 1px, transparent 1px 28px),
                linear-gradient(90deg, rgba(223, 231, 241, 0.82) 0%, rgba(249, 250, 252, 0.96) 18%, rgba(249, 250, 252, 0.96) 82%, rgba(223, 231, 241, 0.82) 100%);
            opacity: 1;
            pointer-events: none;
        }

        .ac-finance-network-section::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 50% 28%, rgba(255, 255, 255, 0.72) 0%, rgba(255, 255, 255, 0) 42%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0) 24%, rgba(210, 222, 236, 0.16) 100%);
            pointer-events: none;
        }

        .ac-finance-network-banner {
            margin-top: 0;
            position: relative;
            z-index: 1;
            display: grid;
            gap: clamp(1.2rem, 2.1vw, 1.65rem);
            padding: 0;
            border: none;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
        }

        .ac-finance-network-top {
            display: grid;
            gap: clamp(0.75rem, 1.6vw, 1.1rem);
            justify-items: center;
            text-align: center;
        }

        .ac-finance-network-headcard {
            display: inline-grid;
            grid-template-columns: auto auto;
            align-items: center;
            gap: clamp(0.9rem, 1.8vw, 1.25rem);
            width: fit-content;
            max-width: 100%;
            padding: clamp(1rem, 1.9vw, 1.35rem) clamp(1rem, 2.5vw, 1.85rem);
            border-radius: 1.65rem;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.82) 0%, rgba(244, 248, 251, 0.94) 100%);
            border: 1px solid rgba(132, 150, 176, 0.2);
        }

        .ac-finance-network-title {
            width: 100%;
            min-width: 0;
        }

        .ac-finance-network-title.is-single-line {
            width: auto;
        }

        .ac-finance-network-title h3 {
            margin: 0;
            max-width: none;
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.32rem, 1.7vw, 1.82rem);
            line-height: 1.06;
            font-weight: 600;
            color: #16263d;
            text-wrap: balance;
            letter-spacing: -0.025em;
            text-align: left;
        }

        .ac-finance-network-title.is-single-line h3 {
            max-width: none;
            white-space: nowrap;
        }

        .ac-finance-network-title h3 span {
            display: block;
        }

        .ac-finance-network-divider {
            display: block;
            flex: 1 1 3.5rem;
            min-width: 2.5rem;
            height: 1px;
            background: linear-gradient(90deg, rgba(151, 121, 65, 0.22) 0%, rgba(151, 121, 65, 0.08) 100%);
        }

        .ac-finance-network-title-note {
            flex: 0 1 15rem;
            max-width: 15rem;
            margin: 0;
            font-size: 0.86rem;
            line-height: 1.5;
            color: #41546b;
        }

        .ac-finance-network-columns {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.95rem 1.5rem;
            width: 100%;
            max-width: 70rem;
            margin: 0 auto;
            align-items: start;
        }

        .ac-finance-network-columns p {
            margin: 0;
            font-size: 0.94rem;
            line-height: 1.74;
            color: #26384c;
            text-align: left;
            white-space: pre-line;
        }

        .ac-finance-network-logo-card {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0.9rem 1.15rem;
            border-radius: 1.15rem;
            background: linear-gradient(180deg, #1b2d45 0%, #223754 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .ac-finance-network-logo {
            width: min(100%, 9.5rem);
            max-width: 9.5rem;
            height: auto;
            object-fit: contain;
            filter: none;
        }

        .ac-finance-editorial-wrap {
            position: relative;
            overflow: hidden;
            padding: clamp(1.5rem, 2.5vw, 2rem) 0 clamp(1.7rem, 2.6vw, 2.2rem);
            background: linear-gradient(180deg, #f4f1eb 0%, #f8f6f1 100%);
        }

        .ac-finance-editorial-wrap::before {
            content: '';
            position: absolute;
            inset: 0;
            background: repeating-linear-gradient(90deg, rgba(53, 45, 35, 0.018) 0 1px, transparent 1px 180px);
            opacity: 0.2;
            pointer-events: none;
        }

        .ac-finance-editorial-wrap > .mx-auto {
            position: relative;
            z-index: 1;
        }

        .ac-finance-editorial-wrap .ac-support-story-head {
            max-width: 54rem;
            margin: 0 auto;
            padding-top: clamp(0.4rem, 0.9vw, 0.7rem);
            text-align: center;
        }

        .ac-finance-editorial-wrap .ac-services-eyebrow {
            justify-content: center;
        }

        .ac-finance-editorial-wrap .ac-services-eyebrow-line {
            display: none;
        }

        .ac-finance-editorial-wrap .ac-services-kicker {
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

        .ac-finance-editorial-wrap .ac-services-intro {
            max-width: 48rem;
            margin-right: auto;
            margin-left: auto;
            color: #57534e;
        }

        .ac-finance-editorial-wrap .ac-services-divider {
            max-width: 32rem;
            margin: 1.7rem auto 0;
        }

        .ac-finance-editorial-wrap .ac-services-divider-line {
            background: rgba(120, 96, 58, 0.18);
        }

        .ac-finance-editorial-wrap .ac-services-divider-glyph {
            width: 2.55rem;
            height: 2.55rem;
            border: 1px solid rgba(171, 141, 82, 0.28);
            background: rgba(255, 255, 255, 0.78);
        }

        .ac-finance-editorial-shell {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 0;
            margin-top: clamp(1.8rem, 3vw, 2.35rem);
        }

        .ac-finance-editorial-section {
            width: 100%;
            min-width: 0;
            padding: clamp(2.2rem, 3vw, 3rem) 0;
        }

        .ac-finance-editorial-section + .ac-finance-editorial-section {
            padding-top: clamp(2.5rem, 3.4vw, 3.2rem);
            border-top: 1px solid rgba(120, 96, 58, 0.14);
        }

        .ac-finance-editorial-section:last-child {
            padding-bottom: 0;
        }

        .ac-finance-editorial-head {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 1.75rem;
            align-items: start;
        }

        .ac-finance-editorial-title {
            display: flex;
            gap: 1.15rem;
            align-items: flex-start;
            min-width: 0;
        }

        .ac-finance-editorial-badge {
            display: grid;
            gap: 0.65rem;
            justify-items: center;
            flex: none;
        }

        .ac-finance-editorial-icon {
            display: inline-flex;
            width: 3.6rem;
            height: 3.6rem;
            flex: none;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.82);
            color: #211d1a;
            border: 1px solid rgba(171, 141, 82, 0.24);
        }

        .ac-finance-editorial-index {
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

        .ac-finance-editorial-icon svg {
            width: 1.55rem;
            height: 1.55rem;
        }

        .ac-finance-editorial-heading {
            min-width: 0;
        }

        .ac-finance-editorial-title h2 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.6rem, 2.3vw, 2.3rem);
            line-height: 1.15;
            font-weight: 600;
            color: #0f172a;
            text-wrap: balance;
            overflow-wrap: anywhere;
        }

        .ac-finance-editorial-intro {
            min-width: 0;
            max-width: 40rem;
            font-size: 0.98rem;
            line-height: 1.7;
            color: #403a34;
            text-wrap: pretty;
        }

        .ac-finance-columns {
            display: grid;
            gap: 1.35rem 2rem;
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

        .ac-finance-columns--single {
            grid-template-columns: minmax(0, 1fr);
        }

        .ac-finance-column {
            min-width: 0;
            padding-top: 0.15rem;
        }

        .ac-finance-column--spacious {
            max-width: none;
        }

        .ac-finance-column h3 {
            font-size: 1.12rem;
            font-weight: 700;
            line-height: 1.5rem;
            color: #0f172a;
            font-family: 'Sora', 'Public Sans', 'Segoe UI', Arial, sans-serif;
            letter-spacing: -0.02em;
        }

        .ac-finance-column p {
            margin-top: 0.85rem;
            font-size: 0.98rem;
            line-height: 1.72;
            color: #403a34;
            overflow-wrap: anywhere;
        }

        .ac-finance-column p + p {
            margin-top: 0.95rem;
        }

        .ac-finance-list {
            margin-top: 1rem;
            display: grid;
            gap: 0.8rem;
            padding-left: 0;
            list-style: none;
            color: #403a34;
        }

        .ac-finance-list--multi {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.95rem 1.9rem;
        }

        .ac-finance-list--triple {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .ac-finance-list li,
        .ac-finance-phase-list li {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: 0.72rem;
            align-items: start;
        }

        .ac-finance-list li {
            line-height: 1.7rem;
        }

        .ac-finance-followup {
            margin-top: 1.75rem;
            min-width: 0;
            max-width: 100%;
        }

        .ac-finance-restructuring-stack {
            display: grid;
            gap: 1.35rem;
            min-width: 0;
        }

        .ac-finance-kicker-label {
            display: inline-grid;
            grid-template-columns: auto minmax(0, 1fr);
            align-items: center;
            gap: 0.6rem;
            max-width: 100%;
            font-family: 'Sora', 'Public Sans', 'Segoe UI', Arial, sans-serif;
        }

        .ac-finance-kicker-label span:last-child {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .ac-finance-kicker-icon {
            display: inline-flex;
            width: 1.7rem;
            height: 1.7rem;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid rgba(171, 141, 82, 0.22);
            background: rgba(255, 255, 255, 0.56);
            color: #6d5633;
        }

        .ac-finance-kicker-icon svg {
            width: 0.8rem;
            height: 0.8rem;
        }

        .ac-finance-phase-caption {
            margin-top: 0.65rem;
            font-size: 0.74rem;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #6b6258;
            font-family: 'Sora', 'Public Sans', 'Segoe UI', Arial, sans-serif;
        }

        .ac-finance-phase-table-shell {
            width: 100%;
            min-width: 0;
            overflow-x: auto;
            max-width: 100%;
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.76);
            border: 1px solid rgba(171, 141, 82, 0.12);
            -webkit-overflow-scrolling: touch;
        }

        .ac-finance-phase-table {
            width: 100%;
            min-width: 68rem;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
        }

        .ac-finance-phase-table thead th {
            padding: 1.15rem 1.15rem 0.85rem;
            background: transparent;
            border-bottom: 1px solid rgba(171, 141, 82, 0.16);
            border-right: 1px solid rgba(171, 141, 82, 0.12);
            font-size: 0.76rem;
            font-weight: 600;
            text-align: left;
            color: #3f3830;
        }

        .ac-finance-phase-table thead th:last-child,
        .ac-finance-phase-table tbody td:last-child {
            border-right: none;
        }

        .ac-finance-phase-table tbody td {
            padding: 0.7rem 1.15rem 1.25rem;
            vertical-align: top;
            border-right: 1px solid rgba(171, 141, 82, 0.12);
        }

        .ac-finance-phase-step {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #8a6a38;
            margin-bottom: 0.5rem;
        }

        .ac-finance-phase-focus {
            font-size: 0.94rem;
            font-weight: 700;
            line-height: 1.5;
            color: #201c18;
            font-family: 'Sora', 'Public Sans', 'Segoe UI', Arial, sans-serif;
            letter-spacing: -0.02em;
        }

        .ac-finance-phase-list {
            display: grid;
            gap: 0.65rem;
            padding-left: 0;
            list-style: none;
            color: #403a34;
        }

        .ac-finance-phase-list li {
            line-height: 1.62rem;
        }

        .ac-finance-list-bullet {
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

        .ac-finance-list-bullet svg {
            width: 0.42rem;
            height: 0.68rem;
        }

        .ac-finance-list li span:last-child,
        .ac-finance-phase-focus,
        .ac-finance-phase-list li span:last-child,
        .ac-finance-visit-line {
            overflow-wrap: anywhere;
        }

        .ac-finance-quote {
            position: relative;
            margin: 0;
            padding: 1.2rem 1.35rem 1.2rem 3.5rem;
            border-left: 2px solid rgba(171, 141, 82, 0.26);
            background: rgba(255, 255, 255, 0.58);
        }

        .ac-finance-quote-icon {
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

        .ac-finance-quote-icon svg {
            width: 1rem;
            height: 1rem;
        }

        .ac-finance-quote p {
            margin-top: 0;
            font-size: 1rem;
            line-height: 1.82;
            color: #2d2925;
        }

        .ac-finance-page .ac-home-blog-card,
        .ac-finance-page .ac-home-blog-card-link,
        .ac-finance-page .ac-finance-phase-table-shell {
            box-shadow: none;
        }

        .ac-finance-page #finance-sastanak {
            margin-top: clamp(1.2rem, 2vw, 1.6rem);
        }

        .ac-finance-page .front-contact-input:focus,
        .ac-finance-page .front-contact-textarea:focus {
            box-shadow: none;
            outline: 2px solid rgba(171, 141, 82, 0.22);
            outline-offset: 0;
        }

        @media (min-width: 960px) {
            .ac-finance-editorial-head {
                grid-template-columns: minmax(0, 0.86fr) minmax(0, 1fr);
                gap: 2.4rem;
            }
        }

        @media (max-width: 900px) {
            .ac-finance-network-title h3,
            .ac-finance-network-columns p {
                max-width: none;
            }

            .ac-finance-network-title.is-single-line h3 {
                white-space: normal;
            }

            .ac-finance-network-title {
                width: 100%;
            }

            .ac-finance-network-headcard {
                width: min(100%, 42rem);
            }

            .ac-finance-network-columns {
                grid-template-columns: minmax(0, 1fr);
                gap: 1rem;
            }

            .ac-finance-network-logo-card {
                justify-self: center;
            }

            .ac-finance-columns--two,
            .ac-finance-columns--two-wide,
            .ac-finance-columns--three {
                grid-template-columns: minmax(0, 1fr);
            }

            .ac-finance-list--multi,
            .ac-finance-list--triple {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        @media (max-width: 640px) {
            .ac-finance-network-section {
                margin-top: 0;
                padding: 1.15rem 0 1.55rem;
            }

            .ac-finance-editorial-wrap {
                padding: 2.1rem 0;
            }

            .ac-finance-editorial-section {
                padding: 1.7rem 0;
                border-radius: 0;
            }

            .ac-finance-editorial-section + .ac-finance-editorial-section {
                padding-top: 2rem;
            }

            .ac-finance-editorial-title {
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                align-items: flex-start;
                gap: 0.9rem;
            }

            .ac-finance-editorial-badge {
                gap: 0.45rem;
                justify-items: start;
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

            .ac-finance-network-title h3 {
                font-size: 1.42rem;
            }

            .ac-finance-network-title h3 span {
                display: inline;
            }

            .ac-finance-network-headcard {
                width: 100%;
                grid-template-columns: minmax(0, 1fr);
                justify-items: center;
                gap: 0.75rem;
                padding: 0.95rem 0.9rem 1rem;
                border-radius: 1.25rem;
            }

            .ac-finance-network-title h3 {
                text-align: center;
            }

            .ac-finance-network-columns p {
                font-size: 0.91rem;
                line-height: 1.64;
            }

            .ac-finance-network-logo-card {
                padding: 0.72rem 0.95rem;
                border-radius: 0.9rem;
            }

            .ac-finance-network-logo {
                width: min(100%, 8.7rem);
                max-width: 8.7rem;
            }

            .ac-finance-column {
                padding-top: 0.1rem;
            }

            .ac-finance-kicker-label {
                width: 100%;
                align-items: start;
            }

            .ac-finance-visit-line {
                white-space: normal;
            }

            .ac-finance-phase-table {
                min-width: 56rem;
            }

            .ac-finance-phase-table thead th,
            .ac-finance-phase-table tbody td {
                padding-right: 1rem;
                padding-left: 1rem;
            }
        }

        @media (min-width: 901px) and (max-width: 1180px) {
            .ac-finance-list--triple {
                grid-template-columns: repeat(2, minmax(0, 1fr));
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
            const section = document.getElementById('finance-sastanak');

            if (shouldFocusSection && section) {
                requestAnimationFrame(function () {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            }

            const initFinanceBlogSlider = function () {
                if (typeof window.Splide !== 'function') {
                    return false;
                }

                document.querySelectorAll('[data-finance-blog-splide]').forEach(function (el) {
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

            if (initFinanceBlogSlider()) {
                return;
            }

            let attempts = 0;
            const timer = window.setInterval(function () {
                attempts += 1;
                if (initFinanceBlogSlider() || attempts > 40) {
                    window.clearInterval(timer);
                }
            }, 120);
        }());
    </script>
@endpush
