<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('front.partials.seo-meta')
    @include('front.partials.schema-markup')
    @include('front.partials.analytics')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="{{ asset('front-theme/styles/rising-sun-font.css') }}">
    @if (!empty($storeSettings['branding']['favicons']['ico_url'] ?? null))
        <link rel="icon" href="{{ $storeSettings['branding']['favicons']['ico_url'] }}" sizes="any">
    @endif
    @if (!empty($storeSettings['branding']['favicons']['32_url'] ?? null))
        <link rel="icon" type="image/png" sizes="32x32" href="{{ $storeSettings['branding']['favicons']['32_url'] }}">
    @endif
    @if (!empty($storeSettings['branding']['favicons']['16_url'] ?? null))
        <link rel="icon" type="image/png" sizes="16x16" href="{{ $storeSettings['branding']['favicons']['16_url'] }}">
    @endif
    @if (!empty($storeSettings['branding']['favicons']['180_url'] ?? null))
        <link rel="apple-touch-icon" sizes="180x180" href="{{ $storeSettings['branding']['favicons']['180_url'] }}">
    @endif
    @if (!empty($storeSettings['branding']['favicons']['192_url'] ?? null))
        <link rel="icon" type="image/png" sizes="192x192" href="{{ $storeSettings['branding']['favicons']['192_url'] }}">
    @endif
    @if (!empty($storeSettings['branding']['favicons']['512_url'] ?? null))
        <link rel="icon" type="image/png" sizes="512x512" href="{{ $storeSettings['branding']['favicons']['512_url'] }}">
    @endif
    @if (empty($storeSettings['branding']['favicons']['ico_url'] ?? null) && !empty($storeSettings['branding']['favicon_url'] ?? null))
        <link rel="icon" href="{{ $storeSettings['branding']['favicon_url'] }}">
    @endif
    <link rel="manifest" href="{{ route('front.manifest') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $mainNavigation = app(\App\Services\Front\NavigationMenuService::class)->forLocale((string) app()->getLocale());
@endphp
<body class="front-desktop-shell min-h-screen overflow-x-hidden antialiased">
<header class="front-site-header sticky top-0 z-40 border-b">
    @if ((bool) ($storeSettings['announcement']['enabled'] ?? true))
        <div class="front-announcement py-2 text-center text-xs font-semibold uppercase tracking-[0.18em]">
            @php
                $announcementText = (string) ($storeSettings['announcement']['text'] ?? __('ui.front.desktop.promo_bar'));
                $announcementUrl = trim((string) ($storeSettings['announcement']['url'] ?? ''));
                $announcementNewTab = (bool) ($storeSettings['announcement']['new_tab'] ?? false);
            @endphp
            @if ($announcementUrl !== '')
                <a href="{{ $announcementUrl }}" class="hover:underline" @if($announcementNewTab) target="_blank" rel="noopener noreferrer" @endif>
                    {{ $announcementText }}
                </a>
            @else
                {{ $announcementText }}
            @endif
        </div>
    @endif

    <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="front-logo inline-flex items-center py-4 text-2xl font-black sm:text-4xl">
            @if (!empty($storeSettings['branding']['logo_url'] ?? null))
                <img src="{{ $storeSettings['branding']['logo_url'] }}" alt="{{ $storeSettings['branding']['store_name'] ?? config('app.name', 'AG Info') }}" class="h-9 w-auto object-contain sm:h-11">
            @else
                {{ (string) ($storeSettings['branding']['store_name'] ?? config('app.name', 'AG Info')) }}
            @endif
        </a>

        <nav class="front-nav relative hidden flex-1 items-center justify-center gap-5 px-3 text-sm font-semibold uppercase tracking-wide lg:flex">
            @include('front.desktop.partials.main-nav')
        </nav>

        <div class="hidden min-h-[68px] items-stretch lg:flex">
            @php
                $activeLocale = (string) ($frontLocale ?? app()->getLocale());
                $switchLanguage = collect($frontLanguages ?? [])->first(
                    static fn (array $language): bool => (string) ($language['code'] ?? '') !== $activeLocale
                );
                $canOpenAdmin = auth()->check() && (auth()->user()->isA('superadmin') || auth()->user()->can('admin.access'));
            @endphp
            @if ($switchLanguage)
                <div class="front-top-action inline-flex w-[72px] items-center justify-center text-xs font-semibold uppercase tracking-wide">
                    <a href="{{ route('front.locale.switch', ['code' => $switchLanguage['code']]) }}" class="text-white/80 hover:text-white" hreflang="{{ $switchLanguage['code'] }}">
                        {{ strtoupper((string) $switchLanguage['code']) }}
                    </a>
                </div>
            @endif

            @auth
                <a href="{{ route('account.dashboard') }}" class="front-top-action inline-flex min-w-[128px] items-center justify-center gap-2 px-4 text-sm transition">
                    {{ __('ui.front.desktop.account') }}
                </a>
            @else
                <a href="{{ route('front.auth.login') }}" class="front-top-action inline-flex min-w-[128px] items-center justify-center gap-2 px-4 text-sm transition">
                    {{ __('ui.front.desktop.account') }}
                </a>
            @endauth

            @if ($canOpenAdmin)
                <a href="{{ route('admin.dashboard') }}" class="front-top-action front-admin-action inline-flex min-w-[112px] items-center justify-center gap-2 px-4 text-sm transition">
                    Admin
                </a>
            @endif
        </div>

        <div class="flex min-h-[68px] items-stretch lg:hidden">
            @auth
                <a href="{{ route('account.dashboard') }}" class="front-top-action inline-flex w-12 items-center justify-center transition sm:w-14" aria-label="{{ __('ui.front.desktop.account') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 20c1.6-3.2 4.3-5 8-5s6.4 1.8 8 5"></path>
                    </svg>
                </a>
            @else
                <a href="{{ route('front.auth.login') }}" class="front-top-action inline-flex w-12 items-center justify-center transition sm:w-14" aria-label="{{ __('ui.front.desktop.sign_in') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 20c1.6-3.2 4.3-5 8-5s6.4 1.8 8 5"></path>
                    </svg>
                </a>
            @endauth

            <button type="button" class="front-top-action flex h-full w-12 items-center justify-center transition sm:w-14" aria-label="{{ __('ui.front.desktop.open_navigation') }}" data-mobile-menu-open>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 7h16M4 12h16M4 17h16"></path>
                </svg>
            </button>
        </div>
    </div>
</header>

<div class="pointer-events-none fixed inset-0 z-[60] lg:hidden" data-mobile-menu-root>
    <button type="button" class="front-mobile-menu-backdrop absolute inset-0 opacity-0 transition-opacity duration-300" aria-label="{{ __('ui.front.desktop.close_navigation') }}" data-mobile-menu-close></button>
    <aside class="front-mobile-menu-panel absolute inset-y-0 left-0 flex w-[90vw] max-w-md -translate-x-full flex-col shadow-2xl transition-transform duration-300 ease-out" data-mobile-menu-panel>
        <div class="flex items-center justify-between border-b border-white/10 px-4 py-4">
            <span class="text-xl font-black tracking-tight text-white">{{ (string) ($storeSettings['branding']['store_name'] ?? 'AG Info') }}</span>
            <button type="button" class="inline-flex h-10 w-10 items-center justify-center border border-white/20 text-white/80 transition hover:bg-white/10 hover:text-white" aria-label="{{ __('ui.front.desktop.close_navigation') }}" data-mobile-menu-close>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18"></path>
                </svg>
            </button>
        </div>
        @include('front.desktop.partials.main-nav-mobile')
    </aside>
</div>

<main class="front-content-shell @yield('main_class', 'mx-auto w-full max-w-7xl px-6 py-10')">
    @include('front.desktop.partials.flash')
    @yield('content')
</main>

<footer class="front-footer {{ request()->routeIs('home') ? 'mt-8' : 'mt-16' }} border-t">
    <div class="mx-auto w-full max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-8 md:grid-cols-3">
            <div>
                <p class="front-kicker">{{ (string) ($storeSettings['branding']['store_name'] ?? config('app.name', 'AG Info')) }}</p>
                <p class="front-footer-muted mt-2 text-sm">{{ __('Product-free content platform baseline for pages, blogs, and campaigns.') }}</p>
            </div>

            @foreach ((array) ($storeSettings['footer']['link_columns'] ?? []) as $column)
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white">{{ (string) ($column['title'] ?? '') }}</h3>
                    <ul class="front-footer-muted mt-2 space-y-1 text-sm">
                        @foreach ((array) ($column['links'] ?? []) as $link)
                            @php
                                $url = trim((string) ($link['url'] ?? ''));
                                $label = trim((string) ($link['label'] ?? ''));
                            @endphp
                            @continue($url === '' || $label === '')
                            <li><a href="{{ $url }}" class="hover:text-fuchsia-300">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <div class="front-footer-muted mt-8 border-t border-white/10 pt-5 text-xs">
            {{ (string) ($storeSettings['footer']['bottom_copyright_text'] ?? ('© '.now()->year.' '.config('app.name', 'AG Info'))) }}
        </div>
    </div>
</footer>
</body>
</html>
