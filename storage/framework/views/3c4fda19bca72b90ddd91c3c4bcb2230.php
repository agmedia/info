<?php
    $analytics = $storeSettings['analytics'] ?? [];
    $analyticsEnabled = (bool) ($analytics['enabled'] ?? false);
    $ga4Id = trim((string) ($analytics['ga4_measurement_id'] ?? ''));
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($analyticsEnabled && $ga4Id !== ''): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo e($ga4Id); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo e($ga4Id); ?>');
    </script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/partials/analytics.blade.php ENDPATH**/ ?>