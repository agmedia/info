<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'breadcrumbs' => [],
    'sectionClass' => '',
    'containerClass' => 'mx-auto w-full max-w-[1320px] px-4 sm:px-6 lg:px-8',
    'heroClass' => '',
    'panelClass' => '',
    'breadcrumbClass' => '',
    'trackClass' => '',
    'centerBreadcrumbs' => true,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'breadcrumbs' => [],
    'sectionClass' => '',
    'containerClass' => 'mx-auto w-full max-w-[1320px] px-4 sm:px-6 lg:px-8',
    'heroClass' => '',
    'panelClass' => '',
    'breadcrumbClass' => '',
    'trackClass' => '',
    'centerBreadcrumbs' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $bandClass = trim('ac-page-title-band '.$sectionClass);
    $heroClassName = trim('ac-page-title-hero '.$heroClass);
    $panelClassName = trim('ac-page-title-panel '.$panelClass);
    $breadcrumbClassName = trim('front-scroll-breadcrumb ac-page-title-breadcrumb '.$breadcrumbClass);
    $trackClassName = trim('front-scroll-breadcrumb-track '.($centerBreadcrumbs ? 'is-centered ' : '').$trackClass);
?>

<section class="<?php echo e($bandClass); ?>">
    <div class="<?php echo e($containerClass); ?>">
        <section class="<?php echo e($heroClassName); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($breadcrumbs !== []): ?>
                <nav aria-label="Breadcrumb" class="<?php echo e($breadcrumbClassName); ?>">
                    <ol class="<?php echo e($trackClassName); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $breadcrumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $label = trim((string) ($breadcrumb['label'] ?? ''));
                                $url = trim((string) ($breadcrumb['url'] ?? ''));
                                $current = (bool) ($breadcrumb['current'] ?? false);
                                $linkClass = trim((string) ($breadcrumb['link_class'] ?? ''));
                                $currentClass = trim((string) ($breadcrumb['current_class'] ?? ''));
                                $title = trim((string) ($breadcrumb['title'] ?? ''));
                            ?>

                            <?php if($label === '') continue; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $loop->first): ?>
                                <li class="front-scroll-breadcrumb-separator" aria-hidden="true">/</li>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <li>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($url !== '' && ! $current): ?>
                                    <a href="<?php echo e($url); ?>" class="<?php echo e(trim('front-scroll-breadcrumb-link '.$linkClass)); ?>"><?php echo e($label); ?></a>
                                <?php else: ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($title !== ''): ?>
                                        <span class="<?php echo e(trim('front-scroll-breadcrumb-current '.$currentClass)); ?>" title="<?php echo e($title); ?>"><?php echo e($label); ?></span>
                                    <?php else: ?>
                                        <span class="<?php echo e(trim('front-scroll-breadcrumb-current '.$currentClass)); ?>"><?php echo e($label); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ol>
                </nav>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="<?php echo e($panelClassName); ?>">
                <?php echo e($slot); ?>

            </div>
        </section>
    </div>
</section>
<?php /**PATH /Users/tomek/Herd/info/resources/views/components/front/page-title-band.blade.php ENDPATH**/ ?>