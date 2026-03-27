<?php
    use App\Models\Content\Blog\BlogPost;

    $locale = app()->getLocale();
    $fallbackLocale = config('app.locale');
    $translationPayload = is_array($translation?->payload ?? null) ? $translation->payload : [];
    $blockPayload = is_array($block->payload ?? null) ? $block->payload : [];
    $mergedPayload = array_merge($blockPayload, $translationPayload);
    $allowedRoutes = config('content_blocks.route_whitelist', []);
    $displayTitle = trim((string) ($translation?->title ?? ''));
    $displaySubtitle = trim((string) ($translation?->subtitle ?? ''));
    $source = in_array((string) ($mergedPayload['blog_source'] ?? 'latest'), ['latest', 'featured'], true)
        ? (string) $mergedPayload['blog_source']
        : 'latest';
    $limit = max(1, min(12, (int) ($mergedPayload['items_limit'] ?? 6)));

    if ($displayTitle === '' || $displaySubtitle === '') {
        $allTranslations = $block->translations()->get(['locale', 'title', 'subtitle']);

        if ($displayTitle === '') {
            $displayTitle = trim((string) ($allTranslations->firstWhere('locale', $locale)?->title ?? ''));
            if ($displayTitle === '') {
                $displayTitle = trim((string) ($allTranslations->firstWhere('locale', $fallbackLocale)?->title ?? ''));
            }
            if ($displayTitle === '') {
                $displayTitle = trim((string) ($allTranslations->first(
                    static fn ($row): bool => trim((string) ($row->title ?? '')) !== ''
                )?->title ?? ''));
            }
        }

        if ($displaySubtitle === '') {
            $displaySubtitle = trim((string) ($allTranslations->firstWhere('locale', $locale)?->subtitle ?? ''));
            if ($displaySubtitle === '') {
                $displaySubtitle = trim((string) ($allTranslations->firstWhere('locale', $fallbackLocale)?->subtitle ?? ''));
            }
            if ($displaySubtitle === '') {
                $displaySubtitle = trim((string) ($allTranslations->first(
                    static fn ($row): bool => trim((string) ($row->subtitle ?? '')) !== ''
                )?->subtitle ?? ''));
            }
        }
    }

    if ($displayTitle === '') {
        $displayTitle = (string) $block->name;
    }

    $resolveRouteUrl = function (?string $routeName, mixed $routeParams, string $fallbackUrl = '#') use ($allowedRoutes): string {
        $name = trim((string) $routeName);
        if ($name === '') {
            return $fallbackUrl;
        }

        $isAllowed = $allowedRoutes === []
            || collect($allowedRoutes)->contains(fn ($pattern) => \Illuminate\Support\Str::is((string) $pattern, $name));

        if (! $isAllowed || !\Illuminate\Support\Facades\Route::has($name)) {
            return $fallbackUrl;
        }

        $params = is_array($routeParams) ? $routeParams : [];

        try {
            return route($name, $params);
        } catch (\Throwable) {
            return $fallbackUrl;
        }
    };

    $ctaLabel = trim((string) ($translation?->cta_label ?? ''));
    $ctaFallbackUrl = (string) ($translation?->cta_url ?? '#');
    $ctaRoute = (string) ($mergedPayload['cta_route'] ?? '');
    $ctaRouteParams = $mergedPayload['cta_route_params'] ?? [];
    $ctaUrl = $resolveRouteUrl($ctaRoute, $ctaRouteParams, $ctaFallbackUrl);

    $postsQuery = BlogPost::query()
        ->where('is_active', true)
        ->where(function ($q): void {
            $q->whereNull('published_at')->orWhere('published_at', '<=', now());
        })
        ->with([
            'translations' => fn ($q) => $q->whereIn('locale', [$locale, $fallbackLocale]),
            'media',
        ])
        ->orderByDesc('published_at')
        ->orderByDesc('id');

    if ($source === 'featured') {
        $postsQuery->where('is_featured', true);
    }

    $posts = $postsQuery->limit($limit)->get();
?>

<section class="relative left-1/2 right-1/2 -ml-[50vw] -mr-[50vw] w-screen bg-white py-8">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <div class="mx-auto flex max-w-3xl items-center gap-4 md:gap-6">
                <span class="h-px flex-1 bg-slate-300"></span>
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl"><?php echo e($displayTitle); ?></h2>
                <span class="h-px flex-1 bg-slate-300"></span>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($displaySubtitle !== ''): ?>
                <p class="mx-auto mt-2 max-w-2xl text-sm text-slate-600 md:text-base"><?php echo e($displaySubtitle); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ctaLabel !== '' && $ctaUrl !== ''): ?>
                <a href="<?php echo e($ctaUrl); ?>" class="front-link-cta mt-4 inline-flex h-10 items-center bg-slate-100 px-5 text-xs font-semibold uppercase tracking-[0.14em] text-slate-700 hover:bg-slate-200">
                    <?php echo e($ctaLabel); ?>

                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($posts->isNotEmpty()): ?>
            <style>
                #blogs-carousel-<?php echo e($block->id); ?> .splide__arrow {
                    opacity: 0;
                    width: 46px;
                    height: 46px;
                    border-radius: 9999px;
                    border: 1px solid rgba(255, 255, 255, 0.75);
                    background: rgba(15, 23, 42, 0.35);
                    backdrop-filter: blur(6px);
                    transform: translateY(-50%) scale(0.92);
                    transition: opacity .25s ease, transform .25s ease, background-color .25s ease;
                }

                #blogs-carousel-<?php echo e($block->id); ?>:hover .splide__arrow,
                #blogs-carousel-<?php echo e($block->id); ?>:focus-within .splide__arrow {
                    opacity: 1;
                    transform: translateY(-50%) scale(1);
                }

                #blogs-carousel-<?php echo e($block->id); ?> .splide__arrow:hover {
                    background: rgba(15, 23, 42, 0.55);
                }

                #blogs-carousel-<?php echo e($block->id); ?> .splide__arrow svg {
                    fill: #fff;
                }

                @media (hover: none) {
                    #blogs-carousel-<?php echo e($block->id); ?> .splide__arrow {
                        opacity: 1;
                        transform: translateY(-50%) scale(1);
                    }
                }
            </style>

            <?php if (! $__env->hasRenderedOnce('c9a4fc59-941e-4c1f-83e0-d835e93a9365')): $__env->markAsRenderedOnce('c9a4fc59-941e-4c1f-83e0-d835e93a9365'); ?>
                <?php $__env->startPush('scripts'); ?>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
                    <script defer src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
                <?php $__env->stopPush(); ?>
            <?php endif; ?>

            <div class="mt-4">
                <div id="blogs-carousel-<?php echo e($block->id); ?>" class="splide" data-blogs-carousel-splide>
                    <div class="splide__track">
                        <ul class="splide__list">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $postTranslation = $post->translations->firstWhere('locale', $locale)
                                        ?? $post->translations->firstWhere('locale', $fallbackLocale);
                                    $postTitle = (string) ($postTranslation?->title ?? $post->code);
                                    $postExcerpt = (string) ($postTranslation?->excerpt ?? '');
                                    $postSlug = (string) ($postTranslation?->slug ?? $post->id);
                                    $postUrl = route('blog.show', ['slug' => $postSlug]);
                                    $postCover = $post->getFirstMedia('blog_cover');
                                    $postCoverUrl = $postCover?->getUrl();
                                ?>
                                <li class="splide__slide">
                                    <article class="group h-full bg-white">
                                        <a href="<?php echo e($postUrl); ?>" class="block">
                                            <div class="overflow-hidden bg-slate-100">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($postCoverUrl): ?>
                                                    <img src="<?php echo e($postCoverUrl); ?>" alt="<?php echo e($postTitle); ?>" class="h-auto w-full object-contain transition duration-300 group-hover:scale-[1.01]" loading="lazy" decoding="async">
                                                <?php else: ?>
                                                    <div class="flex h-full w-full items-center justify-center text-xs font-semibold uppercase tracking-wide text-slate-500"><?php echo e(__('ui.product.no_image')); ?></div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <div class="p-4">
                                                <h3 class="text-lg font-semibold leading-tight text-slate-900"><?php echo e($postTitle); ?></h3>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($postExcerpt !== ''): ?>
                                                    <p class="mt-2 text-sm text-slate-600"><?php echo e(\Illuminate\Support\Str::limit($postExcerpt, 120, '...')); ?></p>
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

            <?php if (! $__env->hasRenderedOnce('60c14ae0-0305-40b7-a08d-1adbcbda334e')): $__env->markAsRenderedOnce('60c14ae0-0305-40b7-a08d-1adbcbda334e'); ?>
                <?php $__env->startPush('scripts'); ?>
                    <script>
                        (function () {
                            const init = function () {
                                if (typeof window.Splide !== 'function') {
                                    return false;
                                }

                                const sliders = document.querySelectorAll('[data-blogs-carousel-splide]');
                                sliders.forEach(function (el) {
                                    if (el.dataset.splideReady === '1') {
                                        return;
                                    }
                                    el.dataset.splideReady = '1';

                                    const count = el.querySelectorAll('.splide__slide').length;
                                    new window.Splide(el, {
                                        type: count > 1 ? 'loop' : 'slide',
                                        perPage: Math.min(4, Math.max(1, count)),
                                        perMove: 1,
                                        gap: '1.25rem',
                                        drag: count > 1,
                                        snap: true,
                                        pagination: count > 1,
                                        arrows: count > 1,
                                        updateOnMove: true,
                                        speed: 520,
                                        breakpoints: {
                                            1280: { perPage: Math.min(3, Math.max(1, count)) },
                                            1024: { perPage: Math.min(2, Math.max(1, count)) },
                                            860: { perPage: 1, gap: '1rem' },
                                            640: { perPage: 1, gap: '0.8rem' },
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
        <?php else: ?>
            <div class="bg-slate-50 p-4 text-xs text-slate-500">
                No blog posts available for selected source.
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/content-blocks/types/blogs_carousel.blade.php ENDPATH**/ ?>