<?php
    $captchaSiteKey = trim((string) ($storeSettings['captcha']['recaptcha_v3_site_key'] ?? ''));
    $captchaEnabled = (bool) ($storeSettings['captcha']['recaptcha_v3_enabled'] ?? false) && $captchaSiteKey !== '';
    $contactEmail = trim((string) ($storeSettings['footer']['email_support'] ?? '')) ?: 'info@alphacapitalis.com';
    $contactPhone = trim((string) ($storeSettings['footer']['phone'] ?? '')) ?: '+385 (1) 580 6656';
    $contactPhoneHref = preg_replace('/\s+/', '', $contactPhone);
    $heroBadgeIcon = asset('front-theme/images/services-icons/eufondovi.svg');
    $heroBadgeAccent = '#3f7c78';
    $meetingFormLabels = $meetingSection['form_labels'] ?? [];
    $testimonialReadMoreLabel = $locale === 'hr' ? 'Pročitaj više' : 'Read more';
    $testimonialShowLessLabel = $locale === 'hr' ? 'Prikaži manje' : 'Show less';
    $callDownloadLink = $callsSection['download_link'] ?? ['url' => ''];
    $aboutParagraphs = array_values(array_filter(array_map(
        static fn ($paragraph): string => trim((string) $paragraph),
        (array) ($aboutSection['body'] ?? [])
    ), static fn (string $paragraph): bool => $paragraph !== ''));
    $aboutOrbitBlocks = $aboutParagraphs;

    if (count($aboutOrbitBlocks) > 4) {
        $aboutOrbitBlocks = [
            $aboutOrbitBlocks[0],
            $aboutOrbitBlocks[1],
            $aboutOrbitBlocks[2],
            implode(' ', array_slice($aboutOrbitBlocks, 3)),
        ];
    }

    $aboutOrbitBlocks = array_pad($aboutOrbitBlocks, 4, '');
    $aboutLeftBlocks = array_values(array_filter([
        [
            'text' => $aboutOrbitBlocks[0] ?? '',
            'is_quote' => false,
        ],
        [
            'text' => $aboutOrbitBlocks[2] ?? '',
            'is_quote' => false,
        ],
    ], static fn (array $block): bool => $block['text'] !== ''));
    $aboutRightBlocks = array_values(array_filter([
        [
            'text' => $aboutOrbitBlocks[1] ?? '',
            'is_quote' => false,
        ],
        [
            'text' => $aboutOrbitBlocks[3] ?? '',
            'is_quote' => ($aboutOrbitBlocks[3] ?? '') !== '',
        ],
    ], static fn (array $block): bool => $block['text'] !== ''));
?>

<?php $__env->startSection('title', $servicePageMetaTitle !== '' ? $servicePageMetaTitle : ($servicePageTitle ?? 'EU fondovi')); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startSection('content'); ?>
    <div class="ac-family-business-page ac-eu-page">
        <section class="ac-family-hero">
            <div class="ac-family-hero-media" aria-hidden="true" style="background-image: url('<?php echo e($heroBackgroundUrl); ?>');"></div>
            <div class="ac-family-hero-overlay"></div>

            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-family-hero-content">
                    <div class="ac-family-hero-shell">
                        <div class="ac-family-hero-copy">
                            <?php echo $__env->make('front.desktop.partials.service-hero-icon-badge', ['iconUrl' => $heroBadgeIcon, 'accentColor' => $heroBadgeAccent], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <h1 class="ac-family-hero-title">
                                <span class="is-brand"><?php echo e($heroSection['brand_title'] ?? 'ALPHA CAPITALIS'); ?></span>
                                <span class="is-subtitle">
                                    <span class="is-subtitle-lead"><?php echo e($heroSection['subtitle_lead'] ?? 'Savjetnici za'); ?></span>
                                    <span class="is-subtitle-accent"><?php echo e($heroSection['subtitle_accent'] ?? 'EU fondove'); ?></span>
                                </span>
                            </h1>

                            <p class="ac-family-hero-intro"><?php echo e($heroSection['intro'] ?? ''); ?></p>

                            <div class="ac-family-hero-actions">
                                <a href="<?php echo e($heroSection['cta_url'] ?? '#eu-funds-calls'); ?>" class="front-action-cta">
                                    <span><?php echo e($heroSection['cta_label'] ?? 'Pregledajte natječaje'); ?></span>
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 5v14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                                        <path d="m6 13 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="ac-eu-section ac-eu-section--intro ac-blog-related-section" aria-labelledby="ac-eu-about-title">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-support-story-hero">
                    <div class="ac-support-story-shell">
                        <div class="ac-services-head ac-support-story-head ac-eu-intro-head">
                            <div class="ac-services-eyebrow">
                                <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                                <p class="ac-services-kicker"><?php echo e($aboutSection['kicker'] ?? 'EU ODJEL'); ?></p>
                                <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                            </div>
                            <h2 id="ac-eu-about-title">
                                <span><?php echo e($aboutSection['title'] ?? ''); ?></span>
                            </h2>
                            <div class="ac-services-divider" aria-hidden="true">
                                <span class="ac-services-divider-line"></span>
                                <span class="ac-services-divider-glyph"></span>
                                <span class="ac-services-divider-line"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ac-eu-intro-grid">
                    <div class="ac-eu-intro-stage">
                        <div class="ac-eu-about-orbit" aria-label="<?php echo e($locale === 'hr' ? 'Opis EU odjela' : 'EU department description'); ?>">
                            <div class="ac-eu-about-column">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $aboutLeftBlocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <article class="ac-eu-about-block">
                                        <p><?php echo e($block['text']); ?></p>
                                    </article>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="ac-eu-about-column">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $aboutRightBlocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <article class="ac-eu-about-block">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($block['is_quote']): ?>
                                            <blockquote class="ac-eu-about-blockquote">
                                                <p><?php echo e($block['text']); ?></p>
                                            </blockquote>
                                        <?php else: ?>
                                            <p><?php echo e($block['text']); ?></p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </article>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="ac-eu-section ac-eu-section--overview" aria-labelledby="ac-eu-overview-title">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-eu-editorial-content">
                    <div class="ac-services-head ac-support-story-head ac-eu-editorial-head">
                        <div class="ac-services-eyebrow">
                            <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                            <p class="ac-services-kicker"><?php echo e($overviewSection['kicker'] ?? 'EU FONDOVI'); ?></p>
                            <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                        </div>
                        <h2 id="ac-eu-overview-title"><?php echo e($overviewSection['title'] ?? ''); ?></h2>
                        <p class="ac-services-intro"><?php echo e($overviewSection['intro'] ?? ''); ?></p>
                        <div class="ac-services-divider" aria-hidden="true">
                            <span class="ac-services-divider-line"></span>
                            <span class="ac-services-divider-glyph"></span>
                            <span class="ac-services-divider-line"></span>
                        </div>
                    </div>

                    <div class="ac-eu-editorial-body">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($overviewSection['body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p><?php echo e($paragraph); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="ac-eu-section ac-eu-section--metrics" aria-labelledby="ac-eu-metrics-title">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-eu-metrics-grid">
                    <article class="ac-eu-panel ac-eu-panel--chart">
                        <p class="ac-family-section-kicker"><?php echo e($chartSection['kicker'] ?? 'OKVIR FINANCIRANJA'); ?></p>
                        <h2 id="ac-eu-metrics-title"><?php echo e($chartSection['title'] ?? ''); ?></h2>
                        <p class="ac-eu-panel-intro"><?php echo e($chartSection['intro'] ?? ''); ?></p>

                        <div class="ac-eu-stat-stack">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($chartSection['stats'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $share = max(8, min(100, (int) ($stat['share'] ?? 0)));
                                ?>
                                <div class="ac-eu-stat-row">
                                    <div class="ac-eu-stat-copy">
                                        <div class="ac-eu-stat-label-row">
                                            <h3><?php echo e($stat['label'] ?? ''); ?></h3>
                                            <strong><?php echo e($stat['value'] ?? ''); ?></strong>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($stat['description'] ?? null)): ?>
                                            <p><?php echo e($stat['description']); ?></p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div class="ac-eu-stat-bar">
                                        <span style="width: <?php echo e($share); ?>%;"></span>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($chartSection['footnote'] ?? '')) !== ''): ?>
                            <p class="ac-eu-footnote"><?php echo e($chartSection['footnote']); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </article>

                    <article class="ac-eu-panel ac-eu-panel--process">
                        <p class="ac-family-section-kicker"><?php echo e($processSection['kicker'] ?? 'KAKO RADIMO'); ?></p>
                        <h2><?php echo e($processSection['title'] ?? ''); ?></h2>
                        <p class="ac-eu-panel-intro"><?php echo e($processSection['intro'] ?? ''); ?></p>

                        <div class="ac-eu-process-list">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($processSection['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <article class="ac-eu-process-card">
                                    <span class="ac-eu-process-index"><?php echo e(str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT)); ?></span>
                                    <h3><?php echo e($item['title'] ?? ''); ?></h3>
                                    <p><?php echo e($item['text'] ?? ''); ?></p>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section id="eu-funds-calls" class="ac-eu-section ac-eu-section--calls" aria-labelledby="ac-eu-calls-title">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-eu-section-head">
                    <div>
                        <p class="ac-family-section-kicker"><?php echo e($callsSection['kicker'] ?? 'PREGLED NATJEČAJA'); ?></p>
                        <h2 id="ac-eu-calls-title"><?php echo e($callsSection['title'] ?? ''); ?></h2>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($callDownloadLink['url'] ?? '')): ?>
                        <a
                            href="<?php echo e($callDownloadLink['url']); ?>"
                            class="ac-eu-download-button"
                            <?php if($callDownloadLink['open_in_new_tab'] ?? false): ?> target="_blank" rel="<?php echo e($callDownloadLink['rel'] ?? 'noopener noreferrer'); ?>" <?php endif; ?>
                        >
                            <span><?php echo e($callDownloadLink['label'] ?: 'Preuzmite pregled natječaja'); ?></span>
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 4v11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                                <path d="m7 11 5 5 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M5 20h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                            </svg>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <p class="ac-eu-section-intro"><?php echo e($callsSection['intro'] ?? ''); ?></p>

                <div class="ac-eu-call-grid">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($callsSection['groups'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $tone = trim((string) ($group['tone'] ?? 'pending'));
                            $groupItems = array_values((array) ($group['items'] ?? []));
                            $isCollapsibleClosedGroup = $tone === 'closed' && count($groupItems) > 6;
                            $visibleItems = $isCollapsibleClosedGroup ? array_slice($groupItems, 0, 6) : $groupItems;
                            $hiddenItems = $isCollapsibleClosedGroup ? array_slice($groupItems, 6) : [];
                            $collapseId = $isCollapsibleClosedGroup ? 'ac-eu-call-more-'.$loop->index : null;
                        ?>
                        <article class="ac-eu-call-card is-<?php echo e($tone); ?>">
                            <div class="ac-eu-call-card-head">
                                <h3><?php echo e($group['title'] ?? ''); ?></h3>
                                <span><?php echo e(count($groupItems)); ?></span>
                            </div>

                            <ul class="ac-eu-call-list">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $visibleItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $resolvedLink = $item['resolved_link'] ?? ['url' => ''];
                                        $itemUrl = trim((string) ($resolvedLink['url'] ?? ''));
                                        $publishedLabel = trim((string) ($item['published_label'] ?? ''));
                                    ?>
                                    <li class="<?php echo e($itemUrl !== '' ? 'is-linked' : 'is-static'); ?>">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($itemUrl !== ''): ?>
                                            <a
                                                href="<?php echo e($itemUrl); ?>"
                                                <?php if($resolvedLink['open_in_new_tab'] ?? false): ?> target="_blank" rel="<?php echo e($resolvedLink['rel'] ?? 'noopener noreferrer'); ?>" <?php endif; ?>
                                            >
                                                <span class="ac-eu-call-item-title"><?php echo e($item['title'] ?? ''); ?></span>
                                                <span class="ac-eu-call-item-meta">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($publishedLabel !== ''): ?>
                                                        <span class="ac-eu-call-item-date"><?php echo e($publishedLabel); ?></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <path d="M4 12L12 4"></path>
                                                        <path d="M6 4h6v6"></path>
                                                    </svg>
                                                </span>
                                            </a>
                                        <?php else: ?>
                                            <div class="ac-eu-call-item-row">
                                                <span class="ac-eu-call-item-title"><?php echo e($item['title'] ?? ''); ?></span>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($publishedLabel !== ''): ?>
                                                    <span class="ac-eu-call-item-meta">
                                                        <span class="ac-eu-call-item-date"><?php echo e($publishedLabel); ?></span>
                                                    </span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </ul>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCollapsibleClosedGroup): ?>
                                <div
                                    id="<?php echo e($collapseId); ?>"
                                    class="ac-eu-call-list-more"
                                    data-eu-call-more
                                    data-expanded="false"
                                    hidden
                                >
                                    <ul class="ac-eu-call-list ac-eu-call-list--more">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $hiddenItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $resolvedLink = $item['resolved_link'] ?? ['url' => ''];
                                                $itemUrl = trim((string) ($resolvedLink['url'] ?? ''));
                                                $publishedLabel = trim((string) ($item['published_label'] ?? ''));
                                            ?>
                                            <li class="<?php echo e($itemUrl !== '' ? 'is-linked' : 'is-static'); ?>">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($itemUrl !== ''): ?>
                                                    <a
                                                        href="<?php echo e($itemUrl); ?>"
                                                        <?php if($resolvedLink['open_in_new_tab'] ?? false): ?> target="_blank" rel="<?php echo e($resolvedLink['rel'] ?? 'noopener noreferrer'); ?>" <?php endif; ?>
                                                    >
                                                        <span class="ac-eu-call-item-title"><?php echo e($item['title'] ?? ''); ?></span>
                                                        <span class="ac-eu-call-item-meta">
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($publishedLabel !== ''): ?>
                                                                <span class="ac-eu-call-item-date"><?php echo e($publishedLabel); ?></span>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                                <path d="M4 12L12 4"></path>
                                                                <path d="M6 4h6v6"></path>
                                                            </svg>
                                                        </span>
                                                    </a>
                                                <?php else: ?>
                                                    <div class="ac-eu-call-item-row">
                                                        <span class="ac-eu-call-item-title"><?php echo e($item['title'] ?? ''); ?></span>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($publishedLabel !== ''): ?>
                                                            <span class="ac-eu-call-item-meta">
                                                                <span class="ac-eu-call-item-date"><?php echo e($publishedLabel); ?></span>
                                                            </span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </ul>
                                </div>

                                <button
                                    type="button"
                                    class="ac-eu-call-toggle"
                                    data-eu-call-toggle
                                    data-target="<?php echo e($collapseId); ?>"
                                    data-label-more="<?php echo e(str_starts_with(strtolower($locale), 'hr') ? 'Pogledaj sve' : 'View all'); ?>"
                                    data-label-less="<?php echo e(str_starts_with(strtolower($locale), 'hr') ? 'Prikaži manje' : 'Show less'); ?>"
                                    aria-expanded="false"
                                    aria-controls="<?php echo e($collapseId); ?>"
                                >
                                    <span><?php echo e(str_starts_with(strtolower($locale), 'hr') ? 'Pogledaj sve' : 'View all'); ?></span>
                                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M4 6L8 10L12 6"></path>
                                    </svg>
                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <section class="ac-eu-section ac-eu-section--resources" aria-labelledby="ac-eu-resources-title">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-services-head ac-support-story-head ac-eu-centered-head">
                    <div class="ac-services-eyebrow">
                        <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                        <p class="ac-services-kicker"><?php echo e($resourcesSection['kicker'] ?? 'PROGRAMI PODRŠKE'); ?></p>
                        <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                    </div>
                    <h2 id="ac-eu-resources-title"><?php echo e($resourcesSection['title'] ?? ''); ?></h2>
                    <p class="ac-services-intro"><?php echo e($resourcesSection['intro'] ?? ''); ?></p>
                </div>

                <div class="ac-eu-resource-grid">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($resourcesSection['cards'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="ac-eu-resource-card">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($card['eyebrow'] ?? null)): ?>
                                <p class="ac-family-section-kicker"><?php echo e($card['eyebrow']); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <h3><?php echo e($card['title'] ?? ''); ?></h3>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($card['body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p><?php echo e($paragraph); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($card['groups'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="ac-eu-resource-group">
                                    <h4><?php echo e($group['label'] ?? ''); ?></h4>
                                    <ul>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($group['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $resolvedLink = $item['resolved_link'] ?? ['url' => ''];
                                                $itemUrl = trim((string) ($resolvedLink['url'] ?? ''));
                                            ?>
                                            <li>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($itemUrl !== ''): ?>
                                                    <a
                                                        href="<?php echo e($itemUrl); ?>"
                                                        <?php if($resolvedLink['open_in_new_tab'] ?? false): ?> target="_blank" rel="<?php echo e($resolvedLink['rel'] ?? 'noopener noreferrer'); ?>" <?php endif; ?>
                                                    ><?php echo e($item['title'] ?? ''); ?></a>
                                                <?php else: ?>
                                                    <span><?php echo e($item['title'] ?? ''); ?></span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </ul>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty(($card['primary_link']['url'] ?? '')) || !empty(($card['secondary_link']['url'] ?? ''))): ?>
                                <div class="ac-eu-resource-actions">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($card['primary_link']['url'] ?? '')): ?>
                                        <a
                                            href="<?php echo e($card['primary_link']['url']); ?>"
                                            class="ac-eu-inline-link"
                                            <?php if($card['primary_link']['open_in_new_tab'] ?? false): ?> target="_blank" rel="<?php echo e($card['primary_link']['rel'] ?? 'noopener noreferrer'); ?>" <?php endif; ?>
                                        >
                                            <?php echo e($card['primary_link']['label'] ?: 'Saznaj vise'); ?>

                                        </a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($card['secondary_link']['url'] ?? '')): ?>
                                        <a
                                            href="<?php echo e($card['secondary_link']['url']); ?>"
                                            class="ac-eu-inline-link ac-eu-inline-link--secondary"
                                            <?php if($card['secondary_link']['open_in_new_tab'] ?? false): ?> target="_blank" rel="<?php echo e($card['secondary_link']['rel'] ?? 'noopener noreferrer'); ?>" <?php endif; ?>
                                        >
                                            <?php echo e($card['secondary_link']['label'] ?: 'Otvori dokument'); ?>

                                        </a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <section class="ac-eu-section ac-eu-section--laws" aria-labelledby="ac-eu-laws-title">
            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-services-head ac-support-story-head ac-eu-centered-head">
                    <div class="ac-services-eyebrow">
                        <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                        <p class="ac-services-kicker"><?php echo e($lawsSection['kicker'] ?? 'ZAKONSKI OKVIR'); ?></p>
                        <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                    </div>
                    <h2 id="ac-eu-laws-title"><?php echo e($lawsSection['title'] ?? ''); ?></h2>
                    <p class="ac-services-intro"><?php echo e($lawsSection['intro'] ?? ''); ?></p>
                </div>

                <div class="ac-eu-law-grid">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($lawsSection['cards'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="ac-eu-law-card">
                            <h3><?php echo e($card['title'] ?? ''); ?></h3>
                            <p class="ac-eu-law-summary"><?php echo e($card['summary'] ?? ''); ?></p>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($card['lists'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="ac-eu-law-list-block">
                                    <h4><?php echo e($list['label'] ?? ''); ?></h4>
                                    <ul class="ac-eu-law-list">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($list['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($item); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </ul>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($card['note'] ?? '')) !== ''): ?>
                                <p class="ac-eu-law-note"><?php echo e($card['note']); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty(($card['primary_link']['url'] ?? '')) || !empty(($card['secondary_link']['url'] ?? ''))): ?>
                                <div class="ac-eu-resource-actions">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($card['primary_link']['url'] ?? '')): ?>
                                        <a
                                            href="<?php echo e($card['primary_link']['url']); ?>"
                                            class="ac-eu-inline-link"
                                            <?php if($card['primary_link']['open_in_new_tab'] ?? false): ?> target="_blank" rel="<?php echo e($card['primary_link']['rel'] ?? 'noopener noreferrer'); ?>" <?php endif; ?>
                                        >
                                            <?php echo e($card['primary_link']['label'] ?: 'Vise informacija'); ?>

                                        </a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($card['secondary_link']['url'] ?? '')): ?>
                                        <a
                                            href="<?php echo e($card['secondary_link']['url']); ?>"
                                            class="ac-eu-inline-link ac-eu-inline-link--secondary"
                                            <?php if($card['secondary_link']['open_in_new_tab'] ?? false): ?> target="_blank" rel="<?php echo e($card['secondary_link']['rel'] ?? 'noopener noreferrer'); ?>" <?php endif; ?>
                                        >
                                            <?php echo e($card['secondary_link']['label'] ?: 'Otvori dokument'); ?>

                                        </a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($euFundsTestimonials ?? collect())->isNotEmpty()): ?>
            <section class="ac-global-memberships ac-client-experiences ac-eu-testimonials" aria-labelledby="ac-eu-testimonials-title">
                <div class="ac-global-memberships-shell mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                    <div class="ac-services-head ac-support-story-head ac-global-memberships-head ac-client-experiences-head">
                        <div class="ac-services-eyebrow">
                            <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                            <p class="ac-services-kicker"><?php echo e($testimonialsSection['kicker'] ?? 'PREPORUKE KLIJENATA'); ?></p>
                            <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                        </div>
                        <h2 id="ac-eu-testimonials-title">
                            <span><?php echo e($testimonialsSection['title'] ?? ''); ?></span>
                        </h2>
                        <p class="ac-services-intro"><?php echo e($testimonialsSection['intro'] ?? ''); ?></p>
                        <div class="ac-services-divider" aria-hidden="true">
                            <span class="ac-services-divider-line"></span>
                            <span class="ac-services-divider-glyph"></span>
                            <span class="ac-services-divider-line"></span>
                        </div>
                    </div>

                    <div class="ac-client-experiences-carousel">
                        <div id="ac-eu-testimonials-splide" class="splide ac-client-experiences-splide" data-eu-funds-testimonials-splide>
                            <div class="splide__track">
                                <ul class="splide__list ac-client-experiences-list">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $euFundsTestimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testimonial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $company = trim((string) ($testimonial->payload['company'] ?? ''));
                                            $rating = max(1, min(5, (int) ($testimonial->rating ?? 5)));
                                        ?>
                                        <li class="splide__slide ac-client-experiences-slide">
                                            <article class="ac-client-experience-card" data-eu-funds-testimonial-card>
                                                <div class="ac-client-experience-card-inner">
                                                    <div class="ac-client-experience-quote-mark" aria-hidden="true">“</div>
                                                    <div class="ac-client-experience-content">
                                                        <div class="ac-client-experience-rating" aria-label="<?php echo e($rating); ?> / 5">
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 1; $i <= 5; $i++): ?>
                                                                <span class="<?php echo e($i <= $rating ? 'is-active' : ''); ?>">★</span>
                                                            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </div>
                                                        <p class="ac-client-experience-body" data-eu-funds-testimonial-body><?php echo e($testimonial->body); ?></p>
                                                        <button
                                                            type="button"
                                                            class="ac-client-experience-toggle"
                                                            data-eu-funds-testimonial-toggle
                                                            data-more-label="<?php echo e($testimonialReadMoreLabel); ?>"
                                                            data-less-label="<?php echo e($testimonialShowLessLabel); ?>"
                                                            aria-expanded="false"
                                                            hidden
                                                        ><?php echo e($testimonialReadMoreLabel); ?></button>
                                                    </div>
                                                    <div class="ac-client-experience-meta">
                                                        <h3><?php echo e($testimonial->author_name ?: __('Anonymous')); ?></h3>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($company !== ''): ?>
                                                            <p><?php echo e($company); ?></p>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </div>
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

        <?php echo $__env->make('front.desktop.partials.service-videos', [
            'serviceVideoSection' => $serviceVideoSection ?? [],
            'serviceVideos' => $serviceVideos ?? [],
            'locale' => $locale ?? app()->getLocale(),
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
            <section id="eu-funds-contact" class="ac-family-section ac-eu-contact-section pb-16 md:pb-24" aria-labelledby="ac-eu-contact-title">
                <div class="ac-family-team-showcase-head">
                    <p class="ac-family-section-kicker"><?php echo e($meetingSection['kicker'] ?? 'KONTAKT'); ?></p>
                    <h2 id="ac-eu-contact-title"><?php echo e($meetingSection['title'] ?? ''); ?></h2>
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
                        <input type="hidden" name="redirect_to" value="<?php echo e(route('eu-funds.show')); ?>#eu-funds-contact">

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
                            <div class="front-contact-status" role="status">
                                <?php echo e(session('status')); ?>

                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="eu-first-name"><?php echo e($meetingFormLabels['first_name'] ?? 'Ime'); ?></label>
                                <input id="eu-first-name" type="text" name="first_name" value="<?php echo e(old('first_name')); ?>" class="front-contact-input h-11 w-full text-sm" required>
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
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="eu-last-name"><?php echo e($meetingFormLabels['last_name'] ?? 'Prezime'); ?></label>
                                <input id="eu-last-name" type="text" name="last_name" value="<?php echo e(old('last_name')); ?>" class="front-contact-input h-11 w-full text-sm">
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
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="eu-company"><?php echo e($meetingFormLabels['company'] ?? 'Tvrtka'); ?></label>
                                <input id="eu-company" type="text" name="company" value="<?php echo e(old('company')); ?>" class="front-contact-input h-11 w-full text-sm">
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
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="eu-phone"><?php echo e($meetingFormLabels['phone'] ?? 'Broj telefona'); ?></label>
                                <input id="eu-phone" type="text" name="phone" value="<?php echo e(old('phone')); ?>" class="front-contact-input h-11 w-full text-sm">
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
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="eu-email"><?php echo e($meetingFormLabels['email'] ?? 'Email'); ?></label>
                            <input id="eu-email" type="email" name="email" value="<?php echo e(old('email', auth()->user()?->email)); ?>" class="front-contact-input h-11 w-full text-sm" required>
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
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="eu-subject"><?php echo e($meetingFormLabels['subject'] ?? 'Naslov poruke'); ?></label>
                            <input id="eu-subject" type="text" name="subject" value="<?php echo e(old('subject')); ?>" class="front-contact-input h-11 w-full text-sm">
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
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="eu-message"><?php echo e($meetingFormLabels['message'] ?? 'Poruka'); ?></label>
                            <textarea id="eu-message" name="message" rows="8" class="front-contact-textarea w-full text-sm" required><?php echo e(old('message')); ?></textarea>
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

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($euFundsPosts ?? collect())->isNotEmpty()): ?>
            <section class="ac-support-story ac-home-blog ac-blog-related-section ac-family-blog-section" aria-labelledby="ac-eu-blog-title">
                <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                    <div class="ac-support-story-hero">
                        <div class="ac-support-story-shell">
                            <div class="ac-services-head ac-support-story-head">
                                <h2 id="ac-eu-blog-title">
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

                    <div class="ac-home-blog-carousel">
                        <div id="ac-eu-blog-splide" class="splide ac-home-blog-splide" data-eu-funds-blog-splide>
                            <div class="splide__track">
                                <ul class="splide__list">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $euFundsPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                            $categoryLabel = trim((string) ($categoryTranslation?->name ?? 'Novosti'));
                                            $publishedLabel = ($post->published_at ?? $post->created_at)?->translatedFormat('j. F Y.');
                                        ?>
                                        <li class="splide__slide ac-home-blog-slide">
                                            <article class="ac-home-blog-card">
                                                <a href="<?php echo e($postUrl); ?>" class="ac-home-blog-card-link" aria-label="Otvori blog post: <?php echo e($postTitle); ?>">
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
                                                            <span>Opširnije</span>
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
                </div>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php if (! $__env->hasRenderedOnce('29ec1c81-f7f9-42b0-9ef6-9d25b5f83d72')): $__env->markAsRenderedOnce('29ec1c81-f7f9-42b0-9ef6-9d25b5f83d72'); ?>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .ac-eu-page {
            background: linear-gradient(180deg, #faf7f1 0%, #f7f2e8 100%);
        }

        .ac-eu-page .ac-eu-section {
            position: relative;
            padding: clamp(2.8rem, 4vw, 4rem) 0;
            background: transparent !important;
            border-color: transparent !important;
            overflow: visible !important;
        }

        .ac-eu-page .ac-eu-section::before {
            content: none !important;
            display: none !important;
        }

        .ac-eu-page .ac-eu-section--metrics,
        .ac-eu-page .ac-eu-section--laws,
        .ac-eu-page .ac-eu-testimonials {
            background: linear-gradient(180deg, #f1ece2 0%, #f8f5ef 100%) !important;
        }

        .ac-eu-page .ac-eu-section--intro.ac-blog-related-section {
            --ac-eu-corner-stars-size: min(37.4rem, 39.1vw);
            --ac-eu-corner-stars-offset-x: calc(var(--ac-eu-corner-stars-size) * -0.52);
            --ac-eu-corner-stars-offset-y: calc(var(--ac-eu-corner-stars-size) * -0.52);
            margin-top: 0;
            padding-top: clamp(2.6rem, 4vw, 3.8rem);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.34), rgba(255, 255, 255, 0.08)),
                repeating-linear-gradient(90deg, rgba(15, 42, 67, 0.05) 0 1px, transparent 1px 24px),
                radial-gradient(54% 76% at 86% 18%, rgba(65, 122, 176, 0.16), transparent 62%),
                radial-gradient(38% 56% at 12% 84%, rgba(171, 141, 82, 0.08), transparent 68%),
                linear-gradient(120deg, #eef3f7 0%, #e7eef4 48%, #dde7f0 100%) !important;
            overflow: hidden !important;
        }

        .ac-eu-page .ac-eu-section--intro.ac-blog-related-section::before {
            content: '' !important;
            display: block !important;
            position: absolute;
            top: var(--ac-eu-corner-stars-offset-y);
            left: var(--ac-eu-corner-stars-offset-x);
            width: var(--ac-eu-corner-stars-size);
            height: var(--ac-eu-corner-stars-size);
            background: url('<?php echo e(asset('front-theme/images/services/Stars_of_the_European_Union_(bw).svg')); ?>') no-repeat center center / contain;
            opacity: 0.045;
            pointer-events: none;
            z-index: 0;
        }

        .ac-eu-page .ac-eu-section--intro.ac-blog-related-section::after {
            content: '';
            display: block !important;
            position: absolute;
            bottom: var(--ac-eu-corner-stars-offset-y);
            right: var(--ac-eu-corner-stars-offset-x);
            width: var(--ac-eu-corner-stars-size);
            height: var(--ac-eu-corner-stars-size);
            background: url('<?php echo e(asset('front-theme/images/services/Stars_of_the_European_Union_(bw).svg')); ?>') no-repeat center center / contain;
            opacity: 0.045;
            pointer-events: none;
            z-index: 0;
            transform: rotate(180deg);
            transform-origin: center;
        }

        .ac-eu-page .ac-eu-section--intro > .mx-auto {
            position: relative;
            z-index: 1;
        }

        .ac-eu-intro-grid {
            position: relative;
            z-index: 1;
        }

        .ac-eu-panel,
        .ac-eu-resource-card,
        .ac-eu-law-card {
            border: 1px solid rgba(171, 141, 82, 0.14);
            box-shadow: 0 18px 44px rgba(58, 86, 120, 0.08);
        }

        .ac-eu-section--overview {
            padding-top: 0.2rem;
        }

        .ac-eu-intro-head {
            margin-bottom: 1.7rem;
        }

        .ac-eu-intro-head h2,
        .ac-eu-intro-head .ac-services-intro {
            text-align: center;
        }

        .ac-eu-intro-head .ac-services-eyebrow,
        .ac-eu-intro-head .ac-services-divider {
            justify-content: center;
        }

        .ac-eu-editorial-head h2,
        .ac-eu-panel h2,
        .ac-eu-section-head h2,
        .ac-eu-centered-head h2 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.55rem, 2.2vw, 2.32rem);
            line-height: 1.08;
            font-weight: 600;
            color: #0f172a;
        }

        .ac-eu-about-block p,
        .ac-eu-editorial-body p,
        .ac-eu-panel p,
        .ac-eu-resource-card p,
        .ac-eu-law-card p {
            margin: 0;
            font-size: 1rem;
            line-height: 1.8;
            color: #403a34;
            text-wrap: pretty;
        }

        .ac-eu-editorial-body p + p,
        .ac-eu-resource-card p + p {
            margin-top: 0.95rem;
        }

        .ac-eu-intro-stage {
            position: relative;
            max-width: 1040px;
            margin: 0 auto;
            padding: 0.35rem 0 0;
        }

        .ac-eu-about-orbit {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: clamp(2rem, 3vw, 2.8rem);
            max-width: 980px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
            align-items: start;
        }

        .ac-eu-about-orbit::before {
            content: '';
            position: absolute;
            top: 1rem;
            bottom: 1rem;
            left: 50%;
            width: 1px;
            background: linear-gradient(180deg, transparent 0%, rgba(150, 167, 183, 0.34) 14%, rgba(150, 167, 183, 0.34) 86%, transparent 100%);
            transform: translateX(-50%);
        }

        .ac-eu-about-column {
            display: grid;
            align-content: start;
            gap: clamp(2rem, 3vw, 2.8rem);
        }

        .ac-eu-about-column:first-child {
            padding-right: clamp(0.35rem, 0.8vw, 0.75rem);
        }

        .ac-eu-about-column:last-child {
            padding-left: clamp(0.35rem, 0.8vw, 0.75rem);
        }

        .ac-eu-about-block {
            position: relative;
            padding: 0;
        }

        .ac-eu-about-blockquote {
            position: relative;
            margin: 0;
            padding: 1.4rem 1.5rem 1.45rem 3.9rem;
            border-left: 4px solid rgba(76, 118, 163, 0.26);
            background: transparent;
            box-shadow: none;
        }

        .ac-eu-about-blockquote::before {
            content: '\201C';
            position: absolute;
            top: 1.1rem;
            left: 1.15rem;
            font-family: 'Playfair Display', serif;
            font-size: 3.2rem;
            line-height: 1;
            font-weight: 700;
            color: rgba(76, 118, 163, 0.9);
        }

        .ac-eu-about-blockquote p {
            font-style: normal;
            color: #24384f;
        }

        .ac-eu-chip-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .ac-eu-chip-list li {
            display: inline-flex;
            align-items: center;
            min-height: 2.35rem;
            padding: 0.55rem 0.9rem;
            border-radius: 999px;
            background: rgba(120, 96, 58, 0.08);
            color: #6d5633;
            font-size: 0.88rem;
            line-height: 1.3;
        }

        .ac-eu-editorial-content {
            max-width: 1040px;
            margin: 0 auto;
        }

        .ac-eu-editorial-head {
            max-width: 50rem;
            margin: 0 auto;
            text-align: center;
            padding-top: 0.35rem;
        }

        .ac-eu-editorial-head .ac-services-eyebrow {
            justify-content: center;
        }

        .ac-eu-editorial-head .ac-services-intro {
            max-width: 48rem;
            margin-left: auto;
            margin-right: auto;
            color: #57534e;
            text-align: center;
        }

        .ac-eu-editorial-head .ac-services-divider {
            justify-content: center;
        }

        .ac-eu-editorial-body {
            max-width: 62rem;
            margin: 1.7rem auto 0;
        }

        .ac-eu-centered-head .ac-services-intro {
            max-width: 48rem;
            margin-left: auto;
            margin-right: auto;
            color: #57534e;
        }

        .ac-eu-metrics-grid,
        .ac-eu-resource-grid,
        .ac-eu-law-grid {
            display: grid;
            gap: 1.35rem;
        }

        .ac-eu-panel {
            padding: 1.6rem;
            border-radius: 22px;
        }

        .ac-eu-section--metrics .ac-eu-panel {
            padding: 0;
            border: none;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
        }

        .ac-eu-panel-intro {
            margin-top: 0;
            color: #57534e;
        }

        .ac-eu-section--metrics .ac-family-section-kicker {
            margin: 0 0 0.85rem;
            color: #7c653b;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .ac-eu-section--metrics .ac-eu-panel h2 {
            margin-bottom: 1.15rem;
        }

        .ac-eu-stat-stack {
            margin-top: 2.1rem;
            display: grid;
            gap: 1.1rem;
        }

        .ac-eu-stat-row {
            display: grid;
            gap: 0.72rem;
            padding: 1rem 1.05rem 1.05rem;
            border: 1px solid rgba(171, 141, 82, 0.12);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.62);
        }

        .ac-eu-stat-copy {
            display: grid;
            gap: 0.45rem;
        }

        .ac-eu-stat-label-row {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            justify-content: space-between;
            gap: 0.8rem;
        }

        .ac-eu-stat-label-row h3,
        .ac-eu-process-card h3,
        .ac-eu-call-card h3,
        .ac-eu-resource-card h3,
        .ac-eu-law-card h3 {
            font-size: 1.02rem;
            font-weight: 700;
            line-height: 1.45;
            color: #0f172a;
        }

        .ac-eu-stat-label-row h3 {
            margin: 0;
        }

        .ac-eu-stat-label-row strong {
            font-size: 1rem;
            font-weight: 700;
            color: #6d5633;
        }

        .ac-eu-stat-copy p {
            color: #57534e;
        }

        .ac-eu-stat-bar {
            width: 100%;
            height: 0.8rem;
            border-radius: 999px;
            background: rgba(120, 96, 58, 0.08);
            overflow: hidden;
        }

        .ac-eu-stat-bar span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: #b89862;
        }

        .ac-eu-footnote {
            margin-top: 1.45rem;
            padding-top: 1.15rem;
            border-top: 1px solid rgba(171, 141, 82, 0.14);
            color: #57534e;
        }

        .ac-eu-process-list {
            display: grid;
            gap: 1rem;
            margin-top: 2.1rem;
        }

        .ac-eu-process-card {
            position: relative;
            padding: 1.2rem 1.15rem 1.2rem 3.9rem;
            border: 1px solid rgba(171, 141, 82, 0.12);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.52);
        }

        .ac-eu-process-card h3 {
            margin: 0 0 0.5rem;
        }

        .ac-eu-process-index {
            position: absolute;
            top: 1.15rem;
            left: 1.15rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 999px;
            background: rgba(120, 96, 58, 0.08);
            color: #6d5633;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.1em;
        }

        .ac-eu-section-head {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
            justify-content: space-between;
        }

        .ac-eu-section-intro {
            max-width: 58rem;
            margin-top: 1rem;
            font-size: 0.99rem;
            line-height: 1.74;
            color: #57534e;
        }

        .ac-eu-page .front-action-cta,
        .ac-eu-page .front-contact-submit,
        .ac-eu-download-button,
        .ac-eu-inline-link,
        .ac-eu-inline-link--secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.7rem;
            padding: 0.62rem 1rem;
            border-radius: 10px;
            border: 2px solid rgba(15, 23, 42, 0.86);
            background: #0f172a;
            color: #f8f6f1 !important;
            font-family: 'Sora', 'Public Sans', 'Segoe UI', Arial, sans-serif;
            font-size: 0.84rem;
            font-weight: 600;
            letter-spacing: 0.07em;
            line-height: 1;
            text-decoration: none;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.14);
            transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .ac-eu-page .ac-family-hero-actions .front-action-cta {
            border-color: rgba(248, 246, 241, 0.38);
        }

        .ac-eu-download-button {
            gap: 0.7rem;
            min-height: 3rem;
            padding: 0.75rem 1.15rem;
        }

        .ac-eu-download-button span,
        .ac-eu-download-button svg {
            color: inherit !important;
            opacity: 1;
        }

        .ac-eu-page .front-action-cta:hover,
        .ac-eu-page .front-contact-submit:hover,
        .ac-eu-download-button:hover,
        .ac-eu-inline-link:hover,
        .ac-eu-inline-link--secondary:hover {
            background: #123250;
            color: #ffffff !important;
            border-color: #123250;
            box-shadow: 0 14px 24px rgba(15, 42, 67, 0.12);
            transform: translateY(-1px);
        }

        .ac-eu-page .ac-family-hero-actions .front-action-cta:hover,
        .ac-eu-page .ac-family-hero-actions .front-action-cta:focus-visible {
            background: rgba(183, 150, 82, 0.3);
            color: #ffffff !important;
            border-color: rgba(183, 150, 82, 0.92);
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.24);
            transform: none;
        }

        .ac-eu-download-button svg {
            width: 1.05rem;
            height: 1.05rem;
        }

        .ac-eu-call-grid {
            display: grid;
            gap: 1.15rem;
            margin-top: 1.8rem;
        }

        .ac-eu-call-card {
            padding: 1.25rem 1.15rem 1.05rem;
            border-radius: 22px;
            border: 1px solid rgba(171, 141, 82, 0.12);
            box-shadow: none;
        }

        .ac-eu-call-card.is-pending {
            background: rgba(255, 255, 255, 0.7);
        }

        .ac-eu-call-card.is-open {
            background: rgba(255, 255, 255, 0.78);
            border-color: rgba(171, 141, 82, 0.16);
        }

        .ac-eu-call-card.is-closed {
            background: rgba(249, 246, 240, 0.72);
        }

        .ac-eu-call-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.95rem;
        }

        .ac-eu-call-card-head span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 2rem;
            padding: 0 0.55rem;
            border-radius: 999px;
            background: rgba(120, 96, 58, 0.08);
            color: #6d5633;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .ac-eu-call-list {
            display: grid;
            gap: 0;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .ac-eu-call-list li {
            min-width: 0;
            border-top: 1px solid rgba(171, 141, 82, 0.12);
        }

        .ac-eu-call-list li a,
        .ac-eu-call-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.95rem 0.15rem;
            font-size: 0.94rem;
            line-height: 1.55;
        }

        .ac-eu-call-list li a {
            color: #0f172a;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .ac-eu-call-list li a:hover {
            color: #123250;
        }

        .ac-eu-call-item-title {
            min-width: 0;
            flex: 1 1 auto;
        }

        .ac-eu-call-item-meta {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.75rem;
            flex: none;
            color: #6b7280;
            white-space: nowrap;
        }

        .ac-eu-call-item-date {
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #78716c;
        }

        .ac-eu-call-list li a svg {
            width: 0.92rem;
            height: 0.92rem;
            flex: none;
        }

        .ac-eu-call-list--more {
            padding-top: 0.1rem;
        }

        .ac-eu-call-list-more {
            overflow: hidden;
            max-height: 0;
            opacity: 0;
            margin-top: 0;
            transition: max-height 0.34s ease, opacity 0.24s ease, margin-top 0.34s ease;
        }

        .ac-eu-call-list-more[data-expanded="true"] {
            opacity: 1;
            margin-top: 0.15rem;
        }

        .ac-eu-call-toggle {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            margin-top: 0.95rem;
            padding: 0;
            border: 0;
            background: transparent;
            color: #0f172a;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .ac-eu-call-toggle:hover {
            color: #123250;
        }

        .ac-eu-call-toggle svg {
            width: 0.95rem;
            height: 0.95rem;
            transition: transform 0.24s ease;
        }

        .ac-eu-call-toggle[aria-expanded="true"] svg {
            transform: rotate(180deg);
        }

        .ac-eu-centered-head {
            max-width: 52rem;
            margin: 0 auto 2rem;
            text-align: center;
        }

        .ac-eu-centered-head .ac-services-eyebrow {
            justify-content: center;
        }

        .ac-eu-resource-card,
        .ac-eu-law-card {
            padding: 1.45rem 1.2rem;
            border-radius: 22px;
        }

        .ac-eu-resource-card h4,
        .ac-eu-law-list-block h4 {
            margin-top: 1rem;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #6d5633;
        }

        .ac-eu-resource-group ul,
        .ac-eu-law-list {
            display: grid;
            gap: 0.55rem;
            list-style: none;
            padding: 0;
            margin: 0.75rem 0 0;
        }

        .ac-eu-resource-group li,
        .ac-eu-law-list li {
            position: relative;
            padding-left: 1.1rem;
            font-size: 0.93rem;
            line-height: 1.6;
            color: #403a34;
        }

        .ac-eu-resource-group li::before,
        .ac-eu-law-list li::before {
            content: '';
            position: absolute;
            top: 0.62rem;
            left: 0;
            width: 0.42rem;
            height: 0.42rem;
            border-radius: 999px;
            background: #8a7047;
        }

        .ac-eu-resource-group a {
            color: #0f172a;
            text-decoration: underline;
            text-decoration-color: rgba(171, 141, 82, 0.34);
            text-underline-offset: 0.18em;
        }

        .ac-eu-resource-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1.2rem;
        }

        .ac-eu-law-summary {
            margin-top: 0.8rem;
        }

        .ac-eu-law-note {
            margin-top: 1rem;
            padding: 0.95rem 1rem;
            border-radius: 18px;
            background: rgba(249, 246, 240, 0.88);
            color: #6d5633;
        }

        .ac-eu-testimonials {
            padding-top: clamp(2.8rem, 4vw, 3.8rem);
            padding-bottom: clamp(2.8rem, 4vw, 3.8rem);
        }

        .ac-eu-page .ac-home-blog-card,
        .ac-eu-page .ac-home-blog-card-link,
        .ac-eu-page .ac-client-experience-card {
            border-color: rgba(171, 141, 82, 0.16);
        }

        .ac-eu-page .front-contact-input:focus,
        .ac-eu-page .front-contact-textarea:focus {
            box-shadow: none;
            outline: 2px solid rgba(171, 141, 82, 0.22);
            outline-offset: 0;
        }

        @media (min-width: 960px) {
            .ac-eu-intro-stage {
                min-height: 22rem;
            }

            .ac-eu-metrics-grid {
                grid-template-columns: minmax(0, 0.95fr) minmax(0, 1.05fr);
                gap: 3.25rem;
                align-items: start;
            }

            .ac-eu-panel--chart {
                padding-right: 0.35rem;
            }

            .ac-eu-panel--process {
                padding-left: 0.35rem;
            }

            .ac-eu-resource-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .ac-eu-law-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .ac-eu-section-head {
                flex-direction: row;
                align-items: flex-end;
            }
        }

        @media (max-width: 820px) {
            .ac-eu-page .ac-eu-section--intro.ac-blog-related-section {
                --ac-eu-corner-stars-size: 12.75rem;
                --ac-eu-corner-stars-offset-x: calc(var(--ac-eu-corner-stars-size) * -0.52);
                --ac-eu-corner-stars-offset-y: calc(var(--ac-eu-corner-stars-size) * -0.52);
            }

            .ac-eu-page .ac-eu-section--intro.ac-blog-related-section::before {
                opacity: 0.03;
            }

            .ac-eu-page .ac-eu-section--intro.ac-blog-related-section::after {
                opacity: 0.03;
            }

            .ac-eu-about-orbit {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 1.2rem 1.4rem;
            }

            .ac-eu-about-orbit::before {
                content: none;
            }

            .ac-eu-about-column {
                gap: 1.2rem;
            }

            .ac-eu-about-column:first-child,
            .ac-eu-about-column:last-child {
                padding-left: 0;
                padding-right: 0;
            }

        }

        @media (max-width: 639px) {
            .ac-eu-page .ac-eu-section {
                padding: 2.5rem 0;
            }

            .ac-eu-page .ac-eu-section--intro.ac-blog-related-section {
                --ac-eu-corner-stars-size: 11.05rem;
                --ac-eu-corner-stars-offset-x: calc(var(--ac-eu-corner-stars-size) * -0.52);
                --ac-eu-corner-stars-offset-y: calc(var(--ac-eu-corner-stars-size) * -0.52);
            }

            .ac-eu-page .ac-eu-section--intro.ac-blog-related-section::before {
                opacity: 0.022;
            }

            .ac-eu-page .ac-eu-section--intro.ac-blog-related-section::after {
                opacity: 0.022;
            }

            .ac-eu-panel,
            .ac-eu-call-card,
            .ac-eu-resource-card,
            .ac-eu-law-card {
                border-radius: 18px;
            }

            .ac-eu-about-orbit {
                grid-template-columns: minmax(0, 1fr);
                gap: 1rem;
            }

            .ac-eu-about-column {
                gap: 1rem;
            }

            .ac-eu-intro-stage {
                padding: 0;
            }

            .ac-eu-process-card {
                padding-left: 3.5rem;
            }

            .ac-eu-stat-row {
                padding: 0.9rem 0.9rem 0.95rem;
            }

            .ac-eu-section-head {
                align-items: stretch;
            }

            .ac-eu-download-button {
                width: 100%;
            }

            .ac-eu-call-list li a,
            .ac-eu-call-item-row {
                align-items: flex-start;
                flex-direction: column;
            }

            .ac-eu-call-item-meta {
                justify-content: flex-start;
            }
        }

    </style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.desktop.contact.partials.form-script', [
    'captchaEnabled' => $captchaEnabled,
    'captchaSiteKey' => $captchaSiteKey,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if (! $__env->hasRenderedOnce('74b3c08b-0807-4be2-98e6-ac77e098005e')): $__env->markAsRenderedOnce('74b3c08b-0807-4be2-98e6-ac77e098005e'); ?>
    <?php $__env->startPush('scripts'); ?>
        <script defer src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        (function () {
            const syncTestimonialToggles = function () {
                document.querySelectorAll('[data-eu-funds-testimonial-card]').forEach(function (card) {
                    const body = card.querySelector('[data-eu-funds-testimonial-body]');
                    const toggle = card.querySelector('[data-eu-funds-testimonial-toggle]');

                    if (!body || !toggle) {
                        return;
                    }

                    if (card.classList.contains('is-expanded')) {
                        toggle.hidden = false;
                        toggle.textContent = toggle.dataset.lessLabel || 'Show less';
                        toggle.setAttribute('aria-expanded', 'true');
                        return;
                    }

                    const hasOverflow = body.scrollHeight > body.clientHeight + 1;
                    toggle.hidden = !hasOverflow;
                    toggle.textContent = toggle.dataset.moreLabel || 'Read more';
                    toggle.setAttribute('aria-expanded', 'false');
                });
            };

            const syncCallExpanders = function () {
                document.querySelectorAll('[data-eu-call-more][data-expanded="true"]').forEach(function (content) {
                    content.style.maxHeight = content.scrollHeight + 'px';
                });
            };

            const initCallExpanders = function () {
                document.querySelectorAll('[data-eu-call-toggle]').forEach(function (button) {
                    if (button.dataset.callToggleReady === '1') {
                        return;
                    }

                    button.dataset.callToggleReady = '1';

                    const targetId = button.getAttribute('data-target');
                    const content = targetId ? document.getElementById(targetId) : null;
                    if (!content) {
                        return;
                    }

                    const labelNode = button.querySelector('span');
                    const moreLabel = button.dataset.labelMore || 'View all';
                    const lessLabel = button.dataset.labelLess || 'Show less';

                    const setExpanded = function (expanded) {
                        button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
                        content.dataset.expanded = expanded ? 'true' : 'false';

                        if (labelNode) {
                            labelNode.textContent = expanded ? lessLabel : moreLabel;
                        }
                    };

                    setExpanded(false);

                    button.addEventListener('click', function () {
                        const expanded = button.getAttribute('aria-expanded') === 'true';

                        if (expanded) {
                            content.style.maxHeight = content.scrollHeight + 'px';

                            window.requestAnimationFrame(function () {
                                setExpanded(false);
                                content.style.maxHeight = '0px';

                                const onCollapseEnd = function (event) {
                                    if (event.propertyName !== 'max-height') {
                                        return;
                                    }

                                    content.hidden = true;
                                    content.removeEventListener('transitionend', onCollapseEnd);
                                };

                                content.addEventListener('transitionend', onCollapseEnd);
                            });

                            return;
                        }

                        content.hidden = false;
                        content.style.maxHeight = '0px';
                        setExpanded(true);

                        window.requestAnimationFrame(function () {
                            content.style.maxHeight = content.scrollHeight + 'px';
                        });
                    });
                });
            };

            document.addEventListener('click', function (event) {
                const toggle = event.target.closest('[data-eu-funds-testimonial-toggle]');

                if (!toggle) {
                    return;
                }

                const card = toggle.closest('[data-eu-funds-testimonial-card]');

                if (!card) {
                    return;
                }

                const isExpanded = card.classList.toggle('is-expanded');
                toggle.textContent = isExpanded
                    ? (toggle.dataset.lessLabel || 'Show less')
                    : (toggle.dataset.moreLabel || 'Read more');
                toggle.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');

                window.requestAnimationFrame(syncTestimonialToggles);
            });

            let resizeFrame = null;
            window.addEventListener('resize', function () {
                if (resizeFrame !== null) {
                    window.cancelAnimationFrame(resizeFrame);
                }

                resizeFrame = window.requestAnimationFrame(function () {
                    resizeFrame = null;
                    syncTestimonialToggles();
                    syncCallExpanders();
                });
            });

            const init = function () {
                if (typeof window.Splide !== 'function') {
                    return false;
                }

                const mountSlider = function (selector, optionsFactory) {
                    document.querySelectorAll(selector).forEach(function (el) {
                        if (el.dataset.splideReady === '1') {
                            return;
                        }

                        el.dataset.splideReady = '1';
                        const count = el.querySelectorAll('.splide__slide').length;
                        const slider = new window.Splide(el, optionsFactory(count));
                        slider.mount();
                    });
                };

                mountSlider('[data-eu-funds-testimonials-splide]', function (count) {
                    return {
                        type: 'slide',
                        perPage: 2,
                        perMove: 1,
                        gap: '1rem',
                        arrows: count > 1,
                        pagination: count > 1,
                        rewind: count > 1,
                        breakpoints: {
                            900: { perPage: 1 },
                        },
                    };
                });

                mountSlider('[data-eu-funds-blog-splide]', function (count) {
                    return {
                        type: 'slide',
                        perPage: Math.min(3, Math.max(1, count)),
                        perMove: 1,
                        gap: '1.1rem',
                        arrows: count > 1,
                        pagination: count > 1,
                        rewind: count > 1,
                        breakpoints: {
                            1024: { perPage: Math.min(2, Math.max(1, count)) },
                            700: { perPage: 1 },
                        },
                    };
                });

                initCallExpanders();
                window.requestAnimationFrame(syncTestimonialToggles);
                window.requestAnimationFrame(syncCallExpanders);

                return true;
            };

            if (!init()) {
                let tries = 0;
                const interval = window.setInterval(function () {
                    tries += 1;

                    if (init() || tries > 30) {
                        window.clearInterval(interval);
                    }
                }, 200);
            }
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/eu-funds.blade.php ENDPATH**/ ?>