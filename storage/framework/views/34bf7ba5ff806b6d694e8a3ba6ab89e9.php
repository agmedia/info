<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight"><?php echo e(__('admin.content.resources.manager.title')); ?></h1>
                <p class="mt-1 text-sm text-slate-600"><?php echo e(__('admin.content.resources.manager.subtitle')); ?></p>
                <p class="mt-2 text-xs text-slate-500"><?php echo e(__('admin.content.resources.manager.items_per_page')); ?>: <span class="admin-chip"><?php echo e($perPage); ?></span></p>
            </div>

            <div class="flex w-[72rem] max-w-full items-end justify-end gap-3">
                <div class="grid w-full max-w-[64rem] items-end gap-3" style="grid-template-columns: minmax(22rem, 1fr) 10rem 12rem;">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.common.search')); ?></label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="<?php echo e(__('admin.content.resources.manager.search_placeholder')); ?>"
                            class="admin-search-input w-full rounded-xl border px-3 py-2 text-sm"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.common.locale')); ?></label>
                        <select wire:model.live="locale" data-tom-select data-tom-no-search="1" class="admin-search-input admin-select w-full rounded-xl border px-3 py-2 text-sm lowercase">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $adminLocaleOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($localeOption); ?>"><?php echo e($localeOption); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.content.resources.manager.group')); ?></label>
                        <select wire:model.live="groupCode" data-tom-select data-tom-no-search="1" class="admin-search-input admin-select w-full rounded-xl border px-3 py-2 text-sm">
                            <option value=""><?php echo e(__('admin.content.resources.manager.all_groups')); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $groupLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($code); ?>"><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                </div>
                <a href="<?php echo e(route('admin.content.resources.create', ['locale' => $locale])); ?>" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                    <?php echo e(__('admin.common.create')); ?>

                </a>
            </div>
        </div>
    </div>

    <div class="admin-panel admin-panel-soft p-5">
        <h2 class="admin-section-title"><?php echo e(__('admin.common.items')); ?></h2>

        <div class="mt-4 overflow-x-auto">
            <table class="admin-items-table min-w-full text-sm">
                <thead class="text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('admin.content.resources.manager.table.preview')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('admin.content.resources.manager.table.document')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('admin.content.resources.manager.table.slug')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('admin.content.resources.manager.table.group')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('admin.content.resources.manager.table.file')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('admin.content.resources.manager.table.state')); ?></th>
                        <th class="px-3 py-2 text-right font-semibold"><?php echo e(__('admin.content.resources.manager.table.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $tr = $row->translations->first();
                            $downloadPath = parse_url((string) $row->download_url, PHP_URL_PATH) ?: '';
                            $extension = strtoupper((string) pathinfo($downloadPath, PATHINFO_EXTENSION));
                        ?>
                        <tr>
                            <td class="px-3 py-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->cover_image_url): ?>
                                    <img src="<?php echo e($row->cover_image_url); ?>" alt="<?php echo e($tr?->title ?? $row->code); ?>" class="h-16 w-12 rounded-lg object-cover" />
                                <?php else: ?>
                                    <div class="flex h-16 w-12 items-center justify-center rounded-lg border border-dashed border-slate-300 text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">
                                        <?php echo e(__('admin.common.no_image')); ?>

                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-3 py-2 text-slate-800">
                                <div class="font-medium"><?php echo e($tr?->title ?? __('admin.content.resources.manager.missing_title')); ?></div>
                                <div class="text-xs text-slate-500"><?php echo e($row->code); ?></div>
                            </td>
                            <td class="px-3 py-2 font-mono text-xs text-slate-700"><?php echo e($tr?->slug ?? '-'); ?></td>
                            <td class="px-3 py-2 text-center text-slate-700"><?php echo e($groupLabels[$row->group_code] ?? $row->group_code); ?></td>
                            <td class="px-3 py-2 text-center text-slate-700">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->download_url): ?>
                                    <div class="font-semibold"><?php echo e($extension !== '' ? $extension : 'FILE'); ?></div>
                                    <div class="text-xs text-slate-500"><?php echo e(\Illuminate\Support\Str::limit(\Illuminate\Support\Str::afterLast($downloadPath, '/'), 38)); ?></div>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400">-</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($row->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700'); ?>">
                                    <?php echo e($row->is_active ? __('admin.common.active') : __('admin.common.inactive')); ?>

                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <div class="flex justify-end gap-2">
                                    <a href="<?php echo e(route('admin.content.resources.edit', ['document' => $row->id, 'locale' => $locale])); ?>" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                        <?php echo e(__('admin.common.edit')); ?>

                                    </a>
                                    <button
                                        type="button"
                                        wire:click="delete(<?php echo e((int) $row->id); ?>)"
                                        wire:confirm="<?php echo e(__('admin.content.resources.manager.confirm_delete', ['name' => $tr?->title ?? $row->code])); ?>"
                                        class="rounded-lg border border-rose-300 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"
                                    >
                                        <?php echo e(__('admin.common.delete')); ?>

                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-sm text-slate-500"><?php echo e(__('admin.content.resources.manager.empty')); ?></td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($rows->links()); ?>

        </div>
    </div>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/content/resource/manager.blade.php ENDPATH**/ ?>