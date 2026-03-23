<?php
    $translation = $selectedTranslation
        ?? $page->translations->firstWhere('locale', $locale)
        ?? $page->translations->firstWhere('locale', $fallbackLocale);

    $pageTitleBreadcrumbs = [
        ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
        ['label' => $translation?->title ?? $page->code, 'current' => true],
    ];
    $academyBlogPosts = $academyBlogPosts ?? collect();
    $academyBlogSection = $academyBlogSection ?? ['title' => '', 'intro' => ''];
    $academyResourceDocuments = $academyResourceDocuments ?? collect();
    $academyResourceSection = $academyResourceSection ?? ['title' => '', 'intro' => ''];
    $academyVideos = $academyVideos ?? collect();
    $academyVideoSection = $academyVideoSection ?? ['title' => '', 'intro' => ''];
    $academyVideoInitialCount = 4;
    $academyVideoHasOverflow = $academyVideos->count() > $academyVideoInitialCount;
    $academyVideoShowMoreLabel = $locale === 'hr' ? 'Pokaži još' : 'Show more';
    $academyResourceCtaLabel = $locale === 'hr' ? 'Preuzmi' : 'Download';

    $academyPrograms = [
        [
            'title' => 'Seminari za male i srednje poduzetnike',
            'icon' => 'growth',
            'accent' => 'gold',
            'intro' => 'Edukacija je namijenjena poduzetnicima koji imaju interes izraditi poslovni plan, godišnji budžet, pribaviti financiranje, pristupiti investitoru, prodati poslovne udjele i/ili napraviti prijenos poslovanja na mlađe generacije ili na menadžment.',
            'items' => [
                [
                    'title' => 'Pribavljanje kapitala',
                    'text' => 'Struktura kapitala predstavlja omjer dužničkog i vlasničkog kapitala društva, iz čega se spoznaje način na koji društvo financira imovinu s kojom generira prihode. Edukacija će polaznicima omogućiti usvajanje znanja o: vrstama izvora financiranja, modelima financiranja, namjeni pribavljenih sredstava te poslovnim procesom pribavljanja kapitala.',
                ],
                [
                    'title' => 'Business Transfer',
                    'text' => 'Podrazumijeva prijenos vlasništva društva na mlađu generaciju, drugu osobu ili drugo društvo, čime se osigurava kontinuitet postojanja i poslovne aktivnosti društva. Polaznici će moći opisati koncept prijenosa poslovanja, diskutirati o različitim opcijama izlaska iz obiteljskog poslovanja, kritički razlagati o pravovremenom planiranju prijenosa poslovanja i sl.',
                ],
                [
                    'title' => 'Procjena vrijednosti',
                    'text' => 'Procjena vrijednosti kompleksan je proces koji se koristi u mnogim situacijama: od prijenosa udjela u vlasništvu na odabrane nasljednike, inicijalne javne ponude, dokapitalizacije, davanje udjela u vlasništvu kao nagrada menadžmentu za ostvarene rezultate i dr. Sve teme vezane uz procjenu vrijednosti bit će potkrijepljene stvarnim primjerima iz prakse.',
                ],
                [
                    'title' => 'Računovodstvo za male poduzetnike',
                    'text' => 'Program edukacije namijenjen je vlasnicima malih poduzeća koji žele konkretne odgovore kroz praktične primjere, a ne teoriju. Kroz seminare polaznici će naučiti kako samostalno razumjeti financijske izvještaje, te porezne, računovodstvene i financijske poslove. Naši stručnjaci prenose konkretne slučajeve, te Vas upućuju na koje stvari trebate paziti da izbjegnete najčešće greške u poslovanju.',
                ],
            ],
        ],
        [
            'title' => 'Specijalistički seminari',
            'icon' => 'insight',
            'accent' => 'blue',
            'intro' => 'Edukacija o temama iz područja financija i računovodstva namijenjena je vlasnicima i menadžmentu, stručnjacima iz odjela financija, djelatnicima nefinancijskih odjela, početnicima u kontrolingu, reviziji, financijama i računovodstvu te korporativnim pravnicima koji trebaju više znanja i iskustva iz područja računovodstva i financija.',
            'items' => [
                [
                    'title' => 'Financije za nefinancijaše',
                    'text' => 'Kroz teorijsko predavanje, te radom na stvarnim primjerima sudionici će se upoznati s osnovnim financijskim izvještajima i njihovom analizom, upravljanjem kapitalom društva, ekonomskom profitabilnošću te financijskom vrijednošću društva. Polaznici će naučiti pravilno tumačiti informacije iz financijskih izvještaja što je iznimno važno za opstanak društva. Seminar je namijenjen djelatnicima nefinancijskih odjela, početnicima u kontrolingu, reviziji, financijama, računovodstvu te vlasnicima i menadžmentu.',
                ],
                [
                    'title' => 'Financije za odvjetnike',
                    'text' => 'Edukacija je namijenjena korporativnim pravnicima koji sve više trebaju znanja i iskustva iz područja računovodstva i financija. Kroz teorijsko predavanje i praktične primjere iz prakse detaljnije ćemo vas upoznati s osnovnim načelima računovodstva i financija s kojima se korporativni pravnici i odvjetnici svakodnevno susreću u svom radu. Također, kroz seminar polaznici će naučiti tumačiti i analizirati financijske izvještaje i pokazatelje.',
                ],
                [
                    'title' => 'Analiza financijskih izvještaja',
                    'text' => 'Obuhvaća vrednovanje prethodnog financijskog poslovanja društva i njegovog budućeg poslovanja. Polaznici će biti upoznati s pojmom financijskih izvještaja, horizontalnom i vertikalnom analizom istih, te značenjem financijskih omjera i indikatora prikazanim na stvarnim primjerima iz prakse. Postupak analize financijskih izvještaja bit će prikazan na stvarnim primjerima iz prakse.',
                ],
                [
                    'title' => 'Manipulacije financijskim izvještajima',
                    'text' => 'Kroz primjere iz prakse, seminar će omogućiti polaznicima da brže uoče neuobičajene odnose i sumnjive transakcije te spriječe ili barem umanje posljedice prijevare. Na seminaru će se prezentirati i pojasniti otkrivanje i upravljanje rizicima poslovnih prijevara. Objasnit će se dva osnovna pristupa pomoću kojih se može manipulirati financijskim izvještajima te prikazati tehnike manipulacije financijskim izvještajima.',
                ],
            ],
        ],
        [
            'title' => 'Računovodstveni seminari',
            'icon' => 'ledger',
            'accent' => 'sand',
            'intro' => 'Edukacija razvija vještine potrebne za osiguravanje pouzdanih i usporedivih informacija, razumijevanje manipulacija financijskim izvještajima te razumijevanje složenijih poslovnih aktivnosti poput spajanja i preuzimanja.',
            'items' => [
                [
                    'title' => 'Forenzičko računovodstvo',
                    'text' => 'Polaznici seminara će se upoznati s mogućim manipulacijama financijskih izvještaja, ciljevima, tehnikama i posljedicama istih. Na seminaru prolazimo kroz računovodstvena načela, politike i procjene koje su usklađene s najnovijim promjenama u računovodstvenim standardima.',
                ],
                [
                    'title' => 'Menadžersko računovodstvo / Kontroling',
                    'text' => 'Edukacija iz područja menadžerskog računovodstva i kontrolinga polaznicima omogućuje razvijanje vještina kojim će se osigurati posjedovanje pouzdanih i usporedivih informacija u očekivanim ili ostvarenim vrijednosno izraženim ciljevima. Glavni cilj je razumijevanje prošlosti, kontrola sadašnjosti i planiranje budućnosti.',
                ],
                [
                    'title' => 'Poslovne kombinacije – financijski, porezni i pravni aspekti',
                    'text' => 'Edukacija iz područja poslovnih kombinacija pružit će znanja o poslovnim aktivnostima poput spajanja i preuzimanja kao i ostalim aktivnostima koje su obuhvaćene navedenim procesima. Sukladno tome polaznicima će se prezentirati osnove procjene vrijednosti i metode kojima se ona provodi, Due Diligence proces te završetak same aktivnosti. Poseban naglasak bit će na povezanim osobama i društvima, kao i na kontroli kroz upravljačku moć.',
                ],
                [
                    'title' => 'Poslovne kombinacije – financijski, porezni i pravni aspekti',
                    'text' => 'Edukacija iz područja poslovnih kombinacija pružit će znanja o poslovnim aktivnostima poput spajanja i preuzimanja kao i ostalim aktivnostima koje su obuhvaćene navedenim procesima. Sukladno tome polaznicima će se prezentirati osnove procjene vrijednosti i metode kojima se ona provodi, Due Diligence proces te završetak same aktivnosti. Poseban naglasak bit će na povezanim osobama i društvima, kao i na kontroli kroz upravljačku moć.',
                ],
            ],
        ],
        [
            'title' => 'Porezni seminari',
            'icon' => 'compliance',
            'accent' => 'slate',
            'intro' => 'Edukacija polaznicima pruža jasan uvid u načela i metodologiju transfernih cijena te u osnove poreznog nadzora uz primjenu na konkretnim primjerima iz prakse.',
            'items' => [
                [
                    'title' => 'Transferne cijene',
                    'text' => 'Transfernim cijenama vrednuju se transakcije između povezanih osoba te bi trebale biti u skladu s uobičajenim tržišnim cijenama. Polaznici će se upoznati s načelima i metodologijom transfernih cijena, zahtjevima OECD-ovih Smjernica o transfernim cijenama te s primjenom transfernih cijena na određene specifične transakcije između povezanih društava, uključujući njihovu primjenu u praksi. Na edukaciji će na temelju primjera iz prakse biti prikazan odabir pojedine metode utvrđivanja transfernih cijena, posebnosti transfernih cijena i njihov utjecaj na osnovicu poreza na dobit.',
                ],
                [
                    'title' => 'Porezni nadzor',
                    'text' => 'Edukacija će polaznicima omogućiti uvid u osnove faza poreznog nadzora koji započinje odabirom subjekta za porezni nadzor, zatim slijedi obavijest o istome, zaključno do poreznog rješenja i mogućnosti žalbe na njega. Unutar procesa pregledava se dokumentacija, prikupljaju dodatne informacije, odnosno pojašnjenja ukoliko postoje nejasnoće. Nakon pregleda dokumentacije i prikupljanja dodatnih informacija izrađuje se zapisnik te prigovor. Navedeni segmenti pojasnit će se teoretski, ali i na praktičnim primjerima kako bi se polaznicima što kvalitetnije približio ovaj segment poslovanja.',
                ],
            ],
        ],
    ];
?>

<?php $__env->startSection('title', $translation?->title ?? 'Akademija'); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0 pb-[80px]'); ?>

<?php $__env->startSection('content'); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($topBlocks->isNotEmpty()): ?>
        <section class="mx-auto mb-8 w-full max-w-[1320px] px-4 pt-10 sm:px-6 lg:px-8"><?php echo $__env->make('components.content-placement', ['items' => $topBlocks], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if (isset($component)) { $__componentOriginale6a101278d02d7bbbf9e98ee1142bf75 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.front.page-title-band','data' => ['breadcrumbs' => $pageTitleBreadcrumbs]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('front.page-title-band'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pageTitleBreadcrumbs)]); ?>
        <div class="ac-page-title-copy">
            <h1><?php echo e($translation?->title ?? $page->code); ?></h1>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($translation?->excerpt)): ?>
                <p><?php echo e($translation->excerpt); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75)): ?>
<?php $attributes = $__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75; ?>
<?php unset($__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale6a101278d02d7bbbf9e98ee1142bf75)): ?>
<?php $component = $__componentOriginale6a101278d02d7bbbf9e98ee1142bf75; ?>
<?php unset($__componentOriginale6a101278d02d7bbbf9e98ee1142bf75); ?>
<?php endif; ?>

    <section id="academy-programs" class="ac-academy-programs" aria-labelledby="academy-programs-title">
        <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
            <h2 id="academy-programs-title" class="sr-only">Programi Akademije</h2>

            <div class="ac-academy-program-grid ac-academy-program-grid--tight">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $academyPrograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="ac-academy-program-card ac-academy-program-card--<?php echo e($program['accent']); ?>">
                        <div class="ac-academy-program-card-head">
                            <span class="ac-academy-program-icon" aria-hidden="true">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php switch($program['icon']):
                                    case ('growth'): ?>
                                        <svg class="ac-academy-program-fa" viewBox="0 0 512 512" fill="currentColor" aria-hidden="true">
                                            <use href="<?php echo e(asset('front-theme/fonts/sprites/solid.svg#chart-line')); ?>"></use>
                                        </svg>
                                        <?php break; ?>
                                    <?php case ('insight'): ?>
                                        <svg class="ac-academy-program-fa" viewBox="0 0 640 512" fill="currentColor" aria-hidden="true">
                                            <use href="<?php echo e(asset('front-theme/fonts/sprites/solid.svg#graduation-cap')); ?>"></use>
                                        </svg>
                                        <?php break; ?>
                                    <?php case ('ledger'): ?>
                                        <svg class="ac-academy-program-fa" viewBox="0 0 576 512" fill="currentColor" aria-hidden="true">
                                            <use href="<?php echo e(asset('front-theme/fonts/sprites/solid.svg#book-open')); ?>"></use>
                                        </svg>
                                        <?php break; ?>
                                    <?php default: ?>
                                        <svg class="ac-academy-program-fa" viewBox="0 0 640 512" fill="currentColor" aria-hidden="true">
                                            <use href="<?php echo e(asset('front-theme/fonts/sprites/solid.svg#scale-balanced')); ?>"></use>
                                        </svg>
                                <?php endswitch; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>

                            <div>
                                <h3><?php echo e($program['title']); ?></h3>
                            </div>
                        </div>

                        <p class="ac-academy-program-intro"><?php echo e($program['intro']); ?></p>

                        <div class="ac-academy-topic-list">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $program['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <article class="ac-academy-topic">
                                    <h4><?php echo e($item['title']); ?></h4>
                                    <p><?php echo e($item['text']); ?></p>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($academyBlogPosts->isNotEmpty()): ?>
        <section class="ac-support-story ac-home-blog ac-blog-related-section ac-family-blog-section" aria-labelledby="ac-academy-blog-title">
            <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                <div class="ac-support-story-hero">
                    <div class="ac-support-story-shell">
                        <div class="ac-services-head ac-support-story-head">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($academyBlogSection['title'] ?? '') !== ''): ?>
                                <h2 id="ac-academy-blog-title">
                                    <span><?php echo e($academyBlogSection['title']); ?></span>
                                </h2>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($academyBlogSection['intro'] ?? '') !== ''): ?>
                                <p class="ac-services-intro"><?php echo e($academyBlogSection['intro']); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="ac-services-divider" aria-hidden="true">
                                <span class="ac-services-divider-line"></span>
                                <span class="ac-services-divider-glyph"></span>
                                <span class="ac-services-divider-line"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ac-home-blog-carousel ac-blog-related-content">
                    <div class="ac-blog-grid ac-blog-grid-related">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $academyBlogPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('front.desktop.blog.partials.card', [
                                'post' => $post,
                                'locale' => $locale,
                                'fallbackLocale' => $fallbackLocale,
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($academyResourceDocuments->isNotEmpty()): ?>
        <section class="ac-academy-resources-section" aria-labelledby="ac-academy-resources-title">
            <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                <div class="ac-support-story-hero">
                    <div class="ac-support-story-shell">
                        <div class="ac-services-head ac-support-story-head">
                            <h2 id="ac-academy-resources-title">
                                <span><?php echo e($academyResourceSection['title']); ?></span>
                            </h2>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($academyResourceSection['intro'] ?? '') !== ''): ?>
                                <p class="ac-services-intro"><?php echo e($academyResourceSection['intro']); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="ac-services-divider" aria-hidden="true">
                                <span class="ac-services-divider-line"></span>
                                <span class="ac-services-divider-glyph"></span>
                                <span class="ac-services-divider-line"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ac-academy-resources-carousel">
                    <div id="ac-academy-resources-splide" class="splide ac-academy-resources-splide" data-academy-resources-splide>
                        <div class="splide__track">
                            <ul class="splide__list">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $academyResourceDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $documentUrl = route('resources.show', ['slug' => $document['slug']]);
                                        $documentTitle = trim((string) ($document['title'] ?? ''));
                                    ?>
                                    <li class="splide__slide ac-academy-resource-slide">
                                        <article class="ac-academy-resource-card group">
                                            <a href="<?php echo e($documentUrl); ?>" class="ac-academy-resource-card-link" aria-label="<?php echo e($academyResourceCtaLabel); ?>: <?php echo e($documentTitle); ?>">
                                                <div class="ac-academy-resource-card-media">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($document['cover_image_url'])): ?>
                                                        <img
                                                            src="<?php echo e($document['cover_image_url']); ?>"
                                                            alt="<?php echo e($documentTitle); ?>"
                                                            class="ac-academy-resource-card-image"
                                                            loading="lazy"
                                                            decoding="async"
                                                        >
                                                    <?php else: ?>
                                                        <div class="ac-academy-resource-card-fallback ac-academy-resource-card-fallback--<?php echo e($document['group_code']); ?>">
                                                            <span class="ac-academy-resource-card-badge"><?php echo e($document['group_label']); ?></span>
                                                            <h3><?php echo e($documentTitle); ?></h3>
                                                        </div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>

                                                <div class="ac-academy-resource-card-body">
                                                    <h3><?php echo e($documentTitle); ?></h3>
                                                    <span class="ac-academy-resource-card-cta">
                                                        <span><?php echo e($academyResourceCtaLabel); ?></span>
                                                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                            <path d="M4 12L12 4"></path>
                                                            <path d="M6 4h6v6"></path>
                                                        </svg>
                                                    </span>
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

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($academyVideos->isNotEmpty()): ?>
        <section class="ac-support-story ac-home-blog ac-blog-related-section ac-family-blog-section ac-academy-videos-section" aria-labelledby="ac-academy-videos-title">
            <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                <div class="ac-support-story-hero">
                    <div class="ac-support-story-shell">
                        <div class="ac-services-head ac-support-story-head">
                            <h2 id="ac-academy-videos-title">
                                <span><?php echo e($academyVideoSection['title']); ?></span>
                            </h2>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($academyVideoSection['intro'] ?? '') !== ''): ?>
                                <p class="ac-services-intro"><?php echo e($academyVideoSection['intro']); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="ac-services-divider" aria-hidden="true">
                                <span class="ac-services-divider-line"></span>
                                <span class="ac-services-divider-glyph"></span>
                                <span class="ac-services-divider-line"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ac-academy-video-grid" data-academy-video-grid>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $academyVideos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article
                            class="ac-academy-video-card"
                            <?php if($academyVideoHasOverflow && $index >= $academyVideoInitialCount): ?>
                                hidden
                                data-academy-video-hidden
                            <?php endif; ?>
                        >
                            <div class="ac-academy-video-frame-wrap">
                                <iframe
                                    src="<?php echo e($video['embed_url']); ?>"
                                    title="<?php echo e($video['title'] !== '' ? $video['title'] : $academyVideoSection['title']); ?>"
                                    loading="lazy"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    referrerpolicy="strict-origin-when-cross-origin"
                                    allowfullscreen
                                ></iframe>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($video['title'] ?? '') !== ''): ?>
                                <div class="ac-academy-video-card-body">
                                    <h3><?php echo e($video['title']); ?></h3>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($academyVideoHasOverflow): ?>
                    <div class="ac-academy-video-actions" data-academy-video-actions>
                        <button type="button" class="front-action-cta ac-academy-video-more-button" data-academy-video-show-more>
                            <?php echo e($academyVideoShowMoreLabel); ?>

                        </button>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bottomBlocks->isNotEmpty()): ?>
        <section class="mx-auto mt-4 w-full max-w-[1320px] px-4 pb-10 sm:px-6 lg:px-8"><?php echo $__env->make('components.content-placement', ['items' => $bottomBlocks], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .ac-academy-programs {
            padding: 2.25rem 0 4.5rem;
            background:
                linear-gradient(180deg, #f6f1e7 0%, #fbfaf7 18%, #ffffff 100%);
        }

        .ac-academy-program-grid {
            display: grid;
            gap: 1.35rem;
        }

        .ac-academy-program-grid--tight {
            margin-top: 0;
        }

        .ac-academy-program-card {
            position: relative;
            overflow: hidden;
            padding: 1.5rem;
            border: 1px solid rgba(15, 27, 45, 0.08);
            border-radius: var(--front-card-radius);
            background: #ffffff;
            box-shadow: 0 22px 44px rgba(15, 27, 45, 0.06);
        }

        .ac-academy-program-card::before {
            content: "";
            position: absolute;
            inset: 0 0 auto;
            height: 0.28rem;
            background: linear-gradient(90deg, rgba(15, 27, 45, 0.96), rgba(209, 175, 112, 0.82));
        }

        .ac-academy-program-card--blue::before {
            background: linear-gradient(90deg, #0f1b2d, #2b7ba6);
        }

        .ac-academy-program-card--sand::before {
            background: linear-gradient(90deg, #0f1b2d, #bc9150);
        }

        .ac-academy-program-card--slate::before {
            background: linear-gradient(90deg, #23364d, #5f738c);
        }

        .ac-academy-program-card-head {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 1rem;
            align-items: start;
        }

        .ac-academy-program-icon {
            display: inline-flex;
            width: 3.4rem;
            height: 3.4rem;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
            color: #fff;
            background: linear-gradient(180deg, #0f1b2d 0%, #123250 100%);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12);
        }

        .ac-academy-program-icon svg {
            width: 1.5rem;
            height: 1.5rem;
        }

        .ac-academy-program-fa {
            width: 1.5rem;
            height: 1.5rem;
            display: block;
        }

        .ac-academy-program-card h3 {
            margin: 0;
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.45rem, 2vw, 1.95rem);
            font-weight: 600;
            line-height: 1.08;
            color: #0f1b2d;
            text-wrap: balance;
        }

        .ac-academy-program-intro {
            margin: 1.05rem 0 0;
            font-size: 0.98rem;
            line-height: 1.78;
            color: #3a4758;
        }

        .ac-academy-topic-list {
            display: grid;
            gap: 0.9rem;
            margin-top: 1.3rem;
        }

        .ac-academy-topic {
            padding: 1rem 1rem 1.05rem;
            border: 1px solid rgba(15, 27, 45, 0.08);
            border-radius: var(--front-card-radius);
            background: linear-gradient(180deg, rgba(248, 243, 232, 0.52), rgba(255, 255, 255, 0.9));
        }

        .ac-academy-topic h4 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.4;
            color: #0f1b2d;
        }

        .ac-academy-topic p {
            margin: 0.45rem 0 0;
            font-size: 0.94rem;
            line-height: 1.72;
            color: #526172;
        }

        .ac-academy-resources-section {
            padding: 0 0 4.75rem;
            background:
                linear-gradient(180deg, #ffffff 0%, #faf7f2 100%);
        }

        .ac-academy-resources-carousel {
            position: relative;
            margin-top: 2rem;
        }

        .ac-academy-resources-splide .splide__track {
            padding: 0.35rem;
        }

        .ac-academy-resource-slide {
            height: auto;
        }

        .ac-academy-resource-card {
            height: 100%;
            border: 1px solid rgba(212, 191, 155, 0.55);
            border-radius: var(--front-card-radius);
            background: #ffffff;
            overflow: hidden;
        }

        .ac-academy-resource-card-link {
            display: flex;
            flex-direction: column;
            height: 100%;
            color: inherit;
            text-decoration: none;
        }

        .ac-academy-resource-card-media {
            position: relative;
            aspect-ratio: 0.74;
            overflow: hidden;
            background: #e8edf2;
        }

        .ac-academy-resource-card-image {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center top;
            transition: transform 320ms ease;
        }

        .ac-academy-resource-card:hover .ac-academy-resource-card-image {
            transform: scale(1.02);
        }

        .ac-academy-resource-card-fallback {
            position: relative;
            display: flex;
            height: 100%;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.65rem;
            color: #fffdf8;
            background: linear-gradient(160deg, #0f1b2d 0%, #183a62 55%, #c4934f 100%);
        }

        .ac-academy-resource-card-fallback::before,
        .ac-academy-resource-card-fallback::after {
            content: "";
            position: absolute;
            border-radius: 9999px;
            pointer-events: none;
        }

        .ac-academy-resource-card-fallback::before {
            width: 11rem;
            height: 11rem;
            right: -3rem;
            top: -3rem;
            background: rgba(255, 255, 255, 0.1);
            filter: blur(8px);
        }

        .ac-academy-resource-card-fallback::after {
            width: 8rem;
            height: 8rem;
            left: -1.5rem;
            bottom: -2rem;
            background: rgba(245, 204, 124, 0.18);
            filter: blur(12px);
        }

        .ac-academy-resource-card-fallback--transaction-analysis {
            background: linear-gradient(160deg, #102542 0%, #0f766e 56%, #f6b93a 100%);
        }

        .ac-academy-resource-card-fallback--sector-analysis {
            background: linear-gradient(160deg, #111827 0%, #1d4ed8 55%, #f59e0b 100%);
        }

        .ac-academy-resource-card-badge {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-self: flex-start;
            padding: 0.5rem 0.85rem;
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.08);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .ac-academy-resource-card-fallback h3 {
            position: relative;
            z-index: 1;
            margin: 0;
            max-width: 14rem;
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.45rem, 2vw, 1.9rem);
            line-height: 1.06;
            text-wrap: balance;
        }

        .ac-academy-resource-card-body {
            display: flex;
            flex: 1;
            flex-direction: column;
            justify-content: space-between;
            gap: 1.4rem;
            padding: 1.35rem 1.35rem 1.45rem;
        }

        .ac-academy-resource-card-body h3 {
            margin: 0;
            font-size: 1.16rem;
            font-weight: 700;
            line-height: 1.42;
            color: #0f172a;
        }

        .ac-academy-resource-card-cta {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            gap: 0.55rem;
            padding: 0.78rem 1.15rem;
            border: 1px solid rgba(191, 204, 219, 0.95);
            border-radius: var(--front-button-radius);
            color: #173b5d;
            font-size: 0.95rem;
            font-weight: 700;
            transition: transform 180ms ease, border-color 180ms ease, color 180ms ease, box-shadow 180ms ease;
        }

        .ac-academy-resource-card-cta svg {
            width: 1rem;
            height: 1rem;
        }

        .ac-academy-resource-card:hover .ac-academy-resource-card-cta,
        .ac-academy-resource-card:focus-within .ac-academy-resource-card-cta {
            transform: translateY(-1px);
            border-color: rgba(23, 59, 93, 0.45);
            color: #0f2c47;
            box-shadow: 0 16px 32px -28px rgba(15, 27, 45, 0.5);
        }

        .ac-academy-resources-splide .splide__arrow {
            width: 2.85rem;
            height: 2.85rem;
            opacity: 0;
            transform: translateY(-50%) scale(0.94);
            transition: opacity 0.2s ease, transform 0.2s ease, background-color 0.2s ease;
            background: rgba(15, 27, 45, 0.92);
            border: 1px solid rgba(255, 255, 255, 0.16);
        }

        .ac-academy-resources-splide .splide__arrow svg {
            fill: #fff;
        }

        .ac-academy-resources-splide .splide__arrow:hover {
            background: #102b46;
        }

        .ac-academy-resources-splide .splide__arrow--prev {
            left: -1.15rem;
        }

        .ac-academy-resources-splide .splide__arrow--next {
            right: -1.15rem;
        }

        .ac-academy-resources-carousel:hover .splide__arrow,
        .ac-academy-resources-carousel:focus-within .splide__arrow {
            opacity: 1;
            transform: translateY(-50%) scale(1);
        }

        .ac-academy-resources-splide .splide__pagination {
            bottom: -1.75rem;
        }

        .ac-academy-resources-splide .splide__pagination__page {
            width: 0.62rem;
            height: 0.62rem;
            margin: 0 0.28rem;
            background: rgba(15, 27, 45, 0.18);
            opacity: 1;
        }

        .ac-academy-resources-splide .splide__pagination__page.is-active {
            background: #173b5d;
            transform: scale(1.18);
        }

        .ac-academy-videos-section {
            padding-top: 4.9rem;
            padding-bottom: 5.1rem;
        }

        .ac-academy-video-grid {
            display: grid;
            gap: 1.4rem;
            margin-top: 2rem;
        }

        .ac-academy-video-card {
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: var(--front-card-radius);
            background: rgba(255, 255, 255, 0.92);
        }

        .ac-academy-video-frame-wrap {
            position: relative;
            aspect-ratio: 16 / 9;
            background: #0f1b2d;
        }

        .ac-academy-video-frame-wrap iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .ac-academy-video-card-body {
            padding: 1.15rem 1.2rem 1.3rem;
        }

        .ac-academy-video-card-body h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.5;
            color: #112033;
        }

        .ac-academy-video-actions {
            display: flex;
            justify-content: center;
            margin-top: 1.75rem;
        }

        .ac-academy-video-more-button {
            min-width: 10.5rem;
            min-height: 3rem;
            padding: 0 1.35rem;
        }

        @media (min-width: 768px) {
            .ac-academy-program-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .ac-academy-video-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1024px) {
            .ac-academy-program-card {
                padding: 1.7rem;
            }
        }

        @media (max-width: 767px) {
            .ac-academy-programs {
                padding: 2rem 0 4rem;
            }

            .ac-academy-resources-section {
                padding-bottom: 4.25rem;
            }

            .ac-academy-videos-section {
                padding-top: 4.15rem;
                padding-bottom: 4.3rem;
            }

            .ac-academy-program-card {
                padding: 1.2rem;
            }

            .ac-academy-program-card-head {
                gap: 0.85rem;
            }

            .ac-academy-program-icon {
                width: 3rem;
                height: 3rem;
                border-radius: 0.95rem;
            }

            .ac-academy-topic {
                padding: 0.9rem 0.9rem 1rem;
            }

            .ac-academy-resource-card-body {
                padding: 1.15rem 1.15rem 1.25rem;
            }

            .ac-academy-resource-card-body h3 {
                font-size: 1.05rem;
            }

            .ac-academy-video-card-body {
                padding: 1rem 1rem 1.15rem;
            }

            .ac-academy-resources-splide .splide__arrow {
                display: none;
            }

            .ac-academy-resources-splide .splide__track {
                padding-inline: 0;
            }
        }

        @media (hover: none) {
            .ac-academy-resources-splide .splide__arrow {
                opacity: 1;
                transform: translateY(-50%) scale(1);
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php if (! $__env->hasRenderedOnce('373098a4-19a8-40d6-8c61-b3949596ff4c')): $__env->markAsRenderedOnce('373098a4-19a8-40d6-8c61-b3949596ff4c'); ?>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php if (! $__env->hasRenderedOnce('9ac8454f-18df-4bc9-9d0f-a47f2e77d5b5')): $__env->markAsRenderedOnce('9ac8454f-18df-4bc9-9d0f-a47f2e77d5b5'); ?>
    <?php $__env->startPush('scripts'); ?>
        <script defer src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php if (! $__env->hasRenderedOnce('b701f649-225c-41a3-aa04-8fb33a076ee7')): $__env->markAsRenderedOnce('b701f649-225c-41a3-aa04-8fb33a076ee7'); ?>
    <?php $__env->startPush('scripts'); ?>
        <script>
            (function () {
                const init = function () {
                    if (typeof window.Splide !== 'function') {
                        return false;
                    }

                    const sliders = document.querySelectorAll('[data-academy-resources-splide]');
                    sliders.forEach(function (el) {
                        if (el.dataset.splideReady === '1') {
                            return;
                        }

                        el.dataset.splideReady = '1';

                        const count = el.querySelectorAll('.splide__slide').length;
                        new window.Splide(el, {
                            type: count > 4 ? 'loop' : 'slide',
                            rewind: count <= 4,
                            perPage: Math.min(4, Math.max(1, count)),
                            perMove: 1,
                            gap: '1.15rem',
                            drag: count > 1,
                            snap: true,
                            pagination: count > 1,
                            arrows: count > 1,
                            updateOnMove: true,
                            speed: 520,
                            breakpoints: {
                                1200: { perPage: Math.min(3, Math.max(1, count)) },
                                900: { perPage: Math.min(2, Math.max(1, count)), gap: '1rem' },
                                640: { perPage: 1, gap: '0.9rem' },
                            },
                        }).mount();
                    });

                    return true;
                };

                if (init()) {
                    return;
                }

                let attempts = 0;
                const timer = window.setInterval(function () {
                    attempts += 1;
                    if (init() || attempts > 40) {
                        window.clearInterval(timer);
                    }
                }, 120);
            })();
        </script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php if (! $__env->hasRenderedOnce('c6c5acf9-1c9d-4a75-bcc5-8097a5cf6663')): $__env->markAsRenderedOnce('c6c5acf9-1c9d-4a75-bcc5-8097a5cf6663'); ?>
    <?php $__env->startPush('scripts'); ?>
        <script>
            (function () {
                const init = function () {
                    const sections = document.querySelectorAll('[data-academy-video-grid]');

                    sections.forEach(function (grid) {
                        if (grid.dataset.videoShowMoreReady === '1') {
                            return;
                        }

                        const button = grid.parentElement?.querySelector('[data-academy-video-show-more]');
                        const actions = grid.parentElement?.querySelector('[data-academy-video-actions]');
                        if (!button) {
                            grid.dataset.videoShowMoreReady = '1';
                            return;
                        }

                        grid.dataset.videoShowMoreReady = '1';

                        button.addEventListener('click', function () {
                            grid.querySelectorAll('[data-academy-video-hidden]').forEach(function (card) {
                                card.hidden = false;
                                card.removeAttribute('data-academy-video-hidden');
                            });

                            if (actions) {
                                actions.hidden = true;
                            }
                        }, { once: true });
                    });
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', init, { once: true });
                    return;
                }

                init();
            })();
        </script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/pages/academy.blade.php ENDPATH**/ ?>