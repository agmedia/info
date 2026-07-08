@php
    $basePayload = is_array($block->payload ?? null) ? $block->payload : [];
    $translationPayload = is_array($translation?->payload ?? null) ? $translation->payload : [];
    $payload = array_merge($basePayload, $translationPayload);
    $services = collect((array) ($payload['services'] ?? []))
        ->map(static function ($service): array {
            $service = is_array($service) ? $service : [];
            $bullets = collect((array) ($service['bullets'] ?? []))
                ->map(static fn ($bullet): string => trim((string) $bullet))
                ->filter(static fn (string $bullet): bool => $bullet !== '')
                ->values()
                ->all();

            return [
                'title' => trim((string) ($service['title'] ?? '')),
                'subtitle' => trim((string) ($service['subtitle'] ?? '')),
                'text' => trim((string) ($service['text'] ?? '')),
                'bullets' => $bullets,
                'url' => trim((string) ($service['url'] ?? '')),
                'action_label' => trim((string) ($service['action_label'] ?? '')),
            ];
        })
        ->filter(static fn (array $service): bool => $service['title'] !== '')
        ->values();

    if ($services->isEmpty()) {
        $services = collect([
            [
                'title' => 'Revizija',
                'subtitle' => 'sigurnost i povjerenje u brojke',
                'text' => 'Neovisna provjera financijskih izvještaja koja povećava povjerenje vlasnika, investitora i partnera.',
                'bullets' => [
                    'Pomažemo vlasnicima, investitorima i upravi da imaju potpunu sigurnost u financijske izvještaje.',
                    'Revizija smanjuje rizik pogrešnih odluka jer potvrđuje da su podaci točni, potpuni i u skladu s propisima.',
                    'Kroz neovisnu provjeru dobivate jasnu sliku stvarnog financijskog stanja poduzeća, što jača povjerenje banaka, partnera i regulatora.',
                ],
                'url' => route('audit.show'),
                'action_label' => 'Detaljnije',
            ],
            [
                'title' => 'Računovodstvo',
                'subtitle' => 'kontrola i jasnoća poslovanja',
                'text' => 'Precizno vođenje knjiga i pravovremeno izvještavanje koje oslobađa menadžment za strateške odluke.',
                'bullets' => [
                    'Omogućujemo da vaše poslovanje bude financijski uredno, pregledno i uvijek spremno za odluke.',
                    'To znači da u svakom trenutku imate točne podatke o prihodima, troškovima i rezultatu, bez kašnjenja i nejasnoća.',
                    'Umjesto da reagirate na probleme, možete upravljati poslovanjem na temelju pouzdanih informacija.',
                ],
                'url' => route('accounting.show'),
                'action_label' => 'Detaljnije',
            ],
            [
                'title' => 'Savjetovanje',
                'subtitle' => 'rast, optimizacija i bolji financijski izbor',
                'text' => 'Financijsko i porezno savjetovanje te pribavljanje kapitala - sve na jednom mjestu.',
                'bullets' => [
                    'Pomažemo društvima, investitorima i poduzetnicima u donošenju kvalitetnih odluka, upravljanju rizicima i stvaranju dugoročne vrijednosti.',
                    'Pružamo podršku u procjenama vrijednosti, due diligence postupcima, M&A procesima i strukturiranju financiranja.',
                    'EU fondovi, bankovni krediti i porezne olakšice povezani su u okviru pribavljanja financiranja.',
                ],
                'url' => route('advisory.show'),
                'action_label' => 'Detaljnije',
            ],
        ]);
    }
@endphp

@include('front.desktop.partials.service-pillars-showcase', [
    'sectionId' => 'ac-home-services-showcase',
    'headingLevel' => 2,
    'titleLead' => trim((string) ($translation?->title ?? '')) ?: 'Stvaramo vrijednost za naše klijente u',
    'titleAccent' => trim((string) ($payload['title_accent'] ?? '')) ?: 'svim fazama razvoja poslovanja',
    'intro' => trim((string) ($translation?->subtitle ?? '')),
    'cards' => $services->all(),
])
