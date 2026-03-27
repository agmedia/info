<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight"><?php echo e(__('admin.messages.download_requests.manager.title')); ?></h1>
                <p class="mt-1 text-sm text-slate-600"><?php echo e(__('admin.messages.download_requests.manager.subtitle')); ?></p>
                <p class="mt-2 text-xs text-slate-500"><?php echo e(__('admin.messages.download_requests.manager.items_per_page')); ?>: <span class="admin-chip"><?php echo e($perPage); ?></span></p>
            </div>

            <div class="flex w-[64rem] max-w-full items-end justify-end gap-3">
                <div class="grid w-full max-w-[48rem] items-end gap-3 md:grid-cols-[minmax(0,1fr)_12rem]">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.common.search')); ?></label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="<?php echo e(__('admin.messages.download_requests.manager.search_placeholder')); ?>"
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
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.messages.download_requests.manager.summary.all')); ?></p>
            <p class="mt-2 text-2xl font-semibold text-slate-900"><?php echo e(number_format((int) ($totals['all'] ?? 0))); ?></p>
        </div>
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.messages.download_requests.status.new')); ?></p>
            <p class="mt-2 text-2xl font-semibold text-amber-700"><?php echo e(number_format((int) ($totals['new'] ?? 0))); ?></p>
        </div>
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.messages.download_requests.status.read')); ?></p>
            <p class="mt-2 text-2xl font-semibold text-sky-700"><?php echo e(number_format((int) ($totals['read'] ?? 0))); ?></p>
        </div>
        <div class="admin-panel admin-panel-soft p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.messages.download_requests.status.resolved')); ?></p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700"><?php echo e(number_format((int) ($totals['resolved'] ?? 0))); ?></p>
        </div>
    </div>

    <div class="admin-panel admin-panel-soft p-5">
        <h2 class="admin-section-title"><?php echo e(__('admin.common.items')); ?></h2>

        <div class="mt-4 overflow-x-auto">
            <table class="admin-items-table min-w-full text-sm">
                <thead class="text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('admin.messages.download_requests.manager.table.contact')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('admin.messages.download_requests.manager.table.resource')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('admin.messages.download_requests.manager.table.delivery')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('admin.messages.download_requests.manager.table.state')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('admin.messages.download_requests.manager.table.received')); ?></th>
                        <th class="px-3 py-2 text-right font-semibold"><?php echo e(__('admin.common.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
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
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->company): ?>
                                    <div class="mt-1 text-xs text-slate-500"><?php echo e($row->company); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-3 py-3 text-slate-700">
                                <div class="font-medium text-slate-900"><?php echo e($row->document_title); ?></div>
                                <div class="mt-1 text-xs text-slate-500">
                                    <?php echo e($row->document_group_code ? \App\Support\Content\ResourceDocumentGroupRegistry::label((string) $row->document_group_code) : '-'); ?>

                                </div>
                            </td>
                            <td class="px-3 py-3 text-slate-700">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->document_download_url): ?>
                                    <a href="<?php echo e($row->document_download_url); ?>" target="_blank" rel="noreferrer" class="inline-flex rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                        <?php echo e(__('admin.messages.download_requests.manager.open_document')); ?>

                                    </a>
                                <?php else: ?>
                                    <span class="text-xs text-slate-500"><?php echo e(__('admin.messages.download_requests.manager.no_document')); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($statusClasses); ?>">
                                    <?php echo e(__('admin.messages.download_requests.status.'.$row->status)); ?>

                                </span>
                            </td>
                            <td class="px-3 py-3 text-center text-xs text-slate-600">
                                <?php echo e($row->created_at?->format('Y-m-d H:i') ?? '-'); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->reviewed_at): ?>
                                    <div class="mt-1 text-[11px] text-slate-500">
                                        <?php echo e(__('admin.messages.download_requests.manager.reviewed_by', ['name' => $row->reviewer?->name ?: __('admin.layout.admin')])); ?>

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
                                        <?php echo e(__('admin.messages.download_requests.manager.actions.mark_new')); ?>

                                    </button>
                                    <button
                                        type="button"
                                        wire:click="markAsRead(<?php echo e((int) $row->id); ?>)"
                                        class="rounded-lg border border-sky-200 px-2 py-1 text-xs font-semibold text-sky-700 hover:bg-sky-50"
                                    >
                                        <?php echo e(__('admin.messages.download_requests.manager.actions.mark_read')); ?>

                                    </button>
                                    <button
                                        type="button"
                                        wire:click="markAsResolved(<?php echo e((int) $row->id); ?>)"
                                        class="rounded-lg border border-emerald-200 px-2 py-1 text-xs font-semibold text-emerald-700 hover:bg-emerald-50"
                                    >
                                        <?php echo e(__('admin.messages.download_requests.manager.actions.mark_resolved')); ?>

                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-sm text-slate-500"><?php echo e(__('admin.messages.download_requests.manager.empty')); ?></td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($rows->links()); ?>

        </div>
    </div>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/message/resource-download-request-manager.blade.php ENDPATH**/ ?>