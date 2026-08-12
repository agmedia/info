@extends('front.desktop.layouts.store')

@section('title', $servicePageTitle ?? 'Usluge')
@section('main_class', 'w-full px-0 py-0')

@section('content')
    @php
        $showcase = (array) ($servicesShowcase ?? []);
        $titleLead = trim((string) ($showcase['title_lead'] ?? '')) ?: 'Naše usluge';
        $titleAccent = trim((string) ($showcase['title_accent'] ?? ''));
        $intro = trim((string) ($showcase['intro'] ?? ''))
            ?: 'Kroz integrirani pristup reviziji, računovodstvu i financijskom savjetovanju stvaramo dodatnu vrijednost pomažući klijentima da posluju sigurnije, transparentnije i učinkovitije.';
        $introWithServiceLinks = e($intro);
        $introServiceLinks = [
            route('advisory.show') => ['financijskom savjetovanju', 'financial advisory'],
            route('accounting.show') => ['računovodstvu', 'accounting'],
            route('audit.show') => ['reviziji', 'audit'],
        ];

        foreach ($introServiceLinks as $url => $labels) {
            foreach ($labels as $label) {
                $escapedLabel = e($label);
                $introWithServiceLinks = str_replace(
                    $escapedLabel,
                    '<a class="services-index-inline-link" href="'.e($url).'">'.$escapedLabel.'</a>',
                    $introWithServiceLinks,
                );
            }
        }
        $introTitleWords = preg_split('/\s+/u', $titleLead, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $introTitleAccentWords = preg_split('/\s+/u', $titleAccent, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $cardDesign = collect([
            'audit' => [
                'title' => 'Revizija',
                'statement' => 'sigurnost i povjerenje u brojke',
                'text' => 'Neovisna provjera financijskih izvještaja koja povećava povjerenje vlasnika, investitora i partnera.',
                'image' => asset('alpha/service-revizija.jpg'),
                'image_alt' => 'Potpisivanje poslovnog dokumenta za stolom',
                'url' => route('audit.show'),
            ],
            'accounting' => [
                'title' => 'Računovodstvo',
                'statement' => 'kontrola i jasnoća poslovanja',
                'text' => 'Precizno vođenje knjiga i pravovremeno izvještavanje koje oslobađa menadžment za strateške odluke.',
                'image' => asset('alpha/service-racunovodstvo.jpg'),
                'image_alt' => 'Rad na financijskim podacima na prijenosnom računalu',
                'url' => route('accounting.show'),
            ],
            'advisory' => [
                'title' => 'Savjetovanje',
                'statement' => 'rast, optimizacija i bolji financijski izbor',
                'text' => 'Financijsko i porezno savjetovanje te pribavljanje kapitala - sve na jednom mjestu.',
                'image' => asset('alpha/service-savjetovanje.jpg'),
                'image_alt' => 'Poslovni razgovor tijekom savjetovanja',
                'url' => route('advisory.show'),
            ],
        ]);

        $serviceItems = collect($primaryServicePillars ?? [])
            ->map(function ($service, int $index) use ($cardDesign): array {
                $service = is_array($service) ? $service : [];
                $key = trim((string) ($service['key'] ?? ''));

                if ($key === '') {
                    $key = ['audit', 'accounting', 'advisory'][$index] ?? '';
                }

                $fallback = (array) $cardDesign->get($key, []);

                if ($fallback === []) {
                    return [];
                }

                return array_merge($fallback, [
                    'title' => trim((string) ($service['title'] ?? '')) ?: $fallback['title'],
                    'statement' => trim((string) ($service['subtitle'] ?? '')) ?: $fallback['statement'],
                    'text' => trim((string) ($service['text'] ?? '')) ?: $fallback['text'],
                    'url' => trim((string) ($service['url'] ?? '')) ?: $fallback['url'],
                ]);
            })
            ->filter()
            ->values();

        if ($serviceItems->isEmpty()) {
            $serviceItems = $cardDesign->values();
        }
    @endphp

    <section class="values-section services-index-intro" aria-labelledby="ac-services-index-title">
        <div class="values-inner services-index-intro-layout">
            <div class="values-intro">
                <h1 class="values-title services-index-intro-title" id="ac-services-index-title" data-words-slide-from-right aria-label="{{ trim($titleLead.' '.$titleAccent) }}">
                    @foreach ($introTitleWords as $word)
                        <span class="values-word {{ mb_strtolower(trim($word, '.,!?')) === 'usluge' || ($introTitleAccentWords === [] && $loop->last) ? 'is-accent' : '' }}" style="--value-word-index: {{ $loop->index }}" aria-hidden="true">{{ $word }}</span>
                    @endforeach
                    @foreach ($introTitleAccentWords as $word)
                        <span class="values-word is-accent" style="--value-word-index: {{ count($introTitleWords) + $loop->index }}" aria-hidden="true">{{ $word }}</span>
                    @endforeach
                </h1>

            </div>

            @if ($intro !== '')
                <p class="values-copy services-index-intro-copy content-reveal" data-image-reveal>{!! $introWithServiceLinks !!}</p>
            @endif
        </div>
    </section>

    <section id="ac-services-index" class="services-section services-section--index-page" aria-labelledby="ac-services-index-title">
        <div class="services-shell services-index-cards-shell">

            <div class="services-grid services-grid--count-{{ min(3, $serviceItems->count()) }}">
                @foreach ($serviceItems as $service)
                    <a class="service-card" href="{{ $service['url'] }}" data-image-reveal style="--service-index: {{ $loop->index }}">
                        <div class="service-card-media">
                            <img src="{{ $service['image'] }}" alt="{{ $service['image_alt'] }}" width="1080" height="1350" loading="{{ $loop->index < 3 ? 'eager' : 'lazy' }}" decoding="async">
                        </div>
                        <div class="service-card-copy">
                            <h2 class="service-card-title" data-words-slide-from-right aria-label="{{ $service['title'] }}">
                                <span class="service-title-word" style="--services-word-index: 0" aria-hidden="true">{{ $service['title'] }}</span>
                            </h2>
                            <p class="service-statement">{{ $service['statement'] }}</p>
                            <p class="service-description">{{ $service['text'] }}</p>
                            <span class="service-link" aria-hidden="true">SAZNAJTE VIŠE <i class="fa-duotone fa-thin fa-arrow-right fa-fw"></i></span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
