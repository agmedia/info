<?php
    $translation = $selectedTranslation
        ?? $page->translations->firstWhere('locale', $locale)
        ?? $page->translations->firstWhere('locale', $fallbackLocale);

    $referenceItems = collect($referenceItems ?? [])->values();
    $pageBodyHtml = (string) ($translation?->body_html ?? '');
    $hasBodyCopy = trim(strip_tags($pageBodyHtml)) !== '';
    $isCroatian = str_starts_with(strtolower((string) $locale), 'hr');
    $referencesTitleLead = $isCroatian ? 'Naše' : 'Our';
    $referencesTitleAccent = $isCroatian ? 'reference' : 'references';
    $referencesIntro = trim((string) ($translation?->excerpt ?? ''))
        ?: ($isCroatian
            ? 'Odabrani klijenti i partneri koji su nam ukazali povjerenje.'
            : 'Selected clients and partners who have placed their trust in us.');
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

        <section class="values-section services-index-intro ac-references-intro" aria-labelledby="ac-references-title">
            <div class="values-inner services-index-intro-layout">
                <div class="values-intro">
                    <h1 class="values-title services-index-intro-title" id="ac-references-title" data-words-slide-from-right aria-label="<?php echo e($referencesTitleLead); ?> <?php echo e($referencesTitleAccent); ?>">
                        <span class="values-word animation-index-0" aria-hidden="true"><?php echo e($referencesTitleLead); ?></span>
                        <span class="values-word animation-index-1 is-accent" aria-hidden="true"><?php echo e($referencesTitleAccent); ?></span>
                    </h1>
                </div>

                <p class="values-copy services-index-intro-copy content-reveal" data-image-reveal><?php echo e($referencesIntro); ?></p>
            </div>
        </section>

        <section class="ac-references-section">
            <div class="ac-references-container">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasBodyCopy): ?>
                    <article class="ac-references-body content-reveal" data-image-reveal>
                        <div class="content-richtext">
                            <?php echo $pageBodyHtml; ?>

                        </div>
                    </article>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($referenceItems->isNotEmpty()): ?>
                    <div class="ac-reference-grid">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $referenceItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="ac-reference-card" aria-label="<?php echo e($item['name']); ?>">
                                <div class="ac-reference-logo content-reveal animation-index-<?php echo e($loop->index % 2); ?>" data-image-reveal>
                                    <img
                                        src="<?php echo e($item['url']); ?>"
                                        alt="<?php echo e($item['alt']); ?>"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php else: ?>
                    <article class="ac-reference-empty content-reveal" data-image-reveal>
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
    <link rel="stylesheet" href="<?php echo e(asset('front-theme/styles/pages/references.css')); ?>?v=<?php echo e(filemtime(public_path('front-theme/styles/pages/references.css'))); ?>">
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/references.blade.php ENDPATH**/ ?>