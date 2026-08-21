@php
    $analytics = $storeSettings['analytics'] ?? [];
    $ga4Id = trim((string) ($analytics['ga4_measurement_id'] ?? ''));
    $gtmId = trim((string) ($analytics['gtm_container_id'] ?? ''));
    $googleAdsId = trim((string) ($analytics['google_ads_conversion_id'] ?? ''));
    $metaPixelId = trim((string) ($analytics['meta_pixel_id'] ?? ''));

    $ga4Id = (bool) ($analytics['enabled'] ?? false) && preg_match('/^G-[A-Z0-9]+$/i', $ga4Id) === 1
        ? strtoupper($ga4Id)
        : '';
    $gtmId = (bool) ($analytics['gtm_enabled'] ?? false) && preg_match('/^GTM-[A-Z0-9]+$/i', $gtmId) === 1
        ? strtoupper($gtmId)
        : '';
    $googleAdsId = (bool) ($analytics['google_ads_enabled'] ?? false) && preg_match('/^AW-[0-9]+$/i', $googleAdsId) === 1
        ? strtoupper($googleAdsId)
        : '';
    $metaPixelId = (bool) ($analytics['meta_pixel_enabled'] ?? false) && preg_match('/^[0-9]{5,20}$/', $metaPixelId) === 1
        ? $metaPixelId
        : '';
@endphp

@if ($ga4Id !== '' || $gtmId !== '' || $googleAdsId !== '' || $metaPixelId !== '')
    <meta
        name="store-tracking-config"
        data-ga4-measurement-id="{{ $ga4Id }}"
        data-gtm-container-id="{{ $gtmId }}"
        data-google-ads-conversion-id="{{ $googleAdsId }}"
        data-meta-pixel-id="{{ $metaPixelId }}"
    >
    <script defer src="{{ asset('front-theme/scripts/tracking-integrations.js') }}?v={{ filemtime(public_path('front-theme/scripts/tracking-integrations.js')) }}"></script>
@endif
