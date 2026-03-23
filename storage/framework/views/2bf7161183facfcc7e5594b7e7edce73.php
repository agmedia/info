<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php echo $__env->make('front.partials.seo-meta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('front.partials.schema-markup', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('front.partials.analytics', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?php echo e(asset('front-theme/styles/rising-sun-font.css')); ?>">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($storeSettings['branding']['favicons']['ico_url'] ?? null)): ?>
        <link rel="icon" href="<?php echo e($storeSettings['branding']['favicons']['ico_url']); ?>" sizes="any">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($storeSettings['branding']['favicons']['32_url'] ?? null)): ?>
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo e($storeSettings['branding']['favicons']['32_url']); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($storeSettings['branding']['favicons']['16_url'] ?? null)): ?>
        <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e($storeSettings['branding']['favicons']['16_url']); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($storeSettings['branding']['favicons']['180_url'] ?? null)): ?>
        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e($storeSettings['branding']['favicons']['180_url']); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($storeSettings['branding']['favicons']['192_url'] ?? null)): ?>
        <link rel="icon" type="image/png" sizes="192x192" href="<?php echo e($storeSettings['branding']['favicons']['192_url']); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($storeSettings['branding']['favicons']['512_url'] ?? null)): ?>
        <link rel="icon" type="image/png" sizes="512x512" href="<?php echo e($storeSettings['branding']['favicons']['512_url']); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($storeSettings['branding']['favicons']['ico_url'] ?? null) && !empty($storeSettings['branding']['favicon_url'] ?? null)): ?>
        <link rel="icon" href="<?php echo e($storeSettings['branding']['favicon_url']); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <style>
        body.front-preload-pending {
            background: #030b17;
        }

        #front-initial-preloader {
            position: fixed;
            inset: 0;
            z-index: 120;
            pointer-events: none;
            opacity: 1;
            visibility: visible;
            transition: opacity 0.22s ease, visibility 0.22s ease;
            background:
                radial-gradient(120% 160% at 82% -44%, rgba(4, 86, 146, 0.28), transparent 58%),
                linear-gradient(90deg, #050607 0%, #07090c 30%, #07213a 58%, #0a3d64 100%);
        }

        #front-initial-preloader.is-hidden {
            opacity: 0;
            visibility: hidden;
        }
    </style>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->routeIs('home')): ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
        <script defer src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<?php
    $mainNavigation = app(\App\Services\Front\NavigationMenuService::class)->forLocale((string) app()->getLocale());
    $defaultLogoRelativePath = 'front-theme/images/branding/alpha-capitalis-logo.svg';
    $defaultLogoUrl = file_exists(public_path($defaultLogoRelativePath))
        ? asset($defaultLogoRelativePath)
        : null;
    $headerHeroBackdropRelativePath = 'front-theme/images/hero/alpha-finance-tech.svg';
    $headerHeroBackdropPath = public_path($headerHeroBackdropRelativePath);
    $headerHeroBackdropUrl = file_exists($headerHeroBackdropPath)
        ? asset($headerHeroBackdropRelativePath).'?v='.filemtime($headerHeroBackdropPath)
        : asset($headerHeroBackdropRelativePath);

    if (empty($mainNavigation)) {
        $homeUrl = route('home');
        $mainNavigation = [
            [
                'label' => __('ui.front.desktop.nav.about'),
                'url' => $homeUrl.'#o-nama',
                'children' => [
                    ['label' => 'ALPHA CAPITALIS Tim', 'url' => route('team.index')],
                    ['label' => 'Edukacija', 'url' => $homeUrl.'#edukacija', 'children' => [
                        ['label' => 'Akademija', 'url' => $homeUrl.'#edukacija-akademija'],
                        ['label' => 'Svijet financija', 'url' => route('glossary.index')],
                    ]],
                    ['label' => 'EU projekti', 'url' => route('pages.show', ['slug' => 'eu-projekti'])],
                    ['label' => 'Karijera', 'url' => route('pages.show', ['slug' => 'karijera'])],
                ],
                'open_in_new_tab' => false,
            ],
            [
                'label' => __('ui.front.desktop.nav.departments'),
                'url' => $homeUrl.'#odjeli',
                'children' => [
                    ['label' => __('ui.front.desktop.nav.finance'), 'url' => $homeUrl.'#odjel-financije'],
                    ['label' => __('ui.front.desktop.nav.accounting'), 'url' => $homeUrl.'#odjel-racunovodstvo'],
                    ['label' => __('ui.front.desktop.nav.audit'), 'url' => $homeUrl.'#odjel-revizija'],
                    ['label' => __('ui.front.desktop.nav.tax'), 'url' => $homeUrl.'#odjel-porezi'],
                    ['label' => 'EU fondovi', 'url' => $homeUrl.'#odjel-eu-fondovi'],
                    ['label' => 'Obiteljski biznis', 'url' => route('family-business.show')],
                ],
                'open_in_new_tab' => false,
            ],
            [
                'label' => __('ui.front.desktop.nav.tools'),
                'url' => $homeUrl.'#alati',
                'children' => [
                    ['label' => __('ui.front.desktop.nav.ifrs16_calculator'), 'url' => route('lease-calculator.show')],
                    ['label' => __('ui.front.desktop.nav.valuation_assessment'), 'url' => $homeUrl.'#procjena-vrijednosti'],
                    ['label' => __('ui.front.desktop.nav.chatbot'), 'url' => $homeUrl.'#chatbot'],
                ],
                'open_in_new_tab' => false,
            ],
            [
                'label' => __('ui.front.desktop.nav.insights'),
                'url' => route('blog.index'),
                'children' => [
                    ['label' => __('ui.front.desktop.nav.blog'), 'url' => route('blog.index')],
                    ['label' => __('ui.front.desktop.nav.case_studies'), 'url' => $homeUrl.'#studije-slucaja'],
                    ['label' => __('ui.front.desktop.nav.video'), 'url' => $homeUrl.'#video-sadrzaj'],
                ],
                'open_in_new_tab' => false,
            ],
            [
                'label' => __('ui.front.desktop.nav.contact'),
                'url' => route('contact.create'),
                'children' => [],
                'open_in_new_tab' => false,
            ],
        ];
    }
?>
<body class="front-desktop-shell front-preload-pending min-h-screen overflow-x-hidden antialiased" style="--front-header-hero-backdrop: url('<?php echo e($headerHeroBackdropUrl); ?>');">
    <div id="front-initial-preloader" aria-hidden="true"></div>
    <?php
        $activeLocale = (string) ($frontLocale ?? app()->getLocale());
        $availableLanguages = collect($frontLanguages ?? [])->filter(
            static fn (array $language): bool => (string) ($language['code'] ?? '') !== ''
        )->values();
        $headerPhoneRaw = trim((string) ($storeSettings['footer']['phone'] ?? ''));
        $headerEmailRaw = trim((string) ($storeSettings['footer']['email_support'] ?? ''));
        $headerAddressRaw = trim((string) ($storeSettings['footer']['address'] ?? ''));
        $headerPhone = $headerPhoneRaw !== '' ? $headerPhoneRaw : '+385 (1) 580 6656';
        $headerEmail = $headerEmailRaw !== '' ? $headerEmailRaw : 'info@alphacapitalis.com';
        $headerAddress = $headerAddressRaw !== '' ? $headerAddressRaw : 'Ulica R. F. Mihanovića 9, 10110 Zagreb, Sky Office';

        $homeUrl = route('home');
        $mainNavigation = [
            ['label' => 'Usluge', 'url' => $homeUrl.'#usluge', 'children' => [
                ['label' => 'Financije', 'url' => $homeUrl.'#odjel-financije'],
                ['label' => 'Računovodstvo', 'url' => $homeUrl.'#odjel-racunovodstvo'],
                ['label' => 'Revizija', 'url' => $homeUrl.'#odjel-revizija'],
                ['label' => 'Porezi', 'url' => $homeUrl.'#odjel-porezi'],
                ['label' => 'EU fondovi', 'url' => $homeUrl.'#odjel-eu-fondovi'],
                ['label' => 'Obiteljski biznis', 'url' => route('family-business.show')],
            ]],
            ['label' => 'O nama', 'url' => $homeUrl.'#o-nama', 'children' => [
                ['label' => 'ALPHA CAPITALIS Tim', 'url' => route('team.index')],
                ['label' => 'Edukacija', 'url' => $homeUrl.'#edukacija', 'children' => [
                    ['label' => 'Akademija', 'url' => $homeUrl.'#edukacija-akademija'],
                    ['label' => 'Svijet financija', 'url' => route('glossary.index')],
                ]],
                ['label' => 'EU projekti', 'url' => route('pages.show', ['slug' => 'eu-projekti'])],
                ['label' => 'Karijera', 'url' => route('pages.show', ['slug' => 'karijera'])],
            ]],
            ['label' => 'Blog', 'url' => route('blog.index'), 'children' => []],
            ['label' => 'Kontakt', 'url' => route('contact.create'), 'children' => []],
        ];
    ?>

    <div class="front-header-meta hidden lg:block">
        <div class="front-header-meta-inner flex w-full items-center justify-between gap-4 px-5 sm:px-8 xl:px-10">
            <div class="flex min-w-0 items-center gap-5">
                <a href="tel:<?php echo e(preg_replace('/\s+/', '', $headerPhone)); ?>" class="front-meta-link inline-flex items-center gap-2 text-xs">
                    <svg class="front-meta-icon h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2A19.8 19.8 0 0 1 11.2 19a19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.9.8 2.7a2 2 0 0 1-.5 2.1L8.1 9.8a16 16 0 0 0 6.1 6.1l1.3-1.3a2 2 0 0 1 2.1-.5c.9.4 1.8.6 2.8.8a2 2 0 0 1 1.6 2z"/></svg>
                    <span><?php echo e($headerPhone); ?></span>
                </a>
                <a href="mailto:<?php echo e($headerEmail); ?>" class="front-meta-link inline-flex items-center gap-2 text-xs">
                    <svg class="front-meta-icon h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 8l9 6 9-6"/></svg>
                    <span><?php echo e($headerEmail); ?></span>
                </a>
                <p class="front-meta-link inline-flex items-center gap-2 text-xs">
                    <svg class="front-meta-icon h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" aria-hidden="true"><path d="M12 21s7-5.4 7-11a7 7 0 1 0-14 0c0 5.6 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    <span class="truncate"><?php echo e($headerAddress); ?></span>
                </p>
            </div>
            <div class="front-lang-switch inline-flex items-center p-0.5 text-xs font-semibold uppercase tracking-[0.08em]">
                <a href="<?php echo e(route('front.locale.switch', ['code' => 'hr'])); ?>" class="front-meta-lang <?php echo e($activeLocale === 'hr' ? 'is-active' : ''); ?>" hreflang="hr">HR</a>
                <a href="<?php echo e(route('front.locale.switch', ['code' => 'en'])); ?>" class="front-meta-lang <?php echo e($activeLocale === 'en' ? 'is-active' : ''); ?>" hreflang="en">EN</a>
            </div>
        </div>
    </div>

<header class="front-site-header sticky top-0 z-40 border-b" data-front-sticky-header>
    <div class="front-header-main">
        <div class="front-header-row flex w-full items-center justify-between gap-2.5 sm:px-8 xl:px-10">
            <a href="<?php echo e(route('home')); ?>" class="front-logo inline-flex items-center text-2xl font-black sm:text-4xl">
                <?php
                    $headerLogoUrl = (string) ($storeSettings['branding']['logo_url'] ?? $defaultLogoUrl ?? '');
                    $stickyMarkRelativePath = 'front-theme/images/branding/znak-ac.svg';
                    $stickyMarkUrl = file_exists(public_path($stickyMarkRelativePath))
                        ? asset($stickyMarkRelativePath)
                        : $headerLogoUrl;
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($headerLogoUrl !== ''): ?>
                    <img src="<?php echo e($headerLogoUrl); ?>" alt="<?php echo e($storeSettings['branding']['store_name'] ?? config('app.name', 'AG Info')); ?>" class="front-logo-full h-[52px] w-auto object-contain sm:h-[56px] xl:h-[66px]">
                    <img src="<?php echo e($stickyMarkUrl); ?>" alt="" aria-hidden="true" class="front-logo-mark hidden h-10 w-auto object-contain sm:h-[50px]">
                <?php else: ?>
                    <?php echo e((string) ($storeSettings['branding']['store_name'] ?? config('app.name', 'AG Info'))); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </a>

            <nav class="front-nav relative hidden flex-1 items-center justify-center gap-7 px-4 text-sm font-semibold xl:flex">
                <?php echo $__env->make('front.desktop.partials.main-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </nav>

            <div class="front-header-actions hidden min-h-[84px] items-center gap-2.5 xl:flex">
                <a href="<?php echo e(route('assessment.create')); ?>" class="front-action-cta">
                    Procjena suradnje
                </a>
                <a href="<?php echo e(route('lease-calculator.show')); ?>" class="front-action-cta front-action-cta-secondary">
                    MSFI 16 Kalkulator
                </a>
                <span class="front-actions-separator" aria-hidden="true"></span>
                <button type="button" class="front-search-action inline-flex h-10 w-10 items-center justify-center transition" aria-label="Pretraga" data-header-search-toggle>
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="M20 20l-3.2-3.2"></path>
                    </svg>
                </button>
            </div>

            <div class="front-mobile-actions flex self-stretch items-center xl:hidden">
                <button type="button" class="front-top-action flex h-full items-center justify-center transition" aria-label="Pretraga" data-header-search-toggle>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="M20 20l-3.2-3.2"></path>
                    </svg>
                </button>
                <button type="button" class="front-top-action flex h-full items-center justify-center transition" aria-label="<?php echo e(__('ui.front.desktop.open_navigation')); ?>" data-mobile-menu-open>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 7h16M4 12h16M4 17h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="front-header-search-panel pointer-events-none max-h-0 overflow-hidden opacity-0 transition-all duration-300" data-header-search-panel>
        <div class="w-full px-5 py-3 sm:px-8 xl:px-10">
            <form action="<?php echo e(route('home')); ?>" method="get" class="front-header-search-form">
                <label for="front-header-search-input" class="sr-only">Pretraga sadržaja</label>
                <div class="front-search-field">
                    <span class="front-search-field-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="M20 20l-3.2-3.2"></path>
                        </svg>
                    </span>
                    <input
                        id="front-header-search-input"
                        type="search"
                        name="q"
                        value="<?php echo e(request('q')); ?>"
                        class="front-search-input"
                        placeholder="Naziv, usluga, članak..."
                        data-header-search-input
                    >
                </div>
                <button type="submit" class="front-search-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="M20 20l-3.2-3.2"></path>
                    </svg>
                    <span>Pretraga</span>
                </button>
            </form>
        </div>
    </div>
</header>

<div class="pointer-events-none fixed inset-0 z-[60] lg:hidden" data-mobile-menu-root>
    <button type="button" class="front-mobile-menu-backdrop absolute inset-0 opacity-0 transition-opacity duration-300" aria-label="<?php echo e(__('ui.front.desktop.close_navigation')); ?>" data-mobile-menu-close></button>
    <aside class="front-mobile-menu-panel absolute inset-0 flex w-full max-w-none -translate-x-full flex-col shadow-2xl transition-transform duration-300 ease-out" data-mobile-menu-panel>
        <div class="front-mobile-menu-head flex items-center justify-between border-b px-4 py-4">
            <?php
                $mobileHeaderLogoUrl = (string) ($storeSettings['branding']['logo_url'] ?? $defaultLogoUrl ?? '');
            ?>
            <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mobileHeaderLogoUrl !== ''): ?>
                    <img src="<?php echo e($mobileHeaderLogoUrl); ?>" alt="<?php echo e($storeSettings['branding']['store_name'] ?? config('app.name', 'AG Info')); ?>" class="h-12 w-auto object-contain">
                <?php else: ?>
                    <span class="text-xl font-black tracking-tight text-white"><?php echo e((string) ($storeSettings['branding']['store_name'] ?? 'AG Info')); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </a>
            <button type="button" class="inline-flex h-10 w-10 items-center justify-center border transition" aria-label="<?php echo e(__('ui.front.desktop.close_navigation')); ?>" data-mobile-menu-close>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18"></path>
                </svg>
            </button>
        </div>
        <?php echo $__env->make('front.desktop.partials.main-nav-mobile', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($availableLanguages->isNotEmpty()): ?>
            <div class="front-mobile-menu-locale mt-auto border-t px-4 py-4">
                <div class="front-mobile-lang-switch inline-flex items-center p-0.5 text-xs font-semibold uppercase tracking-[0.08em]">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $availableLanguages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $code = (string) ($language['code'] ?? '');
                        ?>
                        <a href="<?php echo e(route('front.locale.switch', ['code' => $code])); ?>" class="front-mobile-menu-locale-link <?php echo e($activeLocale === $code ? 'is-active' : ''); ?>" hreflang="<?php echo e($code); ?>">
                            <?php echo e(strtoupper($code)); ?>

                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </aside>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->routeIs('home')): ?>
    <?php
        $heroCityPhotoRelativePath = 'front-theme/images/image-from-rawpixel-id-6030607-original-2.jpg';
        $heroCityPhotoPath = public_path($heroCityPhotoRelativePath);
        $heroCityPhotoUrl = file_exists($heroCityPhotoPath)
            ? asset($heroCityPhotoRelativePath).'?v='.filemtime($heroCityPhotoPath)
            : asset($headerHeroBackdropRelativePath);
        $heroMarkRelativePath = 'front-theme/images/branding/znak-ac-w.svg';
        $heroMarkPath = public_path($heroMarkRelativePath);
        $heroMarkUrl = file_exists($heroMarkPath)
            ? asset($heroMarkRelativePath).'?v='.filemtime($heroMarkPath)
            : asset('front-theme/images/branding/alpha-capitalis-logo.svg');
    ?>
    <section id="video-sadrzaj" class="front-hero-video-section w-full border-b border-black/20 bg-black">
        <div class="front-hero-video-wrap relative w-full overflow-hidden">
            <div class="front-hero-image absolute inset-0"></div>
            <div class="front-hero-city-photo absolute inset-y-0 left-0" style="--front-hero-city-photo-url: url('<?php echo e($heroCityPhotoUrl); ?>');"></div>
            <img src="<?php echo e($heroMarkUrl); ?>" alt="" aria-hidden="true" class="front-hero-mark absolute">

            <div class="front-hero-video-overlay absolute inset-0"></div>

            <div class="front-hero-video-content absolute inset-0 flex items-center justify-center px-6 text-center">
                <div>
                    <h1 class="front-hero-video-title text-white">ALPHA CAPITALIS</h1>
                    <p class="front-hero-video-subtitle mt-5 text-white/90">VAŠ KOMPAS KROZ GENERACIJE</p>
                    <div class="front-hero-cta-row mt-8 flex flex-wrap items-center justify-center gap-3">
                        <a href="#usluge" class="front-hero-cta front-hero-cta-primary inline-flex items-center justify-center px-6 py-3 text-[11px] font-semibold uppercase tracking-[0.12em]">
                            Naše usluge
                        </a>
                        <a href="<?php echo e(route('contact.create')); ?>" class="front-hero-cta front-hero-cta-secondary inline-flex items-center justify-center px-6 py-3 text-[11px] font-semibold uppercase tracking-[0.12em]">
                            Ugovori sastanak
                        </a>
                    </div>
                </div>
            </div>

        </div>
        <div class="front-hero-stats-card relative z-10" data-home-hero-stats>
            <div class="grid w-full grid-cols-2 md:grid-cols-3">
                <article class="front-hero-stat-card px-6 py-8 text-center" data-home-hero-stat style="--front-hero-stat-delay: 0ms;">
                    <span class="front-hero-stat-icon mx-auto inline-flex h-11 w-11 items-center justify-center rounded-full" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8">
                            <path d="M4 19h16"/>
                            <rect x="6" y="11" width="2.8" height="6" rx="1"/>
                            <rect x="10.6" y="8" width="2.8" height="9" rx="1"/>
                            <rect x="15.2" y="5" width="2.8" height="12" rx="1"/>
                        </svg>
                    </span>
                    <div class="front-hero-stat-value-shell" data-home-hero-display data-home-hero-display-value="300+">
                        <p class="front-hero-stat-value" data-home-hero-count data-count-to="300" data-count-suffix="+">300</p>
                    </div>
                    <span class="front-hero-stat-accent" aria-hidden="true"></span>
                    <p class="front-hero-stat-label">Odrađenih projekata</p>
                </article>
                <article class="front-hero-stat-card px-6 py-8 text-center" data-home-hero-stat style="--front-hero-stat-delay: 320ms;">
                    <span class="front-hero-stat-icon mx-auto inline-flex h-11 w-11 items-center justify-center rounded-full" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/>
                            <circle cx="10" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </span>
                    <div class="front-hero-stat-value-shell" data-home-hero-display data-home-hero-display-value="600+">
                        <p class="front-hero-stat-value" data-home-hero-count data-count-to="600" data-count-suffix="+">600</p>
                    </div>
                    <span class="front-hero-stat-accent" aria-hidden="true"></span>
                    <p class="front-hero-stat-label">Redovnih klijenata</p>
                </article>
                <article class="front-hero-stat-card front-hero-stat-card--wide px-6 py-8 text-center" data-home-hero-stat style="--front-hero-stat-delay: 640ms;">
                    <span class="front-hero-stat-icon mx-auto inline-flex h-11 w-11 items-center justify-center rounded-full" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </span>
                    <div class="front-hero-stat-value-shell" data-home-hero-display data-home-hero-display-value="60+">
                        <p class="front-hero-stat-value" data-home-hero-count data-count-to="60" data-count-suffix="+">60</p>
                    </div>
                    <span class="front-hero-stat-accent" aria-hidden="true"></span>
                    <p class="front-hero-stat-label">Kvalificiranih stručnjaka</p>
                </article>
            </div>
        </div>
    </section>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<main <?php if(request()->routeIs('home')): ?> id="usluge" <?php endif; ?> class="front-content-shell <?php echo $__env->yieldContent('main_class', 'mx-auto w-full max-w-7xl px-6 py-10'); ?>">
    <?php echo $__env->make('front.desktop.partials.flash', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldContent('content'); ?>
</main>

<button type="button" class="front-footer-compass front-scroll-compass" data-scroll-top data-scroll-top-floating aria-label="Povratak na vrh">
    <img src="<?php echo e(asset('front-theme/images/icons/znak-zlatni.svg')); ?>" alt="" aria-hidden="true" class="front-footer-compass-mark">
</button>

<footer class="front-footer mt-0">
    <?php
        $footerCompanies = collect((array) ($storeSettings['official_entities'] ?? []))
            ->filter(static fn ($company): bool => is_array($company) && trim((string) ($company['name'] ?? '')) !== '')
            ->values()
            ->all();

        $footerIsoCertificates = collect([
            ['code' => 'ISO 9001:2015', 'title' => 'Sustav upravljanja kvalitetom', 'icon' => 'front-theme/images/certificates/iso-9001-sgs.png'],
            ['code' => 'ISO 14001:2015', 'title' => 'Sustav upravljanja okolišem', 'icon' => 'front-theme/images/certificates/iso-14001-sgs.png'],
            ['code' => 'ISO 45001:2018', 'title' => 'Sustav upravljanja zaštitom zdravlja i sigurnošću na radu', 'icon' => 'front-theme/images/certificates/iso-45001-sgs.png'],
        ])->take(1)->values();
    ?>

    <button type="button" class="front-footer-compass" data-scroll-top aria-label="Povratak na vrh">
        <img src="<?php echo e(asset('front-theme/images/icons/znak-zlatni.svg')); ?>" alt="" aria-hidden="true" class="front-footer-compass-mark">
    </button>

    <div class="mx-auto w-full max-w-[1320px] px-4 py-12 sm:px-6 lg:px-8">
        <div class="front-footer-newsletter">
            <div class="front-footer-newsletter-copy">
                <p class="front-kicker">Newsletter</p>
                <h2 class="front-footer-newsletter-title">Prijava na newsletter</h2>
                <p class="front-footer-muted mt-2 text-sm">Primajte novosti i praktične savjete iz financija, računovodstva i revizije.</p>
            </div>
            <form action="<?php echo e(route('contact.create')); ?>" method="get" class="front-footer-newsletter-form" aria-label="Prijava na newsletter">
                <div class="front-footer-newsletter-row">
                    <label for="footer-newsletter-email" class="sr-only">Email adresa</label>
                    <input id="footer-newsletter-email" type="email" name="newsletter_email" placeholder="Upišite email adresu" class="front-footer-newsletter-input" required>
                    <button type="submit" class="front-footer-newsletter-button">Prijavi me</button>
                </div>
                <label class="front-footer-newsletter-consent">
                    <input type="checkbox" name="newsletter_consent" value="1" required>
                    <span>Prihvaćam uvjete korištenja i obradu podataka za newsletter.</span>
                </label>
            </form>
        </div>

        <?php
            $footerSocialLinks = [
                [
                    'key' => 'x',
                    'label' => 'X',
                    'url' => '',
                ],
                [
                    'key' => 'facebook',
                    'label' => 'Facebook',
                    'url' => trim((string) ($storeSettings['branding']['social']['facebook']['url'] ?? '')),
                ],
                [
                    'key' => 'linkedin',
                    'label' => 'LinkedIn',
                    'url' => '',
                ],
                [
                    'key' => 'instagram',
                    'label' => 'Instagram',
                    'url' => trim((string) ($storeSettings['branding']['social']['instagram']['url'] ?? '')),
                ],
            ];
        ?>
        <div class="front-footer-social-band" aria-label="Društvene mreže">
            <div class="front-footer-social-copy">
                <p class="front-footer-social-kicker">Business Insights</p>
                <p class="front-footer-social-text">Stručni uvidi za bolje poslovne odluke.</p>
            </div>
            <div class="front-footer-social-links">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $footerSocialLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $url = $social['url'];
                        $isPlaceholder = $url === '';
                    ?>
                    <a
                        href="<?php echo e($isPlaceholder ? '#' : $url); ?>"
                        class="front-footer-social-link front-footer-social-link--<?php echo e($social['key']); ?> <?php echo e($isPlaceholder ? 'is-placeholder' : ''); ?>"
                        <?php if(!$isPlaceholder): ?> target="_blank" rel="noopener noreferrer" <?php endif; ?>
                        aria-label="<?php echo e($social['label']); ?>"
                    >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($social['key'] === 'x'): ?>
                            <svg viewBox="0 0 448 512" fill="currentColor" aria-hidden="true"><path d="M64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l320 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32zm297.1 84l-103.8 118.6 122.1 161.4-95.6 0-74.8-97.9-85.7 97.9-47.5 0 111-126.9-117.1-153.1 98 0 67.7 89.5 78.2-89.5 47.5 0zM323.3 367.6l-169.9-224.7-28.3 0 171.8 224.7 26.4 0z"/></svg>
                        <?php elseif($social['key'] === 'facebook'): ?>
                            <svg viewBox="0 0 448 512" fill="currentColor" aria-hidden="true"><path d="M64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l98.2 0 0-145.8-52.8 0 0-78.2 52.8 0 0-33.7c0-87.1 39.4-127.5 125-127.5 16.2 0 44.2 3.2 55.7 6.4l0 70.8c-6-.6-16.5-1-29.6-1-42 0-58.2 15.9-58.2 57.2l0 27.8 83.6 0-14.4 78.2-69.3 0 0 145.8 129 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32z"/></svg>
                        <?php elseif($social['key'] === 'linkedin'): ?>
                            <svg viewBox="0 0 448 512" fill="currentColor" aria-hidden="true"><path d="M64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l320 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32zm5 170.2l66.5 0 0 213.8-66.5 0 0-213.8zm71.7-67.7a38.5 38.5 0 1 1 -77 0 38.5 38.5 0 1 1 77 0zM317.9 416l0-104c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9l0 105.8-66.4 0 0-213.8 63.7 0 0 29.2 .9 0c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9l0 117.2-66.4 0z"/></svg>
                        <?php else: ?>
                            <svg viewBox="0 0 448 512" fill="currentColor" aria-hidden="true"><path d="M194.4 211.7a53.3 53.3 0 1 0 59.2 88.6 53.3 53.3 0 1 0 -59.2-88.6zm142.3-68.4c-5.2-5.2-11.5-9.3-18.4-12-18.1-7.1-57.6-6.8-83.1-6.5-4.1 0-7.9 .1-11.2 .1s-7.2 0-11.4-.1c-25.5-.3-64.8-.7-82.9 6.5-6.9 2.7-13.1 6.8-18.4 12s-9.3 11.5-12 18.4c-7.1 18.1-6.7 57.7-6.5 83.2 0 4.1 .1 7.9 .1 11.1s0 7-.1 11.1c-.2 25.5-.6 65.1 6.5 83.2 2.7 6.9 6.8 13.1 12 18.4s11.5 9.3 18.4 12c18.1 7.1 57.6 6.8 83.1 6.5 4.1 0 7.9-.1 11.2-.1s7.2 0 11.4 .1c25.5 .3 64.8 .7 82.9-6.5 6.9-2.7 13.1-6.8 18.4-12s9.3-11.5 12-18.4c7.2-18 6.8-57.4 6.5-83 0-4.2-.1-8.1-.1-11.4s0-7.1 .1-11.4c.3-25.5 .7-64.9-6.5-83-2.7-6.9-6.8-13.1-12-18.4l0 .2zm-67.1 44.5c18.1 12.1 30.6 30.9 34.9 52.2s-.2 43.5-12.3 61.6c-6 9-13.7 16.6-22.6 22.6s-19 10.1-29.6 12.2c-21.3 4.2-43.5-.2-61.6-12.3s-30.6-30.9-34.9-52.2 .2-43.5 12.2-61.6 30.9-30.6 52.2-34.9 43.5 .2 61.6 12.2l.1 0zm29.2-1.3c-3.1-2.1-5.6-5.1-7.1-8.6s-1.8-7.3-1.1-11.1 2.6-7.1 5.2-9.8 6.1-4.5 9.8-5.2 7.6-.4 11.1 1.1 6.5 3.9 8.6 7 3.2 6.8 3.2 10.6c0 2.5-.5 5-1.4 7.3s-2.4 4.4-4.1 6.2-3.9 3.2-6.2 4.2-4.8 1.5-7.3 1.5c-3.8 0-7.5-1.1-10.6-3.2l-.1 0zM448 96c0-35.3-28.7-64-64-64L64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l320 0c35.3 0 64-28.7 64-64l0-320zM357 389c-18.7 18.7-41.4 24.6-67 25.9-26.4 1.5-105.6 1.5-132 0-25.6-1.3-48.3-7.2-67-25.9s-24.6-41.4-25.8-67c-1.5-26.4-1.5-105.6 0-132 1.3-25.6 7.1-48.3 25.8-67s41.5-24.6 67-25.8c26.4-1.5 105.6-1.5 132 0 25.6 1.3 48.3 7.1 67 25.8s24.6 41.4 25.8 67c1.5 26.3 1.5 105.4 0 131.9-1.3 25.6-7.1 48.3-25.8 67l0 .1z"/></svg>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="front-footer-company-grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $footerCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="front-footer-company-card">
                    <h3 class="front-footer-company-name"><?php echo e($company['name']); ?></h3>
                    <div class="front-footer-company-body">
                        <p class="front-footer-company-line"><?php echo e($company['address'][0]); ?></p>
                        <p class="front-footer-company-line"><?php echo e($company['address'][1]); ?></p>
                        <p class="front-footer-company-line">OIB: <?php echo e($company['oib']); ?></p>
                        <p class="front-footer-company-line">MBS: <?php echo e($company['mbs']); ?></p>
                        <p class="front-footer-company-line">IBAN: <?php echo e($company['iban']); ?></p>
                        <p class="front-footer-company-line">T: <a href="tel:<?php echo e(preg_replace('/\s+/', '', $company['phone'])); ?>" class="front-footer-company-contact"><?php echo e($company['phone']); ?></a></p>
                        <p class="front-footer-company-line">E: <a href="mailto:<?php echo e($company['email']); ?>" class="front-footer-company-mail"><?php echo e($company['email']); ?></a></p>
                        <p class="front-footer-company-line mt-3">
                            <span class="front-footer-whatsapp" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8">
                                    <path d="M20 11.5a8.5 8.5 0 0 1-12.4 7.5L4 20l1.1-3.4A8.5 8.5 0 1 1 20 11.5z"/>
                                    <path d="M9.6 8.7c.2-.4.4-.4.7-.4h.6c.2 0 .4 0 .5.3.2.5.7 1.7.8 1.8.1.2.1.4 0 .5-.1.2-.2.3-.4.5l-.3.3c-.1.1-.2.3-.1.5.1.2.6 1 1.3 1.6.9.8 1.7 1.1 1.9 1.2.2.1.4 0 .5-.1l.6-.7c.2-.2.4-.2.6-.1l1.8.8c.3.1.5.2.5.4 0 .1 0 .8-.3 1.2-.3.4-1 .8-1.4.8-.4.1-.8.1-1.4 0-.4-.1-1-.3-1.8-.7-3.1-1.4-5-4.6-5.2-4.9-.2-.3-.9-1.2-.9-2.3 0-1.1.6-1.7.8-2z"/>
                                </svg>
                            </span>
                            WhatsApp
                        </p>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="front-footer-company-accordion">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $footerCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <details class="front-footer-company-dropdown">
                    <summary class="front-footer-company-dropdown-summary">
                        <span><?php echo e($company['name']); ?></span>
                        <span class="front-footer-company-dropdown-icon" aria-hidden="true"></span>
                    </summary>
                    <div class="front-footer-company-dropdown-body">
                        <p class="front-footer-company-line"><?php echo e($company['address'][0]); ?></p>
                        <p class="front-footer-company-line"><?php echo e($company['address'][1]); ?></p>
                        <p class="front-footer-company-line">OIB: <?php echo e($company['oib']); ?></p>
                        <p class="front-footer-company-line">MBS: <?php echo e($company['mbs']); ?></p>
                        <p class="front-footer-company-line">IBAN: <?php echo e($company['iban']); ?></p>
                        <p class="front-footer-company-line">T: <a href="tel:<?php echo e(preg_replace('/\s+/', '', $company['phone'])); ?>" class="front-footer-company-contact"><?php echo e($company['phone']); ?></a></p>
                        <p class="front-footer-company-line">E: <a href="mailto:<?php echo e($company['email']); ?>" class="front-footer-company-mail"><?php echo e($company['email']); ?></a></p>
                        <p class="front-footer-company-line mt-3">
                            <span class="front-footer-whatsapp" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8">
                                    <path d="M20 11.5a8.5 8.5 0 0 1-12.4 7.5L4 20l1.1-3.4A8.5 8.5 0 1 1 20 11.5z"/>
                                    <path d="M9.6 8.7c.2-.4.4-.4.7-.4h.6c.2 0 .4 0 .5.3.2.5.7 1.7.8 1.8.1.2.1.4 0 .5-.1.2-.2.3-.4.5l-.3.3c-.1.1-.2.3-.1.5.1.2.6 1 1.3 1.6.9.8 1.7 1.1 1.9 1.2.2.1.4 0 .5-.1l.6-.7c.2-.2.4-.2.6-.1l1.8.8c.3.1.5.2.5.4 0 .1 0 .8-.3 1.2-.3.4-1 .8-1.4.8-.4.1-.8.1-1.4 0-.4-.1-1-.3-1.8-.7-3.1-1.4-5-4.6-5.2-4.9-.2-.3-.9-1.2-.9-2.3 0-1.1.6-1.7.8-2z"/>
                                </svg>
                            </span>
                            WhatsApp
                        </p>
                    </div>
                </details>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="front-footer-iso-grid <?php echo e($footerIsoCertificates->count() === 1 ? 'is-single' : ''); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $footerIsoCertificates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $certificate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="front-footer-iso-item">
                    <span class="front-footer-iso-logo-wrap" aria-hidden="true">
                        <img
                            src="<?php echo e(asset((string) ($certificate['icon'] ?? ''))); ?>"
                            alt="<?php echo e($certificate['code']); ?> certifikat"
                            class="front-footer-iso-logo"
                            loading="lazy"
                            decoding="async"
                        >
                    </span>
                    <p><strong><?php echo e($certificate['code']); ?></strong> - <?php echo e($certificate['title']); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="front-footer-legal">
            <p>
                ALPHA CAPITALIS © Sva prava pridržana.
            </p>
            <p class="front-footer-legal-credit">
                Web by:
                <a href="https://www.agmedia.hr" target="_blank" rel="noopener noreferrer">AG media</a>
            </p>
        </div>
    </div>
</footer>
<script>
    (function () {
        var preloader = document.getElementById('front-initial-preloader');
        var hide = function () {
            document.body.classList.remove('front-preload-pending');

            if (!preloader) {
                return;
            }

            preloader.classList.add('is-hidden');
            window.setTimeout(function () {
                preloader.remove();
            }, 260);
        };

        if (document.readyState === 'complete') {
            hide();
            return;
        }

        window.addEventListener('load', hide, { once: true });
        window.setTimeout(hide, 1400);
    })();

    (function () {
        var prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var activeAnimationFrame = null;
        var getAutoDuration = function (startTop, finalTop, options) {
            if (options && typeof options.duration === 'number') {
                return options.duration;
            }

            var distance = Math.abs(finalTop - startTop);

            return Math.max(720, Math.min(980, 620 + (distance * 0.16)));
        };

        window.__frontAnimateScrollTo = function (targetTop, options) {
            var finalTop = Math.max(0, Math.round(targetTop));
            var onComplete = options && typeof options.onComplete === 'function' ? options.onComplete : null;
            var startTop = window.pageYOffset || document.documentElement.scrollTop || 0;
            var distance = finalTop - startTop;
            var duration = getAutoDuration(startTop, finalTop, options);

            if (typeof window.__frontLockWheelSmoothing === 'function') {
                window.__frontLockWheelSmoothing(duration, finalTop);
            }

            if (activeAnimationFrame) {
                window.cancelAnimationFrame(activeAnimationFrame);
                activeAnimationFrame = null;
            }

            if (prefersReducedMotion || duration <= 0 || Math.abs(distance) < 2) {
                window.scrollTo(0, finalTop);
                if (typeof window.__frontSyncWheelTarget === 'function') {
                    window.__frontSyncWheelTarget(finalTop);
                }
                if (onComplete) {
                    onComplete();
                }
                return;
            }

            var startTime = null;
            var easeSwing = function (progress) {
                return 0.5 - Math.cos(progress * Math.PI) / 2;
            };

            var step = function (currentTime) {
                if (startTime === null) {
                    startTime = currentTime;
                }

                var elapsed = currentTime - startTime;
                var progress = Math.min(elapsed / duration, 1);
                var nextTop = startTop + (distance * easeSwing(progress));

                window.scrollTo(0, nextTop);

                if (progress < 1) {
                    activeAnimationFrame = window.requestAnimationFrame(step);
                    return;
                }

                activeAnimationFrame = null;
                window.scrollTo(0, finalTop);
                if (typeof window.__frontSyncWheelTarget === 'function') {
                    window.__frontSyncWheelTarget(finalTop);
                }

                if (onComplete) {
                    onComplete();
                }
            };

            activeAnimationFrame = window.requestAnimationFrame(step);
        };
    })();

    (function () {
        var scrollTopButtons = Array.prototype.slice.call(document.querySelectorAll('[data-scroll-top]'));
        if (!scrollTopButtons.length) {
            return;
        }

        var footerButton = document.querySelector('.front-footer [data-scroll-top]');
        var floatingButton = document.querySelector('[data-scroll-top-floating]');
        var footer = footerButton ? footerButton.closest('.front-footer') : document.querySelector('.front-footer');
        var floatingVisible = null;
        var floatingSyncFrame = null;
        var syncCompassBackground = function () {
            if (!footer || !footerButton) {
                return;
            }

            var footerRect = footer.getBoundingClientRect();
            var compassRect = footerButton.getBoundingClientRect();
            var offsetX = -(compassRect.left - footerRect.left);

            footerButton.style.setProperty('--front-footer-compass-bg-pos', offsetX + 'px 0px');
            footerButton.style.setProperty('--front-footer-compass-bg-size', footerRect.width + 'px ' + footerRect.height + 'px');
        };

        var syncFloatingVisibility = function () {
            if (!floatingButton) {
                return;
            }

            var viewportHeight = window.innerHeight || document.documentElement.clientHeight || 0;
            var shouldShow = window.innerWidth > 900 && window.scrollY > Math.max(viewportHeight * 0.45, 420);

            if (floatingVisible === shouldShow) {
                return;
            }

            floatingVisible = shouldShow;
            floatingButton.classList.toggle('is-visible', shouldShow);
        };

        var requestFloatingVisibilitySync = function () {
            if (floatingSyncFrame !== null) {
                return;
            }

            floatingSyncFrame = window.requestAnimationFrame(function () {
                floatingSyncFrame = null;
                syncFloatingVisibility();
            });
        };

        syncCompassBackground();
        syncFloatingVisibility();
        window.addEventListener('resize', syncCompassBackground);
        window.addEventListener('resize', syncFloatingVisibility);
        window.addEventListener('orientationchange', syncCompassBackground);
        window.addEventListener('orientationchange', syncFloatingVisibility);
        window.addEventListener('scroll', requestFloatingVisibilitySync, { passive: true });
        window.setTimeout(syncCompassBackground, 120);
        window.setTimeout(syncFloatingVisibility, 120);

        scrollTopButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                if (typeof window.__frontAnimateScrollTo === 'function') {
                    window.__frontAnimateScrollTo(0);
                    return;
                }

                window.scrollTo(0, 0);
            });
        });
    })();

    (function () {
        var stickyHeader = document.querySelector('[data-front-sticky-header]');
        var getScrollOffset = function () {
            if (!stickyHeader) {
                return 2;
            }

            return Math.round(stickyHeader.getBoundingClientRect().height) + 2;
        };

        var getHashTarget = function (hash) {
            if (!hash || hash === '#') {
                return null;
            }

            var decodedHash = hash;
            try {
                decodedHash = decodeURIComponent(hash);
            } catch (error) {
                decodedHash = hash;
            }

            try {
                return document.querySelector(decodedHash);
            } catch (error) {
                return document.getElementById(decodedHash.replace(/^#/, ''));
            }
        };

        var scrollToHashTarget = function (hash, options) {
            var targetElement = getHashTarget(hash);
            if (!targetElement) {
                return false;
            }

            var targetTop = window.pageYOffset + targetElement.getBoundingClientRect().top - getScrollOffset();

            if (typeof window.__frontAnimateScrollTo === 'function') {
                window.__frontAnimateScrollTo(targetTop, options);
                return true;
            }

            window.scrollTo(0, Math.max(0, targetTop));

            return true;
        };

        document.addEventListener('click', function (event) {
            var link = event.target.closest('a[href*="#"]');
            if (!link || link.target === '_blank' || link.hasAttribute('download')) {
                return;
            }

            if (event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            var rawHref = link.getAttribute('href');
            if (!rawHref || rawHref === '#') {
                return;
            }

            var parsedUrl;
            try {
                parsedUrl = new URL(rawHref, window.location.href);
            } catch (error) {
                return;
            }

            if (!parsedUrl.hash || parsedUrl.origin !== window.location.origin || parsedUrl.pathname !== window.location.pathname) {
                return;
            }

            if (!getHashTarget(parsedUrl.hash)) {
                return;
            }

            event.preventDefault();
            scrollToHashTarget(parsedUrl.hash);

            if (window.history && typeof window.history.pushState === 'function') {
                window.history.pushState(null, '', parsedUrl.hash);
            }
        });

        var syncInitialHashScroll = function () {
            if (!window.location.hash || !getHashTarget(window.location.hash)) {
                return;
            }

            window.scrollTo(0, 0);
            window.requestAnimationFrame(function () {
                window.requestAnimationFrame(function () {
                    scrollToHashTarget(window.location.hash);
                });
            });
        };

        window.addEventListener('hashchange', function () {
            scrollToHashTarget(window.location.hash);
        });

        if (document.readyState === 'complete') {
            syncInitialHashScroll();
            return;
        }

        window.addEventListener('load', syncInitialHashScroll, { once: true });
    })();
</script>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->routeIs('home')): ?>
<script>
    (function () {
        var statsSection = document.querySelector('[data-home-hero-stats]');
        if (!statsSection) {
            return;
        }

        var items = Array.prototype.slice.call(statsSection.querySelectorAll('[data-home-hero-stat]'));
        if (!items.length) {
            return;
        }

        var prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var formatDisplayValue = function (value) {
            return Math.round(Number(value) || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        };

        var digitTrackMarkup = (function () {
            var trackDigits = '';

            for (var cycle = 0; cycle < 4; cycle += 1) {
                for (var digit = 0; digit <= 9; digit += 1) {
                    trackDigits += '<span>' + digit + '</span>';
                }
            }

            return trackDigits;
        })();

        var buildCounterMarkup = function (formattedValue) {
            return formattedValue.split('').map(function (character) {
                if (!/[0-9]/.test(character)) {
                    return '<span class="front-hero-formatting-mark">' + character + '</span>';
                }

                return '<span class="front-hero-digit"><span class="front-hero-digit-track" data-roll-target="' + character + '">' + digitTrackMarkup + '</span></span>';
            }).join('');
        };

        var prepareItem = function (item) {
            var valueElement = item.querySelector('[data-home-hero-count]');
            var valueShell = item.querySelector('[data-home-hero-display]');
            var targetValue = Number.parseInt(valueElement instanceof HTMLElement ? valueElement.dataset.countTo || '0' : '0', 10);
            var formattedValue = formatDisplayValue(targetValue);
            var suffix = valueElement instanceof HTMLElement ? valueElement.dataset.countSuffix || '' : '';

            if (valueShell instanceof HTMLElement) {
                valueShell.dataset.homeHeroDisplayValue = '0' + suffix;
            }

            if (valueElement instanceof HTMLElement) {
                valueElement.innerHTML = buildCounterMarkup(formattedValue);
            }
        };

        var animateValue = function (item, valueElement, valueShell, targetValue) {
            var duration = Math.max(2400, Math.min(3200, 2200 + (String(targetValue).length * 240)));
            var digitTracks = Array.prototype.slice.call(valueElement.querySelectorAll('.front-hero-digit-track'));

            item.classList.add('is-counting');

            digitTracks.forEach(function (track) {
                track.style.transitionDuration = duration + 'ms';
                track.style.transitionTimingFunction = 'cubic-bezier(0.16, 1, 0.3, 1)';
            });

            window.requestAnimationFrame(function () {
                window.requestAnimationFrame(function () {
                    digitTracks.forEach(function (track) {
                        var targetDigit = Number.parseInt(track.dataset.rollTarget || '0', 10);
                        var targetOffset = 20 + (Number.isNaN(targetDigit) ? 0 : targetDigit);

                        track.style.transform = 'translate3d(0, -' + targetOffset + 'em, 0)';
                    });
                });
            });

            window.setTimeout(function () {
                var suffix = valueElement instanceof HTMLElement ? valueElement.dataset.countSuffix || '' : '';

                if (valueShell instanceof HTMLElement) {
                    valueShell.dataset.homeHeroDisplayValue = formatDisplayValue(targetValue) + suffix;
                }

                item.classList.remove('is-counting');
                item.classList.add('is-counted');
            }, duration + 60);
        };

        var getItemDelay = function (item) {
            if (!(item instanceof HTMLElement)) {
                return 0;
            }

            var delayValue = window.getComputedStyle(item).getPropertyValue('--front-hero-stat-delay').trim();
            var parsedDelay = Number.parseFloat(delayValue);

            if (Number.isNaN(parsedDelay)) {
                return 0;
            }

            return delayValue.endsWith('s') && !delayValue.endsWith('ms')
                ? parsedDelay * 1000
                : parsedDelay;
        };

        var revealItem = function (item) {
            if (!(item instanceof HTMLElement) || item.dataset.heroStatAnimated === '1') {
                return;
            }

            item.dataset.heroStatAnimated = '1';
            window.setTimeout(function () {
                item.classList.add('is-revealed');

                var valueElement = item.querySelector('[data-home-hero-count]');
                var valueShell = item.querySelector('[data-home-hero-display]');
                var targetValue = Number.parseInt(valueElement instanceof HTMLElement ? valueElement.dataset.countTo || '0' : '0', 10);

                if (!(valueElement instanceof HTMLElement) || Number.isNaN(targetValue)) {
                    return;
                }

                if (prefersReducedMotion) {
                    var suffix = valueElement.dataset.countSuffix || '';

                    valueElement.textContent = formatDisplayValue(targetValue);

                    if (valueShell instanceof HTMLElement) {
                        valueShell.dataset.homeHeroDisplayValue = formatDisplayValue(targetValue) + suffix;
                    }

                    item.classList.add('is-counted');
                    return;
                }

                animateValue(item, valueElement, valueShell, targetValue);
            }, getItemDelay(item));
        };

        var revealAll = function () {
            items.forEach(function (item) {
                revealItem(item);
            });
        };

        items.forEach(function (item) {
            prepareItem(item);
        });

        statsSection.classList.add('is-enhanced');

        if (!('IntersectionObserver' in window)) {
            revealAll();
            return;
        }

        var observer = new IntersectionObserver(function (entries, currentObserver) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                    return;
                }

                revealAll();
                currentObserver.disconnect();
            });
        }, {
            threshold: 0.2,
            rootMargin: '0px 0px -8% 0px',
        });

        observer.observe(statsSection);
    })();
</script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/layouts/store.blade.php ENDPATH**/ ?>