<?php
    $careerEmail = 'info@alphacapitalis.com';
    $careerUrl = 'mailto:'.$careerEmail.'?subject='.rawurlencode((string) __('ui.team.career_email_subject'));
    $isCroatian = str_starts_with(strtolower((string) $locale), 'hr');
    $teamTitleLead = 'ALPHA CAPITALIS';
    $teamTitleAccent = $isCroatian ? 'Tim' : 'Team';
    $careerBodyParts = preg_split('/(?<=\.)\s+/u', trim((string) __('ui.team.career_body')), 2) ?: [];
    $careerBodyLead = $careerBodyParts[0] ?? '';
    $careerBodyRest = $careerBodyParts[1] ?? '';
    $careerTitleWords = collect(preg_split('/\s+/u', trim((string) __('ui.team.career_title'))) ?: [])
        ->filter()
        ->values();
?>

<?php $__env->startSection('title', __('ui.team.page_title')); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startSection('content'); ?>
    <div class="ac-team-page">
        <section class="values-section services-index-intro ac-team-intro" aria-labelledby="ac-team-title">
            <div class="values-inner services-index-intro-layout">
                <div class="values-intro">
                    <h1 class="values-title services-index-intro-title" id="ac-team-title" data-words-slide-from-right aria-label="<?php echo e($teamTitleLead); ?> <?php echo e($teamTitleAccent); ?>">
                        <span class="values-word animation-index-0" aria-hidden="true">ALPHA</span>
                        <span class="values-word animation-index-1" aria-hidden="true">CAPITALIS</span>
                        <span class="values-word animation-index-2 is-accent" aria-hidden="true"><?php echo e($teamTitleAccent); ?></span>
                    </h1>
                </div>

                <p class="values-copy services-index-intro-copy content-reveal" data-image-reveal><?php echo e(__('ui.team.subtitle')); ?></p>
            </div>
        </section>

        <section class="ac-team-section">
            <div class="ac-team-container">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($members->isEmpty()): ?>
                    <div class="ac-team-page-empty border border-dashed border-slate-300 bg-white/80 px-6 py-14 text-center shadow-[0_18px_54px_rgba(15,23,42,0.06)]">
                        <h2 class="text-2xl font-black tracking-tight text-slate-950"><?php echo e(__('ui.team.empty_title')); ?></h2>
                        <p class="mx-auto mt-3 max-w-[34rem] text-sm leading-7 text-slate-600"><?php echo e(__('ui.team.empty')); ?></p>
                    </div>
                <?php else: ?>
                    <div class="ac-team-member-list">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="ac-team-member-card content-reveal animation-index-<?php echo e($loop->index % 2); ?>" data-image-reveal>
                                <div class="ac-team-member-layout grid gap-4 lg:grid-cols-[220px_minmax(0,1fr)] lg:items-start lg:gap-5">
                                    <div class="ac-team-member-media self-start overflow-hidden border border-slate-200 bg-white">
                                        <div class="relative overflow-hidden <?php echo e($member['photo_url'] !== '' ? 'image-reveal-media' : ''); ?>">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($member['photo_url'] !== ''): ?>
                                                <img
                                                    src="<?php echo e($member['photo_url']); ?>"
                                                    alt="<?php echo e($member['name']); ?>"
                                                    class="ac-team-member-photo block h-auto w-full bg-white"
                                                    loading="lazy"
                                                    decoding="async"
                                            >
                                                <span class="image-reveal-curtain" aria-hidden="true"></span>
                                            <?php else: ?>
                                                <div class="ac-team-member-photo flex h-full items-center justify-center">
                                                    <span class="text-6xl font-black tracking-[0.18em] text-white/92"><?php echo e($member['initials']); ?></span>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="ac-team-member-head pb-3.5">
                                        <h3 class="ac-team-member-name text-[1.2rem] font-black leading-tight tracking-tight text-slate-950 sm:text-[1.38rem]"><?php echo e($member['name']); ?></h3>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($member['position'] !== ''): ?>
                                            <p class="ac-team-role mt-2 text-[0.8rem] font-semibold uppercase text-sky-800">
                                                <?php echo e($member['position']); ?>

                                            </p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($member['description_html'] !== ''): ?>
                                        <div class="ac-team-member-bio">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($member['has_long_description']): ?>
                                                <div class="ac-team-bio mt-4">
                                                    <input id="team-bio-<?php echo e($member['id']); ?>" type="checkbox" class="ac-team-bio-toggle">
                                                    <p class="ac-team-bio-excerpt text-[0.9rem] leading-7 text-slate-600">
                                                        <?php echo e($member['description_excerpt']); ?>

                                                    </p>
                                                    <div class="ac-team-bio-panel">
                                                        <div class="ac-team-bio-panel-inner">
                                                            <div class="content-richtext ac-team-bio-content text-[0.9rem] leading-7 text-slate-600">
                                                                <?php echo $member['description_html']; ?>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <label for="team-bio-<?php echo e($member['id']); ?>" class="ac-team-bio-trigger services-index-inline-link">
                                                        <span class="ac-team-bio-more"><?php echo e(__('ui.team.read_more')); ?></span>
                                                        <span class="ac-team-bio-less"><?php echo e(__('ui.team.read_less')); ?></span>
                                                        <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                                                    </label>
                                                </div>
                                            <?php else: ?>
                                                <div class="content-richtext mt-4 text-[0.9rem] leading-7 text-slate-600">
                                                    <?php echo $member['description_html']; ?>

                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <?php
                                        $memberPhoneHref = preg_replace('/[^0-9+]/', '', $member['mobile_phone']);
                                    ?>

                                    <div class="ac-team-member-actions mt-4 flex flex-wrap gap-2.5">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($member['email'] !== ''): ?>
                                            <a href="mailto:<?php echo e($member['email']); ?>" title="<?php echo e(__('ui.team.social.email')); ?>" aria-label="<?php echo e(__('ui.team.social.email')); ?>" class="ac-team-social-link">
                                                <i class="fa-light fa-envelope" aria-hidden="true"></i>
                                            </a>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($member['mobile_phone'] !== '' && $memberPhoneHref !== ''): ?>
                                            <a href="tel:<?php echo e($memberPhoneHref); ?>" title="<?php echo e(__('ui.team.social.phone')); ?>" aria-label="<?php echo e(__('ui.team.social.phone')); ?>" class="ac-team-social-link">
                                                <i class="fa-light fa-mobile-screen-button" aria-hidden="true"></i>
                                            </a>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($member['facebook_url'] !== ''): ?>
                                            <a href="<?php echo e($member['facebook_url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo e(__('ui.team.social.facebook')); ?>" aria-label="<?php echo e(__('ui.team.social.facebook')); ?>" class="ac-team-social-link">
                                                <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                                            </a>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($member['twitter_url'] !== ''): ?>
                                            <a href="<?php echo e($member['twitter_url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo e(__('ui.team.social.twitter')); ?>" aria-label="<?php echo e(__('ui.team.social.twitter')); ?>" class="ac-team-social-link">
                                                <i class="fa-brands fa-x-twitter" aria-hidden="true"></i>
                                            </a>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($member['linkedin_url'] !== ''): ?>
                                            <a href="<?php echo e($member['linkedin_url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo e(__('ui.team.social.linkedin')); ?>" aria-label="<?php echo e(__('ui.team.social.linkedin')); ?>" class="ac-team-social-link">
                                                <i class="fa-brands fa-linkedin-in" aria-hidden="true"></i>
                                            </a>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($members->isNotEmpty()): ?>
            <section class="contact-cta ac-team-contact-cta" aria-labelledby="ac-team-career-title">
                <div class="contact-cta-shell">
                    <div class="contact-cta-copy">
                        <h2 class="contact-cta-title" id="ac-team-career-title" data-words-slide-from-right aria-label="<?php echo e(__('ui.team.career_title')); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $careerTitleWords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="contact-cta-title-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->remaining < 2 ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </h2>
                    </div>

                    <div class="contact-cta-card" data-image-reveal>
                        <div class="contact-cta-card-heading"><span><?php echo e(__('ui.team.eyebrow')); ?></span></div>
                        <div class="ac-team-cta-expand">
                            <p class="ac-team-cta-body ac-team-cta-lead"><?php echo e($careerBodyLead); ?></p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($careerBodyRest !== ''): ?>
                                <input id="ac-team-career-copy" type="checkbox" class="ac-team-cta-toggle">
                                <div class="ac-team-cta-panel">
                                    <div class="ac-team-cta-panel-inner">
                                        <p class="ac-team-cta-body ac-team-cta-rest"><?php echo e($careerBodyRest); ?></p>
                                    </div>
                                </div>
                                <label for="ac-team-career-copy" class="ac-team-cta-trigger services-index-inline-link">
                                    <span class="ac-team-cta-more"><?php echo e(__('ui.team.read_more')); ?></span>
                                    <span class="ac-team-cta-less"><?php echo e(__('ui.team.read_less')); ?></span>
                                    <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                                </label>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <a class="contact-cta-button" href="<?php echo e($careerUrl); ?>">
                            <span><?php echo e(__('ui.team.career_button')); ?></span>
                            <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('front-theme/styles/pages/team.css')); ?>?v=<?php echo e(filemtime(public_path('front-theme/styles/pages/team.css'))); ?>">
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/team/index.blade.php ENDPATH**/ ?>