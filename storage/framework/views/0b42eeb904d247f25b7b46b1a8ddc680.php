<?php
    $sectionId = $sectionId ?? 'ac-service-pillars-showcase';
    $headingLevel = (int) ($headingLevel ?? 2);
    $headingTag = $headingLevel === 1 ? 'h1' : 'h2';
    $variant = (string) ($variant ?? 'text');
    $cards = collect($cards ?? [])->values();
    $titleLead = trim((string) ($titleLead ?? ''));
    $titleAccent = trim((string) ($titleAccent ?? ''));
    $intro = trim((string) ($intro ?? ''));
    $outro = collect($outro ?? [])->filter(fn ($item) => trim((string) $item) !== '')->values();
    $isImageVariant = $variant === 'image';
?>

<section id="<?php echo e($sectionId); ?>" class="ac-service-pillars-showcase <?php echo e($isImageVariant ? 'ac-service-pillars-showcase--image' : ''); ?>" aria-labelledby="<?php echo e($sectionId); ?>-title">
    <div class="ac-service-pillars-showcase-inner mx-auto w-full max-w-[1240px] px-5 lg:px-8">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($titleLead !== '' || $titleAccent !== '' || $intro !== ''): ?>
            <div class="ac-service-pillars-showcase-head">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($titleLead !== '' || $titleAccent !== ''): ?>
                    <<?php echo e($headingTag); ?> id="<?php echo e($sectionId); ?>-title">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($titleLead !== ''): ?>
                            <span><?php echo e($titleLead); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($titleAccent !== ''): ?>
                            <span class="ac-service-pillars-showcase-title-accent"><?php echo e($titleAccent); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </<?php echo e($headingTag); ?>>
                <?php else: ?>
                    <<?php echo e($headingTag); ?> id="<?php echo e($sectionId); ?>-title" class="sr-only">Usluge Alpha Capitalisa</<?php echo e($headingTag); ?>>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($intro !== ''): ?>
                    <p><?php echo e($intro); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php else: ?>
            <<?php echo e($headingTag); ?> id="<?php echo e($sectionId); ?>-title" class="sr-only">Usluge Alpha Capitalisa</<?php echo e($headingTag); ?>>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="ac-service-pillars-showcase-grid <?php echo e($isImageVariant ? 'ac-service-pillars-showcase-grid--image' : ''); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isImageVariant): ?>
                    <a href="<?php echo e($card['url'] ?? route('services.index')); ?>" class="ac-service-pillar-image-card">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($card['image_url'])): ?>
                            <img src="<?php echo e($card['image_url']); ?>" alt="" aria-hidden="true" loading="<?php echo e($loop->index < 3 ? 'eager' : 'lazy'); ?>" decoding="async">
                        <?php else: ?>
                            <span class="ac-service-pillar-image-card-placeholder" aria-hidden="true"></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <span class="ac-service-pillar-image-card-shade" aria-hidden="true"></span>
                        <span class="ac-service-pillar-image-card-content">
                            <span class="ac-service-pillar-text-card-title"><?php echo e($card['title'] ?? ''); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($card['text'])): ?>
                                <span class="ac-service-pillar-image-card-text"><?php echo e($card['text']); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span class="ac-service-pillar-image-card-action">
                                <span><?php echo e($card['action_label'] ?? 'Detaljnije'); ?></span>
                                <span class="ac-service-pillar-image-card-arrow" aria-hidden="true"></span>
                            </span>
                        </span>
                    </a>
                <?php else: ?>
                    <a href="<?php echo e($card['url'] ?? route('services.index')); ?>" class="ac-service-pillar-text-card">
                        <span class="ac-service-pillar-text-card-title"><?php echo e($card['title'] ?? ''); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($card['subtitle'])): ?>
                            <span class="ac-service-pillar-text-card-subtitle"><?php echo e($card['subtitle']); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($card['bullets'])): ?>
                            <ul>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $card['bullets']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bullet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($bullet); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </ul>
                        <?php elseif(!empty($card['text'])): ?>
                            <p><?php echo e($card['text']); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($outro->isNotEmpty()): ?>
            <div class="ac-service-pillars-showcase-copy">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $outro; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p><?php echo e($paragraph); ?></p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/partials/service-pillars-showcase.blade.php ENDPATH**/ ?>