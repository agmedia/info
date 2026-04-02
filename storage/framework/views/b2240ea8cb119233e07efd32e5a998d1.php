<?php
    $iconUrl = trim((string) ($iconUrl ?? ''));
    $accentColor = trim((string) ($accentColor ?? '#ab8d52'));
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($iconUrl !== ''): ?>
    <div class="ac-family-hero-badge" aria-hidden="true" style="--ac-family-hero-icon-accent: <?php echo e($accentColor); ?>;">
        <img src="<?php echo e($iconUrl); ?>" alt="" class="ac-family-hero-badge-icon" loading="eager" decoding="async">
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/partials/service-hero-icon-badge.blade.php ENDPATH**/ ?>