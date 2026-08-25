@extends('front.desktop.layouts.store')

@php
    $locale = strtolower(trim((string) ($locale ?? app()->getLocale())));
    $requiresExactTranslation = $locale === 'en' || (bool) ($frontRequiresExactTranslation ?? false);
    $exactBlockItem = static function ($items, string $type) use ($locale): ?array {
        $item = collect($items ?? [])->first(static function ($candidate) use ($locale, $type): bool {
            $block = is_array($candidate) ? ($candidate['block'] ?? null) : null;
            $translation = is_array($candidate) ? ($candidate['translation'] ?? null) : null;

            return (string) ($block?->type ?? '') === $type
                && strtolower(trim((string) ($translation?->locale ?? ''))) === $locale;
        });

        return is_array($item) ? $item : null;
    };
    $translationPayload = static function (?array $item): array {
        $payload = $item['translation']?->payload ?? null;

        return is_array($payload) ? $payload : [];
    };
    $headingLines = static function (string $heading): array {
        $words = preg_split('/\s+/u', trim($heading), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return collect($words)
            ->chunk(max(1, (int) ceil(count($words) / 2)))
            ->map(static fn ($line): array => $line->values()->all())
            ->values()
            ->all();
    };
    $balancedHeadingLines = static function (string $heading): array {
        $words = preg_split('/\s+/u', trim($heading), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if (count($words) < 2) {
            return $words === [] ? [] : [$words];
        }

        $bestSplit = 1;
        $bestDifference = PHP_INT_MAX;
        for ($split = 1; $split < count($words); $split++) {
            $firstLength = mb_strlen(implode(' ', array_slice($words, 0, $split)));
            $secondLength = mb_strlen(implode(' ', array_slice($words, $split)));
            $difference = abs($firstLength - $secondLength);
            if ($difference < $bestDifference) {
                $bestDifference = $difference;
                $bestSplit = $split;
            }
        }

        return [array_slice($words, 0, $bestSplit), array_slice($words, $bestSplit)];
    };

    $homeHeroItem = $exactBlockItem($homeHeroBlocks ?? [], 'home_hero');
    $homeHeroTranslation = $homeHeroItem['translation'] ?? null;
    $homeHeroPayload = $translationPayload($homeHeroItem);
    $heroTitle = trim((string) ($homeHeroTranslation?->title ?? ''));
    $heroSubtitle = trim((string) ($homeHeroTranslation?->subtitle ?? ''));
    $heroPrimaryLabel = trim((string) ($homeHeroTranslation?->cta_label ?? ''));
    $heroPrimaryUrl = trim((string) ($homeHeroTranslation?->cta_url ?? ''));
    $heroSecondaryLabel = trim((string) ($homeHeroPayload['secondary_cta_label'] ?? ''));
    $heroSecondaryUrl = trim((string) ($homeHeroPayload['secondary_cta_url'] ?? ''));
    $homePageTitle = trim((string) ($homeHeroPayload['page_title'] ?? '')) ?: $heroTitle;
    $heroTitleLines = $balancedHeadingLines($heroTitle);
    $heroTitleLengthClass = mb_strlen($heroTitle) > 36 ? 'hero-title--long' : '';

    // Locale-neutral settings are intentionally limited to presentation and media.
    $heroSettings = (array) ($storeSettings['home_hero'] ?? []);
    $heroTypography = (array) ($heroSettings['typography'] ?? []);
    $heroFontKey = (string) ($heroTypography['key'] ?? \App\Support\Front\HeroFontRegistry::DEFAULT);
    $heroFontWeight = (int) ($heroSettings['font_weight'] ?? \App\Support\Front\HeroFontRegistry::DEFAULT_WEIGHT);
    $heroDesktopVideoUrl = trim((string) ($heroSettings['desktop_video_url'] ?? '')) ?: asset('alpha/alpha-zagreb-loop-hq.mp4');
    $heroMobileVideoUrl = trim((string) ($heroSettings['mobile_video_url'] ?? '')) ?: asset('alpha/alpha-zagreb-loop-mobile.mp4');
    $heroVideoType = static fn (string $url): string => str_ends_with(strtolower((string) parse_url($url, PHP_URL_PATH)), '.webm')
        ? 'video/webm'
        : 'video/mp4';

    $homeServicesItem = $exactBlockItem($homeServicesBlocks ?? [], 'home_services');
    $homeServicesTranslation = $homeServicesItem['translation'] ?? null;
    $homeServicesPayload = $translationPayload($homeServicesItem);
    $servicesHeadingBase = trim((string) ($homeServicesTranslation?->title ?? ''));
    $servicesHeadingAccent = trim((string) ($homeServicesPayload['title_accent'] ?? ''));
    $servicesHeading = $servicesHeadingBase;
    if ($servicesHeadingAccent !== '' && !preg_match('/[.!?]$/u', $servicesHeadingBase)) {
        $servicesHeading = trim($servicesHeadingBase.' '.$servicesHeadingAccent);
    }
    $servicesIntro = trim((string) ($homeServicesTranslation?->subtitle ?? ''));
    $servicesHeadingLines = $headingLines($servicesHeading);

    $valuesContent = is_array($homeServicesPayload['values'] ?? null) ? $homeServicesPayload['values'] : [];
    $valuesTitle = trim((string) ($valuesContent['title'] ?? ''));
    $valuesIntro = trim((string) ($valuesContent['intro'] ?? ''));
    $homePageDescription = $valuesIntro !== '' ? $valuesIntro : $heroSubtitle;
    $valueIcons = ['fa-badge-check', 'fa-scale-balanced', 'fa-chart-line'];
    $valueItems = collect((array) ($valuesContent['items'] ?? []))
        ->filter(static fn ($item): bool => is_array($item) && trim((string) ($item['title'] ?? '')) !== '')
        ->map(static function (array $item, int $index) use ($valueIcons): array {
            return [
                'icon' => $valueIcons[$index] ?? 'fa-badge-check',
                'title' => trim((string) ($item['title'] ?? '')),
                'text' => trim((string) ($item['text'] ?? '')),
            ];
        })
        ->values();
    $valuesTitleWords = preg_split('/\s+/u', $valuesTitle, -1, PREG_SPLIT_NO_EMPTY) ?: [];

    $serviceVisuals = collect([
        'audit' => [
            'image' => asset('alpha/service-revizija.jpg'),
            'image_srcset' => asset('alpha/service-revizija-480.webp').' 480w, '.asset('alpha/service-revizija-768.webp').' 768w, '.asset('alpha/service-revizija-1080.webp').' 1080w',
            'url' => \App\Support\Localization\FrontendRoute::url('audit.show', locale: $locale),
        ],
        'accounting' => [
            'image' => asset('alpha/service-racunovodstvo.jpg'),
            'image_srcset' => asset('alpha/service-racunovodstvo-480.webp').' 480w, '.asset('alpha/service-racunovodstvo-768.webp').' 768w, '.asset('alpha/service-racunovodstvo-1080.webp').' 1080w',
            'url' => \App\Support\Localization\FrontendRoute::url('accounting.show', locale: $locale),
        ],
        'advisory' => [
            'image' => asset('alpha/service-savjetovanje.jpg'),
            'image_srcset' => asset('alpha/service-savjetovanje-480.webp').' 480w, '.asset('alpha/service-savjetovanje-768.webp').' 768w, '.asset('alpha/service-savjetovanje-1080.webp').' 1080w',
            'url' => \App\Support\Localization\FrontendRoute::url('advisory.show', locale: $locale),
        ],
    ]);
    $primaryServiceImages = collect($primaryServicePillars ?? [])
        ->mapWithKeys(static fn (array $service): array => [
            (string) ($service['key'] ?? '') => trim((string) ($service['image_url'] ?? '')),
        ]);
    $serviceItems = collect((array) ($homeServicesPayload['services'] ?? []))
        ->filter(static fn ($service): bool => is_array($service))
        ->map(function (array $service, int $index) use ($serviceVisuals, $primaryServiceImages): array {
            $key = trim((string) ($service['key'] ?? '')) ?: (['audit', 'accounting', 'advisory'][$index] ?? '');
            $visual = (array) $serviceVisuals->get($key, []);
            if ($visual === []) {
                return [];
            }

            $dynamicImage = trim((string) $primaryServiceImages->get($key, ''));

            return [
                'key' => $key,
                'title' => trim((string) ($service['title'] ?? '')),
                'statement' => trim((string) ($service['subtitle'] ?? '')),
                'text' => trim((string) ($service['text'] ?? '')),
                'image_alt' => trim((string) ($service['image_alt'] ?? '')),
                'action_label' => trim((string) ($service['action_label'] ?? '')),
                'image' => $dynamicImage !== '' ? $dynamicImage : (string) ($visual['image'] ?? ''),
                'image_srcset' => $dynamicImage !== '' ? '' : (string) ($visual['image_srcset'] ?? ''),
                'url' => trim((string) ($service['url'] ?? '')) ?: (string) ($visual['url'] ?? ''),
            ];
        })
        ->filter(static fn (array $service): bool => ($service['title'] ?? '') !== '' && ($service['url'] ?? '') !== '')
        ->values();

    $processContent = is_array($homeServicesPayload['process'] ?? null) ? $homeServicesPayload['process'] : [];
    $processHeading = trim((string) ($processContent['title'] ?? ''));
    $processHeadingWords = preg_split('/\s+/u', $processHeading, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $processIcons = ['fa-magnifying-glass-chart', 'fa-chart-line', 'fa-clipboard-check', 'fa-bullseye'];
    $processItems = collect((array) ($processContent['items'] ?? []))
        ->filter(static fn ($item): bool => is_array($item) && trim((string) ($item['title'] ?? '')) !== '')
        ->map(static function (array $item, int $index) use ($processIcons): array {
            return [
                'icon' => $processIcons[$index] ?? 'fa-bullseye',
                'title' => trim((string) ($item['title'] ?? '')),
                'text' => trim((string) ($item['text'] ?? '')),
            ];
        })
        ->values();

    $homeStatsItem = $exactBlockItem($homeStatsBlocks ?? [], 'home_stats');
    $homeStatsPayload = $translationPayload($homeStatsItem);
    $locationsContent = is_array($homeStatsPayload['locations'] ?? null) ? $homeStatsPayload['locations'] : [];
    $locationsContent['items'] = collect((array) ($locationsContent['items'] ?? []))
        ->filter(static fn ($item): bool => is_array($item)
            && trim((string) ($item['entity_key'] ?? '')) !== ''
            && trim((string) ($item['address'] ?? '')) !== '')
        ->values()
        ->all();
    $locationStats = collect((array) ($homeStatsPayload['stats'] ?? []))
        ->filter(static fn ($stat): bool => is_array($stat))
        ->map(static function (array $stat): array {
            $rawValue = trim((string) ($stat['value'] ?? ''));

            return [
                'value' => (int) (preg_replace('/\D+/', '', $rawValue) ?: 0),
                'suffix' => trim((string) ($stat['suffix'] ?? '')),
                'label' => trim((string) ($stat['label'] ?? '')),
            ];
        })
        ->filter(static fn (array $stat): bool => $stat['value'] > 0 && $stat['label'] !== '')
        ->values();
    $locationStatIcons = ['fa-chart-column', 'fa-users', 'fa-user-tie', 'fa-shield-heart'];
    $heroLocationItems = collect((array) ($locationsContent['items'] ?? []));
    $heroLocationsAriaLabel = trim((string) ($locationsContent['hero_aria_label'] ?? ''));
    $locationsStatsAriaLabel = trim((string) ($locationsContent['stats_aria_label'] ?? ''));

    $newsContent = is_array($homeServicesPayload['news'] ?? null) ? $homeServicesPayload['news'] : [];
    $newsHeading = trim((string) ($newsContent['title'] ?? ''));
    $newsHeadingWords = preg_split('/\s+/u', $newsHeading, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $newsAllPostsLabel = trim((string) ($newsContent['all_posts_label'] ?? ''));
    $newsAllPostsUrl = trim((string) ($newsContent['all_posts_url'] ?? ''));
    $newsPostActionLabel = trim((string) ($newsContent['post_action_label'] ?? ''));
    $newsCategoryFallback = trim((string) ($newsContent['category_fallback'] ?? ''));
    $newsExcerptFallback = trim((string) ($newsContent['excerpt_fallback'] ?? ''));
    $newsItems = collect($latestBlogPosts ?? [])
        ->map(function ($post) use ($locale, $requiresExactTranslation, $newsCategoryFallback, $newsExcerptFallback): array {
            $translation = $post->translations->firstWhere('locale', $locale);
            if ($translation === null) {
                return [];
            }

            $title = trim((string) ($translation->title ?? ''));
            $slug = trim((string) ($translation->slug ?? ''));
            if ($title === '' || $slug === '') {
                return [];
            }

            $category = $post->categories
                ->sortByDesc(static fn ($item): int => (int) ($item->pivot->is_primary ?? false))
                ->first();
            $categoryTranslation = $category?->translations->firstWhere('locale', $locale);
            $categoryName = trim((string) ($categoryTranslation?->name ?? ''));
            $excerpt = trim(strip_tags((string) ($translation->excerpt ?? '')));

            return [
                'category' => $categoryName !== '' || $requiresExactTranslation ? $categoryName : $newsCategoryFallback,
                'title' => $title,
                'text' => Illuminate\Support\Str::limit(
                    $excerpt !== '' || $requiresExactTranslation ? $excerpt : $newsExcerptFallback,
                    210
                ),
                'url' => route('blog.show', ['slug' => $slug]),
            ];
        })
        ->filter()
        ->take(3)
        ->values();

    $contactCta = is_array($homeServicesPayload['contact_cta'] ?? null) ? $homeServicesPayload['contact_cta'] : [];
    $contactCtaTitle = trim((string) ($contactCta['title'] ?? ''));
    $contactCtaTitleWords = preg_split('/\s+/u', $contactCtaTitle, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $contactCtaCardTitle = trim((string) ($contactCta['card_title'] ?? ''));
    $contactCtaText = trim((string) ($contactCta['text'] ?? ''));
    $contactCtaButtonLabel = trim((string) ($contactCta['button_label'] ?? ''));
    $contactCtaButtonUrl = trim((string) ($contactCta['button_url'] ?? ''));
    $contactCtaStatus = trim((string) ($contactCta['status'] ?? ''));
@endphp

@section('title', $homePageTitle)
@section('description', $homePageDescription)
@section('main_class', 'hero-page')

@section('content')
    <section class="hero" id="vrh" @if ($heroTitle !== '') aria-labelledby="hero-title" @endif data-front-font="{{ $heroFontKey }}" data-front-font-weight="{{ $heroFontWeight }}">
        <picture class="hero-poster" aria-hidden="true">
            <source
                type="image/webp"
                srcset="{{ asset('alpha/alpha-zagreb-poster-640.webp') }} 640w, {{ asset('alpha/alpha-zagreb-poster-1280.webp') }} 1280w"
                sizes="100vw"
            >
            <img src="{{ asset('alpha/alpha-zagreb-poster.jpg') }}" alt="" width="1280" height="720" fetchpriority="high" decoding="async">
        </picture>
        <video
            class="hero-video"
            muted
            loop
            playsinline
            preload="none"
            aria-hidden="true"
            data-alpha-hero-video
            data-alpha-hero-video-mobile-src="{{ $heroMobileVideoUrl }}"
            data-alpha-hero-video-mobile-type="{{ $heroVideoType($heroMobileVideoUrl) }}"
            data-alpha-hero-video-desktop-src="{{ $heroDesktopVideoUrl }}"
            data-alpha-hero-video-desktop-type="{{ $heroVideoType($heroDesktopVideoUrl) }}"
        ></video>
        <div class="hero-overlay" aria-hidden="true"></div>

        <div class="hero-content">
            @if ($heroTitle !== '')
                <h1 class="{{ $heroTitleLengthClass }}" id="hero-title" aria-label="{{ $heroTitle }}">
                    @php $heroWordIndex = 0; @endphp
                    @foreach ($heroTitleLines as $line)
                        <span class="hero-line">
                            @foreach ($line as $word)
                                <span class="hero-word animation-index-{{ min($heroWordIndex++, 12) }} {{ $loop->parent->last && $loop->last ? 'is-accent' : '' }}" aria-hidden="true">
                                    @foreach (mb_str_split(Illuminate\Support\Str::upper($word)) as $character)<span class="hero-char">{{ $character }}</span>@endforeach
                                </span>
                            @endforeach
                        </span>
                    @endforeach
                </h1>
            @endif
            @if ($heroSubtitle !== '')
                <p>{{ $heroSubtitle }}</p>
            @endif
            @if (($heroPrimaryLabel !== '' && $heroPrimaryUrl !== '') || ($heroSecondaryLabel !== '' && $heroSecondaryUrl !== ''))
                <div class="hero-actions">
                    @if ($heroPrimaryLabel !== '' && $heroPrimaryUrl !== '')
                        <a class="button button-gold" href="{{ $heroPrimaryUrl }}"><span>{{ $heroPrimaryLabel }}</span></a>
                    @endif
                    @if ($heroSecondaryLabel !== '' && $heroSecondaryUrl !== '')
                        <a class="button button-outline" href="{{ $heroSecondaryUrl }}"><span>{{ $heroSecondaryLabel }}</span></a>
                    @endif
                </div>
            @endif
            <div class="scroll-cue" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span></div>
        </div>

        @if ($heroLocationItems->isNotEmpty() && $heroLocationsAriaLabel !== '')
            <aside class="hero-locations" aria-label="{{ $heroLocationsAriaLabel }}">
                <span class="location-number" aria-hidden="true"><i class="fa-duotone fa-thin fa-bullseye-pointer"></i></span>
                <div>
                    @foreach ($heroLocationItems as $location)
                        @if (trim((string) ($location['short_city'] ?? '')) !== '')
                            <p>{{ Illuminate\Support\Str::upper((string) $location['short_city']) }}</p>
                        @endif
                    @endforeach
                </div>
            </aside>
        @endif
    </section>

    @if ($valuesTitle !== '' && ($valuesIntro !== '' || $valueItems->isNotEmpty()))
        <section class="values-section" id="vrijednosti" aria-labelledby="values-title">
            <div class="values-inner">
                <div class="values-intro">
                    <h2 class="values-title" id="values-title" data-words-slide-from-right aria-label="{{ $valuesTitle }}">
                        @foreach ($valuesTitleWords as $word)
                            <span class="values-word animation-index-{{ min($loop->index, 12) }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>
                    @if ($valuesIntro !== '')
                        <p class="values-copy content-reveal" data-image-reveal>{{ $valuesIntro }}</p>
                    @endif
                </div>

                @if ($valueItems->isNotEmpty())
                    <div class="values-list">
                        @foreach ($valueItems as $item)
                            <article class="value-item content-reveal animation-index-{{ min($loop->index, 12) }}" data-image-reveal>
                                <div class="value-icon" aria-hidden="true"><i class="fa-duotone fa-thin fa-fw {{ $item['icon'] }}"></i></div>
                                <div class="value-content">
                                    <h3 data-words-slide-from-right aria-label="{{ $item['title'] }}">
                                        @foreach (preg_split('/\s+/u', $item['title'], -1, PREG_SPLIT_NO_EMPTY) ?: [] as $word)
                                            <span class="value-title-word animation-index-{{ min($loop->index, 12) }}" aria-hidden="true">{{ $word }}</span>
                                        @endforeach
                                    </h3>
                                    @if ($item['text'] !== '')<p>{{ $item['text'] }}</p>@endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if ($servicesHeading !== '' && $serviceItems->isNotEmpty())
        <section class="services-section" id="usluge" aria-labelledby="services-title">
            <div class="services-shell">
                <header class="services-header">
                    <h2 class="services-title" id="services-title" data-words-slide-from-right aria-label="{{ $servicesHeading }}">
                        @php $servicesWordIndex = 0; @endphp
                        @foreach ($servicesHeadingLines as $line)
                            <span class="services-title-line" aria-hidden="true">
                                @foreach ($line as $word)
                                    <span class="services-word animation-index-{{ min($servicesWordIndex++, 12) }} {{ $loop->parent->last && $loop->last ? 'is-accent' : '' }}">{{ $word }}</span>
                                @endforeach
                            </span>
                        @endforeach
                    </h2>
                    @if ($servicesIntro !== '')
                        <p class="services-intro content-reveal" data-image-reveal>{{ $servicesIntro }}</p>
                    @endif
                </header>

                <div class="services-grid services-grid--count-{{ min(3, $serviceItems->count()) }}">
                    @foreach ($serviceItems as $service)
                        <a class="service-card" href="{{ $service['url'] }}" data-service-key="{{ $service['key'] }}" data-image-reveal>
                            <div class="service-card-media">
                                @if ($service['image_srcset'] !== '')
                                    <picture>
                                        <source type="image/webp" srcset="{{ $service['image_srcset'] }}" sizes="(max-width: 700px) calc(100vw - 48px), (max-width: 1100px) calc(50vw - 48px), 470px">
                                        <img src="{{ $service['image'] }}" alt="{{ $service['image_alt'] }}" width="1080" height="1350" loading="lazy" decoding="async">
                                    </picture>
                                @else
                                    <img src="{{ $service['image'] }}" alt="{{ $service['image_alt'] }}" width="1080" height="1350" loading="lazy" decoding="async">
                                @endif
                            </div>
                            <div class="service-card-copy">
                                <h3 class="service-card-title" data-words-slide-from-right aria-label="{{ $service['title'] }}">
                                    <span class="service-title-word animation-index-0" aria-hidden="true">{{ $service['title'] }}</span>
                                </h3>
                                @if ($service['statement'] !== '')<p class="service-statement">{{ $service['statement'] }}</p>@endif
                                @if ($service['text'] !== '')<p class="service-description">{{ $service['text'] }}</p>@endif
                                @if ($service['action_label'] !== '')
                                    <span class="service-link" aria-hidden="true">{{ $service['action_label'] }} <i class="fa-duotone fa-thin fa-arrow-right fa-fw"></i></span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($processHeading !== '' && $processItems->isNotEmpty())
        <section class="process-section" id="proces" aria-labelledby="process-title" data-process-reveal>
            <div class="process-shell">
                <header class="process-header">
                    <h2 class="process-title" id="process-title" data-words-slide-from-right aria-label="{{ $processHeading }}">
                        @foreach ($processHeadingWords as $word)
                            <span class="process-title-word animation-index-{{ min($loop->index, 12) }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>
                </header>
                <div class="process-track">
                    @foreach ($processItems as $item)
                        <article class="process-item animation-index-{{ min($loop->index, 12) }}">
                            <div class="process-marker" aria-hidden="true"><span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></div>
                            <i class="process-icon fa-duotone fa-thin fa-fw {{ $item['icon'] }}" aria-hidden="true"></i>
                            <div class="process-copy"><h3>{{ $item['title'] }}</h3>@if ($item['text'] !== '')<p>{{ $item['text'] }}</p>@endif</div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (trim((string) ($locationsContent['title'] ?? '')) !== '' && !empty($locationsContent['items']))
        @include('front.desktop.partials.locations-showcase', [
            'locationsSectionId' => 'lokacije',
            'locationsTitleId' => 'locations-title',
            'locationDetailsPrefix' => 'location-details',
            'locationsContent' => $locationsContent,
            'locationStats' => $locationStats,
            'statIcons' => $locationStatIcons,
            'showLocationStats' => true,
        ])
    @elseif ($locationStats->isNotEmpty())
        <section class="locations-section" id="lokacije" @if ($locationsStatsAriaLabel !== '') aria-label="{{ $locationsStatsAriaLabel }}" @endif data-locations-reveal>
            <div class="locations-shell">
                <div class="locations-stats">
                    @foreach ($locationStats as $stat)
                        <article class="location-stat animation-index-{{ min($loop->index, 12) }}">
                            <div class="location-stat-icon" aria-hidden="true"><i class="fa-duotone fa-thin fa-fw {{ $locationStatIcons[$loop->index] ?? $locationStatIcons[0] }}"></i></div>
                            <div><strong><span data-count-target="{{ $stat['value'] }}">0</span><span class="location-stat-suffix">{{ $stat['suffix'] }}</span></strong><p>{{ $stat['label'] }}</p></div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($newsItems->isNotEmpty())
        <section class="news-section ac-home-news" id="novosti" @if ($newsHeading !== '') aria-labelledby="news-title" @endif>
            <div class="news-shell">
                @if ($newsHeading !== '' || ($newsAllPostsLabel !== '' && $newsAllPostsUrl !== ''))
                    <header class="news-header">
                        @if ($newsHeading !== '')
                            <h2 class="news-title" id="news-title" data-words-slide-from-right aria-label="{{ $newsHeading }}">
                                @foreach ($newsHeadingWords as $word)
                                    <span class="news-title-word animation-index-{{ min($loop->index, 12) }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                                @endforeach
                            </h2>
                        @endif
                        @if ($newsAllPostsLabel !== '' && $newsAllPostsUrl !== '')
                            <a class="news-all-link content-reveal" data-image-reveal href="{{ $newsAllPostsUrl }}"><span>{{ $newsAllPostsLabel }}</span><i class="fa-duotone fa-thin fa-arrow-right fa-fw" aria-hidden="true"></i></a>
                        @endif
                    </header>
                @endif
                <div class="news-grid">
                    @foreach ($newsItems as $item)
                        <a class="news-card animation-index-{{ min($loop->index, 12) }}" data-image-reveal href="{{ $item['url'] }}">
                            @if ($item['category'] !== '')<span class="news-card-category">{{ $item['category'] }}</span>@endif
                            <h3>{{ $item['title'] }}</h3>
                            @if ($item['text'] !== '')<p>{{ $item['text'] }}</p>@endif
                            @if ($newsPostActionLabel !== '')
                                <span class="news-card-link" aria-hidden="true">{{ $newsPostActionLabel }} <i class="fa-duotone fa-thin fa-arrow-right fa-fw"></i></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($contactCtaTitle !== '' && ($contactCtaCardTitle !== '' || $contactCtaText !== ''))
        <section class="contact-cta" id="kontakt-cta" aria-labelledby="contact-cta-title">
            <div class="contact-cta-shell">
                <div class="contact-cta-copy">
                    <h2 class="contact-cta-title" id="contact-cta-title" data-words-slide-from-right aria-label="{{ $contactCtaTitle }}">
                        @foreach ($contactCtaTitleWords as $word)
                            <span class="contact-cta-title-word animation-index-{{ min($loop->index, 12) }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>
                </div>
                <div class="contact-cta-card" data-image-reveal>
                    @if ($contactCtaCardTitle !== '')<div class="contact-cta-card-heading"><span>{{ $contactCtaCardTitle }}</span></div>@endif
                    @if ($contactCtaText !== '')<p>{{ $contactCtaText }}</p>@endif
                    @if ($contactCtaButtonLabel !== '' && $contactCtaButtonUrl !== '')
                        <a class="contact-cta-button" href="{{ $contactCtaButtonUrl }}"><span>{{ $contactCtaButtonLabel }}</span><i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i></a>
                    @endif
                    @if ($contactCtaStatus !== '')<small><span class="contact-cta-status-dot" aria-hidden="true"></span>{{ $contactCtaStatus }}</small>@endif
                </div>
            </div>
        </section>
    @endif
@endsection
