@extends('front.desktop.layouts.store')

@php
    $faqTitle = trim((string) __('ui.faq.title')) ?: 'Česta pitanja';
    $faqSubtitle = trim((string) __('ui.faq.subtitle'));
    $headingWords = preg_split('/\s+/u', $faqTitle, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $faqKicker = str_starts_with(strtolower((string) $locale), 'hr') ? 'Pitanja i odgovori' : 'Questions and answers';
    $faqListTitle = str_starts_with(strtolower((string) $locale), 'hr') ? 'Pronađite odgovor' : 'Find an answer';
    $faqCountLabel = str_starts_with(strtolower((string) $locale), 'hr') ? 'dostupnih odgovora' : 'answers available';
    $faqTranslations = $faqs
        ->map(fn ($faq) => $faq->translations->firstWhere('locale', $locale)
            ?? $faq->translations->firstWhere('locale', $fallbackLocale)
            ?? $faq->translations->first())
        ->filter()
        ->values();
@endphp

@section('title', __('ui.faq.page_title'))
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-faq-page">
        @if ($topBlocks->isNotEmpty())
            <section class="ac-faq-blocks ac-faq-blocks--top">
                @include('components.content-placement', ['items' => $topBlocks])
            </section>
        @endif

        <section class="values-section services-index-intro ac-faq-intro" aria-labelledby="ac-faq-title">
            <div class="values-inner services-index-intro-layout ac-faq-intro-layout">
                <div class="values-intro">
                    <h1 class="values-title services-index-intro-title ac-faq-intro-title" id="ac-faq-title" data-words-slide-from-right aria-label="{{ $faqTitle }}">
                        @foreach ($headingWords as $word)
                            <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h1>
                </div>

                <div class="values-copy services-index-intro-copy ac-faq-intro-copy content-reveal" data-image-reveal>
                    <p class="ac-faq-kicker">{{ $faqKicker }}</p>
                    @if ($faqSubtitle !== '')
                        <p>{{ $faqSubtitle }}</p>
                    @endif
                </div>
            </div>
        </section>

        <section class="ac-faq-section" aria-labelledby="ac-faq-list-title">
            <div class="ac-faq-container ac-faq-layout">
                <header class="ac-faq-section-heading content-reveal" data-image-reveal>
                    <p class="ac-faq-kicker">{{ $faqKicker }}</p>
                    <h2 id="ac-faq-list-title">{{ $faqListTitle }}</h2>
                    <p class="ac-faq-count"><strong>{{ $faqTranslations->count() }}</strong> {{ $faqCountLabel }}</p>
                </header>

                <div class="ac-faq-list">
                    @forelse ($faqTranslations as $translation)
                        <details class="ac-faq-item content-reveal animation-index-{{ $loop->index % 3 }}" data-image-reveal>
                            <summary class="ac-faq-summary">
                                <span class="ac-faq-number" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="ac-faq-question">{{ $translation->question }}</span>
                                <span class="ac-faq-toggle" aria-hidden="true">
                                    <i class="fa-duotone fa-thin fa-plus"></i>
                                </span>
                            </summary>
                            <div class="ac-faq-answer">
                                <div class="content-richtext">
                                    {!! $translation->answer_html ?: '<p>—</p>' !!}
                                </div>
                            </div>
                        </details>
                    @empty
                        <div class="ac-faq-empty">
                            <i class="fa-duotone fa-thin fa-message-question" aria-hidden="true"></i>
                            <p>{{ __('ui.faq.empty') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        @if ($bottomBlocks->isNotEmpty())
            <section class="ac-faq-blocks ac-faq-blocks--bottom">
                @include('components.content-placement', ['items' => $bottomBlocks])
            </section>
        @endif
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/faq.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/faq.css')) }}">
@endpush
