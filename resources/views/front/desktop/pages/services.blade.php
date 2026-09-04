@extends('front.desktop.layouts.store')

@section('title', $servicePageMetaTitle)
@section('main_class', 'w-full px-0 py-0')

@section('content')
    @php
        $showcase = (array) ($servicesShowcase ?? []);
        $titleLead = trim((string) ($showcase['title_lead'] ?? ''));
        $intro = trim((string) ($showcase['intro'] ?? ''));
        $cardActionLabel = trim((string) ($showcase['card_action_label'] ?? ''));
        $introTitleWords = preg_split('/\s+/u', $titleLead, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $valueCardIcons = [
            'how' => [
                'fa-shield-check',
                'fa-telescope',
                'fa-gauge-max',
                'fa-arrow-up-right-dots',
                'fa-route-interstate',
            ],
            'audience' => [
                'fa-key-skeleton',
                'fa-user-crown',
                'fa-person-chalkboard',
                'fa-people-roof',
                'fa-file-chart-pie',
            ],
        ];
        $valueCards = collect((array) ($showcase['value_cards'] ?? []))
            ->map(static function ($card, int $index): array {
                $card = is_array($card) ? $card : [];
                $items = collect((array) ($card['items'] ?? []))
                    ->map(static function ($item): array {
                        $item = is_array($item) ? $item : [];

                        return [
                            'title' => trim((string) ($item['title'] ?? '')),
                            'text' => trim((string) ($item['text'] ?? '')),
                        ];
                    })
                    ->filter(static fn (array $item): bool => $item['title'] !== '' || $item['text'] !== '')
                    ->values();

                return [
                    'key' => trim((string) ($card['key'] ?? '')) ?: ($index === 0 ? 'how' : 'audience'),
                    'title' => trim((string) ($card['title'] ?? '')),
                    'items' => $items,
                ];
            })
            ->filter(static fn (array $card): bool => $card['title'] !== '' && $card['items']->isNotEmpty())
            ->values();

        $cardDesign = collect([
            'audit' => [
                'key' => 'audit',
                'image' => asset('alpha/service-revizija.jpg'),
            ],
            'accounting' => [
                'key' => 'accounting',
                'image' => asset('alpha/service-racunovodstvo.jpg'),
            ],
            'advisory' => [
                'key' => 'advisory',
                'image' => asset('alpha/service-savjetovanje.jpg'),
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
                    'title' => trim((string) ($service['title'] ?? '')),
                    'statement' => trim((string) ($service['subtitle'] ?? '')),
                    'text' => trim((string) ($service['text'] ?? '')),
                    'image' => trim((string) ($service['image_url'] ?? '')) ?: $fallback['image'],
                    'image_alt' => trim((string) ($service['image_alt'] ?? '')),
                    'url' => trim((string) ($service['url'] ?? '')),
                ]);
            })
            ->filter(fn (array $service): bool => $service !== [] && $service['title'] !== '' && $service['url'] !== '')
            ->values();

        $introWithServiceLinks = e($intro);
        foreach ($serviceItems as $service) {
            $serviceTitle = trim((string) ($service['title'] ?? ''));
            $serviceUrl = trim((string) ($service['url'] ?? ''));
            $stemLength = max(4, mb_strlen($serviceTitle) - 1);
            $serviceStem = mb_substr($serviceTitle, 0, $stemLength);

            if (mb_strlen($serviceStem) < 4 || $serviceUrl === '') {
                continue;
            }

            $introWithServiceLinks = preg_replace_callback(
                '/\b(\p{L}*'.preg_quote(e($serviceStem), '/').'\p{L}*)\b/ui',
                static fn (array $match): string => '<a class="services-index-inline-link" href="'.e($serviceUrl).'">'.$match[0].'</a>',
                $introWithServiceLinks,
                1,
            ) ?? $introWithServiceLinks;
        }
    @endphp

    @push('styles')
        <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/services.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/services.css')) }}">
    @endpush

    <section class="values-section services-index-intro" aria-labelledby="ac-services-index-title">
        <div class="values-inner services-index-intro-layout">
            <div class="values-intro">
                <h1 class="values-title services-index-intro-title" id="ac-services-index-title" data-words-slide-from-right aria-label="{{ $titleLead }}">
                    @foreach ($introTitleWords as $word)
                        <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                    @endforeach
                </h1>

            </div>

            @if ($intro !== '')
                <p class="values-copy services-index-intro-copy content-reveal" data-image-reveal>{!! $introWithServiceLinks !!}</p>
            @endif
        </div>
    </section>

    @if ($valueCards->isNotEmpty())
        <section class="services-value-section" aria-label="{{ $titleLead }}">
            <div class="services-value-shell">
                <div class="services-value-grid">
                    @foreach ($valueCards as $card)
                        @php($icons = $valueCardIcons[$card['key']] ?? $valueCardIcons['how'])
                        <article class="services-value-card content-reveal animation-index-{{ $loop->index }}" data-image-reveal>
                            <h2>{{ $card['title'] }}</h2>

                            <ul class="services-value-list">
                                @foreach ($card['items'] as $item)
                                    <li>
                                        <span class="services-value-icon" aria-hidden="true">
                                            <i class="fa-duotone fa-thin fa-fw {{ $icons[$loop->index] ?? 'fa-circle-check' }}"></i>
                                        </span>
                                        <div>
                                            @if ($item['title'] !== '')
                                                <h3>{{ $item['title'] }}</h3>
                                            @endif
                                            @if ($item['text'] !== '')
                                                <p>{{ $item['text'] }}</p>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="ac-services-index" class="services-section services-section--index-page" aria-labelledby="ac-services-index-title">
        <div class="services-shell services-index-cards-shell">

            <div class="services-grid services-grid--count-{{ min(3, $serviceItems->count()) }}">
                @foreach ($serviceItems as $service)
                    <a class="service-card" href="{{ $service['url'] }}" data-service-key="{{ $service['key'] }}" data-image-reveal>
                        <div class="service-card-media">
                            <img src="{{ $service['image'] }}" alt="{{ $service['image_alt'] }}" width="1080" height="1350" loading="{{ $loop->index < 3 ? 'eager' : 'lazy' }}" decoding="async">
                        </div>
                        <div class="service-card-copy">
                            <h2 class="service-card-title" data-words-slide-from-right aria-label="{{ $service['title'] }}">
                                <span class="service-title-word animation-index-0" aria-hidden="true">{{ $service['title'] }}</span>
                            </h2>
                            <p class="service-statement">{{ $service['statement'] }}</p>
                            <p class="service-description">{{ $service['text'] }}</p>
                            @if ($cardActionLabel !== '')
                                <span class="service-link" aria-hidden="true">{{ $cardActionLabel }} <i class="fa-duotone fa-thin fa-arrow-right fa-fw"></i></span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
