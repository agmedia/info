@php
    $pageTitleBreadcrumbs = [
        ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
        ['label' => __('ui.search.page_title'), 'current' => true],
    ];
@endphp

<section class="ac-site-search-page">
    <x-front.page-title-band
        :breadcrumbs="$pageTitleBreadcrumbs"
        section-class="ac-site-search-title-band"
        breadcrumb-class="ac-site-search-breadcrumb"
    >
        <div class="ac-page-title-copy ac-site-search-title-copy">
            <p class="ac-site-search-kicker">{{ __('ui.search.title') }}</p>
            <h1>{{ __('ui.search.results_title') }}</h1>

            @if ($searchQuery !== '')
                <div class="ac-site-search-summary">
                    <p>{{ __('ui.search.results_for', ['query' => $searchQuery]) }}</p>
                    <span>{{ __('ui.search.results_count', ['count' => $searchTotalResults]) }}</span>
                </div>
            @else
                <p class="ac-site-search-summary-copy">{{ __('ui.search.prompt_text') }}</p>
            @endif
        </div>
    </x-front.page-title-band>

    <div class="ac-site-search-shell">
        <div class="ac-site-search-hero">
            <form action="{{ route('search.index') }}" method="get" class="ac-site-search-form" role="search">
                <label for="search-page-query" class="sr-only">{{ __('ui.search.title') }}</label>
                <div class="ac-site-search-input-wrap">
                    <span class="ac-site-search-input-icon" aria-hidden="true">
                        <i class="fa-light fa-magnifying-glass"></i>
                    </span>
                    <input
                        id="search-page-query"
                        type="search"
                        name="q"
                        value="{{ $searchQuery }}"
                        class="ac-site-search-input"
                        placeholder="{{ __('ui.search.input_placeholder') }}"
                    >
                </div>
                <button type="submit" class="ac-site-search-button">{{ __('ui.search.submit') }}</button>
            </form>
        </div>

        @if ($searchQuery === '')
            <div class="ac-site-search-empty">
                <h2>{{ __('ui.search.prompt_title') }}</h2>
                <p>{{ __('ui.search.prompt_text') }}</p>
            </div>
        @elseif ($searchTotalResults === 0)
            <div class="ac-site-search-empty">
                <h2>{{ __('ui.search.empty') }}</h2>
                <p>{{ __('ui.search.empty_hint') }}</p>
            </div>
        @else
            <div class="ac-site-search-sections">
                @foreach ($searchSections as $section)
                    <section class="ac-site-search-section" aria-labelledby="search-section-{{ $section['key'] }}">
                        <div class="ac-site-search-section-head">
                            <h2 id="search-section-{{ $section['key'] }}">{{ $section['label'] }}</h2>
                            <span>{{ __('ui.search.results_count', ['count' => $section['total_count']]) }}</span>
                        </div>

                        <div class="ac-site-search-list">
                            @foreach ($section['items'] as $item)
                                <article class="ac-site-search-card">
                                    <a href="{{ $item['url'] }}" class="ac-site-search-card-link">
                                        <div class="ac-site-search-card-body">
                                            @if (!empty($item['eyebrow']) || !empty($item['meta']))
                                                <div class="ac-site-search-card-meta">
                                                    @if (!empty($item['eyebrow']))
                                                        <span>{{ $item['eyebrow'] }}</span>
                                                    @endif
                                                    @if (!empty($item['meta']))
                                                        <span>{{ $item['meta'] }}</span>
                                                    @endif
                                                </div>
                                            @endif

                                            <h3>{{ $item['title'] }}</h3>

                                            @if (!empty($item['excerpt']))
                                                <p>{{ $item['excerpt'] }}</p>
                                            @endif
                                        </div>

                                        <span class="ac-site-search-card-arrow" aria-hidden="true">
                                            <i class="fa-light fa-arrow-right-long"></i>
                                        </span>
                                    </a>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        @endif
    </div>
</section>

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/search.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/search.css')) }}">
@endpush
