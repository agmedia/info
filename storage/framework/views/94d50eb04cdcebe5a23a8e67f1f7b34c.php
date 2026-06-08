<?php $__env->startSection('title', config('app.name', 'AG Shop').' Store'); ?>
<?php $__env->startSection('main_class', 'w-full pt-0 pb-0'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $homeServicesShowcaseCards = array_values($primaryServicePillars ?? []);
        $homeServicesShowcaseText = 'ALPHA CAPITALIS čini tim stručnjaka iz područja revizije, računovodstva i financijskog savjetovanja. Kroz zajedničko djelovanje pružamo cjelovita rješenja poduzećima, investitorima i poduzetnicima koji žele sigurno rasti.';

        $globalMemberships = [
            [
                'name' => 'TAG Alliances',
                'logo_mark' => 'TAG',
                'logo_sub' => 'Alliances',
                'logo_tag' => 'Member',
                'logo_url' => asset('front-theme/images/logos/tag-alliances-logo.png'),
                'logo_theme' => 'light',
                'description' => 'Globalna mreža neovisnih računovodstvenih i savjetodavnih kuća za međunarodnu suradnju i razmjenu stručnog znanja.',
                'url' => 'https://www.tagalliances.com/',
                'accent' => 'blue',
            ],
            [
                'name' => 'Family Firm Institute',
                'logo_mark' => 'FFI',
                'logo_sub' => 'GEN',
                'logo_tag' => 'Family Business',
                'logo_url' => 'https://www.ffi.org/wp-content/uploads/2018/08/ffi-foot-logo.png',
                'logo_theme' => 'light',
                'description' => 'Najutjecajnija globalna mreža lidera i edukatora u području obiteljskog biznisa i savjetovanja za vlasničke tranzicije.',
                'url' => 'https://www.ffi.org/',
                'accent' => 'amber',
            ],
            [
                'name' => 'Pandea Global M&A',
                'logo_mark' => 'Pandea',
                'logo_sub' => 'Global M&A',
                'logo_tag' => 'Cross-border',
                'logo_url' => asset('front-theme/images/logos/pandea-logo-small.png'),
                'logo_image_class' => 'is-compact',
                'logo_theme' => 'light',
                'description' => 'Međunarodna M&A mreža koja povezuje investitore, kupce i prodavatelje kroz strukturirane cross-border transakcije.',
                'url' => 'https://pandeaglobal.com/',
                'accent' => 'navy',
            ],
            [
                'name' => 'Transeo International',
                'logo_mark' => 'Transeo',
                'logo_sub' => 'International',
                'logo_tag' => 'Transfer',
                'logo_url' => asset('front-theme/images/logos/transeo.svg'),
                'logo_theme' => 'light',
                'description' => 'Europska zajednica stručnjaka za prijenos vlasništva, SME transakcije i razvoj business transfer ekosustava.',
                'url' => 'https://www.transeo-association.eu/',
                'accent' => 'teal',
            ],
            [
                'name' => 'International Fiscal Association',
                'logo_mark' => 'IFA',
                'logo_sub' => 'Fiscal Association',
                'logo_tag' => 'Tax',
                'logo_url' => asset('front-theme/images/logos/ifa-logo-white.svg'),
                'logo_theme' => 'light',
                'description' => 'Vodeća međunarodna neovisna organizacija posvećena međunarodnom poreznom pravu i usporednim fiskalnim pitanjima.',
                'url' => 'https://www.ifa.nl/',
                'accent' => 'violet',
            ],
        ];

        $clientExperienceSection = [
            'eyebrow' => $locale === 'hr' ? 'Preporuke klijenata' : 'Client testimonials',
            'title' => $locale === 'hr' ? 'Iskustva naših klijenata' : 'What Our Clients Say',
            'intro' => $locale === 'hr'
                ? 'Odabrana iskustva tvrtki i timova koji s nama grade stabilnije i jasnije poslovne odluke.'
                : 'Selected testimonials from companies and teams that rely on us for clearer and more stable business decisions.',
        ];
    ?>

    <?php echo $__env->make('front.desktop.partials.service-pillars-showcase', [
        'sectionId' => 'ac-home-services-showcase',
        'headingLevel' => 2,
        'titleLead' => 'Stvaramo vrijednost za naše klijente u',
        'titleAccent' => 'svim fazama razvoja poslovanja',
        'intro' => $homeServicesShowcaseText,
        'cards' => $homeServicesShowcaseCards,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(false): ?>
    <section id="clanstva" class="ac-global-memberships" aria-labelledby="ac-global-memberships-title">
        <div class="ac-global-memberships-shell mx-auto w-full max-w-[1240px] px-6 lg:px-10">
            <div class="ac-services-head ac-support-story-head ac-global-memberships-head">
                <div class="ac-services-eyebrow">
                    <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                    <p class="ac-services-kicker">Mreže i članstva</p>
                    <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                </div>
                <h2 id="ac-global-memberships-title">
                    <span>Globalna partnerstva i stručna članstva</span>
                </h2>
                <p class="ac-services-intro">ALPHA CAPITALIS surađuje s relevantnim međunarodnim mrežama koje šire pristup znanju, investitorima i specijaliziranim ekspertima.</p>
                <div class="ac-services-divider" aria-hidden="true">
                    <span class="ac-services-divider-line"></span>
                    <span class="ac-services-divider-glyph"></span>
                    <span class="ac-services-divider-line"></span>
                </div>
            </div>

            <div class="ac-global-memberships-carousel">
                <div id="ac-global-memberships-splide" class="splide ac-global-memberships-splide" data-global-memberships-splide>
                    <div class="splide__track">
                        <ul class="splide__list ac-global-memberships-list">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $globalMemberships; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $membership): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="splide__slide ac-global-memberships-slide">
                                    <article class="ac-membership-card ac-membership-card--<?php echo e($membership['accent']); ?>">
                                        <h3 class="sr-only"><?php echo e($membership['name']); ?></h3>
                                        <div class="ac-membership-logo <?php echo e(!empty($membership['logo_url']) ? 'has-image' : ''); ?> <?php echo e(($membership['logo_theme'] ?? '') === 'light' ? 'is-light' : ''); ?>" aria-hidden="true">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($membership['logo_url'])): ?>
                                                <img
                                                    src="<?php echo e($membership['logo_url']); ?>"
                                                    alt=""
                                                    class="ac-membership-logo-image <?php echo e($membership['logo_image_class'] ?? ''); ?>"
                                                    loading="lazy"
                                                    decoding="async"
                                                >
                                            <?php else: ?>
                                                <span class="ac-membership-logo-tag"><?php echo e($membership['logo_tag']); ?></span>
                                                <span class="ac-membership-logo-mark"><?php echo e($membership['logo_mark']); ?></span>
                                                <span class="ac-membership-logo-sub"><?php echo e($membership['logo_sub']); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>

                                        <p class="ac-membership-copy"><?php echo e($membership['description']); ?></p>

                                        <a href="<?php echo e($membership['url']); ?>" class="ac-membership-link" target="_blank" rel="noopener noreferrer">
                                            <span>Opširnije</span>
                                            <span class="ac-membership-link-arrow" aria-hidden="true">
                                                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M4 12L12 4"></path>
                                                    <path d="M6 4h6v6"></path>
                                                </svg>
                                            </span>
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

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(false): ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($latestBlogPosts ?? collect())->isNotEmpty()): ?>
        <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
            <section id="novosti" class="ac-support-story ac-home-blog" aria-labelledby="ac-home-blog-title">
                <div class="ac-support-story-hero">
                    <div class="ac-support-story-shell">
                        <div class="ac-services-head ac-support-story-head">
                            <div class="ac-services-eyebrow">
                                <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                                <p class="ac-services-kicker">ALPHA CAPITALIS</p>
                                <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                            </div>
                            <h2 id="ac-home-blog-title">
                                <span>Zadnje objave i novosti</span>

                            </h2>
                            <p class="ac-services-intro">
                                Zadnjih pet blog objava iz područja financija, poreza, transakcija i poslovnog savjetovanja.
                            </p>
                            <div class="ac-services-divider" aria-hidden="true">
                                <span class="ac-services-divider-line"></span>
                                <span class="ac-services-divider-glyph"></span>
                                <span class="ac-services-divider-line"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ac-home-blog-carousel">
                    <div id="ac-home-blog-splide" class="splide ac-home-blog-splide" data-home-blog-splide>
                        <div class="splide__track">
                            <ul class="splide__list">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $latestBlogPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $translation = $post->translations->firstWhere('locale', $locale)
                                            ?? $post->translations->firstWhere('locale', $fallbackLocale);
                                        $postSlug = trim((string) ($translation?->slug ?? ''));
                                        $postUrl = $postSlug !== '' ? route('blog.show', ['slug' => $postSlug]) : route('blog.index');
                                        $postTitle = trim((string) ($translation?->title ?? $post->code));
                                        $postExcerpt = trim((string) ($translation?->excerpt ?? '')) ?: __('ui.blog.excerpt_fallback');
                                        $postExcerpt = \Illuminate\Support\Str::limit($postExcerpt, 180, '...', true);
                                        $postImage = $post->getFirstMedia('blog_cover');
                                        $postImageUrl = $postImage?->getUrl();
                                        $primaryCategory = $post->categories
                                            ->sortByDesc(fn ($category) => (int) ($category->pivot->is_primary ?? false))
                                            ->first();
                                        $categoryTranslation = $primaryCategory?->translations->firstWhere('locale', $locale)
                                            ?? $primaryCategory?->translations->firstWhere('locale', $fallbackLocale);
                                        $categoryLabel = trim((string) ($categoryTranslation?->name ?? 'Novosti'));
                                        $publishedLabel = ($post->published_at ?? $post->created_at)?->translatedFormat('j. F Y.');
                                    ?>

                                    <li class="splide__slide ac-home-blog-slide">
                                        <article class="ac-home-blog-card">
                                            <a href="<?php echo e($postUrl); ?>" class="ac-home-blog-card-link" aria-label="Otvori blog post: <?php echo e($postTitle); ?>">
                                                <div class="ac-home-blog-card-media">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($postImageUrl): ?>
                                                        <img
                                                            src="<?php echo e($postImageUrl); ?>"
                                                            alt="<?php echo e($postTitle); ?>"
                                                            class="ac-home-blog-card-image"
                                                            loading="lazy"
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
                                                        <span>Opširnije</span>
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
            </section>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(false): ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($clientTestimonials ?? collect())->isNotEmpty()): ?>
        <section id="iskustva-klijenata" class="ac-global-memberships ac-client-experiences" aria-labelledby="ac-client-experiences-title">
            <div class="ac-global-memberships-shell mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                <div class="ac-services-head ac-support-story-head ac-global-memberships-head ac-client-experiences-head">
                    <div class="ac-services-eyebrow">
                        <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                        <p class="ac-services-kicker"><?php echo e($clientExperienceSection['eyebrow']); ?></p>
                        <span class="ac-services-eyebrow-line" aria-hidden="true"></span>
                    </div>
                    <h2 id="ac-client-experiences-title">
                        <span><?php echo e($clientExperienceSection['title']); ?></span>
                    </h2>
                    <p class="ac-services-intro"><?php echo e($clientExperienceSection['intro']); ?></p>
                    <div class="ac-services-divider" aria-hidden="true">
                        <span class="ac-services-divider-line"></span>
                        <span class="ac-services-divider-glyph"></span>
                        <span class="ac-services-divider-line"></span>
                    </div>
                </div>

                <?php
                    $testimonialReadMoreLabel = $locale === 'hr' ? 'Pročitaj više' : 'Read more';
                    $testimonialShowLessLabel = $locale === 'hr' ? 'Prikaži manje' : 'Show less';
                ?>

                <div class="ac-client-experiences-carousel">
                    <div id="ac-client-experiences-splide" class="splide ac-client-experiences-splide" data-client-experiences-splide>
                        <div class="splide__track">
                            <ul class="splide__list ac-client-experiences-list">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $clientTestimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testimonial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $company = trim((string) ($testimonial->payload['company'] ?? ''));
                                        $rating = max(1, min(5, (int) ($testimonial->rating ?? 5)));
                                    ?>
                                    <li class="splide__slide ac-client-experiences-slide">
                                        <article class="ac-client-experience-card" data-testimonial-card>
                                            <div class="ac-client-experience-card-inner">
                                                <div class="ac-client-experience-quote-mark" aria-hidden="true">“</div>
                                                <div class="ac-client-experience-content">
                                                    <div class="ac-client-experience-rating" aria-label="<?php echo e($rating); ?> / 5">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 1; $i <= 5; $i++): ?>
                                                            <span class="<?php echo e($i <= $rating ? 'is-active' : ''); ?>">★</span>
                                                        <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                    <p class="ac-client-experience-body" data-testimonial-body><?php echo e($testimonial->body); ?></p>
                                                    <button
                                                        type="button"
                                                        class="ac-client-experience-toggle"
                                                        data-testimonial-toggle
                                                        data-more-label="<?php echo e($testimonialReadMoreLabel); ?>"
                                                        data-less-label="<?php echo e($testimonialShowLessLabel); ?>"
                                                        aria-expanded="false"
                                                        hidden
                                                    ><?php echo e($testimonialReadMoreLabel); ?></button>
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
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if (! $__env->hasRenderedOnce('6eb971e8-fca5-40b8-8903-2cf6d6ae8845')): $__env->markAsRenderedOnce('6eb971e8-fca5-40b8-8903-2cf6d6ae8845'); ?>
        <script>
            (function () {
                const syncTestimonialToggles = function () {
                    document.querySelectorAll('[data-testimonial-card]').forEach(function (card) {
                        const body = card.querySelector('[data-testimonial-body]');
                        const toggle = card.querySelector('[data-testimonial-toggle]');

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
                    const toggle = event.target.closest('[data-testimonial-toggle]');

                    if (!toggle) {
                        return;
                    }

                    const card = toggle.closest('[data-testimonial-card]');

                    if (!card) {
                        return;
                    }

                    const isExpanded = card.classList.toggle('is-expanded');
                    toggle.textContent = isExpanded
                        ? (toggle.dataset.lessLabel || 'Show less')
                        : (toggle.dataset.moreLabel || 'Read more');
                    toggle.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');

                    window.requestAnimationFrame(syncTestimonialToggles);
                });

                let testimonialResizeFrame = null;
                window.addEventListener('resize', function () {
                    if (testimonialResizeFrame !== null) {
                        window.cancelAnimationFrame(testimonialResizeFrame);
                    }

                    testimonialResizeFrame = window.requestAnimationFrame(function () {
                        testimonialResizeFrame = null;
                        syncTestimonialToggles();
                    });
                });

                const init = function () {
                    if (typeof window.Splide !== 'function') {
                        return false;
                    }

                    const mountSlider = function (selector, optionsFactory) {
                        const sliders = document.querySelectorAll(selector);
                        sliders.forEach(function (el) {
                            if (el.dataset.splideReady === '1') {
                                return;
                            }
                            el.dataset.splideReady = '1';

                            const count = el.querySelectorAll('.splide__slide').length;
                            const slider = new window.Splide(el, optionsFactory(count));
                            slider.mount();

                            if (selector === '[data-client-experiences-splide]') {
                                window.requestAnimationFrame(syncTestimonialToggles);
                            }
                        });
                    };

                    mountSlider('[data-home-blog-splide]', function (count) {
                        return {
                            type: count > 1 ? 'loop' : 'slide',
                            perPage: Math.min(3, Math.max(1, count)),
                            perMove: 1,
                            gap: '1.25rem',
                            drag: count > 1,
                            snap: true,
                            pagination: count > 1,
                            arrows: count > 1,
                            updateOnMove: true,
                            speed: 520,
                            breakpoints: {
                                1180: { perPage: Math.min(2, Math.max(1, count)) },
                                760: { perPage: 1, gap: '1rem' },
                            },
                        };
                    });

                    mountSlider('[data-global-memberships-splide]', function (count) {
                        return {
                            type: count > 4 ? 'loop' : 'slide',
                            perPage: Math.min(4, Math.max(1, count)),
                            perMove: 1,
                            gap: '1.1rem',
                            drag: count > 1,
                            snap: true,
                            pagination: count > 1,
                            arrows: count > 1,
                            updateOnMove: true,
                            speed: 520,
                            breakpoints: {
                                1280: { perPage: Math.min(3, Math.max(1, count)) },
                                960: { perPage: Math.min(2, Math.max(1, count)), gap: '1rem' },
                                760: { perPage: 1, gap: '0.92rem' },
                            },
                        };
                    });

                    mountSlider('[data-client-experiences-splide]', function (count) {
                        return {
                            type: count > 2 ? 'loop' : 'slide',
                            rewind: count <= 2,
                            perPage: Math.min(2, Math.max(1, count)),
                            perMove: 1,
                            gap: '1.15rem',
                            drag: count > 1,
                            snap: true,
                            pagination: count > 1,
                            arrows: count > 1,
                            updateOnMove: true,
                            speed: 540,
                            breakpoints: {
                                1080: { perPage: 1, gap: '1rem' },
                                760: { gap: '0.92rem' },
                            },
                        };
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
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/home/index.blade.php ENDPATH**/ ?>