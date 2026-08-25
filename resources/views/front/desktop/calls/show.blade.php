@extends('front.desktop.layouts.store')

@php
    use Illuminate\Support\Str;

    $translation = $callPost->translations->firstWhere('locale', $locale)
        ?? $callPost->translations->firstWhere('locale', $fallbackLocale)
        ?? $callPost->translations->first();
    $mediaItems = $callPost->relationLoaded('media')
        ? $callPost->media
            ->sortBy(static fn ($mediaItem) => (int) ($mediaItem->order_column ?? 0))
            ->values()
        : collect();
    $coverImage = $mediaItems->firstWhere('collection_name', 'call_cover') ?? $callPost->getFirstMedia('call_cover');
    $coverImageUrl = $coverImage ? $coverImage->getUrl() : null;
    $galleryItems = $mediaItems->where('collection_name', 'call_gallery')->values();
    if ($galleryItems->isEmpty()) {
        $galleryItems = $callPost->getMedia('call_gallery')
            ->sortBy(static fn ($mediaItem) => (int) ($mediaItem->order_column ?? 0))
            ->values();
    }
    $bodyHtml = (string) ($callPostBodyHtml ?? $translation?->body_html ?? '');
    $excerpt = trim((string) ($translation?->excerpt ?? ''));
    $callCategories = $callPost->categories
        ->sortByDesc(fn ($category) => (int) ($category->pivot->is_primary ?? false))
        ->values();
    $articleTitle = trim((string) ($translation?->title ?? $callPost->code));
    $publishedLabel = ($callPost->published_at ?? $callPost->created_at)?->translatedFormat('j. F Y.');
    $publishedAt = $callPost->published_at ?? $callPost->created_at;
    $callDetailUi = (array) ($callDetailUi ?? []);
    $euFundsLabel = trim((string) ($callDetailUi['eu_funds_label'] ?? ''));
    $callsLabel = trim((string) ($callDetailUi['calls_label'] ?? ''));
    $emptyBodyCopy = trim((string) ($callDetailUi['empty_body_copy'] ?? ''));
    $galleryCount = $galleryItems->count();
    $galleryColumnsClass = match (true) {
        $galleryCount <= 1 => 'grid-cols-1',
        $galleryCount === 2 || $galleryCount === 4 => 'grid-cols-1 md:grid-cols-2',
        default => 'grid-cols-1 md:grid-cols-3',
    };
    $headingWords = static fn (string $title): array => preg_split('/\s+/u', trim($title), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $meetingUi = (array) ($callDetailUi['meeting'] ?? []);
    $callCtaTitle = trim((string) ($meetingUi['title'] ?? ''));
    $callCtaCardTitle = trim((string) ($meetingUi['contact_title'] ?? ''));
    $callCtaCardCopy = trim((string) ($meetingUi['intro'] ?? ''));
    $callCtaButton = trim((string) ($meetingUi['button_label'] ?? ''));
    $callCtaStatus = trim((string) ($meetingUi['status'] ?? ''));
    $showCallCtaCard = $callCtaCardTitle !== '' || $callCtaCardCopy !== '' || $callCtaButton !== '' || $callCtaStatus !== '';
    $showCallCta = $callCtaTitle !== '' || $showCallCtaCard;
    $pageTitleBreadcrumbs = collect([
        ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
        $euFundsLabel !== '' ? ['label' => $euFundsLabel, 'url' => \App\Support\Localization\FrontendRoute::url('eu-funds.show')] : null,
        $callsLabel !== '' ? ['label' => $callsLabel, 'url' => \App\Support\Localization\FrontendRoute::url('eu-funds.show').'#eu-funds-calls'] : null,
        [
            'label' => Str::limit($articleTitle, 72, '...'),
            'current' => true,
            'current_class' => 'ac-blog-breadcrumb-current',
            'title' => $articleTitle,
        ],
    ])->filter()->values()->all();
@endphp

@section('title', $translation?->meta_title ?: $articleTitle)
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-blog-page ac-blog-article-page ac-call-article-page">
        <x-front.page-title-band
            :breadcrumbs="$pageTitleBreadcrumbs"
            section-class="ac-blog-article-intro"
            container-class="ac-blog-container"
            hero-class="ac-blog-article-intro-hero"
            panel-class="ac-blog-article-intro-grid"
            breadcrumb-class="ac-blog-article-breadcrumb"
        >
            <h1 class="ac-blog-article-title content-reveal animation-index-1" id="ac-call-article-title" data-image-reveal>{{ $articleTitle }}</h1>

            <div class="ac-blog-article-meta content-reveal animation-index-2" data-image-reveal>
                @if ($publishedLabel)
                    <time datetime="{{ $publishedAt?->toDateString() }}">{{ $publishedLabel }}</time>
                @endif

                @foreach ($callCategories as $category)
                    @php
                        $categoryTranslation = $category->translations->firstWhere('locale', $locale)
                            ?? $category->translations->firstWhere('locale', $fallbackLocale)
                            ?? $category->translations->first();
                    @endphp
                    <a href="{{ \App\Support\Localization\FrontendRoute::url('eu-funds.show') }}#eu-funds-calls">{{ $categoryTranslation?->name ?? $category->code }}</a>
                @endforeach
            </div>
        </x-front.page-title-band>

        <article class="ac-blog-article-body">
            <div class="ac-blog-container ac-blog-article-shell ac-blog-post-article-shell">
                @if ($coverImageUrl)
                    <figure class="ac-blog-article-cover content-reveal" data-image-reveal>
                        <div class="image-reveal-media">
                            <img
                                src="{{ $coverImageUrl }}"
                                alt="{{ $articleTitle }}"
                                loading="eager"
                                decoding="async"
                                fetchpriority="high"
                            >
                            <span class="image-reveal-curtain" aria-hidden="true"></span>
                        </div>
                    </figure>
                @endif

                <div class="ac-blog-article-body-inner content-reveal animation-index-1" data-image-reveal>
                    <div class="content-richtext">
                        @if ($bodyHtml !== '')
                            {!! $bodyHtml !!}
                        @elseif ($excerpt !== '')
                            <p>{{ $excerpt }}</p>
                        @elseif ($emptyBodyCopy !== '')
                            <p>{{ $emptyBodyCopy }}</p>
                        @endif
                    </div>
                </div>

                @if ($galleryItems->isNotEmpty())
                    <section class="ac-blog-article-gallery" aria-label="{{ $articleTitle }}">
                        <div class="ac-blog-gallery-grid {{ $galleryColumnsClass }}" data-blog-gallery>
                        @foreach ($galleryItems as $mediaItem)
                            @php
                                $galleryImageUrl = $mediaItem->getUrl();
                            @endphp
                            <a
                                href="{{ $galleryImageUrl }}"
                                class="ac-blog-gallery-item"
                                data-blog-gallery-item
                                data-sub-html="{{ $articleTitle }}"
                            >
                                <img
                                    src="{{ $galleryImageUrl }}"
                                    alt="{{ $articleTitle }}"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </a>
                        @endforeach
                    </div>
                    </section>
                @endif
            </div>
        </article>

        @if ($showCallCta)
        <section class="contact-cta ac-blog-contact-cta" @if ($callCtaTitle !== '') aria-labelledby="ac-call-contact-cta-title" @endif>
            <div class="contact-cta-shell">
                @if ($callCtaTitle !== '')
                <div class="contact-cta-copy">
                    <h2 class="contact-cta-title" id="ac-call-contact-cta-title" data-words-slide-from-right aria-label="{{ $callCtaTitle }}">
                        @foreach ($headingWords($callCtaTitle) as $word)
                            <span class="contact-cta-title-word animation-index-{{ $loop->index }} {{ $loop->remaining < 2 ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h2>
                </div>
                @endif

                @if ($showCallCtaCard)
                <div class="contact-cta-card" data-image-reveal>
                    @if ($callCtaCardTitle !== '')
                        <div class="contact-cta-card-heading"><span>{{ $callCtaCardTitle }}</span></div>
                    @endif
                    @if ($callCtaCardCopy !== '')
                        <p>{{ $callCtaCardCopy }}</p>
                    @endif
                    @if ($callCtaButton !== '')
                    <a class="contact-cta-button" href="{{ \App\Support\Localization\FrontendRoute::url('contact.create') }}">
                        <span>{{ $callCtaButton }}</span>
                        <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                    </a>
                    @endif
                    @if ($callCtaStatus !== '')
                        <small><span class="contact-cta-status-dot" aria-hidden="true"></span>{{ $callCtaStatus }}</small>
                    @endif
                </div>
                @endif
            </div>
        </section>
        @endif
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/blog.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/blog.css')) }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/css/lightgallery-bundle.min.css">
@endpush

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.min.js"></script>
    <script defer src="{{ asset('front-theme/scripts/blog.js') }}?v={{ filemtime(public_path('front-theme/scripts/blog.js')) }}"></script>
@endpush
