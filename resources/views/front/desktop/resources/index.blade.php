@extends('front.desktop.layouts.store')

@section('title', __('resources.page_title'))
@section('main_class', 'w-full bg-white px-0 py-0 pb-0')

@section('content')
    @php
        $pageTitleBreadcrumbs = [
            ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
            ['label' => __('resources.page_title'), 'current' => true],
        ];
    @endphp

    <x-front.page-title-band :breadcrumbs="$pageTitleBreadcrumbs">
        <div class="ac-page-title-copy">
            <h1>{{ __('resources.page_title') }}</h1>
            <p>{{ __('resources.index.intro') }}</p>
        </div>
    </x-front.page-title-band>

    <section class="bg-white">
        <div class="mx-auto w-full max-w-[1320px] px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
            @forelse ($groups as $group)
                <section class="{{ $loop->first ? '' : 'mt-14' }}">
                    <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">{{ $group['label'] }}</p>
                            <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">{{ $group['label'] }}</h2>
                            <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600 sm:text-base">{{ $group['description'] }}</p>
                        </div>
                        <div class="ac-resource-index-count inline-flex items-center border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700">
                            {{ number_format($group['items']->count()) }}
                        </div>
                    </div>

                    <div class="mt-6 grid gap-5 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach ($group['items'] as $item)
                            @php
                                $accentClasses = match ($item['group_code']) {
                                    'sector-analysis' => 'from-[#0f172a] via-[#1d4ed8] to-[#f59e0b]',
                                    'transaction-analysis' => 'from-[#102542] via-[#0f766e] to-[#fbbf24]',
                                    default => 'from-[#111827] via-[#334155] to-[#f8b84e]',
                                };
                            @endphp
                            <article class="ac-resource-index-card group flex h-full flex-col overflow-hidden border border-slate-200 bg-white shadow-[0_18px_60px_-42px_rgba(15,23,42,0.35)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_30px_80px_-40px_rgba(15,23,42,0.42)]">
                                <a href="{{ route('resources.show', ['slug' => $item['slug']]) }}" class="block">
                                    @if ($item['cover_image_url'])
                                        <div class="flex aspect-[3/4] items-center justify-center overflow-hidden bg-slate-100">
                                            <img src="{{ $item['cover_image_url'] }}" alt="{{ $item['title'] }}" class="h-full w-full object-cover object-top transition duration-500 group-hover:scale-[1.02]">
                                        </div>
                                    @else
                                        <div class="relative aspect-[3/4] overflow-hidden bg-gradient-to-br {{ $accentClasses }} p-6 text-white">
                                            <div class="absolute -right-12 top-5 h-28 w-28 rounded-full bg-white/12 blur-2xl"></div>
                                            <div class="absolute -bottom-10 left-5 h-24 w-24 rounded-full bg-amber-200/25 blur-2xl"></div>
                                            <div class="relative flex h-full flex-col justify-between">
                                                <span class="inline-flex w-fit rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.26em] text-white/80">
                                                    {{ $item['group_label'] }}
                                                </span>
                                                <h3 class="max-w-[16rem] text-2xl font-semibold tracking-tight">{{ $item['title'] }}</h3>
                                            </div>
                                        </div>
                                    @endif
                                </a>

                                <div class="flex flex-1 flex-col justify-between p-5">
                                    <h3 class="text-lg font-semibold leading-snug tracking-tight text-slate-950">
                                        <a href="{{ route('resources.show', ['slug' => $item['slug']]) }}" class="transition-colors hover:text-[#173b5d]">
                                            {{ $item['title'] }}
                                        </a>
                                    </h3>

                                    <div class="mt-6 flex items-center justify-start gap-4">
                                        <a href="{{ route('resources.show', ['slug' => $item['slug']]) }}" class="ac-resource-card-cta inline-flex items-center gap-2 border px-4 py-2 text-sm font-semibold transition">
                                            <span>{{ __('resources.index.cta') }}</span>
                                            <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M4 12L12 4"></path>
                                                <path d="M6 4h6v6"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @empty
                <div class="ac-resource-index-empty border border-dashed border-slate-300 bg-white px-6 py-14 text-center text-slate-600">
                    {{ __('resources.index.empty') }}
                </div>
            @endforelse
        </div>
    </section>
@endsection
