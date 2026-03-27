<?php
    $basePayload = is_array($block->payload ?? null) ? $block->payload : [];
    $translationPayload = is_array($translation?->payload ?? null) ? $translation->payload : [];
    $payload = array_merge($basePayload, $translationPayload);

    $sectionClass = (string) ($payload['section_class'] ?? 'front-section-surface rounded-3xl p-6');
    $gridClass = (string) ($payload['grid_class'] ?? 'mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4');
    $cardClass = (string) ($payload['card_class'] ?? 'rounded-2xl border border-white/10 bg-white/5 p-4');
?>

<section class="<?php echo e($sectionClass); ?>">
    <h2 class="text-2xl font-semibold tracking-tight text-white"><?php echo e($translation?->title ?: $block->name); ?></h2>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($translation?->subtitle)): ?>
        <p class="mt-2 text-sm text-white/70"><?php echo e($translation->subtitle); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="<?php echo e($gridClass); ?>">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $ct = $category->translations->firstWhere('locale', app()->getLocale())
                    ?? $category->translations->firstWhere('locale', config('app.locale'));
            ?>
            <article class="<?php echo e($cardClass); ?>">
                <div class="h-28 rounded-xl bg-gradient-to-br from-fuchsia-500/35 via-purple-600/20 to-slate-950/70"></div>
                <h3 class="mt-3 text-sm font-semibold text-white"><?php echo e($ct?->name ?? $category->code); ?></h3>
                <p class="mt-1 text-xs uppercase tracking-[0.12em] text-white/55"><?php echo e($category->scope); ?></p>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="rounded-xl border border-dashed border-white/30 bg-white/5 p-4 text-sm text-white/60 sm:col-span-2 xl:col-span-4">
                No categories selected for this block.
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/content-blocks/types/categories.blade.php ENDPATH**/ ?>