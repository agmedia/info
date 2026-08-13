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
    $hiddenCategoryCount = max(0, $categories->count() - $categoryPreviewLimit);
    $baseIndexUrl = route('blog.index').($searchTerm !== '' ? '?'.http_build_query(['q' => $searchTerm]) : '');
    $hasSelectedHiddenCategory = $categories
        ->slice($categoryPreviewLimit)
        ->contains(fn (array $category): bool => in_array((int) $category['id'], $activeCategoryIds, true));
    $headingWords = static fn (string $title): array => preg_split('/\s+/u', trim($title), -1, PREG_SPLIT_NO_EMPTY) ?: [];
?>

<?php $__env->startSection('title', $heroTitle !== '' ? $heroTitle : __('ui.blog.page_title')); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startSection('content'); ?>
    <div class="ac-blog-page">
        <section class="values-section services-index-intro ac-blog-intro" aria-labelledby="ac-blog-title">
            <div class="values-inner services-index-intro-layout ac-blog-intro-layout">
                <div class="values-intro">
                    <h1 class="values-title services-index-intro-title ac-blog-intro-title" id="ac-blog-title" data-words-slide-from-right aria-label="<?php echo e($heroTitle); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($heroTitle); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="values-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h1>
                </div>

                <div class="values-copy services-index-intro-copy ac-blog-intro-copy content-reveal" data-image-reveal>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($heroIntro !== ''): ?>
                        <p><?php echo e($heroIntro); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($heroCtaLabel !== '' && $heroCtaUrl !== ''): ?>
                        <a href="<?php echo e($heroCtaUrl); ?>" class="services-index-inline-link ac-blog-intro-link">
                            <span><?php echo e($heroCtaLabel); ?></span>
                            <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <section class="ac-blog-list-section" aria-label="<?php echo e(__('ui.blog.title')); ?>">
            <div class="ac-blog-container ac-blog-list-shell">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categories->isNotEmpty()): ?>
                <section class="ac-blog-category-nav" aria-labelledby="ac-blog-category-nav-title">
                    <h2 class="visually-hidden" id="ac-blog-category-nav-title"><?php echo e(__('ui.blog.browse_categories')); ?></h2>

                    <div class="front-scroll-rail ac-blog-category-rail <?php echo e($hasMoreCategories ? 'has-more-items' : ''); ?>">
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
                            <summary class="ac-blog-filter-more-toggle ac-blog-category-more-toggle">
                                <span class="ac-blog-category-more-count">+<?php echo e($hiddenCategoryCount); ?></span>
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
                    <div class="ac-blog-grid ac-blog-grid--index">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('front.desktop.blog.partials.card', [
                                'post' => $post,
                                'locale' => $locale,
                                'fallbackLocale' => $fallbackLocale,
                                'revealIndex' => $loop->index % 3,
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
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('front-theme/styles/pages/blog.css')); ?>?v=<?php echo e(filemtime(public_path('front-theme/styles/pages/blog.css'))); ?>">
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/blog/index.blade.php ENDPATH**/ ?>