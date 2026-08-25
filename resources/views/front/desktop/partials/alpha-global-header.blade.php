@php
    $alphaCurrentUrl = rtrim(url()->current(), '/');
    $alphaLocale = strtolower((string) app()->getLocale());
    $alphaNavigationService = app(\App\Services\Front\NavigationMenuService::class);
    $alphaServicesUrl = rtrim($alphaNavigationService->servicePageUrlForLocale('services', $alphaLocale), '/');
    $alphaChrome = $alphaNavigationService->chromeForLocale($alphaLocale);
    $alphaServiceNavigation = collect($alphaNavigationService->serviceNavigationForLocale($alphaLocale))
        ->map(static function (array $item): array {
        return array_merge($item, [
            'active' => request()->routeIs($item['route_pattern']),
        ]);
    });
    $alphaLanguages = collect($frontLanguages ?? [])
        ->filter(static fn ($language): bool => is_array($language) && trim((string) ($language['code'] ?? '')) !== '')
        ->map(static function (array $language) use ($alphaNavigationService): array {
            $code = strtolower(trim((string) $language['code']));

            return [
                'code' => $code,
                'label' => trim((string) ($language['label'] ?? '')),
                'redirect_url' => $alphaNavigationService->localizedCurrentUrlForLocale($code),
            ];
        })
        ->unique('code')
        ->values();
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
                'has_services_dropdown' => $isServices
                    && (bool) ($item['show_dropdown'] ?? true)
                    && $alphaServiceNavigation->isNotEmpty(),
                'current' => $normalizedHref !== '' && $normalizedHref === $alphaCurrentUrl,
                'active' => ($normalizedHref !== '' && $normalizedHref === $alphaCurrentUrl)
                    || ($isServices && $alphaServiceNavigation->contains('active', true)),
            ]);
        })
        ->values();
    $alphaServicesDropdownEnabled = $alphaNavigation->contains('has_services_dropdown', true);
    $alphaOfferUrl = route('assessment.create');
    $alphaShowLeaseCalculator = request()->routeIs('accounting.show', 'accounting.show.en');
    $alphaPrimaryCtaUrl = $alphaShowLeaseCalculator ? route('lease-calculator.show') : $alphaOfferUrl;
    $alphaOfferCtaLabel = trim((string) ($alphaChrome['header_primary_cta_label'] ?? ''));
    $alphaCalculatorCtaLabel = trim((string) ($alphaChrome['header_calculator_cta_label'] ?? ''));
    $alphaPrimaryCtaLabel = $alphaShowLeaseCalculator ? $alphaCalculatorCtaLabel : $alphaOfferCtaLabel;
    $alphaBrandName = trim((string) ($storeSettings['branding']['store_name'] ?? '')) ?: 'Alpha Capitalis';
    $alphaBrandLogoUrl = trim((string) ($storeSettings['branding']['logo_url'] ?? '')) ?: asset('alpha/logo.svg');
@endphp

<header class="site-header" data-front-sticky-header data-alpha-header>
    <div class="header-inner">
        <a class="brand" href="{{ route('home') }}" aria-label="{{ __('ui.alpha_chrome.header.home_aria') }}">
            <img src="{{ $alphaBrandLogoUrl }}" alt="{{ $alphaBrandName }}" width="300" height="80">
        </a>

        <nav class="desktop-nav" aria-label="{{ __('ui.alpha_chrome.header.main_navigation_aria') }}">
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
            @if ($alphaLanguages->count() > 1)
                <nav class="header-language-switch" aria-label="{{ __('ui.alpha_chrome.header.language_switch_aria') }}">
                    @foreach ($alphaLanguages as $language)
                        <a
                            href="{{ route('front.locale.switch', ['code' => $language['code'], 'redirect' => $language['redirect_url']]) }}"
                            hreflang="{{ $language['code'] }}"
                            lang="{{ $language['code'] }}"
                            title="{{ $language['label'] }}"
                            @class(['is-active' => $alphaLocale === $language['code']])
                            @if ($alphaLocale === $language['code']) aria-current="true" @endif
                        >{{ strtoupper($language['code']) }}</a>
                    @endforeach
                </nav>
            @endif
            @if ($alphaPrimaryCtaLabel !== '')
                <a @class(['header-cta', 'header-cta--calculator' => $alphaShowLeaseCalculator]) href="{{ $alphaPrimaryCtaUrl }}">
                    <span>{{ $alphaPrimaryCtaLabel }}</span>
                </a>
            @endif
            <button class="search-link" type="button" aria-label="{{ __('ui.alpha_chrome.header.search_aria') }}" aria-controls="alpha-header-search-panel" aria-expanded="false" data-header-search-toggle>
                <i class="fa-light fa-magnifying-glass" aria-hidden="true"></i>
            </button>
        </div>

        <div class="header-mobile-actions">
            <button class="search-link search-link--mobile" type="button" aria-label="{{ __('ui.alpha_chrome.header.search_aria') }}" aria-controls="alpha-header-search-panel" aria-expanded="false" data-header-search-toggle>
                <i class="fa-light fa-magnifying-glass" aria-hidden="true"></i>
            </button>
            <button
                class="menu-toggle"
                type="button"
                aria-label="{{ __('ui.alpha_chrome.header.open_menu_aria') }}"
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
        aria-label="{{ __('ui.alpha_chrome.header.mobile_menu_aria') }}"
        aria-hidden="true"
        data-alpha-mobile-menu
        data-alpha-initial-panel="{{ $alphaServicesDropdownEnabled && $alphaServiceNavigation->contains('active', true) ? 'services' : 'root' }}"
    >
        <div class="mobile-menu-inner">
            <div class="mobile-menu-topbar">
                <a class="mobile-menu-brand" href="{{ route('home') }}" aria-label="{{ __('ui.alpha_chrome.header.home_aria') }}">
                    <img src="{{ asset('front-theme/images/branding/znak-ac.svg') }}" alt="Alpha Capitalis" width="74" height="74">
                </a>
                @if ($alphaLanguages->count() > 1)
                    <nav class="mobile-menu-language-switch" aria-label="{{ __('ui.alpha_chrome.header.language_switch_aria') }}">
                        @foreach ($alphaLanguages as $language)
                            <a
                                href="{{ route('front.locale.switch', ['code' => $language['code'], 'redirect' => $language['redirect_url']]) }}"
                                hreflang="{{ $language['code'] }}"
                                lang="{{ $language['code'] }}"
                                title="{{ $language['label'] }}"
                                @class(['is-active' => $alphaLocale === $language['code']])
                                @if ($alphaLocale === $language['code']) aria-current="true" @endif
                            >{{ strtoupper($language['code']) }}</a>
                        @endforeach
                    </nav>
                @endif
            </div>

            <div class="mobile-menu-content">
                <div class="mobile-menu-panels" data-alpha-menu-panels>
                    <section class="mobile-menu-panel mobile-menu-panel--root" aria-label="{{ __('ui.alpha_chrome.header.main_navigation_aria') }}" data-alpha-menu-panel="root">
                        <p class="mobile-menu-eyebrow" aria-hidden="true"><span></span> {{ __('ui.alpha_chrome.header.main_navigation_aria') }}</p>

                        <nav class="mobile-menu-nav" aria-label="{{ __('ui.alpha_chrome.header.mobile_navigation_aria') }}">
                            @foreach ($alphaNavigation as $item)
                                @if ($item['has_services_dropdown'])
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
                                            aria-label="{{ __('ui.alpha_chrome.header.open_services_aria') }}"
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

                            @if ($alphaOfferCtaLabel !== '')
                                <a class="mobile-menu-item mobile-menu-link mobile-menu-link--offer" href="{{ $alphaOfferUrl }}">
                                    <span class="mobile-menu-label">{{ $alphaOfferCtaLabel }}</span>
                                </a>
                            @endif

                            @if ($alphaShowLeaseCalculator && $alphaCalculatorCtaLabel !== '')
                                <a class="mobile-menu-item mobile-menu-link" href="{{ route('lease-calculator.show') }}">
                                    <span class="mobile-menu-label">{{ $alphaCalculatorCtaLabel }}</span>
                                </a>
                            @endif
                        </nav>
                    </section>

                    @if ($alphaServicesDropdownEnabled)
                    <section id="alpha-mobile-services-panel" class="mobile-menu-panel mobile-menu-panel--services" aria-label="{{ __('ui.alpha_chrome.header.services_aria') }}" aria-hidden="true" data-alpha-menu-panel="services">
                        <button class="mobile-menu-back" type="button" data-alpha-submenu-close>
                            <i class="fa-light fa-arrow-left-long" aria-hidden="true"></i>
                            <span>{{ __('ui.alpha_chrome.header.back_label') }}</span>
                        </button>

                        <nav class="mobile-menu-subnav" aria-label="{{ __('ui.alpha_chrome.header.services_aria') }}">
                            @foreach ($alphaServiceNavigation as $serviceItem)
                                <a href="{{ $serviceItem['url'] }}" @class(['mobile-menu-subnav-link', 'is-active' => $serviceItem['active']]) @if ($serviceItem['active']) aria-current="page" @endif>
                                    <span>{{ $serviceItem['label'] }}</span>
                                </a>
                            @endforeach
                        </nav>
                    </section>
                    @endif
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
        <button class="alpha-search-close" type="button" aria-label="{{ __('ui.alpha_chrome.header.close_search_aria') }}" data-header-search-close>
            <i class="fa-light fa-xmark" aria-hidden="true"></i>
        </button>

        <div class="alpha-search-dialog">
            <p class="alpha-search-eyebrow">{{ __('ui.search.title') }}</p>
            <form
                action="{{ \App\Support\Localization\FrontendRoute::url('search.index') }}"
                method="get"
                class="alpha-search-form"
                role="search"
                data-header-search-form
                data-search-suggest-endpoint="{{ route('search.suggest') }}"
                data-search-results-endpoint="{{ \App\Support\Localization\FrontendRoute::url('search.index') }}"
            >
                <div class="alpha-search-field">
                    <label for="alpha-header-search-input" class="visually-hidden">{{ __('ui.alpha_chrome.header.search_content_label') }}</label>
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
