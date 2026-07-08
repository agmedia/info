<?php
    $rangeOptions = [
        '1' => __('Today'),
        '7' => __('Last 7 Days'),
        '30' => __('Last 30 Days'),
    ];
?>

<div class="space-y-4 sm:space-y-6">
    <div class="admin-panel admin-search-panel p-4 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <h1 class="text-xl font-semibold tracking-tight"><?php echo e(__('Info Site Overview')); ?></h1>
                <p class="mt-1 text-sm text-slate-600"><?php echo e(__('Operational metrics for users, content, and inbound contact volume.')); ?></p>
                <p class="mt-2 text-xs text-slate-500"><?php echo e(__('Window')); ?>: <span class="admin-chip"><?php echo e($start->format('Y-m-d')); ?> - <?php echo e($end->format('Y-m-d')); ?></span></p>
            </div>
            <div class="w-full sm:w-56">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Range')); ?></label>
                <select wire:model.live="rangeDays" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border px-3 py-2 text-sm">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $rangeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $delta = $kpi['delta'];
                $direction = $delta['direction'];
                $tone = $direction === 'up' ? 'text-emerald-700' : ($direction === 'down' ? 'text-rose-700' : 'text-slate-600');
            ?>
            <div class="admin-panel admin-panel-soft p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e($kpi['label']); ?></p>
                <p class="mt-2 text-2xl font-semibold text-slate-900"><?php echo e($kpi['value']); ?></p>
                <p class="mt-2 text-xs <?php echo e($tone); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($direction === 'up'): ?> + <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php echo e(number_format($delta['delta'], 2)); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($delta['percent'] !== null): ?>
                        (<?php echo e($delta['percent'] >= 0 ? '+' : ''); ?><?php echo e(number_format($delta['percent'], 1)); ?>%)
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="admin-panel admin-panel-soft p-4 sm:p-5">
        <h2 class="admin-section-title"><?php echo e(__('Users & Messages Trend')); ?></h2>
        <div class="mt-4 h-64 sm:h-72">
            <canvas
                data-dashboard-chart
                data-chart-key="users_contacts_trend"
                data-chart-payload='<?php echo json_encode($dashboardCharts["users_contacts_trend"], 15, 512) ?>'
            ></canvas>
        </div>
    </div>

    <div class="admin-panel admin-panel-soft p-4 sm:p-5">
        <h2 class="admin-section-title"><?php echo e(__('Daily New Users (:days Days)', ['days' => min($days, 30)])); ?></h2>
        <div class="mt-4 space-y-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $trendRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="grid items-center gap-2 sm:gap-3" style="grid-template-columns: minmax(4.75rem, 6rem) minmax(0, 1fr) 2.5rem 2.5rem;">
                    <span class="text-xs text-slate-600"><?php echo e(\Illuminate\Support\Carbon::parse($row['date'])->format('M d')); ?></span>
                    <div class="h-2 rounded-full bg-slate-200">
                        <div class="h-2 rounded-full bg-cyan-600" style="width: <?php echo e(max(2, (int) $row['bar_width'])); ?>%;"></div>
                    </div>
                    <span class="text-right text-xs text-slate-700"><?php echo e($row['users']); ?></span>
                    <span class="text-right text-xs text-slate-500"><?php echo e($row['messages']); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <p class="mt-3 text-xs text-slate-500"><?php echo e(__('Right columns: users / contact messages')); ?></p>
    </div>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/dashboard/overview.blade.php ENDPATH**/ ?>