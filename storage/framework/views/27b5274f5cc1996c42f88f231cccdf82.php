<?php
    $overviewBody = array_values((array) ($overviewSection['body'] ?? []));
    $serviceItems = array_values((array) ($processSection['items'] ?? []));
    $approachBody = array_values((array) ($approachSection['body'] ?? []));
    $sourceCards = array_values((array) ($sourceModulesSection['items'] ?? []));
    $callGroups = array_values((array) ($callsSection['groups'] ?? []));
    $resourceCards = array_values((array) ($resourcesSection['cards'] ?? []));
    $lawCards = array_values((array) ($lawsSection['cards'] ?? []));
    $meetingTitle = trim((string) ($meetingSection['title'] ?? '')) ?: 'Razgovarajmo o vašem projektu';
    $meetingIntro = trim((string) ($meetingSection['intro'] ?? '')) ?: 'Javite nam se i zajedno ćemo procijeniti koji su izvori financiranja dostupni za vaš projekt.';
    $meetingLinkLabel = trim((string) ($meetingSection['contact_title'] ?? '')) ?: 'Kontaktirajte nas';
    $isCroatianLocale = str_starts_with(strtolower((string) ($locale ?? app()->getLocale())), 'hr');
    $readMoreLabel = $isCroatianLocale ? 'Opširnije' : 'Read more';
    $currentHost = request()->getHost();
    $sameOriginAssetUrl = static function (?string $url) use ($currentHost): string {
        $assetUrl = trim((string) $url);
        $assetHost = parse_url($assetUrl, PHP_URL_HOST);

        if ($assetUrl === '' || ($assetHost !== null && $assetHost !== $currentHost)) {
            return $assetUrl;
        }

        $assetPath = parse_url($assetUrl, PHP_URL_PATH);
        $assetQuery = parse_url($assetUrl, PHP_URL_QUERY);

        if (is_string($assetPath) && $assetPath !== '') {
            return $assetPath.($assetQuery ? '?'.$assetQuery : '');
        }

        return $assetUrl;
    };
    $resolveContentUrl = static function (?string $url): string {
        $target = trim((string) $url);

        if ($target === '' || str_starts_with($target, '#') || str_starts_with($target, 'http://') || str_starts_with($target, 'https://')) {
            return $target;
        }

        return url(str_starts_with($target, '/') ? $target : '/'.$target);
    };
    $heroImageUrl = $sameOriginAssetUrl((string) $heroBackgroundUrl);
    $hasEuFundsPosts = ($euFundsPosts ?? collect())->isNotEmpty();
    $hasServiceVideos = collect($serviceVideos ?? [])->isNotEmpty();
?>

<?php $__env->startSection('title', $servicePageMetaTitle !== '' ? $servicePageMetaTitle : ($servicePageTitle ?? 'EU fondovi')); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startSection('content'); ?>
    <div class="ac-family-business-page ac-audit-page ac-eu-service-page <?php echo e($lawCards !== [] ? 'ac-service-band-even' : ''); ?>">
        <section class="ac-family-hero ac-service-hero ac-service-hero--eu-funds">
            <div class="ac-family-hero-media" aria-hidden="true" style="--audit-hero-image: url('<?php echo e($heroImageUrl); ?>'); background-image: url('<?php echo e($heroImageUrl); ?>');">
                <img src="<?php echo e($heroImageUrl); ?>" alt="" class="ac-family-hero-media-image" loading="eager" decoding="async">
            </div>
            <div class="ac-family-hero-overlay"></div>

            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-family-hero-content">
                    <div class="ac-family-hero-shell">
                        <div class="ac-family-hero-copy ac-service-hero-card">
                            <h1 class="ac-family-hero-title">
                                <span class="is-brand"><?php echo e($heroSection['brand_title'] ?? 'ALPHA CAPITALIS'); ?></span>
                                <span class="is-subtitle">
                                    <span class="is-subtitle-lead"><?php echo e($heroSection['subtitle_lead'] ?? 'EU fondovi'); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($heroSection['subtitle_accent'] ?? '')) !== ''): ?>
                                        <span class="is-subtitle-accent"><?php echo e($heroSection['subtitle_accent']); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                            </h1>

                            <p class="ac-family-hero-intro"><?php echo e($heroSection['intro'] ?? ''); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="eu-funds-overview" class="ac-audit-editorial-wrap" aria-labelledby="ac-eu-overview-title">
            <div class="mx-auto w-full max-w-[1120px] px-5 lg:px-8">
                <div class="ac-audit-editorial-shell">
                    <article class="ac-audit-editorial-section ac-audit-editorial-section--overview">
                        <div class="ac-audit-section-head ac-audit-section-head--center">
                            <p class="ac-family-section-kicker"><?php echo e($overviewSection['kicker'] ?? 'EU FONDOVI'); ?></p>
                            <h2 id="ac-eu-overview-title"><?php echo e($overviewSection['title'] ?? 'Što su EU fondovi?'); ?></h2>
                        </div>

                        <div class="ac-audit-copy ac-audit-copy--full">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $overviewBody; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) $paragraph) !== ''): ?>
                                    <p><?php echo e($paragraph); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </article>

                    <article id="eu-funds-services" class="ac-audit-editorial-section">
                        <div class="ac-audit-section-head ac-audit-section-head--center">
                            <p class="ac-family-section-kicker"><?php echo e($processSection['kicker'] ?? 'USLUGE'); ?></p>
                            <h2><?php echo e($processSection['title'] ?? 'Naše usluge'); ?></h2>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($processSection['intro'] ?? '')) !== ''): ?>
                                <p><?php echo e($processSection['intro']); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="ac-audit-card-grid">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $serviceItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <article class="ac-audit-service-card">
                                    <h3><?php echo e($item['title'] ?? ''); ?></h3>
                                    <p><?php echo e($item['text'] ?? ''); ?></p>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </article>

                    <article id="eu-funds-approach" class="ac-audit-editorial-section">
                        <div class="ac-audit-section-head ac-audit-section-head--center">
                            <p class="ac-family-section-kicker"><?php echo e($approachSection['kicker'] ?? 'PRISTUP'); ?></p>
                            <h2><?php echo e($approachSection['title'] ?? 'Naš pristup'); ?></h2>
                        </div>

                        <blockquote class="ac-audit-copy ac-audit-copy--full ac-audit-approach-copy">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $approachBody; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) $paragraph) !== ''): ?>
                                    <p><?php echo e($paragraph); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </blockquote>
                    </article>

                    <article id="eu-funds-sources" class="ac-audit-editorial-section">
                        <div class="ac-audit-section-head ac-audit-section-head--center">
                            <p class="ac-family-section-kicker"><?php echo e($sourceModulesSection['kicker'] ?? 'IZVORI FINANCIRANJA'); ?></p>
                            <h2><?php echo e($sourceModulesSection['title'] ?? 'Dostupni izvori financiranja'); ?></h2>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($sourceModulesSection['intro'] ?? '')) !== ''): ?>
                                <p><?php echo e($sourceModulesSection['intro']); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="ac-advisory-module-grid">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sourceCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $moduleUrl = $resolveContentUrl($module['url'] ?? ''); ?>
                                <article class="ac-advisory-source-card ac-eu-source-card">
                                    <h3><?php echo e($module['title'] ?? ''); ?></h3>
                                    <p><?php echo e($module['text'] ?? ''); ?></p>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($moduleUrl !== ''): ?>
                                        <a href="<?php echo e($moduleUrl); ?>" class="ac-advisory-card-link"><?php echo e($readMoreLabel); ?></a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($callGroups !== []): ?>
                            <div id="eu-funds-calls" class="ac-eu-call-module" aria-labelledby="ac-eu-calls-title">
                                <div class="ac-audit-section-head ac-audit-section-head--center ac-advisory-subhead">
                                    <p class="ac-family-section-kicker"><?php echo e($callsSection['kicker'] ?? 'NATJEČAJI'); ?></p>
                                    <h3 id="ac-eu-calls-title"><?php echo e($callsSection['title'] ?? 'Natječaji prema statusu'); ?></h3>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($callsSection['intro'] ?? '')) !== ''): ?>
                                        <p><?php echo e($callsSection['intro']); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <div class="ac-eu-call-group-grid">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $callGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $tone = trim((string) ($group['tone'] ?? 'pending')) ?: 'pending';
                                            $items = array_values((array) ($group['items'] ?? []));
                                            $visibleItems = array_slice($items, 0, 5);
                                            $hiddenItems = array_slice($items, 5);
                                            $statusLabel = trim((string) ($group['status_label'] ?? '')) ?: match ($tone) {
                                                'open' => 'Otvoreno',
                                                'closed' => 'Zatvoreno',
                                                default => 'U najavi',
                                            };
                                        ?>
                                        <article id="eu-funds-calls-<?php echo e($tone); ?>" class="ac-eu-call-group-card is-<?php echo e($tone); ?>">
                                            <div class="ac-eu-call-group-head">
                                                <h3><?php echo e($group['title'] ?? $statusLabel); ?></h3>
                                                <span class="ac-eu-status-badge is-<?php echo e($tone); ?>"><?php echo e($statusLabel); ?></span>
                                            </div>

                                            <ul class="ac-eu-call-list">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $visibleItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php echo $__env->make('front.desktop.pages.partials.eu-funds-call-item', ['item' => $item], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </ul>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hiddenItems !== []): ?>
                                                <details class="ac-eu-call-details">
                                                    <summary><?php echo e($isCroatianLocale ? 'Pogledaj sve natječaje' : 'View all calls'); ?></summary>
                                                    <ul class="ac-eu-call-list ac-eu-call-list--details">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $hiddenItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php echo $__env->make('front.desktop.pages.partials.eu-funds-call-item', ['item' => $item], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </ul>
                                                </details>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </article>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </article>

                    <article id="eu-funds-programs" class="ac-audit-editorial-section">
                        <div class="ac-audit-section-head ac-audit-section-head--center">
                            <p class="ac-family-section-kicker"><?php echo e($resourcesSection['kicker'] ?? 'PROGRAMI I INSTRUMENTI'); ?></p>
                            <h2><?php echo e($resourcesSection['title'] ?? 'HBOR, HAMAG i ostali izvori potpore'); ?></h2>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($resourcesSection['intro'] ?? '')) !== ''): ?>
                                <p><?php echo e($resourcesSection['intro']); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="ac-eu-program-grid">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $resourceCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <article class="ac-advisory-text-panel ac-eu-program-card">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($card['eyebrow'] ?? '')) !== ''): ?>
                                        <p class="ac-family-section-kicker"><?php echo e($card['eyebrow']); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <h2><?php echo e($card['title'] ?? ''); ?></h2>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = (array) ($card['body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) $paragraph) !== ''): ?>
                                            <p><?php echo e($paragraph); ?></p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = (array) ($card['groups'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="ac-eu-program-list-block">
                                            <h3><?php echo e($group['label'] ?? ''); ?></h3>
                                            <ul class="ac-advisory-list">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = (array) ($group['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        $resolvedLink = $item['resolved_link'] ?? ['url' => ''];
                                                        $itemUrl = trim((string) ($resolvedLink['url'] ?? ''));
                                                    ?>
                                                    <li>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($itemUrl !== ''): ?>
                                                            <a href="<?php echo e($itemUrl); ?>" <?php if($resolvedLink['open_in_new_tab'] ?? false): ?> target="_blank" rel="<?php echo e($resolvedLink['rel'] ?? 'noopener noreferrer'); ?>" <?php endif; ?>><?php echo e($item['title'] ?? ''); ?></a>
                                                        <?php else: ?>
                                                            <?php echo e($item['title'] ?? ''); ?>

                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </ul>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($card['primary_link']['url'] ?? '') || !empty($card['secondary_link']['url'] ?? '')): ?>
                                        <div class="ac-eu-program-actions">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($card['primary_link']['url'] ?? '')): ?>
                                                <a
                                                    href="<?php echo e($card['primary_link']['url']); ?>"
                                                    class="ac-advisory-card-link"
                                                    <?php if($card['primary_link']['open_in_new_tab'] ?? false): ?> target="_blank" rel="<?php echo e($card['primary_link']['rel'] ?? 'noopener noreferrer'); ?>" <?php endif; ?>
                                                ><?php echo e($card['primary_link']['label'] ?: $readMoreLabel); ?></a>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($card['secondary_link']['url'] ?? '')): ?>
                                                <a
                                                    href="<?php echo e($card['secondary_link']['url']); ?>"
                                                    class="ac-advisory-card-link"
                                                    <?php if($card['secondary_link']['open_in_new_tab'] ?? false): ?> target="_blank" rel="<?php echo e($card['secondary_link']['rel'] ?? 'noopener noreferrer'); ?>" <?php endif; ?>
                                                ><?php echo e($card['secondary_link']['label'] ?: $readMoreLabel); ?></a>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </article>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lawCards !== []): ?>
                        <article id="eu-funds-laws" class="ac-audit-editorial-section">
                            <div class="ac-audit-section-head ac-audit-section-head--center">
                                <p class="ac-family-section-kicker"><?php echo e($lawsSection['kicker'] ?? 'ZAKONI I UREDBE'); ?></p>
                                <h2><?php echo e($lawsSection['title'] ?? 'Porezne olakšice, zakoni i uredbe'); ?></h2>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($lawsSection['intro'] ?? '')) !== ''): ?>
                                    <p><?php echo e($lawsSection['intro']); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="ac-eu-program-grid">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $lawCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <article class="ac-advisory-text-panel ac-eu-program-card">
                                        <h2><?php echo e($card['title'] ?? ''); ?></h2>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($card['summary'] ?? '')) !== ''): ?>
                                            <p><?php echo e($card['summary']); ?></p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = (array) ($card['lists'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="ac-eu-program-list-block">
                                                <h3><?php echo e($list['label'] ?? ''); ?></h3>
                                                <ul class="ac-advisory-list">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = (array) ($list['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <li><?php echo e($item); ?></li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </ul>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($card['note'] ?? '')) !== ''): ?>
                                            <p class="ac-eu-program-note"><?php echo e($card['note']); ?></p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($card['primary_link']['url'] ?? '') || !empty($card['secondary_link']['url'] ?? '')): ?>
                                            <div class="ac-eu-program-actions">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($card['primary_link']['url'] ?? '')): ?>
                                                    <a
                                                        href="<?php echo e($card['primary_link']['url']); ?>"
                                                        class="ac-advisory-card-link"
                                                        <?php if($card['primary_link']['open_in_new_tab'] ?? false): ?> target="_blank" rel="<?php echo e($card['primary_link']['rel'] ?? 'noopener noreferrer'); ?>" <?php endif; ?>
                                                    ><?php echo e($card['primary_link']['label'] ?: $readMoreLabel); ?></a>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($card['secondary_link']['url'] ?? '')): ?>
                                                    <a
                                                        href="<?php echo e($card['secondary_link']['url']); ?>"
                                                        class="ac-advisory-card-link"
                                                        <?php if($card['secondary_link']['open_in_new_tab'] ?? false): ?> target="_blank" rel="<?php echo e($card['secondary_link']['rel'] ?? 'noopener noreferrer'); ?>" <?php endif; ?>
                                                    ><?php echo e($card['secondary_link']['label'] ?: $readMoreLabel); ?></a>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </article>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </article>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <?php echo $__env->make('front.desktop.partials.service-videos', [
            'serviceVideoSection' => $serviceVideoSection ?? [],
            'serviceVideos' => $serviceVideos ?? [],
            'locale' => $locale ?? app()->getLocale(),
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <section id="eu-funds-cta" class="ac-service-cta-section" aria-labelledby="ac-eu-meeting-title">
            <div class="ac-service-cta-container">
                <div class="ac-service-cta-card">
                    <div class="ac-service-cta-copy">
                        <h2 id="ac-eu-meeting-title"><?php echo e($meetingTitle); ?></h2>
                        <p><?php echo e($meetingIntro); ?></p>
                    </div>

                    <a href="<?php echo e(route('contact.create')); ?>" class="ac-service-cta-link">
                        <span><?php echo e($meetingLinkLabel); ?></span>
                    </a>
                </div>
            </div>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasEuFundsPosts): ?>
            <section class="ac-support-story ac-home-blog ac-blog-related-section ac-family-blog-section ac-audit-blog-section ac-eu-blog-section" aria-labelledby="ac-eu-blog-title">
                <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                    <div class="ac-support-story-hero">
                        <div class="ac-support-story-shell">
                            <div class="ac-services-head ac-support-story-head">
                                <p class="ac-family-section-kicker"><?php echo e($isCroatianLocale ? 'NAJNOVIJE OBJAVE' : 'LATEST POSTS'); ?></p>
                                <h2 id="ac-eu-blog-title">
                                    <span><?php echo e($blogSection['title'] ?? ''); ?></span>
                                </h2>
                                <p class="ac-services-intro"><?php echo e($blogSection['intro'] ?? ''); ?></p>
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
                                            $postImageSource = $postImage
                                                ? ($postImage->hasGeneratedConversion('card_360x240') ? $postImage->getUrl('card_360x240') : $postImage->getUrl())
                                                : '';
                                            $postImageUrl = $sameOriginAssetUrl($postImageSource);
                                            $primaryCategory = $post->categories
                                                ->sortByDesc(fn ($category) => (int) ($category->pivot->is_primary ?? false))
                                                ->first();
                                            $categoryTranslation = $primaryCategory?->translations->firstWhere('locale', $locale)
                                                ?? $primaryCategory?->translations->firstWhere('locale', $fallbackLocale);
                                            $categoryLabel = trim((string) ($categoryTranslation?->name ?? 'Novosti'));
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
                                                                width="360"
                                                                height="240"
                                                                sizes="(min-width: 1180px) 384px, (min-width: 760px) 50vw, 100vw"
                                                                loading="eager"
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
                </div>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasEuFundsPosts || $hasServiceVideos): ?>
    <?php if (! $__env->hasRenderedOnce('87e3b30d-28f5-4f08-beb0-fd2d76210ee5')): $__env->markAsRenderedOnce('87e3b30d-28f5-4f08-beb0-fd2d76210ee5'); ?>
        <?php $__env->startPush('styles'); ?>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
        <?php $__env->stopPush(); ?>
    <?php endif; ?>

    <?php if (! $__env->hasRenderedOnce('f13fee51-0b24-4e55-8174-84d4d21567fc')): $__env->markAsRenderedOnce('f13fee51-0b24-4e55-8174-84d4d21567fc'); ?>
        <?php $__env->startPush('scripts'); ?>
            <script defer src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
        <?php $__env->stopPush(); ?>
    <?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasEuFundsPosts): ?>
    <?php $__env->startPush('scripts'); ?>
        <script>
            (function () {
                const initEuFundsBlogSlider = function () {
                    if (typeof window.Splide !== 'function') {
                        return false;
                    }

                    document.querySelectorAll('[data-eu-funds-blog-splide]').forEach(function (el) {
                        if (el.dataset.splideReady === '1') {
                            return;
                        }

                        el.dataset.splideReady = '1';

                        const count = el.querySelectorAll('.splide__slide').length;
                        const slider = new window.Splide(el, {
                            type: 'slide',
                            perPage: Math.min(3, Math.max(1, count)),
                            perMove: 1,
                            gap: '1.25rem',
                            drag: count > 1,
                            snap: true,
                            rewind: count > 1,
                            pagination: count > 1,
                            arrows: count > 1,
                            updateOnMove: true,
                            speed: 520,
                            breakpoints: {
                                1180: { perPage: Math.min(2, Math.max(1, count)) },
                                760: { perPage: 1, gap: '1rem' },
                            },
                        });

                        slider.mount();
                    });

                    return true;
                };

                if (initEuFundsBlogSlider()) {
                    return;
                }

                let attempts = 0;
                const timer = window.setInterval(function () {
                    attempts += 1;
                    if (initEuFundsBlogSlider() || attempts > 40) {
                        window.clearInterval(timer);
                    }
                }, 120);
            }());
        </script>
    <?php $__env->stopPush(); ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/eu-funds.blade.php ENDPATH**/ ?>