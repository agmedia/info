<?php $__env->startSection('title', __('eu_funds_questionnaire.page_title')); ?>
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
            ['label' => __('eu_funds_questionnaire.page_title'), 'url' => route('eu-funds.show')],
            ['label' => __('eu_funds_questionnaire.form.title'), 'current' => true],
        ];
        $employeeOptions = [
            '0' => __('eu_funds_questionnaire.options.employee_count.0'),
            '1_9' => __('eu_funds_questionnaire.options.employee_count.1_9'),
            '10_49' => __('eu_funds_questionnaire.options.employee_count.10_49'),
            '50_249' => __('eu_funds_questionnaire.options.employee_count.50_249'),
            '250_plus' => __('eu_funds_questionnaire.options.employee_count.250_plus'),
        ];
        $relatedCompanyOptions = [
            'yes' => __('eu_funds_questionnaire.options.related_companies.yes'),
            'no' => __('eu_funds_questionnaire.options.related_companies.no'),
        ];
        $projectSectorOptions = [
            'manufacturing' => __('eu_funds_questionnaire.options.project_sectors.manufacturing'),
            'ict' => __('eu_funds_questionnaire.options.project_sectors.ict'),
            'creative_industries' => __('eu_funds_questionnaire.options.project_sectors.creative_industries'),
            'tourism' => __('eu_funds_questionnaire.options.project_sectors.tourism'),
            'agriculture' => __('eu_funds_questionnaire.options.project_sectors.agriculture'),
            'education' => __('eu_funds_questionnaire.options.project_sectors.education'),
            'construction' => __('eu_funds_questionnaire.options.project_sectors.construction'),
            'trade' => __('eu_funds_questionnaire.options.project_sectors.trade'),
            'transport_logistics' => __('eu_funds_questionnaire.options.project_sectors.transport_logistics'),
            'other' => __('eu_funds_questionnaire.options.project_sectors.other'),
        ];
        $plannedCostOptions = [
            'construction' => __('eu_funds_questionnaire.options.planned_costs.construction'),
            'equipment' => __('eu_funds_questionnaire.options.planned_costs.equipment'),
            'innovation_research' => __('eu_funds_questionnaire.options.planned_costs.innovation_research'),
            'energy_efficiency' => __('eu_funds_questionnaire.options.planned_costs.energy_efficiency'),
            'digitalization' => __('eu_funds_questionnaire.options.planned_costs.digitalization'),
        ];
        $investmentAmountOptions = [
            'up_to_100k' => __('eu_funds_questionnaire.options.investment_amount.up_to_100k'),
            '100k_500k' => __('eu_funds_questionnaire.options.investment_amount.100k_500k'),
            '500k_1000k' => __('eu_funds_questionnaire.options.investment_amount.500k_1000k'),
            '1000k_2000k' => __('eu_funds_questionnaire.options.investment_amount.1000k_2000k'),
            'over_2000k' => __('eu_funds_questionnaire.options.investment_amount.over_2000k'),
        ];
        $interestedServiceOptions = [
            'loans' => __('eu_funds_questionnaire.options.interested_services.loans'),
            'investment_incentives' => __('eu_funds_questionnaire.options.interested_services.investment_incentives'),
            'r_and_d_support' => __('eu_funds_questionnaire.options.interested_services.r_and_d_support'),
            'none' => __('eu_funds_questionnaire.options.interested_services.none'),
        ];
        $selectedProjectSectors = collect((array) old('project_sectors', []))->map(fn ($value) => (string) $value)->all();
        $selectedPlannedCosts = collect((array) old('planned_costs', []))->map(fn ($value) => (string) $value)->all();
        $selectedInterestedServices = collect((array) old('interested_services', []))->map(fn ($value) => (string) $value)->all();
        $showAdditionalNotes = old('related_companies') === 'yes' || trim((string) old('additional_notes')) !== '';
        $showProjectSectorOther = in_array('other', $selectedProjectSectors, true) || trim((string) old('project_sector_other')) !== '';
    ?>

    <div class="front-contact-page ac-assessment-page ac-eu-questionnaire-page">
        <?php if (isset($component)) { $__componentOriginale6a101278d02d7bbbf9e98ee1142bf75 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.front.page-title-band','data' => ['breadcrumbs' => $pageTitleBreadcrumbs,'sectionClass' => 'ac-assessment-title-band ac-eu-questionnaire-title-band']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('front.page-title-band'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pageTitleBreadcrumbs),'section-class' => 'ac-assessment-title-band ac-eu-questionnaire-title-band']); ?>
            <div class="ac-page-title-copy">
                <h1><?php echo e(__('eu_funds_questionnaire.heading')); ?></h1>
                <p><?php echo e(__('eu_funds_questionnaire.subheading')); ?></p>
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
                        action="<?php echo e(route('eu-funds.questionnaire.store')); ?>"
                        class="front-contact-form ac-assessment-form ac-eu-questionnaire-form"
                        novalidate
                        data-eu-funds-questionnaire-form
                        <?php if($captchaEnabled): ?> data-recaptcha-form data-recaptcha-site-key="<?php echo e($captchaSiteKey); ?>" data-recaptcha-action="eu_funds_questionnaire_form" <?php endif; ?>
                    >
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="recaptcha_token" value="" data-recaptcha-token>

                        <div class="front-contact-form-head">
                            <p class="front-contact-section-kicker"><?php echo e(__('eu_funds_questionnaire.form.kicker')); ?></p>
                            <h2><?php echo e(__('eu_funds_questionnaire.form.title')); ?></h2>
                            <p><?php echo e(__('eu_funds_questionnaire.form.intro')); ?></p>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
                            <div class="front-contact-status" role="status">
                                <?php echo e(session('status')); ?>

                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="ac-assessment-section">
                            <div class="ac-assessment-section-head">
                                <h3><?php echo e(__('eu_funds_questionnaire.sections.company')); ?></h3>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('eu_funds_questionnaire.form.company_name')); ?> *</label>
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
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('eu_funds_questionnaire.form.company_oib')); ?> *</label>
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

                            <div class="ac-assessment-field ac-assessment-field--full">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('eu_funds_questionnaire.form.company_activity')); ?> *</label>
                                <input type="text" name="company_activity" value="<?php echo e(old('company_activity')); ?>" class="front-contact-input h-11 w-full text-sm" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['company_activity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <fieldset class="ac-assessment-field ac-assessment-field--full">
                                <legend class="ac-eu-questionnaire-legend"><?php echo e(__('eu_funds_questionnaire.form.employee_count')); ?> *</legend>
                                <div class="ac-eu-questionnaire-option-grid ac-eu-questionnaire-option-grid--compact">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $employeeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="ac-eu-questionnaire-option">
                                            <input type="radio" name="employee_count" value="<?php echo e($value); ?>" <?php if(old('employee_count') === $value): echo 'checked'; endif; ?> required>
                                            <span><?php echo e($label); ?></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['employee_count'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </fieldset>

                            <fieldset class="ac-assessment-field ac-assessment-field--full" data-conditional-root="related_companies">
                                <legend class="ac-eu-questionnaire-legend"><?php echo e(__('eu_funds_questionnaire.form.related_companies')); ?> *</legend>
                                <div class="ac-eu-questionnaire-option-grid ac-eu-questionnaire-option-grid--binary">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $relatedCompanyOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="ac-eu-questionnaire-option">
                                            <input type="radio" name="related_companies" value="<?php echo e($value); ?>" <?php if(old('related_companies') === $value): echo 'checked'; endif; ?> required data-conditional-toggle="related_companies">
                                            <span><?php echo e($label); ?></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['related_companies'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <div class="ac-eu-questionnaire-conditional <?php echo e($showAdditionalNotes ? '' : 'hidden'); ?>" data-conditional-target="related_companies">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('eu_funds_questionnaire.form.additional_notes')); ?> *</label>
                                    <textarea name="additional_notes" rows="4" class="front-contact-textarea ac-assessment-textarea w-full text-sm" placeholder="<?php echo e(__('eu_funds_questionnaire.form.additional_notes_placeholder')); ?>"><?php echo e(old('additional_notes')); ?></textarea>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['additional_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </fieldset>
                        </div>

                        <div class="ac-assessment-section">
                            <div class="ac-assessment-section-head">
                                <h3><?php echo e(__('eu_funds_questionnaire.sections.investment')); ?></h3>
                            </div>

                            <fieldset class="ac-assessment-field ac-assessment-field--full" data-conditional-root="project_sector_other">
                                <legend class="ac-eu-questionnaire-legend"><?php echo e(__('eu_funds_questionnaire.form.project_sectors')); ?> *</legend>
                                <div class="ac-eu-questionnaire-option-grid">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $projectSectorOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="ac-eu-questionnaire-option">
                                            <input
                                                type="checkbox"
                                                name="project_sectors[]"
                                                value="<?php echo e($value); ?>"
                                                <?php if(in_array($value, $selectedProjectSectors, true)): echo 'checked'; endif; ?>
                                                <?php if($value === 'other'): ?> data-conditional-checkbox="project_sector_other" <?php endif; ?>
                                            >
                                            <span><?php echo e($label); ?></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['project_sectors'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['project_sectors.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <div class="ac-eu-questionnaire-conditional <?php echo e($showProjectSectorOther ? '' : 'hidden'); ?>" data-conditional-target="project_sector_other">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('eu_funds_questionnaire.form.project_sector_other')); ?></label>
                                    <input type="text" name="project_sector_other" value="<?php echo e(old('project_sector_other')); ?>" class="front-contact-input h-11 w-full text-sm" placeholder="<?php echo e(__('eu_funds_questionnaire.form.project_sector_other_placeholder')); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['project_sector_other'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </fieldset>

                            <div class="ac-assessment-field ac-assessment-field--full">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('eu_funds_questionnaire.form.investment_location')); ?> *</label>
                                <input type="text" name="investment_location" value="<?php echo e(old('investment_location')); ?>" class="front-contact-input h-11 w-full text-sm" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['investment_location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <fieldset class="ac-assessment-field ac-assessment-field--full">
                                <legend class="ac-eu-questionnaire-legend"><?php echo e(__('eu_funds_questionnaire.form.planned_costs')); ?> *</legend>
                                <div class="ac-eu-questionnaire-option-grid">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $plannedCostOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="ac-eu-questionnaire-option">
                                            <input type="checkbox" name="planned_costs[]" value="<?php echo e($value); ?>" <?php if(in_array($value, $selectedPlannedCosts, true)): echo 'checked'; endif; ?>>
                                            <span><?php echo e($label); ?></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['planned_costs'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['planned_costs.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </fieldset>

                            <fieldset class="ac-assessment-field ac-assessment-field--full">
                                <legend class="ac-eu-questionnaire-legend"><?php echo e(__('eu_funds_questionnaire.form.investment_amount')); ?> *</legend>
                                <div class="ac-eu-questionnaire-option-grid">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $investmentAmountOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="ac-eu-questionnaire-option">
                                            <input type="radio" name="investment_amount" value="<?php echo e($value); ?>" <?php if(old('investment_amount') === $value): echo 'checked'; endif; ?> required>
                                            <span><?php echo e($label); ?></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['investment_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </fieldset>
                        </div>

                        <div class="ac-assessment-section">
                            <div class="ac-assessment-section-head">
                                <h3><?php echo e(__('eu_funds_questionnaire.sections.services')); ?></h3>
                            </div>

                            <fieldset class="ac-assessment-field ac-assessment-field--full">
                                <legend class="ac-eu-questionnaire-legend"><?php echo e(__('eu_funds_questionnaire.form.interested_services')); ?> *</legend>
                                <div class="ac-eu-questionnaire-option-grid">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $interestedServiceOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="ac-eu-questionnaire-option">
                                            <input type="checkbox" name="interested_services[]" value="<?php echo e($value); ?>" <?php if(in_array($value, $selectedInterestedServices, true)): echo 'checked'; endif; ?>>
                                            <span><?php echo e($label); ?></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['interested_services'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['interested_services.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </fieldset>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('eu_funds_questionnaire.form.contact_name')); ?> *</label>
                                    <input type="text" name="contact_name" value="<?php echo e(old('contact_name')); ?>" class="front-contact-input h-11 w-full text-sm" required>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['contact_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs font-semibold text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('eu_funds_questionnaire.form.email')); ?> *</label>
                                    <input type="email" name="email" value="<?php echo e(old('email', auth()->user()?->email)); ?>" class="front-contact-input h-11 w-full text-sm" required>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
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
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('eu_funds_questionnaire.form.contact_phone')); ?> *</label>
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

                        <div class="front-contact-consent-wrap">
                            <label class="front-contact-consent">
                                <input type="checkbox" name="accept_terms" value="1" class="front-contact-checkbox mt-0.5 h-4 w-4 border-slate-300 text-slate-900 focus:ring-0" <?php if((bool) old('accept_terms')): echo 'checked'; endif; ?>>
                                <span><?php echo e(__('eu_funds_questionnaire.form.accept_terms_label')); ?></span>
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

                        <p class="ac-eu-questionnaire-privacy"><?php echo e(__('eu_funds_questionnaire.privacy_note')); ?></p>

                        <div class="front-contact-form-actions">
                            <button type="submit" class="front-contact-submit inline-flex h-11 items-center justify-center px-6 text-sm font-semibold text-white transition">
                                <?php echo e(__('eu_funds_questionnaire.form.submit')); ?>

                            </button>
                        </div>
                    </form>

                    <aside class="front-contact-sidebar">
                        <div class="front-contact-panel front-contact-panel--direct">
                            <h2><?php echo e(__('eu_funds_questionnaire.sidebar.title')); ?></h2>
                            <p class="front-contact-panel-intro"><?php echo e(__('eu_funds_questionnaire.sidebar.body')); ?></p>

                            <ul class="front-contact-direct-list">
                                <li>
                                    <span><?php echo e(__('eu_funds_questionnaire.sidebar.point_1_label')); ?></span>
                                    <strong><?php echo e(__('eu_funds_questionnaire.sidebar.point_1')); ?></strong>
                                </li>
                                <li>
                                    <span><?php echo e(__('eu_funds_questionnaire.sidebar.point_2_label')); ?></span>
                                    <strong><?php echo e(__('eu_funds_questionnaire.sidebar.point_2')); ?></strong>
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
                const forms = document.querySelectorAll('[data-eu-funds-questionnaire-form]');

                const bindConditionalFields = function (form) {
                    const relatedTargets = form.querySelectorAll('[data-conditional-target="related_companies"]');
                    const relatedInputs = form.querySelectorAll('[data-conditional-toggle="related_companies"]');
                    const sectorOtherTargets = form.querySelectorAll('[data-conditional-target="project_sector_other"]');
                    const sectorOtherCheckboxes = form.querySelectorAll('[data-conditional-checkbox="project_sector_other"]');

                    const updateRelatedCompanies = function () {
                        const active = Array.from(relatedInputs).some(function (input) {
                            return input.checked && input.value === 'yes';
                        });

                        relatedTargets.forEach(function (target) {
                            target.classList.toggle('hidden', !active);
                        });
                    };

                    const updateProjectSectorOther = function () {
                        const active = Array.from(sectorOtherCheckboxes).some(function (input) {
                            return input.checked;
                        });

                        sectorOtherTargets.forEach(function (target) {
                            target.classList.toggle('hidden', !active);
                        });
                    };

                    relatedInputs.forEach(function (input) {
                        input.addEventListener('change', updateRelatedCompanies);
                    });
                    sectorOtherCheckboxes.forEach(function (input) {
                        input.addEventListener('change', updateProjectSectorOther);
                    });

                    updateRelatedCompanies();
                    updateProjectSectorOther();
                };

                forms.forEach(function (form) {
                    bindConditionalFields(form);

                    form.addEventListener('submit', function (event) {
                        event.preventDefault();

                        const tokenInput = form.querySelector('[data-recaptcha-token]');
                        const siteKey = form.dataset.recaptchaSiteKey;
                        const action = form.dataset.recaptchaAction || 'eu_funds_questionnaire_form';

                        if (!tokenInput || !window.grecaptcha || !siteKey) {
                            form.submit();
                            return;
                        }

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

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/eu-funds-questionnaire.blade.php ENDPATH**/ ?>