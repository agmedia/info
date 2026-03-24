@extends('front.desktop.layouts.store')

@php
    $pageTranslation = $glossaryPageTranslation
        ?? $glossaryPage->translations->firstWhere('locale', $locale)
        ?? $glossaryPage->translations->firstWhere('locale', $fallbackLocale);
    $termTranslation = $glossaryTermTranslation;
    $glossaryPageTitle = trim((string) ($pageTranslation?->title ?? '')) ?: 'Svijet financija';
    $termTitle = $termTranslation?->title ?? $glossaryTerm->code;
    $termLead = trim((string) ($glossaryTermLead ?? ''));
    $payload = is_array($glossaryTermPayload ?? null) ? $glossaryTermPayload : [];
    $synonyms = collect($payload['synonyms'] ?? [])->map(fn ($item) => trim((string) $item))->filter()->values()->all();
    $variations = collect($payload['variations'] ?? [])->map(fn ($item) => trim((string) $item))->filter()->values()->all();
    $tags = collect($payload['tags'] ?? [])->map(fn ($item) => trim((string) $item))->filter()->values()->all();
    $categories = collect($payload['categories'] ?? [])->map(fn ($item) => trim((string) $item))->filter()->values()->all();
    $abbreviation = trim((string) ($payload['abbreviation'] ?? ''));
@endphp

@section('title', $termTranslation?->meta_title ?: $termTitle ?: $glossaryPageTitle)
@section('main_class', 'w-full px-0 py-0 pb-[100px]')

@section('content')
    @php
        $pageTitleBreadcrumbs = [
            ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
            ['label' => $glossaryPageTitle, 'url' => route('glossary.index')],
            ['label' => $termTitle, 'current' => true],
        ];
    @endphp

    @if ($topBlocks->isNotEmpty())
        <section class="mx-auto mb-8 w-full max-w-[1320px] px-4 pt-10 sm:px-6 lg:px-8">@include('components.content-placement', ['items' => $topBlocks])</section>
    @endif

    <x-front.page-title-band :breadcrumbs="$pageTitleBreadcrumbs">
        <div class="ac-page-title-copy">
            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.24em] text-[rgba(232,205,142,0.94)]">{{ $glossaryPageTitle }}</p>
            <h1>{{ $termTitle }}</h1>
            @if ($termLead !== '')
                <p>{{ $termLead }}</p>
            @endif
        </div>
    </x-front.page-title-band>

    <section class="mx-auto w-full max-w-[1040px] px-4 py-10 sm:px-6 lg:px-8">
        <div class="border-t border-slate-300 pt-6">
            <a href="{{ route('glossary.index') }}" class="text-sm font-medium text-[#ab8d52] underline-offset-4 hover:underline">&larr; Natrag u {{ $glossaryPageTitle }}</a>
        </div>

        <article class="ac-glossary-term-body mt-6 border border-slate-300 bg-white px-5 py-6 sm:px-8">
            <div class="flex flex-col gap-4 border-b border-slate-200 pb-6">
                @if ($categories !== [])
                    <p class="text-[0.72rem] font-semibold uppercase tracking-[0.18em] text-[#ab8d52]">{{ implode(' / ', $categories) }}</p>
                @endif

                <div>
                    <h2 class="text-[clamp(1.55rem,2.2vw,2.15rem)] font-semibold tracking-[-0.04em] text-slate-900">{{ $termTitle }}</h2>
                    @if ($abbreviation !== '')
                        <p class="mt-2 text-sm uppercase tracking-[0.14em] text-slate-500">{{ $abbreviation }}</p>
                    @endif
                </div>
            </div>

            @if ($synonyms !== [] || $variations !== [] || $tags !== [])
                <div class="grid gap-5 border-b border-slate-200 py-6 md:grid-cols-3">
                    @if ($synonyms !== [])
                        <div>
                            <p class="text-[0.72rem] font-semibold uppercase tracking-[0.18em] text-slate-500">Sinonimi</p>
                            <p class="mt-2 text-base leading-8 text-slate-700">{{ implode(', ', $synonyms) }}</p>
                        </div>
                    @endif
                    @if ($variations !== [])
                        <div>
                            <p class="text-[0.72rem] font-semibold uppercase tracking-[0.18em] text-slate-500">Varijante</p>
                            <p class="mt-2 text-base leading-8 text-slate-700">{{ implode(', ', $variations) }}</p>
                        </div>
                    @endif
                    @if ($tags !== [])
                        <div>
                            <p class="text-[0.72rem] font-semibold uppercase tracking-[0.18em] text-slate-500">Oznake</p>
                            <p class="mt-2 text-base leading-8 text-slate-700">{{ implode(', ', $tags) }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <div class="content-richtext pt-6">
                {!! $glossaryTermBodyHtml ?: '<p>Ovaj pojam trenutno nema dodatni opis.</p>' !!}
            </div>
        </article>

        @if ($relatedGlossaryTerms !== [])
            <section class="mt-12 border-t border-slate-300 pt-8">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-[0.72rem] font-semibold uppercase tracking-[0.18em] text-[#ab8d52]">{{ $glossaryPageTitle }}</p>
                        <h2 class="mt-2 text-[clamp(1.7rem,2.6vw,2.2rem)] font-semibold tracking-[-0.04em] text-slate-900">Povezani pojmovi</h2>
                    </div>
                    <a href="{{ route('glossary.index') }}" class="text-sm font-medium text-[#ab8d52] underline-offset-4 hover:underline">Prikaži sve</a>
                </div>

                <div class="mt-6 divide-y divide-slate-200 border-y border-slate-200">
                    @foreach ($relatedGlossaryTerms as $relatedTerm)
                        <article class="py-4">
                            <h3 class="text-xl text-slate-900">
                                <a href="{{ $relatedTerm['url'] }}" class="hover:underline">{{ $relatedTerm['title'] }}</a>
                            </h3>
                            @if ($relatedTerm['excerpt'] !== '')
                                <p class="mt-2 max-w-4xl text-base leading-8 text-slate-600">{{ $relatedTerm['excerpt'] }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </section>

    @if ($bottomBlocks->isNotEmpty())
        <section class="mx-auto mt-2 w-full max-w-[1320px] px-4 pb-10 sm:px-6 lg:px-8">@include('components.content-placement', ['items' => $bottomBlocks])</section>
    @endif
@endsection
