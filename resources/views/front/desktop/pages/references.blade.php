@extends('front.desktop.layouts.store')

@php
    $translation = $selectedTranslation
        ?? $page->translations->firstWhere('locale', $locale)
        ?? $page->translations->firstWhere('locale', $fallbackLocale);

    $pageTitleBreadcrumbs = [
        ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
        ['label' => $translation?->title ?? $page->code, 'current' => true],
    ];

    $referenceItems = collect($referenceItems ?? [])->values();
    $pageBodyHtml = (string) ($translation?->body_html ?? '');
    $hasBodyCopy = trim(strip_tags($pageBodyHtml)) !== '';
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

        <x-front.page-title-band :breadcrumbs="$pageTitleBreadcrumbs" section-class="ac-references-title-band">
            <div class="ac-page-title-copy">
                <h1>{{ $translation?->title ?? $page->code }}</h1>
                @if (!empty($translation?->excerpt))
                    <p>{{ $translation->excerpt }}</p>
                @endif
            </div>
        </x-front.page-title-band>

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
                            <article class="ac-reference-card">
                                <div class="ac-reference-logo-shell">
                                    <img
                                        src="{{ $item['url'] }}"
                                        alt="{{ $item['alt'] }}"
                                        loading="lazy"
                                        decoding="async"
                                        class="ac-reference-logo-image"
                                    >
                                </div>

                                <h3>{{ $item['name'] }}</h3>

                                @if (($item['caption'] ?? '') !== '' && ($item['caption'] ?? '') !== ($item['name'] ?? ''))
                                    <p>{{ $item['caption'] }}</p>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @else
                    <article class="ac-reference-empty">
                        <p class="ac-reference-kicker">{{ __('Alpha Capitalis') }}</p>
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
            background: #f6f1e7;
            color: #101820;
        }

        .ac-references-container,
        .ac-references-blocks {
            width: min(100% - 2rem, 1320px);
            margin: 0 auto;
        }

        .ac-references-blocks--top {
            padding: 2.5rem 0 0;
        }

        .ac-references-blocks--bottom {
            padding: 2.5rem 0 4rem;
        }

        .ac-references-title-band {
            margin-bottom: 0;
            background: #f6f1e7;
            border-top-color: transparent;
            border-bottom-color: rgba(15, 42, 67, 0.08);
        }

        .ac-references-title-band .ac-page-title-copy h1 {
            color: #101820;
            font-size: 2.65rem;
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: 0;
        }

        .ac-references-title-band .ac-page-title-copy > p,
        .ac-references-title-band .front-scroll-breadcrumb-link,
        .ac-references-title-band .front-scroll-breadcrumb-current,
        .ac-references-title-band .front-scroll-breadcrumb-separator {
            color: #4f4a43;
        }

        .ac-references-title-band .ac-page-title-breadcrumb::before,
        .ac-references-title-band .ac-page-title-breadcrumb::after {
            background: rgba(120, 96, 58, 0.16);
        }

        .ac-references-section {
            padding: clamp(3rem, 5vw, 4.8rem) 0 clamp(5rem, 7vw, 7rem);
            background: #f6f1e7;
        }

        .ac-references-body,
        .ac-reference-card,
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
            font-family: 'Montserrat', sans-serif;
            font-size: clamp(1.9rem, 3vw, 2.5rem);
            font-weight: 700;
            line-height: 1.14;
            letter-spacing: 0;
            text-wrap: balance;
        }

        .ac-reference-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }

        .ac-reference-card {
            display: grid;
            align-content: start;
            gap: 0.9rem;
            min-height: 11.7rem;
            overflow: hidden;
            padding: 1rem;
        }

        .ac-reference-logo-shell {
            display: grid;
            place-items: center;
            min-height: 5.8rem;
            padding: 0.9rem;
            border: 1px solid rgba(15, 42, 67, 0.08);
            border-radius: 8px;
            background: #fff;
        }

        .ac-reference-logo-image {
            display: block;
            width: auto;
            max-width: 100%;
            max-height: 4.2rem;
            object-fit: contain;
            opacity: 0.86;
            filter: grayscale(1) contrast(1.08);
        }

        .ac-reference-card h3 {
            margin: 0;
            color: #101820;
            font-size: 0.98rem;
            font-weight: 700;
            line-height: 1.45;
            letter-spacing: 0;
        }

        .ac-reference-card p,
        .ac-reference-empty > p:last-child {
            margin: 0;
            color: #403a34;
            font-size: 0.92rem;
            line-height: 1.6;
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

        @media (max-width: 1120px) {
            .ac-reference-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
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
                width: min(100% - 1.35rem, 1320px);
            }

            .ac-reference-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }
    </style>
@endpush
