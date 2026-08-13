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
    $academyTestimonials = $academyTestimonials ?? collect();
    $academyGalleryItems = $academyGalleryItems ?? collect();
    $academyVideoInitialCount = 4;
    $academyVideoHasOverflow = $academyVideos->count() > $academyVideoInitialCount;
    $academyVideoShowMoreLabel = $locale === 'hr' ? 'Pokaži još' : 'Show more';
    $academyResourceCtaLabel = $locale === 'hr' ? 'Preuzmi' : 'Download';
    $academyVideoActivateLabel = $locale === 'hr' ? 'Pokreni video' : 'Play video';
    $academyTestimonialReadMoreLabel = $locale === 'hr' ? 'Pročitaj više' : 'Read more';
    $academyTestimonialShowLessLabel = $locale === 'hr' ? 'Prikaži manje' : 'Show less';
    $academyExperienceSection = [
        'eyebrow' => $locale === 'hr' ? 'Iskustva polaznika' : 'Participant feedback',
        'title' => $locale === 'hr' ? 'Iskustva polaznika akademije' : 'Academy participant experiences',
        'intro' => $locale === 'hr'
            ? 'Komentari polaznika i djelić atmosfere s edukacija.'
            : 'Participant feedback and a glimpse of the academy sessions.',
    ];
    $academyExperienceLayoutClass = $academyTestimonials->isNotEmpty() && $academyGalleryItems->isNotEmpty()
        ? 'ac-academy-experience-layout--split'
        : 'ac-academy-experience-layout--single';
    $academyPrograms = $academyPrograms
        ?? \App\Support\Content\AcademyPageDefaults::mergePrograms(data_get($translation?->payload, 'academy_programs'));
?>

<?php $__env->startSection('title', $translation?->title ?? 'Akademija'); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

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
                            <div class="ac-academy-video-frame-wrap" data-academy-video-frame>
                                <iframe
                                    data-academy-video-iframe
                                    data-base-src="<?php echo e($video['embed_url']); ?>"
                                    src="<?php echo e($video['embed_url']); ?>"
                                    title="<?php echo e($video['title'] !== '' ? $video['title'] : $academyVideoSection['title']); ?>"
                                    loading="lazy"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    referrerpolicy="strict-origin-when-cross-origin"
                                    allowfullscreen
                                ></iframe>

                                <button
                                    type="button"
                                    class="ac-academy-video-poster"
                                    data-academy-video-activate
                                    aria-label="<?php echo e($academyVideoActivateLabel); ?>: <?php echo e($video['title'] !== '' ? $video['title'] : $academyVideoSection['title']); ?>"
                                >
                                    <span class="ac-academy-video-poster-media" aria-hidden="true">
                                        <img src="<?php echo e($video['poster_url']); ?>" alt="" loading="lazy">
                                    </span>
                                    <span class="ac-academy-video-poster-shade" aria-hidden="true"></span>
                                    <span class="ac-academy-video-poster-play" aria-hidden="true">
                                        <svg viewBox="0 0 384 512" fill="currentColor" focusable="false" aria-hidden="true">
                                            <path d="M73 39c-14.8-9.1-33.4-9.4-48.5-.9S0 62.5 0 80v352c0 17.5 9.4 33.8 24.5 42.9s33.7 8.2 48.5-.9l288-176c14.7-9 23-25 23-42.3s-8.3-33.4-23-42.3L73 39z"></path>
                                        </svg>
                                    </span>
                                </button>
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

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($academyTestimonials->isNotEmpty() || $academyGalleryItems->isNotEmpty()): ?>
        <section class="ac-academy-experiences-section" aria-labelledby="ac-academy-experiences-title">
            <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                <div class="ac-support-story-hero">
                    <div class="ac-support-story-shell">
                        <div class="ac-services-head ac-support-story-head">
                            <div class="ac-services-eyebrow">
                                <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                                <p class="ac-services-kicker"><?php echo e($academyExperienceSection['eyebrow']); ?></p>
                                <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                            </div>
                            <h2 id="ac-academy-experiences-title">
                                <span><?php echo e($academyExperienceSection['title']); ?></span>
                            </h2>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($academyExperienceSection['intro'] ?? '') !== ''): ?>
                                <p class="ac-services-intro"><?php echo e($academyExperienceSection['intro']); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="ac-services-divider" aria-hidden="true">
                                <span class="ac-services-divider-line"></span>
                                <span class="ac-services-divider-glyph"></span>
                                <span class="ac-services-divider-line"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ac-academy-experience-layout <?php echo e($academyExperienceLayoutClass); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($academyTestimonials->isNotEmpty()): ?>
                        <div class="ac-academy-experience-column ac-academy-experience-column--quotes">
                            <div class="ac-client-experiences-carousel ac-academy-testimonials-carousel">
                                <div id="ac-academy-testimonials-splide" class="splide ac-academy-testimonials-splide" data-academy-testimonials-splide>
                                    <div class="splide__track">
                                        <ul class="splide__list ac-client-experiences-list">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $academyTestimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testimonial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $company = trim((string) ($testimonial->payload['company'] ?? ''));
                                                    $rating = max(1, min(5, (int) ($testimonial->rating ?? 5)));
                                                ?>
                                                <li class="splide__slide ac-client-experiences-slide">
                                                    <article class="ac-client-experience-card" data-academy-testimonial-card>
                                                        <div class="ac-client-experience-card-inner">
                                                            <div class="ac-client-experience-quote-mark" aria-hidden="true">“</div>
                                                            <div class="ac-client-experience-content">
                                                                <div class="ac-client-experience-rating" aria-label="<?php echo e($rating); ?> / 5">
                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 1; $i <= 5; $i++): ?>
                                                                        <span class="<?php echo e($i <= $rating ? 'is-active' : ''); ?>">★</span>
                                                                    <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                </div>
                                                                <p class="ac-client-experience-body" data-academy-testimonial-body><?php echo e($testimonial->body); ?></p>
                                                                <button
                                                                    type="button"
                                                                    class="ac-client-experience-toggle"
                                                                    data-academy-testimonial-toggle
                                                                    data-more-label="<?php echo e($academyTestimonialReadMoreLabel); ?>"
                                                                    data-less-label="<?php echo e($academyTestimonialShowLessLabel); ?>"
                                                                    aria-expanded="false"
                                                                    hidden
                                                                ><?php echo e($academyTestimonialReadMoreLabel); ?></button>
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
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($academyGalleryItems->isNotEmpty()): ?>
                        <div class="ac-academy-experience-column ac-academy-experience-column--gallery" data-academy-gallery>
                            <div class="ac-academy-gallery-carousel">
                                <div id="ac-academy-gallery-splide" class="splide ac-academy-gallery-splide" data-academy-gallery-splide>
                                    <div class="splide__track">
                                        <ul class="splide__list">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $academyGalleryItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $galleryItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li class="splide__slide ac-academy-gallery-slide">
                                                    <a
                                                        href="<?php echo e($galleryItem['full_url']); ?>"
                                                        target="_blank"
                                                        rel="noopener"
                                                        class="ac-academy-gallery-link"
                                                        data-academy-gallery-item
                                                        data-sub-html="<?php echo e($galleryItem['alt']); ?>"
                                                        aria-label="<?php echo e($galleryItem['alt']); ?>"
                                                    >
                                                        <span class="ac-academy-gallery-image-wrap">
                                                            <img
                                                                src="<?php echo e($galleryItem['image_url']); ?>"
                                                                alt="<?php echo e($galleryItem['alt']); ?>"
                                                                class="ac-academy-gallery-image"
                                                                loading="lazy"
                                                                decoding="async"
                                                            >
                                                        </span>
                                                    </a>
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
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
            font-family: "Instrument Sans Variable", Arial, sans-serif;
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
            font-family: "Instrument Sans Variable", Arial, sans-serif;
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
            overflow: hidden;
            background: #0f1b2d;
        }

        .ac-academy-video-frame-wrap iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .ac-academy-video-poster {
            position: absolute;
            inset: 0;
            display: block;
            width: 100%;
            height: 100%;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
            z-index: 2;
            transition: opacity 0.24s ease, visibility 0.24s ease;
        }

        .ac-academy-video-poster-media,
        .ac-academy-video-poster-media img {
            display: block;
            width: 100%;
            height: 100%;
        }

        .ac-academy-video-poster-media img {
            object-fit: cover;
        }

        .ac-academy-video-poster-shade {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(15, 27, 45, 0.18) 0%, rgba(15, 27, 45, 0.34) 100%);
        }

        .ac-academy-video-poster-play {
            position: absolute;
            top: 50%;
            left: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: clamp(3.7rem, 10vw, 4.9rem);
            height: clamp(3.7rem, 10vw, 4.9rem);
            border-radius: 999px;
            color: #0f1b2d;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 18px 36px rgba(15, 27, 45, 0.24);
            transform: translate(-50%, -50%);
            transition: transform 0.22s ease, box-shadow 0.22s ease;
        }

        .ac-academy-video-poster-play svg {
            width: 1.1rem;
            height: 1.1rem;
            margin-left: 0.16rem;
            display: block;
        }

        .ac-academy-video-poster:hover .ac-academy-video-poster-play,
        .ac-academy-video-poster:focus-visible .ac-academy-video-poster-play {
            transform: translate(-50%, -50%) scale(1.05);
            box-shadow: 0 20px 42px rgba(15, 27, 45, 0.28);
        }

        .ac-academy-video-poster:focus-visible {
            outline: 3px solid rgba(255, 255, 255, 0.92);
            outline-offset: -3px;
        }

        .ac-academy-video-frame-wrap.is-active .ac-academy-video-poster {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
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

        .ac-academy-experiences-section {
            padding: 0 0 8rem;
            background:
                linear-gradient(180deg, #ffffff 0%, #f7f3eb 100%);
        }

        .ac-academy-experience-layout {
            display: grid;
            gap: 1.35rem;
            margin-top: 2rem;
        }

        .ac-academy-experience-layout--single {
            grid-template-columns: minmax(0, 1fr);
        }

        .ac-academy-experience-column {
            min-width: 0;
        }

        .ac-academy-experience-column--quotes {
            display: flex;
            aspect-ratio: 1 / 1;
            min-height: 0;
        }

        .ac-academy-experience-column--gallery {
            aspect-ratio: 1 / 1;
            min-height: 0;
        }

        .ac-academy-testimonials-carousel,
        .ac-academy-gallery-carousel,
        .ac-academy-testimonials-splide,
        .ac-academy-gallery-splide {
            width: 100%;
            height: 100%;
        }

        .ac-academy-testimonials-splide .splide__track,
        .ac-academy-gallery-splide .splide__track,
        .ac-academy-testimonials-splide .splide__list,
        .ac-academy-gallery-splide .splide__list {
            height: 100%;
        }

        .ac-academy-gallery-splide .splide__track {
            overflow: hidden;
            border-radius: calc(var(--front-card-radius) + 0.35rem);
            transform: translateZ(0);
            -webkit-mask-image: -webkit-radial-gradient(white, black);
        }

        .ac-academy-gallery-splide .splide__list,
        .ac-academy-gallery-splide .splide__slide {
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            transform: translateZ(0);
        }

        .ac-academy-testimonials-splide .splide__slide,
        .ac-academy-gallery-splide .splide__slide {
            height: 100%;
        }

        .ac-academy-gallery-splide .splide__slide {
            overflow: hidden;
        }

        .ac-academy-testimonials-splide .ac-client-experience-card {
            height: 100%;
        }

        .ac-academy-testimonials-splide .ac-client-experience-card-inner {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 100%;
            height: 100%;
        }

        .ac-academy-testimonials-splide .ac-client-experience-content {
            justify-content: center;
        }

        .ac-academy-testimonials-splide .ac-client-experience-meta {
            margin-top: 1.4rem;
        }

        .ac-academy-gallery-link {
            display: block;
            height: 100%;
            text-decoration: none;
            overflow: hidden;
            border-radius: calc(var(--front-card-radius) + 0.35rem);
            transform: translateZ(0);
        }

        .ac-academy-gallery-image-wrap {
            display: block;
            width: 100%;
            height: 100%;
            overflow: hidden;
            border-radius: calc(var(--front-card-radius) + 0.35rem);
            aspect-ratio: 1 / 1;
            background: linear-gradient(180deg, #dfe6ee 0%, #eef2f6 100%);
            box-shadow: 0 22px 46px rgba(15, 27, 45, 0.12);
            isolation: isolate;
            transform: translateZ(0);
            -webkit-mask-image: -webkit-radial-gradient(white, black);
        }

        .ac-academy-gallery-image {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: inherit;
            transition: transform 0.45s ease;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            transform: translateZ(0) scale(1.001);
        }

        .ac-academy-gallery-link:hover .ac-academy-gallery-image,
        .ac-academy-gallery-link:focus-visible .ac-academy-gallery-image {
            transform: scale(1.03);
        }

        .ac-academy-testimonials-splide .splide__arrow,
        .ac-academy-gallery-splide .splide__arrow {
            width: 2.85rem;
            height: 2.85rem;
            border: 1px solid rgba(15, 27, 45, 0.12);
            background: rgba(255, 255, 255, 0.96);
            color: #0f1b2d;
            box-shadow: 0 12px 24px rgba(15, 27, 45, 0.12);
            opacity: 0;
            pointer-events: none;
            transform: translateY(-50%) scale(0.92);
            transition: opacity 0.22s ease, transform 0.22s ease, background-color 0.22s ease, color 0.22s ease;
        }

        .ac-academy-testimonials-splide .splide__arrow svg,
        .ac-academy-gallery-splide .splide__arrow svg {
            fill: none;
            stroke: currentColor;
            stroke-width: 2.25;
        }

        .ac-academy-testimonials-splide:hover .splide__arrow,
        .ac-academy-gallery-splide:hover .splide__arrow,
        .ac-academy-testimonials-splide:focus-within .splide__arrow,
        .ac-academy-gallery-splide:focus-within .splide__arrow {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(-50%) scale(1);
        }

        .ac-academy-testimonials-splide .splide__arrow:hover,
        .ac-academy-gallery-splide .splide__arrow:hover {
            background: #0f1b2d;
            color: #fff;
        }

        .ac-academy-testimonials-splide .splide__arrow--prev,
        .ac-academy-gallery-splide .splide__arrow--prev {
            left: -1.05rem;
        }

        .ac-academy-testimonials-splide .splide__arrow--next,
        .ac-academy-gallery-splide .splide__arrow--next {
            right: -1.05rem;
        }

        .ac-academy-testimonials-splide .splide__pagination,
        .ac-academy-gallery-splide .splide__pagination {
            display: none;
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
            .ac-academy-experience-layout--split {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                align-items: stretch;
            }

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

            .ac-academy-experiences-section {
                padding-bottom: 6rem;
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

            .ac-academy-video-poster-play {
                width: 3.6rem;
                height: 3.6rem;
            }

            .ac-academy-video-poster-play svg {
                width: 1rem;
                height: 1rem;
            }

            .ac-academy-resources-splide .splide__arrow,
            .ac-academy-testimonials-splide .splide__arrow,
            .ac-academy-gallery-splide .splide__arrow {
                display: none;
            }

            .ac-academy-resources-splide .splide__track {
                padding-inline: 0;
            }
        }

        @media (hover: none) {
            .ac-academy-resources-splide .splide__arrow,
            .ac-academy-testimonials-splide .splide__arrow,
            .ac-academy-gallery-splide .splide__arrow {
                opacity: 1;
                transform: translateY(-50%) scale(1);
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php if (! $__env->hasRenderedOnce('ea041d6a-5311-4dfd-8858-fc0e6c6a1b74')): $__env->markAsRenderedOnce('ea041d6a-5311-4dfd-8858-fc0e6c6a1b74'); ?>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php if (! $__env->hasRenderedOnce('6e1e3886-6083-421a-af05-760abd4038bb')): $__env->markAsRenderedOnce('6e1e3886-6083-421a-af05-760abd4038bb'); ?>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/css/lightgallery-bundle.min.css">
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php if (! $__env->hasRenderedOnce('7c8850f3-de1f-49f8-ac3d-db02dab1f398')): $__env->markAsRenderedOnce('7c8850f3-de1f-49f8-ac3d-db02dab1f398'); ?>
    <?php $__env->startPush('scripts'); ?>
        <script defer src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php if (! $__env->hasRenderedOnce('a45bae33-e2e3-441d-8632-b3fd84e0aafd')): $__env->markAsRenderedOnce('a45bae33-e2e3-441d-8632-b3fd84e0aafd'); ?>
    <?php $__env->startPush('scripts'); ?>
        <script defer src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.min.js"></script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php if (! $__env->hasRenderedOnce('6077b213-fc3d-4735-bc22-1143c4b9d70c')): $__env->markAsRenderedOnce('6077b213-fc3d-4735-bc22-1143c4b9d70c'); ?>
    <?php $__env->startPush('scripts'); ?>
        <script>
            (function () {
                const syncAcademyTestimonialToggles = function () {
                    document.querySelectorAll('[data-academy-testimonial-card]').forEach(function (card) {
                        const body = card.querySelector('[data-academy-testimonial-body]');
                        const toggle = card.querySelector('[data-academy-testimonial-toggle]');

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

                document.addEventListener('click', function (event) {
                    const toggle = event.target.closest('[data-academy-testimonial-toggle]');

                    if (!toggle) {
                        return;
                    }

                    const card = toggle.closest('[data-academy-testimonial-card]');

                    if (!card) {
                        return;
                    }

                    const isExpanded = card.classList.toggle('is-expanded');
                    toggle.textContent = isExpanded
                        ? (toggle.dataset.lessLabel || 'Show less')
                        : (toggle.dataset.moreLabel || 'Read more');
                    toggle.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');

                    window.requestAnimationFrame(syncAcademyTestimonialToggles);
                });

                let academyTestimonialResizeFrame = null;
                window.addEventListener('resize', function () {
                    if (academyTestimonialResizeFrame !== null) {
                        window.cancelAnimationFrame(academyTestimonialResizeFrame);
                    }

                    academyTestimonialResizeFrame = window.requestAnimationFrame(function () {
                        academyTestimonialResizeFrame = null;
                        syncAcademyTestimonialToggles();
                    });
                });

                const init = function () {
                    if (typeof window.Splide !== 'function') {
                        return false;
                    }

                    const mountSlider = function (selector, optionsFactory, onMount) {
                        document.querySelectorAll(selector).forEach(function (el) {
                            if (el.dataset.splideReady === '1') {
                                return;
                            }

                            el.dataset.splideReady = '1';

                            const count = el.querySelectorAll('.splide__slide').length;
                            const slider = new window.Splide(el, optionsFactory(count));
                            slider.mount();

                            if (typeof onMount === 'function') {
                                window.requestAnimationFrame(onMount);
                            }
                        });
                    };

                    mountSlider('[data-academy-resources-splide]', function (count) {
                        return {
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
                        };
                    });

                    mountSlider('[data-academy-testimonials-splide]', function (count) {
                        return {
                            type: count > 1 ? 'loop' : 'slide',
                            rewind: count <= 1,
                            perPage: 1,
                            perMove: 1,
                            gap: '1rem',
                            drag: count > 1,
                            snap: true,
                            pagination: false,
                            arrows: count > 1,
                            autoplay: count > 1,
                            interval: 6000,
                            pauseOnHover: true,
                            pauseOnFocus: true,
                            autoHeight: true,
                            updateOnMove: true,
                            speed: 520,
                        };
                    }, syncAcademyTestimonialToggles);

                    mountSlider('[data-academy-gallery-splide]', function (count) {
                        return {
                            type: count > 1 ? 'loop' : 'slide',
                            rewind: count <= 1,
                            perPage: 1,
                            perMove: 1,
                            gap: '0.95rem',
                            drag: count > 1,
                            snap: true,
                            pagination: false,
                            arrows: count > 1,
                            autoplay: count > 1,
                            interval: 5000,
                            pauseOnHover: true,
                            pauseOnFocus: true,
                            updateOnMove: true,
                            speed: 520,
                        };
                    });

                    document.querySelectorAll('[data-academy-gallery]').forEach(function (root) {
                        if (root.dataset.lightGalleryReady === '1' || typeof window.lightGallery !== 'function') {
                            return;
                        }

                        root.dataset.lightGalleryReady = '1';
                        window.lightGallery(root, {
                            selector: '[data-academy-gallery-item]',
                            download: false,
                            counter: false,
                        });
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

<?php if (! $__env->hasRenderedOnce('53c8b26b-1d13-426e-8c93-bbeb3775b7ed')): $__env->markAsRenderedOnce('53c8b26b-1d13-426e-8c93-bbeb3775b7ed'); ?>
    <?php $__env->startPush('scripts'); ?>
        <script>
            (function () {
                const framePlayers = new WeakMap();
                const frameLeaveTimers = new WeakMap();
                let youtubeApiPromise = null;

                const syncFrameState = function (frame) {
                    if (!frame) {
                        return;
                    }

                    const isPreview = frame.dataset.videoPreview === '1';
                    const isPinned = frame.dataset.videoPinned === '1';
                    const isPlaying = frame.dataset.videoPlaying === '1';

                    frame.classList.toggle('is-active', isPreview || isPinned || isPlaying);
                };

                const loadYouTubeApi = function () {
                    if (window.YT && typeof window.YT.Player === 'function') {
                        return Promise.resolve(window.YT);
                    }

                    if (youtubeApiPromise) {
                        return youtubeApiPromise;
                    }

                    youtubeApiPromise = new Promise(function (resolve, reject) {
                        const previousReady = window.onYouTubeIframeAPIReady;
                        const existingScript = document.querySelector('script[data-youtube-iframe-api]');

                        window.onYouTubeIframeAPIReady = function () {
                            if (typeof previousReady === 'function') {
                                previousReady();
                            }

                            resolve(window.YT);
                        };

                        if (existingScript) {
                            return;
                        }

                        const script = document.createElement('script');
                        script.src = 'https://www.youtube.com/iframe_api';
                        script.async = true;
                        script.defer = true;
                        script.dataset.youtubeIframeApi = '1';
                        script.onerror = reject;
                        document.head.appendChild(script);
                    });

                    return youtubeApiPromise;
                };

                const ensureFramePlayer = function (frame) {
                    if (!frame) {
                        return Promise.resolve(null);
                    }

                    const cachedPlayer = framePlayers.get(frame);
                    if (cachedPlayer) {
                        return Promise.resolve(cachedPlayer);
                    }

                    const iframe = frame.querySelector('[data-academy-video-iframe]');
                    if (!iframe) {
                        return Promise.resolve(null);
                    }

                    if (!iframe.id) {
                        iframe.id = 'academy-video-' + Math.random().toString(36).slice(2, 11);
                    }

                    return loadYouTubeApi()
                        .then(function (YT) {
                            const existingPlayer = framePlayers.get(frame);
                            if (existingPlayer || !YT || typeof YT.Player !== 'function') {
                                return existingPlayer || null;
                            }

                            const player = new YT.Player(iframe.id, {
                                events: {
                                    onReady: function (event) {
                                        if (frame.dataset.videoAutoplayRequested === '1') {
                                            frame.dataset.videoAutoplayRequested = '0';
                                            event.target.playVideo();
                                        }
                                    },
                                    onStateChange: function (event) {
                                        if (!window.YT || !window.YT.PlayerState) {
                                            return;
                                        }

                                        const state = event.data;
                                        const isPlaying = state === window.YT.PlayerState.PLAYING || state === window.YT.PlayerState.BUFFERING;
                                        frame.dataset.videoPlaying = isPlaying ? '1' : '0';

                                        if (!isPlaying && frame.dataset.videoPinned !== '1' && !frame.matches(':hover')) {
                                            frame.dataset.videoPreview = '0';
                                        }

                                        syncFrameState(frame);
                                    },
                                },
                            });

                            framePlayers.set(frame, player);

                            return player;
                        })
                        .catch(function () {
                            return null;
                        });
                };

                const autoplayFrameFallback = function (frame) {
                    const iframe = frame.querySelector('[data-academy-video-iframe]');
                    if (!iframe) {
                        return;
                    }

                    const baseSrc = iframe.dataset.baseSrc || iframe.getAttribute('src') || '';
                    if (baseSrc === '') {
                        return;
                    }

                    try {
                        const url = new URL(baseSrc, window.location.origin);
                        url.searchParams.set('autoplay', '1');
                        url.searchParams.set('playsinline', '1');
                        iframe.src = url.toString();
                    } catch (error) {
                        iframe.src = baseSrc + (baseSrc.includes('?') ? '&' : '?') + 'autoplay=1&playsinline=1';
                    }
                };

                const setFramePreview = function (frame, active) {
                    if (!frame || frame.dataset.videoPinned === '1' || frame.dataset.videoPlaying === '1') {
                        return;
                    }

                    const leaveTimer = frameLeaveTimers.get(frame);
                    if (leaveTimer) {
                        window.clearTimeout(leaveTimer);
                        frameLeaveTimers.delete(frame);
                    }

                    frame.dataset.videoPreview = active ? '1' : '0';
                    syncFrameState(frame);
                };

                const pinAndPlayFrame = function (frame) {
                    if (!frame) {
                        return;
                    }

                    frame.dataset.videoPinned = '1';
                    frame.dataset.videoPreview = '1';
                    frame.dataset.videoAutoplayRequested = '1';
                    syncFrameState(frame);

                    ensureFramePlayer(frame).then(function (player) {
                        if (player && typeof player.playVideo === 'function') {
                            player.playVideo();
                            return;
                        }

                        autoplayFrameFallback(frame);
                    });
                };

                const init = function () {
                    const hoverMedia = window.matchMedia('(hover: hover) and (pointer: fine)');

                    document.querySelectorAll('[data-academy-video-frame]').forEach(function (frame) {
                        if (frame.dataset.videoOverlayReady === '1') {
                            return;
                        }

                        frame.dataset.videoOverlayReady = '1';
                        frame.dataset.videoPreview = frame.dataset.videoPreview || '0';
                        frame.dataset.videoPinned = frame.dataset.videoPinned || '0';
                        frame.dataset.videoPlaying = frame.dataset.videoPlaying || '0';
                        syncFrameState(frame);
                        ensureFramePlayer(frame);

                        if (hoverMedia.matches) {
                            frame.addEventListener('pointerenter', function (event) {
                                if (event.pointerType && event.pointerType !== 'mouse' && event.pointerType !== 'pen') {
                                    return;
                                }

                                setFramePreview(frame, true);
                            });

                            frame.addEventListener('pointerleave', function (event) {
                                if (event.pointerType && event.pointerType !== 'mouse' && event.pointerType !== 'pen') {
                                    return;
                                }

                                const timer = window.setTimeout(function () {
                                    if (frame.dataset.videoPinned !== '1' && frame.dataset.videoPlaying !== '1') {
                                        frame.dataset.videoPreview = '0';
                                        syncFrameState(frame);
                                    }

                                    frameLeaveTimers.delete(frame);
                                }, 160);

                                frameLeaveTimers.set(frame, timer);
                            });
                        }

                        const button = frame.querySelector('[data-academy-video-activate]');
                        if (!button) {
                            return;
                        }

                        button.addEventListener('click', function (event) {
                            event.preventDefault();
                            pinAndPlayFrame(frame);
                        });
                    });

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