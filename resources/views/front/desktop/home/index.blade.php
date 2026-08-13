@extends('front.desktop.layouts.store')

@section('title', 'Alpha Capitalis | Jedna adresa za sve brojke')
@section('main_class', 'hero-page')

@section('content')
    {{-- CMS content is projected into the redesigned homepage components below using the shared Alpha typography. --}}
    @php
        $valueItems = [
            ['icon' => 'fa-badge-check', 'title' => 'Stručnost i iskustvo', 'text' => 'Višegodišnje iskustvo u širokom spektru industrija i najviši standardi profesionalne izvrsnosti.'],
            ['icon' => 'fa-scale-balanced', 'title' => 'Neovisnost i povjerenje', 'text' => 'Neovisni smo, objektivni i posvećeni najvišim profesionalnim i etičkim načelima.'],
            ['icon' => 'fa-chart-line', 'title' => 'Partner u rastu', 'text' => 'Ulažemo u vaše ciljeve i pružamo konkretne smjernice koje donose mjerljive rezultate.'],
        ];

        $homeHeroItem = collect($homeHeroBlocks ?? [])->first(fn ($item) => (string) (($item['block'] ?? null)?->type ?? '') === 'home_hero')
            ?? collect($homeHeroBlocks ?? [])->first();
        $homeHeroTranslation = $homeHeroItem['translation'] ?? null;
        $homeHeroPayload = array_merge(
            is_array(($homeHeroItem['block'] ?? null)?->payload ?? null) ? $homeHeroItem['block']->payload : [],
            is_array($homeHeroTranslation?->payload ?? null) ? $homeHeroTranslation->payload : [],
        );
        $cmsHeroTitle = trim((string) ($homeHeroTranslation?->title ?? ''));
        $cmsHeroSubtitle = trim((string) ($homeHeroTranslation?->subtitle ?? ''));
        $heroTitle = $cmsHeroTitle !== '' && mb_strtoupper($cmsHeroTitle) !== 'ALPHA CAPITALIS'
            ? $cmsHeroTitle
            : 'Jedna adresa za sve brojke';
        $heroSubtitle = $cmsHeroSubtitle !== '' && mb_strtoupper($cmsHeroSubtitle) !== 'VAŠ KOMPAS KROZ SVIJET FINANCIJA'
            ? $cmsHeroSubtitle
            : 'Računovodstvo, revizija i savjetovanje — sve na jednom mjestu.';
        $cmsHeroPrimaryLabel = trim((string) ($homeHeroTranslation?->cta_label ?? ''));
        $heroPrimaryLabel = $cmsHeroPrimaryLabel !== '' && mb_strtoupper($cmsHeroPrimaryLabel) !== 'NAŠE USLUGE'
            ? $cmsHeroPrimaryLabel
            : 'Dogovorite sastanak';
        $heroPrimaryUrl = $heroPrimaryLabel === 'Dogovorite sastanak'
            ? route('contact.create')
            : (trim((string) ($homeHeroTranslation?->cta_url ?? '')) ?: route('contact.create'));
        $cmsHeroSecondaryLabel = trim((string) ($homeHeroPayload['secondary_cta_label'] ?? ''));
        $heroSecondaryLabel = $cmsHeroSecondaryLabel !== '' && mb_strtoupper($cmsHeroSecondaryLabel) !== 'UGOVORI SASTANAK'
            ? $cmsHeroSecondaryLabel
            : 'Naše usluge';
        $heroSecondaryUrl = $heroSecondaryLabel === 'Naše usluge'
            ? route('services.index')
            : (trim((string) ($homeHeroPayload['secondary_cta_url'] ?? '')) ?: route('services.index'));
        $heroTitleWords = preg_split('/\s+/u', $heroTitle, -1, PREG_SPLIT_NO_EMPTY) ?: ['Jedna', 'adresa', 'za', 'sve', 'brojke'];
        $heroTitleLines = $heroTitle === 'Jedna adresa za sve brojke'
            ? [['Jedna', 'adresa'], ['za', 'sve', 'brojke']]
            : collect($heroTitleWords)->chunk(max(1, (int) ceil(count($heroTitleWords) / 2)))->map(fn ($line) => $line->values()->all())->values()->all();

        $homeServicesItem = collect($homeServicesBlocks ?? [])->first(fn ($item) => (string) (($item['block'] ?? null)?->type ?? '') === 'home_services')
            ?? collect($homeServicesBlocks ?? [])->first();
        $homeServicesTranslation = $homeServicesItem['translation'] ?? null;
        $homeServicesPayload = array_merge(
            is_array(($homeServicesItem['block'] ?? null)?->payload ?? null) ? $homeServicesItem['block']->payload : [],
            is_array($homeServicesTranslation?->payload ?? null) ? $homeServicesTranslation->payload : [],
        );
        $cmsServicesTitle = trim((string) ($homeServicesTranslation?->title ?? ''));
        $useCmsServicesHeading = $cmsServicesTitle !== '' && !str_starts_with($cmsServicesTitle, 'Stvaramo vrijednost za naše klijente');
        $servicesHeading = $useCmsServicesHeading ? $cmsServicesTitle : 'Vi vodite poslovanje. Mi brinemo da brojke prate vaš rast.';
        $servicesIntro = $useCmsServicesHeading ? trim((string) ($homeServicesTranslation?->subtitle ?? '')) : '';
        $servicesHeadingWords = preg_split('/\s+/u', $servicesHeading, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $servicesHeadingLines = $servicesHeading === 'Vi vodite poslovanje. Mi brinemo da brojke prate vaš rast.'
            ? [['Vi', 'vodite', 'poslovanje.'], ['Mi', 'brinemo', 'da', 'brojke', 'prate', 'vaš', 'rast.']]
            : collect($servicesHeadingWords)->chunk(max(1, (int) ceil(count($servicesHeadingWords) / 2)))->map(fn ($line) => $line->values()->all())->values()->all();

        $serviceDesign = collect([
            'audit' => [
                'title' => 'Revizija',
                'statement' => 'Sigurnost i povjerenje u svakoj odluci.',
                'text' => 'Pouzdani financijski izvještaji jačaju povjerenje vlasnika, banaka, investitora i partnera te smanjuju rizik u važnim poslovnim odlukama.',
                'image' => asset('alpha/service-revizija.jpg'),
                'image_alt' => 'Potpisivanje poslovnog dokumenta za stolom',
                'url' => route('audit.show'),
            ],
            'accounting' => [
                'title' => 'Računovodstvo',
                'statement' => 'Red u brojkama, mir u poslovanju.',
                'text' => 'Ažurni podaci, uredna administracija i kontrola nad financijama oslobađaju vam vrijeme za ono što je najvažnije — razvoj poslovanja.',
                'image' => asset('alpha/service-racunovodstvo.jpg'),
                'image_alt' => 'Rad na financijskim podacima na prijenosnom računalu',
                'url' => route('accounting.show'),
            ],
            'advisory' => [
                'title' => 'Savjetovanje',
                'statement' => 'Prave odluke stvaraju najveću vrijednost.',
                'text' => 'Stručna podrška pomaže prepoznati prilike, smanjiti rizike i donijeti sigurnije odluke za rast, financiranje i budućnost poslovanja.',
                'image' => asset('alpha/service-savjetovanje.jpg'),
                'image_alt' => 'Poslovni razgovor tijekom savjetovanja',
                'url' => route('advisory.show'),
            ],
        ]);

        $cmsServiceItems = collect($homeServicesPayload['services'] ?? [])->filter(fn ($service) => is_array($service))->values();
        $serviceSource = $cmsServiceItems->isNotEmpty() ? $cmsServiceItems : collect($primaryServicePillars ?? []);
        $serviceItems = $serviceSource->map(function (array $service, int $index) use ($serviceDesign): array {
            $key = (string) ($service['key'] ?? '');
            if ($key === '') {
                $key = ['audit', 'accounting', 'advisory'][$index] ?? '';
            }
            $fallback = (array) $serviceDesign->get($key, []);
            if ($fallback === []) {
                return [];
            }

            $dynamicImage = trim((string) ($service['image_url'] ?? ''));
            $useDynamicImage = $dynamicImage !== '' && !str_contains($dynamicImage, '/front-theme/images/services/');

            return array_merge($fallback, [
                'title' => trim((string) ($service['title'] ?? '')) ?: $fallback['title'],
                'statement' => trim((string) ($service['subtitle'] ?? '')) ?: $fallback['statement'],
                'text' => trim((string) ($service['text'] ?? '')) ?: $fallback['text'],
                'image' => $useDynamicImage ? $dynamicImage : $fallback['image'],
                'url' => trim((string) ($service['url'] ?? '')) ?: $fallback['url'],
            ]);
        })->filter()->values();

        if ($serviceItems->isEmpty()) {
            $serviceItems = $serviceDesign->values();
        }

        $processItems = [
            ['icon' => 'fa-magnifying-glass-chart', 'title' => 'Upoznajemo vaš posao', 'text' => 'Razumijemo vaše ciljeve, izazove i okruženje kako bismo identificirali ključne prilike.'],
            ['icon' => 'fa-chart-line', 'title' => 'Analiziramo i planiramo', 'text' => 'Analiziramo podatke i procese te kreiramo strategiju i konkretne korake prema ciljevima.'],
            ['icon' => 'fa-clipboard-check', 'title' => 'Provodimo i pratimo', 'text' => 'Provodimo dogovorene aktivnosti uz kontinuirano praćenje i pravovremene prilagodbe.'],
            ['icon' => 'fa-bullseye', 'title' => 'Donosimo vrijednost', 'text' => 'Ostvarujemo mjerljive rezultate koji jačaju vašu poziciju i donose dugoročnu vrijednost.'],
        ];

        $entities = collect($storeSettings['official_entities'] ?? [])->keyBy('key');
        $locationDefinitions = [
            ['key' => 'alpha-capitalis', 'city' => 'Zagreb – HQ ured', 'short_city' => 'Zagreb', 'css' => 'is-zagreb', 'number' => '01 · HQ'],
            ['key' => 'alpha-capitalis-timia', 'city' => 'Rijeka', 'short_city' => 'Rijeka', 'css' => 'is-rijeka', 'number' => '02'],
            ['key' => 'alpha-capitalis-east', 'city' => 'Vinkovci', 'short_city' => 'Vinkovci', 'css' => 'is-vinkovci', 'number' => '03'],
        ];
        $locationItems = collect($locationDefinitions)->map(function (array $definition) use ($entities): array {
            $entity = (array) $entities->get($definition['key'], []);
            $addressParts = collect($entity['contact_address'] ?? $entity['address'] ?? [])->map(fn ($part) => trim((string) $part))->filter();
            $address = $addressParts->implode(', ');
            $mapQuery = trim((string) ($entity['map_query'] ?? '')) ?: $address;

            return array_merge($definition, [
                'office_label' => trim((string) ($entity['office_label'] ?? '')) ?: 'Ured '.$definition['short_city'],
                'company' => trim((string) ($entity['company'] ?? $entity['name'] ?? '')) ?: 'ALPHA CAPITALIS d.o.o.',
                'address' => $address ?: $definition['short_city'],
                'email' => trim((string) ($entity['email'] ?? '')) ?: 'info@alphacapitalis.com',
                'phone' => trim((string) ($entity['phone'] ?? '')) ?: '+385 (1) 580 6656',
                'map_url' => 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($mapQuery),
            ]);
        })->values();

        $homeStatsItem = collect($homeStatsBlocks ?? [])->first(fn ($item) => (string) (($item['block'] ?? null)?->type ?? '') === 'home_stats')
            ?? collect($homeStatsBlocks ?? [])->first();
        $homeStatsPayload = array_merge(
            is_array(($homeStatsItem['block'] ?? null)?->payload ?? null) ? $homeStatsItem['block']->payload : [],
            is_array(($homeStatsItem['translation'] ?? null)?->payload ?? null) ? $homeStatsItem['translation']->payload : [],
        );
        $dynamicStats = collect($homeStatsPayload['stats'] ?? [])->map(function ($stat): array {
            $stat = is_array($stat) ? $stat : [];
            $rawValue = trim((string) ($stat['value'] ?? '0'));
            return [
                'value' => (int) (preg_replace('/\D+/', '', $rawValue) ?: 0),
                'suffix' => trim((string) ($stat['suffix'] ?? '')) ?: '+',
                'label' => trim((string) ($stat['label'] ?? '')),
            ];
        })->filter(fn (array $stat) => $stat['value'] > 0 && $stat['label'] !== '')->values();
        $statFallbacks = collect([
            ['value' => 300, 'suffix' => '+', 'label' => 'Zadovoljnih klijenata'],
            ['value' => 600, 'suffix' => '+', 'label' => 'Poslovnih klijenata'],
            ['value' => 60, 'suffix' => '+', 'label' => 'Kvalificiranih stručnjaka'],
            ['value' => 20, 'suffix' => '+', 'label' => 'Godina iskustva'],
        ]);
        $locationStats = $statFallbacks->map(fn (array $fallback, int $index) => array_merge($fallback, (array) $dynamicStats->get($index, [])));
        $statIcons = ['fa-chart-column', 'fa-users', 'fa-user-tie', 'fa-shield-heart'];

        $newsItems = collect($latestBlogPosts ?? [])->take(3)->map(function ($post) use ($locale, $fallbackLocale): array {
            $translation = $post->translations->firstWhere('locale', $locale)
                ?? $post->translations->firstWhere('locale', $fallbackLocale)
                ?? $post->translations->first();
            $category = $post->categories->sortByDesc(fn ($item) => (int) ($item->pivot->is_primary ?? false))->first();
            $categoryTranslation = $category?->translations->firstWhere('locale', $locale)
                ?? $category?->translations->firstWhere('locale', $fallbackLocale)
                ?? $category?->translations->first();
            $slug = trim((string) ($translation?->slug ?? ''));
            $excerpt = trim(strip_tags((string) ($translation?->excerpt ?? '')));

            return [
                'category' => trim((string) ($categoryTranslation?->name ?? '')) ?: 'Novosti',
                'title' => trim((string) ($translation?->title ?? $post->code)) ?: 'Alpha Capitalis',
                'text' => Illuminate\Support\Str::limit($excerpt ?: 'Saznajte aktualne informacije, rokove i stručne savjete za sigurnije poslovne odluke.', 210),
                'url' => $slug !== '' ? route('blog.show', ['slug' => $slug]) : route('blog.index'),
            ];
        })->values();

        if ($newsItems->isEmpty()) {
            $newsItems = collect([
                ['category' => 'Financije', 'title' => 'Dubinsko snimanje – zašto je ključno?', 'text' => 'Točna i vjerodostojna financijska izvješća temelj su sigurnijih transakcija i kvalitetnijih poslovnih odluka.', 'url' => route('blog.index')],
                ['category' => 'EU fondovi', 'title' => 'Jesu li inovacije nužne za EU financiranje?', 'text' => 'Europska unija sve više ulaže u projekte koji donose dodanu vrijednost, održivost i mjerljiv razvoj.', 'url' => route('blog.index')],
                ['category' => 'EU fondovi', 'title' => 'EU fondovi za male i srednje poduzetnike', 'text' => 'Pregled mogućnosti financijske podrške za razvoj, ulaganja i digitalizaciju malih i srednjih poduzeća.', 'url' => route('blog.index')],
            ]);
        }
    @endphp

    <section class="hero" id="vrh" aria-labelledby="hero-title">
        <video class="hero-video" autoplay muted loop playsinline preload="metadata" poster="{{ asset('alpha/alpha-zagreb-poster.jpg') }}" aria-hidden="true" data-alpha-hero-video>
            <source src="{{ asset('alpha/alpha-zagreb-loop-hq.mp4') }}" type="video/mp4">
        </video>
        <div class="hero-overlay" aria-hidden="true"></div>

        <div class="hero-content">
            <h1 id="hero-title" aria-label="{{ $heroTitle }}">
                @php $heroCharacterIndex = 0; @endphp
                @foreach ($heroTitleLines as $line)<span class="hero-line">@foreach ($line as $word)@php $upperWord = Illuminate\Support\Str::upper($word); @endphp<span class="hero-word {{ $loop->parent->last && $loop->last ? 'is-accent' : '' }}" aria-hidden="true">@foreach (mb_str_split($upperWord) as $character)<span class="hero-char" style="--char-index: {{ $heroCharacterIndex++ }}">{{ $character }}</span>@endforeach</span>@endforeach</span>@endforeach
            </h1>
            <p>@if ($heroSubtitle === 'Računovodstvo, revizija i savjetovanje — sve na jednom mjestu.')Računovodstvo, revizija i savjetovanje —<br> sve na jednom mjestu.@else{{ $heroSubtitle }}@endif</p>
            <div class="hero-actions">
                <a class="button button-gold" href="{{ $heroPrimaryUrl }}"><span>{{ $heroPrimaryLabel }}</span></a>
                <a class="button button-outline" href="{{ $heroSecondaryUrl }}"><span>{{ $heroSecondaryLabel }}</span></a>
            </div>
            <div class="scroll-cue" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span></div>
        </div>

        <aside class="hero-locations" aria-label="Naše lokacije">
            <span class="location-number" aria-hidden="true"><i class="fa-duotone fa-thin fa-bullseye-pointer"></i></span>
            <div>@foreach ($locationItems as $location)<p>{{ Illuminate\Support\Str::upper($location['short_city']) }}</p>@endforeach</div>
        </aside>
    </section>

    <section class="values-section" id="vrijednosti" aria-labelledby="values-title">
        <div class="values-inner">
            <div class="values-intro">
                @php $valuesWords = explode(' ', 'Stvaramo vrijednost za naše klijente u svim fazama razvoja poslovanja.'); @endphp
                <h2 class="values-title" id="values-title" data-words-slide-from-right aria-label="Stvaramo vrijednost za naše klijente u svim fazama razvoja poslovanja.">
                    @foreach ($valuesWords as $word)<span class="values-word {{ $word === 'vrijednost' ? 'is-accent' : '' }}" style="--value-word-index: {{ $loop->index }}" aria-hidden="true">{{ $word }}</span>@endforeach
                </h2>
                <p class="values-copy content-reveal" data-image-reveal><strong>ALPHA CAPITALIS</strong> pruža vam sigurnost u poslovanju, jasnoću u financijama i partnera koji vam pomaže donositi bolje odluke, smanjiti rizike i ostvariti održiv rast.</p>
            </div>

            <div class="values-list">
                @foreach ($valueItems as $item)
                    <article class="value-item content-reveal" data-image-reveal style="--reveal-index: {{ $loop->index }}">
                        <div class="value-icon" aria-hidden="true"><i class="fa-duotone fa-thin fa-fw {{ $item['icon'] }}"></i></div>
                        <div class="value-content">
                            <h3 data-words-slide-from-right aria-label="{{ $item['title'] }}">
                                @foreach (explode(' ', $item['title']) as $word)<span class="value-title-word" style="--services-word-index: {{ $loop->index }}" aria-hidden="true">{{ $word }}</span>@endforeach
                            </h3>
                            <p>{{ $item['text'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="services-section" id="usluge" aria-labelledby="services-title">
        <div class="services-shell">
            <header class="services-header">
                <h2 class="services-title" id="services-title" data-words-slide-from-right aria-label="{{ $servicesHeading }}">
                    @php $servicesWordIndex = 0; @endphp
                    @foreach ($servicesHeadingLines as $line)<span class="services-title-line" aria-hidden="true">@foreach ($line as $word)<span class="services-word {{ mb_strtolower(trim($word, '.,!?')) === 'brojke' || ($useCmsServicesHeading && $loop->parent->last && $loop->last) ? 'is-accent' : '' }}" style="--services-word-index: {{ $servicesWordIndex++ }}">{{ $word }}</span>@endforeach</span>@endforeach
                </h2>
                @if ($servicesIntro !== '')
                    <p class="services-intro content-reveal" data-image-reveal>{{ $servicesIntro }}</p>
                @endif
            </header>

            <div class="services-grid services-grid--count-{{ min(3, $serviceItems->count()) }}">
                @foreach ($serviceItems as $service)
                    <a class="service-card" href="{{ $service['url'] }}" data-image-reveal style="--service-index: {{ $loop->index }}">
                        <div class="service-card-media">
                            <img src="{{ $service['image'] }}" alt="{{ $service['image_alt'] }}" width="1080" height="1350" loading="lazy" decoding="async">
                        </div>
                        <div class="service-card-copy">
                            <h3 class="service-card-title" data-words-slide-from-right aria-label="{{ $service['title'] }}">
                                <span class="service-title-word" style="--services-word-index: 0" aria-hidden="true">{{ $service['title'] }}</span>
                            </h3>
                            <p class="service-statement">{{ $service['statement'] }}</p>
                            <p class="service-description">{{ $service['text'] }}</p>
                            <span class="service-link" aria-hidden="true">SAZNAJTE VIŠE <i class="fa-duotone fa-thin fa-arrow-right fa-fw"></i></span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="process-section" id="proces" aria-labelledby="process-title" data-process-reveal>
        <div class="process-shell">
            <header class="process-header">
                @php $processTitle = ['Jednostavan', 'proces.', 'Jasni', 'koraci.']; @endphp
                <h2 class="process-title" id="process-title" data-words-slide-from-right aria-label="Jednostavan proces. Jasni koraci.">
                    @foreach ($processTitle as $word)<span class="process-title-word {{ $word === 'koraci.' ? 'is-accent' : '' }}" style="--services-word-index: {{ $loop->index }}" aria-hidden="true">{{ $word }}</span>@endforeach
                </h2>
            </header>
            <div class="process-track">
                @foreach ($processItems as $item)
                    <article class="process-item" style="--process-index: {{ $loop->index }}">
                        <div class="process-marker" aria-hidden="true"><span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></div>
                        <i class="process-icon fa-duotone fa-thin fa-fw {{ $item['icon'] }}" aria-hidden="true"></i>
                        <div class="process-copy"><h3>{{ $item['title'] }}</h3><p>{{ $item['text'] }}</p></div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="locations-section" id="lokacije" aria-labelledby="locations-title" data-locations-reveal>
        <div class="locations-shell">
            <div class="locations-layout">
                <div class="locations-copy">
                    <h2 class="locations-title" id="locations-title" data-words-slide-from-right aria-label="Prisutni na 3 lokacije">
                        @foreach (['Prisutni', 'na', '3', 'lokacije'] as $word)<span class="locations-title-word {{ $word === 'lokacije' ? 'is-accent' : '' }}" style="--services-word-index: {{ $loop->index }}" aria-hidden="true">{{ $word }}</span>@endforeach
                    </h2>
                    <p class="locations-intro"><strong>Zagreb, Rijeka i Vinkovci</strong> — podrška klijentima diljem Hrvatske.</p>

                    <div class="locations-addresses">
                        @foreach ($locationItems as $location)
                            <article class="location-address" style="--location-index: {{ $loop->index }}">
                                <button class="location-address-trigger" type="button" aria-expanded="false" aria-controls="location-details-{{ $loop->index }}" data-location-index="{{ $loop->index }}">
                                    <span class="location-address-marker" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="location-address-summary"><span class="location-address-title">{{ $location['city'] }}</span><span class="location-address-short">{{ $location['address'] }}</span></span>
                                    <span class="location-address-toggle" aria-hidden="true"><span></span><span></span></span>
                                </button>
                                <div class="location-details" id="location-details-{{ $loop->index }}" aria-hidden="true" inert>
                                    <div class="location-details-inner">
                                        <div class="location-details-card">
                                            <span class="location-office-label">{{ $location['office_label'] }}</span>
                                            <h3>{{ $location['company'] }}</h3>
                                            <a class="location-map-link" href="{{ $location['map_url'] }}" target="_blank" rel="noopener noreferrer" tabindex="-1"><i class="fa-light fa-location-dot" aria-hidden="true"></i><span>Pogledaj na karti</span><i class="fa-light fa-arrow-up-right" aria-hidden="true"></i></a>
                                            <div class="location-contacts">
                                                <a href="mailto:{{ $location['email'] }}" tabindex="-1"><i class="fa-light fa-envelope" aria-hidden="true"></i><span><small>Email</small><strong>{{ $location['email'] }}</strong></span></a>
                                                <a href="tel:{{ preg_replace('/[^+0-9]/', '', $location['phone']) }}" tabindex="-1"><i class="fa-light fa-phone" aria-hidden="true"></i><span><small>Telefon</small><strong>{{ $location['phone'] }}</strong></span></a>
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
                        <svg class="map-routes" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true"><path class="map-route" d="M 15.8 28.7 C 24 20, 33 18, 42.5 18.8"></path><path class="map-route" d="M 42.5 18.8 C 57 15, 71 18, 86.7 23.8"></path></svg>
                        @foreach ([1, 0, 2] as $locationIndex)
                            @php $location = $locationItems[$locationIndex]; @endphp
                            <button class="map-location {{ $location['css'] }}" type="button" aria-label="Prikaži kontaktne podatke za ured {{ $location['short_city'] }}" aria-expanded="false" aria-controls="location-details-{{ $locationIndex }}" style="--map-index: {{ $loop->index }}" data-location-index="{{ $locationIndex }}">
                                <span class="map-beacon" aria-hidden="true"><i class="fa-duotone fa-thin fa-bullseye-pointer"></i></span>
                                <span class="map-location-label"><small>{{ $location['number'] }}</small><strong>{{ $location['short_city'] }}</strong></span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="locations-stats" aria-label="Alpha Capitalis u brojkama">
                @foreach ($locationStats as $stat)
                    <article class="location-stat" style="--stat-index: {{ $loop->index }}">
                        <div class="location-stat-icon" aria-hidden="true"><i class="fa-duotone fa-thin fa-fw {{ $statIcons[$loop->index] }}"></i></div>
                        <div><strong><span data-count-target="{{ $stat['value'] }}">0</span><span class="location-stat-suffix">{{ $stat['suffix'] }}</span></strong><p>{{ $stat['label'] }}</p></div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="news-section ac-home-news" id="novosti" aria-labelledby="news-title">
        <div class="news-shell">
            <header class="news-header">
                @php $newsHeading = explode(' ', 'Rokovi, novosti i savjeti za sigurnije poslovanje.'); @endphp
                <h2 class="news-title" id="news-title" data-words-slide-from-right aria-label="Rokovi, novosti i savjeti za sigurnije poslovanje.">
                    @foreach ($newsHeading as $word)<span class="news-title-word {{ $word === 'poslovanje.' ? 'is-accent' : '' }}" style="--services-word-index: {{ $loop->index }}" aria-hidden="true">{{ $word }}</span>@endforeach
                </h2>
                <a class="news-all-link content-reveal" data-image-reveal href="{{ route('blog.index') }}"><span>Pogledaj sve objave</span><i class="fa-duotone fa-thin fa-arrow-right fa-fw" aria-hidden="true"></i></a>
            </header>
            <div class="news-grid">
                @foreach ($newsItems as $item)
                    <a class="news-card" data-image-reveal href="{{ $item['url'] }}" style="--news-index: {{ $loop->index }}">
                        <span class="news-card-category">{{ $item['category'] }}</span><h3>{{ $item['title'] }}</h3><p>{{ $item['text'] }}</p>
                        <span class="news-card-link" aria-hidden="true">{{ __('ui.blog.read_more') }} <i class="fa-duotone fa-thin fa-arrow-right fa-fw"></i></span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="contact-cta" id="kontakt-cta" aria-labelledby="contact-cta-title">
        <div class="contact-cta-shell">
            <div class="contact-cta-copy">
                @php $contactHeading = explode(' ', 'Razgovarajmo o sljedećoj fazi vašeg poslovanja.'); @endphp
                <h2 class="contact-cta-title" id="contact-cta-title" data-words-slide-from-right aria-label="Razgovarajmo o sljedećoj fazi vašeg poslovanja.">
                    @foreach ($contactHeading as $word)<span class="contact-cta-title-word {{ in_array($word, ['sljedećoj', 'fazi'], true) ? 'is-accent' : '' }}" style="--services-word-index: {{ $loop->index }}" aria-hidden="true">{{ $word }}</span>@endforeach
                </h2>
            </div>
            <div class="contact-cta-card" data-image-reveal>
                <div class="contact-cta-card-heading"><span>Vrijeme je za pravi korak.</span></div>
                <p>Dogovorite uvodni sastanak s našim stručnjacima i pretvorite izazove u jasne, izvedive korake.</p>
                <a class="contact-cta-button" href="{{ route('contact.create') }}"><span>Dogovorite sastanak</span><i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i></a>
                <small><span class="contact-cta-status-dot" aria-hidden="true"></span>Termin razgovora prilagođavamo vama.</small>
            </div>
        </div>
    </section>
@endsection
