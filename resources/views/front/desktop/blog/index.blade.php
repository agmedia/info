@extends('front.desktop.layouts.store')

@php
    $blogSettings = $storeSettings['blog'] ?? [];
    $defaultHeroTitle = trim((string) ($blogSettings['hero_title'] ?? '')) ?: __('ui.blog.title');
    $heroIntro = trim((string) ($blogSettings['hero_intro'] ?? '')) ?: __('ui.blog.subtitle');
    $heroCtaLabel = trim((string) ($blogSettings['hero_cta_label'] ?? ''));
    $heroCtaUrl = \App\Support\Localization\FrontendRoute::localizeUrl(
        trim((string) ($blogSettings['hero_cta_url'] ?? ''))
    );
    $categoryPreviewLimit = max(1, (int) ($blogSettings['category_preview_limit'] ?? 8));
    $activeCategoryIds = collect($selectedCategoryIds ?? [])->map(fn ($id) => (int) $id)->all();
    $fallbackActiveCategory = count($activeCategoryIds) === 1
        ? collect($selectedCategories ?? [])->first()
        : null;
    $currentCategoryName = trim((string) ($currentCategory['name'] ?? ($fallbackActiveCategory['name'] ?? '')));
    $isCategoryArchive = $currentCategoryName !== '';
    $heroTitle = $isCategoryArchive ? $currentCategoryName : $defaultHeroTitle;
    $hasMoreCategories = $categories->count() > $categoryPreviewLimit;
    $hiddenCategoryCount = max(0, $categories->count() - $categoryPreviewLimit);
    $baseIndexUrl = route('blog.index').($searchTerm !== '' ? '?'.http_build_query(['q' => $searchTerm]) : '');
    $hasSelectedHiddenCategory = $categories
        ->slice($categoryPreviewLimit)
        ->contains(fn (array $category): bool => in_array((int) $category['id'], $activeCategoryIds, true));
    $headingWords = static fn (string $title): array => preg_split('/\s+/u', trim($title), -1, PREG_SPLIT_NO_EMPTY) ?: [];
@endphp

@section('title', $heroTitle !== '' ? $heroTitle : __('ui.blog.page_title'))
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-blog-page">
        <section class="values-section services-index-intro ac-blog-intro" aria-labelledby="ac-blog-title">
            <div class="values-inner services-index-intro-layout ac-blog-intro-layout">
                <div class="values-intro">
                    <h1 class="values-title services-index-intro-title ac-blog-intro-title" id="ac-blog-title" data-words-slide-from-right aria-label="{{ $heroTitle }}">
                        @foreach ($headingWords($heroTitle) as $word)
                            <span class="values-word animation-index-{{ $loop->index }} {{ $loop->count > 1 && $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h1>
                </div>

                <div class="values-copy services-index-intro-copy ac-blog-intro-copy content-reveal" data-image-reveal>
                    @if ($heroIntro !== '')
                        <p>{{ $heroIntro }}</p>
                    @endif

                    @if ($heroCtaLabel !== '' && $heroCtaUrl !== '')
                        <a href="{{ $heroCtaUrl }}" class="services-index-inline-link ac-blog-intro-link">
                            <span>{{ $heroCtaLabel }}</span>
                            <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <section class="ac-blog-list-section" aria-label="{{ __('ui.blog.title') }}">
            <div class="ac-blog-container ac-blog-list-shell">
            @if ($categories->isNotEmpty())
                <section class="ac-blog-category-nav" aria-labelledby="ac-blog-category-nav-title">
                    <h2 class="visually-hidden" id="ac-blog-category-nav-title">{{ __('ui.blog.browse_categories') }}</h2>

                    <div class="front-scroll-rail ac-blog-category-rail {{ $hasMoreCategories ? 'has-more-items' : '' }}">
                        <div class="front-scroll-rail-track">
                            <a
                                href="{{ $baseIndexUrl }}"
                                class="ac-blog-category-chip {{ $activeCategoryIds === [] ? 'is-active' : '' }}"
                            >
                                <span>{{ __('ui.blog.all_posts') }}</span>
                            </a>

                            @foreach ($categories->take($categoryPreviewLimit) as $category)
                                @php
                                    $categoryUrl = trim((string) $category['slug']) !== ''
                                        ? url('/blog/'.$category['slug']).($searchTerm !== '' ? '?'.http_build_query(['q' => $searchTerm]) : '')
                                        : $baseIndexUrl;
                                @endphp
                                <a
                                    href="{{ $categoryUrl }}"
                                    class="ac-blog-category-chip {{ in_array((int) $category['id'], $activeCategoryIds, true) ? 'is-active' : '' }}"
                                >
                                    <span>{{ $category['name'] }}</span>
                                    <span class="ac-blog-category-chip-count">{{ $category['count'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    @if ($hasMoreCategories)
                        <details class="ac-blog-filter-more ac-blog-category-more" @open($hasSelectedHiddenCategory)>
                            <summary class="ac-blog-filter-more-toggle ac-blog-category-more-toggle">
                                <span class="ac-blog-category-more-count">+{{ $hiddenCategoryCount }}</span>
                                <span class="label-more">{{ __('ui.blog.filters.show_more') }}</span>
                                <span class="label-less">{{ __('ui.blog.filters.show_less') }}</span>
                            </summary>
                            <div class="front-scroll-rail mt-3">
                                <div class="front-scroll-rail-track">
                                    @foreach ($categories->slice($categoryPreviewLimit) as $category)
                                        @php
                                            $categoryUrl = trim((string) $category['slug']) !== ''
                                                ? url('/blog/'.$category['slug']).($searchTerm !== '' ? '?'.http_build_query(['q' => $searchTerm]) : '')
                                                : $baseIndexUrl;
                                        @endphp
                                        <a
                                            href="{{ $categoryUrl }}"
                                            class="ac-blog-category-chip {{ in_array((int) $category['id'], $activeCategoryIds, true) ? 'is-active' : '' }}"
                                        >
                                            <span>{{ $category['name'] }}</span>
                                            <span class="ac-blog-category-chip-count">{{ $category['count'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </details>
                    @endif
                </section>
            @endif

            @if ($topBlocks->isNotEmpty())
                <section class="mb-8">
                    @include('components.content-placement', ['items' => $topBlocks])
                </section>
            @endif

            <section class="ac-blog-content">
                @if ($posts->isEmpty())
                    <div class="ac-blog-empty">
                        <p>{{ __('ui.blog.empty') }}</p>
                    </div>
                @else
                    <div class="ac-blog-grid ac-blog-grid--index">
                        @foreach ($posts as $post)
                            @include('front.desktop.blog.partials.card', [
                                'post' => $post,
                                'locale' => $locale,
                                'fallbackLocale' => $fallbackLocale,
                                'revealIndex' => $loop->index % 3,
                            ])
                        @endforeach
                    </div>

                    <div class="ac-blog-pagination">
                        {{ $posts->links() }}
                    </div>
                @endif
            </section>

            @if ($bottomBlocks->isNotEmpty())
                <section class="mt-10">
                    @include('components.content-placement', ['items' => $bottomBlocks])
                </section>
            @endif
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/blog.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/blog.css')) }}">
@endpush
