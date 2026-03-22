<?php
    $translation = $selectedTranslation
        ?? $page->translations->firstWhere('locale', $locale)
        ?? $page->translations->firstWhere('locale', $fallbackLocale);

    $pageTitleBreadcrumbs = [
        ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
        ['label' => $translation?->title ?? $page->code, 'current' => true],
    ];

    $careerHeroImageUrl = asset('front-theme/images/careers/hero-team.png');
    $careerCaptchaSiteKey = trim((string) ($storeSettings['captcha']['recaptcha_v3_site_key'] ?? ''));
    $careerCaptchaEnabled = (bool) ($storeSettings['captcha']['recaptcha_v3_enabled'] ?? false) && $careerCaptchaSiteKey !== '';
    $careerFormShouldScroll = $errors->any() || session()->has('status');
    $careerProcessSteps = [
        [
            'step' => 'Korak 01',
            'title' => 'Ispunjavanje prijave',
            'description' => 'Predaja prijave stiže u naš odjel ljudskih potencijala koji je ocjenjuje i poziva kandidata na razgovor u slučaju poklapanja profila i otvorene pozicije.',
            'icon_view_box' => '0 0 384 512',
            'icon_href' => asset('front-theme/fonts/sprites/solid.svg#file-lines'),
        ],
        [
            'step' => 'Korak 02',
            'title' => 'Testiranje znanja',
            'description' => 'Poziv i dolazak na opće i tehničko testiranje znanja kojim provjeravamo stručnost, pristup problemima i usklađenost s otvorenom pozicijom.',
            'icon_view_box' => '0 0 384 512',
            'icon_href' => asset('front-theme/fonts/sprites/solid.svg#clipboard-check'),
        ],
        [
            'step' => 'Korak 03',
            'title' => 'Razgovori',
            'description' => 'Ljudski potencijali kontaktiraju osobe koje su zadovoljile očekivane kriterije na testiranju, nakon čega slijedi razgovor s timom i višim menadžmentom odjela.',
            'icon_view_box' => '0 0 640 512',
            'icon_href' => asset('front-theme/fonts/sprites/solid.svg#comments'),
        ],
        [
            'step' => 'Korak 04',
            'title' => 'Ponuda za zaposlenje i onboarding',
            'description' => 'Kada osoba završi razgovore, slijedi završni korak selekcijskog procesa: potpis ugovora i onboarding kroz koji upoznaje naše poslovanje, vrijednosti, kulturu i kolege.',
            'icon_view_box' => '0 0 640 512',
            'icon_href' => asset('front-theme/fonts/sprites/solid.svg#user-check'),
        ],
    ];
?>

<?php $__env->startSection('title', $translation?->title ?? 'Karijera'); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginale6a101278d02d7bbbf9e98ee1142bf75 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.front.page-title-band','data' => ['breadcrumbs' => $pageTitleBreadcrumbs]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('front.page-title-band'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pageTitleBreadcrumbs)]); ?>
        <div class="ac-page-title-copy">
            <h1><?php echo e($translation?->title ?? $page->code); ?></h1>
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

    <section class="ac-career-page">
        <div class="mx-auto w-full max-w-[1320px] px-4 sm:px-6 lg:px-8">
            <div class="ac-career-layout">
                <section class="ac-career-section ac-career-section--intro">
                    <div class="ac-career-intro-shell">
                        <div class="ac-career-intro-copy-wrap">
                            <div class="ac-career-intro-copy">
                                <div class="ac-career-intro-heading">
                                    <h2>Postani dio tima</h2>
                                </div>
                                <div class="ac-career-intro-body">
                                    <p class="ac-career-intro-highlight">ALPHA CAPITALIS postoji od 2012. godine s ciljem pružanja podrške klijentima u svijetu financija kroz sve faze razvoja poslovanja.</p>
                                    <p>Oformili smo tim stručnjaka iz područja financija, revizije, računovodstva i poreza koji kroz zajedničko djelovanje nude cjelokupno rješenje za investitore, poduzetnike i menadžere. Članovi tima ALPHA CAPITALIS posjeduju višegodišnje iskustvo u investicijskom bankarstvu, financijskom savjetovanju, EU fonodvima, reviziji, restrukturiranju, kontrolingu i menadžerskom računovodstvu.</p>
                                </div>
                            </div>
                        </div>

                        <div class="ac-career-intro-media">
                            <img
                                src="<?php echo e($careerHeroImageUrl); ?>"
                                alt="Alpha Capitalis tim"
                                loading="lazy"
                                decoding="async"
                            >
                        </div>
                    </div>
                </section>

                <section class="ac-career-section ac-career-section--process" aria-labelledby="ac-career-process-title">
                    <div class="ac-career-process-shell">
                        <div class="ac-support-story-hero">
                            <div class="ac-support-story-shell">
                                <div class="ac-services-head ac-support-story-head ac-career-process-head">
                                    <div class="ac-services-eyebrow">
                                        <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                                        <p class="ac-services-kicker">Proces prijave</p>
                                        <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                                    </div>
                                    <h2 id="ac-career-process-title" aria-label="Selekcijski proces u ALPHA CAPITALISU">
                                        <span>Selekcijski proces u</span>
                                        <span>ALPHA CAPITALISU</span>
                                    </h2>
                                    <p class="ac-services-intro">Proces je jasan, strukturiran i fokusiran na kvalitetno upoznavanje kandidata i tima.</p>
                                    <div class="ac-services-divider" aria-hidden="true">
                                        <span class="ac-services-divider-line"></span>
                                        <span class="ac-services-divider-glyph"></span>
                                        <span class="ac-services-divider-line"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ac-support-story-grid ac-career-process-grid">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $careerProcessSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <article class="ac-support-story-card ac-career-process-card">
                                    <span class="ac-support-story-card-icon ac-career-process-icon" aria-hidden="true">
                                        <svg class="ac-career-process-fa" viewBox="<?php echo e($step['icon_view_box']); ?>" fill="currentColor">
                                            <use href="<?php echo e($step['icon_href']); ?>"></use>
                                        </svg>
                                    </span>
                                    <p class="ac-career-process-step-label"><?php echo e($step['step']); ?></p>
                                    <h3><?php echo e($step['title']); ?></h3>
                                    <p class="ac-support-story-card-lead ac-career-process-copy"><?php echo e($step['description']); ?></p>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </section>

                <section id="career-cta" class="ac-career-section ac-career-section--application">
                    <div class="ac-career-application-shell">
                        <div class="ac-career-intro-copy-wrap ac-career-application-copy-wrap">
                            <div class="ac-career-intro-copy ac-career-application-copy">
                                <div class="ac-career-intro-heading">
                                    <h2>Pridružite se timu ALPHA CAPITALIS!</h2>
                                </div>
                                <div class="ac-career-intro-body">
                                    <p class="ac-career-intro-highlight">Bez obzira jeste li iskusni profesionalac koji želi karijeru podići na novu razinu ili ste tek diplomirali, ALPHA CAPITALIS nudi mogućnosti za osobni i profesionalni napredak te dinamično radno okruženje koje će Vam omogućiti da postignete svoj puni potencijal.</p>
                                    <p>Potičemo polaganje stručnih ispita, razmjenu znanja kroz interne edukacije te rotacijski program uz stručno mentorstvo za stjecanje znanja iz područja financija, revizije, računovodstva i poreza.</p>
                                    <p>Tražimo motivirane i izvrsne osobe koje imaju želju za napretkom i stjecanjem novih znanja, a čiji je sustav vrijednosti u skladu s vrijednostima organizacije.</p>
                                    <p>Upoznajte nas i postanite dio tima ALPHA CAPITALIS.</p>
                                </div>
                            </div>
                        </div>

                        <div class="ac-career-form-wrap">
                            <div class="ac-career-form-card">
                                <div class="ac-career-form-head">
                                    <p class="ac-career-form-kicker"><?php echo e(__('career.form.eyebrow')); ?></p>
                                    <h3><?php echo e(__('career.form.title')); ?></h3>
                                    <p><?php echo e(__('career.form.intro')); ?></p>
                                </div>

                                <form
                                    method="POST"
                                    action="<?php echo e(route('career.applications.store')); ?>"
                                    enctype="multipart/form-data"
                                    class="ac-career-form"
                                    novalidate
                                    data-career-form
                                    data-msg-first-name-required="<?php echo e(__('career.validation.inline.first_name_required')); ?>"
                                    data-msg-last-name-required="<?php echo e(__('career.validation.inline.last_name_required')); ?>"
                                    data-msg-email-required="<?php echo e(__('career.validation.inline.email_required')); ?>"
                                    data-msg-email-invalid="<?php echo e(__('career.validation.inline.email_invalid')); ?>"
                                    data-msg-cv-required="<?php echo e(__('career.validation.inline.cv_required')); ?>"
                                    data-msg-accept-terms="<?php echo e(__('career.validation.inline.accept_terms')); ?>"
                                    data-scroll-on-load="<?php echo e($careerFormShouldScroll ? 'true' : 'false'); ?>"
                                    <?php if($careerCaptchaEnabled): ?> data-recaptcha-form data-recaptcha-site-key="<?php echo e($careerCaptchaSiteKey); ?>" data-recaptcha-action="career_application_form" <?php endif; ?>
                                >
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="recaptcha_token" value="" data-recaptcha-token>

                                    <div class="ac-career-form-grid">
                                        <div>
                                            <label class="ac-career-form-label" for="career-first-name"><?php echo e(__('career.form.first_name')); ?></label>
                                            <input id="career-first-name" type="text" name="first_name" value="<?php echo e(old('first_name')); ?>" class="ac-career-form-input" required>
                                            <p class="ac-career-form-error <?php echo e($errors->has('first_name') ? '' : 'hidden'); ?>" data-field-error="first_name"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                                        </div>
                                        <div>
                                            <label class="ac-career-form-label" for="career-last-name"><?php echo e(__('career.form.last_name')); ?></label>
                                            <input id="career-last-name" type="text" name="last_name" value="<?php echo e(old('last_name')); ?>" class="ac-career-form-input" required>
                                            <p class="ac-career-form-error <?php echo e($errors->has('last_name') ? '' : 'hidden'); ?>" data-field-error="last_name"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="ac-career-form-label" for="career-email"><?php echo e(__('career.form.email')); ?></label>
                                        <input id="career-email" type="email" name="email" value="<?php echo e(old('email', auth()->user()?->email)); ?>" class="ac-career-form-input" required>
                                        <p class="ac-career-form-error <?php echo e($errors->has('email') ? '' : 'hidden'); ?>" data-field-error="email"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                                    </div>

                                    <div>
                                        <label class="ac-career-form-label" for="career-message"><?php echo e(__('career.form.message')); ?></label>
                                        <textarea id="career-message" name="message" rows="2" class="ac-career-form-textarea"><?php echo e(old('message')); ?></textarea>
                                        <p class="ac-career-form-error <?php echo e($errors->has('message') ? '' : 'hidden'); ?>" data-field-error="message"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                                    </div>

                                    <div>
                                        <label class="ac-career-form-label" for="career-cv"><?php echo e(__('career.form.cv')); ?></label>
                                        <input id="career-cv" type="file" name="cv" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="ac-career-form-file" required>
                                        <p class="ac-career-form-help"><?php echo e(__('career.form.cv_help')); ?></p>
                                        <p class="ac-career-form-error <?php echo e($errors->has('cv') ? '' : 'hidden'); ?>" data-field-error="cv"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['cv'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                                    </div>

                                    <div class="ac-career-form-consent-wrap">
                                        <label class="ac-career-form-consent">
                                            <input type="checkbox" name="accept_terms" value="1" class="ac-career-form-checkbox" <?php if((bool) old('accept_terms')): echo 'checked'; endif; ?>>
                                            <span><?php echo e(__('career.form.accept_terms')); ?></span>
                                        </label>
                                        <p class="ac-career-form-error <?php echo e($errors->has('accept_terms') ? '' : 'hidden'); ?>" data-field-error="accept_terms"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['accept_terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                                    </div>

                                    <div class="ac-career-form-actions">
                                        <button type="submit" class="ac-career-submit-button">
                                            <?php echo e(__('career.form.submit')); ?>

                                        </button>
                                        <p class="ac-career-form-error <?php echo e($errors->has('recaptcha_token') ? '' : 'hidden'); ?>" data-field-error="recaptcha_token"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['recaptcha_token'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        html {
            scroll-behavior: smooth;
        }

        .ac-career-page {
            padding: 0;
            background: #fff;
        }

        .ac-career-layout {
            display: grid;
        }

        .ac-career-section {
            display: grid;
            gap: clamp(1.5rem, 3vw, 2.2rem);
            padding: clamp(2.1rem, 4vw, 3rem) 0;
            border-bottom: 1px solid rgba(15, 23, 42, 0.12);
        }

        .ac-career-section--intro {
            align-items: start;
            margin-inline: calc(50% - 50vw);
            padding: 0;
            background:
                radial-gradient(120% 140% at 0% 0%, rgba(255, 255, 255, 0.72), transparent 55%),
                linear-gradient(180deg, #f7f3ec 0%, #f8f4ec 100%);
        }

        .ac-career-intro-shell {
            width: 100%;
            display: grid;
            min-height: clamp(34rem, 46vw, 41rem);
        }

        .ac-career-intro-copy-wrap {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: clamp(2.8rem, 5vw, 5rem) clamp(1.5rem, 4vw, 3.5rem);
            overflow: hidden;
        }

        .ac-career-intro-copy-wrap::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: linear-gradient(90deg, rgba(16, 33, 58, 0.06) 0 1px, transparent 1px 100%);
            background-size: 42px 42px;
            opacity: 0.55;
            pointer-events: none;
        }

        .ac-career-intro-copy {
            position: relative;
            z-index: 1;
            max-width: 33rem;
            width: 100%;
            margin-left: auto;
        }

        .ac-career-intro-copy > * + * {
            margin-top: clamp(1.1rem, 1.8vw, 1.55rem);
        }

        .ac-career-intro-heading {
            max-width: none;
        }

        .ac-career-intro-heading > * + * {
            margin-top: 0.8rem;
        }

        .ac-career-intro-copy h2 {
            margin: 0;
            color: #10213a;
            font-family: "Playfair Display", Sans-serif;
            max-width: none;
            font-size: clamp(2.2rem, 3.1vw, 3.15rem);
            font-weight: 600;
            letter-spacing: -0.03em;
            line-height: 1.02;
            text-wrap: balance;
        }

        .ac-career-intro-lead {
            margin: 0;
            max-width: 30rem;
            color: #425671;
            font-size: clamp(1rem, 1.3vw, 1.14rem);
            line-height: 1.68;
            text-wrap: pretty;
        }

        .ac-career-intro-body {
            display: grid;
            gap: 0.95rem;
            max-width: 30rem;
        }

        .ac-career-intro-body p {
            color: #31455d;
            font-size: 1rem;
            line-height: 1.76;
            text-wrap: pretty;
        }

        .ac-career-intro-highlight {
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(16, 33, 58, 0.18);
            color: #20344d;
            font-size: clamp(1.04rem, 1.28vw, 1.12rem) !important;
            line-height: 1.7;
        }

        .ac-career-intro-media {
            position: relative;
            overflow: hidden;
            min-width: 0;
        }

        .ac-career-intro-media img {
            display: block;
            width: 100%;
            height: 100%;
            min-height: clamp(21rem, 42vw, 41rem);
            object-fit: cover;
            object-position: center top;
        }

        .ac-career-copy {
            max-width: 58rem;
        }

        .ac-career-copy--narrow {
            max-width: 54rem;
        }

        .ac-career-copy h2 {
            margin: 0;
            color: #0f172a;
            font-family: "Playfair Display", Sans-serif;
            font-size: clamp(1.85rem, 3vw, 2.85rem);
            font-weight: 600;
            letter-spacing: -0.025em;
            line-height: 1.08;
        }

        .ac-career-copy > * + * {
            margin-top: 1rem;
        }

        .ac-career-page p {
            margin: 0;
        }

        .ac-career-copy p {
            margin: 0;
            color: #334155;
            font-size: 0.98rem;
            line-height: 1.9;
        }

        .ac-career-application-shell {
            width: 100%;
            display: grid;
        }

        .ac-career-section--application {
            margin-inline: calc(50% - 50vw);
            padding: 0 0 0;
            border-bottom: 0;
        }

        .ac-career-application-copy-wrap {
            justify-content: flex-end;
            padding: clamp(2.8rem, 5vw, 4.5rem) clamp(1.5rem, 4vw, 3.5rem) clamp(4rem, 7vw, 6rem);
            background:
                radial-gradient(120% 140% at 0% 0%, rgba(255, 255, 255, 0.74), transparent 58%),
                linear-gradient(180deg, #f7f3ec 0%, #f8f4ec 100%);
        }

        .ac-career-application-copy-wrap::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: linear-gradient(90deg, rgba(16, 33, 58, 0.06) 0 1px, transparent 1px 100%);
            background-size: 42px 42px;
            opacity: 0.55;
            pointer-events: none;
        }

        .ac-career-form-wrap {
            position: relative;
            display: flex;
            align-items: stretch;
            justify-content: flex-start;
            padding: clamp(2.8rem, 4.8vw, 4.4rem) clamp(1.5rem, 3.5vw, 3rem) clamp(4rem, 7vw, 6rem);
            background:
                radial-gradient(92% 128% at 100% 0%, rgba(148, 163, 184, 0.12), transparent 46%),
                linear-gradient(180deg, #fcfdfe 0%, #f5f8fb 100%);
            overflow: hidden;
        }

        .ac-career-form-wrap::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(15, 23, 42, 0.04) 0 1px, transparent 1px 100%),
                linear-gradient(180deg, rgba(15, 23, 42, 0.025) 0 1px, transparent 1px 100%);
            background-size: 36px 36px;
            opacity: 0.55;
            pointer-events: none;
        }

        .ac-career-form-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 34rem;
            margin-right: auto;
            padding: 0;
            border: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
            backdrop-filter: none;
        }

        .ac-career-form-head {
            display: grid;
            gap: 0.55rem;
            margin-bottom: 1.8rem;
        }

        .ac-career-form-kicker {
            color: #7b8797;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .ac-career-form-head h3 {
            margin: 0;
            color: #10213a;
            font-family: "Playfair Display", Sans-serif;
            font-size: clamp(1.9rem, 2.5vw, 2.45rem);
            font-weight: 600;
            line-height: 1.04;
        }

        .ac-career-form-head p:last-child {
            max-width: 31rem;
            color: #54667d;
            font-size: 0.96rem;
            line-height: 1.78;
        }

        .ac-career-form {
            display: grid;
            gap: 1.3rem;
        }

        .ac-career-form-grid {
            display: grid;
            gap: 1.1rem;
        }

        .ac-career-form-label {
            display: block;
            margin-bottom: 0.35rem;
            color: #6a7c92;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .ac-career-form-input,
        .ac-career-form-textarea,
        .ac-career-form-file {
            width: 100%;
            border-radius: 0;
            background: transparent;
            color: #0f172a;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
        }

        .ac-career-form-input {
            min-height: 3.15rem;
            padding: 0.8rem 0 0.9rem;
            border: 0;
            border-bottom: 1px solid rgba(15, 23, 42, 0.16);
        }

        .ac-career-form-textarea {
            min-height: 3.2rem;
            padding: 0.85rem 0 0.9rem;
            border: 0;
            border-bottom: 1px solid rgba(15, 23, 42, 0.16);
            resize: vertical;
        }

        .ac-career-form-file {
            min-height: 3.15rem;
            padding: 0.82rem 0;
            border: 0;
            border-bottom: 1px solid rgba(15, 23, 42, 0.16);
            font-size: 0.94rem;
        }

        .ac-career-form-input:focus,
        .ac-career-form-textarea:focus,
        .ac-career-form-file:focus {
            outline: none;
            border-color: #173b5d;
            box-shadow: none;
            background: transparent;
        }

        .ac-career-form-help {
            margin-top: 0.55rem;
            color: #64748b;
            font-size: 0.78rem;
            line-height: 1.5;
        }

        .ac-career-form-error {
            margin-top: 0.45rem;
            color: #b42318;
            font-size: 0.78rem;
            font-weight: 700;
            line-height: 1.45;
        }

        .ac-career-form-consent-wrap {
            display: grid;
            gap: 0.4rem;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(15, 23, 42, 0.08);
        }

        .ac-career-form-consent {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            color: #475569;
            font-size: 0.88rem;
            line-height: 1.6;
        }

        .ac-career-form-checkbox {
            width: 1rem;
            height: 1rem;
            margin-top: 0.18rem;
            accent-color: #10213a;
        }

        .ac-career-form-actions {
            display: grid;
            gap: 0.55rem;
            padding-top: 0.4rem;
        }

        .ac-career-submit-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 3.2rem;
            width: 100%;
            padding: 0.95rem 1.35rem;
            border: 1px solid #0f2a43;
            border-radius: 0;
            background: #0f2a43;
            color: #fff;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            transition: background-color 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
        }

        .ac-career-submit-button:hover {
            background: #173b5d;
            border-color: #173b5d;
            transform: translateY(-1px);
        }

        .ac-career-process-step-label {
            margin: 0;
            color: #9a773d;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .ac-career-section--process {
            position: relative;
            gap: 0;
            margin-inline: calc(50% - 50vw);
            padding: clamp(2.3rem, 4.6vw, 4rem) 0;
            border-bottom: 1px solid rgba(15, 42, 67, 0.08);
            background: linear-gradient(180deg, #fbfdff 0%, #f3f7fb 100%);
            overflow: hidden;
        }

        .ac-career-section--process::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(44% 66% at 86% 18%, rgba(33, 106, 164, 0.12), transparent 72%),
                radial-gradient(34% 48% at 10% 82%, rgba(171, 141, 82, 0.09), transparent 74%),
                repeating-linear-gradient(90deg, rgba(15, 42, 67, 0.045) 0 1px, transparent 1px 33px);
            opacity: 0.9;
            pointer-events: none;
        }

        .ac-career-process-shell {
            position: relative;
            z-index: 1;
            display: grid;
            gap: clamp(1.3rem, 3vw, 2rem);
            width: 100%;
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 1.25rem;
        }

        .ac-career-process-head {
            max-width: 1020px;
            margin: 0 auto;
            text-align: center;
        }

        .ac-career-process-head h2 {
            max-width: 24ch;
        }

        .ac-career-process-head h2 span {
            display: block;
            white-space: nowrap;
        }

        .ac-career-process-head .ac-services-intro {
            max-width: 62ch;
            margin-left: auto;
            margin-right: auto;
            color: #506074;
            font-size: 1rem;
            line-height: 1.8;
            text-align: center;
        }

        .ac-career-process-grid {
            display: grid;
            gap: 1.3rem;
        }

        .ac-career-process-card {
            min-width: 0;
            min-height: 15rem;
        }

        .ac-career-process-icon {
            color: inherit;
        }

        .ac-career-process-fa {
            width: 1.12rem;
            height: 1.12rem;
        }

        .ac-career-process-step-label {
            margin-bottom: 0.48rem;
            color: #738399;
        }

        .ac-career-process-card h3 {
            margin: 0;
            padding-bottom: 0.5rem;
            max-width: calc(100% - 4.8rem);
            color: #0f1b2d;
            font-family: "Playfair Display", Sans-serif;
            font-size: clamp(1.42rem, 1.8vw, 1.82rem);
            line-height: 1.14;
            font-weight: 600;
        }

        .ac-career-process-copy {
            margin: 1.85rem 0 0;
            padding-top: 1.05rem;
            max-width: calc(100% - 4.25rem);
            border-top: 1px solid rgba(148, 163, 184, 0.28);
            color: #4c5d6f;
            font-size: 0.98rem;
            line-height: 1.72;
            text-wrap: pretty;
        }

        .ac-career-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.9rem;
            align-items: center;
            margin-top: 1.8rem;
        }

        .ac-career-primary-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 3.15rem;
            padding: 0.9rem 1.35rem;
            border: 1px solid #0f2a43;
            background: #0f2a43;
            color: #fff !important;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            text-decoration: none;
            transition: background-color 0.18s ease, border-color 0.18s ease;
        }

        .ac-career-primary-link:hover {
            background: #173b5d;
            border-color: #173b5d;
            color: #fff !important;
        }

        @media (min-width: 960px) {
            .ac-career-intro-shell {
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            }

            .ac-career-application-shell {
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            }

            .ac-career-intro-copy-wrap {
                padding-left: clamp(2rem, 5vw, 5rem);
                padding-right: clamp(1.5rem, 3vw, 3rem);
            }

            .ac-career-application-copy-wrap {
                padding-left: clamp(2rem, 5vw, 5rem);
                padding-right: clamp(1.5rem, 3vw, 3rem);
            }

            .ac-career-process-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: clamp(1.35rem, 2.6vw, 1.7rem);
            }

            .ac-career-form-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 959px) {
            .ac-career-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .ac-career-primary-link {
                width: 100%;
            }

            .ac-career-section--process {
                padding: 2rem 0;
            }

            .ac-career-section--intro {
                padding: 0;
            }

            .ac-career-process-shell {
                padding: 0 1rem;
            }

            .ac-career-form-wrap {
                padding-top: 1.8rem;
                padding-bottom: 3.4rem;
            }
        }

        @media (max-width: 640px) {
            .ac-career-intro-copy h2 {
                font-size: clamp(1.9rem, 7.8vw, 2.35rem);
            }

            .ac-career-intro-copy-wrap {
                padding: 2.25rem 1.15rem 2rem;
            }

            .ac-career-intro-copy > * + * {
                margin-top: 1rem;
            }

            .ac-career-intro-heading > * + * {
                margin-top: 0.75rem;
            }

            .ac-career-intro-body {
                gap: 0.85rem;
            }

            .ac-career-intro-body p {
                font-size: 0.96rem;
                line-height: 1.7;
            }

            .ac-career-intro-highlight {
                padding-bottom: 0.85rem;
                font-size: 1rem !important;
            }

            .ac-career-intro-media img {
                min-height: min(92vw, 24rem);
            }

            .ac-career-process-card {
                min-height: auto;
            }

            .ac-career-process-head h2 span {
                white-space: normal;
            }

            .ac-career-process-card h3 {
                padding-bottom: 0.42rem;
                max-width: calc(100% - 4.15rem);
                font-size: clamp(1.34rem, 6vw, 1.66rem);
            }

            .ac-career-process-copy {
                max-width: calc(100% - 3.6rem);
                margin-top: 1.55rem;
                padding-top: 0.9rem;
                font-size: 0.94rem;
                line-height: 1.66;
            }

            .ac-career-form-head h3 {
                font-size: clamp(1.7rem, 7vw, 2.05rem);
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($careerCaptchaEnabled): ?>
    <?php $__env->startPush('scripts'); ?>
        <script src="https://www.google.com/recaptcha/api.js?render=<?php echo e($careerCaptchaSiteKey); ?>"></script>
    <?php $__env->stopPush(); ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        (function () {
            const form = document.querySelector('[data-career-form]');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!form) {
                return;
            }

            const clearError = function (field) {
                const errorNode = form.querySelector('[data-field-error="' + field + '"]');
                if (!errorNode) {
                    return;
                }

                errorNode.textContent = '';
                errorNode.classList.add('hidden');
            };

            const setError = function (field, message) {
                const errorNode = form.querySelector('[data-field-error="' + field + '"]');
                if (!errorNode) {
                    return;
                }

                errorNode.textContent = message;
                errorNode.classList.remove('hidden');
            };

            const getHeaderOffset = function () {
                const stickyHeader = document.querySelector('[data-front-sticky-header]');
                if (!(stickyHeader instanceof HTMLElement)) {
                    return 18;
                }

                return Math.round(stickyHeader.getBoundingClientRect().height) + 18;
            };

            const scrollToForm = function () {
                const target = document.getElementById('career-cta') || form;
                if (!(target instanceof HTMLElement)) {
                    return;
                }

                const targetTop = window.pageYOffset + target.getBoundingClientRect().top - getHeaderOffset();

                if (typeof window.__frontAnimateScrollTo === 'function') {
                    window.__frontAnimateScrollTo(targetTop);
                    return;
                }

                window.scrollTo(0, Math.max(0, targetTop));
            };

            form.querySelectorAll('[data-field-error]').forEach(function (node) {
                if ((node.textContent || '').trim() === '') {
                    node.classList.add('hidden');
                } else {
                    node.classList.remove('hidden');
                }
            });

            if (form.dataset.scrollOnLoad === 'true') {
                window.requestAnimationFrame(scrollToForm);
            }

            const validate = function () {
                ['first_name', 'last_name', 'email', 'cv', 'accept_terms', 'recaptcha_token'].forEach(clearError);

                const firstName = form.querySelector('[name="first_name"]');
                const lastName = form.querySelector('[name="last_name"]');
                const email = form.querySelector('[name="email"]');
                const cv = form.querySelector('[name="cv"]');
                const acceptTerms = form.querySelector('[name="accept_terms"]');
                let valid = true;

                if (!firstName || firstName.value.trim() === '') {
                    setError('first_name', form.dataset.msgFirstNameRequired || '');
                    valid = false;
                }

                if (!lastName || lastName.value.trim() === '') {
                    setError('last_name', form.dataset.msgLastNameRequired || '');
                    valid = false;
                }

                const emailValue = email ? email.value.trim() : '';
                if (emailValue === '') {
                    setError('email', form.dataset.msgEmailRequired || '');
                    valid = false;
                } else if (!emailRegex.test(emailValue)) {
                    setError('email', form.dataset.msgEmailInvalid || '');
                    valid = false;
                }

                if (!cv || !cv.files || cv.files.length === 0) {
                    setError('cv', form.dataset.msgCvRequired || '');
                    valid = false;
                }

                if (!acceptTerms || !acceptTerms.checked) {
                    setError('accept_terms', form.dataset.msgAcceptTerms || '');
                    valid = false;
                }

                return valid;
            };

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                if (!validate()) {
                    scrollToForm();
                    return;
                }

                const tokenInput = form.querySelector('[data-recaptcha-token]');
                const siteKey = form.dataset.recaptchaSiteKey;
                const action = form.dataset.recaptchaAction || 'career_application_form';

                if (!tokenInput || !window.grecaptcha || !siteKey) {
                    form.submit();
                    return;
                }

                grecaptcha.ready(function () {
                    grecaptcha.execute(siteKey, { action: action }).then(function (token) {
                        tokenInput.value = token || '';
                        form.submit();
                    });
                });
            });
        }());
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/career.blade.php ENDPATH**/ ?>