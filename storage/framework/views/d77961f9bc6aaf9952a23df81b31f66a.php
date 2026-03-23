<section class="rounded-2xl border border-slate-200 bg-white p-6">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($translation?->title)): ?>
        <h2 class="text-xl font-semibold text-slate-900"><?php echo e($translation->title); ?></h2>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="prose prose-slate mt-3 max-w-none">
        <?php echo $translation->body_html ?? ''; ?>

    </div>
</section>

<?php /**PATH /Users/tomek/Herd/info/resources/views/front/content-blocks/types/rich_text.blade.php ENDPATH**/ ?>