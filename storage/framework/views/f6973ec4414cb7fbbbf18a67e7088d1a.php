<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold tracking-tight"><?php echo e(__('Content Slots')); ?></h1>
                <p class="mt-1 text-sm text-slate-600"><?php echo e(__('Map blocks to placement/target rules.')); ?></p>
                <p class="mt-2 text-xs text-slate-500"><?php echo e(__('Items per page')); ?>: <span class="admin-chip"><?php echo e($perPage); ?></span></p>
            </div>
            <div class="flex w-full gap-2 sm:w-auto sm:items-end">
                <div class="w-full sm:w-80">
                    <label for="content-slot-search" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Search')); ?></label>
                    <input
                        id="content-slot-search"
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="<?php echo e(__('Placement, target or block...')); ?>"
                        class="admin-search-input w-full rounded-xl border px-3 py-2 text-sm"
                    />
                </div>
                <a href="<?php echo e(route('admin.content.slots.create')); ?>" class="inline-flex h-10 items-center rounded-xl bg-cyan-700 px-4 text-sm font-semibold text-white hover:bg-cyan-800"><?php echo e(__('Create')); ?></a>
            </div>
        </div>
    </div>

    <div class="admin-panel admin-panel-soft p-5">
        <h2 class="admin-section-title"><?php echo e(__('Items')); ?></h2>

        <div class="mt-4 overflow-x-auto">
            <table class="admin-items-table min-w-full text-sm">
                <thead class="text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Placement')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Target')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Block')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Preview')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('Sort')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('State')); ?></th>
                        <th class="px-3 py-2 text-right font-semibold"><?php echo e(__('Actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-3 py-2 font-mono text-xs text-slate-700"><?php echo e($row->placement); ?></td>
                            <td class="px-3 py-2 text-slate-700">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->target_type): ?>
                                    <span class="font-semibold"><?php echo e($row->target_type); ?></span>
                                    <span class="text-xs text-slate-500">/<?php echo e($row->target_ref ?: '*'); ?></span>
                                <?php else: ?>
                                    <span class="text-slate-500"><?php echo e(__('Global')); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-3 py-2 text-slate-800">
                                <span class="font-medium"><?php echo e($row->block?->name ?? __('Missing block')); ?></span>
                                <span class="ml-1 text-xs text-slate-500">(<?php echo e($row->block?->code ?? '-'); ?>)</span>
                            </td>
                            <td class="px-3 py-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->block): ?>
                                    <?php echo $__env->make('admin.content.partials.block-type-preview', ['type' => $row->block->type, 'size' => 'xs'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php else: ?>
                                    <span class="text-xs text-slate-500">-</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-3 py-2 text-center text-slate-700"><?php echo e($row->sort_order); ?></td>
                            <td class="px-3 py-2 text-center">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($row->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700'); ?>">
                                    <?php echo e($row->is_active ? __('Active') : __('Inactive')); ?>

                                </span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="<?php echo e(route('admin.content.slots.edit', ['slot' => $row->id])); ?>" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('Edit')); ?></a>
                                    <button type="button" wire:click="delete(<?php echo e($row->id); ?>)" wire:confirm="<?php echo e(__('Delete this slot?')); ?>" class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"><?php echo e(__('Delete')); ?></button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-sm text-slate-500"><?php echo e(__('No content slots yet.')); ?></td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($rows->links()); ?>

        </div>
    </div>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/content/slot/index.blade.php ENDPATH**/ ?>