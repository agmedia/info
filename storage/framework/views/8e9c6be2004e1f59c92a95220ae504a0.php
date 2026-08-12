<?php
    $alphaFooterPhone = trim((string) ($storeSettings['footer']['phone'] ?? '')) ?: '+385 (1) 580 6656';
    $alphaFooterEmail = trim((string) ($storeSettings['footer']['email_support'] ?? '')) ?: 'info@alphacapitalis.com';
    $alphaFooterAddress = 'Ulica R. F. Mihanovića 9, 10110 Zagreb';
    $alphaFooterMap = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($alphaFooterAddress);
    $alphaFooterHome = route('home');
    $alphaFooterNavigation = [
        ['label' => 'O nama', 'url' => route('pages.show', ['slug' => 'o-nama'])],
        ['label' => 'Karijera', 'url' => route('pages.show', ['slug' => 'karijera'])],
        ['label' => 'Objave', 'url' => route('blog.index')],
        ['label' => 'Kontakt', 'url' => route('contact.create')],
    ];
    $alphaFooterServices = [
        ['label' => 'Revizija', 'url' => route('audit.show')],
        ['label' => 'Računovodstvo', 'url' => route('accounting.show')],
        ['label' => 'Savjetovanje', 'url' => route('advisory.show')],
    ];
    $alphaFooterSocials = collect([
        ['label' => 'X', 'icon' => 'fa-x-twitter', 'url' => trim((string) ($storeSettings['branding']['social']['x']['url'] ?? '')), 'enabled' => (bool) ($storeSettings['branding']['social']['x']['enabled'] ?? true)],
        ['label' => 'Facebook', 'icon' => 'fa-facebook-f', 'url' => trim((string) ($storeSettings['branding']['social']['facebook']['url'] ?? '')), 'enabled' => (bool) ($storeSettings['branding']['social']['facebook']['enabled'] ?? true)],
        ['label' => 'LinkedIn', 'icon' => 'fa-linkedin-in', 'url' => trim((string) ($storeSettings['branding']['social']['linkedin']['url'] ?? '')), 'enabled' => (bool) ($storeSettings['branding']['social']['linkedin']['enabled'] ?? true)],
        ['label' => 'Instagram', 'icon' => 'fa-instagram', 'url' => trim((string) ($storeSettings['branding']['social']['instagram']['url'] ?? '')), 'enabled' => (bool) ($storeSettings['branding']['social']['instagram']['enabled'] ?? true)],
    ])->filter(fn (array $social): bool => $social['enabled'])->map(function (array $social) use ($alphaFooterHome): array {
        $social['url'] = $social['url'] !== '' ? $social['url'] : $alphaFooterHome;

        return $social;
    })->values();
    $alphaFooterLegalLinks = collect($storeSettings['footer']['bottom_links'] ?? [])->filter(
        static fn ($item): bool => is_array($item) && trim((string) ($item['label'] ?? '')) !== '' && trim((string) ($item['url'] ?? '')) !== ''
    )->values();

    if ($alphaFooterLegalLinks->isEmpty()) {
        $alphaFooterLegalLinks = collect([
            ['label' => 'Politika privatnosti', 'url' => route('pages.show', ['slug' => 'politika-privatnosti'])],
            ['label' => 'Uvjeti korištenja', 'url' => route('pages.show', ['slug' => 'uvjeti-koristenja'])],
        ]);
    }
?>

<footer class="site-footer" data-image-reveal>
    <div class="footer-shell">
        <section class="footer-newsletter" id="newsletter" aria-labelledby="footer-newsletter-title" data-image-reveal>
            <div class="footer-newsletter-copy">
                <span class="footer-label">Newsletter</span>
                <h2 id="footer-newsletter-title">
                    Primajte važne novosti na <span class="footer-newsletter-accent">vrijeme.</span>
                </h2>
            </div>
            <form action="<?php echo e(route('contact.create')); ?>" method="get">
                <label class="visually-hidden" for="newsletter-email">Vaša email adresa</label>
                <div class="footer-newsletter-field">
                    <i class="fa-light fa-envelope" aria-hidden="true"></i>
                    <input id="newsletter-email" name="newsletter_email" type="email" autocomplete="email" placeholder="Vaša email adresa" required>
                    <button type="submit" aria-label="Nastavite na prijavu za newsletter">
                        <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </div>
            </form>
        </section>

        <div class="footer-main" data-image-reveal>
            <div class="footer-brand-block content-reveal" data-image-reveal style="--reveal-index: 0">
                <a class="footer-brand" href="<?php echo e($alphaFooterHome); ?>" aria-label="Alpha Capitalis — početna">
                    <img src="<?php echo e(asset('alpha/logo.svg')); ?>" alt="Alpha Capitalis" width="300" height="80">
                </a>
                <p>Jedna adresa za sve brojke.</p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($alphaFooterSocials->isNotEmpty()): ?>
                    <div class="footer-socials" aria-label="Društvene mreže">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $alphaFooterSocials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e($social['url']); ?>" aria-label="<?php echo e($social['label']); ?>" title="<?php echo e($social['label']); ?>" target="_blank" rel="noopener noreferrer">
                                <i class="fa-brands <?php echo e($social['icon']); ?>" aria-hidden="true"></i>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="footer-desktop-only footer-nav-block content-reveal" data-image-reveal style="--reveal-index: 1">
                <span class="footer-label">Alpha Capitalis</span>
                <nav aria-label="Alpha Capitalis poveznice u podnožju">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $alphaFooterNavigation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($item['url']); ?>"><?php echo e($item['label']); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </nav>
            </div>

            <div class="footer-desktop-only footer-services-block content-reveal" data-image-reveal style="--reveal-index: 2">
                <span class="footer-label">Usluge</span>
                <nav aria-label="Usluge u podnožju">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $alphaFooterServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($item['url']); ?>"><?php echo e($item['label']); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </nav>
            </div>

            <div class="footer-desktop-only footer-contact-block content-reveal" data-image-reveal style="--reveal-index: 3">
                <span class="footer-label">Kontakt</span>
                <address class="footer-contact">
                    <a href="<?php echo e($alphaFooterMap); ?>" target="_blank" rel="noopener noreferrer"><?php echo e($alphaFooterAddress); ?></a>
                    <a href="tel:<?php echo e(preg_replace('/[^+0-9]/', '', $alphaFooterPhone)); ?>"><?php echo e($alphaFooterPhone); ?></a>
                    <a href="mailto:<?php echo e($alphaFooterEmail); ?>"><?php echo e($alphaFooterEmail); ?></a>
                </address>
            </div>

            <details class="footer-mobile-only footer-accordion footer-nav-block content-reveal" data-image-reveal style="--reveal-index: 1">
                <summary class="footer-label"><span>Alpha Capitalis</span><i class="fa-light fa-plus" aria-hidden="true"></i></summary>
                <nav class="footer-accordion-content" aria-label="Alpha Capitalis poveznice u podnožju">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $alphaFooterNavigation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($item['url']); ?>"><?php echo e($item['label']); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </nav>
            </details>

            <details class="footer-mobile-only footer-accordion footer-services-block content-reveal" data-image-reveal style="--reveal-index: 2">
                <summary class="footer-label"><span>Usluge</span><i class="fa-light fa-plus" aria-hidden="true"></i></summary>
                <nav class="footer-accordion-content" aria-label="Usluge u podnožju">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $alphaFooterServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($item['url']); ?>"><?php echo e($item['label']); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </nav>
            </details>

            <details class="footer-mobile-only footer-accordion footer-contact-block content-reveal" data-image-reveal style="--reveal-index: 3">
                <summary class="footer-label"><span>Kontakt</span><i class="fa-light fa-plus" aria-hidden="true"></i></summary>
                <address class="footer-contact footer-accordion-content">
                    <a href="<?php echo e($alphaFooterMap); ?>" target="_blank" rel="noopener noreferrer"><span><?php echo e($alphaFooterAddress); ?></span></a>
                    <a href="tel:<?php echo e(preg_replace('/[^+0-9]/', '', $alphaFooterPhone)); ?>"><span><?php echo e($alphaFooterPhone); ?></span></a>
                    <a href="mailto:<?php echo e($alphaFooterEmail); ?>"><span><?php echo e($alphaFooterEmail); ?></span></a>
                </address>
            </details>
        </div>

        <div class="footer-bottom content-reveal" data-image-reveal>
            <p>© <?php echo e(now()->year); ?> Alpha Capitalis d.o.o. Sva prava pridržana.</p>
            <div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $alphaFooterLegalLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($link['url']); ?>"><?php echo e($link['label']); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <a class="footer-back-to-top" href="<?php echo e(request()->routeIs('home') ? '#vrh' : $alphaFooterHome.'#vrh'); ?>">
                <span>Na vrh</span>
                <i class="fa-duotone fa-thin fa-arrow-up" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</footer>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/partials/alpha-global-footer.blade.php ENDPATH**/ ?>