@php
    $alphaFooterPhone = trim((string) ($storeSettings['footer']['phone'] ?? '')) ?: '+385 (1) 580 6656';
    $alphaFooterEmail = trim((string) ($storeSettings['footer']['email_support'] ?? '')) ?: 'info@alphacapitalis.com';
    $alphaFooterAddress = 'Ulica R. F. Mihanovića 9, 10110 Zagreb';
    $alphaFooterMap = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($alphaFooterAddress);
    $alphaFooterHome = route('home');
    $alphaFooterNavigation = [
        ['label' => 'O nama', 'url' => route('pages.show', ['slug' => 'o-nama'])],
        ['label' => 'Karijera', 'url' => route('pages.show', ['slug' => 'karijera'])],
        ['label' => 'Objave', 'url' => route('blog.index')],
        ['label' => 'Kontakt', 'url' => route('contact.create')],
    ];
    $alphaFooterServices = [
        ['label' => 'Revizija', 'url' => route('audit.show')],
        ['label' => 'Računovodstvo', 'url' => route('accounting.show')],
        ['label' => 'Savjetovanje', 'url' => route('advisory.show')],
    ];
    $alphaFooterSocials = collect([
        ['label' => 'X', 'icon' => 'fa-x-twitter', 'url' => trim((string) ($storeSettings['branding']['social']['x']['url'] ?? '')), 'enabled' => (bool) ($storeSettings['branding']['social']['x']['enabled'] ?? true)],
        ['label' => 'Facebook', 'icon' => 'fa-facebook-f', 'url' => trim((string) ($storeSettings['branding']['social']['facebook']['url'] ?? '')), 'enabled' => (bool) ($storeSettings['branding']['social']['facebook']['enabled'] ?? true)],
        ['label' => 'LinkedIn', 'icon' => 'fa-linkedin-in', 'url' => trim((string) ($storeSettings['branding']['social']['linkedin']['url'] ?? '')), 'enabled' => (bool) ($storeSettings['branding']['social']['linkedin']['enabled'] ?? true)],
        ['label' => 'Instagram', 'icon' => 'fa-instagram', 'url' => trim((string) ($storeSettings['branding']['social']['instagram']['url'] ?? '')), 'enabled' => (bool) ($storeSettings['branding']['social']['instagram']['enabled'] ?? true)],
    ])->filter(fn (array $social): bool => $social['enabled'])->map(function (array $social) use ($alphaFooterHome): array {
        $social['url'] = $social['url'] !== '' ? $social['url'] : $alphaFooterHome;

        return $social;
    })->values();
    $alphaFooterLegalLinks = collect($storeSettings['footer']['bottom_links'] ?? [])->filter(
        static fn ($item): bool => is_array($item) && trim((string) ($item['label'] ?? '')) !== '' && trim((string) ($item['url'] ?? '')) !== ''
    )->values();

    if ($alphaFooterLegalLinks->isEmpty()) {
        $alphaFooterLegalLinks = collect([
            ['label' => 'Politika privatnosti', 'url' => route('pages.show', ['slug' => 'politika-privatnosti'])],
            ['label' => 'Uvjeti korištenja', 'url' => route('pages.show', ['slug' => 'uvjeti-koristenja'])],
        ]);
    }
@endphp

<footer class="site-footer" data-image-reveal>
    <div class="footer-shell">
        @unless ($__env->hasSection('hide_footer_newsletter'))
        <section class="footer-newsletter" id="newsletter" aria-labelledby="footer-newsletter-title" data-image-reveal>
            <div class="footer-newsletter-copy">
                <span class="footer-label">Newsletter</span>
                <h2 id="footer-newsletter-title">
                    Primajte važne novosti na <span class="footer-newsletter-accent">vrijeme.</span>
                </h2>
            </div>
            <form
                action="{{ route('contact.create') }}"
                method="get"
                novalidate
                data-newsletter-form
                data-msg-email-required="{{ __('contact.validation.inline.email_required') }}"
                data-msg-email-invalid="{{ __('contact.validation.inline.email_invalid') }}"
            >
                <label class="visually-hidden" for="newsletter-email">Vaša email adresa</label>
                <div class="footer-newsletter-field">
                    <i class="fa-light fa-envelope" aria-hidden="true"></i>
                    <input id="newsletter-email" name="newsletter_email" type="email" autocomplete="email" placeholder="Vaša email adresa" required aria-describedby="newsletter-email-error" aria-invalid="false">
                    <button type="submit" aria-label="Nastavite na prijavu za newsletter">
                        <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </div>
                <p id="newsletter-email-error" class="footer-newsletter-error" data-newsletter-error role="alert" aria-live="polite" hidden></p>
            </form>
        </section>
        @endunless

        <div class="footer-main" data-image-reveal>
            <div class="footer-brand-block content-reveal animation-index-0" data-image-reveal>
                <a class="footer-brand" href="{{ $alphaFooterHome }}" aria-label="Alpha Capitalis — početna">
                    <img src="{{ asset('alpha/logo.svg') }}" alt="Alpha Capitalis" width="300" height="80">
                </a>
                <p>Jedna adresa za sve brojke.</p>
                @if ($alphaFooterSocials->isNotEmpty())
                    <div class="footer-socials" aria-label="Društvene mreže">
                        @foreach ($alphaFooterSocials as $social)
                            <a href="{{ $social['url'] }}" aria-label="{{ $social['label'] }}" title="{{ $social['label'] }}" target="_blank" rel="noopener noreferrer">
                                <i class="fa-brands {{ $social['icon'] }}" aria-hidden="true"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="footer-desktop-only footer-nav-block content-reveal animation-index-1" data-image-reveal>
                <span class="footer-label">Alpha Capitalis</span>
                <nav aria-label="Alpha Capitalis poveznice u podnožju">
                    @foreach ($alphaFooterNavigation as $item)
                        <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                    @endforeach
                </nav>
            </div>

            <div class="footer-desktop-only footer-services-block content-reveal animation-index-2" data-image-reveal>
                <span class="footer-label">Usluge</span>
                <nav aria-label="Usluge u podnožju">
                    @foreach ($alphaFooterServices as $item)
                        <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                    @endforeach
                </nav>
            </div>

            <div class="footer-desktop-only footer-contact-block content-reveal animation-index-3" data-image-reveal>
                <span class="footer-label">Kontakt</span>
                <address class="footer-contact">
                    <a href="{{ $alphaFooterMap }}" target="_blank" rel="noopener noreferrer">{{ $alphaFooterAddress }}</a>
                    <a href="tel:{{ preg_replace('/[^+0-9]/', '', $alphaFooterPhone) }}">{{ $alphaFooterPhone }}</a>
                    <a href="mailto:{{ $alphaFooterEmail }}">{{ $alphaFooterEmail }}</a>
                </address>
            </div>

            <details class="footer-mobile-only footer-accordion footer-nav-block content-reveal animation-index-1" data-image-reveal>
                <summary class="footer-label"><span>Alpha Capitalis</span><i class="fa-light fa-plus" aria-hidden="true"></i></summary>
                <nav class="footer-accordion-content" aria-label="Alpha Capitalis poveznice u podnožju">
                    @foreach ($alphaFooterNavigation as $item)
                        <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                    @endforeach
                </nav>
            </details>

            <details class="footer-mobile-only footer-accordion footer-services-block content-reveal animation-index-2" data-image-reveal>
                <summary class="footer-label"><span>Usluge</span><i class="fa-light fa-plus" aria-hidden="true"></i></summary>
                <nav class="footer-accordion-content" aria-label="Usluge u podnožju">
                    @foreach ($alphaFooterServices as $item)
                        <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                    @endforeach
                </nav>
            </details>

            <details class="footer-mobile-only footer-accordion footer-contact-block content-reveal animation-index-3" data-image-reveal>
                <summary class="footer-label"><span>Kontakt</span><i class="fa-light fa-plus" aria-hidden="true"></i></summary>
                <address class="footer-contact footer-accordion-content">
                    <a href="{{ $alphaFooterMap }}" target="_blank" rel="noopener noreferrer"><span>{{ $alphaFooterAddress }}</span></a>
                    <a href="tel:{{ preg_replace('/[^+0-9]/', '', $alphaFooterPhone) }}"><span>{{ $alphaFooterPhone }}</span></a>
                    <a href="mailto:{{ $alphaFooterEmail }}"><span>{{ $alphaFooterEmail }}</span></a>
                </address>
            </details>
        </div>

        <div class="footer-bottom content-reveal" data-image-reveal>
            <p>© {{ now()->year }} Alpha Capitalis d.o.o. Sva prava pridržana.</p>
            <div>
                @foreach ($alphaFooterLegalLinks as $link)
                    <a href="{{ $link['url'] }}">{{ $link['label'] }}</a>
                @endforeach
            </div>
            <a class="footer-back-to-top" href="{{ request()->routeIs('home') ? '#vrh' : $alphaFooterHome.'#vrh' }}">
                <span>Na vrh</span>
                <i class="fa-duotone fa-thin fa-arrow-up" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</footer>
