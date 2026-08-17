@extends('front.desktop.layouts.store')

@php
    $content = (array) ($advisoryContent ?? []);
    $subpage = (array) ($subpage ?? []);
    $type = (string) ($subpage['type'] ?? 'detail');
    $detailKey = (string) ($subpage['detail_key'] ?? '');
    $detail = (array) data_get($content, $detailKey, []);
    $funding = (array) ($content['funding'] ?? []);
    $sourceModules = (array) ($content['source_modules'] ?? []);
    $pandea = (array) ($content['pandea'] ?? []);
    $pageContent = $type === 'funding' ? $funding : $detail;
    $meeting = (array) ($pageContent['meeting'] ?? $content['meeting'] ?? []);
    $blogSection = (array) ($pageContent['blog_section'] ?? $content['blog_section'] ?? []);
    $isCroatian = str_starts_with(strtolower((string) ($locale ?? app()->getLocale())), 'hr');
    $richTextBlocks = static function (array $section, string $htmlKey, string $legacyKey): array {
        $html = array_key_exists($htmlKey, $section)
            ? trim((string) ($section[$htmlKey] ?? ''))
            : \App\Support\Content\StructuredRichText::fromParagraphs((array) ($section[$legacyKey] ?? []));

        return \App\Support\Content\StructuredRichText::blocks($html);
    };

    $overviewTitle = $type === 'funding'
        ? trim((string) ($funding['title'] ?? $subpage['title'] ?? ''))
        : trim((string) ($detail['overview_title'] ?? $detail['title'] ?? $subpage['title'] ?? ''));
    $overviewBlocks = $type === 'funding'
        ? \App\Support\Content\StructuredRichText::blocks(
            \App\Support\Content\StructuredRichText::fromParagraphs([
                trim((string) ($funding['intro'] ?? $subpage['intro'] ?? '')),
            ])
        )
        : $richTextBlocks($detail, 'overview_body_html', 'overview_body');

    $servicesTitle = $type === 'funding'
        ? trim((string) ($sourceModules['title'] ?? ''))
        : trim((string) ($detail['services_title'] ?? ''));
    $servicesTitle = $servicesTitle !== ''
        ? $servicesTitle
        : ($isCroatian ? 'Naše usluge' : 'Our services');
    $servicesBlocks = $type === 'funding'
        ? \App\Support\Content\StructuredRichText::blocks(
            \App\Support\Content\StructuredRichText::fromParagraphs([
                trim((string) ($sourceModules['intro'] ?? '')),
            ])
        )
        : $richTextBlocks($detail, 'services_body_html', 'services_body');

    $serviceCards = $type === 'funding'
        ? array_values(array_filter(
            (array) ($funding['cards'] ?? []),
            static fn ($card): bool => is_array($card) && trim((string) ($card['title'] ?? '')) !== '',
        ))
        : array_values(array_map(
            static fn ($item): array => ['title' => trim((string) $item), 'text' => '', 'url' => ''],
            array_filter(
                (array) ($detail['help_items'] ?? []),
                static fn ($item): bool => trim((string) $item) !== '',
            ),
        ));

    $approach = $type === 'funding'
        ? [
            'title' => $funding['approach_title'] ?? data_get($content, 'approach.title', ''),
            'body' => $funding['approach_body'] ?? data_get($content, 'approach.body', []),
        ]
        : [
            'title' => $detail['approach_title'] ?? '',
            'body' => $detail['approach_body'] ?? [],
        ];
    $approachTitle = trim((string) ($approach['title'] ?? ''))
        ?: ($isCroatian ? 'Naš pristup' : 'Our approach');
    $approachBlocks = $type === 'funding'
        ? $richTextBlocks($funding, 'approach_body_html', 'approach_body')
        : $richTextBlocks($detail, 'approach_body_html', 'approach_body');

    $serviceIconSets = [
        'funding' => ['fa-hand-holding-circle-dollar', 'fa-landmark-dome', 'fa-badge-percent'],
        'financial' => ['fa-chart-line-up', 'fa-calculator', 'fa-money-check-dollar-pen', 'fa-landmark', 'fa-file-chart-column', 'fa-chart-network'],
        'ma' => ['fa-chart-mixed', 'fa-chart-user', 'fa-diagram-project', 'fa-file-certificate', 'fa-people-group', 'fa-buildings', 'fa-handshake', 'fa-badge-check'],
        'due_diligence' => ['fa-magnifying-glass-chart', 'fa-chart-waterfall', 'fa-shield-halved', 'fa-chart-column', 'fa-bullseye-pointer', 'fa-lightbulb-on'],
        'valuations' => ['fa-chart-user', 'fa-calculator', 'fa-chart-mixed', 'fa-coins', 'fa-people-arrows-left-right', 'fa-file-certificate'],
        'tax' => ['fa-badge-percent', 'fa-building-shield', 'fa-file-check', 'fa-shield-halved', 'fa-wallet', 'fa-diagram-project', 'fa-people-arrows-left-right'],
        'bank_loans' => ['fa-wallet', 'fa-chart-waterfall', 'fa-file-invoice-dollar', 'fa-people-arrows-left-right', 'fa-calculator', 'fa-landmark-dome'],
        'zopu' => ['fa-hand-holding-circle-dollar', 'fa-chart-user', 'fa-file-certificate', 'fa-chart-column', 'fa-landmark-dome', 'fa-diagram-project'],
    ];
    $serviceIcons = $serviceIconSets[$type === 'funding' ? 'funding' : $detailKey]
        ?? ['fa-chart-network'];

    $heroLabel = trim((string) ($heroSection['subtitle_lead'] ?? $subpage['title'] ?? ''));
    $heroHook = trim((string) ($heroSection['intro'] ?? $subpage['hook'] ?? ''));
    $heroImageAlt = trim((string) ($subpage['hero_image_alt'] ?? ''))
        ?: $heroLabel.($isCroatian ? ' — stručna savjetodavna podrška' : ' — expert advisory support');
    $heroLabelClass = mb_strlen($heroLabel) > 26 ? 'is-long' : '';
    $meetingTitle = trim((string) ($meeting['title'] ?? ''))
        ?: ($isCroatian ? 'Razgovarajmo o vašim poslovnim odlukama' : 'Let’s discuss your business decisions');
    $meetingIntro = trim((string) ($meeting['intro'] ?? ''))
        ?: ($isCroatian
            ? 'Javite nam se i zajedno ćemo procijeniti koji oblik savjetodavne podrške najbolje odgovara vašem cilju.'
            : 'Contact us and we will assess which form of advisory support best fits your goal.');
    $meetingCardTitle = trim((string) ($meeting['contact_title'] ?? ''))
        ?: ($isCroatian ? 'Kontaktirajte nas' : 'Contact us');
    $meetingButtonLabel = trim((string) ($meeting['button_label'] ?? ''))
        ?: ($isCroatian ? 'Dogovorite sastanak' : 'Schedule a meeting');
    $meetingStatus = trim((string) ($meeting['status'] ?? ''))
        ?: ($isCroatian ? 'Termin razgovora prilagođavamo vama.' : 'We arrange the meeting around your schedule.');
    $blogHeadingTitle = trim((string) ($blogSection['title'] ?? ''))
        ?: ($isCroatian ? 'Stručni uvidi u financije, poreze i transakcije' : 'Expert insights into finance, tax and transactions');
    $allPostsLabel = trim((string) ($blogSection['all_posts_label'] ?? ''))
        ?: ($isCroatian ? 'Pogledaj sve objave' : 'View all posts');
    $readMoreLabel = trim((string) ($blogSection['post_action_label'] ?? ''))
        ?: ($isCroatian ? 'Opširnije' : 'Read more');

    $pagePandea = (array) ($detail['pandea'] ?? $pandea);
    $pandeaBlocks = (bool) ($detail['show_pandea'] ?? false)
        ? $richTextBlocks($pagePandea, 'body_html', 'body')
        : [];
    $networkTitle = trim((string) ($pagePandea['title'] ?? ''));
    $networkTitleLines = preg_split('/(?=Pandea Global M&A)/u', $networkTitle, 2, PREG_SPLIT_NO_EMPTY) ?: [$networkTitle];

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

        if ($target === '' || str_starts_with($target, '#') || str_starts_with($target, 'http://') || str_starts_with($target, 'https://')) {
            return $target;
        }

        return url(str_starts_with($target, '/') ? $target : '/'.$target);
    };
    $headingWords = static fn (string $heading): array => preg_split('/\s+/u', trim($heading)) ?: [];
    $heroImageUrl = $sameOriginAssetUrl((string) $heroBackgroundUrl);
    $networkLogoUrl = $sameOriginAssetUrl((string) ($pandeaLogoUrl ?? ''));
    $hasAdvisoryPosts = ($advisoryPosts ?? collect())->isNotEmpty();
@endphp

@section('title', $servicePageMetaTitle !== '' ? $servicePageMetaTitle : ($servicePageTitle ?? $heroLabel))
@section('main_class', 'w-full px-0 py-0')

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/advisory.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/advisory.css')) }}">
@endpush

@section('content')
    <div class="ac-advisory-page ac-advisory-subpage ac-advisory-subpage--{{ $type === 'funding' ? 'funding' : $detailKey }}">
        <section class="ac-advisory-hero" id="vrh" aria-labelledby="ac-advisory-subpage-hero-title">
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
                    <h1 id="ac-advisory-subpage-hero-title" aria-label="{{ $heroLabel }}. {{ $heroHook }}">
                        <span class="ac-advisory-hero-label {{ $heroLabelClass }}">{{ $heroLabel }}</span>
                        @if ($heroHook !== '')
                            <span class="ac-advisory-hero-hook">{{ $heroHook }}</span>
                        @endif
                    </h1>
                </div>
            </div>
        </section>

        <section class="ac-advisory-intro" id="advisory-subpage-overview" aria-labelledby="ac-advisory-subpage-overview-title">
            <div class="ac-advisory-wide-shell ac-advisory-intro-grid">
                <div class="ac-advisory-intro-heading">
                    <h2 id="ac-advisory-subpage-overview-title" data-words-slide-from-right aria-label="{{ $overviewTitle }}">
                        @foreach ($headingWords($overviewTitle) as $word)
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

        @if ($pandeaBlocks !== [])
            <section class="ac-advisory-network" aria-labelledby="ac-advisory-subpage-network-title">
                <div class="ac-advisory-wide-shell ac-advisory-network-grid">
                    <div class="ac-advisory-network-heading">
                        <h2 id="ac-advisory-subpage-network-title" data-words-slide-from-right aria-label="{{ $networkTitle }}">
                            @foreach ($networkTitleLines as $line)
                                <span class="ac-advisory-network-title-line">
                                    @foreach ($headingWords($line) as $word)
                                        <span class="service-title-word animation-index-{{ $loop->parent->index + $loop->index }} {{ $loop->parent->last && $loop->index > 0 ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                                    @endforeach
                                </span>
                            @endforeach
                        </h2>

                        @if ($networkLogoUrl !== '')
                            <div class="ac-advisory-network-logo-card content-reveal" data-image-reveal>
                                <img
                                    src="{{ $networkLogoUrl }}"
                                    alt="{{ $pagePandea['logo_alt'] ?? 'Pandea Global M&A' }}"
                                    class="ac-advisory-network-logo"
                                    width="380"
                                    height="100"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </div>
                        @endif
                    </div>

                    <div class="ac-advisory-network-copy content-reveal animation-index-1" data-image-reveal>
                        @foreach ($pandeaBlocks as $block)
                            {!! $block !!}
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if ($servicesTitle !== '' || $servicesBlocks !== [] || $serviceCards !== [])
            <section class="ac-advisory-services ac-advisory-subpage-services" id="advisory-subpage-services" aria-labelledby="ac-advisory-subpage-services-title">
                <div class="ac-advisory-wide-shell">
                    <header class="ac-advisory-section-heading">
                        <h2 id="ac-advisory-subpage-services-title" data-words-slide-from-right aria-label="{{ $servicesTitle }}">
                            @foreach ($headingWords($servicesTitle) as $word)
                                <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                            @endforeach
                        </h2>
                        @foreach ($servicesBlocks as $block)
                            {!! $block !!}
                        @endforeach
                    </header>

                    @if ($serviceCards !== [])
                        <div class="ac-advisory-services-grid ac-advisory-services-grid--subpage">
                            @foreach ($serviceCards as $card)
                                @php $cardUrl = $resolveContentUrl($card['url'] ?? ''); @endphp

                                @if ($cardUrl !== '')
                                    <a class="ac-advisory-service-card ac-advisory-subpage-service-card animation-index-{{ $loop->index }}" data-image-reveal href="{{ $cardUrl }}">
                                        <span class="ac-advisory-service-icon" aria-hidden="true">
                                            <i class="fa-duotone fa-thin fa-fw {{ $serviceIcons[$loop->index] ?? 'fa-chart-network' }}"></i>
                                        </span>
                                        <h3>{{ $card['title'] ?? '' }}</h3>
                                        @if (trim((string) ($card['text'] ?? '')) !== '')
                                            <p>{{ $card['text'] }}</p>
                                        @endif
                                        <span class="ac-advisory-service-link" aria-hidden="true">
                                            {{ $readMoreLabel }}
                                            <i class="fa-duotone fa-thin fa-arrow-right fa-fw"></i>
                                        </span>
                                    </a>
                                @else
                                    <article class="ac-advisory-service-card ac-advisory-subpage-service-card ac-advisory-subpage-service-card--capability content-reveal animation-index-{{ $loop->index }}" data-image-reveal>
                                        <span class="ac-advisory-service-icon" aria-hidden="true">
                                            <i class="fa-duotone fa-thin fa-fw {{ $serviceIcons[$loop->index] ?? 'fa-chart-network' }}"></i>
                                        </span>
                                        <h3>{{ $card['title'] ?? '' }}</h3>
                                    </article>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        @endif

        @if ($approachBlocks !== [])
            <section class="ac-advisory-approach" aria-labelledby="ac-advisory-subpage-approach-title">
                <div class="ac-advisory-wide-shell ac-advisory-approach-grid">
                    <div class="ac-advisory-approach-heading">
                        <h2 id="ac-advisory-subpage-approach-title" data-words-slide-from-right aria-label="{{ $approachTitle }}">
                            @foreach ($headingWords($approachTitle) as $word)
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

        @if ($hasAdvisoryPosts)
            <section class="news-section ac-advisory-news" aria-labelledby="ac-advisory-subpage-news-title">
                <div class="news-shell">
                    <header class="news-header">
                        <h2 class="news-title" id="ac-advisory-subpage-news-title" data-words-slide-from-right aria-label="{{ $blogHeadingTitle }}">
                            @foreach ($headingWords($blogHeadingTitle) as $word)
                                <span class="news-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                            @endforeach
                        </h2>

                        <a class="news-all-link content-reveal" data-image-reveal href="{{ $advisoryArchiveUrl }}">
                            <span>{{ $allPostsLabel }}</span>
                            <i class="fa-duotone fa-thin fa-arrow-right fa-fw" aria-hidden="true"></i>
                        </a>
                    </header>

                    <div class="news-grid">
                        @foreach ($advisoryPosts->take(3) as $post)
                            @php
                                $translation = $post->translations->firstWhere('locale', $locale)
                                    ?? $post->translations->firstWhere('locale', $fallbackLocale);
                                $postSlug = trim((string) ($translation?->slug ?? ''));
                                $postUrl = $postSlug !== '' ? route('blog.show', ['slug' => $postSlug]) : route('blog.index');
                                $postTitle = trim((string) ($translation?->title ?? $post->code));
                                $postExcerpt = trim((string) ($translation?->excerpt ?? '')) ?: __('ui.blog.excerpt_fallback');
                                $postExcerpt = \Illuminate\Support\Str::limit($postExcerpt, 190, '...', true);
                                $primaryCategory = $post->categories
                                    ->sortByDesc(fn ($category) => (int) ($category->pivot->is_primary ?? false))
                                    ->first();
                                $categoryTranslation = $primaryCategory?->translations->firstWhere('locale', $locale)
                                    ?? $primaryCategory?->translations->firstWhere('locale', $fallbackLocale);
                                $categoryLabel = trim((string) ($categoryTranslation?->name ?? ($isCroatian ? 'Novosti' : 'News')));
                            @endphp

                            <a class="news-card animation-index-{{ $loop->index }}" data-image-reveal href="{{ $postUrl }}" aria-label="{{ $isCroatian ? 'Otvori blog post' : 'Open blog post' }}: {{ $postTitle }}">
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

        <section class="contact-cta ac-advisory-contact-cta" aria-labelledby="ac-advisory-subpage-contact-title">
            <div class="contact-cta-shell">
                <div class="contact-cta-copy">
                    <h2 class="contact-cta-title" id="ac-advisory-subpage-contact-title" data-words-slide-from-right aria-label="{{ $meetingTitle }}">
                        @foreach ($headingWords($meetingTitle) as $word)
                            <span class="contact-cta-title-word animation-index-{{ $loop->index }} {{ $loop->remaining < 2 ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>
                </div>

                <div class="contact-cta-card" data-image-reveal>
                    <h3 class="contact-cta-card-heading">{{ $meetingCardTitle }}</h3>
                    <p>{{ $meetingIntro }}</p>
                    <a class="contact-cta-button" href="{{ route('contact.create') }}">
                        <span>{{ $meetingButtonLabel }}</span>
                        <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                    </a>
                    <small><span class="contact-cta-status-dot" aria-hidden="true"></span>{{ $meetingStatus }}</small>
                </div>
            </div>
        </section>
    </div>
@endsection
