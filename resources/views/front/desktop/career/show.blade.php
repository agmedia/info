@extends('front.desktop.layouts.store')

@php
    use Illuminate\Support\Str;

    $translation = $jobOpeningTranslation;
    $articleTitle = trim((string) ($translation->title ?? $jobOpening->code));
    $locations = trim((string) ($translation->locations ?? ''));
    $bodyHtml = trim((string) ($translation->body_html ?? ''));
    $excerpt = trim((string) ($translation->excerpt ?? ''));
    $publishedAt = $jobOpening->published_at
        ?->copy()
        ->setTimezone(config('admin_ui.timezone', 'Europe/Zagreb'));
    $publishedLabel = $publishedAt
        ? \App\Support\Localization\FrontendDate::long($publishedAt, (string) $locale, config('admin_ui.timezone', 'Europe/Zagreb'))
        : '';
    $careerPageUrl = trim((string) ($careerPageUrl ?? '')) ?: route('home');
    $careerBackUrl = trim((string) ($careerBackUrl ?? '')) ?: $careerPageUrl;
    $breadcrumbs = [
        ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
        ['label' => __('career.openings.career'), 'url' => $careerPageUrl],
        [
            'label' => Str::limit($articleTitle, 72, '...'),
            'current' => true,
            'current_class' => 'ac-blog-breadcrumb-current',
            'title' => $articleTitle,
        ],
    ];
@endphp

@section('title', $translation->meta_title ?: $articleTitle)
@section('description', $translation->meta_description ?: $excerpt)
@section('main_class', 'w-full px-0 py-0')
@section('hide_footer_newsletter', '1')

@section('content')
    <div class="ac-career-page ac-blog-page ac-blog-article-page ac-career-opening-page">
        @if ($isAdminPreview ?? false)
            <div class="ac-blog-admin-preview" role="status">
                <div class="ac-blog-container ac-blog-admin-preview__inner">
                    <i class="fa-light fa-eye" aria-hidden="true"></i>
                    <strong>{{ __('Admin preview') }}</strong>
                    <span>{{ __('career.openings.preview_notice') }}</span>
                </div>
            </div>
        @endif

        <x-front.page-title-band
            :breadcrumbs="$breadcrumbs"
            section-class="ac-blog-article-intro ac-career-opening-intro"
            container-class="ac-blog-container"
            hero-class="ac-blog-article-intro-hero"
            panel-class="ac-blog-article-intro-grid ac-career-opening-intro-grid"
            breadcrumb-class="ac-blog-article-breadcrumb"
        >
            <div class="ac-career-opening-heading">
                <p class="ac-career-opening-eyebrow content-reveal" data-image-reveal>{{ __('career.openings.eyebrow') }}</p>
                <h1 class="ac-blog-article-title content-reveal animation-index-1" id="ac-career-opening-title" data-image-reveal>{{ $articleTitle }}</h1>
            </div>

            <div class="ac-blog-article-meta ac-career-opening-meta content-reveal animation-index-2" data-image-reveal>
                @if ($locations !== '')
                    <span>
                        <i class="fa-duotone fa-thin fa-location-dot" aria-hidden="true"></i>
                        <span>
                            <span class="sr-only">{{ __('career.openings.locations') }}:</span>
                            {{ $locations }}
                        </span>
                    </span>
                @endif
                @if ($publishedAt)
                    <time datetime="{{ $publishedAt->toDateString() }}">
                        <i class="fa-duotone fa-thin fa-calendar-days" aria-hidden="true"></i>
                        <span>
                            <span class="sr-only">{{ __('career.openings.published') }}:</span>
                            {{ $publishedLabel }}
                        </span>
                    </time>
                @endif
            </div>
        </x-front.page-title-band>

        <article class="ac-blog-article-body" aria-labelledby="ac-career-opening-title">
            <div class="ac-blog-container ac-blog-article-shell ac-blog-post-article-shell ac-career-opening-shell">
                <div class="ac-blog-article-body-inner ac-career-opening-body-inner content-reveal animation-index-1" data-image-reveal>
                    <div class="content-richtext">
                        @if ($bodyHtml !== '')
                            {!! $bodyHtml !!}
                        @elseif ($excerpt !== '')
                            <p>{{ $excerpt }}</p>
                        @endif
                    </div>

                    <a class="services-index-inline-link ac-career-opening-back" href="{{ $careerBackUrl }}">
                        <i class="fa-duotone fa-thin fa-arrow-left" aria-hidden="true"></i>
                        <span>{{ __('career.openings.back') }}</span>
                    </a>
                </div>
            </div>
        </article>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/career.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/career.css')) }}">
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/blog.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/blog.css')) }}">
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/career-opening.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/career-opening.css')) }}">
@endpush
