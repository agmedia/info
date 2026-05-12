<?php
    $captchaSiteKey = trim((string) ($storeSettings['captcha']['recaptcha_v3_site_key'] ?? ''));
    $captchaEnabled = (bool) ($storeSettings['captcha']['recaptcha_v3_enabled'] ?? false) && $captchaSiteKey !== '';
    $contactEmail = trim((string) ($storeSettings['footer']['email_support'] ?? '')) ?: 'info@alphacapitalis.com';
    $contactPhone = trim((string) ($storeSettings['footer']['phone'] ?? '')) ?: '+385 (1) 580 6656';
    $contactPhoneHref = preg_replace('/\s+/', '', $contactPhone);
    $isCroatianLocale = str_starts_with(strtolower((string) $locale), 'hr');
    $accountingSprite = asset('front-theme/fonts/sprites/solid.svg');
    $meetingFormLabels = $meetingSection['form_labels'] ?? [];
    $introBody = array_values($introSection['body'] ?? []);
    $introItems = array_values($introSection['items'] ?? []);
    $introLead = $introBody[0] ?? '';
    $introAnchorLabel = $introBody[1] ?? '';
    $editorialCards = array_values($editorialSection['cards'] ?? []);
    $videoSection = $videoSection ?? [];
    $accountingVideos = collect($accountingVideos ?? []);
    $videoSectionTitle = trim((string) ($videoSection['title'] ?? ''));
    $videoSectionIntro = trim((string) ($videoSection['intro'] ?? ''));
    $hasAccountingVideoHead = $videoSectionTitle !== '' || $videoSectionIntro !== '';
    $anchorLinkIcon = [
        'view_box' => '0 0 256 512',
        'href' => $accountingSprite.'#angle-right',
    ];
    $detailSectionIcons = [
        'book-open' => [
            'view_box' => '0 0 576 512',
            'href' => $accountingSprite.'#book-open',
        ],
        'file-lines' => [
            'view_box' => '0 0 384 512',
            'href' => $accountingSprite.'#file-lines',
        ],
        'chart-line' => [
            'view_box' => '0 0 512 512',
            'href' => $accountingSprite.'#chart-line',
        ],
        'briefcase' => [
            'view_box' => '0 0 512 512',
            'href' => $accountingSprite.'#briefcase',
        ],
        'user-group' => [
            'view_box' => '0 0 640 512',
            'href' => $accountingSprite.'#user-group',
        ],
        'building-columns' => [
            'view_box' => '0 0 512 512',
            'href' => $accountingSprite.'#building-columns',
        ],
        'magnifying-glass' => [
            'view_box' => '0 0 512 512',
            'href' => $accountingSprite.'#magnifying-glass',
        ],
    ];
    $detailQuoteIcon = [
        'view_box' => '0 0 448 512',
        'href' => $accountingSprite.'#quote-right',
    ];
    $detailServiceSections = collect($detailSections ?? [])->map(function (array $section, int $index) {
        $slug = \Illuminate\Support\Str::slug((string) ($section['slug'] ?? $section['title'] ?? 'section-'.($index + 1)));

        return array_merge($section, [
            'slug' => $slug,
            'anchor_id' => 'accounting-service-'.$slug,
            'index_label' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
            'items' => array_values($section['items'] ?? []),
            'downloads' => array_values($section['downloads'] ?? []),
            'after_list' => array_values($section['after_list'] ?? []),
        ]);
    })->values();
    $detailSectionIconByAnchor = $detailServiceSections
        ->mapWithKeys(fn (array $section): array => [$section['anchor_id'] => (string) ($section['icon'] ?? 'file-lines')])
        ->all();
    $introAnchorItems = collect($introItems)->map(function (string $item) use ($detailSectionIconByAnchor, $detailSectionIcons): array {
        $slug = \Illuminate\Support\Str::slug($item);
        $anchorId = 'accounting-service-'.$slug;
        $iconKey = $detailSectionIconByAnchor[$anchorId] ?? 'file-lines';
        $icon = $detailSectionIcons[$iconKey] ?? $detailSectionIcons['file-lines'];

        return [
            'label' => $item,
            'href' => '#'.$anchorId,
            'icon_view_box' => $icon['view_box'],
            'icon_href' => $icon['href'],
        ];
    })->values();
    $heroCtaLabel = trim((string) ($heroSection['cta_label'] ?? ''));
    $heroCtaUrl = trim((string) ($heroSection['cta_url'] ?? ''));

    if ($heroCtaLabel === '' || in_array($heroCtaLabel, ['Pošaljite upit', 'Send an inquiry'], true)) {
        $heroCtaLabel = $isCroatianLocale ? 'Pogledajte usluge' : 'View services';
    }

    if ($heroCtaUrl === '' || $heroCtaUrl === '#accounting-sastanak') {
        $heroCtaUrl = '#accounting-overview';
    }

    $readMoreLabel = $isCroatianLocale ? 'Opširnije' : 'Read more';
    $playVideoLabel = $isCroatianLocale ? 'Pokreni video' : 'Play video';
?>

<?php $__env->startSection('title', $servicePageMetaTitle !== '' ? $servicePageMetaTitle : ($servicePageTitle ?? 'Računovodstvo')); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startSection('content'); ?>
    <div class="ac-family-business-page ac-accounting-page">
        <section class="ac-family-hero">
            <div class="ac-family-hero-media" aria-hidden="true" style="background-image: url('<?php echo e($heroBackgroundUrl); ?>');"></div>
            <div class="ac-family-hero-overlay"></div>

            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-family-hero-content">
                    <div class="ac-family-hero-shell">
                        <div class="ac-family-hero-copy">
                            <h1 class="ac-family-hero-title">
                                <span class="is-brand"><?php echo e($heroSection['brand_title'] ?? 'ALPHA CAPITALIS'); ?></span>
                                <span class="is-subtitle">
                                    <span class="is-subtitle-lead"><?php echo e($heroSection['subtitle_lead'] ?? 'Računovodstvo'); ?></span>
                                    <span class="is-subtitle-accent"><?php echo e($heroSection['subtitle_accent'] ?? 'i izvještavanje'); ?></span>
                                </span>
                            </h1>

                            <p class="ac-family-hero-intro"><?php echo e($heroSection['intro'] ?? ''); ?></p>

                            <div class="ac-family-hero-actions">
                                <a href="<?php echo e($heroCtaUrl); ?>" class="front-action-cta">
                                    <span><?php echo e($heroCtaLabel); ?></span>
                                    <svg viewBox="0 0 320 512" fill="currentColor" aria-hidden="true">
                                        <use href="<?php echo e(asset('front-theme/fonts/sprites/solid.svg')); ?>#angle-down"></use>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="accounting-overview" class="ac-accounting-overview-section" aria-labelledby="ac-accounting-overview-title">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-support-story-hero ac-accounting-overview-hero">
                    <div class="ac-support-story-shell">
                        <div class="ac-services-head ac-support-story-head">
                            <div class="ac-services-eyebrow">
                                <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                                <p class="ac-services-kicker"><?php echo e($introSection['kicker'] ?? 'RAČUNOVODSTVO'); ?></p>
                                <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                            </div>
                            <h2 id="ac-accounting-overview-title">
                                <span><?php echo e($introSection['title'] ?? ''); ?></span>
                            </h2>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) $introLead) !== ''): ?>
                                <p class="ac-services-intro"><?php echo e($introLead); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="ac-services-divider" aria-hidden="true">
                                <span class="ac-services-divider-line"></span>
                                <span class="ac-services-divider-glyph"></span>
                                <span class="ac-services-divider-line"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ac-accounting-overview-grid">
                    <article class="ac-accounting-overview-copy">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($introAnchorItems->isNotEmpty()): ?>
                            <section class="ac-accounting-anchor-nav" aria-labelledby="ac-accounting-anchor-nav-title">
                                <div class="ac-accounting-copy-head">
                                    <h3 id="ac-accounting-anchor-nav-title">
                                        <?php echo e(trim((string) $introAnchorLabel) !== '' ? $introAnchorLabel : ($introSection['title'] ?? 'Računovodstvene usluge')); ?>

                                    </h3>
                                </div>
                                <ul class="ac-accounting-anchor-list">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $introAnchorItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>
                                            <a href="<?php echo e($item['href']); ?>" class="ac-accounting-anchor-link">
                                                <span class="ac-accounting-anchor-link-icon" aria-hidden="true">
                                                    <svg viewBox="<?php echo e($item['icon_view_box']); ?>" fill="currentColor">
                                                        <use href="<?php echo e($item['icon_href']); ?>"></use>
                                                    </svg>
                                                </span>
                                                <span><?php echo e($item['label']); ?></span>
                                            </a>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </ul>
                            </section>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    </article>

                    <article class="ac-accounting-video-card">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($introSection['video_title'] ?? '')) !== ''): ?>
                            <div class="ac-accounting-video-card-body">
                                <h3><?php echo e($introSection['video_title']); ?></h3>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="ac-accounting-video-frame-wrap" data-accounting-video-frame>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($introVideo['embed_url'] ?? '') !== ''): ?>
                                <iframe
                                    data-accounting-video-iframe
                                    data-base-src="<?php echo e($introVideo['embed_url']); ?>"
                                    src="<?php echo e($introVideo['embed_url']); ?>"
                                    title="<?php echo e(trim((string) ($introSection['video_title'] ?? '')) !== '' ? $introSection['video_title'] : ($introSection['title'] ?? 'Accounting video')); ?>"
                                    loading="lazy"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    referrerpolicy="strict-origin-when-cross-origin"
                                    allowfullscreen
                                ></iframe>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($introVideo['poster_url'] ?? '') !== ''): ?>
                                    <button
                                        type="button"
                                        class="ac-accounting-video-poster"
                                        data-accounting-video-activate
                                        aria-label="<?php echo e($playVideoLabel); ?>: <?php echo e(trim((string) ($introSection['video_title'] ?? '')) !== '' ? $introSection['video_title'] : ($introSection['title'] ?? 'Accounting video')); ?>"
                                    >
                                        <span class="ac-accounting-video-poster-media" aria-hidden="true">
                                            <img src="<?php echo e($introVideo['poster_url']); ?>" alt="" loading="lazy">
                                        </span>
                                        <span class="ac-accounting-video-poster-shade" aria-hidden="true"></span>
                                        <span class="ac-accounting-video-poster-play" aria-hidden="true">
                                            <svg viewBox="0 0 384 512" fill="currentColor" focusable="false" aria-hidden="true">
                                                <path d="M73 39c-14.8-9.1-33.4-9.4-48.5-.9S0 62.5 0 80v352c0 17.5 9.4 33.8 24.5 42.9s33.7 8.2 48.5-.9l288-176c14.7-9 23-25 23-42.3s-8.3-33.4-23-42.3L73 39z"></path>
                                            </svg>
                                        </span>
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php else: ?>
                                <div class="ac-accounting-video-fallback">
                                    <span><?php echo e(trim((string) ($introSection['video_title'] ?? '')) !== '' ? $introSection['video_title'] : 'Video'); ?></span>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($editorialSection['title'] ?? '')) !== '' || !empty($editorialCards)): ?>
            <section class="ac-support-story ac-accounting-editorial-section" aria-labelledby="ac-accounting-editorial-title">
                <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                    <div class="ac-support-story-hero ac-accounting-editorial-hero">
                        <div class="ac-support-story-shell">
                            <div class="ac-services-head ac-support-story-head ac-accounting-editorial-head">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($editorialSection['eyebrow'] ?? '')) !== ''): ?>
                                    <div class="ac-services-eyebrow">
                                        <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                                        <p class="ac-services-kicker"><?php echo e($editorialSection['eyebrow']); ?></p>
                                        <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <h2 id="ac-accounting-editorial-title">
                                    <span><?php echo e($editorialSection['title']); ?></span>
                                </h2>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($editorialSection['subtitle'] ?? '')) !== ''): ?>
                                    <p class="ac-services-intro"><?php echo e($editorialSection['subtitle']); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <div class="ac-services-divider" aria-hidden="true">
                                    <span class="ac-services-divider-line"></span>
                                    <span class="ac-services-divider-glyph"></span>
                                    <span class="ac-services-divider-line"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ac-support-story-grid ac-accounting-editorial-grid">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $editorialCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="ac-support-story-card ac-accounting-editorial-card">
                                <span class="ac-accounting-editorial-card-badge" aria-hidden="true">
                                    <?php echo e(str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT)); ?>

                                </span>
                                <div class="ac-accounting-editorial-card-inner">
                                    <h3><?php echo e($card['title'] ?? ''); ?></h3>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($card['body'] ?? '')) !== ''): ?>
                                        <p class="ac-support-story-card-lead ac-accounting-editorial-card-copy"><?php echo e($card['body']); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $detailServiceSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $sectionIcon = $detailSectionIcons[$section['icon'] ?? 'file-lines'] ?? $detailSectionIcons['file-lines'];
            ?>
            <section id="<?php echo e($section['anchor_id']); ?>" class="ac-accounting-detail-section" aria-labelledby="ac-accounting-detail-title-<?php echo e($section['slug']); ?>">
                <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                    <div class="ac-accounting-detail-shell">
                        <div class="ac-accounting-detail-head">
                            <div class="ac-accounting-detail-title">
                                <div class="ac-accounting-detail-badge" aria-hidden="true">
                                    <span class="ac-accounting-detail-icon">
                                        <svg viewBox="<?php echo e($sectionIcon['view_box']); ?>" fill="currentColor">
                                            <use href="<?php echo e($sectionIcon['href']); ?>"></use>
                                        </svg>
                                    </span>
                                    <span class="ac-accounting-detail-index"><?php echo e($section['index_label']); ?></span>
                                </div>
                                <div class="ac-accounting-detail-heading">
                                    <h2 id="ac-accounting-detail-title-<?php echo e($section['slug']); ?>"><?php echo e($section['title'] ?? ''); ?></h2>
                                </div>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($section['intro'] ?? '')) !== ''): ?>
                                <div class="ac-accounting-detail-intro-col">
                                    <p><?php echo e($section['intro']); ?></p>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="ac-accounting-detail-grid">
                            <div class="ac-accounting-detail-column">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($section['list_title'] ?? '')) !== ''): ?>
                                    <p class="ac-accounting-detail-list-title"><?php echo e($section['list_title']); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($section['items'])): ?>
                                    <ul class="ac-accounting-detail-list">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li>
                                                <span class="ac-accounting-detail-list-bullet" aria-hidden="true">
                                                    <svg viewBox="<?php echo e($anchorLinkIcon['view_box']); ?>" fill="currentColor">
                                                        <use href="<?php echo e($anchorLinkIcon['href']); ?>"></use>
                                                    </svg>
                                                </span>
                                                <span><?php echo e($item); ?></span>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </ul>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($section['after_list'])): ?>
                                    <div class="ac-accounting-detail-after-list">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $section['after_list']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <p><?php echo e($paragraph); ?></p>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="ac-accounting-detail-column">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($section['downloads'])): ?>
                                    <div class="ac-accounting-detail-downloads">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $section['downloads']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $download): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e($download['url'] ?? '#'); ?>" class="ac-accounting-detail-download" target="_blank" rel="noopener noreferrer">
                                                <span class="ac-accounting-detail-download-title"><?php echo e($download['title'] ?? ''); ?></span>
                                                <span class="ac-accounting-detail-download-cta"><?php echo e($download['label'] ?? ($isCroatianLocale ? 'Preuzmi' : 'Download')); ?></span>
                                            </a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($section['quote'] ?? '')) !== ''): ?>
                                    <blockquote class="ac-accounting-detail-quote">
                                        <span class="ac-accounting-detail-quote-icon" aria-hidden="true">
                                            <svg viewBox="<?php echo e($detailQuoteIcon['view_box']); ?>" fill="currentColor">
                                                <use href="<?php echo e($detailQuoteIcon['href']); ?>"></use>
                                            </svg>
                                        </span>
                                        <p><?php echo e($section['quote']); ?></p>
                                    </blockquote>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($section['cta_text'] ?? '')) !== ''): ?>
                                    <p class="ac-accounting-detail-cta-copy"><?php echo e($section['cta_text']); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($section['cta_label'] ?? '')) !== ''): ?>
                                    <div class="ac-accounting-detail-action">
                                        <a
                                            href="<?php echo e($section['cta_url'] ?? '#accounting-sastanak'); ?>"
                                            class="front-contact-submit inline-flex h-11 items-center justify-center px-6 text-sm font-semibold !text-white transition"
                                        >
                                            <span><?php echo e($section['cta_label']); ?></span>
                                        </a>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($accountingVideos->isNotEmpty()): ?>
            <section
                class="ac-support-story ac-home-blog ac-blog-related-section ac-family-blog-section ac-accounting-videos-section"
                <?php if($videoSectionTitle !== ''): ?>
                    aria-labelledby="ac-accounting-videos-title"
                <?php else: ?>
                    aria-label="<?php echo e($isCroatianLocale ? 'Video sekcija računovodstva' : 'Accounting video section'); ?>"
                <?php endif; ?>
            >
                <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasAccountingVideoHead): ?>
                        <div class="ac-support-story-hero">
                            <div class="ac-support-story-shell">
                                <div class="ac-services-head ac-support-story-head">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($videoSectionTitle !== ''): ?>
                                        <h2 id="ac-accounting-videos-title">
                                            <span><?php echo e($videoSectionTitle); ?></span>
                                        </h2>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($videoSectionIntro !== ''): ?>
                                        <p class="ac-services-intro"><?php echo e($videoSectionIntro); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <div class="ac-services-divider" aria-hidden="true">
                                        <span class="ac-services-divider-line"></span>
                                        <span class="ac-services-divider-glyph"></span>
                                        <span class="ac-services-divider-line"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="ac-accounting-videos-carousel<?php echo e($hasAccountingVideoHead ? '' : ' ac-accounting-videos-carousel--flush'); ?>">
                        <div id="ac-accounting-videos-splide" class="splide ac-accounting-videos-splide" data-accounting-videos-splide>
                            <div class="splide__track">
                                <ul class="splide__list">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $accountingVideos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="splide__slide ac-accounting-video-slide">
                                            <article class="ac-accounting-video-library-card">
                                                <div class="ac-accounting-video-library-frame" data-accounting-video-frame>
                                                    <iframe
                                                        data-accounting-video-iframe
                                                        data-base-src="<?php echo e($video['embed_url']); ?>"
                                                        src="<?php echo e($video['embed_url']); ?>"
                                                        title="<?php echo e(trim((string) ($video['title'] ?? '')) !== '' ? $video['title'] : ($videoSection['title'] ?? 'Video')); ?>"
                                                        loading="lazy"
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                        referrerpolicy="strict-origin-when-cross-origin"
                                                        allowfullscreen
                                                    ></iframe>

                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($video['poster_url'] ?? '')) !== ''): ?>
                                                        <button
                                                            type="button"
                                                            class="ac-accounting-video-poster"
                                                            data-accounting-video-activate
                                                            aria-label="<?php echo e($playVideoLabel); ?>: <?php echo e(trim((string) ($video['title'] ?? '')) !== '' ? $video['title'] : ($videoSection['title'] ?? 'Video')); ?>"
                                                        >
                                                            <span class="ac-accounting-video-poster-media" aria-hidden="true">
                                                                <img src="<?php echo e($video['poster_url']); ?>" alt="" loading="lazy">
                                                            </span>
                                                            <span class="ac-accounting-video-poster-shade" aria-hidden="true"></span>
                                                            <span class="ac-accounting-video-poster-play" aria-hidden="true">
                                                                <svg viewBox="0 0 384 512" fill="currentColor" focusable="false" aria-hidden="true">
                                                                    <path d="M73 39c-14.8-9.1-33.4-9.4-48.5-.9S0 62.5 0 80v352c0 17.5 9.4 33.8 24.5 42.9s33.7 8.2 48.5-.9l288-176c14.7-9 23-25 23-42.3s-8.3-33.4-23-42.3L73 39z"></path>
                                                                </svg>
                                                            </span>
                                                        </button>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($video['title'] ?? '')) !== ''): ?>
                                                    <div class="ac-accounting-video-library-body">
                                                        <h3><?php echo e($video['title']); ?></h3>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </article>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <section class="ac-accounting-contact-shell">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <section id="accounting-sastanak" class="ac-family-section ac-accounting-contact pb-16 pt-12 md:pb-24 md:pt-16" aria-labelledby="ac-accounting-meeting-title">
                    <div class="ac-family-team-showcase-head">
                        <p class="ac-family-section-kicker"><?php echo e($meetingSection['kicker'] ?? 'KONTAKT'); ?></p>
                        <h2 id="ac-accounting-meeting-title"><?php echo e($meetingSection['title'] ?? ''); ?></h2>
                        <p><?php echo e($meetingSection['intro'] ?? ''); ?></p>
                    </div>

                    <div class="mt-10 grid gap-5 lg:grid-cols-[300px_minmax(0,1fr)] lg:items-start">
                        <aside class="front-contact-sidebar">
                            <div class="front-contact-panel front-contact-panel--direct">
                                <h2><?php echo e($meetingSection['visit_title'] ?? 'Posjetite nas'); ?></h2>
                                <div class="mt-4 space-y-1 text-[0.89rem] leading-6 text-slate-700">
                                    <p><?php echo e($meetingSection['visit_lines'][0] ?? ''); ?></p>
                                    <p><?php echo e($meetingSection['visit_lines'][1] ?? ''); ?></p>
                                </div>
                            </div>

                            <div class="front-contact-panel front-contact-panel--direct">
                                <h2><?php echo e($meetingSection['contact_title'] ?? 'Kontaktirajte nas'); ?></h2>
                                <ul class="front-contact-direct-list">
                                    <li>
                                        <span><?php echo e($meetingSection['direct_phone_label'] ?? 'Telefon'); ?></span>
                                        <a href="tel:<?php echo e($contactPhoneHref); ?>"><?php echo e($contactPhone); ?></a>
                                    </li>
                                    <li>
                                        <span><?php echo e($meetingSection['direct_email_label'] ?? 'Email'); ?></span>
                                        <a href="mailto:<?php echo e($contactEmail); ?>"><?php echo e($contactEmail); ?></a>
                                    </li>
                                </ul>
                            </div>
                        </aside>

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
                            <input type="hidden" name="redirect_to" value="<?php echo e(route('accounting.show')); ?>#accounting-sastanak">

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
                                <div class="front-contact-status" role="status">
                                    <?php echo e(session('status')); ?>

                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="accounting-first-name"><?php echo e($meetingFormLabels['first_name'] ?? 'Ime'); ?></label>
                                    <input id="accounting-first-name" type="text" name="first_name" value="<?php echo e(old('first_name')); ?>" class="front-contact-input h-11 w-full text-sm" required>
                                    <p class="mt-2 text-xs font-semibold text-rose-600 <?php echo e($errors->has('first_name') ? '' : 'hidden'); ?>" data-field-error="first_name"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="accounting-last-name"><?php echo e($meetingFormLabels['last_name'] ?? 'Prezime'); ?></label>
                                    <input id="accounting-last-name" type="text" name="last_name" value="<?php echo e(old('last_name')); ?>" class="front-contact-input h-11 w-full text-sm">
                                    <p class="mt-2 text-xs font-semibold text-rose-600 <?php echo e($errors->has('last_name') ? '' : 'hidden'); ?>" data-field-error="last_name"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                                </div>
                            </div>

                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="accounting-company"><?php echo e($meetingFormLabels['company'] ?? 'Tvrtka'); ?></label>
                                    <input id="accounting-company" type="text" name="company" value="<?php echo e(old('company')); ?>" class="front-contact-input h-11 w-full text-sm">
                                    <p class="mt-2 text-xs font-semibold text-rose-600 <?php echo e($errors->has('company') ? '' : 'hidden'); ?>" data-field-error="company"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['company'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="accounting-phone"><?php echo e($meetingFormLabels['phone'] ?? 'Broj telefona'); ?></label>
                                    <input id="accounting-phone" type="text" name="phone" value="<?php echo e(old('phone')); ?>" class="front-contact-input h-11 w-full text-sm">
                                    <p class="mt-2 text-xs font-semibold text-rose-600 <?php echo e($errors->has('phone') ? '' : 'hidden'); ?>" data-field-error="phone"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phone'];
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
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="accounting-email"><?php echo e($meetingFormLabels['email'] ?? 'Email'); ?></label>
                                <input id="accounting-email" type="email" name="email" value="<?php echo e(old('email', auth()->user()?->email)); ?>" class="front-contact-input h-11 w-full text-sm" required>
                                <p class="mt-2 text-xs font-semibold text-rose-600 <?php echo e($errors->has('email') ? '' : 'hidden'); ?>" data-field-error="email"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                            </div>

                            <div class="mt-4">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="accounting-subject"><?php echo e($meetingFormLabels['subject'] ?? 'Naslov poruke'); ?></label>
                                <input id="accounting-subject" type="text" name="subject" value="<?php echo e(old('subject')); ?>" class="front-contact-input h-11 w-full text-sm">
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
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="accounting-message"><?php echo e($meetingFormLabels['message'] ?? 'Poruka'); ?></label>
                                <textarea id="accounting-message" name="message" rows="8" class="front-contact-textarea w-full text-sm" required><?php echo e(old('message')); ?></textarea>
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
                                    <?php echo e($meetingSection['submit'] ?? 'Pošalji'); ?>

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
                    </div>
                </section>
            </div>
        </section>

        <section class="ac-support-story ac-home-blog ac-blog-related-section ac-family-blog-section ac-accounting-blog-section" aria-labelledby="ac-accounting-blog-title">
            <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                <div class="ac-support-story-hero">
                    <div class="ac-support-story-shell">
                        <div class="ac-services-head ac-support-story-head">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($blogSection['kicker'] ?? '')) !== ''): ?>
                                <div class="ac-services-eyebrow">
                                    <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                                    <p class="ac-services-kicker"><?php echo e($blogSection['kicker']); ?></p>
                                    <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <h2 id="ac-accounting-blog-title">
                                <span><?php echo e($blogSection['title'] ?? ''); ?></span>
                            </h2>
                            <p class="ac-services-intro"><?php echo e($blogSection['intro'] ?? ''); ?></p>
                            <div class="ac-services-divider" aria-hidden="true">
                                <span class="ac-services-divider-line"></span>
                                <span class="ac-services-divider-glyph"></span>
                                <span class="ac-services-divider-line"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($accountingPosts ?? collect())->isNotEmpty()): ?>
                    <div class="ac-home-blog-carousel">
                        <div id="ac-accounting-blog-splide" class="splide ac-home-blog-splide" data-accounting-blog-splide>
                            <div class="splide__track">
                                <ul class="splide__list">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $accountingPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $translation = $post->translations->firstWhere('locale', $locale)
                                                ?? $post->translations->firstWhere('locale', $fallbackLocale);
                                            $postSlug = trim((string) ($translation?->slug ?? ''));
                                            $postUrl = $postSlug !== '' ? route('blog.show', ['slug' => $postSlug]) : route('blog.index');
                                            $postTitle = trim((string) ($translation?->title ?? $post->code));
                                            $postExcerpt = trim((string) ($translation?->excerpt ?? '')) ?: __('ui.blog.excerpt_fallback');
                                            $postExcerpt = \Illuminate\Support\Str::limit($postExcerpt, 180, '...', true);
                                            $postImage = $post->getFirstMedia('blog_cover');
                                            $postImageUrl = $postImage?->getUrl();
                                            $primaryCategory = $post->categories
                                                ->sortByDesc(fn ($category) => (int) ($category->pivot->is_primary ?? false))
                                                ->first();
                                            $categoryTranslation = $primaryCategory?->translations->firstWhere('locale', $locale)
                                                ?? $primaryCategory?->translations->firstWhere('locale', $fallbackLocale);
                                            $categoryLabel = trim((string) ($categoryTranslation?->name ?? ($accountingCategoryName ?? 'Novosti')));
                                            $publishedLabel = ($post->published_at ?? $post->created_at)?->translatedFormat($isCroatianLocale ? 'j. F Y.' : 'F j, Y');
                                        ?>
                                        <li class="splide__slide ac-home-blog-slide">
                                            <article class="ac-home-blog-card">
                                                <a href="<?php echo e($postUrl); ?>" class="ac-home-blog-card-link" aria-label="<?php echo e($readMoreLabel); ?>: <?php echo e($postTitle); ?>">
                                                    <div class="ac-home-blog-card-media">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($postImageUrl): ?>
                                                            <img
                                                                src="<?php echo e($postImageUrl); ?>"
                                                                alt="<?php echo e($postTitle); ?>"
                                                                class="ac-home-blog-card-image"
                                                                loading="lazy"
                                                                decoding="async"
                                                            >
                                                        <?php else: ?>
                                                            <div class="ac-home-blog-card-placeholder">
                                                                <span><?php echo e(__('ui.blog.title')); ?></span>
                                                            </div>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                        <div class="ac-home-blog-card-overlay">
                                                            <span class="ac-home-blog-card-overlay-kicker">
                                                                <?php echo e(\Illuminate\Support\Str::upper(\Illuminate\Support\Str::limit($categoryLabel, 22, ''))); ?>

                                                            </span>
                                                            <span class="ac-home-blog-card-overlay-line" aria-hidden="true"></span>
                                                        </div>
                                                    </div>

                                                    <div class="ac-home-blog-card-body">
                                                        <h3 class="ac-home-blog-card-title"><?php echo e($postTitle); ?></h3>
                                                        <p class="ac-home-blog-card-excerpt"><?php echo e($postExcerpt); ?></p>
                                                    </div>

                                                    <div class="ac-home-blog-card-meta">
                                                        <span class="ac-home-blog-card-meta-link">
                                                            <span><?php echo e($readMoreLabel); ?></span>
                                                            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                                <path d="M4 12L12 4"></path>
                                                                <path d="M6 4h6v6"></path>
                                                            </svg>
                                                        </span>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($publishedLabel): ?>
                                                            <span class="ac-home-blog-card-meta-date"><?php echo e($publishedLabel); ?></span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </a>
                                            </article>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="ac-accounting-empty-state">
                        <p><?php echo e($blogSection['empty'] ?? 'Novosti iz ove kategorije uskoro će biti dostupne.'); ?></p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php if (! $__env->hasRenderedOnce('9582227a-0d21-438b-8409-1c744afbf9cf')): $__env->markAsRenderedOnce('9582227a-0d21-438b-8409-1c744afbf9cf'); ?>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .ac-accounting-page {
            background:
                radial-gradient(46% 32% at 100% 0%, rgba(95, 127, 145, 0.12), transparent 72%),
                linear-gradient(180deg, #eef4f6 0%, #f8fbfc 100%);
        }

        .ac-accounting-overview-section {
            scroll-margin-top: 7rem;
            padding: clamp(1.8rem, 3.4vw, 2.75rem) 0 clamp(3.25rem, 6vw, 5rem);
            background: linear-gradient(180deg, #f5f8fb 0%, #eef4f8 100%);
        }

        .ac-accounting-overview-hero .ac-support-story-head {
            max-width: 54rem;
            margin: 0 auto;
            text-align: center;
        }

        .ac-accounting-overview-hero .ac-services-eyebrow {
            justify-content: center;
        }

        .ac-accounting-overview-hero .ac-services-eyebrow-line {
            display: none;
        }

        .ac-accounting-overview-hero .ac-services-divider {
            justify-content: center;
        }

        .ac-accounting-anchor-nav {
            margin: 0;
        }

        .ac-accounting-copy-head {
            padding: 0 0 0.95rem;
        }

        .ac-accounting-copy-head h3,
        .ac-accounting-video-card h3 {
            margin: 0;
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.35rem, 2.1vw, 1.8rem);
            line-height: 1.12;
            color: #0f1b2d;
            text-wrap: balance;
        }

        .ac-accounting-anchor-list {
            display: grid;
            gap: 0.95rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .ac-accounting-anchor-list li {
            display: flex;
        }

        .ac-accounting-anchor-link {
            display: inline-flex;
            align-items: center;
            gap: 0.58rem;
            width: 100%;
            min-height: 3rem;
            padding: 0.72rem 1rem;
            border: 1px solid rgba(148, 163, 184, 0.24);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.96);
            font-size: 0.95rem;
            font-weight: 600;
            line-height: 1.6;
            color: #365a72;
            text-decoration: none;
            transition: color 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
        }

        .ac-accounting-anchor-link-icon {
            display: inline-flex;
            width: 1.15rem;
            height: 1.15rem;
            flex: none;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: rgba(171, 141, 82, 0.12);
            color: #8a6a38;
        }

        .ac-accounting-anchor-link-icon svg {
            width: 0.7rem;
            height: 0.7rem;
        }

        .ac-accounting-anchor-link:hover,
        .ac-accounting-anchor-link:focus-visible {
            color: #0f1b2d;
            transform: translateX(0.16rem);
            border-color: rgba(54, 90, 114, 0.28);
        }

        .ac-accounting-overview-grid {
            display: grid;
            margin-top: clamp(1.75rem, 3vw, 2.35rem);
            gap: clamp(1.75rem, 3vw, 2.65rem);
            grid-template-columns: minmax(0, 4fr) minmax(0, 8fr);
            align-items: start;
        }

        .ac-accounting-overview-copy {
            padding-top: 0;
        }

        .ac-accounting-overview-body {
            display: grid;
            gap: 1rem;
        }

        .ac-accounting-overview-body p {
            margin: 0;
            font-size: 1.02rem;
            line-height: 1.84;
            color: #425466;
        }

        .ac-accounting-video-card-body {
            padding: 0 0 0.95rem;
        }

        .ac-accounting-video-frame-wrap {
            position: relative;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            border-radius: 1.5rem;
            background: #0f1b2d;
            box-shadow: 0 22px 42px rgba(15, 27, 45, 0.16);
        }

        .ac-accounting-video-frame-wrap iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .ac-accounting-video-library-frame {
            position: relative;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            background: #0f1b2d;
        }

        .ac-accounting-video-library-frame iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .ac-accounting-video-poster {
            position: absolute;
            inset: 0;
            display: block;
            width: 100%;
            height: 100%;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
            z-index: 2;
            transition: opacity 0.24s ease, visibility 0.24s ease;
        }

        .ac-accounting-video-poster-media,
        .ac-accounting-video-poster-media img {
            display: block;
            width: 100%;
            height: 100%;
        }

        .ac-accounting-video-poster-media img {
            object-fit: cover;
        }

        .ac-accounting-video-poster-shade {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(15, 27, 45, 0.16) 0%, rgba(15, 27, 45, 0.34) 100%);
        }

        .ac-accounting-video-poster-play {
            position: absolute;
            top: 50%;
            left: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: clamp(3.7rem, 10vw, 4.9rem);
            height: clamp(3.7rem, 10vw, 4.9rem);
            border-radius: 999px;
            color: #0f1b2d;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 18px 36px rgba(15, 27, 45, 0.24);
            transform: translate(-50%, -50%);
            transition: transform 0.22s ease, box-shadow 0.22s ease;
        }

        .ac-accounting-video-poster-play svg {
            width: 1.1rem;
            height: 1.1rem;
            margin-left: 0.16rem;
            display: block;
        }

        .ac-accounting-video-poster:hover .ac-accounting-video-poster-play,
        .ac-accounting-video-poster:focus-visible .ac-accounting-video-poster-play {
            transform: translate(-50%, -50%) scale(1.06);
            box-shadow: 0 22px 40px rgba(15, 27, 45, 0.3);
        }

        .ac-accounting-video-frame-wrap.is-active .ac-accounting-video-poster,
        .ac-accounting-video-library-frame.is-active .ac-accounting-video-poster {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .ac-accounting-video-fallback {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.84);
            text-align: center;
            padding: 1rem;
        }

        .ac-accounting-editorial-section {
            padding: clamp(2.2rem, 4vw, 3rem) 0;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.34), rgba(255, 255, 255, 0.08)),
                repeating-linear-gradient(90deg, rgba(15, 42, 67, 0.05) 0 1px, transparent 1px 24px),
                radial-gradient(54% 76% at 86% 18%, rgba(65, 122, 176, 0.16), transparent 62%),
                radial-gradient(38% 56% at 12% 84%, rgba(171, 141, 82, 0.08), transparent 68%),
                linear-gradient(120deg, #eef3f7 0%, #e7eef4 48%, #dde7f0 100%);
            overflow: hidden;
        }

        .ac-accounting-editorial-head {
            max-width: 56rem;
            margin: 0 auto;
            padding: clamp(0.2rem, 1vw, 0.6rem) 0 0;
            text-align: center;
        }

        .ac-accounting-editorial-head .ac-services-eyebrow,
        .ac-accounting-editorial-head .ac-services-divider {
            justify-content: center;
        }

        .ac-accounting-editorial-head .ac-services-eyebrow-line {
            display: none;
        }

        .ac-accounting-editorial-head .ac-services-kicker {
            padding: 0.45rem 0.9rem;
            border: 1px solid rgba(148, 163, 184, 0.28);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 10px 24px rgba(15, 27, 45, 0.05);
            letter-spacing: 0.18em;
            color: #365a72;
        }

        .ac-accounting-editorial-head h2 {
            margin: 0 auto;
            width: 100%;
            max-width: none;
            font-size: clamp(1.72rem, 2.45vw, 2.5rem);
            line-height: 1.08;
            letter-spacing: -0.02em;
            color: #0f1b2d;
        }

        .ac-accounting-editorial-head .ac-services-divider-line {
            background: rgba(148, 163, 184, 0.26);
        }

        .ac-accounting-editorial-head .ac-services-divider-glyph {
            border-color: rgba(148, 163, 184, 0.24);
            color: rgba(95, 127, 145, 0.52);
        }

        .ac-accounting-editorial-head h2 span {
            display: block;
            white-space: normal;
            text-wrap: balance;
        }

        .ac-accounting-editorial-head .ac-services-intro {
            max-width: 43rem;
            margin: 0.9rem auto 0;
            font-size: clamp(0.98rem, 1.2vw, 1.08rem);
            line-height: 1.72;
            color: #4d6175;
            text-wrap: balance;
        }

        .ac-accounting-editorial-grid {
            margin-top: clamp(2rem, 4vw, 2.75rem);
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.35rem;
        }

        .ac-accounting-editorial-card {
            position: relative;
            min-height: 18rem;
            padding: 1.6rem 1.4rem 1.45rem;
        }

        .ac-accounting-editorial-card-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.7rem;
            height: 2.7rem;
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 999px;
            background: linear-gradient(180deg, rgba(250, 251, 252, 0.96), rgba(241, 245, 249, 0.96));
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: #6b7f92;
        }

        .ac-accounting-editorial-card-inner {
            max-width: calc(100% - 4.6rem);
        }

        .ac-accounting-editorial-card h3 {
            max-width: none;
        }

        .ac-accounting-editorial-card-copy {
            max-width: none;
            font-size: 0.95rem;
            line-height: 1.74;
            color: #4a5b6e;
        }

        .ac-accounting-detail-section {
            scroll-margin-top: 7rem;
            padding: clamp(2.6rem, 4.4vw, 3.5rem) 0;
            border-top: 1px solid rgba(171, 141, 82, 0.16);
            border-bottom: 1px solid rgba(171, 141, 82, 0.16);
            background: linear-gradient(180deg, #f8f4eb 0%, #fbf8f2 100%);
        }

        .ac-accounting-detail-section + .ac-accounting-detail-section {
            border-top: 0;
        }

        .ac-accounting-detail-shell {
            display: grid;
            gap: 2.1rem;
        }

        .ac-accounting-detail-head {
            display: grid;
            gap: 1.8rem;
            align-items: start;
        }

        .ac-accounting-detail-title {
            display: flex;
            gap: 1.15rem;
            align-items: flex-start;
            min-width: 0;
        }

        .ac-accounting-detail-badge {
            display: grid;
            gap: 0.65rem;
            justify-items: center;
            flex: none;
        }

        .ac-accounting-detail-icon {
            display: inline-flex;
            width: 3.6rem;
            height: 3.6rem;
            flex: none;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(171, 141, 82, 0.24);
        }

        .ac-accounting-detail-icon svg {
            width: 1.5rem;
            height: 1.5rem;
            color: #2d2925;
        }

        .ac-accounting-detail-index {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.55rem;
            min-height: 1.65rem;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            background: rgba(120, 96, 58, 0.08);
            color: #6d5633;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .ac-accounting-detail-heading {
            min-width: 0;
        }

        .ac-accounting-detail-heading h2 {
            margin: 0;
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.6rem, 2.3vw, 2.3rem);
            line-height: 1.15;
            font-weight: 600;
            color: #0f172a;
            text-wrap: balance;
            overflow-wrap: anywhere;
        }

        .ac-accounting-detail-intro-col {
            min-width: 0;
            max-width: 40rem;
        }

        .ac-accounting-detail-intro-col p {
            margin: 0;
            font-size: 0.98rem;
            line-height: 1.7;
            color: #403a34;
            text-wrap: pretty;
        }

        .ac-accounting-detail-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
            gap: 1.35rem 2rem;
        }

        .ac-accounting-detail-column {
            min-width: 0;
            padding-top: 0.15rem;
        }

        .ac-accounting-detail-list-title {
            margin: 0;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #6d5633;
            font-family: 'Sora', 'Public Sans', 'Segoe UI', Arial, sans-serif;
        }

        .ac-accounting-detail-list {
            margin: 1.35rem 0 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 0.8rem;
            color: #403a34;
        }

        .ac-accounting-detail-list li {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: 0.72rem;
            align-items: start;
            font-size: 0.98rem;
            line-height: 1.7rem;
            color: #403a34;
        }

        .ac-accounting-detail-after-list {
            margin-top: 1.15rem;
            display: grid;
            gap: 0.9rem;
        }

        .ac-accounting-detail-after-list p {
            margin: 0;
            font-size: 0.98rem;
            line-height: 1.72;
            color: #403a34;
        }

        .ac-accounting-detail-list-bullet {
            display: inline-flex;
            width: 1.15rem;
            height: 1.15rem;
            flex: none;
            align-items: center;
            justify-content: center;
            margin-top: 0.22rem;
            border-radius: 999px;
            background: rgba(171, 141, 82, 0.12);
            color: #7d6134;
        }

        .ac-accounting-detail-list-bullet svg {
            width: 0.42rem;
            height: 0.68rem;
        }

        .ac-accounting-detail-quote {
            position: relative;
            margin: 0;
            padding: 1.2rem 1.35rem 1.2rem 3.5rem;
            border-left: 2px solid rgba(171, 141, 82, 0.26);
            background: rgba(255, 255, 255, 0.58);
        }

        .ac-accounting-detail-quote-icon {
            position: absolute;
            top: 1.05rem;
            left: 1.2rem;
            display: inline-flex;
            width: 1.35rem;
            height: 1.35rem;
            align-items: center;
            justify-content: center;
            color: #8a6a38;
        }

        .ac-accounting-detail-quote-icon svg {
            width: 1rem;
            height: 1rem;
        }

        .ac-accounting-detail-quote p {
            margin: 0;
            font-size: 1rem;
            line-height: 1.82;
            color: #2d2925;
        }

        .ac-accounting-detail-downloads {
            display: grid;
            gap: 0.9rem;
            margin-bottom: 1.1rem;
        }

        .ac-accounting-detail-download {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.1rem;
            border: 1px solid rgba(171, 141, 82, 0.16);
            background: rgba(255, 255, 255, 0.58);
            color: #2d2925;
            text-decoration: none;
            transition: border-color 0.18s ease, background-color 0.18s ease;
        }

        .ac-accounting-detail-download:hover,
        .ac-accounting-detail-download:focus-visible {
            border-color: rgba(171, 141, 82, 0.28);
            background: rgba(255, 255, 255, 0.76);
        }

        .ac-accounting-detail-download-title {
            font-size: 0.98rem;
            line-height: 1.55;
            color: #2d2925;
        }

        .ac-accounting-detail-download-cta {
            flex: none;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #8a6a38;
        }

        .ac-accounting-detail-cta-copy {
            margin: 1rem 0 0;
            font-size: 0.96rem;
            line-height: 1.7;
            color: #5b5148;
        }

        .ac-accounting-detail-action {
            margin-top: 1.4rem;
        }

        .ac-accounting-videos-section {
            padding-top: clamp(4.2rem, 6vw, 5.2rem);
            padding-bottom: clamp(4.3rem, 6.5vw, 5.4rem);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.34), rgba(255, 255, 255, 0.12)),
                repeating-linear-gradient(90deg, rgba(15, 42, 67, 0.05) 0 1px, transparent 1px 24px),
                radial-gradient(48% 74% at 86% 16%, rgba(65, 122, 176, 0.12), transparent 62%),
                radial-gradient(34% 52% at 14% 84%, rgba(171, 141, 82, 0.06), transparent 68%),
                linear-gradient(120deg, #eef3f7 0%, #e8eff5 52%, #dfe9f2 100%);
        }

        .ac-accounting-videos-carousel {
            max-width: 100%;
            margin: 2rem auto 0;
        }

        .ac-accounting-videos-carousel--flush {
            margin-top: 0;
        }

        .ac-accounting-videos-splide .splide__track {
            overflow: hidden;
        }

        .ac-accounting-videos-splide .splide__list {
            align-items: stretch;
        }

        .ac-accounting-video-slide {
            display: flex;
            height: auto;
        }

        .ac-accounting-video-slide .ac-accounting-video-library-card {
            width: 100%;
            min-height: 100%;
        }

        .ac-accounting-video-library-card {
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: var(--front-card-radius);
            background: rgba(255, 255, 255, 0.92);
            height: 100%;
        }

        .ac-accounting-video-library-frame {
            border-bottom: 1px solid rgba(15, 27, 45, 0.08);
        }

        .ac-accounting-video-library-body {
            padding: 1.15rem 1.2rem 1.3rem;
        }

        .ac-accounting-video-library-body h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.5;
            color: #112033;
        }

        @media (min-width: 960px) {
            .ac-accounting-detail-head {
                grid-template-columns: minmax(0, 0.86fr) minmax(0, 1fr);
                gap: 2.4rem;
            }
        }

        .ac-accounting-videos-splide .splide__pagination {
            bottom: -2.1rem;
        }

        .ac-accounting-videos-splide .splide__pagination__page {
            width: 0.48rem;
            height: 0.48rem;
            margin: 0 0.22rem;
            background: rgba(54, 90, 114, 0.22);
            opacity: 1;
        }

        .ac-accounting-videos-splide .splide__pagination__page.is-active {
            background: #365a72;
            transform: scale(1.15);
        }

        .ac-accounting-contact-shell {
            background: linear-gradient(180deg, #eaf4fb 0%, #f4f9fd 100%);
        }

        .ac-accounting-contact {
            position: relative;
            z-index: 1;
            margin-top: 0;
        }

        .ac-accounting-blog-section {
            padding-bottom: clamp(8rem, 14vw, 11rem);
        }

        .ac-accounting-empty-state {
            margin-top: 2rem;
            padding: 1.4rem 1.5rem;
            border: 1px solid rgba(95, 127, 145, 0.2);
            border-radius: 1.25rem;
            background: rgba(255, 255, 255, 0.72);
            text-align: center;
            color: #475569;
        }

        @media (max-width: 767px) {
            .ac-accounting-overview-section {
                padding-top: 1.65rem;
                padding-bottom: 2.9rem;
            }

            .ac-accounting-anchor-nav {
                margin-top: 1.35rem;
            }

            .ac-accounting-overview-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .ac-accounting-video-card-body {
                padding-bottom: 0.8rem;
            }

            .ac-accounting-editorial-section {
                padding-bottom: 3.2rem;
            }

            .ac-accounting-editorial-grid {
                margin-top: 1rem;
                grid-template-columns: minmax(0, 1fr);
                padding: 0;
            }

            .ac-accounting-editorial-card {
                min-height: auto;
            }

            .ac-accounting-editorial-card-inner {
                max-width: none;
            }

            .ac-accounting-editorial-card-copy {
                max-width: none;
            }

            .ac-accounting-detail-section {
                padding: 2.2rem 0;
            }

            .ac-accounting-videos-section {
                padding-top: 3.8rem;
                padding-bottom: 4rem;
            }

            .ac-accounting-videos-carousel {
                margin-top: 1.2rem;
                max-width: 100%;
            }

            .ac-accounting-video-library-body {
                padding: 1rem 1rem 1.15rem;
            }

            .ac-accounting-detail-head,
            .ac-accounting-detail-grid {
                grid-template-columns: minmax(0, 1fr);
                gap: 1.2rem;
            }

            .ac-accounting-contact-shell {
                padding-left: 0;
                padding-right: 0;
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.desktop.contact.partials.form-script', [
    'captchaEnabled' => $captchaEnabled,
    'captchaSiteKey' => $captchaSiteKey,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if (! $__env->hasRenderedOnce('d2d84a06-8278-41f5-b56f-d824855cc504')): $__env->markAsRenderedOnce('d2d84a06-8278-41f5-b56f-d824855cc504'); ?>
    <?php $__env->startPush('scripts'); ?>
        <script defer src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        (function () {
            const shouldFocusSection = <?php echo e(($errors->any() || session('status')) ? 'true' : 'false'); ?>;
            const section = document.getElementById('accounting-sastanak');

            if (shouldFocusSection && section) {
                requestAnimationFrame(function () {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            }

            document.querySelectorAll('[data-accounting-video-frame]').forEach(function (frame) {
                if (frame.dataset.videoReady === '1') {
                    return;
                }

                frame.dataset.videoReady = '1';

                const button = frame.querySelector('[data-accounting-video-activate]');
                const iframe = frame.querySelector('[data-accounting-video-iframe]');

                if (!button || !iframe) {
                    return;
                }

                button.addEventListener('click', function () {
                    const baseSrc = iframe.dataset.baseSrc || iframe.getAttribute('src') || '';

                    try {
                        const url = new URL(baseSrc, window.location.origin);
                        url.searchParams.set('autoplay', '1');
                        url.searchParams.set('playsinline', '1');
                        iframe.src = url.toString();
                    } catch (error) {
                        iframe.src = baseSrc + (baseSrc.includes('?') ? '&' : '?') + 'autoplay=1&playsinline=1';
                    }

                    frame.classList.add('is-active');
                });
            });

            const mountSplide = function (el, options) {
                if (el.dataset.splideReady === '1') {
                    return;
                }

                el.dataset.splideReady = '1';

                const slider = new window.Splide(el, options);
                slider.mount();
            };

            const initAccountingSliders = function () {
                if (typeof window.Splide !== 'function') {
                    return false;
                }

                document.querySelectorAll('[data-accounting-blog-splide]').forEach(function (el) {
                    const count = el.querySelectorAll('.splide__slide').length;
                    mountSplide(el, {
                        type: count > 1 ? 'loop' : 'slide',
                        perPage: Math.min(3, Math.max(1, count)),
                        perMove: 1,
                        gap: '1.25rem',
                        drag: count > 1,
                        snap: true,
                        pagination: count > 1,
                        arrows: count > 1,
                        updateOnMove: true,
                        speed: 520,
                        breakpoints: {
                            1180: { perPage: Math.min(2, Math.max(1, count)) },
                            760: { perPage: 1, gap: '1rem' },
                        },
                    });
                });

                document.querySelectorAll('[data-accounting-videos-splide]').forEach(function (el) {
                    const count = el.querySelectorAll('.splide__slide').length;
                    mountSplide(el, {
                        type: count > 2 ? 'loop' : 'slide',
                        perPage: Math.min(2, Math.max(1, count)),
                        perMove: 1,
                        gap: '1.4rem',
                        drag: count > 1,
                        snap: true,
                        pagination: count > 1,
                        arrows: false,
                        updateOnMove: true,
                        speed: 520,
                        breakpoints: {
                            760: { perPage: 1, gap: '1rem' },
                        },
                    });
                });

                return true;
            };

            if (initAccountingSliders()) {
                return;
            }

            let attempts = 0;
            const timer = window.setInterval(function () {
                attempts += 1;
                if (initAccountingSliders() || attempts > 40) {
                    window.clearInterval(timer);
                }
            }, 120);
        }());
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/accounting.blade.php ENDPATH**/ ?>