<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight"><?php echo e(__('Call Posts')); ?></h1>
                <p class="mt-1 text-sm text-slate-600"><?php echo e(__('Manage EU fund calls with locale-specific slugs, categories, and SEO fields.')); ?></p>
                <p class="mt-2 text-xs text-slate-500"><?php echo e(__('Items per page')); ?>: <span class="admin-chip"><?php echo e($perPage); ?></span></p>
            </div>

            <div class="flex w-[64rem] max-w-full items-end justify-end gap-3">
                <div class="grid w-full max-w-[56rem] items-end gap-3" style="grid-template-columns: minmax(26rem, 1fr) 8rem;">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Search')); ?></label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="<?php echo e(__('Code, title or slug...')); ?>"
                            class="admin-search-input w-full rounded-xl border px-3 py-2 text-sm"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Locale')); ?></label>
                        <select wire:model.live="locale" data-tom-select data-tom-no-search="1" class="admin-search-input admin-select w-full rounded-xl border px-3 py-2 text-sm lowercase">
                              <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $adminLocaleOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($localeOption); ?>"><?php echo e($localeOption); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                </div>
                <a href="<?php echo e(route('admin.content.calls.create', ['locale' => $locale])); ?>" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                    <?php echo e(__('Create')); ?>

                </a>
            </div>
        </div>
    </div>

    <div class="admin-panel admin-panel-soft p-5">
        <h2 class="admin-section-title"><?php echo e(__('Items')); ?></h2>

        <div class="mt-4 overflow-x-auto">
            <table class="admin-items-table min-w-full text-sm">
                <thead class="text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Preview')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Post')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Slug')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('Published')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('Categories')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('Featured')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('State')); ?></th>
                        <th class="px-3 py-2 text-right font-semibold"><?php echo e(__('Actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $tr = $row->translations->first();
                            $cover = $row->media->firstWhere('collection_name', 'call_cover');
                            $coverUrl = $cover
                                ? ($cover->hasGeneratedConversion('thumb_100x100') ? $cover->getUrl('thumb_100x100') : $cover->getUrl())
                                : null;
                        ?>
                        <tr>
                            <td class="px-3 py-2 align-top">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($coverUrl): ?>
                                    <img src="<?php echo e($coverUrl); ?>" alt="" class="h-16 w-16 rounded-lg border border-slate-200 bg-slate-100 object-cover" />
                                <?php else: ?>
                                    <div class="flex h-16 w-16 items-center justify-center rounded-lg border border-dashed border-slate-300 bg-slate-50 text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                                        <?php echo e(__('admin.common.no_image')); ?>

                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-3 py-2 text-slate-800">
                                <div class="font-medium"><?php echo e($tr?->title ?? __('(missing title)')); ?></div>
                                <div class="text-xs text-slate-500"><?php echo e($row->code); ?></div>
                            </td>
                            <td class="px-3 py-2 font-mono text-xs text-slate-700"><?php echo e($tr?->slug ?? '-'); ?></td>
                            <td class="px-3 py-2 text-center text-slate-700"><?php echo e($row->published_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                            <td class="px-3 py-2 text-center text-slate-700"><?php echo e($row->categories_count); ?></td>
                            <td class="px-3 py-2 text-center">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($row->is_featured ? 'bg-cyan-100 text-cyan-800' : 'bg-slate-200 text-slate-700'); ?>">
                                    <?php echo e($row->is_featured ? __('Yes') : __('No')); ?>

                                </span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($row->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700'); ?>">
                                    <?php echo e($row->is_active ? __('Active') : __('Inactive')); ?>

                                </span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="<?php echo e(route('admin.content.calls.edit', ['callPost' => $row->id, 'locale' => $locale])); ?>" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                        <?php echo e(__('Edit')); ?>

                                    </a>
                                    <button
                                        type="button"
                                        wire:click="delete(<?php echo e($row->id); ?>)"
                                        wire:confirm="<?php echo e(__('Delete this call post?')); ?>"
                                        class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"
                                    >
                                        <?php echo e(__('Delete')); ?>

                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-3 py-8 text-center text-sm text-slate-500"><?php echo e(__('No call posts yet.')); ?></td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($rows->links()); ?>

        </div>
    </div>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/content/call/manager.blade.php ENDPATH**/ ?>