<?php $__env->startSection('title', __('contact.page_title')); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>
<?php $__env->startSection('hide_footer_newsletter', '1'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $captchaSiteKey = trim((string) ($storeSettings['captcha']['recaptcha_v3_site_key'] ?? ''));
        $captchaEnabled = (bool) ($storeSettings['captcha']['recaptcha_v3_enabled'] ?? false) && $captchaSiteKey !== '';
        $contactEntities = collect((array) ($storeSettings['official_entities'] ?? []))->keyBy('key');
        $primaryOffice = (array) ($contactEntities->get('alpha-capitalis-timia') ?? $contactEntities->first() ?? []);
        $contactEmail = trim((string) ($storeSettings['footer']['email_support'] ?? ''))
            ?: trim((string) ($primaryOffice['email'] ?? ''))
            ?: 'info@alphacapitalis.com';
        $contactPhone = trim((string) ($storeSettings['footer']['phone'] ?? ''))
            ?: trim((string) ($primaryOffice['phone'] ?? ''))
            ?: '+385 (0) 51 301 503';
        $contactPhoneHref = preg_replace('/\s+/', '', $contactPhone);
        $contactHours = trim((string) ($storeSettings['footer']['hours'] ?? '')) ?: __('contact.direct.response_fallback');
        $headingWords = static fn (string $title): array => preg_split('/\s+/u', trim($title), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    ?>

    <div class="front-contact-page ac-contact-page">
        <section class="ac-contact-intro" aria-labelledby="ac-contact-title">
            <div class="ac-contact-container ac-contact-intro-layout">
                <div class="ac-contact-intro-heading">
                    <?php ($contactHeadingWords = $headingWords(__('contact.heading'))); ?>
                    <h1 class="values-title services-index-intro-title ac-contact-display-title" id="ac-contact-title" data-words-slide-from-right aria-label="<?php echo e(__('contact.heading')); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $contactHeadingWords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="values-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last && count($contactHeadingWords) > 1 ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h1>
                </div>

                <div class="ac-contact-intro-copy content-reveal animation-index-1" data-image-reveal>
                    <p><?php echo e(__('contact.subheading')); ?></p>
                </div>
            </div>
        </section>

        <?php echo $__env->make('front.desktop.partials.locations-showcase', [
            'locationsSectionId' => 'contact-locations',
            'locationsTitleId' => 'contact-locations-title',
            'locationDetailsPrefix' => 'contact-location-details',
            'showLocationStats' => true,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <section class="front-contact-content-shell ac-contact-form-section" aria-labelledby="ac-contact-form-title">
            <div class="ac-contact-container front-contact-layout">
                <form
                    method="POST"
                    action="<?php echo e(route('contact.store')); ?>"
                    class="front-contact-form content-reveal animation-index-0"
                    novalidate
                    data-contact-form
                    data-image-reveal
                    data-msg-name-required="<?php echo e(__('contact.validation.inline.name_required')); ?>"
                    data-msg-email-required="<?php echo e(__('contact.validation.inline.email_required')); ?>"
                    data-msg-email-invalid="<?php echo e(__('contact.validation.inline.email_invalid')); ?>"
                    data-msg-message-required="<?php echo e(__('contact.validation.inline.message_required')); ?>"
                    data-msg-message-min="<?php echo e(__('contact.validation.inline.message_min')); ?>"
                    data-msg-accept-terms="<?php echo e(__('contact.validation.inline.accept_terms')); ?>"
                    <?php if($captchaEnabled): ?> data-recaptcha-form data-recaptcha-site-key="<?php echo e($captchaSiteKey); ?>" data-recaptcha-action="contact_form" <?php endif; ?>
                >
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="recaptcha_token" value="" data-recaptcha-token>

                    <div class="front-contact-form-head">
                        <h2 id="ac-contact-form-title"><?php echo e(__('contact.form.title')); ?></h2>
                        <p><?php echo e(__('contact.form.intro')); ?></p>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
                        <div class="front-contact-status" role="status">
                            <i class="fa-light fa-circle-check" aria-hidden="true"></i>
                            <span><?php echo e(session('status')); ?></span>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="ac-contact-field-grid">
                        <div class="ac-contact-field">
                            <label for="contact-name"><?php echo e(__('contact.form.name')); ?></label>
                            <input id="contact-name" type="text" name="name" value="<?php echo e(old('name', auth()->user()?->name)); ?>" class="front-contact-input" autocomplete="name" required aria-describedby="contact-name-error" aria-invalid="<?php echo e($errors->has('name') ? 'true' : 'false'); ?>">
                            <p id="contact-name-error" class="front-contact-field-error" data-field-error="name" <?php if(! $errors->has('name')): ?> hidden <?php endif; ?>><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                        </div>
                        <div class="ac-contact-field">
                            <label for="contact-email"><?php echo e(__('contact.form.email')); ?></label>
                            <input id="contact-email" type="email" name="email" value="<?php echo e(old('email', auth()->user()?->email)); ?>" class="front-contact-input" autocomplete="email" required aria-describedby="contact-email-error" aria-invalid="<?php echo e($errors->has('email') ? 'true' : 'false'); ?>">
                            <p id="contact-email-error" class="front-contact-field-error" data-field-error="email" <?php if(! $errors->has('email')): ?> hidden <?php endif; ?>><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                        </div>
                        <div class="ac-contact-field">
                            <label for="contact-phone"><?php echo e(__('contact.form.phone')); ?></label>
                            <input id="contact-phone" type="tel" name="phone" value="<?php echo e(old('phone')); ?>" class="front-contact-input" autocomplete="tel" aria-describedby="contact-phone-error" aria-invalid="<?php echo e($errors->has('phone') ? 'true' : 'false'); ?>">
                            <p id="contact-phone-error" class="front-contact-field-error" data-field-error="phone" <?php if(! $errors->has('phone')): ?> hidden <?php endif; ?>><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                        </div>
                        <div class="ac-contact-field">
                            <label for="contact-subject"><?php echo e(__('contact.form.subject')); ?></label>
                            <input id="contact-subject" type="text" name="subject" value="<?php echo e(old('subject')); ?>" class="front-contact-input" aria-describedby="contact-subject-error" aria-invalid="<?php echo e($errors->has('subject') ? 'true' : 'false'); ?>">
                            <p id="contact-subject-error" class="front-contact-field-error" data-field-error="subject" <?php if(! $errors->has('subject')): ?> hidden <?php endif; ?>><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                        </div>
                        <div class="ac-contact-field ac-contact-field--full">
                            <label for="contact-message"><?php echo e(__('contact.form.message')); ?></label>
                            <textarea id="contact-message" name="message" rows="7" class="front-contact-textarea" required aria-describedby="contact-message-error" aria-invalid="<?php echo e($errors->has('message') ? 'true' : 'false'); ?>"><?php echo e(old('message')); ?></textarea>
                            <p id="contact-message-error" class="front-contact-field-error" data-field-error="message" <?php if(! $errors->has('message')): ?> hidden <?php endif; ?>><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                        </div>
                    </div>

                    <div class="front-contact-consent-wrap">
                        <label class="front-contact-consent" for="contact-accept-terms">
                            <input id="contact-accept-terms" type="checkbox" name="accept_terms" value="1" class="front-contact-checkbox" <?php if((bool) old('accept_terms')): echo 'checked'; endif; ?> aria-describedby="contact-accept-terms-error" aria-invalid="<?php echo e($errors->has('accept_terms') ? 'true' : 'false'); ?>">
                            <span><?php echo e(__('contact.form.accept_terms')); ?></span>
                        </label>
                        <p id="contact-accept-terms-error" class="front-contact-field-error" data-field-error="accept_terms" <?php if(! $errors->has('accept_terms')): ?> hidden <?php endif; ?>><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['accept_terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                    </div>

                    <div class="front-contact-form-actions">
                        <button type="submit" class="editorial-dark-button ac-contact-submit">
                            <span><?php echo e(__('contact.form.submit')); ?></span>
                            <i class="fa-light fa-arrow-up-right" aria-hidden="true"></i>
                        </button>
                        <p class="front-contact-field-error" data-field-error="recaptcha_token" <?php if(! $errors->has('recaptcha_token')): ?> hidden <?php endif; ?>><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['recaptcha_token'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                    </div>
                </form>

                <aside class="front-contact-sidebar content-reveal animation-index-1" data-image-reveal aria-label="<?php echo e(__('contact.direct.title')); ?>">
                    <div class="front-contact-panel front-contact-panel--direct">
                        <h2><?php echo e(__('contact.direct.title')); ?></h2>
                        <p class="front-contact-panel-intro"><?php echo e(__('contact.direct.body')); ?></p>

                        <ul class="front-contact-direct-list">
                            <li>
                                <i class="fa-light fa-envelope" aria-hidden="true"></i>
                                <span>
                                    <small><?php echo e(__('contact.direct.email')); ?></small>
                                    <a href="mailto:<?php echo e($contactEmail); ?>"><?php echo e($contactEmail); ?></a>
                                </span>
                            </li>
                            <li>
                                <i class="fa-light fa-phone" aria-hidden="true"></i>
                                <span>
                                    <small><?php echo e(__('contact.direct.phone')); ?></small>
                                    <a href="tel:<?php echo e($contactPhoneHref); ?>"><?php echo e($contactPhone); ?></a>
                                </span>
                            </li>
                            <li>
                                <i class="fa-light fa-clock" aria-hidden="true"></i>
                                <span>
                                    <small><?php echo e(__('contact.direct.response_time')); ?></small>
                                    <strong><?php echo e($contactHours); ?></strong>
                                </span>
                            </li>
                        </ul>
                    </div>

                    <div class="front-contact-help">
                        <span class="front-contact-help-icon" aria-hidden="true">
                            <i class="fa-light fa-message-lines"></i>
                        </span>
                        <div>
                            <h3><?php echo e(__('contact.help.title')); ?></h3>
                            <p><?php echo e(__('contact.help.body')); ?></p>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

    </div>

    <?php echo $__env->make('front.desktop.contact.partials.form-script', [
        'captchaEnabled' => $captchaEnabled,
        'captchaSiteKey' => $captchaSiteKey,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('front-theme/styles/pages/contact.css')); ?>?v=<?php echo e(filemtime(public_path('front-theme/styles/pages/contact.css'))); ?>">
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/contact/create.blade.php ENDPATH**/ ?>