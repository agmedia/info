@php
    $iconUrl = trim((string) ($iconUrl ?? ''));
    $accentColor = trim((string) ($accentColor ?? '#ab8d52'));
@endphp

@if ($iconUrl !== '')
    <div class="ac-family-hero-badge-slot" aria-hidden="true">
        <div class="ac-family-hero-badge" style="--ac-family-hero-icon-accent: {{ $accentColor }};">
            <img src="{{ $iconUrl }}" alt="" class="ac-family-hero-badge-icon" loading="eager" decoding="async">
        </div>
    </div>
@endif
