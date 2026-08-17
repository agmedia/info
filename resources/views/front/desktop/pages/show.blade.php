@extends('front.desktop.layouts.store')

@php
    $translation = $selectedTranslation
        ?? $page->translations->firstWhere('locale', $locale)
        ?? $page->translations->firstWhere('locale', $fallbackLocale);
@endphp

@section('title', $translation?->title ?? 'Page')
@section('main_class', 'w-full px-0 py-0')

@section('content')
    @php
        $pageTitle = $translation?->title ?? $page->code;
        $pageTitleWords = preg_split('/\s+/u', trim((string) $pageTitle), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    @endphp

    <div class="ac-default-page">
        @if ($topBlocks->isNotEmpty())
            <section class="ac-default-blocks ac-default-blocks--top">
                @include('components.content-placement', ['items' => $topBlocks])
            </section>
        @endif

        <x-front.page-title-band
            section-class="ac-default-title-band"
            container-class="ac-default-title-container"
        >
            <div class="ac-default-title-layout">
                <div class="ac-page-title-copy">
                    <h1 class="values-title services-index-intro-title ac-default-page-title" data-words-slide-from-right aria-label="{{ $pageTitle }}">
                        @foreach ($pageTitleWords as $word)
                            <span class="values-word ac-default-title-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h1>
                </div>
            </div>
        </x-front.page-title-band>

        <section class="ac-default-content-section" aria-label="{{ $pageTitle }}">
            <div class="ac-default-container">
                <article class="ac-default-article content-reveal" data-image-reveal>
                    <div class="ac-page-body-inner">
                        <div class="content-richtext">
                            {!! $translation?->body_html ?: '' !!}
                        </div>
                    </div>
                </article>
            </div>
        </section>

        @if ($bottomBlocks->isNotEmpty())
            <section class="ac-default-blocks ac-default-blocks--bottom">
                @include('components.content-placement', ['items' => $bottomBlocks])
            </section>
        @endif
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/default.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/default.css')) }}">
@endpush
