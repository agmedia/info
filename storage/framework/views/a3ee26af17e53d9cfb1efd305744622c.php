<?php
    $navigationItems = collect($mainNavigation ?? [])->reject(function ($item) {
        $label = trim((string) ($item['label'] ?? ''));
        $url = rtrim((string) ($item['url'] ?? ''), '/');
        $homeUrl = rtrim(route('home'), '/');

        return $label === 'Početna' || $url === $homeUrl;
    })->values();
    $hasNavigation = $navigationItems->isNotEmpty();
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasNavigation): ?>
    <div class="front-mobile-nav min-h-0 flex-1 overflow-y-auto border-t px-0 text-sm tracking-[0.03em]">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $navigationItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $children = collect($item['children'] ?? []);
                $hasChildren = $children->isNotEmpty();
                $target = !empty($item['open_in_new_tab']) ? '_blank' : null;
                $rel = !empty($item['open_in_new_tab']) ? 'noopener noreferrer' : null;
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasChildren): ?>
                <details class="group/nav border-b">
                    <summary class="front-mobile-nav-summary flex min-h-[56px] cursor-pointer list-none items-center justify-between px-4 py-3">
                        <span class="min-w-0 truncate pr-3 text-[14px] font-semibold"><?php echo e($item['label']); ?></span>
                        <span class="front-mobile-nav-toggle inline-flex h-8 w-8 items-center justify-center text-[21px] font-light leading-none group-open/nav:hidden">+</span>
                        <span class="front-mobile-nav-toggle hidden h-8 w-8 items-center justify-center text-[21px] font-light leading-none group-open/nav:inline-flex">-</span>
                    </summary>
                    <ul class="px-0 pb-0 text-[13px]">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('front.desktop.partials.main-nav-mobile-child', ['child' => $child, 'level' => 0], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                </details>
            <?php else: ?>
                <a href="<?php echo e($item['url'] ?? '#'); ?>" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold" <?php if($target): ?> target="<?php echo e($target); ?>" rel="<?php echo e($rel); ?>" <?php endif; ?>><?php echo e($item['label']); ?></a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <a href="<?php echo e(route('assessment.create')); ?>" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold">Procjena suradnje</a>
        <a href="<?php echo e(route('lease-calculator.show')); ?>" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold">MSFI 16 Kalkulator</a>
    </div>
<?php else: ?>
    <nav class="front-mobile-nav min-h-0 flex-1 overflow-y-auto border-t px-0 text-sm tracking-[0.03em]">
        <a href="<?php echo e(route('blog.index')); ?>" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold"><?php echo e(__('ui.front.desktop.nav.blog')); ?></a>
        <a href="<?php echo e(route('faq.index')); ?>" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold"><?php echo e(__('ui.front.desktop.nav.faq')); ?></a>
        <a href="<?php echo e(route('contact.create')); ?>" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold"><?php echo e(__('ui.front.desktop.nav.contact')); ?></a>
        <a href="<?php echo e(route('assessment.create')); ?>" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold">Procjena suradnje</a>
        <a href="<?php echo e(route('lease-calculator.show')); ?>" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold">MSFI 16 Kalkulator</a>
    </nav>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/partials/main-nav-mobile.blade.php ENDPATH**/ ?>