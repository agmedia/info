<?php
    $children = collect($child['children'] ?? []);
    $padding = 1.25 + ($level * 0.9);
    $textSizeClass = match (true) {
        $level >= 2 => 'text-[12px]',
        $level === 1 => 'text-[12.5px]',
        default => 'text-[13px]',
    };
    $labelWeightClass = $level === 0 ? 'font-semibold' : ($level === 1 ? 'font-medium' : 'font-normal');
    $leafWeightClass = $level === 0 ? 'font-medium' : ($level === 1 ? 'font-normal' : 'font-light');
    $target = !empty($child['open_in_new_tab']) ? '_blank' : null;
    $rel = !empty($child['open_in_new_tab']) ? 'noopener noreferrer' : null;
?>

<li class="border-b">
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($children->isNotEmpty()): ?>
        <details class="group/subnav">
            <summary class="front-mobile-subnav-summary flex min-h-[52px] cursor-pointer list-none items-center justify-between gap-3 py-3 pr-3 <?php echo e($textSizeClass); ?>" style="padding-left: <?php echo e($padding); ?>rem;">
                <span class="min-w-0 truncate pr-2 <?php echo e($labelWeightClass); ?>"><?php echo e($child['label'] ?? ''); ?></span>
                <span class="front-mobile-nav-toggle inline-flex h-7 w-7 items-center justify-center text-[18px] font-light leading-none group-open/subnav:hidden">+</span>
                <span class="front-mobile-nav-toggle hidden h-7 w-7 items-center justify-center text-[18px] font-light leading-none group-open/subnav:inline-flex">-</span>
            </summary>
            <ul class="bg-white/[0.02]">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nestedChild): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('front.desktop.partials.main-nav-mobile-child', ['child' => $nestedChild, 'level' => $level + 1], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>
        </details>
    <?php else: ?>
        <a href="<?php echo e($child['url'] ?? '#'); ?>" class="front-mobile-subnav-link flex min-h-[52px] items-center py-3 <?php echo e($textSizeClass); ?> <?php echo e($leafWeightClass); ?>" style="padding-left: <?php echo e($padding); ?>rem;" <?php if($target): ?> target="<?php echo e($target); ?>" rel="<?php echo e($rel); ?>" <?php endif; ?>>
            <?php echo e($child['label'] ?? ''); ?>

        </a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</li>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/partials/main-nav-mobile-child.blade.php ENDPATH**/ ?>