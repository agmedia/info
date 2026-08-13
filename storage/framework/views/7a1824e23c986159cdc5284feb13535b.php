<?php
    $translation = $post->translations->firstWhere('locale', $locale)
        ?? $post->translations->firstWhere('locale', $fallbackLocale);
    $mediaItems = $post->relationLoaded('media')
        ? $post->media
            ->sortBy(static fn ($mediaItem) => (int) ($mediaItem->order_column ?? 0))
            ->values()
        : collect();
    $coverImage = $mediaItems->firstWhere('collection_name', 'blog_cover') ?? $post->getFirstMedia('blog_cover');
    $sameOriginStorageUrl = static function (?string $url): string {
        $value = trim((string) $url);
        if ($value === '') {
            return '';
        }

        $path = parse_url(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'), PHP_URL_PATH);
        if (! is_string($path) || ! str_starts_with($path, '/storage/')) {
            return $value;
        }

        $query = parse_url($value, PHP_URL_QUERY);
        $fragment = parse_url($value, PHP_URL_FRAGMENT);

        return $path
            .(is_string($query) && $query !== '' ? '?'.$query : '')
            .(is_string($fragment) && $fragment !== '' ? '#'.$fragment : '');
    };
    $coverImageUrl = $coverImage ? $sameOriginStorageUrl($coverImage->getUrl()) : null;
    $galleryItems = $mediaItems->where('collection_name', 'blog_gallery')->values();
    if ($galleryItems->isEmpty()) {
        $galleryItems = $post->getMedia('blog_gallery')
            ->sortBy(static fn ($mediaItem) => (int) ($mediaItem->order_column ?? 0))
            ->values();
    }
    $bodyHtml = (string) ($translation?->body_html ?? '');
    if ($bodyHtml !== '') {
        $bodyHtml = preg_replace_callback(
            '/\b(src|href)=(["\'])(.*?)\2/i',
            static function (array $matches) use ($sameOriginStorageUrl): string {
                $normalizedUrl = $sameOriginStorageUrl((string) ($matches[3] ?? ''));

                return (string) $matches[1].'='.(string) $matches[2].$normalizedUrl.(string) $matches[2];
            },
            $bodyHtml
        ) ?? $bodyHtml;
    }
    $normalizeAssetUrl = static function (?string $url): string {
        $value = trim((string) $url);
        if ($value === '') {
            return '';
        }

        $path = parse_url($value, PHP_URL_PATH);

        return rawurldecode(is_string($path) && $path !== '' ? $path : $value);
    };
    $inlineImagePaths = collect();
    if ($bodyHtml !== '') {
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $bodyHtml, $bodyImageMatches);
        $inlineImagePaths = collect($bodyImageMatches[1] ?? [])
            ->map($normalizeAssetUrl)
            ->filter()
            ->values();
    }
    $galleryItems = $galleryItems
        ->reject(fn ($mediaItem) => $inlineImagePaths->contains($normalizeAssetUrl($mediaItem->getUrl())))
        ->values();
    $galleryCount = $galleryItems->count();
    $galleryColumnsClass = match (true) {
        $galleryCount <= 1 => 'grid-cols-1',
        $galleryCount === 2 => 'grid-cols-1 md:grid-cols-2',
        $galleryCount === 4 => 'grid-cols-1 md:grid-cols-2',
        default => 'grid-cols-1 md:grid-cols-3',
    };
    $postCategories = $post->categories
        ->sortByDesc(fn ($category) => (int) ($category->pivot->is_primary ?? false))
        ->values();
    $articleTitle = trim((string) ($translation?->title ?? $post->code));
    $publishedAt = $post->published_at ?? $post->created_at;
    $publishedLabel = $publishedAt?->translatedFormat('j. F Y.');
    $primaryCategory = $postCategories->first();
    $primaryCategoryTranslation = $primaryCategory
        ? ($primaryCategory->translations->firstWhere('locale', $locale)
            ?? $primaryCategory->translations->firstWhere('locale', $fallbackLocale)
            ?? $primaryCategory->translations->first())
        : null;
    $primaryCategoryLabel = trim((string) ($primaryCategoryTranslation?->name ?? $primaryCategory?->code ?? ''));
    $primaryCategorySlug = trim((string) ($primaryCategoryTranslation?->slug ?? ''));
    $primaryCategoryUrl = $primaryCategorySlug !== '' ? url('/blog/'.$primaryCategorySlug) : route('blog.index');
    $shareUrl = urlencode(url()->current());
    $shareTitle = urlencode($articleTitle);
    $shareLinks = [
        [
            'key' => 'x',
            'label' => __('ui.blog.share.x'),
            'url' => 'https://twitter.com/intent/tweet?url=' . $shareUrl . '&text=' . $shareTitle,
        ],
        [
            'key' => 'facebook',
            'label' => __('ui.blog.share.facebook'),
            'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . $shareUrl,
        ],
        [
            'key' => 'linkedin',
            'label' => __('ui.blog.share.linkedin'),
            'url' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $shareUrl,
        ],
    ];
    $articleCta = [
        'title_lines' => [
            __('ui.blog.article_cta.title_line_1'),
            __('ui.blog.article_cta.title_line_2'),
        ],
        'button' => [
            'label' => __('ui.blog.article_cta.button'),
            'url' => route('contact.create'),
        ],
    ];
    $articleCta['title_lines'] = array_values(array_filter(
        $articleCta['title_lines'],
        static fn ($line) => trim((string) $line) !== '',
    ));
    $articleCtaTitle = implode(' ', $articleCta['title_lines']);
    $headingWords = static fn (string $title): array => preg_split('/\s+/u', trim($title), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $isCroatian = str_starts_with(strtolower((string) ($locale ?? app()->getLocale())), 'hr');
    $pageTitleBreadcrumbs = [
        ['label' => $isCroatian ? 'Početna' : 'Home', 'url' => route('home')],
        ['label' => $isCroatian ? 'Objave' : 'Posts', 'url' => route('blog.index')],
    ];
    if ($primaryCategoryLabel !== '') {
        $pageTitleBreadcrumbs[] = ['label' => $primaryCategoryLabel, 'url' => $primaryCategoryUrl];
    }
    $articleCtaCardTitle = $isCroatian ? 'Razgovarajmo o vašem poslovanju.' : 'Let’s talk about your business.';
    $articleCtaCardCopy = $isCroatian
        ? 'Naš multidisciplinarni tim pomoći će vam jasno sagledati sljedeći korak.'
        : 'Our multidisciplinary team will help you clearly identify the next step.';
    $articleCtaStatus = $isCroatian
        ? 'Odgovaramo brzo i konkretno.'
        : 'We respond quickly and with clarity.';
?>

<?php $__env->startSection('title', $translation?->title ?? __('ui.blog.page_title')); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startSection('content'); ?>
    <div class="ac-blog-page ac-blog-article-page">
        <?php if (isset($component)) { $__componentOriginale6a101278d02d7bbbf9e98ee1142bf75 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.front.page-title-band','data' => ['breadcrumbs' => $pageTitleBreadcrumbs,'sectionClass' => 'ac-blog-article-intro','containerClass' => 'ac-blog-container','heroClass' => 'ac-blog-article-intro-hero','panelClass' => 'ac-blog-article-intro-grid','breadcrumbClass' => 'ac-blog-article-breadcrumb']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('front.page-title-band'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pageTitleBreadcrumbs),'section-class' => 'ac-blog-article-intro','container-class' => 'ac-blog-container','hero-class' => 'ac-blog-article-intro-hero','panel-class' => 'ac-blog-article-intro-grid','breadcrumb-class' => 'ac-blog-article-breadcrumb']); ?>
            <h1 class="ac-blog-article-title content-reveal animation-index-1" id="ac-blog-article-title" data-image-reveal><?php echo e($articleTitle); ?></h1>

            <div class="ac-blog-article-meta content-reveal animation-index-2" data-image-reveal>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($publishedLabel): ?>
                    <time datetime="<?php echo e($publishedAt?->toDateString()); ?>"><?php echo e($publishedLabel); ?></time>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $postCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $categoryTranslation = $category->translations->firstWhere('locale', $locale)
                            ?? $category->translations->firstWhere('locale', $fallbackLocale)
                            ?? $category->translations->first();
                        $categoryLabel = trim((string) ($categoryTranslation?->name ?? $category->code));
                        $categorySlug = trim((string) ($categoryTranslation?->slug ?? ''));
                        $categoryUrl = $categorySlug !== '' ? url('/blog/'.$categorySlug) : route('blog.index');
                    ?>
                    <a href="<?php echo e($categoryUrl); ?>"><?php echo e($categoryLabel); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

        <article class="ac-blog-article-body">
            <div class="ac-blog-container ac-blog-article-shell ac-blog-post-article-shell">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($coverImageUrl): ?>
                    <figure class="ac-blog-article-cover content-reveal" data-image-reveal>
                        <div class="image-reveal-media">
                            <img
                                src="<?php echo e($coverImageUrl); ?>"
                                alt="<?php echo e($translation?->title ?? $post->code); ?>"
                                loading="eager"
                                decoding="async"
                                fetchpriority="high"
                            >
                            <span class="image-reveal-curtain" aria-hidden="true"></span>
                        </div>
                    </figure>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="ac-blog-article-body-inner content-reveal animation-index-1" data-image-reveal>
                    <div class="content-richtext">
                        <?php echo $bodyHtml !== '' ? $bodyHtml : '<p>No body content available.</p>'; ?>

                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($galleryItems->isNotEmpty()): ?>
                    <section class="ac-blog-article-gallery" aria-label="<?php echo e($articleTitle); ?>">
                        <div class="ac-blog-gallery-grid <?php echo e($galleryColumnsClass); ?>" data-blog-gallery>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $galleryItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mediaItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $galleryImageUrl = $sameOriginStorageUrl($mediaItem->getUrl());
                                ?>
                                <a
                                    href="<?php echo e($galleryImageUrl); ?>"
                                    class="ac-blog-gallery-item"
                                    data-blog-gallery-item
                                    data-sub-html="<?php echo e($translation?->title ?? $post->code); ?>"
                                >
                                    <img
                                        src="<?php echo e($galleryImageUrl); ?>"
                                        alt="<?php echo e($translation?->title ?? $post->code); ?>"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </section>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <section class="ac-blog-share" aria-labelledby="ac-blog-share-title">
                    <div class="ac-blog-share-copy">
                        <p><?php echo e(__('ui.blog.eyebrow')); ?></p>
                        <h2 id="ac-blog-share-title"><?php echo e(__('ui.blog.share.title')); ?></h2>
                        <span><?php echo e(__('ui.blog.share.subtitle')); ?></span>
                    </div>

                    <div class="ac-blog-share-links">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $shareLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shareLink): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a
                                href="<?php echo e($shareLink['url']); ?>"
                                class="ac-blog-share-link ac-blog-share-link--<?php echo e($shareLink['key']); ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="<?php echo e($shareLink['label']); ?>"
                            >
                                <i class="fa-brands <?php echo e($shareLink['key'] === 'x' ? 'fa-x-twitter' : 'fa-'.$shareLink['key']); ?>" aria-hidden="true"></i>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </section>
            </div>
        </article>

        <section class="contact-cta ac-blog-contact-cta" aria-labelledby="ac-blog-contact-cta-title">
            <div class="contact-cta-shell">
                <div class="contact-cta-copy">
                    <h2 class="contact-cta-title" id="ac-blog-contact-cta-title" data-words-slide-from-right aria-label="<?php echo e($articleCtaTitle); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords($articleCtaTitle); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="contact-cta-title-word animation-index-<?php echo e($loop->index); ?> <?php echo e($loop->remaining < 2 ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h2>
                </div>

                <div class="contact-cta-card" data-image-reveal>
                    <div class="contact-cta-card-heading"><span><?php echo e($articleCtaCardTitle); ?></span></div>
                    <p><?php echo e($articleCtaCardCopy); ?></p>
                    <a class="contact-cta-button" href="<?php echo e($articleCta['button']['url']); ?>">
                        <span><?php echo e($articleCta['button']['label']); ?></span>
                        <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                    </a>
                    <small><span class="contact-cta-status-dot" aria-hidden="true"></span><?php echo e($articleCtaStatus); ?></small>
                </div>
            </div>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($related->isNotEmpty()): ?>
            <section class="ac-blog-related-section" aria-labelledby="ac-blog-related-title">
                <div class="ac-blog-container ac-blog-related-container">
                    <div class="ac-blog-related-head">
                        <h2 id="ac-blog-related-title" aria-label="<?php echo e(__('ui.blog.related_title')); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $headingWords(__('ui.blog.related_title')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="<?php echo e($loop->last ? 'is-accent' : ''); ?>" aria-hidden="true"><?php echo e($word); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </h2>
                        <span><?php echo e(__('ui.blog.related_intro')); ?></span>
                    </div>

                    <div class="ac-blog-grid ac-blog-grid-related">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $related; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $relatedPost): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('front.desktop.blog.partials.card', [
                                'post' => $relatedPost,
                                'locale' => $locale,
                                'fallbackLocale' => $fallbackLocale,
                                'headingLevel' => 3,
                                'revealIndex' => $loop->index,
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('front-theme/styles/pages/blog.css')); ?>?v=<?php echo e(filemtime(public_path('front-theme/styles/pages/blog.css'))); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/css/lightgallery-bundle.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script defer src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.min.js"></script>
    <script defer src="<?php echo e(asset('front-theme/scripts/blog.js')); ?>?v=<?php echo e(filemtime(public_path('front-theme/scripts/blog.js'))); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/blog/show.blade.php ENDPATH**/ ?>