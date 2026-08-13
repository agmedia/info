<?php
    use Illuminate\Support\Str;

    $translation = $post->translations->firstWhere('locale', $locale)
        ?? $post->translations->firstWhere('locale', $fallbackLocale);
    $postSlug = trim((string) ($translation?->slug ?? ''));
    $postUrl = $postSlug !== '' ? route('blog.show', ['slug' => $postSlug]) : route('blog.index');
    $postTitle = trim((string) ($translation?->title ?? $post->code));
    $postExcerpt = trim((string) ($translation?->excerpt ?? '')) ?: __('ui.blog.excerpt_fallback');
    $postExcerpt = Str::limit($postExcerpt, 180, '...', true);
    $postImage = $post->getFirstMedia('blog_cover');
    $postImageUrl = $postImage?->getUrl();
    if ($postImageUrl) {
        $postImagePath = parse_url($postImageUrl, PHP_URL_PATH);
        if (is_string($postImagePath) && str_starts_with($postImagePath, '/storage/')) {
            $postImageUrl = $postImagePath;
        }
    }
    $primaryCategory = $post->categories
        ->sortByDesc(fn ($category) => (int) ($category->pivot->is_primary ?? false))
        ->first();
    $categoryTranslation = $primaryCategory?->translations->firstWhere('locale', $locale)
        ?? $primaryCategory?->translations->firstWhere('locale', $fallbackLocale);
    $categoryLabel = trim((string) ($categoryTranslation?->name ?? __('ui.blog.default_category')));
    $publishedAt = $post->published_at ?? $post->created_at;
    $publishedLabel = $publishedAt?->translatedFormat('j. F Y.');
    $animationIndex = max(0, min(3, (int) ($revealIndex ?? 0)));
    $cardHeadingLevel = (int) ($headingLevel ?? 2);
?>

<article class="ac-home-blog-card ac-blog-card content-reveal animation-index-<?php echo e($animationIndex); ?>" data-image-reveal>
    <a href="<?php echo e($postUrl); ?>" class="ac-home-blog-card-link" aria-label="<?php echo e(__('ui.blog.open_post', ['title' => $postTitle])); ?>">
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
        </div>

        <div class="ac-home-blog-card-body">
            <div class="ac-blog-card-eyebrow">
                <span><?php echo e(Str::upper(Str::limit($categoryLabel, 30, ''))); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($publishedLabel): ?>
                    <time datetime="<?php echo e($publishedAt?->toDateString()); ?>"><?php echo e($publishedLabel); ?></time>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cardHeadingLevel === 3): ?>
                <h3 class="ac-home-blog-card-title"><?php echo e($postTitle); ?></h3>
            <?php else: ?>
                <h2 class="ac-home-blog-card-title"><?php echo e($postTitle); ?></h2>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <p class="ac-home-blog-card-excerpt"><?php echo e($postExcerpt); ?></p>
            <span class="ac-home-blog-card-meta-link">
                <span><?php echo e(__('ui.blog.read_more')); ?></span>
                <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
            </span>
        </div>
    </a>
</article>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/blog/partials/card.blade.php ENDPATH**/ ?>