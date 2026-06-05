<?php $__env->startSection('title', __('contact.page_title')); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $captchaSiteKey = trim((string) ($storeSettings['captcha']['recaptcha_v3_site_key'] ?? ''));
        $captchaEnabled = (bool) ($storeSettings['captcha']['recaptcha_v3_enabled'] ?? false) && $captchaSiteKey !== '';
        $contactOffices = collect((array) ($storeSettings['official_entities'] ?? []))
            ->filter(static fn ($office): bool => is_array($office) && (bool) ($office['show_on_contact'] ?? false))
            ->map(static function (array $office): array {
                $address = $office['contact_address'] ?? $office['address'] ?? [];
                $office['label'] = trim((string) ($office['label'] ?? $office['office_label'] ?? ''));
                $office['address'] = collect(is_array($address) ? $address : [])
                    ->map(static fn ($line): string => trim((string) $line))
                    ->filter()
                    ->values()
                    ->all();
                $office['phone_href'] = preg_replace('/\s+/', '', (string) ($office['phone'] ?? ''));

                $query = trim((string) ($office['map_query'] ?? ''));
                $encodedQuery = rawurlencode($query);
                $embedUrl = trim((string) ($office['map_embed_url'] ?? ''));

                $office['map_embed_url'] = $embedUrl !== ''
                    ? $embedUrl
                    : ($encodedQuery !== '' ? 'https://www.google.com/maps?q='.$encodedQuery.'&z=16&output=embed' : '');
                $office['map_external_url'] = $encodedQuery !== ''
                    ? 'https://www.google.com/maps/search/?api=1&query='.$encodedQuery
                    : '';

                return $office;
            })
            ->values()
            ->all();
        $primaryOffice = collect($contactOffices)->firstWhere('key', 'alpha-capitalis-timia') ?? ($contactOffices[0] ?? null);
        $contactEmail = trim((string) ($storeSettings['footer']['email_support'] ?? ''))
            ?: trim((string) ($primaryOffice['email'] ?? ''))
            ?: 'info@alphacapitalis.com';
        $contactPhone = trim((string) ($storeSettings['footer']['phone'] ?? ''))
            ?: trim((string) ($primaryOffice['phone'] ?? ''))
            ?: '+385 (0) 51 301 503';
        $contactPhoneHref = preg_replace('/\s+/', '', $contactPhone);
        $contactHours = trim((string) ($storeSettings['footer']['hours'] ?? '')) ?: __('contact.direct.response_fallback');
        $pageTitleBreadcrumbs = [
            ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
            ['label' => __('contact.page_title'), 'current' => true],
        ];
    ?>

    <div class="front-contact-page ac-contact-page">
        <?php if (isset($component)) { $__componentOriginale6a101278d02d7bbbf9e98ee1142bf75 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.front.page-title-band','data' => ['breadcrumbs' => $pageTitleBreadcrumbs,'sectionClass' => 'ac-contact-title-band']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('front.page-title-band'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pageTitleBreadcrumbs),'section-class' => 'ac-contact-title-band']); ?>
            <div class="ac-page-title-copy">
                <h1><?php echo e(__('contact.heading')); ?></h1>
                <p><?php echo e(__('contact.subheading')); ?></p>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75)): ?>
<?php $attributes = $__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75; ?>
<?php unset($__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale6a101278d02d7bbbf9e98ee1142bf75)): ?>
<?php $component = $__componentOriginale6a101278d02d7bbbf9e98ee1142bf75; ?>
<?php unset($__componentOriginale6a101278d02d7bbbf9e98ee1142bf75); ?>
<?php endif; ?>

        <section class="front-contact-offices-shell">
            <div class="mx-auto w-full max-w-[1320px] px-4 sm:px-6 lg:px-8">
                <div class="front-contact-offices-head">
                    <p class="front-contact-section-kicker"><?php echo e(__('contact.offices.kicker')); ?></p>
                    <h2><?php echo e(__('contact.offices.title')); ?></h2>
                    <p><?php echo e(__('contact.offices.intro')); ?></p>
                </div>

                <div class="front-contact-offices-grid">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $contactOffices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $office): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="front-contact-office-card">
                            <div class="front-contact-office-top">
                                <p class="front-contact-office-label"><?php echo e($office['label']); ?></p>
                                <h3><?php echo e($office['company']); ?></h3>
                            </div>

                            <div class="front-contact-office-body">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $office['address']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <p><?php echo e($line); ?></p>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($office['mbs'] ?? '')) !== ''): ?>
                                    <p>MBS: <?php echo e($office['mbs']); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($office['iban'] ?? '')) !== ''): ?>
                                    <p>IBAN: <?php echo e($office['iban']); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <button type="button" class="front-contact-office-map-trigger" data-office-map-trigger="<?php echo e($office['key']); ?>">
                                    <span class="front-contact-inline-icon" aria-hidden="true">
                                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8">
                                            <path d="M10 18s5-4.6 5-9a5 5 0 1 0-10 0c0 4.4 5 9 5 9z"/>
                                            <circle cx="10" cy="9" r="1.9"/>
                                        </svg>
                                    </span>
                                    <span><?php echo e(__('contact.offices.view_map')); ?></span>
                                </button>
                            </div>

                            <div class="front-contact-office-meta">
                                <a href="mailto:<?php echo e($office['email']); ?>" class="front-contact-office-link">
                                    <span><?php echo e(__('contact.direct.email')); ?></span>
                                    <strong>
                                        <span class="front-contact-inline-icon" aria-hidden="true">
                                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8">
                                                <path d="M3 5.5h14v9H3z"/>
                                                <path d="m4 6 6 4.8L16 6"/>
                                            </svg>
                                        </span>
                                        <?php echo e($office['email']); ?>

                                    </strong>
                                </a>
                                <a href="tel:<?php echo e($office['phone_href']); ?>" class="front-contact-office-link">
                                    <span><?php echo e(__('contact.direct.phone')); ?></span>
                                    <strong>
                                        <span class="front-contact-inline-icon" aria-hidden="true">
                                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8">
                                                <path d="M6.2 3.8h2.1l1.2 3.2-1.5 1.5a11 11 0 0 0 3.9 3.9l1.5-1.5 3.2 1.2v2.1c0 .6-.5 1-1.1 1A12.6 12.6 0 0 1 4.1 4.9c0-.6.5-1.1 1.1-1.1z"/>
                                            </svg>
                                        </span>
                                        <?php echo e($office['phone']); ?>

                                    </strong>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <section class="front-contact-content-shell">
            <div class="mx-auto w-full max-w-[1320px] px-4 sm:px-6 lg:px-8">
                <div class="front-contact-layout">
                    <form
                        method="POST"
                        action="<?php echo e(route('contact.store')); ?>"
                        class="front-contact-form"
                        novalidate
                        data-contact-form
                        data-msg-name-required="<?php echo e(__('contact.validation.inline.name_required')); ?>"
                        data-msg-email-required="<?php echo e(__('contact.validation.inline.email_required')); ?>"
                        data-msg-email-invalid="<?php echo e(__('contact.validation.inline.email_invalid')); ?>"
                        data-msg-message-required="<?php echo e(__('contact.validation.inline.message_required')); ?>"
                        data-msg-message-min="<?php echo e(__('contact.validation.inline.message_min')); ?>"
                        data-msg-accept-terms="<?php echo e(__('contact.validation.inline.accept_terms')); ?>"
                        <?php if($captchaEnabled): ?> data-recaptcha-form data-recaptcha-site-key="<?php echo e($captchaSiteKey); ?>" data-recaptcha-action="contact_form" <?php endif; ?>
                    >
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="recaptcha_token" value="" data-recaptcha-token>

                        <div class="front-contact-form-head">
                            <p class="front-contact-section-kicker"><?php echo e(__('contact.form.kicker')); ?></p>
                            <h2><?php echo e(__('contact.form.title')); ?></h2>
                            <p><?php echo e(__('contact.form.intro')); ?></p>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
                            <div class="front-contact-status" role="status">
                                <?php echo e(session('status')); ?>

                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('contact.form.name')); ?></label>
                                <input type="text" name="name" value="<?php echo e(old('name', auth()->user()?->name)); ?>" class="front-contact-input h-11 w-full text-sm" required>
                                <p class="mt-2 text-xs font-semibold text-rose-600 <?php echo e($errors->has('name') ? '' : 'hidden'); ?>" data-field-error="name"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('contact.form.email')); ?></label>
                                <input type="email" name="email" value="<?php echo e(old('email', auth()->user()?->email)); ?>" class="front-contact-input h-11 w-full text-sm" required>
                                <p class="mt-2 text-xs font-semibold text-rose-600 <?php echo e($errors->has('email') ? '' : 'hidden'); ?>" data-field-error="email"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('contact.form.phone')); ?></label>
                            <input type="text" name="phone" value="<?php echo e(old('phone')); ?>" class="front-contact-input h-11 w-full text-sm">
                            <p class="mt-2 text-xs font-semibold text-rose-600 <?php echo e($errors->has('phone') ? '' : 'hidden'); ?>" data-field-error="phone"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('contact.form.subject')); ?></label>
                            <input type="text" name="subject" value="<?php echo e(old('subject')); ?>" class="front-contact-input h-11 w-full text-sm">
                            <p class="mt-2 text-xs font-semibold text-rose-600 <?php echo e($errors->has('subject') ? '' : 'hidden'); ?>" data-field-error="subject"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('contact.form.message')); ?></label>
                            <textarea name="message" rows="8" class="front-contact-textarea w-full text-sm" required><?php echo e(old('message')); ?></textarea>
                            <p class="mt-2 text-xs font-semibold text-rose-600 <?php echo e($errors->has('message') ? '' : 'hidden'); ?>" data-field-error="message"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                        </div>

                        <div class="front-contact-consent-wrap">
                            <label class="front-contact-consent">
                                <input type="checkbox" name="accept_terms" value="1" class="front-contact-checkbox mt-0.5 h-4 w-4 border-slate-300 text-slate-900 focus:ring-0" <?php if((bool) old('accept_terms')): echo 'checked'; endif; ?>>
                                <span><?php echo e(__('contact.form.accept_terms')); ?></span>
                            </label>
                            <p class="mt-2 text-xs font-semibold text-rose-600 <?php echo e($errors->has('accept_terms') ? '' : 'hidden'); ?>" data-field-error="accept_terms"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['accept_terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                        </div>

                        <div class="front-contact-form-actions">
                            <button type="submit" class="front-contact-submit inline-flex h-11 items-center justify-center px-6 text-sm font-semibold text-white transition">
                                <?php echo e(__('contact.form.submit')); ?>

                            </button>
                            <p class="text-xs font-semibold text-rose-600 <?php echo e($errors->has('recaptcha_token') ? '' : 'hidden'); ?>" data-field-error="recaptcha_token"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['recaptcha_token'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                        </div>
                    </form>

                    <aside class="front-contact-sidebar">
                        <div class="front-contact-panel front-contact-panel--direct">
                            <h2><?php echo e(__('contact.direct.title')); ?></h2>
                            <p class="front-contact-panel-intro"><?php echo e(__('contact.direct.body')); ?></p>

                            <ul class="front-contact-direct-list">
                                <li>
                                    <span><?php echo e(__('contact.direct.email')); ?></span>
                                    <a href="mailto:<?php echo e($contactEmail); ?>"><?php echo e($contactEmail); ?></a>
                                </li>
                                <li>
                                    <span><?php echo e(__('contact.direct.phone')); ?></span>
                                    <a href="tel:<?php echo e($contactPhoneHref); ?>"><?php echo e($contactPhone); ?></a>
                                </li>
                                <li>
                                    <span><?php echo e(__('contact.direct.response_time')); ?></span>
                                    <strong><?php echo e($contactHours); ?></strong>
                                </li>
                            </ul>
                        </div>

                        <div class="front-contact-help">
                            <h3><?php echo e(__('contact.help.title')); ?></h3>
                            <p><?php echo e(__('contact.help.body')); ?></p>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        <section id="contact-map-section" class="front-contact-map-shell">
            <div class="mx-auto w-full max-w-[1320px] px-4 sm:px-6 lg:px-8">
                <div class="front-contact-map-tabs" role="tablist" aria-label="<?php echo e(__('contact.map.title')); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $contactOffices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $office): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button
                            type="button"
                            class="front-contact-map-tab<?php echo e($index === 0 ? ' is-active' : ''); ?>"
                            data-office-map-tab="<?php echo e($office['key']); ?>"
                            role="tab"
                            aria-selected="<?php echo e($index === 0 ? 'true' : 'false'); ?>"
                        >
                            <?php echo e($office['map_label']); ?>

                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="front-contact-map-stage">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $contactOffices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $office): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div
                            class="front-contact-map-panel<?php echo e($index === 0 ? ' is-active' : ''); ?>"
                            data-office-map-panel="<?php echo e($office['key']); ?>"
                            <?php if($index !== 0): ?> hidden <?php endif; ?>
                        >
                            <div class="front-contact-map-frame">
                                <iframe
                                    src="<?php echo e($office['map_embed_url']); ?>"
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    allowfullscreen
                                    title="<?php echo e($office['map_label']); ?>"
                                ></iframe>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>
    </div>

    <?php echo $__env->make('front.desktop.contact.partials.form-script', [
        'captchaEnabled' => $captchaEnabled,
        'captchaSiteKey' => $captchaSiteKey,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script>
            (function () {
                const mapSection = document.getElementById('contact-map-section');
                const mapStage = mapSection ? mapSection.querySelector('.front-contact-map-stage') : null;
                const mapPanels = Array.from(document.querySelectorAll('[data-office-map-panel]'));
                const mapTabs = Array.from(document.querySelectorAll('[data-office-map-tab]'));
                const mapTriggers = Array.from(document.querySelectorAll('[data-office-map-trigger]'));

                const getHeaderOffset = function () {
                    const stickyHeader = document.querySelector('[data-front-sticky-header]');
                    if (!(stickyHeader instanceof HTMLElement)) {
                        return 18;
                    }

                    return Math.round(stickyHeader.getBoundingClientRect().height) + 18;
                };

                const scrollToMap = function () {
                    const scrollTarget = mapStage || mapSection;
                    if (!(scrollTarget instanceof HTMLElement)) {
                        return;
                    }

                    const targetTop = window.pageYOffset + scrollTarget.getBoundingClientRect().top - getHeaderOffset();

                    if (typeof window.__frontAnimateScrollTo === 'function') {
                        window.__frontAnimateScrollTo(targetTop);
                        return;
                    }

                    window.scrollTo(0, Math.max(0, targetTop));
                };

                const activateOfficeMap = function (officeKey) {
                    if (!officeKey) {
                        return;
                    }

                    mapPanels.forEach(function (panel) {
                        const isActive = panel.dataset.officeMapPanel === officeKey;
                        panel.hidden = !isActive;
                        panel.classList.toggle('is-active', isActive);
                    });

                    mapTabs.forEach(function (tab) {
                        const isActive = tab.dataset.officeMapTab === officeKey;
                        tab.classList.toggle('is-active', isActive);
                        tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
                    });
                };

                mapTabs.forEach(function (tab) {
                    tab.addEventListener('click', function () {
                        activateOfficeMap(tab.dataset.officeMapTab || '');
                    });
                });

                mapTriggers.forEach(function (trigger) {
                    trigger.addEventListener('click', function () {
                        const officeKey = trigger.dataset.officeMapTrigger || '';
                        activateOfficeMap(officeKey);

                        window.requestAnimationFrame(scrollToMap);
                    });
                });
            }());
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/contact/create.blade.php ENDPATH**/ ?>