<div class="admin-panel admin-form-panel p-6">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="admin-section-title"><?php echo e(__('Images')); ?></p>
            <p class="mt-1 text-sm text-slate-600"><?php echo e(__('Manage main, banner/icon and gallery images with per-locale alt/caption metadata.')); ?></p>
        </div>
        <span class="admin-chip"><?php echo e(__('Locale:')); ?> <?php echo e($locale); ?></span>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $recordExists): ?>
        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-600">
            <?php echo e(__('Save this record first, then upload and organize images.')); ?>

        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $collections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $collectionName => $collectionConfig): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $collectionMedia = $mediaByCollection[$collectionName] ?? collect();
                    $isSingle = (bool) ($collectionConfig['single_file'] ?? false);
                    $acceptMime = (array) ($collectionConfig['accept_mime_types'] ?? []);
                    $maxUploadKb = max(1, (int) ($collectionConfig['max_upload_kb'] ?? 8192));
                    $previewConversion = (string) ($collectionConfig['preview_conversion'] ?? '');
                    $mainCollection = (string) ($modelProfile['main_collection'] ?? '');
                    $isMainCollection = $mainCollection !== '' && $mainCollection === $collectionName;
                ?>

                <section class="rounded-xl border border-slate-200 bg-white p-4">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-sm font-semibold text-slate-800"><?php echo e($collectionConfig['label'] ?? $collectionName); ?></h3>
                            <span class="admin-chip"><?php echo e($collectionName); ?></span>
                            <span class="admin-chip"><?php echo e($collectionMedia->count()); ?> <?php echo e($collectionMedia->count() === 1 ? __('image') : __('images')); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isMainCollection): ?>
                                <span class="admin-chip"><?php echo e(__('Main')); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <div class="grid gap-3 lg:grid-cols-[1fr_auto]">
                        <div>
                            <input
                                type="file"
                                wire:model="uploads.<?php echo e($collectionName); ?>"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-200"
                                accept="<?php echo e(implode(',', $acceptMime)); ?>"
                                <?php if(! $isSingle): ?> multiple <?php endif; ?>
                            />
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["uploads.$collectionName"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["uploads.$collectionName.*"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <p class="mt-1 text-xs text-slate-500">
                                <?php echo e(__('Max upload:')); ?> <?php echo e(number_format($maxUploadKb / 1024, 1)); ?> MB
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($acceptMime !== []): ?>
                                    | <?php echo e(implode(', ', $acceptMime)); ?>

                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                        </div>
                        <div class="flex items-start">
                            <button
                                type="button"
                                wire:click="uploadCollection('<?php echo e($collectionName); ?>')"
                                class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800"
                            >
                                <?php echo e(__('Upload')); ?>

                            </button>
                        </div>
                    </div>

                    <div class="mt-4 overflow-x-auto">
                        <table class="admin-items-table min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-3 py-2 text-left"><?php echo e(__('Preview')); ?></th>
                                    <th class="px-3 py-2 text-left"><?php echo e(__('Meta')); ?></th>
                                    <th class="px-3 py-2 text-center"><?php echo e(__('Sort')); ?></th>
                                    <th class="px-3 py-2 text-right"><?php echo e(__('Actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $collectionMedia; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $media): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $mediaMeta = (array) ($meta[$media->id] ?? []);
                                        $focalX = (float) ($mediaMeta['focal_x'] ?? 50);
                                        $focalY = (float) ($mediaMeta['focal_y'] ?? 50);
                                        $cropEnabled = (bool) ($mediaMeta['crop_enabled'] ?? false);
                                        $cropX = (float) ($mediaMeta['crop_x'] ?? 0);
                                        $cropY = (float) ($mediaMeta['crop_y'] ?? 0);
                                        $cropWidth = (float) ($mediaMeta['crop_width'] ?? 100);
                                        $cropHeight = (float) ($mediaMeta['crop_height'] ?? 100);
                                        $previewUrl = $previewConversion !== '' && $media->hasGeneratedConversion($previewConversion)
                                            ? $media->getUrl($previewConversion)
                                            : $media->getUrl();
                                    ?>
                                    <tr wire:key="media-<?php echo e($collectionName); ?>-<?php echo e($media->id); ?>">
                                        <td class="px-3 py-3 align-top">
                                            <img src="<?php echo e($previewUrl); ?>" alt="" class="h-20 w-28 rounded-lg border border-slate-200 bg-slate-100 object-cover" />
                                            <p class="mt-1 text-[11px] text-slate-500"><?php echo e($media->file_name); ?></p>
                                        </td>
                                        <td class="px-3 py-3 align-top">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isDualImageCtaBlock && $collectionName === 'block_slides'): ?>
                                                <div class="grid gap-2 md:grid-cols-2">
                                                    <input type="text" wire:model.defer="meta.<?php echo e($media->id); ?>.block_title" class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs md:col-span-2" placeholder="<?php echo e(__('Block title')); ?> (<?php echo e($locale); ?>)" />
                                                    <input type="text" wire:model.defer="meta.<?php echo e($media->id); ?>.cta_1_label" class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs" placeholder="<?php echo e(__('CTA 1 label')); ?> (<?php echo e($locale); ?>)" />
                                                    <input type="text" wire:model.defer="meta.<?php echo e($media->id); ?>.cta_1_url" class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs" placeholder="<?php echo e(__('CTA 1 URL')); ?> (<?php echo e($locale); ?>)" />
                                                    <input type="text" wire:model.defer="meta.<?php echo e($media->id); ?>.cta_2_label" class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs" placeholder="<?php echo e(__('CTA 2 label')); ?> (<?php echo e($locale); ?>)" />
                                                    <input type="text" wire:model.defer="meta.<?php echo e($media->id); ?>.cta_2_url" class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs" placeholder="<?php echo e(__('CTA 2 URL')); ?> (<?php echo e($locale); ?>)" />
                                                </div>
                                            <?php else: ?>
                                                <div class="grid gap-2 md:grid-cols-3">
                                                    <input type="text" wire:model.defer="meta.<?php echo e($media->id); ?>.name" class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs" placeholder="<?php echo e(__('Name')); ?>" />
                                                    <input type="text" wire:model.defer="meta.<?php echo e($media->id); ?>.alt" class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs" placeholder="<?php echo e(__('Alt')); ?> (<?php echo e($locale); ?>)" />
                                                    <input type="text" wire:model.defer="meta.<?php echo e($media->id); ?>.caption" class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs" placeholder="<?php echo e(__('Caption')); ?> (<?php echo e($locale); ?>)" />
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isLinkableSliderBlock && $collectionName === 'block_slides'): ?>
                                                        <input type="text" wire:model.defer="meta.<?php echo e($media->id); ?>.link_url" class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs" placeholder="<?php echo e(__('Link URL')); ?> (<?php echo e($locale); ?>)" />
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <p class="mt-1 text-[11px] text-slate-500">
                                                <?php echo e(number_format($media->size / 1024, 0)); ?> KB
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($media->width && $media->height): ?>
                                                    | <?php echo e($media->width); ?>x<?php echo e($media->height); ?>

                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </p>
                                            <div class="mt-2 flex flex-wrap items-center gap-1 text-[11px]">
                                                <span class="admin-chip"><?php echo e(__('Focal:')); ?> <?php echo e(number_format($focalX, 1)); ?> / <?php echo e(number_format($focalY, 1)); ?></span>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cropEnabled): ?>
                                                    <span class="admin-chip"><?php echo e(__('Crop:')); ?> <?php echo e(number_format($cropX, 1)); ?>,<?php echo e(number_format($cropY, 1)); ?> / <?php echo e(number_format($cropWidth, 1)); ?>x<?php echo e(number_format($cropHeight, 1)); ?></span>
                                                <?php else: ?>
                                                    <span class="admin-chip"><?php echo e(__('Crop: Off')); ?></span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 align-top text-center">
                                            <div class="inline-flex items-center gap-1">
                                                <button type="button" wire:click="moveUp(<?php echo e($media->id); ?>)" class="rounded border border-slate-300 px-2 py-1 text-xs text-slate-700 hover:bg-slate-100">↑</button>
                                                <button type="button" wire:click="moveDown(<?php echo e($media->id); ?>)" class="rounded border border-slate-300 px-2 py-1 text-xs text-slate-700 hover:bg-slate-100">↓</button>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 align-top">
                                            <div class="flex flex-wrap justify-end gap-1">
                                                <button type="button" wire:click="saveMeta(<?php echo e($media->id); ?>)" class="rounded border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('Save Meta')); ?></button>
                                                <button
                                                    type="button"
                                                    data-image-edit-open
                                                    data-media-id="<?php echo e($media->id); ?>"
                                                    data-image-url="<?php echo e($media->getUrl()); ?>"
                                                    data-focal-x="<?php echo e($focalX); ?>"
                                                    data-focal-y="<?php echo e($focalY); ?>"
                                                    data-crop-enabled="<?php echo e($cropEnabled ? '1' : '0'); ?>"
                                                    data-crop-x="<?php echo e($cropX); ?>"
                                                    data-crop-y="<?php echo e($cropY); ?>"
                                                    data-crop-width="<?php echo e($cropWidth); ?>"
                                                    data-crop-height="<?php echo e($cropHeight); ?>"
                                                    class="rounded border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100"
                                                >
                                                    <?php echo e(__('Edit Crop/Focus')); ?>

                                                </button>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mainCollection !== '' && ! $isMainCollection): ?>
                                                    <button type="button" wire:click="copyToMain(<?php echo e($media->id); ?>)" class="rounded border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('Copy to Main')); ?></button>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <a href="<?php echo e($media->getUrl()); ?>" target="_blank" class="rounded border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('Open')); ?></a>
                                                <button
                                                    type="button"
                                                    wire:click="delete(<?php echo e($media->id); ?>)"
                                                    wire:confirm="<?php echo e(__('Delete this image?')); ?>"
                                                    class="rounded border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50"
                                                >
                                                    <?php echo e(__('Delete')); ?>

                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="px-3 py-5 text-center text-sm text-slate-500">
                                            <?php echo e(__('No images in this collection yet.')); ?>

                                        </td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/media/manager.blade.php ENDPATH**/ ?>