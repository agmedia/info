<?php
    $title = $translation?->title ?: 'Modern essentials, built for everyday carry.';
    $subtitle = $translation?->subtitle ?: 'AGShop combines durable materials, clean silhouettes and practical storage to keep your daily setup lightweight and ready.';
    $primaryCtaLabel = $translation?->cta_label ?: 'Shop featured';
    $primaryCtaUrl = $translation?->cta_url ?: '#featured';
?>

<div class="front-section-surface max-w-4xl rounded-[2rem] px-8 py-12 text-white lg:px-12 lg:py-14">
    <p class="front-kicker inline-flex items-center gap-2 rounded-full border border-fuchsia-300/35 bg-fuchsia-500/10 px-4 py-2 text-[0.68rem]">
        <span class="h-2 w-2 rounded-full bg-fuchsia-300"></span>
        New season collection live now
    </p>

    <h1 class="mt-6 text-4xl font-semibold leading-[1.03] tracking-[-0.03em] lg:text-6xl">
        <?php echo nl2br(e($title)); ?>

    </h1>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subtitle !== ''): ?>
        <p class="mt-6 max-w-2xl text-lg text-white/80"><?php echo e($subtitle); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="mt-10 flex flex-wrap items-center gap-4">
        <a href="<?php echo e($primaryCtaUrl); ?>" class="front-cta-primary rounded-xl px-6 py-3 text-sm uppercase tracking-[0.12em]">
            <?php echo e($primaryCtaLabel); ?>

        </a>
        <a href="#categories" class="front-cta-outline rounded-xl px-6 py-3 text-sm uppercase tracking-[0.12em]">
            Browse categories
        </a>
    </div>
</div>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/content-blocks/types/desktop_hero_banner.blade.php ENDPATH**/ ?>