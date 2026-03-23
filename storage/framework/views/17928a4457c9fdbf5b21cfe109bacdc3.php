<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500"><?php echo e(__('Admin / Users')); ?></p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900"><?php echo e(__('Edit Admin User')); ?></h1>
                <p class="mt-2 text-sm text-slate-600"><?php echo e(__('Update core admin account data and access in one place.')); ?></p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="admin-chip"><?php echo e(__('User ID')); ?>: <?php echo e($userId); ?></span>
                <button type="button" wire:click="backToList" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100"><?php echo e(__('Back to List')); ?></button>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="admin-panel admin-form-panel p-6">
            <p class="admin-section-title"><?php echo e(__('Core Data')); ?></p>

            <div class="mt-4 grid gap-3 md:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Name')); ?></label>
                    <input type="text" wire:model="form.name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Email')); ?></label>
                    <input type="email" wire:model="form.email" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Role')); ?></label>
                    <select wire:model="form.role" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($role->name); ?>" <?php if($form['role'] === $role->name): echo 'selected'; endif; ?>><?php echo e($role->title ?: ucfirst($role->name)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-3">
                <button
                    type="button"
                    wire:click="$toggle('form.email_verified')"
                    class="admin-switch"
                    data-state="<?php echo e($form['email_verified'] ? 'on' : 'off'); ?>"
                    role="switch"
                    aria-checked="<?php echo e($form['email_verified'] ? 'true' : 'false'); ?>"
                    aria-label="<?php echo e(__('Toggle email verified state')); ?>"
                >
                    <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                    <span class="admin-switch-label"><?php echo e($form['email_verified'] ? __('Email Verified') : __('Email Unverified')); ?></span>
                </button>
            </div>
        </div>

        <div class="admin-panel admin-form-panel p-6">
            <p class="admin-section-title"><?php echo e(__('Contact Details')); ?></p>

            <div class="mt-4 grid gap-3 md:grid-cols-3">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('First Name')); ?></label>
                    <input type="text" wire:model="form.profile.first_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.profile.first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Last Name')); ?></label>
                    <input type="text" wire:model="form.profile.last_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.profile.last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Phone')); ?></label>
                    <input type="text" wire:model="form.profile.phone" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.profile.phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="admin-panel admin-form-panel p-6">
            <p class="admin-section-title"><?php echo e(__('Password Reset (Optional)')); ?></p>
            <p class="mt-1 text-sm text-slate-600"><?php echo e(__('Leave blank to keep current password.')); ?></p>

            <div class="mt-4 grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('New Password')); ?></label>
                    <input type="password" wire:model="form.password" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" autocomplete="new-password" />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Confirm Password')); ?></label>
                    <input type="password" wire:model="form.password_confirmation" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" autocomplete="new-password" />
                </div>
            </div>
        </div>

        <div class="admin-form-actions flex items-center gap-2 pt-2">
            <button type="submit" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                <?php echo e(__('Update User')); ?>

            </button>
            <button type="button" wire:click="backToList" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                <?php echo e(__('Cancel')); ?>

            </button>
        </div>
    </form>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/user/form.blade.php ENDPATH**/ ?>