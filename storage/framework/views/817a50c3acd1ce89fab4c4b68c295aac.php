<?php
    $locale = app()->getLocale();
    $fallbackLocale = config('app.locale');
    $translationPayload = is_array($translation?->payload ?? null) ? $translation->payload : [];
    $customClasses = trim((string) ($translationPayload['custom_classes'] ?? ''));
    $slides = $block->getMedia('block_slides')->take(2);
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($slides->isNotEmpty()): ?>
    <section class="relative left-1/2 right-1/2 -ml-[50vw] -mr-[50vw] w-screen <?php echo e($customClasses); ?>">
        <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $slides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $media): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $imageUrl = $media->hasGeneratedConversion('hero_1440x480')
                        ? $media->getUrl('hero_1440x480')
                        : $media->getUrl();
                    $props = (array) ($media->custom_properties ?? []);
                    $slideTitle = trim((string) (
                        data_get($props, "block_title.$locale")
                        ?: data_get($props, "block_title.$fallbackLocale")
                        ?: $media->name
                    ));

                    $cta1Label = trim((string) (
                        data_get($props, "cta_1_label.$locale")
                        ?: data_get($props, "cta_1_label.$fallbackLocale")
                        ?: __('ui.content_blocks.dual_image_cta.default_cta_1')
                    ));
                    $cta1Url = trim((string) (
                        data_get($props, "cta_1_url.$locale")
                        ?: data_get($props, "cta_1_url.$fallbackLocale")
                        ?: '#'
                    ));

                    $cta2Label = trim((string) (
                        data_get($props, "cta_2_label.$locale")
                        ?: data_get($props, "cta_2_label.$fallbackLocale")
                        ?: __('ui.content_blocks.dual_image_cta.default_cta_2')
                    ));
                    $cta2Url = trim((string) (
                        data_get($props, "cta_2_url.$locale")
                        ?: data_get($props, "cta_2_url.$fallbackLocale")
                        ?: '#'
                    ));
                ?>

                <article class="group relative min-h-[360px] overflow-hidden md:min-h-[560px]">
                    <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($slideTitle !== '' ? $slideTitle : $block->name); ?>" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/20 to-transparent"></div>

                    <div class="absolute inset-x-0 bottom-12 px-8 text-center text-white md:bottom-16 md:px-10">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($slideTitle !== ''): ?>
                            <h3 class="text-3xl font-black uppercase tracking-[0.02em] md:text-4xl"><?php echo e($slideTitle); ?></h3>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mx-auto mt-5 flex max-w-[460px] flex-col justify-center gap-2.5 sm:flex-row">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cta1Label !== ''): ?>
                                <a href="<?php echo e($cta1Url !== '' ? $cta1Url : '#'); ?>" class="front-link-cta inline-flex h-11 min-w-[145px] items-center justify-center border border-white bg-white px-5 text-base font-black uppercase tracking-[0.02em] text-slate-900 transition hover:bg-slate-100">
                                    <?php echo e($cta1Label); ?>

                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cta2Label !== ''): ?>
                                <a href="<?php echo e($cta2Url !== '' ? $cta2Url : '#'); ?>" class="front-link-cta inline-flex h-11 min-w-[145px] items-center justify-center border border-white bg-white px-5 text-base font-black uppercase tracking-[0.02em] text-slate-900 transition hover:bg-slate-100">
                                    <?php echo e($cta2Label); ?>

                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/content-blocks/types/dual_image_cta.blade.php ENDPATH**/ ?>