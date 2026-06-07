<?php
    use Illuminate\Support\Str;

    $translation = $callPost->translations->firstWhere('locale', $locale)
        ?? $callPost->translations->firstWhere('locale', $fallbackLocale)
        ?? $callPost->translations->first();
    $mediaItems = $callPost->relationLoaded('media')
        ? $callPost->media
            ->sortBy(static fn ($mediaItem) => (int) ($mediaItem->order_column ?? 0))
            ->values()
        : collect();
    $coverImage = $mediaItems->firstWhere('collection_name', 'call_cover') ?? $callPost->getFirstMedia('call_cover');
    $coverImageUrl = $coverImage ? $coverImage->getUrl() : null;
    $galleryItems = $mediaItems->where('collection_name', 'call_gallery')->values();
    if ($galleryItems->isEmpty()) {
        $galleryItems = $callPost->getMedia('call_gallery')
            ->sortBy(static fn ($mediaItem) => (int) ($mediaItem->order_column ?? 0))
            ->values();
    }
    $bodyHtml = (string) ($callPostBodyHtml ?? $translation?->body_html ?? '');
    $excerpt = trim((string) ($translation?->excerpt ?? ''));
    $callCategories = $callPost->categories
        ->sortByDesc(fn ($category) => (int) ($category->pivot->is_primary ?? false))
        ->values();
    $articleTitle = trim((string) ($translation?->title ?? $callPost->code));
    $publishedLabel = ($callPost->published_at ?? $callPost->created_at)?->translatedFormat('j. F Y.');
    $euFundsLabel = str_starts_with(strtolower($locale), 'hr') ? 'EU fondovi' : 'EU Funds';
    $callsLabel = str_starts_with(strtolower($locale), 'hr') ? 'Pozivi' : 'Calls';
    $pageTitleBreadcrumbs = [
        ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
        ['label' => $euFundsLabel, 'url' => route('eu-funds.show')],
        ['label' => $callsLabel, 'url' => route('eu-funds.show').'#eu-funds-calls'],
        [
            'label' => Str::limit($articleTitle, 72, '...'),
            'current' => true,
            'current_class' => 'ac-blog-breadcrumb-current',
            'title' => $articleTitle,
        ],
    ];
?>

<?php $__env->startSection('title', $translation?->meta_title ?: $articleTitle); ?>
<?php $__env->startSection('main_class', 'w-full px-0 py-0'); ?>

<?php $__env->startSection('content'); ?>
    <div class="ac-blog-page ac-blog-article-page">
        <?php if (isset($component)) { $__componentOriginale6a101278d02d7bbbf9e98ee1142bf75 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a101278d02d7bbbf9e98ee1142bf75 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.front.page-title-band','data' => ['breadcrumbs' => $pageTitleBreadcrumbs,'sectionClass' => 'ac-blog-title-band ac-blog-article-title-band','heroClass' => 'ac-blog-article-hero','panelClass' => 'ac-blog-article-panel','breadcrumbClass' => 'ac-blog-hero-breadcrumb ac-blog-article-breadcrumb']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('front.page-title-band'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pageTitleBreadcrumbs),'section-class' => 'ac-blog-title-band ac-blog-article-title-band','hero-class' => 'ac-blog-article-hero','panel-class' => 'ac-blog-article-panel','breadcrumb-class' => 'ac-blog-hero-breadcrumb ac-blog-article-breadcrumb']); ?>
            <div class="ac-blog-article-head">
                <h1 class="ac-blog-article-title"><?php echo e($articleTitle); ?></h1>

                <div class="ac-blog-article-meta">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($publishedLabel): ?>
                        <span class="ac-blog-article-chip is-date"><?php echo e($publishedLabel); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $callCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $categoryTranslation = $category->translations->firstWhere('locale', $locale)
                                ?? $category->translations->firstWhere('locale', $fallbackLocale)
                                ?? $category->translations->first();
                        ?>
                        <span class="ac-blog-article-chip"><?php echo e($categoryTranslation?->name ?? $category->code); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
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

        <div class="mx-auto w-full max-w-[1320px] px-4 sm:px-6 lg:px-8">
            <article class="ac-blog-article-body">
                <div class="ac-blog-article-body-inner">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($coverImageUrl): ?>
                        <figure class="ac-blog-article-cover">
                            <img
                                src="<?php echo e($coverImageUrl); ?>"
                                alt="<?php echo e($articleTitle); ?>"
                                class="h-auto w-full object-cover"
                                loading="eager"
                                decoding="async"
                            >
                        </figure>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="content-richtext">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bodyHtml !== ''): ?>
                            <?php echo $bodyHtml; ?>

                        <?php elseif($excerpt !== ''): ?>
                            <p><?php echo e($excerpt); ?></p>
                        <?php else: ?>
                            <p><?php echo e(str_starts_with(strtolower($locale), 'hr') ? 'Sadržaj ove stavke još nije dopunjen.' : 'Content for this call has not been added yet.'); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </article>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($galleryItems->isNotEmpty()): ?>
                <section class="ac-blog-article-gallery">
                    <div class="grid gap-5 grid-cols-1 md:grid-cols-3" data-blog-gallery>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $galleryItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mediaItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $galleryImageUrl = $mediaItem->getUrl();
                            ?>
                            <a
                                href="<?php echo e($galleryImageUrl); ?>"
                                class="block aspect-[3/4] overflow-hidden rounded-[18px] bg-slate-100"
                                data-blog-gallery-item
                                data-sub-html="<?php echo e($articleTitle); ?>"
                            >
                                <img
                                    src="<?php echo e($galleryImageUrl); ?>"
                                    alt="<?php echo e($articleTitle); ?>"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </section>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <section class="ac-inline-cta ac-inline-cta--blog" aria-labelledby="ac-call-inline-cta-title">
                <div class="ac-inline-cta-card ac-inline-cta-card--blog">
                    <div class="mx-auto grid w-full max-w-[860px] gap-4 py-5 md:grid-cols-[minmax(0,1fr)_auto] md:items-center">
                        <div class="ac-inline-cta-copy">
                            <h2 id="ac-call-inline-cta-title" class="ac-inline-cta-title">
                                <span><?php echo e(str_starts_with(strtolower($locale), 'hr') ? 'Povratak na pregled poziva' : 'Back to calls overview'); ?></span>
                            </h2>
                        </div>

                        <div class="ac-inline-cta-action">
                            <a href="<?php echo e(route('eu-funds.show')); ?>#eu-funds-calls" class="front-action-cta">
                                <span><?php echo e(str_starts_with(strtolower($locale), 'hr') ? 'Pogledaj sve pozive' : 'View all calls'); ?></span>
                                <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M4 12L12 4"></path>
                                    <path d="M6 4h6v6"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/css/lightgallery-bundle.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script defer src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.min.js"></script>
    <script defer>
        document.addEventListener('DOMContentLoaded', function () {
            const galleryRoot = document.querySelector('[data-blog-gallery]');
            if (!galleryRoot || typeof window.lightGallery !== 'function') {
                return;
            }

            window.lightGallery(galleryRoot, {
                selector: '[data-blog-gallery-item]',
                download: false,
                counter: true,
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.desktop.layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/calls/show.blade.php ENDPATH**/ ?>