<?php
    $translation = $selectedTranslation
        ?? $page->translations->firstWhere('locale', $locale)
        ?? $page->translations->firstWhere('locale', $fallbackLocale);

    $careerCaptchaSiteKey = trim((string) ($storeSettings['captcha']['recaptcha_v3_site_key'] ?? ''));
    $careerCaptchaEnabled = (bool) ($storeSettings['captcha']['recaptcha_v3_enabled'] ?? false) && $careerCaptchaSiteKey !== '';
    $careerFormShouldScroll = $errors->any() || session()->has('status');
    $careerContent = is_array($careerContent ?? null) ? $careerContent : [];
    $careerIntro = is_array($careerContent['intro'] ?? null) ? $careerContent['intro'] : [];
    $careerProcess = is_array($careerContent['process'] ?? null) ? $careerContent['process'] : [];
    $careerApplication = is_array($careerContent['application'] ?? null) ? $careerContent['application'] : [];
    $careerFormContent = is_array($careerContent['form'] ?? null) ? $careerContent['form'] : [];
    $isCroatian = str_starts_with(strtolower((string) $locale), 'hr');
    $careerValues = collect((array) ($careerContent['values'] ?? []))
        ->map(static fn ($value): string => trim((string) $value))
        ->filter()
        ->values();
    $careerIntroBody = collect((array) ($careerIntro['body'] ?? []))
        ->map(static fn ($paragraph): string => trim((string) $paragraph))
        ->filter()
        ->values();
    $careerApplicationParagraphs = collect((array) ($careerApplication['paragraphs'] ?? []))
        ->map(static fn ($paragraph): string => trim((string) $paragraph))
        ->filter()
        ->values();
    $careerProcessSteps = collect((array) ($careerProcess['steps'] ?? []))
        ->map(static fn ($step): array => is_array($step) ? $step : [])
        ->filter(static fn (array $step): bool => trim((string) ($step['title'] ?? '')) !== '')
        ->values();
    $careerStories = collect((array) ($careerContent['stories'] ?? []))
        ->map(static function ($story): array {
            $story = is_array($story) ? $story : [];

            return [
                'kicker' => trim((string) ($story['kicker'] ?? '')),
                'title' => trim((string) ($story['title'] ?? '')),
                'paragraphs' => collect((array) ($story['paragraphs'] ?? []))
                    ->map(static fn ($paragraph): string => trim((string) $paragraph))
                    ->filter()
                    ->values()
                    ->all(),
                'list' => collect((array) ($story['list'] ?? []))
                    ->map(static fn ($item): string => trim((string) $item))
                    ->filter()
                    ->values()
                    ->all(),
            ];
        })
        ->filter(static fn (array $story): bool => $story['title'] !== '')
        ->values();

    $careerCanonicalTitle = $isCroatian ? 'Karijera' : 'Career';
    $careerTranslationTitle = trim((string) ($translation?->title ?? ''));
    $careerPageTitle = $careerTranslationTitle !== '' && ! in_array($careerTranslationTitle, ['Ljudski potencijali', 'Human potential'], true)
        ? $careerTranslationTitle
        : $careerCanonicalTitle;
    $careerIntroTitle = $isCroatian ? 'Karijera u ALPHA CAPITALISU' : 'A career at ALPHA CAPITALIS';
    $careerHeroTitle = trim((string) ($careerIntro['title'] ?? '')) ?: ($isCroatian ? 'Mjesto gdje karijera stvarno raste' : 'A place where careers truly grow');
    $careerHeroHighlight = trim((string) ($careerIntro['highlight'] ?? '')) ?: ($isCroatian ? 'Ne tražimo samo zaposlenike.' : 'We are not simply looking for employees.');
    $careerIntroLead = (string) ($careerIntroBody->first() ?? '');
    $careerHeroParagraphs = $careerIntroBody->skip(1)->values();
    $careerProcessTitle = trim(implode(' ', array_filter([
        trim((string) ($careerProcess['title_line_one'] ?? '')),
        trim((string) ($careerProcess['title_line_two'] ?? '')),
    ])));
    $careerProcessTitle = $careerProcessTitle !== '' ? $careerProcessTitle : ($isCroatian ? 'Razvoj koji nije samo fraza' : 'Growth that is more than a phrase');
    $careerApplicationTitle = trim((string) ($careerApplication['title'] ?? '')) ?: ($isCroatian ? 'Otvorene pozicije' : 'Open positions');
    $careerApplicationHighlight = trim((string) ($careerApplication['highlight'] ?? ''));
    $headingWords = static fn (string $title): array => preg_split('/\s+/u', trim($title), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $processIconClasses = ['fa-handshake', 'fa-hands-holding-heart', 'fa-chart-line-up', 'fa-lightbulb-on'];
    $storyIconClasses = ['fa-people-group', 'fa-compass', 'fa-seedling'];
    $careerHeroPhoto = [
        'src' => asset('front-theme/images/about/o-nama-alpha-capitalis.jpg'),
        'alt' => $isCroatian ? 'ALPHA CAPITALIS tim' : 'ALPHA CAPITALIS team',
    ];
?>

<?php $__env->startSection('title', $careerPageTitle); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>
<?php $__env->startSection('hide_footer_newsletter', '1'); ?>

<?php $__env->startSection('content'); ?>
    <div class="ac-career-page">
        <section class="ac-career-intro" aria-labelledby="ac-career-intro-title">
            <div class="ac-career-container ac-career-intro-layout">
                <div class="ac-career-intro-heading">
                    <h1 class="values-title services-index-intro-title ac-career-display-title" id="ac-career-intro-title" data-words-slide-from-right aria-label="<?php echo e($careerIntroTitle); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($careerIntroTitle); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="values-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h1>
                </div>

                <div class="ac-career-intro-copy content-reveal animation-index-1" data-image-reveal>
                    <h2 class="ac-career-copy-heading"><?php echo e($careerHeroHighlight); ?></h2>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($careerIntroLead !== ''): ?>
                        <p><?php echo e($careerIntroLead); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <section class="ac-career-hero" aria-labelledby="ac-career-hero-title">
            <div class="ac-career-container ac-career-hero-grid">
                <div class="ac-career-hero-copy">
                    <p class="ac-family-section-kicker ac-career-kicker"><?php echo e($isCroatian ? 'Rastemo zajedno' : 'Growing together'); ?></p>
                    <h2 class="ac-career-dark-title" id="ac-career-hero-title" data-words-slide-from-right aria-label="<?php echo e($careerHeroTitle); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($careerHeroTitle); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="service-title-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h2>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($careerHeroParagraphs->isNotEmpty()): ?>
                        <div class="ac-career-copy-stack ac-career-copy-stack--light content-reveal animation-index-1" data-image-reveal>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $careerHeroParagraphs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p><?php echo e($paragraph); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($careerValues->isNotEmpty()): ?>
                        <ul class="ac-career-value-list content-reveal animation-index-2" data-image-reveal aria-label="<?php echo e($isCroatian ? 'Što nudimo' : 'What we offer'); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $careerValues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <i class="fa-duotone fa-thin fa-circle-check" aria-hidden="true"></i>
                                    <span><?php echo e($value); ?></span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="ac-career-hero-actions content-reveal animation-index-3" data-image-reveal>
                        <a href="#career-open-positions" class="button button-gold">
                            <span><?php echo e($isCroatian ? 'OTVORENE POZICIJE' : 'OPEN POSITIONS'); ?></span>
                        </a>
                    </div>
                </div>

                <div class="ac-career-hero-media content-reveal animation-index-1" data-image-reveal>
                    <figure class="ac-career-hero-image image-reveal-media">
                        <img
                            src="<?php echo e($careerHeroPhoto['src']); ?>"
                            alt="<?php echo e($careerHeroPhoto['alt']); ?>"
                            width="1386"
                            height="925"
                            loading="eager"
                            decoding="async"
                            fetchpriority="high"
                        >
                        <span class="image-reveal-curtain" aria-hidden="true"></span>
                    </figure>

                    <div class="ac-career-stat-card">
                        <strong>70+</strong>
                        <span><?php echo e($isCroatian ? 'stručnjaka iz računovodstva, financija, revizije i savjetovanja' : 'experts in accounting, finance, audit and advisory'); ?></span>
                    </div>
                </div>
            </div>
        </section>

        <section class="ac-career-development" aria-labelledby="ac-career-development-title">
            <div class="ac-career-container">
                <div class="ac-career-section-intro">
                    <h2 class="values-title services-index-intro-title ac-career-section-title" id="ac-career-development-title" data-words-slide-from-right aria-label="<?php echo e($careerProcessTitle); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($careerProcessTitle); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="values-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h2>

                    <div class="ac-career-section-copy content-reveal animation-index-1" data-image-reveal>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($careerProcess['kicker'] ?? '')) !== ''): ?>
                            <h3 class="ac-career-copy-heading"><?php echo e($careerProcess['kicker']); ?></h3>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($careerProcess['intro'] ?? '')) !== ''): ?>
                            <p><?php echo e($careerProcess['intro']); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($careerProcessSteps->isNotEmpty()): ?>
                    <div class="ac-career-development-grid">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $careerProcessSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="ac-career-development-card content-reveal animation-index-<?php echo e($loop->index % 4); ?>" data-image-reveal>
                                <div class="ac-career-card-head">
                                    <span class="ac-career-card-icon" aria-hidden="true">
                                        <i class="fa-duotone fa-thin fa-fw <?php echo e($processIconClasses[$loop->index] ?? 'fa-circle-check'); ?>"></i>
                                    </span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($step['step'] ?? '')) !== ''): ?>
                                        <span class="ac-career-card-number"><?php echo e($step['step']); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <h3><?php echo e($step['title']); ?></h3>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($step['description'] ?? '')) !== ''): ?>
                                    <p><?php echo e($step['description']); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($careerStories->isNotEmpty()): ?>
            <section class="ac-career-stories" aria-labelledby="ac-career-stories-title">
                <div class="ac-career-container">
                    <div class="ac-career-section-intro ac-career-stories-head">
                        <h2 class="values-title services-index-intro-title ac-career-section-title ac-career-stories-title" id="ac-career-stories-title" data-words-slide-from-right aria-label="<?php echo e($isCroatian ? 'Život u ALPHA CAPITALISU' : 'Life at ALPHA CAPITALIS'); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($isCroatian ? 'Život u ALPHA CAPITALISU' : 'Life at ALPHA CAPITALIS'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="values-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </h2>

                        <div class="ac-career-stories-intro content-reveal animation-index-1" data-image-reveal>
                            <h3><?php echo e($isCroatian ? 'Više od radnog mjesta' : 'More than a workplace'); ?></h3>
                        </div>
                    </div>

                    <div class="ac-career-story-grid">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $careerStories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $story): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="ac-career-story content-reveal animation-index-<?php echo e($loop->index); ?>" data-image-reveal>
                                <span class="ac-career-story-icon" aria-hidden="true">
                                    <i class="fa-duotone fa-thin fa-fw <?php echo e($storyIconClasses[$loop->index] ?? 'fa-star'); ?>"></i>
                                </span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($story['kicker'] !== ''): ?>
                                    <p class="ac-career-story-kicker"><?php echo e($story['kicker']); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <h3><?php echo e($story['title']); ?></h3>
                                <div class="ac-career-copy-stack ac-career-copy-stack--light">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $story['paragraphs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <p><?php echo e($paragraph); ?></p>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($story['list'] !== []): ?>
                                    <ul class="ac-career-story-list">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $story['list']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li>
                                                <i class="fa-duotone fa-thin fa-circle-check" aria-hidden="true"></i>
                                                <span><?php echo e($item); ?></span>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </ul>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <section id="career-open-positions" class="ac-career-openings" aria-labelledby="ac-career-openings-title">
            <div class="ac-career-container ac-career-openings-grid">
                <div class="ac-career-openings-copy">
                    <p class="ac-family-section-kicker ac-career-kicker"><?php echo e($isCroatian ? 'Prijave' : 'Applications'); ?></p>
                    <h2 class="values-title services-index-intro-title ac-career-section-title" id="ac-career-openings-title" data-words-slide-from-right aria-label="<?php echo e($careerApplicationTitle); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($careerApplicationTitle); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="values-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h2>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($careerApplicationHighlight !== ''): ?>
                        <p class="ac-career-openings-lead"><?php echo e($careerApplicationHighlight); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="ac-career-copy-stack">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $careerApplicationParagraphs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p><?php echo e($paragraph); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div id="career-cta" class="ac-career-form-wrap content-reveal animation-index-1" data-image-reveal>
                    <div class="ac-career-form-head">
                        <h3 id="ac-career-form-title"><?php echo e(trim((string) ($careerFormContent['title'] ?? '')) ?: __('career.form.title')); ?></h3>
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
                        data-file-empty-label="<?php echo e(__('career.form.cv_empty')); ?>"
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
                            <textarea id="career-message" name="message" rows="3" class="ac-career-form-textarea"><?php echo e(old('message')); ?></textarea>
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
                            <div class="ac-career-form-file-wrap">
                                <input id="career-cv" type="file" name="cv" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="ac-career-form-file" aria-describedby="career-cv-status career-cv-help" required>
                                <div class="ac-career-form-file-ui">
                                    <span class="ac-career-form-file-button">
                                        <i class="fa-duotone fa-thin fa-cloud-arrow-up" aria-hidden="true"></i>
                                        <?php echo e(__('career.form.cv_button')); ?>

                                    </span>
                                    <span id="career-cv-status" class="ac-career-form-file-name" data-file-name aria-live="polite"><?php echo e(__('career.form.cv_empty')); ?></span>
                                </div>
                            </div>
                            <p id="career-cv-help" class="ac-career-form-help"><?php echo e(__('career.form.cv_help')); ?></p>
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
                            <button type="submit" class="editorial-dark-button ac-career-submit-button">
                                <span><?php echo e(__('career.form.submit')); ?></span>
                                <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
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
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('front-theme/styles/pages/career.css')); ?>?v=<?php echo e(filemtime(public_path('front-theme/styles/pages/career.css'))); ?>">
<?php $__env->stopPush(); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($careerCaptchaEnabled): ?>
    <?php $__env->startPush('scripts'); ?>
        <script src="https://www.google.com/recaptcha/api.js?render=<?php echo e($careerCaptchaSiteKey); ?>"></script>
    <?php $__env->stopPush(); ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php $__env->startPush('scripts'); ?>
    <script defer src="<?php echo e(asset('front-theme/scripts/career.js')); ?>?v=<?php echo e(filemtime(public_path('front-theme/scripts/career.js'))); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/career.blade.php ENDPATH**/ ?>