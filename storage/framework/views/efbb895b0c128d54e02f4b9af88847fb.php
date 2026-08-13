<?php
    $locationsSectionId = $locationsSectionId ?? 'lokacije';
    $locationsTitleId = $locationsTitleId ?? 'locations-title';
    $locationDetailsPrefix = $locationDetailsPrefix ?? 'location-details';
    $showLocationStats = $showLocationStats ?? true;
    $locationStats = $locationStats ?? collect([
        ['value' => 300, 'suffix' => '+', 'label' => 'Odrađenih projekata'],
        ['value' => 600, 'suffix' => '+', 'label' => 'Redovnih klijenata'],
        ['value' => 60, 'suffix' => '+', 'label' => 'Kvalificiranih stručnjaka'],
        ['value' => 20, 'suffix' => '+', 'label' => 'Godina iskustva'],
    ]);
    $statIcons = $statIcons ?? ['fa-chart-column', 'fa-users', 'fa-user-tie', 'fa-shield-heart'];

    $locationEntities = collect($storeSettings['official_entities'] ?? [])->keyBy('key');
    $locationDefinitions = [
        ['key' => 'alpha-capitalis', 'city' => 'Zagreb – HQ ured', 'short_city' => 'Zagreb', 'css' => 'is-zagreb', 'number' => '01 · HQ'],
        ['key' => 'alpha-capitalis-timia', 'city' => 'Rijeka', 'short_city' => 'Rijeka', 'css' => 'is-rijeka', 'number' => '02'],
        ['key' => 'alpha-capitalis-east', 'city' => 'Vinkovci', 'short_city' => 'Vinkovci', 'css' => 'is-vinkovci', 'number' => '03'],
    ];
    $locationItems = collect($locationDefinitions)->map(function (array $definition) use ($locationEntities): array {
        $entity = (array) $locationEntities->get($definition['key'], []);
        $addressParts = collect($entity['contact_address'] ?? $entity['address'] ?? [])
            ->map(fn ($part) => trim((string) $part))
            ->filter();
        $address = $addressParts->implode(', ');
        $mapQuery = trim((string) ($entity['map_query'] ?? '')) ?: $address;

        return array_merge($definition, [
            'office_label' => trim((string) ($entity['office_label'] ?? $entity['label'] ?? '')) ?: 'Ured '.$definition['short_city'],
            'company' => trim((string) ($entity['company'] ?? $entity['name'] ?? '')) ?: 'ALPHA CAPITALIS d.o.o.',
            'address' => $address ?: $definition['short_city'],
            'email' => trim((string) ($entity['email'] ?? '')) ?: 'info@alphacapitalis.com',
            'phone' => trim((string) ($entity['phone'] ?? '')) ?: '+385 (1) 580 6656',
            'map_url' => 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($mapQuery),
        ]);
    })->values();
?>

<section class="locations-section" id="<?php echo e($locationsSectionId); ?>" aria-labelledby="<?php echo e($locationsTitleId); ?>" data-locations-reveal>
    <div class="locations-shell">
        <div class="locations-layout">
            <div class="locations-copy">
                <h2 class="locations-title" id="<?php echo e($locationsTitleId); ?>" data-words-slide-from-right aria-label="Prisutni na 3 lokacije">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Prisutni', 'na', '3', 'lokacije']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="locations-title-word animation-index-<?php echo e($loop->index); ?> <?php echo e($word === 'lokacije' ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </h2>
                <p class="locations-intro"><strong>Zagreb, Rijeka i Vinkovci</strong> — podrška klijentima diljem Hrvatske.</p>

                <div class="locations-addresses">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $locationItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="location-address animation-index-<?php echo e($loop->index); ?>">
                            <button class="location-address-trigger" type="button" aria-expanded="false" aria-controls="<?php echo e($locationDetailsPrefix); ?>-<?php echo e($loop->index); ?>" data-location-index="<?php echo e($loop->index); ?>">
                                <span class="location-address-marker" aria-hidden="true"><?php echo e(str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT)); ?></span>
                                <span class="location-address-summary">
                                    <span class="location-address-title"><?php echo e($location['city']); ?></span>
                                    <span class="location-address-short"><?php echo e($location['address']); ?></span>
                                </span>
                                <span class="location-address-toggle" aria-hidden="true"><span></span><span></span></span>
                            </button>
                            <div class="location-details" id="<?php echo e($locationDetailsPrefix); ?>-<?php echo e($loop->index); ?>" aria-hidden="true" inert>
                                <div class="location-details-inner">
                                    <div class="location-details-card">
                                        <span class="location-office-label"><?php echo e($location['office_label']); ?></span>
                                        <h3><?php echo e($location['company']); ?></h3>
                                        <a class="location-map-link" href="<?php echo e($location['map_url']); ?>" target="_blank" rel="noopener noreferrer" tabindex="-1">
                                            <i class="fa-light fa-location-dot" aria-hidden="true"></i>
                                            <span>Pogledaj na karti</span>
                                            <i class="fa-light fa-arrow-up-right" aria-hidden="true"></i>
                                        </a>
                                        <div class="location-contacts">
                                            <a href="mailto:<?php echo e($location['email']); ?>" tabindex="-1">
                                                <i class="fa-light fa-envelope" aria-hidden="true"></i>
                                                <span><small>Email</small><strong><?php echo e($location['email']); ?></strong></span>
                                            </a>
                                            <a href="tel:<?php echo e(preg_replace('/[^+0-9]/', '', $location['phone'])); ?>" tabindex="-1">
                                                <i class="fa-light fa-phone" aria-hidden="true"></i>
                                                <span><small>Telefon</small><strong><?php echo e($location['phone']); ?></strong></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="locations-map" aria-label="Karta lokacija u Hrvatskoj">
                <div class="locations-map-corners" aria-hidden="true">
                    <span class="locations-map-corner is-top-left">RIJEKA · 45.33° N · 14.44° E</span>
                    <span class="locations-map-corner is-top-right">ZAGREB · 45.80° N · 15.91° E</span>
                    <span class="locations-map-corner is-bottom-right">VINKOVCI · 45.29° N · 18.80° E</span>
                    <span class="locations-map-corner is-bottom-left">HR / 3 UREDA</span>
                </div>
                <div class="locations-map-stage">
                    <div class="locations-map-glow" aria-hidden="true"></div>
                    <img class="croatia-map" src="<?php echo e(asset('alpha/croatia-map.svg')); ?>" alt="Karta Hrvatske s uredima u Zagrebu, Rijeci i Vinkovcima" width="800" height="800" loading="lazy" decoding="async">
                    <img class="map-routes contact-map-routes" src="<?php echo e(asset('alpha/croatia-map-routes.svg')); ?>?v=5" alt="" aria-hidden="true" width="100" height="100" decoding="sync">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [1, 0, 2]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locationIndex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php ($location = $locationItems[$locationIndex]); ?>
                        <button class="map-location animation-index-<?php echo e($loop->index); ?> <?php echo e($location['css']); ?>" type="button" aria-label="Prikaži kontaktne podatke za ured <?php echo e($location['short_city']); ?>" aria-expanded="false" aria-controls="<?php echo e($locationDetailsPrefix); ?>-<?php echo e($locationIndex); ?>" data-location-index="<?php echo e($locationIndex); ?>">
                            <span class="map-beacon" aria-hidden="true"><i class="fa-duotone fa-thin fa-bullseye-pointer"></i></span>
                            <span class="map-location-label"><small><?php echo e($location['number']); ?></small><strong><?php echo e($location['short_city']); ?></strong></span>
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showLocationStats): ?>
            <div class="locations-stats" aria-label="Alpha Capitalis u brojkama">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $locationStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="location-stat animation-index-<?php echo e($loop->index); ?>">
                        <div class="location-stat-icon" aria-hidden="true"><i class="fa-duotone fa-thin fa-fw <?php echo e($statIcons[$loop->index]); ?>"></i></div>
                        <div><strong><span data-count-target="<?php echo e($stat['value']); ?>">0</span><span class="location-stat-suffix"><?php echo e($stat['suffix']); ?></span></strong><p><?php echo e($stat['label']); ?></p></div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/partials/locations-showcase.blade.php ENDPATH**/ ?>