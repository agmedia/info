<?php
    $payload = $block->payload ?? [];
?>

<section class="grid gap-4 md:grid-cols-2">
    <div class="rounded-2xl border border-slate-200 bg-white p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e($payload['left_label'] ?? 'Left'); ?></p>
        <h3 class="mt-2 text-xl font-semibold text-slate-900"><?php echo e($translation->title ?? $block->name); ?></h3>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($translation?->subtitle)): ?>
            <p class="mt-2 text-sm text-slate-600"><?php echo e($translation->subtitle); ?></p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e($payload['right_label'] ?? 'Right'); ?></p>
        <div class="mt-2 text-sm text-slate-700"><?php echo $translation->body_html ?? ''; ?></div>
    </div>
</section>

<?php /**PATH /Users/tomek/Herd/info/resources/views/front/content-blocks/types/split_message.blade.php ENDPATH**/ ?>