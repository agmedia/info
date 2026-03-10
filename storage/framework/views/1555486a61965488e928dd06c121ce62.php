<?php
    $blogSettings = $storeSettings['blog'] ?? [];
    $defaultHeroTitle = trim((string) ($blogSettings['hero_title'] ?? '')) ?: __('ui.blog.title');
    $heroIntro = trim((string) ($blogSettings['hero_intro'] ?? '')) ?: __('ui.blog.subtitle');
    $heroCtaLabel = trim((string) ($blogSettings['hero_cta_label'] ?? ''));
    $heroCtaUrl = trim((string) ($blogSettings['hero_cta_url'] ?? ''));
    $categoryPreviewLimit = max(1, (int) ($blogSettings['category_preview_limit'] ?? 8));
    $activeCategoryIds = collect($selectedCategoryIds ?? [])->map(fn ($id) => (int) $id)->all();
    $fallbackActiveCategory = count($activeCategoryIds) === 1
        ? collect($selectedCategories ?? [])->first()
        : null;
    $currentCategoryName = trim((string) ($currentCategory['name'] ?? ($fallbackActiveCategory['name'] ?? '')));
    $isCategoryArchive = $currentCategoryName !== '';
    $heroTitle = $isCategoryArchive ? $currentCategoryName : $defaultHeroTitle;
    $hasMoreCategories = $categories->count() > $categoryPreviewLimit;
    $baseIndexUrl = route('blog.index').($searchTerm !== '' ? '?'.http_build_query(['q' => $searchTerm]) : '');
    $hasSelectedHiddenCategory = $categories
        ->slice($categoryPreviewLimit)
        ->contains(fn (array $category): bool => in_array((int) $category['id'], $activeCategoryIds, true));
    $pageTitleBreadcrumbs = [
        ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
    ];

    if ($isCategoryArchive) {
        $pageTitleBreadcrumbs[] = ['label' => __('ui.blog.title'), 'url' => route('blog.index')];
        $pageTitleBreadcrumbs[] = ['label' => $currentCategoryName, 'current' => true];
    } else {
        $pageTitleBreadcrumbs[] = ['label' => __('ui.blog.title'), 'current' => true];
    }
?>

<?php $__env->startSection('title', $heroTitle !== '' ? $heroTitle : __('ui.blog.page_title')); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startSection('content'); ?>
    <div class="ac-blog-page">
        <?php if (isset($component)) { $__componentOriginale6a101278d02d7bbbf9e98ee1142bf75 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.front.page-title-band','data' => ['breadcrumbs' => $pageTitleBreadcrumbs,'sectionClass' => 'ac-blog-title-band','breadcrumbClass' => 'ac-blog-hero-breadcrumb']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('front.page-title-band'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pageTitleBreadcrumbs),'section-class' => 'ac-blog-title-band','breadcrumb-class' => 'ac-blog-hero-breadcrumb']); ?>
            <div class="ac-page-title-copy">
                <h1 id="ac-blog-title"><?php echo e($heroTitle); ?></h1>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($heroIntro !== ''): ?>
                    <p><?php echo e($heroIntro); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($heroCtaLabel !== '' && $heroCtaUrl !== ''): ?>
                    <div class="ac-page-title-actions ac-blog-hero-action">
                        <a href="<?php echo e($heroCtaUrl); ?>" class="front-action-cta">
                            <span><?php echo e($heroCtaLabel); ?></span>
                            <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M4 12L12 4"></path>
                                <path d="M6 4h6v6"></path>
                            </svg>
                        </a>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

        <div class="mx-auto w-full max-w-[1320px] px-4 sm:px-6 lg:px-8">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categories->isNotEmpty()): ?>
                <section class="ac-blog-category-nav" aria-labelledby="ac-blog-category-nav-title">
                    <h2 id="ac-blog-category-nav-title" class="sr-only"><?php echo e(__('ui.blog.browse_categories')); ?></h2>
                    <div class="front-scroll-rail">
                        <div class="front-scroll-rail-track">
                            <a
                                href="<?php echo e($baseIndexUrl); ?>"
                                class="ac-blog-category-chip <?php echo e($activeCategoryIds === [] ? 'is-active' : ''); ?>"
                            >
                                <span><?php echo e(__('ui.blog.all_posts')); ?></span>
                            </a>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories->take($categoryPreviewLimit); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $categoryUrl = trim((string) $category['slug']) !== ''
                                        ? url('/blog/'.$category['slug']).($searchTerm !== '' ? '?'.http_build_query(['q' => $searchTerm]) : '')
                                        : $baseIndexUrl;
                                ?>
                                <a
                                    href="<?php echo e($categoryUrl); ?>"
                                    class="ac-blog-category-chip <?php echo e(in_array((int) $category['id'], $activeCategoryIds, true) ? 'is-active' : ''); ?>"
                                >
                                    <span><?php echo e($category['name']); ?></span>
                                    <span class="ac-blog-category-chip-count"><?php echo e($category['count']); ?></span>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasMoreCategories): ?>
                        <details class="ac-blog-filter-more ac-blog-category-more" @open($hasSelectedHiddenCategory)>
                            <summary class="ac-blog-filter-more-toggle">
                                <span class="label-more"><?php echo e(__('ui.blog.filters.show_more')); ?></span>
                                <span class="label-less"><?php echo e(__('ui.blog.filters.show_less')); ?></span>
                            </summary>
                            <div class="front-scroll-rail mt-3">
                                <div class="front-scroll-rail-track">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories->slice($categoryPreviewLimit); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $categoryUrl = trim((string) $category['slug']) !== ''
                                                ? url('/blog/'.$category['slug']).($searchTerm !== '' ? '?'.http_build_query(['q' => $searchTerm]) : '')
                                                : $baseIndexUrl;
                                        ?>
                                        <a
                                            href="<?php echo e($categoryUrl); ?>"
                                            class="ac-blog-category-chip <?php echo e(in_array((int) $category['id'], $activeCategoryIds, true) ? 'is-active' : ''); ?>"
                                        >
                                            <span><?php echo e($category['name']); ?></span>
                                            <span class="ac-blog-category-chip-count"><?php echo e($category['count']); ?></span>
                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </details>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </section>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($topBlocks->isNotEmpty()): ?>
                <section class="mb-8">
                    <?php echo $__env->make('components.content-placement', ['items' => $topBlocks], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </section>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <section class="ac-blog-content">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($posts->isEmpty()): ?>
                    <div class="ac-blog-empty">
                        <p><?php echo e(__('ui.blog.empty')); ?></p>
                    </div>
                <?php else: ?>
                    <div class="ac-blog-grid">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('front.desktop.blog.partials.card', [
                                'post' => $post,
                                'locale' => $locale,
                                'fallbackLocale' => $fallbackLocale,
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="ac-blog-pagination">
                        <?php echo e($posts->links()); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bottomBlocks->isNotEmpty()): ?>
                <section class="mt-10">
                    <?php echo $__env->make('components.content-placement', ['items' => $bottomBlocks], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </section>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/blog/index.blade.php ENDPATH**/ ?>