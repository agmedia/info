<?php
    $translation = $selectedTranslation
        ?? $page->translations->firstWhere('locale', $locale)
        ?? $page->translations->firstWhere('locale', $fallbackLocale);

    $pageTitleBreadcrumbs = [
        ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
        ['label' => $translation?->title ?? $page->code, 'current' => true],
    ];

    $referenceItems = collect($referenceItems ?? [])->values();
    $pageBodyHtml = (string) ($translation?->body_html ?? '');
    $hasBodyCopy = trim(strip_tags($pageBodyHtml)) !== '';
    $emptyStateTitle = $locale === 'hr'
        ? 'Reference se ažuriraju'
        : 'References are being updated';
    $emptyStateText = $locale === 'hr'
        ? 'Logotipi će uskoro biti dostupni i na ovoj stranici.'
        : 'Reference logos will be available on this page soon.';
?>

<?php $__env->startSection('title', $translation?->title ?? 'Reference'); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startSection('content'); ?>
    <div class="ac-references-page">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($topBlocks->isNotEmpty()): ?>
            <section class="ac-references-blocks ac-references-blocks--top"><?php echo $__env->make('components.content-placement', ['items' => $topBlocks], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if (isset($component)) { $__componentOriginale6a101278d02d7bbbf9e98ee1142bf75 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.front.page-title-band','data' => ['breadcrumbs' => $pageTitleBreadcrumbs,'sectionClass' => 'ac-references-title-band']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('front.page-title-band'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pageTitleBreadcrumbs),'section-class' => 'ac-references-title-band']); ?>
            <div class="ac-page-title-copy">
                <h1><?php echo e($translation?->title ?? $page->code); ?></h1>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($translation?->excerpt)): ?>
                    <p><?php echo e($translation->excerpt); ?></p>
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

        <section class="ac-references-section">
            <div class="ac-references-container">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasBodyCopy): ?>
                    <article class="ac-references-body">
                        <div class="content-richtext">
                            <?php echo $pageBodyHtml; ?>

                        </div>
                    </article>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($referenceItems->isNotEmpty()): ?>
                    <div class="ac-reference-grid">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $referenceItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="ac-reference-card">
                                <div class="ac-reference-logo-shell">
                                    <img
                                        src="<?php echo e($item['url']); ?>"
                                        alt="<?php echo e($item['alt']); ?>"
                                        loading="lazy"
                                        decoding="async"
                                        class="ac-reference-logo-image"
                                    >
                                </div>

                                <h3><?php echo e($item['name']); ?></h3>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($item['caption'] ?? '') !== '' && ($item['caption'] ?? '') !== ($item['name'] ?? '')): ?>
                                    <p><?php echo e($item['caption']); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php else: ?>
                    <article class="ac-reference-empty">
                        <p class="ac-reference-kicker"><?php echo e(__('ALPHA CAPITALIS')); ?></p>
                        <h2><?php echo e($emptyStateTitle); ?></h2>
                        <p><?php echo e($emptyStateText); ?></p>
                    </article>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bottomBlocks->isNotEmpty()): ?>
            <section class="ac-references-blocks ac-references-blocks--bottom"><?php echo $__env->make('components.content-placement', ['items' => $bottomBlocks], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .ac-references-page {
            min-height: 100vh;
            background: #f6f1e7;
            color: #101820;
        }

        .ac-references-container,
        .ac-references-blocks {
            width: min(100% - 2rem, 1320px);
            margin: 0 auto;
        }

        @media (min-width: 640px) {
            .ac-references-container,
            .ac-references-blocks {
                width: min(100% - 3rem, 1272px);
            }
        }

        @media (min-width: 1024px) {
            .ac-references-container,
            .ac-references-blocks {
                width: min(100% - 4rem, 1256px);
            }
        }

        .ac-references-blocks--top {
            padding: 2.5rem 0 0;
        }

        .ac-references-blocks--bottom {
            padding: 2.5rem 0 4rem;
        }

        .ac-references-title-band {
            margin-bottom: 0;
            background: #f6f1e7;
            border-top-color: transparent;
            border-bottom-color: rgba(120, 96, 58, 0.05);
        }

        .ac-references-title-band .ac-page-title-copy h1 {
            color: #101820;
            font-size: 2.65rem;
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: 0;
        }

        .ac-references-title-band .ac-page-title-copy > p,
        .ac-references-title-band .front-scroll-breadcrumb-link,
        .ac-references-title-band .front-scroll-breadcrumb-current,
        .ac-references-title-band .front-scroll-breadcrumb-separator {
            color: #4f4a43;
        }

        .ac-references-title-band .ac-page-title-breadcrumb::before,
        .ac-references-title-band .ac-page-title-breadcrumb::after {
            background: rgba(120, 96, 58, 0.07);
        }

        .ac-references-section {
            padding: clamp(3rem, 5vw, 4.8rem) 0 clamp(5rem, 7vw, 7rem);
            background: #f6f1e7;
        }

        .ac-references-body,
        .ac-reference-card,
        .ac-reference-empty {
            border: 1px solid rgba(171, 141, 82, 0.12);
            border-radius: 8px;
            background: #fff;
            box-shadow: none;
        }

        .ac-references-body {
            margin-bottom: clamp(2rem, 4vw, 3rem);
            padding: clamp(1.1rem, 2vw, 1.55rem);
        }

        .ac-reference-kicker {
            margin: 0;
            color: #7c653b;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .ac-reference-empty h2 {
            margin: 0.7rem 0 0;
            color: #101820;
            font-family: 'Montserrat', sans-serif;
            font-size: clamp(1.9rem, 3vw, 2.5rem);
            font-weight: 700;
            line-height: 1.14;
            letter-spacing: 0;
            text-wrap: balance;
        }

        .ac-reference-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }

        .ac-reference-card {
            display: grid;
            align-content: start;
            gap: 0.9rem;
            min-height: 11.7rem;
            overflow: hidden;
            padding: 1rem;
        }

        .ac-reference-logo-shell {
            display: grid;
            place-items: center;
            min-height: 5.8rem;
            padding: 0.9rem;
            border: 1px solid rgba(15, 42, 67, 0.08);
            border-radius: 8px;
            background: #fff;
        }

        .ac-reference-logo-image {
            display: block;
            width: auto;
            max-width: 100%;
            max-height: 4.2rem;
            object-fit: contain;
            opacity: 0.86;
            filter: grayscale(1) contrast(1.08);
        }

        .ac-reference-card h3 {
            margin: 0;
            color: #101820;
            font-size: 0.98rem;
            font-weight: 700;
            line-height: 1.45;
            letter-spacing: 0;
        }

        .ac-reference-card p,
        .ac-reference-empty > p:last-child {
            margin: 0;
            color: #403a34;
            font-size: 0.92rem;
            line-height: 1.6;
        }

        .ac-reference-empty {
            display: grid;
            justify-items: center;
            padding: clamp(2rem, 5vw, 3.2rem);
            text-align: center;
        }

        .ac-reference-empty > p:last-child {
            max-width: 34rem;
            margin-top: 0.8rem;
        }

        .front-desktop-shell:has(.ac-references-page) .front-footer {
            --front-footer-bg: #071326;
            background: #071326;
        }

        @media (max-width: 1120px) {
            .ac-reference-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 820px) {
            .ac-reference-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .ac-references-container,
            .ac-references-blocks {
                width: min(100% - 1.35rem, 1320px);
            }

            .ac-reference-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/references.blade.php ENDPATH**/ ?>