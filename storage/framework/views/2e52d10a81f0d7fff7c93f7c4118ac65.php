<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $block = $item['block'];
        $translation = $item['translation'];
        $slot = $item['slot'];
        $locale = app()->getLocale();
        $fallbackLocale = config('app.locale');
        $translationPayload = is_array($translation?->payload ?? null) ? $translation->payload : [];
        $wrapperClasses = trim((string) ($translationPayload['custom_classes'] ?? ''));
        $wrapperStyle = trim((string) ($translationPayload['bg_css'] ?? ''));

        $overridePrefix = (string) config('content_blocks.view_overrides.prefix', 'front.content-blocks.instances.');
        $codeOverride = $overridePrefix.$block->code;

        $overrideView = '';
        if (view()->exists($codeOverride)) {
            $overrideView = $codeOverride;
        }

        $partial = 'front.content-blocks.types.'.$block->type;

        $blockItems = $block->relationLoaded('items')
            ? $block->items
            : $block->items()->orderBy('sort_order')->orderBy('id')->get();

        $categoryIds = $blockItems->where('item_type', 'category')->pluck('item_id')->map(fn ($id) => (int) $id)->all();
        $blogIds = $blockItems->where('item_type', 'blog')->pluck('item_id')->map(fn ($id) => (int) $id)->all();

        $categories = collect();
        if ($categoryIds !== []) {
            $categories = \App\Models\Catalog\Category\Category::query()
                ->whereIn('id', $categoryIds)
                ->with(['translations' => fn ($q) => $q->whereIn('locale', [$locale, $fallbackLocale])])
                ->get()
                ->sortBy(fn ($row) => array_search((int) $row->id, $categoryIds, true))
                ->values();
        }

        $blogs = collect();
        if ($blogIds !== []) {
            $blogs = \App\Models\Content\Blog\BlogPost::query()
                ->whereIn('id', $blogIds)
                ->with(['translations' => fn ($q) => $q->whereIn('locale', [$locale, $fallbackLocale])])
                ->get()
                ->sortBy(fn ($row) => array_search((int) $row->id, $blogIds, true))
                ->values();
        }

        $comments = collect();
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($wrapperClasses !== '' || $wrapperStyle !== ''): ?>
        <div <?php if($wrapperClasses !== ''): ?> class="<?php echo e($wrapperClasses); ?>" <?php endif; ?> <?php if($wrapperStyle !== ''): ?> style="<?php echo e($wrapperStyle); ?>" <?php endif; ?>>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($overrideView !== ''): ?>
            <?php echo $__env->make($overrideView, [
                'block' => $block,
                'translation' => $translation,
                'slot' => $slot,
                'blockItems' => $blockItems,
                'categories' => $categories,
                'blogs' => $blogs,
                'comments' => $comments,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php elseif(view()->exists($partial)): ?>
            <?php echo $__env->make($partial, [
                'block' => $block,
                'translation' => $translation,
                'slot' => $slot,
                'blockItems' => $blockItems,
                'categories' => $categories,
                'blogs' => $blogs,
                'comments' => $comments,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php else: ?>
            <?php echo $__env->make('front.content-blocks.types.custom', [
                'block' => $block,
                'translation' => $translation,
                'slot' => $slot,
                'blockItems' => $blockItems,
                'categories' => $categories,
                'blogs' => $blogs,
                'comments' => $comments,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($wrapperClasses !== '' || $wrapperStyle !== ''): ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/tomek/Herd/info/resources/views/components/content-placement.blade.php ENDPATH**/ ?>