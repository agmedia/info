@php
    $alphaCurrentUrl = rtrim(url()->current(), '/');
    $alphaNavigation = collect($mainNavigation ?? [])
        ->filter(static fn ($item): bool => is_array($item)
            && trim((string) ($item['label'] ?? '')) !== ''
            && trim((string) ($item['url'] ?? '')) !== '')
        ->map(static function (array $item) use ($alphaCurrentUrl): array {
            $href = trim((string) $item['url']);
            $normalizedHref = rtrim(url($href), '/');

            return array_merge($item, [
                'url' => $href,
                'active' => $normalizedHref !== '' && $normalizedHref === $alphaCurrentUrl,
            ]);
        })
        ->values();
    $alphaOfferUrl = route('assessment.create');
    $alphaShowLeaseCalculator = request()->routeIs('accounting.show');
    $alphaPrimaryCtaUrl = $alphaShowLeaseCalculator ? route('lease-calculator.show') : $alphaOfferUrl;
    $alphaPrimaryCtaLabel = $alphaShowLeaseCalculator ? 'MSFI 16 Kalkulator' : 'ZATRAŽI PONUDU';
@endphp

<header class="site-header" data-front-sticky-header data-alpha-header>
    <div class="header-inner">
        <a class="brand" href="{{ route('home') }}" aria-label="Alpha Capitalis — početna">
            <img src="{{ asset('alpha/logo.svg') }}" alt="Alpha Capitalis" width="300" height="80">
        </a>

        <nav class="desktop-nav" aria-label="Glavna navigacija">
            @foreach ($alphaNavigation as $item)
                <a
                    href="{{ $item['url'] }}"
                    @class(['is-active' => $item['active']])
                    @if (! empty($item['open_in_new_tab'])) target="_blank" rel="noopener noreferrer" @endif
                >
                    <span class="nav-label" data-label="{{ $item['label'] }}">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="header-actions">
            <a @class(['header-cta', 'header-cta--calculator' => $alphaShowLeaseCalculator]) href="{{ $alphaPrimaryCtaUrl }}">
                <span>{{ $alphaPrimaryCtaLabel }}</span>
            </a>
            <button class="search-link" type="button" aria-label="Pretraga" aria-expanded="false" data-header-search-toggle>
                <i class="fa-light fa-magnifying-glass" aria-hidden="true"></i>
            </button>
        </div>

        <button
            class="menu-toggle"
            type="button"
            aria-label="Otvori izbornik"
            aria-expanded="false"
            data-alpha-menu-toggle
        >
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </button>
    </div>

    <div class="mobile-menu" aria-hidden="true" data-alpha-mobile-menu>
        <nav aria-label="Mobilna navigacija">
            @foreach ($alphaNavigation as $item)
                <a
                    href="{{ $item['url'] }}"
                    @class(['is-active' => $item['active']])
                    @if (! empty($item['open_in_new_tab'])) target="_blank" rel="noopener noreferrer" @endif
                >
                    <span>{{ $item['label'] }}</span>
                    <i class="fa-light fa-arrow-right-long" aria-hidden="true"></i>
                </a>
            @endforeach
        </nav>
        <a @class(['mobile-cta', 'mobile-cta--calculator' => $alphaShowLeaseCalculator]) href="{{ $alphaPrimaryCtaUrl }}">
            <span>{{ $alphaPrimaryCtaLabel }}</span>
        </a>
    </div>

    <div class="alpha-search-panel" data-header-search-panel>
        <form
            action="{{ route('search.index') }}"
            method="get"
            class="alpha-search-form"
            role="search"
            data-header-search-form
            data-search-suggest-endpoint="{{ route('search.suggest') }}"
            data-search-results-endpoint="{{ route('search.index') }}"
        >
            <div class="alpha-search-field">
                <label for="alpha-header-search-input" class="visually-hidden">Pretraga sadržaja</label>
                <i class="fa-light fa-magnifying-glass" aria-hidden="true"></i>
                <input
                    id="alpha-header-search-input"
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="{{ __('ui.search.input_placeholder') }}"
                    autocomplete="off"
                    spellcheck="false"
                    data-header-search-input
                >
                <div class="front-search-suggestions hidden" data-header-search-suggestions></div>
            </div>
            <button type="submit" class="alpha-search-submit">{{ __('ui.search.submit') }}</button>
        </form>
    </div>
</header>
