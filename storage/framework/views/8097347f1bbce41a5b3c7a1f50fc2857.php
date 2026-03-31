<?php
    $translationPayload = $form['translation_payload'] ?? [];
    $pagePayload = $form['page_payload'] ?? [];
    $currentTemplateKey = $form['template_key'] ?? \App\Support\Content\ServicePageTemplateRegistry::FAMILY_BUSINESS;
    $currentTemplateLabel = $templateOptions[$currentTemplateKey] ?? $currentTemplateKey;
    $isFinanceTemplate = $currentTemplateKey === \App\Support\Content\ServicePageTemplateRegistry::FINANCE;
    $isAccountingTemplate = $currentTemplateKey === \App\Support\Content\ServicePageTemplateRegistry::ACCOUNTING;
    $isAuditTemplate = $currentTemplateKey === \App\Support\Content\ServicePageTemplateRegistry::AUDIT;
    $isTaxTemplate = $currentTemplateKey === \App\Support\Content\ServicePageTemplateRegistry::TAX;
    $isEuFundsTemplate = $currentTemplateKey === \App\Support\Content\ServicePageTemplateRegistry::EU_FUNDS;
    $accountingEditorSections = [
        'accounting-intro-admin' => __('Overview'),
        'accounting-meeting-admin' => __('Meeting'),
        'accounting-blog-admin' => __('Blog'),
    ];
    $financeEditorSections = [
        'finance-pandea' => __('Pandea'),
        'finance-services-intro' => __('Services Intro'),
        'finance-ma' => __('M&A'),
        'finance-due-diligence' => __('Due Diligence'),
        'finance-valuations' => __('Valuations'),
        'finance-capital-raising' => __('Capital Raising'),
        'finance-restructuring' => __('Restructuring'),
        'finance-meeting' => __('Meeting'),
    ];
    $auditEditorSections = [
        'audit-overview-admin' => __('Overview'),
        'audit-obligors-admin' => __('Obligors'),
        'audit-services-admin' => __('Services'),
        'audit-value-admin' => __('Value'),
        'audit-approach-admin' => __('Approach'),
        'audit-meeting-admin' => __('Meeting'),
    ];
    $taxEditorSections = [
        'tax-overview-admin' => __('Overview'),
        'tax-services-admin' => __('Services'),
        'tax-compliance-admin' => __('Compliance'),
        'tax-review-admin' => __('Tax Review'),
        'tax-optimization-admin' => __('Optimization'),
        'tax-due-diligence-admin' => __('Due Diligence'),
        'tax-transfer-pricing-admin' => __('Transfer Pricing'),
        'tax-meeting-admin' => __('Meeting'),
    ];
    $euFundsEditorSections = [
        'eu-funds-about' => 'About',
        'eu-funds-overview' => 'Overview',
        'eu-funds-chart' => 'Funding Chart',
        'eu-funds-process' => 'Process',
        'eu-funds-calls' => 'Calls',
        'eu-funds-resources' => 'Resources',
        'eu-funds-laws' => 'Laws',
        'eu-funds-testimonials' => 'Testimonials',
        'eu-funds-blog' => 'Blog',
        'eu-funds-meeting' => 'Meeting',
    ];
    $blogAutoCategoryLabel = $isAccountingTemplate
        ? __('Auto (current accounting category)')
        : ($isAuditTemplate
            ? __('Auto (current audit category)')
            : ($isTaxTemplate
                ? __('Auto (current tax category)')
                : ($isEuFundsTemplate
                    ? __('Auto (current EU funds category)')
                    : __('Auto (current family-business category)'))));
?>

<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500"><?php echo e(__('Content / Services')); ?></p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900"><?php echo e($isEdit ? __('Edit Service Page') : __('Create Service Page')); ?></h1>
                <p class="mt-2 text-sm text-slate-600"><?php echo e(__('Template-driven service landing page with locale content, sources, and media.')); ?></p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="admin-chip"><?php echo e(__('Locale:')); ?> <?php echo e($form['locale']); ?></span>
                <button type="button" wire:click="backToList" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('Back to List')); ?></button>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="admin-panel admin-form-panel p-3 sm:p-4">
            <div class="flex flex-wrap gap-2">
                <button type="button" wire:click="setTab('content')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] <?php echo e($activeTab === 'content' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100'); ?>">
                    <?php echo e(__('Content')); ?>

                </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($templateSupportsSources): ?>
                    <button type="button" wire:click="setTab('sources')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] <?php echo e($activeTab === 'sources' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100'); ?>">
                        <?php echo e(__('Sources')); ?>

                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button type="button" wire:click="setTab('seo')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] <?php echo e($activeTab === 'seo' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100'); ?>">
                    <?php echo e(__('SEO')); ?>

                </button>
                <button type="button" wire:click="setTab('media')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] <?php echo e($activeTab === 'media' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100'); ?>">
                    <?php echo e(__('Media')); ?>

                </button>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'content'): ?>
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title"><?php echo e(__('Core Data')); ?></p>

                <div class="mt-4 grid gap-3" style="grid-template-columns: repeat(12, minmax(0, 1fr));">
                    <div style="grid-column: span 3;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Code')); ?></label>
                        <input type="text" wire:model="form.code" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-mono" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div style="grid-column: span 3;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Template')); ?></label>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isEdit): ?>
                            <input type="text" value="<?php echo e($currentTemplateLabel); ?>" readonly class="w-full rounded-xl border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-700" />
                        <?php else: ?>
                            <select wire:model.live="form.template_key" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $templateOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionKey => $templateLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($optionKey); ?>" <?php if($currentTemplateKey === $optionKey): echo 'selected'; endif; ?>><?php echo e($templateLabel); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isEdit): ?>
                            <p class="mt-1 text-xs text-slate-500"><?php echo e(__('Template is locked after creation so block structure stays stable.')); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.template_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Published At')); ?></label>
                        <input type="datetime-local" wire:model="form.published_at" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.published_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Sort Order')); ?></label>
                        <input type="number" min="0" wire:model="form.sort_order" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Locale')); ?></label>
                        <select wire:model.live="form.locale" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $adminLocaleOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($localeOption); ?>"><?php echo e($localeOption); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.locale'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <button
                        type="button"
                        wire:click="$toggle('form.is_active')"
                        class="admin-switch"
                        data-state="<?php echo e($form['is_active'] ? 'on' : 'off'); ?>"
                        role="switch"
                        aria-checked="<?php echo e($form['is_active'] ? 'true' : 'false'); ?>"
                        aria-label="<?php echo e(__('Toggle service page active state')); ?>"
                    >
                        <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                        <span class="admin-switch-label"><?php echo e($form['is_active'] ? __('Active') : __('Inactive')); ?></span>
                    </button>
                </div>

                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                        <input type="text" wire:model="form.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Slug')); ?></label>
                            <button type="button" wire:click="generateSlug" class="text-xs font-semibold text-slate-600 hover:text-slate-900"><?php echo e(__('Generate')); ?></button>
                        </div>
                        <input type="text" wire:model="form.slug" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title"><?php echo e(__('Hero')); ?></p>

                <div class="mt-4 grid gap-3 md:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Brand Title')); ?></label>
                        <input type="text" wire:model="form.translation_payload.hero.brand_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Subtitle Lead')); ?></label>
                        <input type="text" wire:model="form.translation_payload.hero.subtitle_lead" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Subtitle Accent')); ?></label>
                        <input type="text" wire:model="form.translation_payload.hero.subtitle_accent" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                    <textarea rows="5" wire:model="form.translation_payload.hero.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>

                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('CTA Label')); ?></label>
                        <input type="text" wire:model="form.translation_payload.hero.cta_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('CTA URL')); ?></label>
                        <input type="text" wire:model="form.translation_payload.hero.cta_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isFinanceTemplate): ?>
                <div class="admin-panel admin-form-panel p-6">
                    <p class="admin-section-title"><?php echo e(__('Finance Navigator')); ?></p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $financeEditorSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sectionId => $sectionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="#<?php echo e($sectionId); ?>" class="admin-chip"><?php echo e($sectionLabel); ?></a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <p class="mt-4 text-sm text-slate-600">
                        <?php echo e(__('Top-level finance sections are fixed by the frontend template. You can add or remove paragraphs, list rows, and sale phases below, but adding a completely new section still requires a template/code change.')); ?>

                    </p>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div id="finance-pandea" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title"><?php echo e(__('Pandea Network')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.pandea.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['pandea']['body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Paragraph')); ?> #<?php echo e($index + 1); ?></label>
                                    <button type="button" wire:click="removeTranslationListItem('pandea.body', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                </div>
                                <textarea rows="5" wire:model="form.translation_payload.pandea.body.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('pandea.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add Paragraph')); ?>

                            </button>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Logo Alt')); ?></label>
                            <input type="text" wire:model="form.translation_payload.pandea.logo_alt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div id="finance-services-intro" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title"><?php echo e(__('Services Intro')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                            <input type="text" wire:model="form.translation_payload.services_intro.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.services_intro.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                            <textarea rows="6" wire:model="form.translation_payload.services_intro.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>
                    </div>
                </div>

                <div id="finance-ma" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                    <p class="admin-section-title"><?php echo e(__('M&A Section')); ?></p>

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Section Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.ma.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Sale Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.ma.sale.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Section Intro')); ?></label>
                        <textarea rows="5" wire:model="form.translation_payload.ma.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Sale Body')); ?></label>
                        <textarea rows="6" wire:model="form.translation_payload.ma.sale.body" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Sale Process Title')); ?></label>
                        <input type="text" wire:model="form.translation_payload.ma.sale.process_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>

                    <div class="mt-6 space-y-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['ma']['sale']['phases'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phaseIndex => $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Phase Block')); ?> #<?php echo e($phaseIndex + 1); ?></p>
                                    <button type="button" wire:click="removeTranslationListItem('ma.sale.phases', <?php echo e($phaseIndex); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove Phase')); ?></button>
                                </div>

                                <div class="mt-3 grid gap-3 md:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Phase')); ?> #<?php echo e($phaseIndex + 1); ?></label>
                                        <input type="text" wire:model="form.translation_payload.ma.sale.phases.<?php echo e($phaseIndex); ?>.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Phase Label')); ?></label>
                                        <input type="text" wire:model="form.translation_payload.ma.sale.phases.<?php echo e($phaseIndex); ?>.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                    </div>
                                </div>

                                <div class="mt-4 grid gap-3 lg:grid-cols-2">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($phase['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemIndex => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div>
                                            <div class="mb-1 flex items-center justify-between gap-3">
                                                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Item')); ?> #<?php echo e($itemIndex + 1); ?></label>
                                                <button type="button" wire:click="removeTranslationListItem('ma.sale.phases.<?php echo e($phaseIndex); ?>.items', <?php echo e($itemIndex); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                            </div>
                                            <input type="text" wire:model="form.translation_payload.ma.sale.phases.<?php echo e($phaseIndex); ?>.items.<?php echo e($itemIndex); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <div class="mt-4">
                                    <button type="button" wire:click="addTranslationListItem('ma.sale.phases.<?php echo e($phaseIndex); ?>.items')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                        <?php echo e(__('Add Item')); ?>

                                    </button>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="mt-4">
                        <button type="button" wire:click="addTranslationListItem('ma.sale.phases', 'phase')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                            <?php echo e(__('Add Phase')); ?>

                        </button>
                    </div>

                    <div class="mt-6 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Acquisition Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.ma.acquisition.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div></div>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Acquisition Body')); ?></label>
                        <textarea rows="5" wire:model="form.translation_payload.ma.acquisition.body" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div id="finance-due-diligence" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title"><?php echo e(__('Due Diligence')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.due_diligence.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                            <textarea rows="6" wire:model="form.translation_payload.due_diligence.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Help Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.due_diligence.help_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-4 space-y-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['due_diligence']['help_items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div>
                                    <div class="mb-1 flex items-center justify-between gap-3">
                                        <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Help Item')); ?> #<?php echo e($index + 1); ?></label>
                                        <button type="button" wire:click="removeTranslationListItem('due_diligence.help_items', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                    </div>
                                    <input type="text" wire:model="form.translation_payload.due_diligence.help_items.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('due_diligence.help_items')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add Help Item')); ?>

                            </button>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Closing Text')); ?></label>
                            <textarea rows="4" wire:model="form.translation_payload.due_diligence.closing" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>
                    </div>

                    <div id="finance-valuations" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title"><?php echo e(__('Valuations')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.valuations.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['valuations']['body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Paragraph')); ?> #<?php echo e($index + 1); ?></label>
                                    <button type="button" wire:click="removeTranslationListItem('valuations.body', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                </div>
                                <textarea rows="4" wire:model="form.translation_payload.valuations.body.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('valuations.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add Paragraph')); ?>

                            </button>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Methods Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.valuations.methods_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-4 space-y-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['valuations']['methods'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div>
                                    <div class="mb-1 flex items-center justify-between gap-3">
                                        <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Method')); ?> #<?php echo e($index + 1); ?></label>
                                        <button type="button" wire:click="removeTranslationListItem('valuations.methods', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                    </div>
                                    <input type="text" wire:model="form.translation_payload.valuations.methods.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('valuations.methods')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add Method')); ?>

                            </button>
                        </div>
                    </div>
                </div>

                <div id="finance-capital-raising" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                    <p class="admin-section-title"><?php echo e(__('Capital Raising')); ?></p>

                    <div class="mt-4">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                        <input type="text" wire:model="form.translation_payload.capital_raising.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>

                    <div class="mt-4 grid gap-4 lg:grid-cols-2">
                        <div class="space-y-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['capital_raising']['body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div>
                                    <div class="mb-1 flex items-center justify-between gap-3">
                                        <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Paragraph')); ?> #<?php echo e($index + 1); ?></label>
                                        <button type="button" wire:click="removeTranslationListItem('capital_raising.body', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                    </div>
                                    <textarea rows="4" wire:model="form.translation_payload.capital_raising.body.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="pt-1">
                                <button type="button" wire:click="addTranslationListItem('capital_raising.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                    <?php echo e(__('Add Paragraph')); ?>

                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Sources Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.capital_raising.sources_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />

                            <div class="mt-4 space-y-3">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['capital_raising']['sources'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $source): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div>
                                        <div class="mb-1 flex items-center justify-between gap-3">
                                            <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Source')); ?> #<?php echo e($index + 1); ?></label>
                                            <button type="button" wire:click="removeTranslationListItem('capital_raising.sources', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                        </div>
                                        <input type="text" wire:model="form.translation_payload.capital_raising.sources.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="mt-3">
                                <button type="button" wire:click="addTranslationListItem('capital_raising.sources')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                    <?php echo e(__('Add Source')); ?>

                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="finance-restructuring" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                    <p class="admin-section-title"><?php echo e(__('Financial Restructuring')); ?></p>

                    <div class="mt-4">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                        <input type="text" wire:model="form.translation_payload.restructuring.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>

                    <div class="mt-4 grid gap-4 xl:grid-cols-2">
                        <div class="space-y-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['restructuring']['body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div>
                                    <div class="mb-1 flex items-center justify-between gap-3">
                                        <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Paragraph')); ?> #<?php echo e($index + 1); ?></label>
                                        <button type="button" wire:click="removeTranslationListItem('restructuring.body', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                    </div>
                                    <textarea rows="4" wire:model="form.translation_payload.restructuring.body.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="pt-1">
                                <button type="button" wire:click="addTranslationListItem('restructuring.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                    <?php echo e(__('Add Paragraph')); ?>

                                </button>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Pre-bankruptcy Title')); ?></label>
                                <input type="text" wire:model="form.translation_payload.restructuring.prebankruptcy_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Pre-bankruptcy Text')); ?></label>
                                <textarea rows="4" wire:model="form.translation_payload.restructuring.prebankruptcy_body" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Options Title')); ?></label>
                                <input type="text" wire:model="form.translation_payload.restructuring.options_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />

                                <div class="mt-3 space-y-3">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['restructuring']['options'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div>
                                            <div class="mb-1 flex items-center justify-between gap-3">
                                                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Option')); ?> #<?php echo e($index + 1); ?></label>
                                                <button type="button" wire:click="removeTranslationListItem('restructuring.options', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                            </div>
                                            <input type="text" wire:model="form.translation_payload.restructuring.options.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <div class="mt-3">
                                    <button type="button" wire:click="addTranslationListItem('restructuring.options')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                        <?php echo e(__('Add Option')); ?>

                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Reasons Title')); ?></label>
                                <input type="text" wire:model="form.translation_payload.restructuring.reasons_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />

                                <div class="mt-3 space-y-3">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['restructuring']['reasons'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div>
                                            <div class="mb-1 flex items-center justify-between gap-3">
                                                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Reason')); ?> #<?php echo e($index + 1); ?></label>
                                                <button type="button" wire:click="removeTranslationListItem('restructuring.reasons', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                            </div>
                                            <input type="text" wire:model="form.translation_payload.restructuring.reasons.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <div class="mt-3">
                                    <button type="button" wire:click="addTranslationListItem('restructuring.reasons')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                        <?php echo e(__('Add Reason')); ?>

                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Team Services Title')); ?></label>
                                <input type="text" wire:model="form.translation_payload.restructuring.team_services_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />

                                <div class="mt-3 space-y-3">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['restructuring']['team_services'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div>
                                            <div class="mb-1 flex items-center justify-between gap-3">
                                                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Team Service')); ?> #<?php echo e($index + 1); ?></label>
                                                <button type="button" wire:click="removeTranslationListItem('restructuring.team_services', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                            </div>
                                            <input type="text" wire:model="form.translation_payload.restructuring.team_services.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <div class="mt-3">
                                    <button type="button" wire:click="addTranslationListItem('restructuring.team_services')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                        <?php echo e(__('Add Team Service')); ?>

                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="finance-meeting" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                    <p class="admin-section-title"><?php echo e(__('Meeting Section')); ?></p>

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                        <textarea rows="5" wire:model="form.translation_payload.meeting.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>

                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Visit Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.visit_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Contact Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.contact_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Visit Line 1')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.visit_lines.0" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Visit Line 2')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.visit_lines.1" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Submit Label')); ?></label>
                        <input type="text" wire:model="form.translation_payload.meeting.submit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-[18rem]" />
                    </div>

                    <div class="mt-6 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Direct Phone Label')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.direct_phone_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Direct Email Label')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.direct_email_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Form Labels')); ?></p>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('First Name')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.first_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Last Name')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.last_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Company')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.company" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Phone Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.phone" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Email Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.email" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Subject Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.subject" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Message Label')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.form_labels.message" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>
                </div>
            <?php elseif($isAuditTemplate): ?>
                <div class="admin-panel admin-form-panel p-6">
                    <p class="admin-section-title"><?php echo e(__('Audit Navigator')); ?></p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $auditEditorSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sectionId => $sectionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="#<?php echo e($sectionId); ?>" class="admin-chip"><?php echo e($sectionLabel); ?></a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <p class="mt-4 text-sm text-slate-600">
                        <?php echo e(__('Revizija koristi fiksni landing layout. Ovdje uređujete copy sekcija, liste i završne blokove za kontakt i blog.')); ?>

                    </p>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div id="audit-overview-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title"><?php echo e(__('Overview Block')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                            <input type="text" wire:model="form.translation_payload.overview.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.overview.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                            <textarea rows="4" wire:model="form.translation_payload.overview.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Highlight Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.overview.highlight_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['overview']['body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Paragraph')); ?> #<?php echo e($index + 1); ?></label>
                                    <button type="button" wire:click="removeTranslationListItem('overview.body', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                </div>
                                <textarea rows="4" wire:model="form.translation_payload.overview.body.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('overview.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add Paragraph')); ?>

                            </button>
                        </div>
                    </div>

                    <div id="audit-obligors-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title"><?php echo e(__('Obligors & Thresholds')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                            <input type="text" wire:model="form.translation_payload.obligors.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.obligors.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                            <textarea rows="4" wire:model="form.translation_payload.obligors.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Primary Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.obligors.primary_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['obligors']['primary_items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Primary Item')); ?> #<?php echo e($index + 1); ?></label>
                                    <button type="button" wire:click="removeTranslationListItem('obligors.primary_items', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                </div>
                                <input type="text" wire:model="form.translation_payload.obligors.primary_items.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('obligors.primary_items')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add Primary Item')); ?>

                            </button>
                        </div>

                        <div class="mt-5">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Thresholds Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.obligors.thresholds_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Thresholds Intro')); ?></label>
                            <textarea rows="3" wire:model="form.translation_payload.obligors.thresholds_intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['obligors']['thresholds'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Threshold')); ?> #<?php echo e($index + 1); ?></label>
                                    <button type="button" wire:click="removeTranslationListItem('obligors.thresholds', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                </div>
                                <input type="text" wire:model="form.translation_payload.obligors.thresholds.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('obligors.thresholds')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add Threshold')); ?>

                            </button>
                        </div>

                        <div class="mt-5">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Note')); ?></label>
                            <textarea rows="4" wire:model="form.translation_payload.obligors.note" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>
                    </div>
                </div>

                <div id="audit-services-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                    <p class="admin-section-title"><?php echo e(__('Audit Services')); ?></p>

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                            <input type="text" wire:model="form.translation_payload.services.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.services.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                        <textarea rows="4" wire:model="form.translation_payload.services.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>

                    <div class="mt-6 grid gap-4 xl:grid-cols-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['services']['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Service Item')); ?> #<?php echo e($index + 1); ?></p>
                                <div class="mt-3">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                                    <input type="text" wire:model="form.translation_payload.services.items.<?php echo e($index); ?>.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div class="mt-3">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Text')); ?></label>
                                    <textarea rows="5" wire:model="form.translation_payload.services.items.<?php echo e($index); ?>.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div id="audit-value-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title"><?php echo e(__('Value Section')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                            <input type="text" wire:model="form.translation_payload.value.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.value.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                            <textarea rows="4" wire:model="form.translation_payload.value.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['value']['benefits'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Benefit')); ?> #<?php echo e($index + 1); ?></label>
                                    <button type="button" wire:click="removeTranslationListItem('value.benefits', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                </div>
                                <input type="text" wire:model="form.translation_payload.value.benefits.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('value.benefits')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add Benefit')); ?>

                            </button>
                        </div>

                        <div class="mt-5">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Conclusion')); ?></label>
                            <textarea rows="4" wire:model="form.translation_payload.value.conclusion" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>
                    </div>

                    <div id="audit-approach-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title"><?php echo e(__('Approach Section')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                            <input type="text" wire:model="form.translation_payload.approach.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.approach.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                            <textarea rows="4" wire:model="form.translation_payload.approach.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Principles Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.approach.principles_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['approach']['principles'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Principle')); ?> #<?php echo e($index + 1); ?></label>
                                    <button type="button" wire:click="removeTranslationListItem('approach.principles', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                </div>
                                <input type="text" wire:model="form.translation_payload.approach.principles.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('approach.principles')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add Principle')); ?>

                            </button>
                        </div>

                        <div class="mt-5">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Reasons Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.approach.reasons_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['approach']['reasons'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Reason')); ?> #<?php echo e($index + 1); ?></label>
                                    <button type="button" wire:click="removeTranslationListItem('approach.reasons', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                </div>
                                <input type="text" wire:model="form.translation_payload.approach.reasons.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('approach.reasons')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add Reason')); ?>

                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div class="admin-panel admin-form-panel p-6">
                        <p class="admin-section-title"><?php echo e(__('Blog Section')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                            <input type="text" wire:model="form.translation_payload.blog_section.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.blog_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            <p class="mt-1 text-xs text-slate-500"><?php echo e(__('Use :category placeholder if you want the current blog category name inserted automatically.')); ?></p>
                        </div>
                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                            <textarea rows="4" wire:model="form.translation_payload.blog_section.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>
                    </div>

                    <div id="audit-meeting-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title"><?php echo e(__('Meeting Section')); ?></p>

                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                            <textarea rows="5" wire:model="form.translation_payload.meeting.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Visit Title')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.visit_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Contact Title')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.contact_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Visit Line 1')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.visit_lines.0" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Visit Line 2')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.visit_lines.1" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Submit Label')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.submit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-[18rem]" />
                        </div>

                        <div class="mt-6 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Direct Phone Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.direct_phone_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Direct Email Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.direct_email_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Form Labels')); ?></p>

                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('First Name')); ?></label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.first_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Last Name')); ?></label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.last_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            </div>

                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Company')); ?></label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.company" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Phone Label')); ?></label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.phone" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            </div>

                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Email Label')); ?></label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.email" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Subject Label')); ?></label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.subject" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Message Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.message" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>
                    </div>
                </div>
            <?php elseif($isTaxTemplate): ?>
                <div class="admin-panel admin-form-panel p-6">
                    <p class="admin-section-title"><?php echo e(__('Tax Navigator')); ?></p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $taxEditorSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sectionId => $sectionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="#<?php echo e($sectionId); ?>" class="admin-chip"><?php echo e($sectionLabel); ?></a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <p class="mt-4 text-sm text-slate-600">
                        <?php echo e(__('Porezi koristi fiksni landing layout. Ovdje uređujete copy, liste, compliance blokove i završne kontakt/blog sekcije.')); ?>

                    </p>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div id="tax-overview-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title"><?php echo e(__('Overview Block')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                            <input type="text" wire:model="form.translation_payload.overview.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.overview.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                            <textarea rows="4" wire:model="form.translation_payload.overview.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Highlight Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.overview.highlight_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['overview']['body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Paragraph')); ?> #<?php echo e($index + 1); ?></label>
                                    <button type="button" wire:click="removeTranslationListItem('overview.body', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                </div>
                                <textarea rows="4" wire:model="form.translation_payload.overview.body.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('overview.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add Paragraph')); ?>

                            </button>
                        </div>
                    </div>

                    <div class="admin-panel admin-form-panel p-6">
                        <p class="admin-section-title"><?php echo e(__('Overview Highlights')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Highlights Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.overview.highlights_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['overview']['highlights'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Highlight')); ?> #<?php echo e($index + 1); ?></label>
                                    <button type="button" wire:click="removeTranslationListItem('overview.highlights', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                </div>
                                <input type="text" wire:model="form.translation_payload.overview.highlights.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('overview.highlights')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add Highlight')); ?>

                            </button>
                        </div>
                    </div>
                </div>

                <div id="tax-services-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                    <p class="admin-section-title"><?php echo e(__('Services Grid')); ?></p>

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                            <input type="text" wire:model="form.translation_payload.services.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.services.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                        <textarea rows="4" wire:model="form.translation_payload.services.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>

                    <div class="mt-6 grid gap-4 xl:grid-cols-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['services']['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Service Item')); ?> #<?php echo e($index + 1); ?></p>
                                <div class="mt-3">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                                    <input type="text" wire:model="form.translation_payload.services.items.<?php echo e($index); ?>.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div class="mt-3">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Text')); ?></label>
                                    <textarea rows="5" wire:model="form.translation_payload.services.items.<?php echo e($index); ?>.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div id="tax-compliance-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                    <p class="admin-section-title"><?php echo e(__('Compliance Block')); ?></p>

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                            <input type="text" wire:model="form.translation_payload.compliance.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.compliance.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                        <textarea rows="4" wire:model="form.translation_payload.compliance.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>

                    <div class="mt-6 grid gap-6 xl:grid-cols-2">
                        <div class="space-y-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Corporate')); ?></p>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                                <input type="text" wire:model="form.translation_payload.compliance.corporate.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                                <textarea rows="4" wire:model="form.translation_payload.compliance.corporate.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['compliance']['corporate']['groups'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupIndex => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Group Title')); ?> #<?php echo e($groupIndex + 1); ?></label>
                                    <input type="text" wire:model="form.translation_payload.compliance.corporate.groups.<?php echo e($groupIndex); ?>.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($group['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemIndex => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="mt-3">
                                            <div class="mb-1 flex items-center justify-between gap-3">
                                                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Item')); ?> #<?php echo e($itemIndex + 1); ?></label>
                                                <button type="button" wire:click="removeTranslationListItem('compliance.corporate.groups.<?php echo e($groupIndex); ?>.items', <?php echo e($itemIndex); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                            </div>
                                            <input type="text" wire:model="form.translation_payload.compliance.corporate.groups.<?php echo e($groupIndex); ?>.items.<?php echo e($itemIndex); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <div class="mt-3">
                                        <button type="button" wire:click="addTranslationListItem('compliance.corporate.groups.<?php echo e($groupIndex); ?>.items')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                            <?php echo e(__('Add Item')); ?>

                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="space-y-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Individual')); ?></p>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                                <input type="text" wire:model="form.translation_payload.compliance.individual.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                                <textarea rows="4" wire:model="form.translation_payload.compliance.individual.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['compliance']['individual']['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div>
                                    <div class="mb-1 flex items-center justify-between gap-3">
                                        <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Item')); ?> #<?php echo e($index + 1); ?></label>
                                        <button type="button" wire:click="removeTranslationListItem('compliance.individual.items', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                    </div>
                                    <input type="text" wire:model="form.translation_payload.compliance.individual.items.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="mt-3">
                                <button type="button" wire:click="addTranslationListItem('compliance.individual.items')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                    <?php echo e(__('Add Item')); ?>

                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div id="tax-review-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title"><?php echo e(__('Tax Review')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                            <input type="text" wire:model="form.translation_payload.review.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.review.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                            <textarea rows="4" wire:model="form.translation_payload.review.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['review']['body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Paragraph')); ?> #<?php echo e($index + 1); ?></label>
                                    <button type="button" wire:click="removeTranslationListItem('review.body', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                </div>
                                <textarea rows="4" wire:model="form.translation_payload.review.body.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('review.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add Paragraph')); ?>

                            </button>
                        </div>

                        <div class="mt-5">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Highlights Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.review.highlights_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['review']['highlights'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Highlight')); ?> #<?php echo e($index + 1); ?></label>
                                    <button type="button" wire:click="removeTranslationListItem('review.highlights', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                </div>
                                <input type="text" wire:model="form.translation_payload.review.highlights.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('review.highlights')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add Highlight')); ?>

                            </button>
                        </div>
                    </div>

                    <div id="tax-optimization-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title"><?php echo e(__('Optimization')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                            <input type="text" wire:model="form.translation_payload.optimization.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.optimization.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                            <textarea rows="4" wire:model="form.translation_payload.optimization.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['optimization']['body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Paragraph')); ?> #<?php echo e($index + 1); ?></label>
                                    <button type="button" wire:click="removeTranslationListItem('optimization.body', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                </div>
                                <textarea rows="4" wire:model="form.translation_payload.optimization.body.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('optimization.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add Paragraph')); ?>

                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div id="tax-due-diligence-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title"><?php echo e(__('Due Diligence')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                            <input type="text" wire:model="form.translation_payload.due_diligence.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.due_diligence.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                            <textarea rows="4" wire:model="form.translation_payload.due_diligence.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['due_diligence']['body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Paragraph')); ?> #<?php echo e($index + 1); ?></label>
                                    <button type="button" wire:click="removeTranslationListItem('due_diligence.body', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                </div>
                                <textarea rows="4" wire:model="form.translation_payload.due_diligence.body.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('due_diligence.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add Paragraph')); ?>

                            </button>
                        </div>
                    </div>

                    <div id="tax-transfer-pricing-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title"><?php echo e(__('Transfer Pricing')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                            <input type="text" wire:model="form.translation_payload.transfer_pricing.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.transfer_pricing.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                            <textarea rows="4" wire:model="form.translation_payload.transfer_pricing.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['transfer_pricing']['body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Paragraph')); ?> #<?php echo e($index + 1); ?></label>
                                    <button type="button" wire:click="removeTranslationListItem('transfer_pricing.body', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                </div>
                                <textarea rows="4" wire:model="form.translation_payload.transfer_pricing.body.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('transfer_pricing.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add Paragraph')); ?>

                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div class="admin-panel admin-form-panel p-6">
                        <p class="admin-section-title"><?php echo e(__('Blog Section')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                            <input type="text" wire:model="form.translation_payload.blog_section.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.blog_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            <p class="mt-1 text-xs text-slate-500"><?php echo e(__('Use :category placeholder if you want the current blog category name inserted automatically.')); ?></p>
                        </div>
                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                            <textarea rows="4" wire:model="form.translation_payload.blog_section.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>
                    </div>

                    <div id="tax-meeting-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title"><?php echo e(__('Meeting Section')); ?></p>

                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                            <textarea rows="5" wire:model="form.translation_payload.meeting.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Visit Title')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.visit_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Contact Title')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.contact_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Visit Line 1')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.visit_lines.0" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Visit Line 2')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.visit_lines.1" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Submit Label')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.submit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-[18rem]" />
                        </div>

                        <div class="mt-6 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Direct Phone Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.direct_phone_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Direct Email Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.direct_email_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Form Labels')); ?></p>

                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('First Name')); ?></label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.first_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Last Name')); ?></label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.last_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            </div>

                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Company')); ?></label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.company" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Phone Label')); ?></label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.phone" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            </div>

                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Email Label')); ?></label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.email" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Subject Label')); ?></label>
                                    <input type="text" wire:model="form.translation_payload.meeting.form_labels.subject" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Message Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.message" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>
                    </div>
                </div>
            <?php elseif($isAccountingTemplate): ?>
                <div class="admin-panel admin-form-panel p-6">
                    <p class="admin-section-title"><?php echo e(__('Accounting Navigator')); ?></p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $accountingEditorSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sectionId => $sectionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="#<?php echo e($sectionId); ?>" class="admin-chip"><?php echo e($sectionLabel); ?></a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <p class="mt-4 text-sm text-slate-600">
                        <?php echo e(__('Računovodstvo trenutno koristi minimalni landing template: hero, kontakt forma i blog sekcija. Dodatne sadržajne sekcije možemo nadograditi kroz novi template update.')); ?>

                    </p>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <div id="accounting-intro-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title"><?php echo e(__('Overview Block')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                            <input type="text" wire:model="form.translation_payload.intro_section.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.intro_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['intro_section']['body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Paragraph')); ?> #<?php echo e($index + 1); ?></label>
                                    <button type="button" wire:click="removeTranslationListItem('intro_section.body', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                </div>
                                <textarea rows="4" wire:model="form.translation_payload.intro_section.body.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('intro_section.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add Paragraph')); ?>

                            </button>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['intro_section']['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('List Item')); ?> #<?php echo e($index + 1); ?></label>
                                    <button type="button" wire:click="removeTranslationListItem('intro_section.items', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                </div>
                                <input type="text" wire:model="form.translation_payload.intro_section.items.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('intro_section.items')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add List Item')); ?>

                            </button>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Video Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.intro_section.video_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('YouTube URL')); ?></label>
                            <input type="text" wire:model="form.translation_payload.intro_section.video_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div id="accounting-meeting-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title"><?php echo e(__('Meeting Block')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                            <textarea rows="4" wire:model="form.translation_payload.meeting.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Visit Title')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.visit_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Contact Title')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.contact_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['meeting']['visit_lines'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Visit Line')); ?> #<?php echo e($index + 1); ?></label>
                                    <button type="button" wire:click="removeTranslationListItem('meeting.visit_lines', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                </div>
                                <input type="text" wire:model="form.translation_payload.meeting.visit_lines.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('meeting.visit_lines')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add Visit Line')); ?>

                            </button>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Phone Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.direct_phone_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Email Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.direct_email_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Submit Label')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.submit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('First Name Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.first_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Last Name Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.last_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Company Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.company" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Phone Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.phone" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Email Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.email" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Subject Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.meeting.form_labels.subject" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Message Label')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.form_labels.message" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div id="accounting-blog-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
                        <p class="admin-section-title"><?php echo e(__('Blog Block')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                            <input type="text" wire:model="form.translation_payload.blog_section.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                            <input type="text" wire:model="form.translation_payload.blog_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                            <textarea rows="4" wire:model="form.translation_payload.blog_section.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Empty State')); ?></label>
                            <textarea rows="3" wire:model="form.translation_payload.blog_section.empty" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>
                    </div>
                </div>
            <?php elseif($isEuFundsTemplate): ?>
                <?php echo $__env->make('livewire.admin.content.service.partials.eu-funds-editor', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php else: ?>
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title"><?php echo e(__('Audience & FFI')); ?></p>

                <div class="mt-4">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Audience Headline')); ?></label>
                    <textarea rows="3" wire:model="form.translation_payload.audience.headline" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>

                <div class="mt-6 space-y-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['audience']['cards'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="grid gap-3 md:grid-cols-[1fr_220px]">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Card Title')); ?> #<?php echo e($index + 1); ?></label>
                                    <input type="text" wire:model="form.translation_payload.audience.cards.<?php echo e($index); ?>.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Icon')); ?></label>
                                    <select wire:model="form.translation_payload.audience.cards.<?php echo e($index); ?>.icon" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $audienceIconOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iconKey => $iconLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($iconKey); ?>"><?php echo e($iconLabel); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Card Text')); ?></label>
                                <textarea rows="5" wire:model="form.translation_payload.audience.cards.<?php echo e($index); ?>.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="mt-6 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('FFI Title')); ?></label>
                        <input type="text" wire:model="form.translation_payload.ffi.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('FFI Logo Alt')); ?></label>
                        <input type="text" wire:model="form.translation_payload.ffi.logo_alt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('FFI Body')); ?></label>
                    <textarea rows="4" wire:model="form.translation_payload.ffi.body.0" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>
            </div>

            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title"><?php echo e(__('FAQ Intro Block')); ?></p>

                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                        <input type="text" wire:model="form.translation_payload.what_we_do.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                        <input type="text" wire:model="form.translation_payload.what_we_do.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                    <textarea rows="5" wire:model="form.translation_payload.what_we_do.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>
            </div>

            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title"><?php echo e(__('Advisory Approach')); ?></p>

                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                        <input type="text" wire:model="form.translation_payload.advisory.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Box Title')); ?></label>
                        <input type="text" wire:model="form.translation_payload.advisory.box_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                    <input type="text" wire:model="form.translation_payload.advisory.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                    <textarea rows="5" wire:model="form.translation_payload.advisory.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>

                <div class="mt-6 space-y-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['advisory']['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Advisory Item')); ?> #<?php echo e($index + 1); ?></p>
                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Lead')); ?></label>
                                <input type="text" wire:model="form.translation_payload.advisory.items.<?php echo e($index); ?>.lead" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Body')); ?></label>
                                <textarea rows="4" wire:model="form.translation_payload.advisory.items.<?php echo e($index); ?>.body" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title"><?php echo e(__('Capability Sections')); ?></p>

                <div class="mt-4 space-y-5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['capabilities'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sectionIndex => $capability): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <div class="grid gap-3 md:grid-cols-[1fr_220px]">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Section Title')); ?> #<?php echo e($sectionIndex + 1); ?></label>
                                    <input type="text" wire:model="form.translation_payload.capabilities.<?php echo e($sectionIndex); ?>.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Icon')); ?></label>
                                    <select wire:model="form.translation_payload.capabilities.<?php echo e($sectionIndex); ?>.icon" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $capabilityIconOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iconKey => $iconLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($iconKey); ?>"><?php echo e($iconLabel); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                                <textarea rows="4" wire:model="form.translation_payload.capabilities.<?php echo e($sectionIndex); ?>.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>

                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Help Text')); ?></label>
                                <textarea rows="4" wire:model="form.translation_payload.capabilities.<?php echo e($sectionIndex); ?>.help" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                            </div>

                            <div class="mt-4 grid gap-4 lg:grid-cols-3">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($capability['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemIndex => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Item Title')); ?> #<?php echo e($itemIndex + 1); ?></label>
                                        <input type="text" wire:model="form.translation_payload.capabilities.<?php echo e($sectionIndex); ?>.items.<?php echo e($itemIndex); ?>.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                        <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Item Text')); ?></label>
                                        <textarea rows="5" wire:model="form.translation_payload.capabilities.<?php echo e($sectionIndex); ?>.items.<?php echo e($itemIndex); ?>.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title"><?php echo e(__('Capability CTA')); ?></p>

                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                        <input type="text" wire:model="form.translation_payload.capability_cta.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Button Label')); ?></label>
                        <input type="text" wire:model="form.translation_payload.capability_cta.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-2">
                <div class="admin-panel admin-form-panel p-6">
                    <p class="admin-section-title"><?php echo e(__('Team Section')); ?></p>

                    <div class="mt-4">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                        <input type="text" wire:model="form.translation_payload.team_section.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                        <input type="text" wire:model="form.translation_payload.team_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                        <textarea rows="4" wire:model="form.translation_payload.team_section.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>
                </div>

                <div class="admin-panel admin-form-panel p-6">
                    <p class="admin-section-title"><?php echo e(__('Blog Section')); ?></p>

                    <div class="mt-4">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                        <input type="text" wire:model="form.translation_payload.blog_section.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                        <input type="text" wire:model="form.translation_payload.blog_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <p class="mt-1 text-xs text-slate-500"><?php echo e(__('Use :category placeholder if you want the current blog category name inserted automatically.')); ?></p>
                    </div>
                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                        <textarea rows="4" wire:model="form.translation_payload.blog_section.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>
                </div>
            </div>

            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title"><?php echo e(__('Meeting Section')); ?></p>

                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                        <input type="text" wire:model="form.translation_payload.meeting.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                        <input type="text" wire:model="form.translation_payload.meeting.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
                    <textarea rows="5" wire:model="form.translation_payload.meeting.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>

                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Visit Title')); ?></label>
                        <input type="text" wire:model="form.translation_payload.meeting.visit_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Contact Title')); ?></label>
                        <input type="text" wire:model="form.translation_payload.meeting.contact_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>

                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Visit Line 1')); ?></label>
                        <input type="text" wire:model="form.translation_payload.meeting.visit_lines.0" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Visit Line 2')); ?></label>
                        <input type="text" wire:model="form.translation_payload.meeting.visit_lines.1" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Submit Label')); ?></label>
                    <input type="text" wire:model="form.translation_payload.meeting.submit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-[18rem]" />
                </div>

                <div class="mt-6 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Direct Phone Label')); ?></label>
                        <input type="text" wire:model="form.translation_payload.meeting.direct_phone_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Direct Email Label')); ?></label>
                        <input type="text" wire:model="form.translation_payload.meeting.direct_email_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>

                <div class="mt-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Form Labels')); ?></p>

                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('First Name')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.form_labels.first_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Last Name')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.form_labels.last_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Company')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.form_labels.company" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Phone Label')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.form_labels.phone" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Email Label')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.form_labels.email" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Subject Label')); ?></label>
                            <input type="text" wire:model="form.translation_payload.meeting.form_labels.subject" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Message Label')); ?></label>
                        <input type="text" wire:model="form.translation_payload.meeting.form_labels.message" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'sources' && $templateSupportsSources): ?>
            <div class="grid gap-6 <?php echo e($templateSupportsFaqSource ? 'xl:grid-cols-2' : ''); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($templateSupportsBlogSource): ?>
                    <div class="admin-panel admin-form-panel p-6">
                        <p class="admin-section-title"><?php echo e(__('Blog Feed')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Mode')); ?></label>
                            <select wire:model.live="form.page_payload.blog_source.mode" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                <option value="auto_category">
                                    <?php echo e($blogAutoCategoryLabel); ?>

                                </option>
                                <option value="category"><?php echo e(__('Specific blog category')); ?></option>
                                <option value="manual"><?php echo e(__('Manual post selection')); ?></option>
                            </select>
                        </div>

                        <div class="mt-3">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Limit')); ?></label>
                            <input type="number" min="1" max="24" wire:model="form.page_payload.blog_source.limit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-[12rem]" />
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($pagePayload['blog_source']['mode'] ?? 'auto_category') === 'category'): ?>
                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Blog Category')); ?></label>
                                <select wire:model="form.page_payload.blog_source.category_id" data-tom-select class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                    <option value=""><?php echo e(__('Select category')); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->blogCategoryOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($row['id']); ?>"><?php echo e($row['label']); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($pagePayload['blog_source']['mode'] ?? 'auto_category') === 'manual'): ?>
                            <div class="mt-4 grid gap-3 md:grid-cols-[1fr_auto] md:items-end">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Available Posts')); ?></label>
                                    <select wire:model="blogPickerId" data-tom-select class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                        <option value=""><?php echo e(__('Select post...')); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->blogPostOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($row['id']); ?>"><?php echo e($row['label']); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                </div>
                                <button type="button" wire:click="addManualItem('blog_posts', <?php echo e((int) ($blogPickerId ?? 0)); ?>)" class="h-10 rounded-xl bg-cyan-700 px-4 text-sm font-semibold text-white hover:bg-cyan-800">
                                    <?php echo e(__('Add')); ?>

                                </button>
                            </div>

                            <div class="mt-4 space-y-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->selectedBlogPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                                        <div class="text-sm text-slate-800"><?php echo e($row['label']); ?></div>
                                        <div class="inline-flex items-center gap-1">
                                            <button type="button" wire:click="moveManualItemUp('blog_posts', <?php echo e($row['index']); ?>)" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('Up')); ?></button>
                                            <button type="button" wire:click="moveManualItemDown('blog_posts', <?php echo e($row['index']); ?>)" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('Down')); ?></button>
                                            <button type="button" wire:click="removeManualItem('blog_posts', <?php echo e($row['id']); ?>)" class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"><?php echo e(__('Remove')); ?></button>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500"><?php echo e(__('No blog posts selected.')); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($templateSupportsFaqSource): ?>
                    <div class="admin-panel admin-form-panel p-6">
                        <p class="admin-section-title"><?php echo e(__('FAQ Feed')); ?></p>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Mode')); ?></label>
                            <select wire:model.live="form.page_payload.faq_source.mode" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                <option value="auto_group"><?php echo e(__('Auto (family-business FAQ group)')); ?></option>
                                <option value="group"><?php echo e(__('Specific FAQ group')); ?></option>
                                <option value="manual"><?php echo e(__('Manual FAQ selection')); ?></option>
                            </select>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($pagePayload['faq_source']['mode'] ?? 'auto_group') === 'group'): ?>
                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('FAQ Group')); ?></label>
                                <select wire:model="form.page_payload.faq_source.group_code" data-tom-select class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                    <option value=""><?php echo e(__('Select group')); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->faqGroupOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($row['id']); ?>"><?php echo e($row['label']); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($pagePayload['faq_source']['mode'] ?? 'auto_group') === 'manual'): ?>
                            <div class="mt-4 grid gap-3 md:grid-cols-[1fr_auto] md:items-end">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Available FAQs')); ?></label>
                                    <select wire:model="faqPickerId" data-tom-select class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                        <option value=""><?php echo e(__('Select FAQ...')); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->faqOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($row['id']); ?>"><?php echo e($row['label']); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                </div>
                                <button type="button" wire:click="addManualItem('faqs', <?php echo e((int) ($faqPickerId ?? 0)); ?>)" class="h-10 rounded-xl bg-cyan-700 px-4 text-sm font-semibold text-white hover:bg-cyan-800">
                                    <?php echo e(__('Add')); ?>

                                </button>
                            </div>

                            <div class="mt-4 space-y-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->selectedFaqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                                        <div class="text-sm text-slate-800"><?php echo e($row['label']); ?></div>
                                        <div class="inline-flex items-center gap-1">
                                            <button type="button" wire:click="moveManualItemUp('faqs', <?php echo e($row['index']); ?>)" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('Up')); ?></button>
                                            <button type="button" wire:click="moveManualItemDown('faqs', <?php echo e($row['index']); ?>)" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('Down')); ?></button>
                                            <button type="button" wire:click="removeManualItem('faqs', <?php echo e($row['id']); ?>)" class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"><?php echo e(__('Remove')); ?></button>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500"><?php echo e(__('No FAQs selected.')); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($templateSupportsTeamSource || $templateSupportsBrochure): ?>
                <div class="grid gap-6 xl:grid-cols-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($templateSupportsTeamSource): ?>
                        <div class="admin-panel admin-form-panel p-6">
                            <p class="admin-section-title"><?php echo e(__('Team Feed')); ?></p>

                            <div class="mt-4">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Mode')); ?></label>
                                <select wire:model.live="form.page_payload.team_source.mode" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                    <option value="auto"><?php echo e(__('Auto (existing family-business logic)')); ?></option>
                                    <option value="manual"><?php echo e(__('Manual team selection')); ?></option>
                                </select>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($pagePayload['team_source']['mode'] ?? 'auto') === 'manual'): ?>
                                <div class="mt-4 grid gap-3 md:grid-cols-[1fr_auto] md:items-end">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Available Team Members')); ?></label>
                                        <select wire:model="teamPickerId" data-tom-select class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                            <option value=""><?php echo e(__('Select team member...')); ?></option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->teamOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($row['id']); ?>"><?php echo e($row['label']); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </select>
                                    </div>
                                    <button type="button" wire:click="addManualItem('team_members', <?php echo e((int) ($teamPickerId ?? 0)); ?>)" class="h-10 rounded-xl bg-cyan-700 px-4 text-sm font-semibold text-white hover:bg-cyan-800">
                                        <?php echo e(__('Add')); ?>

                                    </button>
                                </div>

                                <div class="mt-4 space-y-2">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->selectedTeamMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                                            <div class="text-sm text-slate-800"><?php echo e($row['label']); ?></div>
                                            <div class="inline-flex items-center gap-1">
                                                <button type="button" wire:click="moveManualItemUp('team_members', <?php echo e($row['index']); ?>)" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('Up')); ?></button>
                                                <button type="button" wire:click="moveManualItemDown('team_members', <?php echo e($row['index']); ?>)" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('Down')); ?></button>
                                                <button type="button" wire:click="removeManualItem('team_members', <?php echo e($row['id']); ?>)" class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"><?php echo e(__('Remove')); ?></button>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500"><?php echo e(__('No team members selected.')); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($templateSupportsBrochure): ?>
                        <div class="admin-panel admin-form-panel p-6">
                            <p class="admin-section-title"><?php echo e(__('Assets & Links')); ?></p>

                            <div class="mt-4">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Brochure Button Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.brochure_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>

                            <div class="mt-3">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Brochure URL Override')); ?></label>
                                <input type="text" wire:model="form.page_payload.brochure_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="<?php echo e(__('Leave empty to keep the current brochure asset.')); ?>" />
                                <p class="mt-1 text-xs text-slate-500"><?php echo e(__('This can be a relative public path or a full URL.')); ?></p>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'seo'): ?>
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title"><?php echo e(__('SEO')); ?></p>

                <div class="mt-4">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Meta Title')); ?></label>
                    <input type="text" wire:model="form.meta_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.meta_title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Meta Description')); ?></label>
                    <textarea rows="4" wire:model="form.meta_description" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'media'): ?>
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.media.manager', ['modelClass' => \App\Models\Content\Service\ServicePage::class,'modelId' => $servicePageId,'locale' => $form['locale']]);

$key = 'service-page-media-manager-'.($servicePageId ?? 'new').'-'.$form['locale'];

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1080228232-0', 'service-page-media-manager-'.($servicePageId ?? 'new').'-'.$form['locale']);

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="admin-form-actions flex items-center gap-2 pt-2">
            <button type="submit" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                <?php echo e($isEdit ? __('Update Service Page') : __('Create Service Page')); ?>

            </button>
            <button type="button" wire:click="backToList" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                <?php echo e(__('Cancel')); ?>

            </button>
        </div>
    </form>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/content/service/form.blade.php ENDPATH**/ ?>