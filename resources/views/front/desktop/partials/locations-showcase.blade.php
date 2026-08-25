@php
    $locationsSectionId = $locationsSectionId ?? 'lokacije';
    $locationsTitleId = $locationsTitleId ?? 'locations-title';
    $locationDetailsPrefix = $locationDetailsPrefix ?? 'location-details';
    $showLocationStats = $showLocationStats ?? true;
    $locationsContent = is_array($locationsContent ?? null) ? $locationsContent : [];
    $locationStats = collect($locationStats ?? []);
    $statIcons = $statIcons ?? ['fa-chart-column', 'fa-users', 'fa-user-tie', 'fa-shield-heart'];

    $locationCssClasses = [
        'alpha-capitalis' => 'is-zagreb',
        'alpha-capitalis-timia' => 'is-rijeka',
        'alpha-capitalis-east' => 'is-vinkovci',
    ];
    $locationDefinitions = collect((array) ($locationsContent['items'] ?? []))
        ->filter(static fn ($item): bool => is_array($item) && trim((string) ($item['entity_key'] ?? '')) !== '')
        ->map(static function (array $item) use ($locationCssClasses): array {
            $key = trim((string) ($item['entity_key'] ?? ''));

            return [
                'key' => $key,
                'city' => trim((string) ($item['city'] ?? '')),
                'short_city' => trim((string) ($item['short_city'] ?? '')),
                'office_label' => trim((string) ($item['office_label'] ?? '')),
                'company' => trim((string) ($item['company'] ?? '')),
                'address' => trim((string) ($item['address'] ?? '')),
                'map_query' => trim((string) ($item['map_query'] ?? '')),
                'email' => trim((string) ($item['email'] ?? '')),
                'phone' => trim((string) ($item['phone'] ?? '')),
                'css' => (string) ($locationCssClasses[$key] ?? ''),
                'number' => trim((string) ($item['number'] ?? '')),
                'coordinates_label' => trim((string) ($item['coordinates_label'] ?? '')),
                'marker_aria_label' => trim((string) ($item['marker_aria_label'] ?? '')),
            ];
        })
        ->values()
        ->all();
    $locationItems = collect($locationDefinitions)->map(function (array $definition): array {
        $mapQuery = $definition['map_query'] !== ''
            ? $definition['map_query']
            : $definition['address'];

        return array_merge($definition, [
            'map_url' => $mapQuery !== ''
                ? 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($mapQuery)
                : '',
        ]);
    })->values();
    $locationsTitle = trim((string) ($locationsContent['title'] ?? ''));
    $locationsIntroLead = trim((string) ($locationsContent['intro_lead'] ?? ''));
    $locationsIntroText = trim((string) ($locationsContent['intro_text'] ?? ''));
    $locationsHeroAriaLabel = $locationsTitle;
    $locationsMapAriaLabel = trim((string) ($locationsContent['map_aria_label'] ?? ''));
    $locationsMapImageAlt = trim((string) ($locationsContent['map_image_alt'] ?? ''));
    $locationsMapLinkLabel = trim((string) ($locationsContent['map_link_label'] ?? ''));
    $locationsEmailLabel = trim((string) ($locationsContent['email_label'] ?? ''));
    $locationsPhoneLabel = trim((string) ($locationsContent['phone_label'] ?? ''));
    $locationsStatsAriaLabel = trim((string) ($locationsContent['stats_aria_label'] ?? ''));
    $locationsRegionLabel = trim((string) ($locationsContent['region_label'] ?? ''));
    $locationTitleWords = preg_split('/\s+/u', $locationsTitle, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $mapSortOrder = ['is-rijeka' => 0, 'is-zagreb' => 1, 'is-vinkovci' => 2];
    $mapCornerClasses = ['is-rijeka' => 'is-top-left', 'is-zagreb' => 'is-top-right', 'is-vinkovci' => 'is-bottom-right'];
    $mapLocationItems = $locationItems
        ->map(static fn (array $item, int $index): array => [...$item, 'item_index' => $index])
        ->sortBy(static fn (array $item): int => $mapSortOrder[$item['css']] ?? 99)
        ->values();
@endphp

<section class="locations-section" id="{{ $locationsSectionId }}" aria-labelledby="{{ $locationsTitleId }}" data-locations-reveal>
    <div class="locations-shell">
        <div class="locations-layout">
            <div class="locations-copy">
                <h2 class="locations-title" id="{{ $locationsTitleId }}" data-words-slide-from-right aria-label="{{ $locationsHeroAriaLabel }}">
                    @foreach ($locationTitleWords as $word)
                        <span class="locations-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                    @endforeach
                </h2>
                @if ($locationsIntroLead !== '' || $locationsIntroText !== '')
                    <p class="locations-intro">
                        @if ($locationsIntroLead !== '')<strong>{{ $locationsIntroLead }}</strong>@endif
                        {{ $locationsIntroText }}
                    </p>
                @endif

                <div class="locations-addresses">
                    @foreach ($locationItems as $location)
                        <article class="location-address animation-index-{{ $loop->index }}">
                            <button class="location-address-trigger" type="button" aria-expanded="false" aria-controls="{{ $locationDetailsPrefix }}-{{ $loop->index }}" data-location-index="{{ $loop->index }}">
                                <span class="location-address-marker" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="location-address-summary">
                                    <span class="location-address-title">{{ $location['city'] }}</span>
                                    <span class="location-address-short">{{ $location['address'] }}</span>
                                </span>
                                <span class="location-address-toggle" aria-hidden="true"><span></span><span></span></span>
                            </button>
                            <div class="location-details" id="{{ $locationDetailsPrefix }}-{{ $loop->index }}" aria-hidden="true" inert>
                                <div class="location-details-inner">
                                    <div class="location-details-card">
                                        <span class="location-office-label">{{ $location['office_label'] }}</span>
                                        @if ($location['company'] !== '')
                                            <h3>{{ $location['company'] }}</h3>
                                        @endif
                                        @if ($location['map_url'] !== '' && $locationsMapLinkLabel !== '')
                                            <a class="location-map-link" href="{{ $location['map_url'] }}" target="_blank" rel="noopener noreferrer" tabindex="-1" aria-label="{{ $location['marker_aria_label'] }}">
                                                <i class="fa-light fa-location-dot" aria-hidden="true"></i>
                                                <span>{{ $locationsMapLinkLabel }}</span>
                                                <i class="fa-light fa-arrow-up-right" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                        <div class="location-contacts">
                                            @if ($location['email'] !== '')
                                            <a href="mailto:{{ $location['email'] }}" tabindex="-1">
                                                <i class="fa-light fa-envelope" aria-hidden="true"></i>
                                                <span><small>{{ $locationsEmailLabel }}</small><strong>{{ $location['email'] }}</strong></span>
                                            </a>
                                            @endif
                                            @if ($location['phone'] !== '')
                                            <a href="tel:{{ preg_replace('/[^+0-9]/', '', $location['phone']) }}" tabindex="-1">
                                                <i class="fa-light fa-phone" aria-hidden="true"></i>
                                                <span><small>{{ $locationsPhoneLabel }}</small><strong>{{ $location['phone'] }}</strong></span>
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="locations-map" aria-label="{{ $locationsMapAriaLabel }}">
                <div class="locations-map-corners" aria-hidden="true">
                    @foreach ($locationItems as $location)
                        @if ($location['coordinates_label'] !== '')
                            <span class="locations-map-corner {{ $mapCornerClasses[$location['css']] ?? '' }}">{{ $location['coordinates_label'] }}</span>
                        @endif
                    @endforeach
                    @if ($locationsRegionLabel !== '')
                        <span class="locations-map-corner is-bottom-left">{{ $locationsRegionLabel }}</span>
                    @endif
                </div>
                <div class="locations-map-stage">
                    <div class="locations-map-glow" aria-hidden="true"></div>
                    <img class="croatia-map" src="{{ asset('alpha/croatia-map.svg') }}" alt="{{ $locationsMapImageAlt }}" width="800" height="800" loading="lazy" decoding="async">
                    <img class="map-routes contact-map-routes" src="{{ asset('alpha/croatia-map-routes.svg') }}?v=5" alt="" aria-hidden="true" width="100" height="100" decoding="sync">
                    @foreach ($mapLocationItems as $location)
                        <button class="map-location animation-index-{{ $loop->index }} {{ $location['css'] }}" type="button" aria-label="{{ $location['marker_aria_label'] }}" aria-expanded="false" aria-controls="{{ $locationDetailsPrefix }}-{{ $location['item_index'] }}" data-location-index="{{ $location['item_index'] }}">
                            <span class="map-beacon" aria-hidden="true"><i class="fa-duotone fa-thin fa-bullseye-pointer"></i></span>
                            <span class="map-location-label"><small>{{ $location['number'] }}</small><strong>{{ $location['short_city'] }}</strong></span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        @if ($showLocationStats && $locationStats->isNotEmpty())
            <div class="locations-stats locations-stats--{{ $locationStats->count() }}" aria-label="{{ $locationsStatsAriaLabel }}">
                @foreach ($locationStats as $stat)
                    <article class="location-stat animation-index-{{ $loop->index }}">
                        <div class="location-stat-icon" aria-hidden="true"><i class="fa-duotone fa-thin fa-fw {{ $statIcons[$loop->index] }}"></i></div>
                        <div><strong><span data-count-target="{{ $stat['value'] }}">0</span><span class="location-stat-suffix">{{ $stat['suffix'] }}</span></strong><p>{{ $stat['label'] }}</p></div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
