@php
    $alphaCurrentUrl = rtrim(url()->current(), '/');
    $alphaServicesUrl = rtrim(route('services.index'), '/');
    $alphaIsCroatian = app()->getLocale() === 'hr';
    $alphaServiceNavigation = collect([
        [
            'label' => $alphaIsCroatian ? 'Revizija' : 'Audit',
            'url' => route('audit.show'),
            'route_pattern' => 'audit.*',
        ],
        [
            'label' => $alphaIsCroatian ? 'Računovodstvo' : 'Accounting',
            'url' => route('accounting.show'),
            'route_pattern' => 'accounting.*',
        ],
        [
            'label' => $alphaIsCroatian ? 'Savjetovanje' : 'Advisory',
            'url' => route('advisory.show'),
            'route_pattern' => 'advisory.*',
        ],
    ])->map(static function (array $item): array {
        return array_merge($item, [
            'active' => request()->routeIs($item['route_pattern']),
        ]);
    });
    $alphaNavigation = collect($mainNavigation ?? [])
        ->filter(static fn ($item): bool => is_array($item)
            && trim((string) ($item['label'] ?? '')) !== ''
            && trim((string) ($item['url'] ?? '')) !== '')
        ->map(static function (array $item) use ($alphaCurrentUrl, $alphaServicesUrl, $alphaServiceNavigation): array {
            $href = trim((string) $item['url']);
            $normalizedHref = rtrim(url($href), '/');
            $isServices = $normalizedHref === $alphaServicesUrl;

            return array_merge($item, [
                'url' => $href,
                'is_services' => $isServices,
                'current' => $normalizedHref !== '' && $normalizedHref === $alphaCurrentUrl,
                'active' => ($normalizedHref !== '' && $normalizedHref === $alphaCurrentUrl)
                    || ($isServices && $alphaServiceNavigation->contains('active', true)),
            ]);
        })
        ->values();
    $alphaOfferUrl = route('assessment.create');
    $alphaShowLeaseCalculator = request()->routeIs('accounting.show');
    $alphaPrimaryCtaUrl = $alphaShowLeaseCalculator ? route('lease-calculator.show') : $alphaOfferUrl;
    $alphaPrimaryCtaLabel = $alphaShowLeaseCalculator ? 'MSFI 16 Kalkulator' : 'ZATRAŽI PONUDU';
    $alphaBrandName = trim((string) ($storeSettings['branding']['store_name'] ?? '')) ?: 'Alpha Capitalis';
    $alphaBrandLogoUrl = trim((string) ($storeSettings['branding']['logo_url'] ?? '')) ?: asset('alpha/logo.svg');
@endphp

<header class="site-header" data-front-sticky-header data-alpha-header>
    <div class="header-inner">
        <a class="brand" href="{{ route('home') }}" aria-label="Alpha Capitalis — početna">
            <img src="{{ $alphaBrandLogoUrl }}" alt="{{ $alphaBrandName }}" width="300" height="80">
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
            <button class="search-link" type="button" aria-label="Pretraga" aria-controls="alpha-header-search-panel" aria-expanded="false" data-header-search-toggle>
                <i class="fa-light fa-magnifying-glass" aria-hidden="true"></i>
            </button>
        </div>

        <div class="header-mobile-actions">
            <button class="search-link search-link--mobile" type="button" aria-label="Pretraga" aria-controls="alpha-header-search-panel" aria-expanded="false" data-header-search-toggle>
                <i class="fa-light fa-magnifying-glass" aria-hidden="true"></i>
            </button>
            <button
                class="menu-toggle"
                type="button"
                aria-label="Otvori izbornik"
                aria-expanded="false"
                data-alpha-menu-toggle
            >
                <i class="fa-light fa-bars menu-toggle-open-icon" aria-hidden="true"></i>
                <i class="fa-light fa-xmark menu-toggle-close-icon" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <div
        class="mobile-menu"
        role="dialog"
        aria-modal="true"
        aria-label="{{ $alphaIsCroatian ? 'Mobilni izbornik' : 'Mobile menu' }}"
        aria-hidden="true"
        data-alpha-mobile-menu
        data-alpha-initial-panel="{{ $alphaServiceNavigation->contains('active', true) ? 'services' : 'root' }}"
    >
        <div class="mobile-menu-inner">
            <div class="mobile-menu-topbar">
                <a class="mobile-menu-brand" href="{{ route('home') }}" aria-label="Alpha Capitalis — početna">
                    <img src="{{ asset('front-theme/images/branding/znak-ac.svg') }}" alt="Alpha Capitalis" width="74" height="74">
                </a>
            </div>

            <div class="mobile-menu-content">
                <div class="mobile-menu-panels" data-alpha-menu-panels>
                    <section class="mobile-menu-panel mobile-menu-panel--root" aria-label="{{ $alphaIsCroatian ? 'Glavna navigacija' : 'Main navigation' }}" data-alpha-menu-panel="root">
                        <p class="mobile-menu-eyebrow" aria-hidden="true"><span></span> {{ $alphaIsCroatian ? 'Glavna navigacija' : 'Main navigation' }}</p>

                        <nav class="mobile-menu-nav" aria-label="Mobilna navigacija">
                            @foreach ($alphaNavigation as $item)
                                @if ($item['is_services'])
                                    <div @class(['mobile-menu-item', 'mobile-menu-parent-row', 'is-active' => $item['active']])>
                                        <a
                                            href="{{ $item['url'] }}"
                                            @class(['mobile-menu-link', 'is-active' => $item['active']])
                                            @if ($item['current']) aria-current="page" @endif
                                            @if (! empty($item['open_in_new_tab'])) target="_blank" rel="noopener noreferrer" @endif
                                        >
                                            <span class="mobile-menu-label">{{ $item['label'] }}</span>
                                        </a>
                                        <button
                                            class="mobile-menu-next"
                                            type="button"
                                            aria-label="{{ $alphaIsCroatian ? 'Otvori usluge' : 'Open services' }}"
                                            aria-controls="alpha-mobile-services-panel"
                                            aria-expanded="false"
                                            data-alpha-submenu-open
                                        >
                                            <i class="fa-light fa-plus" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                @else
                                    <a
                                        href="{{ $item['url'] }}"
                                        @class(['mobile-menu-item', 'mobile-menu-link', 'is-active' => $item['active']])
                                        @if ($item['current']) aria-current="page" @endif
                                        @if (! empty($item['open_in_new_tab'])) target="_blank" rel="noopener noreferrer" @endif
                                    >
                                        <span class="mobile-menu-label">{{ $item['label'] }}</span>
                                    </a>
                                @endif
                            @endforeach

                            <a class="mobile-menu-item mobile-menu-link mobile-menu-link--offer" href="{{ $alphaOfferUrl }}">
                                <span class="mobile-menu-label">{{ $alphaIsCroatian ? 'Zatraži ponudu' : 'Request an offer' }}</span>
                            </a>

                            @if ($alphaShowLeaseCalculator)
                                <a class="mobile-menu-item mobile-menu-link" href="{{ route('lease-calculator.show') }}">
                                    <span class="mobile-menu-label">MSFI 16 Kalkulator</span>
                                </a>
                            @endif
                        </nav>
                    </section>

                    <section id="alpha-mobile-services-panel" class="mobile-menu-panel mobile-menu-panel--services" aria-label="{{ $alphaIsCroatian ? 'Usluge' : 'Services' }}" aria-hidden="true" data-alpha-menu-panel="services">
                        <button class="mobile-menu-back" type="button" data-alpha-submenu-close>
                            <i class="fa-light fa-arrow-left-long" aria-hidden="true"></i>
                            <span>{{ $alphaIsCroatian ? 'Natrag' : 'Back' }}</span>
                        </button>

                        <nav class="mobile-menu-subnav" aria-label="{{ $alphaIsCroatian ? 'Usluge' : 'Services' }}">
                            @foreach ($alphaServiceNavigation as $serviceItem)
                                <a href="{{ $serviceItem['url'] }}" @class(['mobile-menu-subnav-link', 'is-active' => $serviceItem['active']]) @if ($serviceItem['active']) aria-current="page" @endif>
                                    <span>{{ $serviceItem['label'] }}</span>
                                </a>
                            @endforeach
                        </nav>
                    </section>
                </div>

                <div class="mobile-menu-status" aria-hidden="true">
                    <span class="mobile-menu-status-dot"></span>
                    <span>Alpha Capitalis / {{ strtoupper((string) app()->getLocale()) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div
        id="alpha-header-search-panel"
        class="alpha-search-panel"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('ui.search.title') }}"
        aria-hidden="true"
        data-header-search-panel
    >
        <button class="alpha-search-close" type="button" aria-label="{{ $alphaIsCroatian ? 'Zatvori pretragu' : 'Close search' }}" data-header-search-close>
            <i class="fa-light fa-xmark" aria-hidden="true"></i>
        </button>

        <div class="alpha-search-dialog">
            <p class="alpha-search-eyebrow">{{ __('ui.search.title') }}</p>
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
                        aria-controls="alpha-header-search-suggestions"
                        aria-expanded="false"
                        data-header-search-input
                    >
                </div>
                <div
                    id="alpha-header-search-suggestions"
                    class="front-search-suggestions hidden"
                    aria-live="polite"
                    data-header-search-suggestions
                ></div>
            </form>
        </div>
    </div>
</header>
