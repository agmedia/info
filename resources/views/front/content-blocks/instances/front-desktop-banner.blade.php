@php
    $title = $translation?->title ?: 'Modern essentials, built for everyday carry.';
    $subtitle = $translation?->subtitle ?: 'AGShop combines durable materials, clean silhouettes and practical storage to keep your daily setup lightweight and ready.';
    $primaryCtaLabel = $translation?->cta_label ?: 'Shop featured';
    $primaryCtaUrl = $translation?->cta_url ?: '#featured';
@endphp

<div class="front-section-surface max-w-4xl rounded-[2rem] px-8 py-12 text-white lg:px-12 lg:py-14">
    <p class="front-kicker inline-flex items-center gap-2 rounded-full border border-fuchsia-300/35 bg-fuchsia-500/10 px-4 py-2 text-[0.68rem]">
        <span class="h-2 w-2 rounded-full bg-fuchsia-300"></span>
        New season collection live now
    </p>

    <h1 class="mt-6 text-4xl font-semibold leading-[1.03] tracking-[-0.03em] lg:text-6xl">
        {!! nl2br(e($title)) !!}
    </h1>

    @if ($subtitle !== '')
        <p class="mt-6 max-w-2xl text-lg text-white/80">{{ $subtitle }}</p>
    @endif

    <div class="mt-10 flex flex-wrap items-center gap-4">
        <a href="{{ $primaryCtaUrl }}" class="front-cta-primary rounded-xl px-6 py-3 text-sm uppercase tracking-[0.12em]">
            {{ $primaryCtaLabel }}
        </a>
        <a href="#categories" class="front-cta-outline rounded-xl px-6 py-3 text-sm uppercase tracking-[0.12em]">
            Browse categories
        </a>
    </div>
</div>
