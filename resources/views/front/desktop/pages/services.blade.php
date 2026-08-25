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
