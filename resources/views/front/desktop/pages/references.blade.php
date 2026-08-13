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
                        <span class="values-word animation-index-0" aria-hidden="true">{{ $referencesTitleLead }}</span>
                        <span class="values-word animation-index-1 is-accent" aria-hidden="true">{{ $referencesTitleAccent }}</span>
                    </h1>
                </div>

                <p class="values-copy services-index-intro-copy content-reveal" data-image-reveal>{{ $referencesIntro }}</p>
            </div>
        </section>

        <section class="ac-references-section">
            <div class="ac-references-container">
                @if ($hasBodyCopy)
                    <article class="ac-references-body content-reveal" data-image-reveal>
                        <div class="content-richtext">
                            {!! $pageBodyHtml !!}
                        </div>
                    </article>
                @endif

                @if ($referenceItems->isNotEmpty())
                    <div class="ac-reference-grid">
                        @foreach ($referenceItems as $item)
                            <article class="ac-reference-card content-reveal animation-index-{{ $loop->index % 2 }}" data-image-reveal aria-label="{{ $item['name'] }}">
                                <div class="ac-reference-logo">
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
                    <article class="ac-reference-empty content-reveal" data-image-reveal>
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
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/references.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/references.css')) }}">
@endpush
