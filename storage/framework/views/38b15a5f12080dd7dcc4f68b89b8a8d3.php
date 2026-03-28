<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight"><?php echo e(__('Admin Users')); ?></h1>
                <p class="mt-1 text-sm text-slate-600"><?php echo e(__('Manage administrator accounts, roles and access in one place.')); ?></p>
                <p class="mt-2 text-xs text-slate-500"><?php echo e(__('Items per page')); ?>: <span class="admin-chip"><?php echo e($perPage); ?></span></p>
            </div>

            <div class="flex w-[64rem] max-w-full items-end justify-end gap-3">
                <div class="grid w-full max-w-[56rem] items-end gap-3" style="grid-template-columns: minmax(30rem, 1fr) 12rem;">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.common.search')); ?></label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="<?php echo e(__('Name or email...')); ?>"
                            class="admin-search-input w-full rounded-xl border px-3 py-2 text-sm"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Role')); ?></label>
                        <select wire:model.live="role" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border px-3 py-2 text-sm">
                            <option value=""><?php echo e(__('All roles')); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($roleItem->name); ?>"><?php echo e($roleItem->title ?: ucfirst($roleItem->name)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-panel admin-panel-soft p-5">
        <h2 class="admin-section-title"><?php echo e(__('admin.common.items')); ?></h2>

        <div class="mt-4 overflow-x-auto">
            <table class="admin-items-table min-w-full text-sm">
                <thead class="text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-center font-semibold">
                            <button type="button" wire:click="sort('id')" class="inline-flex items-center gap-1">
                                <?php echo e(__('ID')); ?> <span class="text-xs"><?php echo e($sortBy === 'id' ? ($sortDir === 'asc' ? '^' : 'v') : '<>'); ?></span>
                            </button>
                        </th>
                        <th class="px-3 py-2 text-left font-semibold">
                            <button type="button" wire:click="sort('name')" class="inline-flex items-center gap-1">
                                <?php echo e(__('Name')); ?> <span class="text-xs"><?php echo e($sortBy === 'name' ? ($sortDir === 'asc' ? '^' : 'v') : '<>'); ?></span>
                            </button>
                        </th>
                        <th class="px-3 py-2 text-left font-semibold">
                            <button type="button" wire:click="sort('email')" class="inline-flex items-center gap-1">
                                <?php echo e(__('Email')); ?> <span class="text-xs"><?php echo e($sortBy === 'email' ? ($sortDir === 'asc' ? '^' : 'v') : '<>'); ?></span>
                            </button>
                        </th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('Role')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold">
                            <button type="button" wire:click="sort('email_verified_at')" class="inline-flex items-center gap-1">
                                <?php echo e(__('Verified')); ?> <span class="text-xs"><?php echo e($sortBy === 'email_verified_at' ? ($sortDir === 'asc' ? '^' : 'v') : '<>'); ?></span>
                            </button>
                        </th>
                        <th class="px-3 py-2 text-center font-semibold">
                            <button type="button" wire:click="sort('created_at')" class="inline-flex items-center gap-1">
                                <?php echo e(__('Created')); ?> <span class="text-xs"><?php echo e($sortBy === 'created_at' ? ($sortDir === 'asc' ? '^' : 'v') : '<>'); ?></span>
                            </button>
                        </th>
                        <th class="px-3 py-2 text-right font-semibold"><?php echo e(__('admin.common.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $displayRole = $row->roles->reject(fn ($role) => $role->name === 'customer')->sortBy('id')->first();
                            $roleName = $displayRole?->name ?? 'admin';
                            $roleTitle = $displayRole?->title ?? ucfirst($roleName);
                            $isCurrent = auth()->id() === $row->id;
                        ?>
                        <tr>
                            <td class="px-3 py-2 text-center font-mono text-xs text-slate-700"><?php echo e($row->id); ?></td>
                            <td class="px-3 py-2 text-slate-800">
                                <div class="font-medium"><?php echo e($row->name); ?></div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCurrent): ?>
                                    <div class="text-xs text-cyan-700"><?php echo e(__('Current user')); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-3 py-2 text-slate-700"><?php echo e($row->email); ?></td>
                            <td class="px-3 py-2 text-center">
                                <span class="rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700"><?php echo e($roleTitle); ?></span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($row->email_verified_at ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'); ?>">
                                    <?php echo e($row->email_verified_at ? __('admin.common.yes') : __('admin.common.no')); ?>

                                </span>
                            </td>
                            <td class="px-3 py-2 text-center text-slate-600"><?php echo e(optional($row->created_at)->format('Y-m-d')); ?></td>
                            <td class="px-3 py-2 text-right">
                                <a href="<?php echo e(route('admin.users.edit', ['user' => $row->id])); ?>" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                    <?php echo e(__('admin.common.edit')); ?>

                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-sm text-slate-500"><?php echo e(__('No admin users found.')); ?></td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($rows->links()); ?>

        </div>
    </div>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/user/manager.blade.php ENDPATH**/ ?>