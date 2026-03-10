<?php
    $children = collect($child['children'] ?? []);
    $hasChildren = $children->isNotEmpty();
    $childHref = (string) ($child['url'] ?? '#');
    $childTarget = !empty($child['open_in_new_tab']) ? '_blank' : null;
    $childRel = !empty($child['open_in_new_tab']) ? 'noopener noreferrer' : null;
?>
<li class="front-nav-dropdown-item <?php echo e($hasChildren ? 'group/subnav relative' : ''); ?>">
    <a href="<?php echo e($childHref); ?>" class="front-nav-dropdown-link <?php echo e($hasChildren ? 'front-nav-dropdown-link--branch' : ''); ?>" <?php if($childTarget): ?> target="<?php echo e($childTarget); ?>" rel="<?php echo e($childRel); ?>" <?php endif; ?>>
        <span class="min-w-0 truncate"><?php echo e($child['label'] ?? ''); ?></span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasChildren): ?>
            <span class="front-nav-dropdown-caret" aria-hidden="true">›</span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </a>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasChildren): ?>
        <div class="front-nav-submenu invisible pointer-events-none absolute left-full top-0 z-20 w-[19rem] pl-2 opacity-0 transition-all duration-150 group-hover/subnav:visible group-hover/subnav:pointer-events-auto group-hover/subnav:opacity-100">
            <div class="front-nav-submenu-panel p-2">
                <ul class="front-nav-dropdown-list space-y-1">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nestedChild): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('front.desktop.partials.main-nav-child', ['child' => $nestedChild, 'level' => $level + 1], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</li>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/partials/main-nav-child.blade.php ENDPATH**/ ?>