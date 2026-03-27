<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight"><?php echo e(__('admin.content.team.manager.title')); ?></h1>
                <p class="mt-1 text-sm text-slate-600"><?php echo e(__('admin.content.team.manager.subtitle')); ?></p>
                <p class="mt-2 text-xs text-slate-500"><?php echo e(__('admin.content.team.manager.items_per_page')); ?>: <span class="admin-chip"><?php echo e($perPage); ?></span></p>
            </div>

            <div class="flex w-[64rem] max-w-full items-end justify-end gap-3">
                <div class="grid w-full max-w-[56rem] items-end gap-3" style="grid-template-columns: minmax(26rem, 1fr) 8rem;">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.common.search')); ?></label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="<?php echo e(__('admin.content.team.manager.search_placeholder')); ?>"
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
                </div>
                <a href="<?php echo e(route('admin.content.team.create', ['locale' => $locale])); ?>" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
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
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('admin.content.team.manager.table.preview')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('admin.content.team.manager.table.member')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('admin.content.team.manager.table.departments')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('admin.content.team.manager.table.contact')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('admin.content.team.manager.table.sort')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('admin.content.team.manager.table.state')); ?></th>
                        <th class="px-3 py-2 text-right font-semibold"><?php echo e(__('admin.content.team.manager.table.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $tr = $row->translations->first();
                            $photo = $row->media->firstWhere('collection_name', 'team_photo');
                            $photoUrl = $photo
                                ? ($photo->hasGeneratedConversion('thumb_100x100') ? $photo->getUrl('thumb_100x100') : $photo->getUrl())
                                : null;
                        ?>
                        <tr>
                            <td class="px-3 py-2 align-top">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($photoUrl): ?>
                                    <img src="<?php echo e($photoUrl); ?>" alt="" class="h-16 w-16 rounded-lg border border-slate-200 bg-slate-100 object-cover" />
                                <?php else: ?>
                                    <div class="flex h-16 w-16 items-center justify-center rounded-lg border border-dashed border-slate-300 bg-slate-50 text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                                        <?php echo e(__('admin.common.no_image')); ?>

                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-3 py-2 text-slate-800">
                                <div class="font-medium"><?php echo e($tr?->name ?? __('admin.content.team.manager.missing_name')); ?></div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($tr?->position)): ?>
                                    <div class="text-xs text-slate-500"><?php echo e($tr?->position); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="mt-1 text-xs text-slate-500"><?php echo e($row->code); ?></div>
                            </td>
                            <td class="px-3 py-2 text-sm text-slate-700">
                                <?php echo e($tr?->departments ?: '-'); ?>

                            </td>
                            <td class="px-3 py-2 text-sm text-slate-700">
                                <div><?php echo e($row->email ?: '-'); ?></div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->mobile_phone): ?>
                                    <div class="mt-1 text-xs text-slate-500"><?php echo e($row->mobile_phone); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="mt-1 flex flex-wrap gap-1 text-[11px]">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->facebook_url): ?>
                                        <span class="admin-chip">Facebook</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->twitter_url): ?>
                                        <span class="admin-chip">X</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->linkedin_url): ?>
                                        <span class="admin-chip">LinkedIn</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-center text-slate-700"><?php echo e($row->sort_order); ?></td>
                            <td class="px-3 py-2 text-center">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($row->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700'); ?>">
                                    <?php echo e($row->is_active ? __('admin.common.active') : __('admin.common.inactive')); ?>

                                </span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="<?php echo e(route('admin.content.team.edit', ['member' => $row->id, 'locale' => $locale])); ?>" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                        <?php echo e(__('admin.common.edit')); ?>

                                    </a>
                                    <button
                                        type="button"
                                        wire:click="delete(<?php echo e($row->id); ?>)"
                                        wire:confirm="<?php echo e(__('admin.content.team.manager.confirm_delete', ['name' => $tr?->name ?? $row->code])); ?>"
                                        class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"
                                    >
                                        <?php echo e(__('admin.common.delete')); ?>

                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-sm text-slate-500"><?php echo e(__('admin.content.team.manager.empty')); ?></td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($rows->links()); ?>

        </div>
    </div>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/content/team/manager.blade.php ENDPATH**/ ?>