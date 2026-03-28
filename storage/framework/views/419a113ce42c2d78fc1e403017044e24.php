<?php $__env->startSection('title', __('resources.page_title')); ?>
<?php $__env->startSection('main_class', 'w-full bg-white px-0 py-0 pb-0'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $pageTitleBreadcrumbs = [
            ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
            ['label' => __('resources.page_title'), 'current' => true],
        ];
    ?>

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
            <h1><?php echo e(__('resources.page_title')); ?></h1>
            <p><?php echo e(__('resources.index.intro')); ?></p>
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

    <section class="bg-white">
        <div class="mx-auto w-full max-w-[1320px] px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <section class="<?php echo e($loop->first ? '' : 'mt-14'); ?>">
                    <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500"><?php echo e($group['label']); ?></p>
                            <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950"><?php echo e($group['label']); ?></h2>
                            <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600 sm:text-base"><?php echo e($group['description']); ?></p>
                        </div>
                        <div class="ac-resource-index-count inline-flex items-center border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700">
                            <?php echo e(number_format($group['items']->count())); ?>

                        </div>
                    </div>

                    <div class="mt-6 grid gap-5 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $group['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $accentClasses = match ($item['group_code']) {
                                    'sector-analysis' => 'from-[#0f172a] via-[#1d4ed8] to-[#f59e0b]',
                                    'transaction-analysis' => 'from-[#102542] via-[#0f766e] to-[#fbbf24]',
                                    default => 'from-[#111827] via-[#334155] to-[#f8b84e]',
                                };
                            ?>
                            <article class="ac-resource-index-card group flex h-full flex-col overflow-hidden border border-slate-200 bg-white shadow-[0_18px_60px_-42px_rgba(15,23,42,0.35)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_30px_80px_-40px_rgba(15,23,42,0.42)]">
                                <a href="<?php echo e(route('resources.show', ['slug' => $item['slug']])); ?>" class="block">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item['cover_image_url']): ?>
                                        <div class="flex aspect-[3/4] items-center justify-center overflow-hidden bg-slate-100">
                                            <img src="<?php echo e($item['cover_image_url']); ?>" alt="<?php echo e($item['title']); ?>" class="h-full w-full object-cover object-top transition duration-500 group-hover:scale-[1.02]">
                                        </div>
                                    <?php else: ?>
                                        <div class="relative aspect-[3/4] overflow-hidden bg-gradient-to-br <?php echo e($accentClasses); ?> p-6 text-white">
                                            <div class="absolute -right-12 top-5 h-28 w-28 rounded-full bg-white/12 blur-2xl"></div>
                                            <div class="absolute -bottom-10 left-5 h-24 w-24 rounded-full bg-amber-200/25 blur-2xl"></div>
                                            <div class="relative flex h-full flex-col justify-between">
                                                <span class="inline-flex w-fit rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.26em] text-white/80">
                                                    <?php echo e($item['group_label']); ?>

                                                </span>
                                                <h3 class="max-w-[16rem] text-2xl font-semibold tracking-tight"><?php echo e($item['title']); ?></h3>
                                            </div>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </a>

                                <div class="flex flex-1 flex-col justify-between p-5">
                                    <h3 class="text-lg font-semibold leading-snug tracking-tight text-slate-950">
                                        <a href="<?php echo e(route('resources.show', ['slug' => $item['slug']])); ?>" class="transition-colors hover:text-[#173b5d]">
                                            <?php echo e($item['title']); ?>

                                        </a>
                                    </h3>

                                    <div class="mt-6 flex items-center justify-start gap-4">
                                        <a href="<?php echo e(route('resources.show', ['slug' => $item['slug']])); ?>" class="ac-resource-card-cta inline-flex items-center gap-2 border px-4 py-2 text-sm font-semibold transition">
                                            <span><?php echo e(__('resources.index.cta')); ?></span>
                                            <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M4 12L12 4"></path>
                                                <path d="M6 4h6v6"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </section>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="ac-resource-index-empty border border-dashed border-slate-300 bg-white px-6 py-14 text-center text-slate-600">
                    <?php echo e(__('resources.index.empty')); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/resources/index.blade.php ENDPATH**/ ?>