@php
    $analytics = $storeSettings['analytics'] ?? [];
    $analyticsEnabled = (bool) ($analytics['enabled'] ?? false);
    $ga4Id = trim((string) ($analytics['ga4_measurement_id'] ?? ''));
    $ga4IdIsValid = preg_match('/^G-[A-Z0-9]+$/i', $ga4Id) === 1;
@endphp

@if ($analyticsEnabled && $ga4IdIsValid)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
    <script>
        window.gtag('js', new Date());
        window.gtag('config', @json($ga4Id));
    </script>
@endif
