@extends('front.desktop.layouts.store')

@php
    $blogSettings = $storeSettings['blog'] ?? [];
    $defaultHeroTitle = trim((string) ($blogSettings['hero_title'] ?? '')) ?: __('ui.blog.title');
    $heroIntro = trim((string) ($blogSettings['hero_intro'] ?? '')) ?: __('ui.blog.subtitle');
    $heroCtaLabel = trim((string) ($blogSettings['hero_cta_label'] ?? ''));
    $heroCtaUrl = trim((string) ($blogSettings['hero_cta_url'] ?? ''));
    $categoryPreviewLimit = max(1, (int) ($blogSettings['category_preview_limit'] ?? 8));
    $activeCategoryIds = collect($selectedCategoryIds ?? [])->map(fn ($id) => (int) $id)->all();
    $fallbackActiveCategory = count($activeCategoryIds) === 1
        ? collect($selectedCategories ?? [])->first()
        : null;
    $currentCategoryName = trim((string) ($currentCategory['name'] ?? ($fallbackActiveCategory['name'] ?? '')));
    $isCategoryArchive = $currentCategoryName !== '';
    $heroTitle = $isCategoryArchive ? $currentCategoryName : $defaultHeroTitle;
    $hasMoreCategories = $categories->count() > $categoryPreviewLimit;
    $baseIndexUrl = route('blog.index').($searchTerm !== '' ? '?'.http_build_query(['q' => $searchTerm]) : '');
    $hasSelectedHiddenCategory = $categories
        ->slice($categoryPreviewLimit)
        ->contains(fn (array $category): bool => in_array((int) $category['id'], $activeCategoryIds, true));
    $pageTitleBreadcrumbs = [
        ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
    ];

    if ($isCategoryArchive) {
        $pageTitleBreadcrumbs[] = ['label' => __('ui.blog.title'), 'url' => route('blog.index')];
        $pageTitleBreadcrumbs[] = ['label' => $currentCategoryName, 'current' => true];
    } else {
        $pageTitleBreadcrumbs[] = ['label' => __('ui.blog.title'), 'current' => true];
    }
@endphp

@section('title', $heroTitle !== '' ? $heroTitle : __('ui.blog.page_title'))
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-blog-page">
        <x-front.page-title-band
            :breadcrumbs="$pageTitleBreadcrumbs"
            section-class="ac-blog-title-band"
            breadcrumb-class="ac-blog-hero-breadcrumb"
        >
            <div class="ac-page-title-copy">
                <h1 id="ac-blog-title">{{ $heroTitle }}</h1>

                @if ($heroIntro !== '')
                    <p>{{ $heroIntro }}</p>
                @endif

                @if ($heroCtaLabel !== '' && $heroCtaUrl !== '')
                    <div class="ac-page-title-actions ac-blog-hero-action">
                        <a href="{{ $heroCtaUrl }}" class="front-action-cta">
                            <span>{{ $heroCtaLabel }}</span>
                            <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M4 12L12 4"></path>
                                <path d="M6 4h6v6"></path>
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
        </x-front.page-title-band>

        <div class="mx-auto w-full max-w-[1320px] px-4 sm:px-6 lg:px-8">
            @if ($categories->isNotEmpty())
                <section class="ac-blog-category-nav" aria-labelledby="ac-blog-category-nav-title">
                    <h2 id="ac-blog-category-nav-title" class="sr-only">{{ __('ui.blog.browse_categories') }}</h2>
                    <div class="front-scroll-rail">
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
                            <summary class="ac-blog-filter-more-toggle">
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
                    <div class="ac-blog-grid">
                        @foreach ($posts as $post)
                            @include('front.desktop.blog.partials.card', [
                                'post' => $post,
                                'locale' => $locale,
                                'fallbackLocale' => $fallbackLocale,
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
    </div>
@endsection
