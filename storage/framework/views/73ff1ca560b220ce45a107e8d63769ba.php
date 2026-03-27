<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight"><?php echo e(__('API Settings')); ?></h1>
                <p class="mt-2 text-sm text-slate-600"><?php echo e(__('Wholesale API base URL:')); ?> <code>/api/v1/wholesale</code></p>
                <p class="mt-2 text-xs text-slate-500"><?php echo e(__('Approve users for API access, issue scoped tokens, and revoke credentials.')); ?></p>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="admin-chip"><?php echo e(__('Items per page')); ?>: <?php echo e($perPage); ?></span>
                <span class="admin-chip"><?php echo e(__('Endpoints:')); ?> 8</span>
            </div>
        </div>
    </div>

    <div class="admin-panel admin-form-panel p-6">
        <p class="admin-section-title"><?php echo e(__('API User Access')); ?></p>

        <div class="mt-4 grid gap-3" style="grid-template-columns: 3fr 1fr 1fr;">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('admin.common.search')); ?></label>
                <input
                    type="text"
                    wire:model.live.debounce.250ms="search"
                    class="admin-search-input w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
                    placeholder="<?php echo e(__('Name or email...')); ?>"
                />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Role')); ?></label>
                <select wire:model.live="role" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                    <option value=""><?php echo e(__('All')); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($roleOption->name); ?>"><?php echo e($roleOption->title ?: ucfirst($roleOption->name)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('API Access')); ?></label>
                <select wire:model.live="accessFilter" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                    <option value="all"><?php echo e(__('All')); ?></option>
                    <option value="enabled"><?php echo e(__('Enabled')); ?></option>
                    <option value="disabled"><?php echo e(__('Disabled')); ?></option>
                </select>
            </div>
        </div>

        <div class="mt-5 overflow-x-auto rounded-xl border border-slate-200">
            <table class="admin-items-table min-w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-[0.12em] text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left"><?php echo e(__('User')); ?></th>
                        <th class="px-4 py-3 text-left"><?php echo e(__('Role')); ?></th>
                        <th class="px-4 py-3 text-center"><?php echo e(__('API Access')); ?></th>
                        <th class="px-4 py-3 text-center"><?php echo e(__('Tokens')); ?></th>
                        <th class="px-4 py-3 text-right"><?php echo e(__('admin.common.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $rowRole = $row->roles->sortBy('id')->first();
                            $apiEnabled = (bool) $row->api_access_enabled;
                        ?>
                        <tr>
                            <td class="px-4 py-3 align-middle">
                                <p class="font-semibold text-slate-900"><?php echo e($row->name); ?></p>
                                <p class="text-xs text-slate-500"><?php echo e($row->email); ?></p>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <span class="admin-chip"><?php echo e($rowRole?->title ?: ucfirst((string) ($rowRole?->name ?? 'customer'))); ?></span>
                            </td>
                            <td class="px-4 py-3 align-middle text-center">
                                <button
                                    type="button"
                                    wire:click="toggleApiAccess(<?php echo e($row->id); ?>)"
                                    class="admin-switch"
                                    data-state="<?php echo e($apiEnabled ? 'on' : 'off'); ?>"
                                    role="switch"
                                    aria-checked="<?php echo e($apiEnabled ? 'true' : 'false'); ?>"
                                    aria-label="<?php echo e(__('Toggle API access for')); ?> <?php echo e($row->email); ?>"
                                >
                                    <span class="admin-switch-track"><span class="admin-switch-thumb"></span></span>
                                    <span class="admin-switch-label"><?php echo e($apiEnabled ? __('On') : __('Off')); ?></span>
                                </button>
                            </td>
                            <td class="px-4 py-3 text-center align-middle">
                                <span class="admin-chip"><?php echo e((int) $row->tokens_count); ?></span>
                            </td>
                            <td class="px-4 py-3 text-right align-middle">
                                <div class="inline-flex items-center gap-2">
                                    <button
                                        type="button"
                                        wire:click="prepareIssueToken(<?php echo e($row->id); ?>)"
                                        class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100"
                                    >
                                        <?php echo e(__('Issue Token')); ?>

                                    </button>
                                    <button
                                        type="button"
                                        wire:click="revokeAllTokensForUser(<?php echo e($row->id); ?>)"
                                        class="rounded-lg border border-rose-300 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50"
                                    >
                                        <?php echo e(__('Revoke All')); ?>

                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500"><?php echo e(__('No users found for current filters.')); ?></td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4"><?php echo e($users->links()); ?></div>
    </div>

    <div class="admin-panel admin-form-panel p-6">
        <p class="admin-section-title"><?php echo e(__('Issue API Token')); ?></p>

        <form wire:submit="issueToken" class="admin-form mt-4 space-y-4">
            <div class="grid gap-3" style="grid-template-columns: 3fr 2fr 2fr 2fr;">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Approved User')); ?></label>
                    <select wire:model="issueUserId" data-tom-select class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        <option value=""><?php echo e(__('Select user')); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $approvedUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approvedUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($approvedUser->id); ?>"><?php echo e($approvedUser->name); ?> (<?php echo e($approvedUser->email); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['issueUserId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Preset')); ?></label>
                    <select wire:model.live="preset" data-tom-select data-tom-no-search="1" class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $presetCatalog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $presetKey => $presetLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($presetKey); ?>"><?php echo e($presetLabel); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Token Name')); ?></label>
                    <input type="text" wire:model="tokenName" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['tokenName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Expires At')); ?></label>
                    <input type="datetime-local" wire:model="expiresAt" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['expiresAt'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div>
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Token Abilities')); ?></label>
                <div class="grid gap-3" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $abilityCatalog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $abilityKey => $ability): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="flex items-start gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2">
                            <input type="checkbox" wire:model="selectedAbilities" value="<?php echo e($abilityKey); ?>" class="mt-1 h-4 w-4 rounded border-slate-300 text-cyan-600" />
                            <span>
                                <span class="block text-sm font-semibold text-slate-800"><?php echo e($ability['title']); ?></span>
                                <span class="block text-xs text-slate-500"><?php echo e($abilityKey); ?></span>
                            </span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['selectedAbilities'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['selectedAbilities.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="admin-form-actions flex items-center gap-2">
                <button type="submit" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800"><?php echo e(__('Create Token')); ?></button>
            </div>
        </form>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($generatedPlainToken !== ''): ?>
            <div class="mt-4 rounded-xl border border-emerald-300 bg-emerald-50 p-4 text-sm text-emerald-900">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-emerald-700"><?php echo e(__('Plain Token (show once)')); ?></p>
                <div class="mt-2 rounded-lg border border-emerald-200 bg-white p-3 font-mono text-xs break-all"><?php echo e($generatedPlainToken); ?></div>
                <p class="mt-2 text-xs text-emerald-700"><?php echo e(__('Copy this now. It cannot be displayed again.')); ?></p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="admin-panel admin-items-panel p-6">
        <p class="admin-section-title"><?php echo e(__('Issued Tokens')); ?></p>

        <div class="mt-4 grid gap-3" style="grid-template-columns: 3fr 2fr;">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Search Token/User')); ?></label>
                <input
                    type="text"
                    wire:model.live.debounce.250ms="tokenSearch"
                    class="admin-search-input w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"
                    placeholder="<?php echo e(__('Token name, user name, email...')); ?>"
                />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('User Filter')); ?></label>
                <select wire:model.live="tokenUserFilter" data-tom-select class="admin-select w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                    <option value=""><?php echo e(__('All Users')); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $approvedUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approvedUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($approvedUser->id); ?>"><?php echo e($approvedUser->name); ?> (<?php echo e($approvedUser->email); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
        </div>

        <div class="mt-5 overflow-x-auto rounded-xl border border-slate-200">
            <table class="admin-items-table min-w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-[0.12em] text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left"><?php echo e(__('User')); ?></th>
                        <th class="px-4 py-3 text-left"><?php echo e(__('Token')); ?></th>
                        <th class="px-4 py-3 text-left"><?php echo e(__('Abilities')); ?></th>
                        <th class="px-4 py-3 text-center"><?php echo e(__('Last Used')); ?></th>
                        <th class="px-4 py-3 text-center"><?php echo e(__('Expires')); ?></th>
                        <th class="px-4 py-3 text-center"><?php echo e(__('Created')); ?></th>
                        <th class="px-4 py-3 text-right"><?php echo e(__('Action')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tokens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $token): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $tokenUser = $token->tokenable; ?>
                        <tr>
                            <td class="px-4 py-3 align-middle">
                                <p class="font-semibold text-slate-900"><?php echo e($tokenUser?->name ?? __('Unknown User')); ?></p>
                                <p class="text-xs text-slate-500"><?php echo e($tokenUser?->email ?? '-'); ?></p>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <span class="font-medium text-slate-900"><?php echo e($token->name); ?></span>
                                <p class="text-xs text-slate-500"><?php echo e(__('ID:')); ?> <?php echo e($token->id); ?></p>
                            </td>
                            <td class="px-4 py-3 align-middle text-xs text-slate-600">
                                <?php echo e(implode(', ', $token->abilities ?? [])); ?>

                            </td>
                            <td class="px-4 py-3 text-center align-middle text-xs text-slate-600"><?php echo e($token->last_used_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                            <td class="px-4 py-3 text-center align-middle text-xs text-slate-600"><?php echo e($token->expires_at?->format('Y-m-d H:i') ?? __('No expiry')); ?></td>
                            <td class="px-4 py-3 text-center align-middle text-xs text-slate-600"><?php echo e($token->created_at?->format('Y-m-d H:i')); ?></td>
                            <td class="px-4 py-3 text-right align-middle">
                                <button
                                    type="button"
                                    wire:click="revokeToken(<?php echo e($token->id); ?>)"
                                    class="rounded-lg border border-rose-300 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50"
                                >
                                    <?php echo e(__('Revoke')); ?>

                                </button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-sm text-slate-500"><?php echo e(__('No tokens found for current filters.')); ?></td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($tokens->links()); ?>

        </div>
    </div>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/settings/api/manager.blade.php ENDPATH**/ ?>