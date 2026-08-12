<?php
    $translation = $selectedTranslation
        ?? $page->translations->firstWhere('locale', $locale)
        ?? $page->translations->firstWhere('locale', $fallbackLocale);

    $content = is_array($aboutContent ?? null) ? $aboutContent : [];
    $hero = is_array($content['hero'] ?? null) ? $content['hero'] : [];
    $story = is_array($content['story'] ?? null) ? $content['story'] : [];
    $values = is_array($content['values'] ?? null) ? $content['values'] : [];
    $why = is_array($content['why'] ?? null) ? $content['why'] : [];
    $team = is_array($content['team'] ?? null) ? $content['team'] : [];
    $culture = is_array($content['culture'] ?? null) ? $content['culture'] : [];
    $responsibility = is_array($content['responsibility'] ?? null) ? $content['responsibility'] : [];
    $references = is_array($content['references'] ?? null) ? $content['references'] : [];
    $aboutTeamMembers = collect($aboutTeamMembers ?? [])->values();
    $aboutPreviewTeamMembers = $aboutTeamMembers->take(3)->values();
    $aboutReferenceItems = collect($aboutReferenceItems ?? [])->values();
    $aboutHeroPhoto = [
        'class' => 'ac-about-image--hero',
        'src' => asset('front-theme/images/about/o-nama-alpha-capitalis.jpg'),
        'alt' => str_starts_with(strtolower((string) $locale), 'hr')
            ? 'ALPHA CAPITALIS tim'
            : 'ALPHA CAPITALIS team',
    ];
    $referencePageUrl = route('pages.show', ['slug' => 'reference']);
    $teamButtonLabel = str_starts_with(strtolower((string) $locale), 'hr') ? 'Upoznaj cijeli tim' : 'Meet the full team';
    $referencesButtonLabel = str_starts_with(strtolower((string) $locale), 'hr') ? 'Sve reference' : 'All references';
    $heroStatLabel = str_starts_with(strtolower((string) $locale), 'hr')
        ? 'klijenata kojima svakodnevno pružamo podršku'
        : 'clients supported by our team';
    $whyQuote = trim((string) ($why['quote'] ?? ''));
    $cultureQuote = trim((string) ($culture['quote'] ?? ''));
    $responsibilityQuote = trim((string) ($responsibility['quote'] ?? ''));
    $responsibilityCtaIntro = trim((string) ($responsibility['cta_intro'] ?? ''));
    $responsibilityCtaText = trim((string) ($responsibility['cta_text'] ?? ''));
    $responsibilityCtaLabel = trim((string) ($responsibility['cta_button_label'] ?? '')) ?: (
        str_starts_with(strtolower((string) $locale), 'hr') ? 'Kontaktirajte nas' : 'Contact us'
    );

    $pageTitle = trim((string) ($translation?->title ?? '')) ?: 'O nama';
    $heroTitle = trim((string) ($hero['title'] ?? '')) ?: $pageTitle;
    $heroLead = trim((string) ($hero['lead'] ?? ''));
    $storyParagraphs = collect((array) ($story['paragraphs'] ?? []))
        ->map(static fn ($paragraph): string => trim((string) $paragraph))
        ->filter()
        ->values();
    $introStoryHtml = e((string) ($storyParagraphs->first() ?? ''));
    $introStoryHtml = str_replace(
        'ALPHA CAPITALIS',
        '<a class="services-index-inline-link" href="'.e(route('contact.create')).'">ALPHA CAPITALIS</a>',
        $introStoryHtml,
    );
    $headingWords = static fn (string $title): array => preg_split('/\s+/u', trim($title), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $valuesLabel = str_starts_with(strtolower((string) $locale), 'hr')
        ? 'Naše vrijednosti'
        : 'Our values';
    $valuesTitle = trim((string) ($values['title'] ?? '')) ?: 'Jednostavni principi koji vode svaki dan';
    $valuesIntro = trim((string) ($values['intro'] ?? ''));
    $valuesIntroLinkText = str_contains($valuesIntro, 'ALPHA CAPITALISU')
        ? 'ALPHA CAPITALISU'
        : 'ALPHA CAPITALIS';
    $valuesIntroHtml = str_replace(
        $valuesIntroLinkText,
        '<a class="services-index-inline-link" href="'.e(route('contact.create')).'">'.e($valuesIntroLinkText).'</a>',
        e($valuesIntro),
    );
    $whyLabel = trim((string) ($why['kicker'] ?? '')) ?: 'Zašto postojimo';
    $whyTitle = trim((string) ($why['title'] ?? '')) ?: 'Podrška za sigurno, kvalitetno i održivo poslovanje';
    $whyParagraphs = collect((array) ($why['paragraphs'] ?? []))
        ->map(static fn ($paragraph): string => trim((string) $paragraph))
        ->filter()
        ->values();
    $whyServiceTermLinks = [
        'strateškog razvoja' => route('advisory.show'),
        'računovodstva' => route('accounting.show'),
        'EU fondova' => route('eu-funds.show'),
        'financija' => route('advisory.finance.show'),
        'revizije' => route('audit.show'),
        'strategic development' => route('advisory.show'),
        'accounting' => route('accounting.show'),
        'EU funds' => route('eu-funds.show'),
        'finance' => route('advisory.finance.show'),
        'audit' => route('audit.show'),
    ];
    $linkWhyServiceTerms = static function (string $paragraph) use ($whyServiceTermLinks): string {
        $replacements = [];

        foreach ($whyServiceTermLinks as $term => $url) {
            $replacements[e($term)] = '<a class="ac-about-dark-inline-link" href="'.e($url).'">'.e($term).'</a>';
        }

        return strtr(e($paragraph), $replacements);
    };
    $teamTitle = trim((string) ($team['title'] ?? '')) ?: 'Tim';
    $teamLabel = str_starts_with(strtolower((string) $locale), 'hr') ? 'Naš tim' : 'Our team';
    $teamStats = collect((array) ($team['stats'] ?? []))
        ->map(static fn ($stat): array => is_array($stat) ? $stat : [])
        ->filter(static fn (array $stat): bool => trim((string) ($stat['value'] ?? '')) !== '')
        ->values();
    $cultureTitle = trim((string) ($culture['title'] ?? '')) ?: 'Naša kultura';
    $responsibilityTitle = trim((string) ($responsibility['title'] ?? ''));
    $referencesTitle = trim((string) ($references['title'] ?? '')) ?: 'Reference';
    $valueIconClasses = [
        'fa-brain-circuit',
        'fa-lightbulb-gear',
        'fa-hands-holding-heart',
    ];
    $teamStatIconClasses = [
        'fa-users-gear',
        'fa-chess-king',
        'fa-user-check',
        'fa-location-dot',
    ];
?>

<?php $__env->startSection('title', $pageTitle); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startSection('content'); ?>
    <div class="ac-about-page">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($topBlocks->isNotEmpty()): ?>
            <section class="ac-about-blocks ac-about-blocks--top"><?php echo $__env->make('components.content-placement', ['items' => $topBlocks], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <section class="values-section services-index-intro ac-about-intro" aria-labelledby="ac-about-hero-title">
            <div class="values-inner services-index-intro-layout ac-about-intro-layout">
                <div class="values-intro">
                    <h1 class="values-title services-index-intro-title ac-about-intro-title" id="ac-about-hero-title" data-words-slide-from-right aria-label="<?php echo e($heroTitle); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($heroTitle); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="values-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h1>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($storyParagraphs->isNotEmpty()): ?>
                    <div class="values-copy services-index-intro-copy ac-about-intro-copy content-reveal" data-image-reveal>
                        <p><?php echo $introStoryHtml; ?></p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>

        <section class="ac-about-hero" aria-label="<?php echo e($heroTitle); ?>">
            <div class="ac-about-container">
                <div class="ac-about-hero-grid content-reveal" data-image-reveal>
                    <div class="ac-about-hero-media">
                        <figure class="ac-about-image image-reveal-media <?php echo e($aboutHeroPhoto['class']); ?>">
                            <img
                                src="<?php echo e($aboutHeroPhoto['src']); ?>"
                                alt="<?php echo e($aboutHeroPhoto['alt']); ?>"
                                width="1386"
                                height="925"
                                loading="eager"
                                decoding="async"
                                fetchpriority="high"
                            >
                            <span class="image-reveal-curtain" aria-hidden="true"></span>
                        </figure>

                        <div class="ac-about-stat-card">
                            <strong>600+</strong>
                            <span><?php echo e($heroStatLabel); ?></span>
                        </div>
                    </div>

                    <div class="ac-about-copy-stack ac-about-hero-story">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($heroLead !== ''): ?>
                            <h2 class="ac-about-story-title" data-words-slide-from-right aria-label="<?php echo e($heroLead); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($heroLead); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="service-title-word animation-index-<?php echo e($loop->index); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </h2>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $storyParagraphs->skip(1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $storyParagraphHtml = str_replace(
                                    'ALPHA CAPITALIS',
                                    '<a class="ac-about-dark-inline-link" href="'.e(route('contact.create')).'">ALPHA CAPITALIS</a>',
                                    e($paragraph),
                                );
                            ?>
                            <p><?php echo $storyParagraphHtml; ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="ac-about-values" aria-labelledby="ac-about-values-title">
            <div class="ac-about-container">
                <div class="ac-about-section-intro ac-about-values-intro content-reveal" data-image-reveal>
                    <h2 class="values-title services-index-intro-title ac-about-section-intro-title ac-about-values-label" id="ac-about-values-title" data-words-slide-from-right aria-label="<?php echo e($valuesLabel); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($valuesLabel); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="values-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h2>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($valuesIntro !== ''): ?>
                        <div class="values-copy services-index-intro-copy ac-about-section-intro-copy ac-about-values-copy">
                            <h3 class="ac-about-copy-heading ac-about-values-copy-title"><?php echo e($valuesTitle); ?></h3>
                            <p><?php echo $valuesIntroHtml; ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="ac-about-value-grid">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = (array) ($values['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $item = is_array($item) ? $item : [];
                            $itemTitle = trim((string) ($item['title'] ?? ''));
                        ?>

                        <?php if($itemTitle === '') continue; ?>

                        <article class="ac-about-value-card content-reveal animation-index-<?php echo e($loop->index); ?>" data-image-reveal>
                            <span class="ac-about-value-icon" aria-hidden="true">
                                <i class="fa-duotone fa-thin fa-fw <?php echo e($valueIconClasses[$loop->index] ?? 'fa-circle-check'); ?>"></i>
                            </span>
                            <h3><?php echo e($itemTitle); ?></h3>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($item['lead'] ?? '')) !== ''): ?>
                                <p class="ac-about-card-lead"><?php echo e($item['lead']); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="ac-about-copy-stack">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = (array) ($item['paragraphs'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(trim((string) $paragraph) === '') continue; ?>
                                    <p><?php echo e($paragraph); ?></p>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <section class="ac-about-why" aria-labelledby="ac-about-why-title">
            <div class="ac-about-container">
                <div class="ac-about-section-intro ac-about-why-intro content-reveal" data-image-reveal>
                    <h2 class="values-title services-index-intro-title ac-about-section-intro-title ac-about-why-title" id="ac-about-why-title" data-words-slide-from-right aria-label="<?php echo e($whyLabel); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($whyLabel); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="values-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h2>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($whyTitle !== '' || $whyQuote !== ''): ?>
                        <div class="values-copy services-index-intro-copy ac-about-section-intro-copy ac-about-why-copy">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($whyTitle !== ''): ?>
                                <h3 class="ac-about-copy-heading ac-about-copy-heading--light"><?php echo e($whyTitle); ?></h3>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($whyQuote !== ''): ?>
                                <blockquote class="ac-about-why-quote">
                                    <p><?php echo e($whyQuote); ?></p>
                                </blockquote>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($whyParagraphs->isNotEmpty()): ?>
                    <div class="ac-about-why-body">
                        <div class="ac-about-why-body-lead content-reveal animation-index-1" data-image-reveal>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $whyParagraphs->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p><?php echo $linkWhyServiceTerms($paragraph); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="ac-about-copy-stack ac-about-why-body-copy content-reveal animation-index-2" data-image-reveal>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $whyParagraphs->skip(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p><?php echo $linkWhyServiceTerms($paragraph); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>

        <section class="ac-about-team" aria-labelledby="ac-about-team-intro-title">
            <div class="ac-about-team-intro">
                <div class="ac-about-container">
                    <div class="ac-about-section-intro ac-about-team-intro-grid content-reveal" data-image-reveal>
                        <h2 class="values-title services-index-intro-title ac-about-section-intro-title ac-about-team-label" id="ac-about-team-intro-title" data-words-slide-from-right aria-label="<?php echo e($teamLabel); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($teamLabel); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="values-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </h2>

                        <div class="values-copy services-index-intro-copy ac-about-section-intro-copy ac-about-team-copy">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($team['intro'] ?? '')) !== ''): ?>
                                <p class="ac-about-team-lead"><?php echo e($team['intro']); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($team['body'] ?? '')) !== ''): ?>
                                <p><?php echo e($team['body']); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($teamStats->isNotEmpty()): ?>
                <div class="ac-about-team-stats" role="region" aria-label="<?php echo e($teamLabel); ?>" data-locations-reveal>
                    <div class="ac-about-team-stats-shell">
                        <div class="locations-stats ac-about-stat-grid">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $teamStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $statValue = trim((string) ($stat['value'] ?? ''));
                                $statCountTo = preg_replace('/\D+/', '', $statValue) ?: '';
                                $statSuffix = $statCountTo !== '' ? trim(str_replace($statCountTo, '', $statValue)) : '';
                            ?>

                            <article class="location-stat ac-about-stat-item">
                                <div class="location-stat-icon" aria-hidden="true">
                                    <i class="fa-duotone fa-thin fa-fw <?php echo e($teamStatIconClasses[$loop->index] ?? 'fa-circle-check'); ?>"></i>
                                </div>
                                <div>
                                    <strong>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statCountTo !== ''): ?>
                                            <span data-count-target="<?php echo e($statCountTo); ?>">0</span><span class="location-stat-suffix"><?php echo e($statSuffix); ?></span>
                                        <?php else: ?>
                                            <?php echo e($statValue); ?>

                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </strong>
                                    <p><?php echo e($stat['label'] ?? ''); ?></p>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="ac-about-team-members" role="region" aria-labelledby="ac-about-team-title">
                <div class="ac-about-container">
                    <header class="ac-about-team-members-head content-reveal" data-image-reveal>
                        <h2 class="ac-about-story-title ac-about-team-members-title" id="ac-about-team-title" data-words-slide-from-right aria-label="<?php echo e($teamTitle); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($teamTitle); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="service-title-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->remaining < 2 ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </h2>
                    </header>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aboutPreviewTeamMembers->isNotEmpty()): ?>
                        <div class="ac-about-member-grid">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $aboutPreviewTeamMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="ac-about-member-card content-reveal animation-index-<?php echo e($loop->index); ?>" data-image-reveal>
                                <div class="ac-about-member-photo">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($member['photo_url'] ?? '') !== ''): ?>
                                        <img
                                            src="<?php echo e($member['photo_url']); ?>"
                                            alt="<?php echo e($member['name']); ?>"
                                            loading="lazy"
                                            decoding="async"
                                        >
                                    <?php else: ?>
                                        <span><?php echo e($member['initials'] ?? 'AC'); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <div>
                                    <h3><?php echo e($member['name']); ?></h3>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($member['position'] ?? '')) !== ''): ?>
                                        <p><?php echo e($member['position']); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <article class="ac-about-member-card ac-about-member-cta-card">
                            <a href="<?php echo e(route('team.index')); ?>" class="ac-about-member-cta-link">
                                <span class="ac-about-member-cta-button">
                                    <span><?php echo e($teamButtonLabel); ?></span>
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M4 12L12 4"></path>
                                        <path d="M6 4h6v6"></path>
                                    </svg>
                                </span>
                            </a>
                        </article>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <section class="ac-about-culture" aria-labelledby="ac-about-culture-title">
            <div class="ac-about-container">
                <div class="ac-about-split-grid content-reveal" data-image-reveal>
                    <div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($culture['kicker'] ?? '')) !== ''): ?>
                            <p class="ac-about-kicker"><?php echo e($culture['kicker']); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <h2 class="values-title ac-about-heading" id="ac-about-culture-title" data-words-slide-from-right aria-label="<?php echo e($cultureTitle); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($cultureTitle); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="values-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </h2>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cultureQuote !== ''): ?>
                            <blockquote class="ac-about-pullquote">
                                <p><?php echo e($cultureQuote); ?></p>
                            </blockquote>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="ac-about-copy-stack ac-about-copy-stack--large">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = (array) ($culture['paragraphs'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(trim((string) $paragraph) === '') continue; ?>
                            <p><?php echo e($paragraph); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="ac-about-responsibility" aria-labelledby="ac-about-responsibility-title">
            <div class="ac-about-container">
                <div class="ac-about-responsibility-grid content-reveal" data-image-reveal>
                    <div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($responsibility['kicker'] ?? '')) !== ''): ?>
                            <p class="ac-about-kicker"><?php echo e($responsibility['kicker']); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <h2 class="values-title ac-about-heading" id="ac-about-responsibility-title" data-words-slide-from-right aria-label="<?php echo e($responsibilityTitle); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($responsibilityTitle); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="values-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </h2>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($responsibilityQuote !== ''): ?>
                            <blockquote class="ac-about-pullquote">
                                <p><?php echo e($responsibilityQuote); ?></p>
                            </blockquote>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div>
                        <div class="ac-about-copy-stack">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = (array) ($responsibility['paragraphs'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(trim((string) $paragraph) === '') continue; ?>
                                <p><?php echo e($paragraph); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($responsibilityCtaIntro !== '' || $responsibilityCtaText !== ''): ?>
                    <div class="ac-about-wide-cta">
                        <div class="ac-about-wide-cta-copy">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($responsibilityCtaIntro !== ''): ?>
                                <h3><?php echo e($responsibilityCtaIntro); ?></h3>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($responsibilityCtaText !== ''): ?>
                                <p><?php echo e($responsibilityCtaText); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <a href="<?php echo e(route('contact.create')); ?>" class="ac-about-wide-cta-link">
                            <span><?php echo e($responsibilityCtaLabel); ?></span>
                        </a>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>

        <section class="ac-about-references" aria-labelledby="ac-about-references-title">
            <div class="ac-about-container">
                <div class="ac-about-reference-head content-reveal" data-image-reveal>
                    <div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($references['kicker'] ?? '')) !== ''): ?>
                            <p class="ac-about-kicker"><?php echo e($references['kicker']); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <h2 class="values-title ac-about-heading" id="ac-about-references-title" data-words-slide-from-right aria-label="<?php echo e($referencesTitle); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($referencesTitle); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="values-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </h2>
                    </div>

                    <div class="ac-about-copy-stack">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = (array) ($references['paragraphs'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(trim((string) $paragraph) === '') continue; ?>
                            <p><?php echo e($paragraph); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aboutReferenceItems->isNotEmpty()): ?>
                    <div class="ac-about-reference-grid">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $aboutReferenceItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="ac-about-reference-card content-reveal animation-index-<?php echo e($loop->index); ?>" data-image-reveal aria-label="<?php echo e($item['name']); ?>">
                                <img
                                    src="<?php echo e($item['url']); ?>"
                                    alt="<?php echo e($item['alt']); ?>"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="ac-about-section-actions">
                    <a href="<?php echo e($referencePageUrl); ?>" class="front-action-cta ac-about-secondary-cta">
                        <span><?php echo e($referencesButtonLabel); ?></span>
                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 12L12 4"></path>
                            <path d="M6 4h6v6"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bottomBlocks->isNotEmpty()): ?>
            <section class="ac-about-blocks ac-about-blocks--bottom"><?php echo $__env->make('components.content-placement', ['items' => $bottomBlocks], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('front-theme/styles/pages/about.css')); ?>">
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/about.blade.php ENDPATH**/ ?>