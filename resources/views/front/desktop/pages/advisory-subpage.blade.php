@extends('front.desktop.layouts.store')

@php
    $content = (array) ($advisoryContent ?? []);
    $subpage = (array) ($subpage ?? []);
    $type = (string) ($subpage['type'] ?? 'financial');
    $heroSection = (array) ($heroSection ?? []);
    $pandea = (array) ($content['pandea'] ?? []);
    $funding = (array) ($content['funding'] ?? []);
    $sourceModules = (array) ($content['source_modules'] ?? []);
    $bankLoans = (array) ($content['bank_loans'] ?? []);
    $zopu = (array) ($content['zopu'] ?? []);
    $ma = (array) ($content['ma'] ?? []);
    $valuations = (array) ($content['valuations'] ?? []);
    $dueDiligence = (array) ($content['due_diligence'] ?? []);
    $tax = (array) ($content['tax'] ?? []);
    $meeting = (array) ($content['meeting'] ?? []);
    $currentHost = request()->getHost();
    $sameOriginAssetUrl = static function (?string $url) use ($currentHost): string {
        $assetUrl = trim((string) $url);
        $assetHost = parse_url($assetUrl, PHP_URL_HOST);

        if ($assetUrl === '' || ($assetHost !== null && $assetHost !== $currentHost)) {
            return $assetUrl;
        }

        $assetPath = parse_url($assetUrl, PHP_URL_PATH);
        $assetQuery = parse_url($assetUrl, PHP_URL_QUERY);

        return is_string($assetPath) && $assetPath !== ''
            ? $assetPath.($assetQuery ? '?'.$assetQuery : '')
            : $assetUrl;
    };
    $resolveContentUrl = static function (?string $url): string {
        $target = trim((string) $url);

        if ($target === '' || str_starts_with($target, '#') || str_starts_with($target, 'http://') || str_starts_with($target, 'https://')) {
            return $target;
        }

        return url(str_starts_with($target, '/') ? $target : '/'.$target);
    };
    $heroImageUrl = $sameOriginAssetUrl((string) $heroBackgroundUrl);
@endphp

@section('title', $servicePageMetaTitle !== '' ? $servicePageMetaTitle : ($servicePageTitle ?? 'Savjetovanje'))
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-family-business-page ac-audit-page ac-advisory-page">
        <section class="ac-family-hero ac-service-hero ac-service-hero--advisory">
            <div class="ac-family-hero-media" aria-hidden="true" style="--audit-hero-image: url('{{ $heroImageUrl }}'); background-image: url('{{ $heroImageUrl }}');">
                <img src="{{ $heroImageUrl }}" alt="" class="ac-family-hero-media-image" loading="eager" decoding="async">
            </div>
            <div class="ac-family-hero-overlay"></div>

            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-family-hero-content">
                    <div class="ac-family-hero-shell">
                        <div class="ac-family-hero-copy ac-service-hero-card">
                            <h1 class="ac-family-hero-title">
                                <span class="is-brand">{{ $heroSection['brand_title'] ?? 'ALPHA CAPITALIS' }}</span>
                                <span class="is-subtitle">
                                    <span class="is-subtitle-lead">{{ $heroSection['subtitle_lead'] ?? ($subpage['title'] ?? 'Savjetovanje') }}</span>
                                </span>
                            </h1>
                            <p class="ac-family-hero-intro">{{ $heroSection['intro'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="ac-audit-editorial-wrap" aria-labelledby="ac-advisory-subpage-title">
            <div class="mx-auto w-full max-w-[1120px] px-5 lg:px-8">
                <div class="ac-audit-editorial-shell">
                    @if ($type === 'funding')
                        <article class="ac-audit-editorial-section">
                            <div class="ac-audit-section-head ac-audit-section-head--center">
                                <p class="ac-family-section-kicker">PRIBAVLJANJE FINANCIRANJA</p>
                                <h2 id="ac-advisory-subpage-title">{{ $funding['title'] ?? 'Pribavljanje financiranja' }}</h2>
                            </div>

                            <div class="ac-advisory-three-grid">
                                @foreach ((array) ($funding['cards'] ?? []) as $card)
                                    @php $cardUrl = $resolveContentUrl($card['url'] ?? ''); @endphp
                                    <article class="ac-audit-service-card ac-advisory-link-card">
                                        <h3>{{ $card['title'] ?? '' }}</h3>
                                        <p>{{ $card['text'] ?? '' }}</p>
                                        @if ($cardUrl !== '')
                                            <a href="{{ $cardUrl }}" class="ac-advisory-card-link">{{ str_starts_with(strtolower((string) $locale), 'hr') ? 'Opširnije' : 'Read more' }}</a>
                                        @endif
                                    </article>
                                @endforeach
                            </div>

                            <div class="ac-advisory-feature-block">
                                <h3>{{ $funding['overview_title'] ?? 'EU fondovi' }}</h3>
                                @foreach ((array) ($funding['overview_body'] ?? []) as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </div>

                            <div class="ac-advisory-detail-grid">
                                @foreach ((array) ($funding['services'] ?? []) as $item)
                                    <article class="ac-advisory-detail-card">
                                        <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                        <h4>{{ $item['title'] ?? '' }}</h4>
                                        <p>{{ $item['text'] ?? '' }}</p>
                                    </article>
                                @endforeach
                            </div>
                        </article>

                        <article class="ac-audit-editorial-section">
                            <div class="ac-advisory-two-col">
                                <section class="ac-advisory-text-panel">
                                    <h2>{{ $bankLoans['title'] ?? 'Bankovni krediti' }}</h2>
                                    @foreach ((array) ($bankLoans['body'] ?? []) as $paragraph)
                                        <p>{{ $paragraph }}</p>
                                    @endforeach
                                </section>
                                <section class="ac-advisory-text-panel">
                                    <h2>{{ $zopu['title'] ?? 'Zakon o poticanju ulaganja' }}</h2>
                                    @foreach ((array) ($zopu['body'] ?? []) as $paragraph)
                                        <p>{{ $paragraph }}</p>
                                    @endforeach
                                </section>
                            </div>
                        </article>

                        <article class="ac-audit-editorial-section">
                            <div class="ac-audit-section-head ac-audit-section-head--center">
                                <p class="ac-family-section-kicker">{{ $sourceModules['kicker'] ?? 'DOSTUPNI IZVORI FINANCIRANJA' }}</p>
                                <h2>{{ $sourceModules['title'] ?? '' }}</h2>
                                <p>{{ $sourceModules['intro'] ?? '' }}</p>
                            </div>
                            <div class="ac-advisory-module-grid">
                                @foreach ((array) ($sourceModules['items'] ?? []) as $module)
                                    @php $moduleUrl = $resolveContentUrl($module['url'] ?? ''); @endphp
                                    <article class="ac-advisory-source-card">
                                        <h3>{{ $module['title'] ?? '' }}</h3>
                                        <p>{{ $module['text'] ?? '' }}</p>
                                        @if ($moduleUrl !== '')
                                            <a href="{{ $moduleUrl }}" class="ac-advisory-card-link">{{ str_starts_with(strtolower((string) $locale), 'hr') ? 'Opširnije' : 'Read more' }}</a>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        </article>
                    @elseif ($type === 'tax')
                        <article class="ac-audit-editorial-section">
                            <div class="ac-audit-section-head ac-audit-section-head--center">
                                <p class="ac-family-section-kicker">POREZNO SAVJETOVANJE</p>
                                <h2 id="ac-advisory-subpage-title">{{ $tax['title'] ?? 'Porezno savjetovanje' }}</h2>
                            </div>

                            <div class="ac-advisory-feature-block">
                                <h3>{{ $tax['overview_title'] ?? 'Što je porezno savjetovanje?' }}</h3>
                                @foreach ((array) ($tax['overview_body'] ?? []) as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </div>

                            <div class="ac-advisory-detail-grid">
                                @foreach ((array) ($tax['services'] ?? []) as $item)
                                    <article class="ac-advisory-detail-card">
                                        <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                        <h4>{{ $item['title'] ?? '' }}</h4>
                                        <p>{{ $item['text'] ?? '' }}</p>
                                    </article>
                                @endforeach
                            </div>
                        </article>

                        <article class="ac-audit-editorial-section">
                            <div class="ac-advisory-service-grid">
                                @foreach ((array) ($tax['cards'] ?? []) as $card)
                                    <article class="ac-audit-service-card">
                                        <h3>{{ $card['title'] ?? '' }}</h3>
                                        <p>{{ $card['text'] ?? '' }}</p>
                                    </article>
                                @endforeach
                            </div>
                            <div class="ac-advisory-feature-block">
                                <h3>{{ $tax['approach_title'] ?? '' }}</h3>
                                @foreach ((array) ($tax['approach_body'] ?? []) as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </div>
                        </article>
                    @else
                        <article class="ac-audit-editorial-section">
                            <div class="ac-audit-section-head ac-audit-section-head--center">
                                <p class="ac-family-section-kicker">FINANCIJSKO SAVJETOVANJE</p>
                                <h2 id="ac-advisory-subpage-title">{{ $subpage['title'] ?? 'Financijsko savjetovanje' }}</h2>
                            </div>

                            <div class="ac-advisory-network-panel">
                                <p class="ac-family-section-kicker">Pandea Global M&amp;A</p>
                                <h2>{{ $pandea['title'] ?? '' }}</h2>
                                <div class="ac-advisory-network-copy">
                                    @foreach ((array) ($pandea['body'] ?? []) as $paragraph)
                                        <p>{{ $paragraph }}</p>
                                    @endforeach
                                </div>
                            </div>
                        </article>

                        <article class="ac-audit-editorial-section">
                            <div class="ac-audit-section-head ac-audit-section-head--center">
                                <p class="ac-family-section-kicker">M&amp;A SAVJETOVANJE</p>
                                <h2>{{ $ma['title'] ?? 'Spajanja i preuzimanja (M&A)' }}</h2>
                                <p>{{ $ma['intro'] ?? '' }}</p>
                            </div>
                            <div class="ac-advisory-two-col">
                                <article class="ac-audit-service-card">
                                    <h3>{{ $ma['sale']['title'] ?? 'Prodaja poduzeća' }}</h3>
                                    <p>{{ $ma['sale']['body'] ?? '' }}</p>
                                </article>
                                <article class="ac-audit-service-card">
                                    <h3>{{ $ma['acquisition']['title'] ?? 'Kupnja poduzeća' }}</h3>
                                    <p>{{ $ma['acquisition']['body'] ?? '' }}</p>
                                </article>
                            </div>
                        </article>

                        <article class="ac-audit-editorial-section">
                            <div class="ac-advisory-two-col">
                                <section class="ac-advisory-text-panel">
                                    <p class="ac-family-section-kicker">DUE DILIGENCE</p>
                                    <h2>{{ $dueDiligence['title'] ?? 'Due diligence' }}</h2>
                                    <p>{{ $dueDiligence['intro'] ?? '' }}</p>
                                    <h3>{{ $dueDiligence['help_title'] ?? 'Pomažemo vam:' }}</h3>
                                    <ul class="ac-advisory-list">
                                        @foreach ((array) ($dueDiligence['help_items'] ?? []) as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                    <p>{{ $dueDiligence['closing'] ?? '' }}</p>
                                </section>

                                <section class="ac-advisory-text-panel">
                                    <p class="ac-family-section-kicker">PROCJENE VRIJEDNOSTI</p>
                                    <h2>{{ $valuations['title'] ?? 'Procjene vrijednosti' }}</h2>
                                    @foreach ((array) ($valuations['body'] ?? []) as $paragraph)
                                        <p>{{ $paragraph }}</p>
                                    @endforeach
                                    <h3>{{ $valuations['methods_title'] ?? 'Metode vrednovanja' }}</h3>
                                    <ul class="ac-advisory-list">
                                        @foreach ((array) ($valuations['methods'] ?? []) as $method)
                                            <li>{{ $method }}</li>
                                        @endforeach
                                    </ul>
                                </section>
                            </div>
                        </article>
                    @endif
                </div>
            </div>
        </section>

        @include('front.desktop.partials.service-videos', [
            'serviceVideoSection' => $serviceVideoSection ?? [],
            'serviceVideos' => $serviceVideos ?? [],
            'locale' => $locale ?? app()->getLocale(),
        ])

        <section class="ac-service-cta-section" aria-labelledby="ac-advisory-subpage-meeting-title">
            <div class="ac-service-cta-container">
                <div class="ac-service-cta-card">
                    <div class="ac-service-cta-copy">
                        <p class="ac-family-section-kicker">{{ $meeting['kicker'] ?? 'KONTAKT' }}</p>
                        <h2 id="ac-advisory-subpage-meeting-title">{{ $meeting['title'] ?? 'Razgovarajmo o poslovnom savjetovanju' }}</h2>
                        <p>{{ $meeting['intro'] ?? '' }}</p>
                    </div>
                    <a href="{{ route('contact.create') }}" class="ac-service-cta-link">
                        <span>{{ $meeting['contact_title'] ?? 'Kontaktirajte nas' }}</span>
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
