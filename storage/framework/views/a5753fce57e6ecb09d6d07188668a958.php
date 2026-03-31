<div class="admin-panel admin-form-panel p-6">
    <p class="admin-section-title"><?php echo e(__('EU Funds Navigator')); ?></p>
    <div class="mt-4 flex flex-wrap gap-2">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $euFundsEditorSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sectionId => $sectionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="#<?php echo e($sectionId); ?>" class="admin-chip"><?php echo e($sectionLabel); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <p class="mt-4 text-sm text-slate-600">
        <?php echo e(__('EU fondovi koristi zaseban landing layout. Ovdje uređujete tekstove sekcija, kartice programa, zakonske blokove i završne kontakt/blog elemente koji se prikazuju na frontend stranici.')); ?>

    </p>
</div>

<div id="eu-funds-about" class="admin-panel admin-form-panel p-6 scroll-mt-24">
    <p class="admin-section-title"><?php echo e(__('About Block')); ?></p>

    <div class="mt-4 grid gap-3 md:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
            <input type="text" wire:model="form.translation_payload.about.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
            <input type="text" wire:model="form.translation_payload.about.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['about']['body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="mt-3">
            <div class="mb-1 flex items-center justify-between gap-3">
                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Paragraph')); ?> #<?php echo e($index + 1); ?></label>
                <button type="button" wire:click="removeTranslationListItem('about.body', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
            </div>
            <textarea rows="5" wire:model="form.translation_payload.about.body.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="mt-3">
        <button type="button" wire:click="addTranslationListItem('about.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
            <?php echo e(__('Add Paragraph')); ?>

        </button>
    </div>

    <div class="mt-6">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Supporting Box Title')); ?></label>
        <input type="text" wire:model="form.translation_payload.about.box_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['about']['box_items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="mt-3">
            <div class="mb-1 flex items-center justify-between gap-3">
                <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Supporting Box Item')); ?> #<?php echo e($index + 1); ?></label>
                <button type="button" wire:click="removeTranslationListItem('about.box_items', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
            </div>
            <input type="text" wire:model="form.translation_payload.about.box_items.<?php echo e($index); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="mt-3">
        <button type="button" wire:click="addTranslationListItem('about.box_items')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
            <?php echo e(__('Add Supporting Item')); ?>

        </button>
    </div>
</div>

<div class="grid gap-6 xl:grid-cols-2">
    <div id="eu-funds-overview" class="admin-panel admin-form-panel p-6 scroll-mt-24">
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

    <div id="eu-funds-chart" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <p class="admin-section-title"><?php echo e(__('Funding Chart')); ?></p>

        <div class="mt-4">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
            <input type="text" wire:model="form.translation_payload.chart.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
            <input type="text" wire:model="form.translation_payload.chart.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
            <textarea rows="4" wire:model="form.translation_payload.chart.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['chart']['stats'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Stat')); ?> #<?php echo e($index + 1); ?></p>
                    <button type="button" wire:click="removeTranslationListItem('chart.stats', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                </div>

                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Label')); ?></label>
                        <input type="text" wire:model="form.translation_payload.chart.stats.<?php echo e($index); ?>.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Value')); ?></label>
                        <input type="text" wire:model="form.translation_payload.chart.stats.<?php echo e($index); ?>.value" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>

                <div class="mt-3 grid gap-3 md:grid-cols-[160px_1fr]">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Share (%)')); ?></label>
                        <input type="number" min="0" max="100" wire:model="form.translation_payload.chart.stats.<?php echo e($index); ?>.share" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Description')); ?></label>
                        <input type="text" wire:model="form.translation_payload.chart.stats.<?php echo e($index); ?>.description" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="mt-3">
            <button type="button" wire:click="addTranslationListItem('chart.stats', 'eu_chart_stat')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                <?php echo e(__('Add Stat')); ?>

            </button>
        </div>

        <div class="mt-4">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Footnote')); ?></label>
            <textarea rows="3" wire:model="form.translation_payload.chart.footnote" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>
    </div>
</div>

<div class="grid gap-6 xl:grid-cols-2">
    <div id="eu-funds-process" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <p class="admin-section-title"><?php echo e(__('Process Section')); ?></p>

        <div class="mt-4">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
            <input type="text" wire:model="form.translation_payload.process.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
            <input type="text" wire:model="form.translation_payload.process.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
            <textarea rows="4" wire:model="form.translation_payload.process.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['process']['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Process Item')); ?> #<?php echo e($index + 1); ?></p>
                    <button type="button" wire:click="removeTranslationListItem('process.items', <?php echo e($index); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                    <input type="text" wire:model="form.translation_payload.process.items.<?php echo e($index); ?>.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Text')); ?></label>
                    <textarea rows="4" wire:model="form.translation_payload.process.items.<?php echo e($index); ?>.text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="mt-3">
            <button type="button" wire:click="addTranslationListItem('process.items', 'eu_process_item')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                <?php echo e(__('Add Process Item')); ?>

            </button>
        </div>
    </div>

    <div id="eu-funds-calls" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <p class="admin-section-title"><?php echo e(__('Calls Section')); ?></p>

        <div class="mt-4">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
            <input type="text" wire:model="form.translation_payload.calls.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
            <input type="text" wire:model="form.translation_payload.calls.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
            <textarea rows="4" wire:model="form.translation_payload.calls.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>

        <p class="mt-4 text-sm text-slate-600">
            <?php echo e(__('Kartice poziva na frontend stranici popunjavaju se automatski iz Call sadržaja kada je dostupan. Ovdje uređujete naslov sekcije i fallback download CTA.')); ?>

        </p>

        <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Download CTA')); ?></p>

            <div class="mt-3 grid gap-3 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Button Label')); ?></label>
                    <input type="text" wire:model="form.translation_payload.calls.download_link.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Link Type')); ?></label>
                    <select wire:model="form.translation_payload.calls.download_link.type" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        <option value="none"><?php echo e(__('None')); ?></option>
                        <option value="external"><?php echo e(__('External / Internal URL')); ?></option>
                        <option value="blog"><?php echo e(__('Blog Post')); ?></option>
                        <option value="call"><?php echo e(__('Call Post')); ?></option>
                        <option value="pdf"><?php echo e(__('PDF Asset')); ?></option>
                    </select>
                </div>
            </div>

                <div class="mt-3 grid gap-3 md:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('URL')); ?></label>
                        <input type="text" wire:model="form.translation_payload.calls.download_link.url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Slug')); ?></label>
                        <input type="text" wire:model="form.translation_payload.calls.download_link.slug" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <?php echo $__env->make('livewire.admin.content.service.partials.pdf-asset-upload-field', [
                        'currentPath' => (string) ($translationPayload['calls']['download_link']['path'] ?? ''),
                        'uploadModel' => 'assetUploads.calls_download_link_path',
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </div>
</div>

<div id="eu-funds-resources" class="admin-panel admin-form-panel p-6 scroll-mt-24">
    <p class="admin-section-title"><?php echo e(__('Resources Section')); ?></p>

    <div class="mt-4 grid gap-3 md:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
            <input type="text" wire:model="form.translation_payload.resources.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
            <input type="text" wire:model="form.translation_payload.resources.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>

    <div class="mt-3">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
        <textarea rows="4" wire:model="form.translation_payload.resources.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
    </div>

    <div class="mt-6 space-y-5">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['resources']['cards'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cardIndex => $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Resource Card')); ?> #<?php echo e($cardIndex + 1); ?></p>

                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Eyebrow')); ?></label>
                        <input type="text" wire:model="form.translation_payload.resources.cards.<?php echo e($cardIndex); ?>.eyebrow" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                        <input type="text" wire:model="form.translation_payload.resources.cards.<?php echo e($cardIndex); ?>.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($card['body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraphIndex => $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="mt-3">
                        <div class="mb-1 flex items-center justify-between gap-3">
                            <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Paragraph')); ?> #<?php echo e($paragraphIndex + 1); ?></label>
                            <button type="button" wire:click="removeTranslationListItem('resources.cards.<?php echo e($cardIndex); ?>.body', <?php echo e($paragraphIndex); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                        </div>
                        <textarea rows="4" wire:model="form.translation_payload.resources.cards.<?php echo e($cardIndex); ?>.body.<?php echo e($paragraphIndex); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="mt-3">
                    <button type="button" wire:click="addTranslationListItem('resources.cards.<?php echo e($cardIndex); ?>.body')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                        <?php echo e(__('Add Paragraph')); ?>

                    </button>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($card['groups'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupIndex => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Group Label')); ?> #<?php echo e($groupIndex + 1); ?></label>
                        <input type="text" wire:model="form.translation_payload.resources.cards.<?php echo e($cardIndex); ?>.groups.<?php echo e($groupIndex); ?>.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($group['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemIndex => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Group Item')); ?> #<?php echo e($itemIndex + 1); ?></p>

                                <div class="mt-3">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                                    <input type="text" wire:model="form.translation_payload.resources.cards.<?php echo e($cardIndex); ?>.groups.<?php echo e($groupIndex); ?>.items.<?php echo e($itemIndex); ?>.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>

                                <div class="mt-3 grid gap-3 md:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Link Type')); ?></label>
                                        <select wire:model="form.translation_payload.resources.cards.<?php echo e($cardIndex); ?>.groups.<?php echo e($groupIndex); ?>.items.<?php echo e($itemIndex); ?>.link.type" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                            <option value="none"><?php echo e(__('None')); ?></option>
                                            <option value="external"><?php echo e(__('External / Internal URL')); ?></option>
                                            <option value="blog"><?php echo e(__('Blog Post')); ?></option>
                                            <option value="call"><?php echo e(__('Call Post')); ?></option>
                                            <option value="pdf"><?php echo e(__('PDF Asset')); ?></option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Link Label')); ?></label>
                                        <input type="text" wire:model="form.translation_payload.resources.cards.<?php echo e($cardIndex); ?>.groups.<?php echo e($groupIndex); ?>.items.<?php echo e($itemIndex); ?>.link.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                    </div>
                                </div>

                                <div class="mt-3 grid gap-3 md:grid-cols-3">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('URL')); ?></label>
                                        <input type="text" wire:model="form.translation_payload.resources.cards.<?php echo e($cardIndex); ?>.groups.<?php echo e($groupIndex); ?>.items.<?php echo e($itemIndex); ?>.link.url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Slug')); ?></label>
                                        <input type="text" wire:model="form.translation_payload.resources.cards.<?php echo e($cardIndex); ?>.groups.<?php echo e($groupIndex); ?>.items.<?php echo e($itemIndex); ?>.link.slug" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                    </div>
                                    <?php echo $__env->make('livewire.admin.content.service.partials.pdf-asset-upload-field', [
                                        'currentPath' => (string) ($item['link']['path'] ?? ''),
                                        'uploadModel' => 'assetUploads.resources_cards_'.$cardIndex.'_groups_'.$groupIndex.'_items_'.$itemIndex.'_link_path',
                                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="mt-5 grid gap-4 xl:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Primary Link')); ?></p>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.resources.cards.<?php echo e($cardIndex); ?>.primary_link.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Type')); ?></label>
                                <select wire:model="form.translation_payload.resources.cards.<?php echo e($cardIndex); ?>.primary_link.type" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                    <option value="none"><?php echo e(__('None')); ?></option>
                                    <option value="external"><?php echo e(__('External / Internal URL')); ?></option>
                                    <option value="blog"><?php echo e(__('Blog Post')); ?></option>
                                    <option value="call"><?php echo e(__('Call Post')); ?></option>
                                    <option value="pdf"><?php echo e(__('PDF Asset')); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('URL')); ?></label>
                                <input type="text" wire:model="form.translation_payload.resources.cards.<?php echo e($cardIndex); ?>.primary_link.url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Slug')); ?></label>
                                <input type="text" wire:model="form.translation_payload.resources.cards.<?php echo e($cardIndex); ?>.primary_link.slug" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <?php echo $__env->make('livewire.admin.content.service.partials.pdf-asset-upload-field', [
                                'currentPath' => (string) ($card['primary_link']['path'] ?? ''),
                                'uploadModel' => 'assetUploads.resources_cards_'.$cardIndex.'_primary_link_path',
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Secondary Link')); ?></p>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.resources.cards.<?php echo e($cardIndex); ?>.secondary_link.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Type')); ?></label>
                                <select wire:model="form.translation_payload.resources.cards.<?php echo e($cardIndex); ?>.secondary_link.type" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                    <option value="none"><?php echo e(__('None')); ?></option>
                                    <option value="external"><?php echo e(__('External / Internal URL')); ?></option>
                                    <option value="blog"><?php echo e(__('Blog Post')); ?></option>
                                    <option value="call"><?php echo e(__('Call Post')); ?></option>
                                    <option value="pdf"><?php echo e(__('PDF Asset')); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('URL')); ?></label>
                                <input type="text" wire:model="form.translation_payload.resources.cards.<?php echo e($cardIndex); ?>.secondary_link.url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Slug')); ?></label>
                                <input type="text" wire:model="form.translation_payload.resources.cards.<?php echo e($cardIndex); ?>.secondary_link.slug" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <?php echo $__env->make('livewire.admin.content.service.partials.pdf-asset-upload-field', [
                                'currentPath' => (string) ($card['secondary_link']['path'] ?? ''),
                                'uploadModel' => 'assetUploads.resources_cards_'.$cardIndex.'_secondary_link_path',
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>

<div id="eu-funds-laws" class="admin-panel admin-form-panel p-6 scroll-mt-24">
    <p class="admin-section-title"><?php echo e(__('Laws Section')); ?></p>

    <div class="mt-4 grid gap-3 md:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
            <input type="text" wire:model="form.translation_payload.laws.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
            <input type="text" wire:model="form.translation_payload.laws.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    </div>

    <div class="mt-3">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
        <textarea rows="4" wire:model="form.translation_payload.laws.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
    </div>

    <div class="mt-6 space-y-5">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($translationPayload['laws']['cards'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cardIndex => $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Law Card')); ?> #<?php echo e($cardIndex + 1); ?></p>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                    <input type="text" wire:model="form.translation_payload.laws.cards.<?php echo e($cardIndex); ?>.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                </div>

                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Summary')); ?></label>
                    <textarea rows="4" wire:model="form.translation_payload.laws.cards.<?php echo e($cardIndex); ?>.summary" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($card['lists'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $listIndex => $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-4">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('List Label')); ?> #<?php echo e($listIndex + 1); ?></label>
                        <input type="text" wire:model="form.translation_payload.laws.cards.<?php echo e($cardIndex); ?>.lists.<?php echo e($listIndex); ?>.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($list['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemIndex => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label class="block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('List Item')); ?> #<?php echo e($itemIndex + 1); ?></label>
                                    <button type="button" wire:click="removeTranslationListItem('laws.cards.<?php echo e($cardIndex); ?>.lists.<?php echo e($listIndex); ?>.items', <?php echo e($itemIndex); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Remove')); ?></button>
                                </div>
                                <input type="text" wire:model="form.translation_payload.laws.cards.<?php echo e($cardIndex); ?>.lists.<?php echo e($listIndex); ?>.items.<?php echo e($itemIndex); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-3">
                            <button type="button" wire:click="addTranslationListItem('laws.cards.<?php echo e($cardIndex); ?>.lists.<?php echo e($listIndex); ?>.items')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                <?php echo e(__('Add List Item')); ?>

                            </button>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="mt-4">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Note')); ?></label>
                    <textarea rows="3" wire:model="form.translation_payload.laws.cards.<?php echo e($cardIndex); ?>.note" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>

                <div class="mt-5 grid gap-4 xl:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Primary Link')); ?></p>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.laws.cards.<?php echo e($cardIndex); ?>.primary_link.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Type')); ?></label>
                                <select wire:model="form.translation_payload.laws.cards.<?php echo e($cardIndex); ?>.primary_link.type" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                    <option value="none"><?php echo e(__('None')); ?></option>
                                    <option value="external"><?php echo e(__('External / Internal URL')); ?></option>
                                    <option value="blog"><?php echo e(__('Blog Post')); ?></option>
                                    <option value="call"><?php echo e(__('Call Post')); ?></option>
                                    <option value="pdf"><?php echo e(__('PDF Asset')); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('URL')); ?></label>
                                <input type="text" wire:model="form.translation_payload.laws.cards.<?php echo e($cardIndex); ?>.primary_link.url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Slug')); ?></label>
                                <input type="text" wire:model="form.translation_payload.laws.cards.<?php echo e($cardIndex); ?>.primary_link.slug" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <?php echo $__env->make('livewire.admin.content.service.partials.pdf-asset-upload-field', [
                                'currentPath' => (string) ($card['primary_link']['path'] ?? ''),
                                'uploadModel' => 'assetUploads.laws_cards_'.$cardIndex.'_primary_link_path',
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Secondary Link')); ?></p>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Label')); ?></label>
                                <input type="text" wire:model="form.translation_payload.laws.cards.<?php echo e($cardIndex); ?>.secondary_link.label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Type')); ?></label>
                                <select wire:model="form.translation_payload.laws.cards.<?php echo e($cardIndex); ?>.secondary_link.type" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                    <option value="none"><?php echo e(__('None')); ?></option>
                                    <option value="external"><?php echo e(__('External / Internal URL')); ?></option>
                                    <option value="blog"><?php echo e(__('Blog Post')); ?></option>
                                    <option value="call"><?php echo e(__('Call Post')); ?></option>
                                    <option value="pdf"><?php echo e(__('PDF Asset')); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('URL')); ?></label>
                                <input type="text" wire:model="form.translation_payload.laws.cards.<?php echo e($cardIndex); ?>.secondary_link.url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Slug')); ?></label>
                                <input type="text" wire:model="form.translation_payload.laws.cards.<?php echo e($cardIndex); ?>.secondary_link.slug" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            </div>
                            <?php echo $__env->make('livewire.admin.content.service.partials.pdf-asset-upload-field', [
                                'currentPath' => (string) ($card['secondary_link']['path'] ?? ''),
                                'uploadModel' => 'assetUploads.laws_cards_'.$cardIndex.'_secondary_link_path',
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>

<div class="grid gap-6 xl:grid-cols-2">
    <div id="eu-funds-testimonials" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <p class="admin-section-title"><?php echo e(__('Testimonials Section')); ?></p>

        <div class="mt-4">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
            <input type="text" wire:model="form.translation_payload.testimonials.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
            <input type="text" wire:model="form.translation_payload.testimonials.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>

        <div class="mt-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
            <textarea rows="4" wire:model="form.translation_payload.testimonials.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
        </div>
    </div>

    <div id="eu-funds-blog" class="admin-panel admin-form-panel p-6 scroll-mt-24">
        <p class="admin-section-title"><?php echo e(__('Blog Section')); ?></p>

        <div class="mt-4">
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

<div id="eu-funds-meeting" class="admin-panel admin-form-panel p-6 scroll-mt-24">
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
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/content/service/partials/eu-funds-editor.blade.php ENDPATH**/ ?>