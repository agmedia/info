<?php
    $isCroatian = str_starts_with(strtolower((string) ($locale ?? app()->getLocale())), 'hr');
    $overviewBody = array_values(array_filter(
        (array) ($overviewSection['body'] ?? []),
        static fn ($paragraph): bool => trim((string) $paragraph) !== '',
    ));
    $accountingServices = array_values(array_filter(
        (array) ($servicesSection['items'] ?? []),
        static fn ($item): bool => is_array($item) && trim((string) ($item['title'] ?? '')) !== '',
    ));
    $serviceIcons = [
        'fa-book-copy',
        'fa-user-tie-hair',
        'fa-file-certificate',
        'fa-chart-waterfall',
        'fa-building-shield',
        'fa-diagram-project',
    ];
    $approachBody = array_values(array_filter(
        (array) ($approachSection['body'] ?? []),
        static fn ($paragraph): bool => trim((string) $paragraph) !== '',
    ));
    $approachIntro = trim((string) ($approachSection['intro'] ?? ''));
    $meetingTitle = trim((string) ($meetingSection['title'] ?? ''))
        ?: ($isCroatian ? 'Razgovarajmo o vašem računovodstvu' : 'Let’s discuss your accounting');
    $meetingIntro = trim((string) ($meetingSection['intro'] ?? ''))
        ?: ($isCroatian
            ? 'Javite nam se - procijenit ćemo vaše potrebe i predložiti model računovodstvene podrške.'
            : 'Contact us and we will assess your needs and propose a suitable accounting support model.');
    $meetingCardTitle = trim((string) ($meetingSection['contact_title'] ?? ''))
        ?: ($isCroatian ? 'Kontaktirajte nas' : 'Contact us');
    $meetingButtonLabel = $isCroatian ? 'Dogovorite sastanak' : 'Schedule a meeting';
    $meetingStatus = $isCroatian ? 'Termin razgovora prilagođavamo vama.' : 'We arrange the meeting around your schedule.';
    $heroLabel = trim((string) ($heroSection['subtitle_lead'] ?? '')) ?: ($isCroatian ? 'Računovodstvo' : 'Accounting');
    $heroHook = trim((string) ($heroSection['intro'] ?? ''))
        ?: ($isCroatian
            ? 'Vi vodite poslovanje. Mi brinemo da Vaše brojke budu točne, pravovremene i spremne za svaku odluku.'
            : 'You run the business. We make sure your numbers are accurate, timely, and ready for every decision.');
    $heroImageAlt = $isCroatian ? 'Računovodstvene i financijske usluge' : 'Accounting and financial services';
    $blogHeadingTitle = $isCroatian
        ? 'Stručni uvidi u računovodstvo, izvještavanje i poslovne brojke'
        : 'Expert insights into accounting, reporting and business figures';
    $allPostsLabel = $isCroatian ? 'Pogledaj sve objave' : 'View all posts';
    $currentHost = request()->getHost();
    $sameOriginAssetUrl = static function (?string $url) use ($currentHost): string {
        $assetUrl = trim((string) $url);
        $assetHost = parse_url($assetUrl, PHP_URL_HOST);

        if ($assetUrl === '' || ($assetHost !== null && $assetHost !== $currentHost)) {
            return $assetUrl;
        }

        $assetPath = parse_url($assetUrl, PHP_URL_PATH);
        $assetQuery = parse_url($assetUrl, PHP_URL_QUERY);

        return is_string($assetPath) && $assetPath !== ''
            ? $assetPath.($assetQuery ? '?'.$assetQuery : '')
            : $assetUrl;
    };
    $heroImageUrl = $sameOriginAssetUrl((string) $heroBackgroundUrl);
    $headingWords = static fn (string $heading): array => preg_split('/\s+/u', trim($heading)) ?: [];
    $overviewLead = $isCroatian
        ? 'Mirnije poslovanje počinje jasnim i pouzdanim brojkama.'
        : 'Calmer business operations begin with clear and reliable numbers.';
    $overviewTitle = trim((string) ($overviewSection['title'] ?? ''));
    $overviewTitleBreakIndex = in_array($overviewTitle, [
        'Zašto Vam je računovodstvo bitno?',
        'Why does accounting matter to you?',
    ], true) ? 3 : null;
    $partnerStatements = array_slice($overviewBody, 1);
    $overviewBody = array_slice($overviewBody, 0, 1);

    if ($approachBody === [] && $approachIntro !== '') {
        $approachBody = [$approachIntro];
    }

    $hasAccountingPosts = ($accountingPosts ?? collect())->isNotEmpty();
?>

<?php $__env->startSection('title', $servicePageMetaTitle !== '' ? $servicePageMetaTitle : ($servicePageTitle ?? $heroLabel)); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('front-theme/styles/pages/audit.css')); ?>?v=<?php echo e(filemtime(public_path('front-theme/styles/pages/audit.css'))); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('front-theme/styles/pages/accounting.css')); ?>?v=<?php echo e(filemtime(public_path('front-theme/styles/pages/accounting.css'))); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="ac-audit-page ac-accounting-page">
        <section class="ac-audit-hero" id="vrh" aria-labelledby="ac-accounting-hero-title">
            <div class="ac-audit-hero-media">
                <img
                    src="<?php echo e($heroImageUrl); ?>"
                    alt="<?php echo e($heroImageAlt); ?>"
                    class="ac-audit-hero-image"
                    width="1366"
                    height="768"
                    loading="eager"
                    decoding="async"
                    fetchpriority="high"
                >
            </div>
            <div class="ac-audit-hero-overlay" aria-hidden="true"></div>

            <div class="ac-audit-hero-shell">
                <div class="ac-audit-hero-copy">
                    <h1 id="ac-accounting-hero-title" aria-label="<?php echo e($heroLabel); ?>. <?php echo e($heroHook); ?>">
                        <span class="ac-audit-hero-label"><?php echo e($heroLabel); ?></span>
                        <span class="ac-audit-hero-hook"><?php echo e($heroHook); ?></span>
                    </h1>
                </div>
            </div>
        </section>

        <section class="ac-audit-intro" id="accounting-overview" aria-labelledby="ac-accounting-overview-title">
            <div class="ac-audit-wide-shell ac-audit-intro-grid">
                <div class="ac-audit-intro-heading">
                    <h2
                        id="ac-accounting-overview-title"
                        class="<?php echo e($overviewTitleBreakIndex !== null ? 'has-fixed-two-lines' : ''); ?>"
                        data-words-slide-from-right
                        aria-label="<?php echo e($overviewTitle); ?>"
                    >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($overviewTitle); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($overviewTitleBreakIndex === $loop->index): ?>
                                <br aria-hidden="true">
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span class="service-title-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h2>
                </div>

                <div class="ac-audit-intro-copy content-reveal animation-index-1" data-image-reveal>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($overviewSection['intro'] ?? '')) !== ''): ?>
                        <p><?php echo e($overviewSection['intro']); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $overviewBody; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $paragraphText = trim((string) $paragraph); ?>
                        <p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($loop->first && str_starts_with($paragraphText, $overviewLead)): ?>
                                <strong><?php echo e($overviewLead); ?></strong><?php echo e(\Illuminate\Support\Str::after($paragraphText, $overviewLead)); ?>

                            <?php else: ?>
                                <?php echo e($paragraphText); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($partnerStatements !== []): ?>
            <section
                class="ac-audit-obligors ac-accounting-partner-note"
                aria-label="<?php echo e($isCroatian ? 'ALPHA CAPITALIS kao računovodstveni partner' : 'ALPHA CAPITALIS as your accounting partner'); ?>"
            >
                <div class="ac-audit-wide-shell ac-accounting-partner-note-shell">
                    <blockquote class="ac-accounting-partner-note-quote content-reveal" data-image-reveal>
                        <i class="fa-duotone fa-thin fa-quote-left" aria-hidden="true"></i>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $partnerStatements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p class="ac-accounting-partner-note-text">
                                <?php echo e(trim((string) $statement)); ?>

                            </p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </blockquote>
                </div>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <section class="ac-audit-services" id="accounting-services" aria-labelledby="ac-accounting-services-title">
            <div class="ac-audit-wide-shell">
                <header class="ac-audit-section-heading">
                    <h2 id="ac-accounting-services-title" data-words-slide-from-right aria-label="<?php echo e($servicesSection['title'] ?? ''); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords((string) ($servicesSection['title'] ?? '')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="service-title-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h2>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($servicesSection['intro'] ?? '')) !== ''): ?>
                        <p><?php echo e($servicesSection['intro']); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </header>

                <div class="ac-audit-services-grid">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $accountingServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="ac-audit-service-card content-reveal animation-index-<?php echo e($loop->index); ?>" data-image-reveal>
                            <span class="ac-audit-service-icon" aria-hidden="true">
                                <i class="fa-duotone fa-thin fa-fw <?php echo e($serviceIcons[$loop->index] ?? 'fa-calculator'); ?>"></i>
                            </span>
                            <h3><?php echo e($item['title'] ?? ''); ?></h3>
                            <p><?php echo e($item['text'] ?? ''); ?></p>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($approachBody !== []): ?>
            <section class="ac-audit-approach" aria-labelledby="ac-accounting-approach-title">
                <div class="ac-audit-wide-shell ac-audit-approach-grid">
                    <div class="ac-audit-approach-heading">
                        <h2 id="ac-accounting-approach-title" data-words-slide-from-right aria-label="<?php echo e($approachSection['title'] ?? ''); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords((string) ($approachSection['title'] ?? '')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="service-title-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </h2>
                    </div>

                    <blockquote class="ac-audit-approach-quote content-reveal animation-index-1" data-image-reveal>
                        <i class="fa-duotone fa-thin fa-quote-left" aria-hidden="true"></i>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $approachBody; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p><?php echo e($paragraph); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </blockquote>
                </div>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasAccountingPosts): ?>
            <section class="news-section ac-audit-news" aria-labelledby="ac-accounting-news-title">
                <div class="news-shell">
                    <header class="news-header">
                        <div class="ac-audit-news-heading-copy">
                            <h2 class="news-title" id="ac-accounting-news-title" data-words-slide-from-right aria-label="<?php echo e($blogHeadingTitle); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($blogHeadingTitle); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="news-title-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </h2>
                        </div>

                        <a class="news-all-link content-reveal" data-image-reveal href="<?php echo e($accountingArchiveUrl); ?>">
                            <span><?php echo e($allPostsLabel); ?></span>
                            <i class="fa-duotone fa-thin fa-arrow-right fa-fw" aria-hidden="true"></i>
                        </a>
                    </header>

                    <div class="news-grid">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $accountingPosts->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $translation = $post->translations->firstWhere('locale', $locale)
                                    ?? $post->translations->firstWhere('locale', $fallbackLocale);
                                $postSlug = trim((string) ($translation?->slug ?? ''));
                                $postUrl = $postSlug !== '' ? route('blog.show', ['slug' => $postSlug]) : route('blog.index');
                                $postTitle = trim((string) ($translation?->title ?? $post->code));
                                $postExcerpt = trim((string) ($translation?->excerpt ?? '')) ?: __('ui.blog.excerpt_fallback');
                                $postExcerpt = \Illuminate\Support\Str::limit($postExcerpt, 190, '...', true);
                                $primaryCategory = $post->categories
                                    ->sortByDesc(fn ($category) => (int) ($category->pivot->is_primary ?? false))
                                    ->first();
                                $categoryTranslation = $primaryCategory?->translations->firstWhere('locale', $locale)
                                    ?? $primaryCategory?->translations->firstWhere('locale', $fallbackLocale);
                                $categoryLabel = trim((string) ($categoryTranslation?->name ?? ($isCroatian ? 'Novosti' : 'News')));
                            ?>

                            <a class="news-card animation-index-<?php echo e($loop->index); ?>" data-image-reveal href="<?php echo e($postUrl); ?>" aria-label="<?php echo e($isCroatian ? 'Otvori blog post' : 'Open blog post'); ?>: <?php echo e($postTitle); ?>">
                                <span class="news-card-category"><?php echo e($categoryLabel); ?></span>
                                <h3><?php echo e($postTitle); ?></h3>
                                <p><?php echo e($postExcerpt); ?></p>
                                <span class="news-card-link" aria-hidden="true">
                                    <?php echo e($isCroatian ? 'Opširnije' : 'Read more'); ?>

                                    <i class="fa-duotone fa-thin fa-arrow-right fa-fw"></i>
                                </span>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <section class="contact-cta ac-audit-contact-cta" aria-labelledby="ac-accounting-contact-title">
            <div class="contact-cta-shell">
                <div class="contact-cta-copy">
                    <h2 class="contact-cta-title" id="ac-accounting-contact-title" data-words-slide-from-right aria-label="<?php echo e($meetingTitle); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($meetingTitle); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="contact-cta-title-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->remaining < 2 ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h2>
                </div>

                <div class="contact-cta-card" data-image-reveal>
                    <h3 class="contact-cta-card-heading"><?php echo e($meetingCardTitle); ?></h3>
                    <p><?php echo e($meetingIntro); ?></p>
                    <a class="contact-cta-button" href="<?php echo e(route('contact.create')); ?>">
                        <span><?php echo e($meetingButtonLabel); ?></span>
                        <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                    </a>
                    <small><span class="contact-cta-status-dot" aria-hidden="true"></span><?php echo e($meetingStatus); ?></small>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/accounting.blade.php ENDPATH**/ ?>