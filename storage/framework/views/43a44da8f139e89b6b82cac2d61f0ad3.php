<?php
    $isCroatian = str_starts_with(strtolower((string) ($locale ?? app()->getLocale())), 'hr');
    $overviewBody = array_values(array_filter(
        (array) ($overviewSection['body'] ?? []),
        static fn ($paragraph): bool => trim((string) $paragraph) !== '',
    ));
    $obligorsIntro = trim((string) ($obligorsSection['intro'] ?? ''));
    $obligorsPrimaryTitle = trim((string) ($obligorsSection['primary_title'] ?? ''));
    $obligorsPrimaryItems = array_values((array) ($obligorsSection['primary_items'] ?? []));
    $obligorsNote = trim((string) ($obligorsSection['note'] ?? ''));
    $useObligorsList = ($obligorsSection['display_mode'] ?? '') === 'list' && $obligorsPrimaryItems !== [];
    $auditServices = array_values(array_filter(
        (array) ($servicesSection['items'] ?? []),
        static fn ($item): bool => is_array($item) && trim((string) ($item['title'] ?? '')) !== '',
    ));
    $serviceIcons = [
        'fa-file-check',
        'fa-layer-group',
        'fa-magnifying-glass-chart',
        'fa-leaf',
        'fa-briefcase',
        'fa-shield-halved',
    ];
    $obligorIcons = [
        'fa-city',
        'fa-landmark-dome',
        'fa-chart-mixed',
        'fa-code-merge',
        'fa-coins',
        'fa-heart-pulse',
        'fa-file-invoice-dollar',
    ];
    $criteriaIcons = [
        'fa-wallet',
        'fa-chart-column',
        'fa-user-group',
    ];
    $approachBody = array_values(array_filter(
        (array) ($approachSection['body'] ?? []),
        static fn ($paragraph): bool => trim((string) $paragraph) !== '',
    ));
    $approachIntro = trim((string) ($approachSection['intro'] ?? ''));
    $meetingTitle = trim((string) ($meetingSection['title'] ?? ''))
        ?: ($isCroatian ? 'Razgovarajmo o vašem revizorskom angažmanu' : 'Let’s discuss your audit engagement');
    $meetingIntro = trim((string) ($meetingSection['intro'] ?? ''))
        ?: ($isCroatian
            ? 'Javite nam se - procijenit ćemo vaše potrebe i predložiti pristup koji odgovara veličini i specifičnostima vašeg poslovanja.'
            : 'Contact us and we will assess your needs and propose an approach suited to the size and specifics of your business.');
    $meetingCardTitle = trim((string) ($meetingSection['contact_title'] ?? ''))
        ?: ($isCroatian ? 'Kontaktirajte nas' : 'Contact us');
    $meetingButtonLabel = $isCroatian ? 'Dogovorite sastanak' : 'Schedule a meeting';
    $meetingStatus = $isCroatian ? 'Termin razgovora prilagođavamo vama.' : 'We arrange the meeting around your schedule.';
    $heroLabel = trim((string) ($heroSection['subtitle_lead'] ?? '')) ?: ($isCroatian ? 'Revizija' : 'Audit');
    $heroHook = trim((string) ($heroSection['intro'] ?? ''))
        ?: ($isCroatian
            ? 'Povjerenje u financijske informacije počinje neovisnom i stručnom revizijom.'
            : 'Trust in financial information begins with an independent and expert audit.');
    $heroImageAlt = $isCroatian ? 'Revizija financijskih izvještaja' : 'Financial statement audit';
    $blogHeadingTitle = $isCroatian
        ? 'Stručni uvidi u reviziju, izvještavanje i usklađenost'
        : 'Expert insights into audit, reporting and compliance';
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

    if ($approachBody === [] && $approachIntro !== '') {
        $approachBody = [$approachIntro];
    }

    $hasAuditPosts = ($auditPosts ?? collect())->isNotEmpty();
?>

<?php $__env->startSection('title', $servicePageMetaTitle !== '' ? $servicePageMetaTitle : ($servicePageTitle ?? $heroLabel)); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('front-theme/styles/pages/audit.css')); ?>?v=<?php echo e(filemtime(public_path('front-theme/styles/pages/audit.css'))); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="ac-audit-page">
        <section class="ac-audit-hero" id="vrh" aria-labelledby="ac-audit-hero-title">
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
                    <h1 id="ac-audit-hero-title" aria-label="<?php echo e($heroLabel); ?>. <?php echo e($heroHook); ?>">
                        <span class="ac-audit-hero-label"><?php echo e($heroLabel); ?></span>
                        <span class="ac-audit-hero-hook"><?php echo e($heroHook); ?></span>
                    </h1>
                </div>
            </div>
        </section>

        <section class="ac-audit-intro" id="audit-overview" aria-labelledby="ac-audit-overview-title">
            <div class="ac-audit-wide-shell ac-audit-intro-grid">
                <div class="ac-audit-intro-heading">
                    <h2 id="ac-audit-overview-title" data-words-slide-from-right aria-label="<?php echo e($overviewSection['title'] ?? ''); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords((string) ($overviewSection['title'] ?? '')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="service-title-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h2>
                </div>

                <div class="ac-audit-intro-copy content-reveal animation-index-1" data-image-reveal>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($overviewSection['intro'] ?? '')) !== ''): ?>
                        <p><?php echo e($overviewSection['intro']); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $overviewBody; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p class="<?php echo e($loop->last ? 'is-emphasis' : ''); ?>"><?php echo e($paragraph); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($obligorsIntro !== '' || $useObligorsList || $obligorsNote !== ''): ?>
            <section class="ac-audit-obligors" aria-labelledby="ac-audit-obligors-title">
                <div class="ac-audit-wide-shell ac-audit-obligors-grid">
                    <div class="ac-audit-obligors-heading">
                        <h2 id="ac-audit-obligors-title" data-words-slide-from-right aria-label="<?php echo e($obligorsSection['title'] ?? ''); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords((string) ($obligorsSection['title'] ?? '')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="service-title-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </h2>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($obligorsNote !== ''): ?>
                        <aside class="ac-audit-obligors-note content-reveal animation-index-1" data-image-reveal>
                            <p><?php echo e($obligorsNote); ?></p>
                        </aside>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="ac-audit-obligors-content content-reveal animation-index-1" data-image-reveal>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($useObligorsList): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($obligorsPrimaryTitle !== ''): ?>
                                <h3><?php echo e($obligorsPrimaryTitle); ?></h3>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <ul class="ac-audit-obligors-list">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $obligorsPrimaryItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $itemText = is_array($item) ? trim((string) ($item['text'] ?? '')) : trim((string) $item);
                                        $children = is_array($item) ? array_values((array) ($item['children'] ?? [])) : [];
                                    ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($itemText !== ''): ?>
                                        <li class="ac-audit-obligor-card <?php echo e($children !== [] ? 'ac-audit-obligor-card--wide' : ''); ?>">
                                            <span class="ac-audit-obligor-icon" aria-hidden="true">
                                                <i class="fa-duotone fa-thin fa-fw <?php echo e($obligorIcons[$loop->index] ?? 'fa-circle-check'); ?>"></i>
                                            </span>
                                            <div class="ac-audit-obligor-copy">
                                                <span class="ac-audit-obligor-title"><?php echo e($itemText); ?></span>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($children !== []): ?>
                                                    <ul class="ac-audit-obligor-criteria">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) $child) !== ''): ?>
                                                                <li>
                                                                    <i class="fa-duotone fa-thin fa-fw <?php echo e($criteriaIcons[$loop->index] ?? 'fa-badge-check'); ?>" aria-hidden="true"></i>
                                                                    <span><?php echo e($child); ?></span>
                                                                </li>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </ul>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </li>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </ul>
                        <?php elseif($obligorsIntro !== ''): ?>
                            <p><?php echo e($obligorsIntro); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <section class="ac-audit-services" id="audit-services" aria-labelledby="ac-audit-services-title">
            <div class="ac-audit-wide-shell">
                <header class="ac-audit-section-heading">
                    <h2 id="ac-audit-services-title" data-words-slide-from-right aria-label="<?php echo e($servicesSection['title'] ?? ''); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords((string) ($servicesSection['title'] ?? '')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="service-title-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h2>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($servicesSection['intro'] ?? '')) !== ''): ?>
                        <p><?php echo e($servicesSection['intro']); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </header>

                <div class="ac-audit-services-grid">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $auditServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="ac-audit-service-card content-reveal animation-index-<?php echo e($loop->index); ?>" data-image-reveal>
                            <span class="ac-audit-service-icon" aria-hidden="true">
                                <i class="fa-duotone fa-thin fa-fw <?php echo e($serviceIcons[$loop->index] ?? 'fa-chart-network'); ?>"></i>
                            </span>
                            <h3><?php echo e($item['title'] ?? ''); ?></h3>
                            <p><?php echo e($item['text'] ?? ''); ?></p>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($approachBody !== []): ?>
            <section class="ac-audit-approach" aria-labelledby="ac-audit-approach-title">
                <div class="ac-audit-wide-shell ac-audit-approach-grid">
                    <div class="ac-audit-approach-heading">
                        <h2 id="ac-audit-approach-title" data-words-slide-from-right aria-label="<?php echo e($approachSection['title'] ?? ''); ?>">
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

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasAuditPosts): ?>
            <section class="news-section ac-audit-news" aria-labelledby="ac-audit-news-title">
                <div class="news-shell">
                    <header class="news-header">
                        <div class="ac-audit-news-heading-copy">
                            <h2 class="news-title" id="ac-audit-news-title" data-words-slide-from-right aria-label="<?php echo e($blogHeadingTitle); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($blogHeadingTitle); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="news-title-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </h2>

                        </div>

                        <a class="news-all-link content-reveal" data-image-reveal href="<?php echo e($auditArchiveUrl); ?>">
                            <span><?php echo e($allPostsLabel); ?></span>
                            <i class="fa-duotone fa-thin fa-arrow-right fa-fw" aria-hidden="true"></i>
                        </a>
                    </header>

                    <div class="news-grid">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $auditPosts->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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

        <section class="contact-cta ac-audit-contact-cta" aria-labelledby="ac-audit-contact-title">
            <div class="contact-cta-shell">
                <div class="contact-cta-copy">
                    <h2 class="contact-cta-title" id="ac-audit-contact-title" data-words-slide-from-right aria-label="<?php echo e($meetingTitle); ?>">
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

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/audit.blade.php ENDPATH**/ ?>