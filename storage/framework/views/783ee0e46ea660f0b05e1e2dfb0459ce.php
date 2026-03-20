<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold tracking-tight"><?php echo e(__('Content Blocks')); ?></h1>
                <p class="mt-1 text-sm text-slate-600"><?php echo e(__('Unified builder: block, primary slot, selected items, and per-block Blade template.')); ?></p>
                <p class="mt-2 text-xs text-slate-500"><?php echo e(__('Items per page')); ?>: <span class="admin-chip"><?php echo e($perPage); ?></span></p>
            </div>
            <div class="flex w-full gap-2 sm:w-auto sm:items-end">
                <div class="w-full sm:w-80">
                    <label for="content-block-search" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Search')); ?></label>
                    <input
                        id="content-block-search"
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="<?php echo e(__('Code, name or type...')); ?>"
                        class="admin-search-input w-full rounded-xl border px-3 py-2 text-sm"
                    />
                </div>
                <div class="w-full sm:w-44">
                    <label for="content-block-surface" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Surface')); ?></label>
                    <select id="content-block-surface" wire:model="surface" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border px-3 py-2 text-sm">
                        <option value="all"><?php echo e(__('All')); ?></option>
                        <option value="desktop"><?php echo e(__('Desktop')); ?></option>
                        <option value="mobile"><?php echo e(__('Mobile')); ?></option>
                    </select>
                </div>
                <a href="<?php echo e(route('admin.content.blocks.create')); ?>" class="inline-flex h-10 items-center rounded-xl bg-cyan-700 px-4 text-sm font-semibold text-white hover:bg-cyan-800"><?php echo e(__('Create')); ?></a>
            </div>
        </div>
    </div>

    <div class="admin-panel admin-panel-soft p-5">
        <h2 class="admin-section-title"><?php echo e(__('Items')); ?></h2>

        <div class="mt-4 overflow-x-auto">
            <table class="admin-items-table min-w-full text-sm">
                <thead class="text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Code')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Name')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Type')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Placement')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Surface')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('Preview')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('Items')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('Slots')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('State')); ?></th>
                        <th class="px-3 py-2 text-right font-semibold"><?php echo e(__('Actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $title = $row->translations->first()?->title;
                            $primarySlot = $row->slots->first();
                        ?>
                        <tr>
                            <td class="px-3 py-2 font-mono text-xs text-slate-700"><?php echo e($row->code); ?></td>
                            <td class="px-3 py-2 text-slate-800">
                                <div class="font-medium"><?php echo e($row->name); ?></div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($title): ?>
                                    <div class="text-xs text-slate-500"><?php echo e($title); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-3 py-2 text-slate-700"><?php echo e(config('content_blocks.types.'.$row->type, $row->type)); ?></td>
                            <td class="px-3 py-2 text-xs text-slate-600">
                                <div><?php echo e($primarySlot?->placement ?: __('n/a')); ?></div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($primarySlot?->target_type)): ?>
                                    <div class="mt-1 text-slate-500"><?php echo e($primarySlot->target_type); ?>: <?php echo e($primarySlot->target_ref ?: '*'); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-3 py-2 text-xs text-slate-700">
                                <?php
                                    $surface = (string) ($primarySlot?->frontend_variant ?? 'all');
                                ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($surface === 'desktop'): ?>
                                    <?php echo e(__('Desktop')); ?>

                                <?php elseif($surface === 'mobile'): ?>
                                    <?php echo e(__('Mobile')); ?>

                                <?php else: ?>
                                    <?php echo e(__('All')); ?>

                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-3 py-2">
                                <?php echo $__env->make('admin.content.partials.block-type-preview', ['type' => $row->type, 'size' => 'xs'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            </td>
                            <td class="px-3 py-2 text-center text-slate-700"><?php echo e($row->items_count); ?></td>
                            <td class="px-3 py-2 text-center text-slate-700"><?php echo e($row->slots_count); ?></td>
                            <td class="px-3 py-2 text-center">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($row->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700'); ?>">
                                    <?php echo e($row->is_active ? __('Active') : __('Inactive')); ?>

                                </span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <button type="button" wire:click="openPreview(<?php echo e($row->id); ?>)" class="rounded-lg border border-cyan-200 px-2 py-1 text-xs font-semibold text-cyan-800 hover:bg-cyan-50"><?php echo e(__('Preview')); ?></button>
                                    <a href="<?php echo e(route('admin.content.blocks.edit', ['block' => $row->id])); ?>" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('Edit')); ?></a>
                                    <button type="button" wire:click="delete(<?php echo e($row->id); ?>)" wire:confirm="<?php echo e(__('Delete this content block?')); ?>" class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"><?php echo e(__('Delete')); ?></button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="10" class="px-3 py-8 text-center text-sm text-slate-500"><?php echo e(__('No content blocks yet.')); ?></td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($rows->links()); ?>

        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($previewBlock): ?>
        <?php
            $previewTranslation = $previewBlock->translations->firstWhere('locale', $locale)
                ?? $previewBlock->translations->firstWhere('locale', config('app.locale'));
            $previewPlacement = (string) ($previewBlock->slots->first()?->placement ?? 'home.hero');
            $previewVariant = (string) ($previewBlock->slots->first()?->frontend_variant ?? 'all');
            $frontVariant = in_array($previewVariant, ['desktop', 'mobile'], true) ? $previewVariant : 'desktop';
            $frontPreviewUrl = route('home', [
                'preview_block' => $previewBlock->id,
                'preview_placement' => $previewPlacement,
                'frontend_variant' => $frontVariant,
            ]);
        ?>
        <div wire:click="closePreview" class="fixed inset-0 z-[72] bg-slate-900/45 p-4 md:p-6">
            <div wire:click.stop class="mx-auto flex h-full max-h-[92vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
                <div class="flex items-start justify-between gap-3 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-cyan-50 px-5 py-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Block Preview')); ?></p>
                        <h3 class="mt-1 text-lg font-semibold tracking-tight text-slate-900"><?php echo e($previewBlock->name); ?></h3>
                        <p class="mt-1 text-xs text-slate-500">
                            <?php echo e(__('Code:')); ?> <span class="font-mono"><?php echo e($previewBlock->code); ?></span>
                            <span class="mx-1.5">|</span>
                            <?php echo e(__('Type:')); ?> <?php echo e(config('content_blocks.types.'.$previewBlock->type, $previewBlock->type)); ?>

                            <span class="mx-1.5">|</span>
                            <?php echo e(__('Slots:')); ?> <?php echo e($previewBlock->slots_count); ?>

                        </p>
                    </div>
                    <div class="inline-flex items-center gap-2">
                        <a href="<?php echo e($frontPreviewUrl); ?>" target="_blank" rel="noopener" class="rounded-lg border border-cyan-200 bg-cyan-50 px-3 py-1.5 text-xs font-semibold text-cyan-800 hover:bg-cyan-100">
                            <?php echo e(__('Open Front')); ?>

                        </a>
                        <button type="button" wire:click="closePreview" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('Close')); ?></button>
                    </div>
                </div>

                <div class="grid flex-1 gap-4 overflow-y-auto p-5 lg:grid-cols-[18rem_1fr]">
                    <div class="space-y-3">
                        <?php echo $__env->make('admin.content.partials.block-type-preview', ['type' => $previewBlock->type, 'size' => 'md'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-600">
                            <p>
                                <?php echo e(__('State:')); ?>

                                <span class="font-semibold <?php echo e($previewBlock->is_active ? 'text-emerald-700' : 'text-slate-500'); ?>">
                                    <?php echo e($previewBlock->is_active ? __('Active') : __('Inactive')); ?>

                                </span>
                            </p>
                            <p class="mt-1">
                                <?php echo e(__('Locale:')); ?>

                                <span class="font-semibold text-slate-700"><?php echo e($previewTranslation?->locale ?? __('n/a')); ?></span>
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="rounded-xl border border-slate-200 bg-white p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500"><?php echo e(__('Content Snapshot')); ?></p>
                            <h4 class="mt-2 text-base font-semibold text-slate-900"><?php echo e($previewTranslation?->title ?: __('(no title)')); ?></h4>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($previewTranslation?->subtitle)): ?>
                                <p class="mt-1 text-sm text-slate-600"><?php echo e($previewTranslation->subtitle); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($previewTranslation?->cta_label) || !empty($previewTranslation?->cta_url)): ?>
                                <p class="mt-2 text-xs text-slate-500"><?php echo e(__('CTA:')); ?> <?php echo e($previewTranslation?->cta_label ?: '-'); ?> <?php echo e(!empty($previewTranslation?->cta_url) ? '-> '.$previewTranslation->cta_url : ''); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-white p-4 text-xs text-slate-600">
                            <p><?php echo e(__('Placement:')); ?> <span class="font-semibold text-slate-800"><?php echo e($previewPlacement); ?></span></p>
                            <p class="mt-1"><?php echo e(__('Surface:')); ?> <span class="font-semibold text-slate-800"><?php echo e($frontVariant); ?></span></p>
                            <p class="mt-1"><?php echo e(__('Selected items:')); ?> <span class="font-semibold text-slate-800"><?php echo e($previewBlock->items_count); ?></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/content/block/index.blade.php ENDPATH**/ ?>