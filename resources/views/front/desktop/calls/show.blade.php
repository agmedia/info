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
    $euFundsLabel = str_starts_with(strtolower($locale), 'hr') ? 'EU fondovi' : 'EU Funds';
    $callsLabel = str_starts_with(strtolower($locale), 'hr') ? 'Pozivi' : 'Calls';
    $pageTitleBreadcrumbs = [
        ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
        ['label' => $euFundsLabel, 'url' => route('eu-funds.show')],
        ['label' => $callsLabel, 'url' => route('eu-funds.show').'#eu-funds-calls'],
        [
            'label' => Str::limit($articleTitle, 72, '...'),
            'current' => true,
            'current_class' => 'ac-blog-breadcrumb-current',
            'title' => $articleTitle,
        ],
    ];
@endphp

@section('title', $translation?->meta_title ?: $articleTitle)
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-blog-page ac-blog-article-page">
        <x-front.page-title-band
            :breadcrumbs="$pageTitleBreadcrumbs"
            section-class="ac-blog-title-band ac-blog-article-title-band"
            hero-class="ac-blog-article-hero"
            panel-class="ac-blog-article-panel"
            breadcrumb-class="ac-blog-hero-breadcrumb ac-blog-article-breadcrumb"
        >
            <div class="ac-blog-article-head">
                <h1 class="ac-blog-article-title">{{ $articleTitle }}</h1>

                <div class="ac-blog-article-meta">
                    @if ($publishedLabel)
                        <span class="ac-blog-article-chip is-date">{{ $publishedLabel }}</span>
                    @endif

                    @foreach ($callCategories as $category)
                        @php
                            $categoryTranslation = $category->translations->firstWhere('locale', $locale)
                                ?? $category->translations->firstWhere('locale', $fallbackLocale)
                                ?? $category->translations->first();
                        @endphp
                        <span class="ac-blog-article-chip">{{ $categoryTranslation?->name ?? $category->code }}</span>
                    @endforeach
                </div>
            </div>
        </x-front.page-title-band>

        <div class="mx-auto w-full max-w-[1320px] px-4 sm:px-6 lg:px-8">
            <article class="ac-blog-article-body">
                <div class="ac-blog-article-body-inner">
                    @if ($coverImageUrl)
                        <figure class="ac-blog-article-cover">
                            <img
                                src="{{ $coverImageUrl }}"
                                alt="{{ $articleTitle }}"
                                class="h-auto w-full object-cover"
                                loading="eager"
                                decoding="async"
                            >
                        </figure>
                    @endif

                    <div class="content-richtext">
                        @if ($bodyHtml !== '')
                            {!! $bodyHtml !!}
                        @elseif ($excerpt !== '')
                            <p>{{ $excerpt }}</p>
                        @else
                            <p>{{ str_starts_with(strtolower($locale), 'hr') ? 'Sadržaj ove stavke još nije dopunjen.' : 'Content for this call has not been added yet.' }}</p>
                        @endif
                    </div>
                </div>
            </article>

            @if ($galleryItems->isNotEmpty())
                <section class="ac-blog-article-gallery">
                    <div class="grid gap-5 grid-cols-1 md:grid-cols-3" data-blog-gallery>
                        @foreach ($galleryItems as $mediaItem)
                            @php
                                $galleryImageUrl = $mediaItem->getUrl();
                            @endphp
                            <a
                                href="{{ $galleryImageUrl }}"
                                class="block aspect-[3/4] overflow-hidden rounded-[18px] bg-slate-100"
                                data-blog-gallery-item
                                data-sub-html="{{ $articleTitle }}"
                            >
                                <img
                                    src="{{ $galleryImageUrl }}"
                                    alt="{{ $articleTitle }}"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="ac-inline-cta ac-inline-cta--blog" aria-labelledby="ac-call-inline-cta-title">
                <div class="ac-inline-cta-card ac-inline-cta-card--blog">
                    <div class="mx-auto grid w-full max-w-[860px] gap-4 py-5 md:grid-cols-[minmax(0,1fr)_auto] md:items-center">
                        <div class="ac-inline-cta-copy">
                            <h2 id="ac-call-inline-cta-title" class="ac-inline-cta-title">
                                <span>{{ str_starts_with(strtolower($locale), 'hr') ? 'Povratak na pregled poziva' : 'Back to calls overview' }}</span>
                            </h2>
                        </div>

                        <div class="ac-inline-cta-action">
                            <a href="{{ route('eu-funds.show') }}#eu-funds-calls" class="front-action-cta">
                                <span>{{ str_starts_with(strtolower($locale), 'hr') ? 'Pogledaj sve pozive' : 'View all calls' }}</span>
                                <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M4 12L12 4"></path>
                                    <path d="M6 4h6v6"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/css/lightgallery-bundle.min.css">
@endpush

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.min.js"></script>
    <script defer>
        document.addEventListener('DOMContentLoaded', function () {
            const galleryRoot = document.querySelector('[data-blog-gallery]');
            if (!galleryRoot || typeof window.lightGallery !== 'function') {
                return;
            }

            window.lightGallery(galleryRoot, {
                selector: '[data-blog-gallery-item]',
                download: false,
                counter: true,
            });
        });
    </script>
@endpush
