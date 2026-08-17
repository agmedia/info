@php
    $cookieConsentIsEnglish = str_starts_with(strtolower((string) app()->getLocale()), 'en');
    $cookieConsentLabel = $cookieConsentIsEnglish ? 'Cookie settings' : 'Postavke kolačića';
    $cookieConsentPolicyLabel = $cookieConsentIsEnglish
        ? 'Read the privacy policy'
        : 'Pročitajte politiku privatnosti';
@endphp

<div
    hidden
    data-cookie-consent-config
    data-locale="{{ app()->getLocale() }}"
    data-privacy-url="{{ route('pages.show', ['slug' => 'politika-privatnosti']) }}"
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
