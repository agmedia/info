<?php
    $navigationItems = collect($mainNavigation ?? [])->values();
    $hasNavigation = $navigationItems->isNotEmpty();
    $currentUrl = rtrim(url()->current(), '/');
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasNavigation): ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $navigationItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $href = (string) ($item['url'] ?? '#');
            $normalizedHref = rtrim($href, '/');
            $target = !empty($item['open_in_new_tab']) ? '_blank' : null;
            $rel = !empty($item['open_in_new_tab']) ? 'noopener noreferrer' : null;
            $isActive = $normalizedHref !== '' && $normalizedHref !== '#' && $normalizedHref === $currentUrl;
        ?>

        <a href="<?php echo e($href); ?>" class="front-nav-link <?php echo e($isActive ? 'is-active' : ''); ?> inline-flex items-center py-6 transition" <?php if($target): ?> target="<?php echo e($target); ?>" rel="<?php echo e($rel); ?>" <?php endif; ?>>
            <span class="front-nav-link-label border-b pb-0.5 transition"><?php echo e($item['label']); ?></span>
        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php else: ?>
    <a href="<?php echo e(route('blog.index')); ?>" class="front-nav-link inline-flex items-center py-6"><span class="front-nav-link-label border-b pb-0.5 transition"><?php echo e(__('ui.front.desktop.nav.blog')); ?></span></a>
    <a href="<?php echo e(route('faq.index')); ?>" class="front-nav-link inline-flex items-center py-6"><span class="front-nav-link-label border-b pb-0.5 transition"><?php echo e(__('ui.front.desktop.nav.faq')); ?></span></a>
    <a href="<?php echo e(route('contact.create')); ?>" class="front-nav-link inline-flex items-center py-6"><span class="front-nav-link-label border-b pb-0.5 transition"><?php echo e(__('ui.front.desktop.nav.contact')); ?></span></a>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/partials/main-nav.blade.php ENDPATH**/ ?>