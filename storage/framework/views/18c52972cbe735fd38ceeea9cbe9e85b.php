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
    $responsibilityCtaCardTitle = str_starts_with(strtolower((string) $locale), 'hr')
        ? 'Zajedno možemo više.'
        : 'Together, we can do more.';
    $responsibilityCtaStatus = str_starts_with(strtolower((string) $locale), 'hr')
        ? 'Otvoreni smo za razgovor i nova partnerstva.'
        : 'We are open to conversations and new partnerships.';

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
    $cultureLabel = trim((string) ($culture['kicker'] ?? '')) ?: (
        str_starts_with(strtolower((string) $locale), 'hr') ? 'Naša kultura' : 'Our culture'
    );
    $cultureTitle = trim((string) ($culture['title'] ?? '')) ?: 'Kvalitetno poslovanje počinje kvalitetnim odnosima';
    $cultureParagraphs = collect((array) ($culture['paragraphs'] ?? []))
        ->map(static fn ($paragraph): string => trim((string) $paragraph))
        ->filter()
        ->values();
    $cultureColumnSplit = $cultureParagraphs->count() >= 4 ? 2 : 1;
    $responsibilityLabel = trim((string) ($responsibility['kicker'] ?? '')) ?: (
        str_starts_with(strtolower((string) $locale), 'hr') ? 'Društveno odgovorno poslovanje' : 'Social responsibility'
    );
    $responsibilityTitle = trim((string) ($responsibility['title'] ?? ''));
    $responsibilityParagraphs = collect((array) ($responsibility['paragraphs'] ?? []))
        ->map(static fn ($paragraph): string => trim((string) $paragraph))
        ->filter()
        ->values();
    $referencesLabel = str_starts_with(strtolower((string) $locale), 'hr')
        ? 'Naše reference'
        : 'Our references';
    $referencesTitle = trim((string) ($references['title'] ?? '')) ?: 'Reference';
    $referenceParagraphs = collect((array) ($references['paragraphs'] ?? []))
        ->map(static fn ($paragraph): string => trim((string) $paragraph))
        ->filter()
        ->values();
    $valueIconClasses = [
        'fa-brain-circuit',
        'fa-lightbulb-gear',
        'fa-hands-holding-heart',
    ];
    $teamStatIconClasses = [
        'fa-people-group',
        'fa-users-crown',
        'fa-handshake',
        'fa-buildings',
    ];
?>

<?php $__env->startSection('title', $pageTitle); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>
<?php $__env->startSection('hide_footer_newsletter', '1'); ?>

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
                            <h2 class="ac-about-story-title ac-about-copy-heading ac-about-team-members-title" id="ac-about-team-title" data-words-slide-from-right aria-label="<?php echo e($teamTitle); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($teamTitle); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="service-title-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->remaining < 2 ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </h2>

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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aboutPreviewTeamMembers->isNotEmpty()): ?>
                        <div class="ac-about-member-grid">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $aboutPreviewTeamMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="ac-about-member-card content-reveal animation-index-<?php echo e($loop->index); ?>" data-image-reveal style="--reveal-index: <?php echo e($loop->index); ?>">
                                <div class="ac-about-member-photo <?php echo e(($member['photo_url'] ?? '') !== '' ? 'image-reveal-media' : ''); ?>">
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

                                    <div class="ac-about-member-info">
                                        <h3><?php echo e($member['name']); ?></h3>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($member['position'] ?? '')) !== ''): ?>
                                            <p><?php echo e($member['position']); ?></p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <article
                            class="ac-about-member-card ac-about-member-cta-card content-reveal animation-index-<?php echo e($aboutPreviewTeamMembers->count()); ?>"
                            data-image-reveal
                            style="--reveal-index: <?php echo e($aboutPreviewTeamMembers->count()); ?>"
                        >
                            <a href="<?php echo e(route('team.index')); ?>" class="ac-about-member-cta-link">
                                <span class="ac-about-member-cta-button">
                                    <span><?php echo e($teamButtonLabel); ?></span>
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
                <div class="ac-about-section-intro ac-about-culture-intro content-reveal" data-image-reveal>
                    <h2 class="values-title services-index-intro-title ac-about-section-intro-title ac-about-culture-label" id="ac-about-culture-title" data-words-slide-from-right aria-label="<?php echo e($cultureLabel); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($cultureLabel); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="values-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h2>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cultureTitle !== '' || $cultureQuote !== ''): ?>
                        <div class="values-copy services-index-intro-copy ac-about-section-intro-copy ac-about-culture-copy">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cultureTitle !== ''): ?>
                                <h3 class="ac-about-copy-heading ac-about-culture-copy-title"><?php echo e($cultureTitle); ?></h3>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cultureQuote !== ''): ?>
                                <blockquote class="ac-about-culture-quote">
                                    <p><?php echo e($cultureQuote); ?></p>
                                </blockquote>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cultureParagraphs->isNotEmpty()): ?>
                    <div class="ac-about-culture-body">
                        <div class="ac-about-culture-body-lead content-reveal animation-index-1" data-image-reveal>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cultureParagraphs->take($cultureColumnSplit); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p><?php echo e($paragraph); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="ac-about-copy-stack ac-about-culture-body-copy content-reveal animation-index-2" data-image-reveal>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cultureParagraphs->skip($cultureColumnSplit); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p><?php echo e($paragraph); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>

        <section class="ac-about-responsibility" aria-labelledby="ac-about-responsibility-title">
            <div class="ac-about-container">
                <div class="ac-about-section-intro ac-about-responsibility-intro content-reveal" data-image-reveal>
                    <h2 class="values-title services-index-intro-title ac-about-section-intro-title ac-about-responsibility-label" id="ac-about-responsibility-title" data-words-slide-from-right aria-label="<?php echo e($responsibilityLabel); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($responsibilityLabel); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="values-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h2>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($responsibilityTitle !== '' || $responsibilityQuote !== ''): ?>
                        <div class="values-copy services-index-intro-copy ac-about-section-intro-copy ac-about-responsibility-copy">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($responsibilityTitle !== ''): ?>
                                <h3 class="ac-about-copy-heading ac-about-responsibility-copy-title"><?php echo e($responsibilityTitle); ?></h3>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($responsibilityQuote !== ''): ?>
                                <blockquote class="ac-about-responsibility-quote">
                                    <p><?php echo e($responsibilityQuote); ?></p>
                                </blockquote>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($responsibilityParagraphs->isNotEmpty()): ?>
                    <div class="ac-about-responsibility-body">
                        <div class="ac-about-responsibility-body-lead content-reveal animation-index-1" data-image-reveal>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $responsibilityParagraphs->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p><?php echo e($paragraph); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="ac-about-copy-stack ac-about-responsibility-body-copy content-reveal animation-index-2" data-image-reveal>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $responsibilityParagraphs->skip(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p><?php echo e($paragraph); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            </div>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($responsibilityCtaIntro !== '' || $responsibilityCtaText !== ''): ?>
            <section class="contact-cta ac-about-contact-cta" aria-labelledby="ac-about-contact-cta-title">
                <div class="contact-cta-shell">
                    <div class="contact-cta-copy">
                        <h2 class="contact-cta-title" id="ac-about-contact-cta-title" data-words-slide-from-right aria-label="<?php echo e($responsibilityCtaIntro); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($responsibilityCtaIntro); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="contact-cta-title-word <?php echo e($loop->remaining < 2 ? 'is-accent' : ''); ?>" style="--services-word-index: <?php echo e($loop->index); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </h2>
                    </div>

                    <div class="contact-cta-card" data-image-reveal>
                        <div class="contact-cta-card-heading"><span><?php echo e($responsibilityCtaCardTitle); ?></span></div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($responsibilityCtaText !== ''): ?>
                            <p><?php echo e($responsibilityCtaText); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <a class="contact-cta-button" href="<?php echo e(route('contact.create')); ?>">
                            <span><?php echo e($responsibilityCtaLabel); ?></span>
                            <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                        </a>

                        <small><span class="contact-cta-status-dot" aria-hidden="true"></span><?php echo e($responsibilityCtaStatus); ?></small>
                    </div>
                </div>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <section class="ac-about-references" aria-labelledby="ac-about-references-title">
            <div class="ac-about-container">
                <div class="ac-about-section-intro ac-about-reference-head">
                    <h2 class="values-title services-index-intro-title ac-about-section-intro-title ac-about-reference-label" id="ac-about-references-title" data-words-slide-from-right aria-label="<?php echo e($referencesLabel); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($referencesLabel); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="values-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h2>

                    <div class="values-copy services-index-intro-copy ac-about-section-intro-copy ac-about-reference-copy content-reveal" data-image-reveal style="--reveal-index: 1">
                        <h3 class="ac-about-copy-heading ac-about-reference-copy-title"><?php echo e($referencesTitle); ?></h3>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($referenceParagraphs->isNotEmpty()): ?>
                    <div class="ac-about-reference-body">
                        <div class="ac-about-reference-body-lead content-reveal animation-index-1" data-image-reveal>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $referenceParagraphs->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p><?php echo e($paragraph); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="ac-about-copy-stack ac-about-reference-body-copy content-reveal animation-index-2" data-image-reveal>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $referenceParagraphs->skip(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p><?php echo e($paragraph); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aboutReferenceItems->isNotEmpty()): ?>
                    <div class="ac-about-reference-grid">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $aboutReferenceItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article
                                class="ac-about-reference-card"
                                data-image-reveal
                                style="--reveal-index: <?php echo e($loop->index % 5); ?>"
                                aria-label="<?php echo e($item['name']); ?>"
                            >
                                <div class="ac-about-reference-logo image-reveal-media">
                                    <img
                                        src="<?php echo e($item['url']); ?>"
                                        alt="<?php echo e($item['alt']); ?>"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                    <span class="image-reveal-curtain" aria-hidden="true"></span>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="ac-about-section-actions content-reveal" data-image-reveal style="--reveal-index: 1">
                    <a href="<?php echo e($referencePageUrl); ?>" class="front-action-cta ac-about-secondary-cta">
                        <span><?php echo e($referencesButtonLabel); ?></span>
                        <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
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