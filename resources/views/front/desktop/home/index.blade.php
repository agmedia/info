@extends('front.desktop.layouts.store')

@section('title', config('app.name', 'AG Shop').' Store')
@section('main_class', 'mx-auto w-full max-w-[1180px] px-5 pt-8 pb-14 lg:px-7')

@section('content')
    @php
        $resolver = app(\App\Services\Content\ContentBlockResolver::class);
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.locale');

        $homeHeroBlocks = $resolver->forPlacement('home.hero', $locale, null, null, 'desktop');
        $homeHeroBenefitsBlocks = $resolver->forPlacement('home.hero_benefits', $locale, null, null, 'desktop');
        $homeBeforeProductsBlocks = $resolver->forPlacement('home.before_products', $locale, null, null, 'desktop');
        $homeCategoriesBlocks = $resolver->forPlacement('home.categories', $locale, null, null, 'desktop');
        $homeAfterProductsBlocks = $resolver->forPlacement('home.after_products', $locale, null, null, 'desktop');
        $homeBottomBlocks = $resolver->forPlacement('home.bottom', $locale, null, null, 'desktop');

        $viewer = auth()->user();
        $canPreviewBlock = $viewer && ($viewer->isA('superadmin') || $viewer->can('content.blocks'));
        $previewBlockId = $canPreviewBlock ? (int) request()->query('preview_block', 0) : 0;
        $requestedPreviewPlacement = $canPreviewBlock ? (string) request()->query('preview_placement', '') : '';

        if ($previewBlockId > 0) {
            $previewBlock = \App\Models\Content\ContentBlock::query()
                ->with([
                    'translations' => fn ($q) => $q->whereIn('locale', [$locale, config('app.locale')]),
                    'slots' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
                ])
                ->find($previewBlockId);

            if ($previewBlock) {
                $previewPlacement = $requestedPreviewPlacement !== ''
                    ? $requestedPreviewPlacement
                    : (string) ($previewBlock->slots->first()?->placement ?? 'home.hero');

                $previewTranslation = $previewBlock->translations->firstWhere('locale', $locale)
                    ?? $previewBlock->translations->firstWhere('locale', config('app.locale'));

                $previewSlot = $previewBlock->slots->firstWhere('placement', $previewPlacement)
                    ?? new \App\Models\Content\ContentBlockSlot(['placement' => $previewPlacement]);

                $previewItem = collect([[
                    'slot' => $previewSlot,
                    'block' => $previewBlock,
                    'translation' => $previewTranslation,
                ]]);

                if ($previewPlacement === 'home.hero') {
                    $homeHeroBlocks = $previewItem;
                } elseif ($previewPlacement === 'home.hero_benefits') {
                    $homeHeroBenefitsBlocks = $previewItem;
                } elseif ($previewPlacement === 'home.before_products') {
                    $homeBeforeProductsBlocks = $previewItem;
                } elseif ($previewPlacement === 'home.categories') {
                    $homeCategoriesBlocks = $previewItem;
                } elseif ($previewPlacement === 'home.after_products') {
                    $homeAfterProductsBlocks = $previewItem;
                } elseif ($previewPlacement === 'home.bottom') {
                    $homeBottomBlocks = $previewItem;
                }
            }
        }

        $latestPosts = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('content_blog_posts')) {
            $latestPosts = \App\Models\Content\Blog\BlogPost::query()
                ->where('is_active', true)
                ->where(function ($q): void {
                    $q->whereNull('published_at')
                        ->orWhere('published_at', '<=', now());
                })
                ->with([
                    'translations' => fn ($q) => $q->whereIn('locale', [$locale, $fallbackLocale]),
                ])
                ->orderByDesc('is_featured')
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit(3)
                ->get();
        }

        $featuredPages = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('content_info_pages')) {
            $featuredPages = \App\Models\Content\Page\InfoPage::query()
                ->where('is_active', true)
                ->where(function ($q): void {
                    $q->whereNull('published_at')
                        ->orWhere('published_at', '<=', now());
                })
                ->with([
                    'translations' => fn ($q) => $q->whereIn('locale', [$locale, $fallbackLocale]),
                ])
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->limit(3)
                ->get();
        }

        $serviceHighlights = [
            [
                'index' => '01',
                'title' => 'Izrada web stranica',
                'copy' => 'Od jednostavnih prezentacijskih stranica do naprednih višejezičnih sustava.',
            ],
            [
                'index' => '02',
                'title' => 'Izrada web shopa',
                'copy' => 'Brzi i stabilni ecommerce sustavi s fokusom na prodaju i SEO vidljivost.',
            ],
            [
                'index' => '03',
                'title' => 'Custom web aplikacije',
                'copy' => 'Laravel rješenja, API integracije i automatizacija poslovnih procesa.',
            ],
        ];
    @endphp

    <section class="ag-home-hero overflow-hidden rounded-[2rem] border p-7 sm:p-10 lg:p-12">
        <div class="ag-space-banner" data-space-banner aria-hidden="true">
            <canvas class="ag-space-canvas" data-space-canvas></canvas>
            <div class="ag-space-grid"></div>
            <div class="ag-space-vignette"></div>
        </div>

        <div class="ag-home-hero-content">
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_220px] lg:items-start">
                <div>
                    <p class="ag-home-kicker">Stvaramo web od 2005.</p>
                    <h1 class="ag-home-hero-title mt-4 text-4xl font-semibold leading-[1.02] tracking-[-0.03em] text-white sm:text-5xl lg:text-7xl">
                        Izrada web shopa, web stranica i custom web aplikacija
                    </h1>
                    <p class="mt-6 max-w-3xl text-base text-white/70 sm:text-lg">
                        Razvijamo moderne digitalne sustave i marketinške kampanje koje donose mjerljive rezultate.
                    </p>
                </div>

                <div class="ag-home-year mt-1 lg:text-right">
                    <p class="ag-home-year-number">20</p>
                    <p class="ag-home-year-label">osnovani 2005.</p>
                </div>
            </div>

            <div class="ag-home-service-list mt-10 divide-y">
                @foreach ($serviceHighlights as $service)
                    <article class="ag-home-service-row py-5">
                        <a href="{{ route('contact.create') }}" class="group grid gap-4 md:grid-cols-[64px_minmax(0,1fr)_minmax(0,1.2fr)] md:items-center">
                            <span class="ag-home-service-index text-sm font-semibold text-white/55">{{ $service['index'] }}</span>
                            <h2 class="text-2xl font-semibold tracking-tight text-white">{{ $service['title'] }}</h2>
                            <p class="text-sm text-white/70">{{ $service['copy'] }}</p>
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="mt-10 flex flex-wrap gap-3">
                <a href="{{ route('contact.create') }}" class="front-cta-primary rounded-xl px-6 py-3 text-sm uppercase tracking-[0.12em]">
                    Zatražite ponudu
                </a>
                <a href="{{ route('blog.index') }}" class="front-cta-outline rounded-xl px-6 py-3 text-sm uppercase tracking-[0.12em]">
                    Pogledajte blog
                </a>
            </div>
        </div>
    </section>

    @if ($homeHeroBlocks->isNotEmpty())
        <section class="mt-7">
            @include('components.content-placement', ['items' => $homeHeroBlocks])
        </section>
    @endif

    @if ($homeHeroBenefitsBlocks->isNotEmpty())
        <section class="mt-8">
            @include('components.content-placement', ['items' => $homeHeroBenefitsBlocks])
        </section>
    @endif

    @if ($homeBeforeProductsBlocks->isNotEmpty())
        <section class="mt-8">
            @include('components.content-placement', ['items' => $homeBeforeProductsBlocks])
        </section>
    @endif

    <section class="mt-10">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <p class="front-kicker">Blog</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-white">Novosti i uvidi</h2>
            </div>
            <a href="{{ route('blog.index') }}" class="text-sm font-semibold uppercase tracking-[0.12em] text-white/65 transition hover:text-white">Svi članci</a>
        </div>

        @if ($latestPosts->isNotEmpty())
            <div class="grid gap-5 lg:grid-cols-3">
                @foreach ($latestPosts as $post)
                    @php
                        $translation = $post->translations->firstWhere('locale', $locale)
                            ?? $post->translations->firstWhere('locale', $fallbackLocale);
                        $imageUrl = $post->getFirstMedia('blog_cover')?->getUrl();
                    @endphp
                    <a href="{{ route('blog.show', ['slug' => $translation?->slug ?? $post->id]) }}" class="ag-home-card group block overflow-hidden rounded-2xl border">
                        <div class="aspect-[16/10] w-full overflow-hidden">
                            @if ($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $translation?->title ?? $post->code }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]" loading="lazy" decoding="async">
                            @else
                                <div class="ag-home-card-placeholder flex h-full items-center justify-center text-xs uppercase tracking-[0.15em] text-white/60">No image</div>
                            @endif
                        </div>
                        <div class="p-5">
                            <h3 class="text-lg font-semibold leading-tight text-white">{{ $translation?->title ?? $post->code }}</h3>
                            <p class="mt-3 text-sm text-white/70">{{ $translation?->excerpt ?: 'Practical notes on delivery, performance, and digital operations.' }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="ag-home-empty rounded-2xl border p-6 text-sm text-white/65">No blog posts are published yet.</div>
        @endif
    </section>

    <section class="mt-10">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <p class="front-kicker">Usluge</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-white">Stranice usluga i sadržaja</h2>
            </div>
        </div>

        @if ($featuredPages->isNotEmpty())
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($featuredPages as $page)
                    @php
                        $translation = $page->translations->firstWhere('locale', $locale)
                            ?? $page->translations->firstWhere('locale', $fallbackLocale);
                    @endphp
                    <a href="{{ route('pages.show', ['slug' => $translation?->slug ?? $page->id]) }}" class="ag-home-page-card block rounded-2xl border p-5 transition hover:-translate-y-0.5">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/50">Page</p>
                        <h3 class="mt-3 text-xl font-semibold leading-tight text-white">{{ $translation?->title ?? $page->code }}</h3>
                        @if (!empty($translation?->excerpt))
                            <p class="mt-3 text-sm text-white/70">{{ $translation->excerpt }}</p>
                        @else
                            <p class="mt-3 text-sm text-white/70">Structured content page with reusable blocks and editable SEO fields.</p>
                        @endif
                    </a>
                @endforeach
            </div>
        @else
            <div class="ag-home-empty rounded-2xl border p-6 text-sm text-white/65">No active pages are available yet.</div>
        @endif
    </section>

    @if ($homeCategoriesBlocks->isNotEmpty())
        <section class="mt-10">
            @include('components.content-placement', ['items' => $homeCategoriesBlocks])
        </section>
    @endif

    @if ($homeAfterProductsBlocks->isNotEmpty())
        <section class="mt-10">
            @include('components.content-placement', ['items' => $homeAfterProductsBlocks])
        </section>
    @endif

    @if ($homeBottomBlocks->isNotEmpty())
        <section class="mt-10">
            @include('components.content-placement', ['items' => $homeBottomBlocks])
        </section>
    @endif
@endsection
