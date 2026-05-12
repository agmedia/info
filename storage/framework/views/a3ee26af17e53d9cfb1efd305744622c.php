<?php
    $navigationItems = collect($mainNavigation ?? [])->values();
    $hasNavigation = $navigationItems->isNotEmpty();
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasNavigation): ?>
    <div class="front-mobile-nav min-h-0 flex-1 overflow-y-auto border-t px-0 text-sm tracking-[0.03em]">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $navigationItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $target = !empty($item['open_in_new_tab']) ? '_blank' : null;
                $rel = !empty($item['open_in_new_tab']) ? 'noopener noreferrer' : null;
            ?>

            <a href="<?php echo e($item['url'] ?? '#'); ?>" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold" <?php if($target): ?> target="<?php echo e($target); ?>" rel="<?php echo e($rel); ?>" <?php endif; ?>><?php echo e($item['label']); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <a href="<?php echo e(route('assessment.create')); ?>" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold">Zatraži ponudu</a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showLeaseCalculatorLink ?? false): ?>
            <a href="<?php echo e(route('lease-calculator.show')); ?>" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold">MSFI 16 Kalkulator</a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php else: ?>
    <nav class="front-mobile-nav min-h-0 flex-1 overflow-y-auto border-t px-0 text-sm tracking-[0.03em]">
        <a href="<?php echo e(route('blog.index')); ?>" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold"><?php echo e(__('ui.front.desktop.nav.blog')); ?></a>
        <a href="<?php echo e(route('faq.index')); ?>" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold"><?php echo e(__('ui.front.desktop.nav.faq')); ?></a>
        <a href="<?php echo e(route('contact.create')); ?>" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold"><?php echo e(__('ui.front.desktop.nav.contact')); ?></a>
        <a href="<?php echo e(route('assessment.create')); ?>" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold">Zatraži ponudu</a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showLeaseCalculatorLink ?? false): ?>
            <a href="<?php echo e(route('lease-calculator.show')); ?>" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold">MSFI 16 Kalkulator</a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </nav>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/partials/main-nav-mobile.blade.php ENDPATH**/ ?>