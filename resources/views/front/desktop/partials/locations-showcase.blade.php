@php
    $locationsSectionId = $locationsSectionId ?? 'lokacije';
    $locationsTitleId = $locationsTitleId ?? 'locations-title';
    $locationDetailsPrefix = $locationDetailsPrefix ?? 'location-details';
    $showLocationStats = $showLocationStats ?? true;
    $locationStats = $locationStats ?? collect([
        ['value' => 300, 'suffix' => '+', 'label' => 'Odrađenih projekata'],
        ['value' => 600, 'suffix' => '+', 'label' => 'Redovnih klijenata'],
        ['value' => 60, 'suffix' => '+', 'label' => 'Kvalificiranih stručnjaka'],
        ['value' => 20, 'suffix' => '+', 'label' => 'Godina iskustva'],
    ]);
    $statIcons = $statIcons ?? ['fa-chart-column', 'fa-users', 'fa-user-tie', 'fa-shield-heart'];

    $locationEntities = collect($storeSettings['official_entities'] ?? [])->keyBy('key');
    $locationDefinitions = [
        ['key' => 'alpha-capitalis', 'city' => 'Zagreb – HQ ured', 'short_city' => 'Zagreb', 'css' => 'is-zagreb', 'number' => '01 · HQ'],
        ['key' => 'alpha-capitalis-timia', 'city' => 'Rijeka', 'short_city' => 'Rijeka', 'css' => 'is-rijeka', 'number' => '02'],
        ['key' => 'alpha-capitalis-east', 'city' => 'Vinkovci', 'short_city' => 'Vinkovci', 'css' => 'is-vinkovci', 'number' => '03'],
    ];
    $locationItems = collect($locationDefinitions)->map(function (array $definition) use ($locationEntities): array {
        $entity = (array) $locationEntities->get($definition['key'], []);
        $addressParts = collect($entity['contact_address'] ?? $entity['address'] ?? [])
            ->map(fn ($part) => trim((string) $part))
            ->filter();
        $address = $addressParts->implode(', ');
        $mapQuery = trim((string) ($entity['map_query'] ?? '')) ?: $address;

        return array_merge($definition, [
            'office_label' => trim((string) ($entity['office_label'] ?? $entity['label'] ?? '')) ?: 'Ured '.$definition['short_city'],
            'company' => trim((string) ($entity['company'] ?? $entity['name'] ?? '')) ?: 'ALPHA CAPITALIS d.o.o.',
            'address' => $address ?: $definition['short_city'],
            'email' => trim((string) ($entity['email'] ?? '')) ?: 'info@alphacapitalis.com',
            'phone' => trim((string) ($entity['phone'] ?? '')) ?: '+385 (1) 580 6656',
            'map_url' => 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($mapQuery),
        ]);
    })->values();
@endphp

<section class="locations-section" id="{{ $locationsSectionId }}" aria-labelledby="{{ $locationsTitleId }}" data-locations-reveal>
    <div class="locations-shell">
        <div class="locations-layout">
            <div class="locations-copy">
                <h2 class="locations-title" id="{{ $locationsTitleId }}" data-words-slide-from-right aria-label="Prisutni na 3 lokacije">
                    @foreach (['Prisutni', 'na', '3', 'lokacije'] as $word)
                        <span class="locations-title-word animation-index-{{ $loop->index }} {{ $word === 'lokacije' ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                    @endforeach
                </h2>
                <p class="locations-intro"><strong>Zagreb, Rijeka i Vinkovci</strong> — podrška klijentima diljem Hrvatske.</p>

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
                                        <h3>{{ $location['company'] }}</h3>
                                        <a class="location-map-link" href="{{ $location['map_url'] }}" target="_blank" rel="noopener noreferrer" tabindex="-1">
                                            <i class="fa-light fa-location-dot" aria-hidden="true"></i>
                                            <span>Pogledaj na karti</span>
                                            <i class="fa-light fa-arrow-up-right" aria-hidden="true"></i>
                                        </a>
                                        <div class="location-contacts">
                                            <a href="mailto:{{ $location['email'] }}" tabindex="-1">
                                                <i class="fa-light fa-envelope" aria-hidden="true"></i>
                                                <span><small>Email</small><strong>{{ $location['email'] }}</strong></span>
                                            </a>
                                            <a href="tel:{{ preg_replace('/[^+0-9]/', '', $location['phone']) }}" tabindex="-1">
                                                <i class="fa-light fa-phone" aria-hidden="true"></i>
                                                <span><small>Telefon</small><strong>{{ $location['phone'] }}</strong></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="locations-map" aria-label="Karta lokacija u Hrvatskoj">
                <div class="locations-map-corners" aria-hidden="true">
                    <span class="locations-map-corner is-top-left">RIJEKA · 45.33° N · 14.44° E</span>
                    <span class="locations-map-corner is-top-right">ZAGREB · 45.80° N · 15.91° E</span>
                    <span class="locations-map-corner is-bottom-right">VINKOVCI · 45.29° N · 18.80° E</span>
                    <span class="locations-map-corner is-bottom-left">HR / 3 UREDA</span>
                </div>
                <div class="locations-map-stage">
                    <div class="locations-map-glow" aria-hidden="true"></div>
                    <img class="croatia-map" src="{{ asset('alpha/croatia-map.svg') }}" alt="Karta Hrvatske s uredima u Zagrebu, Rijeci i Vinkovcima" width="800" height="800" loading="lazy" decoding="async">
                    <img class="map-routes contact-map-routes" src="{{ asset('alpha/croatia-map-routes.svg') }}?v=5" alt="" aria-hidden="true" width="100" height="100" decoding="sync">
                    @foreach ([1, 0, 2] as $locationIndex)
                        @php($location = $locationItems[$locationIndex])
                        <button class="map-location animation-index-{{ $loop->index }} {{ $location['css'] }}" type="button" aria-label="Prikaži kontaktne podatke za ured {{ $location['short_city'] }}" aria-expanded="false" aria-controls="{{ $locationDetailsPrefix }}-{{ $locationIndex }}" data-location-index="{{ $locationIndex }}">
                            <span class="map-beacon" aria-hidden="true"><i class="fa-duotone fa-thin fa-bullseye-pointer"></i></span>
                            <span class="map-location-label"><small>{{ $location['number'] }}</small><strong>{{ $location['short_city'] }}</strong></span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        @if ($showLocationStats)
            <div class="locations-stats" aria-label="Alpha Capitalis u brojkama">
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
