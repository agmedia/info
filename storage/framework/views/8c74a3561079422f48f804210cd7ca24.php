<?php
    $advisoryEditorSections = [
        'advisory-overview-admin' => 'Overview',
        'advisory-services-admin' => 'Services',
        'advisory-pandea-admin' => 'Pandea',
        'advisory-funding-admin' => 'Pribavljanje financiranja',
        'advisory-bank-loans-admin' => 'Bankovni krediti',
        'advisory-zopu-admin' => 'Zakon o poticanju ulaganja',
        'advisory-ma-admin' => 'M&A',
        'advisory-due-diligence-admin' => 'Due Diligence',
        'advisory-valuations-admin' => 'Procjene vrijednosti',
        'advisory-tax-admin' => 'Porezno savjetovanje',
        'advisory-approach-admin' => 'Approach',
        'advisory-meeting-admin' => 'Meeting',
    ];

    $advisoryDetailSections = [
        'bank_loans' => [
            'id' => 'advisory-bank-loans-admin',
            'label' => 'Bankovni krediti',
        ],
        'zopu' => [
            'id' => 'advisory-zopu-admin',
            'label' => 'Zakon o poticanju ulaganja',
        ],
        'ma' => [
            'id' => 'advisory-ma-admin',
            'label' => 'Prodaja i kupnja poduzeca (M&A)',
        ],
        'due_diligence' => [
            'id' => 'advisory-due-diligence-admin',
            'label' => 'Dubinska snimanja (Due Diligence)',
        ],
        'valuations' => [
            'id' => 'advisory-valuations-admin',
            'label' => 'Procjena vrijednosti drustva',
        ],
        'tax' => [
            'id' => 'advisory-tax-admin',
            'label' => 'Porezno savjetovanje',
        ],
    ];
?>

<div class="admin-panel admin-form-panel p-6">
    <p class="admin-section-title"><?php echo e(__('Advisory Navigator')); ?></p>
    <div class="mt-4 flex flex-wrap gap-2">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $advisoryEditorSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sectionId => $sectionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="#<?php echo e($sectionId); ?>" class="admin-chip"><?php echo e($sectionLabel); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <p class="mt-4 text-sm text-slate-600">
        <?php echo e(__('Savjetovanje je krovna usluga. Podstranice su prikazane ispod i uređuju isti zapis, ali svaki blok odgovara jednoj front ruti.')); ?>

    </p>
</div>

<div class="grid gap-6 xl:grid-cols-2">
    <div id="advisory-overview-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <p class="admin-section-title"><?php echo e(__('Overview')); ?></p>

        <div class="mt-4 grid gap-3 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                <input type="text" wire:model="form.translation_payload.overview.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                <input type="text" wire:model="form.translation_payload.overview.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['overview']['body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="mt-3">
                <div class="mb-1 flex items-center justify-between gap-3">
                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Paragraph')); ?> #<?php echo e($index + 1); ?></label>
                    <button type="button" wire:click="removeTranslationListItem('overview.body', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                </div>
                <textarea rows="5" wire:model="form.translation_payload.overview.body.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="mt-3">
            <button type="button" wire:click="addTranslationListItem('overview.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                <?php echo e(__('Add Paragraph')); ?>

            </button>
        </div>
    </div>

    <div id="advisory-services-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <p class="admin-section-title"><?php echo e(__('Services Intro')); ?></p>

        <div class="mt-4 grid gap-3 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                <input type="text" wire:model="form.translation_payload.services_intro.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                <input type="text" wire:model="form.translation_payload.services_intro.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
        </div>
        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
            <textarea rows="5" wire:model="form.translation_payload.services_intro.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>
    </div>
</div>

<div class="admin-panel admin-form-panel p-6">
    <div class="flex items-center justify-between gap-3">
        <p class="admin-section-title"><?php echo e(__('Service Cards')); ?></p>
        <button type="button" wire:click="addTranslationListItem('service_cards', 'service_card')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
            <?php echo e(__('Add Card')); ?>

        </button>
    </div>
    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['service_cards'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Card')); ?> #<?php echo e($index + 1); ?></p>
                    <button type="button" wire:click="removeTranslationListItem('service_cards', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                </div>
                <div class="mt-3 grid gap-3 md:grid-cols-[1fr_1fr]">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                        <input type="text" wire:model="form.translation_payload.service_cards.<?php echo e($index); ?>.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('URL')); ?></label>
                        <input type="text" wire:model="form.translation_payload.service_cards.<?php echo e($index); ?>.url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>
                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Text')); ?></label>
                    <textarea rows="4" wire:model="form.translation_payload.service_cards.<?php echo e($index); ?>.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>

<div id="advisory-pandea-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
    <p class="admin-section-title"><?php echo e(__('Pandea')); ?></p>
    <div class="mt-4 grid gap-3 md:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
            <input type="text" wire:model="form.translation_payload.pandea.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Logo Alt')); ?></label>
            <input type="text" wire:model="form.translation_payload.pandea.logo_alt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
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
</div>

<div id="advisory-funding-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
    <p class="admin-section-title"><?php echo e(__('Pribavljanje financiranja')); ?></p>

    <div class="mt-4 grid gap-3 md:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
            <input type="text" wire:model="form.translation_payload.funding.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Overview Title')); ?></label>
            <input type="text" wire:model="form.translation_payload.funding.overview_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>

    <div class="mt-3">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
        <textarea rows="4" wire:model="form.translation_payload.funding.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['funding']['overview_body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="mt-3">
            <div class="mb-1 flex items-center justify-between gap-3">
                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Overview Paragraph')); ?> #<?php echo e($index + 1); ?></label>
                <button type="button" wire:click="removeTranslationListItem('funding.overview_body', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
            </div>
            <textarea rows="4" wire:model="form.translation_payload.funding.overview_body.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="mt-3">
        <button type="button" wire:click="addTranslationListItem('funding.overview_body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
            <?php echo e(__('Add Overview Paragraph')); ?>

        </button>
    </div>

    <div class="mt-6 flex items-center justify-between gap-3">
        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Funding Cards')); ?></p>
        <button type="button" wire:click="addTranslationListItem('funding.cards', 'service_card')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
            <?php echo e(__('Add Card')); ?>

        </button>
    </div>
    <div class="mt-4 grid gap-4 lg:grid-cols-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['funding']['cards'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Card')); ?> #<?php echo e($index + 1); ?></p>
                    <button type="button" wire:click="removeTranslationListItem('funding.cards', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                </div>
                <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                <input type="text" wire:model="form.translation_payload.funding.cards.<?php echo e($index); ?>.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Text')); ?></label>
                <textarea rows="4" wire:model="form.translation_payload.funding.cards.<?php echo e($index); ?>.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('URL')); ?></label>
                <input type="text" wire:model="form.translation_payload.funding.cards.<?php echo e($index); ?>.url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="mt-6">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Services Title')); ?></label>
        <input type="text" wire:model="form.translation_payload.funding.services_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-md" />
    </div>
    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['funding']['services'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Funding Service')); ?> #<?php echo e($index + 1); ?></p>
                    <button type="button" wire:click="removeTranslationListItem('funding.services', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                </div>
                <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                <input type="text" wire:model="form.translation_payload.funding.services.<?php echo e($index); ?>.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                <label class="mb-1 mt-3 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Text')); ?></label>
                <textarea rows="4" wire:model="form.translation_payload.funding.services.<?php echo e($index); ?>.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="mt-3">
        <button type="button" wire:click="addTranslationListItem('funding.services', 'title_text')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
            <?php echo e(__('Add Service')); ?>

        </button>
    </div>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $advisoryDetailSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detailKey => $detailConfig): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $detail = (array) ($translationPayload[$detailKey] ?? []); ?>
    <div id="<?php echo e($detailConfig['id']); ?>" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="admin-section-title"><?php echo e($detailConfig['label']); ?></p>
            <span class="admin-chip"><?php echo e($detailKey); ?></span>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-3">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                <input type="text" wire:model="form.translation_payload.<?php echo e($detailKey); ?>.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                <input type="text" wire:model="form.translation_payload.<?php echo e($detailKey); ?>.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Overview Title')); ?></label>
                <input type="text" wire:model="form.translation_payload.<?php echo e($detailKey); ?>.overview_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detailKey === 'ma'): ?>
            <div class="mt-4">
                <button
                    type="button"
                    wire:click="$toggle('form.translation_payload.ma.show_pandea')"
                    class="admin-switch"
                    data-state="<?php echo e(data_get($translationPayload, 'ma.show_pandea') ? 'on' : 'off'); ?>"
                    role="switch"
                    aria-checked="<?php echo e(data_get($translationPayload, 'ma.show_pandea') ? 'true' : 'false'); ?>"
                    aria-label="<?php echo e(__('Toggle Pandea block on M&A page')); ?>"
                >
                    <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                    <span class="admin-switch-label"><?php echo e(data_get($translationPayload, 'ma.show_pandea') ? __('Pandea visible') : __('Pandea hidden')); ?></span>
                </button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="mt-6">
            <div class="mb-3 flex items-center justify-between gap-3">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Overview Paragraphs')); ?></p>
                <button type="button" wire:click="addTranslationListItem('<?php echo e($detailKey); ?>.overview_body')" class="rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                    <?php echo e(__('Add Paragraph')); ?>

                </button>
            </div>
            <div class="space-y-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($detail['overview_body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3">
                            <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Paragraph')); ?> #<?php echo e($index + 1); ?></label>
                            <button type="button" wire:click="removeTranslationListItem('<?php echo e($detailKey); ?>.overview_body', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                        </div>
                        <textarea rows="4" wire:model="form.translation_payload.<?php echo e($detailKey); ?>.overview_body.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="mt-6">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Services Title')); ?></label>
            <input type="text" wire:model="form.translation_payload.<?php echo e($detailKey); ?>.services_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-md" />
        </div>

        <div class="mt-4">
            <div class="mb-3 flex items-center justify-between gap-3">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Services Paragraphs')); ?></p>
                <button type="button" wire:click="addTranslationListItem('<?php echo e($detailKey); ?>.services_body')" class="rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                    <?php echo e(__('Add Paragraph')); ?>

                </button>
            </div>
            <div class="space-y-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($detail['services_body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3">
                            <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Paragraph')); ?> #<?php echo e($index + 1); ?></label>
                            <button type="button" wire:click="removeTranslationListItem('<?php echo e($detailKey); ?>.services_body', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                        </div>
                        <textarea rows="4" wire:model="form.translation_payload.<?php echo e($detailKey); ?>.services_body.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="mt-6">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Help Title')); ?></label>
            <input type="text" wire:model="form.translation_payload.<?php echo e($detailKey); ?>.help_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-md" />
        </div>

        <div class="mt-4">
            <div class="mb-3 flex items-center justify-between gap-3">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Help Items')); ?></p>
                <button type="button" wire:click="addTranslationListItem('<?php echo e($detailKey); ?>.help_items')" class="rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                    <?php echo e(__('Add Item')); ?>

                </button>
            </div>
            <div class="grid gap-3 lg:grid-cols-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($detail['help_items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3">
                            <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Item')); ?> #<?php echo e($index + 1); ?></label>
                            <button type="button" wire:click="removeTranslationListItem('<?php echo e($detailKey); ?>.help_items', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                        </div>
                        <input type="text" wire:model="form.translation_payload.<?php echo e($detailKey); ?>.help_items.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="mt-6">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Approach Title')); ?></label>
            <input type="text" wire:model="form.translation_payload.<?php echo e($detailKey); ?>.approach_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-md" />
        </div>

        <div class="mt-4">
            <div class="mb-3 flex items-center justify-between gap-3">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Approach Paragraphs')); ?></p>
                <button type="button" wire:click="addTranslationListItem('<?php echo e($detailKey); ?>.approach_body')" class="rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                    <?php echo e(__('Add Paragraph')); ?>

                </button>
            </div>
            <div class="space-y-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($detail['approach_body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3">
                            <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Paragraph')); ?> #<?php echo e($index + 1); ?></label>
                            <button type="button" wire:click="removeTranslationListItem('<?php echo e($detailKey); ?>.approach_body', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                        </div>
                        <textarea rows="4" wire:model="form.translation_payload.<?php echo e($detailKey); ?>.approach_body.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<div class="grid gap-6 xl:grid-cols-2">
    <div id="advisory-approach-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <p class="admin-section-title"><?php echo e(__('Approach')); ?></p>
        <div class="mt-4 grid gap-3 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
                <input type="text" wire:model="form.translation_payload.approach.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                <input type="text" wire:model="form.translation_payload.approach.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['approach']['body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="mt-3">
                <div class="mb-1 flex items-center justify-between gap-3">
                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Paragraph')); ?> #<?php echo e($index + 1); ?></label>
                    <button type="button" wire:click="removeTranslationListItem('approach.body', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                </div>
                <textarea rows="4" wire:model="form.translation_payload.approach.body.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="mt-3">
            <button type="button" wire:click="addTranslationListItem('approach.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                <?php echo e(__('Add Paragraph')); ?>

            </button>
        </div>
    </div>

    <div id="advisory-meeting-admin" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <p class="admin-section-title"><?php echo e(__('Meeting / Blog')); ?></p>
        <div class="mt-4 grid gap-3 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Meeting Kicker')); ?></label>
                <input type="text" wire:model="form.translation_payload.meeting.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Meeting Title')); ?></label>
                <input type="text" wire:model="form.translation_payload.meeting.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
        </div>
        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Meeting Intro')); ?></label>
            <textarea rows="4" wire:model="form.translation_payload.meeting.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>
        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Contact Title')); ?></label>
            <input type="text" wire:model="form.translation_payload.meeting.contact_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-md" />
        </div>

        <div class="mt-6 grid gap-3 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Blog Kicker')); ?></label>
                <input type="text" wire:model="form.translation_payload.blog_section.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Blog Title')); ?></label>
                <input type="text" wire:model="form.translation_payload.blog_section.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
        </div>
        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Blog Intro')); ?></label>
            <textarea rows="4" wire:model="form.translation_payload.blog_section.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>
    </div>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/content/service/partials/advisory-editor.blade.php ENDPATH**/ ?>