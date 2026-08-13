<?php
    $content = (array) ($advisoryContent ?? []);
    $hero = (array) ($content['hero'] ?? []);
    $overview = (array) ($content['overview'] ?? []);
    $services = (array) ($content['services_intro'] ?? []);
    $serviceCards = array_values(array_filter(
        (array) ($content['service_cards'] ?? []),
        static fn ($item): bool => is_array($item) && trim((string) ($item['title'] ?? '')) !== '',
    ));
    $pandea = (array) ($content['pandea'] ?? []);
    $approach = (array) ($content['approach'] ?? []);
    $meeting = (array) ($content['meeting'] ?? []);
    $isCroatian = str_starts_with(strtolower((string) ($locale ?? app()->getLocale())), 'hr');
    $overviewBody = array_values(array_filter(
        (array) ($overview['body'] ?? []),
        static fn ($paragraph): bool => trim((string) $paragraph) !== '',
    ));
    $pandeaBody = array_values(array_filter(
        (array) ($pandea['body'] ?? []),
        static fn ($paragraph): bool => trim((string) $paragraph) !== '',
    ));
    $networkTitle = trim((string) ($pandea['title'] ?? ''));
    $networkTitleLines = preg_split('/(?=Pandea Global M&A)/u', $networkTitle, 2, PREG_SPLIT_NO_EMPTY) ?: [$networkTitle];
    $approachBody = array_values(array_filter(
        (array) ($approach['body'] ?? []),
        static fn ($paragraph): bool => trim((string) $paragraph) !== '',
    ));
    $serviceIcons = [
        'fa-hand-holding-circle-dollar',
        'fa-people-arrows-left-right',
        'fa-magnifying-glass-dollar',
        'fa-chart-user',
        'fa-badge-percent',
    ];
    $heroLabel = trim((string) ($hero['subtitle_lead'] ?? '')) ?: ($isCroatian ? 'Savjetovanje' : 'Advisory');
    $heroHook = trim((string) ($hero['intro'] ?? ''));
    $heroImageAlt = $isCroatian ? 'Stručno financijsko i strateško savjetovanje' : 'Expert financial and strategic advisory';
    $meetingTitle = trim((string) ($meeting['title'] ?? ''))
        ?: ($isCroatian ? 'Razgovarajmo o vašim poslovnim odlukama' : 'Let’s discuss your business decisions');
    $meetingIntro = trim((string) ($meeting['intro'] ?? ''))
        ?: ($isCroatian
            ? 'Javite nam se i zajedno ćemo procijeniti koji oblik savjetodavne podrške najbolje odgovara vašem cilju.'
            : 'Contact us and we will assess which form of advisory support best fits your goal.');
    $meetingCardTitle = trim((string) ($meeting['contact_title'] ?? ''))
        ?: ($isCroatian ? 'Kontaktirajte nas' : 'Contact us');
    $meetingButtonLabel = $isCroatian ? 'Dogovorite sastanak' : 'Schedule a meeting';
    $meetingStatus = $isCroatian ? 'Termin razgovora prilagođavamo vama.' : 'We arrange the meeting around your schedule.';
    $blogHeadingTitle = $isCroatian
        ? 'Stručni uvidi u financije, poreze i transakcije'
        : 'Expert insights into finance, tax and transactions';
    $allPostsLabel = $isCroatian ? 'Pogledaj sve objave' : 'View all posts';
    $readMoreLabel = $isCroatian ? 'Opširnije' : 'Read more';
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
    $resolveContentUrl = static function (?string $url): string {
        $target = trim((string) $url);

        if ($target === '' || str_starts_with($target, '#') || str_starts_with($target, 'http://') || str_starts_with($target, 'https://')) {
            return $target;
        }

        return url(str_starts_with($target, '/') ? $target : '/'.$target);
    };
    $headingWords = static fn (string $heading): array => preg_split('/\s+/u', trim($heading)) ?: [];
    $heroImageUrl = $sameOriginAssetUrl((string) $heroBackgroundUrl);
    $networkLogoUrl = $sameOriginAssetUrl((string) ($pandeaLogoUrl ?? ''));
    $hasAdvisoryPosts = ($advisoryPosts ?? collect())->isNotEmpty();
?>

<?php $__env->startSection('title', $servicePageMetaTitle !== '' ? $servicePageMetaTitle : ($servicePageTitle ?? $heroLabel)); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('front-theme/styles/pages/advisory.css')); ?>?v=<?php echo e(filemtime(public_path('front-theme/styles/pages/advisory.css'))); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="ac-advisory-page">
        <section class="ac-advisory-hero" id="vrh" aria-labelledby="ac-advisory-hero-title">
            <div class="ac-advisory-hero-media">
                <img
                    src="<?php echo e($heroImageUrl); ?>"
                    alt="<?php echo e($heroImageAlt); ?>"
                    class="ac-advisory-hero-image"
                    width="1366"
                    height="768"
                    loading="eager"
                    decoding="async"
                    fetchpriority="high"
                >
            </div>
            <div class="ac-advisory-hero-overlay" aria-hidden="true"></div>

            <div class="ac-advisory-hero-shell">
                <div class="ac-advisory-hero-copy">
                    <h1 id="ac-advisory-hero-title" aria-label="<?php echo e($heroLabel); ?>. <?php echo e($heroHook); ?>">
                        <span class="ac-advisory-hero-label"><?php echo e($heroLabel); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($heroHook !== ''): ?>
                            <span class="ac-advisory-hero-hook"><?php echo e($heroHook); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h1>
                </div>
            </div>
        </section>

        <section class="ac-advisory-intro" id="advisory-overview" aria-labelledby="ac-advisory-overview-title">
            <div class="ac-advisory-wide-shell ac-advisory-intro-grid">
                <div class="ac-advisory-intro-heading">
                    <h2 id="ac-advisory-overview-title" data-words-slide-from-right aria-label="<?php echo e($overview['title'] ?? ''); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords((string) ($overview['title'] ?? '')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="service-title-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h2>
                </div>

                <div class="ac-advisory-intro-copy content-reveal animation-index-1" data-image-reveal>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $overviewBody; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p class="<?php echo e($loop->last ? 'is-emphasis' : ''); ?>"><?php echo e($paragraph); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pandeaBody !== []): ?>
            <section class="ac-advisory-network" id="advisory-network" aria-labelledby="ac-advisory-network-title">
                <div class="ac-advisory-wide-shell ac-advisory-network-grid">
                    <div class="ac-advisory-network-heading">
                        <h2 id="ac-advisory-network-title" data-words-slide-from-right aria-label="<?php echo e($networkTitle); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $networkTitleLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="ac-advisory-network-title-line">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($line); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="service-title-word animation-index-<?php echo e($loop->parent->index + $loop->index); ?> <?php echo e($loop->parent->last && $loop->index > 0 ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </h2>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($networkLogoUrl !== ''): ?>
                            <div class="ac-advisory-network-logo-card content-reveal" data-image-reveal>
                                <img
                                    src="<?php echo e($networkLogoUrl); ?>"
                                    alt="<?php echo e($pandea['logo_alt'] ?? 'Pandea Global M&A'); ?>"
                                    class="ac-advisory-network-logo"
                                    width="380"
                                    height="100"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="ac-advisory-network-copy content-reveal animation-index-1" data-image-reveal>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pandeaBody; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p><?php echo e($paragraph); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <section class="ac-advisory-services" id="advisory-services" aria-labelledby="ac-advisory-services-title">
            <div class="ac-advisory-wide-shell">
                <header class="ac-advisory-section-heading">
                    <h2 id="ac-advisory-services-title" data-words-slide-from-right aria-label="<?php echo e($services['title'] ?? ''); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords((string) ($services['title'] ?? '')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="service-title-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h2>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($services['intro'] ?? '')) !== ''): ?>
                        <p><?php echo e($services['intro']); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </header>

                <div class="ac-advisory-services-grid">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $serviceCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $cardUrl = $resolveContentUrl($card['url'] ?? ''); ?>

                        <a class="ac-advisory-service-card animation-index-<?php echo e($loop->index); ?>" data-image-reveal href="<?php echo e($cardUrl !== '' ? $cardUrl : '#advisory-services'); ?>">
                            <span class="ac-advisory-service-icon" aria-hidden="true">
                                <i class="fa-duotone fa-thin fa-fw <?php echo e($serviceIcons[$loop->index] ?? 'fa-chart-network'); ?>"></i>
                            </span>
                            <h3><?php echo e($card['title'] ?? ''); ?></h3>
                            <p><?php echo e($card['text'] ?? ''); ?></p>
                            <span class="ac-advisory-service-link" aria-hidden="true">
                                <?php echo e($readMoreLabel); ?>

                                <i class="fa-duotone fa-thin fa-arrow-right fa-fw"></i>
                            </span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($approachBody !== []): ?>
            <section class="ac-advisory-approach" aria-labelledby="ac-advisory-approach-title">
                <div class="ac-advisory-wide-shell ac-advisory-approach-grid">
                    <div class="ac-advisory-approach-heading">
                        <h2 id="ac-advisory-approach-title" data-words-slide-from-right aria-label="<?php echo e($approach['title'] ?? ''); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords((string) ($approach['title'] ?? '')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="service-title-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </h2>
                    </div>

                    <blockquote class="ac-advisory-approach-quote content-reveal animation-index-1" data-image-reveal>
                        <i class="fa-duotone fa-thin fa-quote-left" aria-hidden="true"></i>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $approachBody; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p><?php echo e($paragraph); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </blockquote>
                </div>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasAdvisoryPosts): ?>
            <section class="news-section ac-advisory-news" aria-labelledby="ac-advisory-news-title">
                <div class="news-shell">
                    <header class="news-header">
                        <h2 class="news-title" id="ac-advisory-news-title" data-words-slide-from-right aria-label="<?php echo e($blogHeadingTitle); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($blogHeadingTitle); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="news-title-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </h2>

                        <a class="news-all-link content-reveal" data-image-reveal href="<?php echo e($advisoryArchiveUrl); ?>">
                            <span><?php echo e($allPostsLabel); ?></span>
                            <i class="fa-duotone fa-thin fa-arrow-right fa-fw" aria-hidden="true"></i>
                        </a>
                    </header>

                    <div class="news-grid">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $advisoryPosts->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                    <?php echo e($readMoreLabel); ?>

                                    <i class="fa-duotone fa-thin fa-arrow-right fa-fw"></i>
                                </span>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <section class="contact-cta ac-advisory-contact-cta" aria-labelledby="ac-advisory-contact-title">
            <div class="contact-cta-shell">
                <div class="contact-cta-copy">
                    <h2 class="contact-cta-title" id="ac-advisory-contact-title" data-words-slide-from-right aria-label="<?php echo e($meetingTitle); ?>">
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

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/advisory.blade.php ENDPATH**/ ?>