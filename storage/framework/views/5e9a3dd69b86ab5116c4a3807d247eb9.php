<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight"><?php echo e(__('Categories')); ?></h1>
                <p class="mt-1 text-sm text-slate-600"><?php echo e(__('Lazy tree view with root pagination for blog and page categories.')); ?></p>
            </div>

            <div class="grid w-[64rem] max-w-full items-end gap-3" style="grid-template-columns: minmax(34rem, 1fr) 9rem 7rem;">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.common.search')); ?></label>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="<?php echo e(__('Code, name or slug...')); ?>" class="admin-search-input w-full rounded-xl border px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Scope')); ?></label>
                    <select wire:model.live="scope" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border px-3 py-2 text-sm">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->scopeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scopeOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $scopeLabel = match ($scopeOption) {
                                    'blog' => __('Blog'),
                                    'page' => __('Pages'),
                                    default => str($scopeOption)->replace('_', ' ')->title()->toString(),
                                };
                            ?>
                            <option value="<?php echo e($scopeOption); ?>"><?php echo e($scopeLabel); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.common.locale')); ?></label>
                    <select wire:model.live="locale" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border px-3 py-2 text-sm lowercase">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->localeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($localeOption); ?>"><?php echo e($localeOption); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-2">
            <a href="<?php echo e(route('admin.categories.create', ['scope' => $scope, 'locale' => $locale])); ?>" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                <?php echo e(__('Create Category')); ?>

            </a>
            <span class="admin-chip"><?php echo e($isSearchMode ? __('Search mode') : __('Tree mode')); ?></span>
            <span class="admin-chip"><?php echo e(__('Items per page:')); ?> <?php echo e($paginator->perPage()); ?></span>
            <span class="admin-chip">
                <?php echo e(__('Scope:')); ?>

                <?php echo e(match ($scope) {
                    'blog' => __('Blog'),
                    'page' => __('Pages'),
                    default => str($scope)->replace('_', ' ')->title()->toString(),
                }); ?>

            </span>
            <span class="admin-chip"><?php echo e(__('Locale:')); ?> <?php echo e($locale); ?></span>
        </div>
    </div>

    <div class="admin-panel admin-panel-soft p-5">
        <h2 class="admin-section-title"><?php echo e(__('admin.common.items')); ?></h2>

        <div class="mt-4 overflow-x-auto">
            <table class="admin-items-table min-w-full text-sm">
                <thead class="text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Category')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Slug')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('Depth')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('admin.common.sort')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('admin.common.state')); ?></th>
                        <th class="px-3 py-2 text-right font-semibold"><?php echo e(__('admin.common.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            /** @var \App\Models\Catalog\Category $node */
                            $node = $row['node'];
                            $translation = $node->translations->first();
                            $depth = (int) ($row['depth'] ?? 0);
                            $indent = $depth * 18;
                            $hasChildren = (bool) ($row['hasChildren'] ?? false);
                            $expanded = (bool) ($row['isExpanded'] ?? false);
                        ?>
                        <tr wire:key="category-tree-row-<?php echo e($node->id); ?>">
                            <td class="px-3 py-2 text-slate-800">
                                <div class="flex items-center gap-2" style="padding-left: <?php echo e($indent); ?>px;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasChildren): ?>
                                        <button type="button" wire:click="toggleExpand(<?php echo e($node->id); ?>)" class="inline-flex h-5 w-5 items-center justify-center rounded border border-slate-300 text-xs text-slate-600 hover:bg-slate-100">
                                            <?php echo e($expanded ? '−' : '+'); ?>

                                        </button>
                                    <?php else: ?>
                                        <span class="inline-block h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <span class="font-medium"><?php echo e($translation?->name ?? __('(missing name)')); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($node->code): ?>
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600"><?php echo e($node->code); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </td>
                            <td class="px-3 py-2 font-mono text-xs text-slate-700"><?php echo e($translation?->slug ?? '-'); ?></td>
                            <td class="px-3 py-2 text-center text-slate-700"><?php echo e($depth); ?></td>
                            <td class="px-3 py-2 text-center text-slate-700"><?php echo e($node->sort_order); ?></td>
                            <td class="px-3 py-2 text-center">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($node->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700'); ?>">
                                    <?php echo e($node->is_active ? __('admin.common.active') : __('admin.common.inactive')); ?>

                                </span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <button type="button" wire:click="moveUp(<?php echo e($node->id); ?>)" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('Up')); ?></button>
                                    <button type="button" wire:click="moveDown(<?php echo e($node->id); ?>)" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('Down')); ?></button>
                                    <a href="<?php echo e(route('admin.categories.edit', ['category' => $node->id, 'scope' => $scope, 'locale' => $locale])); ?>" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('admin.common.edit')); ?></a>
                                    <button type="button" wire:click="delete(<?php echo e($node->id); ?>)" wire:confirm="<?php echo e(__('Delete this category?')); ?>" class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"><?php echo e(__('admin.common.delete')); ?></button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-sm text-slate-500"><?php echo e(__('No categories found.')); ?></td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($paginator->links()); ?>

        </div>
    </div>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/catalog/category/tree.blade.php ENDPATH**/ ?>