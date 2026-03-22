<?php
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
?>

<?php $__env->startSection('title', $termTranslation?->meta_title ?: $termTitle ?: $glossaryPageTitle); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0 pb-[100px]'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $pageTitleBreadcrumbs = [
            ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
            ['label' => $glossaryPageTitle, 'url' => route('glossary.index')],
            ['label' => $termTitle, 'current' => true],
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
            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.24em] text-[rgba(232,205,142,0.94)]"><?php echo e($glossaryPageTitle); ?></p>
            <h1><?php echo e($termTitle); ?></h1>
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

    <section class="mx-auto w-full max-w-[1040px] px-4 py-10 sm:px-6 lg:px-8">
        <div class="border-t border-slate-300 pt-6">
            <a href="<?php echo e(route('glossary.index')); ?>" class="text-sm font-medium text-[#ab8d52] underline-offset-4 hover:underline">&larr; Natrag u <?php echo e($glossaryPageTitle); ?></a>
        </div>

        <article class="mt-6 border border-slate-300 bg-white px-5 py-6 sm:px-8">
            <div class="flex flex-col gap-4 border-b border-slate-200 pb-6">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categories !== []): ?>
                    <p class="text-[0.72rem] font-semibold uppercase tracking-[0.18em] text-[#ab8d52]"><?php echo e(implode(' / ', $categories)); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div>
                    <h2 class="text-[clamp(1.55rem,2.2vw,2.15rem)] font-semibold tracking-[-0.04em] text-slate-900"><?php echo e($termTitle); ?></h2>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($abbreviation !== ''): ?>
                        <p class="mt-2 text-sm uppercase tracking-[0.14em] text-slate-500"><?php echo e($abbreviation); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($synonyms !== [] || $variations !== [] || $tags !== []): ?>
                <div class="grid gap-5 border-b border-slate-200 py-6 md:grid-cols-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($synonyms !== []): ?>
                        <div>
                            <p class="text-[0.72rem] font-semibold uppercase tracking-[0.18em] text-slate-500">Sinonimi</p>
                            <p class="mt-2 text-base leading-8 text-slate-700"><?php echo e(implode(', ', $synonyms)); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variations !== []): ?>
                        <div>
                            <p class="text-[0.72rem] font-semibold uppercase tracking-[0.18em] text-slate-500">Varijante</p>
                            <p class="mt-2 text-base leading-8 text-slate-700"><?php echo e(implode(', ', $variations)); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tags !== []): ?>
                        <div>
                            <p class="text-[0.72rem] font-semibold uppercase tracking-[0.18em] text-slate-500">Oznake</p>
                            <p class="mt-2 text-base leading-8 text-slate-700"><?php echo e(implode(', ', $tags)); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="content-richtext pt-6">
                <?php echo $glossaryTermBodyHtml ?: '<p>Ovaj pojam trenutno nema dodatni opis.</p>'; ?>

            </div>
        </article>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($relatedGlossaryTerms !== []): ?>
            <section class="mt-12 border-t border-slate-300 pt-8">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-[0.72rem] font-semibold uppercase tracking-[0.18em] text-[#ab8d52]"><?php echo e($glossaryPageTitle); ?></p>
                        <h2 class="mt-2 text-[clamp(1.7rem,2.6vw,2.2rem)] font-semibold tracking-[-0.04em] text-slate-900">Povezani pojmovi</h2>
                    </div>
                    <a href="<?php echo e(route('glossary.index')); ?>" class="text-sm font-medium text-[#ab8d52] underline-offset-4 hover:underline">Prikaži sve</a>
                </div>

                <div class="mt-6 divide-y divide-slate-200 border-y border-slate-200">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $relatedGlossaryTerms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $relatedTerm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="py-4">
                            <h3 class="text-xl text-slate-900">
                                <a href="<?php echo e($relatedTerm['url']); ?>" class="hover:underline"><?php echo e($relatedTerm['title']); ?></a>
                            </h3>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($relatedTerm['excerpt'] !== ''): ?>
                                <p class="mt-2 max-w-4xl text-base leading-8 text-slate-600"><?php echo e($relatedTerm['excerpt']); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </section>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bottomBlocks->isNotEmpty()): ?>
        <section class="mx-auto mt-2 w-full max-w-[1320px] px-4 pb-10 sm:px-6 lg:px-8"><?php echo $__env->make('components.content-placement', ['items' => $bottomBlocks], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/glossary-term.blade.php ENDPATH**/ ?>