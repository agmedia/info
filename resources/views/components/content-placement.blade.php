@foreach ($items as $item)
    @php
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
                ->published()
                ->with(['translations' => fn ($q) => $q->whereIn('locale', [$locale, $fallbackLocale])])
                ->get()
                ->sortBy(fn ($row) => array_search((int) $row->id, $blogIds, true))
                ->values();
        }

        $comments = collect();
    @endphp

    @if ($wrapperClasses !== '' || $wrapperStyle !== '')
        <div @if($wrapperClasses !== '') class="{{ $wrapperClasses }}" @endif @if($wrapperStyle !== '') style="{{ $wrapperStyle }}" @endif>
    @endif
        @if ($overrideView !== '')
            @include($overrideView, [
                'block' => $block,
                'translation' => $translation,
                'slot' => $slot,
                'blockItems' => $blockItems,
                'categories' => $categories,
                'blogs' => $blogs,
                'comments' => $comments,
            ])
        @elseif (view()->exists($partial))
            @include($partial, [
                'block' => $block,
                'translation' => $translation,
                'slot' => $slot,
                'blockItems' => $blockItems,
                'categories' => $categories,
                'blogs' => $blogs,
                'comments' => $comments,
            ])
        @else
            @include('front.content-blocks.types.custom', [
                'block' => $block,
                'translation' => $translation,
                'slot' => $slot,
                'blockItems' => $blockItems,
                'categories' => $categories,
                'blogs' => $blogs,
                'comments' => $comments,
            ])
        @endif
    @if ($wrapperClasses !== '' || $wrapperStyle !== '')
        </div>
    @endif
@endforeach
