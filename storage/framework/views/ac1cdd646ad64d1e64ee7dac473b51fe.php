<?php
    $rangeOptions = [
        '1' => __('Today'),
        '7' => __('Last 7 Days'),
        '30' => __('Last 30 Days'),
    ];
?>

<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight"><?php echo e(__('Info Site Overview')); ?></h1>
                <p class="mt-1 text-sm text-slate-600"><?php echo e(__('Operational metrics for users, content, and inbound contact volume.')); ?></p>
                <p class="mt-2 text-xs text-slate-500"><?php echo e(__('Window')); ?>: <span class="admin-chip"><?php echo e($start->format('Y-m-d')); ?> - <?php echo e($end->format('Y-m-d')); ?></span></p>
            </div>
            <div class="w-56">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Range')); ?></label>
                <select wire:model.live="rangeDays" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border px-3 py-2 text-sm">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $rangeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="grid gap-4" style="grid-template-columns: repeat(12, minmax(0, 1fr));">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $delta = $kpi['delta'];
                $direction = $delta['direction'];
                $tone = $direction === 'up' ? 'text-emerald-700' : ($direction === 'down' ? 'text-rose-700' : 'text-slate-600');
            ?>
            <div class="admin-panel admin-panel-soft p-4" style="grid-column: span 3;">
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

    <div class="grid gap-4" style="grid-template-columns: repeat(12, minmax(0, 1fr));">
        <div class="admin-panel admin-panel-soft p-5" style="grid-column: span 7;">
            <h2 class="admin-section-title"><?php echo e(__('Users & Messages Trend')); ?></h2>
            <div class="mt-4" style="height: 16rem;">
                <canvas
                    data-dashboard-chart
                    data-chart-key="users_contacts_trend"
                    data-chart-payload='<?php echo json_encode($dashboardCharts["users_contacts_trend"], 15, 512) ?>'
                ></canvas>
            </div>
        </div>

        <div class="admin-panel admin-panel-soft p-5" style="grid-column: span 5;">
            <h2 class="admin-section-title"><?php echo e(__('Feature Flags')); ?></h2>
            <div class="mt-4 grid gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $featureFlags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flag => $enabled): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                        <span class="text-slate-700"><?php echo e($flag); ?></span>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($enabled ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700'); ?>">
                            <?php echo e($enabled ? __('On') : __('Off')); ?>

                        </span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <div class="grid gap-6" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
        <div class="admin-panel admin-panel-soft p-5">
            <h2 class="admin-section-title"><?php echo e(__('Daily New Users (:days Days)', ['days' => min($days, 30)])); ?></h2>
            <div class="mt-4 space-y-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $trendRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="grid items-center gap-3" style="grid-template-columns: 7rem minmax(0, 1fr) 4rem 4rem;">
                        <span class="text-xs text-slate-600"><?php echo e(\Illuminate\Support\Carbon::parse($row['date'])->format('M d')); ?></span>
                        <div class="h-2 rounded-full bg-slate-200">
                            <div class="h-2 rounded-full bg-cyan-600" style="width: <?php echo e(max(2, (int) $row['bar_width'])); ?>%;"></div>
                        </div>
                        <span class="text-xs text-slate-700 text-right"><?php echo e($row['users']); ?></span>
                        <span class="text-xs text-slate-500 text-right"><?php echo e($row['messages']); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <p class="mt-3 text-xs text-slate-500"><?php echo e(__('Right columns: users / contact messages')); ?></p>
        </div>

        <div class="admin-panel admin-panel-soft p-5">
            <h2 class="admin-section-title"><?php echo e(__('Content & System Snapshot')); ?></h2>
            <div class="mt-4 grid gap-2" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $catalogSnapshot; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item['url'])): ?>
                        <a href="<?php echo e($item['url']); ?>" class="rounded-xl border border-slate-200 bg-white p-3 hover:bg-slate-50">
                            <p class="text-xs uppercase tracking-[0.12em] text-slate-500"><?php echo e($item['label']); ?></p>
                            <p class="mt-2 text-lg font-semibold text-slate-900"><?php echo e(number_format((int) $item['value'])); ?></p>
                        </a>
                    <?php else: ?>
                        <div class="rounded-xl border border-slate-200 bg-white p-3">
                            <p class="text-xs uppercase tracking-[0.12em] text-slate-500"><?php echo e($item['label']); ?></p>
                            <p class="mt-2 text-lg font-semibold text-slate-900"><?php echo e(number_format((int) $item['value'])); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <div class="grid gap-6" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
        <div class="admin-panel admin-panel-soft p-5">
            <h2 class="admin-section-title"><?php echo e(__('Recent Contact Messages')); ?></h2>
            <div class="mt-3 overflow-x-auto">
                <table class="admin-items-table min-w-full text-xs">
                    <thead>
                        <tr>
                            <th class="px-2 py-2 text-left"><?php echo e(__('Time')); ?></th>
                            <th class="px-2 py-2 text-left"><?php echo e(__('Name')); ?></th>
                            <th class="px-2 py-2 text-left"><?php echo e(__('Email')); ?></th>
                            <th class="px-2 py-2 text-left"><?php echo e(__('Subject')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentContactMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-2 py-2"><?php echo e($row->created_at?->format('m-d H:i') ?? '-'); ?></td>
                                <td class="px-2 py-2"><?php echo e($row->name ?: '-'); ?></td>
                                <td class="px-2 py-2"><?php echo e($row->email ?: '-'); ?></td>
                                <td class="px-2 py-2"><?php echo e($row->subject ?: '-'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="4" class="px-2 py-4 text-center text-slate-500"><?php echo e(__('No messages yet.')); ?></td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-panel admin-panel-soft p-5">
            <h2 class="admin-section-title"><?php echo e(__('Recent Admin Activity')); ?></h2>
            <div class="mt-3 overflow-x-auto">
                <table class="admin-items-table min-w-full text-xs">
                    <thead>
                        <tr>
                            <th class="px-2 py-2 text-left"><?php echo e(__('Time')); ?></th>
                            <th class="px-2 py-2 text-left"><?php echo e(__('Event')); ?></th>
                            <th class="px-2 py-2 text-left"><?php echo e(__('Causer')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentAdminActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-2 py-2"><?php echo e($activity->created_at?->format('m-d H:i') ?? '-'); ?></td>
                                <td class="px-2 py-2"><?php echo e($activity->event ?: $activity->description); ?></td>
                                <td class="px-2 py-2"><?php echo e($activity->causer?->name ?: __('System')); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="3" class="px-2 py-4 text-center text-slate-500"><?php echo e(__('No admin activity.')); ?></td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($trackingEnabled): ?>
        <div class="admin-panel admin-panel-soft p-5">
            <h2 class="admin-section-title"><?php echo e(__('Recent Tracking Events')); ?></h2>
            <div class="mt-3 overflow-x-auto">
                <table class="admin-items-table min-w-full text-xs">
                    <thead>
                        <tr>
                            <th class="px-2 py-2 text-left"><?php echo e(__('Time')); ?></th>
                            <th class="px-2 py-2 text-left"><?php echo e(__('Event')); ?></th>
                            <th class="px-2 py-2 text-left"><?php echo e(__('User')); ?></th>
                            <th class="px-2 py-2 text-left"><?php echo e(__('URL')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentTrackingEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-2 py-2"><?php echo e(optional($row->occurred_at)->format('m-d H:i') ?? '-'); ?></td>
                                <td class="px-2 py-2"><?php echo e($row->event); ?></td>
                                <td class="px-2 py-2"><?php echo e($row->user?->name ?: '-'); ?></td>
                                <td class="px-2 py-2"><?php echo e($row->url ?: '-'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="4" class="px-2 py-4 text-center text-slate-500"><?php echo e(__('No tracking events.')); ?></td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/dashboard/overview.blade.php ENDPATH**/ ?>