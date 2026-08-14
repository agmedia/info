@extends('front.desktop.layouts.store')

@php
    $translation = $selectedTranslation
        ?? $page->translations->firstWhere('locale', $locale)
        ?? $page->translations->firstWhere('locale', $fallbackLocale);
    $pagePayload = is_array($page->payload ?? null) ? $page->payload : [];
    $translationPayload = is_array($translation?->payload ?? null) ? $translation->payload : [];
    $glossaryKicker = trim((string) ($translationPayload['glossary_kicker'] ?? $pagePayload['glossary_kicker'] ?? 'Rječnik pojmova')) ?: 'Rječnik pojmova';
    $searchPlaceholder = trim((string) ($pagePayload['glossary_search_placeholder'] ?? 'Pretražite pojam, kraticu ili povezani izraz'));
    $emptyTitle = trim((string) ($pagePayload['glossary_empty_title'] ?? 'Nema rezultata za zadane filtre'));
    $emptyBody = trim((string) ($pagePayload['glossary_empty_body'] ?? 'Pokušajte s drugim pojmom ili vratite prikaz na sva slova.'));
    $alphabetLetters = array_values(array_filter($glossaryAlphabet, fn ($candidate) => $candidate !== 'ALL'));
    $readMoreLabel = __('ui.blog.read_more');
    $searchLabel = __('ui.blog.filters.search');
    $resetLabel = __('ui.blog.filters.reset');
    $pageTitle = trim((string) ($translation?->title ?? '')) ?: 'Svijet financija';
    $pageIntro = !empty($translation?->excerpt)
        ? \Illuminate\Support\Str::limit((string) $translation->excerpt, 260, '...')
        : 'Brzo pretražite pojmove i otvorite detaljno objašnjenje svakog izraza.';
    $headingWords = preg_split('/\s+/u', $pageTitle, -1, PREG_SPLIT_NO_EMPTY) ?: [];
@endphp

@section('title', $pageTitle)
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-glossary-page">
        @if ($topBlocks->isNotEmpty())
            <section class="ac-glossary-blocks ac-glossary-blocks--top">
                @include('components.content-placement', ['items' => $topBlocks])
            </section>
        @endif

        <section class="values-section services-index-intro ac-glossary-intro" aria-labelledby="ac-glossary-title">
            <div class="values-inner services-index-intro-layout ac-glossary-intro-layout">
                <div class="values-intro">
                    <h1 class="values-title services-index-intro-title ac-glossary-intro-title" id="ac-glossary-title" data-words-slide-from-right aria-label="{{ $pageTitle }}">
                        @foreach ($headingWords as $word)
                            <span class="values-word animation-index-{{ $loop->index }} {{ $loop->count > 1 && $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h1>
                </div>

                <div class="values-copy services-index-intro-copy ac-glossary-intro-copy content-reveal" data-image-reveal>
                    <p class="ac-glossary-kicker">{{ $glossaryKicker }}</p>
                    <p>{{ $pageIntro }}</p>
                </div>
            </div>
        </section>

        <section class="ac-glossary-explorer" aria-labelledby="ac-glossary-explorer-title">
            <h2 class="visually-hidden" id="ac-glossary-explorer-title">{{ $glossaryKicker }}</h2>

            <div class="ac-glossary-container" data-glossary-root data-active-letter="{{ $glossaryActiveLetter }}">
                <div class="ac-glossary-toolbar content-reveal" data-image-reveal>
                    <div class="ac-glossary-toolbar-main">
                        <form class="ac-glossary-search" method="get" action="{{ route('glossary.index') }}" role="search" data-glossary-form>
                            <label for="finance-glossary-search">{{ $searchLabel }}</label>
                            <div class="ac-glossary-search-field">
                                <input
                                    id="finance-glossary-search"
                                    type="search"
                                    name="q"
                                    value="{{ $glossarySearch }}"
                                    placeholder="{{ $searchPlaceholder }}"
                                    data-glossary-search
                                    autocomplete="off"
                                >
                                <input type="hidden" name="letter" value="{{ $glossaryActiveLetter }}" data-glossary-letter-input>
                                <button type="button" data-glossary-clear @if($glossarySearch === '' && $glossaryActiveLetter === 'ALL') hidden @endif>
                                    {{ $resetLabel }}
                                </button>
                            </div>
                        </form>

                        <div class="ac-glossary-result" aria-live="polite">
                            <span>Rezultati</span>
                            <strong><output data-glossary-count>{{ $glossaryInitialVisibleCount }}</output> pojmova</strong>
                        </div>
                    </div>

                    <div class="ac-glossary-alphabet-wrap">
                        <p>Filtrirajte prema početnom slovu</p>
                        <div class="front-scroll-rail ac-glossary-alphabet" aria-label="Filter po početnom slovu">
                            <div class="front-scroll-rail-track">
                                @foreach ($glossaryAlphabet as $letter)
                                    @php
                                        $hasItems = $letter === 'ALL' || in_array($letter, $glossaryAvailableLetters, true);
                                        $isActive = $glossaryActiveLetter === $letter;
                                        $label = $letter === 'ALL' ? 'Sve' : $letter;
                                    @endphp
                                    <button
                                        type="button"
                                        class="ac-glossary-letter {{ $isActive ? 'is-active' : '' }}"
                                        data-glossary-letter="{{ $letter }}"
                                        data-empty="{{ $hasItems ? 'false' : 'true' }}"
                                        aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                                        @disabled(! $hasItems)
                                    >
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ac-glossary-groups">
                    @foreach ($alphabetLetters as $letter)
                        @continue(! $groupedGlossaryTerms->has($letter))
                        @php
                            $terms = $groupedGlossaryTerms->get($letter);
                            $groupIsVisible = (bool) ($glossaryVisibleGroups[$letter] ?? false);
                        @endphp
                        <section
                            class="ac-glossary-group content-reveal animation-index-{{ $loop->index % 3 }}"
                            data-glossary-group
                            data-letter="{{ $letter }}"
                            data-image-reveal
                            @if(! $groupIsVisible) hidden @endif
                        >
                            <header class="ac-glossary-group-heading">
                                <h2>{{ $letter }}</h2>
                                <span>{{ $terms->count() }} pojmova</span>
                            </header>

                            <div class="ac-glossary-term-grid">
                                @foreach ($terms as $term)
                                    @php
                                        $excerpt = \Illuminate\Support\Str::limit((string) $term['excerpt'], 150, '...');
                                    @endphp
                                    <article
                                        id="pojam-{{ $term['slug'] }}"
                                        class="ac-glossary-item"
                                        data-glossary-item
                                        data-letter="{{ $term['letter_key'] }}"
                                        data-search="{{ $term['search_text'] }}"
                                        @if(! $term['initial_visible']) hidden @endif
                                    >
                                        @if ($term['abbreviation'] !== '')
                                            <p class="ac-glossary-abbreviation">{{ $term['abbreviation'] }}</p>
                                        @endif

                                        <h3>
                                            <a href="{{ $term['url'] }}">{{ $term['title'] }}</a>
                                        </h3>

                                        @if ($excerpt !== '')
                                            <p class="ac-glossary-excerpt">{{ $excerpt }}</p>
                                        @endif

                                        <a href="{{ $term['url'] }}" class="news-all-link ac-glossary-term-link">
                                            <span>{{ $readMoreLabel }}</span>
                                            <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                                        </a>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endforeach

                    <div class="ac-glossary-empty" data-glossary-empty @if($glossaryInitialVisibleCount > 0) hidden @endif>
                        <i class="fa-duotone fa-thin fa-magnifying-glass" aria-hidden="true"></i>
                        <p>{{ $glossaryKicker }}</p>
                        <h2>{{ $emptyTitle }}</h2>
                        <span>{{ $emptyBody }}</span>
                    </div>
                </div>
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

@push('scripts')
    <script src="{{ asset('front-theme/scripts/glossary.js') }}?v={{ filemtime(public_path('front-theme/scripts/glossary.js')) }}" defer></script>
@endpush
