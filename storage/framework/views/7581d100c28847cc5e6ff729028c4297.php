<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold tracking-tight"><?php echo e(__('User Groups')); ?></h1>
                <p class="mt-1 text-sm text-slate-600"><?php echo e(__('Manage segmentation groups for audience rules, pricing, and campaigns.')); ?></p>
                <p class="mt-2 text-xs text-slate-500"><?php echo e(__('Items per page:')); ?> <span class="admin-chip"><?php echo e($perPage); ?></span></p>
            </div>
            <div class="w-full sm:w-80">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.common.search')); ?></label>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="<?php echo e(__('Code, name or description...')); ?>"
                    class="admin-search-input w-full rounded-xl border px-3 py-2 text-sm"
                />
            </div>
        </div>
    </div>

    <div class="admin-stack">
        <div class="admin-panel admin-form-panel p-6" style="order:2;">
            <h2 class="admin-section-title"><?php echo e($editingId ? __('Edit Group') : __('Create Group')); ?></h2>

            <form wire:submit="save" class="admin-form mt-4 space-y-4">
                <div class="grid gap-3" style="grid-template-columns: repeat(12, minmax(0, 1fr));">
                    <div style="grid-column: span 3;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Code')); ?></label>
                        <input type="text" wire:model="form.code" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div style="grid-column: span 4;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Name')); ?></label>
                        <input type="text" wire:model="form.name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.common.sort')); ?></label>
                        <input type="number" wire:model="form.sort_order" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div style="grid-column: span 3;">
                        <div class="mt-6 flex flex-wrap gap-3">
                            <button
                                type="button"
                                wire:click="$toggle('form.is_active')"
                                class="admin-switch"
                                data-state="<?php echo e($form['is_active'] ? 'on' : 'off'); ?>"
                                role="switch"
                            >
                                <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                                <span class="admin-switch-label"><?php echo e($form['is_active'] ? __('admin.common.active') : __('admin.common.inactive')); ?></span>
                            </button>

                            <button
                                type="button"
                                wire:click="$toggle('form.is_default')"
                                class="admin-switch"
                                data-state="<?php echo e($form['is_default'] ? 'on' : 'off'); ?>"
                                role="switch"
                            >
                                <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                                <span class="admin-switch-label"><?php echo e($form['is_default'] ? __('Default') : __('Not Default')); ?></span>
                            </button>
                        </div>
                    </div>
                    <div style="grid-column: span 12;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Description')); ?></label>
                        <textarea wire:model="form.description" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>
                </div>

                <div class="admin-form-actions flex items-center gap-2 pt-2">
                    <button type="submit" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                        <?php echo e($editingId ? __('Update Group') : __('Create Group')); ?>

                    </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editingId): ?>
                        <button type="button" wire:click="cancelEdit" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                            <?php echo e(__('Cancel')); ?>

                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </form>
        </div>

        <div class="admin-panel admin-panel-soft p-5" style="order:1;">
            <h2 class="admin-section-title"><?php echo e(__('admin.common.items')); ?></h2>

            <div class="mt-4 overflow-x-auto">
                <table class="admin-items-table min-w-full text-sm">
                    <thead class="text-slate-600">
                        <tr>
                            <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('ID')); ?></th>
                            <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Code')); ?></th>
                            <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Name')); ?></th>
                            <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('Users')); ?></th>
                            <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('admin.common.sort')); ?></th>
                            <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('admin.common.state')); ?></th>
                            <th class="px-3 py-2 text-right font-semibold"><?php echo e(__('admin.common.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-3 py-2 text-center font-mono text-xs text-slate-700"><?php echo e($row->id); ?></td>
                                <td class="px-3 py-2 text-slate-800"><?php echo e($row->code); ?></td>
                                <td class="px-3 py-2 text-slate-800">
                                    <div><?php echo e($row->name); ?></div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->description): ?>
                                        <div class="text-xs text-slate-500"><?php echo e($row->description); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-3 py-2 text-center text-slate-700"><?php echo e($row->users_count); ?></td>
                                <td class="px-3 py-2 text-center text-slate-700"><?php echo e($row->sort_order); ?></td>
                                <td class="px-3 py-2 text-center">
                                    <button type="button" wire:click="toggleActive(<?php echo e($row->id); ?>)" class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($row->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700'); ?>">
                                        <?php echo e($row->is_active ? __('admin.common.active') : __('admin.common.inactive')); ?>

                                    </button>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->is_default): ?>
                                        <span class="ml-1 rounded-full bg-cyan-100 px-2 py-0.5 text-xs font-semibold text-cyan-800"><?php echo e(__('Default')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$row->is_default): ?>
                                            <button type="button" wire:click="makeDefault(<?php echo e($row->id); ?>)" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('Default')); ?></button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <button type="button" wire:click="edit(<?php echo e($row->id); ?>)" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('admin.common.edit')); ?></button>
                                        <button type="button" wire:click="delete(<?php echo e($row->id); ?>)" wire:confirm="<?php echo e(__('Delete this group?')); ?>" class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"><?php echo e(__('admin.common.delete')); ?></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-3 py-8 text-center text-sm text-slate-500"><?php echo e(__('No groups yet.')); ?></td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <?php echo e($rows->links()); ?>

            </div>
        </div>
    </div>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/user/group-manager.blade.php ENDPATH**/ ?>