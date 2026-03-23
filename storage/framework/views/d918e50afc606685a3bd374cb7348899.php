<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500"><?php echo e(__('Content / Blog')); ?></p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900"><?php echo e($isEdit ? __('Edit Blog Post') : __('Create Blog Post')); ?></h1>
                <p class="mt-2 text-sm text-slate-600"><?php echo e(__('Core post data, locale content and SEO.')); ?></p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="admin-chip"><?php echo e(__('Locale:')); ?> <?php echo e($form['locale']); ?></span>
                <button type="button" wire:click="backToList" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('Back to List')); ?></button>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="admin-panel admin-form-panel p-3 sm:p-4">
            <div class="flex flex-wrap gap-2">
                <button type="button" wire:click="setTab('content')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] <?php echo e($activeTab === 'content' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100'); ?>">
                    <?php echo e(__('Sadržaj')); ?>

                </button>
                <button type="button" wire:click="setTab('seo')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] <?php echo e($activeTab === 'seo' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100'); ?>">
                    <?php echo e(__('SEO')); ?>

                </button>
                <button type="button" wire:click="setTab('media')" class="rounded-lg border px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] <?php echo e($activeTab === 'media' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100'); ?>">
                    <?php echo e(__('Media')); ?>

                </button>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'content'): ?>
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title"><?php echo e(__('Core Data')); ?></p>

                <div class="mt-4 grid gap-3" style="grid-template-columns: repeat(12, minmax(0, 1fr));">
                    <div class="sm:col-span-4" style="grid-column: span 4;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                        <input type="text" wire:model.live.debounce.250ms="form.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="sm:col-span-3" style="grid-column: span 3;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Code')); ?></label>
                        <input type="text" wire:model.live.debounce.250ms="form.code" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-mono lowercase" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="sm:col-span-3" style="grid-column: span 3;">
                        <div class="flex items-center justify-between">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Slug')); ?></label>
                            <button type="button" wire:click="generateSlug" class="text-xs font-semibold text-slate-600 hover:text-slate-900"><?php echo e(__('Generate')); ?></button>
                        </div>
                        <input type="text" wire:model.live.debounce.250ms="form.slug" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="sm:col-span-2" style="grid-column: span 2;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.common.locale')); ?></label>
                        <select wire:model.live="form.locale" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm lowercase">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $adminLocaleOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($localeOption); ?>"><?php echo e($localeOption); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.locale'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="mt-3 grid gap-3" style="grid-template-columns: repeat(12, minmax(0, 1fr));">
                    <div class="sm:col-span-3" style="grid-column: span 3;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Published At')); ?></label>
                        <input type="date" wire:model="form.published_at" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.published_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="sm:col-span-2" style="grid-column: span 2;">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Sort Order')); ?></label>
                        <input type="number" min="0" wire:model="form.sort_order" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="sm:col-span-7 flex items-end gap-3" style="grid-column: span 7;">
                        <button
                            type="button"
                            wire:click="$toggle('form.is_active')"
                            class="admin-switch"
                            data-state="<?php echo e($form['is_active'] ? 'on' : 'off'); ?>"
                            role="switch"
                            aria-checked="<?php echo e($form['is_active'] ? 'true' : 'false'); ?>"
                            aria-label="<?php echo e(__('Toggle blog post active state')); ?>"
                        >
                            <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                            <span class="admin-switch-label"><?php echo e($form['is_active'] ? __('admin.common.active') : __('admin.common.inactive')); ?></span>
                        </button>

                        <button
                            type="button"
                            wire:click="$toggle('form.is_featured')"
                            class="admin-switch"
                            data-state="<?php echo e($form['is_featured'] ? 'on' : 'off'); ?>"
                            role="switch"
                            aria-checked="<?php echo e($form['is_featured'] ? 'true' : 'false'); ?>"
                            aria-label="<?php echo e(__('Toggle featured state')); ?>"
                        >
                            <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                            <span class="admin-switch-label"><?php echo e($form['is_featured'] ? __('Featured') : __('Normal')); ?></span>
                        </button>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Excerpt')); ?></label>
                    <textarea rows="3" wire:model="form.excerpt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>

                <div class="mt-4" wire:key="blog-post-body-<?php echo e($postId ?? 'new'); ?>-<?php echo e($form['locale']); ?>">
                    <label for="blog-post-body-html" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Body')); ?></label>
                    <textarea id="blog-post-body-html" rows="14" wire:model.live.debounce.300ms="form.body_html" data-quill-editor data-quill-image-upload-url="<?php echo e(route('admin.content.blog.editor-image.upload')); ?>" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    <p class="mt-2 text-xs text-slate-500"><?php echo e(__('Image ikona u editoru podrzava upload direktno u clanak. Ako zelis promijeniti postojecu sliku, klikni nju u editoru pa opet odaberi image ikonu.')); ?></p>
                </div>

                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Smart Link')); ?></p>
                    <p class="mt-1 text-xs text-slate-500"><?php echo e(__('Odaberi tip i cilj, pa umetni link na trenutno označeni tekst u editoru.')); ?></p>
                    <div class="mt-3 grid gap-3 md:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Tip')); ?></label>
                            <select wire:model.live="linkType" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                <option value="category"><?php echo e(__('Page category')); ?></option>
                                <option value="blog"><?php echo e(__('Blog')); ?></option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Pretraga')); ?></label>
                            <input type="text" wire:model.live.debounce.250ms="linkSearch" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="<?php echo e(__('Upisi naziv ili slug...')); ?>">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Rezultat')); ?></label>
                            <select wire:model="linkTargetId" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                <option value=""><?php echo e(__('Odaberi')); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->linkTargetOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($row['id']); ?>"><?php echo e($row['label']); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($row['hint'])): ?> (<?php echo e($row['hint']); ?>) <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mt-2 flex flex-wrap items-center gap-3 text-xs">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->linkTargetOptions->count() > 0): ?>
                            <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 font-semibold text-emerald-700">
                                <?php echo e($this->linkTargetOptions->count()); ?> <?php echo e(__('rezultata')); ?>

                            </span>
                        <?php else: ?>
                            <span class="rounded-full border border-rose-200 bg-rose-50 px-2 py-0.5 font-semibold text-rose-700">
                                <?php echo e(__('Nema rezultata')); ?>

                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php
                            $selectedLinkRow = $this->linkTargetOptions->firstWhere('id', (int) ($linkTargetId ?? 0));
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedLinkRow): ?>
                            <span class="text-slate-600">
                                <?php echo e(__('Odabrano:')); ?> <strong><?php echo e($selectedLinkRow['label']); ?></strong>
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="mt-3">
                        <button type="button" wire:click="insertEditorLink" class="rounded-lg border border-slate-900 bg-slate-900 px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] text-white hover:bg-slate-700">
                            <?php echo e(__('Umetni link u editor')); ?>

                        </button>
                    </div>
                </div>

                <div class="mt-4 grid gap-4 xl:grid-cols-2">
                    <div class="rounded-xl border border-slate-200 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Blog kategorije')); ?></p>
                        <input type="text" wire:model.live.debounce.250ms="categorySearch" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="<?php echo e(__('Pretraga kategorija...')); ?>">
                        <div class="mt-3 max-h-60 overflow-auto rounded-xl border border-slate-200 bg-white p-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->filteredCategoryOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <button type="button" wire:click="addCategory(<?php echo e($category['id']); ?>)" class="mb-1 flex w-full items-center justify-between rounded-lg border border-slate-200 px-2 py-1.5 text-left text-sm text-slate-700 hover:bg-slate-50">
                                    <span><?php echo e($category['label']); ?></span>
                                    <span class="text-xs font-semibold text-slate-500">+</span>
                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <p class="px-1 py-1 text-xs text-slate-500"><?php echo e(__('Nema rezultata')); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="mt-3">
                            <p class="mb-1 text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Odabrano')); ?></p>
                            <div class="space-y-1">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->selectedCategoryRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-2 py-1.5 text-sm">
                                        <span><?php echo e($row['label']); ?></span>
                                        <button type="button" wire:click="removeCategory(<?php echo e($row['id']); ?>)" class="text-xs font-semibold text-rose-600 hover:text-rose-700"><?php echo e(__('Makni')); ?></button>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <p class="text-xs text-slate-500"><?php echo e(__('Nema odabranih kategorija.')); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'seo'): ?>
            <div class="admin-panel admin-form-panel p-6">
                <p class="admin-section-title"><?php echo e(__('SEO & Payload')); ?></p>
                <div class="mt-4">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Meta Title')); ?></label>
                    <input type="text" wire:model.live.debounce.250ms="form.meta_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.meta_title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Meta Description')); ?></label>
                    <textarea rows="4" wire:model.live.debounce.250ms="form.meta_description" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>
                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Post Payload JSON')); ?></label>
                    <textarea rows="8" wire:model="form.payload_text" class="w-full rounded-xl border border-slate-300 px-3 py-2 font-mono text-xs"></textarea>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.payload_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="mt-3">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Translation Payload JSON')); ?></label>
                    <textarea rows="8" wire:model="form.translation_payload_text" class="w-full rounded-xl border border-slate-300 px-3 py-2 font-mono text-xs"></textarea>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.translation_payload_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'media'): ?>
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.media.manager', ['modelClass' => \App\Models\Content\Blog\BlogPost::class,'modelId' => $postId,'locale' => $form['locale']]);

$key = 'blog-post-media-manager-'.($postId ?? 'new').'-'.$form['locale'];

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-2694034916-1', 'blog-post-media-manager-'.($postId ?? 'new').'-'.$form['locale']);

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="admin-form-actions flex items-center gap-2 pt-2">
            <button type="submit" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                <?php echo e($isEdit ? __('Update Blog Post') : __('Create Blog Post')); ?>

            </button>
            <button type="button" wire:click="backToList" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                <?php echo e(__('Cancel')); ?>

            </button>
        </div>
    </form>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/content/blog/form.blade.php ENDPATH**/ ?>