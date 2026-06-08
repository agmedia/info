<?php $__env->startSection('title', __('assessment.page_title')); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $captchaSiteKey = trim((string) ($storeSettings['captcha']['recaptcha_v3_site_key'] ?? ''));
        $captchaEnabled = (bool) ($storeSettings['captcha']['recaptcha_v3_enabled'] ?? false) && $captchaSiteKey !== '';
        $contactEmail = trim((string) ($storeSettings['footer']['email_support'] ?? '')) ?: 'info@alphacapitalis.com';
        $contactPhone = trim((string) ($storeSettings['footer']['phone'] ?? '')) ?: '+385 (1) 580 6656';
        $contactPhoneHref = preg_replace('/\s+/', '', $contactPhone);
        $pageTitleBreadcrumbs = [
            ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
            ['label' => __('assessment.page_title'), 'current' => true],
        ];
    ?>

    <div class="front-contact-page ac-assessment-page" data-assessment-form-root data-locale="<?php echo e(app()->getLocale()); ?>">
        <?php if (isset($component)) { $__componentOriginale6a101278d02d7bbbf9e98ee1142bf75 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.front.page-title-band','data' => ['breadcrumbs' => $pageTitleBreadcrumbs,'sectionClass' => 'ac-assessment-title-band']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('front.page-title-band'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pageTitleBreadcrumbs),'section-class' => 'ac-assessment-title-band']); ?>
            <div class="ac-page-title-copy">
                <h1><?php echo e(__('assessment.heading')); ?></h1>
                <p><?php echo e(__('assessment.subheading')); ?></p>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75)): ?>
<?php $attributes = $__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75; ?>
<?php unset($__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale6a101278d02d7bbbf9e98ee1142bf75)): ?>
<?php $component = $__componentOriginale6a101278d02d7bbbf9e98ee1142bf75; ?>
<?php unset($__componentOriginale6a101278d02d7bbbf9e98ee1142bf75); ?>
<?php endif; ?>

        <section class="front-contact-content-shell">
            <div class="mx-auto w-full max-w-[1320px] px-4 sm:px-6 lg:px-8">
                <div class="front-contact-layout ac-assessment-layout">
                    <form
                        method="POST"
                        action="<?php echo e(route('assessment.store')); ?>"
                        class="front-contact-form ac-assessment-form"
                        novalidate
                        <?php if($captchaEnabled): ?> data-recaptcha-form data-recaptcha-site-key="<?php echo e($captchaSiteKey); ?>" data-recaptcha-action="collaboration_assessment_form" <?php endif; ?>
                    >
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="recaptcha_token" value="" data-recaptcha-token>

                        <div class="front-contact-form-head">
                            <p class="front-contact-section-kicker"><?php echo e(__('assessment.form.kicker')); ?></p>
                            <h2><?php echo e(__('assessment.form.title')); ?></h2>
                            <p><?php echo e(__('assessment.form.intro')); ?></p>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
                            <div class="front-contact-status" role="status">
                                <?php echo e(session('status')); ?>

                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="ac-assessment-section">
                            <div class="ac-assessment-section-head">
                                <h3><?php echo e(__('assessment.sections.company')); ?></h3>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.company_name')); ?></label>
                                    <input type="text" name="company_name" value="<?php echo e(old('company_name')); ?>" class="front-contact-input h-11 w-full text-sm" required>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['company_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.company_oib')); ?></label>
                                    <input type="text" name="company_oib" value="<?php echo e(old('company_oib')); ?>" class="front-contact-input h-11 w-full text-sm" required>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['company_oib'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.activity')); ?></label>
                                    <input type="text" name="activity" value="<?php echo e(old('activity')); ?>" class="front-contact-input h-11 w-full text-sm" required>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['activity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.potential_start_date')); ?></label>
                                    <div class="ac-assessment-date-field" data-assessment-date-field="assessment-start-date">
                                        <span class="ac-lease-date-display is-placeholder" data-assessment-date-display="assessment-start-date"><?php echo e(__('assessment.form.date_placeholder')); ?></span>
                                        <input
                                            id="assessment-start-date"
                                            type="date"
                                            name="potential_start_date"
                                            value="<?php echo e(old('potential_start_date')); ?>"
                                            class="ac-lease-date-input"
                                        >
                                        <button
                                            type="button"
                                            class="ac-lease-date-trigger"
                                            data-assessment-date-trigger="assessment-start-date"
                                            aria-expanded="false"
                                            aria-label="<?php echo e(__('assessment.form.open_calendar')); ?>: <?php echo e(__('assessment.form.potential_start_date')); ?>"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" aria-hidden="true">
                                                <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                                <path d="M16 3v4M8 3v4M3 10h18"></path>
                                            </svg>
                                        </button>
                                        <div class="ac-lease-calendar" data-assessment-calendar-panel hidden>
                                            <div class="ac-lease-calendar-head">
                                                <button type="button" class="ac-lease-calendar-nav" data-assessment-calendar-prev aria-label="<?php echo e(__('assessment.form.previous_month')); ?>">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" aria-hidden="true">
                                                        <path d="M15 18l-6-6 6-6"></path>
                                                    </svg>
                                                </button>
                                                <div class="ac-lease-calendar-title" data-assessment-calendar-title></div>
                                                <label class="sr-only" for="assessment-start-year"><?php echo e(__('assessment.form.select_year')); ?></label>
                                                <select id="assessment-start-year" class="ac-lease-calendar-year" data-assessment-calendar-year></select>
                                                <button type="button" class="ac-lease-calendar-nav" data-assessment-calendar-next aria-label="<?php echo e(__('assessment.form.next_month')); ?>">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" aria-hidden="true">
                                                        <path d="M9 18l6-6-6-6"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="ac-lease-calendar-weekdays" data-assessment-calendar-weekdays></div>
                                            <div class="ac-lease-calendar-grid" data-assessment-calendar-grid></div>
                                        </div>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['potential_start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.contact_email')); ?></label>
                                    <input type="email" name="contact_email" value="<?php echo e(old('contact_email')); ?>" class="front-contact-input h-11 w-full text-sm" required>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['contact_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.contact_phone')); ?></label>
                                    <input type="text" name="contact_phone" value="<?php echo e(old('contact_phone')); ?>" class="front-contact-input h-11 w-full text-sm" required>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['contact_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="ac-assessment-section">
                            <div class="ac-assessment-section-head">
                                <h3><?php echo e(__('assessment.sections.volume')); ?></h3>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.incoming_invoices_monthly')); ?></label>
                                    <input type="text" name="incoming_invoices_monthly" value="<?php echo e(old('incoming_invoices_monthly')); ?>" class="front-contact-input h-11 w-full text-sm" required>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['incoming_invoices_monthly'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.outgoing_invoices_monthly')); ?></label>
                                    <input type="text" name="outgoing_invoices_monthly" value="<?php echo e(old('outgoing_invoices_monthly')); ?>" class="front-contact-input h-11 w-full text-sm" required>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['outgoing_invoices_monthly'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.bank_accounts_monthly')); ?></label>
                                    <input type="text" name="bank_accounts_monthly" value="<?php echo e(old('bank_accounts_monthly')); ?>" class="front-contact-input h-11 w-full text-sm" required>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bank_accounts_monthly'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.payroll_calculations_monthly')); ?></label>
                                    <input type="text" name="payroll_calculations_monthly" value="<?php echo e(old('payroll_calculations_monthly')); ?>" class="front-contact-input h-11 w-full text-sm" required>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['payroll_calculations_monthly'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <div class="ac-assessment-field ac-assessment-field--full">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.other_calculations_monthly')); ?></label>
                                <textarea name="other_calculations_monthly" rows="3" class="front-contact-textarea ac-assessment-textarea w-full text-sm"><?php echo e(old('other_calculations_monthly')); ?></textarea>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['other_calculations_monthly'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        <div class="ac-assessment-section">
                            <div class="ac-assessment-section-head">
                                <h3><?php echo e(__('assessment.sections.process')); ?></h3>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.inventory_bookkeeping')); ?></label>
                                    <select name="inventory_bookkeeping" class="front-contact-input h-11 w-full text-sm">
                                        <option value=""><?php echo e(__('assessment.options.choose')); ?></option>
                                        <option value="yes" <?php if(old('inventory_bookkeeping') === 'yes'): echo 'selected'; endif; ?>><?php echo e(__('assessment.options.yes')); ?></option>
                                        <option value="no" <?php if(old('inventory_bookkeeping') === 'no'): echo 'selected'; endif; ?>><?php echo e(__('assessment.options.no')); ?></option>
                                    </select>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['inventory_bookkeeping'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.cost_centers_tracking')); ?></label>
                                    <select name="cost_centers_tracking" class="front-contact-input h-11 w-full text-sm">
                                        <option value=""><?php echo e(__('assessment.options.choose')); ?></option>
                                        <option value="yes" <?php if(old('cost_centers_tracking') === 'yes'): echo 'selected'; endif; ?>><?php echo e(__('assessment.options.yes')); ?></option>
                                        <option value="no" <?php if(old('cost_centers_tracking') === 'no'): echo 'selected'; endif; ?>><?php echo e(__('assessment.options.no')); ?></option>
                                    </select>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['cost_centers_tracking'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.monthly_reporting')); ?></label>
                                    <select name="monthly_reporting" class="front-contact-input h-11 w-full text-sm">
                                        <option value=""><?php echo e(__('assessment.options.choose')); ?></option>
                                        <option value="yes" <?php if(old('monthly_reporting') === 'yes'): echo 'selected'; endif; ?>><?php echo e(__('assessment.options.yes')); ?></option>
                                        <option value="no" <?php if(old('monthly_reporting') === 'no'): echo 'selected'; endif; ?>><?php echo e(__('assessment.options.no')); ?></option>
                                    </select>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['monthly_reporting'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.travel_orders_monthly')); ?></label>
                                    <input type="text" name="travel_orders_monthly" value="<?php echo e(old('travel_orders_monthly')); ?>" class="front-contact-input h-11 w-full text-sm">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['travel_orders_monthly'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <div class="ac-assessment-field ac-assessment-field--full">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.incoming_invoice_payments')); ?></label>
                                <textarea name="incoming_invoice_payments" rows="3" class="front-contact-textarea ac-assessment-textarea w-full text-sm"><?php echo e(old('incoming_invoice_payments')); ?></textarea>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['incoming_invoice_payments'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        <div class="ac-assessment-section">
                            <div class="ac-assessment-section-head">
                                <h3><?php echo e(__('assessment.sections.special')); ?></h3>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.intrastat_obligation')); ?></label>
                                    <textarea name="intrastat_obligation" rows="3" class="front-contact-textarea ac-assessment-textarea w-full text-sm"><?php echo e(old('intrastat_obligation')); ?></textarea>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['intrastat_obligation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.audit_obligation')); ?></label>
                                    <textarea name="audit_obligation" rows="3" class="front-contact-textarea ac-assessment-textarea w-full text-sm"><?php echo e(old('audit_obligation')); ?></textarea>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['audit_obligation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.vat_status')); ?></label>
                                    <textarea name="vat_status" rows="3" class="front-contact-textarea ac-assessment-textarea w-full text-sm"><?php echo e(old('vat_status')); ?></textarea>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['vat_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="ac-assessment-field ac-assessment-field--tall-control">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.accounting_software')); ?></label>
                                    <input type="text" name="accounting_software" value="<?php echo e(old('accounting_software')); ?>" class="front-contact-input h-11 w-full text-sm">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['accounting_software'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.tax_issues')); ?></label>
                                    <textarea name="tax_issues" rows="3" class="front-contact-textarea ac-assessment-textarea w-full text-sm"><?php echo e(old('tax_issues')); ?></textarea>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['tax_issues'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.document_delivery')); ?></label>
                                    <textarea name="document_delivery" rows="3" class="front-contact-textarea ac-assessment-textarea w-full text-sm"><?php echo e(old('document_delivery')); ?></textarea>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['document_delivery'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="ac-assessment-section">
                            <div class="ac-assessment-section-head">
                                <h3><?php echo e(__('assessment.sections.additional')); ?></h3>
                            </div>

                            <div class="ac-assessment-field ac-assessment-field--full">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('assessment.form.additional_requirements')); ?></label>
                                <textarea name="additional_requirements" rows="5" class="front-contact-textarea ac-assessment-textarea ac-assessment-textarea--lg w-full text-sm"><?php echo e(old('additional_requirements')); ?></textarea>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['additional_requirements'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        <div class="front-contact-consent-wrap">
                            <label class="front-contact-consent">
                                <input type="checkbox" name="accept_terms" value="1" class="front-contact-checkbox mt-0.5 h-4 w-4 border-slate-300 text-slate-900 focus:ring-0" <?php if((bool) old('accept_terms')): echo 'checked'; endif; ?>>
                                <span><?php echo e(__('assessment.form.accept_terms')); ?></span>
                            </label>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['accept_terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['recaptcha_token'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="front-contact-form-actions">
                            <button type="submit" class="front-contact-submit inline-flex h-11 items-center justify-center px-6 text-sm font-semibold text-white transition">
                                <?php echo e(__('assessment.form.submit')); ?>

                            </button>
                        </div>
                    </form>

                    <aside class="front-contact-sidebar">
                        <div class="front-contact-panel front-contact-panel--direct">
                            <h2><?php echo e(__('assessment.sidebar.title')); ?></h2>
                            <p class="front-contact-panel-intro"><?php echo e(__('assessment.sidebar.body')); ?></p>

                            <ul class="front-contact-direct-list">
                                <li>
                                    <span><?php echo e(__('assessment.sidebar.point_1_label')); ?></span>
                                    <strong><?php echo e(__('assessment.sidebar.point_1')); ?></strong>
                                </li>
                                <li>
                                    <span><?php echo e(__('assessment.sidebar.point_2_label')); ?></span>
                                    <strong><?php echo e(__('assessment.sidebar.point_2')); ?></strong>
                                </li>
                                <li>
                                    <span><?php echo e(__('contact.direct.email')); ?></span>
                                    <a href="mailto:<?php echo e($contactEmail); ?>"><?php echo e($contactEmail); ?></a>
                                </li>
                                <li>
                                    <span><?php echo e(__('contact.direct.phone')); ?></span>
                                    <a href="tel:<?php echo e($contactPhoneHref); ?>"><?php echo e($contactPhone); ?></a>
                                </li>
                            </ul>
                        </div>

                        <div class="front-contact-help">
                            <h3><?php echo e(__('assessment.help.title')); ?></h3>
                            <p><?php echo e(__('assessment.help.body')); ?></p>
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($captchaEnabled): ?>
        <?php $__env->startPush('scripts'); ?>
            <script src="https://www.google.com/recaptcha/api.js?render=<?php echo e($captchaSiteKey); ?>"></script>
        <?php $__env->stopPush(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php $__env->startPush('scripts'); ?>
        <script>
            (function () {
                const root = document.querySelector('[data-assessment-form-root]');
                if (!root) {
                    return;
                }

                const locale = root.dataset.locale === 'hr' ? 'hr-HR' : 'en-US';
                const datePlaceholder = locale === 'hr-HR' ? 'dd.mm.gggg' : 'mm/dd/yyyy';
                const calendarWeekdays = locale === 'hr-HR'
                    ? ['Pon', 'Uto', 'Sri', 'Čet', 'Pet', 'Sub', 'Ned']
                    : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                const calendarMonthFormatter = new Intl.DateTimeFormat(locale, { month: 'long' });
                const dateFields = Array.from(root.querySelectorAll('[data-assessment-date-field]'));
                const dateDisplays = Array.from(root.querySelectorAll('[data-assessment-date-display]'));
                const dateTriggers = Array.from(root.querySelectorAll('[data-assessment-date-trigger]'));

                const capitalize = function (value) {
                    return value.charAt(0).toUpperCase() + value.slice(1);
                };

                const formatInputDate = function (date) {
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = String(date.getFullYear());

                    return year + '-' + month + '-' + day;
                };

                const formatDateValue = function (date) {
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = String(date.getFullYear());

                    if (locale === 'hr-HR') {
                        return day + '.' + month + '.' + year + '.';
                    }

                    return month + '/' + day + '/' + year;
                };

                const parseDate = function (value) {
                    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(value || '').trim());
                    if (!match) {
                        return null;
                    }

                    const date = new Date(Number(match[1]), Number(match[2]) - 1, Number(match[3]));
                    return Number.isNaN(date.getTime()) ? null : date;
                };

                const setCalendarMonth = function (field, monthDate) {
                    field.dataset.calendarMonth = String(monthDate.getFullYear()) + '-' + String(monthDate.getMonth());
                };

                const getCalendarMonth = function (field, fallbackDate) {
                    const storedValue = String(field.dataset.calendarMonth || '');
                    const parts = storedValue.split('-');

                    if (parts.length === 2) {
                        const year = Number(parts[0]);
                        const month = Number(parts[1]);
                        if (Number.isInteger(year) && Number.isInteger(month)) {
                            return new Date(year, month, 1);
                        }
                    }

                    return new Date(fallbackDate.getFullYear(), fallbackDate.getMonth(), 1);
                };

                const closeCalendars = function () {
                    dateFields.forEach(function (field) {
                        const panel = field.querySelector('[data-assessment-calendar-panel]');
                        const trigger = field.querySelector('[data-assessment-date-trigger]');

                        field.classList.remove('is-open');

                        if (panel) {
                            panel.hidden = true;
                        }

                        if (trigger instanceof HTMLButtonElement) {
                            trigger.setAttribute('aria-expanded', 'false');
                        }
                    });
                };

                const syncDateDisplays = function () {
                    dateDisplays.forEach(function (display) {
                        const targetId = display.getAttribute('data-assessment-date-display');
                        if (!targetId) {
                            return;
                        }

                        const input = root.querySelector('#' + targetId);
                        if (!(input instanceof HTMLInputElement)) {
                            return;
                        }

                        const parsedDate = parseDate(input.value);
                        const hasValue = parsedDate instanceof Date;

                        display.textContent = hasValue ? formatDateValue(parsedDate) : datePlaceholder;
                        display.classList.toggle('is-placeholder', !hasValue);
                    });
                };

                const renderCalendar = function (field) {
                    const input = field.querySelector('.ac-lease-date-input');
                    const title = field.querySelector('[data-assessment-calendar-title]');
                    const yearSelect = field.querySelector('[data-assessment-calendar-year]');
                    const weekdays = field.querySelector('[data-assessment-calendar-weekdays]');
                    const grid = field.querySelector('[data-assessment-calendar-grid]');

                    if (!(input instanceof HTMLInputElement) || !title || !weekdays || !grid) {
                        return;
                    }

                    const selectedDate = parseDate(input.value);
                    const fallbackDate = selectedDate || new Date();
                    const monthDate = getCalendarMonth(field, fallbackDate);
                    const today = new Date();
                    const currentYear = today.getFullYear();
                    const daysInMonth = new Date(monthDate.getFullYear(), monthDate.getMonth() + 1, 0).getDate();
                    const firstWeekday = (new Date(monthDate.getFullYear(), monthDate.getMonth(), 1).getDay() + 6) % 7;
                    const cells = [];

                    title.textContent = capitalize(calendarMonthFormatter.format(monthDate));

                    if (yearSelect instanceof HTMLSelectElement) {
                        const minYear = Math.min(currentYear - 20, monthDate.getFullYear() - 5);
                        const maxYear = Math.max(currentYear + 30, monthDate.getFullYear() + 5);
                        const options = [];

                        for (let year = minYear; year <= maxYear; year += 1) {
                            options.push('<option value="' + year + '"' + (year === monthDate.getFullYear() ? ' selected' : '') + '>' + year + '</option>');
                        }

                        yearSelect.innerHTML = options.join('');
                        yearSelect.value = String(monthDate.getFullYear());
                    }

                    weekdays.innerHTML = calendarWeekdays.map(function (label) {
                        return '<span>' + label + '</span>';
                    }).join('');

                    for (let index = 0; index < firstWeekday; index += 1) {
                        cells.push('<span class="ac-lease-calendar-cell is-empty" aria-hidden="true"></span>');
                    }

                    for (let day = 1; day <= daysInMonth; day += 1) {
                        const cellDate = new Date(monthDate.getFullYear(), monthDate.getMonth(), day);
                        const isoDate = formatInputDate(cellDate);
                        const isSelected = selectedDate && formatInputDate(selectedDate) === isoDate;
                        const isToday = formatInputDate(today) === isoDate;

                        cells.push(
                            '<button type="button" class="ac-lease-calendar-day' +
                                (isSelected ? ' is-selected' : '') +
                                (isToday ? ' is-today' : '') +
                            '" data-assessment-calendar-day="' + isoDate + '">' + day + '</button>'
                        );
                    }

                    while (cells.length % 7 !== 0) {
                        cells.push('<span class="ac-lease-calendar-cell is-empty" aria-hidden="true"></span>');
                    }

                    grid.innerHTML = cells.join('');

                    grid.querySelectorAll('[data-assessment-calendar-day]').forEach(function (button) {
                        button.addEventListener('click', function () {
                            const isoDate = button.getAttribute('data-assessment-calendar-day');
                            if (!isoDate) {
                                return;
                            }

                            input.value = isoDate;
                            syncDateDisplays();
                            closeCalendars();
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                            input.dispatchEvent(new Event('change', { bubbles: true }));
                        });
                    });
                };

                const openCalendar = function (field) {
                    const input = field.querySelector('.ac-lease-date-input');
                    const panel = field.querySelector('[data-assessment-calendar-panel]');
                    const trigger = field.querySelector('[data-assessment-date-trigger]');
                    const selectedDate = input instanceof HTMLInputElement ? parseDate(input.value) : null;
                    const baseDate = selectedDate || new Date();

                    if (window.matchMedia('(max-width: 760px)').matches) {
                        if (input instanceof HTMLInputElement) {
                            if (typeof input.showPicker === 'function') {
                                input.showPicker();
                            } else {
                                input.focus();
                                input.click();
                            }
                        }
                        return;
                    }

                    closeCalendars();
                    setCalendarMonth(field, new Date(baseDate.getFullYear(), baseDate.getMonth(), 1));
                    renderCalendar(field);

                    field.classList.add('is-open');

                    if (panel) {
                        panel.hidden = false;
                    }

                    if (trigger instanceof HTMLButtonElement) {
                        trigger.setAttribute('aria-expanded', 'true');
                    }
                };

                dateTriggers.forEach(function (trigger) {
                    trigger.addEventListener('click', function () {
                        const targetId = trigger.getAttribute('data-assessment-date-trigger');
                        if (!targetId) {
                            return;
                        }

                        const input = root.querySelector('#' + targetId);
                        if (!(input instanceof HTMLInputElement)) {
                            return;
                        }

                        const field = input.closest('[data-assessment-date-field]');
                        if (!field) {
                            return;
                        }

                        openCalendar(field);
                    });
                });

                dateFields.forEach(function (field) {
                    const display = field.querySelector('[data-assessment-date-display]');
                    const prevButton = field.querySelector('[data-assessment-calendar-prev]');
                    const nextButton = field.querySelector('[data-assessment-calendar-next]');
                    const yearSelect = field.querySelector('[data-assessment-calendar-year]');

                    if (display) {
                        display.addEventListener('click', function () {
                            openCalendar(field);
                        });
                    }

                    if (prevButton instanceof HTMLButtonElement) {
                        prevButton.addEventListener('click', function () {
                            const currentMonth = getCalendarMonth(field, new Date());
                            setCalendarMonth(field, new Date(currentMonth.getFullYear(), currentMonth.getMonth() - 1, 1));
                            renderCalendar(field);
                        });
                    }

                    if (nextButton instanceof HTMLButtonElement) {
                        nextButton.addEventListener('click', function () {
                            const currentMonth = getCalendarMonth(field, new Date());
                            setCalendarMonth(field, new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 1));
                            renderCalendar(field);
                        });
                    }

                    if (yearSelect instanceof HTMLSelectElement) {
                        yearSelect.addEventListener('change', function () {
                            const currentMonth = getCalendarMonth(field, new Date());
                            const nextYear = Number(yearSelect.value);

                            if (!Number.isInteger(nextYear)) {
                                return;
                            }

                            setCalendarMonth(field, new Date(nextYear, currentMonth.getMonth(), 1));
                            renderCalendar(field);
                        });
                    }
                });

                document.addEventListener('click', function (event) {
                    if (event.target.closest('[data-assessment-date-field]')) {
                        return;
                    }

                    closeCalendars();
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        closeCalendars();
                    }
                });

                syncDateDisplays();
            }());

            (function () {
                const forms = document.querySelectorAll('[data-recaptcha-form]');

                forms.forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        const tokenInput = form.querySelector('[data-recaptcha-token]');
                        const siteKey = form.dataset.recaptchaSiteKey;
                        const action = form.dataset.recaptchaAction || 'collaboration_assessment_form';

                        if (!tokenInput || !window.grecaptcha || !siteKey) {
                            return;
                        }

                        event.preventDefault();

                        grecaptcha.ready(function () {
                            grecaptcha.execute(siteKey, { action: action }).then(function (token) {
                                tokenInput.value = token || '';
                                form.submit();
                            });
                        });
                    });
                });
            }());
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/assessment/create.blade.php ENDPATH**/ ?>