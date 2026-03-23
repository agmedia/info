<?php
    use App\Models\Catalog\Category\Category;
    use App\Models\Content\Blog\BlogPost;

    $locale = app()->getLocale();
    $fallbackLocale = config('app.locale');
    $blockPayload = is_array($block->payload ?? null) ? $block->payload : [];
    $translationPayload = is_array($translation?->payload ?? null) ? $translation->payload : [];
    $mergedPayload = array_merge($blockPayload, $translationPayload);
    $allowedRoutes = config('content_blocks.route_whitelist', []);
    $sectionId = 'blog-grid-'.$block->id;

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

    $source = ($blockPayload['source'] ?? 'query') === 'manual' ? 'manual' : 'query';
    $limit = max(1, min(12, (int) ($mergedPayload['items_limit'] ?? $blockPayload['limit'] ?? 3)));
    $sort = (string) ($blockPayload['sort'] ?? 'newest');
    $manualIds = collect($blockPayload['manual_blog_ids'] ?? [])->map(fn ($id) => (int) $id)->filter()->values()->all();
    $categoryIds = collect($blockPayload['category_ids'] ?? [])->map(fn ($id) => (int) $id)->filter()->values()->all();

    $selectedCategory = null;
    if ($categoryIds !== []) {
        $selectedCategory = Category::query()
            ->where('scope', Category::SCOPE_BLOG)
            ->whereIn('id', $categoryIds)
            ->with([
                'translations' => fn ($q) => $q
                    ->where('scope', Category::SCOPE_BLOG)
                    ->whereIn('locale', [$locale, $fallbackLocale]),
            ])
            ->orderBy('_lft')
            ->first();
    }

    $selectedCategoryTranslation = $selectedCategory?->translations->firstWhere('locale', $locale)
        ?? $selectedCategory?->translations->firstWhere('locale', $fallbackLocale)
        ?? $selectedCategory?->translations->first();
    $selectedCategorySlug = trim((string) ($selectedCategoryTranslation?->slug ?? ''));
    $selectedCategoryUrl = $selectedCategorySlug !== '' ? url('/blog/'.$selectedCategorySlug) : route('blog.index');

    $query = BlogPost::query()
        ->where('is_active', true)
        ->where(function ($q): void {
            $q->whereNull('published_at')->orWhere('published_at', '<=', now());
        })
        ->with([
            'translations' => fn ($q) => $q->whereIn('locale', [$locale, $fallbackLocale]),
            'categories' => fn ($q) => $q
                ->where('scope', Category::SCOPE_BLOG)
                ->with([
                    'translations' => fn ($translationQuery) => $translationQuery
                        ->where('scope', Category::SCOPE_BLOG)
                        ->whereIn('locale', [$locale, $fallbackLocale]),
                ]),
            'media',
        ]);

    if ($source === 'manual' && $manualIds !== []) {
        $query->whereIn('id', $manualIds);
    } elseif ($categoryIds !== []) {
        $query->whereHas('categories', function ($q) use ($categoryIds): void {
            $q->where('categories.scope', Category::SCOPE_BLOG)
                ->whereIn('categories.id', $categoryIds);
        });
    }

    if ($sort === 'featured') {
        $query->orderByDesc('is_featured')->orderByDesc('published_at')->orderByDesc('id');
    } elseif ($sort === 'title') {
        $query->join('content_blog_post_translations as bt_sort', function ($join) use ($locale) {
            $join->on('bt_sort.post_id', '=', 'content_blog_posts.id')->where('bt_sort.locale', '=', $locale);
        })->orderBy('bt_sort.title')->select('content_blog_posts.*');
    } else {
        $query->orderByDesc('published_at')->orderByDesc('id');
    }

    $posts = $query->limit($limit)->get();

    if ($source === 'manual' && $manualIds !== []) {
        $rank = array_flip($manualIds);
        $posts = $posts->sortBy(fn ($item) => $rank[$item->id] ?? PHP_INT_MAX)->values();
    }

    $displayTitle = trim((string) ($translation?->title ?? '')) ?: (string) $block->name;
    $displaySubtitle = trim((string) ($translation?->subtitle ?? ''));
    $ctaLabel = trim((string) ($translation?->cta_label ?? ''));
    $ctaFallbackUrl = trim((string) ($translation?->cta_url ?? ''));
    if ($ctaFallbackUrl === '' && $selectedCategorySlug !== '') {
        $ctaFallbackUrl = $selectedCategoryUrl;
    }
    $ctaRoute = (string) ($mergedPayload['cta_route'] ?? '');
    $ctaRouteParams = $mergedPayload['cta_route_params'] ?? [];
    $ctaUrl = $resolveRouteUrl($ctaRoute, $ctaRouteParams, $ctaFallbackUrl);
?>

<style>
    #<?php echo e($sectionId); ?> {
        position: relative;
        overflow: hidden;
        padding: clamp(2.6rem, 4vw, 4.2rem) 0;
        border: 1px solid #dbe4ef;
        border-radius: 2rem;
        background: linear-gradient(180deg, #eef3f8 0%, #f8fbfd 100%);
    }

    #<?php echo e($sectionId); ?>::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            repeating-linear-gradient(
                90deg,
                rgba(113, 141, 175, 0.11) 0,
                rgba(113, 141, 175, 0.11) 1px,
                transparent 1px,
                transparent 28px
            );
        opacity: 0.75;
        pointer-events: none;
    }

    #<?php echo e($sectionId); ?> .ac-block-blog-grid-inner {
        position: relative;
        z-index: 1;
        max-width: 1240px;
        margin: 0 auto;
        padding: 0 1.25rem;
    }

    #<?php echo e($sectionId); ?> .ac-block-blog-grid-head {
        max-width: 54rem;
        margin: 0 auto 2rem;
        text-align: center;
    }

    #<?php echo e($sectionId); ?> .ac-block-blog-grid-title {
        margin: 0;
        font-family: 'Playfair Display', serif;
        font-size: clamp(2rem, 3vw, 3.35rem);
        font-weight: 600;
        line-height: 1.05;
        color: #13233a;
        text-wrap: balance;
    }

    #<?php echo e($sectionId); ?> .ac-block-blog-grid-subtitle {
        margin: 0.9rem auto 0;
        max-width: 42rem;
        font-size: 1rem;
        line-height: 1.75;
        color: #5a6c82;
    }

    #<?php echo e($sectionId); ?> .ac-block-blog-grid-divider {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        margin-top: 1.4rem;
    }

    #<?php echo e($sectionId); ?> .ac-block-blog-grid-divider-line {
        width: clamp(4rem, 9vw, 10rem);
        height: 1px;
        background: rgba(122, 144, 170, 0.28);
    }

    #<?php echo e($sectionId); ?> .ac-block-blog-grid-divider-dot {
        display: inline-flex;
        width: 2rem;
        height: 2rem;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(217, 225, 235, 0.95);
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.96);
        color: #d0b06f;
        font-size: 0.9rem;
        line-height: 1;
    }

    #<?php echo e($sectionId); ?> .ac-block-blog-grid-list {
        display: grid;
        gap: 1.35rem;
        margin-top: 2rem;
    }

    #<?php echo e($sectionId); ?> .ac-blog-card,
    #<?php echo e($sectionId); ?> .ac-home-blog-card-link {
        height: 100%;
    }

    #<?php echo e($sectionId); ?> .ac-block-blog-grid-actions {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
    }

    #<?php echo e($sectionId); ?> .ac-block-blog-grid-empty {
        max-width: 32rem;
        margin: 2rem auto 0;
        padding: 1rem 1.2rem;
        border: 1px dashed #c7d4e3;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.78);
        text-align: center;
        font-size: 0.95rem;
        color: #66788e;
    }

    @media (min-width: 768px) {
        #<?php echo e($sectionId); ?> .ac-block-blog-grid-list {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1200px) {
        #<?php echo e($sectionId); ?> .ac-block-blog-grid-list {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 767px) {
        #<?php echo e($sectionId); ?> {
            border-radius: 1.5rem;
            padding: 2.2rem 0;
        }

        #<?php echo e($sectionId); ?> .ac-block-blog-grid-inner {
            padding: 0 1rem;
        }
    }
</style>

<section id="<?php echo e($sectionId); ?>">
    <div class="ac-block-blog-grid-inner">
        <div class="ac-block-blog-grid-head">
            <h2 class="ac-block-blog-grid-title"><?php echo e($displayTitle); ?></h2>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($displaySubtitle !== ''): ?>
                <p class="ac-block-blog-grid-subtitle"><?php echo e($displaySubtitle); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="ac-block-blog-grid-divider" aria-hidden="true">
                <span class="ac-block-blog-grid-divider-line"></span>
                <span class="ac-block-blog-grid-divider-dot">*</span>
                <span class="ac-block-blog-grid-divider-line"></span>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($posts->isNotEmpty()): ?>
            <div class="ac-block-blog-grid-list">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('front.desktop.blog.partials.card', [
                        'post' => $post,
                        'locale' => $locale,
                        'fallbackLocale' => $fallbackLocale,
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ctaLabel !== '' && $ctaUrl !== ''): ?>
                <div class="ac-block-blog-grid-actions">
                    <a href="<?php echo e($ctaUrl); ?>" class="front-action-cta">
                        <span><?php echo e($ctaLabel); ?></span>
                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 12L12 4"></path>
                            <path d="M6 4h6v6"></path>
                        </svg>
                    </a>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php else: ?>
            <div class="ac-block-blog-grid-empty"><?php echo e(__('No blog posts matched this category source.')); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/content-blocks/types/blog_grid_3.blade.php ENDPATH**/ ?>