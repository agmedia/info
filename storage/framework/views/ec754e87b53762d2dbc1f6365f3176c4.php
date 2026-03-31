<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight"><?php echo e(__('admin.messages.collaboration_assessment.manager.title')); ?></h1>
                <p class="mt-1 text-sm text-slate-600"><?php echo e(__('admin.messages.collaboration_assessment.manager.subtitle')); ?></p>
                <p class="mt-2 text-xs text-slate-500"><?php echo e(__('admin.messages.collaboration_assessment.manager.items_per_page')); ?>: <span class="admin-chip"><?php echo e($perPage); ?></span></p>
            </div>

            <div class="flex w-[64rem] max-w-full items-end justify-end gap-3">
                <div class="grid w-full max-w-[48rem] items-end gap-3 md:grid-cols-[minmax(0,1fr)_12rem]">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.common.search')); ?></label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="<?php echo e(__('admin.messages.collaboration_assessment.manager.search_placeholder')); ?>"
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
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.messages.collaboration_assessment.manager.summary.all')); ?></p>
            <p class="mt-2 text-2xl font-semibold text-slate-900"><?php echo e(number_format((int) ($totals['all'] ?? 0))); ?></p>
        </div>
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.messages.collaboration_assessment.status.new')); ?></p>
            <p class="mt-2 text-2xl font-semibold text-amber-700"><?php echo e(number_format((int) ($totals['new'] ?? 0))); ?></p>
        </div>
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.messages.collaboration_assessment.status.read')); ?></p>
            <p class="mt-2 text-2xl font-semibold text-sky-700"><?php echo e(number_format((int) ($totals['read'] ?? 0))); ?></p>
        </div>
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.messages.collaboration_assessment.status.resolved')); ?></p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700"><?php echo e(number_format((int) ($totals['resolved'] ?? 0))); ?></p>
        </div>
    </div>

    <div class="admin-panel admin-panel-soft p-5">
        <h2 class="admin-section-title"><?php echo e(__('admin.common.items')); ?></h2>

        <div class="mt-4 overflow-x-auto">
            <table class="admin-items-table min-w-full text-sm">
                <thead class="text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('admin.messages.collaboration_assessment.manager.table.contact')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('admin.messages.collaboration_assessment.manager.table.company')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('admin.messages.collaboration_assessment.manager.table.assessment')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('admin.messages.collaboration_assessment.manager.table.state')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('admin.messages.collaboration_assessment.manager.table.received')); ?></th>
                        <th class="px-3 py-2 text-right font-semibold"><?php echo e(__('admin.common.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $answers = (array) data_get($row->payload, 'answers', []);
                            $booleanMap = [
                                'yes' => __('admin.common.yes'),
                                'no' => __('admin.common.no'),
                            ];
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
                                <div class="font-medium text-slate-900"><?php echo e($answers['company_name'] ?? __('admin.messages.collaboration_assessment.manager.not_provided')); ?></div>
                                <div class="mt-1 text-xs text-slate-500">
                                    <?php echo e(__('admin.messages.collaboration_assessment.manager.labels.company_oib')); ?>:
                                    <?php echo e($answers['company_oib'] ?? __('admin.messages.collaboration_assessment.manager.not_provided')); ?>

                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    <?php echo e(__('admin.messages.collaboration_assessment.manager.labels.activity')); ?>:
                                    <?php echo e($answers['activity'] ?? __('admin.messages.collaboration_assessment.manager.not_provided')); ?>

                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($answers['potential_start_date'] ?? '')) !== ''): ?>
                                    <div class="mt-1 text-xs text-slate-500">
                                        <?php echo e(__('admin.messages.collaboration_assessment.manager.labels.potential_start_date')); ?>:
                                        <?php echo e($answers['potential_start_date']); ?>

                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-3 py-3 text-slate-700">
                                <div class="font-medium text-slate-900">
                                    <?php echo e(__('admin.messages.collaboration_assessment.manager.labels.incoming_invoices_monthly')); ?>:
                                    <?php echo e($answers['incoming_invoices_monthly'] ?? __('admin.messages.collaboration_assessment.manager.not_provided')); ?>

                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    <?php echo e(__('admin.messages.collaboration_assessment.manager.labels.outgoing_invoices_monthly')); ?>:
                                    <?php echo e($answers['outgoing_invoices_monthly'] ?? __('admin.messages.collaboration_assessment.manager.not_provided')); ?>

                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    <?php echo e(__('admin.messages.collaboration_assessment.manager.labels.bank_accounts_monthly')); ?>:
                                    <?php echo e($answers['bank_accounts_monthly'] ?? __('admin.messages.collaboration_assessment.manager.not_provided')); ?>

                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    <?php echo e(__('admin.messages.collaboration_assessment.manager.labels.payroll_calculations_monthly')); ?>:
                                    <?php echo e($answers['payroll_calculations_monthly'] ?? __('admin.messages.collaboration_assessment.manager.not_provided')); ?>

                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    <?php echo e(__('admin.messages.collaboration_assessment.manager.labels.inventory_bookkeeping')); ?>:
                                    <?php echo e($booleanMap[(string) ($answers['inventory_bookkeeping'] ?? '')] ?? __('admin.messages.collaboration_assessment.manager.not_provided')); ?>

                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    <?php echo e(__('admin.messages.collaboration_assessment.manager.labels.cost_centers_tracking')); ?>:
                                    <?php echo e($booleanMap[(string) ($answers['cost_centers_tracking'] ?? '')] ?? __('admin.messages.collaboration_assessment.manager.not_provided')); ?>

                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    <?php echo e(__('admin.messages.collaboration_assessment.manager.labels.monthly_reporting')); ?>:
                                    <?php echo e($booleanMap[(string) ($answers['monthly_reporting'] ?? '')] ?? __('admin.messages.collaboration_assessment.manager.not_provided')); ?>

                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($answers['additional_requirements'] ?? '')) !== ''): ?>
                                    <div class="mt-1 text-xs text-slate-500">
                                        <?php echo e(__('admin.messages.collaboration_assessment.manager.labels.additional_requirements')); ?>:
                                        <?php echo e(\Illuminate\Support\Str::limit((string) $answers['additional_requirements'], 120)); ?>

                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($answers['tax_issues'] ?? '')) !== ''): ?>
                                    <div class="mt-1 text-xs text-slate-500">
                                        <?php echo e(__('admin.messages.collaboration_assessment.manager.labels.tax_issues')); ?>:
                                        <?php echo e(\Illuminate\Support\Str::limit((string) $answers['tax_issues'], 120)); ?>

                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($statusClasses); ?>">
                                    <?php echo e(__('admin.messages.collaboration_assessment.status.'.$row->status)); ?>

                                </span>
                            </td>
                            <td class="px-3 py-3 text-center text-xs text-slate-600">
                                <?php echo e($row->created_at?->format('Y-m-d H:i') ?? '-'); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->reviewed_at): ?>
                                    <div class="mt-1 text-[11px] text-slate-500">
                                        <?php echo e(__('admin.messages.collaboration_assessment.manager.reviewed_by', ['name' => $row->reviewer?->name ?: __('admin.layout.admin')])); ?>

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
                                        <?php echo e(__('admin.messages.collaboration_assessment.manager.actions.mark_new')); ?>

                                    </button>
                                    <button
                                        type="button"
                                        wire:click="markAsRead(<?php echo e((int) $row->id); ?>)"
                                        class="rounded-lg border border-sky-200 px-2 py-1 text-xs font-semibold text-sky-700 hover:bg-sky-50"
                                    >
                                        <?php echo e(__('admin.messages.collaboration_assessment.manager.actions.mark_read')); ?>

                                    </button>
                                    <button
                                        type="button"
                                        wire:click="markAsResolved(<?php echo e((int) $row->id); ?>)"
                                        class="rounded-lg border border-emerald-200 px-2 py-1 text-xs font-semibold text-emerald-700 hover:bg-emerald-50"
                                    >
                                        <?php echo e(__('admin.messages.collaboration_assessment.manager.actions.mark_resolved')); ?>

                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-sm text-slate-500"><?php echo e(__('admin.messages.collaboration_assessment.manager.empty')); ?></td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($rows->links()); ?>

        </div>
    </div>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/message/collaboration-assessment-message-manager.blade.php ENDPATH**/ ?>