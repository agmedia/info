@extends('front.desktop.layouts.store')

@php
    $translation = $selectedTranslation
        ?? $page->translations->firstWhere('locale', $locale)
        ?? $page->translations->firstWhere('locale', $fallbackLocale);

    $referenceItems = collect($referenceItems ?? [])->values();
    $pageBodyHtml = (string) ($translation?->body_html ?? '');
    $hasBodyCopy = trim(strip_tags($pageBodyHtml)) !== '';
    $isCroatian = str_starts_with(strtolower((string) $locale), 'hr');
    $referencesTitleLead = $isCroatian ? 'Naše' : 'Our';
    $referencesTitleAccent = $isCroatian ? 'reference' : 'references';
    $referencesIntro = trim((string) ($translation?->excerpt ?? ''))
        ?: ($isCroatian
            ? 'Odabrani klijenti i partneri koji su nam ukazali povjerenje.'
            : 'Selected clients and partners who have placed their trust in us.');
    $emptyStateTitle = $locale === 'hr'
        ? 'Reference se ažuriraju'
        : 'References are being updated';
    $emptyStateText = $locale === 'hr'
        ? 'Logotipi će uskoro biti dostupni i na ovoj stranici.'
        : 'Reference logos will be available on this page soon.';
@endphp

@section('title', $translation?->title ?? 'Reference')
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-references-page">
        @if ($topBlocks->isNotEmpty())
            <section class="ac-references-blocks ac-references-blocks--top">@include('components.content-placement', ['items' => $topBlocks])</section>
        @endif

        <section class="values-section services-index-intro ac-references-intro" aria-labelledby="ac-references-title">
            <div class="values-inner services-index-intro-layout">
                <div class="values-intro">
                    <h1 class="values-title services-index-intro-title" id="ac-references-title" data-words-slide-from-right aria-label="{{ $referencesTitleLead }} {{ $referencesTitleAccent }}">
                        <span class="values-word" style="--value-word-index: 0" aria-hidden="true">{{ $referencesTitleLead }}</span>
                        <span class="values-word is-accent" style="--value-word-index: 1" aria-hidden="true">{{ $referencesTitleAccent }}</span>
                    </h1>
                </div>

                <p class="values-copy services-index-intro-copy content-reveal" data-image-reveal>{{ $referencesIntro }}</p>
            </div>
        </section>

        <section class="ac-references-section">
            <div class="ac-references-container">
                @if ($hasBodyCopy)
                    <article class="ac-references-body">
                        <div class="content-richtext">
                            {!! $pageBodyHtml !!}
                        </div>
                    </article>
                @endif

                @if ($referenceItems->isNotEmpty())
                    <div class="ac-reference-grid">
                        @foreach ($referenceItems as $item)
                            <article class="ac-reference-card" aria-label="{{ $item['name'] }}">
                                <div
                                    class="ac-reference-logo"
                                    style="background-image: url('{{ $item['url'] }}')"
                                >
                                    <img
                                        src="{{ $item['url'] }}"
                                        alt="{{ $item['alt'] }}"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <article class="ac-reference-empty">
                        <p class="ac-reference-kicker">{{ __('ALPHA CAPITALIS') }}</p>
                        <h2>{{ $emptyStateTitle }}</h2>
                        <p>{{ $emptyStateText }}</p>
                    </article>
                @endif
            </div>
        </section>

        @if ($bottomBlocks->isNotEmpty())
            <section class="ac-references-blocks ac-references-blocks--bottom">@include('components.content-placement', ['items' => $bottomBlocks])</section>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .ac-references-page {
            min-height: 100vh;
            background: #f4f1ea;
            color: #101820;
        }

        .ac-references-container,
        .ac-references-blocks {
            width: min(1600px, calc(100% - 136px));
            margin: 0 auto;
        }

        .ac-references-blocks--top {
            padding: 2.5rem 0 0;
        }

        .ac-references-blocks--bottom {
            padding: 2.5rem 0 4rem;
        }

        .ac-references-intro {
            border-top: 1px solid rgba(120, 96, 58, 0.05);
        }

        .ac-references-intro .services-index-intro-copy {
            display: flex;
            min-height: clamp(4.5rem, 7vw, 6rem);
            align-items: center;
        }

        .ac-references-section {
            padding: clamp(3.5rem, 5vw, 5rem) 0 clamp(5rem, 7vw, 7rem);
            background: #f4f1ea;
        }

        .ac-references-body,
        .ac-reference-empty {
            border: 1px solid rgba(171, 141, 82, 0.12);
            border-radius: 8px;
            background: #fff;
            box-shadow: none;
        }

        .ac-references-body {
            margin-bottom: clamp(2rem, 4vw, 3rem);
            padding: clamp(1.1rem, 2vw, 1.55rem);
        }

        .ac-reference-kicker {
            margin: 0;
            color: #7c653b;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .ac-reference-empty h2 {
            margin: 0.7rem 0 0;
            color: #101820;
            font-family: "Instrument Sans Variable", Arial, sans-serif;
            font-size: clamp(1.9rem, 3vw, 2.5rem);
            font-weight: 700;
            line-height: 1.14;
            letter-spacing: 0;
            text-wrap: balance;
        }

        .ac-reference-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 0;
            border-top: 1px solid rgba(3, 18, 31, 0.14);
            border-left: 1px solid rgba(3, 18, 31, 0.14);
            background: transparent;
        }

        .ac-reference-card {
            display: grid;
            min-height: clamp(9.5rem, 11vw, 11.5rem);
            place-items: center;
            padding: clamp(1.5rem, 2.3vw, 2.4rem);
            border: 0;
            border-right: 1px solid rgba(3, 18, 31, 0.14);
            border-bottom: 1px solid rgba(3, 18, 31, 0.14);
            border-radius: 0;
            background: #fbf9f5;
            box-shadow: none;
            transition: background-color 220ms ease;
        }

        .ac-reference-card:hover {
            background: #fff;
        }

        .ac-reference-logo {
            display: grid;
            width: min(70%, 13rem);
            height: 4.2rem;
            place-items: center;
            background-color: #fbf9f5;
            background-position: center;
            background-repeat: no-repeat;
            background-size: contain;
            background-blend-mode: darken;
            opacity: 0.82;
            transition: background-color 220ms ease, opacity 220ms ease;
        }

        .ac-reference-logo img {
            display: block;
            max-width: 100%;
            max-height: 100%;
            border-radius: 0;
            opacity: 0;
        }

        .ac-reference-card:hover .ac-reference-logo {
            background-color: #fff;
            opacity: 1;
        }

        .ac-reference-empty {
            display: grid;
            justify-items: center;
            padding: clamp(2rem, 5vw, 3.2rem);
            text-align: center;
        }

        .ac-reference-empty > p:last-child {
            max-width: 34rem;
            margin-top: 0.8rem;
        }

        .front-desktop-shell:has(.ac-references-page) .front-footer {
            --front-footer-bg: #071326;
            background: #071326;
        }

        .front-desktop-shell:has(.ac-references-page) .footer-newsletter::before {
            background: #f4f1ea;
        }

        @media (max-width: 1120px) {
            .ac-reference-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (max-width: 820px) {
            .ac-reference-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .ac-references-container,
            .ac-references-blocks {
                width: calc(100% - 40px);
            }

            .ac-reference-card {
                min-height: 8.5rem;
                padding: 1rem;
            }
        }

        @media (max-width: 420px) {
            .ac-reference-card {
                min-height: 7.5rem;
            }
        }
    </style>
@endpush
