<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight"><?php echo e(__('User Activity')); ?></h1>
                <p class="mt-1 text-sm text-slate-600"><?php echo e(__('Audit admin actions and front user tracking events.')); ?></p>
                <p class="mt-2 text-xs text-slate-500"><?php echo e(__('Items per page')); ?>: <span class="admin-chip"><?php echo e($perPage); ?></span></p>
            </div>

            <div class="flex w-[66rem] max-w-full items-end justify-end gap-3">
                <div class="grid w-full max-w-[56rem] items-end gap-3" style="grid-template-columns: 12rem minmax(24rem, 1fr);">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Source')); ?></label>
                        <select wire:model.live="source" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border px-3 py-2 text-sm">
                            <option value="admin"><?php echo e(__('Admin Activity Log')); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($loyaltyEnabled): ?>
                                <option value="loyalty"><?php echo e(__('Loyalty Audit')); ?></option>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <option value="tracking"><?php echo e(__('User Tracking')); ?></option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.common.search')); ?></label>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="<?php echo e(__('Log/event/user/url...')); ?>" class="admin-search-input w-full rounded-xl border px-3 py-2 text-sm" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-panel admin-panel-soft p-5">
        <h2 class="admin-section-title"><?php echo e(__('admin.common.items')); ?></h2>

        <div class="mt-4 overflow-x-auto">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($source === 'tracking'): ?>
                <table class="admin-items-table min-w-full text-sm">
                    <thead class="text-slate-600">
                        <tr>
                            <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('Time')); ?></th>
                            <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Event')); ?></th>
                            <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('User')); ?></th>
                            <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('URL')); ?></th>
                            <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Subject')); ?></th>
                            <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('IP')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-3 py-2 text-center text-xs text-slate-600"><?php echo e($row->occurred_at?->format('Y-m-d H:i:s') ?? '-'); ?></td>
                                <td class="px-3 py-2">
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700"><?php echo e($row->event); ?></span>
                                </td>
                                <td class="px-3 py-2 text-slate-800">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->user): ?>
                                        <div><?php echo e($row->user->name); ?></div>
                                        <div class="text-xs text-slate-500"><?php echo e($row->user->email); ?></div>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-500"><?php echo e(__('Guest')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-3 py-2 text-xs text-slate-700">
                                    <div class="max-w-[28rem] truncate"><?php echo e($row->url ?: '-'); ?></div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->referrer): ?>
                                        <div class="mt-1 max-w-[28rem] truncate text-slate-500"><?php echo e(__('Ref:')); ?> <?php echo e($row->referrer); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-3 py-2 text-xs text-slate-700">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->subject_type && $row->subject_id): ?>
                                        <?php echo e(class_basename($row->subject_type)); ?> #<?php echo e($row->subject_id); ?>

                                    <?php else: ?>
                                        -
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-3 py-2 text-center text-xs text-slate-600"><?php echo e($row->ip_address ?: '-'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-3 py-8 text-center text-sm text-slate-500"><?php echo e(__('No tracking events found.')); ?></td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <table class="admin-items-table min-w-full text-sm">
                    <thead class="text-slate-600">
                        <tr>
                            <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('Time')); ?></th>
                            <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Log')); ?></th>
                            <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Event')); ?></th>
                            <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Description')); ?></th>
                            <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Causer')); ?></th>
                            <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Subject')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-3 py-2 text-center text-xs text-slate-600"><?php echo e($row->created_at?->format('Y-m-d H:i:s') ?? '-'); ?></td>
                                <td class="px-3 py-2">
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700"><?php echo e($row->log_name ?: '-'); ?></span>
                                </td>
                                <td class="px-3 py-2 text-xs text-slate-700"><?php echo e($row->event ?: '-'); ?></td>
                                <td class="px-3 py-2 text-slate-800"><?php echo e($row->description); ?></td>
                                <td class="px-3 py-2 text-slate-800">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->causer): ?>
                                        <div><?php echo e($row->causer->name); ?></div>
                                        <div class="text-xs text-slate-500"><?php echo e($row->causer->email); ?></div>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-500"><?php echo e(__('System')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-3 py-2 text-xs text-slate-700">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->subject_type && $row->subject_id): ?>
                                        <?php echo e(class_basename($row->subject_type)); ?> #<?php echo e($row->subject_id); ?>

                                    <?php else: ?>
                                        -
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-3 py-8 text-center text-sm text-slate-500"><?php echo e(__('No admin activities found.')); ?></td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="mt-4">
            <?php echo e($rows->links()); ?>

        </div>
    </div>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/user/activity-manager.blade.php ENDPATH**/ ?>