<?php
    $hasNavigation = !empty($mainNavigation ?? []);
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasNavigation): ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $mainNavigation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $children = collect($item['children'] ?? []);
            $hasChildren = $children->isNotEmpty();
            $href = (string) ($item['url'] ?? '#');
            $target = !empty($item['open_in_new_tab']) ? '_blank' : null;
            $rel = !empty($item['open_in_new_tab']) ? 'noopener noreferrer' : null;
        ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasChildren): ?>
            <div class="group/nav relative">
                <a href="<?php echo e($href); ?>" class="front-nav-link inline-flex items-center gap-1 py-6 transition" <?php if($target): ?> target="<?php echo e($target); ?>" rel="<?php echo e($rel); ?>" <?php endif; ?>>
                    <span class="front-nav-link-label border-b pb-0.5 transition"><?php echo e($item['label']); ?></span>
                    <span class="text-[10px] opacity-70">▼</span>
                </a>

                <div class="front-nav-dropdown invisible pointer-events-none absolute left-1/2 top-full z-50 min-w-[19rem] -translate-x-1/2 p-2 opacity-0 transition-all duration-150 group-hover/nav:visible group-hover/nav:pointer-events-auto group-hover/nav:opacity-100">
                    <ul class="front-nav-dropdown-list space-y-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('front.desktop.partials.main-nav-child', ['child' => $child, 'level' => 0], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                </div>
            </div>
        <?php else: ?>
            <a href="<?php echo e($href); ?>" class="front-nav-link inline-flex items-center py-6 transition" <?php if($target): ?> target="<?php echo e($target); ?>" rel="<?php echo e($rel); ?>" <?php endif; ?>>
                <span class="front-nav-link-label border-b pb-0.5 transition"><?php echo e($item['label']); ?></span>
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php else: ?>
    <a href="<?php echo e(route('home')); ?>" class="front-nav-link inline-flex items-center py-6"><span class="front-nav-link-label border-b pb-0.5 transition">Home</span></a>
    <a href="<?php echo e(route('blog.index')); ?>" class="front-nav-link inline-flex items-center py-6"><span class="front-nav-link-label border-b pb-0.5 transition"><?php echo e(__('ui.front.desktop.nav.blog')); ?></span></a>
    <a href="<?php echo e(route('faq.index')); ?>" class="front-nav-link inline-flex items-center py-6"><span class="front-nav-link-label border-b pb-0.5 transition"><?php echo e(__('ui.front.desktop.nav.faq')); ?></span></a>
    <a href="<?php echo e(route('contact.create')); ?>" class="front-nav-link inline-flex items-center py-6"><span class="front-nav-link-label border-b pb-0.5 transition"><?php echo e(__('ui.front.desktop.nav.contact')); ?></span></a>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/partials/main-nav.blade.php ENDPATH**/ ?>