@extends('front.desktop.layouts.store')

@php
    $pageTitle = trim((string) __('resources.page_title'));
    $headingWords = preg_split('/\s+/u', $pageTitle, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $isCroatian = str_starts_with(strtolower((string) app()->getLocale()), 'hr');
    $sectionKicker = $isCroatian ? 'Stručni materijali' : 'Expert resources';
    $countLabel = $isCroatian ? 'dokumenata' : 'documents';
@endphp

@section('title', $pageTitle)
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-resources-page ac-resources-index-page">
        <section class="values-section services-index-intro ac-resources-intro" aria-labelledby="ac-resources-title">
            <div class="values-inner services-index-intro-layout ac-resources-intro-layout">
                <div class="values-intro">
                    <h1 class="values-title services-index-intro-title ac-resources-intro-title" id="ac-resources-title" data-words-slide-from-right aria-label="{{ $pageTitle }}">
                        @foreach ($headingWords as $word)
                            <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h1>
                </div>

                <div class="values-copy services-index-intro-copy ac-resources-intro-copy content-reveal" data-image-reveal>
                    <p class="ac-resources-kicker">{{ $sectionKicker }}</p>
                    <p>{{ __('resources.index.intro') }}</p>
                </div>
            </div>
        </section>

        <div class="ac-resources-groups">
            @forelse ($groups as $group)
                <section class="ac-resources-group" aria-labelledby="ac-resources-group-{{ $group['code'] }}">
                    <div class="ac-resources-container">
                        <header class="ac-resources-group-head">
                            <div class="ac-resources-group-title content-reveal" data-image-reveal>
                                <p class="ac-resources-kicker">{{ $sectionKicker }}</p>
                                <h2 id="ac-resources-group-{{ $group['code'] }}">{{ $group['label'] }}</h2>
                                <p class="ac-resources-count"><strong>{{ number_format($group['items']->count()) }}</strong> {{ $countLabel }}</p>
                            </div>

                            <div class="ac-resources-group-copy content-reveal animation-index-1" data-image-reveal>
                                <p>{{ $group['description'] }}</p>
                            </div>
                        </header>

                        <div class="ac-resources-grid">
                            @foreach ($group['items'] as $item)
                                @include('front.desktop.resources.partials.card', [
                                    'item' => $item,
                                    'revealIndex' => $loop->index,
                                ])
                            @endforeach
                        </div>
                    </div>
                </section>
            @empty
                <section class="ac-resources-empty-section">
                    <div class="ac-resources-container">
                        <div class="ac-resources-empty">
                            <i class="fa-duotone fa-thin fa-folder-open" aria-hidden="true"></i>
                            <p>{{ __('resources.index.empty') }}</p>
                        </div>
                    </div>
                </section>
            @endforelse
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/resources.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/resources.css')) }}">
@endpush
