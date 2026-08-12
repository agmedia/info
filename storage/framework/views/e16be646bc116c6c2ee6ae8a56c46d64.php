<?php
    $alphaCurrentUrl = rtrim(url()->current(), '/');
    $alphaNavigation = collect($mainNavigation ?? [])
        ->filter(static fn ($item): bool => is_array($item)
            && trim((string) ($item['label'] ?? '')) !== ''
            && trim((string) ($item['url'] ?? '')) !== '')
        ->map(static function (array $item) use ($alphaCurrentUrl): array {
            $href = trim((string) $item['url']);
            $normalizedHref = rtrim(url($href), '/');

            return array_merge($item, [
                'url' => $href,
                'active' => $normalizedHref !== '' && $normalizedHref === $alphaCurrentUrl,
            ]);
        })
        ->values();
    $alphaOfferUrl = route('assessment.create');
    $alphaShowLeaseCalculator = request()->routeIs('accounting.show');
    $alphaPrimaryCtaUrl = $alphaShowLeaseCalculator ? route('lease-calculator.show') : $alphaOfferUrl;
    $alphaPrimaryCtaLabel = $alphaShowLeaseCalculator ? 'MSFI 16 Kalkulator' : 'ZATRAŽI PONUDU';
?>

<header class="site-header" data-front-sticky-header data-alpha-header>
    <div class="header-inner">
        <a class="brand" href="<?php echo e(route('home')); ?>" aria-label="Alpha Capitalis — početna">
            <img src="<?php echo e(asset('alpha/logo.svg')); ?>" alt="Alpha Capitalis" width="300" height="80">
        </a>

        <nav class="desktop-nav" aria-label="Glavna navigacija">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $alphaNavigation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a
                    href="<?php echo e($item['url']); ?>"
                    class="<?php echo \Illuminate\Support\Arr::toCssClasses(['is-active' => $item['active']]); ?>"
                    <?php if(! empty($item['open_in_new_tab'])): ?> target="_blank" rel="noopener noreferrer" <?php endif; ?>
                >
                    <span class="nav-label" data-label="<?php echo e($item['label']); ?>"><?php echo e($item['label']); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </nav>

        <div class="header-actions">
            <a class="<?php echo \Illuminate\Support\Arr::toCssClasses(['header-cta', 'header-cta--calculator' => $alphaShowLeaseCalculator]); ?>" href="<?php echo e($alphaPrimaryCtaUrl); ?>">
                <span><?php echo e($alphaPrimaryCtaLabel); ?></span>
            </a>
            <button class="search-link" type="button" aria-label="Pretraga" aria-expanded="false" data-header-search-toggle>
                <i class="fa-light fa-magnifying-glass" aria-hidden="true"></i>
            </button>
        </div>

        <button
            class="menu-toggle"
            type="button"
            aria-label="Otvori izbornik"
            aria-expanded="false"
            data-alpha-menu-toggle
        >
            <span></span>
            <span></span>
        </button>
    </div>

    <div class="mobile-menu" aria-hidden="true" data-alpha-mobile-menu>
        <nav aria-label="Mobilna navigacija">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $alphaNavigation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a
                    href="<?php echo e($item['url']); ?>"
                    class="<?php echo \Illuminate\Support\Arr::toCssClasses(['is-active' => $item['active']]); ?>"
                    <?php if(! empty($item['open_in_new_tab'])): ?> target="_blank" rel="noopener noreferrer" <?php endif; ?>
                >
                    <span><?php echo e($item['label']); ?></span>
                    <i class="fa-light fa-arrow-right-long" aria-hidden="true"></i>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </nav>
        <a class="<?php echo \Illuminate\Support\Arr::toCssClasses(['mobile-cta', 'mobile-cta--calculator' => $alphaShowLeaseCalculator]); ?>" href="<?php echo e($alphaPrimaryCtaUrl); ?>">
            <span><?php echo e($alphaPrimaryCtaLabel); ?></span>
        </a>
    </div>

    <div class="alpha-search-panel" data-header-search-panel>
        <form
            action="<?php echo e(route('search.index')); ?>"
            method="get"
            class="alpha-search-form"
            role="search"
            data-header-search-form
            data-search-suggest-endpoint="<?php echo e(route('search.suggest')); ?>"
            data-search-results-endpoint="<?php echo e(route('search.index')); ?>"
        >
            <div class="alpha-search-field">
                <label for="alpha-header-search-input" class="visually-hidden">Pretraga sadržaja</label>
                <i class="fa-light fa-magnifying-glass" aria-hidden="true"></i>
                <input
                    id="alpha-header-search-input"
                    type="search"
                    name="q"
                    value="<?php echo e(request('q')); ?>"
                    placeholder="<?php echo e(__('ui.search.input_placeholder')); ?>"
                    autocomplete="off"
                    spellcheck="false"
                    data-header-search-input
                >
                <div class="front-search-suggestions hidden" data-header-search-suggestions></div>
            </div>
            <button type="submit" class="alpha-search-submit"><?php echo e(__('ui.search.submit')); ?></button>
        </form>
    </div>
</header>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/partials/alpha-global-header.blade.php ENDPATH**/ ?>