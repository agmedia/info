@extends('front.desktop.layouts.store')

@php
    $richTextBlocks = static function (array $section, string $htmlKey = 'body_html', string $legacyKey = 'body'): array {
        $html = array_key_exists($htmlKey, $section)
            ? trim((string) ($section[$htmlKey] ?? ''))
            : \App\Support\Content\StructuredRichText::fromParagraphs((array) ($section[$legacyKey] ?? []));

        return \App\Support\Content\StructuredRichText::blocks($html);
    };
    $overviewBlocks = $richTextBlocks((array) $overviewSection);
    $serviceItems = array_values(array_filter(
        (array) ($processSection['items'] ?? []),
        static fn ($item): bool => is_array($item) && trim((string) ($item['title'] ?? '')) !== '',
    ));
    $approachBlocks = $richTextBlocks((array) $approachSection);
    $sourceCards = array_values(array_filter(
        (array) ($sourceModulesSection['items'] ?? []),
        static fn ($item): bool => is_array($item) && trim((string) ($item['title'] ?? '')) !== '',
    ));
    $callGroups = array_values((array) ($callsSection['groups'] ?? []));
    $resourceCards = array_values((array) ($resourcesSection['cards'] ?? []));
    $lawCards = array_values((array) ($lawsSection['cards'] ?? []));
    $readMoreLabel = trim((string) ($blogSection['post_action_label'] ?? ''));
    $allPostsLabel = trim((string) ($blogSection['all_posts_label'] ?? ''));
    $viewAllCallsLabel = trim((string) ($callsSection['view_all_label'] ?? ''));
    $callsDownloadLink = (array) ($callsSection['download_link'] ?? []);
    $otherCallsSection = (array) ($callsSection['other_calls'] ?? []);
    $otherCallItems = array_values(array_filter(
        (array) ($otherCallsSection['items'] ?? []),
        static fn ($item): bool => is_array($item) && (
            trim((string) ($item['title'] ?? '')) !== ''
            || trim((string) data_get($item, 'resolved_link.url', '')) !== ''
        ),
    ));
    $meetingTitle = trim((string) ($meetingSection['title'] ?? ''));
    $meetingIntro = trim((string) ($meetingSection['intro'] ?? ''));
    $meetingCardTitle = trim((string) ($meetingSection['contact_title'] ?? ''));
    $meetingButtonLabel = trim((string) ($meetingSection['button_label'] ?? ''));
    $meetingStatus = trim((string) ($meetingSection['status'] ?? ''));
    $heroLabel = trim((string) ($heroSection['subtitle_lead'] ?? ''));
    $heroAccent = trim((string) ($heroSection['subtitle_accent'] ?? ''));
    $heroLabel = trim($heroLabel.' '.$heroAccent);
    $heroHook = trim((string) ($heroSection['intro'] ?? ''));
    $heroImageAlt = trim((string) ($heroSection['image_alt'] ?? ''));
    $serviceIcons = ['fa-magnifying-glass-chart', 'fa-file-certificate', 'fa-diagram-project', 'fa-wallet', 'fa-badge-percent', 'fa-bullseye-pointer'];
    $sourceIcons = ['fa-folder-open', 'fa-lightbulb-on', 'fa-file-check', 'fa-coins', 'fa-badge-percent', 'fa-landmark-dome'];
    $headingWords = static fn (string $heading): array => preg_split('/\s+/u', trim($heading)) ?: [];
    $currentHost = request()->getHost();
    $sameOriginAssetUrl = static function (?string $url) use ($currentHost): string {
        $assetUrl = trim((string) $url);
        $assetHost = parse_url($assetUrl, PHP_URL_HOST);

        if ($assetUrl === '' || ($assetHost !== null && $assetHost !== $currentHost)) {
            return $assetUrl;
        }

        $assetPath = parse_url($assetUrl, PHP_URL_PATH);
        $assetQuery = parse_url($assetUrl, PHP_URL_QUERY);

        return is_string($assetPath) && $assetPath !== ''
            ? $assetPath.($assetQuery ? '?'.$assetQuery : '')
            : $assetUrl;
    };
    $resolveContentUrl = static function (?string $url): string {
        $target = trim((string) $url);

        $target = \App\Support\Localization\FrontendRoute::localizeUrl($target);

        if ($target === '' || str_starts_with($target, '#') || str_starts_with($target, 'http://') || str_starts_with($target, 'https://')) {
            return $target;
        }

        return url(str_starts_with($target, '/') ? $target : '/'.$target);
    };
    $heroImageUrl = $sameOriginAssetUrl((string) $heroBackgroundUrl);
    $hasEuFundsPosts = ($euFundsPosts ?? collect())->isNotEmpty();
    $blogHeadingTitle = trim((string) ($blogSection['title'] ?? ''));
@endphp

@section('title', $servicePageMetaTitle)
@section('main_class', 'w-full px-0 py-0')

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/advisory.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/advisory.css')) }}">
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/eu-funds.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/eu-funds.css')) }}">
@endpush

@section('content')
    <div class="ac-advisory-page ac-advisory-subpage ac-eu-service-page">
        <section class="ac-advisory-hero" id="vrh" aria-labelledby="ac-eu-funds-hero-title">
            <div class="ac-advisory-hero-media">
                <img
                    src="{{ $heroImageUrl }}"
                    alt="{{ $heroImageAlt }}"
                    class="ac-advisory-hero-image"
                    width="1366"
                    height="768"
                    loading="eager"
                    decoding="async"
                    fetchpriority="high"
                >
            </div>
            <div class="ac-advisory-hero-overlay" aria-hidden="true"></div>

            <div class="ac-advisory-hero-shell">
                <div class="ac-advisory-hero-copy">
                    <h1 id="ac-eu-funds-hero-title" aria-label="{{ $heroLabel }}. {{ $heroHook }}">
                        <span class="ac-advisory-hero-label">{{ $heroLabel }}</span>
                        @if ($heroHook !== '')
                            <span class="ac-advisory-hero-hook">{{ $heroHook }}</span>
                        @endif
                    </h1>
                </div>
            </div>
        </section>

        <section class="ac-advisory-intro" id="eu-funds-overview" aria-labelledby="ac-eu-funds-overview-title">
            <div class="ac-advisory-wide-shell ac-advisory-intro-grid">
                <div class="ac-advisory-intro-heading">
                    <h2 id="ac-eu-funds-overview-title" data-words-slide-from-right aria-label="{{ $overviewSection['title'] ?? '' }}">
                        @foreach ($headingWords((string) ($overviewSection['title'] ?? '')) as $word)
                            <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>
                </div>

                <div class="ac-advisory-intro-copy content-reveal animation-index-1" data-image-reveal>
                    @foreach ($overviewBlocks as $block)
                        {!! count($overviewBlocks) > 1 && $loop->last
                            ? \App\Support\Content\StructuredRichText::addClassToFirstBlock($block, 'is-emphasis')
                            : $block !!}
                    @endforeach
                </div>
            </div>
        </section>

        <section class="ac-advisory-services ac-eu-funds-services" id="eu-funds-services" aria-labelledby="ac-eu-funds-services-title">
            <div class="ac-advisory-wide-shell">
                <header class="ac-advisory-section-heading">
                    <h2 id="ac-eu-funds-services-title" data-words-slide-from-right aria-label="{{ $processSection['title'] ?? '' }}">
                        @foreach ($headingWords((string) ($processSection['title'] ?? '')) as $word)
                            <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>
                    @if (trim((string) ($processSection['intro'] ?? '')) !== '')
                        <p>{{ $processSection['intro'] }}</p>
                    @endif
                </header>

                <div class="ac-advisory-services-grid ac-advisory-services-grid--subpage">
                    @foreach ($serviceItems as $item)
                        <article class="ac-advisory-service-card ac-eu-funds-service-card content-reveal animation-index-{{ $loop->index }}" data-image-reveal>
                            <span class="ac-advisory-service-icon" aria-hidden="true">
                                <i class="fa-duotone fa-thin fa-fw {{ $serviceIcons[$loop->index] ?? 'fa-chart-network' }}"></i>
                            </span>
                            <h3>{{ $item['title'] ?? '' }}</h3>
                            <p>{{ $item['text'] ?? '' }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        @if ($approachBlocks !== [])
            <section class="ac-advisory-approach" id="eu-funds-approach" aria-labelledby="ac-eu-funds-approach-title">
                <div class="ac-advisory-wide-shell ac-advisory-approach-grid">
                    <div class="ac-advisory-approach-heading">
                        <h2 id="ac-eu-funds-approach-title" data-words-slide-from-right aria-label="{{ $approachSection['title'] ?? '' }}">
                            @foreach ($headingWords((string) ($approachSection['title'] ?? '')) as $word)
                                <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                            @endforeach
                        </h2>
                    </div>

                    <blockquote class="ac-advisory-approach-quote content-reveal animation-index-1" data-image-reveal>
                        <i class="fa-duotone fa-thin fa-quote-left" aria-hidden="true"></i>
                        @foreach ($approachBlocks as $block)
                            {!! $block !!}
                        @endforeach
                    </blockquote>
                </div>
            </section>
        @endif

        <section class="ac-eu-funds-module-section" id="eu-funds-sources" aria-labelledby="ac-eu-funds-sources-title">
            <div class="ac-advisory-wide-shell">
                <header class="ac-advisory-section-heading">
                    <h2 id="ac-eu-funds-sources-title" data-words-slide-from-right aria-label="{{ $sourceModulesSection['title'] ?? '' }}">
                        @foreach ($headingWords((string) ($sourceModulesSection['title'] ?? '')) as $word)
                            <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>
                    @if (trim((string) ($sourceModulesSection['intro'] ?? '')) !== '')
                        <p>{{ $sourceModulesSection['intro'] }}</p>
                    @endif
                </header>

                <div class="ac-advisory-services-grid ac-advisory-services-grid--subpage ac-eu-funds-source-grid">
                    @foreach ($sourceCards as $module)
                        @php $moduleUrl = $resolveContentUrl($module['url'] ?? ''); @endphp
                        <a class="ac-advisory-service-card ac-advisory-subpage-service-card animation-index-{{ $loop->index }}" data-image-reveal href="{{ $moduleUrl !== '' ? $moduleUrl : '#eu-funds-sources' }}">
                            <span class="ac-advisory-service-icon" aria-hidden="true">
                                <i class="fa-duotone fa-thin fa-fw {{ $sourceIcons[$loop->index] ?? 'fa-coins' }}"></i>
                            </span>
                            <h3>{{ $module['title'] ?? '' }}</h3>
                            <p>{{ $module['text'] ?? '' }}</p>
                            <span class="ac-advisory-service-link" aria-hidden="true">
                                {{ $readMoreLabel }}
                                <i class="fa-duotone fa-thin fa-arrow-right fa-fw"></i>
                            </span>
                        </a>
                    @endforeach
                </div>

                @if ($callGroups !== [] || trim((string) ($callsDownloadLink['url'] ?? '')) !== '' || $otherCallItems !== [])
                    <div id="eu-funds-calls" class="ac-eu-call-module" aria-labelledby="ac-eu-funds-calls-title">
                        <header class="ac-eu-module-heading">
                            <p class="ac-eu-module-kicker">{{ trim((string) ($callsSection['kicker'] ?? '')) }}</p>
                            <h3 id="ac-eu-funds-calls-title">{{ trim((string) ($callsSection['title'] ?? '')) }}</h3>
                            @if (trim((string) ($callsSection['intro'] ?? '')) !== '')
                                <p>{{ $callsSection['intro'] }}</p>
                            @endif
                            @if (trim((string) ($callsDownloadLink['url'] ?? '')) !== '')
                                <a href="{{ $callsDownloadLink['url'] }}" class="ac-eu-editorial-link ac-eu-calls-download-link" @if($callsDownloadLink['open_in_new_tab'] ?? false) target="_blank" rel="{{ $callsDownloadLink['rel'] ?? 'noopener noreferrer' }}" @endif>
                                    <span>{{ $callsDownloadLink['label'] ?? '' }}</span>
                                    <i class="fa-duotone fa-thin fa-arrow-down-to-line fa-fw" aria-hidden="true"></i>
                                </a>
                            @endif
                        </header>

                        @if ($callGroups !== [])
                            <div class="ac-eu-call-group-grid">
                                @foreach ($callGroups as $group)
                                    @php
                                        $tone = trim((string) ($group['tone'] ?? 'pending')) ?: 'pending';
                                        $items = array_values((array) ($group['items'] ?? []));
                                        $visibleItems = array_slice($items, 0, 5);
                                        $hiddenItems = array_slice($items, 5);
                                        $statusLabel = trim((string) ($group['status_label'] ?? ''));
                                    @endphp
                                    <article id="eu-funds-calls-{{ $tone }}" class="ac-eu-call-group-card content-reveal animation-index-{{ $loop->index }}" data-image-reveal>
                                        <div class="ac-eu-call-group-head">
                                            <h3>{{ trim((string) ($group['title'] ?? '')) }}</h3>
                                            <span class="ac-eu-status-badge is-{{ $tone }}">{{ $statusLabel }}</span>
                                        </div>

                                        <ul class="ac-eu-call-list">
                                            @foreach ($visibleItems as $item)
                                                @include('front.desktop.pages.partials.eu-funds-call-item', ['item' => $item])
                                            @endforeach
                                        </ul>

                                        @if ($hiddenItems !== [])
                                            <details class="ac-eu-call-details">
                                                <summary>{{ $viewAllCallsLabel }}</summary>
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
                        @endif
                        @if ($otherCallItems !== [])
                            <div class="ac-eu-other-calls content-reveal" data-image-reveal>
                                @if (trim((string) ($otherCallsSection['title'] ?? '')) !== '')
                                    <h3>{{ $otherCallsSection['title'] }}</h3>
                                @endif
                                @if (trim((string) ($otherCallsSection['intro'] ?? '')) !== '')
                                    <p>{{ $otherCallsSection['intro'] }}</p>
                                @endif
                                <ul class="ac-eu-other-call-list">
                                    @foreach ($otherCallItems as $item)
                                        @php $otherCallLink = (array) ($item['resolved_link'] ?? []); @endphp
                                        <li>
                                            @if (trim((string) ($otherCallLink['url'] ?? '')) !== '')
                                                <a href="{{ $otherCallLink['url'] }}" @if($otherCallLink['open_in_new_tab'] ?? false) target="_blank" rel="{{ $otherCallLink['rel'] ?? 'noopener noreferrer' }}" @endif>{{ $item['title'] ?? ($otherCallLink['label'] ?? '') }}</a>
                                            @else
                                                <span>{{ $item['title'] ?? '' }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </section>

        @if ($resourceCards !== [])
            <section class="ac-eu-funds-module-section ac-eu-funds-module-section--soft" id="eu-funds-programs" aria-labelledby="ac-eu-funds-programs-title">
                <div class="ac-advisory-wide-shell">
                    <header class="ac-advisory-section-heading">
                        <h2 id="ac-eu-funds-programs-title" data-words-slide-from-right aria-label="{{ $resourcesSection['title'] ?? '' }}">
                            @foreach ($headingWords((string) ($resourcesSection['title'] ?? '')) as $word)
                                <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                            @endforeach
                        </h2>
                        @if (trim((string) ($resourcesSection['intro'] ?? '')) !== '')
                            <p>{{ $resourcesSection['intro'] }}</p>
                        @endif
                    </header>

                    <div class="ac-eu-program-grid">
                        @foreach ($resourceCards as $card)
                            @php $cardBodyBlocks = $richTextBlocks((array) $card); @endphp
                            <article class="ac-advisory-text-panel ac-eu-program-card content-reveal animation-index-{{ $loop->index }}" data-image-reveal>
                                @if (trim((string) ($card['eyebrow'] ?? '')) !== '')
                                    <p class="ac-eu-program-eyebrow">{{ $card['eyebrow'] }}</p>
                                @endif
                                <h3>{{ $card['title'] ?? '' }}</h3>

                                @foreach ($cardBodyBlocks as $block)
                                    {!! $block !!}
                                @endforeach

                                @foreach ((array) ($card['groups'] ?? []) as $group)
                                    <div class="ac-eu-program-list-block">
                                        <h4>{{ $group['label'] ?? '' }}</h4>
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
                                        @foreach (['primary_link', 'secondary_link'] as $linkKey)
                                            @if (!empty($card[$linkKey]['url'] ?? ''))
                                                <a href="{{ $card[$linkKey]['url'] }}" class="ac-eu-editorial-link" @if($card[$linkKey]['open_in_new_tab'] ?? false) target="_blank" rel="{{ $card[$linkKey]['rel'] ?? 'noopener noreferrer' }}" @endif>
                                                    <span>{{ $card[$linkKey]['label'] }}</span>
                                                    <i class="fa-duotone fa-thin fa-arrow-right fa-fw" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if ($lawCards !== [])
            <section class="ac-eu-funds-module-section" id="eu-funds-laws" aria-labelledby="ac-eu-funds-laws-title">
                <div class="ac-advisory-wide-shell">
                    <header class="ac-advisory-section-heading">
                        <h2 id="ac-eu-funds-laws-title" data-words-slide-from-right aria-label="{{ $lawsSection['title'] ?? '' }}">
                            @foreach ($headingWords((string) ($lawsSection['title'] ?? '')) as $word)
                                <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                            @endforeach
                        </h2>
                        @if (trim((string) ($lawsSection['intro'] ?? '')) !== '')
                            <p>{{ $lawsSection['intro'] }}</p>
                        @endif
                    </header>

                    <div class="ac-eu-program-grid">
                        @foreach ($lawCards as $card)
                            <article class="ac-advisory-text-panel ac-eu-program-card content-reveal animation-index-{{ $loop->index }}" data-image-reveal>
                                <h3>{{ $card['title'] ?? '' }}</h3>
                                @if (trim((string) ($card['summary'] ?? '')) !== '')
                                    <p>{{ $card['summary'] }}</p>
                                @endif

                                @foreach ((array) ($card['lists'] ?? []) as $list)
                                    <div class="ac-eu-program-list-block">
                                        <h4>{{ $list['label'] ?? '' }}</h4>
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
                                        @foreach (['primary_link', 'secondary_link'] as $linkKey)
                                            @if (!empty($card[$linkKey]['url'] ?? ''))
                                                <a href="{{ $card[$linkKey]['url'] }}" class="ac-eu-editorial-link" @if($card[$linkKey]['open_in_new_tab'] ?? false) target="_blank" rel="{{ $card[$linkKey]['rel'] ?? 'noopener noreferrer' }}" @endif>
                                                    <span>{{ $card[$linkKey]['label'] }}</span>
                                                    <i class="fa-duotone fa-thin fa-arrow-right fa-fw" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if ($hasEuFundsPosts)
            <section class="news-section ac-advisory-news" aria-labelledby="ac-eu-funds-news-title">
                <div class="news-shell">
                    <header class="news-header">
                        <div class="ac-eu-funds-news-heading-copy">
                            <h2 class="news-title" id="ac-eu-funds-news-title" data-words-slide-from-right aria-label="{{ $blogHeadingTitle }}">
                                @foreach ($headingWords($blogHeadingTitle) as $word)
                                    <span class="news-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                                @endforeach
                            </h2>
                        </div>

                        <a class="news-all-link content-reveal" data-image-reveal href="{{ $euFundsArchiveUrl }}">
                            <span>{{ $allPostsLabel }}</span>
                            <i class="fa-duotone fa-thin fa-arrow-right fa-fw" aria-hidden="true"></i>
                        </a>
                    </header>

                    <div class="news-grid">
                        @foreach ($euFundsPosts->take(3) as $post)
                            @php
                                $translation = $post->translations->firstWhere('locale', $locale)
                                    ?? $post->translations->firstWhere('locale', $fallbackLocale);
                                $postSlug = trim((string) ($translation?->slug ?? ''));
                                $postUrl = $postSlug !== '' ? route('blog.show', ['slug' => $postSlug]) : route('blog.index');
                                $postTitle = trim((string) ($translation?->title ?? $post->code));
                                $postExcerpt = trim((string) ($translation?->excerpt ?? ''));
                                $postExcerpt = \Illuminate\Support\Str::limit($postExcerpt, 190, '...', true);
                                $primaryCategory = $post->categories
                                    ->sortByDesc(fn ($category) => (int) ($category->pivot->is_primary ?? false))
                                    ->first();
                                $categoryTranslation = $primaryCategory?->translations->firstWhere('locale', $locale)
                                    ?? $primaryCategory?->translations->firstWhere('locale', $fallbackLocale);
                                $categoryLabel = trim((string) ($categoryTranslation?->name ?? $euFundsCategoryName ?? ''));
                            @endphp

                            <a class="news-card animation-index-{{ $loop->index }}" data-image-reveal href="{{ $postUrl }}" aria-label="{{ $postTitle }}">
                                <span class="news-card-category">{{ $categoryLabel }}</span>
                                <h3>{{ $postTitle }}</h3>
                                <p>{{ $postExcerpt }}</p>
                                <span class="news-card-link" aria-hidden="true">
                                    {{ $readMoreLabel }}
                                    <i class="fa-duotone fa-thin fa-arrow-right fa-fw"></i>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="contact-cta ac-advisory-contact-cta" id="eu-funds-cta" aria-labelledby="ac-eu-funds-contact-title">
            <div class="contact-cta-shell">
                <div class="contact-cta-copy">
                    <h2 class="contact-cta-title" id="ac-eu-funds-contact-title" data-words-slide-from-right aria-label="{{ $meetingTitle }}">
                        @foreach ($headingWords($meetingTitle) as $word)
                            <span class="contact-cta-title-word animation-index-{{ $loop->index }} {{ $loop->remaining < 2 ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>
                </div>

                <div class="contact-cta-card" data-image-reveal>
                    <h3 class="contact-cta-card-heading">{{ $meetingCardTitle }}</h3>
                    <p>{{ $meetingIntro }}</p>
                    <a class="contact-cta-button" href="{{ \App\Support\Localization\FrontendRoute::url('contact.create') }}">
                        <span>{{ $meetingButtonLabel }}</span>
                        <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                    </a>
                    <small><span class="contact-cta-status-dot" aria-hidden="true"></span>{{ $meetingStatus }}</small>
                </div>
            </div>
        </section>
    </div>
@endsection
