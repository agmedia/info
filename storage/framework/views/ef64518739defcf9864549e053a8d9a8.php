<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
    <div class="mb-6 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" data-flash-message>
        <div class="flex items-start gap-3">
            <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center text-emerald-700" aria-hidden="true">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="m5 13 4 4L19 7"></path>
                </svg>
            </span>
            <p class="flex-1"><?php echo e(session('status')); ?></p>
            <button type="button" class="inline-flex h-6 w-6 items-center justify-center text-emerald-700 hover:text-emerald-900" aria-label="<?php echo e(__('ui.notifications.close')); ?>" onclick="this.closest('[data-flash-message]')?.remove()">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 6l12 12M18 6 6 18"></path>
                </svg>
            </button>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php
    $globalErrorMessages = collect($errors->getBag('default')->getMessages())
        ->except(['product_option_value_id'])
        ->flatten();
    $showGlobalErrorSummary = !request()->routeIs('assessment.*')
        && !request()->routeIs('eu-funds.questionnaire.*')
        && !request()->is('eu-fondovi/upitnik');
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showGlobalErrorSummary && $globalErrorMessages->isNotEmpty()): ?>
    <div class="mb-6 border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" data-flash-message>
        <div class="flex items-start gap-3">
            <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center text-rose-700" aria-hidden="true">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 8v5"></path>
                    <path d="M12 16h.01"></path>
                </svg>
            </span>
            <div class="flex-1">
                <ul class="list-disc space-y-1 pl-5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $globalErrorMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            </div>
            <button type="button" class="inline-flex h-6 w-6 items-center justify-center text-rose-700 hover:text-rose-900" aria-label="<?php echo e(__('ui.notifications.close')); ?>" onclick="this.closest('[data-flash-message]')?.remove()">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 6l12 12M18 6 6 18"></path>
                </svg>
            </button>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/partials/flash.blade.php ENDPATH**/ ?>