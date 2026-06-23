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
    $detailSections = [
        'ma' => $ma,
        'due_diligence' => $dueDiligence,
        'valuations' => $valuations,
        'tax' => $tax,
        'bank_loans' => $bankLoans,
        'zopu' => $zopu,
    ];
    $detailKey = (string) ($subpage['detail_key'] ?? '');
    $detail = (array) ($detailSections[$detailKey] ?? []);
    $pandeaLogo = trim((string) ($pandeaLogoUrl ?? ''));
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
    <div class="ac-family-business-page ac-audit-page ac-advisory-page {{ $detailKey === 'tax' ? 'ac-service-band-even' : '' }}">
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
                                @if (trim((string) ($funding['intro'] ?? '')) !== '')
                                    <p>{{ $funding['intro'] }}</p>
                                @endif
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

                        </article>
                    @elseif ($type === 'detail')
                        <article class="ac-audit-editorial-section ac-audit-editorial-section--overview">
                            <div class="ac-audit-section-head ac-audit-section-head--center">
                                <p class="ac-family-section-kicker">{{ $detail['kicker'] ?? \Illuminate\Support\Str::upper((string) ($detail['title'] ?? $subpage['title'] ?? 'Savjetovanje')) }}</p>
                                <h2 id="ac-advisory-subpage-title">{{ $detail['overview_title'] ?? ($detail['title'] ?? ($subpage['title'] ?? 'Savjetovanje')) }}</h2>
                            </div>

                            <div class="ac-audit-copy ac-audit-copy--full">
                                @foreach ((array) ($detail['overview_body'] ?? []) as $paragraph)
                                    @if (trim((string) $paragraph) !== '')
                                        <p>{{ $paragraph }}</p>
                                    @endif
                                @endforeach
                            </div>
                        </article>

                        @if ((bool) ($detail['show_pandea'] ?? false))
                            <article class="ac-audit-editorial-section">
                                <div class="ac-advisory-network-panel">
                                    <div class="ac-advisory-network-head">
                                        @if ($pandeaLogo !== '')
                                            <div class="ac-advisory-network-logo-card">
                                                <img src="{{ $pandeaLogo }}" alt="{{ $pandea['logo_alt'] ?? 'Pandea Global M&A' }}" class="ac-advisory-network-logo" loading="lazy" decoding="async">
                                            </div>
                                        @endif
                                        <div>
                                            <p class="ac-family-section-kicker">Pandea Global M&amp;A</p>
                                            <h2>{{ $pandea['title'] ?? '' }}</h2>
                                        </div>
                                    </div>
                                    <div class="ac-advisory-network-copy">
                                        @foreach ((array) ($pandea['body'] ?? []) as $paragraph)
                                            <p>{{ $paragraph }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            </article>
                        @endif

                        <article class="ac-audit-editorial-section">
                            <div class="ac-audit-section-head ac-audit-section-head--center">
                                <p class="ac-family-section-kicker">USLUGE</p>
                                <h2>{{ $detail['services_title'] ?? 'Naše usluge' }}</h2>
                                @foreach ((array) ($detail['services_body'] ?? []) as $paragraph)
                                    @if (trim((string) $paragraph) !== '')
                                        <p>{{ $paragraph }}</p>
                                    @endif
                                @endforeach
                            </div>

                            @if (! empty($detail['help_items'] ?? []))
                                <div class="ac-audit-section-head ac-audit-section-head--center ac-advisory-subhead">
                                    <h3>{{ $detail['help_title'] ?? 'U okviru usluge pomažemo u:' }}</h3>
                                </div>
                                <div class="ac-advisory-check-grid">
                                    @foreach ((array) ($detail['help_items'] ?? []) as $item)
                                        <div class="ac-advisory-check-pill">
                                            <span class="ac-advisory-check-mark" aria-hidden="true">&#10003;</span>
                                            <span>{{ $item }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </article>

                        @if (trim((string) ($detail['approach_title'] ?? '')) !== '' || ! empty($detail['approach_body'] ?? []))
                            <article class="ac-audit-editorial-section">
                                <div class="ac-audit-section-head ac-audit-section-head--center">
                                    <p class="ac-family-section-kicker">PRISTUP</p>
                                    <h2>{{ $detail['approach_title'] ?? 'Naš pristup' }}</h2>
                                </div>

                                <blockquote class="ac-audit-copy ac-audit-copy--full ac-audit-approach-copy">
                                    @foreach ((array) ($detail['approach_body'] ?? []) as $paragraph)
                                        @if (trim((string) $paragraph) !== '')
                                            <p>{{ $paragraph }}</p>
                                        @endif
                                    @endforeach
                                </blockquote>
                            </article>
                        @endif
                    @else
                        <article class="ac-audit-editorial-section">
                            <div class="ac-audit-section-head ac-audit-section-head--center">
                                <p class="ac-family-section-kicker">SAVJETOVANJE</p>
                                <h2 id="ac-advisory-subpage-title">{{ $subpage['title'] ?? 'Savjetovanje' }}</h2>
                                @if (trim((string) ($subpage['intro'] ?? '')) !== '')
                                    <p>{{ $subpage['intro'] }}</p>
                                @endif
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
