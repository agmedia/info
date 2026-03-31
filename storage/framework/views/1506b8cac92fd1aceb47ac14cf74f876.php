<div>
    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e($label ?? __('Asset Path')); ?></label>
    <input
        type="text"
        value="<?php echo e($currentPath ?? ''); ?>"
        readonly
        placeholder="<?php echo e(__('No asset uploaded yet')); ?>"
        class="w-full rounded-xl border border-slate-300 bg-slate-100 px-3 py-2 text-sm font-mono text-slate-600"
    />
    <p class="mt-1 text-xs text-slate-500">
        <?php echo e(__('Odaberite PDF datoteku i pri spremanju će se asset path popuniti automatski.')); ?>

    </p>

    <input
        type="file"
        wire:model="<?php echo e($uploadModel); ?>"
        accept=".pdf,application/pdf"
        class="mt-3 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
    />
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = [$uploadModel];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div wire:loading wire:target="<?php echo e($uploadModel); ?>" class="mt-2 text-xs text-slate-500">
        <?php echo e(__('Uploading PDF...')); ?>

    </div>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/content/service/partials/pdf-asset-upload-field.blade.php ENDPATH**/ ?>