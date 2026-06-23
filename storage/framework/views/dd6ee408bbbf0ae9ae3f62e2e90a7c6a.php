<?php $__env->startSection('title', $servicePageTitle ?? 'Usluge'); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('front.desktop.partials.service-pillars-showcase', [
        'sectionId' => 'ac-services-index',
        'headingLevel' => 1,
        'titleLead' => 'Naše usluge',
        'titleAccent' => '',
        'intro' => 'Kroz integrirani pristup reviziji, računovodstvu i financijskom savjetovanju stvaramo dodatnu vrijednost pomažući klijentima da posluju sigurnije, transparentnije i učinkovitije.',
        'variant' => 'image',
        'outro' => [],
        'cards' => $primaryServicePillars ?? [],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/services.blade.php ENDPATH**/ ?>