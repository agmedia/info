@php
    $cookieConsentIsEnglish = str_starts_with(strtolower((string) app()->getLocale()), 'en');
    $cookieConsentLabel = $cookieConsentIsEnglish ? 'Cookie settings' : 'Postavke kolačića';
    $cookieConsentPolicyLabel = $cookieConsentIsEnglish
        ? 'Read the privacy policy'
        : 'Pročitajte politiku privatnosti';
    $whatsappContactLabel = $cookieConsentIsEnglish
        ? 'Contact Kristina on WhatsApp'
        : 'Kontaktirajte Kristinu putem WhatsAppa';
    $cookieConsentPrivacyUrl = collect(app(\App\Services\Front\NavigationMenuService::class)
        ->defaultFooterLegalNavigationForLocale((string) app()->getLocale()))
        ->firstWhere('code', 'privacy-policy')['url'] ?? '';
@endphp

<div
    hidden
    data-cookie-consent-config
    data-locale="{{ app()->getLocale() }}"
    data-privacy-url="{{ $cookieConsentPrivacyUrl }}"
    data-policy-label="{{ $cookieConsentPolicyLabel }}"
></div>

<button
    type="button"
    class="cookie-consent-trigger"
    data-cookie-consent-trigger
    aria-label="{{ $cookieConsentLabel }}"
    title="{{ $cookieConsentLabel }}"
>
    <i class="fa-light fa-cookie-bite" aria-hidden="true"></i>
</button>

<a
    class="floating-whatsapp-contact"
    href="https://wa.me/385995318350"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="{{ $whatsappContactLabel }}"
    title="{{ $whatsappContactLabel }}"
>
    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
</a>
