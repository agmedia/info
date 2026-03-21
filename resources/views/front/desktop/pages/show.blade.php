@extends('front.desktop.layouts.store')

@php
    $translation = $selectedTranslation
        ?? $page->translations->firstWhere('locale', $locale)
        ?? $page->translations->firstWhere('locale', $fallbackLocale);
@endphp

@section('title', $translation?->title ?? 'Page')
@section('main_class', 'w-full px-0 py-0 pb-[100px]')

@section('content')
    @php
        $pageTitleBreadcrumbs = [
            ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
            ['label' => $translation?->title ?? $page->code, 'current' => true],
        ];
    @endphp

    @if ($topBlocks->isNotEmpty())
        <section class="mx-auto mb-8 w-full max-w-[1320px] px-4 pt-10 sm:px-6 lg:px-8">@include('components.content-placement', ['items' => $topBlocks])</section>
    @endif

    <x-front.page-title-band :breadcrumbs="$pageTitleBreadcrumbs">
        <div class="ac-page-title-copy">
            <h1>{{ $translation?->title ?? $page->code }}</h1>
            @if (!empty($translation?->excerpt))
                <p>{{ $translation->excerpt }}</p>
            @endif
        </div>
    </x-front.page-title-band>

    <section class="border-y border-slate-200 bg-slate-100/80">
        <div class="mx-auto w-full max-w-[1320px] px-4 py-10 sm:px-6 lg:px-8">
            <article class="border border-slate-200 bg-white px-5 py-6 sm:px-6 lg:px-8 lg:py-8">
                <div class="ac-page-body-inner">
                    <div class="content-richtext">
                        {!! $translation?->body_html ?: '<p>This page has no body content.</p>' !!}
                    </div>
                </div>
            </article>
        </div>
    </section>

    @if ($bottomBlocks->isNotEmpty())
        <section class="mx-auto mt-2 w-full max-w-[1320px] px-4 pb-10 sm:px-6 lg:px-8">@include('components.content-placement', ['items' => $bottomBlocks])</section>
    @endif
@endsection
