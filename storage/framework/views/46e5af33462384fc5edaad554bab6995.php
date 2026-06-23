<?php
    $content = (array) ($advisoryContent ?? []);
    $subpage = (array) ($subpage ?? []);
    $type = (string) ($subpage['type'] ?? 'financial');
    $heroSection = (array) ($heroSection ?? []);
    $pandea = (array) ($content['pandea'] ?? []);
    $funding = (array) ($content['funding'] ?? []);
    $sourceModules = (array) ($content['source_modules'] ?? []);
    $bankLoans = (array) ($content['bank_loans'] ?? []);
    $zopu = (array) ($content['zopu'] ?? []);
    $ma = (array) ($content['ma'] ?? []);
    $valuations = (array) ($content['valuations'] ?? []);
    $dueDiligence = (array) ($content['due_diligence'] ?? []);
    $tax = (array) ($content['tax'] ?? []);
    $meeting = (array) ($content['meeting'] ?? []);
    $detailSections = [
        'ma' => $ma,
        'due_diligence' => $dueDiligence,
        'valuations' => $valuations,
        'tax' => $tax,
        'bank_loans' => $bankLoans,
        'zopu' => $zopu,
    ];
    $detailKey = (string) ($subpage['detail_key'] ?? '');
    $detail = (array) ($detailSections[$detailKey] ?? []);
    $pandeaLogo = trim((string) ($pandeaLogoUrl ?? ''));
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
    $heroImageUrl = $sameOriginAssetUrl((string) $heroBackgroundUrl);
?>

<?php $__env->startSection('title', $servicePageMetaTitle !== '' ? $servicePageMetaTitle : ($servicePageTitle ?? 'Savjetovanje')); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startSection('content'); ?>
    <div class="ac-family-business-page ac-audit-page ac-advisory-page <?php echo e($detailKey === 'tax' ? 'ac-service-band-even' : ''); ?>">
        <section class="ac-family-hero ac-service-hero ac-service-hero--advisory">
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
                                    <span class="is-subtitle-lead"><?php echo e($heroSection['subtitle_lead'] ?? ($subpage['title'] ?? 'Savjetovanje')); ?></span>
                                </span>
                            </h1>
                            <p class="ac-family-hero-intro"><?php echo e($heroSection['intro'] ?? ''); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="ac-audit-editorial-wrap" aria-labelledby="ac-advisory-subpage-title">
            <div class="mx-auto w-full max-w-[1120px] px-5 lg:px-8">
                <div class="ac-audit-editorial-shell">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'funding'): ?>
                        <article class="ac-audit-editorial-section">
                            <div class="ac-audit-section-head ac-audit-section-head--center">
                                <p class="ac-family-section-kicker">PRIBAVLJANJE FINANCIRANJA</p>
                                <h2 id="ac-advisory-subpage-title"><?php echo e($funding['title'] ?? 'Pribavljanje financiranja'); ?></h2>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($funding['intro'] ?? '')) !== ''): ?>
                                    <p><?php echo e($funding['intro']); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="ac-advisory-three-grid">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = (array) ($funding['cards'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $cardUrl = $resolveContentUrl($card['url'] ?? ''); ?>
                                    <article class="ac-audit-service-card ac-advisory-link-card">
                                        <h3><?php echo e($card['title'] ?? ''); ?></h3>
                                        <p><?php echo e($card['text'] ?? ''); ?></p>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cardUrl !== ''): ?>
                                            <a href="<?php echo e($cardUrl); ?>" class="ac-advisory-card-link"><?php echo e(str_starts_with(strtolower((string) $locale), 'hr') ? 'Opširnije' : 'Read more'); ?></a>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </article>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                        </article>
                    <?php elseif($type === 'detail'): ?>
                        <article class="ac-audit-editorial-section ac-audit-editorial-section--overview">
                            <div class="ac-audit-section-head ac-audit-section-head--center">
                                <p class="ac-family-section-kicker"><?php echo e($detail['kicker'] ?? \Illuminate\Support\Str::upper((string) ($detail['title'] ?? $subpage['title'] ?? 'Savjetovanje'))); ?></p>
                                <h2 id="ac-advisory-subpage-title"><?php echo e($detail['overview_title'] ?? ($detail['title'] ?? ($subpage['title'] ?? 'Savjetovanje'))); ?></h2>
                            </div>

                            <div class="ac-audit-copy ac-audit-copy--full">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = (array) ($detail['overview_body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) $paragraph) !== ''): ?>
                                        <p><?php echo e($paragraph); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </article>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((bool) ($detail['show_pandea'] ?? false)): ?>
                            <article class="ac-audit-editorial-section">
                                <div class="ac-advisory-network-panel">
                                    <div class="ac-advisory-network-head">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pandeaLogo !== ''): ?>
                                            <div class="ac-advisory-network-logo-card">
                                                <img src="<?php echo e($pandeaLogo); ?>" alt="<?php echo e($pandea['logo_alt'] ?? 'Pandea Global M&A'); ?>" class="ac-advisory-network-logo" loading="lazy" decoding="async">
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <div>
                                            <p class="ac-family-section-kicker">Pandea Global M&amp;A</p>
                                            <h2><?php echo e($pandea['title'] ?? ''); ?></h2>
                                        </div>
                                    </div>
                                    <div class="ac-advisory-network-copy">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = (array) ($pandea['body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <p><?php echo e($paragraph); ?></p>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <article class="ac-audit-editorial-section">
                            <div class="ac-audit-section-head ac-audit-section-head--center">
                                <p class="ac-family-section-kicker">USLUGE</p>
                                <h2><?php echo e($detail['services_title'] ?? 'Naše usluge'); ?></h2>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = (array) ($detail['services_body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) $paragraph) !== ''): ?>
                                        <p><?php echo e($paragraph); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($detail['help_items'] ?? [])): ?>
                                <div class="ac-audit-section-head ac-audit-section-head--center ac-advisory-subhead">
                                    <h3><?php echo e($detail['help_title'] ?? 'U okviru usluge pomažemo u:'); ?></h3>
                                </div>
                                <div class="ac-advisory-check-grid">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = (array) ($detail['help_items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="ac-advisory-check-pill">
                                            <span class="ac-advisory-check-mark" aria-hidden="true">&#10003;</span>
                                            <span><?php echo e($item); ?></span>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </article>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($detail['approach_title'] ?? '')) !== '' || ! empty($detail['approach_body'] ?? [])): ?>
                            <article class="ac-audit-editorial-section">
                                <div class="ac-audit-section-head ac-audit-section-head--center">
                                    <p class="ac-family-section-kicker">PRISTUP</p>
                                    <h2><?php echo e($detail['approach_title'] ?? 'Naš pristup'); ?></h2>
                                </div>

                                <blockquote class="ac-audit-copy ac-audit-copy--full ac-audit-approach-copy">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = (array) ($detail['approach_body'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) $paragraph) !== ''): ?>
                                            <p><?php echo e($paragraph); ?></p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </blockquote>
                            </article>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <article class="ac-audit-editorial-section">
                            <div class="ac-audit-section-head ac-audit-section-head--center">
                                <p class="ac-family-section-kicker">SAVJETOVANJE</p>
                                <h2 id="ac-advisory-subpage-title"><?php echo e($subpage['title'] ?? 'Savjetovanje'); ?></h2>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(trim((string) ($subpage['intro'] ?? '')) !== ''): ?>
                                    <p><?php echo e($subpage['intro']); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

        <section class="ac-service-cta-section" aria-labelledby="ac-advisory-subpage-meeting-title">
            <div class="ac-service-cta-container">
                <div class="ac-service-cta-card">
                    <div class="ac-service-cta-copy">
                        <p class="ac-family-section-kicker"><?php echo e($meeting['kicker'] ?? 'KONTAKT'); ?></p>
                        <h2 id="ac-advisory-subpage-meeting-title"><?php echo e($meeting['title'] ?? 'Razgovarajmo o poslovnom savjetovanju'); ?></h2>
                        <p><?php echo e($meeting['intro'] ?? ''); ?></p>
                    </div>
                    <a href="<?php echo e(route('contact.create')); ?>" class="ac-service-cta-link">
                        <span><?php echo e($meeting['contact_title'] ?? 'Kontaktirajte nas'); ?></span>
                    </a>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/advisory-subpage.blade.php ENDPATH**/ ?>