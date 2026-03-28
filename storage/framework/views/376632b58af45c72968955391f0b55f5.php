<?php $__env->startSection('title', __('ui.search.page_title')); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('front.desktop.search.index-content', [
        'searchQuery' => $searchQuery,
        'searchSections' => $searchSections,
        'searchTotalResults' => $searchTotalResults,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/mobile/search/index.blade.php ENDPATH**/ ?>