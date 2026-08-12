<?php $__env->startSection('title', 'Alpha Capitalis | Jedna adresa za sve brojke'); ?>
<?php $__env->startSection('main_class', 'hero-page'); ?>

<?php $__env->startSection('content'); ?>
    
    <?php
        $valueItems = [
            ['icon' => 'fa-badge-check', 'title' => 'Stručnost i iskustvo', 'text' => 'Višegodišnje iskustvo u širokom spektru industrija i najviši standardi profesionalne izvrsnosti.'],
            ['icon' => 'fa-scale-balanced', 'title' => 'Neovisnost i povjerenje', 'text' => 'Neovisni smo, objektivni i posvećeni najvišim profesionalnim i etičkim načelima.'],
            ['icon' => 'fa-chart-line', 'title' => 'Partner u rastu', 'text' => 'Ulažemo u vaše ciljeve i pružamo konkretne smjernice koje donose mjerljive rezultate.'],
        ];

        $homeHeroItem = collect($homeHeroBlocks ?? [])->first(fn ($item) => (string) (($item['block'] ?? null)?->type ?? '') === 'home_hero')
            ?? collect($homeHeroBlocks ?? [])->first();
        $homeHeroTranslation = $homeHeroItem['translation'] ?? null;
        $homeHeroPayload = array_merge(
            is_array(($homeHeroItem['block'] ?? null)?->payload ?? null) ? $homeHeroItem['block']->payload : [],
            is_array($homeHeroTranslation?->payload ?? null) ? $homeHeroTranslation->payload : [],
        );
        $cmsHeroTitle = trim((string) ($homeHeroTranslation?->title ?? ''));
        $cmsHeroSubtitle = trim((string) ($homeHeroTranslation?->subtitle ?? ''));
        $heroTitle = $cmsHeroTitle !== '' && mb_strtoupper($cmsHeroTitle) !== 'ALPHA CAPITALIS'
            ? $cmsHeroTitle
            : 'Jedna adresa za sve brojke';
        $heroSubtitle = $cmsHeroSubtitle !== '' && mb_strtoupper($cmsHeroSubtitle) !== 'VAŠ KOMPAS KROZ SVIJET FINANCIJA'
            ? $cmsHeroSubtitle
            : 'Računovodstvo, revizija i savjetovanje — sve na jednom mjestu.';
        $cmsHeroPrimaryLabel = trim((string) ($homeHeroTranslation?->cta_label ?? ''));
        $heroPrimaryLabel = $cmsHeroPrimaryLabel !== '' && mb_strtoupper($cmsHeroPrimaryLabel) !== 'NAŠE USLUGE'
            ? $cmsHeroPrimaryLabel
            : 'Dogovorite sastanak';
        $heroPrimaryUrl = $heroPrimaryLabel === 'Dogovorite sastanak'
            ? route('contact.create')
            : (trim((string) ($homeHeroTranslation?->cta_url ?? '')) ?: route('contact.create'));
        $cmsHeroSecondaryLabel = trim((string) ($homeHeroPayload['secondary_cta_label'] ?? ''));
        $heroSecondaryLabel = $cmsHeroSecondaryLabel !== '' && mb_strtoupper($cmsHeroSecondaryLabel) !== 'UGOVORI SASTANAK'
            ? $cmsHeroSecondaryLabel
            : 'Naše usluge';
        $heroSecondaryUrl = $heroSecondaryLabel === 'Naše usluge'
            ? route('services.index')
            : (trim((string) ($homeHeroPayload['secondary_cta_url'] ?? '')) ?: route('services.index'));
        $heroTitleWords = preg_split('/\s+/u', $heroTitle, -1, PREG_SPLIT_NO_EMPTY) ?: ['Jedna', 'adresa', 'za', 'sve', 'brojke'];
        $heroTitleLines = $heroTitle === 'Jedna adresa za sve brojke'
            ? [['Jedna', 'adresa'], ['za', 'sve', 'brojke']]
            : collect($heroTitleWords)->chunk(max(1, (int) ceil(count($heroTitleWords) / 2)))->map(fn ($line) => $line->values()->all())->values()->all();

        $homeServicesItem = collect($homeServicesBlocks ?? [])->first(fn ($item) => (string) (($item['block'] ?? null)?->type ?? '') === 'home_services')
            ?? collect($homeServicesBlocks ?? [])->first();
        $homeServicesTranslation = $homeServicesItem['translation'] ?? null;
        $homeServicesPayload = array_merge(
            is_array(($homeServicesItem['block'] ?? null)?->payload ?? null) ? $homeServicesItem['block']->payload : [],
            is_array($homeServicesTranslation?->payload ?? null) ? $homeServicesTranslation->payload : [],
        );
        $cmsServicesTitle = trim((string) ($homeServicesTranslation?->title ?? ''));
        $useCmsServicesHeading = $cmsServicesTitle !== '' && !str_starts_with($cmsServicesTitle, 'Stvaramo vrijednost za naše klijente');
        $servicesHeading = $useCmsServicesHeading ? $cmsServicesTitle : 'Vi vodite poslovanje. Mi brinemo da brojke prate vaš rast.';
        $servicesIntro = $useCmsServicesHeading ? trim((string) ($homeServicesTranslation?->subtitle ?? '')) : '';
        $servicesHeadingWords = preg_split('/\s+/u', $servicesHeading, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $servicesHeadingLines = $servicesHeading === 'Vi vodite poslovanje. Mi brinemo da brojke prate vaš rast.'
            ? [['Vi', 'vodite', 'poslovanje.'], ['Mi', 'brinemo', 'da', 'brojke', 'prate', 'vaš', 'rast.']]
            : collect($servicesHeadingWords)->chunk(max(1, (int) ceil(count($servicesHeadingWords) / 2)))->map(fn ($line) => $line->values()->all())->values()->all();

        $serviceDesign = collect([
            'audit' => [
                'title' => 'Revizija',
                'statement' => 'Sigurnost i povjerenje u svakoj odluci.',
                'text' => 'Pouzdani financijski izvještaji jačaju povjerenje vlasnika, banaka, investitora i partnera te smanjuju rizik u važnim poslovnim odlukama.',
                'image' => asset('alpha/service-revizija.jpg'),
                'image_alt' => 'Potpisivanje poslovnog dokumenta za stolom',
                'url' => route('audit.show'),
            ],
            'accounting' => [
                'title' => 'Računovodstvo',
                'statement' => 'Red u brojkama, mir u poslovanju.',
                'text' => 'Ažurni podaci, uredna administracija i kontrola nad financijama oslobađaju vam vrijeme za ono što je najvažnije — razvoj poslovanja.',
                'image' => asset('alpha/service-racunovodstvo.jpg'),
                'image_alt' => 'Rad na financijskim podacima na prijenosnom računalu',
                'url' => route('accounting.show'),
            ],
            'advisory' => [
                'title' => 'Savjetovanje',
                'statement' => 'Prave odluke stvaraju najveću vrijednost.',
                'text' => 'Stručna podrška pomaže prepoznati prilike, smanjiti rizike i donijeti sigurnije odluke za rast, financiranje i budućnost poslovanja.',
                'image' => asset('alpha/service-savjetovanje.jpg'),
                'image_alt' => 'Poslovni razgovor tijekom savjetovanja',
                'url' => route('advisory.show'),
            ],
        ]);

        $cmsServiceItems = collect($homeServicesPayload['services'] ?? [])->filter(fn ($service) => is_array($service))->values();
        $serviceSource = $cmsServiceItems->isNotEmpty() ? $cmsServiceItems : collect($primaryServicePillars ?? []);
        $serviceItems = $serviceSource->map(function (array $service, int $index) use ($serviceDesign): array {
            $key = (string) ($service['key'] ?? '');
            if ($key === '') {
                $key = ['audit', 'accounting', 'advisory'][$index] ?? '';
            }
            $fallback = (array) $serviceDesign->get($key, []);
            if ($fallback === []) {
                return [];
            }

            $dynamicImage = trim((string) ($service['image_url'] ?? ''));
            $useDynamicImage = $dynamicImage !== '' && !str_contains($dynamicImage, '/front-theme/images/services/');

            return array_merge($fallback, [
                'title' => trim((string) ($service['title'] ?? '')) ?: $fallback['title'],
                'statement' => trim((string) ($service['subtitle'] ?? '')) ?: $fallback['statement'],
                'text' => trim((string) ($service['text'] ?? '')) ?: $fallback['text'],
                'image' => $useDynamicImage ? $dynamicImage : $fallback['image'],
                'url' => trim((string) ($service['url'] ?? '')) ?: $fallback['url'],
            ]);
        })->filter()->values();

        if ($serviceItems->isEmpty()) {
            $serviceItems = $serviceDesign->values();
        }

        $processItems = [
            ['icon' => 'fa-magnifying-glass-chart', 'title' => 'Upoznajemo vaš posao', 'text' => 'Razumijemo vaše ciljeve, izazove i okruženje kako bismo identificirali ključne prilike.'],
            ['icon' => 'fa-chart-line', 'title' => 'Analiziramo i planiramo', 'text' => 'Analiziramo podatke i procese te kreiramo strategiju i konkretne korake prema ciljevima.'],
            ['icon' => 'fa-clipboard-check', 'title' => 'Provodimo i pratimo', 'text' => 'Provodimo dogovorene aktivnosti uz kontinuirano praćenje i pravovremene prilagodbe.'],
            ['icon' => 'fa-bullseye', 'title' => 'Donosimo vrijednost', 'text' => 'Ostvarujemo mjerljive rezultate koji jačaju vašu poziciju i donose dugoročnu vrijednost.'],
        ];

        $entities = collect($storeSettings['official_entities'] ?? [])->keyBy('key');
        $locationDefinitions = [
            ['key' => 'alpha-capitalis', 'city' => 'Zagreb – HQ ured', 'short_city' => 'Zagreb', 'css' => 'is-zagreb', 'number' => '01 · HQ'],
            ['key' => 'alpha-capitalis-timia', 'city' => 'Rijeka', 'short_city' => 'Rijeka', 'css' => 'is-rijeka', 'number' => '02'],
            ['key' => 'alpha-capitalis-east', 'city' => 'Vinkovci', 'short_city' => 'Vinkovci', 'css' => 'is-vinkovci', 'number' => '03'],
        ];
        $locationItems = collect($locationDefinitions)->map(function (array $definition) use ($entities): array {
            $entity = (array) $entities->get($definition['key'], []);
            $addressParts = collect($entity['contact_address'] ?? $entity['address'] ?? [])->map(fn ($part) => trim((string) $part))->filter();
            $address = $addressParts->implode(', ');
            $mapQuery = trim((string) ($entity['map_query'] ?? '')) ?: $address;

            return array_merge($definition, [
                'office_label' => trim((string) ($entity['office_label'] ?? '')) ?: 'Ured '.$definition['short_city'],
                'company' => trim((string) ($entity['company'] ?? $entity['name'] ?? '')) ?: 'ALPHA CAPITALIS d.o.o.',
                'address' => $address ?: $definition['short_city'],
                'email' => trim((string) ($entity['email'] ?? '')) ?: 'info@alphacapitalis.com',
                'phone' => trim((string) ($entity['phone'] ?? '')) ?: '+385 (1) 580 6656',
                'map_url' => 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($mapQuery),
            ]);
        })->values();

        $homeStatsItem = collect($homeStatsBlocks ?? [])->first(fn ($item) => (string) (($item['block'] ?? null)?->type ?? '') === 'home_stats')
            ?? collect($homeStatsBlocks ?? [])->first();
        $homeStatsPayload = array_merge(
            is_array(($homeStatsItem['block'] ?? null)?->payload ?? null) ? $homeStatsItem['block']->payload : [],
            is_array(($homeStatsItem['translation'] ?? null)?->payload ?? null) ? $homeStatsItem['translation']->payload : [],
        );
        $dynamicStats = collect($homeStatsPayload['stats'] ?? [])->map(function ($stat): array {
            $stat = is_array($stat) ? $stat : [];
            $rawValue = trim((string) ($stat['value'] ?? '0'));
            return [
                'value' => (int) (preg_replace('/\D+/', '', $rawValue) ?: 0),
                'suffix' => trim((string) ($stat['suffix'] ?? '')) ?: '+',
                'label' => trim((string) ($stat['label'] ?? '')),
            ];
        })->filter(fn (array $stat) => $stat['value'] > 0 && $stat['label'] !== '')->values();
        $statFallbacks = collect([
            ['value' => 300, 'suffix' => '+', 'label' => 'Zadovoljnih klijenata'],
            ['value' => 600, 'suffix' => '+', 'label' => 'Poslovnih klijenata'],
            ['value' => 60, 'suffix' => '+', 'label' => 'Kvalificiranih stručnjaka'],
            ['value' => 20, 'suffix' => '+', 'label' => 'Godina iskustva'],
        ]);
        $locationStats = $statFallbacks->map(fn (array $fallback, int $index) => array_merge($fallback, (array) $dynamicStats->get($index, [])));
        $statIcons = ['fa-chart-column', 'fa-users', 'fa-user-tie', 'fa-shield-heart'];

        $newsItems = collect($latestBlogPosts ?? [])->take(3)->map(function ($post) use ($locale, $fallbackLocale): array {
            $translation = $post->translations->firstWhere('locale', $locale)
                ?? $post->translations->firstWhere('locale', $fallbackLocale)
                ?? $post->translations->first();
            $category = $post->categories->sortByDesc(fn ($item) => (int) ($item->pivot->is_primary ?? false))->first();
            $categoryTranslation = $category?->translations->firstWhere('locale', $locale)
                ?? $category?->translations->firstWhere('locale', $fallbackLocale)
                ?? $category?->translations->first();
            $slug = trim((string) ($translation?->slug ?? ''));
            $excerpt = trim(strip_tags((string) ($translation?->excerpt ?? '')));

            return [
                'category' => trim((string) ($categoryTranslation?->name ?? '')) ?: 'Novosti',
                'title' => trim((string) ($translation?->title ?? $post->code)) ?: 'Alpha Capitalis',
                'text' => Illuminate\Support\Str::limit($excerpt ?: 'Saznajte aktualne informacije, rokove i stručne savjete za sigurnije poslovne odluke.', 210),
                'url' => $slug !== '' ? route('blog.show', ['slug' => $slug]) : route('blog.index'),
            ];
        })->values();

        if ($newsItems->isEmpty()) {
            $newsItems = collect([
                ['category' => 'Financije', 'title' => 'Dubinsko snimanje – zašto je ključno?', 'text' => 'Točna i vjerodostojna financijska izvješća temelj su sigurnijih transakcija i kvalitetnijih poslovnih odluka.', 'url' => route('blog.index')],
                ['category' => 'EU fondovi', 'title' => 'Jesu li inovacije nužne za EU financiranje?', 'text' => 'Europska unija sve više ulaže u projekte koji donose dodanu vrijednost, održivost i mjerljiv razvoj.', 'url' => route('blog.index')],
                ['category' => 'EU fondovi', 'title' => 'EU fondovi za male i srednje poduzetnike', 'text' => 'Pregled mogućnosti financijske podrške za razvoj, ulaganja i digitalizaciju malih i srednjih poduzeća.', 'url' => route('blog.index')],
            ]);
        }
    ?>

    <section class="hero" id="vrh" aria-labelledby="hero-title">
        <video class="hero-video" autoplay muted loop playsinline preload="metadata" poster="<?php echo e(asset('alpha/alpha-zagreb-poster.jpg')); ?>" aria-hidden="true" data-alpha-hero-video>
            <source src="<?php echo e(asset('alpha/alpha-zagreb-loop-hq.mp4')); ?>" type="video/mp4">
        </video>
        <div class="hero-overlay" aria-hidden="true"></div>

        <div class="hero-content">
            <h1 id="hero-title" aria-label="<?php echo e($heroTitle); ?>">
                <?php $heroCharacterIndex = 0; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $heroTitleLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="hero-line"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $line; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php $upperWord = Illuminate\Support\Str::upper($word); ?><span class="hero-word <?php echo e($loop->parent->last && $loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = mb_str_split($upperWord); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $character): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="hero-char" style="--char-index: <?php echo e($heroCharacterIndex++); ?>"><?php echo e($character); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </h1>
            <p><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($heroSubtitle === 'Računovodstvo, revizija i savjetovanje — sve na jednom mjestu.'): ?>Računovodstvo, revizija i savjetovanje —<br> sve na jednom mjestu.<?php else: ?><?php echo e($heroSubtitle); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
            <div class="hero-actions">
                <a class="button button-gold" href="<?php echo e($heroPrimaryUrl); ?>"><span><?php echo e($heroPrimaryLabel); ?></span></a>
                <a class="button button-outline" href="<?php echo e($heroSecondaryUrl); ?>"><span><?php echo e($heroSecondaryLabel); ?></span></a>
            </div>
            <div class="scroll-cue" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span></div>
        </div>

        <aside class="hero-locations" aria-label="Naše lokacije">
            <span class="location-number" aria-hidden="true"><i class="fa-duotone fa-thin fa-bullseye-pointer"></i></span>
            <div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $locationItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p><?php echo e(Illuminate\Support\Str::upper($location['short_city'])); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
        </aside>
    </section>

    <section class="values-section" id="vrijednosti" aria-labelledby="values-title">
        <div class="values-inner">
            <div class="values-intro">
                <?php $valuesWords = explode(' ', 'Stvaramo vrijednost za naše klijente u svim fazama razvoja poslovanja.'); ?>
                <h2 class="values-title" id="values-title" data-words-slide-from-right aria-label="Stvaramo vrijednost za naše klijente u svim fazama razvoja poslovanja.">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $valuesWords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="values-word <?php echo e($word === 'vrijednost' ? 'is-accent' : ''); ?>" style="--value-word-index: <?php echo e($loop->index); ?>" aria-hidden="true"><?php echo e($word); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </h2>
                <p class="values-copy content-reveal" data-image-reveal><strong>ALPHA CAPITALIS</strong> pruža vam sigurnost u poslovanju, jasnoću u financijama i partnera koji vam pomaže donositi bolje odluke, smanjiti rizike i ostvariti održiv rast.</p>
            </div>

            <div class="values-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $valueItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="value-item content-reveal" data-image-reveal style="--reveal-index: <?php echo e($loop->index); ?>">
                        <div class="value-icon" aria-hidden="true"><i class="fa-duotone fa-thin fa-fw <?php echo e($item['icon']); ?>"></i></div>
                        <div class="value-content">
                            <h3 data-words-slide-from-right aria-label="<?php echo e($item['title']); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = explode(' ', $item['title']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="value-title-word" style="--services-word-index: <?php echo e($loop->index); ?>" aria-hidden="true"><?php echo e($word); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </h3>
                            <p><?php echo e($item['text']); ?></p>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    <section class="services-section" id="usluge" aria-labelledby="services-title">
        <div class="services-shell">
            <header class="services-header">
                <h2 class="services-title" id="services-title" data-words-slide-from-right aria-label="<?php echo e($servicesHeading); ?>">
                    <?php $servicesWordIndex = 0; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $servicesHeadingLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="services-title-line" aria-hidden="true"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $line; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="services-word <?php echo e(mb_strtolower(trim($word, '.,!?')) === 'brojke' || ($useCmsServicesHeading && $loop->parent->last && $loop->last) ? 'is-accent' : ''); ?>" style="--services-word-index: <?php echo e($servicesWordIndex++); ?>"><?php echo e($word); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </h2>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($servicesIntro !== ''): ?>
                    <p class="services-intro content-reveal" data-image-reveal><?php echo e($servicesIntro); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </header>

            <div class="services-grid services-grid--count-<?php echo e(min(3, $serviceItems->count())); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $serviceItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a class="service-card" href="<?php echo e($service['url']); ?>" data-image-reveal style="--service-index: <?php echo e($loop->index); ?>">
                        <div class="service-card-media">
                            <img src="<?php echo e($service['image']); ?>" alt="<?php echo e($service['image_alt']); ?>" width="1080" height="1350" loading="lazy" decoding="async">
                        </div>
                        <div class="service-card-copy">
                            <h3 class="service-card-title" data-words-slide-from-right aria-label="<?php echo e($service['title']); ?>">
                                <span class="service-title-word" style="--services-word-index: 0" aria-hidden="true"><?php echo e($service['title']); ?></span>
                            </h3>
                            <p class="service-statement"><?php echo e($service['statement']); ?></p>
                            <p class="service-description"><?php echo e($service['text']); ?></p>
                            <span class="service-link" aria-hidden="true">SAZNAJTE VIŠE <i class="fa-duotone fa-thin fa-arrow-right fa-fw"></i></span>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    <section class="process-section" id="proces" aria-labelledby="process-title" data-process-reveal>
        <div class="process-shell">
            <header class="process-header">
                <?php $processTitle = ['Jednostavan', 'proces.', 'Jasni', 'koraci.']; ?>
                <h2 class="process-title" id="process-title" data-words-slide-from-right aria-label="Jednostavan proces. Jasni koraci.">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $processTitle; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="process-title-word <?php echo e($word === 'koraci.' ? 'is-accent' : ''); ?>" style="--services-word-index: <?php echo e($loop->index); ?>" aria-hidden="true"><?php echo e($word); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </h2>
            </header>
            <div class="process-track">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $processItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="process-item" style="--process-index: <?php echo e($loop->index); ?>">
                        <div class="process-marker" aria-hidden="true"><span><?php echo e(str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT)); ?></span></div>
                        <i class="process-icon fa-duotone fa-thin fa-fw <?php echo e($item['icon']); ?>" aria-hidden="true"></i>
                        <div class="process-copy"><h3><?php echo e($item['title']); ?></h3><p><?php echo e($item['text']); ?></p></div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    <section class="locations-section" id="lokacije" aria-labelledby="locations-title" data-locations-reveal>
        <div class="locations-shell">
            <div class="locations-layout">
                <div class="locations-copy">
                    <h2 class="locations-title" id="locations-title" data-words-slide-from-right aria-label="Prisutni na 3 lokacije">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Prisutni', 'na', '3', 'lokacije']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="locations-title-word <?php echo e($word === 'lokacije' ? 'is-accent' : ''); ?>" style="--services-word-index: <?php echo e($loop->index); ?>" aria-hidden="true"><?php echo e($word); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h2>
                    <p class="locations-intro"><strong>Zagreb, Rijeka i Vinkovci</strong> — podrška klijentima diljem Hrvatske.</p>

                    <div class="locations-addresses">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $locationItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="location-address" style="--location-index: <?php echo e($loop->index); ?>">
                                <button class="location-address-trigger" type="button" aria-expanded="false" aria-controls="location-details-<?php echo e($loop->index); ?>" data-location-index="<?php echo e($loop->index); ?>">
                                    <span class="location-address-marker" aria-hidden="true"><?php echo e(str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT)); ?></span>
                                    <span class="location-address-summary"><span class="location-address-title"><?php echo e($location['city']); ?></span><span class="location-address-short"><?php echo e($location['address']); ?></span></span>
                                    <span class="location-address-toggle" aria-hidden="true"><span></span><span></span></span>
                                </button>
                                <div class="location-details" id="location-details-<?php echo e($loop->index); ?>" aria-hidden="true" inert>
                                    <div class="location-details-inner">
                                        <div class="location-details-card">
                                            <span class="location-office-label"><?php echo e($location['office_label']); ?></span>
                                            <h3><?php echo e($location['company']); ?></h3>
                                            <a class="location-map-link" href="<?php echo e($location['map_url']); ?>" target="_blank" rel="noopener noreferrer" tabindex="-1"><i class="fa-light fa-location-dot" aria-hidden="true"></i><span>Pogledaj na karti</span><i class="fa-light fa-arrow-up-right" aria-hidden="true"></i></a>
                                            <div class="location-contacts">
                                                <a href="mailto:<?php echo e($location['email']); ?>" tabindex="-1"><i class="fa-light fa-envelope" aria-hidden="true"></i><span><small>Email</small><strong><?php echo e($location['email']); ?></strong></span></a>
                                                <a href="tel:<?php echo e(preg_replace('/[^+0-9]/', '', $location['phone'])); ?>" tabindex="-1"><i class="fa-light fa-phone" aria-hidden="true"></i><span><small>Telefon</small><strong><?php echo e($location['phone']); ?></strong></span></a>
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
                        <svg class="map-routes" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true"><path class="map-route" d="M 15.8 28.7 C 24 20, 33 18, 42.5 18.8"></path><path class="map-route" d="M 42.5 18.8 C 57 15, 71 18, 86.7 23.8"></path></svg>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [1, 0, 2]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locationIndex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $location = $locationItems[$locationIndex]; ?>
                            <button class="map-location <?php echo e($location['css']); ?>" type="button" aria-label="Prikaži kontaktne podatke za ured <?php echo e($location['short_city']); ?>" aria-expanded="false" aria-controls="location-details-<?php echo e($locationIndex); ?>" style="--map-index: <?php echo e($loop->index); ?>" data-location-index="<?php echo e($locationIndex); ?>">
                                <span class="map-beacon" aria-hidden="true"><i class="fa-duotone fa-thin fa-bullseye-pointer"></i></span>
                                <span class="map-location-label"><small><?php echo e($location['number']); ?></small><strong><?php echo e($location['short_city']); ?></strong></span>
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="locations-stats" aria-label="Alpha Capitalis u brojkama">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $locationStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="location-stat" style="--stat-index: <?php echo e($loop->index); ?>">
                        <div class="location-stat-icon" aria-hidden="true"><i class="fa-duotone fa-thin fa-fw <?php echo e($statIcons[$loop->index]); ?>"></i></div>
                        <div><strong><span data-count-target="<?php echo e($stat['value']); ?>">0</span><span class="location-stat-suffix"><?php echo e($stat['suffix']); ?></span></strong><p><?php echo e($stat['label']); ?></p></div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    <section class="news-section" id="novosti" aria-labelledby="news-title">
        <div class="news-shell">
            <header class="news-header">
                <?php $newsHeading = explode(' ', 'Rokovi, novosti i savjeti za sigurnije poslovanje.'); ?>
                <h2 class="news-title" id="news-title" data-words-slide-from-right aria-label="Rokovi, novosti i savjeti za sigurnije poslovanje.">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $newsHeading; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="news-title-word <?php echo e($word === 'poslovanje.' ? 'is-accent' : ''); ?>" style="--services-word-index: <?php echo e($loop->index); ?>" aria-hidden="true"><?php echo e($word); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </h2>
                <a class="news-all-link content-reveal" data-image-reveal href="<?php echo e(route('blog.index')); ?>"><span>Pogledaj sve objave</span><i class="fa-duotone fa-thin fa-arrow-right fa-fw" aria-hidden="true"></i></a>
            </header>
            <div class="news-grid">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $newsItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a class="news-card" data-image-reveal href="<?php echo e($item['url']); ?>" style="--news-index: <?php echo e($loop->index); ?>">
                        <span class="news-card-category"><?php echo e($item['category']); ?></span><h3><?php echo e($item['title']); ?></h3><p><?php echo e($item['text']); ?></p>
                        <span class="news-card-link" aria-hidden="true">Pročitaj više <i class="fa-duotone fa-thin fa-arrow-right fa-fw"></i></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    <section class="contact-cta" id="kontakt-cta" aria-labelledby="contact-cta-title">
        <div class="contact-cta-shell">
            <div class="contact-cta-copy">
                <?php $contactHeading = explode(' ', 'Razgovarajmo o sljedećoj fazi vašeg poslovanja.'); ?>
                <h2 class="contact-cta-title" id="contact-cta-title" data-words-slide-from-right aria-label="Razgovarajmo o sljedećoj fazi vašeg poslovanja.">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $contactHeading; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="contact-cta-title-word <?php echo e(in_array($word, ['sljedećoj', 'fazi'], true) ? 'is-accent' : ''); ?>" style="--services-word-index: <?php echo e($loop->index); ?>" aria-hidden="true"><?php echo e($word); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </h2>
            </div>
            <div class="contact-cta-card" data-image-reveal>
                <div class="contact-cta-card-heading"><span>Vrijeme je za pravi korak.</span></div>
                <p>Dogovorite uvodni sastanak s našim stručnjacima i pretvorite izazove u jasne, izvedive korake.</p>
                <a class="contact-cta-button" href="<?php echo e(route('contact.create')); ?>"><span>Dogovorite sastanak</span><i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i></a>
                <small><span class="contact-cta-status-dot" aria-hidden="true"></span>Termin razgovora prilagođavamo vama.</small>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/home/index.blade.php ENDPATH**/ ?>