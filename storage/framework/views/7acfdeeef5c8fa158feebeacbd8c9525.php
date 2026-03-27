<?php
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
?>

<?php $__env->startSection('title', $translation?->title ?? 'Svijet financija'); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0 pb-[100px]'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $pageTitle = $translation?->title ?? 'Svijet financija';
        $pageIntro = !empty($translation?->excerpt)
            ? \Illuminate\Support\Str::limit((string) $translation->excerpt, 120, '...')
            : 'Brzo pretražite pojmove i otvorite detaljno objašnjenje svakog izraza.';
        $pageTitleBreadcrumbs = [
            ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
            ['label' => $pageTitle, 'current' => true],
        ];
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($topBlocks->isNotEmpty()): ?>
        <section class="mx-auto mb-8 w-full max-w-[1320px] px-4 pt-10 sm:px-6 lg:px-8"><?php echo $__env->make('components.content-placement', ['items' => $topBlocks], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if (isset($component)) { $__componentOriginale6a101278d02d7bbbf9e98ee1142bf75 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.front.page-title-band','data' => ['breadcrumbs' => $pageTitleBreadcrumbs]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('front.page-title-band'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pageTitleBreadcrumbs)]); ?>
        <div class="ac-page-title-copy">
            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.24em] text-[rgba(232,205,142,0.94)]"><?php echo e($glossaryKicker); ?></p>
            <h1><?php echo e($pageTitle); ?></h1>
            <p><?php echo e($pageIntro); ?></p>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75)): ?>
<?php $attributes = $__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75; ?>
<?php unset($__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale6a101278d02d7bbbf9e98ee1142bf75)): ?>
<?php $component = $__componentOriginale6a101278d02d7bbbf9e98ee1142bf75; ?>
<?php unset($__componentOriginale6a101278d02d7bbbf9e98ee1142bf75); ?>
<?php endif; ?>

    <section class="mx-auto w-full max-w-[1120px] px-4 py-6 sm:px-6 lg:px-8">
        <div data-glossary-root data-active-letter="<?php echo e($glossaryActiveLetter); ?>">
            <div class="pb-4">
                <div class="border border-slate-300 bg-white">
                    <div class="flex flex-col gap-4 px-4 py-4 md:flex-row md:items-center md:justify-between md:px-5">
                    <form class="flex-1" data-glossary-form>
                        <label for="finance-glossary-search" class="sr-only"><?php echo e($searchLabel); ?></label>
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
                                value="<?php echo e($glossarySearch); ?>"
                                placeholder="<?php echo e($searchPlaceholder); ?>"
                                    class="min-h-[2.85rem] w-full border border-slate-300 bg-white pl-10 pr-20 text-sm text-slate-900 outline-none transition focus:border-slate-500"
                                data-glossary-search
                                autocomplete="off"
                            >
                                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold uppercase tracking-[0.12em] text-[#ab8d52] underline-offset-4 hover:underline" data-glossary-clear <?php if($glossarySearch === '' && $glossaryActiveLetter === 'ALL'): ?> hidden <?php endif; ?>><?php echo e($resetLabel); ?></button>
                        </div>
                    </form>

                        <div class="flex items-center gap-4">
                            <p class="text-sm font-medium text-slate-500">
                                <span data-glossary-count><?php echo e($glossaryInitialVisibleCount); ?></span> pojmova
                        </p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($glossarySearch !== '' || $glossaryActiveLetter !== 'ALL'): ?>
                                <p class="text-xs uppercase tracking-[0.16em] text-slate-400">Aktivni filteri</p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 px-4 py-3 md:px-5">
                        <div class="front-scroll-rail" aria-label="Filter po početnom slovu">
                            <div class="front-scroll-rail-track">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $glossaryAlphabet; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $letter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $hasItems = $letter === 'ALL' || in_array($letter, $glossaryAvailableLetters, true);
                                        $isActive = $glossaryActiveLetter === $letter;
                                        $label = $letter === 'ALL' ? 'Sve' : $letter;
                                        $widthClass = in_array($letter, ['ALL', '0-9'], true) ? 'min-w-[4.4rem]' : 'min-w-[2.35rem]';
                                    ?>
                                    <button
                                        type="button"
                                        class="min-h-[2.35rem] <?php echo e($widthClass); ?> border border-slate-300 px-3 text-sm font-medium shadow-none transition <?php echo e($isActive ? 'border-slate-800 bg-slate-800 text-white bg-none' : 'bg-white text-[#ab8d52]'); ?> <?php echo e($hasItems ? '' : 'text-slate-300'); ?>"
                                        data-glossary-letter="<?php echo e($letter); ?>"
                                        data-empty="<?php echo e($hasItems ? 'false' : 'true'); ?>"
                                        aria-pressed="<?php echo e($isActive ? 'true' : 'false'); ?>"
                                    >
                                        <?php echo e($label); ?>

                                    </button>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 border-t border-slate-300">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $alphabetLetters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $letter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(! $groupedGlossaryTerms->has($letter)) continue; ?>
                    <?php
                        $terms = $groupedGlossaryTerms->get($letter);
                        $groupIsVisible = (bool) ($glossaryVisibleGroups[$letter] ?? false);
                    ?>
                    <section class="border-b border-slate-200 py-4 md:py-5" data-glossary-group data-letter="<?php echo e($letter); ?>" <?php if(! $groupIsVisible): ?> hidden <?php endif; ?>>
                        <div class="grid gap-0 md:grid-cols-[3.9rem_minmax(0,1fr)] md:gap-6">
                            <div class="border-b border-slate-200 pb-4 md:border-b-0 md:pb-0 md:pt-1">
                                <h2 class="text-[2.1rem] leading-none tracking-[-0.06em] text-slate-800 md:text-[2.45rem]"><?php echo e($letter); ?></h2>
                            </div>

                            <div class="divide-y divide-slate-200">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $excerpt = \Illuminate\Support\Str::limit((string) $term['excerpt'], 120, '...');
                                    ?>
                                    <article
                                        id="pojam-<?php echo e($term['slug']); ?>"
                                        class="py-3 transition md:py-4"
                                        data-glossary-item
                                        data-letter="<?php echo e($term['letter_key']); ?>"
                                        data-search="<?php echo e($term['search_text']); ?>"
                                        <?php if(! $term['initial_visible']): ?> hidden <?php endif; ?>
                                    >
                                        <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between md:gap-6">
                                            <div class="min-w-0 flex-1">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($term['abbreviation'] !== ''): ?>
                                                    <p class="mb-1 text-[0.72rem] font-semibold uppercase tracking-[0.16em] text-[#ab8d52]"><?php echo e($term['abbreviation']); ?></p>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <h3 class="text-[1.08rem] font-semibold leading-7 text-slate-900 md:text-[1.18rem]">
                                                    <a href="<?php echo e($term['url']); ?>" class="hover:text-[#ab8d52] hover:underline"><?php echo e($term['title']); ?></a>
                                                </h3>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($excerpt !== ''): ?>
                                                    <p class="mt-1.5 max-w-[42rem] text-[0.92rem] leading-7 text-slate-600"><?php echo e($excerpt); ?></p>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <div class="shrink-0 pt-0.5 md:self-center">
                                                <a href="<?php echo e($term['url']); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-[#ab8d52] underline-offset-4 hover:underline">
                                                    <span><?php echo e($readMoreLabel); ?></span>
                                                    <span aria-hidden="true">&rarr;</span>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </section>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="border-b border-dashed border-slate-300 bg-white px-6 py-10 text-center" data-glossary-empty <?php if($glossaryInitialVisibleCount > 0): ?> hidden <?php endif; ?>>
                    <p class="text-[0.72rem] font-semibold uppercase tracking-[0.2em] text-[#ab8d52]"><?php echo e($glossaryKicker); ?></p>
                    <h2 class="mt-3 text-[clamp(1.7rem,3vw,2.4rem)] font-semibold tracking-[-0.04em] text-slate-900"><?php echo e($emptyTitle); ?></h2>
                    <p class="mx-auto mt-3 max-w-2xl text-base leading-8 text-slate-600"><?php echo e($emptyBody); ?></p>
                </div>
            </div>
        </div>
    </section>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bottomBlocks->isNotEmpty()): ?>
        <section class="mx-auto mt-2 w-full max-w-[1320px] px-4 pb-10 sm:px-6 lg:px-8"><?php echo $__env->make('components.content-placement', ['items' => $bottomBlocks], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/glossary.blade.php ENDPATH**/ ?>