<?php
    $basePayload = is_array($block->payload ?? null) ? $block->payload : [];
    $translationPayload = is_array($translation?->payload ?? null) ? $translation->payload : [];
    $payload = array_merge($basePayload, $translationPayload);

    $title = $translation?->title ?: 'Modern essentials';
    $subtitle = $translation?->subtitle ?: 'Browse category picks and essentials.';
    $ctaLabel = $translation?->cta_label ?: 'Shop';
    $ctaUrl = $translation?->cta_url ?: '#categories';
    $sliderId = 'mobile-hero-slider-'.$block->id;

    $slideClassList = ['bg-19', 'bg-18', 'bg-17', 'bg-20'];
    $customClasses = trim((string) ($payload['custom_classes'] ?? ''));
    if ($customClasses !== '') {
        $slideClassList = preg_split('/\s+/', $customClasses) ?: $slideClassList;
    }
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categories->isNotEmpty()): ?>
    <div class="splide single-slider slider-no-arrows slider-no-dots" id="<?php echo e($sliderId); ?>">
        <div class="splide__track">
            <div class="splide__list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $ct = $category->translations->firstWhere('locale', app()->getLocale())
                            ?? $category->translations->firstWhere('locale', config('app.locale'));
                        $categoryName = $ct?->name ?: $category->code;
                        $slideClass = $slideClassList[$index % max(count($slideClassList), 1)] ?? 'bg-19';
                    ?>
                    <div class="splide__slide">
                        <div class="card card-style mb-3 <?php echo e($slideClass); ?>" data-card-height="300">
                            <div class="card-bottom mb-3 ms-3 me-3">
                                <h1 class="color-white font-800 mb-n2"><?php echo e($categoryName); ?></h1>
                                <p class="color-white font-14 mb-2 opacity-60"><?php echo e($subtitle); ?></p>
                                <a href="<?php echo e($ctaUrl); ?>" class="front-link-cta btn btn-xxs rounded-xs bg-white color-black font-700 mt-2">
                                    <?php echo e(trim($ctaLabel.' '.$categoryName)); ?>

                                </a>
                            </div>
                            <div class="card-overlay bg-black opacity-60"></div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="card card-style mb-3 bg-19" data-card-height="300">
        <div class="card-bottom mb-3 ms-3 me-3">
            <h1 class="color-white font-800 mb-n2"><?php echo e($title); ?></h1>
            <p class="color-white font-14 mb-2 opacity-60"><?php echo e($subtitle); ?></p>
            <a href="<?php echo e($ctaUrl); ?>" class="front-link-cta btn btn-xxs rounded-xs bg-white color-black font-700 mt-2"><?php echo e($ctaLabel); ?></a>
        </div>
        <div class="card-overlay bg-black opacity-60"></div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/content-blocks/types/mobile_hero_banner.blade.php ENDPATH**/ ?>