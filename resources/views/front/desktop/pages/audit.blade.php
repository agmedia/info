@extends('front.desktop.layouts.store')

@php
    $legacyOverviewBody = array_values(array_filter([
        ($overviewSection['intro'] ?? ''),
        ...(array) ($overviewSection['body'] ?? []),
    ], static fn ($paragraph): bool => trim((string) $paragraph) !== ''));
    $overviewBodyHtml = array_key_exists('body_html', $overviewSection)
        ? trim((string) $overviewSection['body_html'])
        : \App\Support\Content\StructuredRichText::fromParagraphs($legacyOverviewBody);
    $overviewBlocks = \App\Support\Content\StructuredRichText::blocks($overviewBodyHtml);
    $obligorsIntro = trim((string) ($obligorsSection['intro'] ?? ''));
    $obligorsPrimaryTitle = trim((string) ($obligorsSection['primary_title'] ?? ''));
    $obligorsPrimaryItems = array_values((array) ($obligorsSection['primary_items'] ?? []));
    $obligorsNote = trim((string) ($obligorsSection['note'] ?? ''));
    $useObligorsList = ($obligorsSection['display_mode'] ?? '') === 'list' && $obligorsPrimaryItems !== [];
    $auditServices = array_values(array_filter(
        (array) ($servicesSection['items'] ?? []),
        static fn ($item): bool => is_array($item) && trim((string) ($item['title'] ?? '')) !== '',
    ));
    $serviceIcons = [
        'fa-file-check',
        'fa-layer-group',
        'fa-magnifying-glass-chart',
        'fa-leaf',
        'fa-briefcase',
        'fa-shield-halved',
    ];
    $obligorIcons = [
        'fa-city',
        'fa-landmark-dome',
        'fa-chart-mixed',
        'fa-code-merge',
        'fa-coins',
        'fa-heart-pulse',
        'fa-file-invoice-dollar',
    ];
    $criteriaIcons = [
        'fa-wallet',
        'fa-chart-column',
        'fa-user-group',
    ];
    $legacyApproachBody = array_values(array_filter(
        (array) ($approachSection['body'] ?? []),
        static fn ($paragraph): bool => trim((string) $paragraph) !== '',
    ));
    $approachIntro = trim((string) ($approachSection['intro'] ?? ''));
    if ($legacyApproachBody === [] && $approachIntro !== '') {
        $legacyApproachBody = [$approachIntro];
    }
    $approachBodyHtml = array_key_exists('body_html', $approachSection)
        ? trim((string) $approachSection['body_html'])
        : \App\Support\Content\StructuredRichText::fromParagraphs($legacyApproachBody);
    $approachBlocks = \App\Support\Content\StructuredRichText::blocks($approachBodyHtml);
    $meetingTitle = trim((string) ($meetingSection['title'] ?? ''));
    $meetingIntro = trim((string) ($meetingSection['intro'] ?? ''));
    $meetingCardTitle = trim((string) ($meetingSection['contact_title'] ?? ''));
    $meetingButtonLabel = trim((string) ($meetingSection['button_label'] ?? ''));
    $meetingStatus = trim((string) ($meetingSection['status'] ?? ''));
    $heroLabel = trim((string) ($heroSection['subtitle_lead'] ?? ''));
    $heroHook = trim((string) ($heroSection['intro'] ?? ''));
    $heroImageAlt = trim((string) ($heroSection['image_alt'] ?? ''));
    $blogHeadingTitle = trim((string) ($blogSection['title'] ?? ''));
    $allPostsLabel = trim((string) ($blogSection['all_posts_label'] ?? ''));
    $postActionLabel = trim((string) ($blogSection['post_action_label'] ?? ''));
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
    $heroImageUrl = $sameOriginAssetUrl((string) $heroBackgroundUrl);
    $headingWords = static fn (string $heading): array => preg_split('/\s+/u', trim($heading)) ?: [];

    $hasAuditPosts = ($auditPosts ?? collect())->isNotEmpty();
@endphp

@section('title', $servicePageMetaTitle)
@section('main_class', 'w-full px-0 py-0')

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/audit.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/audit.css')) }}">
@endpush

@section('content')
    <div class="ac-audit-page">
        <section class="ac-audit-hero" id="vrh" aria-labelledby="ac-audit-hero-title">
            <div class="ac-audit-hero-media">
                <img
                    src="{{ $heroImageUrl }}"
                    alt="{{ $heroImageAlt }}"
                    class="ac-audit-hero-image"
                    width="1366"
                    height="768"
                    loading="eager"
                    decoding="async"
                    fetchpriority="high"
                >
            </div>
            <div class="ac-audit-hero-overlay" aria-hidden="true"></div>

            <div class="ac-audit-hero-shell">
                <div class="ac-audit-hero-copy">
                    <h1 id="ac-audit-hero-title" aria-label="{{ $heroLabel }}. {{ $heroHook }}">
                        <span class="ac-audit-hero-label">{{ $heroLabel }}</span>
                        <span class="ac-audit-hero-hook">{{ $heroHook }}</span>
                    </h1>
                </div>
            </div>
        </section>

        <section class="ac-audit-intro" id="audit-overview" aria-labelledby="ac-audit-overview-title">
            <div class="ac-audit-wide-shell ac-audit-intro-grid">
                <div class="ac-audit-intro-heading">
                    <h2 id="ac-audit-overview-title" data-words-slide-from-right aria-label="{{ $overviewSection['title'] ?? '' }}">
                        @foreach ($headingWords((string) ($overviewSection['title'] ?? '')) as $word)
                            <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>
                </div>

                <div class="ac-audit-intro-copy content-reveal animation-index-1" data-image-reveal>
                    @foreach ($overviewBlocks as $block)
                        {!! $loop->last
                            ? \App\Support\Content\StructuredRichText::addClassToFirstBlock((string) $block, 'is-emphasis')
                            : $block !!}
                    @endforeach
                </div>
            </div>
        </section>

        @if ($obligorsIntro !== '' || $useObligorsList || $obligorsNote !== '')
            <section class="ac-audit-obligors" aria-labelledby="ac-audit-obligors-title">
                <div class="ac-audit-wide-shell ac-audit-obligors-grid">
                    <div class="ac-audit-obligors-heading">
                        <h2 id="ac-audit-obligors-title" data-words-slide-from-right aria-label="{{ $obligorsSection['title'] ?? '' }}">
                            @foreach ($headingWords((string) ($obligorsSection['title'] ?? '')) as $word)
                                <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                            @endforeach
                        </h2>
                    </div>

                    @if ($obligorsNote !== '')
                        <aside class="ac-audit-obligors-note content-reveal animation-index-1" data-image-reveal>
                            <p>{{ $obligorsNote }}</p>
                        </aside>
                    @endif

                    <div class="ac-audit-obligors-content content-reveal animation-index-1" data-image-reveal>
                        @if ($useObligorsList)
                            @if ($obligorsPrimaryTitle !== '')
                                <h3>{{ $obligorsPrimaryTitle }}</h3>
                            @endif

                            <ul class="ac-audit-obligors-list">
                                @foreach ($obligorsPrimaryItems as $item)
                                    @php
                                        $itemText = is_array($item) ? trim((string) ($item['text'] ?? '')) : trim((string) $item);
                                        $children = is_array($item) ? array_values((array) ($item['children'] ?? [])) : [];
                                    @endphp

                                    @if ($itemText !== '')
                                        <li class="ac-audit-obligor-card {{ $children !== [] ? 'ac-audit-obligor-card--wide' : '' }}">
                                            <span class="ac-audit-obligor-icon" aria-hidden="true">
                                                <i class="fa-duotone fa-thin fa-fw {{ $obligorIcons[$loop->index] ?? 'fa-circle-check' }}"></i>
                                            </span>
                                            <div class="ac-audit-obligor-copy">
                                                <span class="ac-audit-obligor-title">{{ $itemText }}</span>

                                                @if ($children !== [])
                                                    <ul class="ac-audit-obligor-criteria">
                                                        @foreach ($children as $child)
                                                            @if (trim((string) $child) !== '')
                                                                <li>
                                                                    <i class="fa-duotone fa-thin fa-fw {{ $criteriaIcons[$loop->index] ?? 'fa-badge-check' }}" aria-hidden="true"></i>
                                                                    <span>{{ $child }}</span>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @elseif ($obligorsIntro !== '')
                            <p>{{ $obligorsIntro }}</p>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        <section class="ac-audit-services" id="audit-services" aria-labelledby="ac-audit-services-title">
            <div class="ac-audit-wide-shell">
                <header class="ac-audit-section-heading">
                    <h2 id="ac-audit-services-title" data-words-slide-from-right aria-label="{{ $servicesSection['title'] ?? '' }}">
                        @foreach ($headingWords((string) ($servicesSection['title'] ?? '')) as $word)
                            <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>
                    @if (trim((string) ($servicesSection['intro'] ?? '')) !== '')
                        <p>{{ $servicesSection['intro'] }}</p>
                    @endif
                </header>

                <div class="ac-audit-services-grid">
                    @foreach ($auditServices as $item)
                        <article class="ac-audit-service-card content-reveal animation-index-{{ $loop->index }}" data-image-reveal>
                            <span class="ac-audit-service-icon" aria-hidden="true">
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
            <section class="ac-audit-approach" aria-labelledby="ac-audit-approach-title">
                <div class="ac-audit-wide-shell ac-audit-approach-grid">
                    <div class="ac-audit-approach-heading">
                        <h2 id="ac-audit-approach-title" data-words-slide-from-right aria-label="{{ $approachSection['title'] ?? '' }}">
                            @foreach ($headingWords((string) ($approachSection['title'] ?? '')) as $word)
                                <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                            @endforeach
                        </h2>
                    </div>

                    <blockquote class="ac-audit-approach-quote content-reveal animation-index-1" data-image-reveal>
                        <i class="fa-duotone fa-thin fa-quote-left" aria-hidden="true"></i>
                        @foreach ($approachBlocks as $block)
                            {!! $block !!}
                        @endforeach
                    </blockquote>
                </div>
            </section>
        @endif

        @if ($hasAuditPosts)
            <section class="news-section ac-audit-news" aria-labelledby="ac-audit-news-title">
                <div class="news-shell">
                    <header class="news-header">
                        <div class="ac-audit-news-heading-copy">
                            <h2 class="news-title" id="ac-audit-news-title" data-words-slide-from-right aria-label="{{ $blogHeadingTitle }}">
                                @foreach ($headingWords($blogHeadingTitle) as $word)
                                    <span class="news-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                                @endforeach
                            </h2>

                        </div>

                        <a class="news-all-link content-reveal" data-image-reveal href="{{ $auditArchiveUrl }}">
                            <span>{{ $allPostsLabel }}</span>
                            <i class="fa-duotone fa-thin fa-arrow-right fa-fw" aria-hidden="true"></i>
                        </a>
                    </header>

                    <div class="news-grid">
                        @foreach ($auditPosts->take(3) as $post)
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
                                $categoryLabel = trim((string) ($categoryTranslation?->name ?? $auditCategoryName ?? ''));
                            @endphp

                            <a class="news-card animation-index-{{ $loop->index }}" data-image-reveal href="{{ $postUrl }}" aria-label="{{ $postTitle }}">
                                <span class="news-card-category">{{ $categoryLabel }}</span>
                                <h3>{{ $postTitle }}</h3>
                                <p>{{ $postExcerpt }}</p>
                                <span class="news-card-link" aria-hidden="true">
                                    {{ $postActionLabel }}
                                    <i class="fa-duotone fa-thin fa-arrow-right fa-fw"></i>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="contact-cta ac-audit-contact-cta" aria-labelledby="ac-audit-contact-title">
            <div class="contact-cta-shell">
                <div class="contact-cta-copy">
                    <h2 class="contact-cta-title" id="ac-audit-contact-title" data-words-slide-from-right aria-label="{{ $meetingTitle }}">
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
