<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <h1 class="text-xl font-semibold tracking-tight"><?php echo e(__('Catalog Features')); ?></h1>
        <p class="mt-2 text-sm text-slate-600"><?php echo e(__('Settings namespace:')); ?> <code>Settings/System/CatalogFeatures</code></p>
        <p class="mt-2 text-xs text-slate-500"><?php echo e(__('Disable features you do not use to keep admin/front queries lighter.')); ?></p>
    </div>

    <div class="admin-panel admin-form-panel p-6">
        <form wire:submit="save" class="admin-form mt-1 space-y-4">
            <?php
                $items = [
                    'catalog_use_api' => [
                        'title' => __('Use Wholesale API'),
                        'description' => __('Enable API settings page and `/api/v1/wholesale/*` endpoints.'),
                    ],
                    'catalog_use_blog' => [
                        'title' => __('Use Blog'),
                        'description' => __('Enable blog module in Content section and related front routes.'),
                    ],
                    'catalog_use_attributes' => [
                        'title' => __('Use Attributes'),
                        'description' => __('Additional product characteristics (e.g. material, power, weight).'),
                    ],
                    'catalog_use_options' => [
                        'title' => __('Use Options'),
                        'description' => __('Selectable values like size/color shown in product page/cart.'),
                    ],
                    'catalog_use_manufacturers' => [
                        'title' => __('Use Manufacturers'),
                        'description' => __('Brand/manufacturer relations and listings.'),
                    ],
                    'catalog_use_actions' => [
                        'title' => __('Use Actions & Discounts'),
                        'description' => __('Promotions: percentages, fixed discounts, and advanced action rules.'),
                    ],
                    'catalog_use_mobile_pwa' => [
                        'title' => __('Use Mobile PWA View'),
                        'description' => __('When disabled, mobile devices will use desktop responsive storefront instead of mobile/PWA templates.'),
                    ],
                ];
            ?>

            <div>
                <p class="admin-section-title"><?php echo e(__('Product Data Model Features')); ?></p>
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $enabled = (bool) ($form[$key] ?? false); ?>
                        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <strong class="block text-slate-900"><?php echo e($item['title']); ?></strong>
                                    <p class="mt-1 text-sm text-slate-600"><?php echo e($item['description']); ?></p>
                                </div>
                                <button
                                    type="button"
                                    wire:click="toggle('<?php echo e($key); ?>')"
                                    class="admin-switch"
                                    data-state="<?php echo e($enabled ? 'on' : 'off'); ?>"
                                    role="switch"
                                    aria-checked="<?php echo e($enabled ? 'true' : 'false'); ?>"
                                    aria-label="<?php echo e($item['title']); ?>"
                                >
                                    <span class="admin-switch-track">
                                        <span class="admin-switch-thumb"></span>
                                    </span>
                                    <span class="admin-switch-label"><?php echo e($enabled ? __('On') : __('Off')); ?></span>
                                </button>
                            </div>
                            <p class="mt-2 text-xs font-semibold uppercase tracking-[0.12em] <?php echo e($enabled ? 'text-emerald-700' : 'text-slate-500'); ?>">
                                <?php echo e($enabled ? __('Enabled') : __('Disabled')); ?>

                            </p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-600">
                <?php echo e(__('Recommendation: keep')); ?> <code><?php echo e(__('Options')); ?></code> <?php echo e(__('off unless needed.')); ?>

                <?php echo e(__('Enable it when product SKU/price/stock depends on selected option values.')); ?>

            </div>

            <div class="admin-form-actions flex items-center gap-2">
                <button type="submit" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800"><?php echo e(__('admin.common.save')); ?></button>
                <button type="button" wire:click="resetToDefaults" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('Reset Defaults')); ?></button>
            </div>
        </form>
    </div>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/settings/system/catalog-features.blade.php ENDPATH**/ ?>