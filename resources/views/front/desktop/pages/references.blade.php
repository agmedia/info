@extends('front.desktop.layouts.store')

@php
    $translation = $selectedTranslation
        ?? $page->translations->firstWhere('locale', $locale)
        ?? $page->translations->firstWhere('locale', $fallbackLocale);

    $pageTitleBreadcrumbs = [
        ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
        ['label' => $translation?->title ?? $page->code, 'current' => true],
    ];

    $referenceItems = collect($referenceItems ?? [])->values();
    $pageBodyHtml = (string) ($translation?->body_html ?? '');
    $hasBodyCopy = trim(strip_tags($pageBodyHtml)) !== '';
    $sectionTitle = $locale === 'hr'
        ? 'Odabrani klijenti i partneri'
        : 'Selected clients and partners';
    $emptyStateTitle = $locale === 'hr'
        ? 'Reference se ažuriraju'
        : 'References are being updated';
    $emptyStateText = $locale === 'hr'
        ? 'Logotipi će uskoro biti dostupni i na ovoj stranici.'
        : 'Reference logos will be available on this page soon.';
    $sectionKicker = $locale === 'hr'
        ? 'Reference'
        : 'References';
@endphp

@section('title', $translation?->title ?? 'Reference')
@section('main_class', 'w-full px-0 py-0 pb-[100px]')

@section('content')
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
            @if ($hasBodyCopy)
                <article class="mb-6 rounded-[28px] border border-slate-200 bg-white px-5 py-6 shadow-[0_20px_50px_-40px_rgba(15,23,42,0.45)] sm:px-6 lg:px-8 lg:py-7">
                    <div class="content-richtext">
                        {!! $pageBodyHtml !!}
                    </div>
                </article>
            @endif

            @if ($referenceItems->isNotEmpty())
                <div class="ac-reference-head-wrap">
                    <div class="ac-services-head ac-support-story-head ac-reference-head">
                        <div class="ac-services-eyebrow">
                            <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                            <p class="ac-services-kicker">{{ $sectionKicker }}</p>
                            <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                        </div>

                        <h2>
                            <span>{{ $sectionTitle }}</span>
                        </h2>

                        <div class="ac-services-divider" aria-hidden="true">
                            <span class="ac-services-divider-line"></span>
                            <span class="ac-services-divider-glyph"></span>
                            <span class="ac-services-divider-line"></span>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($referenceItems as $item)
                        <article class="ac-reference-card group rounded-[28px] border border-slate-200 bg-white px-6 py-7 shadow-[0_20px_50px_-38px_rgba(15,23,42,0.45)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_28px_60px_-38px_rgba(15,23,42,0.55)]">
                            <span class="block h-px w-full bg-gradient-to-r from-amber-400/90 via-slate-200 to-transparent" aria-hidden="true"></span>

                            <div class="ac-reference-logo-shell mt-6 flex min-h-[110px] items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-6">
                                <img
                                    src="{{ $item['url'] }}"
                                    alt="{{ $item['alt'] }}"
                                    loading="lazy"
                                    decoding="async"
                                    class="ac-reference-logo-image max-h-[58px] w-auto max-w-full object-contain"
                                >
                            </div>

                            <h3 class="mt-5 text-base font-semibold tracking-tight text-slate-900">{{ $item['name'] }}</h3>

                            @if (($item['caption'] ?? '') !== '' && ($item['caption'] ?? '') !== ($item['name'] ?? ''))
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $item['caption'] }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            @else
                <article class="rounded-[28px] border border-dashed border-slate-300 bg-white px-6 py-10 text-center shadow-[0_20px_50px_-40px_rgba(15,23,42,0.35)]">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">{{ __('Alpha Capitalis') }}</p>
                    <h2 class="mt-3 text-2xl font-semibold tracking-tight text-slate-900">{{ $emptyStateTitle }}</h2>
                    <p class="mx-auto mt-3 max-w-2xl text-sm leading-7 text-slate-600">{{ $emptyStateText }}</p>
                </article>
            @endif
        </div>
    </section>

    @if ($bottomBlocks->isNotEmpty())
        <section class="mx-auto mt-2 w-full max-w-[1320px] px-4 pb-10 sm:px-6 lg:px-8">@include('components.content-placement', ['items' => $bottomBlocks])</section>
    @endif
@endsection

@push('styles')
    <style>
        .ac-reference-head-wrap {
            margin-bottom: 1.85rem;
            padding: 0;
        }

        .ac-reference-head {
            padding-top: clamp(0.4rem, 0.9vw, 0.7rem);
            padding-bottom: clamp(0.2rem, 0.7vw, 0.45rem);
        }

        .ac-reference-head .ac-services-eyebrow {
            justify-content: center;
        }

        .ac-reference-head .ac-services-eyebrow-line {
            display: none;
        }

        .ac-reference-head .ac-services-kicker {
            min-height: 2.55rem;
            padding: 0.45rem 1.15rem;
            border: 1px solid rgba(120, 96, 58, 0.16);
            background: rgba(255, 255, 255, 0.74);
            color: #3d3428;
            letter-spacing: 0.14em;
        }

        .ac-reference-head h2 {
            max-width: 20ch;
            margin-bottom: 0;
            color: #172033;
            font-size: clamp(1.8rem, 3.2vw, 2.8rem);
            line-height: 1.02;
            letter-spacing: -0.025em;
        }

        .ac-reference-head .ac-services-divider {
            max-width: 32rem;
            margin: 1.7rem auto 0;
        }

        .ac-reference-head .ac-services-divider-line {
            background: rgba(120, 96, 58, 0.18);
        }

        .ac-reference-head .ac-services-divider-glyph {
            width: 2.55rem;
            height: 2.55rem;
            border: 1px solid rgba(171, 141, 82, 0.28);
            background: rgba(255, 255, 255, 0.78);
        }

        .ac-reference-card {
            overflow: hidden;
        }

        .ac-reference-logo-shell {
            position: relative;
            overflow: hidden;
            isolation: isolate;
            background:
                radial-gradient(circle at top left, rgba(251, 191, 36, 0.12), transparent 42%),
                linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(241, 245, 249, 0.96));
        }

        .ac-reference-logo-shell::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background:
                linear-gradient(135deg, rgba(15, 23, 42, 0.02), transparent 40%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.36), rgba(255, 255, 255, 0.12));
        }

        .ac-reference-logo-image {
            position: relative;
            z-index: 1;
            mix-blend-mode: multiply;
            filter: contrast(1.04) saturate(0.96);
            transition: transform 0.2s ease;
        }

        .ac-reference-card:hover .ac-reference-logo-image {
            transform: scale(1.02);
        }
    </style>
@endpush
