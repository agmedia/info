<?php
    $pageTitleBreadcrumbs = [
        ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
        ['label' => __('ui.search.page_title'), 'current' => true],
    ];
?>

<section class="ac-site-search-page">
    <?php if (isset($component)) { $__componentOriginale6a101278d02d7bbbf9e98ee1142bf75 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.front.page-title-band','data' => ['breadcrumbs' => $pageTitleBreadcrumbs,'sectionClass' => 'ac-site-search-title-band','breadcrumbClass' => 'ac-site-search-breadcrumb']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('front.page-title-band'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pageTitleBreadcrumbs),'section-class' => 'ac-site-search-title-band','breadcrumb-class' => 'ac-site-search-breadcrumb']); ?>
        <div class="ac-page-title-copy ac-site-search-title-copy">
            <p class="ac-site-search-kicker"><?php echo e(__('ui.search.title')); ?></p>
            <h1><?php echo e(__('ui.search.results_title')); ?></h1>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($searchQuery !== ''): ?>
                <div class="ac-site-search-summary">
                    <p><?php echo e(__('ui.search.results_for', ['query' => $searchQuery])); ?></p>
                    <span><?php echo e(__('ui.search.results_count', ['count' => $searchTotalResults])); ?></span>
                </div>
            <?php else: ?>
                <p class="ac-site-search-summary-copy"><?php echo e(__('ui.search.prompt_text')); ?></p>
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

    <div class="mx-auto w-full max-w-[1320px] px-4 py-6 sm:px-6 lg:px-8">
        <div class="ac-site-search-hero">
            <form action="<?php echo e(route('search.index')); ?>" method="get" class="ac-site-search-form" role="search">
                <label for="search-page-query" class="sr-only"><?php echo e(__('ui.search.title')); ?></label>
                <div class="ac-site-search-input-wrap">
                    <span class="ac-site-search-input-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="M20 20l-3.2-3.2"></path>
                        </svg>
                    </span>
                    <input
                        id="search-page-query"
                        type="search"
                        name="q"
                        value="<?php echo e($searchQuery); ?>"
                        class="ac-site-search-input"
                        placeholder="<?php echo e(__('ui.search.input_placeholder')); ?>"
                    >
                </div>
                <button type="submit" class="ac-site-search-button"><?php echo e(__('ui.search.submit')); ?></button>
            </form>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($searchQuery === ''): ?>
            <div class="ac-site-search-empty">
                <h2><?php echo e(__('ui.search.prompt_title')); ?></h2>
                <p><?php echo e(__('ui.search.prompt_text')); ?></p>
            </div>
        <?php elseif($searchTotalResults === 0): ?>
            <div class="ac-site-search-empty">
                <h2><?php echo e(__('ui.search.empty')); ?></h2>
                <p><?php echo e(__('ui.search.empty_hint')); ?></p>
            </div>
        <?php else: ?>
            <div class="ac-site-search-sections">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $searchSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <section class="ac-site-search-section" aria-labelledby="search-section-<?php echo e($section['key']); ?>">
                        <div class="ac-site-search-section-head">
                            <h2 id="search-section-<?php echo e($section['key']); ?>"><?php echo e($section['label']); ?></h2>
                            <span><?php echo e(__('ui.search.results_count', ['count' => $section['total_count']])); ?></span>
                        </div>

                        <div class="ac-site-search-list">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <article class="ac-site-search-card<?php echo e(!empty($item['image_url']) ? ' has-media' : ''); ?><?php echo e($section['key'] === 'blog' ? ' is-blog' : ''); ?>">
                                    <a href="<?php echo e($item['url']); ?>" class="ac-site-search-card-link">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item['image_url'])): ?>
                                            <div class="ac-site-search-media<?php echo e($section['key'] === 'blog' ? ' is-blog' : ''); ?>">
                                                <img src="<?php echo e($item['image_url']); ?>" alt="<?php echo e($item['title']); ?>" loading="lazy" decoding="async">
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <div class="ac-site-search-card-body">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item['eyebrow']) || !empty($item['meta'])): ?>
                                                <div class="ac-site-search-card-meta">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item['eyebrow'])): ?>
                                                        <span><?php echo e($item['eyebrow']); ?></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item['meta'])): ?>
                                                        <span><?php echo e($item['meta']); ?></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            <h3><?php echo e($item['title']); ?></h3>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item['excerpt'])): ?>
                                                <p><?php echo e($item['excerpt']); ?></p>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </a>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </section>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/search/index-content.blade.php ENDPATH**/ ?>