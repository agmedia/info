<?php $__env->startSection('title', $servicePageTitle ?? 'Usluge'); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $showcase = (array) ($servicesShowcase ?? []);
        $titleLead = trim((string) ($showcase['title_lead'] ?? '')) ?: 'Naše usluge';
        $titleAccent = trim((string) ($showcase['title_accent'] ?? ''));
        $intro = trim((string) ($showcase['intro'] ?? ''))
            ?: 'Kroz integrirani pristup reviziji, računovodstvu i financijskom savjetovanju stvaramo dodatnu vrijednost pomažući klijentima da posluju sigurnije, transparentnije i učinkovitije.';
        $introWithServiceLinks = e($intro);
        $introServiceLinks = [
            route('advisory.show') => ['financijskom savjetovanju', 'financial advisory'],
            route('accounting.show') => ['računovodstvu', 'accounting'],
            route('audit.show') => ['reviziji', 'audit'],
        ];

        foreach ($introServiceLinks as $url => $labels) {
            foreach ($labels as $label) {
                $escapedLabel = e($label);
                $introWithServiceLinks = str_replace(
                    $escapedLabel,
                    '<a class="services-index-inline-link" href="'.e($url).'">'.$escapedLabel.'</a>',
                    $introWithServiceLinks,
                );
            }
        }
        $introTitleWords = preg_split('/\s+/u', $titleLead, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $introTitleAccentWords = preg_split('/\s+/u', $titleAccent, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $cardDesign = collect([
            'audit' => [
                'title' => 'Revizija',
                'statement' => 'sigurnost i povjerenje u brojke',
                'text' => 'Neovisna provjera financijskih izvještaja koja povećava povjerenje vlasnika, investitora i partnera.',
                'image' => asset('alpha/service-revizija.jpg'),
                'image_alt' => 'Potpisivanje poslovnog dokumenta za stolom',
                'url' => route('audit.show'),
            ],
            'accounting' => [
                'title' => 'Računovodstvo',
                'statement' => 'kontrola i jasnoća poslovanja',
                'text' => 'Precizno vođenje knjiga i pravovremeno izvještavanje koje oslobađa menadžment za strateške odluke.',
                'image' => asset('alpha/service-racunovodstvo.jpg'),
                'image_alt' => 'Rad na financijskim podacima na prijenosnom računalu',
                'url' => route('accounting.show'),
            ],
            'advisory' => [
                'title' => 'Savjetovanje',
                'statement' => 'rast, optimizacija i bolji financijski izbor',
                'text' => 'Financijsko i porezno savjetovanje te pribavljanje kapitala - sve na jednom mjestu.',
                'image' => asset('alpha/service-savjetovanje.jpg'),
                'image_alt' => 'Poslovni razgovor tijekom savjetovanja',
                'url' => route('advisory.show'),
            ],
        ]);

        $serviceItems = collect($primaryServicePillars ?? [])
            ->map(function ($service, int $index) use ($cardDesign): array {
                $service = is_array($service) ? $service : [];
                $key = trim((string) ($service['key'] ?? ''));

                if ($key === '') {
                    $key = ['audit', 'accounting', 'advisory'][$index] ?? '';
                }

                $fallback = (array) $cardDesign->get($key, []);

                if ($fallback === []) {
                    return [];
                }

                return array_merge($fallback, [
                    'title' => trim((string) ($service['title'] ?? '')) ?: $fallback['title'],
                    'statement' => trim((string) ($service['subtitle'] ?? '')) ?: $fallback['statement'],
                    'text' => trim((string) ($service['text'] ?? '')) ?: $fallback['text'],
                    'url' => trim((string) ($service['url'] ?? '')) ?: $fallback['url'],
                ]);
            })
            ->filter()
            ->values();

        if ($serviceItems->isEmpty()) {
            $serviceItems = $cardDesign->values();
        }
    ?>

    <section class="values-section services-index-intro" aria-labelledby="ac-services-index-title">
        <div class="values-inner services-index-intro-layout">
            <div class="values-intro">
                <h1 class="values-title services-index-intro-title" id="ac-services-index-title" data-words-slide-from-right aria-label="<?php echo e(trim($titleLead.' '.$titleAccent)); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $introTitleWords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="values-word <?php echo e(mb_strtolower(trim($word, '.,!?')) === 'usluge' || ($introTitleAccentWords === [] && $loop->last) ? 'is-accent' : ''); ?>" style="--value-word-index: <?php echo e($loop->index); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $introTitleAccentWords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="values-word is-accent" style="--value-word-index: <?php echo e(count($introTitleWords) + $loop->index); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </h1>

            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($intro !== ''): ?>
                <p class="values-copy services-index-intro-copy content-reveal" data-image-reveal><?php echo $introWithServiceLinks; ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    <section id="ac-services-index" class="services-section services-section--index-page" aria-labelledby="ac-services-index-title">
        <div class="services-shell services-index-cards-shell">

            <div class="services-grid services-grid--count-<?php echo e(min(3, $serviceItems->count())); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $serviceItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a class="service-card" href="<?php echo e($service['url']); ?>" data-image-reveal style="--service-index: <?php echo e($loop->index); ?>">
                        <div class="service-card-media">
                            <img src="<?php echo e($service['image']); ?>" alt="<?php echo e($service['image_alt']); ?>" width="1080" height="1350" loading="<?php echo e($loop->index < 3 ? 'eager' : 'lazy'); ?>" decoding="async">
                        </div>
                        <div class="service-card-copy">
                            <h2 class="service-card-title" data-words-slide-from-right aria-label="<?php echo e($service['title']); ?>">
                                <span class="service-title-word" style="--services-word-index: 0" aria-hidden="true"><?php echo e($service['title']); ?></span>
                            </h2>
                            <p class="service-statement"><?php echo e($service['statement']); ?></p>
                            <p class="service-description"><?php echo e($service['text']); ?></p>
                            <span class="service-link" aria-hidden="true">SAZNAJTE VIŠE <i class="fa-duotone fa-thin fa-arrow-right fa-fw"></i></span>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/services.blade.php ENDPATH**/ ?>