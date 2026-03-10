@extends('front.desktop.layouts.store')

@php
    $categoryTranslation = $category->translations->firstWhere('locale', $locale)
        ?? $category->translations->firstWhere('locale', $fallbackLocale);
@endphp

@section('title', $categoryTranslation?->name ?? 'Pages')
@section('main_class', 'w-full px-0 py-0')

@section('content')
    @php
        $pageTitleBreadcrumbs = [
            ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
            ['label' => $categoryTranslation?->name ?? __('ui.front.desktop.footer.info'), 'current' => true],
        ];
    @endphp

    @if ($topBlocks->isNotEmpty())
        <section class="mx-auto mb-8 w-full max-w-[1320px] px-4 pt-10 sm:px-6 lg:px-8">@include('components.content-placement', ['items' => $topBlocks])</section>
    @endif

    <x-front.page-title-band :breadcrumbs="$pageTitleBreadcrumbs">
        <div class="ac-page-title-copy">
            <h1>{{ $categoryTranslation?->name ?? __('ui.front.desktop.footer.info') }}</h1>
            @if (!empty($categoryTranslation?->clean_description))
                <p>{{ $categoryTranslation->clean_description }}</p>
            @endif
        </div>
    </x-front.page-title-band>

    <section class="mx-auto w-full max-w-[1320px] px-4 py-10 sm:px-6 lg:px-8">
        @if ($pages->isEmpty())
            <div class="rounded-[22px] border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
                No pages in this category.
            </div>
        @else
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($pages as $page)
                    @php
                        $translation = $page->translations->firstWhere('locale', $locale)
                            ?? $page->translations->firstWhere('locale', $fallbackLocale);
                    @endphp
                    <article class="rounded-[20px] border border-slate-200 bg-white p-5">
                        <h2 class="text-lg font-bold text-slate-900">{{ $translation?->title ?? $page->code }}</h2>
                        @if (!empty($translation?->excerpt))
                            <p class="mt-2 text-sm text-slate-600">{{ $translation->excerpt }}</p>
                        @endif
                        <a href="{{ route('pages.show', ['slug' => $translation?->slug ?? $page->id]) }}" class="mt-4 inline-flex text-sm font-semibold text-slate-900 underline underline-offset-2 hover:text-slate-700">
                            Open page
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $pages->links() }}
            </div>
        @endif
    </section>

    @if ($bottomBlocks->isNotEmpty())
        <section class="mx-auto mt-2 w-full max-w-[1320px] px-4 pb-10 sm:px-6 lg:px-8">@include('components.content-placement', ['items' => $bottomBlocks])</section>
    @endif
@endsection
