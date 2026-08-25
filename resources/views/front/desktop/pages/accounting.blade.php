@extends('front.desktop.layouts.store')

@php
    $legacyOverviewBody = array_values(array_filter(
        (array) ($overviewSection['body'] ?? []),
        static fn ($paragraph): bool => trim((string) $paragraph) !== '',
    ));
    $accountingServices = array_values(array_filter(
        (array) ($servicesSection['items'] ?? []),
        static fn ($item): bool => is_array($item) && trim((string) ($item['title'] ?? '')) !== '',
    ));
    $serviceIcons = [
        'fa-book-copy',
        'fa-badge-percent',
        'fa-user-tie-hair',
        'fa-file-certificate',
        'fa-chart-waterfall',
        'fa-building-shield',
    ];
    $legacyApproachBody = array_values(array_filter(
        (array) ($approachSection['body'] ?? []),
        static fn ($paragraph): bool => trim((string) $paragraph) !== '',
    ));
    $approachIntro = trim((string) ($approachSection['intro'] ?? ''));
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
    $legacyParagraphHtml = static function (mixed $paragraph): string {
        $paragraph = trim((string) $paragraph);
        if ($paragraph === '') {
            return '';
        }

        return '<p>'.nl2br(e($paragraph), false).'</p>';
    };
    $legacyParagraphsHtml = static function (array $paragraphs) use ($legacyParagraphHtml): string {
        return collect($paragraphs)
            ->map(static fn ($paragraph): string => $legacyParagraphHtml($paragraph))
            ->filter()
            ->implode('');
    };
    $normalizeRichHtml = static function (mixed $html): string {
        $html = trim((string) $html);
        if ($html === '') {
            return '';
        }

        $plainText = trim(html_entity_decode(strip_tags(str_ireplace(['<br>', '<br/>', '<br />'], '', $html))));

        return $plainText !== '' ? $html : '';
    };
    $richTextBlocks = static function (string $html) use ($normalizeRichHtml): array {
        $html = $normalizeRichHtml($html);

        return $html === ''
            ? []
            : \App\Support\Content\StructuredRichText::blocks($html);
    };
    $overviewBodyHtml = array_key_exists('body_html', $overviewSection)
        ? $normalizeRichHtml($overviewSection['body_html'])
        : $legacyParagraphHtml($overviewSection['intro'] ?? '')
            .$legacyParagraphHtml($legacyOverviewBody[0] ?? '');
    $partnerBodyHtml = array_key_exists('partner_body_html', $overviewSection)
        ? $normalizeRichHtml($overviewSection['partner_body_html'])
        : $legacyParagraphsHtml(array_slice($legacyOverviewBody, 1));
    $approachBodyHtml = array_key_exists('body_html', $approachSection)
        ? $normalizeRichHtml($approachSection['body_html'])
        : $legacyParagraphsHtml(
            $legacyApproachBody !== []
                ? $legacyApproachBody
                : [$approachIntro],
        );
    $overviewBodyBlocks = $richTextBlocks($overviewBodyHtml);
    $partnerBodyBlocks = $richTextBlocks($partnerBodyHtml);
    $approachBodyBlocks = $richTextBlocks($approachBodyHtml);
    $overviewTitle = trim((string) ($overviewSection['title'] ?? ''));
    $overviewTitleBreakIndex = count($headingWords($overviewTitle)) > 3 ? 3 : null;
    $hasAccountingPosts = ($accountingPosts ?? collect())->isNotEmpty();
@endphp

@section('title', $servicePageMetaTitle)
@section('main_class', 'w-full px-0 py-0')

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/audit.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/audit.css')) }}">
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/accounting.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/accounting.css')) }}">
@endpush

@section('content')
    <div class="ac-audit-page ac-accounting-page">
        <section class="ac-audit-hero" id="vrh" aria-labelledby="ac-accounting-hero-title">
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
                    <h1 id="ac-accounting-hero-title" aria-label="{{ $heroLabel }}. {{ $heroHook }}">
                        <span class="ac-audit-hero-label">{{ $heroLabel }}</span>
                        <span class="ac-audit-hero-hook">{{ $heroHook }}</span>
                    </h1>
                </div>
            </div>
        </section>

        <section class="ac-audit-intro" id="accounting-overview" aria-labelledby="ac-accounting-overview-title">
            <div class="ac-audit-wide-shell ac-audit-intro-grid">
                <div class="ac-audit-intro-heading">
                    <h2
                        id="ac-accounting-overview-title"
                        class="{{ $overviewTitleBreakIndex !== null ? 'has-fixed-two-lines' : '' }}"
                        data-words-slide-from-right
                        aria-label="{{ $overviewTitle }}"
                    >
                        @foreach ($headingWords($overviewTitle) as $word)
                            @if ($overviewTitleBreakIndex === $loop->index)
                                <br aria-hidden="true">
                            @endif
                            <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>
                </div>

                <div class="ac-audit-intro-copy content-reveal animation-index-1" data-image-reveal>
                    @foreach ($overviewBodyBlocks as $block)
                        {!! $block !!}
                    @endforeach
                </div>
            </div>
        </section>

        @if ($partnerBodyBlocks !== [])
            <section
                class="ac-audit-obligors ac-accounting-partner-note"
                aria-label="{{ $overviewTitle }}"
            >
                <div class="ac-audit-wide-shell ac-accounting-partner-note-shell">
                    <blockquote class="ac-accounting-partner-note-quote content-reveal" data-image-reveal>
                        <i class="fa-duotone fa-thin fa-quote-left" aria-hidden="true"></i>
                        @foreach ($partnerBodyBlocks as $block)
                            {!! \App\Support\Content\StructuredRichText::addClassToFirstBlock(
                                $block,
                                'ac-accounting-partner-note-text',
                            ) !!}
                        @endforeach
                    </blockquote>
                </div>
            </section>
        @endif

        <section class="ac-audit-services" id="accounting-services" aria-labelledby="ac-accounting-services-title">
            <div class="ac-audit-wide-shell">
                <header class="ac-audit-section-heading">
                    <h2 id="ac-accounting-services-title" data-words-slide-from-right aria-label="{{ $servicesSection['title'] ?? '' }}">
                        @foreach ($headingWords((string) ($servicesSection['title'] ?? '')) as $word)
                            <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>
                    @if (trim((string) ($servicesSection['intro'] ?? '')) !== '')
                        <p>{{ $servicesSection['intro'] }}</p>
                    @endif
                </header>

                <div class="ac-audit-services-grid">
                    @foreach ($accountingServices as $item)
                        <article class="ac-audit-service-card content-reveal animation-index-{{ $loop->index }}" data-image-reveal>
                            <span class="ac-audit-service-icon" aria-hidden="true">
                                <i class="fa-duotone fa-thin fa-fw {{ $serviceIcons[$loop->index] ?? 'fa-calculator' }}"></i>
                            </span>
                            <h3>{{ $item['title'] ?? '' }}</h3>
                            <p>{{ $item['text'] ?? '' }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        @if ($approachBodyBlocks !== [])
            <section class="ac-audit-approach" aria-labelledby="ac-accounting-approach-title">
                <div class="ac-audit-wide-shell ac-audit-approach-grid">
                    <div class="ac-audit-approach-heading">
                        <h2 id="ac-accounting-approach-title" data-words-slide-from-right aria-label="{{ $approachSection['title'] ?? '' }}">
                            @foreach ($headingWords((string) ($approachSection['title'] ?? '')) as $word)
                                <span class="service-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                            @endforeach
                        </h2>
                    </div>

                    <blockquote class="ac-audit-approach-quote content-reveal animation-index-1" data-image-reveal>
                        <i class="fa-duotone fa-thin fa-quote-left" aria-hidden="true"></i>
                        @foreach ($approachBodyBlocks as $block)
                            {!! $block !!}
                        @endforeach
                    </blockquote>
                </div>
            </section>
        @endif

        @if ($hasAccountingPosts)
            <section class="news-section ac-audit-news" aria-labelledby="ac-accounting-news-title">
                <div class="news-shell">
                    <header class="news-header">
                        <div class="ac-audit-news-heading-copy">
                            <h2 class="news-title" id="ac-accounting-news-title" data-words-slide-from-right aria-label="{{ $blogHeadingTitle }}">
                                @foreach ($headingWords($blogHeadingTitle) as $word)
                                    <span class="news-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                                @endforeach
                            </h2>
                        </div>

                        <a class="news-all-link content-reveal" data-image-reveal href="{{ $accountingArchiveUrl }}">
                            <span>{{ $allPostsLabel }}</span>
                            <i class="fa-duotone fa-thin fa-arrow-right fa-fw" aria-hidden="true"></i>
                        </a>
                    </header>

                    <div class="news-grid">
                        @foreach ($accountingPosts->take(3) as $post)
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
                                $categoryLabel = trim((string) ($categoryTranslation?->name ?? $accountingCategoryName ?? ''));
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

        <section class="contact-cta ac-audit-contact-cta" aria-labelledby="ac-accounting-contact-title">
            <div class="contact-cta-shell">
                <div class="contact-cta-copy">
                    <h2 class="contact-cta-title" id="ac-accounting-contact-title" data-words-slide-from-right aria-label="{{ $meetingTitle }}">
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
