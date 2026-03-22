@extends('front.desktop.layouts.store')

@php
    $translation = $selectedTranslation
        ?? $page->translations->firstWhere('locale', $locale)
        ?? $page->translations->firstWhere('locale', $fallbackLocale);
    $pagePayload = is_array($page->payload ?? null) ? $page->payload : [];
    $translationPayload = is_array($translation?->payload ?? null) ? $translation->payload : [];
    $glossaryKicker = trim((string) ($translationPayload['glossary_kicker'] ?? $pagePayload['glossary_kicker'] ?? 'Rječnik pojmova')) ?: 'Rječnik pojmova';
    $searchPlaceholder = trim((string) ($pagePayload['glossary_search_placeholder'] ?? 'Pretražite pojam, kraticu ili povezani izraz'));
    $emptyTitle = trim((string) ($pagePayload['glossary_empty_title'] ?? 'Nema rezultata za zadane filtre'));
    $emptyBody = trim((string) ($pagePayload['glossary_empty_body'] ?? 'Pokušajte s drugim pojmom ili vratite prikaz na sva slova.'));
    $alphabetLetters = array_values(array_filter($glossaryAlphabet, fn ($candidate) => $candidate !== 'ALL'));
    $readMoreLabel = __('ui.blog.read_more');
    $searchLabel = __('ui.blog.filters.search');
    $resetLabel = __('ui.blog.filters.reset');
@endphp

@section('title', $translation?->title ?? 'Svijet financija')
@section('main_class', 'w-full px-0 py-0 pb-[100px]')

@section('content')
    @php
        $pageTitle = $translation?->title ?? 'Svijet financija';
        $pageIntro = !empty($translation?->excerpt)
            ? \Illuminate\Support\Str::limit((string) $translation->excerpt, 120, '...')
            : 'Brzo pretražite pojmove i otvorite detaljno objašnjenje svakog izraza.';
        $pageTitleBreadcrumbs = [
            ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
            ['label' => $pageTitle, 'current' => true],
        ];
    @endphp

    @if ($topBlocks->isNotEmpty())
        <section class="mx-auto mb-8 w-full max-w-[1320px] px-4 pt-10 sm:px-6 lg:px-8">@include('components.content-placement', ['items' => $topBlocks])</section>
    @endif

    <x-front.page-title-band :breadcrumbs="$pageTitleBreadcrumbs">
        <div class="ac-page-title-copy">
            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.24em] text-[rgba(232,205,142,0.94)]">{{ $glossaryKicker }}</p>
            <h1>{{ $pageTitle }}</h1>
            <p>{{ $pageIntro }}</p>
        </div>
    </x-front.page-title-band>

    <section class="mx-auto w-full max-w-[1120px] px-4 py-6 sm:px-6 lg:px-8">
        <div data-glossary-root data-active-letter="{{ $glossaryActiveLetter }}">
            <div class="pb-4">
                <div class="border border-slate-300 bg-white">
                    <div class="flex flex-col gap-4 px-4 py-4 md:flex-row md:items-center md:justify-between md:px-5">
                    <form class="flex-1" data-glossary-form>
                        <label for="finance-glossary-search" class="sr-only">{{ $searchLabel }}</label>
                            <div class="relative max-w-[28rem]">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8">
                                    <circle cx="11" cy="11" r="7"></circle>
                                    <path d="M20 20l-3.2-3.2"></path>
                                </svg>
                            </span>
                            <input
                                id="finance-glossary-search"
                                type="search"
                                name="q"
                                value="{{ $glossarySearch }}"
                                placeholder="{{ $searchPlaceholder }}"
                                    class="min-h-[2.85rem] w-full border border-slate-300 bg-white pl-10 pr-20 text-sm text-slate-900 outline-none transition focus:border-slate-500"
                                data-glossary-search
                                autocomplete="off"
                            >
                                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold uppercase tracking-[0.12em] text-[#ab8d52] underline-offset-4 hover:underline" data-glossary-clear @if($glossarySearch === '' && $glossaryActiveLetter === 'ALL') hidden @endif>{{ $resetLabel }}</button>
                        </div>
                    </form>

                        <div class="flex items-center gap-4">
                            <p class="text-sm font-medium text-slate-500">
                                <span data-glossary-count>{{ $glossaryInitialVisibleCount }}</span> pojmova
                        </p>
                            @if ($glossarySearch !== '' || $glossaryActiveLetter !== 'ALL')
                                <p class="text-xs uppercase tracking-[0.16em] text-slate-400">Aktivni filteri</p>
                            @endif
                        </div>
                    </div>

                    <div class="border-t border-slate-200 px-4 py-3 md:px-5">
                        <div class="front-scroll-rail" aria-label="Filter po početnom slovu">
                            <div class="front-scroll-rail-track">
                                @foreach ($glossaryAlphabet as $letter)
                                    @php
                                        $hasItems = $letter === 'ALL' || in_array($letter, $glossaryAvailableLetters, true);
                                        $isActive = $glossaryActiveLetter === $letter;
                                        $label = $letter === 'ALL' ? 'Sve' : $letter;
                                        $widthClass = in_array($letter, ['ALL', '0-9'], true) ? 'min-w-[4.4rem]' : 'min-w-[2.35rem]';
                                    @endphp
                                    <button
                                        type="button"
                                        class="min-h-[2.35rem] {{ $widthClass }} border border-slate-300 px-3 text-sm font-medium shadow-none transition {{ $isActive ? 'border-slate-800 bg-slate-800 text-white bg-none' : 'bg-white text-[#ab8d52]' }} {{ $hasItems ? '' : 'text-slate-300' }}"
                                        data-glossary-letter="{{ $letter }}"
                                        data-empty="{{ $hasItems ? 'false' : 'true' }}"
                                        aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                                    >
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 border-t border-slate-300">
                @foreach ($alphabetLetters as $letter)
                    @continue(! $groupedGlossaryTerms->has($letter))
                    @php
                        $terms = $groupedGlossaryTerms->get($letter);
                        $groupIsVisible = (bool) ($glossaryVisibleGroups[$letter] ?? false);
                    @endphp
                    <section class="border-b border-slate-200 py-4 md:py-5" data-glossary-group data-letter="{{ $letter }}" @if(! $groupIsVisible) hidden @endif>
                        <div class="grid gap-0 md:grid-cols-[3.9rem_minmax(0,1fr)] md:gap-6">
                            <div class="border-b border-slate-200 pb-4 md:border-b-0 md:pb-0 md:pt-1">
                                <h2 class="text-[2.1rem] leading-none tracking-[-0.06em] text-slate-800 md:text-[2.45rem]">{{ $letter }}</h2>
                            </div>

                            <div class="divide-y divide-slate-200">
                                @foreach ($terms as $term)
                                    @php
                                        $excerpt = \Illuminate\Support\Str::limit((string) $term['excerpt'], 120, '...');
                                    @endphp
                                    <article
                                        id="pojam-{{ $term['slug'] }}"
                                        class="py-3 transition md:py-4"
                                        data-glossary-item
                                        data-letter="{{ $term['letter_key'] }}"
                                        data-search="{{ $term['search_text'] }}"
                                        @if(! $term['initial_visible']) hidden @endif
                                    >
                                        <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between md:gap-6">
                                            <div class="min-w-0 flex-1">
                                                @if ($term['abbreviation'] !== '')
                                                    <p class="mb-1 text-[0.72rem] font-semibold uppercase tracking-[0.16em] text-[#ab8d52]">{{ $term['abbreviation'] }}</p>
                                                @endif
                                                <h3 class="text-[1.08rem] font-semibold leading-7 text-slate-900 md:text-[1.18rem]">
                                                    <a href="{{ $term['url'] }}" class="hover:text-[#ab8d52] hover:underline">{{ $term['title'] }}</a>
                                                </h3>
                                                @if ($excerpt !== '')
                                                    <p class="mt-1.5 max-w-[42rem] text-[0.92rem] leading-7 text-slate-600">{{ $excerpt }}</p>
                                                @endif
                                            </div>
                                            <div class="shrink-0 pt-0.5 md:self-center">
                                                <a href="{{ $term['url'] }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#ab8d52] underline-offset-4 hover:underline">
                                                    <span>{{ $readMoreLabel }}</span>
                                                    <span aria-hidden="true">&rarr;</span>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endforeach

                <div class="border-b border-dashed border-slate-300 bg-white px-6 py-10 text-center" data-glossary-empty @if($glossaryInitialVisibleCount > 0) hidden @endif>
                    <p class="text-[0.72rem] font-semibold uppercase tracking-[0.2em] text-[#ab8d52]">{{ $glossaryKicker }}</p>
                    <h2 class="mt-3 text-[clamp(1.7rem,3vw,2.4rem)] font-semibold tracking-[-0.04em] text-slate-900">{{ $emptyTitle }}</h2>
                    <p class="mx-auto mt-3 max-w-2xl text-base leading-8 text-slate-600">{{ $emptyBody }}</p>
                </div>
            </div>
        </div>
    </section>

    @if ($bottomBlocks->isNotEmpty())
        <section class="mx-auto mt-2 w-full max-w-[1320px] px-4 pb-10 sm:px-6 lg:px-8">@include('components.content-placement', ['items' => $bottomBlocks])</section>
    @endif
@endsection

@push('scripts')
    <script>
        (() => {
            const root = document.querySelector('[data-glossary-root]');
            if (!root) {
                return;
            }

            const form = root.querySelector('[data-glossary-form]');
            const searchInput = root.querySelector('[data-glossary-search]');
            const clearButton = root.querySelector('[data-glossary-clear]');
            const resultCount = root.querySelector('[data-glossary-count]');
            const emptyState = root.querySelector('[data-glossary-empty]');
            const items = Array.from(root.querySelectorAll('[data-glossary-item]'));
            const groups = Array.from(root.querySelectorAll('[data-glossary-group]'));
            const letterButtons = Array.from(root.querySelectorAll('[data-glossary-letter]'));

            if (!form || !searchInput || !clearButton || !resultCount || !emptyState || items.length === 0) {
                return;
            }

            let activeLetter = root.dataset.activeLetter || 'ALL';
            let debounceHandle = null;

            const normalize = (value) => value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, ' ')
                .trim();

            const syncButtons = () => {
                letterButtons.forEach((button) => {
                    const isActive = button.dataset.glossaryLetter === activeLetter;
                    const isEmpty = button.dataset.empty === 'true';
                    button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                    button.classList.toggle('bg-slate-800', isActive);
                    button.classList.toggle('text-white', isActive);
                    button.classList.toggle('border-slate-800', isActive);
                    button.classList.toggle('bg-white', !isActive);
                    button.classList.toggle('border-slate-300', !isActive);
                    button.classList.toggle('text-[#ab8d52]', !isActive && !isEmpty);
                    button.classList.toggle('text-slate-300', !isActive && isEmpty);
                });
            };

            const updateUrl = () => {
                const params = new URLSearchParams(window.location.search);
                const rawQuery = searchInput.value.trim();

                if (rawQuery !== '') {
                    params.set('q', rawQuery);
                } else {
                    params.delete('q');
                }

                if (activeLetter !== 'ALL') {
                    params.set('letter', activeLetter);
                } else {
                    params.delete('letter');
                }

                const nextQuery = params.toString();
                const nextUrl = `${window.location.pathname}${nextQuery ? `?${nextQuery}` : ''}`;
                window.history.replaceState({}, '', nextUrl);
            };

            const applyFilters = () => {
                const query = normalize(searchInput.value);
                let visibleCount = 0;

                items.forEach((item) => {
                    const matchesLetter = activeLetter === 'ALL' || item.dataset.letter === activeLetter;
                    const haystack = item.dataset.search || '';
                    const matchesSearch = query === '' || haystack.includes(query);
                    const visible = matchesLetter && matchesSearch;

                    item.hidden = !visible;
                    if (visible) {
                        visibleCount += 1;
                    }
                });

                groups.forEach((group) => {
                    const hasVisibleItems = Array.from(group.querySelectorAll('[data-glossary-item]')).some((item) => !item.hidden);
                    group.hidden = !hasVisibleItems;
                });

                resultCount.textContent = String(visibleCount);
                emptyState.hidden = visibleCount > 0;
                clearButton.hidden = searchInput.value.trim() === '' && activeLetter === 'ALL';
                syncButtons();
                updateUrl();
            };

            form.addEventListener('submit', (event) => {
                event.preventDefault();
                applyFilters();
            });

            searchInput.addEventListener('input', () => {
                window.clearTimeout(debounceHandle);
                debounceHandle = window.setTimeout(applyFilters, 140);
            });

            clearButton.addEventListener('click', () => {
                searchInput.value = '';
                activeLetter = 'ALL';
                applyFilters();
                searchInput.focus();
            });

            letterButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const nextLetter = button.dataset.glossaryLetter || 'ALL';
                    activeLetter = activeLetter === nextLetter ? 'ALL' : nextLetter;
                    applyFilters();
                });
            });

            applyFilters();
        })();
    </script>
@endpush
