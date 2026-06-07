@extends('front.desktop.layouts.store')

@php
    $overviewBody = array_values((array) ($overviewSection['body'] ?? []));
    $serviceItems = array_values((array) ($processSection['items'] ?? []));
    $approachBody = array_values((array) ($approachSection['body'] ?? []));
    $sourceCards = array_values((array) ($sourceModulesSection['items'] ?? []));
    $callGroups = array_values((array) ($callsSection['groups'] ?? []));
    $resourceCards = array_values((array) ($resourcesSection['cards'] ?? []));
    $lawCards = array_values((array) ($lawsSection['cards'] ?? []));
    $meetingTitle = trim((string) ($meetingSection['title'] ?? '')) ?: 'Razgovarajmo o vašem projektu';
    $meetingIntro = trim((string) ($meetingSection['intro'] ?? '')) ?: 'Javite nam se i zajedno ćemo procijeniti koji su izvori financiranja dostupni za vaš projekt.';
    $meetingLinkLabel = trim((string) ($meetingSection['contact_title'] ?? '')) ?: 'Kontaktirajte nas';
    $isCroatianLocale = str_starts_with(strtolower((string) ($locale ?? app()->getLocale())), 'hr');
    $readMoreLabel = $isCroatianLocale ? 'Opširnije' : 'Read more';
    $currentHost = request()->getHost();
    $sameOriginAssetUrl = static function (?string $url) use ($currentHost): string {
        $assetUrl = trim((string) $url);
        $assetHost = parse_url($assetUrl, PHP_URL_HOST);

        if ($assetUrl === '' || ($assetHost !== null && $assetHost !== $currentHost)) {
            return $assetUrl;
        }

        $assetPath = parse_url($assetUrl, PHP_URL_PATH);
        $assetQuery = parse_url($assetUrl, PHP_URL_QUERY);

        if (is_string($assetPath) && $assetPath !== '') {
            return $assetPath.($assetQuery ? '?'.$assetQuery : '');
        }

        return $assetUrl;
    };
    $resolveContentUrl = static function (?string $url): string {
        $target = trim((string) $url);

        if ($target === '' || str_starts_with($target, '#') || str_starts_with($target, 'http://') || str_starts_with($target, 'https://')) {
            return $target;
        }

        return url(str_starts_with($target, '/') ? $target : '/'.$target);
    };
    $heroImageUrl = $sameOriginAssetUrl((string) $heroBackgroundUrl);
    $hasEuFundsPosts = ($euFundsPosts ?? collect())->isNotEmpty();
    $hasServiceVideos = collect($serviceVideos ?? [])->isNotEmpty();
@endphp

@section('title', $servicePageMetaTitle !== '' ? $servicePageMetaTitle : ($servicePageTitle ?? 'EU fondovi'))
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-family-business-page ac-audit-page ac-eu-service-page {{ $lawCards !== [] ? 'ac-service-band-even' : '' }}">
        <section class="ac-family-hero ac-service-hero ac-service-hero--eu-funds">
            <div class="ac-family-hero-media" aria-hidden="true" style="--audit-hero-image: url('{{ $heroImageUrl }}'); background-image: url('{{ $heroImageUrl }}');">
                <img src="{{ $heroImageUrl }}" alt="" class="ac-family-hero-media-image" loading="eager" decoding="async">
            </div>
            <div class="ac-family-hero-overlay"></div>

            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-family-hero-content">
                    <div class="ac-family-hero-shell">
                        <div class="ac-family-hero-copy ac-service-hero-card">
                            <h1 class="ac-family-hero-title">
                                <span class="is-brand">{{ $heroSection['brand_title'] ?? 'ALPHA CAPITALIS' }}</span>
                                <span class="is-subtitle">
                                    <span class="is-subtitle-lead">{{ $heroSection['subtitle_lead'] ?? 'EU fondovi' }}</span>
                                    @if (trim((string) ($heroSection['subtitle_accent'] ?? '')) !== '')
                                        <span class="is-subtitle-accent">{{ $heroSection['subtitle_accent'] }}</span>
                                    @endif
                                </span>
                            </h1>

                            <p class="ac-family-hero-intro">{{ $heroSection['intro'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="eu-funds-overview" class="ac-audit-editorial-wrap" aria-labelledby="ac-eu-overview-title">
            <div class="mx-auto w-full max-w-[1120px] px-5 lg:px-8">
                <div class="ac-audit-editorial-shell">
                    <article class="ac-audit-editorial-section ac-audit-editorial-section--overview">
                        <div class="ac-audit-section-head ac-audit-section-head--center">
                            <p class="ac-family-section-kicker">{{ $overviewSection['kicker'] ?? 'EU FONDOVI' }}</p>
                            <h2 id="ac-eu-overview-title">{{ $overviewSection['title'] ?? 'Što su EU fondovi?' }}</h2>
                        </div>

                        <div class="ac-audit-copy ac-audit-copy--full">
                            @foreach ($overviewBody as $paragraph)
                                @if (trim((string) $paragraph) !== '')
                                    <p>{{ $paragraph }}</p>
                                @endif
                            @endforeach
                        </div>
                    </article>

                    <article id="eu-funds-services" class="ac-audit-editorial-section">
                        <div class="ac-audit-section-head ac-audit-section-head--center">
                            <p class="ac-family-section-kicker">{{ $processSection['kicker'] ?? 'USLUGE' }}</p>
                            <h2>{{ $processSection['title'] ?? 'Naše usluge' }}</h2>
                            @if (trim((string) ($processSection['intro'] ?? '')) !== '')
                                <p>{{ $processSection['intro'] }}</p>
                            @endif
                        </div>

                        <div class="ac-audit-card-grid">
                            @foreach ($serviceItems as $item)
                                <article class="ac-audit-service-card">
                                    <h3>{{ $item['title'] ?? '' }}</h3>
                                    <p>{{ $item['text'] ?? '' }}</p>
                                </article>
                            @endforeach
                        </div>
                    </article>

                    <article id="eu-funds-approach" class="ac-audit-editorial-section">
                        <div class="ac-audit-section-head ac-audit-section-head--center">
                            <p class="ac-family-section-kicker">{{ $approachSection['kicker'] ?? 'PRISTUP' }}</p>
                            <h2>{{ $approachSection['title'] ?? 'Naš pristup' }}</h2>
                        </div>

                        <blockquote class="ac-audit-copy ac-audit-copy--full ac-audit-approach-copy">
                            @foreach ($approachBody as $paragraph)
                                @if (trim((string) $paragraph) !== '')
                                    <p>{{ $paragraph }}</p>
                                @endif
                            @endforeach
                        </blockquote>
                    </article>

                    <article id="eu-funds-sources" class="ac-audit-editorial-section">
                        <div class="ac-audit-section-head ac-audit-section-head--center">
                            <p class="ac-family-section-kicker">{{ $sourceModulesSection['kicker'] ?? 'IZVORI FINANCIRANJA' }}</p>
                            <h2>{{ $sourceModulesSection['title'] ?? 'Dostupni izvori financiranja' }}</h2>
                            @if (trim((string) ($sourceModulesSection['intro'] ?? '')) !== '')
                                <p>{{ $sourceModulesSection['intro'] }}</p>
                            @endif
                        </div>

                        <div class="ac-advisory-module-grid">
                            @foreach ($sourceCards as $module)
                                @php $moduleUrl = $resolveContentUrl($module['url'] ?? ''); @endphp
                                <article class="ac-advisory-source-card ac-eu-source-card">
                                    <h3>{{ $module['title'] ?? '' }}</h3>
                                    <p>{{ $module['text'] ?? '' }}</p>
                                    @if ($moduleUrl !== '')
                                        <a href="{{ $moduleUrl }}" class="ac-advisory-card-link">{{ $readMoreLabel }}</a>
                                    @endif
                                </article>
                            @endforeach
                        </div>

                        @if ($callGroups !== [])
                            <div id="eu-funds-calls" class="ac-eu-call-module" aria-labelledby="ac-eu-calls-title">
                                <div class="ac-audit-section-head ac-audit-section-head--center ac-advisory-subhead">
                                    <p class="ac-family-section-kicker">{{ $callsSection['kicker'] ?? 'NATJEČAJI' }}</p>
                                    <h3 id="ac-eu-calls-title">{{ $callsSection['title'] ?? 'Natječaji prema statusu' }}</h3>
                                    @if (trim((string) ($callsSection['intro'] ?? '')) !== '')
                                        <p>{{ $callsSection['intro'] }}</p>
                                    @endif
                                </div>

                                <div class="ac-eu-call-group-grid">
                                    @foreach ($callGroups as $group)
                                        @php
                                            $tone = trim((string) ($group['tone'] ?? 'pending')) ?: 'pending';
                                            $items = array_values((array) ($group['items'] ?? []));
                                            $visibleItems = array_slice($items, 0, 5);
                                            $hiddenItems = array_slice($items, 5);
                                            $statusLabel = trim((string) ($group['status_label'] ?? '')) ?: match ($tone) {
                                                'open' => 'Otvoreno',
                                                'closed' => 'Zatvoreno',
                                                default => 'U najavi',
                                            };
                                        @endphp
                                        <article id="eu-funds-calls-{{ $tone }}" class="ac-eu-call-group-card is-{{ $tone }}">
                                            <div class="ac-eu-call-group-head">
                                                <h3>{{ $group['title'] ?? $statusLabel }}</h3>
                                                <span class="ac-eu-status-badge is-{{ $tone }}">{{ $statusLabel }}</span>
                                            </div>

                                            <ul class="ac-eu-call-list">
                                                @foreach ($visibleItems as $item)
                                                    @include('front.desktop.pages.partials.eu-funds-call-item', ['item' => $item])
                                                @endforeach
                                            </ul>

                                            @if ($hiddenItems !== [])
                                                <details class="ac-eu-call-details">
                                                    <summary>{{ $isCroatianLocale ? 'Pogledaj sve natječaje' : 'View all calls' }}</summary>
                                                    <ul class="ac-eu-call-list ac-eu-call-list--details">
                                                        @foreach ($hiddenItems as $item)
                                                            @include('front.desktop.pages.partials.eu-funds-call-item', ['item' => $item])
                                                        @endforeach
                                                    </ul>
                                                </details>
                                            @endif
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </article>

                    <article id="eu-funds-programs" class="ac-audit-editorial-section">
                        <div class="ac-audit-section-head ac-audit-section-head--center">
                            <p class="ac-family-section-kicker">{{ $resourcesSection['kicker'] ?? 'PROGRAMI I INSTRUMENTI' }}</p>
                            <h2>{{ $resourcesSection['title'] ?? 'HBOR, HAMAG i ostali izvori potpore' }}</h2>
                            @if (trim((string) ($resourcesSection['intro'] ?? '')) !== '')
                                <p>{{ $resourcesSection['intro'] }}</p>
                            @endif
                        </div>

                        <div class="ac-eu-program-grid">
                            @foreach ($resourceCards as $card)
                                <article class="ac-advisory-text-panel ac-eu-program-card">
                                    @if (trim((string) ($card['eyebrow'] ?? '')) !== '')
                                        <p class="ac-family-section-kicker">{{ $card['eyebrow'] }}</p>
                                    @endif
                                    <h2>{{ $card['title'] ?? '' }}</h2>

                                    @foreach ((array) ($card['body'] ?? []) as $paragraph)
                                        @if (trim((string) $paragraph) !== '')
                                            <p>{{ $paragraph }}</p>
                                        @endif
                                    @endforeach

                                    @foreach ((array) ($card['groups'] ?? []) as $group)
                                        <div class="ac-eu-program-list-block">
                                            <h3>{{ $group['label'] ?? '' }}</h3>
                                            <ul class="ac-advisory-list">
                                                @foreach ((array) ($group['items'] ?? []) as $item)
                                                    @php
                                                        $resolvedLink = $item['resolved_link'] ?? ['url' => ''];
                                                        $itemUrl = trim((string) ($resolvedLink['url'] ?? ''));
                                                    @endphp
                                                    <li>
                                                        @if ($itemUrl !== '')
                                                            <a href="{{ $itemUrl }}" @if($resolvedLink['open_in_new_tab'] ?? false) target="_blank" rel="{{ $resolvedLink['rel'] ?? 'noopener noreferrer' }}" @endif>{{ $item['title'] ?? '' }}</a>
                                                        @else
                                                            {{ $item['title'] ?? '' }}
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach

                                    @if (!empty($card['primary_link']['url'] ?? '') || !empty($card['secondary_link']['url'] ?? ''))
                                        <div class="ac-eu-program-actions">
                                            @if (!empty($card['primary_link']['url'] ?? ''))
                                                <a
                                                    href="{{ $card['primary_link']['url'] }}"
                                                    class="ac-advisory-card-link"
                                                    @if($card['primary_link']['open_in_new_tab'] ?? false) target="_blank" rel="{{ $card['primary_link']['rel'] ?? 'noopener noreferrer' }}" @endif
                                                >{{ $card['primary_link']['label'] ?: $readMoreLabel }}</a>
                                            @endif

                                            @if (!empty($card['secondary_link']['url'] ?? ''))
                                                <a
                                                    href="{{ $card['secondary_link']['url'] }}"
                                                    class="ac-advisory-card-link"
                                                    @if($card['secondary_link']['open_in_new_tab'] ?? false) target="_blank" rel="{{ $card['secondary_link']['rel'] ?? 'noopener noreferrer' }}" @endif
                                                >{{ $card['secondary_link']['label'] ?: $readMoreLabel }}</a>
                                            @endif
                                        </div>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </article>

                    @if ($lawCards !== [])
                        <article id="eu-funds-laws" class="ac-audit-editorial-section">
                            <div class="ac-audit-section-head ac-audit-section-head--center">
                                <p class="ac-family-section-kicker">{{ $lawsSection['kicker'] ?? 'ZAKONI I UREDBE' }}</p>
                                <h2>{{ $lawsSection['title'] ?? 'Porezne olakšice, zakoni i uredbe' }}</h2>
                                @if (trim((string) ($lawsSection['intro'] ?? '')) !== '')
                                    <p>{{ $lawsSection['intro'] }}</p>
                                @endif
                            </div>

                            <div class="ac-eu-program-grid">
                                @foreach ($lawCards as $card)
                                    <article class="ac-advisory-text-panel ac-eu-program-card">
                                        <h2>{{ $card['title'] ?? '' }}</h2>
                                        @if (trim((string) ($card['summary'] ?? '')) !== '')
                                            <p>{{ $card['summary'] }}</p>
                                        @endif

                                        @foreach ((array) ($card['lists'] ?? []) as $list)
                                            <div class="ac-eu-program-list-block">
                                                <h3>{{ $list['label'] ?? '' }}</h3>
                                                <ul class="ac-advisory-list">
                                                    @foreach ((array) ($list['items'] ?? []) as $item)
                                                        <li>{{ $item }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endforeach

                                        @if (trim((string) ($card['note'] ?? '')) !== '')
                                            <p class="ac-eu-program-note">{{ $card['note'] }}</p>
                                        @endif

                                        @if (!empty($card['primary_link']['url'] ?? '') || !empty($card['secondary_link']['url'] ?? ''))
                                            <div class="ac-eu-program-actions">
                                                @if (!empty($card['primary_link']['url'] ?? ''))
                                                    <a
                                                        href="{{ $card['primary_link']['url'] }}"
                                                        class="ac-advisory-card-link"
                                                        @if($card['primary_link']['open_in_new_tab'] ?? false) target="_blank" rel="{{ $card['primary_link']['rel'] ?? 'noopener noreferrer' }}" @endif
                                                    >{{ $card['primary_link']['label'] ?: $readMoreLabel }}</a>
                                                @endif

                                                @if (!empty($card['secondary_link']['url'] ?? ''))
                                                    <a
                                                        href="{{ $card['secondary_link']['url'] }}"
                                                        class="ac-advisory-card-link"
                                                        @if($card['secondary_link']['open_in_new_tab'] ?? false) target="_blank" rel="{{ $card['secondary_link']['rel'] ?? 'noopener noreferrer' }}" @endif
                                                    >{{ $card['secondary_link']['label'] ?: $readMoreLabel }}</a>
                                                @endif
                                            </div>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        </article>
                    @endif
                </div>
            </div>
        </section>

        @include('front.desktop.partials.service-videos', [
            'serviceVideoSection' => $serviceVideoSection ?? [],
            'serviceVideos' => $serviceVideos ?? [],
            'locale' => $locale ?? app()->getLocale(),
        ])

        <section id="eu-funds-cta" class="ac-service-cta-section" aria-labelledby="ac-eu-meeting-title">
            <div class="ac-service-cta-container">
                <div class="ac-service-cta-card">
                    <div class="ac-service-cta-copy">
                        <h2 id="ac-eu-meeting-title">{{ $meetingTitle }}</h2>
                        <p>{{ $meetingIntro }}</p>
                    </div>

                    <a href="{{ route('contact.create') }}" class="ac-service-cta-link">
                        <span>{{ $meetingLinkLabel }}</span>
                    </a>
                </div>
            </div>
        </section>

        @if ($hasEuFundsPosts)
            <section class="ac-support-story ac-home-blog ac-blog-related-section ac-family-blog-section ac-audit-blog-section ac-eu-blog-section" aria-labelledby="ac-eu-blog-title">
                <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                    <div class="ac-support-story-hero">
                        <div class="ac-support-story-shell">
                            <div class="ac-services-head ac-support-story-head">
                                <p class="ac-family-section-kicker">{{ $isCroatianLocale ? 'NAJNOVIJE OBJAVE' : 'LATEST POSTS' }}</p>
                                <h2 id="ac-eu-blog-title">
                                    <span>{{ $blogSection['title'] ?? '' }}</span>
                                </h2>
                                <p class="ac-services-intro">{{ $blogSection['intro'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="ac-home-blog-carousel">
                        <div id="ac-eu-blog-splide" class="splide ac-home-blog-splide" data-eu-funds-blog-splide>
                            <div class="splide__track">
                                <ul class="splide__list">
                                    @foreach ($euFundsPosts as $post)
                                        @php
                                            $translation = $post->translations->firstWhere('locale', $locale)
                                                ?? $post->translations->firstWhere('locale', $fallbackLocale);
                                            $postSlug = trim((string) ($translation?->slug ?? ''));
                                            $postUrl = $postSlug !== '' ? route('blog.show', ['slug' => $postSlug]) : route('blog.index');
                                            $postTitle = trim((string) ($translation?->title ?? $post->code));
                                            $postExcerpt = trim((string) ($translation?->excerpt ?? '')) ?: __('ui.blog.excerpt_fallback');
                                            $postExcerpt = \Illuminate\Support\Str::limit($postExcerpt, 180, '...', true);
                                            $postImage = $post->getFirstMedia('blog_cover');
                                            $postImageSource = $postImage
                                                ? ($postImage->hasGeneratedConversion('card_360x240') ? $postImage->getUrl('card_360x240') : $postImage->getUrl())
                                                : '';
                                            $postImageUrl = $sameOriginAssetUrl($postImageSource);
                                            $primaryCategory = $post->categories
                                                ->sortByDesc(fn ($category) => (int) ($category->pivot->is_primary ?? false))
                                                ->first();
                                            $categoryTranslation = $primaryCategory?->translations->firstWhere('locale', $locale)
                                                ?? $primaryCategory?->translations->firstWhere('locale', $fallbackLocale);
                                            $categoryLabel = trim((string) ($categoryTranslation?->name ?? 'Novosti'));
                                            $publishedLabel = ($post->published_at ?? $post->created_at)?->translatedFormat($isCroatianLocale ? 'j. F Y.' : 'F j, Y');
                                        @endphp
                                        <li class="splide__slide ac-home-blog-slide">
                                            <article class="ac-home-blog-card">
                                                <a href="{{ $postUrl }}" class="ac-home-blog-card-link" aria-label="{{ $readMoreLabel }}: {{ $postTitle }}">
                                                    <div class="ac-home-blog-card-media">
                                                        @if ($postImageUrl)
                                                            <img
                                                                src="{{ $postImageUrl }}"
                                                                alt="{{ $postTitle }}"
                                                                class="ac-home-blog-card-image"
                                                                width="360"
                                                                height="240"
                                                                sizes="(min-width: 1180px) 384px, (min-width: 760px) 50vw, 100vw"
                                                                loading="eager"
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
                                                            <span>{{ $readMoreLabel }}</span>
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
                </div>
            </section>
        @endif
    </div>
@endsection

@if ($hasEuFundsPosts || $hasServiceVideos)
    @once
        @push('styles')
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
        @endpush
    @endonce

    @once
        @push('scripts')
            <script defer src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
        @endpush
    @endonce
@endif

@if ($hasEuFundsPosts)
    @push('scripts')
        <script>
            (function () {
                const initEuFundsBlogSlider = function () {
                    if (typeof window.Splide !== 'function') {
                        return false;
                    }

                    document.querySelectorAll('[data-eu-funds-blog-splide]').forEach(function (el) {
                        if (el.dataset.splideReady === '1') {
                            return;
                        }

                        el.dataset.splideReady = '1';

                        const count = el.querySelectorAll('.splide__slide').length;
                        const slider = new window.Splide(el, {
                            type: 'slide',
                            perPage: Math.min(3, Math.max(1, count)),
                            perMove: 1,
                            gap: '1.25rem',
                            drag: count > 1,
                            snap: true,
                            rewind: count > 1,
                            pagination: count > 1,
                            arrows: count > 1,
                            updateOnMove: true,
                            speed: 520,
                            breakpoints: {
                                1180: { perPage: Math.min(2, Math.max(1, count)) },
                                760: { perPage: 1, gap: '1rem' },
                            },
                        });

                        slider.mount();
                    });

                    return true;
                };

                if (initEuFundsBlogSlider()) {
                    return;
                }

                let attempts = 0;
                const timer = window.setInterval(function () {
                    attempts += 1;
                    if (initEuFundsBlogSlider() || attempts > 40) {
                        window.clearInterval(timer);
                    }
                }, 120);
            }());
        </script>
    @endpush
@endif
