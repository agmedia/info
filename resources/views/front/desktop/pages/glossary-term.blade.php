@extends('front.desktop.layouts.store')

@php
    $pageTranslation = $glossaryPageTranslation
        ?? $glossaryPage->translations->firstWhere('locale', $locale)
        ?? $glossaryPage->translations->firstWhere('locale', $fallbackLocale);
    $termTranslation = $glossaryTermTranslation;
    $glossaryPageTitle = trim((string) ($pageTranslation?->title ?? '')) ?: 'Svijet financija';
    $termTitle = trim((string) ($termTranslation?->title ?? $glossaryTerm->code));
    $termLead = trim((string) ($glossaryTermLead ?? ''));
    $payload = is_array($glossaryTermPayload ?? null) ? $glossaryTermPayload : [];
    $synonyms = collect($payload['synonyms'] ?? [])->map(fn ($item) => trim((string) $item))->filter()->values()->all();
    $variations = collect($payload['variations'] ?? [])->map(fn ($item) => trim((string) $item))->filter()->values()->all();
    $tags = collect($payload['tags'] ?? [])->map(fn ($item) => trim((string) $item))->filter()->values()->all();
    $categories = collect($payload['categories'] ?? [])->map(fn ($item) => trim((string) $item))->filter()->values()->all();
    $abbreviation = trim((string) ($payload['abbreviation'] ?? ''));
    $headingWords = preg_split('/\s+/u', $termTitle, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $termCategory = $categories[0] ?? $glossaryPageTitle;
    $termBodyText = \Illuminate\Support\Str::squish(html_entity_decode(strip_tags((string) $glossaryTermBodyHtml)));
    $termLeadPrefix = preg_replace('/(?:\.{3}|…)\s*$/u', '', \Illuminate\Support\Str::squish($termLead)) ?: '';
    $bodyStartsWithLead = $termLeadPrefix !== ''
        && str_starts_with(mb_strtolower($termBodyText), mb_strtolower($termLeadPrefix));
    $hasDistinctBody = $termBodyText !== ''
        && mb_strtolower($termBodyText) !== mb_strtolower(\Illuminate\Support\Str::squish($termLead));
@endphp

@section('title', $termTranslation?->meta_title ?: $termTitle ?: $glossaryPageTitle)
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-glossary-page ac-glossary-detail-page">
        @if ($topBlocks->isNotEmpty())
            <section class="ac-glossary-blocks ac-glossary-blocks--top">
                @include('components.content-placement', ['items' => $topBlocks])
            </section>
        @endif

        <section class="values-section services-index-intro ac-glossary-intro ac-glossary-detail-intro" aria-labelledby="ac-glossary-term-title">
            <div class="values-inner services-index-intro-layout ac-glossary-intro-layout">
                <div class="values-intro">
                    <h1 class="values-title services-index-intro-title ac-glossary-intro-title ac-glossary-detail-title" id="ac-glossary-term-title" data-words-slide-from-right aria-label="{{ $termTitle }}">
                        @foreach ($headingWords as $word)
                            <span class="values-word animation-index-{{ $loop->index }} {{ $loop->count > 1 && $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h1>
                </div>

                <div class="values-copy services-index-intro-copy ac-glossary-intro-copy ac-glossary-detail-intro-copy content-reveal" data-image-reveal>
                    <div class="ac-page-title-copy">
                        <p class="ac-glossary-detail-category">{{ $termCategory }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="ac-glossary-detail" aria-label="{{ $termTitle }}">
            <div class="ac-glossary-container">
                <div class="ac-glossary-detail-layout">
                    <aside class="ac-glossary-detail-aside content-reveal" data-image-reveal>
                        <a href="{{ route('glossary.index') }}" class="news-all-link ac-glossary-back-link">
                            <i class="fa-duotone fa-thin fa-arrow-left" aria-hidden="true"></i>
                            <span>Natrag u {{ $glossaryPageTitle }}</span>
                        </a>

                        @if ($abbreviation !== '')
                            <div class="ac-glossary-detail-summary">
                                <div>
                                    <span>Kratica</span>
                                    <strong>{{ $abbreviation }}</strong>
                                </div>
                            </div>
                        @endif

                        @if ($synonyms !== [] || $variations !== [] || $tags !== [])
                            <dl class="ac-glossary-detail-meta">
                                @if ($synonyms !== [])
                                    <div>
                                        <dt>Sinonimi</dt>
                                        <dd>{{ implode(', ', $synonyms) }}</dd>
                                    </div>
                                @endif
                                @if ($variations !== [])
                                    <div>
                                        <dt>Varijante</dt>
                                        <dd>{{ implode(', ', $variations) }}</dd>
                                    </div>
                                @endif
                                @if ($tags !== [])
                                    <div>
                                        <dt>Oznake</dt>
                                        <dd>{{ implode(', ', $tags) }}</dd>
                                    </div>
                                @endif
                            </dl>
                        @endif
                    </aside>

                    <article class="ac-glossary-term-body content-reveal animation-index-1" data-image-reveal>
                        <div class="content-richtext pt-6">
                            @if ($bodyStartsWithLead)
                                {!! $glossaryTermBodyHtml !!}
                            @else
                                @if ($termLead !== '')
                                    <p>{{ $termLead }}</p>
                                @endif
                                @if ($hasDistinctBody)
                                    {!! $glossaryTermBodyHtml !!}
                                @endif
                            @endif
                            @if ($termLead === '' && $termBodyText === '')
                                <p>Ovaj pojam trenutno nema dodatni opis.</p>
                            @endif
                        </div>
                    </article>
                </div>

                @if ($relatedGlossaryTerms !== [])
                    <section class="ac-glossary-related" aria-labelledby="ac-glossary-related-title">
                        <div class="ac-glossary-related-heading">
                            <div>
                                <p>{{ $glossaryPageTitle }}</p>
                                <h2 id="ac-glossary-related-title">Povezani pojmovi</h2>
                            </div>
                            <a href="{{ route('glossary.index') }}" class="news-all-link">
                                <span>Prikaži sve</span>
                                <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>

                        <div class="ac-glossary-related-grid">
                            @foreach ($relatedGlossaryTerms as $relatedTerm)
                                <article class="ac-glossary-related-item content-reveal animation-index-{{ $loop->index % 3 }}" data-image-reveal>
                                    <h3><a href="{{ $relatedTerm['url'] }}">{{ $relatedTerm['title'] }}</a></h3>
                                    @if ($relatedTerm['excerpt'] !== '')
                                        <p>{{ \Illuminate\Support\Str::limit((string) $relatedTerm['excerpt'], 150, '...') }}</p>
                                    @endif
                                    <a href="{{ $relatedTerm['url'] }}" class="news-all-link ac-glossary-related-link">
                                        <span>{{ __('ui.blog.read_more') }}</span>
                                        <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                                    </a>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        </section>

        @if ($bottomBlocks->isNotEmpty())
            <section class="ac-glossary-blocks ac-glossary-blocks--bottom">
                @include('components.content-placement', ['items' => $bottomBlocks])
            </section>
        @endif
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/glossary.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/glossary.css')) }}">
@endpush
