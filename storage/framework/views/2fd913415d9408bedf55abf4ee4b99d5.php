<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight"><?php echo e(__('admin.content.services.manager.title')); ?></h1>
                <p class="mt-1 text-sm text-slate-600"><?php echo e(__('admin.content.services.manager.subtitle')); ?></p>
                <p class="mt-2 text-xs text-slate-500"><?php echo e(__('admin.content.services.manager.items_per_page')); ?>: <span class="admin-chip"><?php echo e($perPage); ?></span></p>
            </div>

            <div class="flex w-[64rem] max-w-full items-end justify-end gap-3">
                <div class="grid w-full max-w-[56rem] items-end gap-3" style="grid-template-columns: minmax(26rem, 1fr) 8rem;">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.common.search')); ?></label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="<?php echo e(__('admin.content.services.manager.search_placeholder')); ?>"
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
                <a href="<?php echo e(route('admin.content.services.create', ['locale' => $locale])); ?>" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
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
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('admin.content.services.manager.table.service')); ?></th>
                        <th class="px-3 py-2 text-left font-semibold"><?php echo e(__('admin.content.services.manager.table.slug')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('admin.content.services.manager.table.template')); ?></th>
                        <th class="px-3 py-2 text-center font-semibold"><?php echo e(__('admin.content.services.manager.table.state')); ?></th>
                        <th class="px-3 py-2 text-right font-semibold"><?php echo e(__('admin.content.services.manager.table.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $tr = $row->translations->first();
                            $adminPage = (array) ($adminPageTree[$row->template_key] ?? []);
                            $childPages = (array) ($adminPage['children'] ?? []);
                            $editUrl = route('admin.content.services.edit', ['servicePage' => $row->id, 'locale' => $locale]);
                            $frontRoute = (string) ($adminPage['route'] ?? '');
                            $frontUrl = $frontRoute !== '' && \Illuminate\Support\Facades\Route::has($frontRoute) ? route($frontRoute) : '';
                            $isPrimaryService = in_array($row->template_key, $primaryServiceTemplateKeys ?? [], true);
                            $isServicesIndex = $row->template_key === \App\Support\Content\ServicePageTemplateRegistry::SERVICES_INDEX;
                        ?>
                        <tr>
                            <td class="px-3 py-2 text-slate-800">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-medium"><?php echo e($tr?->title ?? __('admin.content.services.manager.missing_title')); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isPrimaryService): ?>
                                        <span class="rounded-full bg-cyan-50 px-2 py-0.5 text-[0.68rem] font-semibold uppercase tracking-[0.12em] text-cyan-800"><?php echo e(__('Osnovna usluga')); ?></span>
                                    <?php elseif($isServicesIndex): ?>
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[0.68rem] font-semibold uppercase tracking-[0.12em] text-slate-600"><?php echo e(__('Landing')); ?></span>
                                    <?php else: ?>
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[0.68rem] font-semibold uppercase tracking-[0.12em] text-slate-600"><?php echo e(__('Front stranica')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                    <span><?php echo e($row->code); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($frontUrl !== ''): ?>
                                        <a href="<?php echo e($frontUrl); ?>" target="_blank" rel="noopener" class="font-semibold text-cyan-700 hover:text-cyan-900"><?php echo e(__('Front')); ?></a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </td>
                            <td class="px-3 py-2 font-mono text-xs text-slate-700"><?php echo e($tr?->slug ?? '-'); ?></td>
                            <td class="px-3 py-2 text-center text-slate-700"><?php echo e($templateLabels[$row->template_key] ?? $row->template_key); ?></td>
                            <td class="px-3 py-2 text-center">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($row->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700'); ?>">
                                    <?php echo e($row->is_active ? __('admin.common.active') : __('admin.common.inactive')); ?>

                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <div class="flex justify-end gap-2">
                                    <a href="<?php echo e($editUrl); ?>" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                        <?php echo e(__('admin.common.edit')); ?>

                                    </a>
                                    <button
                                        type="button"
                                        wire:click="delete(<?php echo e((int) $row->id); ?>)"
                                        wire:confirm="<?php echo e(__('admin.content.services.manager.confirm_delete', ['name' => $tr?->title ?? $row->code])); ?>"
                                        class="rounded-lg border border-rose-300 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"
                                    >
                                        <?php echo e(__('admin.common.delete')); ?>

                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $childPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $childPage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $childRoute = (string) ($childPage['route'] ?? '');
                                $childFrontUrl = $childRoute !== '' && \Illuminate\Support\Facades\Route::has($childRoute) ? route($childRoute) : '';
                                $childAnchor = (string) ($childPage['admin_anchor'] ?? '');
                                $childTemplateKey = (string) ($childPage['template_key'] ?? '');
                                $childTargetRow = $childTemplateKey !== '' ? $servicePagesByTemplate->get($childTemplateKey) : $row;
                                $childEditUrl = $childTargetRow
                                    ? route('admin.content.services.edit', ['servicePage' => $childTargetRow->id, 'locale' => $locale]).$childAnchor
                                    : '#';
                                $grandchildPages = (array) ($childPage['children'] ?? []);
                            ?>
                            <tr class="bg-slate-50/60">
                                <td class="px-3 py-2 text-slate-700">
                                    <div class="ml-4 border-l border-slate-200 pl-4">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-400"><?php echo e(__('Podstranica')); ?></span>
                                            <span class="font-medium"><?php echo e($childPage['title'] ?? ''); ?></span>
                                        </div>
                                        <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                            <span><?php echo e($tr?->title ?? ($templateLabels[$row->template_key] ?? $row->template_key)); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($childFrontUrl !== ''): ?>
                                                <a href="<?php echo e($childFrontUrl); ?>" target="_blank" rel="noopener" class="font-semibold text-cyan-700 hover:text-cyan-900"><?php echo e(__('Front')); ?></a>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2 font-mono text-xs text-slate-700"><?php echo e($childFrontUrl !== '' ? parse_url($childFrontUrl, PHP_URL_PATH) : '-'); ?></td>
                                <td class="px-3 py-2 text-center text-slate-700">
                                    <?php echo e($childTemplateKey !== '' ? ($templateLabels[$childTemplateKey] ?? $childTemplateKey) : __('Pod') . ' ' . ($templateLabels[$row->template_key] ?? $row->template_key)); ?>

                                </td>
                                <td class="px-3 py-2 text-center">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e(($childTargetRow?->is_active ?? $row->is_active) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700'); ?>">
                                        <?php echo e(($childTargetRow?->is_active ?? $row->is_active) ? __('admin.common.active') : __('admin.common.inactive')); ?>

                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex justify-end gap-2">
                                        <a href="<?php echo e($childEditUrl); ?>" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                            <?php echo e(__('admin.common.edit')); ?>

                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $grandchildPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grandchildPage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $grandchildRoute = (string) ($grandchildPage['route'] ?? '');
                                    $grandchildFrontUrl = $grandchildRoute !== '' && \Illuminate\Support\Facades\Route::has($grandchildRoute) ? route($grandchildRoute) : '';
                                    $grandchildAnchor = (string) ($grandchildPage['admin_anchor'] ?? '');
                                    $grandchildTemplateKey = (string) ($grandchildPage['template_key'] ?? '');
                                    $grandchildTargetRow = $grandchildTemplateKey !== '' ? $servicePagesByTemplate->get($grandchildTemplateKey) : $row;
                                    $grandchildEditUrl = $grandchildTargetRow
                                        ? route('admin.content.services.edit', ['servicePage' => $grandchildTargetRow->id, 'locale' => $locale]).$grandchildAnchor
                                        : '#';
                                ?>
                                <tr class="bg-slate-50/80">
                                    <td class="px-3 py-2 text-slate-700">
                                        <div class="ml-10 border-l border-slate-200 pl-4">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-400"><?php echo e(__('Podstranica')); ?></span>
                                                <span class="font-medium"><?php echo e($grandchildPage['title'] ?? ''); ?></span>
                                            </div>
                                            <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                                <span><?php echo e($childPage['title'] ?? ($tr?->title ?? '')); ?></span>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($grandchildFrontUrl !== ''): ?>
                                                    <a href="<?php echo e($grandchildFrontUrl); ?>" target="_blank" rel="noopener" class="font-semibold text-cyan-700 hover:text-cyan-900"><?php echo e(__('Front')); ?></a>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 font-mono text-xs text-slate-700"><?php echo e($grandchildFrontUrl !== '' ? parse_url($grandchildFrontUrl, PHP_URL_PATH) : '-'); ?></td>
                                    <td class="px-3 py-2 text-center text-slate-700">
                                        <?php echo e($grandchildTemplateKey !== '' ? ($templateLabels[$grandchildTemplateKey] ?? $grandchildTemplateKey) : __('Pod') . ' ' . ($templateLabels[$row->template_key] ?? $row->template_key)); ?>

                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e(($grandchildTargetRow?->is_active ?? $row->is_active) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700'); ?>">
                                            <?php echo e(($grandchildTargetRow?->is_active ?? $row->is_active) ? __('admin.common.active') : __('admin.common.inactive')); ?>

                                        </span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="flex justify-end gap-2">
                                            <a href="<?php echo e($grandchildEditUrl); ?>" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                <?php echo e(__('admin.common.edit')); ?>

                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-sm text-slate-500"><?php echo e(__('admin.content.services.manager.empty')); ?></td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($rows->links()); ?>

        </div>
    </div>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/content/service/manager.blade.php ENDPATH**/ ?>