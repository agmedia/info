<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'sectionId',
    'showKicker' => false,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'sectionId',
    'showKicker' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div id="<?php echo e($sectionId); ?>" class="admin-panel admin-form-panel p-6 scroll-mt-24">
    <p class="admin-section-title"><?php echo e(__('Meeting CTA')); ?></p>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showKicker): ?>
        <div class="mt-4">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Kicker')); ?></label>
            <input type="text" wire:model="form.translation_payload.meeting.kicker" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="<?php echo e($showKicker ? 'mt-3' : 'mt-4'); ?>">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
        <input type="text" wire:model="form.translation_payload.meeting.title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
    </div>

    <div class="mt-3">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Intro')); ?></label>
        <textarea rows="4" wire:model="form.translation_payload.meeting.intro" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
    </div>

    <div class="mt-3">
        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Button Label')); ?></label>
        <input type="text" wire:model="form.translation_payload.meeting.contact_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:max-w-md" />
    </div>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/content/service/partials/meeting-cta-editor.blade.php ENDPATH**/ ?>