<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['type' => 'custom', 'size' => 'sm']));

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

foreach (array_filter((['type' => 'custom', 'size' => 'sm']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $typeKey = (string) $type;
?>

<div class="cb-preview cb-preview--<?php echo e($size); ?> cb-preview--<?php echo e($typeKey); ?>" aria-hidden="true">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($typeKey === 'banner'): ?>
        <div class="cb-box cb-hero-media"></div>
        <div class="cb-line cb-w-85"></div>
        <div class="cb-line cb-w-60"></div>
        <div class="cb-pill cb-w-35"></div>
    <?php elseif($typeKey === 'five_star_reviews_carousel'): ?>
        <div class="cb-cards3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 3; $i++): ?>
                <div class="cb-card-mini">
                    <div class="cb-line cb-w-40"></div>
                    <div class="cb-line cb-w-85"></div>
                    <div class="cb-line cb-w-75"></div>
                    <div class="cb-line cb-w-50"></div>
                </div>
            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php elseif($typeKey === 'categories' || $typeKey === 'blogs' || $typeKey === 'blog_grid_3'): ?>
        <div class="cb-cards3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 3; $i++): ?>
                <div class="cb-card-mini">
                    <div class="cb-box cb-mini-media"></div>
                    <div class="cb-line cb-w-75"></div>
                    <div class="cb-line cb-w-55"></div>
                </div>
            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php elseif($typeKey === 'hero_slider'): ?>
        <div class="cb-cards3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 3; $i++): ?>
                <div class="cb-card-mini">
                    <div class="cb-box cb-mini-media"></div>
                    <div class="cb-line cb-w-75"></div>
                    <div class="cb-line cb-w-50"></div>
                </div>
            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php elseif($typeKey === 'cards_2'): ?>
        <div class="cb-split">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 2; $i++): ?>
                <div class="cb-card-mini">
                    <div class="cb-box cb-mini-media"></div>
                    <div class="cb-line cb-w-75"></div>
                    <div class="cb-line cb-w-55"></div>
                </div>
            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php elseif($typeKey === 'hero_single' || $typeKey === 'hero_main'): ?>
        <div class="cb-box cb-hero-media"></div>
        <div class="cb-line cb-w-80"></div>
        <div class="cb-line cb-w-55"></div>
        <div class="cb-pill cb-w-35"></div>
    <?php elseif($typeKey === 'desktop_hero_banner'): ?>
        <div class="cb-line cb-w-55"></div>
        <div class="cb-line cb-w-90"></div>
        <div class="cb-line cb-w-70"></div>
        <div class="cb-line cb-w-60"></div>
        <div class="cb-banner">
            <div class="cb-pill cb-w-35"></div>
            <div class="cb-pill cb-w-40"></div>
        </div>
    <?php elseif($typeKey === 'full_width_image_slider'): ?>
        <div class="cb-box cb-hero-media"></div>
        <div class="cb-banner">
            <div class="cb-pill cb-w-20"></div>
            <div class="cb-pill cb-w-20"></div>
            <div class="cb-pill cb-w-20"></div>
        </div>
    <?php elseif($typeKey === 'dual_image_cta'): ?>
        <div class="cb-split">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 2; $i++): ?>
                <div class="cb-card-mini">
                    <div class="cb-box cb-mini-media"></div>
                    <div class="cb-line cb-w-60"></div>
                    <div class="mt-2 flex gap-2">
                        <div class="cb-pill cb-w-30"></div>
                        <div class="cb-pill cb-w-30"></div>
                    </div>
                </div>
            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php elseif($typeKey === 'mobile_hero_banner'): ?>
        <div class="cb-box cb-hero-media"></div>
        <div class="cb-line cb-w-75"></div>
        <div class="cb-line cb-w-60"></div>
        <div class="cb-pill cb-w-30"></div>
    <?php elseif($typeKey === 'hero_highlights_strip'): ?>
        <div class="cb-cards3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 3; $i++): ?>
                <div class="cb-card-mini">
                    <div class="cb-pill cb-w-20"></div>
                    <div class="cb-line cb-w-70"></div>
                    <div class="cb-line cb-w-55"></div>
                </div>
            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php elseif($typeKey === 'split_message'): ?>
        <div class="cb-split">
            <div>
                <div class="cb-line cb-w-85"></div>
                <div class="cb-line cb-w-70"></div>
                <div class="cb-line cb-w-55"></div>
                <div class="cb-pill cb-w-45"></div>
            </div>
            <div class="cb-box cb-split-media"></div>
        </div>
    <?php elseif($typeKey === 'cards_3'): ?>
        <div class="cb-cards3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 3; $i++): ?>
                <div class="cb-card-mini">
                    <div class="cb-box cb-mini-media"></div>
                    <div class="cb-line cb-w-75"></div>
                    <div class="cb-line cb-w-50"></div>
                </div>
            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php elseif($typeKey === 'rich_text'): ?>
        <div class="cb-line cb-w-90"></div>
        <div class="cb-line cb-w-85"></div>
        <div class="cb-line cb-w-80"></div>
        <div class="cb-line cb-w-65"></div>
        <div class="cb-line cb-w-72"></div>
        <div class="cb-line cb-w-58"></div>
    <?php elseif($typeKey === 'cta_banner'): ?>
        <div class="cb-banner">
            <div>
                <div class="cb-line cb-w-85"></div>
                <div class="cb-line cb-w-60"></div>
            </div>
            <div class="cb-pill cb-w-30"></div>
        </div>
    <?php elseif($typeKey === 'dev_polishing'): ?>
        <div class="cb-line cb-w-85"></div>
        <div class="cb-line cb-w-60"></div>
        <div class="cb-box cb-hero-media"></div>
        <div class="cb-banner">
            <div class="cb-line cb-w-70"></div>
            <div class="cb-pill cb-w-35"></div>
        </div>
    <?php else: ?>
        <div class="cb-line cb-w-85"></div>
        <div class="cb-line cb-w-70"></div>
        <div class="cb-line cb-w-55"></div>
        <div class="cb-box cb-custom"></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Users/tomek/Herd/info/resources/views/admin/content/partials/block-type-preview.blade.php ENDPATH**/ ?>