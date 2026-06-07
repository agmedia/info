<?php
    $resolvedLink = $item['resolved_link'] ?? ['url' => ''];
    $itemUrl = trim((string) ($resolvedLink['url'] ?? ''));
    $dateLabel = trim((string) ($item['date_label'] ?? ''));
    $dateValue = trim((string) ($item['date_value'] ?? ''));
?>

<li class="<?php echo e($itemUrl !== '' ? 'is-linked' : 'is-static'); ?>">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($itemUrl !== ''): ?>
        <a
            href="<?php echo e($itemUrl); ?>"
            <?php if($resolvedLink['open_in_new_tab'] ?? false): ?> target="_blank" rel="<?php echo e($resolvedLink['rel'] ?? 'noopener noreferrer'); ?>" <?php endif; ?>
        >
            <span class="ac-eu-call-item-title"><?php echo e($item['title'] ?? ''); ?></span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dateLabel !== '' && $dateValue !== ''): ?>
                <span class="ac-eu-call-item-date"><?php echo e($dateLabel); ?>: <?php echo e($dateValue); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </a>
    <?php else: ?>
        <div class="ac-eu-call-item-row">
            <span class="ac-eu-call-item-title"><?php echo e($item['title'] ?? ''); ?></span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dateLabel !== '' && $dateValue !== ''): ?>
                <span class="ac-eu-call-item-date"><?php echo e($dateLabel); ?>: <?php echo e($dateValue); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</li>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/partials/eu-funds-call-item.blade.php ENDPATH**/ ?>