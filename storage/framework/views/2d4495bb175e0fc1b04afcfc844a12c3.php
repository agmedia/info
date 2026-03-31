<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight"><?php echo e(__('admin.messages.eu_funds_questionnaire.manager.title')); ?></h1>
                <p class="mt-1 text-sm text-slate-600"><?php echo e(__('admin.messages.eu_funds_questionnaire.manager.subtitle')); ?></p>
                <p class="mt-2 text-xs text-slate-500"><?php echo e(__('admin.messages.eu_funds_questionnaire.manager.items_per_page')); ?>: <span class="admin-chip"><?php echo e($perPage); ?></span></p>
            </div>

            <div class="flex w-[64rem] max-w-full items-end justify-end gap-3">
                <div class="grid w-full max-w-[48rem] items-end gap-3 md:grid-cols-[minmax(0,1fr)_12rem]">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.common.search')); ?></label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="<?php echo e(__('admin.messages.eu_funds_questionnaire.manager.search_placeholder')); ?>"
                            class="admin-search-input w-full rounded-xl border px-3 py-2 text-sm"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.common.state')); ?></label>
                        <select wire:model.live="status" data-tom-select data-tom-no-search="1" class="admin-search-input admin-select w-full rounded-xl border px-3 py-2 text-sm">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.messages.eu_funds_questionnaire.manager.summary.all')); ?></p>
            <p class="mt-2 text-2xl font-semibold text-slate-900"><?php echo e(number_format((int) ($totals['all'] ?? 0))); ?></p>
        </div>
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.messages.eu_funds_questionnaire.status.new')); ?></p>
            <p class="mt-2 text-2xl font-semibold text-amber-700"><?php echo e(number_format((int) ($totals['new'] ?? 0))); ?></p>
        </div>
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.messages.eu_funds_questionnaire.status.read')); ?></p>
            <p class="mt-2 text-2xl font-semibold text-sky-700"><?php echo e(number_format((int) ($totals['read'] ?? 0))); ?></p>
        </div>
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.messages.eu_funds_questionnaire.status.resolved')); ?></p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700"><?php echo e(number_format((int) ($totals['resolved'] ?? 0))); ?></p>
        </div>
    </div>

    <div class="admin-panel admin-panel-soft p-5">
        <h2 class="admin-section-title"><?php echo e(__('admin.common.items')); ?></h2>

        <div class="mt-4 overflow-x-auto">
            <table class="admin-items-table min-w-full text-sm">
                <thead class="text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('admin.messages.eu_funds_questionnaire.manager.table.contact')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('admin.messages.eu_funds_questionnaire.manager.table.company')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('admin.messages.eu_funds_questionnaire.manager.table.questionnaire')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('admin.messages.eu_funds_questionnaire.manager.table.state')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('admin.messages.eu_funds_questionnaire.manager.table.received')); ?></th>
                        <th class="px-3 py-2 text-right font-semibold"><?php echo e(__('admin.common.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $answers = (array) data_get($row->payload, 'answers', []);
                            $projectSectors = implode(', ', array_values(array_filter((array) ($answers['project_sectors'] ?? []))));
                            $plannedCosts = implode(', ', array_values(array_filter((array) ($answers['planned_costs'] ?? []))));
                            $interestedServices = implode(', ', array_values(array_filter((array) ($answers['interested_services'] ?? []))));
                            $statusClasses = match ($row->status) {
                                'read' => 'bg-sky-100 text-sky-800',
                                'resolved' => 'bg-emerald-100 text-emerald-800',
                                default => 'bg-amber-100 text-amber-800',
                            };
                        ?>
                        <tr class="<?php echo e($row->status === 'new' ? 'bg-amber-50/40' : ''); ?>">
                            <td class="px-3 py-3 text-slate-800">
                                <div class="font-semibold text-slate-900"><?php echo e($row->name); ?></div>
                                <div class="mt-1 text-sm text-slate-600">
                                    <a href="mailto:<?php echo e($row->email); ?>" class="hover:text-slate-900 hover:underline"><?php echo e($row->email); ?></a>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->phone): ?>
                                    <div class="mt-1 text-xs text-slate-500"><?php echo e($row->phone); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-3 py-3 text-slate-700">
                                <div class="font-medium text-slate-900"><?php echo e($answers['company_name'] ?? __('admin.messages.eu_funds_questionnaire.manager.not_provided')); ?></div>
                                <div class="mt-1 text-xs text-slate-500">
                                    <?php echo e(__('admin.messages.eu_funds_questionnaire.manager.labels.company_oib')); ?>:
                                    <?php echo e($answers['company_oib'] ?? __('admin.messages.eu_funds_questionnaire.manager.not_provided')); ?>

                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    <?php echo e(__('admin.messages.eu_funds_questionnaire.manager.labels.company_activity')); ?>:
                                    <?php echo e($answers['company_activity'] ?? __('admin.messages.eu_funds_questionnaire.manager.not_provided')); ?>

                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    <?php echo e(__('admin.messages.eu_funds_questionnaire.manager.labels.investment_location')); ?>:
                                    <?php echo e($answers['investment_location'] ?? __('admin.messages.eu_funds_questionnaire.manager.not_provided')); ?>

                                </div>
                            </td>
                            <td class="px-3 py-3 text-slate-700">
                                <div class="font-medium text-slate-900">
                                    <?php echo e(__('admin.messages.eu_funds_questionnaire.manager.labels.investment_amount')); ?>:
                                    <?php echo e($answers['investment_amount'] ?? __('admin.messages.eu_funds_questionnaire.manager.not_provided')); ?>

                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    <?php echo e(__('admin.messages.eu_funds_questionnaire.manager.labels.employee_count')); ?>:
                                    <?php echo e($answers['employee_count'] ?? __('admin.messages.eu_funds_questionnaire.manager.not_provided')); ?>

                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    <?php echo e(__('admin.messages.eu_funds_questionnaire.manager.labels.related_companies')); ?>:
                                    <?php echo e($answers['related_companies'] ?? __('admin.messages.eu_funds_questionnaire.manager.not_provided')); ?>

                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    <?php echo e(__('admin.messages.eu_funds_questionnaire.manager.labels.project_sectors')); ?>:
                                    <?php echo e(\Illuminate\Support\Str::limit($projectSectors !== '' ? $projectSectors : __('admin.messages.eu_funds_questionnaire.manager.not_provided'), 120)); ?>

                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($answers['project_sector_other'] ?? '')) !== ''): ?>
                                    <div class="mt-1 text-xs text-slate-500">
                                        <?php echo e(__('admin.messages.eu_funds_questionnaire.manager.labels.project_sector_other')); ?>:
                                        <?php echo e($answers['project_sector_other']); ?>

                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="mt-1 text-xs text-slate-500">
                                    <?php echo e(__('admin.messages.eu_funds_questionnaire.manager.labels.planned_costs')); ?>:
                                    <?php echo e(\Illuminate\Support\Str::limit($plannedCosts !== '' ? $plannedCosts : __('admin.messages.eu_funds_questionnaire.manager.not_provided'), 120)); ?>

                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    <?php echo e(__('admin.messages.eu_funds_questionnaire.manager.labels.interested_services')); ?>:
                                    <?php echo e(\Illuminate\Support\Str::limit($interestedServices !== '' ? $interestedServices : __('admin.messages.eu_funds_questionnaire.manager.not_provided'), 120)); ?>

                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($answers['additional_notes'] ?? '')) !== ''): ?>
                                    <div class="mt-1 text-xs text-slate-500">
                                        <?php echo e(__('admin.messages.eu_funds_questionnaire.manager.labels.additional_notes')); ?>:
                                        <?php echo e(\Illuminate\Support\Str::limit((string) $answers['additional_notes'], 120)); ?>

                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($statusClasses); ?>">
                                    <?php echo e(__('admin.messages.eu_funds_questionnaire.status.'.$row->status)); ?>

                                </span>
                            </td>
                            <td class="px-3 py-3 text-center text-xs text-slate-600">
                                <?php echo e($row->created_at?->format('Y-m-d H:i') ?? '-'); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->reviewed_at): ?>
                                    <div class="mt-1 text-[11px] text-slate-500">
                                        <?php echo e(__('admin.messages.eu_funds_questionnaire.manager.reviewed_by', ['name' => $row->reviewer?->name ?: __('admin.layout.admin')])); ?>

                                    </div>
                                    <div class="text-[11px] text-slate-500"><?php echo e($row->reviewed_at->format('Y-m-d H:i')); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="markAsNew(<?php echo e((int) $row->id); ?>)"
                                        class="rounded-lg border border-amber-200 px-2 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-50"
                                    >
                                        <?php echo e(__('admin.messages.eu_funds_questionnaire.manager.actions.mark_new')); ?>

                                    </button>
                                    <button
                                        type="button"
                                        wire:click="markAsRead(<?php echo e((int) $row->id); ?>)"
                                        class="rounded-lg border border-sky-200 px-2 py-1 text-xs font-semibold text-sky-700 hover:bg-sky-50"
                                    >
                                        <?php echo e(__('admin.messages.eu_funds_questionnaire.manager.actions.mark_read')); ?>

                                    </button>
                                    <button
                                        type="button"
                                        wire:click="markAsResolved(<?php echo e((int) $row->id); ?>)"
                                        class="rounded-lg border border-emerald-200 px-2 py-1 text-xs font-semibold text-emerald-700 hover:bg-emerald-50"
                                    >
                                        <?php echo e(__('admin.messages.eu_funds_questionnaire.manager.actions.mark_resolved')); ?>

                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-sm text-slate-500"><?php echo e(__('admin.messages.eu_funds_questionnaire.manager.empty')); ?></td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($rows->links()); ?>

        </div>
    </div>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/message/eu-funds-questionnaire-manager.blade.php ENDPATH**/ ?>