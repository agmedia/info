<?php $__env->startSection('title', $servicePageTitle ?? 'Usluge'); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startSection('content'); ?>
    <section class="ac-services-index" aria-labelledby="ac-services-index-title">
        <h1 id="ac-services-index-title" class="sr-only"><?php echo e($servicePageTitle ?? 'Usluge'); ?></h1>

        <div class="ac-services-index-grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $serviceCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($card['url']); ?>" class="ac-services-index-card">
                    <img
                        src="<?php echo e($card['image_url']); ?>"
                        alt=""
                        aria-hidden="true"
                        loading="<?php echo e($loop->index < 3 ? 'eager' : 'lazy'); ?>"
                        decoding="async"
                    >
                    <span class="ac-services-index-card-shade" aria-hidden="true"></span>
                    <span class="ac-services-index-card-title"><?php echo e($card['title']); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/services.blade.php ENDPATH**/ ?>