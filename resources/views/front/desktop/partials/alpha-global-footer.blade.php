@php
    $alphaFooterLocale = strtolower((string) app()->getLocale());
    $alphaFooterDefaultLocale = strtolower((string) ($frontDefaultLocale ?? config('app.fallback_locale', 'hr')));
    $alphaFooterIsDefaultLocale = $alphaFooterLocale === $alphaFooterDefaultLocale;
    $alphaFooterNavigationService = app(\App\Services\Front\NavigationMenuService::class);
    $alphaFooterChrome = $alphaFooterNavigationService->chromeForLocale($alphaFooterLocale);
    $alphaFooterLocation = $alphaFooterNavigationService->exactFooterLocationForLocale(
        $alphaFooterLocale,
        'alpha-capitalis',
    );
    $alphaFooterPhone = trim((string) ($storeSettings['footer']['phone'] ?? ''));
    $alphaFooterEmail = trim((string) ($storeSettings['footer']['email_support'] ?? ''));
    $alphaFooterSalesEmail = trim((string) ($storeSettings['footer']['email_sales'] ?? ''));
    $alphaFooterHours = trim((string) ($alphaFooterChrome['footer_hours'] ?? ''))
        ?: ($alphaFooterIsDefaultLocale ? trim((string) ($storeSettings['footer']['hours'] ?? '')) : '');
    $alphaFooterCopyright = trim((string) ($alphaFooterChrome['footer_copyright_text'] ?? ''))
        ?: ($alphaFooterIsDefaultLocale ? trim((string) ($storeSettings['footer']['bottom_copyright_text'] ?? '')) : '');
    $alphaFooterBrandName = trim((string) ($storeSettings['branding']['store_name'] ?? '')) ?: 'Alpha Capitalis';
    $alphaFooterBrandLogoUrl = trim((string) ($storeSettings['branding']['logo_url'] ?? '')) ?: asset('alpha/logo.svg');
    $alphaFooterAddress = trim((string) ($alphaFooterLocation['address'] ?? ''));
    $alphaFooterMapQuery = trim((string) ($alphaFooterLocation['map_query'] ?? '')) ?: $alphaFooterAddress;
    $alphaFooterMap = $alphaFooterMapQuery !== ''
        ? 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($alphaFooterMapQuery)
        : '';
    $alphaFooterHome = route('home');
    $alphaFooterServicesUrl = $alphaFooterNavigationService->servicePageUrlForLocale('services', $alphaFooterLocale);
    $alphaFooterExcludedUrls = collect([$alphaFooterHome, $alphaFooterServicesUrl])
        ->filter()
        ->map(static fn (string $url): string => rtrim($url, '/'));
    $alphaFooterNavigation = collect($mainNavigation ?? [])
        ->filter(static fn ($item): bool => is_array($item)
            && trim((string) ($item['label'] ?? '')) !== ''
            && trim((string) ($item['url'] ?? '')) !== '')
        ->reject(static fn (array $item): bool => $alphaFooterExcludedUrls->contains(rtrim(url((string) $item['url']), '/')))
        ->values();
    $alphaFooterServices = collect($alphaFooterNavigationService->serviceNavigationForLocale($alphaFooterLocale));
    $alphaFooterSocials = collect([
        ['label' => 'X', 'icon' => 'fa-x-twitter', 'url' => trim((string) ($storeSettings['branding']['social']['x']['url'] ?? '')), 'enabled' => (bool) ($storeSettings['branding']['social']['x']['enabled'] ?? true)],
        ['label' => 'Facebook', 'icon' => 'fa-facebook-f', 'url' => trim((string) ($storeSettings['branding']['social']['facebook']['url'] ?? '')), 'enabled' => (bool) ($storeSettings['branding']['social']['facebook']['enabled'] ?? true)],
        ['label' => 'LinkedIn', 'icon' => 'fa-linkedin-in', 'url' => trim((string) ($storeSettings['branding']['social']['linkedin']['url'] ?? '')), 'enabled' => (bool) ($storeSettings['branding']['social']['linkedin']['enabled'] ?? true)],
        ['label' => 'Instagram', 'icon' => 'fa-instagram', 'url' => trim((string) ($storeSettings['branding']['social']['instagram']['url'] ?? '')), 'enabled' => (bool) ($storeSettings['branding']['social']['instagram']['enabled'] ?? true)],
        ['label' => 'TikTok', 'icon' => 'fa-tiktok', 'url' => trim((string) ($storeSettings['branding']['social']['tiktok']['url'] ?? '')), 'enabled' => (bool) ($storeSettings['branding']['social']['tiktok']['enabled'] ?? true)],
        ['label' => 'YouTube', 'icon' => 'fa-youtube', 'url' => trim((string) ($storeSettings['branding']['social']['youtube']['url'] ?? '')), 'enabled' => (bool) ($storeSettings['branding']['social']['youtube']['enabled'] ?? true)],
    ])->filter(fn (array $social): bool => $social['enabled'] && $social['url'] !== '')->values();
    $alphaFooterLegalLinks = collect($storeSettings['footer']['bottom_links'] ?? [])->filter(
        static fn ($item): bool => is_array($item) && trim((string) ($item['label'] ?? '')) !== '' && trim((string) ($item['url'] ?? '')) !== ''
    )->values();

    if ($alphaFooterLegalLinks->isEmpty()) {
        $alphaFooterLegalLinks = collect($alphaFooterNavigationService->defaultFooterLegalNavigationForLocale($alphaFooterLocale));
    }

    $alphaFooterNewsletterLabel = trim((string) ($alphaFooterChrome['footer_newsletter_label'] ?? ''));
    $alphaFooterNewsletterTitle = trim((string) ($alphaFooterChrome['footer_newsletter_title'] ?? ''));
    $alphaFooterNewsletterAccent = trim((string) ($alphaFooterChrome['footer_newsletter_accent'] ?? ''));
    $alphaFooterEmailPlaceholder = trim((string) ($alphaFooterChrome['footer_newsletter_email_placeholder'] ?? ''));
    $alphaFooterNewsletterSubmitLabel = trim((string) ($alphaFooterChrome['footer_newsletter_submit_label'] ?? ''));
    $alphaFooterNewsletterConsent = trim((string) ($alphaFooterChrome['footer_newsletter_consent'] ?? ''));
    $alphaFooterTagline = trim((string) ($alphaFooterChrome['footer_tagline'] ?? ''));
    $alphaFooterServicesLabel = trim((string) ($alphaFooterChrome['footer_services_label'] ?? ''));
    $alphaFooterContactLabel = trim((string) ($alphaFooterChrome['footer_contact_label'] ?? ''));
    $alphaFooterCookieSettingsLabel = trim((string) ($alphaFooterChrome['footer_cookie_settings_label'] ?? ''));
    $alphaFooterBackToTopLabel = trim((string) ($alphaFooterChrome['footer_back_to_top_label'] ?? ''));
    $alphaShowFooterNewsletter = $alphaFooterNewsletterTitle !== ''
        && $alphaFooterEmailPlaceholder !== ''
        && $alphaFooterNewsletterSubmitLabel !== ''
        && $alphaFooterNewsletterConsent !== '';
    $alphaFooterNewsletterSuccess = trim((string) session('newsletter_success', ''));
    $alphaFooterNewsletterError = trim((string) session('newsletter_error', ''));
    $alphaFooterNewsletterFeedback = $alphaFooterNewsletterSuccess !== ''
        ? $alphaFooterNewsletterSuccess
        : $alphaFooterNewsletterError;
    $alphaFooterNewsletterFeedbackState = $alphaFooterNewsletterSuccess !== '' ? 'success' : 'error';
@endphp

<footer class="site-footer" data-image-reveal>
    <div class="footer-shell">
        @if (! $__env->hasSection('hide_footer_newsletter') && $alphaShowFooterNewsletter)
        <section class="footer-newsletter" id="newsletter" aria-labelledby="footer-newsletter-title" data-image-reveal>
            <div class="footer-newsletter-copy">
                @if ($alphaFooterNewsletterLabel !== '')
                    <span class="footer-label">{{ $alphaFooterNewsletterLabel }}</span>
                @endif
                <h2 id="footer-newsletter-title">
                    {{ $alphaFooterNewsletterTitle }} @if ($alphaFooterNewsletterAccent !== '')<span class="footer-newsletter-accent">{{ $alphaFooterNewsletterAccent }}</span>@endif
                </h2>
            </div>
            <form
                action="{{ \App\Support\Localization\FrontendRoute::url('newsletter.subscribe') }}"
                method="post"
                novalidate
                data-newsletter-form
                data-msg-email-required="{{ __('contact.validation.inline.email_required') }}"
                data-msg-email-invalid="{{ __('contact.validation.inline.email_invalid') }}"
                data-msg-consent-required="{{ __('ui.alpha_chrome.footer.newsletter_consent_required') }}"
                data-msg-submitting="{{ __('ui.alpha_chrome.footer.newsletter_submitting') }}"
                data-msg-submit-success="{{ __('ui.alpha_chrome.footer.newsletter_success') }}"
                data-msg-submit-failed="{{ __('ui.alpha_chrome.footer.newsletter_error') }}"
            >
                @csrf
                <div class="footer-newsletter-honeypot" aria-hidden="true">
                    <label for="newsletter-website">Website</label>
                    <input id="newsletter-website" name="website" type="text" tabindex="-1" autocomplete="off">
                </div>
                <label class="visually-hidden" for="newsletter-email">{{ $alphaFooterEmailPlaceholder }}</label>
                <div class="footer-newsletter-field">
                    <i class="fa-light fa-envelope" aria-hidden="true"></i>
                    <input id="newsletter-email" name="email" type="email" autocomplete="email" placeholder="{{ $alphaFooterEmailPlaceholder }}" required aria-describedby="newsletter-email-error newsletter-feedback" aria-invalid="false">
                    <button type="submit" aria-label="{{ $alphaFooterNewsletterSubmitLabel }}">
                        <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </div>
                <label class="footer-newsletter-consent">
                    <input name="consent" type="checkbox" value="1" required aria-describedby="newsletter-email-error newsletter-feedback">
                    <span>{{ $alphaFooterNewsletterConsent }}</span>
                </label>
                <p id="newsletter-email-error" class="footer-newsletter-error" data-newsletter-error role="alert" aria-live="polite" hidden></p>
                <p
                    id="newsletter-feedback"
                    class="footer-newsletter-feedback"
                    data-newsletter-feedback
                    data-state="{{ $alphaFooterNewsletterFeedbackState }}"
                    role="status"
                    aria-live="polite"
                    @if ($alphaFooterNewsletterFeedback === '') hidden @endif
                >{{ $alphaFooterNewsletterFeedback }}</p>
            </form>
        </section>
        @endif

        <div class="footer-main" data-image-reveal>
            <div class="footer-brand-block content-reveal animation-index-0" data-image-reveal>
                <a class="footer-brand" href="{{ $alphaFooterHome }}" aria-label="{{ __('ui.alpha_chrome.footer.home_aria') }}">
                    <img src="{{ $alphaFooterBrandLogoUrl }}" alt="{{ $alphaFooterBrandName }}" width="300" height="80">
                </a>
                @if ($alphaFooterTagline !== '')
                    <p>{{ $alphaFooterTagline }}</p>
                @endif
                @if ($alphaFooterSocials->isNotEmpty())
                    <div class="footer-socials" aria-label="{{ __('ui.alpha_chrome.footer.socials_aria') }}">
                        @foreach ($alphaFooterSocials as $social)
                            <a href="{{ $social['url'] }}" aria-label="{{ $social['label'] }}" title="{{ $social['label'] }}" target="_blank" rel="noopener noreferrer">
                                <i class="fa-brands {{ $social['icon'] }}" aria-hidden="true"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="footer-desktop-only footer-nav-block content-reveal animation-index-1" data-image-reveal>
                <span class="footer-label">{{ $alphaFooterBrandName }}</span>
                <nav aria-label="{{ __('ui.alpha_chrome.footer.navigation_aria') }}">
                    @foreach ($alphaFooterNavigation as $item)
                        <a href="{{ $item['url'] }}" @if (! empty($item['open_in_new_tab'])) target="_blank" rel="noopener noreferrer" @endif>{{ $item['label'] }}</a>
                    @endforeach
                </nav>
            </div>

            @if ($alphaFooterServicesLabel !== '' && $alphaFooterServices->isNotEmpty())
            <div class="footer-desktop-only footer-services-block content-reveal animation-index-2" data-image-reveal>
                <span class="footer-label">{{ $alphaFooterServicesLabel }}</span>
                <nav aria-label="{{ __('ui.alpha_chrome.footer.services_aria') }}">
                    @foreach ($alphaFooterServices as $item)
                        <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                    @endforeach
                </nav>
            </div>
            @endif

            @if ($alphaFooterContactLabel !== '')
            <div class="footer-desktop-only footer-contact-block content-reveal animation-index-3" data-image-reveal>
                <span class="footer-label">{{ $alphaFooterContactLabel }}</span>
                <address class="footer-contact">
                    @if ($alphaFooterAddress !== '' && $alphaFooterMap !== '')
                        <a href="{{ $alphaFooterMap }}" target="_blank" rel="noopener noreferrer">{{ $alphaFooterAddress }}</a>
                    @endif
                    @if ($alphaFooterPhone !== '')
                        <a href="tel:{{ preg_replace('/[^+0-9]/', '', $alphaFooterPhone) }}">{{ $alphaFooterPhone }}</a>
                    @endif
                    @if ($alphaFooterEmail !== '')
                        <a href="mailto:{{ $alphaFooterEmail }}">{{ $alphaFooterEmail }}</a>
                    @endif
                    @if ($alphaFooterSalesEmail !== '' && $alphaFooterSalesEmail !== $alphaFooterEmail)
                        <a href="mailto:{{ $alphaFooterSalesEmail }}">{{ $alphaFooterSalesEmail }}</a>
                    @endif
                    @if ($alphaFooterHours !== '')
                        <p>{{ $alphaFooterHours }}</p>
                    @endif
                </address>
            </div>
            @endif

            <details class="footer-mobile-only footer-accordion footer-nav-block content-reveal animation-index-1" data-image-reveal>
                <summary class="footer-label"><span>{{ $alphaFooterBrandName }}</span><i class="fa-light fa-plus" aria-hidden="true"></i></summary>
                <nav class="footer-accordion-content" aria-label="{{ __('ui.alpha_chrome.footer.navigation_aria') }}">
                    @foreach ($alphaFooterNavigation as $item)
                        <a href="{{ $item['url'] }}" @if (! empty($item['open_in_new_tab'])) target="_blank" rel="noopener noreferrer" @endif>{{ $item['label'] }}</a>
                    @endforeach
                </nav>
            </details>

            @if ($alphaFooterServicesLabel !== '' && $alphaFooterServices->isNotEmpty())
            <details class="footer-mobile-only footer-accordion footer-services-block content-reveal animation-index-2" data-image-reveal>
                <summary class="footer-label"><span>{{ $alphaFooterServicesLabel }}</span><i class="fa-light fa-plus" aria-hidden="true"></i></summary>
                <nav class="footer-accordion-content" aria-label="{{ __('ui.alpha_chrome.footer.services_aria') }}">
                    @foreach ($alphaFooterServices as $item)
                        <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                    @endforeach
                </nav>
            </details>
            @endif

            @if ($alphaFooterContactLabel !== '')
            <details class="footer-mobile-only footer-accordion footer-contact-block content-reveal animation-index-3" data-image-reveal>
                <summary class="footer-label"><span>{{ $alphaFooterContactLabel }}</span><i class="fa-light fa-plus" aria-hidden="true"></i></summary>
                <address class="footer-contact footer-accordion-content">
                    @if ($alphaFooterAddress !== '' && $alphaFooterMap !== '')
                        <a href="{{ $alphaFooterMap }}" target="_blank" rel="noopener noreferrer"><span>{{ $alphaFooterAddress }}</span></a>
                    @endif
                    @if ($alphaFooterPhone !== '')
                        <a href="tel:{{ preg_replace('/[^+0-9]/', '', $alphaFooterPhone) }}"><span>{{ $alphaFooterPhone }}</span></a>
                    @endif
                    @if ($alphaFooterEmail !== '')
                        <a href="mailto:{{ $alphaFooterEmail }}"><span>{{ $alphaFooterEmail }}</span></a>
                    @endif
                    @if ($alphaFooterSalesEmail !== '' && $alphaFooterSalesEmail !== $alphaFooterEmail)
                        <a href="mailto:{{ $alphaFooterSalesEmail }}"><span>{{ $alphaFooterSalesEmail }}</span></a>
                    @endif
                    @if ($alphaFooterHours !== '')
                        <p>{{ $alphaFooterHours }}</p>
                    @endif
                </address>
            </details>
            @endif
        </div>

        <div class="footer-bottom content-reveal" data-image-reveal>
            @if ($alphaFooterCopyright !== '')
                <p>© {{ now()->year }} {{ $alphaFooterCopyright }}</p>
            @endif
            <div>
                @foreach ($alphaFooterLegalLinks as $link)
                    <a href="{{ $link['url'] }}">{{ $link['label'] }}</a>
                @endforeach
                @if ($alphaFooterCookieSettingsLabel !== '')
                    <button type="button" class="footer-cookie-consent-link" data-cookie-consent-trigger>
                        {{ $alphaFooterCookieSettingsLabel }}
                    </button>
                @endif
            </div>
            @if ($alphaFooterBackToTopLabel !== '')
                <a class="footer-back-to-top" href="#page-top">
                    <span>{{ $alphaFooterBackToTopLabel }}</span>
                    <i class="fa-duotone fa-thin fa-arrow-up" aria-hidden="true"></i>
                </a>
            @endif
        </div>
    </div>
</footer>
