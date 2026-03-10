@extends('front.desktop.layouts.store')

@section('title', __('ui.faq.page_title'))
@section('main_class', 'w-full px-0 py-0')

@section('content')
    @php
        $pageTitleBreadcrumbs = [
            ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
            ['label' => __('ui.faq.title'), 'current' => true],
        ];
    @endphp

    @if ($topBlocks->isNotEmpty())
        <section class="mx-auto mb-8 w-full max-w-[1320px] px-4 pt-10 sm:px-6 lg:px-8">@include('components.content-placement', ['items' => $topBlocks])</section>
    @endif

    <x-front.page-title-band :breadcrumbs="$pageTitleBreadcrumbs">
        <div class="ac-page-title-copy">
            <h1>{{ __('ui.faq.title') }}</h1>
            <p>{{ __('ui.faq.subtitle') }}</p>
        </div>
    </x-front.page-title-band>

    <section class="mx-auto w-full max-w-[1320px] px-4 py-10 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-[22px] border border-slate-200 bg-white">
            @forelse ($faqs as $faq)
                @php
                    $translation = $faq->translations->firstWhere('locale', $locale)
                        ?? $faq->translations->firstWhere('locale', $fallbackLocale)
                        ?? $faq->translations->first();
                @endphp
                @if ($translation)
                    <details class="faq-accordion-item group" @if($loop->first) open @endif>
                        <summary class="faq-accordion-summary flex items-center justify-between gap-4 px-4 py-4 text-left">
                            <span class="text-xl font-semibold text-slate-900">{{ $translation->question }}</span>
                            <span class="text-slate-500 transition group-open:rotate-45 text-2xl leading-none">+</span>
                        </summary>
                        <div class="content-richtext px-4 pb-5 text-slate-700">
                            {!! $translation->answer_html ?: '<p>—</p>' !!}
                        </div>
                    </details>
                @endif
            @empty
                <div class="px-4 py-8 text-slate-600">{{ __('ui.faq.empty') }}</div>
            @endforelse
        </div>
    </section>

    @if ($bottomBlocks->isNotEmpty())
        <section class="mx-auto mt-2 w-full max-w-[1320px] px-4 pb-10 sm:px-6 lg:px-8">@include('components.content-placement', ['items' => $bottomBlocks])</section>
    @endif
@endsection
