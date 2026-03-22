<div class="space-y-6">
    <div class="admin-panel admin-search-panel p-6">
        <h1 class="text-xl font-semibold tracking-tight"><?php echo e(__('Settings')); ?></h1>
        <p class="mt-2 text-sm text-slate-600"><?php echo e(__('Central place for public-site email, branding, newsletter, and announcement bar settings.')); ?></p>
    </div>

    <div class="admin-panel admin-form-panel p-6">
        <div class="mb-4 flex flex-wrap gap-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                'email' => 'Email',
                'branding' => 'Branding & Footer',
                'blog' => 'Blog',
                'newsletter' => 'Newsletter',
                'integrations' => 'Integrations',
                'seo' => 'SEO',
                'og' => 'OG / Twitter',
                'schema' => 'Schema Markup',
                'announcement' => 'Announcement bar',
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tabKey => $tabLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button" wire:click="$set('tab', '<?php echo e($tabKey); ?>')" class="rounded-xl border px-3 py-1.5 text-xs font-semibold <?php echo e($tab === $tabKey ? 'border-cyan-700 bg-cyan-700 text-white' : 'border-slate-300 text-slate-700 hover:bg-slate-100'); ?>">
                    <?php echo e(__($tabLabel)); ?>

                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <form wire:submit="save" class="space-y-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'email'): ?>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 md:col-span-2">
                        <input type="checkbox" wire:model="form.store_email_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        <?php echo e(__('Enable email notifications')); ?>

                    </label>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Mailer')); ?></label>
                        <select wire:model="form.store_email_mailer" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                            <option value="smtp"><?php echo e(__('SMTP')); ?></option>
                            <option value="sendmail"><?php echo e(__('Sendmail')); ?></option>
                            <option value="log"><?php echo e(__('Log')); ?></option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('From Address')); ?></label>
                        <input type="email" wire:model="form.store_email_from_address" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.store_email_from_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('From Name')); ?></label>
                        <input type="text" wire:model="form.store_email_from_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Reply-To')); ?></label>
                        <input type="email" wire:model="form.store_email_reply_to" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.store_email_reply_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Contact Forms To')); ?></label>
                        <input type="email" wire:model="form.store_email_contact_to" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.store_email_contact_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="md:col-span-2 grid gap-3 md:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('SMTP Host')); ?></label>
                            <input type="text" wire:model="form.store_email_smtp_host" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('SMTP Port')); ?></label>
                            <input type="number" wire:model="form.store_email_smtp_port" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Encryption')); ?></label>
                            <select wire:model="form.store_email_smtp_encryption" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                <option value=""><?php echo e(__('None')); ?></option>
                                <option value="tls"><?php echo e(__('TLS')); ?></option>
                                <option value="ssl"><?php echo e(__('SSL')); ?></option>
                            </select>
                        </div>
                    </div>
                    <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('SMTP Username')); ?></label>
                        <input type="text" wire:model="form.store_email_smtp_username" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('SMTP Password')); ?></label>
                        <input type="password" wire:model="form.store_email_smtp_password" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Sendmail Path')); ?></label>
                        <input type="text" wire:model="form.store_email_sendmail_path" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'branding'): ?>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Store Name')); ?></label>
                        <input type="text" wire:model="form.store_brand_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Footer Phone')); ?></label>
                        <input type="text" wire:model="form.store_footer_phone" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Footer Sales Email')); ?></label>
                        <input type="email" wire:model="form.store_footer_email_sales" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Footer Support Email')); ?></label>
                        <input type="email" wire:model="form.store_footer_email_support" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Footer Working Hours')); ?></label>
                        <input type="text" wire:model="form.store_footer_hours" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <h3 class="text-sm font-bold text-slate-800"><?php echo e(__('Footer Link Columns')); ?></h3>
                        <p class="mt-1 text-xs text-slate-600"><?php echo e(__('Configure 3 footer columns with page links and custom links.')); ?></p>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [1, 2, 3]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="md:col-span-2 rounded-xl border border-slate-200 p-4">
                            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Column')); ?> <?php echo e($col); ?></p>
                            <div class="grid gap-3 md:grid-cols-3">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Title')); ?></label>
                                    <input type="text" wire:model="form.store_footer_col_<?php echo e($col); ?>_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Pages')); ?></label>
                                    <div class="max-h-40 space-y-1 overflow-auto rounded-xl border border-slate-300 p-2 text-sm">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($pageOptions ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <label class="flex items-center gap-2">
                                                <input type="checkbox" wire:model="form.store_footer_col_<?php echo e($col); ?>_page_ids" value="<?php echo e((int) $option['id']); ?>" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500">
                                                <span><?php echo e((string) $option['label']); ?></span>
                                            </label>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Custom Links')); ?></label>
                                    <textarea wire:model="form.store_footer_col_<?php echo e($col); ?>_custom_links" rows="6" class="w-full rounded-xl border border-slate-300 px-3 py-2 font-mono text-xs" placeholder="<?php echo e(__('Blog|/blog&#10;FAQ|/faq&#10;Kontakt|/contact')); ?>"></textarea>
                                    <p class="mt-1 text-xs text-slate-500"><?php echo e(__('One per line:')); ?> <code>Label|URL</code></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="md:col-span-2 rounded-xl border border-slate-200 p-4">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Bottom Footer Bar')); ?></p>
                        <div class="grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Copyright text')); ?></label>
                                <input type="text" wire:model="form.store_footer_bottom_copyright_text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="<?php echo e(__('Sva prava pridržana.')); ?>" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Bottom links (pages)')); ?></label>
                                <div class="max-h-40 space-y-1 overflow-auto rounded-xl border border-slate-300 p-2 text-sm">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($pageOptions ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" wire:model="form.store_footer_bottom_link_page_ids" value="<?php echo e((int) $option['id']); ?>" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500">
                                            <span><?php echo e((string) $option['label']); ?></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <p class="mt-1 text-xs text-slate-500"><?php echo e(__('Order in this list = display order in footer.')); ?></p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Logo')); ?></label>
                        <input type="file" wire:model="logoUpload" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <p class="mt-1 text-xs text-slate-500"><?php echo e(__('Allowed: JPG, PNG, WEBP, AVIF, SVG')); ?></p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form['store_brand_logo_path']): ?>
                            <p class="mt-1 text-xs text-slate-500"><?php echo e(__('Current:')); ?> <?php echo e($form['store_brand_logo_path']); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Favicon')); ?></label>
                        <input type="file" wire:model="faviconUpload" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <p class="mt-1 text-xs text-slate-500"><?php echo e(__('Auto-generate: 16, 32, 180, 192, 512 and ICO.')); ?></p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form['store_brand_favicon_path']): ?>
                            <p class="mt-1 text-xs text-slate-500"><?php echo e(__('Current:')); ?> <?php echo e($form['store_brand_favicon_path']); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="md:col-span-2 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Facebook URL')); ?></label>
                            <input type="url" wire:model="form.store_social_facebook_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            <label class="mt-2 inline-flex items-center gap-2 text-xs font-semibold text-slate-700">
                                <input type="checkbox" wire:model="form.store_footer_social_facebook_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                                <?php echo e(__('Show in footer')); ?>

                            </label>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Instagram URL')); ?></label>
                            <input type="url" wire:model="form.store_social_instagram_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            <label class="mt-2 inline-flex items-center gap-2 text-xs font-semibold text-slate-700">
                                <input type="checkbox" wire:model="form.store_footer_social_instagram_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                                <?php echo e(__('Show in footer')); ?>

                            </label>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('TikTok URL')); ?></label>
                            <input type="url" wire:model="form.store_social_tiktok_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            <label class="mt-2 inline-flex items-center gap-2 text-xs font-semibold text-slate-700">
                                <input type="checkbox" wire:model="form.store_footer_social_tiktok_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                                <?php echo e(__('Show in footer')); ?>

                            </label>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('YouTube URL')); ?></label>
                            <input type="url" wire:model="form.store_social_youtube_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                            <label class="mt-2 inline-flex items-center gap-2 text-xs font-semibold text-slate-700">
                                <input type="checkbox" wire:model="form.store_footer_social_youtube_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                                <?php echo e(__('Show in footer')); ?>

                            </label>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'blog'): ?>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <h3 class="text-sm font-bold text-slate-800"><?php echo e(__('Blog page settings')); ?></h3>
                        <p class="mt-1 text-xs text-slate-600"><?php echo e(__('Hero copy, category navigation behaviour and posts per page for the public blog listing.')); ?></p>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Eyebrow')); ?></label>
                        <input type="text" wire:model="form.store_blog_header_eyebrow" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="<?php echo e(__('ALPHA CAPITALIS')); ?>" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.store_blog_header_eyebrow'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Hero title')); ?></label>
                        <input type="text" wire:model="form.store_blog_header_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="<?php echo e(__('Novosti i objave')); ?>" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.store_blog_header_title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Hero intro')); ?></label>
                        <textarea wire:model="form.store_blog_header_intro" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="<?php echo e(__('Kratki uvod iznad blog liste i filtera.')); ?>"></textarea>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.store_blog_header_intro'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Hero CTA label')); ?></label>
                        <input type="text" wire:model="form.store_blog_header_cta_label" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="<?php echo e(__('Kontaktirajte nas')); ?>" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.store_blog_header_cta_label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Hero CTA URL')); ?></label>
                        <input type="text" wire:model="form.store_blog_header_cta_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="/contact" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.store_blog_header_cta_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Visible categories before "Vidi još"')); ?></label>
                        <input type="number" min="1" max="40" wire:model="form.store_blog_category_preview_limit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <p class="mt-1 text-xs text-slate-500"><?php echo e(__('Controls how many category checkboxes are shown before the expandable "Vidi još" section.')); ?></p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.store_blog_category_preview_limit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Posts per page')); ?></label>
                        <input type="number" min="1" max="48" wire:model="form.store_blog_posts_per_page" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <p class="mt-1 text-xs text-slate-500"><?php echo e(__('Controls frontend pagination on /blog.')); ?></p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.store_blog_posts_per_page'];
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

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'newsletter'): ?>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Provider')); ?></label>
                        <select wire:model="form.store_newsletter_provider" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:w-72">
                            <option value="none"><?php echo e(__('None')); ?></option>
                            <option value="mailchimp"><?php echo e(__('Mailchimp')); ?></option>
                            <option value="klaviyo"><?php echo e(__('Klaviyo')); ?></option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Mailchimp API Key')); ?></label>
                        <input type="text" wire:model="form.store_newsletter_mailchimp_api_key" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Mailchimp List ID')); ?></label>
                        <input type="text" wire:model="form.store_newsletter_mailchimp_list_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Klaviyo API Key')); ?></label>
                        <input type="text" wire:model="form.store_newsletter_klaviyo_api_key" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Klaviyo List ID')); ?></label>
                        <input type="text" wire:model="form.store_newsletter_klaviyo_list_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'integrations'): ?>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <h3 class="text-sm font-bold text-slate-800"><?php echo e(__('reCAPTCHA v3')); ?></h3>
                        <p class="mt-1 text-xs text-slate-600"><?php echo e(__('Protect contact and public forms from spam bots.')); ?></p>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 md:col-span-2">
                        <input type="checkbox" wire:model="form.store_captcha_recaptcha_v3_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        <?php echo e(__('Enable reCAPTCHA v3')); ?>

                    </label>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Site key')); ?></label>
                        <input type="text" wire:model="form.store_captcha_recaptcha_v3_site_key" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Secret key')); ?></label>
                        <input type="password" wire:model="form.store_captcha_recaptcha_v3_secret_key" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="md:col-span-2 md:w-56">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Min score')); ?></label>
                        <input type="number" step="0.1" min="0" max="1" wire:model="form.store_captcha_recaptcha_v3_min_score" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>

                    <div class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4 mt-2">
                        <h3 class="text-sm font-bold text-slate-800"><?php echo e(__('Google Analytics (GA4)')); ?></h3>
                        <p class="mt-1 text-xs text-slate-600"><?php echo e(__('Inject the global GA4 gtag script on public pages.')); ?></p>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 md:col-span-2">
                        <input type="checkbox" wire:model="form.store_analytics_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        <?php echo e(__('Enable GA4 tracking')); ?>

                    </label>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('GA4 Measurement ID')); ?></label>
                        <input type="text" wire:model="form.store_analytics_ga4_measurement_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="G-XXXXXXXXXX" />
                    </div>
                    <div></div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'seo'): ?>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Default Title')); ?></label>
                        <input type="text" wire:model="form.store_seo_default_title" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Default Meta Description')); ?></label>
                        <textarea wire:model="form.store_seo_default_description" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Robots')); ?></label>
                        <input type="text" wire:model="form.store_seo_robots" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="index,follow" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Canonical policy')); ?></label>
                        <select wire:model="form.store_seo_canonical_policy" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                            <option value="self"><?php echo e(__('Self URL')); ?></option>
                            <option value="none"><?php echo e(__('Disabled')); ?></option>
                        </select>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'og'): ?>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Default OG image')); ?></label>
                        <input type="file" wire:model="ogDefaultImageUpload" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form['store_og_default_image_path']): ?>
                            <p class="mt-1 text-xs text-slate-500"><?php echo e(__('Current:')); ?> <?php echo e($form['store_og_default_image_path']); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Home OG override')); ?></label>
                        <input type="file" wire:model="ogHomeImageUpload" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form['store_og_home_image_path']): ?>
                            <p class="mt-1 text-xs text-slate-500"><?php echo e(__('Current:')); ?> <?php echo e($form['store_og_home_image_path']); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Category OG override')); ?></label>
                        <input type="file" wire:model="ogCategoryImageUpload" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form['store_og_category_image_path']): ?>
                            <p class="mt-1 text-xs text-slate-500"><?php echo e(__('Current:')); ?> <?php echo e($form['store_og_category_image_path']); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Page OG override')); ?></label>
                        <input type="file" wire:model="ogPageImageUpload" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form['store_og_page_image_path']): ?>
                            <p class="mt-1 text-xs text-slate-500"><?php echo e(__('Current:')); ?> <?php echo e($form['store_og_page_image_path']); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Blog OG override')); ?></label>
                        <input type="file" wire:model="ogBlogImageUpload" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form['store_og_blog_image_path']): ?>
                            <p class="mt-1 text-xs text-slate-500"><?php echo e(__('Current:')); ?> <?php echo e($form['store_og_blog_image_path']); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'schema'): ?>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 md:col-span-2">
                        <input type="checkbox" wire:model="form.store_schema_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        <?php echo e(__('Enable schema markup JSON-LD')); ?>

                    </label>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" wire:model="form.store_schema_org_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        <?php echo e(__('Organization schema')); ?>

                    </label>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" wire:model="form.store_schema_website_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        <?php echo e(__('WebSite + SearchAction')); ?>

                    </label>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" wire:model="form.store_schema_breadcrumbs_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        <?php echo e(__('BreadcrumbList')); ?>

                    </label>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" wire:model="form.store_schema_itemlist_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        <?php echo e(__('ItemList (blog list)')); ?>

                    </label>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" wire:model="form.store_schema_home_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        <?php echo e(__('Home WebPage')); ?>

                    </label>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" wire:model="form.store_schema_blog_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        <?php echo e(__('Blog / BlogPosting')); ?>

                    </label>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" wire:model="form.store_schema_page_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        <?php echo e(__('Generic Page schema')); ?>

                    </label>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" wire:model="form.store_schema_faq_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        <?php echo e(__('FAQ schema (home)')); ?>

                    </label>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Organization type')); ?></label>
                        <select wire:model="form.store_schema_org_type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                            <option value="Organization"><?php echo e(__('Organization')); ?></option>
                            <option value="LocalBusiness"><?php echo e(__('LocalBusiness')); ?></option>
                            <option value="Store"><?php echo e(__('Store')); ?></option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Business Name')); ?></label>
                        <input type="text" wire:model="form.store_schema_business_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Business Phone')); ?></label>
                        <input type="text" wire:model="form.store_schema_business_phone" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Business Email')); ?></label>
                        <input type="email" wire:model="form.store_schema_business_email" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>

                    <div class="md:col-span-2 grid gap-3 md:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Street')); ?></label>
                            <input type="text" wire:model="form.store_schema_address_street" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('City')); ?></label>
                            <input type="text" wire:model="form.store_schema_address_city" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Region')); ?></label>
                            <input type="text" wire:model="form.store_schema_address_region" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Postal code')); ?></label>
                            <input type="text" wire:model="form.store_schema_address_postal_code" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Country code')); ?></label>
                            <input type="text" wire:model="form.store_schema_address_country" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="HR" />
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('SameAs URLs (one per line)')); ?></label>
                        <textarea wire:model="form.store_schema_same_as" rows="4" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Blog author name (default)')); ?></label>
                        <input type="text" wire:model="form.store_schema_blog_author_name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Blog author profile URL')); ?></label>
                        <input type="url" wire:model="form.store_schema_blog_author_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('FAQ group code (optional)')); ?></label>
                        <input type="text" wire:model="form.store_schema_faq_group" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="support" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('FAQ max items')); ?></label>
                        <input type="number" min="1" max="20" wire:model="form.store_schema_faq_limit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('ItemList max posts')); ?></label>
                        <input type="number" min="1" max="48" wire:model="form.store_schema_itemlist_limit" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm md:w-48" />
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'announcement'): ?>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 md:col-span-2">
                        <input type="checkbox" wire:model="form.store_announcement_enabled" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        <?php echo e(__('Show top announcement bar')); ?>

                    </label>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Text')); ?></label>
                        <input type="text" wire:model="form.store_announcement_text" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><?php echo e(__('Link URL (optional)')); ?></label>
                        <input type="url" wire:model="form.store_announcement_url" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 md:col-span-2">
                        <input type="checkbox" wire:model="form.store_announcement_new_tab" class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-500" />
                        <?php echo e(__('Open link in new tab')); ?>

                    </label>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="pt-2">
                <button type="submit" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800"><?php echo e(__('Save Settings')); ?></button>
            </div>
        </form>
    </div>
</div><?php /**PATH /Users/tomek/Herd/info/resources/views/livewire/admin/settings/system/store-settings.blade.php ENDPATH**/ ?>