@extends('front.desktop.layouts.store')

@section('title', config('app.name', 'AG Shop').' Store')
@section('main_class', 'w-full pt-0 pb-0')

@section('content')
    @php
        $versionedAsset = static function (string $relativePath): string {
            $absolutePath = public_path($relativePath);

            return file_exists($absolutePath)
                ? asset($relativePath).'?v='.filemtime($absolutePath)
                : asset($relativePath);
        };

        // Static section payloads mirror the structure we can later hydrate from backend content blocks.
        $serviceCards = [
            [
                'title' => 'Financije',
                'tag' => 'Kapital',
                'icon' => 'finance',
                'icon_asset' => 'front-theme/images/services-icons/financije.svg',
                'anchor' => 'odjel-financije',
                'image' => 'front-theme/images/services/finance-editorial-3d.svg',
                'text' => 'Kapital, poslovno savjetovanje, kontroling i podrška pri financijskim odlukama.',
                'highlights' => ['Kapital', 'Savjetovanje', 'Kontroling'],
                'featured' => true,
                'url' => route('contact.create'),
                'accent' => '#2f6f9f',
                'layout_area' => 'finance',
            ],
            [
                'title' => 'Računovodstvo',
                'tag' => 'Preciznost',
                'icon' => 'accounting',
                'icon_asset' => 'front-theme/images/services-icons/racunovodstvo.svg',
                'anchor' => 'odjel-racunovodstvo',
                'image' => 'front-theme/images/services/accounting-editorial-3d.svg',
                'text' => 'Pouzdano vođenje poslovnih knjiga i jasni izvještaji za svakodnevno upravljanje.',
                'highlights' => ['Knjige', 'Izvještaji'],
                'featured' => true,
                'url' => route('contact.create'),
                'accent' => '#5f7f91',
                'layout_area' => 'accounting',
            ],
            [
                'title' => 'Revizija',
                'tag' => 'Povjerenje',
                'icon' => 'audit',
                'icon_asset' => 'front-theme/images/services-icons/revizija.svg',
                'anchor' => 'odjel-revizija',
                'image' => 'front-theme/images/services/audit-editorial-3d.svg',
                'text' => 'Neovisna stručna mišljenja i procjena poslovnih procesa, rizika i kontrola.',
                'highlights' => ['Mišljenja', 'Procjene'],
                'featured' => true,
                'url' => route('contact.create'),
                'accent' => '#214764',
                'layout_area' => 'audit',
            ],
            [
                'title' => 'Porezi',
                'tag' => 'Usklađenost',
                'icon' => 'tax',
                'icon_asset' => 'front-theme/images/services-icons/porezi.svg',
                'anchor' => 'odjel-porezi',
                'image' => 'front-theme/images/services/tax-editorial-3d.svg',
                'text' => 'Porezno planiranje, usklađenost i podrška u složenim poreznim pitanjima.',
                'highlights' => ['Planiranje', 'Zastupanje'],
                'featured' => true,
                'url' => route('contact.create'),
                'accent' => '#ab8d52',
                'layout_area' => 'tax',
            ],
            [
                'title' => 'EU fondovi',
                'tag' => 'Natječaji',
                'icon' => 'europe',
                'icon_asset' => 'front-theme/images/services-icons/eufondovi.svg',
                'anchor' => 'odjel-eu-fondovi',
                'image' => 'front-theme/images/services/advisory-editorial-3d.svg',
                'text' => 'Priprema projektnih prijedloga i usklađivanje ulaganja s aktualnim natječajima.',
                'highlights' => ['Natječaji', 'Projekti'],
                'featured' => true,
                'url' => route('contact.create'),
                'accent' => '#3f7c78',
                'layout_area' => 'eu',
            ],
            [
                'title' => 'Obiteljski biznis',
                'tag' => 'Nasljeđe',
                'icon' => 'family',
                'icon_asset' => 'front-theme/images/services-icons/obiteljski-biznis.svg',
                'anchor' => 'odjel-obiteljski-biznisi',
                'image' => 'front-theme/images/services/family-business-editorial-3d.svg',
                'text' => 'Podrška vlasničkoj tranziciji, upravljanju i dugoročnoj stabilnosti obitelji i poslovanja.',
                'highlights' => ['Tranzicija', 'Nasljeđivanje'],
                'featured' => true,
                'url' => route('family-business.show'),
                'accent' => '#8c6a47',
                'layout_area' => 'family',
            ],
        ];

        $consultationCta = [
            'eyebrow' => 'Besplatni inicijalni sastanak',
            'title_lines' => [
                'Izbjegnite nevolje na području',
                'financija, računovodstva, revizije i poreza',
            ],
            'button' => ['label' => 'Ugovorite sastanak', 'url' => route('contact.create')],
        ];

        $supportJourney = [
            'eyebrow' => 'ALPHA CAPITALIS',
            'title_lines' => ['Podrška klijentima u svim', 'fazama razvoja poslovanja'],
            'text' => 'Pribavljamo kapital za razvoj poslovanja, pomažemo kod donošenja odluka oko financiranja i optimizacije bilance te postavljamo kontroling unutar organizacije.',
            'cards' => [
                [
                    'title' => 'Obiteljski biznis',
                    'icon' => 'family-tree',
                    'lead' => 'Podržavamo obiteljske biznise u svim fazama poslovanja.',
                    'list' => [
                        'Tranzicija na mlađe generacije',
                        'Upravljanje konfliktima i nasljeđivanjem',
                        'Izlazna strategija',
                        'Model korporativnog upravljanja',
                    ],
                ],
                [
                    'title' => 'Zatvaramo transakcije',
                    'icon' => 'handshake',
                    'lead' => 'Vodimo kupnju, prodaju, spajanja i dokapitalizacije.',
                    'list' => [
                        'Prijenos vlasništva na mlađe generacije, menadžment ili investitore',
                        'Priprema dokumentacije za prodaju udjela ili dionica',
                        'Strukturiran pristup potencijalnim investitorima',
                    ],
                ],
                [
                    'title' => 'BTP platforma',
                    'icon' => 'network',
                    'lead' => 'Povezujemo ulagatelje s target društvima i prodavatelje s investitorima.',
                    'list' => [
                        'Kupnja biznisa i dokapitalizacija',
                        'Ulazak na tržište',
                        'Kupnja imovine',
                        'Projekti za ulaganje i društva na prodaju',
                    ],
                ],
                [
                    'title' => 'Pribavljano financiranje',
                    'icon' => 'bank',
                    'lead' => 'Preuzimamo komunikaciju s investitorima i kreditnim institucijama.',
                    'list' => [
                        'Poslovni plan',
                        'Investicijska studija',
                        'Kreditni zahtjev',
                        'Prezentacija poslovnog modela',
                    ],
                ],
                [
                    'title' => 'EU fondovi',
                    'icon' => 'europe',
                    'lead' => 'Pratimo aktualne mogućnosti EU financiranja za mikro, mala i srednja poduzeća.',
                    'list' => [
                        'Oblikovanje projektne ideje',
                        'Priprema investicijskog plana',
                        'Usklađivanje ulaganja s aktualnim natječajima',
                    ],
                ],
                [
                    'title' => 'Digitalno savjetovanje',
                    'icon' => 'chart-grid',
                    'lead' => 'Pomažemo tvrtkama da se prilagode promjenjivim tržišnim uvjetima.',
                    'list' => [
                        'Optimizacija poslovanja',
                        'Poboljšanje financijske stabilnosti',
                        'Veća učinkovitost',
                    ],
                ],
                [
                    'title' => 'Izdajemo neovisna stručna izvješća/mišljenja',
                    'icon' => 'file-check',
                    'lead' => 'Izrađujemo neovisna mišljenja i stručne procjene za ključne odluke.',
                    'list' => [
                        'Revizorsko izvješće ili mišljenje',
                        'Sumnja na korporativne prevare',
                        'Procjena tržišne vrijednosti poslovnih udjela',
                        'Provjera prije preuzimanja društva',
                    ],
                ],
                [
                    'title' => 'Porezno planiranje i porezno zastupanje',
                    'icon' => 'shield-percent',
                    'lead' => 'Savjetujemo i zastupamo klijente kod složenih poreznih pitanja.',
                    'list' => [
                        'Porezno planiranje',
                        'Porezno zastupanje',
                        'Mišljenje i procjena poreznih rizika',
                        'Podrška pri preuzimanju društva',
                    ],
                ],
                [
                    'title' => 'Vodimo poslovne knjige',
                    'icon' => 'book-open',
                    'lead' => 'Otvaramo društva i vodimo poslovne knjige za domaće i strane klijente.',
                    'list' => [
                        'Zastupanje pred poreznom upravom i kreditnim institucijama',
                        'Redoviti mjesečni i tjedni izvještaji',
                        'ALPHA CLOUD pristup aplikaciji',
                    ],
                ],
            ],
        ];

        $globalMemberships = [
            [
                'name' => 'TAG Alliances',
                'logo_mark' => 'TAG',
                'logo_sub' => 'Alliances',
                'logo_tag' => 'Member',
                'logo_url' => asset('front-theme/images/logos/tag-alliances-logo.png'),
                'logo_theme' => 'light',
                'description' => 'Globalna mreža neovisnih računovodstvenih i savjetodavnih kuća za međunarodnu suradnju i razmjenu stručnog znanja.',
                'url' => 'https://www.tagalliances.com/',
                'accent' => 'blue',
            ],
            [
                'name' => 'Family Firm Institute',
                'logo_mark' => 'FFI',
                'logo_sub' => 'GEN',
                'logo_tag' => 'Family Business',
                'logo_url' => 'https://www.ffi.org/wp-content/uploads/2018/08/ffi-foot-logo.png',
                'logo_theme' => 'light',
                'description' => 'Najutjecajnija globalna mreža lidera i edukatora u području obiteljskog biznisa i savjetovanja za vlasničke tranzicije.',
                'url' => 'https://www.ffi.org/',
                'accent' => 'amber',
            ],
            [
                'name' => 'Pandea Global M&A',
                'logo_mark' => 'Pandea',
                'logo_sub' => 'Global M&A',
                'logo_tag' => 'Cross-border',
                'logo_url' => asset('front-theme/images/logos/pandea-logo-small.png'),
                'logo_image_class' => 'is-compact',
                'logo_theme' => 'light',
                'description' => 'Međunarodna M&A mreža koja povezuje investitore, kupce i prodavatelje kroz strukturirane cross-border transakcije.',
                'url' => 'https://pandeaglobal.com/',
                'accent' => 'navy',
            ],
            [
                'name' => 'Transeo International',
                'logo_mark' => 'Transeo',
                'logo_sub' => 'International',
                'logo_tag' => 'Transfer',
                'logo_url' => asset('front-theme/images/logos/transeo.svg'),
                'logo_theme' => 'light',
                'description' => 'Europska zajednica stručnjaka za prijenos vlasništva, SME transakcije i razvoj business transfer ekosustava.',
                'url' => 'https://www.transeo-association.eu/',
                'accent' => 'teal',
            ],
            [
                'name' => 'International Fiscal Association',
                'logo_mark' => 'IFA',
                'logo_sub' => 'Fiscal Association',
                'logo_tag' => 'Tax',
                'logo_url' => asset('front-theme/images/logos/ifa-logo-white.svg'),
                'logo_theme' => 'light',
                'description' => 'Vodeća međunarodna neovisna organizacija posvećena međunarodnom poreznom pravu i usporednim fiskalnim pitanjima.',
                'url' => 'https://www.ifa.nl/',
                'accent' => 'violet',
            ],
        ];

        $clientExperienceSection = [
            'eyebrow' => $locale === 'hr' ? 'Preporuke klijenata' : 'Client testimonials',
            'title' => $locale === 'hr' ? 'Iskustva naših klijenata' : 'What Our Clients Say',
            'intro' => $locale === 'hr'
                ? 'Odabrana iskustva tvrtki i timova koji s nama grade stabilnije i jasnije poslovne odluke.'
                : 'Selected testimonials from companies and teams that rely on us for clearer and more stable business decisions.',
        ];
    @endphp

    <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
        <section id="usluge" class="ac-services-modern">
            <div class="ac-services-head ac-services-head--stacked">
                <div class="ac-services-eyebrow">
                    <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                    <p class="ac-services-kicker">Usluge</p>
                    <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                </div>
                <h2>
                    <span>Ključne usluge za rast,</span>
                    <span>stabilnost i jasne odluke</span>
                </h2>
                <p class="ac-services-intro">
                    Od financija i računovodstva do revizije, poreza, EU fondova i obiteljskih tranzicija,
                    ALPHA CAPITALIS objedinjuje ključna područja poslovne podrške na jednom mjestu.
                </p>
                <div class="ac-services-divider" aria-hidden="true">
                    <span class="ac-services-divider-line"></span>
                    <span class="ac-services-divider-glyph"></span>
                    <span class="ac-services-divider-line"></span>
                </div>
            </div>

            <div class="ac-services-grid ac-services-grid--orbit">
                <div class="ac-services-orbit-lines" aria-hidden="true">
                    <span class="ac-services-orbit-line ac-services-orbit-line--top-left"></span>
                    <span class="ac-services-orbit-line ac-services-orbit-line--top-right"></span>
                    <span class="ac-services-orbit-line ac-services-orbit-line--mid-left"></span>
                    <span class="ac-services-orbit-line ac-services-orbit-line--mid-right"></span>
                    <span class="ac-services-orbit-line ac-services-orbit-line--bottom-left"></span>
                    <span class="ac-services-orbit-line ac-services-orbit-line--bottom-right"></span>
                </div>
                <div class="ac-services-center-card">
                    <div class="ac-services-center-card-inner">
                        <p class="ac-services-center-kicker">Usluge</p>
                        <h3>
                            <span>Ključne usluge za rast,</span>
                            <span>stabilnost i jasne odluke</span>
                        </h3>
                        <p>
                            Od financija i računovodstva do revizije, poreza, EU fondova i obiteljskih tranzicija,
                            ALPHA CAPITALIS objedinjuje ključna područja poslovne podrške na jednom mjestu.
                        </p>
                    </div>
                </div>
                @foreach ($serviceCards as $card)
                    <article
                        id="{{ $card['anchor'] }}"
                        class="ac-service-item {{ $card['featured'] ? 'is-featured' : '' }}"
                        style="--ac-service-accent: {{ $card['accent'] ?? '#ab8d52' }};"
                        data-service-area="{{ $card['layout_area'] ?? '' }}"
                    >
                        <a href="{{ $card['url'] ?? route('contact.create') }}" class="ac-service-card-link">
                            <div class="ac-service-media">
                                <img
                                    src="{{ $versionedAsset($card['image']) }}"
                                    alt=""
                                    aria-hidden="true"
                                    class="ac-service-media-art"
                                    loading="lazy"
                                    decoding="async"
                                >
                                <div class="ac-service-head-content">
                                    <span class="ac-service-icon-wrap" aria-hidden="true">
                                        <img
                                            src="{{ $versionedAsset($card['icon_asset']) }}"
                                            alt=""
                                            aria-hidden="true"
                                            class="ac-service-icon"
                                            loading="lazy"
                                            decoding="async"
                                        >
                                    </span>
                                    <h3>{{ $card['title'] }}</h3>
                                </div>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </section>

    </div>

    <section class="ac-inline-cta" aria-labelledby="ac-inline-cta-title">
        <div class="ac-inline-cta-card">
            <div class="mx-auto grid w-full max-w-[1240px] gap-4 px-5 py-5 md:grid-cols-[minmax(0,1fr)_auto] md:items-center lg:px-8">
                <div class="ac-inline-cta-copy">
                    <h2 id="ac-inline-cta-title" class="ac-inline-cta-title">
                        @foreach ($consultationCta['title_lines'] as $line)
                            <span>{{ $line }}</span>
                        @endforeach
                    </h2>
                </div>

                <div class="ac-inline-cta-action">
                    <a href="{{ $consultationCta['button']['url'] }}" class="front-action-cta">
                        <span>{{ $consultationCta['button']['label'] }}</span>
                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 12L12 4"></path>
                            <path d="M6 4h6v6"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
        <section id="poslovna-podrska" class="ac-support-story" aria-labelledby="ac-support-story-title">
            <div class="ac-support-story-hero">
                <div class="ac-support-story-shell">
                    <div class="ac-services-head ac-support-story-head">
                        <div class="ac-services-eyebrow">
                            <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                            <p class="ac-services-kicker">{{ $supportJourney['eyebrow'] }}</p>
                            <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                        </div>
                        <h2 id="ac-support-story-title">
                            @foreach ($supportJourney['title_lines'] as $line)
                                <span>{{ $line }}</span>
                            @endforeach
                        </h2>
                        <p class="ac-services-intro">{{ $supportJourney['text'] }}</p>
                        <div class="ac-services-divider" aria-hidden="true">
                            <span class="ac-services-divider-line"></span>
                            <span class="ac-services-divider-glyph"></span>
                            <span class="ac-services-divider-line"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ac-support-story-grid">
                @foreach ($supportJourney['cards'] as $card)
                    <article class="ac-support-story-card">
                        <span class="ac-support-story-card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6">
                                @switch($card['icon'] ?? '')
                                    @case('family-tree')
                                        <circle cx="7.5" cy="6.5" r="1.6"></circle>
                                        <circle cx="16.5" cy="6.5" r="1.6"></circle>
                                        <circle cx="12" cy="17" r="1.8"></circle>
                                        <path d="M7.5 8.3V11h9V8.3"></path>
                                        <path d="M12 11v4.2"></path>
                                        @break
                                    @case('handshake')
                                        <path d="M4.5 9.5 8 7.8a3 3 0 0 1 3 .2l1 .6"></path>
                                        <path d="M19.5 9.5 16 7.8a3 3 0 0 0-3 .2l-2.4 1.5a1.7 1.7 0 0 0 1.8 2.9l1.9-1.1"></path>
                                        <path d="m8.7 12.2 2.1 1.8"></path>
                                        <path d="m10.7 11.6 2.2 1.9"></path>
                                        <path d="m12.8 11.1 2.1 1.8"></path>
                                        <path d="M4.5 9.5v5l2.6 1.4 1.6-1"></path>
                                        <path d="M19.5 9.5v5l-2.6 1.4-1.6-1"></path>
                                        @break
                                    @case('network')
                                        <circle cx="6" cy="12" r="1.7"></circle>
                                        <circle cx="18" cy="7" r="1.7"></circle>
                                        <circle cx="18" cy="17" r="1.7"></circle>
                                        <circle cx="12" cy="12" r="1.7"></circle>
                                        <path d="M7.7 12h2.6"></path>
                                        <path d="M13.5 10.9 16.5 8.1"></path>
                                        <path d="M13.5 13.1 16.5 15.9"></path>
                                        @break
                                    @case('bank')
                                        <path d="M4 9.2 12 5l8 4.2"></path>
                                        <path d="M5.5 9.5h13"></path>
                                        <path d="M7 9.5v6.3"></path>
                                        <path d="M12 9.5v6.3"></path>
                                        <path d="M17 9.5v6.3"></path>
                                        <path d="M4.8 18h14.4"></path>
                                        @break
                                    @case('europe')
                                        <circle cx="12" cy="12" r="7"></circle>
                                        <path d="m12 7.9.55 1.1 1.21.17-.88.85.21 1.2L12 10.7l-1.09.57.21-1.2-.88-.85 1.21-.17L12 7.9Z"></path>
                                        <path d="m16.2 10.1.4.8.88.13-.64.62.15.87-.79-.42-.79.42.15-.87-.64-.62.88-.13.4-.8Z"></path>
                                        <path d="m15.1 14.6.38.77.85.13-.61.59.14.84-.76-.4-.77.4.15-.84-.62-.59.85-.13.39-.77Z"></path>
                                        <path d="m8.9 14.6.38.77.85.13-.61.59.14.84-.76-.4-.77.4.15-.84-.62-.59.85-.13.39-.77Z"></path>
                                        @break
                                    @case('chart-grid')
                                        <path d="M5 18.5h14"></path>
                                        <path d="M7.5 16v-4"></path>
                                        <path d="M12 16V8"></path>
                                        <path d="M16.5 16v-6"></path>
                                        <path d="m6 10.5 3-2 2.5 1.5 4-3"></path>
                                        @break
                                    @case('file-check')
                                        <path d="M8 4.5h6l3 3V18a1.5 1.5 0 0 1-1.5 1.5h-7A1.5 1.5 0 0 1 7 18V6a1.5 1.5 0 0 1 1-1.5Z"></path>
                                        <path d="M14 4.5V8h3"></path>
                                        <path d="m9.8 13.2 1.6 1.6 3.1-3.3"></path>
                                        @break
                                    @case('shield-percent')
                                        <path d="m12 4 6 2.7v4.8c0 3.6-2.3 6.4-6 8-3.7-1.6-6-4.4-6-8V6.7L12 4Z"></path>
                                        <path d="M9.4 14.8 14.8 9.4"></path>
                                        <circle cx="9.1" cy="9.2" r=".9"></circle>
                                        <circle cx="14.9" cy="14.8" r=".9"></circle>
                                        @break
                                    @case('book-open')
                                        <path d="M6 6.5A2.5 2.5 0 0 1 8.5 4H18v15h-9.5A2.5 2.5 0 0 0 6 21"></path>
                                        <path d="M18 4h-9.5A2.5 2.5 0 0 0 6 6.5v12"></path>
                                        <path d="M12 6.5v11"></path>
                                        @break
                                    @default
                                        <circle cx="12" cy="12" r="7"></circle>
                                        <path d="M12 8v4l2.8 2.2"></path>
                                @endswitch
                            </svg>
                        </span>
                        <h3>{{ $card['title'] }}</h3>
                        @if (!empty($card['lead']) || !empty($card['text']))
                            <p class="ac-support-story-card-lead">{{ $card['lead'] ?? $card['text'] }}</p>
                        @endif

                        @if (!empty($card['list']))
                            <ul class="ac-support-story-card-list">
                                @foreach ($card['list'] as $listItem)
                                    <li>{{ $listItem }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    </div>

    <section id="clanstva" class="ac-global-memberships" aria-labelledby="ac-global-memberships-title">
        <div class="ac-global-memberships-shell mx-auto w-full max-w-[1240px] px-6 lg:px-10">
            <div class="ac-services-head ac-support-story-head ac-global-memberships-head">
                <div class="ac-services-eyebrow">
                    <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                    <p class="ac-services-kicker">Mreže i članstva</p>
                    <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                </div>
                <h2 id="ac-global-memberships-title">
                    <span>Globalna partnerstva i stručna članstva</span>
                </h2>
                <p class="ac-services-intro">ALPHA CAPITALIS surađuje s relevantnim međunarodnim mrežama koje šire pristup znanju, investitorima i specijaliziranim ekspertima.</p>
                <div class="ac-services-divider" aria-hidden="true">
                    <span class="ac-services-divider-line"></span>
                    <span class="ac-services-divider-glyph"></span>
                    <span class="ac-services-divider-line"></span>
                </div>
            </div>

            <div class="ac-global-memberships-carousel">
                <div id="ac-global-memberships-splide" class="splide ac-global-memberships-splide" data-global-memberships-splide>
                    <div class="splide__track">
                        <ul class="splide__list ac-global-memberships-list">
                            @foreach ($globalMemberships as $membership)
                                <li class="splide__slide ac-global-memberships-slide">
                                    <article class="ac-membership-card ac-membership-card--{{ $membership['accent'] }}">
                                        <h3 class="sr-only">{{ $membership['name'] }}</h3>
                                        <div class="ac-membership-logo {{ !empty($membership['logo_url']) ? 'has-image' : '' }} {{ ($membership['logo_theme'] ?? '') === 'light' ? 'is-light' : '' }}" aria-hidden="true">
                                            @if (!empty($membership['logo_url']))
                                                <img
                                                    src="{{ $membership['logo_url'] }}"
                                                    alt=""
                                                    class="ac-membership-logo-image {{ $membership['logo_image_class'] ?? '' }}"
                                                    loading="lazy"
                                                    decoding="async"
                                                >
                                            @else
                                                <span class="ac-membership-logo-tag">{{ $membership['logo_tag'] }}</span>
                                                <span class="ac-membership-logo-mark">{{ $membership['logo_mark'] }}</span>
                                                <span class="ac-membership-logo-sub">{{ $membership['logo_sub'] }}</span>
                                            @endif
                                        </div>

                                        <p class="ac-membership-copy">{{ $membership['description'] }}</p>

                                        <a href="{{ $membership['url'] }}" class="ac-membership-link" target="_blank" rel="noopener noreferrer">
                                            <span>Opširnije</span>
                                            <span class="ac-membership-link-arrow" aria-hidden="true">
                                                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M4 12L12 4"></path>
                                                    <path d="M6 4h6v6"></path>
                                                </svg>
                                            </span>
                                        </a>
                                    </article>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (($latestBlogPosts ?? collect())->isNotEmpty())
        <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
            <section id="novosti" class="ac-support-story ac-home-blog" aria-labelledby="ac-home-blog-title">
                <div class="ac-support-story-hero">
                    <div class="ac-support-story-shell">
                        <div class="ac-services-head ac-support-story-head">
                            <div class="ac-services-eyebrow">
                                <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                                <p class="ac-services-kicker">ALPHA CAPITALIS</p>
                                <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                            </div>
                            <h2 id="ac-home-blog-title">
                                <span>Zadnje objave i novosti</span>

                            </h2>
                            <p class="ac-services-intro">
                                Zadnjih pet blog objava iz područja financija, poreza, transakcija i poslovnog savjetovanja.
                            </p>
                            <div class="ac-services-divider" aria-hidden="true">
                                <span class="ac-services-divider-line"></span>
                                <span class="ac-services-divider-glyph"></span>
                                <span class="ac-services-divider-line"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ac-home-blog-carousel">
                    <div id="ac-home-blog-splide" class="splide ac-home-blog-splide" data-home-blog-splide>
                        <div class="splide__track">
                            <ul class="splide__list">
                                @foreach ($latestBlogPosts as $post)
                                    @php
                                        $translation = $post->translations->firstWhere('locale', $locale)
                                            ?? $post->translations->firstWhere('locale', $fallbackLocale);
                                        $postSlug = trim((string) ($translation?->slug ?? ''));
                                        $postUrl = $postSlug !== '' ? route('blog.show', ['slug' => $postSlug]) : route('blog.index');
                                        $postTitle = trim((string) ($translation?->title ?? $post->code));
                                        $postExcerpt = trim((string) ($translation?->excerpt ?? '')) ?: __('ui.blog.excerpt_fallback');
                                        $postExcerpt = \Illuminate\Support\Str::limit($postExcerpt, 180, '...', true);
                                        $postImage = $post->getFirstMedia('blog_cover');
                                        $postImageUrl = $postImage?->getUrl();
                                        $primaryCategory = $post->categories
                                            ->sortByDesc(fn ($category) => (int) ($category->pivot->is_primary ?? false))
                                            ->first();
                                        $categoryTranslation = $primaryCategory?->translations->firstWhere('locale', $locale)
                                            ?? $primaryCategory?->translations->firstWhere('locale', $fallbackLocale);
                                        $categoryLabel = trim((string) ($categoryTranslation?->name ?? 'Novosti'));
                                        $publishedLabel = ($post->published_at ?? $post->created_at)?->translatedFormat('j. F Y.');
                                    @endphp

                                    <li class="splide__slide ac-home-blog-slide">
                                        <article class="ac-home-blog-card">
                                            <a href="{{ $postUrl }}" class="ac-home-blog-card-link" aria-label="Otvori blog post: {{ $postTitle }}">
                                                <div class="ac-home-blog-card-media">
                                                    @if ($postImageUrl)
                                                        <img
                                                            src="{{ $postImageUrl }}"
                                                            alt="{{ $postTitle }}"
                                                            class="ac-home-blog-card-image"
                                                            loading="lazy"
                                                            decoding="async"
                                                        >
                                                    @else
                                                        <div class="ac-home-blog-card-placeholder">
                                                            <span>{{ __('ui.blog.title') }}</span>
                                                        </div>
                                                    @endif

                                                    <div class="ac-home-blog-card-overlay">
                                                        <span class="ac-home-blog-card-overlay-kicker">
                                                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::limit($categoryLabel, 22, '')) }}
                                                        </span>
                                                        <span class="ac-home-blog-card-overlay-line" aria-hidden="true"></span>
                                                    </div>
                                                </div>

                                                <div class="ac-home-blog-card-body">
                                                    <h3 class="ac-home-blog-card-title">{{ $postTitle }}</h3>
                                                    <p class="ac-home-blog-card-excerpt">{{ $postExcerpt }}</p>
                                                </div>

                                                <div class="ac-home-blog-card-meta">
                                                    <span class="ac-home-blog-card-meta-link">
                                                        <span>Opširnije</span>
                                                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                            <path d="M4 12L12 4"></path>
                                                            <path d="M6 4h6v6"></path>
                                                        </svg>
                                                    </span>
                                                    @if ($publishedLabel)
                                                        <span class="ac-home-blog-card-meta-date">{{ $publishedLabel }}</span>
                                                    @endif
                                                </div>
                                            </a>
                                        </article>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endif

    @if (($clientTestimonials ?? collect())->isNotEmpty())
        <section id="iskustva-klijenata" class="ac-global-memberships ac-client-experiences" aria-labelledby="ac-client-experiences-title">
            <div class="ac-global-memberships-shell mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                <div class="ac-services-head ac-support-story-head ac-global-memberships-head ac-client-experiences-head">
                    <div class="ac-services-eyebrow">
                        <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                        <p class="ac-services-kicker">{{ $clientExperienceSection['eyebrow'] }}</p>
                        <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                    </div>
                    <h2 id="ac-client-experiences-title">
                        <span>{{ $clientExperienceSection['title'] }}</span>
                    </h2>
                    <p class="ac-services-intro">{{ $clientExperienceSection['intro'] }}</p>
                    <div class="ac-services-divider" aria-hidden="true">
                        <span class="ac-services-divider-line"></span>
                        <span class="ac-services-divider-glyph"></span>
                        <span class="ac-services-divider-line"></span>
                    </div>
                </div>

                @php
                    $testimonialReadMoreLabel = $locale === 'hr' ? 'Pročitaj više' : 'Read more';
                    $testimonialShowLessLabel = $locale === 'hr' ? 'Prikaži manje' : 'Show less';
                @endphp

                <div class="ac-client-experiences-carousel">
                    <div id="ac-client-experiences-splide" class="splide ac-client-experiences-splide" data-client-experiences-splide>
                        <div class="splide__track">
                            <ul class="splide__list ac-client-experiences-list">
                                @foreach ($clientTestimonials as $testimonial)
                                    @php
                                        $company = trim((string) ($testimonial->payload['company'] ?? ''));
                                        $rating = max(1, min(5, (int) ($testimonial->rating ?? 5)));
                                    @endphp
                                    <li class="splide__slide ac-client-experiences-slide">
                                        <article class="ac-client-experience-card" data-testimonial-card>
                                            <div class="ac-client-experience-card-inner">
                                                <div class="ac-client-experience-quote-mark" aria-hidden="true">“</div>
                                                <div class="ac-client-experience-content">
                                                    <div class="ac-client-experience-rating" aria-label="{{ $rating }} / 5">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <span class="{{ $i <= $rating ? 'is-active' : '' }}">★</span>
                                                        @endfor
                                                    </div>
                                                    <p class="ac-client-experience-body" data-testimonial-body>{{ $testimonial->body }}</p>
                                                    <button
                                                        type="button"
                                                        class="ac-client-experience-toggle"
                                                        data-testimonial-toggle
                                                        data-more-label="{{ $testimonialReadMoreLabel }}"
                                                        data-less-label="{{ $testimonialShowLessLabel }}"
                                                        aria-expanded="false"
                                                        hidden
                                                    >{{ $testimonialReadMoreLabel }}</button>
                                                </div>
                                                <div class="ac-client-experience-meta">
                                                    <h3>{{ $testimonial->author_name ?: __('Anonymous') }}</h3>
                                                    @if ($company !== '')
                                                        <p>{{ $company }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </article>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @once
        <script>
            (function () {
                const syncTestimonialToggles = function () {
                    document.querySelectorAll('[data-testimonial-card]').forEach(function (card) {
                        const body = card.querySelector('[data-testimonial-body]');
                        const toggle = card.querySelector('[data-testimonial-toggle]');

                        if (!body || !toggle) {
                            return;
                        }

                        if (card.classList.contains('is-expanded')) {
                            toggle.hidden = false;
                            toggle.textContent = toggle.dataset.lessLabel || 'Show less';
                            toggle.setAttribute('aria-expanded', 'true');
                            return;
                        }

                        const hasOverflow = body.scrollHeight > body.clientHeight + 1;
                        toggle.hidden = !hasOverflow;
                        toggle.textContent = toggle.dataset.moreLabel || 'Read more';
                        toggle.setAttribute('aria-expanded', 'false');
                    });
                };

                document.addEventListener('click', function (event) {
                    const toggle = event.target.closest('[data-testimonial-toggle]');

                    if (!toggle) {
                        return;
                    }

                    const card = toggle.closest('[data-testimonial-card]');

                    if (!card) {
                        return;
                    }

                    const isExpanded = card.classList.toggle('is-expanded');
                    toggle.textContent = isExpanded
                        ? (toggle.dataset.lessLabel || 'Show less')
                        : (toggle.dataset.moreLabel || 'Read more');
                    toggle.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');

                    window.requestAnimationFrame(syncTestimonialToggles);
                });

                let testimonialResizeFrame = null;
                window.addEventListener('resize', function () {
                    if (testimonialResizeFrame !== null) {
                        window.cancelAnimationFrame(testimonialResizeFrame);
                    }

                    testimonialResizeFrame = window.requestAnimationFrame(function () {
                        testimonialResizeFrame = null;
                        syncTestimonialToggles();
                    });
                });

                const init = function () {
                    if (typeof window.Splide !== 'function') {
                        return false;
                    }

                    const mountSlider = function (selector, optionsFactory) {
                        const sliders = document.querySelectorAll(selector);
                        sliders.forEach(function (el) {
                            if (el.dataset.splideReady === '1') {
                                return;
                            }
                            el.dataset.splideReady = '1';

                            const count = el.querySelectorAll('.splide__slide').length;
                            const slider = new window.Splide(el, optionsFactory(count));
                            slider.mount();

                            if (selector === '[data-client-experiences-splide]') {
                                window.requestAnimationFrame(syncTestimonialToggles);
                            }
                        });
                    };

                    mountSlider('[data-home-blog-splide]', function (count) {
                        return {
                            type: count > 1 ? 'loop' : 'slide',
                            perPage: Math.min(3, Math.max(1, count)),
                            perMove: 1,
                            gap: '1.25rem',
                            drag: count > 1,
                            snap: true,
                            pagination: count > 1,
                            arrows: count > 1,
                            updateOnMove: true,
                            speed: 520,
                            breakpoints: {
                                1180: { perPage: Math.min(2, Math.max(1, count)) },
                                760: { perPage: 1, gap: '1rem' },
                            },
                        };
                    });

                    mountSlider('[data-global-memberships-splide]', function (count) {
                        return {
                            type: count > 4 ? 'loop' : 'slide',
                            perPage: Math.min(4, Math.max(1, count)),
                            perMove: 1,
                            gap: '1.1rem',
                            drag: count > 1,
                            snap: true,
                            pagination: count > 1,
                            arrows: count > 1,
                            updateOnMove: true,
                            speed: 520,
                            breakpoints: {
                                1280: { perPage: Math.min(3, Math.max(1, count)) },
                                960: { perPage: Math.min(2, Math.max(1, count)), gap: '1rem' },
                                760: { perPage: 1, gap: '0.92rem' },
                            },
                        };
                    });

                    mountSlider('[data-client-experiences-splide]', function (count) {
                        return {
                            type: count > 2 ? 'loop' : 'slide',
                            rewind: count <= 2,
                            perPage: Math.min(2, Math.max(1, count)),
                            perMove: 1,
                            gap: '1.15rem',
                            drag: count > 1,
                            snap: true,
                            pagination: count > 1,
                            arrows: count > 1,
                            updateOnMove: true,
                            speed: 540,
                            breakpoints: {
                                1080: { perPage: 1, gap: '1rem' },
                                760: { gap: '0.92rem' },
                            },
                        };
                    });

                    return true;
                };

                if (init()) {
                    return;
                }

                let attempts = 0;
                const timer = window.setInterval(function () {
                    attempts += 1;
                    if (init() || attempts > 40) {
                        window.clearInterval(timer);
                    }
                }, 120);
            })();
        </script>
    @endonce
@endsection
