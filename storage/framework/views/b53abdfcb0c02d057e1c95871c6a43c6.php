<?php
    $sectionTitle = $translation?->title ?: 'Shop by category';
    $sectionSubtitle = $translation?->subtitle ?: 'Locker-like layout rhythm with a cleaner ecommerce direction for AGShop.';
    $itemCtaLabel = $translation?->cta_label ?: 'Explore collection';
?>

<section id="categories" class="front-section-surface rounded-[2rem] py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mb-10">
            <p class="front-kicker">Collections</p>
            <h2 class="mt-3 text-4xl font-semibold tracking-[-0.02em] text-white"><?php echo e($sectionTitle); ?></h2>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sectionSubtitle !== ''): ?>
                <p class="mt-4 max-w-2xl text-lg text-white/70"><?php echo e($sectionSubtitle); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $ct = $category->translations->firstWhere('locale', app()->getLocale())
                        ?? $category->translations->firstWhere('locale', config('app.locale'));
                    $categoryName = $ct?->name ?: $category->code;
                    $categoryDesc = trim((string) ($ct?->clean_description ?? ''));
                ?>
                <article class="rounded-3xl border border-white/10 bg-white/5 p-6 transition hover:-translate-y-0.5 hover:border-fuchsia-300/40 hover:bg-white/10">
                    <div class="h-32 rounded-2xl bg-gradient-to-br from-fuchsia-500/35 via-purple-600/20 to-slate-950/70"></div>
                    <h3 class="mt-5 text-xl font-semibold text-white"><?php echo e($categoryName); ?></h3>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categoryDesc !== ''): ?>
                        <p class="mt-3 text-sm text-white/70"><?php echo e($categoryDesc); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <a href="#" class="mt-4 inline-flex text-xs font-semibold uppercase tracking-[0.1em] text-fuchsia-300 hover:text-fuchsia-200"><?php echo e($itemCtaLabel); ?></a>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="rounded-3xl border border-dashed border-white/30 bg-white/5 p-7 text-sm text-white/65 sm:col-span-2 lg:col-span-4">
                    No categories selected for this block.
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/content-blocks/instances/front-shop-by-category.blade.php ENDPATH**/ ?>